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
$config->exerciseNum->delayTask = 20;

$config->task->remind->mailToList = ['all@yunfutech.com'];
$config->task->remind->wsGroup = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=67754c4e-8a3d-4170-8907-f962de0ea662'; # 全体群
