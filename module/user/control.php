<?php
/**
 * The control file of user module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     user
 * @version     $Id: control.php 5005 2013-07-03 08:39:11Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class user extends control
{
    public $referer;

    /**
     * Construct
     *
     * @access public
     * @return void
     */
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->loadModel('company')->setMenu();
        $this->loadModel('dept');
        $this->loadModel('todo');
        $this->loadModel('execution');
        $this->app->loadLang('project');
        $this->app->loadModuleConfig($this->moduleName);//Finish task #5118.(Fix bug #2271)
    }

    /**
     * View a user.
     *
     * @param  string $userID
     * @access public
     * @return void
     */
    public function view($userID)
    {
        $userID = (int)$userID;
        $this->locate($this->createLink('user', 'todo', "userID=$userID&type=all"));
    }

    /**
     * Todos of a user.
     *
     * @param  string $userID
     * @param  string $type         the todo type, today|lastweek|thisweek|all|undone, or a date.
     * @param  string $status
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function todo($userID, $type = 'today', $status = 'all', $orderBy = 'date,status,begin', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $userID = (int)$userID;
        $user   = $this->user->getById($userID, 'id');
        if(empty($user)) return print(js::error($this->lang->notFound) . js::locate($this->createLink('my', 'team')));
        if($user->deleted == 1) return print(js::error($this->lang->user->noticeHasDeleted) . js::locate('back'));

        /* Set thie url to session. */
        $uri = $this->app->getURI(true);
        $this->session->set('todoList', $uri, 'my');
        $this->session->set('bugList',  $uri, 'qa');
        $this->session->set('taskList', $uri, 'execution');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort = common::appendOrder($orderBy);

        /* Get user, totos. */
        $account = $user->account;
        $todos   = $this->todo->getList($type, $account, $status, 0, $pager, $sort);
        $date    = (int)$type == 0 ? helper::today() : $type;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');
        if(!isset($users[$userID])) return print(js::error($this->lang->user->error->noAccess) . js::locate('back'));

        /* set menus. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->todo;
        $this->view->position[] = $this->lang->user->todo;
        $this->view->tabID      = 'todo';
        $this->view->date       = $date;
        $this->view->todos      = $todos;
        $this->view->user       = $user;
        $this->view->type       = $type;
        $this->view->status     = $status;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * Story of a user.
     *
     * @param  int    $userID
     * @param  string $storyType
     * @param  string $type
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function story($userID, $storyType = 'story', $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session. */
        $this->session->set('storyList', $this->app->getURI(true), 'product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Modify story title. */
        $this->loadModel('story');
        if($storyType == 'requirement') $this->lang->story->title  = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);

        /* Assign. */
        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->story;
        $this->view->position[] = $this->lang->user->story;
        $this->view->stories    = $this->story->getUserStories($account, $type, $orderBy, $pager, $storyType);
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->storyType  = $storyType;
        $this->view->orderBy    = $orderBy;
        $this->view->type       = $type;
        $this->view->user       = $user;
        $this->view->pager      = $pager;
        $this->view->userList   = $this->user->setUserList($users, $userID);

        $this->display();
    }

    /**
     * Tasks of a user.
     *
     * @param  int    $userID
     * @param  string $type
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function task($userID, $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save the session. */
        $this->session->set('taskList', $this->app->getURI(true), 'execution');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Set the menu. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        /* Assign. */
        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->task;
        $this->view->position[] = $this->lang->user->task;
        $this->view->tabID      = 'task';
        $this->view->tasks      = $this->loadModel('task')->getUserTasks($account, $type, 0, $pager, $orderBy);
        $this->view->type       = $type;
        $this->view->orderBy    = $orderBy;
        $this->view->user       = $user;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * User bugs.
     *
     * @param  int    $userID
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bug($userID, $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save the session. */
        $this->session->set('bugList', $this->app->getURI(true), 'qa');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Set menu. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        /* Load the lang of bug module. */
        $this->app->loadLang('bug');

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->bug;
        $this->view->position[] = $this->lang->user->bug;
        $this->view->tabID      = 'bug';
        $this->view->bugs       = $this->loadModel('bug')->getUserBugs($account, $type, $orderBy, 0, $pager);
        $this->view->type       = $type;
        $this->view->user       = $user;
        $this->view->orderBy    = $orderBy;
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * User's testtask
     *
     * @param  int    $userID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testtask($userID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Set menu. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        /* Save session. */
        $this->session->set('testtaskList', $this->app->getURI(true), 'qa');
        $this->session->set('buildList', $this->app->getURI(true), 'execution');

        $this->app->loadLang('testcase');

        /* Append id for secend sort. */
        $sort = common::appendOrder($orderBy);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->testTask;
        $this->view->position[] = $this->lang->user->testTask;
        $this->view->tasks      = $this->loadModel('testtask')->getByUser($account, $pager, $sort);
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->user       = $user;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->display();
    }

    /**
     * User's test case.
     *
     * @param  int    $userID
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function testcase($userID, $type = 'case2Him', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Save session, load lang. */
        $this->session->set('caseList', $this->app->getURI(true), 'qa');
        $this->app->loadLang('testcase');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Append id for secend sort. */
        $sort = common::appendOrder($orderBy);

        $cases = array();
        if($type == 'case2Him')
        {
            $cases = $this->loadModel('testcase')->getByAssignedTo($account, $sort, $pager);
        }
        elseif($type == 'caseByHim')
        {
            $cases = $this->loadModel('testcase')->getByOpenedBy($account, $sort, $pager);
        }
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', $type == 'case2Him' ? false : true);

        /* Assign. */
        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->testCase;
        $this->view->position[] = $this->lang->user->testCase;
        $this->view->user       = $user;
        $this->view->cases      = $cases;
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->tabID      = 'test';
        $this->view->type       = $type;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->userList   = $this->user->setUserList($users, $userID);

        $this->display();
    }

    /**
     * User executions.
     *
     * @param  int    $userID
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function execution($userID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $uri = $this->app->getURI(true);
        $this->session->set('executionList', $uri, 'execution');

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Set the menus. */
        $this->loadModel('project');
        $this->view->userList = $this->user->setUserList($users, $userID);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->execution;
        $this->view->position[] = $this->lang->user->execution;
        $this->view->tabID      = 'project';
        $this->view->executions = $this->user->getObjects($account, 'execution', 'all', $orderBy, $pager);
        $this->view->user       = $user;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * User issues.
     *
     * @param  int    $userID
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function issue($userID, $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $uri = $this->app->getURI(true);
        $this->session->set('issueList', $uri, 'project');

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Set the menus. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->issue;
        $this->view->position[] = $this->lang->user->issue;
        $this->view->issues     = $this->loadModel('issue')->getUserIssues($type, $account, $orderBy, $pager);
        $this->view->user       = $user;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->type       = $type;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * User risks.
     *
     * @param  int    $userID
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function risk($userID, $type = 'assignedTo', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $uri = $this->app->getURI(true);
        $this->session->set('riskList', $uri, 'project');

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Set the menus. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->risk;
        $this->view->position[] = $this->lang->user->risk;
        $this->view->risks      = $this->loadModel('risk')->getUserRisks($type, $account, $orderBy, $pager);
        $this->view->user       = $user;
        $this->view->type       = $type;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * The profile of a user.
     *
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function profile($userID = '')
    {
        if(empty($userID)) $userID = $this->app->user->id;

        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        $this->view->title        = "USER #$user->id $user->account/" . $this->lang->user->profile;
        $this->view->position[]   = $this->lang->user->common;
        $this->view->position[]   = $this->lang->user->profile;
        $this->view->user         = $user;
        $this->view->groups       = $this->loadModel('group')->getByAccount($account);
        $this->view->deptPath     = $this->dept->getParents($user->dept);
        $this->view->personalData = $this->user->getPersonalData($user->account);
        $this->view->userList     = $this->user->setUserList($users, $userID);

        $this->display();
    }

    /**
     * Set the rerferer.
     *
     * @param  string   $referer
     * @access public
     * @return void
     */
    public function setReferer($referer = '')
    {
        $this->referer = $this->server->http_referer ? $this->server->http_referer: '';
        if(!empty($referer)) $this->referer = helper::safe64Decode($referer);

        /* Build zentao link regular. */
        $webRoot = $this->config->webRoot;
        $linkReg = $webRoot . 'index.php?' . $this->config->moduleVar . '=\w+&' . $this->config->methodVar . '=\w+';
        if($this->config->requestType == 'PATH_INFO') $linkReg = $webRoot . '\w+' . $this->config->requestFix . '\w+';
        $linkReg = str_replace(array('/', '.', '?', '-'), array('\/', '\.', '\?', '\-'), $linkReg);

        /* Check zentao link by regular. */
        $this->referer = preg_match('/^' . $linkReg . '/', $this->referer) ? $this->referer : $webRoot;
    }

    /**
     * Create a suer.
     *
     * @param  int    $deptID
     * @access public
     * @return void
     */
    public function create($deptID = 0)
    {
        $this->lang->user->menu      = $this->lang->company->menu;
        $this->lang->user->menuOrder = $this->lang->company->menuOrder;

        if(!empty($_POST))
        {
            if(strtolower($_POST['account']) == 'guest')
            {
                return $this->send(array('result' => 'fail', 'message' => str_replace('ID ', '', sprintf($this->lang->user->error->reserved, $_POST['account']))));
            }

            $userID = $this->user->create();
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $userID));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('company', 'browse')));
        }

        $groups = $this->dao->select('id, name, role')
            ->from(TABLE_GROUP)
            ->fetchAll();
        $groupList = array('' => '');
        $roleGroup = array();
        foreach($groups as $group)
        {
            $groupList[$group->id] = $group->name;
            if($group->role) $roleGroup[$group->role] = $group->id;
        }

        $title      = $this->lang->company->common . $this->lang->colon . $this->lang->user->create;
        $position[] = $this->lang->user->create;
        $this->view->title     = $title;
        $this->view->position  = $position;
        $this->view->depts     = $this->dept->getOptionMenu();
        $this->view->groupList = $groupList;
        $this->view->roleGroup = $roleGroup;
        $this->view->deptID    = $deptID;
        $this->view->rand      = $this->user->updateSessionRandom();
        $this->view->companies = $this->loadModel('company')->getOutsideCompanies();

        $this->display();
    }

    /**
     * Batch create users.
     *
     * @param  int    $deptID
     * @access public
     * @return void
     */
    public function batchCreate($deptID = 0)
    {
        $groups = $this->dao->select('id, name, role')
            ->from(TABLE_GROUP)
            ->fetchAll();
        $groupList = array('' => '');
        $roleGroup = array();
        foreach($groups as $group)
        {
            $groupList[$group->id] = $group->name;
            if($group->role) $roleGroup[$group->role] = $group->id;
        }

        $this->lang->user->menu      = $this->lang->company->menu;
        $this->lang->user->menuOrder = $this->lang->company->menuOrder;

        if(!empty($_POST))
        {
            $userIDList = $this->user->batchCreate();

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'idList' => $userIDList));
            return print(js::locate($this->createLink('company', 'browse'), 'parent'));
        }

        /* Set custom. */
        foreach(explode(',', $this->config->user->availableBatchCreateFields) as $field)
        {
            if(!isset($this->lang->user->contactFieldList[$field]) or strpos($this->config->user->contactField, $field) !== false) $customFields[$field] = $this->lang->user->$field;
        }
        foreach(explode(',', $this->config->user->custom->batchCreateFields) as $field)
        {
            if(!isset($this->lang->user->contactFieldList[$field]) or strpos($this->config->user->contactField, $field) !== false) $showFields[$field] = $field;
        }
        $this->view->customFields = $customFields;
        $this->view->showFields   = join(',', $showFields);

        $title      = $this->lang->company->common . $this->lang->colon . $this->lang->user->batchCreate;
        $position[] = $this->lang->user->batchCreate;
        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->depts      = $this->dept->getOptionMenu();
        $this->view->deptID     = $deptID;
        $this->view->groupList  = $groupList;
        $this->view->roleGroup  = $roleGroup;
        $this->view->rand       = $this->user->updateSessionRandom();
        $this->view->visionList = $this->user->getVisionList();

        $this->display();
    }

    /**
     * Edit a user.
     *
     * @param  string|int $userID   the int user id or account
     * @access public
     * @return void
     */
    public function edit($userID)
    {
        $this->lang->user->menu      = $this->lang->company->menu;
        $this->lang->user->menuOrder = $this->lang->company->menuOrder;
        if(!empty($_POST))
        {
            $this->user->update($userID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $link = $this->session->userList ? $this->session->userList : $this->createLink('company', 'browse');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $link));
        }

        $user       = $this->user->getById($userID, 'id');
        $userGroups = $this->loadModel('group')->getByAccount($user->account);

        $title      = $this->lang->company->common . $this->lang->colon . $this->lang->user->edit;
        $position[] = $this->lang->user->edit;
        $this->view->title      = $title;
        $this->view->position   = $position;
        $this->view->user       = $user;
        $this->view->depts      = $this->dept->getOptionMenu();
        $this->view->userGroups = implode(',', array_keys($userGroups));
        $this->view->companies  = $this->loadModel('company')->getOutsideCompanies();
        $this->view->groups     = $this->dao->select('id, name')->from(TABLE_GROUP)->where('project')->eq(0)->fetchPairs('id', 'name');
        $this->view->rand       = $this->user->updateSessionRandom();
        $this->view->visionList = $this->user->getVisionList();

        $this->display();
    }

    /**
     * Batch edit user.
     *
     * @param  int    $deptID
     * @access public
     * @return void
     */
    public function batchEdit($deptID = 0)
    {
        if(isset($_POST['users']))
        {
            $this->view->users = $this->dao->select('*')->from(TABLE_USER)->where('account')->in($this->post->users)->orderBy('id')->fetchAll('id');
        }
        elseif($_POST)
        {
            if($this->post->account) $this->user->batchEdit();
            return print(js::locate($this->session->userList ? $this->session->userList : $this->createLink('company', 'browse', "deptID=$deptID"), 'parent'));
        }
        else
        {
            return print(js::locate($this->session->userList ? $this->session->userList : $this->createLink('company', 'browse', "deptID=$deptID"), 'parent'));
        }

        $this->lang->user->menu      = $this->lang->company->menu;
        $this->lang->user->menuOrder = $this->lang->company->menuOrder;

        /* Set custom. */
        foreach(explode(',', $this->config->user->availableBatchEditFields) as $field)
        {
            if(!isset($this->lang->user->contactFieldList[$field]) or strpos($this->config->user->contactField, $field) !== false) $customFields[$field] = $this->lang->user->$field;
        }
        foreach(explode(',', $this->config->user->custom->batchEditFields) as $field)
        {
            if(!isset($this->lang->user->contactFieldList[$field]) or strpos($this->config->user->contactField, $field) !== false) $showFields[$field] = $field;
        }
        $this->view->customFields = $customFields;
        $this->view->showFields   = join(',', $showFields);

        $this->view->title      = $this->lang->company->common . $this->lang->colon . $this->lang->user->batchEdit;
        $this->view->position[] = $this->lang->user->batchEdit;
        $this->view->depts      = $this->dept->getOptionMenu();
        $this->view->rand       = $this->user->updateSessionRandom();
        $this->view->visionList = $this->user->getVisionList();

        $this->display();
    }

    /**
     * Delete a user.
     *
     * @param  int    $userID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($userID)
    {
        $user = $this->user->getByID($userID, 'id');
        if($this->app->user->admin and $this->app->user->account == $user->account) return;
        if($_POST)
        {
            if($this->post->verifyPassword != md5($this->app->user->password . $this->session->rand)) return print(js::alert($this->lang->user->error->verifyPassword));
            $this->user->delete(TABLE_USER, $userID);
            if(!dao::isError())
            {
                $this->loadModel('mail');
                if($this->config->mail->mta == 'sendcloud' and !empty($user->email)) $this->mail->syncSendCloud('delete', $user->email);
            }

            /* if ajax request, send result. */
            if($this->server->ajax or $this->viewType == 'json')
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
            return print(js::locate($this->session->userList, 'parent.parent'));
        }

        $this->view->rand = $this->user->updateSessionRandom();
        $this->view->user = $user;
        $this->display();
    }

    /**
     * Unlock a user.
     *
     * @param  int    $userID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unlock($userID, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->user->confirmUnlock, $this->createLink('user', 'unlock', "userID=$userID&confirm=yes")));

        $user = $this->user->getById($userID, 'id');
        $this->user->cleanLocked($user->account);
        return print(js::locate($this->session->userList ? $this->session->userList : $this->createLink('company', 'browse'), 'parent'));
    }

    /**
     * Unbind Ranzhi
     *
     * @param  string $userID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unbind($userID, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->user->confirmUnbind, $this->createLink('user', 'unbind', "userID=$userID&confirm=yes")));

        $user = $this->user->getById($userID, 'id');
        $this->user->unbind($user->account);
        return print(js::locate($this->session->userList ? $this->session->userList : $this->createLink('company', 'browse'), 'parent'));
    }


    /**
     * User login, identify him and authorize him.
     *
     * @param string $referer
     * @param string $from
     *
     * @access public
     * @return void
     */
    public function login($referer = '', $from = '')
    {
        /* Check if you can operating on the folder. */
        $canModifyDIR = true;
        if($this->user->checkTmp() === false)
        {
            $canModifyDIR = false;
            $floderPath   = $this->app->tmpRoot;
        }
        elseif(!is_dir($this->app->dataRoot) or substr(base_convert(@fileperms($this->app->dataRoot),10,8),-4) != '0777')
        {
            $canModifyDIR = false;
            $floderPath   = $this->app->dataRoot;
        }

        if(!$canModifyDIR)
        {
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                return print(sprintf($this->lang->user->mkdirWin, $floderPath, $floderPath));
            }
            else
            {
                return print(sprintf($this->lang->user->mkdirLinux, $floderPath, $floderPath, $floderPath, $floderPath));
            }
        }

        $this->setReferer($referer);

        $loginLink = $this->createLink('user', 'login');
        $denyLink  = $this->createLink('user', 'deny');

        /* Reload lang by lang of get when viewType is json. */
        if($this->app->getViewType() == 'json' and $this->get->lang and $this->get->lang != $this->app->getClientLang())
        {
            $this->app->setClientLang($this->get->lang);
            $this->app->loadLang('user');
        }

        /* If user is logon, back to the rerferer. */
        if($this->user->isLogon())
        {
            if($this->app->getViewType() == 'json')
            {
                $data = $this->user->getDataInJSON($this->app->user);
                return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
            }

            $response['result']  = 'success';
            if(strpos($this->referer, $loginLink) === false and
               strpos($this->referer, $denyLink)  === false and
               strpos($this->referer, 'block')  === false and $this->referer
            )
            {
                $response['locate']  = $this->referer;
                return $this->send($response);
            }
            else
            {
                $response['locate']  = $this->config->webRoot;
                return $this->send($response);
            }
        }

        /* Passed account and password by post or get. */
        if(!empty($_POST) or (isset($_GET['account']) and isset($_GET['password'])))
        {
            $account  = '';
            $password = '';
            if($this->post->account)  $account  = $this->post->account;
            if($this->get->account)   $account  = $this->get->account;
            if($this->post->password) $password = $this->post->password;
            if($this->get->password)  $password = $this->get->password;

            $account = trim($account);
            if($this->user->checkLocked($account))
            {
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->lang->user->loginLocked, $this->config->user->lockMinutes);
                if($this->app->getViewType() == 'json') return print(helper::removeUTF8Bom(json_encode(array('status' => 'failed', 'reason' => $failReason))));
                return $this->send($response);
            }

            if((!empty($this->config->safe->loginCaptcha) and strtolower($this->post->captcha) != strtolower($this->session->captcha) and $this->app->getViewType() != 'json'))
            {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->user->errorCaptcha;
                return $this->send($response);
            }

            $user = $this->user->identify($account, $password);

            if($user)
            {
                /* Set user group, rights, view and aword login score. */
                $user = $this->user->login($user);

                /* Go to the referer. */
                if($this->post->referer and strpos($this->post->referer, $loginLink) === false and strpos($this->post->referer, $denyLink) === false and strpos($this->post->referer, 'block') === false)
                {
                    if($this->app->getViewType() == 'json')
                    {
                        $data = $this->user->getDataInJSON($user);
                        return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
                    }

                    /* Get the module and method of the referer. */
                    if($this->config->requestType == 'PATH_INFO')
                    {
                        $path = substr($this->post->referer, strrpos($this->post->referer, '/') + 1);
                        $path = rtrim($path, '.html');
                        if(empty($path)) $path = $this->config->requestFix;
                        list($module, $method) = explode($this->config->requestFix, $path);
                    }
                    else
                    {
                        $url   = html_entity_decode($this->post->referer);
                        $param = substr($url, strrpos($url, '?') + 1);

                        $module = $this->config->default->module;
                        $method = $this->config->default->method;
                        if(strpos($param, '&') !== false) list($module, $method) = explode('&', $param);
                        $module = str_replace('m=', '', $module);
                        $method = str_replace('f=', '', $method);
                    }

                    $response['result']  = 'success';
                    if(common::hasPriv($module, $method))
                    {
                        $response['locate']  = $this->post->referer;
                        return $this->send($response);
                    }
                    else
                    {
                        $response['locate']  = $this->config->webRoot;
                        return $this->send($response);
                    }
                }
                else
                {
                    if($this->app->getViewType() == 'json')
                    {
                        $data = $this->user->getDataInJSON($user);
                        return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
                    }

                    $response['locate']  = $this->config->webRoot;
                    $response['result']  = 'success';
                    return $this->send($response);
                }
            }
            else
            {
                $response['result']  = 'fail';
                $fails = $this->user->failPlus($account);
                if($this->app->getViewType() == 'json') return print(helper::removeUTF8Bom(json_encode(array('status' => 'failed', 'reason' => $this->lang->user->loginFailed))));
                $remainTimes = $this->config->user->failTimes - $fails;
                if($remainTimes <= 0)
                {
                    $response['message'] = sprintf($this->lang->user->loginLocked, $this->config->user->lockMinutes);
                    return $this->send($response);
                }
                else if($remainTimes <= 3)
                {
                    $response['message'] = sprintf($this->lang->user->lockWarning, $remainTimes);
                    return $this->send($response);
                }

                $response['message'] = $this->lang->user->loginFailed;
                return $this->send($response);
            }
        }
        else
        {
            $this->loadModel('misc');
            $this->view->noGDLib     = sprintf($this->lang->misc->noGDLib, common::getSysURL() . $this->config->webRoot, '', false, true);
            $this->view->title       = $this->lang->user->login;
            $this->view->referer     = $this->referer;
            $this->view->s           = zget($this->config->global, 'sn', '');
            $this->view->keepLogin   = $this->cookie->keepLogin ? $this->cookie->keepLogin : 'off';
            $this->view->rand        = $this->user->updateSessionRandom();
            $this->view->unsafeSites = $this->misc->checkOneClickPackage();
            $this->display();
        }
    }

    /**
     * Deny page.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $refererBeforeDeny    the referer of the denied page.
     * @access public
     * @return void
     */
    public function deny($module, $method, $refererBeforeDeny = '')
    {
        $this->setReferer();
        $this->view->title             = $this->lang->user->deny;
        $this->view->module            = $module;
        $this->view->method            = $method;
        $this->view->denyPage          = $this->referer;        // The denied page.
        $this->view->refererBeforeDeny = $refererBeforeDeny;    // The referer of the denied page.
        $this->app->loadLang($module);
        $this->app->loadLang('my');

        /* Check deny type. */
        $rights  = $this->app->user->rights['rights'];
        $acls    = $this->app->user->rights['acls'];

        $module  = strtolower($module);
        $method  = strtolower($method);

        $denyType = 'nopriv';
        if(isset($rights[$module][$method]))
        {
            $menu = isset($lang->navGroup->$module) ? $lang->navGroup->$module : $module;
            $menu = strtolower($menu);

            if(!isset($acls['views'][$menu])) $denyType = 'noview';
            $this->view->menu = $menu;
        }

        $this->view->denyType = $denyType;

        $this->display();
    }

    /**
     * Logout.
     *
     * @access public
     * @return void
     */
    public function logout($referer = 0)
    {
        if(isset($this->app->user->id)) $this->loadModel('action')->create('user', $this->app->user->id, 'logout');
        session_destroy();
        setcookie('za', false);
        setcookie('zp', false);

        if($this->app->getViewType() == 'json') return print(json_encode(array('status' => 'success')));
        $vars = !empty($referer) ? "referer=$referer" : '';
        $this->locate($this->createLink('user', 'login', $vars));
    }

    /**
     * Reset password.
     *
     * @access public
     * @return void
     */
    public function reset()
    {
        if(!isset($_SESSION['resetFileName']))
        {
            $resetFileName = $this->app->getBasePath() . 'tmp' . DIRECTORY_SEPARATOR . uniqid('reset_') . '.txt';
            $this->session->set('resetFileName', $resetFileName);
        }

        $resetFileName = $this->session->resetFileName;

        $needCreateFile = false;
        if(!file_exists($resetFileName) or (time() - filemtime($resetFileName)) > 60 * 2) $needCreateFile = true;

        if($_POST)
        {
            if($needCreateFile) return print(js::reload('parent'));

            $result = $this->user->resetPassword();
            if(dao::isError()) return print(js::error(dao::getError()));
            if(!$result) return print(js::alert($this->lang->user->resetFail));

            echo js::alert($this->lang->user->resetSuccess);
            $referer = helper::safe64Encode($this->createLink('index', 'index'));
            return print(js::locate(inlink('logout', 'referer=' . $referer), 'parent'));
        }

        /* Remove the real path for security reason. */
        $pathPos       = strrpos($this->app->getBasePath(), DIRECTORY_SEPARATOR, -2);
        $resetFileName = substr($resetFileName, $pathPos+1);

        $this->view->title          = $this->lang->user->resetPassword;
        $this->view->status         = 'reset';
        $this->view->needCreateFile = $needCreateFile;
        $this->view->resetFileName  = $resetFileName;

        $this->display();
    }

    /**
     * User dynamic.
     *
     * @param  int    $userID
     * @param  string $period
     * @param  int    $recTotal
     * @param  string $date
     * @param  string $direction     next|pre
     * @access public
     * @return void
     */
    public function dynamic($userID = '', $period = 'today', $recTotal = 0, $date = '', $direction = 'next')
    {
        $user    = $this->user->getById($userID, 'id');
        $account = $user->account;
        $users   = $this->loadModel('dept')->getDeptUserPairs($this->app->user->dept, 'id');

        /* set menus. */
        $this->view->userList = $this->user->setUserList($users, $userID);

        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',     $uri, 'product');
        $this->session->set('productPlanList', $uri, 'product');
        $this->session->set('releaseList',     $uri, 'product');
        $this->session->set('storyList',       $uri, 'product');
        $this->session->set('projectList',     $uri, 'project');
        $this->session->set('executionList',   $uri, 'execution');
        $this->session->set('taskList',        $uri, 'execution');
        $this->session->set('buildList',       $uri, 'execution');
        $this->session->set('bugList',         $uri, 'qa');
        $this->session->set('caseList',        $uri, 'qa');
        $this->session->set('testtaskList',    $uri, 'qa');

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage = 50, $pageID = 1);

        /* Append id for secend sort. */
        $orderBy = $direction == 'next' ? 'date_desc' : 'date_asc';
        $date    = empty($date) ? '' : date('Y-m-d', $date);

        $actions = $this->loadModel('action')->getDynamic($account, $period, $orderBy, $pager, 'all', 'all', 'all', $date, $direction);

        $this->view->title      = $this->lang->user->common . $this->lang->colon . $this->lang->user->dynamic;
        $this->view->position[] = $this->lang->user->dynamic;

        /* Assign. */
        $this->view->type       = $period;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->pager      = $pager;
        $this->view->user       = $user;
        $this->view->dateGroups = $this->action->buildDateGroup($actions, $direction, $period);
        $this->view->direction  = $direction;
        $this->display();
    }

	/**
     * crop avatar
     *
     * @param  int    $image
     * @access public
     * @return void
     */
    public function cropAvatar($image)
    {
        $image = $this->loadModel('file')->getByID($image);

        if(!empty($_POST))
        {
            $size = fixer::input('post')->get();
            $this->file->cropImage($image->realPath, $image->realPath, $size->left, $size->top, $size->right - $size->left, $size->bottom - $size->top, $size->scaled ? $size->scaleWidth : 0, $size->scaled ? $size->scaleHeight : 0);

            $this->app->user->avatar = $image->webPath;
            $this->session->set('user', $this->app->user);
            $this->dao->update(TABLE_USER)->set('avatar')->eq($image->webPath)->where('account')->eq($this->app->user->account)->exec();
            exit('success');
        }

        $this->view->user  = $this->user->getById($this->app->user->account);
        $this->view->title = $this->lang->user->cropAvatar;
        $this->view->image = $image;
        $this->display();
    }

    /**
     * Get user for ajax
     *
     * @param  string $requestID
     * @param  string $assignedTo
     * @access public
     * @return void
     */
    public function ajaxGetUser($taskID = '', $assignedTo = '')
    {
        $users = $this->user->getPairs('noletter, noclosed');
        $html = "<form method='post' target='hiddenwin' action='" . $this->createLink('task', 'assignedTo', "taskID=$taskID&assignedTo=$assignedTo") . "'>";
        $html .= html::select('assignedTo', $users, $assignedTo);
        $html .= html::submitButton('', '', 'btn btn-primary');
        $html .= '</form>';
        echo $html;
    }

    /**
     * AJAX: get users from a contact list.
     *
     * @param  int    $contactListID
     * @param  string $dropdownName mailto|whitelist
     * @access public
     * @return string
     */
    public function ajaxGetContactUsers($contactListID, $dropdownName = 'mailto')
    {
        $list = $contactListID ? $this->user->getContactListByID($contactListID) : '';

        $attr = $dropdownName == 'mailto' ? "data-placeholder='{$this->lang->chooseUsersToMail}'" : '';

        $users = $this->user->getPairs('devfirst|nodeleted|noclosed', $list ? $list->userList : '', $this->config->maxCount);
        if(isset($this->config->user->moreLink)) $this->config->moreLinks[$dropdownName . "[]"] = $this->config->user->moreLink;

        if(!$contactListID)
        {
            return print(html::select($dropdownName . "[]", $users, '', "class='form-control chosen' multiple $attr"));
        }
        else
        {
            return print(html::select($dropdownName . "[]", $users, $list->userList, "class='form-control chosen' multiple $attr"));
        }
    }

    /**
     * Ajax get contact list.
     *
     * @param  $dropdownName mailto|whitelist
     * @access public
     * @return string
     */
    public function ajaxGetContactList($dropdownName = 'mailto')
    {
        $contactList = $this->user->getContactLists($this->app->user->account, 'withnote');
        if(empty($contactList)) return false;
        return print(html::select('', $contactList, '', "class='form-control' onchange=\"setMailto('$dropdownName', this.value)\""));
    }

    /**
     * Ajax print templates.
     *
     * @param  int    $type
     * @param  string $link
     * @access public
     * @return void
     */
    public function ajaxPrintTemplates($type, $link = '')
    {
        $this->view->link      = $link;
        $this->view->type      = $type;
        $this->view->templates = $this->user->getUserTemplates($type);
        $this->display();
    }

    /**
     * Save current template.
     *
     * @access public
     * @return string
     */
    public function ajaxSaveTemplate($type)
    {
        $this->user->saveUserTemplate($type);
        if(dao::isError()) echo js::error(dao::getError(), $full = false);
        return print($this->fetch('user', 'ajaxPrintTemplates', "type=$type"));
    }

    /**
     * Delete a user template.
     *
     * @param  int    $templateID
     * @access public
     * @return void
     */
    public function ajaxDeleteTemplate($templateID)
    {
        $this->dao->delete()->from(TABLE_USERTPL)->where('id')->eq($templateID)
            ->beginIF(!$this->app->user->admin)->andWhere('account')->eq($this->app->user->account)->fi()
            ->exec();
    }

    /**
     * Ajax get more user.
     *
     * @access public
     * @return void
     */
    public function ajaxGetMore()
    {
        $params = base64_decode($this->get->params);
        parse_str($params, $parsedParams);
        $users = $this->user->getPairs($parsedParams['params'], $parsedParams['usersToAppended']);

        $search   = $this->get->search;
        $limit    = $this->get->limit;
        $index    = 0;
        $newUsers = array();
        if(empty($search)) return array();
        foreach($users as $account => $realname)
        {
            if($index >= $limit) break;
            if(stripos($account, $search) === false and stripos($realname, $search) === false) continue;
            $index ++;
            $newUsers[$account] = $realname;
        }

        echo json_encode($newUsers);
    }

    /**
     * Ajax get group by vision.
     *
     * @param  string  $visions rnd|lite
     * @param  int     $i
     * @param  string  $selected
     * @access public
     * @return string
     */
    public function ajaxGetGroup($visions, $i = 0, $selected = '')
    {
        $visions   = explode(',', $visions);
        $groupList = $this->user->getGroupsByVisions($visions);
        if($i)
        {
            if($i > 1) $groupList = $groupList + array('ditto' => $this->lang->user->ditto);
            return print(html::select("group[$i][]", $groupList, $selected, 'size=3 multiple=multiple class="form-control chosen"'));
        }
        return print(html::select('group[]', $groupList, $selected, 'size=3 multiple=multiple class="form-control chosen"'));
    }

    /**
     * Refresh random for login
     *
     * @access public
     * @return void
     */
    public function refreshRandom()
    {
        $rand = (string)$this->user->updateSessionRandom();
        echo $rand;
    }
}
