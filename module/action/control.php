<?php
/**
 * The control file of action module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     action
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class action extends control
{
    /**
     * Trash.
     *
     * @param  string $type all|hidden
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function trash($type = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('backup');

        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',        $uri, 'product');
        $this->session->set('productPlanList',    $uri, 'product');
        $this->session->set('storyList',          $uri, 'product');
        $this->session->set('releaseList',        $uri, 'product');
        $this->session->set('programList',        $uri, 'program');
        $this->session->set('projectList',        $uri, 'project');
        $this->session->set('executionList',      $uri, 'execution');
        $this->session->set('taskList',           $uri, 'execution');
        $this->session->set('buildList',          $uri, 'execution');
        $this->session->set('bugList',            $uri, 'qa');
        $this->session->set('caseList',           $uri, 'qa');
        $this->session->set('testtaskList',       $uri, 'qa');
        $this->session->set('docList',            $uri, 'doc');
        $this->session->set('opportunityList',    $uri, 'project');
        $this->session->set('riskList',           $uri, 'project');
        $this->session->set('trainplanList',      $uri, 'project');
        $this->session->set('roomList',           $uri, 'admin');
        $this->session->set('researchplanList',   $uri, 'project');
        $this->session->set('researchreportList', $uri, 'project');
        $this->session->set('meetingList',        $uri, 'project');
        $this->session->set('storyLibList',       $uri, 'assetlib');
        $this->session->set('issueLibList',       $uri, 'assetlib');
        $this->session->set('riskLibList',        $uri, 'assetlib');
        $this->session->set('opportunityLibList', $uri, 'assetlib');
        $this->session->set('practiceLibList',    $uri, 'assetlib');
        $this->session->set('componentLibList',   $uri, 'assetlib');

        /* Get deleted objects. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        $sort    = common::appendOrder($orderBy);
        $trashes = $this->action->getTrashes($type, $sort, $pager);

        /* Title and position. */
        $this->view->title      = $this->lang->action->trash;
        $this->view->position[] = $this->lang->action->trash;

        $this->view->trashes = $trashes;
        $this->view->type    = $type;
        $this->view->orderBy = $orderBy;
        $this->view->pager   = $pager;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Undelete an object.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function undelete($actionID)
    {
        $this->action->undelete($actionID);
        echo js::reload('parent');
    }

    /**
     * Hide an deleted object.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function hideOne($actionID)
    {
        $this->action->hideOne($actionID);
        echo js::reload('parent');
    }

    /**
     * Hide all deleted objects.
     *
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function hideAll($confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->action->confirmHideAll, inlink('hideAll', "confirm=yes"));
        }
        else
        {
            $this->action->hideAll();
            echo js::reload('parent');
        }
    }

    /**
     * Comment.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return void
     */
    public function comment($objectType, $objectID)
    {
        $actionID = $this->action->create($objectType, $objectID, 'Commented', $this->post->comment);
        if(defined('RUN_MODE') && RUN_MODE == 'api')
        {
            return $this->send(array('status' => 'success', 'data' => $actionID));
        }
        else
        {
            echo js::reload('parent');
        }
    }

    /**
     * Edit comment of a action.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function editComment($actionID)
    {
        if(strlen(trim(strip_tags($this->post->lastComment, '<img>'))) != 0)
        {
            $this->action->updateComment($actionID);
        }
        else
        {
            dao::$errors['submit'][] = $this->lang->action->historyEdit;
            return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }
        return $this->send(array('result' => 'success', 'locate' => 'reload'));
    }
}
