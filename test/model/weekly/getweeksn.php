#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getWeekSN();
cid=1
pid=1

开始时间和结束时间都不为空 >> 1
开始时间不为空，结束时间为空 >> -2730
开始时间为空，结束时间不为空 >> 2732
开始时间和结束时间都为空 >> 0

*/
$begin = array('2022-05-02', '');
$end   = array('2022-05-06', '');

$weekly = new weeklyTest();

r($weekly->getWeekSNTest($begin[0], $end[0])) && p() && e('1');     //开始时间和结束时间都不为空
r($weekly->getWeekSNTest($begin[0], $end[1])) && p() && e('-2730'); //开始时间不为空，结束时间为空
r($weekly->getWeekSNTest($begin[1], $end[0])) && p() && e('2732');  //开始时间为空，结束时间不为空
r($weekly->getWeekSNTest($begin[1], $end[1])) && p() && e('0');     //开始时间和结束时间都为空