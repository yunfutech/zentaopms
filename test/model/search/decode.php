#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/search.class.php';
su('admin');

/**

title=测试 searchModel->decode();
cid=1
pid=1

测试将搜索字转化为正确的汉字 >> 产

*/

$search = new searchTest();

r($search->decodeTest(20135)) && p() && e('产'); //测试将搜索字转化为正确的汉字