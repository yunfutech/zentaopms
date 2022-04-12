#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/program.class.php';

/**

title=测试 programModel::update();
cid=1
pid=1

更新id为10的项目集信息 >> 测试更新项目集十
当计划开始为空时更新项目集信息 >> 『计划开始』不能为空。
当计划完成为空时更新项目集信息 >> 『计划完成』不能为空。
当计划完成小于计划开始时 >> 『计划完成』应当大于『2020-10-10』。
项目集名称已经存在时 >> 『项目集名称』已经有『项目集1』这条记录了。如果您确定该记录已删除，请到后台-系统-数据-回收站还原。
项目集开始时间小于父项目集时 >> 父项目集的开始日期：2019-09-09，开始日期不能小于父项目集的开始日期;父项目集的完成日期：2019-09-09，完成日期不能大于父项目集的完成日期

*/

$t = new Program('admin');

r($t->updateProgram(10))    && p('message:end')               && e('测试更新项目集十'); // 更新id为10的项目集信息
r($t->updateProgram(10, 2)) && p('message[begin]:0')          && e('『计划开始』不能为空。'); //当计划开始为空时更新项目集信息
r($t->updateProgram(10, 3)) && p('message[end]:0')            && e('『计划完成』不能为空。'); //当计划完成为空时更新项目集信息
r($t->updateProgram(10, 4)) && p('message[end]:0')            && e('『计划完成』应当大于『2020-10-10』。'); //当计划完成小于计划开始时
r($t->updateProgram(10, 1)) && p('message[name]:0')           && e('『项目集名称』已经有『项目集1』这条记录了。如果您确定该记录已删除，请到后台-系统-数据-回收站还原。'); //项目集名称已经存在时
r($t->updateProgram(10, 5)) && p('message:begin;message:end') && e('父项目集的开始日期：2019-09-09，开始日期不能小于父项目集的开始日期;父项目集的完成日期：2019-09-09，完成日期不能大于父项目集的完成日期');// 项目集开始时间小于父项目集时
system("./ztest init");