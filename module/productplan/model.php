<?php
/**
 * The model file of productplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     productplan
 * @version     $Id: model.php 4639 2013-04-11 02:06:35Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class productplanModel extends model
{
    /**
     * Get plan by id.
     *
     * @param  int    $planID
     * @param  bool   $setImgSize
     * @access public
     * @return object
     */
    public function getByID($planID, $setImgSize = false)
    {
        $plan = $this->dao->findByID((int)$planID)->from(TABLE_PRODUCTPLAN)->fetch();
        if(!$plan) return false;

        $plan = $this->loadModel('file')->replaceImgURL($plan, 'desc');
        if($setImgSize) $plan->desc = $this->file->setImgSize($plan->desc);
        return $plan;
    }

    /**
     * Get plans by idList
     *
     * @param  int    $planIDList
     * @access public
     * @return array
     */
    public function getByIDList($planIDList)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('id')->in($planIDList)->orderBy('begin desc')->fetchAll('id');
    }

    /**
     * Get last plan.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $parent
     * @access public
     * @return object
     */
    public function getLast($productID, $branch = 0, $parent = 0)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($parent <= 0)->andWhere('parent')->le((int)$parent)->fi()
            ->beginIF($parent > 0)->andWhere('parent')->eq((int)$parent)->fi()
            ->andWhere('product')->eq((int)$productID)
            ->andWhere('end')->ne($this->config->productplan->future)
            ->andWhere('branch')->eq($branch)
            ->orderBy('end desc')
            ->limit(1)
            ->fetch();
    }

    /**
     * Get list
     *
     * @param  int    $product
     * @param  int    $branch
     * @param  string $browseType
     * @param  object $pager
     * @param  string $orderBy
     * @param  string $param skipparent
     * @access public
     * @return object
     */
    public function getList($product = 0, $branch = 0, $browseType = 'doing', $pager = null, $orderBy = 'begin_desc', $param = '')
    {
        $date  = date('Y-m-d');
        $plans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('product')->eq($product)
            ->andWhere('deleted')->eq(0)
            ->beginIF(!empty($branch))->andWhere('branch')->eq($branch)->fi()
            ->beginIF($browseType != 'all')->andWhere('status')->eq($browseType)->fi()
            ->beginIF(strpos($param, 'skipparent') !== false)->andWhere('parent')->ne(-1)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        if(!empty($plans))
        {
            $plans      = $this->reorder4Children($plans);
            $planIdList = array_keys($plans);

            $planProjects = $this->dao->select('t1.*,t2.type')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
                ->where('t1.product')->eq($product)
                ->andWhere('t1.plan')->in(array_keys($plans))
                ->andWhere('t2.type')->in('sprint,stage,kanban')
                ->fetchPairs('plan', 'project');

            $storyCountInTable = $this->dao->select('plan,count(story) as count')->from(TABLE_PLANSTORY)->where('plan')->in($planIdList)->groupBy('plan')->fetchPairs('plan', 'count');
            $product = $this->loadModel('product')->getById($product);
            if($product->type == 'normal')
            {
                $storyGroups = $this->dao->select('id,plan,estimate')->from(TABLE_STORY)
                    ->where("plan")->in($planIdList)
                    ->andWhere('deleted')->eq(0)
                    ->fetchGroup('plan', 'id');
            }

            $bugs = $this->dao->select('*')->from(TABLE_BUG)->where("plan")->in($planIdList)->andWhere('deleted')->eq(0)->fetchGroup('plan', 'id');
            $parentStories = $parentBugs = $parentHour = array();
            foreach($plans as $plan)
            {
                if($product->type == 'normal')
                {
                    $stories    = zget($storyGroups, $plan->id, array());
                    $storyPairs = array();
                    foreach($stories as $story) $storyPairs[$story->id] = $story->estimate;
                }
                else
                {
                    $storyPairs = $this->dao->select('id,estimate')->from(TABLE_STORY)
                        ->where("CONCAT(',', plan, ',')")->like("%,{$plan->id},%")
                        ->andWhere('deleted')->eq(0)
                        ->fetchPairs('id', 'estimate');
                }
                $plan->stories   = count($storyPairs);
                $plan->bugs      = isset($bugs[$plan->id]) ? count($bugs[$plan->id]) : 0;
                $plan->hour      = array_sum($storyPairs);
                $plan->project   = zget($planProjects, $plan->id, '');
                $plan->projectID = $plan->project;
                $plan->expired   = $plan->end < $date ? true : false;

                /* Sync linked stories. */
                if(!isset($storyCountInTable[$plan->id]) or $storyCountInTable[$plan->id] != $plan->stories)
                {
                    $this->dao->delete()->from(TABLE_PLANSTORY)->where('plan')->eq($plan->id)->exec();

                    $order = 1;
                    foreach($storyPairs as $storyID => $estimate)
                    {
                        $planStory = new stdclass();
                        $planStory->plan = $plan->id;
                        $planStory->story = $storyID;
                        $planStory->order = $order ++;
                        $this->dao->replace(TABLE_PLANSTORY)->data($planStory)->exec();
                    }
                }

                if(!isset($parentStories[$plan->parent])) $parentStories[$plan->parent] = 0;
                if(!isset($parentBugs[$plan->parent]))    $parentBugs[$plan->parent]    = 0;
                if(!isset($parentHour[$plan->parent]))    $parentHour[$plan->parent]    = 0;

                $parentStories[$plan->parent] += $plan->stories;
                $parentBugs[$plan->parent]    += $plan->bugs;
                $parentHour[$plan->parent]    += $plan->hour;
            }

            unset($parentStories[0]);
            unset($parentBugs[0]);
            unset($parentHour[0]);
            foreach($parentStories as $parentID => $count)
            {
                if(!isset($plans[$parentID])) continue;
                $plan = $plans[$parentID];
                $plan->stories += $count;
                $plan->bugs    += $parentBugs[$parentID];
                $plan->hour    += $parentHour[$parentID];
            }
        }
        return $plans;
    }

    /**
     * Get plan pairs.
     *
     * @param  array|int        $product
     * @param  int|string|array $branch
     * @param  string           $expired
     * @param  bool             $skipParent
     * @access public
     * @return array
     */
    public function getPairs($product = 0, $branch = '', $expired = '', $skipParent = false)
    {
        $date = date('Y-m-d');
        $plans = $this->dao->select('t1.id,t1.title,t1.parent,t1.begin,t1.end,t2.name as branchName,t3.type as productType')->from(TABLE_PRODUCTPLAN)->alias('t1')
            ->leftJoin(TABLE_BRANCH)->alias('t2')->on('t2.id=t1.branch')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t3.id=t1.product')
            ->where('t1.product')->in($product)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($branch !== '')->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF($expired == 'unexpired')->andWhere('t1.end')->ge($date)->fi()
            ->orderBy('t1.begin desc')
            ->fetchAll('id');

        $plans       = $this->reorder4Children($plans);
        $planPairs   = array();
        $parentTitle = array();
        $this->app->loadLang('branch');
        foreach($plans as $plan)
        {
            if($plan->parent == '-1')
            {
                $parentTitle[$plan->id] = $plan->title;
                if($skipParent) continue;
            }
            if($plan->parent > 0 and isset($parentTitle[$plan->parent])) $plan->title = $parentTitle[$plan->parent] . ' /' . $plan->title;
            $planPairs[$plan->id] = $plan->title . " [{$plan->begin} ~ {$plan->end}]";
            if($plan->begin == $this->config->productplan->future and $plan->end == $this->config->productplan->future) $planPairs[$plan->id] = $plan->title . ' ' . $this->lang->productplan->future;
            if($plan->productType != 'normal') $planPairs[$plan->id] = ($plan->branchName ? $plan->branchName : $this->lang->branch->main) . ' / ' . $planPairs[$plan->id];
        }
        return array('' => '') + $planPairs;
    }

    /**
     * Get plan pairs for story.
     *
     * @param  array|int    $product
     * @param  int          $branch
     * @param  string       $param skipParent|withMainPlan|unexpired
     * @access public
     * @return array
     */
    public function getPairsForStory($product = 0, $branch = '', $param = '')
    {
        $date   = date('Y-m-d');
        $param  = strtolower($param);
        $branch = strpos($param, 'withmainplan') !== false ? "0,$branch" : $branch;
        $plans  = $this->dao->select('id,title,parent,begin,end')->from(TABLE_PRODUCTPLAN)
            ->where('product')->in($product)
            ->andWhere('deleted')->eq(0)
            ->beginIF(strpos($param, 'unexpired') !== false)->andWhere('end')->ge($date)->fi()
            ->beginIF($branch !== 'all' or $branch !== '')->andWhere("branch")->in($branch)->fi()
            ->orderBy('begin desc')
            ->fetchAll('id');

        $plans       = $this->reorder4Children($plans);
        $planPairs   = array();
        $parentTitle = array();
        foreach($plans as $plan)
        {
            if($plan->parent == '-1')
            {
                $parentTitle[$plan->id] = $plan->title;
                if(strpos($param, 'skipparent') !== false) continue;
            }
            if($plan->parent > 0 and isset($parentTitle[$plan->parent])) $plan->title = $parentTitle[$plan->parent] . ' /' . $plan->title;
            $planPairs[$plan->id] = $plan->title . " [{$plan->begin} ~ {$plan->end}]";
            if($plan->begin == $this->config->productplan->future and $plan->end == $this->config->productplan->future) $planPairs[$plan->id] = $plan->title . ' ' . $this->lang->productplan->future;
        }

        return array('' => '') + $planPairs;
    }

    /**
     * Get plans for products
     *
     * @param  int    $products
     * @access public
     * @return void
     */
    public function getForProducts($products)
    {
        $plans = $this->dao->select('id,title,parent,begin,end')->from(TABLE_PRODUCTPLAN)
            ->where('product')->in(array_keys($products))
            ->andWhere('deleted')->eq(0)
            ->orderBy('begin desc')
            ->fetchAll('id');

        $plans       = $this->reorder4Children($plans);
        $planPairs   = array();
        $parentTitle = array();
        foreach($plans as $plan)
        {
            if($plan->parent == '-1') $parentTitle[$plan->id] = $plan->title;
            if($plan->parent > 0 and isset($parentTitle[$plan->parent])) $plan->title = $parentTitle[$plan->parent] . ' /' . $plan->title;
            $planPairs[$plan->id] = $plan->title;
        }
        return array('' => '') + $planPairs;
    }

    /**
     * Get plan group by product id list.
     *
     * @param  string|array $products
     * @param  string       $param skipParent|unexpired
     * @param  string       $field name
     * @param  string       $orderBy id_desc|begin_desc
     * @access public
     * @return array
     */
    public function getGroupByProduct($products = '', $param = '', $field = 'name', $orderBy = 'id_desc')
    {
        $date  = date('Y-m-d');
        $param = strtolower($param);
        $plans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->beginIF($products)->andWhere('product')->in($products)->fi()
            ->beginIF(strpos($param, 'skipparent') !== false)->andWhere('parent')->ne(-1)->fi()
            ->beginIF(strpos($param, 'unexpired') !== false)->andWhere('end')->ge($date)->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');

        if(!empty($plans) and $field == 'name') $plans = $this->reorder4Children($plans);

        $parentTitle = array();
        $planGroup   = array();
        foreach($plans as $plan)
        {
            if(!isset($planGroup[$plan->product][$plan->branch])) $planGroup[$plan->product][$plan->branch] = array('' => '');

            if($field == 'name')
            {
                if($plan->parent == '-1') $parentTitle[$plan->id] = $plan->title;
                if($plan->parent > 0 and isset($parentTitle[$plan->parent])) $plan->title = $parentTitle[$plan->parent] . ' /' . $plan->title;
                $planGroup[$plan->product][$plan->branch][$plan->id] = $plan->title . " [{$plan->begin} ~ {$plan->end}]";
                if($plan->begin == $this->config->productplan->future and $plan->end == $this->config->productplan->future) $planGroup[$plan->product][$plan->branch][$plan->id] = $plan->title . ' ' . $this->lang->productplan->future;
            }
            else
            {
                $planGroup[$plan->product][$plan->branch][$plan->id] = $plan;
            }
        }
        return $planGroup;
    }

    /**
     * Get Children plan.
     *
     * @param  int    $planID
     * @access public
     * @return array
     */
    public function getChildren($planID)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('parent')->eq((int)$planID)->andWhere('deleted')->eq('0')->fetchAll();
    }

    /**
     * Get plan list by story id list.
     *
     * @param  string|array $storyIdList
     * @access public
     * @return array
     */
    public function getPlansByStories($storyIdList)
    {
        if(empty($storyIdList)) return array();
        return $this->dao->select('t2.id as storyID, t3.*')->from(TABLE_PLANSTORY)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t3')->on('t3.id=t1.plan')
            ->where('t2.id')->in($storyIdList)
            ->fetchGroup('storyID', 'id');
    }

    /**
     * Get branch plan pairs.
     *
     * @param  int    $productID
     * @param  array  $branches
     * @param  bool   $skipParent
     * @access public
     * @return array
     */
    public function getBranchPlanPairs($productID, $branches = '', $skipParent = false)
    {
        $plans = $this->dao->select('branch,id,title,begin,end')->from(TABLE_PRODUCTPLAN)
            ->where('product')->eq($productID)
            ->andWhere('deleted')->eq(0)
            ->beginIF(!empty($branches))->andWhere('branch')->in($branches)->fi()
            ->beginIF($skipParent)->andWhere('parent')->ne(-1)->fi()
            ->fetchAll('id');

        $planPairs = array();
        foreach($plans as $planID => $plan)
        {
            $planPairs[$plan->branch][$planID] = $plan->title . ' [' . $plan->begin . '~' . $plan->end . ']';
        }
        return $planPairs;
    }

    /**
     * Create a plan.
     *
     * @access public
     * @return int
     */
    public function create()
    {
        $plan = fixer::input('post')->stripTags($this->config->productplan->editor->create['id'], $this->config->allowedTags)
            ->setIF($this->post->future || empty($_POST['begin']), 'begin', $this->config->productplan->future)
            ->setIF($this->post->future || empty($_POST['end']), 'end', $this->config->productplan->future)
            ->remove('delta,uid,future')
            ->get();

        if($plan->parent > 0)
        {
            if($plan->parentBegin != $this->config->productplan->future)
            {
                if($plan->begin < $plan->parentBegin) dao::$errors['begin'] = sprintf($this->lang->productplan->beginLetterParent, $plan->parentBegin);
            }
            if($plan->parentEnd != $this->config->productplan->future)
            {
                if($plan->end !== $this->config->productplan->future and $plan->end > $plan->parentEnd) dao::$errors['end'] = sprintf($this->lang->productplan->endGreaterParent, $plan->parentEnd);
            }
        }

        unset($plan->parentBegin);
        unset($plan->parentEnd);

        if(!$this->post->future and strpos($this->config->productplan->create->requiredFields, 'begin') !== false and empty($_POST['begin']))
        {
            dao::$errors['begin'] = sprintf($this->lang->error->notempty, $this->lang->productplan->begin);
        }
        if(!$this->post->future and strpos($this->config->productplan->create->requiredFields, 'end') !== false and empty($_POST['end']))
        {
            dao::$errors['end'] = sprintf($this->lang->error->notempty, $this->lang->productplan->end);
        }
        if(dao::isError()) return false;

        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->productplan->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_PRODUCTPLAN)
            ->data($plan)
            ->autoCheck()
            ->batchCheck($this->config->productplan->create->requiredFields, 'notempty')
            ->checkIF(!$this->post->future && !empty($_POST['begin']) && !empty($_POST['end']), 'end', 'ge', $plan->begin)
            ->exec();
        if(!dao::isError())
        {
            $planID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $planID, 'plan');
            $this->loadModel('score')->create('productplan', 'create', $planID);
            if(!empty($plan->parent))
            {
                $parentPlan = $this->getByID($plan->parent);
                if($parentPlan->parent == '0')
                {
                    $this->dao->update(TABLE_PRODUCTPLAN)->set('parent')->eq('-1')->where('id')->eq($plan->parent)->andWhere('parent')->eq('0')->exec();

                    /* Transfer stories and bugs linked with the parent plan to the child plan. */
                    $this->dao->update(TABLE_PLANSTORY)->set('plan')->eq($planID)->where('plan')->eq($plan->parent)->exec();
                    $this->dao->update(TABLE_BUG)->set('plan')->eq($planID)->where('plan')->eq($plan->parent)->exec();
                    $stories = $this->dao->select('*')->from(TABLE_STORY)->where("CONCAT(',', plan, ',')")->like("%,{$plan->parent},%")->fetchAll('id');
                    foreach($stories as $storyID => $story)
                    {
                        $storyPlan = str_replace(",{$plan->parent},", ",$planID,", ",$story->plan,");
                        $storyPlan = trim($storyPlan, ',');

                        $this->dao->update(TABLE_STORY)->set('plan')->eq($storyPlan)->where('id')->eq($storyID)->exec();
                    }
                }
            }
            return $planID;
        }
    }

    /**
     * Update a plan.
     *
     * @param  int    $planID
     * @access public
     * @return array
     */
    public function update($planID)
    {
        $oldPlan = $this->dao->findByID((int)$planID)->from(TABLE_PRODUCTPLAN)->fetch();
        $plan = fixer::input('post')->stripTags($this->config->productplan->editor->edit['id'], $this->config->allowedTags)
            ->setIF($this->post->future or empty($_POST['begin']), 'begin', $this->config->productplan->future)
            ->setIF($this->post->future or empty($_POST['end']), 'end', $this->config->productplan->future)
            ->remove('delta,uid,future')
            ->get();

        if($oldPlan->parent > 0)
        {
            $parentPlan = $this->getByID($oldPlan->parent);
            if($parentPlan->begin !== $this->config->productplan->future)
            {
                if($plan->begin < $parentPlan->begin) dao::$errors['begin'] = sprintf($this->lang->productplan->beginLetterParent, $parentPlan->begin);
            }
            if($parentPlan->end !== $this->config->productplan->future)
            {
                if($plan->end !== $this->config->productplan->future and $plan->end > $parentPlan->end) dao::$errors['end'] = sprintf($this->lang->productplan->endGreaterParent, $parentPlan->end);
            }
        }
        elseif($oldPlan->parent == -1 and $plan->begin != $this->config->productplan->future)
        {
            $childPlans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('parent')->eq($planID)->andWhere('deleted')->eq(0)->fetchAll('id');
            $minBegin   = $plan->begin;
            $maxEnd     = $plan->end;
            foreach($childPlans as $childID => $childPlan)
            {
                $childPlan = isset($plans[$childID]) ? $plans[$childID] : $childPlan;
                if($childPlan->begin < $minBegin) $minBegin = $childPlan->begin;
                if($childPlan->end > $maxEnd) $maxEnd = $childPlan->end;
            }
            if($minBegin < $plan->begin and $minBegin != $this->config->productplan->future) dao::$errors['begin'] = sprintf($this->lang->productplan->beginGreaterChildTip, $planID, $plan->begin, $minBegin);
            if($maxEnd > $plan->end and $maxEnd != $this->config->productplan->future) dao::$errors['end'] = sprintf($this->lang->productplan->endLetterChildTip, $planID, $plan->end, $maxEnd);
        }

        if(dao::isError()) return false;
        $plan = $this->loadModel('file')->processImgURL($plan, $this->config->productplan->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PRODUCTPLAN)
            ->data($plan)
            ->autoCheck()
            ->batchCheck($this->config->productplan->edit->requiredFields, 'notempty')
            ->checkIF(!$this->post->future && !empty($_POST['begin']) && !empty($_POST['end']), 'end', 'ge', $plan->begin)
            ->where('id')->eq((int)$planID)
            ->exec();
        if(dao::isError()) return false;

        if($oldPlan->parent > 0) $this->updateParentStatus($oldPlan->parent);

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $planID, 'plan');
            return common::createChanges($oldPlan, $plan);
        }
    }

    /**
     * Update a plan's status.
     *
     * @param  int    $planID
     * @param  string $status doing|done|closed
     * @param  string $action started|finished|closed|activated
     * @access public
     * @return bool
     */
    public function updateStatus($planID, $status = '', $action = '')
    {
        $oldPlan = $this->getByID($planID);

        $plan = new stdclass();
        $plan->status = $status;
        if($status == 'closed' and $this->post->closedReason) $plan->closedReason = $this->post->closedReason;
        if($status !== 'closed') $plan->closedReason = '';

        $this->dao->update(TABLE_PRODUCTPLAN)->data($plan)->where('id')->eq($planID)->exec();

        if(dao::isError()) return false;

        $changes  = common::createChanges($oldPlan, $plan);

        $comment = $this->post->comment ? $this->post->comment : '';
        $actionID = $this->loadModel('action')->create('productplan', $planID, $action, $comment);
        $this->action->logHistory($actionID, $changes);

        if($oldPlan->parent > 0) $this->updateParentStatus($oldPlan->parent);

        return !dao::isError();
    }

    /**
     * Update a parent plan's status.
     *
     * @param  int    $parentID
     * @access public
     * @return bool
     */
    public function updateParentStatus($parentID)
    {
        $parent      = $this->getByID($parentID);
        $childStatus = $this->dao->select('status')->from(TABLE_PRODUCTPLAN)->where('parent')->eq($parentID)->andWhere('deleted')->eq(0)->fetchPairs();

        if(count($childStatus) == 1 and isset($childStatus['wait']))
        {
            return;
        }
        elseif(count($childStatus) == 1 and isset($childStatus['closed']))
        {
            if($parent->status != 'closed')
            {
                $parentStatus = 'closed';
                $parentAction = 'closedbychild';
            }
        }
        elseif(!isset($childStatus['wait']) and !isset($childStatus['doing']))
        {
            if($parent->status != 'done')
            {
                $parentStatus = 'done';
                $parentAction = 'finishedbychild';
            }
        }
        else
        {
            if($parent->status != 'doing')
            {
                $parentStatus = 'doing';
                $parentAction = $this->app->rawMethod == 'create' ? 'createchild' : 'activatedbychild';
            }
        }

        if(isset($parentStatus))
        {
            $this->dao->update(TABLE_PRODUCTPLAN)->set('status')->eq($parentStatus)->where('id')->eq($parentID)->exec();
            $this->loadModel('action')->create('productplan', $parentID, $parentAction, '', $parentAction);
        }
        return !dao::isError();
    }

    /**
     * Batch update plan.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function batchUpdate($productID)
    {
        $data     = fixer::input('post')->get();
        $oldPlans = $this->getByIDList($data->id);

        $this->app->loadClass('purifier', true);
        $config   = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $purifier = new HTMLPurifier($config);

        $plans = array();
        $extendFields = $this->getFlowExtendFields();
        foreach($data->id as $planID)
        {
            $isFuture = isset($data->future[$planID]) ? true : false;
            if(isset($data->status[$planID]) and $data->status[$planID] != 'wait') $isFuture = false;

            $plan = new stdclass();
            $plan->branch = isset($data->branch[$planID]) ? $data->branch[$planID] : $oldPlans[$planID]->branch;
            $plan->title  = $data->title[$planID];
            $plan->begin  = isset($data->begin[$planID]) ? $data->begin[$planID] : '';
            $plan->end    = isset($data->end[$planID]) ? $data->end[$planID] : '';
            $plan->status = isset($data->status[$planID]) ? $data->status[$planID] : $oldPlans[$planID]->status;
            $plan->parent = $oldPlans[$planID]->parent;

            if(empty($plan->title)) return print(js::alter(sprintf($this->lang->productplan->errorNoTitle, $planID)));
            if($plan->begin > $plan->end and !empty($plan->end)) return print(js::alert(sprintf($this->lang->productplan->beginGeEnd, $planID)));

            if($plan->begin == '') $plan->begin = $this->config->productplan->future;
            if($plan->end   == '') $plan->end   = $this->config->productplan->future;

            foreach($extendFields as $extendField)
            {
                $plan->{$extendField->field} = $this->post->{$extendField->field}[$planID];
                if(is_array($plan->{$extendField->field})) $plan->{$extendField->field} = join(',', $plan->{$extendField->field});

                $plan->{$extendField->field} = htmlSpecialString($plan->{$extendField->field});
                $message = $this->checkFlowRule($extendField, $plan->{$extendField->field});
                if($message) return print(js::alert($message));
            }

            $plans[$planID] = $plan;
        }

        $changes = array();
        $parents = array();
        foreach($plans as $planID => $plan)
        {
            $parentID = $oldPlans[$planID]->parent;
            /* Determine whether the begin and end dates of the parent plan and the child plan are correct. */
            if($parentID > 0)
            {
                $parent   = isset($plans[$parentID]) ? $plans[$parentID] : $this->getByID($parentID);
                if($parent->begin != $this->config->productplan->future and $plan->begin != $this->config->productplan->future and $plan->begin < $parent->begin)
                {
                    return print(js::alert(sprintf($this->lang->productplan->beginLetterParentTip, $planID, $plan->begin, $parent->begin)));
                }
                elseif($parent->end != $this->config->productplan->future and $plan->end != $this->config->productplan->future and $plan->end > $parent->end)
                {
                    return print(js::alert(sprintf($this->lang->productplan->endGreaterParentTip, $planID, $plan->end, $parent->end)));
                }
            }
            elseif($parentID == -1 and $plan->begin != $this->config->productplan->future)
            {
                $childPlans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('parent')->eq($planID)->andWhere('deleted')->eq(0)->fetchAll('id');
                $minBegin   = $plan->begin;
                $maxEnd     = $plan->end;
                foreach($childPlans as $childID => $childPlan)
                {
                    $childPlan = isset($plans[$childID]) ? $plans[$childID] : $childPlan;
                    if($childPlan->begin < $minBegin and $minBegin != $this->config->productplan->future) $minBegin = $childPlan->begin;
                    if($childPlan->end > $maxEnd and $maxEnd != $this->config->productplan->future) $maxEnd = $childPlan->end;
                }
                if($minBegin < $plan->begin and $minBegin != $this->config->productplan->future) return print(js::alert(sprintf($this->lang->productplan->beginGreaterChildTip, $planID, $plan->begin, $minBegin)));
                if($maxEnd > $plan->end and $maxEnd != $this->config->productplan->future) return print(js::alert(sprintf($this->lang->productplan->endLetterChildTip, $planID, $plan->end, $maxEnd)));
            }

            $change = common::createChanges($oldPlans[$planID], $plan);
            if($change)
            {
                if($parentID > 0 and !isset($parents[$parentID])) $parents[$parentID] = $parentID;
                $this->dao->update(TABLE_PRODUCTPLAN)->data($plan)->autoCheck()->where('id')->eq($planID)->exec();
                if(dao::isError()) return print(js::error(dao::getError()));
                $changes[$planID] = $change;
            }
        }

        foreach($parents as $parent) $this->updateParentStatus($parent);

        return $changes;
    }

    /**
     * Batch change the status of productplan.
     *
     * @param  array   $planIDList
     * @param  string  $status
     * @access public
     * @return array
     */
    public function batchChangeStatus($status)
    {
        $this->loadModel('action');
        $allChanges = array();

        $planIDList = $this->post->planIDList;
        if($status == 'closed') $closedReasons = $this->post->closedReasons;

        $oldPlans = $this->getByIDList($planIDList, $status);

        foreach($planIDList as $planID)
        {
            $oldPlan = $oldPlans[$planID];
            if($status == $oldPlan->status) continue;

            $plan = new stdclass();
            $plan->status = $status;
            if($status == 'closed')  $plan->closedReason = $closedReasons[$planID];
            if($status !== 'closed') $plan->closedReason = '';

            $this->dao->update(TABLE_PRODUCTPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$planID)->exec();

            if($oldPlan->parent > 0) $this->updateParentStatus($oldPlan->parent);

            if(!dao::isError())
            {
                $allChanges[$planID] = common::createChanges($oldPlan, $plan);
            }
            else
            {
                return print(js::error(dao::getError()));
            }
        }

        foreach($allChanges as $planID => $changes)
        {
            $comment = $this->post->comments[$planID] ? $this->post->comments[$planID] : '';
            $actionID = $this->action->create('productplan', $planID, 'edited', $comment);
            $this->action->logHistory($actionID, $changes);
        }
        return $allChanges;
    }

    /**
     * Check date for plan.
     *
     * @param  object $plan
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return void
     */
    public function checkDate4Plan($plan, $begin, $end)
    {
        if($plan->parent == -1)
        {
            $childPlans = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('parent')->eq($plan->id)->andWhere('deleted')->eq(0)->fetchAll();
            $minBegin   = $begin;
            $maxEnd     = $end;
            foreach($childPlans as $childPlan)
            {
                if($childPlan->begin < $minBegin and $minBegin != $this->config->productplan->future) $minBegin = $childPlan->begin;
                if($childPlan->end > $maxEnd and $maxEnd != $this->config->productplan->future) $maxEnd = $childPlan->end;
            }
            if($minBegin < $begin and $begin != $this->config->productplan->future) dao::$errors['begin'] = sprintf($this->lang->beginGreaterChild, $minBegin);
            if($maxEnd > $end and $end != $this->config->productplan->future) dao::$errors['end'] = sprintf($this->lang->endLetterChild, $maxEnd);
        }
        elseif($plan->parent > 0)
        {
            $parentPlan = $this->getByID($plan->parent);
            if($begin < $parentPlan->begin and $parentPlan->begin != $this->config->productplan->future) dao::$errors['begin'] = sprintf($this->lang->productplan->beginLetterParent, $parentPlan->begin);
            if($end > $parentPlan->end and $parentPlan->end != $this->config->productplan->future) dao::$errors['end'] = sprintf($this->lang->productplan->endGreaterParent, $parentPlan->end);
        }
    }

    /**
     * Change parent field by planID.
     *
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function changeParentField($planID)
    {
        $plan = $this->getById($planID);
        if($plan->parent <= 0) return true;

        $childCount = count($this->getChildren($plan->parent));
        $parent     = $childCount == 0 ? '0' : '-1';

        $parentPlan = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('id')->eq($plan->parent)->andWhere('deleted')->eq(0)->fetch();
        if($parentPlan)
        {
            $this->dao->update(TABLE_PRODUCTPLAN)->set('parent')->eq($parent)->where('id')->eq((int)$plan->parent)->exec();
        }
        else
        {
            $this->dao->update(TABLE_PRODUCTPLAN)->set('parent')->eq('0')->where('id')->eq((int)$planID)->exec();
        }
    }

    /**
     * Link stories.
     *
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function linkStory($planID)
    {
        $this->loadModel('story');
        $this->loadModel('action');

        $stories = $this->story->getByList($this->post->stories);
        $plan    = $this->getByID($planID);

        foreach($this->post->stories as $storyID)
        {
            if(!isset($stories[$storyID])) continue;

            $story = $stories[$storyID];
            if(strpos(",$story->plan,", ",{$planID},") !== false) continue;

            /* Update the plan linked with the story and the order of the story in the plan. */
            if($this->session->currentProductType == 'normal' or $story->branch != 0 or empty($story->plan))
            {
                $this->dao->update(TABLE_STORY)->set("plan")->eq($planID)->where('id')->eq((int)$storyID)->exec();

                $this->story->updateStoryOrderOfPlan($storyID, $planID, $story->plan);
            }
            else
            {
                $plansOfStory = $story->plan . ',' . $planID;

                $this->dao->update(TABLE_STORY)->set("plan")->eq($plansOfStory)->where('id')->eq((int)$storyID)->andWhere('branch')->eq('0')->exec();

                $this->story->updateStoryOrderOfPlan($storyID, $planID);
            }

            $this->action->create('story', $storyID, 'linked2plan', '', $planID);
            $this->story->setStage($storyID);
        }
    }

    /**
     * Unlink story
     *
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory($storyID, $planID)
    {
        $story = $this->dao->findByID($storyID)->from(TABLE_STORY)->fetch();
        $plans = array_unique(explode(',', trim(str_replace(",$planID,", ',', ',' . trim($story->plan) . ','). ',')));
        $this->dao->update(TABLE_STORY)->set('plan')->eq(join(',', $plans))->where('id')->eq((int)$storyID)->exec();

        /* Delete the story in the sort of the plan. */
        $this->loadModel('story');
        $this->story->updateStoryOrderOfPlan($storyID, '', $planID);

        $this->story->setStage($storyID);
        $this->loadModel('action')->create('story', $storyID, 'unlinkedfromplan', '', $planID);
    }

    /**
     * Link bugs.
     *
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function linkBug($planID)
    {
        $this->loadModel('story');
        $this->loadModel('action');

        $bugs = $this->loadModel('bug')->getByList($this->post->bugs);
        foreach($this->post->bugs as $bugID)
        {
            if(!isset($bugs[$bugID])) continue;

            $bug = $bugs[$bugID];
            if($bug->plan == $planID) continue;

            $this->dao->update(TABLE_BUG)->set('plan')->eq($planID)->where('id')->eq((int)$bugID)->exec();
            $this->action->create('bug', $bugID, 'linked2plan', '', $planID);
        }
    }

    /**
     * Unlink bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function unlinkBug($bugID)
    {
        $planID = $this->dao->findByID($bugID)->from(TABLE_BUG)->fetch('plan');
        $this->dao->update(TABLE_BUG)->set('plan')->eq(0)->where('id')->eq((int)$bugID)->exec();
        $this->loadModel('action')->create('bug', $bugID, 'unlinkedfromplan', '', $planID);
    }

    /**
     * Link project.
     *
     * @param  int    $projectID
     * @param  array  $newPlans
     * @access public
     * @return void
     */
    public function linkProject($projectID, $newPlans)
    {
        $this->loadModel('execution');
        foreach($newPlans as $planID)
        {
            $planStories = $planProducts = array();
            $planStory   = $this->loadModel('story')->getPlanStories($planID);
            if(!empty($planStory))
            {
                foreach($planStory as $id => $story)
                {
                    if($story->status == 'draft')
                    {
                        unset($planStory[$id]);
                        continue;
                    }
                    $planProducts[$story->id] = $story->product;
                }
                $planStories = array_keys($planStory);
                $this->execution->linkStory($projectID, $planStories, $planProducts);
            }
        }
    }

    /**
     * Reorder for children plans.
     *
     * @param  array    $plans
     * @access public
     * @return array
     */
    public function reorder4Children($plans)
    {
        /* Get children and unset. */
        $childrenPlans = array();
        foreach($plans as $plan)
        {
            if($plan->parent > 0)
            {
                $childrenPlans[$plan->parent][$plan->id] = $plan;
                if(isset($plans[$plan->parent])) unset($plans[$plan->id]);
            }
        }

        if(!empty($childrenPlans))
        {
            /* Append to parent plan. */
            $reorderedPlans = array();
            foreach($plans as $plan)
            {
                $reorderedPlans[$plan->id] = $plan;
                if(isset($childrenPlans[$plan->id]))
                {
                    $plan->children = count($childrenPlans[$plan->id]);
                    foreach($childrenPlans[$plan->id] as $childrenPlan) $reorderedPlans[$childrenPlan->id] = $childrenPlan;
                }
            }
            $plans = $reorderedPlans;
        }

        return $plans;
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object $plan
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($plan, $action)
    {
        $action    = strtolower($action);
        $clickable = commonModel::hasPriv('productplan', $action);
        if(!$clickable) return false;

        switch($action)
        {
            case 'start' :
                if($plan->status != 'wait' or $plan->parent < 0) return false;
                break;
            case 'finish' :
                if($plan->status != 'doing' or $plan->parent < 0) return false;
                break;
            case 'close' :
                if($plan->status == 'closed' or $plan->parent < 0) return false;
                break;
            case 'activate' :
                if($plan->status == 'wait' or $plan->status == 'doing' or $plan->parent < 0) return false;
                break;
        }

        return true;
    }
}
