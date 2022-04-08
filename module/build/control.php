<?php
/**
 * The control file of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: control.php 4992 2013-07-03 07:21:59Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class build extends control
{
    /**
     * Create a build.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function create($executionID = 0, $productID = 0, $projectID = 0)
    {
        /* Load these models. */
        $this->loadModel('execution');
        $this->loadModel('user');

        if(!empty($_POST))
        {
            $executionID = empty($executionID) ? $this->post->execution : $executionID;
            if(empty($executionID)) dao::$errors['execution'] = $this->lang->build->emptyExecution;
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $buildID = $this->build->create($executionID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('build', $buildID, 'opened');

            $this->executeHooks($buildID);

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $buildID));
            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadExecutionBuilds($executionID, $buildID)")); // Code for task #5126.
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('build', 'view', "buildID=$buildID")));
        }

        /* Set menu. */
        $executions = array();
        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($projectID);
            $executions  = $this->execution->getPairs($projectID, 'all', 'stagefilter');
            $executionID = empty($executionID) ? key($executions) : $executionID;
            $this->session->set('project', $projectID);
        }
        elseif($this->app->tab == 'execution')
        {
            $execution  = $this->execution->getByID($executionID);
            $executions = $this->execution->getPairs($execution->project);
            $this->execution->setMenu($executionID);
            $this->session->set('project', $execution->project);
        }
        elseif($this->app->tab == 'qa')
        {
            $execution  = $this->execution->getByID($executionID);
            $projectID  = $execution ? $execution->project : 0;
            $executions = $this->execution->getPairs($projectID);
        }

        $executionList = empty($executions) ? array() : $this->execution->getByIdList(array_keys($executions));
        foreach($executionList as $execution)
        {
            if($execution->lifetime == 'ops') unset($executions[$execution->id]);
        }

        $productGroups = $this->loadModel('product')->getProducts($executionID);
        $productID     = $productID ? $productID : key($productGroups);
        $branchGroups  = $this->loadModel('project')->getBranchesByProject($executionID);
        $branchPairs   = $this->loadModel('branch')->getPairs($productID, 'active');
        $branches      = array();
        $products      = array();

        /* Set branches and products. */
        if(isset($productGroups[$productID]) and $productGroups[$productID]->type != 'normal' and isset($branchGroups[$productID]))
        {
            foreach($branchGroups[$productID] as $branchID => $branch)
            {
                if(isset($branchPairs[$branchID])) $branches[$branchID] = $branchPairs[$branchID];
            }
        }

        foreach($productGroups as $product) $products[$product->id] = $product->name;

        $this->view->title      = $this->lang->build->create;
        $this->view->position[] = $this->lang->build->create;

        $this->view->product       = isset($productGroups[$productID]) ? $productGroups[$productID] : '';
        $this->view->branches      = $branches;
        $this->view->executionID   = $executionID;
        $this->view->products      = $products;
        $this->view->projectID     = $projectID;
        $this->view->executions    = $executions;
        $this->view->lastBuild     = $this->build->getLast($executionID);
        $this->view->productGroups = $productGroups;
        $this->view->users         = $this->user->getPairs('nodeleted|noclosed');
        $this->display();
    }

    /**
     * Edit a build.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function edit($buildID)
    {
        if(!empty($_POST))
        {
            $changes = $this->build->update($buildID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $files = $this->loadModel('file')->saveUpload('build', $buildID);

            if($changes or $files)
            {
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->loadModel('action')->create('build', $buildID, 'Edited', $fileAction);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($buildID);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "buildID=$buildID")));
        }

        $this->loadModel('execution');
        $this->loadModel('product');
        $build = $this->build->getById((int)$buildID);

        /* Set menu. */
        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($build->project);
        }
        elseif($this->app->tab == 'execution')
        {
            $this->execution->setMenu($build->execution);
        }

        /* Get stories and bugs. */
        $orderBy = 'status_asc, stage_asc, id_desc';

        /* Assign. */
        $execution = $this->execution->getByID($build->execution);
        if(empty($execution))
        {
            $execution = new stdclass();
            $execution->name = '';
        }

        $executions = $this->product->getExecutionPairsByProduct($build->product, $build->branch, 'id_desc', $this->session->project, 'stagefilter');
        if(!isset($executions[$build->execution])) $executions[$build->execution] = $execution->name;

        $productGroups = $this->product->getProducts($build->execution);

        if(!isset($productGroups[$build->product]))
        {
            $product = $this->product->getById($build->product);
            $product->branch = $build->branch;
            $productGroups[$build->product] = $product;
        }

        /* Display status of branch. */
        $branches = $this->loadModel('branch')->getList($build->product, $build->execution, 'all');
        $branchTagOption = array();
        foreach($branches as $branchInfo)
        {
            $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
        }

        foreach($productGroups as $product) $products[$product->id] = $product->name;

        $this->view->title           = $execution->name . $this->lang->colon . $this->lang->build->edit;
        $this->view->position[]      = html::a($this->createLink('execution', 'task', "executionID=$build->execution"), $execution->name);
        $this->view->position[]      = $this->lang->build->edit;
        $this->view->product         = isset($productGroups[$build->product]) ? $productGroups[$build->product] : '';
        $this->view->branchTagOption = $branchTagOption;
        $this->view->executions      = $executions;
        $this->view->orderBy         = $orderBy;

        $this->view->productGroups = $productGroups;
        $this->view->products      = $products;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter', $build->builder);
        $this->view->build         = $build;
        $this->view->testtaskID    = $this->dao->select('id')->from(TABLE_TESTTASK)->where('build')->eq($build->id)->andWhere('deleted')->eq(0)->fetch('id');
        $this->display();
    }

    /**
     * View a build.
     *
     * @param  int    $buildID
     * @param  string $type
     * @param  string $link
     * @param  string $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function view($buildID, $type = 'story', $link = 'false', $param = '', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        $buildID = (int)$buildID;
        $build   = $this->build->getByID($buildID, true);
        if(!$build)
        {
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'code' => 404, 'message' => '404 Not found'));
            die(js::error($this->lang->notFound) . js::locate($this->createLink('execution', 'all')));
        }
        $this->session->project = $build->project;

        $this->loadModel('story');
        $this->loadModel('bug');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;

        /* Get product and bugs. */
        $product = $this->loadModel('product')->getById($build->product);
        if($product->type != 'normal') $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);

        $bugPager = new pager($type == 'bug' ? $recTotal : 0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($build->bugs)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'bug')->orderBy($orderBy)->fi()
            ->page($bugPager)
            ->fetchAll();

        /* Get stories and stages. */
        $storyPager = new pager($type == 'story' ? $recTotal : 0, $recPerPage, $type == 'story' ? $pageID : 1);
        $stories = $this->dao->select('*')->from(TABLE_STORY)->where('id')->in($build->stories)->andWhere('deleted')->eq(0)
            ->beginIF($type == 'story')->orderBy($orderBy)->fi()
            ->page($storyPager)
            ->fetchAll('id');

        $stages = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in($build->stories)->andWhere('branch')->eq($build->branch)->fetchPairs('story', 'stage');
        foreach($stages as $storyID => $stage) $stories[$storyID]->stage = $stage;

        /* Set menu. */
        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($build->project);
        }
        elseif($this->app->tab == 'execution')
        {
            $this->loadModel('execution')->setMenu($build->execution);
        }

        $executions = $this->loadModel('execution')->getPairs($this->session->project, 'all', 'empty');

        $this->view->title      = "BUILD #$build->id $build->name" . (isset($executions[$build->execution]) ? " - " . $executions[$build->execution] : '');
        $this->view->stories    = $stories;
        $this->view->storyPager = $storyPager;

        $generatedBugPager = new pager($type == 'generatedBug' ? $recTotal : 0, $recPerPage, $type == 'generatedBug' ? $pageID : 1);
        $this->view->generatedBugs     = $this->bug->getExecutionBugs($build->execution, $build->product, $build->id, $type, $param, $type == 'generatedBug' ? $orderBy : 'status_desc,id_desc', '', $generatedBugPager);
        $this->view->generatedBugPager = $generatedBugPager;

        $this->executeHooks($buildID);

        /* Assign. */
        $this->view->canBeChanged = common::canBeChanged('build', $build); // Determines whether an object is editable.
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->build        = $build;
        $this->view->buildPairs   = $this->build->getBuildPairs(0, 0, 'noempty,notrunk', $build->execution, 'execution');
        $this->view->actions      = $this->loadModel('action')->getList('build', $buildID);
        $this->view->link         = $link;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;
        $this->view->bugs         = $bugs;
        $this->view->type         = $type;
        $this->view->bugPager     = $bugPager;
        $this->view->branchName   = $build->productType == 'normal' ? '' : $this->loadModel('branch')->getById($build->branch);

        if($this->app->getViewType() == 'json')
        {
            unset($this->view->storyPager);
            unset($this->view->generatedBugPager);
            unset($this->view->bugPager);
        }

        $this->display();
    }

    /**
     * Delete a build.
     *
     * @param  int    $buildID
     * @param  string $confirm  yes|noe
     * @access public
     * @return void
     */
    public function delete($buildID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->build->confirmDelete, $this->createLink('build', 'delete', "buildID=$buildID&confirm=yes")));
        }
        else
        {
            $build = $this->build->getById($buildID);
            $this->build->delete(TABLE_BUILD, $buildID);

            $this->executeHooks($buildID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                return $this->send($response);
            }

            $link = $this->app->tab == 'project' ? $this->createLink('project', 'build', "projectID=$build->project") : $this->createLink('execution', 'build', "executionID=$build->execution");
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
            die(js::locate($link, 'parent'));
        }
    }

    /**
     * AJAX: get builds of a product in html select.
     *
     * @param  int        $productID
     * @param  string     $varName      the name of the select object to create
     * @param  string     $build        build to selected
     * @param  string|int $branch
     * @param  int        $index        the index of batch create bug.
     * @param  string     $type         get all builds or some builds belong to normal releases and executions are not done.
     * @access public
     * @return string
     */
    public function ajaxGetProductBuilds($productID, $varName, $build = '', $branch = 'all', $index = 0, $type = 'normal')
    {
        $isJsonView = $this->app->getViewType() == 'json';
        if($varName == 'openedBuild' )
        {
            $params = ($type == 'all') ? 'noempty,withbranch' : 'noempty, noterminate, nodone, withbranch';
            $builds = $this->build->getBuildPairs($productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . '[]', $builds, $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'openedBuilds' )
        {
            $builds = $this->build->getBuildPairs($productID, $branch, 'noempty');
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . "[$index][]", $builds, $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'resolvedBuild')
        {
            $params = ($type == 'all') ? 'withbranch' : 'noterminate, nodone, withbranch';
            $builds = $this->build->getBuildPairs($productID, $branch, $params);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName, $builds, $build, "class='form-control'"));
        }
    }

    /**
     * AJAX: get builds of a project in html select.
     *
     * @param  int        $projectID
     * @param  string     $varName      the name of the select object to create
     * @param  string     $build        build to selected
     * @param  string|int $branch
     * @param  int        $index        the index of batch create bug.
     * @param  bool       $needCreate   if need to append the link of create build
     * @param  string     $type         get all builds or some builds belong to normal releases and executions are not done.
     * @access public
     * @return string
     */
    public function ajaxGetProjectBuilds($projectID, $productID, $varName, $build = '', $branch = 'all', $index = 0, $needCreate = false, $type = 'normal')
    {
        $isJsonView = $this->app->getViewType() == 'json';
        if($varName == 'openedBuild')
        {
            if(empty($projectID)) $this->ajaxGetProductBuilds($productID, $varName, $build, $branch, $index, $type);

            $params = ($type == 'all') ? 'noempty, withbranch' : 'noempty, noterminate, nodone, withbranch';
            $builds = $this->build->getBuildPairs($productID, $branch, $params, $projectID, 'project', $build);
            if($isJsonView)  die(json_encode($builds));
            die(html::select($varName . '[]', $builds , '', 'size=4 class=form-control multiple'));
        }
        if($varName == 'resolvedBuild')
        {
            if(empty($projectID)) $this->ajaxGetProductBuilds($productID, $varName, $build, $branch, $index, $type);

            $params = ($type == 'all') ? 'withbranch' : 'noterminate, nodone, withbranch';
            $builds = $this->build->getBuildPairs($productID, $branch, $params, $projectID, 'project', $build);
            if($isJsonView)  die(json_encode($builds));
            die(html::select($varName, $builds, $build, "class='form-control'"));
        }
    }

    /**
     * AJAX: get builds of an execution in html select.
     *
     * @param  int        $executionID
     * @param  string     $varName      the name of the select object to create
     * @param  string     $build        build to selected
     * @param  string|int $branch
     * @param  int        $index        the index of batch create bug.
     * @param  bool       $needCreate   if need to append the link of create build
     * @param  string     $type         get all builds or some builds belong to normal releases and executions are not done.
     * @access public
     * @return string
     */
    public function ajaxGetExecutionBuilds($executionID, $productID, $varName, $build = '', $branch = 'all', $index = 0, $needCreate = false, $type = 'normal')
    {
        $isJsonView = $this->app->getViewType() == 'json';
        if($varName == 'openedBuild')
        {
            if(empty($executionID)) $this->ajaxGetProductBuilds($productID, $varName, $build, $branch, $index, $type);

            $params = ($type == 'all') ? 'noempty' : 'noempty, noterminate, nodone';
            $builds = $this->build->getBuildPairs($productID, $branch, $params, $executionID, 'execution', $build);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . '[]', $builds , '', 'size=4 class=form-control multiple'));
        }
        if($varName == 'openedBuilds')
        {
            if(empty($executionID)) $this->ajaxGetProductBuilds($productID, $varName, $build, $branch, $index, $type);

            $builds = $this->build->getBuildPairs($productID, $branch, 'noempty', $executionID, 'execution');
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName . "[$index][]", $builds , $build, 'size=4 class=form-control multiple'));
        }
        if($varName == 'resolvedBuild')
        {
            if(empty($executionID)) $this->ajaxGetProductBuilds($productID, $varName, $build, $branch, $index, $type);

            $params = ($type == 'all') ? '' : 'noterminate, nodone';
            $builds = $this->build->getBuildPairs($productID, $branch, $params, $executionID, 'execution', $build);
            if($isJsonView) die(json_encode($builds));
            else die(html::select($varName, $builds, $build, "class='form-control'"));
        }
        if($varName == 'testTaskBuild')
        {
            $builds = $this->build->getBuildPairs($productID, $branch, 'noempty,notrunk', $executionID, 'execution');
            if($isJsonView) die(json_encode($builds));

            if(empty($builds))
            {
                $projectID = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch('project');

                $html  = html::a($this->createLink('build', 'create', "executionID=$executionID&productID=$productID&projectID=$projectID", '', $onlybody = true), $this->lang->build->create, '', "data-toggle='modal' data-type='iframe'");
                $html .= '&nbsp; ';
                $html .= html::a("javascript:loadExecutionBuilds($executionID)", $this->lang->refresh);
                die($html);
            }
            die(html::select('build', array('') + $builds, $build, "class='form-control'"));
        }
        if($varName == 'dropdownList')
        {
            $builds = $this->build->getBuildPairs($productID, $branch, 'noempty,notrunk', $executionID, 'execution');
            if($isJsonView) die(json_encode($builds));

            $list  = "<div class='list-group'>";
            foreach($builds as $buildID => $buildName) $list .= html::a(inlink('view', "buildID={$buildID}"), $buildName);
            $list .= '</div>';

            die($list);
        }
    }

    /**
     * Link stories.
     *
     * @param  int    $buildID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkStory($buildID = 0, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->build->linkStory($buildID);
            die(js::locate(inlink('view', "buildID=$buildID&type=story"), 'parent'));
        }

        $this->session->set('storyList', inlink('view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), $this->app->tab);

        $build   = $this->build->getById($buildID);
        $product = $this->loadModel('product')->getById($build->product);

        $this->loadModel('execution')->setMenu($build->execution);
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        unset($this->config->product->search['fields']['product']);
        unset($this->config->product->search['fields']['project']);
        $this->config->product->search['actionURL'] = $this->createLink('build', 'view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=bySearch&queryID=myQueryID"));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['plan']['values']   = $this->loadModel('productplan')->getPairsForStory($build->product, $build->branch, 'skipParent');
        $this->config->product->search['params']['module']['values'] = $this->tree->getOptionMenu($build->product, 'story', 0, $build->branch);
        $this->config->product->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->story->statusList);

        if($product->type == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $branchPairs = $this->loadModel('branch')->getPairs($build->product, 'noempty');
            $branches    = array('' => '') + array(BRANCH_MAIN => $this->lang->branch->main);
            if($build->branch) $branches += array($build->branch => $branchPairs[$build->branch]);

            $this->config->product->search['fields']['branch']           = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($build->product, $build->branch, $queryID, 'id', $build->execution, 'story', $build->stories, $pager);
        }
        else
        {
            $allStories = $this->story->getExecutionStories($build->execution, $build->product, 0, 't1.`order`_desc', 'byBranch', $build->branch, 'story', $build->stories, $pager);
        }

        $this->view->allStories   = $allStories;
        $this->view->build        = $build;
        $this->view->buildStories = empty($build->stories) ? array() : $this->story->getByList($build->stories);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->view->pager        = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $storyID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function unlinkStory($buildID, $storyID)
    {
        $this->build->unlinkStory($buildID, $storyID);

        /* if ajax request, send result. */
        if($this->server->ajax)
        {
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            else
            {
                $response['result']  = 'success';
                $response['message'] = '';
            }
            return $this->send($response);
        }
        die(js::reload('parent'));
    }

    /**
     * Batch unlink story.
     *
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function batchUnlinkStory($buildID)
    {
        $this->build->batchUnlinkStory($buildID);
        die(js::locate($this->createLink('build', 'view', "buildID=$buildID&type=story"), 'parent'));
    }

    /**
     * Link bugs.
     *
     * @param  int    $buildID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkBug($buildID = 0, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['bugs']))
        {
            $this->build->linkBug($buildID);
            die(js::locate(inlink('view', "buildID=$buildID&type=bug"), 'parent'));
        }

        $this->session->set('bugList', inlink('view', "buildID=$buildID&type=bug&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'qa');
        /* Set menu. */
        $build   = $this->build->getByID($buildID);
        $product = $this->loadModel('product')->getByID($build->product);
        $this->loadModel('execution')->setMenu($build->execution);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Build the search form. */
        $this->loadModel('bug');
        $this->config->bug->search['actionURL'] = $this->createLink('build', 'view', "buildID=$buildID&type=bug&link=true&param=" . helper::safe64Encode("&browseType=bySearch&queryID=myQueryID"));
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['style']     = 'simple';
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getPairsForStory($build->product, $build->branch, 'skipParent');
        $this->config->bug->search['params']['module']['values']        = $this->loadModel('tree')->getOptionMenu($build->product, 'bug', 0, $build->branch);
        $this->config->bug->search['params']['execution']['values']     = $this->loadModel('product')->getExecutionPairsByProduct($build->product, $build->branch, 'id_desc', $this->session->project);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->build->getBuildPairs($build->product, $branch = 'all', $params = '');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];

        unset($this->config->bug->search['fields']['product']);
        unset($this->config->bug->search['params']['product']);
        unset($this->config->bug->search['fields']['project']);
        unset($this->config->bug->search['params']['project']);
        if($product->type == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $branchName = $this->loadModel('branch')->getById($build->branch);
            $branches   = array('' => '', BRANCH_MAIN => $this->lang->branch->main, $build->branch => $branchName);

            $this->config->bug->search['fields']['branch']           = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        if($browseType == 'bySearch')
        {
            $allBugs = $this->bug->getBySearch($build->product, $build->branch, $queryID, 'id_desc', $build->bugs, $pager, $build->project);
        }
        else
        {
            $allBugs = $this->bug->getExecutionBugs($build->execution, 0, $buildID, 'noclosed', 0, 'status_desc,id_desc', $build->bugs, $pager);
        }

        $this->view->allBugs    = $allBugs;
        $this->view->build      = $build;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType = $browseType;
        $this->view->param      = $param;
        $this->view->pager      = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $buildID
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function unlinkBug($buildID, $bugID)
    {
        $this->build->unlinkBug($buildID, $bugID);

        /* if ajax request, send result. */
        if($this->server->ajax)
        {
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            else
            {
                $response['result']  = 'success';
                $response['message'] = '';
            }
            return $this->send($response);
        }
        die(js::reload('parent'));
    }

    /**
     * Batch unlink story.
     *
     * @param  int $buildID
     * @access public
     * @return void
     */
    public function batchUnlinkBug($buildID)
    {
        $this->build->batchUnlinkBug($buildID);
        die(js::locate($this->createLink('build', 'view', "buildID=$buildID&type=bug"), 'parent'));
    }
}
