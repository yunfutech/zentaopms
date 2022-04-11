#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/testtask.class.php';
su('admin');

/**

title=测试 testtaskModel->getLinkableCasesByBug();
cid=1
pid=1

*/

global $tester;
$tester->loadModel('testtask');
$tester->app->loadClass('pager', $static = true);

$task1 = $tester->testtask->getById(1);
$task2 = $tester->testtask->getById(2);
$pager = new pager(0, 10, 1);

$cases1 = $tester->testtask->getLinkableCasesByBug(1, $task1, '1 = 1', '', $pager);
$cases2 = $tester->testtask->getLinkableCasesByBug(2, $task2, '1 = 1', '', $pager);

r(count($cases1)) && p() && e('0'); // 通过测试单1关联的版本下的Bug，获取可关联到测试单1的用例数量
r(count($cases2)) && p() && e('0'); // 通过测试单2关联的版本下的Bug，获取可关联到测试单2的用例数量