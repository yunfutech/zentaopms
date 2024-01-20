#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getFinished();
cid=1
pid=1

测试project值为0，date值为2022-05-01 >> 0
测试project值为0，date值为空 >> 0
测试project值为1，date值为2022-05-01 >> 0
测试project值为1，date值为空 >> 0
测试project值为11，date值为2022-05-01 >> 0
测试project值为11，date值为空 >> 0

*/

$projectList = array(0, 1, 11);
$dateList    = array('2022-05-01', '');

$weekly = new weeklyTest();

r($weekly->getFinishedTest($projectList[0], $dateList[0])) && p() && e('0'); //测试project值为0，date值为2022-05-01
r($weekly->getFinishedTest($projectList[0], $dateList[1])) && p() && e('0'); //测试project值为0，date值为空
r($weekly->getFinishedTest($projectList[1], $dateList[0])) && p() && e('0'); //测试project值为1，date值为2022-05-01
r($weekly->getFinishedTest($projectList[1], $dateList[1])) && p() && e('0'); //测试project值为1，date值为空
r($weekly->getFinishedTest($projectList[2], $dateList[0])) && p() && e('0'); //测试project值为11，date值为2022-05-01
r($weekly->getFinishedTest($projectList[2], $dateList[1])) && p() && e('0'); //测试project值为11，date值为空