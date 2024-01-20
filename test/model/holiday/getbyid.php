#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/holiday.class.php';
su('admin');

/**

title=测试 holidayModel->getById();
cid=1
pid=1

查询id为1的holiday >> 这是一个节假日1,holiday,这个是节假日的描述1
查询id为5的holiday >> 这是一个节假日5,holiday,这个是节假日的描述5
查询id为10的holiday >> 这是一个节假日10,working,这个是节假日的描述10
查询不存在的holiday >> Object not found

*/
$holidayIDList = array('1', '5', '10', '1001');

$holiday = new holidayTest();

r($holiday->getByIdTest($holidayIDList[0])) && p('name,type,desc') && e('这是一个节假日1,holiday,这个是节假日的描述1'); //查询id为1的holiday
r($holiday->getByIdTest($holidayIDList[1])) && p('name,type,desc') && e('这是一个节假日5,holiday,这个是节假日的描述5'); //查询id为5的holiday
r($holiday->getByIdTest($holidayIDList[2])) && p('name,type,desc') && e('这是一个节假日10,working,这个是节假日的描述10'); //查询id为10的holiday
r($holiday->getByIdTest($holidayIDList[3])) && p() && e('Object not found'); //查询不存在的holiday