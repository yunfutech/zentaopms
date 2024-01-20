#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->createDefaultColumns();
cid=1
pid=1

创建region 1 group 1的默认泳道列 >> 8
创建region 2 group 2的默认泳道列 >> 8
创建region 3 group 3的默认泳道列 >> 8
创建region 4 group 4的默认泳道列 >> 8
创建region 5 group 5的默认泳道列 >> 8
重复创建region 1 group 1的默认泳道列 >> 12

*/

$kanban = new kanbanTest();

$regionIDList = array('1', '2', '3', '4', '5');
$groupIDList  = array('1', '2', '3', '4', '5');

r($kanban->createDefaultColumnsTest($regionIDList[0], $groupIDList[0])) && p() && e('8');  // 创建region 1 group 1的默认泳道列
r($kanban->createDefaultColumnsTest($regionIDList[1], $groupIDList[1])) && p() && e('8');  // 创建region 2 group 2的默认泳道列
r($kanban->createDefaultColumnsTest($regionIDList[2], $groupIDList[2])) && p() && e('8');  // 创建region 3 group 3的默认泳道列
r($kanban->createDefaultColumnsTest($regionIDList[3], $groupIDList[3])) && p() && e('8');  // 创建region 4 group 4的默认泳道列
r($kanban->createDefaultColumnsTest($regionIDList[4], $groupIDList[4])) && p() && e('8');  // 创建region 5 group 5的默认泳道列
r($kanban->createDefaultColumnsTest($regionIDList[0], $groupIDList[0])) && p() && e('12'); // 重复创建region 1 group 1的默认泳道列
