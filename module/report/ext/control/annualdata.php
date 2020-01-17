<?php
class report extends control
{
    public function annualData($year = '')
    {
        $account     = $this->app->user->account;
        $firstAction = $this->dao->select('*')->from(TABLE_ACTION)->orderBy('id')->limit(1)->fetch();
        $currentYear = date('Y');
        $firstYear   = empty($firstAction) ? $currentYear : substr($firstAction->date, 0, 4);

        /* Get years for use zentao. */
        $years = array();
        for($thisYear = $firstYear; $thisYear <= $currentYear; $thisYear ++) $years[$thisYear] = $thisYear;

        /* Init year when year is empty. */
        if(empty($year))
        {
            $year  = date('Y');
            $month = date('n');
            if($month <= $this->config->report->annualData['minMonth'])
            {
                $year -= 1;
                if(!isset($years[$year])) $year += 1;
            }
        }

        /* Get common annual data. */
        $data = array();
        $data['logins'] = $this->report->getUserYearLogins($account, $year);

        /* Set role. */
        $role = 'po';
        if($this->app->user->role == 'dev' or $this->app->user->role == 'td' or $this->app->user->role == 'pm') $role = 'dev';
        if($this->app->user->role == 'qd' or $this->app->user->role == 'qa') $role = 'qa';

        /* Get annual data by role. */
        if($role == 'po')
        {
            $products = $this->report->getUserYearProducts($account, $year);
            $data['involvedProducts'] = count($products);

            $planGroups = $this->report->getPlansByProducts($products, $account, $year);
            $planCount  = 0;
            foreach($planGroups as $plans) $planCount += $plans;
            $data['createdPlans'] = $planCount;
            $data['productStat']  = $this->report->getStatByProducts($products, $account, $year);

            $storyInfo = $this->report->getUserYearStory($products, $account, $year);
            $data['createdStories'] = $storyInfo['count'];
            $data['storyPri']       = $storyInfo['pri'];
            $data['storyStage']     = $storyInfo['stage'];
            $data['storyMonth']     = $storyInfo['month'];

            $storyGroups = $this->report->getStoriesByProducts($products, $account, $year);
            foreach($products as $productID => $product)
            {
                $product->plans   = zget($planGroups, $productID, 0);
                $product->stories = zget($storyGroups, $productID, 0);
            }
            $data['products'] = $products;
        }
        elseif($role == 'dev')
        {
            $data['actions'] = $this->report->getUserYearActions($account, $year);

            $efforts = $this->report->getUserYearEfforts($account, $year);
            $data['efforts']  = $efforts->count;
            $data['consumed'] = round($efforts->consumed, 2);

            $projects    = $this->report->getUserYearProjects($account, $year);
            $projectStat = $this->report->getStatByProjects($projects);

            $tasks = $this->report->getUserYearFinishedTasks($account, $year);
            $bugs  = $this->report->getUserYearResolvedBugs($account, $year);
            $data['finishedTaskPri'] = $tasks['pri'];
            $data['resolvedBugPri']  = $bugs['pri'];
            $data['taskMonth']       = $tasks['month'];
            $data['bugMonth']        = $bugs['month'];
            $data['effortMonth']     = $this->report->getEffort4Month($account, $year);;

            $stories = $this->report->getFinishedStoryByProjects($projects, $account, $year);
            $tasks   = $this->report->getFinishedTaskByProjects($projects, $account, $year);
            $bugs    = $this->report->getResolvedBugByProjects($projects, $account, $year);
            foreach($projects as $projectID => $project)
            {
                $project->stories = zget($stories, $projectID, 0);
                $project->tasks   = zget($tasks, $projectID, 0);
                $project->bugs    = zget($bugs, $projectID, 0);
            }

            $data['projects']    = $projects;
            $data['projectStat'] = $projectStat;
        }
        elseif($role == 'qa')
        {
            $data['actions'] = $this->report->getUserYearActions($account, $year);
            $bugInfo = $this->report->getUserYearCreatedBugs($account, $year);
            $data['foundBugs'] = $bugInfo['count'];
            $data['bugPri']    = $bugInfo['pri'];
            $data['bugMonth']  = $bugInfo['month'];

            $caseInfo = $this->report->getUserYearCreatedCases($account, $year);
            $data['createdCases'] = $caseInfo['count'];
            $data['casePri']      = $caseInfo['pri'];
            $data['caseMonth']    = $caseInfo['month'];

            $products    = $this->report->getUserYearProducts4QA($account, $year);
            $productStat = $this->report->getBugStatByProducts($products, $account, $year);

            $bugs = $this->report->getCreatedBugByProducts($products, $account, $year);
            foreach($products as $productID => $product) $product->bugs = zget($bugs, $productID, 0);

            $data['products']    = $products;
            $data['productStat'] = $productStat;
        }

        $this->view->title = sprintf($this->lang->report->annualData->title, $year, $this->app->user->realname);
        $this->view->data  = $data;
        $this->view->role  = $role;
        $this->view->year  = $year;
        $this->view->years = $years;
        die($this->display());
    }
}
