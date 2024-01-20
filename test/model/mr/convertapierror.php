#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试 mrModel::convertApiError();
cid=0
pid=0

使用正确的message >> success
使用空的message >> success

*/
$mrModel = $tester->loadModel('mr');

$message = array();
$message[0] = 'Another open merge request already exists for this source branch: !11';
$result = $mrModel->convertApiError($message);
if($result != $message[0]) $result = 'success';
r($result) && p('') && e('success'); //使用正确的message

$message = '';
$result = $mrModel->convertApiError($message);
if(empty($result)) $result = 'success';
r($result) && p('') && e('success'); //使用空的message