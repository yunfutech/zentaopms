<?php
/**
 * The control file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id
 * @link        http://www.zentao.net
 */
class projectweekly extends control
{
    public function generateWeekly($projectID = 0)
    {
        $response['result']  = 'success';
        // $response['message'] = $this->lang->projectweekly->generateSuccess;
        $project             = $this->loadModel('project')->getByID($projectID);
        $this->projectweekly->generateWeekly($projectID, $project->name);
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $response['locate']  = $this->createLink('projectweekly', 'projectweeklylist', "projectID=" . $projectID);
        $this->send($response);
    }

    /**
     * AJAX: Check projects.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function projectweeklylist($projectID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static=true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $weeklies = $this->projectweekly->getWeeklyByProject($projectID, $pager, $sort);
        $this->view->weeklies = $weeklies;
        $this->view->projectID  = $projectID;

        $this->loadModel('project')->setMenu($projectID);

        $project                = $this->loadModel('project')->getByID($projectID);
        $this->view->title      = $project->name . $this->lang->colon . $this->lang->projectweekly->weekly;
        $this->view->position[] = $this->lang->projectweekly->common;

        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->display();
    }

    public function edit($weeklyID, $projectID)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->projectweekly->edit($weeklyID, $_POST);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = inlink('view', 'weeklyID=' . $weeklyID . '&projectID=' . $projectID);
            $this->send($response);
        }

        $this->loadModel('project')->setMenu($projectID);

        $weekly = $this->projectweekly->getWeeklyById($weeklyID);
        $this->view->weekly     = $weekly;

        $this->view->title      = $this->lang->projectweekly->edit;
        $this->view->position[] = $this->lang->projectweekly->edit;

        $this->view->weeklyID   = $weeklyID;
        $this->view->projectID  = $projectID;
        $this->display();
    }

    public function view($weeklyID, $projectID)
    {
        $this->loadModel('project')->setMenu($projectID);

        $weekly = $this->projectweekly->getWeeklyById($weeklyID);

        // $hyperdown    = $this->app->loadClass('hyperdown');
        // $weekly->content = $hyperdown->makeHtml($weekly->overview);

        $this->view->title      = $this->lang->projectweekly->view;
        $this->view->position[] = $this->lang->projectweekly->view;
        $this->view->weekly = $weekly;
        $this->view->weeklyID = $weeklyID;
        $this->view->projectID = $projectID;
        $this->display();
    }
}