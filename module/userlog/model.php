<?php

class userlogModel extends model
{
    public $daily = 'daily';
    public $weekly = 'weekly';
    const PROJECT_DEPARTMENT_ID = 1;            # 项目部
    const RD_DEPARTMENT_ID = 3;                 # 研发部
    const GENERAL_AFFAIRS_DEPARTMENT_ID = 4;    # 总务部

    public function getUserlog($type, $pager, $sort)
    {
        return $this->dao->select('*')
            ->from(TABLE_USERLOG)
            ->where('type')->eq($type)
            ->andWhere('status')->eq(1)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getUserlogById($userlogId)
    {
        return $this->dao->select('*')
            ->from(TABLE_USERLOG)
            ->where('id')->eq($userlogId)
            ->fetch();
    }

    public function getUserlogByUser($account, $type, $pager, $sort)
    {
        return $this->dao->select('*')
            ->from(TABLE_USERLOG)
            ->where('account')->eq($account)
            ->andWhere('type')->eq($type)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getUncommittedDailies()
    {
        $committedUserlogs = $this->dao->select('*')
            ->from(TABLE_USERLOG)
            ->where('type')->eq($this->daily)
            ->andWhere('status')->eq(1)
            ->andWhere('DATE_FORMAT(date, "%Y-%m-%d")')->eq(helper::today())
            ->fetchAll();
        $committedAccounts = [];
        foreach ($committedUserlogs as $committedUserlog) {
            array_push($committedAccounts, $committedUserlog->account);
        }

        $users = $this->loadModel('dept')->getUsers($this->getDepts($this->daily));
        $accounts = [];
        foreach ($users as $user) {
            array_push($accounts, $user->account);
        }
        $uncommittedAccounts = array_diff($accounts, $committedAccounts);
        return $uncommittedAccounts;
    }

    public function create($account, $tasks, $type, $realname)
    {
        $type2name = [$this->daily => '日报', $this->weekly => '周报'];
        $content = $this->generateMarkdown($tasks, $type);
        $daily = new stdClass();
        $daily->name = strval(date('Y-m-d') . '-'. $type2name[$type] . '-' . $realname);
        $daily->content = $content;
        $daily->account = $account;
        $daily->status = 0;
        $daily->type = $type;
        $daily->date = helper::now();
        $this->dao->insert(TABLE_USERLOG)->data($daily)->exec();
        return $this->dao->lastInsertID();
    }

    public function edit($id, $post)
    {
        return $this->dao->update(TABLE_USERLOG)
            ->set('name')->eq($post['name'])
            ->set('content')->eq($post['content'])
            ->where('id')->eq($id)
            ->exec();
    }

    public function finish($id)
    {
        return $this->dao->update(TABLE_USERLOG)
            ->set('status')->eq(1)
            ->where('id')->eq($id)
            ->exec();
    }

    public function getTasksByUser($account, $type)
    {
        if ($type == $this->daily) {
            $today = helper::today();
            $tomorrow = date('Y-m-d',strtotime('+1 day'));
            $todayTasks = $this->getDailyTasks($account, $today);
            $tomorrowTasks = $this->getDailyTasks($account, $tomorrow, false);
            return ['todayTasks' => $todayTasks, 'tomorrowTasks' => $tomorrowTasks];
        } else {
            $firstDay = date('Y-m-d', strtotime('this week'));
            $lastDay = date('Y-m-d', strtotime('this week +6 day'));
            $nextWeekFirstDay = date('Y-m-d', strtotime('next week'));
            $nextWeekLastDay = date('Y-m-d', strtotime('next week +6 day'));
            $thisWeekTasks = $this->getWeeklyTasks($account, $firstDay, $lastDay);
            $nextWeekTasks = $this->getWeeklyTasks($account, $nextWeekFirstDay, $nextWeekLastDay, 'false');
            return ['thisWeekTasks' => $thisWeekTasks, 'nextWeekTasks' => $nextWeekTasks];
        }
    }

    private function getDailyTasks($account, $date, $isToday=true)
    {
        $tasks = $this->dao->select('t1.name, t2.name as projectName, t4.name as productName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->leftjoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t1.project = t3.project')
            ->leftjoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t3.product')
            ->where('t1.deleted')->eq(0)
            ->beginIF($isToday == true)
            ->andWhere('t1.finishedBy')->eq($account)
            ->andWhere('DATE_FORMAT(t1.finishedDate, "%Y-%m-%d")')->eq($date)
            ->fi()
            ->beginIF($isToday == false)
            ->andWhere('t1.assignedTo')->eq($account)
            ->andWhere('DATE_FORMAT(t1.deadline, "%Y-%m-%d")')->eq($date)
            ->fi()
            ->fetchAll();
        return $tasks;
    }

    private function getWeeklyTasks($account, $firstDay, $lastDay, $isThisWeek=true)
    {
        $tasks = $this->dao->select('t1.name, t2.name as projectName, t4.name as productName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->leftjoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t1.project = t3.project')
            ->leftjoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t3.product')
            ->where('t1.deleted')->eq(0)
            ->beginIF($isThisWeek == true)
            ->andWhere('t1.finishedBy')->eq($account)
            ->andWhere('DATE_FORMAT(t1.finishedDate, "%Y-%m-%d")')->le($lastDay)
            ->andWhere('DATE_FORMAT(t1.finishedDate, "%Y-%m-%d")')->ge($firstDay)
            ->fi()
            ->beginIF($isThisWeek == false)
            ->andWhere('t1.assignedTo')->eq($account)
            ->andWhere('DATE_FORMAT(t1.deadline, "%Y-%m-%d")')->le($lastDay)
            ->andWhere('DATE_FORMAT(t1.deadline, "%Y-%m-%d")')->ge($firstDay)
            ->fi()
            ->fetchAll();
        return $tasks;
    }

    private function generateMarkdown($tasks, $type)
    {
        if ($type == $this->daily) {
            $todayTasks = $tasks['todayTasks'];
            $tomorrowTasks = $tasks['tomorrowTasks'];
            $content = $this->addTaskList($todayTasks, '### 今天完成工作' . PHP_EOL . PHP_EOL);
            $content .= $this->addTaskList($tomorrowTasks, '### 明天工作计划' . PHP_EOL . PHP_EOL);
        } else {
            $thisWeekTasks = $tasks['thisWeekTasks'];
            $nextWeekTasks = $tasks['nextWeekTasks'];
            $content = $this->addTaskList($thisWeekTasks, '### 本周完成工作' . PHP_EOL . PHP_EOL);
            $content .= $this->addTaskList($nextWeekTasks, '### 下周工作计划' . PHP_EOL . PHP_EOL);
        }
        return $content;
    }

    private function addTaskList($tasks, $content)
    {
        foreach($tasks as $productName => $value) {
            foreach($value as $projectName => $value2) {
                if (empty($productName)) {
                    $content .= PHP_EOL . '#### ' . $projectName . PHP_EOL . PHP_EOL;
                } else {
                    $content .= PHP_EOL . '#### ' . $productName . ' - ' . $projectName . PHP_EOL . PHP_EOL;
                }
                foreach($value2 as $name) {
                    $content .= '- ' . $name . PHP_EOL;
                }
            }
        }
        return $content . PHP_EOL;
    }

    public function getDepts($type='all')
    {
        if ($type == $this->daily) {
            $depts = [self::GENERAL_AFFAIRS_DEPARTMENT_ID, self::PROJECT_DEPARTMENT_ID];
        } else {
            $depts = [self::GENERAL_AFFAIRS_DEPARTMENT_ID, self::RD_DEPARTMENT_ID, self::PROJECT_DEPARTMENT_ID];
        }
        return $depts;
    }

    public function isHaveDaily() {
        $dept = $this->app->user->dept;
        if (in_array($dept, $this->getDepts($this->daily))) {
            return true;
        }
        return false;
    }
}