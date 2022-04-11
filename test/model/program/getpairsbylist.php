#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/program.class.php';

/**

title=测试 programModel::getPairsByList();
cid=1
pid=1

通过字符串'1'获取项目集名称 >> 项目集1
通过字符串'1,2,3'获取项目集名称 >> 项目集1,项目集2,项目集3
通过数组array(1)获取项目集名称 >> 项目集1
通过数组array(1,2,3)获取项目集名称 >> 项目集1,项目集2,项目集3
通过id为空获取项目集名称 >> Not Found
通过id=0获取项目集名称 >> Not Found
通过id=11获取项目集名称 >> Not Found

*/

$itemsetsName = new Program('admin');

$t_getProName = array('1', '1,2,3', array(1), array(1,2,3), '', '0', '11');

r($itemsetsName->getPairsByList($t_getProName[0])) && p('1')       && e('项目集1');                 // 通过字符串'1'获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[1])) && p('1,2,3')   && e('项目集1,项目集2,项目集3'); // 通过字符串'1,2,3'获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[2])) && p('1')       && e('项目集1');                 //通过数组array(1)获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[3])) && p('1,2,3')   && e('项目集1,项目集2,项目集3'); //通过数组array(1,2,3)获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[4])) && p('message') && e('Not Found');               // 通过id为空获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[5])) && p('message') && e('Not Found');               // 通过id=0获取项目集名称
r($itemsetsName->getPairsByList($t_getProName[6])) && p('message') && e('Not Found');               // 通过id=11获取项目集名称