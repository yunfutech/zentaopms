<?php
/**
 * The model file of action module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     action
 * @version     $Id: model.php 5028 2013-07-06 02:59:41Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class actionModel extends model
{
    const BE_UNDELETED  = 0;    // The deleted object has been undeleted.
    const CAN_UNDELETED = 1;    // The deleted object can be undeleted.
    const BE_HIDDEN     = 2;    // The deleted object has been hidded.

    /**
     * Create a action.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $actionType
     * @param  string $comment
     * @param  string $extra        the extra info of this action, according to different modules and actions, can set different extra.
     * @param  string $actor
     * @param  bool   $autoDelete
     * @access public
     * @return int
     */
    public function create($objectType, $objectID, $actionType, $comment = '', $extra = '', $actor = '', $autoDelete = true)
    {
        if(strtolower($actionType) == 'commented' and empty($comment)) return false;

        $actor      = $actor ? $actor : $this->app->user->account;
        $actionType = strtolower($actionType);
        $actor      = ($actionType == 'openedbysystem' or $actionType == 'closedbysystem') ? '' : $actor;
        if($actor == 'guest' and $actionType == 'logout') return false;

        $objectType = str_replace('`', '', $objectType);

        $action             = new stdclass();
        $action->objectType = strtolower($objectType);
        $action->objectID   = $objectID;
        $action->actor      = $actor;
        $action->action     = $actionType;
        $action->date       = helper::now();
        $action->extra      = $extra;

        if($objectType == 'story' and strpos(',reviewpassed,reviewrejected,reviewclarified,', ",$actionType,") !== false) $action->actor = $this->lang->action->system;

        /* Use purifier to process comment. Fix bug #2683. */
        $action->comment = fixer::stripDataTags($comment);

        /* Process action. */
        if($this->post->uid)
        {
            $action = $this->loadModel('file')->processImgURL($action, 'comment', $this->post->uid);
            if($autoDelete) $this->file->autoDelete($this->post->uid);
        }

        /* Get product project and execution for this object. */
        $relation          = $this->getRelatedFields($action->objectType, $objectID, $actionType, $extra);
        $action->product   = $relation['product'];
        $action->project   = (int)$relation['project'];
        $action->execution = (int)$relation['execution'];
        $this->dao->insert(TABLE_ACTION)->data($action)->autoCheck()->exec();
        $actionID = $this->dao->lastInsertID();

        if($this->post->uid) $this->file->updateObjectID($this->post->uid, $objectID, $objectType);

        /* Call the message notification function. */
        $this->loadModel('message')->send($objectType, $objectID, $actionType, $actionID, $actor);

        /* Add index for global search. */
        $this->saveIndex($objectType, $objectID, $actionType);

        return $actionID;
    }

    /**
     * Update read field of action when view a task/bug.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return void
     */
    public function read($objectType, $objectID)
    {
        $this->dao->update(TABLE_ACTION)
            ->set('`read`')->eq(1)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('`read`')->eq(0)
            ->exec();
    }

    /**
     * Get the unread actions.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function getUnreadActions($actionID = 0)
    {
        if(!is_numeric($actionID)) $actionID = 0;

        $actions    = array();
        $objectList = array('task' => TABLE_TASK, 'bug' => TABLE_BUG);
        foreach($objectList as $object => $table)
        {
            $idList = $this->dao->select('id')->from($table)->where('assignedTo')->eq($this->app->user->account)->fetchPairs('id');

            $tmpActions = $this->dao->select('*')->from(TABLE_ACTION)
                ->where('objectType')->eq($object)
                ->andWhere('objectID')->in($idList)
                ->andWhere('`read`')->eq(0)
                ->andWhere('id')->gt($actionID)
                ->fetchAll('id');

            if(empty($tmpActions)) continue;

            $tmpActions = $this->transformActions($tmpActions);
            foreach($tmpActions as $action)
            {
                $actions[$action->objectType][] = array(
                    'actionID'   => $action->id,
                    'objectType' => $action->objectType,
                    'objectID'   => $action->objectID,
                    'action'     => $action->actor . ' ' . $action->actionLabel . ' ' . $action->objectType . " #$action->objectID" . $action->objectName
                );
            }
        }
        return json_encode($actions);
    }

    /**
     * Get product, project, execution of the object.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $actionType
     * @param  string $extra
     * @access public
     * @return array
     */
    public function getRelatedFields($objectType, $objectID, $actionType = '', $extra = '')
    {
        $emptyRecord = array('product' => ',0,', 'project' => 0, 'execution' => 0);

        switch($objectType)
        {
            case 'program':
                return $emptyRecord;
            case 'product':
                return array('product' => ",$objectID,", 'project' => 0, 'execution' => 0);
            case 'project':
            case 'execution':
                $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($objectID)->fetchPairs('product');
                $productList = ',' . join(',', array_keys($products)) . ',';

                $relation = array($objectType => $objectID, 'product' => $productList);

                if($objectType == 'execution')
                {
                    $project = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($objectID)->fetch('project');
                    $relation['project'] = $project;
                }
                else
                {
                    $relation['execution'] = 0;
                }

                return $relation;
        }

        /* Only process these object types. */
        if(strpos($this->config->action->needGetRelateField, ",{$objectType},") !== false)
        {
            if(!isset($this->config->objectTables[$objectType])) return $emptyRecord;

            $record = $emptyRecord;
            switch($objectType)
            {
                case 'story':
                    if($actionType == 'linked2build' or $actionType == 'unlinkedfrombuild')
                    {
                        $build = $this->dao->select('project,execution')->from(TABLE_BUILD)->where('id')->eq((int)$extra)->fetch();
                        $record['project']   = $build->project;
                        $record['execution'] = $build->execution;
                    }
                    elseif($actionType == 'estimated')
                    {
                        $record['project']   = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq((int)$extra)->fetch('project');
                        $record['execution'] = (int)$extra;
                    }
                    else
                    {
                        $projects = $this->dao->select('t2.id,t2.project,t2.type')->from(TABLE_PROJECTSTORY)->alias('t1')
                            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                            ->where('t1.story')->eq($objectID)
                            ->fetchAll();
                        foreach($projects as $project)
                        {
                            if($project->type == 'project')
                            {
                                $record['project'] = $project->id;
                                continue;
                            }
                            $record['project']   = $project->project;
                            $record['execution'] = $project->id;
                        }
                    }
                case 'productplan':
                case 'branch':
                    $record['product'] = $objectID == 0 ? $extra : $this->dao->select('product')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch('product');
                    break;
                case 'case':
                    $result = $this->dao->select('product, project, execution')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch();
                    $record['product']   = $result->product;
                    $record['project']   = $result->project;
                    $record['execution'] = $result->execution;
                    if(strpos(',linked2testtask,unlinkedfromtesttask,assigned,run,', ',' . $actionType . ',') !== false and (int)$extra)
                    {
                        $testtask = $this->dao->select('project,execution')->from(TABLE_TESTTASK)->where('id')->eq((int)$extra)->fetch();
                        $record['project']   = $testtask->project;
                        $record['execution'] = $testtask->execution;
                    }
                    break;
                case 'build':
                case 'bug':
                case 'testtask':
                case 'doc':
                    $result = $this->dao->select('product, project, execution')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch();
                    $record['product']   = $result->product;
                    $record['project']   = $result->project;
                    $record['execution'] = $result->execution;
                    break;
                case 'repo':
                    $record['execution'] = $this->dao->select('execution')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch('execution');
                    break;
                case 'release':
                    $result = $this->dao->select('product, build')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch();
                    $record['product'] = $result->product;
                    $record['project'] = $this->dao->select('project')->from(TABLE_BUILD)->where('id')->eq($result->build)->fetch('project');
                    break;
                case 'task':
                    $fields = 'project, execution, story';
                    $result = $this->dao->select($fields)->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch();
                    if($result->story != 0)
                    {
                        $product = $this->dao->select('product')->from(TABLE_STORY)->where('id')->eq($result->story)->fetchPairs('product');
                        $record['product'] = join(',', array_keys($product));
                    }
                    else
                    {
                        $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($result->execution)->fetchPairs('product');
                        $record['product'] = join(',', array_keys($products));
                    }
                    $record['project']   = $result->project;
                    $record['execution'] = $result->execution;
                    break;
                case 'kanbanlane':
                    $record['execution'] = $this->dao->select('execution')->from(TABLE_KANBANLANE)->where('id')->eq($objectID)->fetch('execution');
                    break;
                case 'kanbancolumn':
                    $record['execution'] = $extra;
                    break;
                case 'team':
                    $team = $this->dao->select('type')->from(TABLE_PROJECT)->where('id')->eq($objectID)->fetch();
                    $type = $team->type == 'project' ? 'project' : 'execution';
                    $record[$type] = $objectID;
                    break;
                case 'whitelist':
                    if($extra == 'product') $record['product'] = $objectID;
                    if($extra == 'project') $record['project'] = $objectID;
                    if($extra == 'sprint' or $extra == 'stage') $record['execution'] = $objectID;
                    break;
                default:
                    $result = $this->dao->select('*')->from($this->config->objectTables[$objectType])->where('id')->eq($objectID)->fetch();
                    $record['product']   = zget($result, 'product', '0');
                    $record['project']   = zget($result, 'project', 0);
                    $record['execution'] = zget($result, 'execution', 0);
            }

            if($actionType == 'unlinkedfromproject' or $actionType == 'linked2project') $record['project'] = (int)$extra ;
            if($actionType == 'unlinkedfromexecution' or $actionType == 'linked2execution') $record['execution'] = (int)$extra;

            if($record)
            {
                $record['product'] = isset($record['product']) ? ',' . $record['product'] . ',' : ',0,';
                if(empty($record['project']))   $record['project']   = 0;
                if(empty($record['execution'])) $record['execution'] = 0;

                if(!empty($record['execution']) and empty($record['project'])) $record['project'] = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($record['execution'])->fetch('project');
                return $record;
            }

            return $emptyRecord;
        }

        return $emptyRecord;
    }

    /**
     * Get actions of an object.
     *
     * @param  int    $objectType
     * @param  int    $objectID
     * @access public
     * @return array
     */
    public function getList($objectType, $objectID)
    {
        $commiters = $this->loadModel('user')->getCommiters();
        $actions   = $this->dao->select('*')->from(TABLE_ACTION)
            ->beginIF($objectType == 'project')
            ->where("objectType IN('project', 'testtask', 'build')")
            ->andWhere('project')->eq((int)$objectID)
            ->fi()
            ->beginIF($objectType != 'project')
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq((int)$objectID)
            ->fi()
            ->orderBy('date, id')
            ->fetchAll('id');

        $histories = $this->getHistory(array_keys($actions));
        $this->loadModel('file');

        if($objectType == 'project')
        {
            $this->app->loadLang('build');
            $this->app->loadLang('testtask');
            $actions = $this->processProjectActions($actions);
        }

        foreach($actions as $actionID => $action)
        {
            $actionName = strtolower($action->action);
            if($actionName == 'svncommited' and isset($commiters[$action->actor]))
            {
                $action->actor = $commiters[$action->actor];
            }
            elseif($actionName == 'gitcommited' and isset($commiters[$action->actor]))
            {
                $action->actor = $commiters[$action->actor];
            }
            elseif($actionName == 'linked2execution')
            {
                $execution = $this->dao->select('name,type')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch();
                $name      = $execution->name;
                $method    = $execution->type == 'kanban' ? 'kanban' : 'view';
                if($name) $action->extra = common::hasPriv('execution', $method) ? html::a(helper::createLink('execution', $method, "executionID=$action->execution"), $name) : $name;
            }
            elseif($actionName == 'linked2project')
            {
                $project   = $this->dao->select('name,model')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch();
                $productID = trim($action->product, ',');
                $name      = $project->name;
                $method    = $project->model == 'kanban' ? 'index' : 'view';
                if($name) $action->extra = common::hasPriv('project', $method) ? html::a(helper::createLink('project', $method, "projectID=$action->project"), $name) : $name;
            }
            elseif($actionName == 'linked2plan')
            {
                $title = $this->dao->select('title')->from(TABLE_PRODUCTPLAN)->where('id')->eq($action->extra)->fetch('title');
                if($title) $action->extra = common::hasPriv('productplan', 'view') ? html::a(helper::createLink('productplan', 'view', "planID=$action->extra"), $title) : $title;
            }
            elseif($actionName == 'linked2build')
            {
                $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
            }
            elseif($actionName == 'linked2bug')
            {
                $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
            }
            elseif($actionName == 'linked2release')
            {
                $name = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('release', 'view') ? html::a(helper::createLink('release', 'view', "releaseID=$action->extra&type={$action->objectType}"), $name) : $name;
            }
            elseif($actionName == 'linked2testtask')
            {
                $name = $this->dao->select('name')->from(TABLE_TESTTASK)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('testtask', 'view') ? html::a(helper::createLink('testtask', 'view', "taskID=$action->extra"), $name) : $name;
            }
            elseif($actionName == 'moved')
            {
                $name = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('execution', 'task') ? html::a(helper::createLink('execution', 'task', "executionID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
            }
            elseif($actionName == 'frombug' and common::hasPriv('bug', 'view'))
            {
                $action->extra = html::a(helper::createLink('bug', 'view', "bugID=$action->extra"), $action->extra);
            }
            elseif($actionName == 'unlinkedfromexecution')
            {
                $name = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('project', 'story') ? html::a(helper::createLink('project', 'story', "projectID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
            }
            elseif($actionName == 'unlinkedfromproject')
            {
                $name      = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($action->extra)->fetch('name');
                $productID = trim($action->product, ',');
                if($name) $action->extra = common::hasPriv('projectstory', 'story') ? html::a(helper::createLink('projectstory', 'story', "projectID=$action->execution&productID=$productID"), "#$action->extra " . $name) : "#$action->extra " . $name;
            }
            elseif($actionName == 'unlinkedfrombuild')
            {
                $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "builID=$action->extra&type={$action->objectType}"), $name) : $name;
            }
            elseif($actionName == 'unlinkedfromrelease')
            {
                $name = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('release', 'view') ? html::a(helper::createLink('release', 'view', "releaseID=$action->extra&type={$action->objectType}"), $name) : $name;
            }
            elseif($actionName == 'unlinkedfromplan')
            {
                $title = $this->dao->select('title')->from(TABLE_PRODUCTPLAN)->where('id')->eq($action->extra)->fetch('title');
                if($title) $action->extra = common::hasPriv('productplan', 'view') ? html::a(helper::createLink('productplan', 'view', "planID=$action->extra"), "#$action->extra " . $title) : "#$action->extra " . $title;
            }
            elseif($actionName == 'unlinkedfromtesttask')
            {
                $name = $this->dao->select('name')->from(TABLE_TESTTASK)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('testtask', 'view') ? html::a(helper::createLink('testtask', 'view', "taskID=$action->extra"), $name) : $name;
            }
            elseif($actionName == 'tostory')
            {
                $title = $this->dao->select('title')->from(TABLE_STORY)->where('id')->eq($action->extra)->fetch('title');
                if($title) $action->extra = common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$action->extra"), "#$action->extra " . $title) : "#$action->extra " . $title;
            }
            elseif($actionName == 'importedcard')
            {
                $title = $this->dao->select('name')->from(TABLE_KANBAN)->where('id')->eq($action->extra)->fetch('name');
                if($title) $action->extra = common::hasPriv('kanban', 'view') ? html::a(helper::createLink('kanban', 'view', "kanbanID=$action->extra"), "#$action->extra " . $title) : "#$action->extra " . $title;
            }
            elseif($actionName == 'createchildren')
            {
                $names = $this->dao->select('id,name')->from(TABLE_TASK)->where('id')->in($action->extra)->fetchPairs('id', 'name');
                $action->extra = '';
                if($names)
                {
                    foreach($names as $id => $name) $action->extra .= common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$id"), "#$id " . $name) . ', ' : "#$id " . $name . ', ';
                }
                $action->extra = trim(trim($action->extra), ',');
            }
            /* Code for waterfall. */
            elseif($actionName == 'createrequirements')
            {
                $names = $this->dao->select('id,title')->from(TABLE_STORY)->where('id')->in($action->extra)->fetchPairs('id', 'title');
                $action->extra = '';
                if($names)
                {
                    foreach($names as $id => $name) $action->extra .= common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$id"), "#$id " . $name) . ', ' : "#$id " . $name . ', ';
                }
                $action->extra = trim(trim($action->extra), ',');
            }
            elseif($actionName == 'totask' or $actionName == 'linkchildtask' or $actionName == 'unlinkchildrentask' or $actionName == 'linkparenttask' or $actionName == 'unlinkparenttask' or $actionName == 'deletechildrentask')
            {
                $name = $this->dao->select('name')->from(TABLE_TASK)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
            }
            elseif($actionName == 'linkchildstory' or $actionName == 'unlinkchildrenstory' or $actionName == 'linkparentstory' or $actionName == 'unlinkparentstory' or $actionName == 'deletechildrenstory')
            {
                $name = $this->dao->select('title')->from(TABLE_STORY)->where('id')->eq($action->extra)->fetch('title');
                if($name) $action->extra = common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$action->extra"), "#$action->extra " . $name) : "#$action->extra " . $name;
            }
            elseif($actionName == 'buildopened')
            {
                $name = $this->dao->select('name')->from(TABLE_BUILD)->where('id')->eq($action->objectID)->fetch('name');
                if($name) $action->extra = common::hasPriv('build', 'view') ? html::a(helper::createLink('build', 'view', "buildID=$action->objectID"), "#$action->objectID " . $name) : "#$action->objectID " . $name;
            }
            elseif($actionName == 'testtaskopened' or $actionName == 'testtaskstarted' or $actionName == 'testtaskclosed')
            {
                $name = $this->dao->select('name')->from(TABLE_TESTTASK)->where('id')->eq($action->objectID)->fetch('name');
                if($name) $action->extra = common::hasPriv('testtask', 'view') ? html::a(helper::createLink('testtask', 'view', "testtaskID=$action->objectID"), "#$action->objectID " . $name) : "#$action->objectID " . $name;
            }
            elseif($actionName == 'fromlib' and $action->objectType == 'case')
            {
                $name = $this->dao->select('name')->from(TABLE_TESTSUITE)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('caselib', 'browse') ? html::a(helper::createLink('caselib', 'browse', "libID=$action->extra"), $name) : $name;
            }
            elseif(strpos('importfromstorylib,importfromrisklib,importfromissuelib,importfromopportunitylib', $actionName) !== false)
            {
                $name = $this->dao->select('name')->from(TABLE_ASSETLIB)->where('id')->eq($action->extra)->fetch('name');
                if($name) $action->extra = common::hasPriv('assetlib', $action->objectType) ? html::a(helper::createLink('assetlib', $action->objectType, "libID=$action->extra"), $name) : $name;
            }
            elseif(($actionName == 'closed' and $action->objectType == 'story') or ($actionName == 'resolved' and $action->objectType == 'bug'))
            {
                $action->appendLink = '';
                if(strpos($action->extra, ':')!== false)
                {
                    list($extra, $id) = explode(':', $action->extra);
                    $action->extra    = $extra;
                    if($id)
                    {
                        $table = $action->objectType == 'story' ? TABLE_STORY : TABLE_BUG;
                        $name  = $this->dao->select('title')->from($table)->where('id')->eq($id)->fetch('title');
                        if($name) $action->appendLink = html::a(helper::createLink($action->objectType, 'view', "id=$id"), "#$id " . $name);
                    }
                }
            }
            elseif($actionName == 'finished' and $objectType == 'todo')
            {
                $action->appendLink = '';
                if(strpos($action->extra, ':')!== false)
                {
                    list($extra, $id) = explode(':', $action->extra);
                    $action->extra    = strtolower($extra);
                    if($id)
                    {
                        $table = $this->config->objectTables[$action->extra];
                        $field = $this->config->action->objectNameFields[$action->extra];
                        $name  = $this->dao->select($field)->from($table)->where('id')->eq($id)->fetch($field);
                        if($name) $action->appendLink = html::a(helper::createLink($action->extra, 'view', "id=$id"), "#$id " . $name);
                    }
                }
            }
            elseif(($actionName == 'opened' or $actionName == 'managed' or $actionName == 'edited') and ($objectType == 'execution' || $objectType == 'project'))
            {
                $this->app->loadLang('execution');
                $linkedProducts = $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('id')->in($action->extra)->fetchPairs('id', 'name');
                $action->extra  = '';
                if($linkedProducts)
                {
                    foreach($linkedProducts as $productID => $productName) $linkedProducts[$productID] = html::a(helper::createLink('product', 'browse', "productID=$productID"), "#{$productID} {$productName}");
                    $action->extra = sprintf($this->lang->execution->action->extra, '<strong>' . join(', ', $linkedProducts) . '</strong>');
                }
            }
            $action->history = isset($histories[$actionID]) ? $histories[$actionID] : array();

            $actionName = strtolower($action->action);
            if($actionName == 'svncommited')
            {
                foreach($action->history as $history)
                {
                    if($history->field == 'subversion') $history->diff = str_replace('+', '%2B', $history->diff);
                }
            }
            elseif($actionName == 'gitcommited')
            {
                foreach($action->history as $history)
                {
                    if($history->field == 'git') $history->diff = str_replace('+', '%2B', $history->diff);
                }
            }

            $action->comment = $this->file->setImgSize($action->comment, $this->config->action->commonImgSize);

            $actions[$actionID] = $action;
        }

        return $actions;
    }

    /**
     * Process Project Actions change actionStype.
     *
     * @param  array  $actions
     * @access public
     * @return array
     */
    public function processProjectActions($actions)
    {
        /* Define the action map table. */
        $map = array();
        $map['testtask']['opened']  = 'testtaskopened';
        $map['testtask']['started'] = 'testtaskstarted';
        $map['testtask']['closed']  = 'testtaskclosed';
        $map['build']['opened']     = 'buildopened';

        /* Process actions. */
        foreach($actions as $key => $action)
        {
            if($action->objectType != 'project' and !isset($map[$action->objectType][$action->action])) unset($actions[$key]);
            if(isset($map[$action->objectType][$action->action])) $action->action = $map[$action->objectType][$action->action];
        }

        return $actions;
    }

    /**
     * Get an action record.
     *
     * @param  int    $actionID
     * @access public
     * @return object
     */
    public function getById($actionID)
    {
        $action = $this->dao->findById((int)$actionID)->from(TABLE_ACTION)->fetch();

        /* Splice domain name for connection when the action is equal to 'repocreated'.*/
        if($action->action == 'repocreated') $action->extra = str_replace("href='", "href='" . common::getSysURL(), $action->extra);

        return $action;
    }

    /**
     * Get deleted objects.
     *
     * @param  string    $type all|hidden
     * @param  string    $orderBy
     * @param  object    $pager
     * @access public
     * @return array
     */
    public function getTrashes($type, $orderBy, $pager)
    {
        $extra = $type == 'hidden' ? self::BE_HIDDEN : self::CAN_UNDELETED;
        $trashes = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('action')->eq('deleted')
            ->andWhere('extra')->eq($extra)
            ->orderBy($orderBy)->page($pager)->fetchAll();
        if(!$trashes) return array();

        /* Group trashes by objectType, and get there name field. */
        foreach($trashes as $object)
        {
            $object->objectType = str_replace('`', '', $object->objectType);
            $typeTrashes[$object->objectType][] = $object->objectID;
        }

        foreach($typeTrashes as $objectType => $objectIdList)
        {
            if(!isset($this->config->objectTables[$objectType])) continue;
            if(!isset($this->config->action->objectNameFields[$objectType])) continue;

            $objectIdList = array_unique($objectIdList);
            $table        = $this->config->objectTables[$objectType];
            $field        = $this->config->action->objectNameFields[$objectType];
            if($objectType == 'pipeline')
            {
                $objectNames['jenkins'] = $this->dao->select("id, $field AS name")->from($table)->where('id')->in($objectIdList)->andWhere('type')->eq('jenkins')->fetchPairs();
                $objectNames['gitlab']  = $this->dao->select("id, $field AS name")->from($table)->where('id')->in($objectIdList)->andWhere('type')->eq('gitlab')->fetchPairs();
            }
            else
            {
                $objectNames[$objectType] = $this->dao->select("id, $field AS name")->from($table)->where('id')->in($objectIdList)->fetchPairs();
            }
        }

        /* Add name field to the trashes. */
        foreach($trashes as $trash)
        {
            $objectType = $trash->objectType;
            if($objectType == 'pipeline')
            {
                if(isset($objectNames['gitlab'][$trash->objectID]))  $objectType = 'gitlab';
                if(isset($objectNames['jenkins'][$trash->objectID])) $objectType = 'jenkins';
                $trash->objectType = $objectType;
            }

            $trash->objectName = isset($objectNames[$objectType][$trash->objectID]) ? $objectNames[$objectType][$trash->objectID] : '';
        }
        return $trashes;
    }

    /**
     * Get histories of an action.
     *
     * @param  int    $actionID
     * @access public
     * @return array
     */
    public function getHistory($actionID)
    {
        return $this->dao->select()->from(TABLE_HISTORY)->where('action')->in($actionID)->orderBy('id')->fetchGroup('action');
    }

    /**
     * Log histories for an action.
     *
     * @param  int    $actionID
     * @param  array  $changes
     * @access public
     * @return void
     */
    public function logHistory($actionID, $changes)
    {
        if(empty($actionID)) return false;
        foreach($changes as $change)
        {
            if(is_object($change))
            {
                $change->action = $actionID;
            }
            else
            {
                $change['action'] = $actionID;
            }
            $this->dao->insert(TABLE_HISTORY)->data($change)->exec();
        }
    }

    /**
     * Print actions of an object.
     *
     * @param  object    $action
     * @param  string   $desc
     * @access public
     * @return void
     */
    public function printAction($action, $desc = '')
    {
        if(!isset($action->objectType) or !isset($action->action)) return false;

        $objectType = $action->objectType;
        $actionType = strtolower($action->action);

        /**
         * Set the desc string of this action.
         *
         * 1. If the module of this action has defined desc of this actionType, use it.
         * 2. If no defined in the module language, search the common action define.
         * 3. If not found in the lang->action->desc, use the $lang->action->desc->common or $lang->action->desc->extra as the default.
         */
        if(empty($desc))
        {
            if($action->objectType == 'story' and $action->action == 'reviewed' and strpos($action->extra, ',') !== false)
            {
                $desc = $this->lang->$objectType->action->rejectreviewed;
            }
            elseif($action->objectType == 'productplan' and in_array($action->action, array('startedbychild','finishedbychild','closedbychild','activatedbychild', 'createchild')))
            {
                $desc = $this->lang->$objectType->action->changebychild;
            }
            elseif(isset($this->config->maxVersion) and strpos($this->config->action->assetType, $action->objectType) !== false and $action->action == 'approved')
            {
                $desc = empty($this->lang->action->approve->{$action->extra}) ? '' : $this->lang->action->approve->{$action->extra};
            }
            elseif(isset($this->lang->$objectType) && isset($this->lang->$objectType->action->$actionType))
            {
                $desc = $this->lang->$objectType->action->$actionType;
            }
            elseif(isset($this->lang->action->desc->$actionType))
            {
                $desc = $this->lang->action->desc->$actionType;
            }
            else
            {
                $desc = $action->extra ? $this->lang->action->desc->extra : $this->lang->action->desc->common;
            }
        }

        if($this->app->getViewType() == 'mhtml') $action->date = date('m-d H:i', strtotime($action->date));

        /* Cycle actions, replace vars. */
        foreach($action as $key => $value)
        {
            if($key == 'history') continue;

            /* Desc can be an array or string. */
            if(is_array($desc))
            {
                if($key == 'extra') continue;
                if($action->objectType == 'story' and $action->action = 'reviewed' and strpos($action->extra, '|') !== false and $key == 'actor')
                {
                    $desc['main'] = str_replace('$actor', $this->lang->action->superReviewer . ' ' . $value, $desc['main']);
                }
                else
                {
                    $desc['main'] = str_replace('$' . $key, $value, $desc['main']);
                }
            }
            else
            {
                $desc = str_replace('$' . $key, $value, $desc);
            }
        }

        /* If the desc is an array, process extra. Please bug/lang. */
        if(is_array($desc))
        {
            $extra = strtolower($action->extra);

            /* Fix bug #741. */
            if(isset($desc['extra'])) $desc['extra'] = $this->lang->$objectType->{$desc['extra']};

            $actionDesc = '';
            if(isset($desc['extra'][$extra]))
            {
                $actionDesc = str_replace('$extra', $desc['extra'][$extra], $desc['main']);
            }
            else
            {
                $actionDesc = str_replace('$extra', $action->extra, $desc['main']);
            }

            if($action->objectType == 'story' and $action->action == 'reviewed')
            {
                if(strpos($action->extra, ',') !== false)
                {
                    list($extra, $reason) = explode(',', $extra);
                    $desc['reason'] = $this->lang->$objectType->{$desc['reason']};
                    $actionDesc = str_replace(array('$extra', '$reason'), array($desc['extra'][$extra], $desc['reason'][$reason]), $desc['main']);
                }

                if(strpos($action->extra, '|') !== false)
                {
                    list($extra, $isSuperReviewer) = explode('|', $extra);
                    $actionDesc = str_replace('$extra', $desc['extra'][$extra], $desc['main']);
                }
            }
            echo $actionDesc;
        }
        else
        {
            echo $desc;
        }
    }

    /**
     * Get actions as dynamic.
     *
     * @param  string $account
     * @param  string $period
     * @param  string $orderBy
     * @param  object $pager
     * @param  string|int $productID   all|int(like 123)|notzero   all => include zero, notzero, greater than 0
     * @param  string|int $projectID   same as productID
     * @param  string|int $executionID same as productID
     * @param  string $date
     * @param  string $direction
     * @access public
     * @return array
     */
    public function getDynamic($account = 'all', $period = 'all', $orderBy = 'date_desc', $pager = null, $productID = 'all', $projectID = 'all', $executionID = 'all', $date = '', $direction = 'next')
    {
        /* Computer the begin and end date of a period. */
        $beginAndEnd = $this->computeBeginAndEnd($period);
        extract($beginAndEnd);

        /* Build has priv condition. */
        $condition = 1;
        if($productID == 'all')   $products   = $this->app->user->view->products;
        if($projectID == 'all')   $projects   = $this->app->user->view->projects;
        if($executionID == 'all') $executions = $this->app->user->view->sprints;

        if($productID == 'all' or $projectID == 'all')
        {
            $productCondition   = $productID   == 'all' ? "product " . helper::dbIN($products) : '';
            $projectCondition   = $projectID   == 'all' ? "project " . helper::dbIN($projects) : '';
            $executionCondition = $executionID == 'all' ? "execution " . helper::dbIN($executions) : '';
            if(is_numeric($productID))   $productCondition = "product like '%,$productID,%' or product = '$productID'";
            if(is_numeric($projectID))   $projectCondition = "project = '$projectID'";
            if(is_numeric($executionID)) $executionCondition = "execution = '$executionID'";

            $condition = "((product =',0,' or product=0) AND project = '0' AND execution = 0)";
            if($productCondition)   $condition .= ' OR ' . $productCondition;
            if($projectCondition)   $condition .= ' OR ' . $projectCondition;
            if($executionCondition) $condition .= ' OR ' . $executionCondition;
            if($this->app->user->admin) $condition = 1;
        }

        /* If is project, select its related. */
        $executions = array();
        if(is_numeric($projectID) and $executionID == 'all') $executions = $this->loadModel('execution')->getPairs($projectID) + array(0 => 0);

        $this->loadModel('doc');
        $libs = $this->doc->getLibs('includeDeleted') + array('' => '');
        $docs = $this->doc->getPrivDocs(array_keys($libs), 0, 'all');

        $actionCondition = $this->getActionCondition();
        if(!$actionCondition and !$this->app->user->admin and isset($this->app->user->rights['acls']['actions'])) return array();

        /* Get actions. */
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->notIN('kanbanregion,kanbanlane,kanbancolumn')
            ->beginIF($period != 'all')->andWhere('date')->gt($begin)->fi()
            ->beginIF($period != 'all')->andWhere('date')->lt($end)->fi()
            ->beginIF($date)->andWhere('date' . ($direction == 'next' ? '<' : '>') . "'{$date}'")->fi()
            ->beginIF($account != 'all')->andWhere('actor')->eq($account)->fi()
            ->beginIF(is_numeric($productID))->andWhere('product')->like("%,$productID,%")->fi()
            ->andWhere()
            ->markLeft(1)
            ->where(1)
            ->beginIF(is_numeric($projectID))->andWhere('project')->eq($projectID)->fi()
            ->beginIF(!empty($executions))->andWhere('execution')->in(array_keys($executions))->fi()
            ->beginIF(is_numeric($executionID))->andWhere('execution')->eq($executionID)->fi()
            ->markRight(1)
            ->beginIF($productID == 'notzero')->andWhere('product')->gt(0)->andWhere('product')->notlike('%,0,%')->fi()
            ->beginIF($projectID == 'notzero')->andWhere('project')->gt(0)->fi()
            ->beginIF($executionID == 'notzero')->andWhere('execution')->gt(0)->fi()
            ->beginIF($productID == 'all' or $projectID == 'all' or $executionID == 'all')->andWhere("IF((objectType!= 'doc' && objectType!= 'doclib'), ($condition), '1=1')")->fi()
            ->beginIF($docs and !$this->app->user->admin)->andWhere("IF(objectType != 'doc' || (objectType = 'doc' && (action = 'approved' || action = 'removed')), '1=1', objectID " . helper::dbIN($docs) . ")")->fi()
            ->beginIF($libs and !$this->app->user->admin)->andWhere("IF(objectType != 'doclib', '1=1', objectID " . helper::dbIN(array_keys($libs)) . ') ')->fi()
            ->beginIF($actionCondition)->andWhere("($actionCondition)")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        if(!$actions) return array();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'action');
        return $this->transformActions($actions);
    }

    /**
     * Get dynamic show action.
     *
     * @return String
     */
    public function getActionCondition()
    {
        if($this->app->user->admin) return '';

        $actionCondition = '';
        if(isset($this->app->user->rights['acls']['actions']))
        {
            if(empty($this->app->user->rights['acls']['actions'])) return '';

            foreach($this->app->user->rights['acls']['actions'] as $moduleName => $actions)
            {
                $actionCondition .= "(`objectType` = '$moduleName' and `action` " . helper::dbIN($actions) . ") or ";
            }
            $actionCondition = trim($actionCondition, 'or ');
        }
        return $actionCondition;
    }

    /**
     * Get dynamic by search.
     *
     * @param  array  $products
     * @param  array  $projects
     * @param  array  $executions
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $date
     * @param  string $direction
     * @access public
     * @return array
     */
    public function getDynamicBySearch($products, $projects, $executions, $queryID, $orderBy = 'date_desc', $pager = null, $date = '', $direction = 'next')
    {
        $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';

        /* Get the sql and form status from the query. */
        if($query)
        {
            $this->session->set('actionQuery', $query->sql);
            $this->session->set('actionForm', $query->form);
        }
        if($this->session->actionQuery == false) $this->session->set('actionQuery', ' 1 = 1');

        $allProducts   = "`product` = 'all'";
        $allProjects   = "`project` = 'all'";
        $allExecutions = "`execution` = 'all'";
        $actionQuery   = $this->session->actionQuery;

        $productID = 0;
        if(preg_match("/`product` = '(\d*)'/", $actionQuery, $out))
        {
            $productID = $out[1];
        }

        /* If the sql not include 'product', add check purview for product. */
        if(strpos($actionQuery, $allProducts) !== false)
        {
            $actionQuery = str_replace($allProducts, '1', $actionQuery);
        }

        /* If the sql not include 'project', add check purview for project. */
        if(strpos($actionQuery, $allProjects) !== false)
        {
            $actionQuery = str_replace($allProjects, '1', $actionQuery);
        }

        /* If the sql not include 'execution', add check purview for execution. */
        if(strpos($actionQuery, $allExecutions) !== false)
        {
            $actionQuery = str_replace($allExecutions, '1', $actionQuery);
        }

        $actionQuery = str_replace("`product` = '$productID'", "`product` LIKE '%,$productID,%'", $actionQuery);

        if($date) $actionQuery = "($actionQuery) AND " . ('date' . ($direction == 'next' ? '<' : '>') . "'{$date}'");
        $actions = $this->getBySQL($actionQuery, $orderBy, $pager);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'action');
        if(!$actions) return array();
        return $this->transformActions($actions);
    }

    /**
     * Get actions by SQL.
     *
     * @param  string $sql
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getBySQL($sql, $orderBy, $pager = null)
    {
        $actionCondition = $this->getActionCondition();
        if(is_array($actionCondition)) return array();

        return $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where($sql)
            ->beginIF(!empty($actionCondition))->andWhere("($actionCondition)")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Transform the actions for display.
     *
     * @param  array    $actions
     * @access public
     * @return object
     */
    public function transformActions($actions)
    {
        $this->app->loadLang('todo');
        $this->app->loadLang('stakeholder');
        $this->app->loadLang('branch');

        /* Get commiters and the same department users. */
        $commiters = $this->loadModel('user')->getCommiters();
        $deptUsers = isset($this->app->user->dept) ? $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id') : '';

        /* Get object names, object projects and requirements by actions. */
        $relatedData     = $this->getRelatedDataByActions($actions);
        $objectNames     = $relatedData['objectNames'];
        $relatedProjects = $relatedData['relatedProjects'];
        $requirements    = $relatedData['requirements'];

        foreach($actions as $i => $action)
        {
            /* Add name field to the actions. */
            $action->objectName = isset($objectNames[$action->objectType][$action->objectID]) ? $objectNames[$action->objectType][$action->objectID] : '';

            if($action->objectType =='program' and strpos('syncexecution,syncproject,syncprogram', $action->action) !==false)
            {
                $action->objectName .= $this->lang->action->label->startProgram;
            }
            elseif($action->objectType == 'branch' and $action->action == 'mergedbranch')
            {
                if($action->objectID == 0) $action->objectName = $this->lang->branch->main;
                $action->objectName = '"' . $action->extra . ' "' . $this->lang->action->to . ' "' . $action->objectName . '"';
            }
            elseif($action->objectType == 'user')
            {
                $user = $this->dao->select('id,realname')->from(TABLE_USER)->where('id')->eq($action->objectID)->fetch();
                if($user) $action->objectName = $user->realname;
            }
            elseif($action->objectType == 'kanbancard' and strpos($action->action, 'imported') !== false and $action->action != 'importedcard')
            {
                $objectType  = str_replace('imported', '', $action->action);
                $objectTable = zget($this->config->objectTables, $objectType);
                $objectName  = $objectType == 'productplan' ? 'title' : 'name';
                $action->objectName = $this->dao->select($objectName)->from($objectTable)->where('id')->eq($action->extra)->fetch($objectName);
            }

            $projectID = isset($relatedProjects[$action->objectType][$action->objectID]) ? $relatedProjects[$action->objectType][$action->objectID] : 0;

            $actionType = strtolower($action->action);
            $objectType = strtolower($action->objectType);

            $action->originalDate = $action->date;
            $action->date         = date(DT_MONTHTIME2, strtotime($action->date));
            $action->actionLabel  = isset($this->lang->$objectType->$actionType) ? $this->lang->$objectType->$actionType : $action->action;
            $action->actionLabel  = isset($this->lang->action->label->$actionType) ? $this->lang->action->label->$actionType : $action->actionLabel;
            $action->objectLabel  = $this->getObjectLabel($objectType, $action->objectID, $actionType, $requirements);

            /* If action type is login or logout, needn't link. */
            if($actionType == 'svncommited' or $actionType == 'gitcommited') $action->actor = zget($commiters, $action->actor);

            /* Get gitlab objectname. */
            if(substr($objectType, 0,6) == 'gitlab') $action->objectName = $action->extra;

            /* Other actions, create a link. */
            if(!$this->setObjectLink($action, $deptUsers))
            {
                unset($actions[$i]);
                continue;
            }

            $action->major = (isset($this->config->action->majorList[$action->objectType]) && in_array($action->action, $this->config->action->majorList[$action->objectType])) ? 1 : 0;
        }
        return $actions;
    }

    /**
     * Get related data by actions.
     *
     * @param  array    $actions
     * @access public
     * @return array
     */
    public function getRelatedDataByActions($actions)
    {
        $objectNames     = array();
        $relatedProjects = array();
        $requirements    = array();

        foreach($actions as $object) $objectTypes[$object->objectType][$object->objectID] = $object->objectID;
        foreach($objectTypes as $objectType => $objectIdList)
        {
            if(!isset($this->config->objectTables[$objectType]) and $objectType != 'makeup') continue;    // If no defination for this type, omit it.

            $table = $objectType == 'makeup' ? '`' . $this->config->db->prefix . 'overtime`' : $this->config->objectTables[$objectType];
            $field = zget($this->config->action->objectNameFields, $objectType, '');
            if(empty($field)) continue;

            if($table != TABLE_TODO)
            {
                $objectName     = array();
                $relatedProject = array();

                if(strpos(",{$this->config->action->needGetProjectType},", ",{$objectType},") !== false)
                {
                    $objectInfo = $this->dao->select("id, project, $field AS name")->from($table)->where('id')->in($objectIdList)->fetchAll();
                    foreach($objectInfo as $object)
                    {
                        $objectName[$object->id]     = $object->name;
                        $relatedProject[$object->id] = $object->project;
                    }
                }
                elseif($objectType == 'project' or $objectType == 'execution')
                {
                    $objectInfo = $this->dao->select("id, project, $field AS name")->from($table)->where('id')->in($objectIdList)->fetchAll();
                    foreach($objectInfo as $object)
                    {
                        $objectName[$object->id]     = $object->name;
                        $relatedProject[$object->id] = $object->project > 0 ? $object->project : $object->id;
                    }
                }
                elseif($objectType == 'story')
                {
                    $objectInfo = $this->dao->select('id,title,type')->from($table)->where('id')->in($objectIdList)->fetchAll();
                    foreach($objectInfo as $object)
                    {
                        $objectName[$object->id] = $object->title;
                        if($object->type == 'requirement') $requirements[$object->id] = $object->id;
                    }
                }
                elseif($objectType == 'reviewcl')
                {
                    $objectInfo = $this->dao->select('id,title')->from($table)->where('id')->in($objectIdList)->fetchAll();
                    foreach($objectInfo as $object)
                    {
                        $objectName[$object->id] = $object->title;
                    }
                }
                elseif($objectType == 'team')
                {
                    $objectInfo = $this->dao->select('id,team,type')->from(TABLE_PROJECT)->where('id')->in($objectIdList)->fetchAll();
                    foreach($objectInfo as $object)
                    {
                        $objectName[$object->id] = $object->team;
                        if($object->type == 'project') $relatedProject[$object->id] = $object->id;
                    }
                }
                elseif($objectType == 'stakeholder')
                {
                    $objectName = $this->dao->select("t1.id, t2.realname")->from($table)->alias('t1')
                        ->leftJoin(TABLE_USER)->alias('t2')->on("t1.{$field} = t2.account")
                        ->where('t1.id')->in($objectIdList)
                        ->fetchPairs();
                }
                elseif($objectType == 'branch')
                {
                    $this->app->loadLang('branch');
                    $objectName = $this->dao->select("id,name")->from(TABLE_BRANCH)->where('id')->in($objectIdList)->fetchPairs();
                    if(in_array(BRANCH_MAIN, $objectIdList)) $objectName[BRANCH_MAIN] = $this->lang->branch->main;
                }
                else
                {
                    $objectName = $this->dao->select("id, $field AS name")->from($table)->where('id')->in($objectIdList)->fetchPairs();
                }

                $objectNames[$objectType]     = $objectName;
                $relatedProjects[$objectType] = $relatedProject;
            }
            else
            {
                $todos = $this->dao->select("id, $field AS name, account, private, type, idvalue")->from($table)->where('id')->in($objectIdList)->fetchAll('id');
                foreach($todos as $id => $todo)
                {
                    if($todo->type == 'task') $todo->name = $this->dao->findById($todo->idvalue)->from(TABLE_TASK)->fetch('name');
                    if($todo->type == 'bug')  $todo->name = $this->dao->findById($todo->idvalue)->from(TABLE_BUG)->fetch('title');

                    $objectNames[$objectType][$id] = $todo->name;
                    if($todo->private == 1 and $todo->account != $this->app->user->account) $objectNames[$objectType][$id] = $this->lang->todo->thisIsPrivate;
                }
            }
        }
        $objectNames['user'][0] = 'guest';    // Add guest account.

        $relatedData['objectNames']     = $objectNames;
        $relatedData['relatedProjects'] = $relatedProjects;
        $relatedData['requirements']    = $requirements;
        return $relatedData;
    }

    /**
     * Get object label.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $actionType
     * @param  array  $requirements
     * @access public
     * @return string
     */
    public function getObjectLabel($objectType, $objectID, $actionType, $requirements)
    {
        $actionObjectLabel = $objectType;
        if(isset($this->lang->action->label->$objectType))
        {
            $objectLabel = $this->lang->action->label->$objectType;

            /* Replace story to requirement. */
            if(isset($requirements[$objectID]) and is_string($objectLabel)) $objectLabel = str_replace($this->lang->SRCommon, $this->lang->URCommon, $objectLabel);

            if(!is_array($objectLabel)) $actionObjectLabel = $objectLabel;
            if(is_array($objectLabel) and isset($objectLabel[$actionType])) $actionObjectLabel = $objectLabel[$actionType];
        }

        if(isset($this->config->maxVersion) and $objectType == 'assetlib')
        {
            $libType = $this->dao->select('type')->from(TABLE_ASSETLIB)->where('id')->eq($objectID)->fetch('type');
            if(strpos('story,issue,risk,opportunity,practice,component', $libType) !== false) $actionObjectLabel = $this->lang->action->label->{$libType . 'assetlib'};
        }

        return $actionObjectLabel;
    }

    /**
     * Set objectLink
     *
     * @param  object   $action
     * @param  array    $deptUsers
     * @access public
     * @return object|bool
     */
    public function setObjectLink($action, $deptUsers)
    {
        $action->objectLink  = '';
        $action->objectLabel = zget($this->lang->action->objectTypes, $action->objectLabel);

        if(strpos($action->objectLabel, '|') !== false)
        {
            list($objectLabel, $moduleName, $methodName, $vars) = explode('|', $action->objectLabel);

            /* Fix bug #2961. */
            $isLoginOrLogout = $action->objectType == 'user' and ($action->action == 'login' or $action->action == 'logout');
            if(!common::hasPriv($moduleName, $methodName) and !$isLoginOrLogout) return false;

            $action->objectLabel = $objectLabel;
            $action->product     = trim($action->product, ',');

            if(isset($this->config->maxVersion)
               and strpos($this->config->action->assetType, $action->objectType) !== false
               and empty($action->project) and empty($action->product) and empty($action->execution))
            {
                if($action->objectType == 'doc')
                {
                    $assetLibType = $this->dao->select('assetLibType')->from(TABLE_DOC)->where('id')->eq($action->objectID)->fetch('assetLibType');
                    $method       = $assetLibType == 'practice' ? 'practiceView' : 'componentView';
                }
                else
                {
                    $method = $this->config->action->assetViewMethod[$action->objectType];
                }

                $action->objectLink = helper::createLink('assetlib', $method, sprintf($vars, $action->objectID));
            }
            else
            {
                if($action->objectType == 'doclib')
                {
                    $libID = $action->objectID;
                    $type  = 'custom';
                    if($action->execution != 0)   $type = 'execution';
                    if($action->project   != 0)   $type = 'project';
                    if($action->product != ',0,') $type = 'product';

                    $libObjectID = $type != 'custom' ? $action->$type : '';
                    $libObjectID = trim($libObjectID, ',');
                    if(empty($libObjectID) and $type != 'custom') return false;

                    $params = sprintf($vars, $type, $libObjectID, $libID);
                }
                elseif($action->objectType == 'api')
                {
                    $api = $this->dao->select('id,lib,module')->from(TABLE_API)->where('id')->eq($action->objectID)->fetch();
                    $params = sprintf($vars, $api->lib, $api->module, $api->id);
                }
                elseif($action->objectType == 'branch')
                {
                    $params = sprintf($vars, trim($action->product, ','));
                }
                elseif($action->objectType == 'kanbanspace')
                {
                    $kanbanSpace = $this->dao->select('type')->from(TABLE_KANBANSPACE)->where('id')->eq($action->objectID)->fetch();
                    $params = sprintf($vars, $kanbanSpace->type);
                }
                elseif($action->objectType == 'kanbancolumn' or $action->objectType == 'kanbanlane')
                {
                    $params = sprintf($vars, $action->extra);
                }
                else
                {
                    $params = sprintf($vars, $action->objectID);
                }
                $action->objectLink = helper::createLink($moduleName, $methodName, $params);

                if($action->objectType == 'execution')
                {
                    $execution = $this->loadModel('execution')->getById($action->objectID);
                    if(!empty($execution) and $execution->type == 'kanban') $action->objectLink = helper::createLink('execution', 'kanban', "executionID={$action->objectID}");
                }

                if($action->objectType == 'doclib')
                {
                    $docLib             = $this->dao->select('type,product,project,execution,deleted')->from(TABLE_DOCLIB)->where('id')->eq($action->objectID)->fetch();
                    $docLib->objectID   = strpos('product,project,execution', $docLib->type) !== false ? $docLib->{$docLib->type} : 0;
                    $appendLib          = $docLib->deleted == '1' ? $action->objectID : 0;
                    if($docLib->type == 'api')
                    {
                        $action->objectLink = helper::createLink('api', 'index', "libID={$action->objectID}");
                    }
                    else
                    {
                        $action->objectLink = helper::createLink('doc', 'objectLibs', sprintf($vars, $docLib->type, $docLib->objectID, $action->objectID, $appendLib));
                    }
                }
                elseif($action->objectType == 'user')
                {
                    $action->objectLink = !isset($deptUsers[$action->objectID]) ? 'javascript:void(0)' : helper::createLink($moduleName, $methodName, sprintf($vars, $action->objectID));
                }
            }
        }
        elseif($action->objectType == 'team')
        {
            if($action->project)   $action->objectLink = helper::createLink('project',   'team', 'projectID=' . $action->project);
            if($action->execution) $action->objectLink = helper::createLink('execution', 'team', 'executionID=' . $action->execution);
        }

        if($action->objectType == 'stakeholder' and $action->project == 0) $action->objectLink = '';

        if($action->objectType == 'story' and $action->action == 'import2storylib')
        {
            $action->objectLink = helper::createLink('assetlib', 'storyView', "storyID=$action->objectID");
        }

        if(strpos(',kanbanregion,kanbancard,', ",{$action->objectType},") !== false)
        {
            $table    = $this->config->objectTables[$action->objectType];
            $kanbanID = $this->dao->select('kanban')->from($table)->where('id')->eq($action->objectID)->fetch('kanban');

            $action->objectLink = helper::createLink('kanban', 'view', "kanbanID=$kanbanID");
        }

        if(strpos(',kanbanlane,kanbancolumn,', ",{$action->objectType},") !== false and empty($action->extra))
        {
            $table    = $this->config->objectTables[$action->objectType];
            $kanbanID = $this->dao->select('t2.kanban')->from($table)->alias('t1')
                ->leftJoin(TABLE_KANBANREGION)->alias('t2')->on('t1.region=t2.id')
                ->where('t1.id')->eq($action->objectID)
                ->fetch('kanban');

            $action->objectLink = helper::createLink('kanban', 'view', "kanbanID=$kanbanID");
        }

        if($action->objectType == 'branch' and $action->action == 'mergedbranch')
        {
            $action->objectLink = 'javascript:void(0)';
        }

        return $action;
    }

    /**
     * Compute the begin date and end date of a period.
     *
     * @param  string    $period   all|today|yesterday|twodaysago|latest2days|thisweek|lastweek|thismonth|lastmonth
     * @access public
     * @return array
     */
    public function computeBeginAndEnd($period)
    {
        $this->app->loadClass('date');

        $today      = date('Y-m-d');
        $tomorrow   = date::tomorrow();
        $yesterday  = date::yesterday();
        $twoDaysAgo = date::twoDaysAgo();

        $period = strtolower($period);

        if($period == 'all')        return array('begin' => '1970-1-1',  'end' => '2109-1-1');
        if($period == 'today')      return array('begin' => $today,      'end' => $tomorrow);
        if($period == 'yesterday')  return array('begin' => $yesterday,  'end' => $today);
        if($period == 'twodaysago') return array('begin' => $twoDaysAgo, 'end' => $yesterday);
        if($period == 'latest3days')return array('begin' => $twoDaysAgo, 'end' => $tomorrow);

        /* If the period is by week, add the end time to the end date. */
        if($period == 'thisweek' or $period == 'lastweek')
        {
            $func = "get$period";
            extract(date::$func());
            return array('begin' => $begin, 'end' => $end . ' 23:59:59');
        }

        if($period == 'thismonth')  return date::getThisMonth();
        if($period == 'lastmonth')  return date::getLastMonth();
    }

    /**
     * Print changes of every action.
     *
     * @param  string    $objectType
     * @param  array     $histories
     * @param  bool      $canChangeTag
     * @access public
     * @return void
     */
    public function printChanges($objectType, $histories, $canChangeTag = true)
    {
        if(empty($histories)) return;

        $maxLength            = 0;          // The max length of fields names.
        $historiesWithDiff    = array();    // To save histories without diff info.
        $historiesWithoutDiff = array();    // To save histories with diff info.

        /* Diff histories by hasing diff info or not. Thus we can to make sure the field with diff show at last. */
        foreach($histories as $history)
        {
            $fieldName = $history->field;
            $history->fieldLabel = (isset($this->lang->$objectType) && isset($this->lang->$objectType->$fieldName)) ? $this->lang->$objectType->$fieldName : $fieldName;
            if(($length = strlen($history->fieldLabel)) > $maxLength) $maxLength = $length;
            $history->diff ? $historiesWithDiff[] = $history : $historiesWithoutDiff[] = $history;
        }
        $histories = array_merge($historiesWithoutDiff, $historiesWithDiff);

        foreach($histories as $history)
        {
            $history->fieldLabel = str_pad($history->fieldLabel, $maxLength, $this->lang->action->label->space);
            if($history->diff != '')
            {
                $history->diff      = str_replace(array('<ins>', '</ins>', '<del>', '</del>'), array('[ins]', '[/ins]', '[del]', '[/del]'), $history->diff);
                $history->diff      = ($history->field != 'subversion' and $history->field != 'git') ? htmlSpecialString($history->diff) : $history->diff;   // Keep the diff link.
                $history->diff      = str_replace(array('[ins]', '[/ins]', '[del]', '[/del]'), array('<ins>', '</ins>', '<del>', '</del>'), $history->diff);
                $history->diff      = nl2br($history->diff);
                $history->noTagDiff = $canChangeTag ? preg_replace('/&lt;\/?([a-z][a-z0-9]*)[^\/]*\/?&gt;/Ui', '', $history->diff) : '';
                printf($this->lang->action->desc->diff2, $history->fieldLabel, $history->noTagDiff, $history->diff);
            }
            else
            {
                printf($this->lang->action->desc->diff1, $history->fieldLabel, $history->old, $history->new);
            }
        }
    }

    /**
     * Undelete a record.
     *
     * @param  int      $actionID
     * @access public
     * @return void
     */
    public function undelete($actionID)
    {
        $action = $this->getById($actionID);
        if($action->action != 'deleted') return;

        if($this->config->systemMode == 'new' and $action->objectType == 'execution')
        {
            $execution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($action->objectID)->fetch();
            if($execution->deleted and empty($execution->project)) return print(js::error($this->lang->action->undeletedTips));
        }

        if($action->objectType == 'product')
        {
            $product = $this->dao->select('id,name,code,acl')->from(TABLE_PRODUCT)->where('id')->eq($action->objectID)->fetch();
            if($product->acl != 'open') $this->loadModel('user')->updateUserView($product->id, 'product');
        }
        elseif(in_array($action->objectType, array('program', 'project', 'execution')))
        {
            $project    = $this->dao->select('id,acl')->from(TABLE_PROJECT)->where('id')->eq($action->objectID)->fetch();
            $objecttype = $action->objectType == 'execution' ? 'sprint' : $action->objectType;
            if($project->acl != 'open') $this->loadModel('user')->updateUserView($project->id, $objecttype);
        }
        elseif($action->objectType == 'module')
        {
            $module     = $this->dao->select('*')->from(TABLE_MODULE)->where('id')->eq($action->objectID)->fetch();
            $repeatName = $this->loadModel('tree')->checkUnique($module);
            if($repeatName) return print(js::alert(sprintf($this->lang->tree->repeatName, $repeatName)));
        }

        /* Update deleted field in object table. */
        $table = $this->config->objectTables[$action->objectType];
        $this->dao->update($table)->set('deleted')->eq(0)->where('id')->eq($action->objectID)->exec();

        $this->loadModel('product');
        /* Revert userView products when undelete project or execution. */
        if($action->objectType == 'project' or $action->objectType == 'execution')
        {
            $products = $this->product->getProducts($project->id, 'all', '', false);
            if(!empty($products)) $this->loadModel('user')->updateUserView(array_keys($products), 'product');
        }

        /* Revert doclib when undelete product or project. */
        if($action->objectType == 'execution' or $action->objectType == 'product')
        {
            $this->dao->update(TABLE_DOCLIB)->set('deleted')->eq(0)->where($action->objectType)->eq($action->objectID)->exec();
        }

        /* Revert productplan parent status. */
        if($action->objectType == 'productplan') $this->loadModel('productplan')->changeParentField($action->objectID);

        /* Update task status when undelete child task. */
        if($action->objectType == 'task') $this->loadModel('task')->updateParentStatus($action->objectID);

        /* Update action record in action table. */
        $this->dao->update(TABLE_ACTION)->set('extra')->eq(ACTIONMODEL::BE_UNDELETED)->where('id')->eq($actionID)->exec();
        $this->create($action->objectType, $action->objectID, 'undeleted');
    }

    /**
     * Hide an object.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function hideOne($actionID)
    {
        $action = $this->getById($actionID);
        if($action->action != 'deleted') return;

        $this->dao->update(TABLE_ACTION)->set('extra')->eq(self::BE_HIDDEN)->where('id')->eq($actionID)->exec();
        $this->create($action->objectType, $action->objectID, 'hidden');
    }

    /**
     * Hide all deleted objects.
     *
     * @access public
     * @return void
     */
    public function hideAll()
    {
        $this->dao->update(TABLE_ACTION)
            ->set('extra')->eq(self::BE_HIDDEN)
            ->where('action')->eq('deleted')
            ->andWhere('extra')->eq(self::CAN_UNDELETED)
            ->exec();
    }

    /**
     * Update comment of a action.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function updateComment($actionID)
    {
        $action = $this->getById($actionID);
        $action->comment = trim(strip_tags($this->post->lastComment, $this->config->allowedTags));

        /* Process action. */
        $action = $this->loadModel('file')->processImgURL($action, 'comment', $this->post->uid);

        $this->dao->update(TABLE_ACTION)
            ->set('date')->eq(helper::now())
            ->set('comment')->eq($action->comment)
            ->where('id')->eq($actionID)
            ->exec();
        $this->file->updateObjectID($this->post->uid, $action->objectID, $action->objectType);
    }

    /**
     * Build date group by actions
     *
     * @param  array  $actions
     * @param  string $direction
     * @param  string $type all|today|yesterday|thisweek|lastweek|thismonth|lastmonth
     * @param  string $orderBy date_desc|date_asc
     * @access public
     * @return array
     */
    public function buildDateGroup($actions, $direction = 'next', $type = 'today', $orderBy = 'date_desc')
    {
        $dateGroup = array();
        foreach($actions as $action)
        {
            $timeStamp    = strtotime(isset($action->originalDate) ? $action->originalDate : $action->date);
            $date         = $type == 'all' ? date(DT_DATE3, $timeStamp) : date(DT_DATE4, $timeStamp);
            $action->time = date(DT_TIME2, $timeStamp);
            $dateGroup[$date][] = $action;
        }

        if($dateGroup)
        {
            $lastDateActions = $this->dao->select('*')->from(TABLE_ACTION)->where($this->session->actionQueryCondition)->andWhere("(LEFT(`date`, 10) = '" . substr($action->originalDate, 0, 10) . "')")->orderBy($this->session->actionOrderBy)->fetchAll('id');
            if(count($dateGroup[$date]) < count($lastDateActions))
            {
                unset($dateGroup[$date]);
                $lastDateActions = $this->transformActions($lastDateActions);
                foreach($lastDateActions as $action)
                {
                    $timeStamp    = strtotime(isset($action->originalDate) ? $action->originalDate : $action->date);
                    $date         = $type == 'all' ? date(DT_DATE3, $timeStamp) : date(DT_DATE4, $timeStamp);
                    $action->time = date(DT_TIME2, $timeStamp);
                    $dateGroup[$date][] = $action;
                }
            }
        }

        /* Modify date to the corrret order. */
        if($this->app->rawModule != 'company' and $direction != 'next')
        {
            $dateGroup = array_reverse($dateGroup);
        }
        elseif($this->app->rawModule == 'company')
        {
            if($direction == 'pre') $dateGroup = array_reverse($dateGroup);
            if(($direction == 'next' and $orderBy == 'date_asc') or ($direction == 'pre' and $orderBy == 'date_desc'))
            {
                foreach($dateGroup as $key => $dateItem) $dateGroup[$key] = array_reverse($dateItem);
            }
        }
        return $dateGroup;
    }

    /**
     * Check Has pre or next.
     *
     * @param  string $date
     * @param  string $direction
     * @access public
     * @return bool
     */
    public function hasPreOrNext($date, $direction = 'next')
    {
        $condition = $this->session->actionQueryCondition;

        /* Remove date condition for direction. */
        $condition = preg_replace("/AND +date[\<\>]'\d{4}\-\d{2}\-\d{2}'/", '', $condition);
        $count     = $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where($condition)
            ->andWhere('date' . ($direction == 'next' ? '<' : '>') . "'{$date}'")
            ->fetch('count');
        return $count > 0;
    }

    /**
     * Save global search object index information.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $actionType
     * @access public
     * @return bool
     */
    public function saveIndex($objectType, $objectID, $actionType)
    {
        $this->loadModel('search');
        $actionType = strtolower($actionType);
        if(!isset($this->config->search->fields->$objectType)) return true;
        if(strpos($this->config->search->buildAction, ",{$actionType},") === false and empty($_POST['comment'])) return true;
        if($actionType == 'deleted' or $actionType == 'erased') return $this->search->deleteIndex($objectType, $objectID);

        $field = $this->config->search->fields->$objectType;
        $query = $this->search->buildIndexQuery($objectType, $testDeleted = false);
        $data  = $query->andWhere('t1.' . $field->id)->eq($objectID)->fetch();
        if(empty($data)) return true;

        $data->comment = '';
        if($objectType == 'effort' and $data->objectType == 'task') return true;
        if($objectType == 'case')
        {
            $caseStep     = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->eq($objectID)->andWhere('version')->eq($data->version)->fetchAll();
            $data->desc   = '';
            $data->expect = '';
            foreach($caseStep as $step)
            {
                $data->desc   .= $step->desc . "\n";
                $data->expect .= $step->expect . "\n";
            }
        }

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->orderBy('id asc')
            ->fetchAll();
        foreach($actions as $action)
        {
            if($action->action == 'opened') $data->{$field->addedDate} = $action->date;
            $data->{$field->editedDate} = $action->date;
            if(!empty($action->comment)) $data->comment .= $action->comment . "\n";
        }

        $this->search->saveIndex($objectType, $data);
    }

    /**
     * Print actions of an object for API(JIHU).
     *
     * @param  object    $action
     * @access public
     * @return void
     */
    public function printActionForGitLab($action)
    {
        if(!isset($action->objectType) or !isset($action->action)) return false;

        $objectType = $action->objectType;
        $actionType = strtolower($action->action);

        if(isset($this->lang->action->apiTitle->$actionType) and isset($action->extra))
        {
            /* If extra column is a username, then assemble link to that. */
            if($action->action == "assigned")
            {
                $userDetails = $this->loadModel('user')->getUserDetailsForAPI($action->extra);
                if(isset($userDetails[$action->extra]))
                {
                    $userDetail    = $userDetails[$action->extra];
                    $action->extra = "<a href='{$userDetail->url}' target='_blank'>{$action->extra}</a>";
                }
            }

            echo sprintf($this->lang->action->apiTitle->$actionType, $action->extra);
        }
        elseif(isset($this->lang->action->apiTitle->$actionType) and !isset($action->extra))
        {
            echo $this->lang->action->apiTitle->$actionType;
        }
        else
        {
            echo $actionType;
        }
    }

    /**
     * Process action for API.
     *
     * @param  array  $actions
     * @param  array  $users
     * @param  array  $objectLang
     * @access public
     * @return array
     */
    public function processActionForAPI($actions, $users = array(), $objectLang = array())
    {
        $actions = (array)$actions;
        foreach($actions as $action)
        {
            $action->actor = zget($users, $action->actor);
            if($action->action == 'assigned') $action->extra = zget($users, $action->extra);
            if(strpos($action->actor, ':') !== false) $action->actor = substr($action->actor, strpos($action->actor, ':') + 1);

            ob_start();
            $this->printAction($action);
            $action->desc = ob_get_contents();
            ob_end_clean();

            if($action->history)
            {
                foreach($action->history as $i => $history)
                {
                    $history->fieldName = zget($objectLang, $history->field);
                    $action->history[$i] = $history;
                }
            }
        }
        return array_values($actions);
    }

    /**
     * Process dynamic for API.
     *
     * @param  array    $dynamics
     * @access public
     * @return array
     */
    public function processDynamicForAPI($dynamics)
    {
        $users = $this->loadModel('user')->getList();
        $simplifyUsers = array();
        foreach($users as $user)
        {
            $simplifyUser = new stdclass();
            $simplifyUser->id       = $user->id;
            $simplifyUser->account  = $user->account;
            $simplifyUser->realname = $user->realname;
            $simplifyUser->avatar   = $user->avatar;
            $simplifyUsers[$user->account] = $simplifyUser;
        }

        $actions = array();
        foreach($dynamics as $key => $dynamic)
        {
            if($dynamic->objectType == 'user') continue;

            $simplifyUser = zget($simplifyUsers, $dynamic->actor, '');
            $actor = $simplifyUser;
            if(empty($simplifyUser))
            {
                $actor = new stdclass();
                $actor->id       = 0;
                $actor->account  = $dynamic->actor;
                $actor->realname = $dynamic->actor;
                $actor->avatar   = '';
            }

            $dynamic->actor = $actor;
            $actions[]      = $dynamic;
        }

        return $actions;
    }
}
