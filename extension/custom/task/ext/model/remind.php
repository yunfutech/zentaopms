<?php

/**
 * 获取部门员工
 */
public function getRemindUsers()
{
    return $this->dao->select('id, account, realname')
        ->from(TABLE_USER)
        ->where('deleted')->eq(0)
        ->andWhere('dept')->notin($this->config->task->remind->ignoredDepts)
        ->fetchall();
}

/**
 * 获取员工预计工时
 */
public function getUserTaskEstimate($account, $date)
{
    $result =  $this->dao->select('sum(t1.estimate) as sum')
        ->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_EXECUTION)->alias('t2')
        ->on('t1.execution = t2.id')
        ->where('t1.status')->notin('cancel, closed')
        ->andWhere('t1.deadline')->eq($date)
        ->andWhere('t1.assignedTo')->eq($account)
        ->andWhere('t1.deleted')->ne(1)
        ->andWhere('t2.deleted')->ne(1)
        ->andWhere('t2.status')->notin('cancel, closed')
        ->fetch();
    return round($result->sum, 1);
}

/**
 * 获取员工延期任务个数
 */
public function getUserDelayTasksCount($account, $date)
{
    $result = $this->dao->select('count(t1.id) as cnt')
        ->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_EXECUTION)->alias('t2')
        ->on('t1.execution = t2.id')
        ->where('t1.status')->notin('cancel, closed, done')
        ->andWhere('t1.deadline')->lt($date)
        ->andWhere('t1.assignedTo')->eq($account)
        ->andWhere('t2.status')->notin('cancel, closed')
        ->andWhere('t1.deleted')->ne(1)
        ->andWhere('t2.deleted')->ne(1)
        ->fetch();
    return $result->cnt;
}

/**
 * 获取延期迭代
 */
public function getDelayExecution()
{
    $today = helper::today();
    return $this->dao->select('t2.realname, t2.account, group_concat(DISTINCT t1.name ORDER BY t1.end SEPARATOR \'<br/>\') as projects, count(t1.name) as cnt')
        ->from(TABLE_EXECUTION)->alias('t1')
        ->leftJoin(TABLE_USER)->alias('t2')
        ->on('t1.PO = t2.account')
        ->where('t1.end')->lt($today)
        ->andWhere('t1.status')->eq('doing')
        ->andWhere('t1.deleted')->ne(1)
        ->andWhere('t2.deleted')->ne(1)
        ->andWhere('t1.type')->eq('sprint')
        ->groupBy('t1.PO')
        ->orderBy('cnt desc')
        ->fetchAll();
}

/**
 * 获取未解决的bug
 */
public function getUnresolvedBugs()
{
    return $this->dao->select('t1.title, t1.assignedTo, t2.name as executionName, t3.realname as userName, TIMESTAMPDIFF(DAY, t1.assignedDate, NOW()) as dateDiff, t4.name as projectName')->from(TABLE_BUG)->alias('t1')
        ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
        ->leftJoin(TABLE_USER)->alias('t3')->on('t1.assignedTo = t3.account')
        ->leftJoin(TABLE_PROJECT)->alias('t4')->on('t1.project = t4.id')
        ->where("t1.status = 'active'")
        ->andWhere('t1.deleted')->eq(0)
        ->orderBy('dateDiff desc')
        ->fetchAll();
}

/**
 * 统计员工bug数量
 */
public function countUserBugs()
{
    return $this->dao->select('t2.realname as userName, COUNT(t1.id) as bugNum')->from(TABLE_BUG)->alias('t1')
        ->leftJoin(TABLE_USER)->alias('t2')->on('t1.assignedTo = t2.account')
        ->where("t1.status = 'active'")
        ->andWhere('t1.deleted')->eq(0)
        ->groupBy('t1.assignedTo')
        ->orderBy('bugNum desc')
        ->fetchAll();
}
