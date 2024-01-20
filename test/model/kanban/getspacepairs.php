#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->getSpacePairs();
cid=1
pid=1

查询用户可以看到的admin private的空间数量 >> 0
查询用户可以看到的admin cooperation的空间数量 >> 17
查询用户可以看到的admin public的空间数量 >> 16
查询用户可以看到的admin involved的空间数量 >> 0
查询用户可以看到的po16 private的空间数量 >> 1
查询用户可以看到的po16 cooperation的空间数量 >> 0
查询用户可以看到的po16 public的空间数量 >> 16
查询用户可以看到的po16 involved的空间数量 >> 0
查询用户可以看到的pm3 private的空间数量 >> 1
查询用户可以看到的test37 cooperation的空间数量 >> 0
查询用户可以看到的test37 public的空间数量 >> 16
查询用户可以看到的test37 involved的空间数量 >> 0
查询用户可以看到的user25 private的空间数量 >> 1
查询用户可以看到的user25 cooperation的空间数量 >> 0
查询用户可以看到的user25 public的空间数量 >> 16
查询用户可以看到的user25 involved的空间数量 >> 0

*/
$userList   = array('admin', 'po16', 'test37', 'user25');
$browseType = array('private', 'cooperation', 'public', 'involved');

$kanban = new kanbanTest();

r($kanban->getSpacePairsTest($userList[0], $browseType[0])) && p() && e('0');  // 查询用户可以看到的admin private的空间数量
r($kanban->getSpacePairsTest($userList[0], $browseType[1])) && p() && e('17'); // 查询用户可以看到的admin cooperation的空间数量
r($kanban->getSpacePairsTest($userList[0], $browseType[2])) && p() && e('16'); // 查询用户可以看到的admin public的空间数量
r($kanban->getSpacePairsTest($userList[0], $browseType[3])) && p() && e('0');  // 查询用户可以看到的admin involved的空间数量
r($kanban->getSpacePairsTest($userList[1], $browseType[0])) && p() && e('1');  // 查询用户可以看到的po16 private的空间数量
r($kanban->getSpacePairsTest($userList[1], $browseType[1])) && p() && e('0');  // 查询用户可以看到的po16 cooperation的空间数量
r($kanban->getSpacePairsTest($userList[1], $browseType[2])) && p() && e('16'); // 查询用户可以看到的po16 public的空间数量
r($kanban->getSpacePairsTest($userList[1], $browseType[3])) && p() && e('0');  // 查询用户可以看到的po16 involved的空间数量
r($kanban->getSpacePairsTest($userList[2], $browseType[0])) && p() && e('1');  // 查询用户可以看到的pm3 private的空间数量
r($kanban->getSpacePairsTest($userList[2], $browseType[1])) && p() && e('0');  // 查询用户可以看到的test37 cooperation的空间数量
r($kanban->getSpacePairsTest($userList[2], $browseType[2])) && p() && e('16'); // 查询用户可以看到的test37 public的空间数量
r($kanban->getSpacePairsTest($userList[2], $browseType[3])) && p() && e('0');  // 查询用户可以看到的test37 involved的空间数量
r($kanban->getSpacePairsTest($userList[3], $browseType[0])) && p() && e('1');  // 查询用户可以看到的user25 private的空间数量
r($kanban->getSpacePairsTest($userList[3], $browseType[1])) && p() && e('0');  // 查询用户可以看到的user25 cooperation的空间数量
r($kanban->getSpacePairsTest($userList[3], $browseType[2])) && p() && e('16'); // 查询用户可以看到的user25 public的空间数量
r($kanban->getSpacePairsTest($userList[3], $browseType[3])) && p() && e('0');  // 查询用户可以看到的user25 involved的空间数量