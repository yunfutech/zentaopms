<?php

/**
 * 获取需生成任务的员工
 */
public function getMeetingUsers()
{
    return $this->dao->select('id, account, dept')->from(TABLE_USER)
        ->where('dept')->in(array_keys($this->config->task->generateTask->deptArr))
        ->andWhere('deleted')->eq('0')
        ->andWhere('leaved')->eq('0')
        ->andWhere('id')->notin($this->config->task->generateTask->blacklist)
        ->fetchall();
}

/**
 * 生成任务
 */
public function generateTask($task)
{
    $this->dao->insert(TABLE_TASK)->data($task)->exec();
}
