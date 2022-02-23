<?php
class projectModel extends model
{
    /**
     * Get Multiple linked products for project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getMultiLinkedProducts($projectID)
    {
        $linkedProducts      = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $multiLinkedProducts = $this->dao->select('t3.id,t3.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->where('t1.product')->in($linkedProducts)
            ->andWhere('t1.project')->ne($projectID)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t3.deleted')->eq('0')
            ->fetchPairs('id', 'name');

        return $multiLinkedProducts;
    }

    /**
     * Show accessDenied response.
     *
     * @access private
     * @return void
     */
    public function accessDenied()
    {
        if(defined('TUTORIAL')) return true;

        echo(js::alert($this->lang->project->accessDenied));
        $this->session->set('project', '');

        return print(js::locate(helper::createLink('project', 'index')));
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object    $project
     * @param  string    $action
     * @access public
     * @return bool
     */
    public static function isClickable($project, $action)
    {
        $action = strtolower($action);

        if(empty($project)) return true;
        if(!isset($project->type)) return true;

        if($action == 'start')    return $project->status == 'wait' or $project->status == 'suspended';
        if($action == 'finish')   return $project->status == 'wait' or $project->status == 'doing';
        if($action == 'close')    return $project->status != 'closed';
        if($action == 'suspend')  return $project->status == 'wait' or $project->status == 'doing';
        if($action == 'activate') return $project->status == 'done' or $project->status == 'closed';

        return true;
    }

    /**
     * Check has content for project.
     *
     * @param  int    $projectID
     * @access public
     * @return bool
     */
    public function checkHasContent($projectID)
    {
        $count  = 0;
        $count += (int)$this->dao->select('count(*) as count')->from(TABLE_PROJECT)->where('parent')->eq($projectID)->fetch('count');
        $count += (int)$this->dao->select('count(*) as count')->from(TABLE_TASK)->where('project')->eq($projectID)->fetch('count');

        return $count > 0;
    }

    /**
     * Check has children project.
     *
     * @param  int    $projectID
     * @access public
     * @return bool
     */
    public function checkHasChildren($projectID)
    {
        $count = $this->dao->select('count(*) as count')->from(TABLE_PROGRAM)->where('parent')->eq($projectID)->fetch('count');
        return $count > 0;
    }

    /**
     * Get budget unit list.
     *
     * @access public
     * @return array
     */
    public function getBudgetUnitList()
    {
        $budgetUnitList = array();
        foreach(explode(',', $this->config->project->unitList) as $unit) $budgetUnitList[$unit] = zget($this->lang->project->unitList, $unit, '');

        return $budgetUnitList;
    }

    /**
     * Save project state.
     *
     * @param  int    $projectID
     * @param  array  $projects
     * @access public
     * @return int
     */
    public function saveState($projectID = 0, $projects = array())
    {
        if($projectID == 0 and $this->cookie->lastProject) $projectID = $this->cookie->lastProject;
        if($projectID == 0 and $this->session->project == '') $projectID = key($projects);
        $this->session->set('project', (int)$projectID, $this->app->tab);

        if(!isset($projects[$this->session->project]))
        {
            $this->session->set('project', key($projects), $this->app->tab);
            if($projectID and strpos(",{$this->app->user->view->projects},", ",{$this->session->project},") === false and !empty($projects)) $this->accessDenied();
        }

        return $this->session->project;
    }

    /*
     * Get project swapper.
     *
     * @param  int     $projectID
     * @param  string  $currentModule
     * @param  string  $currentMethod
     * @access public
     * @return string
     */
    public function getSwitcher($projectID, $currentModule, $currentMethod)
    {
        if($currentModule == 'project' and $currentMethod == 'browse') return;

        $currentProjectName = $this->lang->project->common;
        if($projectID)
        {
            $currentProject     = $this->getById($projectID);
            $currentProjectName = $currentProject->name;
        }

        if($this->app->viewType == 'mhtml' and $projectID)
        {
            $output  = $this->lang->project->common . $this->lang->colon;
            $output .= "<a id='currentItem' href=\"javascript:showSearchMenu('project', '$projectID', '$currentModule', '$currentMethod', '')\">{$currentProjectName} <span class='icon-caret-down'></span></a><div id='currentItemDropMenu' class='hidden affix enter-from-bottom layer'></div>";
            return $output;
        }

        $dropMenuLink = helper::createLink('project', 'ajaxGetDropMenu', "objectID=$projectID&module=$currentModule&method=$currentMethod");
        $output  = "<div class='btn-group header-btn' id='swapper'><button data-toggle='dropdown' type='button' class='btn' id='currentItem' title='{$currentProjectName}'><span class='text'>{$currentProjectName}</span> <span class='caret' style='margin-bottom: -1px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";

        return $output;
    }

    /**
     * Get a project by id.
     *
     * @param  int    $projectID
     * @param  string $type  project|sprint,stage
     * @access public
     * @return object
     */
    public function getByID($projectID, $type = 'project')
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProject();

        $project = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->andWhere('`type`')->in($type)->fetch();
        if(!$project) return false;

        if($project->end == '0000-00-00') $project->end = '';
        $project = $this->loadModel('file')->replaceImgURL($project, 'desc');
        return $project;
    }

    /**
     * Get project info.
     *
     * @param  string    $status
     * @param  string    $orderBy
     * @param  int       $pager
     * @access public
     * @return array
     */
    public function getInfoList($status = 'undone', $orderBy = 'order_desc', $pager = null)
    {
        /* Init vars. */
        $projects = $this->loadModel('program')->getProjectList(0, $status, 0, $orderBy, $pager, 0, 1);
        if(empty($projects)) return array();

        $projectIdList = array_keys($projects);
        $teams = $this->dao->select('root, count(*) as count')->from(TABLE_TEAM)
            ->where('root')->in($projectIdList)
            ->groupBy('root')
            ->fetchAll('root');

        $estimates = $this->dao->select('t2.parent as project, sum(estimate) as estimate')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.parent')->in($projectIdList)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            ->groupBy('project')
            ->fetchAll('project');

        $this->app->loadClass('pager', $static = true);
        foreach($projects as $projectID => $project)
        {
            $orderBy = $project->model == 'waterfall' ? 'id_asc' : 'id_desc';
            $pager   = $project->model == 'waterfall' ? null : new pager(0, 1, 1);
            $project->executions = $this->getStats($projectID, 'undone', 0, 0, 30, $orderBy, $pager);
            $project->teamCount  = isset($teams[$projectID]) ? $teams[$projectID]->count : 0;
            $project->estimate   = isset($estimates[$projectID]) ? round($estimates[$projectID]->estimate, 2) : 0;
            $project->parentName = $this->getParentName($project->parent);
        }
        return $projects;
    }

    /**
     * Gets the top-level project name.
     *
     * @param  int       $parentID
     * @access private
     * @return string
     */
    public function getParentName($parentID = 0)
    {
        if($parentID == 0) return '';

        static $parent;
        $parent = $this->dao->select('id,parent,name')->from(TABLE_PROJECT)->where('id')->eq($parentID)->fetch();
        if($parent->parent) $this->getParentName($parent->parent);

        return $parent->name;
    }

    /**
     * Get project overview for block.
     *
     * @param  string     $queryType byId|byStatus
     * @param  string|int $param
     * @param  string     $orderBy
     * @param  int        $limit
     * @access public
     * @return array
     */
    public function getOverviewList($queryType = 'byStatus', $param = 'all', $orderBy = 'id_desc', $limit = 15)
    {
        $queryType = strtolower($queryType);
        $projects = $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->beginIF($queryType == 'bystatus' and $param == 'undone')->andWhere('status')->notIN('done,closed')->fi()
            ->beginIF($queryType == 'bystatus' and $param != 'all' and $param != 'undone')->andWhere('status')->eq($param)->fi()
            ->beginIF($queryType == 'byid')->andWhere('id')->eq($param)->fi()
            ->orderBy($orderBy)
            ->limit($limit)
            ->fetchAll('id');

        if(empty($projects)) return array();
        $projectIdList = array_keys($projects);

        $teams = $this->dao->select('root, count(*) as teams')->from(TABLE_TEAM)
            ->where('root')->in($projectIdList)
            ->andWhere('type')->eq('project')
            ->groupBy('root')->fetchPairs();

        $hours = $this->dao->select('t2.parent as project, ROUND(SUM(t1.consumed), 1) AS consumed, ROUND(SUM(t1.estimate), 1) AS estimate')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.project')->in($projectIdList)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            ->groupBy('t2.project')
            ->fetchAll('project');

        $leftTasks = $this->dao->select('t2.parent as project, count(*) as tasks')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.project')->in($projectIdList)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.status')->in('wait,doing,pause')
            ->groupBy('t2.project')
            ->fetchPairs();

        $this->loadModel('product');
        foreach($projectIdList as $projectID)
        {
            $productIdList = $this->product->getProductIDByProject($projectID, false);

            $allStories[$projectID]  = $this->getTotalStoriesByProject($projectID, $productIdList, 'story');
            $doneStories[$projectID] = $this->getTotalStoriesByProject($projectID, $productIdList, 'story', 'closed');
            $leftStories[$projectID] = $this->getTotalStoriesByProject($projectID, $productIdList, 'story', 'active');
        }

        $leftBugs = $this->getTotalBugByProject($projectIdList, 'active');
        $allBugs  = $this->getTotalBugByProject($projectIdList, 'all');
        $doneBugs = $this->getTotalBugByProject($projectIdList, 'resolved');

