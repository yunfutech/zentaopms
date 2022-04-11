#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试 gitlabModel::apiGetCurrentUser();
cid=1
pid=1

*/

$gitlab = $tester->loadModel('gitlab');

$host      = '';
$token     = '';
$rootCheck = false;

$result = $gitlab->apiGetCurrentUser($host, $token, $rootCheck);
if($result === false) $result = 'return false';
r($result) && p() && e('return false'); //使用空的数据获取用户信息

$host   = 'http://192.168.1.161:5108';
$result = $gitlab->apiGetCurrentUser($host, $token, $rootCheck);
if(!$result) $result = 'return null';
r($result) && p() && e('return null'); //使用错误的host获取用户信息

$host   = 'http://192.168.1.161:51080';
$result = $gitlab->apiGetCurrentUser($host, $token, $rootCheck);
r($gitlab->apiGetCurrentUser($host, $token, $rootCheck)) && p('message') && e('401 Unauthorized'); //使用正确的host,错误的token获取用户信息

$token  = 'x88fZokrp5hShia2jyBN';
$result = $gitlab->apiGetCurrentUser($host, $token, $rootCheck);
r($result) && p('id') && e('1'); //通过host,token获取用户信息

$token     = 'wVFHE6NZA-cJy-3U2y2J';
$rootCheck = true;
$result    = $gitlab->apiGetCurrentUser($host, $token, $rootCheck);
r($result) && p('error') && e('insufficient_scope'); //通过host,权限不足的token获取用户信息
