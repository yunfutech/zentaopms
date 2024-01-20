#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->getCardGroupByKanban();
cid=1
pid=1

测试查询看板1的卡片数量 >> 1
测试查询看板2的卡片数量 >> 1
测试查询看板3的卡片数量 >> 1
测试查询看板4的卡片数量 >> 1
测试查询看板5的卡片数量 >> 1
测试查询不存在看板的卡片数量 >> 0

*/

$kanbanIDList = array('1', '2', '3', '4', '5', '1000001');

$kanban = new kanbanTest();

r($kanban->getCardGroupByKanbanTest($kanbanIDList[0])) && p() && e('1'); // 测试查询看板1的卡片数量
r($kanban->getCardGroupByKanbanTest($kanbanIDList[1])) && p() && e('1'); // 测试查询看板2的卡片数量
r($kanban->getCardGroupByKanbanTest($kanbanIDList[2])) && p() && e('1'); // 测试查询看板3的卡片数量
r($kanban->getCardGroupByKanbanTest($kanbanIDList[3])) && p() && e('1'); // 测试查询看板4的卡片数量
r($kanban->getCardGroupByKanbanTest($kanbanIDList[4])) && p() && e('1'); // 测试查询看板5的卡片数量
r($kanban->getCardGroupByKanbanTest($kanbanIDList[5])) && p() && e('0'); // 测试查询不存在看板的卡片数量