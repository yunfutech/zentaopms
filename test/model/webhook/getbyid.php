#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/webhook.class.php';
su('admin');

/**

title=测试 webhookModel->getByID();
cid=1
pid=1

通过ID查创建人 >> admin
通过ID查名字 >> 钉钉群机器人
查ID为2的创建人 >> admin
查ID为2的名字 >> 钉钉工作消息
传入不存在的情况 >> 0

*/

$webhook = new webhookTest();

$ID = array();
$ID[0] = 1;
$ID[1] = 2;
$ID[2] = 1111;

$result1 = $webhook->getByIDTest($ID[0]);
$result2 = $webhook->getByIDTest($ID[1]);
$result3 = $webhook->getByIDTest($ID[2]);

r($result1) && p('createdBy')   && e('admin');        //通过ID查创建人
r($result1) && p('name')        && e('钉钉群机器人'); //通过ID查名字
r($result2) && p('createdBy')   && e('admin');        //查ID为2的创建人
r($result2) && p('name')        && e('钉钉工作消息'); //查ID为2的名字
r($result3) && p('')            && e('0');            //传入不存在的情况