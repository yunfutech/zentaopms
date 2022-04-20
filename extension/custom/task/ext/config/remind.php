<?php
$config->task->remind = new stdClass();
// 忽略总务部和管理层
$config->task->remind->ignoredDepts = [4, 9];
// 不足工时下限提示
$config->task->remind->target = 8;
# 超过工时上限提示
$config->task->remind->overtime = 10;

$config->exerciseNum = new stdClass();
$config->exerciseNum->delayProject = 20;
$config->exerciseNum->lackEstimate = 20;
$config->exerciseNum->delayTask = 10;

$config->task->remind->subject = '禅道日报';
$config->task->remind->from = '云孚';
$config->task->remind->to = 'all';
$config->task->remind->email = 'all@yunfutech.com';

// $config->task->remind->wsGroup = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=ed3405d3-f80b-401d-a32b-af217fda97ba'; # 测试
$config->task->remind->wsGroup = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=67754c4e-8a3d-4170-8907-f962de0ea662'; # 全体群
