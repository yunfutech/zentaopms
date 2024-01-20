#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';

/**

title=测试 kanbanModel->getKanbanPairs();
cid=1
pid=1

获取用户admin可以看到的看板pairs >> 100
获取用户po1可以看到的看板pairs >> 32
获取用户po2可以看到的看板pairs >> 32
获取用户user1可以看到的看板pairs >> 32
获取用户user2可以看到的看板pairs >> 32
获取用户pm1可以看到的看板pairs >> 32
获取用户pm2可以看到的看板pairs >> 32

*/

$userList = array('admin', 'po1', 'po2', 'user1', 'user2', 'pm1', 'pm2');

$kanban = new kanbanTest();

r($kanban->getKanbanPairsTest($userList[0])) && p() && e('100'); //获取用户admin可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[1])) && p() && e('32');  //获取用户po1可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[2])) && p() && e('32');  //获取用户po2可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[3])) && p() && e('32');  //获取用户user1可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[4])) && p() && e('32');  //获取用户user2可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[5])) && p() && e('32');  //获取用户pm1可以看到的看板pairs
r($kanban->getKanbanPairsTest($userList[6])) && p() && e('32');  //获取用户pm2可以看到的看板pairs