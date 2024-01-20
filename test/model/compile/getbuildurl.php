#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/compile.class.php';
su('admin');

/**

title=测试 compileModel->getBuildUrl();
cid=1
pid=1

检测password为空时获取的信息 >> 123456:zxd
检测token为空时获取的信息 >> pms.cc.cc/job/11/buildWithParameters/api/json
检测jenkins为空时获取的信息 >> /job//buildWithParameters/api/json
检测jenkins为空时获取的信息 >> :

*/
$jenkins1 = new stdclass();
$jenkins1->url      = 'pms.cc.cc'; 
$jenkins1->account  = '123456';
$jenkins1->token    = 'zxd';
$jenkins1->pipeline = '12';

$jenkins2 = new stdclass();
$jenkins2->url      = 'pms.cc.cc'; 
$jenkins2->account  = '123456';
$jenkins2->password = '8bb44ffbc4b42fcbb3152cc05fd21c67';
$jenkins2->token    = '';
$jenkins2->pipeline = '11';

$jenkins3 = new stdclass();
$jenkins3->url      = '';
$jenkins3->account  = '';
$jenkins3->password = '';
$jenkins3->token    = '';
$jenkins3->pipeline = '';

$compile = new compileTest();

r($compile->getBuildUrlTest($jenkins1)) && p('userPWD') && e('123456:zxd');                                    //检测password为空时获取的信息
r($compile->getBuildUrlTest($jenkins2)) && p('url')     && e('pms.cc.cc/job/11/buildWithParameters/api/json'); //检测token为空时获取的信息
r($compile->getBuildUrlTest($jenkins3)) && p('url')     && e('/job//buildWithParameters/api/json');            //检测jenkins为空时获取的信息
r($compile->getBuildUrlTest($jenkins3)) && p('userPWD') && e(':');                                             //检测jenkins为空时获取的信息