        foreach($projects as $projectID => $project)
        {
            $project->consumed    = isset($hours[$projectID]) ? (float)$hours[$projectID]->consumed : 0;
            $project->estimate    = isset($hours[$projectID]) ? (float)$hours[$projectID]->estimate : 0;
            $project->teamCount   = isset($teams[$projectID]) ? $teams[$projectID] : 0;
            $project->leftTasks   = isset($leftTasks[$projectID]) ? $leftTasks[$projectID] : 0;
            $project->leftBugs    = isset($leftBugs[$projectID])  ? $leftBugs[$projectID]  : 0;
            $project->allBugs     = isset($allBugs[$projectID])   ? $allBugs[$projectID]   : 0;
            $project->doneBugs    = isset($doneBugs[$projectID])  ? $doneBugs[$projectID]  : 0;
            $project->allStories  = $allStories[$projectID];
            $project->doneStories = $doneStories[$projectID];
            $project->leftStories = $leftStories[$projectID];

            if(is_float($project->consumed)) $project->consumed = round($project->consumed, 1);
            if(is_float($project->estimate)) $project->estimate = round($project->estimate, 1);
        }

        return $projects;
    }

    /**
     * Get the number of stories associated with the project.
     *
     * @param  int     $projectID
     * @param  array   $productIdList
     * @param  string  $type          story|requirement
     * @param  string  $status        all|closed|active
     * @access public
     * @return int
     */
    public function getTotalStoriesByProject($projectID = 0, $productIdList = array(), $type = 'story', $status = 'all')
    {
        return $this->dao->select('count(t2.id) as stories')->from(TABLE_PROJECTSTORY)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t2.type')->eq($type)
            ->andWhere('t2.product')->in($productIdList)
            ->beginIF($status == 'active')->andWhere('t2.status')->in('active,changed')->fi()
            ->beginIF($status == 'closed')->andWhere('t2.status')->eq('closed')->fi()
            ->beginIF($status == 'closed')->andWhere('t2.closedReason')->eq('done')->fi()
            ->andWhere('deleted')->eq('0')
            ->fetch('stories');
    }

    /**
     * Get project workhour info.
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function getWorkhour($projectID)
    {
        $total = $this->dao->select('ROUND(SUM(estimate), 1) AS totalEstimate, ROUND(SUM(`left`), 2) AS totalLeft')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.project')->in($projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            ->fetch();

        $totalConsumed = $this->dao->select('ROUND(SUM(consumed), 1) AS totalConsumed')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.project')->in($projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            ->fetch('totalConsumed');

        $closedTotalLeft = $this->dao->select('ROUND(SUM(`left`), 2) AS totalLeft')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t2.project')->in($projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            ->andWhere('t1.status')->in('closed,cancel')
            ->fetch('totalLeft');

        $workhour = new stdclass();
        $workhour->totalHours    = $this->dao->select('sum(t1.days * t1.hours) AS totalHours')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.root=t2.id')
            ->where('t2.project')->in($projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.type')->eq('project')
            ->fetch('totalHours');
        $workhour->totalEstimate = $total->totalEstimate;
        $workhour->totalConsumed = $totalConsumed;
        $workhour->totalLeft     = round($total->totalLeft - $closedTotalLeft, 1);

        return $workhour;
    }

    /**
     * Get projects consumed info.
     *
     * @param  array    $projectID
     * @param  string   $time
     * @access public
     * @return array
     */
    public function getProjectsConsumed($projectIdList, $time = '')
    {
        $projects = array();

        $totalConsumeds = $this->dao->select('t2.project,ROUND(SUM(t1.consumed), 1) AS totalConsumed')->from(TABLE_TASKESTIMATE)->alias('t1')
            ->leftJoin(TABLE_TASK)->alias('t2')->on('t1.task=t2.id')
            ->where('t2.project')->in($projectIdList)
            ->beginIF($time == 'THIS_YEAR')->andWhere('LEFT(t1.`date`, 4)')->eq(date('Y'))->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.parent')->lt(1)
            ->groupBy('t2.project')
            ->fetchAll('project');

        foreach($projectIdList as $projectID)
        {
            $project = new stdClass();
            $project->totalConsumed = isset($totalConsumeds[$projectID]->totalConsumed) ? $totalConsumeds[$projectID]->totalConsumed : 0;
            $projects[$projectID]   = $project;
        }

        return $projects;
    }

    /**
     * Get project stat data .
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function getStatData($projectID)
    {
        $executions = $this->loadModel('execution')->getPairs($projectID);
        $storyCount = $this->dao->select('count(t2.story) as storyCount')->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_PROJECTSTORY)->alias('t2')->on('t1.id = t2.story')
            ->where('t2.project')->eq($projectID)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch('storyCount');

        $taskCount = $this->dao->select('count(id) as taskCount')->from(TABLE_TASK)->where('execution')->in(array_keys($executions))->andWhere('deleted')->eq(0)->fetch('taskCount');
        $bugCount  = $this->dao->select('count(id) as bugCount')->from(TABLE_BUG)->where('project')->in($projectID)->andWhere('deleted')->eq(0)->fetch('bugCount');

        $statusPairs   = $this->dao->select('status,count(id) as count')->from(TABLE_TASK)->where('execution')->in(array_keys($executions))->andWhere('deleted')->eq(0)->groupBy('status')->fetchPairs('status', 'count');
        $finishedCount = $this->dao->select('count(id) as taskCount')->from(TABLE_TASK)->where('execution')->in(array_keys($executions))->andWhere('finishedBy')->ne('')->andWhere('deleted')->eq(0)->fetch('taskCount');
        $delayedCount  = $this->dao->select('count(id) as count')->from(TABLE_TASK)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('deadline')->ne('0000-00-00')
            ->andWhere('deadline')->lt(helper::today())
            ->andWhere('status')->in('wait,doing')
            ->andWhere('deleted')->eq(0)
            ->fetch('count');

        $statData = new stdclass();
        $statData->storyCount    = $storyCount;
        $statData->taskCount     = $taskCount;
        $statData->bugCount      = $bugCount;
        $statData->waitCount     = zget($statusPairs, 'wait', 0);
        $statData->doingCount    = zget($statusPairs, 'doing', 0);
        $statData->finishedCount = $finishedCount;
        $statData->delayedCount  = $delayedCount;

        return $statData;
    }

    /**
     * Get project pairs by programID.
     *
     * @param  int    $programID
     * @param  status $status    all|wait|doing|suspended|closed|noclosed
     * @param  bool   $isQueryAll
     * @param  string $orderBy
     * @param  string $excludedModel
     * @access public
     * @return object
     */
    public function getPairsByProgram($programID = '', $status = 'all', $isQueryAll = false, $orderBy = 'order_asc', $excludedModel = '')
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProjectPairs();
        return $this->dao->select('id, name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->beginIF($programID !== '')->andWhere('parent')->eq($programID)->fi()
            ->beginIF($status != 'all' && $status != 'noclosed')->andWhere('status')->eq($status)->fi()
            ->beginIF($excludedModel)->andWhere('model')->ne($excludedModel)->fi()
            ->beginIF($status == 'noclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin and !$isQueryAll)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->orderBy($orderBy)
            ->fetchPairs();
    }

    /**
     * Get project by id list.
     *
     * @param  array    $projectIdList
     * @access public
     * @return object
     */
    public function getByIdList($projectIdList = array())
    {
        return $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('id')->in($projectIdList)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->fetchAll('id');
    }

    /**
     * Get project pairs by id list.
     *
     * @param  array  $projectIdList
     * @param  string $mode
     * @access public
     * @return array
     */
    public function getPairsByIdList($projectIdList = array(), $mode = '')
    {
        return $this->dao->select('id, name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->beginIF($projectIdList)->andWhere('id')->in($projectIdList)->fi()
            ->beginIF(!$this->app->user->admin and $mode != 'all')->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->fetchPairs('id', 'name');
    }

    /**
     * Get associated bugs by project.
     *
     * @param  array  $projectIdList
     * @param  string $status   active|resolved|all
     * @access public
     * @return array
     */
    public function getTotalBugByProject($projectIdList, $status)
    {
        return $this->dao->select('project, count(*) as bugs')->from(TABLE_BUG)
            ->where('project')->in($projectIdList)
            ->andWhere('deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('status')->eq($status)->fi()
            ->groupBy('project')
            ->fetchPairs('project');
    }

    /**
     * Get branches by project id.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getBranchesByProject($projectID)
    {
        return $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)
            ->where('project')->eq($projectID)
            ->fetchGroup('product', 'branch');
    }

    /**
     * Get branch group by project id.
     *
     * @param  int          $projectID
     * @param  array|string $productIdList
     * @access public
     * @return array
     */
    public function getBranchGroupByProject($projectID, $productIdList)
    {
        return $this->dao->select('t1.product as productID, t1.branch as branchID, t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
            ->where('t1.product')->in($productIdList)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.project')->eq($projectID)
            ->fetchGroup('productID', 'branchID');
    }

    /**
     * Build the query.
     *
     * @param  int    $projectID
     * @access public
     * @return object
     */
    public function buildMenuQuery($projectID = 0)
    {
        $path    = '';
        $project = $this->getByID($projectID);
        if($project) $path = $project->path;

        return $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('deleted')->eq('0')
            ->andWhere('type')->eq('program')->fi()
            ->andWhere('status')->ne('closed')
            ->andWhere('id')->in($this->app->user->view->programs)->fi()
            ->beginIF($projectID > 0)->andWhere('path')->like($path . '%')->fi()
            ->orderBy('grade desc, `order`')
            ->get();
    }

    /**
     * Build project build search form.
     *
     * @param  array  $products
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  string $type project|execution
     * @access public
     * @return void
     */
    public function buildProjectBuildSearchForm($products, $queryID, $actionURL, $type = 'project')
    {
        $this->loadModel('build');

        /* Set search param. */
        if($type == 'execution') $this->config->build->search['module'] = 'executionBuild';
        if($type == 'project') $this->config->build->search['module'] = 'projectBuild';
        $this->config->build->search['actionURL'] = $actionURL;
        $this->config->build->search['queryID']   = $queryID;
        $this->config->build->search['params']['product']['values'] = $products;

        $this->loadModel('search')->setSearchParams($this->config->build->search);
    }

    /**
     * Get project pairs by model and project.
     *
     * @param  string $model all|scrum|waterfall|kanban
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getPairsByModel($model = 'all', $programID = 0, $param = '')
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProjectPairs();

        $projects = $this->dao->select('id, name, path')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->beginIF($programID)->andWhere('parent')->eq($programID)->fi()
            ->beginIF($model != 'all')->andWhere('model')->eq($model)->fi()
            ->beginIF(strpos($param, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->andWhere('deleted')->eq('0')
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->orderBy('id_desc')
            ->fetchAll();

        $programIdList  = array();
        $projectProgram = array();
        foreach($projects as $project)
        {
            list($programID) = explode(',', trim($project->path, ','));
            $programIdList[$programID]    = $programID;
            $projectProgram[$project->id] = $programID;
        }

        $programs = $this->dao->select('id, name')->from(TABLE_PROGRAM)->where('id')->in($programIdList)->fetchPairs('id', 'name');
        $pairs    = array();
        foreach($projects as $project)
        {
            $projectName = $project->name;

            $programID = zget($projectProgram, $project->id, '');
            if($programID != $project->id) $projectName = zget($programs, $programID, '') . ' / ' . $projectName;

            $pairs[$project->id] = $projectName;
        }

        return $pairs;
    }

    /**
     * Get stories by project id.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getStoriesByProject($projectID = 0)
    {
        return $this->dao->select('*')->from(TABLE_PROJECTSTORY)
            ->beginIF($projectID)->where('project')->eq($projectID)->fi()
            ->fetchGroup('product', 'branch');
    }

    /**
     * Get the tree menu of project.
     *
     * @param  int       $projectID
     * @param  string    $userFunc
     * @param  int       $param
     * @access public
     * @return string
     */
    public function getTreeMenu($projectID = 0, $userFunc = '', $param = 0)
    {
        $projectMenu = array();
        $stmt        = $this->dbh->query($this->buildMenuQuery($projectID));

        while($project = $stmt->fetch())
        {
            $linkHtml = call_user_func($userFunc, $project, $param);

            if(isset($projectMenu[$project->id]) and !empty($projectMenu[$project->id]))
            {
                if(!isset($projectMenu[$project->parent])) $projectMenu[$project->parent] = '';
                $projectMenu[$project->parent] .= "<li>$linkHtml";
                $projectMenu[$project->parent] .= "<ul>".$projectMenu[$project->id]."</ul>\n";
            }
            else
            {
                if(isset($projectMenu[$project->parent]) and !empty($projectMenu[$project->parent]))
                {
                    $projectMenu[$project->parent] .= "<li>$linkHtml\n";
                }
                else
                {
                    $projectMenu[$project->parent] = "<li>$linkHtml\n";
                }
            }
            $projectMenu[$project->parent] .= "</li>\n";
        }

        krsort($projectMenu);
        $projectMenu = array_pop($projectMenu);
        $lastMenu    = "<ul class='tree' data-ride='tree' id='projectTree' data-name='tree-project'>{$projectMenu}</ul>\n";

        return $lastMenu;
    }

    /**
     * Create the manage link.
     *
     * @param  object    $project
     * @access public
     * @return string
     */
    public function createManageLink($project)
    {
        $link = $project->type == 'program' ? helper::createLink('project', 'browse', "projectID={$project->id}&status=all") : helper::createLink('project', 'index', "projectID={$project->id}", '', '', $project->id);

        if($this->app->rawModule == 'execution') $link = helper::createLink('execution', 'all', "status=all&projectID={$project->id}");

        return html::a($link, $project->name, '_self', "id=program{$project->id} title='{$project->name}' class='text-ellipsis'");
    }

    /**
     * Create a project.
     *
     * @access public
     * @return int|bool
     */
    public function create()
    {
        $project = fixer::input('post')
            ->setDefault('status', 'wait')
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->delta == 999, 'days', 0)
            ->setIF($this->post->acl   == 'open', 'whitelist', '')
            ->setIF($this->post->budget != 0, 'budget', round((float)$this->post->budget, 2))
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('openedDate', helper::now())
            ->setDefault('team', substr($this->post->name, 0, 30))
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->add('type', 'project')
            ->join('whitelist', ',')
            ->stripTags($this->config->project->editor->create['id'], $this->config->allowedTags)
            ->remove('products,branch,plans,delta,newProduct,productName,future,contactListMenu,teamMembers')
            ->get();

        $linkedProductsCount = 0;
        if(isset($_POST['products']))
        {
            foreach($_POST['products'] as $product)
            {
                if(!empty($product)) $linkedProductsCount++;
            }
        }

        $program = new stdClass();
        if($project->parent)
        {
            $program = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($project->parent)->fetch();
            if($program)
            {
                /* Child project begin cannot less than parent. */
                if($project->begin < $program->begin) dao::$errors['begin'] = sprintf($this->lang->project->beginGreateChild, $program->begin);

                /* When parent set end then child project end cannot greater than parent. */
                if($program->end != '0000-00-00' and $project->end > $program->end) dao::$errors['end'] = sprintf($this->lang->project->endLetterChild, $program->end);

                if(dao::isError()) return false;
            }

            /* The budget of a child project cannot beyond the remaining budget of the parent program. */
            $project->budgetUnit = $program->budgetUnit;
            if(isset($project->budget) and $program->budget != 0)
            {
                $availableBudget = $this->loadModel('program')->getBudgetLeft($program);
                if($project->budget > $availableBudget) dao::$errors['budget'] = $this->lang->program->beyondParentBudget;
            }

            /* Judge products not empty. */
            if(empty($linkedProductsCount) and !isset($_POST['newProduct']))
            {
                dao::$errors[] = $this->lang->project->productNotEmpty;
                return false;
            }
        }

        /* When select create new product, product name cannot be empty and duplicate. */
        if(isset($_POST['newProduct']))
        {
            if(empty($_POST['productName']))
            {
                $this->app->loadLang('product');
                dao::$errors['productName'] = sprintf($this->lang->error->notempty, $this->lang->product->name);
                return false;
            }
            else
            {
                $existProductName = $this->dao->select('name')->from(TABLE_PRODUCT)->where('name')->eq($_POST['productName'])->fetch('name');
                if(!empty($existProductName))
                {
                    dao::$errors['productName'] = $this->lang->project->existProductName;
                    return false;
                }
            }
        }

        $requiredFields = $this->config->project->create->requiredFields;
        if($this->post->delta == 999) $requiredFields = trim(str_replace(',end,', ',', ",{$requiredFields},"), ',');

        /* Redefines the language entries for the fields in the project table. */
        foreach(explode(',', $requiredFields) as $field)
        {
            if(isset($this->lang->project->$field)) $this->lang->project->$field = $this->lang->project->$field;
        }

        $project = $this->loadModel('file')->processImgURL($project, $this->config->project->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->batchcheck($requiredFields, 'notempty')
            ->checkIF(!empty($project->name), 'name', 'unique', "`type`='project' and `parent` = $project->parent")
            ->checkIF(!empty($project->code), 'code', 'unique', "`type`='project'")
            ->checkIF($project->end != '', 'end', 'gt', $project->begin)
            ->exec();

        /* Add the creater to the team. */
        if(!dao::isError())
        {
            $projectID = $this->dao->lastInsertId();

            /* Set team of project. */
            $members = isset($_POST['teamMembers']) ? $_POST['teamMembers'] : array();
            array_push($members, $project->PM, $project->openedBy);
            $members = array_unique($members);
            $roles   = $this->loadModel('user')->getUserRoles(array_values($members));

            $teamMembers = array();
            foreach($members as $account)
            {
                if(empty($account)) continue;

                $member = new stdClass();
                $member->root    = $projectID;
                $member->type    = 'project';
                $member->account = $account;
                $member->role    = zget($roles, $account, '');
                $member->join    = helper::now();
                $member->days    = zget($project, 'days', 0);
                $member->hours   = $this->config->execution->defaultWorkhours;
                $this->dao->insert(TABLE_TEAM)->data($member)->exec();
                $teamMembers[$account] = $member;
            }
            if($this->config->systemMode == 'new') $this->loadModel('execution')->addProjectMembers($projectID, $teamMembers);

            $whitelist = explode(',', $project->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'project', $projectID);

            /* Create doc lib. */
            $this->app->loadLang('doc');
            $authorizedUsers = array();

            if($project->parent and $project->acl == 'program')
            {
                $stakeHolders    = $this->loadModel('stakeholder')->getStakeHolderPairs($project->parent);
                $authorizedUsers = array_keys($stakeHolders);

                foreach(explode(',', $project->whitelist) as $user)
                {
                    if(empty($user)) continue;
                    $authorizedUsers[$user] = $user;
                }

                $authorizedUsers[$project->PM]       = $project->PM;
                $authorizedUsers[$project->openedBy] = $project->openedBy;
                $authorizedUsers[$program->PM]       = $program->PM;
                $authorizedUsers[$program->openedBy] = $program->openedBy;
            }

            $lib = new stdclass();
            $lib->project = $projectID;
            $lib->name    = $this->lang->doclib->main['project'];
            $lib->type    = 'project';
            $lib->main    = '1';
            $lib->acl     = $project->acl != 'program' ? $project->acl : 'custom';
            $lib->users   = ',' . implode(',', array_filter($authorizedUsers)) . ',';
            $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();

            $this->updateProducts($projectID);

            if(isset($_POST['newProduct']) || (!$project->parent && empty($linkedProductsCount)))
            {
                /* If parent not empty, link products or create products. */
                $product = new stdclass();
                $product->name           = $this->post->productName ? $this->post->productName : $project->name;
                $product->bind           = $this->post->parent ? 0 : 1;
                $product->program        = $project->parent ? current(array_filter(explode(',', $program->path))) : 0;
                $product->acl            = $project->acl == 'open' ? 'open' : 'private';
                $product->PO             = $project->PM;
                $product->createdBy      = $this->app->user->account;
                $product->createdDate    = helper::now();
                $product->status         = 'normal';
                $product->createdVersion = $this->config->version;

                $this->dao->insert(TABLE_PRODUCT)->data($product)->exec();
                $productID = $this->dao->lastInsertId();
                $this->loadModel('action')->create('product', $productID, 'opened');
                $this->dao->update(TABLE_PRODUCT)->set('`order`')->eq($productID * 5)->where('id')->eq($productID)->exec();
                if($product->acl != 'open') $this->loadModel('user')->updateUserView($productID, 'product');

                $projectProduct = new stdclass();
                $projectProduct->project = $projectID;
                $projectProduct->product = $productID;

                $this->dao->insert(TABLE_PROJECTPRODUCT)->data($projectProduct)->exec();

                /* Create doc lib. */
                $this->app->loadLang('doc');
                $lib = new stdclass();
                $lib->product = $productID;
                $lib->name    = $this->lang->doclib->main['product'];
                $lib->type    = 'product';
                $lib->main    = '1';
                $lib->acl     = 'default';
                $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();
            }

            /* Save order. */
            $this->dao->update(TABLE_PROJECT)->set('`order`')->eq($projectID * 5)->where('id')->eq($projectID)->exec();
            $this->file->updateObjectID($this->post->uid, $projectID, 'project');
            $this->loadModel('program')->setTreePath($projectID);

            /* Add project admin. */
            $groupPriv = $this->dao->select('t1.*')->from(TABLE_USERGROUP)->alias('t1')
                ->leftJoin(TABLE_GROUP)->alias('t2')->on('t1.`group` = t2.id')
                ->where('t1.account')->eq($this->app->user->account)
                ->andWhere('t2.role')->eq('projectAdmin')
                ->fetch();

            if(!empty($groupPriv))
            {
                $newProject = $groupPriv->project . ",$projectID";
                $this->dao->update(TABLE_USERGROUP)->set('project')->eq($newProject)->where('account')->eq($groupPriv->account)->andWhere('`group`')->eq($groupPriv->group)->exec();
            }
            else
            {
                $projectAdminID = $this->dao->select('id')->from(TABLE_GROUP)->where('role')->eq('projectAdmin')->fetch('id');

                $groupPriv = new stdclass();
                $groupPriv->account = $this->app->user->account;
                $groupPriv->group   = $projectAdminID;
                $groupPriv->project = $projectID;
                $this->dao->replace(TABLE_USERGROUP)->data($groupPriv)->exec();
            }

            if($project->acl != 'open') $this->loadModel('user')->updateUserView($projectID, 'project');

            return $projectID;
        }
    }

    /**
     * Update project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function update($projectID = 0)
    {
        $oldProject        = $this->dao->findById($projectID)->from(TABLE_PROJECT)->fetch();
        $linkedProducts    = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $_POST['products'] = isset($_POST['products']) ? array_filter($_POST['products']) : $linkedProducts;

        $project = fixer::input('post')
            ->setDefault('team', substr($this->post->name, 0, 30))
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->delta == 999, 'days', 0)
            ->setIF($this->post->begin == '0000-00-00', 'begin', '')
            ->setIF($this->post->end   == '0000-00-00', 'end', '')
            ->setIF($this->post->future, 'budget', 0)
            ->setIF($this->post->budget != 0, 'budget', round($this->post->budget, 2))
            ->join('whitelist', ',')
            ->stripTags($this->config->project->editor->edit['id'], $this->config->allowedTags)
            ->remove('products,branch,plans,delta,future,contactListMenu,teamMembers')
            ->get();

        if($project->parent)
        {
            $program = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($project->parent)->fetch();

            if($program)
            {
                /* Child project begin cannot less than parent. */
                if($project->begin < $program->begin) dao::$errors['begin'] = sprintf($this->lang->project->beginGreateChild, $program->begin);

                /* When parent set end then child project end cannot greater than parent. */
                if($program->end != '0000-00-00' and $project->end > $program->end) dao::$errors['end'] = sprintf($this->lang->project->endLetterChild, $program->end);

                if(dao::isError()) return false;
            }

            /* The budget of a child project cannot beyond the remaining budget of the parent project. */
            $project->budgetUnit = $program->budgetUnit;
            if($project->budget != 0 and $program->budget != 0)
            {
                $availableBudget = $this->loadModel('program')->getBudgetLeft($program);
                if($project->budget > $availableBudget + $oldProject->budget) dao::$errors['budget'] = $this->lang->program->beyondParentBudget;
            }
        }

        /* Judge products not empty. */
        $linkedProductsCount = 0;
        foreach($_POST['products'] as $product)
        {
            if(!empty($product)) $linkedProductsCount++;
        }
        if(empty($linkedProductsCount))
        {
            dao::$errors[] = $this->lang->project->errorNoProducts;
            return false;
        }

        $project = $this->loadModel('file')->processImgURL($project, $this->config->project->editor->edit['id'], $this->post->uid);

        $requiredFields = $this->config->project->edit->requiredFields;
        if($this->post->delta == 999) $requiredFields = trim(str_replace(',end,', ',', ",{$requiredFields},"), ',');

        /* Redefines the language entries for the fields in the project table. */
        foreach(explode(',', $requiredFields) as $field)
        {
            if(isset($this->lang->project->$field)) $this->lang->project->$field = $this->lang->project->$field;
        }

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck($skipFields = 'begin,end')
            ->batchcheck($requiredFields, 'notempty')
            ->checkIF($project->begin != '', 'begin', 'date')
            ->checkIF($project->end != '', 'end', 'date')
            ->checkIF($project->end != '', 'end', 'gt', $project->begin)
            ->checkIF(!empty($project->name), 'name', 'unique', "id != $projectID and `type` = 'project' and `parent` = $project->parent")
            ->checkIF(!empty($project->code), 'code', 'unique', "id != $projectID and `type` = 'project'")
            ->where('id')->eq($projectID)
            ->exec();

        /* Get team and language item. */
        $this->loadModel('user');
        $team    = $this->user->getTeamMemberPairs($projectID, 'project');
        $members = isset($_POST['teamMembers']) ? $_POST['teamMembers'] : array();
        array_push($members, $project->PM);
        $members = array_unique($members);
        $roles   = $this->user->getUserRoles(array_values($members));

        $teamMembers = array();
        foreach($members as $account)
        {
            if(empty($account) or isset($team[$account])) continue;

            $member = new stdclass();
            $member->root    = (int)$projectID;
            $member->account = $account;
            $member->join    = helper::today();
            $member->role    = zget($roles, $account, '');
            $member->days    = zget($project, 'days', 0);
            $member->type    = 'project';
            $member->hours   = $this->config->execution->defaultWorkhours;
            $this->dao->replace(TABLE_TEAM)->data($member)->exec();

            $teamMembers[$account] = $member;
        }
        $this->dao->delete()->from(TABLE_TEAM)
            ->where('root')->eq((int)$projectID)
            ->andWhere('type')->eq('project')
            ->andWhere('account')->in(array_keys($team))
            ->andWhere('account')->notin(array_values($members))
            ->andWhere('account')->ne($oldProject->openedBy)
            ->exec();
        if(!empty($projectID) and !empty($teamMembers)) $this->loadModel('execution')->addProjectMembers($projectID, $teamMembers);

        if(!dao::isError())
        {
            $this->updateProductProgram($oldProject->parent, $project->parent, $_POST['products']);
            $this->updateProducts($projectID, $_POST['products']);
            $this->file->updateObjectID($this->post->uid, $projectID, 'project');

            $whitelist = explode(',', $project->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'project', $projectID);
            if($project->acl != 'open')
            {
                $this->loadModel('user')->updateUserView($projectID, 'project');

                $executions = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq($projectID)->fetchPairs('id', 'id');
                if($executions) $this->user->updateUserView($executions, 'sprint');
            }

            if($oldProject->parent != $project->parent) $this->loadModel('program')->processNode($projectID, $project->parent, $oldProject->path, $oldProject->grade);

            /* Fix whitelist changes. */
            $oldWhitelist = explode(',', $oldProject->whitelist) and $oldWhitelist = array_filter($oldWhitelist);
            $newWhitelist = explode(',', $project->whitelist) and $newWhitelist = array_filter($newWhitelist);
            if(count($oldWhitelist) == count($newWhitelist) and count(array_diff($oldWhitelist, $newWhitelist)) == 0) unset($project->whitelist);
            /* Add linkedproducts changes. */
            $oldProject->linkedProducts = implode(',', $linkedProducts);
            $project->linkedProducts    = implode(',', $_POST['products']);

            return common::createChanges($oldProject, $project);
        }
    }

    /**
     * Batch update projects.
     *
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $projects    = array();
        $allChanges  = array();
        $data        = fixer::input('post')->get();
        $oldProjects = $this->getByIdList($this->post->projectIdList);
        $nameList    = array();

        $extendFields = $this->getFlowExtendFields();
        foreach($data->projectIdList as $projectID)
        {
            $projectID   = (int)$projectID;
            $projectName = $data->names[$projectID];

            $projects[$projectID] = new stdClass();
            if(isset($data->parents[$projectID])) $projects[$projectID]->parent = $data->parents[$projectID];
            $projects[$projectID]->name           = $projectName;
            $projects[$projectID]->PM             = $data->PMs[$projectID];
            $projects[$projectID]->begin          = $data->begins[$projectID];
            $projects[$projectID]->end            = isset($data->ends[$projectID]) ? $data->ends[$projectID] : LONG_TIME;
            $projects[$projectID]->days           = $data->dayses[$projectID];
            $projects[$projectID]->acl            = $data->acls[$projectID];
            $projects[$projectID]->lastEditedBy   = $this->app->user->account;
            $projects[$projectID]->lastEditedDate = helper::now();

            if($projects[$projectID]->parent)
            {
                $parentProject = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($projects[$projectID]->parent)->fetch();

                if($parentProject)
                {
                    /* Child project begin cannot less than parent. */
                    if($projects[$projectID]->begin < $parentProject->begin) dao::$errors['begin'] = sprintf($this->lang->project->beginGreateChild, $parentProject->begin);

                    /* When parent set end then child project end cannot greater than parent. */
                    if($parentProject->end != '0000-00-00' and $projects[$projectID]->end > $parentProject->end) dao::$errors['end'] =  sprintf($this->lang->project->endLetterChild, $parentProject->end);

                }
            }

            foreach($extendFields as $extendField)
            {
                $projects[$projectID]->{$extendField->field} = $this->post->{$extendField->field}[$projectID];
                if(is_array($projects[$projectID]->{$extendField->field})) $projects[$projectID]->{$extendField->field} = join(',', $projects[$projectID]->{$extendField->field});

                $projects[$projectID]->{$extendField->field} = htmlSpecialString($projects[$projectID]->{$extendField->field});
                $message = $this->checkFlowRule($extendField, $projects[$projectID]->{$extendField->field});
                if($message) die(js::alert($message));
            }
        }
        if(dao::isError()) die(js::error(dao::getError()));

        foreach($projects as $projectID => $project)
        {
            $oldProject = $oldProjects[$projectID];
            $parentID   = !isset($project->parent) ? $oldProject->parent : $project->parent;

            $this->dao->update(TABLE_PROJECT)->data($project)
                ->autoCheck($skipFields = 'begin,end')
                ->batchCheck($this->config->project->edit->requiredFields , 'notempty')
                ->checkIF($project->begin != '', 'begin', 'date')
                ->checkIF($project->end != '', 'end', 'date')
                ->checkIF($project->end != '', 'end', 'gt', $project->begin)
                ->checkIF(!empty($project->name), 'name', 'unique', "id != $projectID and `type`='project' and `parent` = $parentID")
                ->checkIF(!empty($project->code), 'code', 'unique', "id != $projectID and `type`='project'")
                ->where('id')->eq($projectID)
                ->exec();

            if(dao::isError()) die(js::error('project#' . $projectID . dao::getError(true)));
            if(!dao::isError())
            {
                $linkedProducts = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
                $this->updateProductProgram($oldProject->parent, $project->parent, $linkedProducts);

                if($oldProject->parent != $project->parent) $this->loadModel('program')->processNode($projectID, $project->parent, $oldProject->path, $oldProject->grade);
                /* When acl is open, white list set empty. When acl is private,update user view. */
                if($project->acl == 'open') $this->loadModel('personnel')->updateWhitelist('', 'project', $projectID);
                if($project->acl != 'open') $this->loadModel('user')->updateUserView($projectID, 'project');
            }
            $allChanges[$projectID] = common::createChanges($oldProject, $project);
        }
        return $allChanges;
    }

    /**
     * Start project.
     *
     * @param  int    $projectID
     * @param  string $type
     * @access public
     * @return array
     */
    public function start($projectID, $type = 'project')
    {
        $oldProject = $this->getById($projectID, $type);
        $now        = helper::now();

        $project = fixer::input('post')
            ->setDefault('status', 'doing')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->remove('comment')->get();

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->check($this->config->project->start->requiredFields, 'notempty')
            ->checkIF($project->realBegan != '', 'realBegan', 'le', helper::today())
            ->where('id')->eq((int)$projectID)
            ->exec();

        /* When it has multiple errors, only the first one is prompted */
        if(dao::isError() and count(dao::$errors['realBegan']) > 1) dao::$errors['realBegan'] = dao::$errors['realBegan'][0];

        if(!dao::isError()) return common::createChanges($oldProject, $project);
    }

    /**
     * Put project off.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function putoff($projectID)
    {
        $oldProject = $this->getById($projectID);
        $now        = helper::now();

        $project = fixer::input('post')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->where('id')->eq((int)$projectID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldProject, $project);
    }

    /**
     * Suspend project.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function suspend($projectID)
    {
        $oldProject = $this->getById($projectID);
        $project = fixer::input('post')
            ->setDefault('status', 'suspended')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setDefault('suspendedDate', helper::today())
            ->remove('comment')->get();

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->where('id')->eq((int)$projectID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldProject, $project);
    }

    /**
     * Activate project.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function activate($projectID)
    {
        $oldProject = $this->getById($projectID);
        $now        = helper::now();

        $project = fixer::input('post')
            ->setDefault('realEnd','')
            ->setDefault('status', 'doing')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setIF($oldProject->realBegan == '0000-00-00', 'realBegan', helper::today())
            ->remove('comment,readjustTime,readjustTask')
            ->get();

        if(!$this->post->readjustTime)
        {
            unset($project->begin);
            unset($project->end);
        }

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->where('id')->eq((int)$projectID)
            ->exec();

        /* Readjust task. */
        if($this->post->readjustTime and $this->post->readjustTask)
        {
            $beginTimeStamp = strtotime($project->begin);
            $tasks = $this->dao->select('id,estStarted,deadline,status')->from(TABLE_TASK)
                ->where('deadline')->ne('0000-00-00')
                ->andWhere('status')->in('wait,doing')
                ->andWhere('project')->eq($projectID)
                ->fetchAll();
            foreach($tasks as $task)
            {
                if($task->status == 'wait' and !helper::isZeroDate($task->estStarted))
                {
                    $taskDays   = helper::diffDate($task->deadline, $task->estStarted);
                    $taskOffset = helper::diffDate($task->estStarted, $oldProject->begin);

                    $estStartedTimeStamp = $beginTimeStamp + $taskOffset * 24 * 3600;
                    $estStarted = date('Y-m-d', $estStartedTimeStamp);
                    $deadline   = date('Y-m-d', $estStartedTimeStamp + $taskDays * 24 * 3600);

                    if($estStarted > $project->end) $estStarted = $project->end;
                    if($deadline > $project->end)   $deadline   = $project->end;
                    $this->dao->update(TABLE_TASK)->set('estStarted')->eq($estStarted)->set('deadline')->eq($deadline)->where('id')->eq($task->id)->exec();
                }
                else
                {
                    $taskOffset = helper::diffDate($task->deadline, $oldProject->begin);
                    $deadline   = date('Y-m-d', $beginTimeStamp + $taskOffset * 24 * 3600);

                    if($deadline > $project->end) $deadline = $project->end;
                    $this->dao->update(TABLE_TASK)->set('deadline')->eq($deadline)->where('id')->eq($task->id)->exec();
                }
            }
        }

        if(!dao::isError()) return common::createChanges($oldProject, $project);
    }

    /**
     * Close project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function close($projectID)
    {
        $oldProject = $this->getById($projectID);
        $now        = helper::now();

        $project = fixer::input('post')
            ->setDefault('status', 'closed')
            ->setDefault('closedBy', $this->app->user->account)
            ->setDefault('closedDate', $now)
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->remove('comment')
            ->get();

        $this->lang->error->ge = $this->lang->project->ge;

        $this->dao->update(TABLE_PROJECT)->data($project)
            ->autoCheck()
            ->check($this->config->project->close->requiredFields, 'notempty')
            ->checkIF($project->realEnd != '', 'realEnd', 'le', helper::today())
            ->checkIF($project->realEnd != '', 'realEnd', 'ge', $oldProject->realBegan)
            ->where('id')->eq((int)$projectID)
            ->exec();

        /* When it has multiple errors, only the first one is prompted */
        if(dao::isError() and count(dao::$errors['realEnd']) > 1) dao::$errors['realEnd'] = dao::$errors['realEnd'][0];

        if(!dao::isError())
        {
            $this->loadModel('score')->create('project', 'close', $oldProject);
            return common::createChanges($oldProject, $project);
        }
    }

    /**
     * Update the program of the product.
     *
     * @param  int    $oldProgram
     * @param  int    $newProgram
     * @param  array  $products
     * @access public
     * @return void
     */
    public function updateProductProgram($oldProgram, $newProgram, $products)
    {
        $this->loadModel('action');
        $this->loadModel('program');
        /* Product belonging project set processing. */
        $oldTopProgram = $this->program->getTopByID($oldProgram);
        $newTopProgram = $this->program->getTopByID($newProgram);
        if($oldTopProgram != $newTopProgram)
        {
            foreach($products as $productID)
            {
                $oldProduct = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
                $this->dao->update(TABLE_PRODUCT)->set('program')->eq((int)$newTopProgram)->where('id')->eq((int)$productID)->exec();
                $newProduct = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
                $changes    = common::createChanges($oldProduct, $newProduct);
                $actionID   = $this->action->create('product', $productID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
    }

    /**
     * Unlink a member.
     *
     * @param  int    $projectID
     * @param  string $account
     * @param  string $removeExecution no|yes
     * @access public
     * @return void
     */
    public function unlinkMember($projectID, $account, $removeExecution = 'no')
    {
        $this->dao->delete()->from(TABLE_TEAM)->where('root')->eq((int)$projectID)->andWhere('type')->eq('project')->andWhere('account')->eq($account)->exec();

        $this->loadModel('user')->updateUserView($projectID, 'project', array($account));

        if($removeExecution == 'yes')
        {
            $executions = $this->loadModel('execution')->getByProject($projectID, 'undone', 0, true);
            $this->dao->delete()->from(TABLE_TEAM)->where('root')->in(array_keys($executions))->andWhere('type')->eq('execution')->andWhere('account')->eq($account)->exec();
            $this->user->updateUserView(array_keys($executions), 'sprint', array($account));
        }

        $linkedProducts = $this->loadModel('product')->getProductPairsByProject($projectID);
        if(!empty($linkedProducts)) $this->user->updateUserView(array_keys($linkedProducts), 'product', array($account));
    }

    /**
     * Manage team members.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function manageMembers($projectID)
    {
        $project = $this->getByID($projectID);
        $data    = (array)fixer::input('post')->get();

        extract($data);
        $projectID   = (int)$projectID;
        $projectType = 'project';
        $accounts    = array_unique($accounts);
        $limited     = array_values($limited);
        $oldJoin     = $this->dao->select('`account`, `join`')->from(TABLE_TEAM)->where('root')->eq($projectID)->andWhere('type')->eq($projectType)->fetchPairs();
        $this->dao->delete()->from(TABLE_TEAM)->where('root')->eq($projectID)->andWhere('type')->eq($projectType)->exec();

        $projectMember = array();
        foreach($accounts as $key => $account)
        {
            if(empty($account)) continue;

            $member = new stdclass();
            $member->role    = $roles[$key];
            $member->days    = $days[$key];
            $member->hours   = $hours[$key];
            $member->limited = $limited[$key];

            $member->root    = $projectID;
            $member->account = $account;
            $member->join    = isset($oldJoin[$account]) ? $oldJoin[$account] : helper::today();
            $member->type    = $projectType;

            $projectMember[$account] = $member;
            $this->dao->insert(TABLE_TEAM)->data($member)->exec();
        }

        /* Only changed account update userview. */
        $oldAccounts     = array_keys($oldJoin);
        $removedAccounts = array_diff($oldAccounts, $accounts);
        $changedAccounts = array_merge($removedAccounts, array_diff($accounts, $oldAccounts));
        $changedAccounts = array_unique($changedAccounts);

        $childSprints   = $this->dao->select('id')->from(TABLE_PROJECT)->where('project')->eq($projectID)->andWhere('type')->in('stage,sprint')->andWhere('deleted')->eq('0')->fetchPairs();
        $linkedProducts = $this->loadModel('product')->getProductPairsByProject($projectID);

        $this->loadModel('user')->updateUserView(array($projectID), 'project', $changedAccounts);
        if(!empty($childSprints))   $this->user->updateUserView($childSprints, 'sprint', $changedAccounts);
        if(!empty($linkedProducts)) $this->user->updateUserView(array_keys($linkedProducts), 'product', $changedAccounts);

        /* Remove execution members. */
        if($removeExecution == 'yes' and !empty($childSprints) and !empty($removedAccounts))
        {
            $this->dao->delete()->from(TABLE_TEAM)
                ->where('root')->in($childSprints)
                ->andWhere('type')->eq('execution')
                ->andWhere('account')->in($removedAccounts)
                ->exec();
        }
    }

    /**
     * Print datatable cell.
     *
     * @param  object $col
     * @param  object $project
     * @param  array  $users
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function printCell($col, $project, $users, $programID = 0)
    {
        $canOrder     = common::hasPriv('project', 'updateOrder');
        $canBatchEdit = common::hasPriv('project', 'batchEdit');
        $account      = $this->app->user->account;
        $id           = $col->id;
        $projectLink  = $this->config->systemMode == 'new' ? helper::createLink('project', 'index', "projectID=$project->id", '', '', $project->id) : helper::createLink('execution', 'task', "projectID=$project->id");

        if($col->show)
        {
            $title = '';
            $class = "c-$id" . (in_array($id, array('budget', 'teamCount', 'estimate', 'consume')) ? ' c-number' : '');

            if($id == 'id') $class .= ' cell-id';

            if($id == 'code')
            {
                $class .= ' c-name';
                $title  = "title={$project->code}";
            }

            if($id == 'name')
            {
                $class .= ' text-left';
                $title  = "title='{$project->name}'";
            }

            if($id == 'end')
            {
                $project->end = $project->end == LONG_TIME ? $this->lang->project->longTime : $project->end;
                $class .= ' c-name';
                $title  = "title='{$project->end}'";
            }

            if($id == 'budget')
            {
                $projectBudget = in_array($this->app->getClientLang(), array('zh-cn','zh-tw')) ? round((float)$project->budget / 10000, 2) . $this->lang->project->tenThousand : round((float)$project->budget, 2);
                $budgetTitle   = $project->budget != 0 ? zget($this->lang->project->currencySymbol, $project->budgetUnit) . ' ' . $projectBudget : $this->lang->project->future;

                $title = "title='$budgetTitle'";
            }

            if($id == 'estimate') $title = "title='{$project->hours->totalEstimate} {$this->lang->execution->workHour}'";
            if($id == 'consume')  $title = "title='{$project->hours->totalConsumed} {$this->lang->execution->workHour}'";
            if($id == 'surplus')  $title = "title='{$project->hours->totalLeft} {$this->lang->execution->workHour}'";

            echo "<td class='$class' $title>";
            if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('project', $project, $id);
            switch($id)
            {
                case 'id':
                    if($canBatchEdit)
                    {
                        echo html::checkbox('projectIdList', array($project->id => '')) . html::a($projectLink, sprintf('%03d', $project->id));
                    }
                    else
                    {
                        printf('%03d', $project->id);
                    }
                    break;
                case 'name':
                    $prefix = '';
                    $suffix = '';
                    if($project->model === 'waterfall') $prefix = "<span class='project-type-label label label-outline label-warning'>{$this->lang->project->waterfall}</span> ";
                    if($project->model === 'scrum') $prefix = "<span class='project-type-label label label-outline label-info'>{$this->lang->project->scrum}</span> ";
                    if($project->model === 'kanban') $prefix = "<span class='project-type-label label label-outline label-info'>{$this->lang->project->kanban}</span> ";
                    if(isset($project->delay)) $suffix = "<span class='label label-danger label-badge'>{$this->lang->project->statusList['delay']}</span>";
                    if(!empty($suffix) || !empty($prefix)) echo '<div class="project-name' . (empty($prefix) ? '' : ' has-prefix') . (empty($suffix) ? '' : ' has-suffix') . '">';
                    if(!empty($prefix)) echo $prefix;
                    echo html::a($projectLink, $project->name, '', "class='text-ellipsis'");
                    if(!empty($suffix)) echo $suffix;
                    if(!empty($suffix) || !empty($prefix)) echo '</div>';
                    break;
                case 'code':
                    echo $project->code;
                    break;
                case 'PM':
                    $user     = $this->loadModel('user')->getByID($project->PM, 'account');
                    $userID   = !empty($user) ? $user->id : '';
                    $PMLink   = helper::createLink('user', 'profile', "userID=$userID", '', true);
                    $userName = zget($users, $project->PM);
                    echo empty($project->PM) ? '' : html::a($PMLink, $userName, '', "title='{$userName}' data-toggle='modal' data-type='iframe' data-width='600'");
                    break;
                case 'begin':
                    echo $project->begin;
                    break;
                case 'end':
                    echo $project->end;
                    break;
                case 'status':
                    echo "<span class='status-task status-{$project->status}'> " . zget($this->lang->project->statusList, $project->status) . "</span>";
                    break;
                case 'budget':
                    echo $budgetTitle;
                    break;
                case 'teamCount':
                    echo $project->teamCount;
                    break;
                case 'estimate':
                    echo $project->hours->totalEstimate . $this->lang->execution->workHourUnit;
                    break;
                case 'consume':
                    echo $project->hours->totalConsumed . $this->lang->execution->workHourUnit;
                    break;
                case 'surplus':
                    echo $project->hours->totalLeft     . $this->lang->execution->workHourUnit;
                    break;
                case 'progress':
                    echo html::ring($project->hours->progress);
                    break;
                case 'actions':
                    $moduleName = $this->config->systemMode == 'classic' ? "execution" : "project";
                    if($project->status == 'wait' || $project->status == 'suspended') common::printIcon($moduleName, 'start', "projectID=$project->id", $project, 'list', 'play', '', 'iframe', true);
                    if($project->status == 'doing') common::printIcon($moduleName, 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe', true);
                    if($project->status == 'closed') common::printIcon($moduleName, 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe', true);

                    if(common::hasPriv($moduleName, 'suspend') || (common::hasPriv($moduleName, 'close') && $project->status != 'doing') || (common::hasPriv($moduleName, 'activate') && $project->status != 'closed'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn icon-caret-down dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}' style='width: 16px; padding-left: 0px; border-radius: 4px;'></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu' style='position: unset; min-width: auto; padding: 5px 6px;'>";
                        common::printIcon($moduleName, 'suspend', "projectID=$project->id", $project, 'list', 'pause', '', 'iframe btn-action', true);
                        if($project->status != 'doing') common::printIcon($moduleName, 'close', "projectID=$project->id", $project, 'list', 'off', '', 'iframe btn-action', true);
                        if($project->status != 'closed') common::printIcon($moduleName, 'activate', "projectID=$project->id", $project, 'list', 'magic', '', 'iframe btn-action', true);
                        echo "</ul>";
                        echo "</div>";
                    }

                    $from     = $project->from == 'project' ? 'project' : 'pgmproject';
                    $iframe   = ($this->app->tab == 'program' or $project->model == 'kanban') ? 'iframe' : '';
                    $onlyBody = ($this->app->tab == 'program' or $project->model == 'kanban') ? true : '';
                    $dataApp  = $this->config->systemMode == 'classic' ? "data-app=execution" : "data-app=project";
                    $attr     = $project->model == 'kanban' ? " disabled='disabled'" : '';
                    common::printIcon($moduleName, 'edit', "projectID=$project->id", $project, 'list', 'edit', '', $iframe, $onlyBody, $dataApp);
                    common::printIcon($moduleName, 'manageMembers', "projectID=$project->id", $project, 'list', 'group', '', '', '', $dataApp . $attr, $this->lang->execution->team);
                    if($this->config->systemMode == 'new') common::printIcon('project', 'group', "projectID=$project->id&programID=$programID", $project, 'list', 'lock', '', '', '', $dataApp . $attr);

                    if(common::hasPriv($moduleName, 'manageProducts') || common::hasPriv($moduleName, 'whitelist') || common::hasPriv($moduleName, 'delete'))
                    {
                        echo "<div class='btn-group'>";
                        echo "<button type='button' class='btn dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}'><i class='icon-more-alt'></i></button>";
                        echo "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                        common::printIcon($moduleName, 'manageProducts', "projectID=$project->id", $project, 'list', 'link', '', 'btn-action', '', $dataApp . $attr, $this->lang->project->manageProducts);
                        if($this->config->systemMode == 'new') common::printIcon('project', 'whitelist', "projectID=$project->id&module=project&from=$from", $project, 'list', 'shield-check', '', 'btn-action', '', $dataApp . $attr);
                        if(common::hasPriv($moduleName, 'delete')) echo html::a(helper::createLink($moduleName, "delete", "projectID=$project->id"), "<i class='icon-trash'></i>", 'hiddenwin', "class='btn btn-action' title='{$this->lang->project->delete}'");
                        echo "</ul>";
                        echo "</div>";
                    }
                    break;
            }
            echo '</td>';
        }
    }

    /**
     * Update products of a project.
     *
     * @param  int    $projectID
     * @param  array  $products
     * @access public
     * @return void
     */
    public function updateProducts($projectID, $products = '')
    {
        $this->loadModel('user');
        $products           = isset($_POST['products']) ? $_POST['products'] : $products;
        $oldProjectProducts = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)->where('project')->eq((int)$projectID)->fetchGroup('product', 'branch');
        $this->dao->delete()->from(TABLE_PROJECTPRODUCT)->where('project')->eq((int)$projectID)->exec();
        $members = array_keys($this->getTeamMembers($projectID));
        if(empty($products))
        {
            $this->user->updateUserView(array_keys($oldProjectProducts), 'product', $members);
            return true;
        }

        $branches = isset($_POST['branch']) ? $_POST['branch'] : array();
        $plans    = isset($_POST['plans']) ? $_POST['plans'] : array();;

        $existedProducts = array();
        foreach($products as $i => $productID)
        {
            if(empty($productID)) continue;

            if(!isset($existedProducts[$productID])) $existedProducts[$productID] = array();

            $oldPlan = 0;
            $branch  = isset($branches[$i]) ? $branches[$i] : 0;

            if(isset($existedProducts[$productID][$branch])) continue;

            if(isset($oldProjectProducts[$productID][$branch]))
            {
                $oldProjectProduct = $oldProjectProducts[$productID][$branch];
                $oldPlan           = $oldProjectProduct->plan;
            }

            $data = new stdclass();
            $data->project = $projectID;
            $data->product = $productID;
            $data->branch  = $branch;
            $data->plan    = isset($plans[$productID][$branch]) ? $plans[$productID][$branch] : $oldPlan;
            $this->dao->insert(TABLE_PROJECTPRODUCT)->data($data)->exec();
            $existedProducts[$productID][$branch] = true;
        }

        /* Delete the execution linked products that is not linked with the execution. */
        if((int)$projectID > 0)
        {
            $executions = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq((int)$projectID)->fetchPairs('id');
            $this->dao->delete()->from(TABLE_PROJECTPRODUCT)->where('project')->in($executions)->andWhere('product')->notin($products)->exec();
        }

        $oldProductKeys = array_keys($oldProjectProducts);
        $needUpdate = array_merge(array_diff($oldProductKeys, $products), array_diff($products, $oldProductKeys));
        if($needUpdate) $this->user->updateUserView($needUpdate, 'product', $members);
    }

    /**
     * Update userview for involved product and execution.
     *
     * @param  int    $projectID
     * @param  array  $users
     * @access public
     * @return void
     */
    public function updateInvolvedUserView($projectID, $users = array())
    {
        $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs('product', 'product');
        $this->loadModel('user')->updateUserView($products, 'product', $users);

        $executions = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq($projectID)->fetchPairs('id', 'id');
        if($executions) $this->user->updateUserView($executions, 'sprint', $users);
    }

    /**
     * Get team members.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getTeamMembers($projectID)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getTeamMembers();

        $project = $this->getByID($projectID);
        $type    = $this->config->systemMode == 'new' ? $project->type : 'project';
        if(empty($project)) return array();

        return $this->dao->select("t1.*, t1.hours * t1.days AS totalHours, t2.id as userID, if(t2.deleted='0', t2.realname, t1.account) as realname")->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.root')->eq((int)$projectID)
            ->andWhere('t1.type')->eq($type)
            ->andWhere('t2.deleted')->eq('0')
            ->fetchAll('account');
    }

    /**
     * Get team member pairs by projectID.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getTeamMemberPairs($projectID)
    {
        $project = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();

        if(empty($project)) return array();

        $type = 'project';
        if($this->config->systemMode == 'new')
        {
            if($project->type == 'sprint' or $project->type == 'stage' or $project->type == 'kanban') $type = 'execution';
        }

        $members = $this->dao->select("t1.account, if(t2.deleted='0', t2.realname, t1.account) as realname")->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.root')->eq((int)$projectID)
            ->andWhere('t1.type')->eq($type)
            ->andWhere('t2.deleted')->eq('0')
            ->fetchPairs('account', 'realname');

        return array('' => '') + $members;
    }

    /**
     * Get members of a project who can be imported.
     *
     * @param  int    $projectID
     * @param  array  $currentMembers
     * @access public
     * @return array
     */
    public function getMembers2Import($projectID, $currentMembers)
    {
        if($projectID == 0) return array();

        return $this->dao->select('account, role, hours')
            ->from(TABLE_TEAM)
            ->where('root')->eq($projectID)
            ->andWhere('type')->eq('project')
            ->andWhere('account')->notIN($currentMembers)
            ->fetchAll('account');
    }

    /**
     * Get project stats.
     *
     * @param  int     $projectID
     * @param  string  $status
     * @param  int     $productID
     * @param  int     $itemCounts
     * @param  string  $orderBy
     * @param  object  $pager
     * @access public
     * @return void
     */
    public function getStats($projectID = 0, $status = 'undone', $productID = 0, $branch = 0, $itemCounts = 30, $orderBy = 'id_asc', $pager = null)
    {
        if(empty($productID))
        {
            $myExecutionIDList = array();
            if($status == 'involved')
            {
                $myExecutionIDList = $this->dao->select('root')->from(TABLE_TEAM)
                    ->where('account')->eq($this->app->user->account)
                    ->andWhere('type')->eq('execution')
                    ->fetchPairs();
            }

            $executions = $this->dao->select('t1.*,t2.name projectName, t2.model as projectModel')->from(TABLE_EXECUTION)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t1.type')->in('sprint,stage,kanban')
                ->beginIF($projectID != 0)->andWhere('t1.project')->eq($projectID)->fi()
                ->beginIF(!empty($myExecutionIDList))->andWhere('t1.id')->in(array_keys($myExecutionIDList))->fi()
                ->beginIF($status == 'undone')->andWhere('t1.status')->notIN('done,closed')->fi()
                ->beginIF($status != 'all' and $status != 'undone' and $status != 'involved')->andWhere('t1.status')->eq($status)->fi()
                ->beginIF(!$this->app->user->admin)->andWhere('t1.id')->in($this->app->user->view->sprints)->fi()
                ->andWhere('t1.deleted')->eq('0')
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $executions = $this->dao->select('t2.*,t3.name projectName, t3.model as projectModel')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.project=t2.id')
                ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t2.project=t3.id')
                ->where('t1.product')->eq($productID)
                ->beginIF($projectID)->andWhere('t2.project')->eq($projectID)->fi()
                ->beginIF($status == 'undone')->andWhere('t2.status')->notIN('done,closed')->fi()
                ->beginIF($status != 'all' and $status != 'undone')->andWhere('t2.status')->eq($status)->fi()
                ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
                ->andWhere('t2.deleted')->eq('0')
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        $hours     = $this->computerProgress($executions);
        $emptyHour = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);

        /* Get burndown charts datas. */
        $burns = $this->dao->select('execution, date AS name, `left` AS value')
            ->from(TABLE_BURN)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('task')->eq(0)
            ->orderBy('date desc')
            ->fetchGroup('execution', 'name');

        $this->loadModel('execution');
        foreach($burns as $executionID => $executionBurns)
        {
            /* If executionBurns > $itemCounts, split it, else call processBurnData() to pad burns. */
            $begin = $executions[$executionID]->begin;
            $end   = $executions[$executionID]->end;
            if(helper::isZeroDate($begin)) $begin = $executions[$executionID]->openedDate;
            $executionBurns = $this->execution->processBurnData($executionBurns, $itemCounts, $begin, $end);

            /* Shorter names. */
            foreach($executionBurns as $executionBurn)
            {
                $executionBurn->name = substr($executionBurn->name, 5);
                unset($executionBurn->execution);
            }

            ksort($executionBurns);
            $burns[$executionID] = $executionBurns;
        }

        /* Process executions. */
        $parents  = array();
        $children = array();
        foreach($executions as $key => $execution)
        {
            /* Process the end time. */
            $execution->end = date(DT_DATE1, strtotime($execution->end));

            /* Judge whether the execution is delayed. */
            if($execution->status != 'done' and $execution->status != 'closed' and $execution->status != 'suspended')
            {
                $delay = helper::diffDate(helper::today(), $execution->end);
                if($delay > 0) $execution->delay = $delay;
            }

            /* Process the burns. */
            $execution->burns = array();
            $burnData = isset($burns[$execution->id]) ? $burns[$execution->id] : array();
            foreach($burnData as $data) $execution->burns[] = $data->value;

            /* Process the hours. */
            $execution->hours = isset($hours[$execution->id]) ? $hours[$execution->id] : (object)$emptyHour;

            $execution->children = array();
            $execution->grade == 1 ? $parents[$execution->id] = $execution : $children[$execution->parent][] = $execution;
        }

        /* In the case of the waterfall model, calculate the sub-stage. */
        $project = $this->getByID($projectID);
        if($project and $project->model == 'waterfall')
        {
            foreach($parents as $id => $execution)
            {
                $execution->children = isset($children[$id]) ? $children[$id] : array();
                unset($children[$id]);
            }
        }

        $orphan = array();
        foreach($children as $child) $orphan = array_merge($child, $orphan);

        return array_merge($parents, $orphan);
    }

    /**
     * Get stats for project kanban.
     *
     * @access public
     * @return void
     */
    public function getStats4Kanban()
    {
        $this->loadModel('program');

        $projects   = $this->program->getProjectStats(0, 'all', 0, 'order_asc');
        $executions = $this->getStats(0, 'doing');

        $doingExecutions  = array();
        $latestExecutions = array();
        foreach($executions as $execution) $doingExecutions[$execution->project][$execution->id] = $execution;
        foreach($doingExecutions as $projectID => $executions)
        {
            krsort($doingExecutions[$projectID]);
            $latestExecutions[$projectID] = current($doingExecutions[$projectID]);
        }

        $myProjects    = array();
        $otherProjects = array();
        $closedGroup   = array();
        foreach($projects as $project)
        {
            if(strpos('wait,doing,closed', $project->status) === false) continue;

            $projectPath = explode(',', trim($project->path, ','));
            $topProgram  = !empty($project->parent) ? $projectPath[0] : $project->parent;

            if($project->PM == $this->app->user->account)
            {
                if($project->status != 'closed')
                {
                    $myProjects[$topProgram][$project->status][] = $project;
                }
                else
                {
                    $closedGroup['my'][$topProgram][$project->closedDate] = $project;
                }
            }
            else
            {
                if($project->status != 'closed')
                {
                    $otherProjects[$topProgram][$project->status][] = $project;
                }
                else
                {
                    $closedGroup['other'][$topProgram][$project->closedDate] = $project;
                }
            }
        }

        /* Only display recent two closed projects. */
        foreach($closedGroup as $group => $closedProjects)
        {
            foreach($closedProjects as $topProgram => $projects)
            {
                krsort($projects);
                if($group == 'my')
                {
                    $myProjects[$topProgram]['closed'] = array_slice($projects, 0, 2);
                }
                else
                {
                    $otherProjects[$topProgram]['closed'] = array_slice($projects, 0, 2);
                }
            }
        }

        return array('kanbanGroup' => array('my' => $myProjects, 'other' => $otherProjects), 'latestExecutions' => $latestExecutions);
    }

    /**
     * Computer execution progress.
     *
     * @param  array    $executions
     * @access public
     * @return array
     */
    public function computerProgress($executions)
    {
        $hours     = array();
        $emptyHour = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);

        /* Get all tasks and compute totalEstimate, totalConsumed, totalLeft, progress according to them. */
        $tasks = $this->dao->select('id, execution, estimate, consumed, `left`, status, closedReason')
            ->from(TABLE_TASK)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('parent')->lt(1)
            ->andWhere('deleted')->eq(0)
            ->fetchGroup('execution', 'id');

        /* Compute totalEstimate, totalConsumed, totalLeft. */
        foreach($tasks as $executionID => $executionTasks)
        {
            $hour = (object)$emptyHour;
            foreach($executionTasks as $task)
            {
                $hour->totalEstimate += $task->estimate;
                $hour->totalConsumed += $task->consumed;
                if($task->status != 'cancel' and $task->status != 'closed') $hour->totalLeft += $task->left;
            }
            $hours[$executionID] = $hour;
        }

        /* Compute totalReal and progress. */
        foreach($hours as $hour)
        {
            $hour->totalEstimate = round($hour->totalEstimate, 1) ;
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft     = round($hour->totalLeft, 1);
            $hour->totalReal     = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress      = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal * 100, 2) : 0;
        }

        return $hours;
    }

    /**
     * Set menu of project module.
     *
     * @param  int    $objectID  projectID
     * @access public
     * @return void
     */
    public function setMenu($objectID)
    {
        global $lang;
        $project = $this->getByID($objectID);

        if(isset($project->model) and $project->model == 'waterfall')
        {
            global $lang;
            $this->loadModel('execution');
            $lang->executionCommon = $lang->project->stage;

            include $this->app->getModulePath('', 'execution') . 'lang/' . $this->app->getClientLang() . '.php';
        }

        $model = 'scrum';
        if($project) $model = $project->model;

        if(isset($lang->$model))
        {
            $lang->project->menu        = $lang->{$model}->menu;
            $lang->project->menuOrder   = $lang->{$model}->menuOrder;
            $lang->project->dividerMenu = $lang->{$model}->dividerMenu;
        }

        $this->lang->switcherMenu = $this->getSwitcher($objectID, $this->app->rawModule, $this->app->rawMethod);

        /* Reset project priv. */
        $moduleName = $this->app->rawModule;
        $methodName = $this->app->rawMethod;

        $this->loadModel('common')->resetProjectPriv($objectID);
        if(!$this->common->isOpenMethod($moduleName, $methodName) and !commonModel::hasPriv($moduleName, $methodName)) $this->common->deny($moduleName, $methodName, false);
        common::setMenuVars('project', $objectID);
    }

    /**
     * Check if the project model can be changed.
     *
     * @param  int    $projectID
     * @param  string $model
     * @access public
     * @return bool
     */
    public function checkCanChangeModel($projectID, $model)
    {
        $checkList = $this->config->project->checkList->$model;
        if(isset($this->config->maxVersion)) $checkList = $this->config->project->maxCheckList->$model;
        foreach($checkList as $module)
        {
            if($module == '') continue;

            $type = '';
            $table = constant('TABLE_'. strtoupper($module));
            $object = new stdclass();

            if($table == TABLE_EXECUTION)
            {
                $type = $model == 'scrum' ? 'sprint' : 'stage';
            }

            $object = $this->getDataByProject($table, $projectID, $type);
            if(!empty($object)) return false;
        }
        return true;
    }

    /**
     * Get the objects under the project.
     *
     * @param  constant $table
     * @param  int      $projectID
     * @param  string   $type
     * @access public
     * @return object
     */
    public function getDataByProject($table, $projectID, $type = '')
    {
        $result = $this->dao->select('id')->from($table)
            ->where('project')->eq($projectID)
            ->beginIF(!empty($type))->andWhere('type')->eq($type)->fi()
            ->fetch();
        return $result;
    }
}
