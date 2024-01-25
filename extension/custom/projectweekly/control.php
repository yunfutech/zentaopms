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

    public function weeklyboard($week = 0, $project = 0, $user = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort = $this->loadModel('common')->appendOrder($orderBy);
        $weeklies = $this->projectweekly->getWeekly($pager, $sort, $week, $project, $user);

        $this->view->title      = $this->lang->projectweekly->weeklyboard;
        $this->view->position[] = $this->lang->projectweekly->weeklyboard;
        $this->view->week       = $week;
        $this->view->thisWeek   = date('YW');
        if ($week == 0) {
            $yearWeek = date('YW');
        } else {
            $yearWeek = $week;
        }
        $this->view->lastWeek   = $this->getPreYearWeek($yearWeek);
        $this->view->nextWeek   = $this->getNextYearWeek($yearWeek);
        $this->view->weeks      = $this->getWeeksRange();
        $this->view->weeklies   = $weeklies;
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->project = $project;
        $this->view->projects = $this->projectweekly->getWeeklyProjects();
        $this->view->user = $user;
        $this->view->users = $this->projectweekly->getWeeklyDirectors();
        $this->display();
    }

    private function getPreYearWeek($yearWeek)
    {
        $year = substr($yearWeek, 0, 4);
        $week = substr($yearWeek, 4, 2);
        if ($week != '01') {
            $preWeek = $yearWeek - 1;
        } else {
            $year = $year - 1;
            $weekCount = date("W", mktime(0, 0, 0, 12, 31, $year));
            $preWeek = $year . $weekCount;
        }
        return $preWeek;
    }

    private function getNextYearWeek($yearWeek)
    {
        $year = substr($yearWeek, 0, 4);
        $week = substr($yearWeek, 4, 2);
        $weekCount = date("W", mktime(0, 0, 0, 12, 31, $year));
        if ($week != $weekCount) {
            $nextWeek = $yearWeek + 1;
        } else {
            $nextWeek = ($year + 1) . '01';
        }
        return $nextWeek;
    }

    private function getWeeksRange()
    {
        $year = date('Y');
        $weekRange = [0 => '全部'];
        foreach (range(2021, date('Y')) as $year) {
            $weekCount = date("W", mktime(0, 0, 0, 12, 31, $year));
            $weeks = range(1, $weekCount);
            foreach ($weeks as $week) {
                $weekday = $this->weekday($week, $year);
                $start = date('m.d', ($weekday['start']));
                $end = date('m.d', ($weekday['end']));
                $weekKey = $this->weekIntToStr($week);
                $weekRange[$year . $weekKey] = $year . '年第' . $week . '周(' . $start . '-' . $end . ')';
            }
        }
        return $weekRange;
    }

    public function weekday($week = 1, $year)
    {
        $year_start = mktime(0, 0, 0, 1, 1, $year);
        $year_end = mktime(0, 0, 0, 12, 31, $year);
        if (intval(date('W', $year_start)) === 1) {
            $start = $year_start;
        } else {
            $week++;
            $start = strtotime('+1 monday', $year_start);
        }
        if ($week == 1) {
            $weekday['start'] = $start;
        } else {
            $weekday['start'] = strtotime('+' . ($week - 1) . ' monday', $start);
        }
        $weekday['end'] = strtotime('+1 sunday', $weekday['start']);
        if (date('Y', $weekday['end']) != $year) {
            $weekday['end'] = $year_end;
        }
        return $weekday;
    }

    private function weekIntToStr($weekInt)
    {
        return str_pad($weekInt, 2, '0', STR_PAD_LEFT);
    }
}