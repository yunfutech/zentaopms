#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/webhook.class.php';
su('admin');

/**

title=测试 webhookModel->getBindAccount();
cid=1
pid=1

按条件查出id=1的关联用户 >> user3
按条件查出id=空时，关联的用户 >> 0

*/

$webhook = new webhookTest();

$ID     = array();
$ID[0]     = 1;
$ID[1]     = 2;
$ID[2]     = '';

$type   = array();
$type[0]   = 'gitlab';
$type[1]   = '';

$openID = array();
$openID[0] = 1;
$openID[1] = '';

$result1 = $webhook->getBindAccountTest($ID[0], $type[0], $openID[0]);
$result2 = $webhook->getBindAccountTest($ID[1], $type[1], $openID[1]);

r($result1) && p() && e('user3'); //按条件查出id=1的关联用户
r($result2) && p() && e('0');     //按条件查出id=空时，关联的用户