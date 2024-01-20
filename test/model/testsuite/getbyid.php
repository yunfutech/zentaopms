#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/testsuite.class.php';
su('admin');

/**

title=测试 testsuiteModel->getById();
cid=1
pid=1

测试suiteID值为0,setImgSize值为false >> 0
测试suiteID值为1000,setImgSize值为false >> 0
测试suiteID值为1,setImgSize值为true >> 1,这是测试套件的描述1
测试suiteID值为1,setImgSize值为false >> 1,这是测试套件的描述1

*/
$suiteID    = array(0, 1, 1000);
$setImgSize = array(true, false);

$testsuite = new testsuiteTest();

r($testsuite->getByIdTest($suiteID[0], $setImgSize[1])) && p() && e('0');                               //测试suiteID值为0,setImgSize值为false
r($testsuite->getByIdTest($suiteID[2], $setImgSize[1])) && p() && e('0');                               //测试suiteID值为1000,setImgSize值为false
r($testsuite->getByIdTest($suiteID[1], $setImgSize[0])) && p('id,desc') && e('1,这是测试套件的描述1');  //测试suiteID值为1,setImgSize值为true
r($testsuite->getByIdTest($suiteID[1], $setImgSize[1])) && p('id,desc') && e('1,这是测试套件的描述1');  //测试suiteID值为1,setImgSize值为false