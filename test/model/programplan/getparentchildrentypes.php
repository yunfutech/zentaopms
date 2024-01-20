#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/programplan.class.php';
su('admin');

$programplan = zdTable('project');
$programplan->id->range('10-20');
$programplan->parent->range('1-3');
$programplan->type->range('stage{3},sprint{2},kanban{2},stage{1},sprint{2}');
$programplan->deleted->range('0-1');
$programplan->gen(10);

/**

title=测试 programplanModel->getParentChildrenTypes();
cid=1
pid=1

查找父ID为 1 未删除的阶段类型 stage >> stage
查找父ID为 1 未删除的阶段类型 kanban >> kanban
查找父ID为 1 未删除的阶段类型数量 >> 2
查找父ID为 2 未删除的阶段类型 sprint >> sprint
查找父ID为 2 未删除的阶段类型数量 >> 1
查找父ID为 3 未删除的阶段类型 stage >> stage
查找父ID为 3 未删除的阶段类型 sprint >> sprint
查找父ID为 3 未删除的阶段类型数量 >> 2
查找父ID为 1 未删除的阶段类型 stage >> 0
查找父ID为 1 未删除的阶段类型 sprint >> 0
查找父ID为 1 未删除的阶段类型 kanban >> 0
查找父ID为 1 未删除的阶段类型数量 >> 0

*/

$parentIDList = array(1, 2, 3, 4);

$programplan = new programplanTest();

r($programplan->getParentChildrenTypesTest($parentIDList[0]))        && p('stage')  && e('stage');  // 查找父ID为 1 未删除的阶段类型 stage
r($programplan->getParentChildrenTypesTest($parentIDList[0]))        && p('kanban') && e('kanban'); // 查找父ID为 1 未删除的阶段类型 kanban
r(count($programplan->getParentChildrenTypesTest($parentIDList[0]))) && p()         && e('2');      // 查找父ID为 1 未删除的阶段类型数量
r($programplan->getParentChildrenTypesTest($parentIDList[1]))        && p('sprint') && e('sprint'); // 查找父ID为 2 未删除的阶段类型 sprint
r(count($programplan->getParentChildrenTypesTest($parentIDList[1]))) && p()         && e('1');      // 查找父ID为 2 未删除的阶段类型数量
r($programplan->getParentChildrenTypesTest($parentIDList[2]))        && p('stage')  && e('stage');  // 查找父ID为 3 未删除的阶段类型 stage
r($programplan->getParentChildrenTypesTest($parentIDList[2]))        && p('sprint') && e('sprint'); // 查找父ID为 3 未删除的阶段类型 sprint
r(count($programplan->getParentChildrenTypesTest($parentIDList[2]))) && p()         && e('2');      // 查找父ID为 3 未删除的阶段类型数量
r($programplan->getParentChildrenTypesTest($parentIDList[3]))        && p('stage')  && e('0');      // 查找父ID为 1 未删除的阶段类型 stage
r($programplan->getParentChildrenTypesTest($parentIDList[3]))        && p('sprint') && e('0');      // 查找父ID为 1 未删除的阶段类型 sprint
r($programplan->getParentChildrenTypesTest($parentIDList[3]))        && p('kanban') && e('0');      // 查找父ID为 1 未删除的阶段类型 kanban
r(count($programplan->getParentChildrenTypesTest($parentIDList[3]))) && p()         && e('0');      // 查找父ID为 1 未删除的阶段类型数量
