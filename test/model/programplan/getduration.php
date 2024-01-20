#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/programplan.class.php';
su('admin');

/**

title=测试 programplanModel->getDuration();
cid=1
pid=1

测试获取2022-04-01 ~ 2022-05-01持续时间 >> 21
测试获取2022-04-02 ~ 2022-05-06持续时间 >> 25
测试获取2022-04-03 ~ 2022-05-11持续时间 >> 28
测试获取2022-04-04 ~ 2022-05-16持续时间 >> 31
测试获取2022-04-05 ~ 2022-05-21持续时间 >> 34

*/

$beginList = array('2022-04-01', '2022-04-02', '2022-04-03', '2022-04-04', '2022-04-05');
$endList   = array('2022-05-01', '2022-05-06', '2022-05-11', '2022-05-16', '2022-05-21');

$programplan = new programplanTest();

r($programplan->getDurationTest($beginList[0], $endList[0])) && p() && e('21'); // 测试获取2022-04-01 ~ 2022-05-01持续时间
r($programplan->getDurationTest($beginList[1], $endList[1])) && p() && e('25'); // 测试获取2022-04-02 ~ 2022-05-06持续时间
r($programplan->getDurationTest($beginList[2], $endList[2])) && p() && e('28'); // 测试获取2022-04-03 ~ 2022-05-11持续时间
r($programplan->getDurationTest($beginList[3], $endList[3])) && p() && e('31'); // 测试获取2022-04-04 ~ 2022-05-16持续时间
r($programplan->getDurationTest($beginList[4], $endList[4])) && p() && e('34'); // 测试获取2022-04-05 ~ 2022-05-21持续时间