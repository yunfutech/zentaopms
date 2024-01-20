#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getTips();
cid=1
pid=1

测试type值为progress，data值为0 >> 0
测试type值为progress，data值为1 >> 0
测试type值为cost，data值为0 >> 0
测试type值为cost，data值为1 >> 0
测试type值为空，data值为0 >> 0
测试type值为空，data值为1 >> 0

*/
$typeList = array('progress', 'cost', '');
$dataList = array(0, 1);

$weekly = new weeklyTest();

r($weekly->getTipsTest($typeList[0], $dataList[0])) && p() && e('0'); //测试type值为progress，data值为0
r($weekly->getTipsTest($typeList[0], $dataList[1])) && p() && e('0'); //测试type值为progress，data值为1
r($weekly->getTipsTest($typeList[1], $dataList[0])) && p() && e('0'); //测试type值为cost，data值为0
r($weekly->getTipsTest($typeList[1], $dataList[1])) && p() && e('0'); //测试type值为cost，data值为1
r($weekly->getTipsTest($typeList[2], $dataList[0])) && p() && e('0'); //测试type值为空，data值为0
r($weekly->getTipsTest($typeList[2], $dataList[1])) && p() && e('0'); //测试type值为空，data值为1