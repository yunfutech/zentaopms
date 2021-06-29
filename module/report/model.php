<?php
/**
 * The model file of report module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: model.php 4726 2013-05-03 05:51:27Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class reportModel extends model
{
    /**
     * Compute percent of every item.
     *
     * @param  array    $datas
     * @access public
     * @return array
     */
    public function computePercent($datas)
    {
        $sum = 0;
        foreach($datas as $data) $sum += $data->value;

        $totalPercent = 0;
        foreach($datas as $i => $data)
        {
            $data->percent = round($data->value / $sum, 4);
            $totalPercent += $data->percent;
        }
        if(isset($i)) $datas[$i]->percent = round(1 - $totalPercent + $datas[$i]->percent, 4);
        return $datas;
    }

    /**
     * Create json data of single charts
     * @param  array $sets
     * @param  array $dateList
     * @return string the json string
     */
    public function createSingleJSON($sets, $dateList)
    {
        $data = '[';
        $now  = date('Y-m-d');
        $preValue = 0;
        $setsDate = array_keys($sets);
        foreach($dateList as $i => $date)
        {
            $date  = date('Y-m-d', strtotime($date));
            if($date > $now) break;
            if(!isset($sets[$date]) and $sets)
            {
                $tmpDate = $setsDate;
                $tmpDate[] = $date;
                sort($tmpDate);
                $tmpDateStr = ',' . join(',', $tmpDate);
                $preDate = rtrim(substr($tmpDateStr, 0, strpos($tmpDateStr, $date)), ',');
                $preDate = substr($preDate, strrpos($preDate, ',') + 1);

                if($preDate)
                {
                    $preValue = $sets[$preDate];
                    $preValue = $preValue->value;
                }
            }

            $data .= isset($sets[$date]) ? "{$sets[$date]->value}," : "{$preValue},";
        }
        $data = rtrim($data, ',');
        $data .= ']';
        return $data;
    }

    /**
     * Convert date format.
     *
     * @param  array  $dateList
     * @param  string $format
     * @access public
     * @return array
     */
    public function convertFormat($dateList, $format = 'Y-m-d')
    {
        foreach($dateList as $i => $date) $dateList[$i] = date($format, strtotime($date));
        return $dateList;
    }

    /**
     * Get projects.
     *
     * @access public
     * @return void
     */
    public function getProjects($begin = 0, $end = 0, $status = 'all')
    {
        $tasks = $this->dao->select('t1.project, t1.estimate, t1.consumed, t2.name as projectName')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.status')->ne('cancel')
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.parent')->lt(1)
            // ->andWhere('t2.status')->eq('closed')
            ->beginIF($begin)->andWhere('t2.begin')->ge($begin)->fi()
            ->beginIF($status == 'noclosed')->andWhere('t2.status')->ne('closed')->fi()
            ->beginIF($end)->andWhere('t2.end')->le($end)->fi()
            ->orderBy('t2.end_desc')
            ->fetchAll();

        $projects = array();
        foreach($tasks as $task)
        {
            $projectID = $task->project;
            if(!isset($projects[$projectID]))
            {
                $projects[$projectID] = new stdclass();
                $projects[$projectID]->estimate = 0;
                $projects[$projectID]->consumed = 0;
            }

            $projects[$projectID]->name      = $task->projectName;
            $projects[$projectID]->estimate += $task->estimate;
            $projects[$projectID]->consumed += $task->consumed;
        }

        return $projects;
    }

    /**
     * Get products.
     *
     * @access public
     * @return array
     */
    public function getProducts($conditions)
    {
        $products = $this->dao->select('id, code, name, PO')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->beginIF(strpos($conditions, 'closedProduct') === false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->fetchAll('id');
        $plans    = $this->dao->select('*')->from(TABLE_PRODUCTPLAN)->where('deleted')->eq(0)->andWhere('product')->in(array_keys($products))
            ->beginIF(strpos($conditions, 'overduePlan') === false)->andWhere('end')->gt(date('Y-m-d'))->fi()
            ->orderBy('product,parent_desc,begin')
            ->fetchAll('id');
        foreach($plans as $plan)
        {
            if($plan->parent > 0)
            {
                $parentPlan = zget($plans, $plan->parent, null);
                if($parentPlan)
                {
                    $products[$plan->product]->plans[$parentPlan->id] = $parentPlan;
                    unset($plans[$parentPlan->id]);
                }
                $plan->title = '>>' . $plan->title;
            }
            $products[$plan->product]->plans[$plan->id] = $plan;
        }

        $planStories      = array();
        $unplannedStories = array();
        $stmt = $this->dao->select('id,plan,product,status')->from(TABLE_STORY)->where('deleted')->eq(0)->query();
        while($story = $stmt->fetch())
        {
            if(empty($story->plan))
            {
                $unplannedStories[$story->id] = $story;
                continue;
            }

            $storyPlans   = array();
            $storyPlans[] = $story->plan;
            if(strpos($story->plan, ',') !== false) $storyPlans = explode(',', trim($story->plan, ','));
            foreach($storyPlans as $planID)
            {
                if(isset($plans[$planID]))
                {
                    $planStories[$story->id] = $story;
                    break;
                }
            }
        }

        foreach($planStories as $story)
        {
            $storyPlans = array();
            $storyPlans[] = $story->plan;
            if(strpos($story->plan, ',') !== false) $storyPlans = explode(',', trim($story->plan, ','));
            foreach($storyPlans as $planID)
            {
                if(!isset($plans[$planID])) continue;
                $plan = $plans[$planID];
                $products[$plan->product]->plans[$planID]->status[$story->status] = isset($products[$plan->product]->plans[$planID]->status[$story->status]) ? $products[$plan->product]->plans[$planID]->status[$story->status] + 1 : 1;
            }
        }

        foreach($unplannedStories as $story)
        {
            $product = $story->product;
            if(isset($products[$product]))
            {
                if(!isset($products[$product]->plans[0]))
                {
                    $products[$product]->plans[0] = new stdClass();
                    $products[$product]->plans[0]->title = $this->lang->report->unplanned;
                    $products[$product]->plans[0]->begin = '';
                    $products[$product]->plans[0]->end   = '';
                }
                $products[$product]->plans[0]->status[$story->status] = isset($products[$product]->plans[0]->status[$story->status]) ? $products[$product]->plans[0]->status[$story->status] + 1 : 1;
            }
        }

        unset($products['']);
        return $products;
    }

    /**
     * Get bugs
     *
     * @param  int    $begin
     * @param  int    $end
     * @access public
     * @return array
     */
    public function getBugs($begin, $end, $product, $project)
    {
        $end = date('Ymd', strtotime("$end +1 day"));
        $bugs = $this->dao->select('id, resolution, openedBy, status')->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andWhere('openedDate')->ge($begin)
            ->andWhere('openedDate')->le($end)
            ->beginIF($product)->andWhere('product')->eq($product)->fi()
            ->beginIF($project)->andWhere('project')->eq($project)->fi()
            ->fetchAll();

        $bugCreate = array();
        foreach($bugs as $bug)
        {
            $bugCreate[$bug->openedBy][$bug->resolution] = empty($bugCreate[$bug->openedBy][$bug->resolution]) ? 1 : $bugCreate[$bug->openedBy][$bug->resolution] + 1;
            $bugCreate[$bug->openedBy]['all']            = empty($bugCreate[$bug->openedBy]['all']) ? 1 : $bugCreate[$bug->openedBy]['all'] + 1;
            if($bug->status == 'resolved' or $bug->status == 'closed')
            {
                $bugCreate[$bug->openedBy]['resolved'] = empty($bugCreate[$bug->openedBy]['resolved']) ? 1 : $bugCreate[$bug->openedBy]['resolved'] + 1;
            }
        }

        foreach($bugCreate as $account => $bug)
        {
            $validRate = 0;
            if(isset($bug['fixed']))     $validRate += $bug['fixed'];
            if(isset($bug['postponed'])) $validRate += $bug['postponed'];
            $bugCreate[$account]['validRate'] = (isset($bug['resolved']) and $bug['resolved']) ? ($validRate / $bug['resolved']) : "0";
        }
        uasort($bugCreate, 'sortSummary');
        return $bugCreate;
    }

    /**
     * Get workload.
     *
     * @param int    $dept
     * @param string $assign
     *
     * @access public
     * @return array
     */
    public function getWorkload($dept = 0, $assign = 'assign')
    {
        $deptUsers = array();
        if($dept) $deptUsers = $this->loadModel('dept')->getDeptUserPairs($dept);

        if($assign == 'noassign')
        {
            $members = $this->dao->select('t1.account,t2.name,t1.root')->from(TABLE_TEAM)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t2.id = t1.root')
                ->where('t2.status')->notin('cancel, closed, done, suspended')
                ->beginIF($dept)->andWhere('t1.account')->in(array_keys($deptUsers))->fi()
                ->andWhere('t1.type')->eq('project')
                ->andWhere("t1.account NOT IN(SELECT `assignedTo` FROM " . TABLE_TASK . " WHERE `project` = t1.`root` AND `status` NOT IN('cancel, closed, done, pause') AND assignedTo != '' GROUP BY assignedTo)")
                ->fetchGroup('account', 'name');

            $workload = array();
            if(!empty($members))
            {
                foreach($members as $member => $projects)
                {
                    if(!empty($projects))
                    {
                        foreach($projects as $name => $project)
                        {
                            $workload[$member]['task'][$name]['count']     = 0;
                            $workload[$member]['task'][$name]['manhour']   = 0;
                            $workload[$member]['task'][$name]['projectID'] = $project->root;
                            $workload[$member]['total']['count']           = 0;
                            $workload[$member]['total']['manhour']         = 0;
                        }
                    }
                }
            }
            return $workload;
        }
        $stmt = $this->dao->select('t1.*, t2.name as projectName')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.status')->notin('cancel, closed, done, pause')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.status')->notin('cancel, closed, done, suspended')
            ->andWhere('assignedTo')->ne('');

        $allTasks = $stmt->fetchAll('id');
        $tasks    = $stmt->beginIF($dept)->andWhere('t1.assignedTo')->in(array_keys($deptUsers))->fi()->fetchAll('id');

        if(empty($allTasks)) return array();

        /* Fix bug for children. */
        $parents       = array();
        $taskIdList    = array();
        $taskGroups    = array();
        foreach($tasks as $task)
        {
            if($task->parent > 0) $parents[$task->parent] = $task->parent;
            $taskGroups[$task->assignedTo][$task->id] = $task;
        }

        $multiTaskTeams = $this->dao->select('*')->from(TABLE_TEAM)->where('type')->eq('task')
            ->andWhere('root')->in(array_keys($allTasks))
            ->beginIF($dept)->andWhere('account')->in(array_keys($deptUsers))->fi()
            ->fetchGroup('account', 'root');
        foreach($multiTaskTeams as $assignedTo => $multiTasks)
        {
            foreach($multiTasks as $task)
            {
                $userTask = clone $allTasks[$task->root];
                $userTask->estimate = $task->estimate;
                $userTask->consumed = $task->consumed;
                $userTask->left     = $task->left;
                $taskGroups[$assignedTo][$task->root] = $userTask;
            }
        }

        $workload = array();
        foreach($taskGroups as $user => $userTasks)
        {
            if($user)
            {
                foreach($userTasks as $task)
                {
                    if(isset($parents[$task->id])) continue;
                    $workload[$user]['task'][$task->projectName]['count']     = isset($workload[$user]['task'][$task->projectName]['count']) ? $workload[$user]['task'][$task->projectName]['count'] + 1 : 1;
                    $workload[$user]['task'][$task->projectName]['manhour']   = isset($workload[$user]['task'][$task->projectName]['manhour']) ? $workload[$user]['task'][$task->projectName]['manhour'] + $task->left : $task->left;
                    $workload[$user]['task'][$task->projectName]['projectID'] = $task->project;
                    $workload[$user]['total']['count']   = isset($workload[$user]['total']['count'])   ? $workload[$user]['total']['count'] + 1 : 1;
                    $workload[$user]['total']['manhour'] = isset($workload[$user]['total']['manhour']) ? $workload[$user]['total']['manhour'] + $task->left : $task->left;
                }
            }
        }
        unset($workload['closed']);
        return $workload;
    }

    /**
     * Get bug assign.
     *
     * @access public
     * @return array
     */
    public function getBugAssign()
    {
        $bugs = $this->dao->select('t1.*, t2.name as productName')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.status')->eq('active')
            ->andWhere('t2.deleted')->eq(0)
            ->fetchGroup('assignedTo');
        $assign = array();
        foreach($bugs as $user => $userBugs)
        {
            if($user)
            {
                foreach($userBugs as $bug)
                {
                    $assign[$user]['bug'][$bug->productName]['count']     = isset($assign[$user]['bug'][$bug->productName]['count']) ? $assign[$user]['bug'][$bug->productName]['count'] + 1 : 1;
                    $assign[$user]['bug'][$bug->productName]['productID'] = $bug->product;
                    $assign[$user]['total']['count']   = isset($assign[$user]['total']['count']) ? $assign[$user]['total']['count'] + 1 : 1;
                }
            }
        }
        unset($assign['closed']);
        return $assign;
    }

    /**
     * Get System URL.
     *
     * @access public
     * @return void
     */
    public function getSysURL()
    {
        if(isset($this->config->mail->domain)) return $this->config->mail->domain;

        /* Ger URL when run in shell. */
        if(PHP_SAPI == 'cli')
        {
            $url = parse_url(trim($this->server->argv[1]));
            $port = (empty($url['port']) or $url['port'] == 80) ? '' : $url['port'];
            $host = empty($port) ? $url['host'] : $url['host'] . ':' . $port;
            return $url['scheme'] . '://' . $host;
        }
        else
        {
            return common::getSysURL();
        }
    }

    /**
     * Get user bugs.
     *
     * @access public
     * @return void
     */
    public function getUserBugs()
    {
        return $this->dao->select('t1.id, t1.title, t2.account as user, t1.deadline')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')
            ->on('t1.assignedTo = t2.account')
            ->where('t1.assignedTo')->ne('')
            ->andWhere('t1.assignedTo')->ne('closed')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deadline', true)->eq('0000-00-00')
            ->orWhere('t1.deadline')->lt(date(DT_DATE1, strtotime('+4 day')))
            ->markRight(1)
            ->fetchGroup('user');
    }

    /**
     * Get user tasks.
     *
     * @access public
     * @return void
     */
    public function getUserTasks()
    {
        return $this->dao->select('t1.id, t1.name, t2.account as user, t1.deadline')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.assignedTo = t2.account')
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t1.project = t3.id')
            ->where('t1.assignedTo')->ne('')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.status')->in('wait,doing')
            ->andWhere('t3.status')->ne('suspended')
            ->andWhere('t1.deadline', true)->eq('0000-00-00')
            ->orWhere('t1.deadline')->lt(date(DT_DATE1, strtotime('+4 day')))
            ->markRight(1)
            ->fetchGroup('user');
    }

    /**
     * Get user todos.
     *
     * @access public
     * @return array
     */
    public function getUserTodos()
    {
        $stmt = $this->dao->select('t1.*, t2.account as user')
            ->from(TABLE_TODO)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')
            ->on('t1.account = t2.account')
            ->where('t1.status')->eq('wait')
            ->orWhere('t1.status')->eq('doing')
            ->query();

        $todos = array();
        while($todo = $stmt->fetch())
        {
            if($todo->type == 'task') $todo->name = $this->dao->findById($todo->idvalue)->from(TABLE_TASK)->fetch('name');
            if($todo->type == 'bug')  $todo->name = $this->dao->findById($todo->idvalue)->from(TABLE_BUG)->fetch('title');
            $todos[$todo->user][] = $todo;
        }
        return $todos;
    }

    /**
     * Get user testTasks.
     *
     * @access public
     * @return array
     */
    public function getUserTestTasks()
    {
        return $this->dao->select('t1.*, t2.account as user')->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.owner = t2.account')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere("(t1.status='wait' OR t1.status='doing')")
            ->fetchGroup('user');
    }


    public function getTaskStatistics($dept = 0, $date)
    {
        $childDeptIds = $this->loadModel('dept')->getAllChildID($dept);
        $deptUsers = $this->dept->getUsers($childDeptIds);
        $usernames = array();
        $tasks = array();
        foreach($deptUsers as $user)
        {
            if($user)
            {
                $username = $user->account;
                $usernames[] = $username;
                $all = 0;
                $complete = 0;
                $tasks[$username] = [
                    "detail"=> [],
                    "all"=> 0,
                    "complete"=> 0,
                    "consumed"=> 0
                ];
            }
        }
        $finishedIdstasks = $this->dao->select('t1.id, t1.left, t1.status, t1.pri as taskpri, t1.parent, t1.name, t1.project, t1.estimate, t1.consumed, t1.assignedTo, t1.finishedBy,t2.pri, t2.name as projectName, t3.name as moduleName, t3.id as moduleId, t4.id as storyID, t4.title as storyTitle')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
        ->leftJoin(TABLE_MODULE)->alias('t3')->on('t1.module = t3.id')
        ->leftJoin(TABLE_STORY)->alias('t4')->on('t1.story = t4.id')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t1.finishedBy')->in($usernames)
        ->andWhere('t1.deadline')->eq($date)
        ->andWhere('t1.finishedBy')->ne('')
        ->andWhere('t1.assignedTo')->ne('')->orderBy('t1.id')->fetchAll();


        $todoTasks = $this->dao->select('t1.id, t1.name, t1.project, t1.status, t1.pri as taskpri, t1.estimate, t1.consumed, t1.assignedTo, t1.finishedBy, t2.pri, t2.name as projectName, t3.name as moduleName, t3.id as moduleId, t4.id as storyID, t4.title as storyTitle')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
        ->leftJoin(TABLE_MODULE)->alias('t3')->on('t1.module = t3.id')
        ->leftJoin(TABLE_STORY)->alias('t4')->on('t1.story = t4.id')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t1.assignedTo')->in($usernames)
        ->andWhere('t1.deadline')->eq($date)
        ->andWhere('t1.status')->notin('cancel, closed')
        ->andWhere('t1.finishedBy')->eq('')
        ->andWhere('t1.assignedTo')->ne('')->orderBy('t1.id')->fetchAll();

        // $todoTasks  = $todo->beginIF(0)->andWhere('t1.assignedTo')->in(array_keys($deptUsers))->fi()->fetchAll('id');

        $status_dict = array(
            'doing'=>1,
            'done'=>2,
            'wait'=>3
        );

        foreach($finishedIdstasks as $task)
        {
            if (in_array($task->finishedBy, $usernames)) {
                $task->status_sort = $status_dict[$task->status];
                $tasks[$task->finishedBy]['detail'][]  = $task;
                $tasks[$task->finishedBy]['all'] += $task->estimate;
                if($task->finishedBy != ''){
                    $tasks[$task->finishedBy]['complete'] += $task->estimate;
                    $tasks[$task->finishedBy]['consumed'] += $task->consumed;
                }
            }
        }
        foreach($todoTasks as $task)
        {
            if (in_array($task->assignedTo, $usernames)) {
                $task->status_sort = $status_dict[$task->status];
                $tasks[$task->assignedTo]['detail'][]  = $task;
                $tasks[$task->assignedTo]['all'] += $task->estimate;
                if($task->finishedBy == ''){
                    $tasks[$task->assignedTo]['complete'] += $task->consumed;
                    $tasks[$task->assignedTo]['consumed'] += $task->consumed;
                }
            }
        }
        $short = array();
        $exceed = array();
        foreach($tasks as $user => $task)
        {
            if (in_array($user, $usernames)) {
                if($dept == 3) {
                    if($task['all'] - 8 < 0) {
                        $short[$user] = $task['all'];
                    }elseif($task['all'] - 8 > 2) {
                        $exceed[$user] = $task['all'];
                    }
                }elseif($dept == 1) {
                    if($task['all'] < 6) {
                        $short[$user] = $task['all'];
                    }elseif($task['all'] - 8 > 2) {
                        $exceed[$user] = $task['all'];
                    }
                }
                $tasks[$user]['process'] = $task['complete'] / $task['all'] * 100;
                $taskpri = array_column($task['detail'], 'taskpri');
                $pri = array_column($task['detail'], 'pri');
                $status_pri = array_column($task['detail'], 'status_sort');
                array_multisort($status_pri, SORT_ASC, $taskpri, SORT_ASC, $pri, SORT_ASC, $task['detail']);
                $tasks[$user]['detail'] = $task['detail'];
            }
        }
        // $process = array_column($tasks, 'process');
        asort($short);
        arsort($exceed);
        $complete = array_column($tasks, 'consumed');
        $all = array_column($tasks, 'all');
        array_multisort($complete, SORT_DESC, $all, SORT_DESC,  $tasks);
        return  [
            "tasks"=> $tasks,
            "short"=> $short,
            "exceed"=> $exceed
        ];
    }

    public function getUndoneTask($dept = 0)
    {
        $childDeptIds = $this->loadModel('dept')->getAllChildID($dept);
        $deptUsers = $this->dept->getUsers($childDeptIds);
        $usernames = array();
        $tasks = array();
        foreach($deptUsers as $user)
        {
            if($user)
            {
                $username = $user->account;
                $usernames[] = $username;
                $tasks[$username] = [
                    "detail"=> [],
                    "num"=> 0
                ];
            }
        }
        $date = date('Y-m-d');
        $undoneTasks = $this->dao->select('t1.id, t1.name, t1.status, t1.project, t1.pri as taskpri, t1.estimate, t1.consumed, t1.assignedTo, t1.deadline, t1.finishedBy, t2.pri, t2.name as projectName')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t1.assignedTo')->in($usernames)
        ->andWhere('t1.status')->in('doing, wait, pause')
        ->andWhere('t1.finishedBy')->eq('')
        ->andWhere('t2.status')->notin('cancel, closed')
        ->andWhere('assignedTo')->ne('')->orderBy('t1.pri,t1.deadline')->fetchAll();

        foreach($undoneTasks as $task)
        {
            if (in_array($task->assignedTo, $usernames)) {
                if ($date > $task->deadline) {
                    $task->expired = 1;
                }
                $tasks[$task->assignedTo]['detail'][]  = $task;
            }
        }

        return $tasks;
    }

    /**
     * 获取迭代看板全部迭代
     */
    private function getStatsProjects($end, $project_type) {
        return $this->dao->select('id, name, pri, CONVERT(name USING gbk) as gbkName')->from(TABLE_PROJECT)
            ->where('begin')->le($end)
            ->beginIF($project_type != '')
            ->andWhere('project_type')->eq($project_type)
            ->fi()
            ->andWhere('status')->notin('cancel')
            ->orderBy('pri, gbkName')->fetchAll();
    }

    /**
     * 获取迭代实际工时
     */
    private function getHoursByPid($projectID, $begin, $end) {
        return $this->dao->select('finishedBy, sum(consumed) as totalConsumed')->from(TABLE_TASK)
        ->where('deleted')->eq(0)
        ->andWhere('project')->eq(intval($projectID))
        ->andWhere('finishedDate')->ge($begin)
        ->andWhere('finishedDate')->le($end)
        ->andWhere('finishedBy')->ne('')
        ->groupBy('finishedBy')
        ->fetchAll();
    }

    /**
     * 获取迭代关联需求数和总预计工时
     */
    private function getStoryStatsByPid($projectID, $end) {
        return $this->dao->select('count(t1.id) as total, sum(t1.estimate) as totalEstimate')->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_PROJECTSTORY)->alias('t2')->on('t1.id = t2.story')
            ->where('t2.project')->eq(intval($projectID))
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.openedDate')->le($end)
            ->fetch();
    }

    /**
     * 获取迭代关联需求中已完成需求总数和总预计工时
     */
    private function getDoneStoryStatsByPid($projectID, $begin, $end) {
        return $this->dao->select('count(t1.id) as total, sum(t1.estimate) as totalEstimate')->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_PROJECTSTORY)->alias('t2')->on('t1.id = t2.story')
            ->where('t2.project')->eq(intval($projectID))
            ->andWhere('t1.closedReason')->eq('done')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('closedDate')->ge($begin)
            ->andWhere('closedDate')->le($end)
            ->fetch();
    }

    /**
     * 获取迭代关联的项目
     */
    private function getProductByPid($projectID) {
        return $this->dao->select('t1.name')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.product')
            ->where('t2.project')->eq(intval($projectID))
            ->andWhere('t1.deleted')->eq(0)
            ->fetchPairs();
    }

    public function getProjectStatistics($begin, $end, $project_type)
    {
        $projects = [];
        foreach($this->getStatsProjects($end, $project_type) as $project)
        {
            $project->users = [];
            $project->doneManHour = 0;
            $consumedStats = $this->getHoursByPid($project->id, $begin, $end);
            $project->usersCount = count($consumedStats) != 0 ? count($consumedStats) : 1;
            foreach ($consumedStats as $item) {
                $consumed = round($item->totalConsumed, 2);
                $project->users[$item->finishedBy] = $consumed;
                $project->doneManHour += $consumed;
            }
            $storyStats = $this->getStoryStatsByPid($project->id, $end);
            $project->allStoiresCount = $storyStats->total;
            $project->manHour = round($storyStats->totalEstimate);
            if ($project->manHour == 0) {
                continue;
            }
            $doneStoryStats = $this->getDoneStoryStatsByPid($project->id, $begin, $end);
            $project->allDoneStoiresCount = $doneStoryStats->total;
            $project->doneStoriesEstimate = round($doneStoryStats->totalEstimate);
            $project->schedule = $project->manHour > 0 ? round($project->doneStoriesEstimate / $project->manHour, 4) : 0;
            $project->accuracy = $project->manHour > 0 ? round($project->doneManHour / $project->manHour, 4) : 0;
            $products = $this->getProductByPid($project->id);
            $project->products = implode('<br/>', array_values($products));
            array_push($projects, $project);
        }
        return $projects;
    }

    public function getUserWorkHour($begin, $end, $dept)
    {
        $childDeptIds = $this->loadModel('dept')->getAllChildID($dept);
        $deptUsers = $this->dept->getUsers($childDeptIds);
        $usernames = array();
        $usertasks = array();
        foreach($deptUsers as $user)
        {
            if($user)
            {
                $username = $user->account;
                $usernames[] = $username;
                $tasks[$username] = [
                    "tasks"=> [],
                    "consumed"=> 0
                ];
            }
        }
        $end = date('Y-m-d',strtotime("$end +1 days"));
        $tasks = $this->dao->select('t1.id, t1.project as pid, t1.name, t1.status, t1.project, t1.estimate, t1.consumed, t1.finishedBy, t1.deadline, t2.pri, t2.name as projectName')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t1.finishedBy')->in($usernames)
        ->andWhere('t1.deadline')->ge($begin)
        ->andWhere('t1.deadline')->lt($end)
        ->andWhere('t1.status')->in(['done', 'closed'])
        ->fetchAll();
        // var_dump($this->dao->sqlobj);
        foreach($tasks as $task)
        {
            if($task->consumed > 0) {
                $usertasks[$task->finishedBy]['consumed'] += $task->consumed;
                if(!array_key_exists($task->projectName, $usertasks[$task->finishedBy]['tasks'])) {
                    $usertasks[$task->finishedBy]['tasks'][$task->projectName] = 0;
                }
                $usertasks[$task->finishedBy]['tasks'][$task->projectName] += $task->consumed;
            }
        }
        foreach($usertasks as $index=>$project)
        {
            $tmp = $project['tasks'];
            arsort($tmp);
            $usertasks[$index]['tasks'] = $tmp;
        }
        $date = date('Y-m-d');
        $consumed = array_column($usertasks, 'consumed');
        array_multisort($consumed, SORT_DESC, $usertasks);
        return $usertasks;
    }
    /**
     * Get user year logins.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return int
     */
    public function getUserYearLogins($account, $year)
    {
        return $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where('actor')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->andWhere('action')->eq('login')->fetch('count');
    }

    /**
     * Get user year actions.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return int
     */
    public function getUserYearActions($account, $year)
    {
        return $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where('actor')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->fetch('count');
    }

    /**
     * Get user year efforts.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return object
     */
    public function getUserYearEfforts($account, $year)
    {
        return $this->dao->select('count(*) as count, sum(consumed) as consumed')->from(TABLE_TASKESTIMATE)->where('account')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->fetch();
    }

    /**
     * Get user year story.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearStory($products, $account, $year)
    {
        $stories = $this->dao->select('*')->from(TABLE_STORY)->where('openedBy')->eq($account)->andWhere('product')->in(array_keys($products))->andWhere('LEFT(openedDate, 4)')->eq($year)->andWhere('deleted')->eq(0)->fetchAll();

        $storyInfo = array();
        $storyInfo['count'] = 0;
        $storyInfo['pri']   = array();
        $storyInfo['stage'] = array();
        $storyInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($stories as $story)
        {
            $storyInfo['count'] ++;

            if(!isset($storyInfo['pri'][$story->pri])) $storyInfo['pri'][$story->pri] = 0;
            $storyInfo['pri'][$story->pri] ++;

            if(!isset($storyInfo['stage'][$story->stage])) $storyInfo['stage'][$story->stage] = 0;
            $storyInfo['stage'][$story->stage] ++;

            $month = (int)substr($story->openedDate, 5, 2) - 1;
            $storyInfo['month'][$month] ++;
        }

        return $storyInfo;
    }

    /**
     * Get user year created bugs.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearCreatedBugs($account, $year)
    {
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->andWhere('deleted')->eq(0)->fetchAll();

        $bugInfo = array();
        $bugInfo['count'] = 0;
        $bugInfo['pri']   = array();
        $bugInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($bugs as $bug)
        {
            $bugInfo['count'] ++;

            if(!isset($bugInfo['pri'][$bug->pri])) $bugInfo['pri'][$bug->pri] = 0;
            $bugInfo['pri'][$bug->pri] ++;

            $month = (int)substr($bug->openedDate, 5, 2) - 1;
            $bugInfo['month'][$month] ++;
        }
        return $bugInfo;
    }

    /**
     * Get user year resolved bugs.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearResolvedBugs($account, $year)
    {
        $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('resolvedBy')->eq($account)->andWhere('LEFT(resolvedDate, 4)')->eq($year)->fetchAll();

        $bugInfo = array();
        $bugInfo['count'] = 0;
        $bugInfo['pri']   = array();
        $bugInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($bugs as $bug)
        {
            $bugInfo['count'] ++;

            if(!isset($bugInfo['pri'][$bug->pri])) $bugInfo['pri'][$bug->pri] = 0;
            $bugInfo['pri'][$bug->pri] ++;

            $month = (int)substr($bug->resolvedDate, 5, 2) - 1;
            $bugInfo['month'][$month] ++;
        }
        return $bugInfo;
    }

    /**
     * Get user year finished tasks.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearFinishedTasks($account, $year)
    {
        $tasks = $this->dao->select('id,pri,finishedDate')->from(TABLE_TASK)
            ->where('LEFT(finishedDate, 4)')->eq($year)
            ->andWhere('finishedBy')->eq($account)
            ->fetchAll('id');
        $tasks += $this->dao->select('t1.id,t1.pri,t2.date as finishedDate')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
            ->leftJoin(TABLE_TEAM)->alias('t3')->on("t1.id=t3.root and t3.type='task'")
            ->where('t2.actor')->eq($account)
            ->andWhere('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t2.action')->eq('finished')
            ->andWhere('t3.account')->eq($account)
            ->fetchAll('id');

        $taskInfo = array();
        $taskInfo['count'] = 0;
        $taskInfo['pri']   = array();
        $taskInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($tasks as $task)
        {
            $taskInfo['count'] ++;

            if(!isset($taskInfo['pri'][$task->pri])) $taskInfo['pri'][$task->pri] = 0;
            $taskInfo['pri'][$task->pri] ++;

            $month = (int)substr($task->finishedDate, 5, 2) - 1;
            $taskInfo['month'][$month] ++;
        }
        return $taskInfo;
    }

    /**
     * Get user year created cases.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearCreatedCases($account, $year)
    {
        $cases = $this->dao->select('*')->from(TABLE_CASE)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchAll();

        $caseInfo = array();
        $caseInfo['count'] = 0;
        $caseInfo['pri']   = array();
        $caseInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($cases as $case)
        {
            $caseInfo['count'] ++;

            if(!isset($caseInfo['pri'][$case->pri])) $caseInfo['pri'][$case->pri] = 0;
            $caseInfo['pri'][$case->pri] ++;

            $month = (int)substr($case->openedDate, 5, 2) - 1;
            $caseInfo['month'][$month] ++;
        }
        return $caseInfo;
    }

    /**
     * Get user year products.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearProducts($account, $year)
    {
        $products = $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
            ->where('LEFT(createdDate, 4)')->eq($year)
            ->andWhere('createdBy', true)->eq($account)
            ->orWhere('PO')->eq($account)
            ->orWhere('QD')->eq($account)
            ->orWhere('RD')->eq($account)
            ->markRight(1)
            ->fetchAll('id');

        $storyProducts = $this->dao->select('DISTINCT product')->from(TABLE_STORY)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');
        $planProducts  = $this->dao->select('DISTINCT t1.product')->from(TABLE_PRODUCTPLAN)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='productplan'")
            ->where('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t2.actor')->eq($account)
            ->andWhere('t2.action')->eq('opened')
            ->groupBy('t1.product')
            ->fetchPairs('product', 'product');
        $products += $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
            ->where('id')->in($storyProducts + $planProducts)
            ->fetchAll('id');
        return $products;
    }

    /**
     * Get plans by products.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getPlansByProducts($products, $account, $year)
    {
        $planGroups = $this->dao->select('DISTINCT t1.*')->from(TABLE_PRODUCTPLAN)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='productplan'")
            ->where('t1.product')->in(array_keys($products))
            ->andWhere('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t2.actor')->eq($account)
            ->andWhere('t2.action')->eq('opened')
            ->fetchGroup('product', 'id');

        foreach($planGroups as $productID => $plans) $planGroups[$productID] = count($plans);
        return $planGroups;
    }

    /**
     * Get stories by products.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getStoriesByProducts($products, $account, $year)
    {
        return $this->dao->select('product, count(*) as stories')->from(TABLE_STORY)
            ->where('product')->in(array_keys($products))
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->andWhere('openedBy')->eq($account)
            ->groupBy('product')
            ->fetchPairs('product', 'stories');
    }

    /**
     * Get user year projects.
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearProjects($account, $year)
    {
        $projects = $this->dao->select('id,name,status')->from(TABLE_PROJECT)->where('1=1')
            ->andWhere('LEFT(begin, 4)', true)->eq($year)
            ->orWhere('LEFT(end, 4)')->le($year)
            ->markRight(1)
            ->andWhere('openedBy', true)->eq($account)
            ->orWhere('PO')->eq($account)
            ->orWhere('PM')->eq($account)
            ->orWhere('QD')->eq($account)
            ->orWhere('RD')->eq($account)
            ->markRight(1)
            ->fetchAll('id');

        $teamProjects = $this->dao->select('*')->from(TABLE_TEAM)->where('type')->eq('project')->andWhere('account')->eq($account)->andWhere('LEFT(`join`, 4)')->eq($year)->fetchPairs('root', 'root');
        $taskProjects = $this->dao->select('DISTINCT t1.project')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
            ->where('t2.actor')->eq($account)
            ->andWhere('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t2.action')->eq('finished')
            ->fetchPairs('project', 'project');

        $projects += $this->dao->select('id,name,status')->from(TABLE_PROJECT)
            ->where('id')->in($teamProjects + $taskProjects)
            ->fetchAll('id');
        return $projects;
    }

    /**
     * Get finished story by projects
     *
     * @param  array  $projects
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getFinishedStoryByProjects($projects, $account, $year)
    {
        $storyGroups = $this->dao->select('t1.*')->from(TABLE_PROJECTSTORY)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->leftJoin(TABLE_TASK)->alias('t3')->on('t1.story=t3.story')
            ->leftJoin(TABLE_ACTION)->alias('t4')->on("t3.id=t4.objectID and t4.objectType='task'")
            ->where('t1.project')->in(array_keys($projects))
            ->andWhere('LEFT(t4.date, 4)')->eq($year)
            ->andWhere('t4.actor')->eq($account)
            ->andWhere('t4.action')->eq('finished')
            ->fetchGroup('project', 'story');

        foreach($storyGroups as $projectID => $stories) $storyGroups[$projectID] = count($stories);
        return $storyGroups;
    }

    /**
     * Get finished task by projects.
     *
     * @param  array  $projects
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getFinishedTaskByProjects($projects, $account, $year)
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)
            ->where('LEFT(finishedDate, 4)')->eq($year)
            ->andWhere('finishedBy')->eq($account)
            ->andWhere('project')->in(array_keys($projects))
            ->fetchAll('id');
        $tasks += $this->dao->select('t1.*')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
            ->leftJoin(TABLE_TEAM)->alias('t3')->on("t1.id=t3.root and t3.type='task'")
            ->where('t2.actor')->eq($account)
            ->andWhere('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t2.action')->eq('finished')
            ->andWhere('t3.account')->eq($account)
            ->andWhere('t1.project')->in(array_keys($projects))
            ->fetchAll('id');

        $taskGroups = array();
        foreach($tasks as $taskID => $task) $taskGroups[$task->project][$taskID] = $task;
        foreach($taskGroups as $projectID => $tasks) $taskGroups[$projectID] = count($tasks);
        return $taskGroups;
    }

    /**
     * Get resolved bug by projects.
     *
     * @param  array  $projects
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getResolvedBugByProjects($projects, $account, $year)
    {
        return $this->dao->select('project, count(*) as bugs')->from(TABLE_BUG)
            ->where('project')->in(array_keys($projects))
            ->andWhere('LEFT(resolvedDate, 4)')->eq($year)
            ->andWhere('resolvedBy')->eq($account)
            ->groupBy('project')
            ->fetchPairs('project', 'bugs');
    }

    /**
     * Get stat by projects.
     *
     * @param  array $projects
     * @access public
     * @return array
     */
    public function getStatByProjects($projects)
    {
        $projectStat = array();
        $projectStat['count']     = 0;
        $projectStat['doing']     = 0;
        $projectStat['done']      = 0;
        $projectStat['suspended'] = 0;
        foreach($projects as $project)
        {
            $projectStat['count']++;
            if($project->status == 'closed' or $project->status == 'done') $projectStat['done']++;
            if($project->status == 'wait' or $project->status == 'doing') $projectStat['doing']++;
            if($project->status == 'suspended') $projectStat['suspended']++;
        }
        return $projectStat;
    }

    /**
     * Get stat by products.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getStatByProducts($products, $account, $year)
    {
        $allStories = $this->dao->select('product, count(*) as count')->from(TABLE_STORY)
            ->where('product')->in(array_keys($products))
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->groupBy('product')
            ->fetchPairs('product', 'count');
        $mineStories = $this->dao->select('product, count(*) as count')->from(TABLE_STORY)
            ->where('product')->in(array_keys($products))
            ->andWhere('openedBy')->eq($account)
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->groupBy('product')
            ->fetchPairs('product', 'count');

        $productStat = array();
        foreach($products as $productID => $product)
        {
            $productStat[$productID]['name']  = $product->name;
            $productStat[$productID]['count'] = zget($allStories, $productID, 0);
            $productStat[$productID]['mine']  = zget($mineStories, $productID, 0);
        }

        return $productStat;
    }

    /**
     * Get effort for month
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getEffort4Month($account, $year)
    {
        $efforts = $this->dao->select('*')->from(TABLE_TASKESTIMATE)->where('account')->eq($account)
            ->andWhere('LEFT(date, 4)')->eq($year)
            ->orderBy('date')
            ->fetchAll();

        $months = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach($efforts as $effort)
        {
            $month = (int)substr($effort->date, 5, 2) - 1;
            $months[$month] += $effort->consumed;
        }
        return $months;
    }

    /**
     * Get user year products for qa
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearProducts4QA($account, $year)
    {
        $bugProducts  = $this->dao->select('product')->from(TABLE_BUG)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');
        $caseProducts = $this->dao->select('product')->from(TABLE_CASE)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');

        $products = $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
            ->where('id')->in($bugProducts + $caseProducts)
            ->fetchAll('id');
        return $products;
    }

    /**
     * Get bug stat by products.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getBugStatByProducts($products, $account, $year)
    {
        $allBugs = $this->dao->select('product, count(*) as count')->from(TABLE_BUG)
            ->where('product')->in(array_keys($products))
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->groupBy('product')
            ->fetchPairs('product', 'count');
        $mineBugs = $this->dao->select('product, count(*) as count')->from(TABLE_BUG)
            ->where('product')->in(array_keys($products))
            ->andWhere('openedBy')->eq($account)
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->groupBy('product')
            ->fetchPairs('product', 'count');

        $productStat = array();
        foreach($products as $productID => $product)
        {
            $productStat[$productID]['name']  = $product->name;
            $productStat[$productID]['count'] = zget($allBugs, $productID, 0);
            $productStat[$productID]['mine']  = zget($mineBugs, $productID, 0);
        }

        return $productStat;
    }

    /**
     * Get created bug by products.
     *
     * @param  array  $products
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getCreatedBugByProducts($products, $account, $year)
    {
        return $this->dao->select('product, count(*) as bugs')->from(TABLE_BUG)
            ->where('product')->in(array_keys($products))
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->andWhere('openedBy')->eq($account)
            ->groupBy('product')
            ->fetchPairs('product', 'bugs');
    }
}

/**
 * @param $pre
 * @param $next
 *
 * @return int
 */
function sortSummary($pre, $next)
{
    if($pre['validRate'] == $next['validRate']) return 0;
    return $pre['validRate'] > $next['validRate'] ? -1 : 1;
}
