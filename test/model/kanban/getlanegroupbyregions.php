#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->getLaneGroupByRegions();
cid=1
pid=1

测试获取区域101,102,103的泳道 >> 9
测试获取区域101,102,103的bug泳道 >> 3
测试获取区域101,102,103的task泳道 >> 3
测试获取区域101,102,103的story泳道 >> 3
测试获取区域104,105,106的泳道 >> 9
测试获取区域104,105,106的bug泳道 >> 3
测试获取区域104,105,106的task泳道 >> 3
测试获取区域104,105,106的story泳道 >> 3
测试获取区域107,108,109的泳道 >> 9
测试获取区域107,108,109的bug泳道 >> 3
测试获取区域107,108,109的task泳道 >> 3
测试获取区域107,108,109的story泳道 >> 3
测试获取区域110,111,112的泳道 >> 9
测试获取区域110,111,112的bug泳道 >> 3
测试获取区域110,111,112的task泳道 >> 3
测试获取区域110,111,112的story泳道 >> 3
测试获取区域113,114,115的泳道 >> 9
测试获取区域113,114,115的bug泳道 >> 3
测试获取区域113,114,115的task泳道 >> 3
测试获取区域113,114,115的story泳道 >> 3
测试获取不存在的区域的泳道 >> 0
测试获取不存在的区域的bug泳道 >> 0
测试获取不存在的区域的task泳道 >> 0
测试获取不存在的区域的story泳道 >> 0

*/
$regionsList = array('101,102,103', '104,105,106', '107,108,109', '110,111,112', '113,114,115', '1000001');
$type        = array('bug', 'task', 'story');

$kanban = new kanbanTest();

r($kanban->getLaneGroupByRegionsTest($regionsList[0]))           && p() && e('9'); // 测试获取区域101,102,103的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[0], $type[0])) && p() && e('3'); // 测试获取区域101,102,103的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[0], $type[1])) && p() && e('3'); // 测试获取区域101,102,103的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[0], $type[2])) && p() && e('3'); // 测试获取区域101,102,103的story泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[1]))           && p() && e('9'); // 测试获取区域104,105,106的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[1], $type[0])) && p() && e('3'); // 测试获取区域104,105,106的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[1], $type[1])) && p() && e('3'); // 测试获取区域104,105,106的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[1], $type[2])) && p() && e('3'); // 测试获取区域104,105,106的story泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[2]))           && p() && e('9'); // 测试获取区域107,108,109的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[2], $type[0])) && p() && e('3'); // 测试获取区域107,108,109的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[2], $type[1])) && p() && e('3'); // 测试获取区域107,108,109的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[2], $type[2])) && p() && e('3'); // 测试获取区域107,108,109的story泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[3]))           && p() && e('9'); // 测试获取区域110,111,112的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[3], $type[0])) && p() && e('3'); // 测试获取区域110,111,112的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[3], $type[1])) && p() && e('3'); // 测试获取区域110,111,112的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[3], $type[2])) && p() && e('3'); // 测试获取区域110,111,112的story泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[4]))           && p() && e('9'); // 测试获取区域113,114,115的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[4], $type[0])) && p() && e('3'); // 测试获取区域113,114,115的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[4], $type[1])) && p() && e('3'); // 测试获取区域113,114,115的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[4], $type[2])) && p() && e('3'); // 测试获取区域113,114,115的story泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[5]))           && p() && e('0'); // 测试获取不存在的区域的泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[5], $type[0])) && p() && e('0'); // 测试获取不存在的区域的bug泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[5], $type[1])) && p() && e('0'); // 测试获取不存在的区域的task泳道
r($kanban->getLaneGroupByRegionsTest($regionsList[5], $type[2])) && p() && e('0'); // 测试获取不存在的区域的story泳道