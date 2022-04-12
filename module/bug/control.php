<?php
/**
 * The control file of bug currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class bug extends control
{
    /**
     * All products.
     *
     * @var    array
     * @access public
     */
    public $products = array();

    /**
     * Project id.
     *
     * @var    int
     * @access public
     */
    public $projectID = 0;

    /**
     * Construct function, load some modules auto.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('product');
        $this->loadModel('tree');
        $this->loadModel('user');
        $this->loadModel('action');
        $this->loadModel('story');
        $this->loadModel('task');
        $this->loadModel('qa');

        /* Get product data. */
        $products = array();
        $objectID = 0;
        $tab      = ($this->app->tab == 'project' or $this->app->tab == 'execution') ? $this->app->tab : 'qa';
        if(!isonlybody())
        {
            if($this->app->tab == 'project' or $this->app->tab == 'execution')
            {
                $objectID = $this->app->tab == 'project' ? $this->session->project : $this->session->execution;
                $products = $this->product->getProducts($objectID, 'all', '', false);
            }
            else
            {
                $mode     = ($this->app->methodName == 'create' and empty($this->config->CRProduct)) ? 'noclosed' : '';
                $products = $this->product->getPairs($mode, 0, 'program_asc');
            }
            if(empty($products) and !helper::isAjaxRequest()) return print($this->locate($this->createLink('product', 'showErrorNone', "moduleName=$tab&activeMenu=bug&objectID=$objectID")));
        }
        else
        {
            $products = $this->product->getPairs('', 0, 'program_asc');
        }
        $this->view->products = $this->products = $products;
    }

    /**
     * Browse bugs.
     *
     * @param  int    $productID
     * @param  string $branch
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($productID = 0, $branch = '', $browseType = '', $param = 0, $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('datatable');

        $productID = $this->product->saveState($productID, $this->products);
        $product   = $this->product->getById($productID);
        if($product->type != 'normal')
        {
            /* Set productID, moduleID, queryID and branch. */
            $branch = ($this->cookie->preBranch !== '' and $branch === '') ? $this->cookie->preBranch : $branch;
            setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
            setcookie('preBranch', $branch, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        }
        else
        {
            $branch = 'all';
        }

        $this->qa->setMenu($this->products, $productID, $branch);

        /* Set browse type. */
        $browseType = strtolower($browseType);
        if($this->cookie->preProductID != $productID or $this->cookie->preBranch != $branch or $browseType == 'bybranch')
        {
            $_COOKIE['bugModule'] = 0;
            setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule' or $browseType == '')
        {
            setcookie('bugModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $_COOKIE['bugBranch'] = 0;
            setcookie('bugBranch', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            if($browseType == '') setcookie('treeBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bybranch') setcookie('bugBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if($browseType != 'bymodule' and $browseType != 'bybranch') $this->session->set('bugBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : (($browseType == 'bysearch' or $browseType == 'bybranch') ? 0 : ($this->cookie->bugModule ? $this->cookie->bugModule : 0));
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set session. */
        $this->session->set('bugList', $this->app->getURI(true), 'qa');

        /* Set moduleTree. */
        if($browseType == '')
        {
            setcookie('treeBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $browseType = 'unclosed';
        }
        else
        {
            $branch = $this->cookie->treeBranch;
        }

        if($this->projectID and !$productID)
        {
            $moduleTree = $this->tree->getBugTreeMenu($this->projectID, $productID, 0, array('treeModel', 'createBugLink'));
        }
        else
        {
            $moduleTree = $this->tree->getTreeMenu($productID, 'bug', 0, array('treeModel', 'createBugLink'), '', $branch);
        }

        if(($browseType != 'bymodule' && $browseType != 'bybranch')) $this->session->set('bugBrowseType', $browseType);
        if(($browseType == 'bymodule' || $browseType == 'bybranch') and $this->session->bugBrowseType == 'bysearch') $this->session->set('bugBrowseType', 'unclosed');

        /* Process the order by field. */
        if(!$orderBy) $orderBy = $this->cookie->qaBugOrder ? $this->cookie->qaBugOrder : 'id_desc';
        setcookie('qaBugOrder', $orderBy, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);

        /* Append id for secend sort. */
        $sort = common::appendOrder($orderBy);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml' || $this->app->getViewType() == 'xhtml') $recPerPage = 10;
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get executios. */
        $executions = $this->loadModel('execution')->getPairs($this->projectID, 'all', 'empty|withdelete');

        /* Get product id list. */
        $productIDList = $productID ? $productID : array_keys($this->products);

        /* Get bugs. */
        $bugs = $this->bug->getBugs($productIDList, $executions, $branch, $browseType, $moduleID, $queryID, $sort, $pager, $this->projectID);

        /* Process the sql, get the conditon partion, save it to session. */
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug', $browseType == 'needconfirm' ? false : true);

        /* Process bug for check story changed. */
        $bugs = $this->loadModel('story')->checkNeedConfirm($bugs);

        /* Process the openedBuild and resolvedBuild fields. */
        $bugs = $this->bug->processBuildForBugs($bugs);

        /* Get story and task id list. */
        $storyIdList = $taskIdList = array();
        foreach($bugs as $bug)
        {
            if($bug->story)  $storyIdList[$bug->story] = $bug->story;
            if($bug->task)   $taskIdList[$bug->task]   = $bug->task;
            if($bug->toTask) $taskIdList[$bug->toTask] = $bug->toTask;
        }
        $storyList = $storyIdList ? $this->loadModel('story')->getByList($storyIdList) : array();
        $taskList  = $taskIdList  ? $this->loadModel('task')->getByList($taskIdList)   : array();

        /* Build the search form. */
        $actionURL = $this->createLink('bug', 'browse', "productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID");
        $this->config->bug->search['onMenuBar'] = 'yes';
        $this->bug->buildSearchForm($productID, $this->products, $queryID, $actionURL);

        $showModule  = !empty($this->config->datatable->bugBrowse->showModule) ? $this->config->datatable->bugBrowse->showModule : '';
        $productName = ($productID and isset($this->products[$productID])) ? $this->products[$productID] : $this->lang->product->allProduct;

        $showBranch      = false;
        $branchOption    = array();
        $branchTagOption = array();
        if($product and $product->type != 'normal')
        {
            /* Display of branch label. */
            $showBranch = $this->loadModel('branch')->showBranch($productID);

            /* Display status of branch. */
            $branches = $this->loadModel('branch')->getList($productID, 0, 'all');
            foreach($branches as $branchInfo)
            {
                $branchOption[$branchInfo->id]    = $branchInfo->name;
                $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
            }
        }

        /* Set view. */
        $this->view->title           = $productName . $this->lang->colon . $this->lang->bug->common;
        $this->view->position[]      = html::a($this->createLink('bug', 'browse', "productID=$productID"), $productName,'','title=' . $productName);
        $this->view->position[]      = $this->lang->bug->common;
        $this->view->productID       = $productID;
        $this->view->product         = $product;
        $this->view->projectProducts = $this->product->getProducts($this->projectID);
        $this->view->productName     = $productName;
        $this->view->builds          = $this->loadModel('build')->getBuildPairs($productID, $branch);
        $this->view->modules         = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $branch);
        $this->view->moduleTree      = $moduleTree;
        $this->view->moduleName      = $moduleID ? $this->tree->getById($moduleID)->name : $this->lang->tree->all;
        $this->view->summary         = $this->bug->summary($bugs);
        $this->view->browseType      = $browseType;
        $this->view->bugs            = $bugs;
        $this->view->users           = $this->user->getPairs('noletter');
        $this->view->pager           = $pager;
        $this->view->param           = $param;
        $this->view->orderBy         = $orderBy;
        $this->view->moduleID        = $moduleID;
        $this->view->memberPairs     = $this->user->getPairs('noletter|noclosed');
        $this->view->branch          = $branch;
        $this->view->branchOption    = $branchOption;
        $this->view->branchTagOption = $branchTagOption;
        $this->view->executions      = $executions;
        $this->view->plans           = $this->loadModel('productplan')->getPairs($productID);
        $this->view->stories         = $storyList;
        $this->view->tasks           = $taskList;
        $this->view->setModule       = true;
        $this->view->isProjectBug    = ($productID and !$this->projectID) ? false : true;
        $this->view->modulePairs     = $showModule ? $this->tree->getModulePairs($productID, 'bug', $showModule) : array();
        $this->view->showBranch      = $showBranch;
        $this->view->projectPairs    = $this->loadModel('project')->getPairsByProgram();

        $this->display();
    }

    /**
     * The report page.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  int    $branchID
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function report($productID, $browseType, $branchID, $moduleID, $chartType = 'default')
    {
        $this->loadModel('report');
        $this->view->charts   = array();

        if(!empty($_POST))
        {
            foreach($this->post->charts as $chart)
            {
                $chartFunc   = 'getDataOf' . $chart;
                $chartData   = $this->bug->$chartFunc();
                $chartOption = $this->lang->bug->report->$chart;
                if(!empty($chartType) and $chartType != 'default') $chartOption->type = $chartType;
                $this->bug->mergeChartOption($chart);

                $this->view->charts[$chart] = $chartOption;
                $this->view->datas[$chart]  = $this->report->computePercent($chartData);
            }
        }

        $this->qa->setMenu($this->products, $productID, $branchID);
        $this->view->title         = $this->products[$productID] . $this->lang->colon . $this->lang->bug->common . $this->lang->colon . $this->lang->bug->reportChart;
        $this->view->position[]    = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[]    = $this->lang->bug->reportChart;
        $this->view->productID     = $productID;
        $this->view->browseType    = $browseType;
        $this->view->branchID      = $branchID;
        $this->view->moduleID      = $moduleID;
        $this->view->chartType     = $chartType;
        $this->view->checkedCharts = $this->post->charts ? join(',', $this->post->charts) : '';
        $this->display();
    }

    /**
     * Create a bug.
     *
     * @param  int    $productID
     * @param  string $branch
     * @param  string $extras       others params, forexample, executionID=10,moduleID=10
     * @access public
     * @return void
     */
    public function create($productID, $branch = '', $extras = '')
    {
        if(empty($this->products)) $this->locate($this->createLink('product', 'create'));

        /* Unset discarded types. */
        foreach($this->config->bug->discardedTypes as $type) unset($this->lang->bug->typeList[$type]);

        /* Whether there is a object to transfer bug, for example feedback. */
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);
        $from = isset($output['from']) ? $output['from'] : '';

        $project = new stdclass();

        if($this->app->tab == 'execution')
        {
            if(isset($output['executionID'])) $this->loadModel('execution')->setMenu($output['executionID']);
            $execution = $this->dao->findById((int)$output['executionID'])->from(TABLE_EXECUTION)->fetch();
            if($execution->type == 'kanban')
            {
                $this->loadModel('kanban');
                $regionPairs = $this->kanban->getRegionPairs($execution->id, 0, 'execution');
                $regionID    = isset($output['regionID']) ? $output['regionID'] : key($regionPairs);
                $lanePairs   = $this->kanban->getLanePairsByRegion($regionID, 'bug');
                $laneID      = isset($output['laneID']) ? $output['laneID'] : key($lanePairs);

                $this->view->executionType = $execution->type;
                $this->view->regionID      = $regionID;
                $this->view->laneID        = $laneID;
                $this->view->regionPairs   = $regionPairs;
                $this->view->lanePairs     = $lanePairs;
            }
        }
        else if($this->app->tab == 'project')
        {
            if(isset($output['projectID']))
            {
                $this->loadModel('project')->setMenu($output['projectID']);

                /* Replace language. */
                $project = $this->project->getByID($output['projectID']);
                if(!empty($project->model) and $project->model == 'waterfall')
                {
                    $this->lang->bug->execution = str_replace($this->lang->executionCommon, $this->lang->project->stage, $this->lang->bug->execution);
                }
            }
        }
        else
        {
            $this->qa->setMenu($this->products, $productID, $branch);
        }

        $this->view->users = $this->user->getPairs('devfirst|noclosed|nodeleted');
        $this->app->loadLang('release');

        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            /* Set from param if there is a object to transfer bug. */
            setcookie('lastBugModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $bugResult = $this->bug->create('', $extras);
            if(!$bugResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $bugID = $bugResult['id'];
            if($bugResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->bug->common);
                $response['locate']  = $this->createLink('bug', 'view', "bugID=$bugID");
                $response['id']      = $bugResult['id'];
                return $this->send($response);
            }

            /* Record related action, for example FromSonarqube. */
            $createAction = $from == 'sonarqube' ? 'fromSonarqube' : 'Opened';
            $actionID     = $this->action->create('bug', $bugID, $createAction);

            $extras = str_replace(array(',', ' '), array('&', ''), $extras);
            parse_str($extras, $output);
            if(isset($output['todoID']))
            {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($output['todoID'])->exec();
                $this->action->create('todo', $output['todoID'], 'finished', '', "BUG:$bugID");
            }

            $this->executeHooks($bugID);

            /* Return bug id when call the API. */
            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $bugID));

            /* If link from no head then reload. */
            if(isset($output['executionID']) and isonlybody())
            {
                $executionID = $this->post->execution ? $this->post->execution : $output['executionID'];
                if($executionID == $output['executionID'] and $this->app->tab == 'execution')
                {
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($executionID, $this->session->execLaneType ? $this->session->execLaneType : 'all');
                    $kanbanData = json_encode($kanbanData);
                    return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.updateKanban($kanbanData, 0)"));
                }
                else
                {
                    return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));
                }
            }

            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success', 'data' => $bugID));

            if($this->app->tab == 'execution')
            {
                if(!preg_match("/(m=|\/)execution(&f=|-)bug(&|-|\.)?/", $this->session->bugList))
                {
                    $location = $this->session->bugList;
                }
                else
                {
                    $executionID = $this->post->execution ? $this->post->execution : zget($output, 'executionID', $this->session->execution);
                    $location    = $this->createLink('execution', 'bug', "executionID=$executionID");
                }

            }
            elseif($this->app->tab == 'project')
            {
                $location = $this->createLink('project', 'bug', "projectID=" . zget($output, 'projectID', $this->session->project));
            }
            else
            {
                setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                $location = $this->createLink('bug', 'browse', "productID={$this->post->product}&branch=$branch&browseType=byModule&param={$this->post->module}&orderBy=id_desc");
            }
            if($this->app->getViewType() == 'xhtml') $location = $this->createLink('bug', 'view', "bugID=$bugID", 'html');
            $response['locate'] = $location;
            return $this->send($response);
        }

        /* Get product, then set menu. */
        $productID   = $this->product->saveState($productID, $this->products);
        $productInfo = $this->product->getById($productID);

        if($branch === '') $branch = (int)$this->cookie->preBranch;

        /* Init vars. */
        $projectID   = 0;
        $moduleID    = 0;
        $executionID = 0;
        $taskID      = 0;
        $storyID     = 0;
        $buildID     = 0;
        $caseID      = 0;
        $runID       = 0;
        $testtask    = 0;
        $version     = 0;
        $title       = $from == 'sonarqube' ? $_COOKIE['sonarqubeIssue'] : '';
        $steps       = $this->lang->bug->tplStep . $this->lang->bug->tplResult . $this->lang->bug->tplExpect;
        $os          = '';
        $browser     = '';
        $assignedTo  = '';
        $deadline    = '';
        $mailto      = '';
        $keywords    = '';
        $severity    = 3;
        $type        = 'codeerror';
        $pri         = 3;
        $color       = '';

        /* Parse the extras. extract fix php7.2. */
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);
        extract($output);

        if($runID and $resultID) extract($this->bug->getBugInfoFromResult($resultID, 0, 0, isset($stepIdList) ? $stepIdList : ''));// If set runID and resultID, get the result info by resultID as template.
        if(!$runID and $caseID)  extract($this->bug->getBugInfoFromResult($resultID, $caseID, $version, isset($stepIdList) ? $stepIdList : ''));// If not set runID but set caseID, get the result info by resultID and case info.

        /* If bugID setted, use this bug as template. */
        if(isset($bugID))
        {
            $bug = $this->bug->getById($bugID);
            extract((array)$bug);
            $executionID = $bug->execution;
            $moduleID    = $bug->module;
            $taskID      = $bug->task;
            $storyID     = $bug->story;
            $buildID     = $bug->openedBuild;
            $severity    = $bug->severity;
            $type        = $bug->type;
            $assignedTo  = $bug->assignedTo;
            $deadline    = $bug->deadline;
            $color       = $bug->color;
            $testtask    = $bug->testtask;
        }

        if($testtask)
        {
            $testtask = $this->loadModel('testtask')->getById($testtask);
            $buildID  = $testtask->build;
        }

        if(isset($todoID))
        {
            $todo  = $this->loadModel('todo')->getById($todoID);
            $title = $todo->name;
            $steps = $todo->desc;
            $pri   = $todo->pri;
        }

        /* Get branches. */
        if($this->app->tab == 'execution' or $this->app->tab == 'project')
        {
            $objectID        = $this->app->tab == 'project' ? $projectID : $executionID;
            $productBranches = $productInfo->type != 'normal' ? $this->loadModel('execution')->getBranchByProduct($productID, $objectID) : array();
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $branches = $productInfo->type != 'normal' ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        }

        /* If executionID is setted, get builds and stories of this execution. */
        if($executionID)
        {
            $builds  = $this->loadModel('build')->getBuildPairs($productID, $branch, 'noempty,noterminate,nodone', $executionID, 'execution');
            $stories = $this->story->getExecutionStoryPairs($executionID);
            if(!$projectID) $projectID = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch('project');
        }
        else
        {
            $builds  = $this->loadModel('build')->getBuildPairs($productID, $branch, 'noempty,noterminate,nodone,withbranch');
            $stories = $this->story->getProductStoryPairs($productID, $branch);
        }

        $moduleOwner = $this->bug->getModuleOwner($moduleID, $productID);

        /* Set team members of the latest execution as assignedTo list. */
        $productMembers = $this->bug->getProductMemberPairs($productID);
        if(empty($productMembers)) $productMembers = $this->view->users;
        if($assignedTo and !isset($productMembers[$assignedTo]))
        {
            $user = $this->loadModel('user')->getById($assignedTo);
            if($user) $productMembers[$assignedTo] = $user->realname;
        }

        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);
        if(empty($moduleOptionMenu)) return print(js::locate(helper::createLink('tree', 'browse', "productID=$productID&view=story")));

        /* Get products and projects. */
        $products = $this->config->CRProduct ? $this->products : $this->product->getPairs('noclosed', 0, 'program_asc');
        $projects = array(0 => '');
        if($executionID)
        {
            $products       = array();
            $linkedProducts = $this->loadModel('product')->getProducts($executionID);
            foreach($linkedProducts as $product) $products[$product->id] = $product->name;

            if($projectID)
            {
                $project  = $this->loadModel('project')->getByID($projectID);
                $projects = array($projectID => $project->name);
            }
        }
        elseif($projectID)
        {
            $products    = array();
            $productList = $this->config->CRProduct ? $this->product->getOrderedProducts('all', 40, $projectID) : $this->product->getOrderedProducts('normal', 40, $projectID);
            foreach($productList as $product) $products[$product->id] = $product->name;

            $project   = $this->loadModel('project')->getByID($projectID);
            $projects += array($projectID => $project->name);

            /* Set project menu. */
            if($this->app->tab == 'project') $this->project->setMenu($projectID);
        }
        else
        {
            $projects += $this->product->getProjectPairsByProduct($productID, $branch);
        }

        /* Get block id of assinge to me. */
        $blockID = 0;
        if(isonlybody())
        {
            $blockID = $this->dao->select('id')->from(TABLE_BLOCK)
                ->where('block')->eq('assingtome')
                ->andWhere('module')->eq('my')
                ->andWhere('account')->eq($this->app->user->account)
                ->orderBy('order_desc')
                ->fetch('id');
        }

        /* Get executions. */
        $executions = array(0 => '');
        if(isset($projects[$projectID]) or $this->config->systemMode == 'classic') $executions += $this->product->getExecutionPairsByProduct($productID, $branch ? "0,$branch" : 0, 'id_desc', $projectID);
        $execution  = $executionID ? $this->loadModel('execution')->getByID($executionID) : '';
        $executions = isset($executions[$executionID]) ? $executions : $executions + array($executionID => $execution->name);

        /* Set custom. */
        foreach(explode(',', $this->config->bug->list->customCreateFields) as $field) $customFields[$field] = $this->lang->bug->$field;

        /* In repair classic mode, when the project is required, the asterisk is not displayed. */
        if($this->config->systemMode == 'classic' and strpos(",{$this->config->bug->create->requiredFields},", ',project,') !== false)
        {
            $this->config->bug->create->requiredFields = str_replace(',project,', ',execution,', ",{$this->config->bug->create->requiredFields},");
            $this->config->bug->create->requiredFields = trim($this->config->bug->create->requiredFields, ',');
        }

        $this->view->title        = isset($this->products[$productID]) ? $this->products[$productID] . $this->lang->colon . $this->lang->bug->create : $this->lang->bug->create;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->bug->custom->createFields;

        $this->view->gobackLink       = (isset($output['from']) and $output['from'] == 'global') ? $this->createLink('bug', 'browse', "productID=$productID") : '';
        $this->view->products         = $products;
        $this->view->productID        = $productID;
        $this->view->productName      = isset($this->products[$productID]) ? $this->products[$productID] : '';
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->stories          = $stories;
        $this->view->projects         = defined('TUTORIAL') ? $this->loadModel('tutorial')->getProjectPairs() : $projects;
        $this->view->project          = $project;
        $this->view->executions       = defined('TUTORIAL') ? $this->loadModel('tutorial')->getExecutionPairs() : $executions;
        $this->view->builds           = $builds;
        $this->view->moduleID         = (int)$moduleID;
        $this->view->projectID        = $projectID;
        $this->view->executionID      = $executionID;
        $this->view->taskID           = $taskID;
        $this->view->storyID          = $storyID;
        $this->view->buildID          = $buildID;
        $this->view->caseID           = $caseID;
        $this->view->runID            = $runID;
        $this->view->version          = $version;
        $this->view->testtask         = $testtask;
        $this->view->bugTitle         = $title;
        $this->view->pri              = $pri;
        $this->view->steps            = htmlSpecialString($steps);
        $this->view->os               = $os;
        $this->view->browser          = $browser;
        $this->view->productMembers   = $productMembers;
        $this->view->assignedTo       = $assignedTo;
        $this->view->deadline         = $deadline;
        $this->view->mailto           = $mailto;
        $this->view->keywords         = $keywords;
        $this->view->severity         = $severity;
        $this->view->type             = $type;
        $this->view->productInfo      = $productInfo;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        $this->view->blockID          = $blockID;
        $this->view->color            = $color;
        $this->view->stepsRequired    = strpos($this->config->bug->create->requiredFields, 'steps');
        $this->view->isStepsTemplate  = $steps == $this->lang->bug->tplStep . $this->lang->bug->tplResult . $this->lang->bug->tplExpect ? true : false;
        $this->view->issueKey         = $from == 'sonarqube' ? $output['sonarqubeID'] . ':' . $output['issueKey'] : '';

        $this->display();
    }

    /**
     * Batch create.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @param  int    $moduleID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function batchCreate($productID, $branch = '', $executionID = 0, $moduleID = 0, $extra = '')
    {
        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        if(!empty($_POST))
        {
            $actions = $this->bug->batchCreate($productID, $branch, $extra);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            /* Return bug id list when call the API. */
            if($this->viewType == 'json')
            {
                $bugIDList = array_keys($actions);
                return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'idList' => $bugIDList));
            }

            setcookie('bugModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);

            /* If link from no head then reload. */
            if(isonlybody() and $executionID)
            {
                $execution = $this->loadModel('execution')->getByID($executionID);
                if($execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($executionID, $this->session->execLaneType ? $this->session->execLaneType : 'all');
                    $kanbanData = json_encode($kanbanData);
                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData)"));
                }
                else
                {
                    return print(js::reload('parent.parent'));
                }
            }

            if(isonlybody()) return print(js::reload('parent.parent'));
            return print(js::locate($this->createLink('bug', 'browse', "productID={$productID}&branch=$branch&browseType=unclosed&param=0&orderBy=id_desc"), 'parent'));
        }

        /* Get product, then set menu. */
        $productID = $this->product->saveState($productID, $this->products);
        if($branch === '') $branch = (int)$this->cookie->preBranch;
        $this->qa->setMenu($this->products, $productID, $branch);

        /* If executionID is setted, get builds and stories of this execution. */
        if($executionID)
        {
            $builds    = $this->loadModel('build')->getBuildPairs($productID, $branch, 'noempty', $executionID, 'execution');
            $stories   = $this->story->getExecutionStoryPairs($executionID);
            $execution = $this->loadModel('execution')->getById($executionID);
            if($execution->type == 'kanban')
            {
                $this->loadModel('kanban');
                $regionPairs = $this->kanban->getRegionPairs($executionID, 0, 'execution');
                $regionID    = isset($output['regionID']) ? $output['regionID'] : key($regionPairs);
                $lanePairs   = $this->kanban->getLanePairsByRegion($regionID, 'bug');
                $laneID      = isset($output['laneID']) ? $output['laneID'] : key($lanePairs);

                $this->view->executionType = $execution->type;
                $this->view->regionID      = $regionID;
                $this->view->laneID        = $laneID;
                $this->view->regionPairs   = $regionPairs;
                $this->view->lanePairs     = $lanePairs;
            }
        }
        else
        {
            $builds  = $this->loadModel('build')->getBuildPairs($productID, $branch, 'noempty');
            $stories = $this->story->getProductStoryPairs($productID, $branch);
        }

        if($this->session->bugImagesFile)
        {
            $files = $this->session->bugImagesFile;
            foreach($files as $fileName => $file)
            {
                $title = $file['title'];
                $titles[$title] = $fileName;
            }
            $this->view->titles = $titles;
        }

        /* Set custom. */
        $product = $this->product->getById($productID);
        foreach(explode(',', $this->config->bug->list->customBatchCreateFields) as $field)
        {
            if($product->type != 'normal') $customFields[$product->type] = $this->lang->product->branchName[$product->type];
            $customFields[$field] = $this->lang->bug->$field;
        }

        if($product->type != 'normal')
        {
            $this->config->bug->custom->batchCreateFields = sprintf($this->config->bug->custom->batchCreateFields, $product->type);
        }
        else
        {
            $this->config->bug->custom->batchCreateFields = trim(sprintf($this->config->bug->custom->batchCreateFields, ''), ',');
        }

        $showFields = $this->config->bug->custom->batchCreateFields;
        if($product->type == 'normal')
        {
            $showFields = str_replace(array(0 => ",branch,", 1 => ",platform,"), '', ",$showFields,");
            $showFields = trim($showFields, ',');
        }

        $projectID = $this->lang->navGroup->bug == 'project' ? $this->session->project : (isset($execution) ? $execution->project : 0);

        /* Get branches. */
        if($executionID)
        {
            $productBranches = $product->type != 'normal' ? $this->execution->getBranchByProduct($productID, $executionID) : array();
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $branches = $product->type != 'normal' ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        }

        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->batchCreate;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID&branch=$branch"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->batchCreate;

        $this->view->project          = $this->loadModel('project')->getByID($projectID);
        $this->view->product          = $product;
        $this->view->productID        = $productID;
        $this->view->stories          = $stories;
        $this->view->builds           = $builds;
        $this->view->users            = $this->user->getPairs('devfirst|noclosed');
        $this->view->executions       = array('' => '') + $this->product->getExecutionPairsByProduct($productID, $branch ? "0,$branch" : 0, 'id_desc', $projectID);
        $this->view->executionID      = $executionID;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $branch === 'all' ? 0 : $branch);
        $this->view->moduleID         = $moduleID;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        $this->display();
    }

    /**
     * View a bug.
     *
     * @param  int    $bugID
     * @param  string $form
     * @access public
     * @return void
     */
    public function view($bugID, $from = 'bug')
    {
        /* Judge bug exits or not. */
        $bugID = (int)$bugID;
        $bug   = $this->bug->getById($bugID, true);
        if(!$bug) return print(js::error($this->lang->notFound) . js::locate($this->createLink('qa', 'index')));

        $this->session->set('storyList', '', 'product');
        $this->session->set('projectList', $this->app->getURI(true) . "#app={$this->app->tab}", 'project');
        $this->bug->checkBugExecutionPriv($bug);

        /* Update action. */
        if($bug->assignedTo == $this->app->user->account) $this->loadModel('action')->read('bug', $bugID);

        /* Set menu. */
        if(!isonlybody())
        {
            if($this->app->tab == 'project')   $this->loadModel('project')->setMenu($bug->project);
            if($this->app->tab == 'execution') $this->loadModel('execution')->setMenu($bug->execution);
            if($this->app->tab == 'qa')        $this->qa->setMenu($this->products, $bug->product, $bug->branch);

            if($this->app->tab == 'devops')
            {
                $repos = $this->loadModel('repo')->getRepoPairs('project', $bug->project);
                $this->repo->setMenu($repos);
                $this->lang->navGroup->bug = 'devops';
            }

            if($this->app->tab == 'product')
            {
                $this->loadModel('product')->setMenu($bug->product);
                $this->lang->product->menu->plan['subModule'] .= ',bug';
            }
        }

        /* Get product info. */
        $productID = $bug->product;
        $product   = $this->loadModel('product')->getByID($productID);
        $branches  = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($bug->product);

        $this->executeHooks($bugID);

        /* Header and positon. */
        $this->view->title      = "BUG #$bug->id $bug->title - " . $product->name;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $product->name);
        $this->view->position[] = $this->lang->bug->view;

        /* Assign. */
        $this->view->project     = $this->loadModel('project')->getByID($bug->project);
        $this->view->productID   = $productID;
        $this->view->branches    = $branches;
        $this->view->modulePath  = $this->tree->getParents($bug->module);
        $this->view->bugModule   = empty($bug->module) ? '' : $this->tree->getById($bug->module);
        $this->view->bug         = $bug;
        $this->view->from        = $from;
        $this->view->branchName  = $product->type == 'normal' ? '' : zget($branches, $bug->branch, '');
        $this->view->users       = $this->user->getPairs('noletter');
        $this->view->actions     = $this->action->getList('bug', $bugID);
        $this->view->builds      = $this->loadModel('build')->getBuildPairs($productID, 'all', '');
        $this->view->preAndNext  = $this->loadModel('common')->getPreAndNextObject('bug', $bugID);
        $this->view->product     = $product;

        $this->display();
    }

    /**
     * Edit a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function edit($bugID, $comment = false)
    {
        if(!empty($_POST))
        {
            $changes = array();
            $files   = array();
            if($comment == false)
            {
                $changes = $this->bug->update($bugID);
                if(dao::isError())
                {
                    if(defined('RUN_MODE') && RUN_MODE == 'api')
                    {
                        return $this->send(array('status' => 'error', 'message' => dao::getError()));
                    }
                    else
                    {
                        return print(js::error(dao::getError()));
                    }
                }
                $files = $this->loadModel('file')->saveUpload('bug', $bugID);
            }
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $action = (!empty($changes) or !empty($files)) ? 'Edited' : 'Commented';
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->action->create('bug', $bugID, $action, $fileAction . $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success', 'data' => $bugID));
            $bug = $this->bug->getById($bugID);

            $this->executeHooks($bugID);

            if($bug->toTask != 0)
            {
                foreach($changes as $change)
                {
                    if($change['field'] == 'status')
                    {
                        $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                        $cancelURL  = $this->server->HTTP_REFERER;
                        return print(js::confirm(sprintf($this->lang->bug->remindTask, $bug->Task), $confirmURL, $cancelURL, 'parent', 'parent'));
                    }
                }
            }
            if(isonlybody()) return print(js::reload('parent.parent'));
            return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        /* Get the info of bug, current product and modue. */
        $bug             = $this->bug->getById($bugID);
        $productID       = $bug->product;
        $executionID     = $bug->execution;
        $projectID       = $bug->project;
        $currentModuleID = $bug->module;
        $this->bug->checkBugExecutionPriv($bug);

        /* Set the menu. */
        if($this->app->tab == 'project') $this->loadModel('project')->setMenu($bug->project);
        if($this->app->tab == 'execution') $this->loadModel('execution')->setMenu($bug->execution);
        if($this->app->tab == 'qa') $this->qa->setMenu($this->products, $productID, $bug->branch);
        if($this->app->tab == 'devops')
        {
            session_write_close();
            $repos = $this->loadModel('repo')->getRepoPairs('project', $bug->project);
            $this->repo->setMenu($repos);
            $this->lang->navGroup->bug = 'devops';
        }

        /* Unset discarded types. */
        foreach($this->config->bug->discardedTypes as $type)
        {
            if($bug->type != $type) unset($this->lang->bug->typeList[$type]);
        }

        if($this->app->tab == 'qa')
        {
            $this->view->products = $this->config->CRProduct ? $this->products : $this->product->getPairs('noclosed');
        }
        if($this->app->tab == 'project')
        {
            $products = array();
            $productList = $this->config->CRProduct ? $this->product->getOrderedProducts('all', 40, $bug->project) : $this->product->getOrderedProducts('normal', 40, $bug->project);
            foreach($productList as $product) $products[$product->id] = $product->name;
            $this->view->products = $products;
        }

        /* Set header and position. */
        $this->view->title      = $this->lang->bug->edit . "BUG #$bug->id $bug->title - " . $this->products[$productID];
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->edit;

        /* Assign. */
        $product   = $this->loadModel('product')->getByID($productID);
        $allBuilds = $this->loadModel('build')->getBuildPairs($productID, 'all', 'noempty');
        if($executionID)
        {
            $openedBuilds = $this->build->getBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone,withbranch', $executionID, 'execution');
        }
        elseif($projectID)
        {
            $openedBuilds = $this->build->getBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone,withbranch', $projectID, 'project');
        }
        else
        {
            $openedBuilds = $this->build->getBuildPairs($productID, $bug->branch, 'noempty,noterminate,nodone,withbranch');
        }

        /* Set the openedBuilds list. */
        $oldOpenedBuilds = array();
        $bugOpenedBuilds = explode(',', $bug->openedBuild);
        foreach($bugOpenedBuilds as $buildID)
        {
            if(isset($allBuilds[$buildID])) $oldOpenedBuilds[$buildID] = $allBuilds[$buildID];
        }
        $openedBuilds = $openedBuilds + $oldOpenedBuilds;

        /* Set the resolvedBuilds list. */
        $oldResolvedBuild = array();
        if(($bug->resolvedBuild) and isset($allBuilds[$bug->resolvedBuild])) $oldResolvedBuild[$bug->resolvedBuild] = $allBuilds[$bug->resolvedBuild];

        $projectID = $this->lang->navGroup->bug == 'project' ? $this->session->project : 0;

        if($this->app->tab == 'execution' or $this->app->tab == 'project')
        {
            $objectID = $this->app->tab == 'project' ? $bug->project : $bug->execution;
        }

        /* Display status of branch. */
        $branches = $this->loadModel('branch')->getList($productID, isset($objectID) ? $objectID : 0, 'all');
        $branchOption    = array();
        $branchTagOption = array();
        foreach($branches as $branchInfo)
        {
            $branchOption[$branchInfo->id]    = $branchInfo->name;
            $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
        }

        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'bug', $startModuleID = 0, $bug->branch);
        if(!isset($moduleOptionMenu[$bug->module])) $moduleOptionMenu += $this->tree->getModulesName($bug->module);

        $this->view->bug              = $bug;
        $this->view->productID        = $productID;
        $this->view->product          = $product;
        $this->view->productName      = $this->products[$productID];
        $this->view->plans            = $this->loadModel('productplan')->getPairs($productID, $bug->branch, '', true);
        $this->view->projects         = array(0 => '') + $this->product->getProjectPairsByProduct($productID, $bug->branch, $bug->project);
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->currentModuleID  = $currentModuleID;
        $this->view->executions       = array(0 => '') + $this->product->getExecutionPairsByProduct($bug->product, $bug->branch, 'id_desc', $bug->project);
        $this->view->stories          = $bug->execution ? $this->story->getExecutionStoryPairs($bug->execution) : $this->story->getProductStoryPairs($bug->product, $bug->branch);
        $this->view->branchOption     = $branchOption;
        $this->view->branchTagOption  = $branchTagOption;
        $this->view->tasks            = $this->task->getExecutionTaskPairs($bug->execution);
        $this->view->testtasks        = $this->loadModel('testtask')->getPairs($bug->product, $bug->execution, $bug->testtask);
        $this->view->users            = $this->user->getPairs('', "$bug->assignedTo,$bug->resolvedBy,$bug->closedBy,$bug->openedBy");
        $this->view->openedBuilds     = $openedBuilds;
        $this->view->resolvedBuilds   = array('' => '') + $openedBuilds + $oldResolvedBuild;
        $this->view->actions          = $this->action->getList('bug', $bugID);

        $this->display();
    }

    /**
     * Batch edit bug.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchEdit($productID = 0, $branch = 0)
    {
        if($this->app->tab == 'product')
        {
            $this->product->setMenu($productID);
        }

        if($this->post->titles)
        {
            $allChanges = $this->bug->batchUpdate();

            foreach($allChanges as $bugID => $changes)
            {
                if(empty($changes)) continue;

                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);

                $bug = $this->bug->getById($bugID);
                if($bug->toTask != 0)
                {
                    foreach($changes as $change)
                    {
                        if($change['field'] == 'status')
                        {
                            $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                            $cancelURL  = $this->server->HTTP_REFERER;
                            return print(js::confirm(sprintf($this->lang->bug->remindTask, $bug->task), $confirmURL, $cancelURL, 'parent', 'parent'));
                        }
                    }
                }
            }
            return print(js::locate($this->session->bugList, 'parent'));
        }

        if(!$this->post->bugIDList) return print(js::locate($this->session->bugList, 'parent'));
        /* Initialize vars.*/
        $bugIDList = array_unique($this->post->bugIDList);
        $bugs      = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($bugIDList)->fetchAll('id');

        /* The bugs of a product. */
        if($productID)
        {
            $product = $this->product->getByID($productID);
            $branchProduct = $product->type == 'normal' ? false : true;

            /* Set plans. */
            $plans = $this->loadModel('productplan')->getPairs($productID, $branch, '', true);
            $plans = array('' => '', 'ditto' => $this->lang->bug->ditto) + $plans;

            /* Set branches and modules. */
            $branches        = 0;
            $branchTagOption = array();
            $modules         = array();
            if($product->type != 'normal')
            {
                $branches = $this->loadModel('branch')->getList($productID, 0 ,'all');
                foreach($branches as $branchInfo) $branchTagOption[$productID][$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
                $branches = array_keys($branches);
            }

            $modulePairs = $this->tree->getOptionMenu($productID, 'bug', 0, $branches);
            $modules[$productID] = $product->type != 'normal' ? $modulePairs : array(0 => $modulePairs);

            /* Set product menu. */
            $this->qa->setMenu($this->products, $productID, $branch);

            $this->view->title      = $product->name . $this->lang->colon . "BUG" . $this->lang->bug->batchEdit;
            $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID&branch=$branch"), $this->products[$productID]);
            $this->view->plans      = $plans;
        }
        /* The bugs of my. */
        else
        {
            $branchProduct   = false;
            $productIdList   = array();
            $branchTagOption = array();
            foreach($bugs as $bug) $productIdList[$bug->product] = $bug->product;
            $productList = $this->product->getByIdList($productIdList);
            foreach($productList as $product)
            {
                $branches = 0;
                if($product->type != 'normal')
                {
                    $branches = $this->loadModel('branch')->getList($product->id, 0 ,'all');
                    foreach($branches as $branchInfo) $branchTagOption[$product->id][$branchInfo->id] = '/' . $product->name . '/' . $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');

                    $branches      = array_keys($branches);
                    $branchProduct = true;
                }

                $modulePairs = $this->tree->getOptionMenu($product->id, 'bug', 0, $branches);
                $modules[$product->id] = $product->type != 'normal' ? $modulePairs : array(0 => $modulePairs);
            }

            $this->app->loadLang('my');
            $this->lang->task->menu = $this->lang->my->menu->work;
            $this->lang->my->menu->work['subModule'] = 'bug';

            $this->view->position[]  = html::a($this->createLink('my', 'bug'), $this->lang->my->bug);
            $this->view->title       = "BUG" . $this->lang->bug->batchEdit;
            $this->view->plans       = $this->loadModel('productplan')->getPairs($product->id, $branch);
            $this->view->productList = $productList;
        }

        /* Judge whether the editedBugs is too large and set session. */
        $countInputVars  = count($bugs) * (count(explode(',', $this->config->bug->custom->batchEditFields)) + 2);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* Set Custom*/
        foreach(explode(',', $this->config->bug->list->customBatchEditFields) as $field) $customFields[$field] = $this->lang->bug->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->bug->custom->batchEditFields;

        /* Set users. */
        $appendUsers = array();
        foreach($bugs as $bug)
        {
            $appendUsers[$bug->assignedTo] = $bug->assignedTo;
            $appendUsers[$bug->resolvedBy] = $bug->resolvedBy;

            if(!isset($modules[$bug->product][$bug->branch])) $modules[$bug->product][$bug->branch] = $modules[$bug->product][0] + $this->tree->getModulesName($bug->module);
        }
        $users = $this->user->getPairs('devfirst', $appendUsers, $this->config->maxCount);
        $users = array('' => '', 'ditto' => $this->lang->bug->ditto) + $users;

        /* Assign. */
        $this->view->position[]      = $this->lang->bug->common;
        $this->view->position[]      = $this->lang->bug->batchEdit;
        $this->view->productID       = $productID;
        $this->view->branchProduct   = $branchProduct;
        $this->view->severityList    = array('ditto' => $this->lang->bug->ditto) + $this->lang->bug->severityList;
        $this->view->typeList        = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->typeList;
        $this->view->priList         = array('0' => '', 'ditto' => $this->lang->bug->ditto) + $this->lang->bug->priList;
        $this->view->resolutionList  = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->resolutionList;
        $this->view->statusList      = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->statusList;
        $this->view->osList          = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->osList;
        $this->view->browserList     = array('' => '',  'ditto' => $this->lang->bug->ditto) + $this->lang->bug->browserList;
        $this->view->bugs            = $bugs;
        $this->view->branch          = $branch;
        $this->view->users           = $users;
        $this->view->modules         = $modules;
        $this->view->branchTagOption = $branchTagOption;

        $this->display();
    }

    /**
     * Update assign of bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function assignTo($bugID)
    {
        $bug = $this->bug->getById($bugID);
        $this->bug->checkBugExecutionPriv($bug);

        /* Set menu. */
        $this->qa->setMenu($this->products, $bug->product, $bug->branch);

        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = $this->bug->assign($bugID);
            if(dao::isError()) return print(js::error(dao::getError()));
            $actionID = $this->action->create('bug', $bugID, 'Assigned', $this->post->comment, $this->post->assignedTo);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            if(isonlybody())
            {
                $bug       = $this->bug->getById($bugID);
                $execution = $this->loadModel('execution')->getByID($bug->execution);
                if(isset($execution->type) and $execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($bug->execution, $this->session->execLaneType ? $this->session->execLaneType : 'all');
                    $kanbanData = json_encode($kanbanData);

                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData)"));
                }
                else
                {
                    return print(js::closeModal('parent.parent'));
                }
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {    
                return $this->send(array('status' => 'success', 'data' => $bugID));
            }    
            else 
            {    
                return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
            }    
        }

        if($this->app->tab == 'project')
        {
            $users = $this->user->getTeamMemberPairs($bug->project, 'project', 'nodeleted', $bug->assignedTo);
        }
        elseif($this->app->tab == 'execution')
        {
            $users = $this->user->getTeamMemberPairs($bug->execution, 'execution', 'nodeleted', $bug->assignedTo);
        }
        else
        {
            $users = $this->user->getPairs('noclosed', $bug->assignedTo);
        }

        $this->view->title      = $this->products[$bug->product] . $this->lang->colon . $this->lang->bug->assignedTo;
        $this->view->position[] = $this->lang->bug->assignedTo;

        $this->view->users   = $users;
        $this->view->bug     = $bug;
        $this->view->bugID   = $bugID;
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Batch change branch.
     *
     * @param  int    $branchID
     * @access public
     * @return void
     */
    public function batchChangeBranch($branchID)
    {
        if($this->post->bugIDList)
        {
            $bugIDList     = $this->post->bugIDList;
            $bugIDList     = array_unique($bugIDList);
            $oldBugs       = $this->bug->getByList($bugIDList);
            $skipBugIDList = '';
            unset($_POST['bugIDList']);

            /* Remove condition mismatched bugs. */
            foreach($bugIDList as $key => $bugID)
            {
                $oldBug = $oldBugs[$bugID];
                if($branchID == $oldBug->branch)
                {
                    unset($bugIDList[$key]);
                    continue;
                }
                elseif($branchID != $oldBug->branch and !empty($oldBug->module))
                {
                    $skipBugIDList .= '[' . $bugID . ']';
                    unset($bugIDList[$key]);
                    continue;
                }
            }

            if(!empty($skipBugIDList))
            {
                echo js::alert(sprintf($this->lang->bug->noSwitchBranch, $skipBugIDList));
            }

            $allChanges = $this->bug->batchChangeBranch($bugIDList, $branchID, $oldBugs);
            if(dao::isError()) return print(js::error(dao::getError()));
            foreach($allChanges as $bugID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
        $this->loadModel('score')->create('ajax', 'batchOther');
        return print(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Batch change the module of bug.
     *
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule($moduleID)
    {
        if($this->post->bugIDList)
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);
            unset($_POST['bugIDList']);
            $allChanges = $this->bug->batchChangeModule($bugIDList, $moduleID);
            if(dao::isError()) return print(js::error(dao::getError()));
            foreach($allChanges as $bugID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
        $this->loadModel('score')->create('ajax', 'batchOther');
        return print(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Batch change the plan of bug.
     *
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function batchChangePlan($planID)
    {
        if($this->post->bugIDList)
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);
            unset($_POST['bugIDList']);
            $allChanges = $this->bug->batchChangePlan($bugIDList, $planID);
            if(dao::isError()) return print(js::error(dao::getError()));
            foreach($allChanges as $bugID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('bug', $bugID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
        $this->loadModel('score')->create('ajax', 'batchOther');
        return print(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Batch update assign of bug.
     *
     * @param  int     $objectID  projectID|executionID
     * @param  string  $type      execution|project|product|my
     * @access public
     * @return void
     */
    public function batchAssignTo($objectID, $type = 'execution')
    {
        if(!empty($_POST) && isset($_POST['bugIDList']))
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);
            unset($_POST['bugIDList']);
            foreach($bugIDList as $bugID)
            {
                $this->loadModel('action');
                $changes = $this->bug->assign($bugID);
                if(dao::isError()) return print(js::error(dao::getError()));
                $actionID = $this->action->create('bug', $bugID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                $this->action->logHistory($actionID, $changes);
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
        }

        if($type == 'product' || $type == 'my') return print(js::locate($this->session->bugList, 'parent'));
        if($type == 'execution') return print(js::locate($this->createLink('execution', 'bug', "executionID=$objectID")));
        if($type == 'project')   return print(js::locate($this->createLink('project', 'bug', "projectID=$objectID")));
    }

    /**
     * confirm a bug.
     *
     * @param  int    $bugID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function confirmBug($bugID, $extra = '')
    {
        $bug = $this->bug->getById($bugID);
        if(!empty($_POST))
        {
            $changes = $this->bug->confirm($bugID, $extra);
            if(dao::isError()) return print(js::error(dao::getError()));
            $actionID = $this->action->create('bug', $bugID, 'bugConfirmed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            $extra = str_replace(array(',', ' '), array('&', ''), $extra);
            parse_str($extra, $output);
            if(isonlybody())
            {
                $execution = $this->loadModel('execution')->getByID($bug->execution);
                if(isset($execution->type) and $execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $regionID   = isset($output['regionID']) ? $output['regionID'] : 0;
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($bug->execution, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                    $kanbanData = json_encode($kanbanData);

                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData, $regionID)"));
                }
                else
                {
                    return print(js::closeModal('parent.parent'));
                }
            }
            return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        $productID = $bug->product;
        $this->bug->checkBugExecutionPriv($bug);
        $this->qa->setMenu($this->products, $productID, $bug->branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->confirmBug;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->confirmBug;

        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('noclosed', $bug->assignedTo);
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Batch confirm bugs.
     *
     * @access public
     * @return void
     */
    public function batchConfirm()
    {
        if(!$this->post->bugIDList) return print(js::locate($this->session->bugList, 'parent'));

        $bugIDList = array_unique($this->post->bugIDList);
        $this->bug->batchConfirm($bugIDList);
        if(dao::isError()) return print(js::error(dao::getError()));
        foreach($bugIDList as $bugID) $this->action->create('bug', $bugID, 'bugConfirmed');
        $this->loadModel('score')->create('ajax', 'batchOther');
        return print(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Resolve a bug.
     *
     * @param  int    $bugID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function resolve($bugID, $extra = '')
    {
        $bug = $this->bug->getById($bugID);
        if($bug->execution) $execution = $this->loadModel('execution')->getByID($bug->execution);

        if(!empty($_POST))
        {
            $changes = $this->bug->resolve($bugID, $extra);
            if(dao::isError()) return print(js::error(dao::getError()));
            $files = $this->loadModel('file')->saveUpload('bug', $bugID);

            $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
            $actionID = $this->action->create('bug', $bugID, 'Resolved', $fileAction . $this->post->comment, $this->post->resolution . ($this->post->duplicateBug ? ':' . (int)$this->post->duplicateBug : ''));
            $this->action->logHistory($actionID, $changes);

            $bug = $this->bug->getById($bugID);

            $this->executeHooks($bugID);

            if($bug->toTask != 0)
            {
                /* If task is not finished, update it's status. */
                $task = $this->task->getById($bug->toTask);
                if($task->status != 'done')
                {
                    $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                    unset($_GET['onlybody']);
                    $cancelURL  = $this->createLink('bug', 'view', "bugID=$bugID");
                    return print(js::confirm(sprintf($this->lang->bug->remindTask, $bug->toTask), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                }
            }

            $extra = str_replace(array(',', ' '), array('&', ''), $extra);
            parse_str($extra, $output);
            if(isonlybody())
            {
                if(isset($execution->type) and $execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $regionID   = isset($output['regionID']) ? $output['regionID'] : 0;
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($bug->execution, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                    $kanbanData = json_encode($kanbanData);

                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData, $regionID)"));
                }
                else
                {
                    return print(js::closeModal('parent.parent'));
                }
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                return print(array('status' => 'success', 'data' => $bugID));
            }
            else
            {
                return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
            }
        }

        $projectID  = $this->lang->navGroup->bug == 'project' ? $this->session->project : 0;
        $productID  = $bug->product;
        $users      = $this->user->getPairs('noclosed');
        $assignedTo = $bug->openedBy;
        if(!isset($users[$assignedTo])) $assignedTo = $this->bug->getModuleOwner($bug->module, $productID);
        unset($this->lang->bug->resolutionList['tostory']);

        $this->bug->checkBugExecutionPriv($bug);
        $this->qa->setMenu($this->products, $productID, $bug->branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->resolve;
        $this->view->bug        = $bug;
        $this->view->users      = $users;
        $this->view->assignedTo = $assignedTo;
        $this->view->executions = $this->loadModel('product')->getExecutionPairsByProduct($productID, $bug->branch ? "0,{$bug->branch}" : 0, 'id_desc', $projectID);
        $this->view->builds     = $this->loadModel('build')->getBuildPairs($productID, $bug->branch, 'withbranch');
        $this->view->actions    = $this->action->getList('bug', $bugID);
        $this->view->execution  = isset($execution) ? $execution : '';
        $this->display();
    }

    /**
     * Batch resolve bugs.
     *
     * @param  string    $resolution
     * @param  string    $resolvedBuild
     * @access public
     * @return void
     */
    public function batchResolve($resolution, $resolvedBuild = '')
    {
        if(!$this->post->bugIDList) return print(js::locate($this->session->bugList, 'parent'));

        $bugIDList = array_unique($this->post->bugIDList);
        $changes   = $this->bug->batchResolve($bugIDList, $resolution, $resolvedBuild);
        if(dao::isError()) return print(js::error(dao::getError()));

        foreach($changes as $bugID => $bugChanges)
        {
            $actionID = $this->action->create('bug', $bugID, 'Resolved', '', $resolution);
            $this->action->logHistory($actionID, $bugChanges);
        }

        $this->loadModel('score')->create('ajax', 'batchOther');
        return print(js::locate($this->session->bugList, 'parent'));
    }

    /**
     * Activate a bug.
     *
     * @param  int    $bugID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function activate($bugID, $extra = '')
    {
        $bug = $this->bug->getById($bugID);
        if(!empty($_POST))
        {
            $changes = $this->bug->activate($bugID, $extra);
            if(dao::isError()) return print(js::error(dao::getError()));

            $files = $this->loadModel('file')->saveUpload('bug', $bugID);

            $actionID = $this->action->create('bug', $bugID, 'Activated', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            $extra = str_replace(array(',', ' '), array('&', ''), $extra);
            parse_str($extra, $output);
            if(isonlybody())
            {
                $execution = $this->loadModel('execution')->getByID($bug->execution);
                if(isset($execution->type) and $execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $regionID   = isset($output['regionID']) ? $output['regionID'] : 0;
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($bug->execution, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                    $kanbanData = json_encode($kanbanData);

                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData, $regionID)"));
                }
                else
                {
                    return print(js::closeModal('parent.parent'));
                }
            }
            return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
        }

        $productID = $bug->product;
        $this->bug->checkBugExecutionPriv($bug);
        $this->qa->setMenu($this->products, $productID, $bug->branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->activate;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->activate;

        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('noclosed', $bug->resolvedBy);
        $this->view->builds  = $this->loadModel('build')->getBuildPairs($productID, $bug->branch, 'noempty');
        $this->view->actions = $this->action->getList('bug', $bugID);

        $this->display();
    }

    /**
     * Close a bug.
     *
     * @param  int    $bugID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function close($bugID, $extra = '')
    {
        $bug = $this->bug->getById($bugID);
        if(!empty($_POST))
        {
            $changes = $this->bug->close($bugID, $extra);
            if(dao::isError()) return print(js::error(dao::getError()));

            $actionID = $this->action->create('bug', $bugID, 'Closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            $this->executeHooks($bugID);

            $extra = str_replace(array(',', ' '), array('&', ''), $extra);
            parse_str($extra, $output);
            if(isonlybody())
            {
                $execution = $this->loadModel('execution')->getByID($bug->execution);
                if(isset($execution->type) and $execution->type == 'kanban' and $this->app->tab == 'execution')
                {
                    $regionID   = isset($output['regionID']) ? $output['regionID'] : 0;
                    $kanbanData = $this->loadModel('kanban')->getRDKanban($bug->execution, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                    $kanbanData = json_encode($kanbanData);

                    return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData, $regionID)"));
                }
                else
                {
                    return print(js::closeModal('parent.parent', 'this', "function(){parent.parent.location.reload();}"));
                }
            }
            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                return $this->send(array('status' => 'success', 'data' => $bugID));
            }
            else
            {
                return print(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
            }
        }

        $productID = $bug->product;
        $this->bug->checkBugExecutionPriv($bug);
        $this->qa->setMenu($this->products, $productID, $bug->branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->close;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->close;

        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('noletter');
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Link related bugs.
     *
     * @param  int    $bugID
     * @param  string $browseType
     * @param  int    $param
     * @access public
     * @return void
     */
    public function linkBugs($bugID, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get bug and queryID. */
        $bug     = $this->bug->getById($bugID);
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        $this->bug->checkBugExecutionPriv($bug);

        /* Set the menu. */
        $this->qa->setMenu($this->products, $bug->product, $bug->branch);

        /* Build the search form. */
        $actionURL = $this->createLink('bug', 'linkBugs', "bugID=$bugID&browseType=bySearch&queryID=myQueryID", '', true);
        $this->bug->buildSearchForm($bug->product, $this->products, $queryID, $actionURL);

        /* Get bugs to link. */
        $bugs2Link = $this->bug->getBugs2Link($bugID, $browseType, $queryID, $pager);

        /* Assign. */
        $this->view->title      = $this->lang->bug->linkBugs . "BUG #$bug->id $bug->title - " . $this->products[$bug->product];
        $this->view->position[] = html::a($this->createLink('product', 'view', "productID=$bug->product"), $this->products[$bug->product]);
        $this->view->position[] = html::a($this->createLink('bug', 'view', "bugID=$bugID"), $bug->title);
        $this->view->position[] = $this->lang->bug->linkBugs;
        $this->view->bug        = $bug;
        $this->view->bugs2Link  = $bugs2Link;
        $this->view->pager      = $pager;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->display();
    }

    /**
     * Batch close bugs.
     *
     * @access public
     * @return void
     */
    public function batchClose()
    {
        if($this->post->bugIDList)
        {
            $bugIDList = $this->post->bugIDList;
            $bugIDList = array_unique($bugIDList);

            /* Reset $_POST. Do not unset that because the function of close need that in model. */
            $_POST = array();

            $bugs = $this->bug->getByList($bugIDList);
            foreach($bugs as $bugID => $bug)
            {
                if($bug->status != 'resolved')
                {
                    if($bug->status != 'closed') $skipBugs[$bugID] = $bugID;
                    continue;
                }

                $changes = $this->bug->close($bugID);

                $actionID = $this->action->create('bug', $bugID, 'Closed');
                $this->action->logHistory($actionID, $changes);
            }
            $this->loadModel('score')->create('ajax', 'batchOther');
            if(isset($skipBugs)) echo js::alert(sprintf($this->lang->bug->skipClose, join(',', $skipBugs)));
        }
        return print(js::reload('parent'));
    }

    /**
     * Batch activate bugs.
     *
     * @access public
     * @return void
     */
    public function batchActivate($productID, $branch = 0)
    {
        if($this->post->statusList)
        {
            $activateBugs = $this->bug->batchActivate();
            foreach($activateBugs as $bugID => $bug) $this->action->create('bug', $bugID, 'Activated', $bug['comment']);
            $this->loadModel('score')->create('ajax', 'batchOther');
            return print(js::locate($this->session->bugList, 'parent'));
        }

        if(!$this->post->bugIDList) return print(js::locate($this->session->bugList, 'parent'));

        $bugIDList = array_unique($this->post->bugIDList);
        $bugs = $this->dao->select('id, title, status, resolvedBy, openedBuild')->from(TABLE_BUG)->where('id')->in($bugIDList)->fetchAll('id');

        $this->qa->setMenu($this->products, $productID, $branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->batchActivate;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->batchActivate;

        $this->view->bugs    = $bugs;
        $this->view->users   = $this->user->getPairs();
        $this->view->builds  = $this->loadModel('build')->getBuildPairs($productID, $branch, 'noempty');

        $this->display();
    }

    /**
     * Confirm story change.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function confirmStoryChange($bugID)
    {
        $bug = $this->bug->getById($bugID);
        $this->bug->checkBugExecutionPriv($bug);
        $this->dao->update(TABLE_BUG)->set('storyVersion')->eq($bug->latestStoryVersion)->where('id')->eq($bugID)->exec();
        $this->loadModel('action')->create('bug', $bugID, 'confirmed', '', $bug->latestStoryVersion);
        return print(js::reload('parent'));
    }

    /**
     * Delete a bug.
     *
     * @param  int    $bugID
     * @param  string $confirm  yes|no
     * @param  string $from taskkanban
     * @access public
     * @return void
     */
    public function delete($bugID, $confirm = 'no', $from = '')
    {
        $bug = $this->bug->getById($bugID);
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->bug->confirmDelete, inlink('delete', "bugID=$bugID&confirm=yes&from=$from")));
        }
        else
        {
            $this->bug->delete(TABLE_BUG, $bugID);
            if($bug->toTask != 0)
            {
                $task = $this->task->getById($bug->toTask);
                if(!$task->deleted)
                {
                    $confirmURL = $this->createLink('task', 'view', "taskID=$bug->toTask");
                    unset($_GET['onlybody']);
                    $cancelURL  = $this->createLink('bug', 'view', "bugID=$bugID");
                    return print(js::confirm(sprintf($this->lang->bug->remindTask, $bug->toTask), $confirmURL, $cancelURL, 'parent', 'parent.parent'));
                }
            }

            $this->executeHooks($bugID);

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));

            if(isonlybody()) return print(js::reload('parent.parent'));
            if($from == 'taskkanban') return print(js::reload('parent'));

            $locateLink = $this->session->bugList ? $this->session->bugList : inlink('browse', "productID={$bug->product}");
            return print(js::locate($locateLink, 'parent'));
        }
    }

    /**
     * AJAX: get bugs of a user in html select.
     *
     * @param  int    $userID
     * @param  string $id       the id of the select control.
     * @access public
     * @return string
     */
    public function ajaxGetUserBugs($userID = '', $id = '')
    {
        if($userID == '') $userID = $this->app->user->id;
        $user    = $this->loadModel('user')->getById($userID, 'id');
        $account = $user->account;
        $bugs    = $this->bug->getUserBugPairs($account);

        if($id) return print(html::select("bugs[$id]", $bugs, '', 'class="form-control"'));
        return print(html::select('bug', $bugs, '', 'class=form-control'));
    }

    /**
     * AJAX: Get bug owner of a module.
     *
     * @param  int    $moduleID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function ajaxGetModuleOwner($moduleID, $productID = 0)
    {
        $account  = $this->bug->getModuleOwner($moduleID, $productID);
        $realName = '';
        if(!empty($account))
        {
            $user        = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($account)->fetch();
            $firstLetter = ucfirst(substr($account, 0, 1)) . ':';
            if(!empty($this->config->isINT)) $firstLetter = '';
            $realName = $firstLetter . ($user->realname ? $user->realname : $account);
        }
        return print(json_encode(array($account, $realName)));
    }

    /**
     * AJAX: get team members of the executions as assignedTo list.
     *
     * @param  int    $executionID
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadAssignedTo($executionID, $selectedUser = '')
    {
        $executionMembers = $this->user->getTeamMemberPairs($executionID, 'execution', '', $selectedUser);

        $execution = $this->loadModel('execution')->getByID($executionID);
        if(empty($selectedUser)) $selectedUser = $execution->QD;

        return print(html::select('assignedTo', $executionMembers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get team members of the latest executions of a product as assignedTo list.
     *
     * @param  int    $productID
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadExecutionTeamMembers($productID, $selectedUser = '')
    {
        $productMembers = $this->bug->getProductMemberPairs($productID);

        return print(html::select('assignedTo', $productMembers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get all users as assignedTo list.
     *
     * @param  string $selectedUser
     * @access public
     * @return string
     */
    public function ajaxLoadAllUsers($selectedUser = '')
    {
        $allUsers = $this->loadModel('user')->getPairs('devfirst|noclosed');

        return print(html::select('assignedTo', $allUsers, $selectedUser, 'class="form-control"'));
    }

    /**
     * AJAX: get actions of a bug. for web app.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function ajaxGetDetail($bugID)
    {
        $this->view->actions = $this->loadModel('action')->getList('bug', $bugID);
        $this->display();
    }

    /**
     * Get data to export
     *
     * @param  string $productID
     * @param  string $orderBy
     * @param  string $browseType
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function export($productID, $orderBy, $browseType = '', $executionID = 0)
    {
        if($_POST)
        {
            $this->loadModel('file');
            $this->loadModel('branch');
            $bugLang   = $this->lang->bug;
            $bugConfig = $this->config->bug;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $bugConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($bugLang->$fieldName) ? $bugLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get bugs. */
            $bugs = $this->dao->select('*')->from(TABLE_BUG)->where($this->session->bugQueryCondition)
                ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                ->orderBy($orderBy)->fetchAll('id');

            /* Get users, products and executions. */
            $users      = $this->loadModel('user')->getPairs('noletter');
            $products   = $this->loadModel('product')->getPairs();
            $projects   = $this->loadModel('project')->getPairsByProgram();
            $executions = $this->loadModel('execution')->getPairs($this->projectID, 'all', 'all');

            /* Get related objects id lists. */
            $relatedProductIdList = array();
            $relatedStoryIdList   = array();
            $relatedTaskIdList    = array();
            $relatedBugIdList     = array();
            $relatedCaseIdList    = array();
            $relatedBuildIdList   = array();
            $relatedBranchIdList  = array();

            foreach($bugs as $bug)
            {
                $relatedProductIdList[$bug->product]  = $bug->product;
                $relatedStoryIdList[$bug->story]      = $bug->story;
                $relatedTaskIdList[$bug->task]        = $bug->task;
                $relatedCaseIdList[$bug->case]        = $bug->case;
                $relatedBugIdList[$bug->duplicateBug] = $bug->duplicateBug;
                $relatedBranchIdList[$bug->branch]    = $bug->branch;

                /* Process link bugs. */
                $linkBugs = explode(',', $bug->linkBug);
                foreach($linkBugs as $linkBugID)
                {
                    if($linkBugID) $relatedBugIdList[$linkBugID] = trim($linkBugID);
                }

                /* Process builds. */
                $builds = $bug->openedBuild . ',' . $bug->resolvedBuild;
                $builds = explode(',', $builds);
                foreach($builds as $buildID)
                {
                    if($buildID) $relatedBuildIdList[$buildID] = trim($buildID);
                }
            }

            /* Get related objects title or names. */
            $productsType   = $this->dao->select('id, type')->from(TABLE_PRODUCT)->where('id')->in($relatedProductIdList)->fetchPairs();
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY) ->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedTasks   = $this->dao->select('id, name')->from(TABLE_TASK)->where('id')->in($relatedTaskIdList)->fetchPairs();
            $relatedBugs    = $this->dao->select('id, title')->from(TABLE_BUG)->where('id')->in($relatedBugIdList)->fetchPairs();
            $relatedCases   = $this->dao->select('id, title')->from(TABLE_CASE)->where('id')->in($relatedCaseIdList)->fetchPairs();
            $relatedBranch  = array('0' => $this->lang->branch->main) + $this->dao->select('id, name')->from(TABLE_BRANCH)->where('id')->in($relatedBranchIdList)->fetchPairs();
            $relatedBuilds  = array('trunk' => $this->lang->trunk) + $this->dao->select('id, name')->from(TABLE_BUILD)->where('id')->in($relatedBuildIdList)->fetchPairs();
            $relatedFiles   = $this->dao->select('id, objectID, pathname, title')->from(TABLE_FILE)->where('objectType')->eq('bug')->andWhere('objectID')->in(@array_keys($bugs))->andWhere('extra')->ne('editor')->fetchGroup('objectID');
            $relatedModules = $this->loadModel('tree')->getAllModulePairs('bug');

            foreach($bugs as $bug)
            {
                if($this->post->fileType == 'csv')
                {
                    $bug->steps = str_replace("<br />", "\n", $bug->steps);
                    $bug->steps = str_replace('"', '""', $bug->steps);
                    $bug->steps = str_replace('&nbsp;', ' ', $bug->steps);
                }

                /* fill some field with useful value. */
                $bug->product   = !isset($products[$bug->product])     ? '' : $products[$bug->product] . "(#$bug->product)";
                $bug->project   = !isset($projects[$bug->project])     ? '' : $projects[$bug->project] . "(#$bug->project)";
                $bug->execution = !isset($executions[$bug->execution]) ? '' : $executions[$bug->execution] . "(#$bug->execution)";
                $bug->story     = !isset($relatedStories[$bug->story]) ? '' : $relatedStories[$bug->story] . "(#$bug->story)";
                $bug->task      = !isset($relatedTasks[$bug->task])    ? '' : $relatedTasks[$bug->task] . "($bug->task)";
                $bug->case      = !isset($relatedCases[$bug->case])    ? '' : $relatedCases[$bug->case] . "($bug->case)";

                if(isset($relatedModules[$bug->module]))       $bug->module        = $relatedModules[$bug->module] . "(#$bug->module)";
                if(isset($relatedBugs[$bug->duplicateBug]))    $bug->duplicateBug  = $relatedBugs[$bug->duplicateBug] . "($bug->duplicateBug)";
                if(isset($relatedBuilds[$bug->resolvedBuild])) $bug->resolvedBuild = $relatedBuilds[$bug->resolvedBuild] . "(#$bug->resolvedBuild)";
                if(isset($relatedBranch[$bug->branch]))        $bug->branch        = $relatedBranch[$bug->branch] . "(#$bug->branch)";

                if(isset($bugLang->priList[$bug->pri]))               $bug->pri        = $bugLang->priList[$bug->pri];
                if(isset($bugLang->typeList[$bug->type]))             $bug->type       = $bugLang->typeList[$bug->type];
                if(isset($bugLang->severityList[$bug->severity]))     $bug->severity   = $bugLang->severityList[$bug->severity];
                if(isset($bugLang->osList[$bug->os]))                 $bug->os         = $bugLang->osList[$bug->os];
                if(isset($bugLang->browserList[$bug->browser]))       $bug->browser    = $bugLang->browserList[$bug->browser];
                if(isset($bugLang->statusList[$bug->status]))         $bug->status     = $this->processStatus('bug', $bug);
                if(isset($bugLang->confirmedList[$bug->confirmed]))   $bug->confirmed  = $bugLang->confirmedList[$bug->confirmed];
                if(isset($bugLang->resolutionList[$bug->resolution])) $bug->resolution = $bugLang->resolutionList[$bug->resolution];

                if(isset($users[$bug->openedBy]))     $bug->openedBy     = $users[$bug->openedBy];
                if(isset($users[$bug->assignedTo]))   $bug->assignedTo   = $users[$bug->assignedTo];
                if(isset($users[$bug->resolvedBy]))   $bug->resolvedBy   = $users[$bug->resolvedBy];
                if(isset($users[$bug->lastEditedBy])) $bug->lastEditedBy = $users[$bug->lastEditedBy];
                if(isset($users[$bug->closedBy]))     $bug->closedBy     = $users[$bug->closedBy];

                $bug->title = htmlspecialchars_decode($bug->title,ENT_QUOTES);

                if($bug->linkBug)
                {
                    $tmpLinkBugs = array();
                    $linkBugIdList = explode(',', $bug->linkBug);
                    foreach($linkBugIdList as $linkBugID)
                    {
                        $linkBugID = trim($linkBugID);
                        $tmpLinkBugs[] = isset($relatedBugs[$linkBugID]) ? $relatedBugs[$linkBugID] : $linkBugID;
                    }
                    $bug->linkBug = join("; \n", $tmpLinkBugs);
                }

                if($bug->openedBuild)
                {
                    $tmpOpenedBuilds   = array();
                    $tmpResolvedBuilds = array();
                    $buildIdList = explode(',', $bug->openedBuild);
                    foreach($buildIdList as $buildID)
                    {
                        $buildID = trim($buildID);
                        $tmpOpenedBuilds[] = isset($relatedBuilds[$buildID]) ? $relatedBuilds[$buildID] . "(#$buildID)" : $buildID;
                    }
                    $bug->openedBuild = join("\n", $tmpOpenedBuilds);
                    if($this->post->fileType == 'html') $bug->openedBuild = nl2br($bug->openedBuild);
                }

                /* Set related files. */
                $bug->files = '';
                if(isset($relatedFiles[$bug->id]))
                {
                    foreach($relatedFiles[$bug->id] as $file)
                    {
                        $fileURL = common::getSysURL() . $this->file->webPath . $this->file->getRealPathName($file->pathname);
                        $bug->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }

                $bug->mailto = trim(trim($bug->mailto), ',');
                $mailtos     = explode(',', $bug->mailto);
                $bug->mailto = '';
                foreach($mailtos as $mailto)
                {
                    $mailto = trim($mailto);
                    if(isset($users[$mailto])) $bug->mailto .= $users[$mailto] . ',';
                }
                $bug->mailto = rtrim($bug->mailto, ',');

                unset($bug->caseVersion);
                unset($bug->result);
                unset($bug->deleted);
            }

            if(!(in_array('platform', $productsType) or in_array('branch', $productsType))) unset($fields['branch']);// If products's type are normal, unset branch field.
            if($this->config->edition != 'open') list($fields, $bugs) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $bugs);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $bugs);
            $this->post->set('kind', 'bug');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $fileName = $this->lang->bug->common;
        if($executionID)
        {
            $executionName = $this->dao->findById($executionID)->from(TABLE_EXECUTION)->fetch('name');
            $fileName      = $executionName . $this->lang->dash . $fileName;
        }
        else
        {
            $productName = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch('name');
            $browseType  = isset($this->lang->bug->featureBar['browse'][$browseType]) ? $this->lang->bug->featureBar['browse'][$browseType] : zget($this->lang->bug->moreSelects, $browseType, '');

            $fileName = $productName . $this->lang->dash . $browseType . $fileName;
        }

        $this->view->fileName        = $fileName;
        $this->view->allExportFields = $this->config->bug->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Ajax get bug by ID.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function ajaxGetByID($bugID)
    {
        $bug = $this->dao->select('*')->from(TABLE_BUG)->where('id')->eq($bugID)->fetch();
        $realname = $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($bug->assignedTo)->fetch('realname');
        $bug->assignedTo = $realname ? $realname : ($bug->assignedTo == 'closed' ? 'Closed' : $bug->assignedTo);
        return print(json_encode($bug));
    }

    /**
     * Ajax get bug filed options for auto test.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function ajaxGetBugFieldOptions($productID, $executionID = 0)
    {
        $modules  = $this->loadModel('tree')->getOptionMenu($productID, 'bug');
        $builds   = $this->loadModel('build')->getBuildPairs($productID, 'all', '', $executionID, 'execution');
        $type     = $this->lang->bug->typeList;
        $pri      = $this->lang->bug->priList;
        $severity = $this->lang->bug->severityList;

        return print(json_encode(array('modules' => $modules, 'categories' => $type, 'versions' => $builds, 'severities' => $severity, 'priorities' => $pri)));
    }

    /**
     * Drop menu page.
     *
     * @param  int    $productID
     * @param  string $module
     * @param  string $method
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($productID, $module, $method, $extra = '')
    {
        $products = array();
        if(!empty($extra)) $products = $this->product->getProducts($extra, $this->config->CRProduct ? 'all' : 'noclosed', 'program desc, line desc, ');

        $this->view->link      = $this->product->getProductLink($module, $method, $extra);
        $this->view->productID = $productID;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->extra     = $extra;
        $this->view->products  = $products;
        $this->view->projectID = $this->session->project;
        $this->view->programs  = $this->loadModel('program')->getPairs(true);
        $this->view->lines     = $this->product->getLinePairs();
        $this->display();
    }

    /**
     * Ajax get project team members.
     *
     * @param  int    $projectID
     * @param  string $$selectedUser
     * @access public
     * @return string
     */
    public function ajaxGetProjectTeamMembers($projectID, $selectedUser = '')
    {
        $users       = $this->loadModel('user')->getPairs('noclosed');
        $teamMembers = empty($projectID) ? array() : $this->loadModel('project')->getTeamMemberPairs($projectID);
        foreach($teamMembers as $account => $member) $teamMembers[$account] = $users[$account];

        return print(html::select('assignedTo', $teamMembers, $selectedUser, 'class="form-control"'));
    }


    /**
     * Ajax get execution lang.
     *
     * @param  int  $projectID
     * @access public
     * @return string
     */
    public function ajaxGetExecutionLang($projectID)
    {
        $project = $this->loadModel('project')->getByID($projectID);
        if($project->model == 'kanban') return print($this->lang->bug->kanban);
        return print($this->lang->bug->execution);
    }
}
