#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getFromDB();
cid=1
pid=1

查询项目为已挂起、日期不为空的数据 >> 1,41
查询项目为已挂起、日期为空的数据 >> 0
查询项目为已关闭、日期不为空的数据 >> 2,42
查询项目为已关闭、日期为空的数据 >> 0
查询项目为未开始、日期不为空的数据 >> 3,43
查询项目为未开始、日期为空的数据 >> 0
查询项目为进行中、日期不为空的数据 >> 5,45
查询项目为金箱子、日期为空的数据 >> 0

*/
$productID = array('41', '42', '43', '45');
$date      = array('2022-05-03', '');

$weekly = new weeklyTest();
r($weekly->getFromDBTest($productID[0], $date[0])) && p('id,project') && e('1,41'); //查询项目为已挂起、日期不为空的数据
r($weekly->getFromDBTest($productID[0], $date[1])) && p() && e('0');                //查询项目为已挂起、日期为空的数据
r($weekly->getFromDBTest($productID[1], $date[0])) && p('id,project') && e('2,42'); //查询项目为已关闭、日期不为空的数据
r($weekly->getFromDBTest($productID[1], $date[1])) && p() && e('0');                //查询项目为已关闭、日期为空的数据
r($weekly->getFromDBTest($productID[2], $date[0])) && p('id,project') && e('3,43'); //查询项目为未开始、日期不为空的数据
r($weekly->getFromDBTest($productID[2], $date[1])) && p() && e('0');                //查询项目为未开始、日期为空的数据
r($weekly->getFromDBTest($productID[3], $date[0])) && p('id,project') && e('5,45'); //查询项目为进行中、日期不为空的数据
r($weekly->getFromDBTest($productID[3], $date[1])) && p() && e('0');                //查询项目为金箱子、日期为空的数据