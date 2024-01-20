#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->createRDKanban();
cid=1
pid=1

测试创建执行10001的执行看板 >> 1,3,0
测试创建执行10002的执行看板 >> 1,3,0
测试创建执行10003的执行看板 >> 1,3,0
测试创建执行10004的执行看板 >> 1,3,0
测试创建执行10005的执行看板 >> 1,3,0

*/

$execution1 = new stdclass();
$execution1->id = '100001';

$execution2 = new stdclass();
$execution2->id = '100002';

$execution3 = new stdclass();
$execution3->id = '100003';

$execution4 = new stdclass();
$execution4->id = '100004';

$execution5 = new stdclass();
$execution5->id = '100005';

$kanban = new kanbanTest();

r($kanban->createRDKanbanTest($execution1)) && p() && e('1,3,0'); // 测试创建执行10001的执行看板
r($kanban->createRDKanbanTest($execution2)) && p() && e('1,3,0'); // 测试创建执行10002的执行看板
r($kanban->createRDKanbanTest($execution3)) && p() && e('1,3,0'); // 测试创建执行10003的执行看板
r($kanban->createRDKanbanTest($execution4)) && p() && e('1,3,0'); // 测试创建执行10004的执行看板
r($kanban->createRDKanbanTest($execution5)) && p() && e('1,3,0'); // 测试创建执行10005的执行看板
