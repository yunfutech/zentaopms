#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/user.class.php';
su('admin');

/**

title=测试 userModel->checkPassword();
cid=1
pid=1

正常的用户密码 >> 无报错
两次密码不相同的情况 >> 两次密码应该相同。
密码强度小于系统设定 >> 您的密码强度小于系统设定。
使用常见简单密码，给出错误提示 >> 密码不能使用【123456,password,12345,12345678,qwerty,123456789,1234,1234567,abc123,111111,123123】这些常用弱口令。

*/

$user = new userTest();
$normalUser = array();
$normalUser['password1']        = 'Adsd@#!%qaz';
$normalUser['password2']        = 'Adsd@#!%qaz';
$normalUser['passwordStrength'] = 1;

$weakPassword = $normalUser;
$weakPassword['passwordStrength'] = '0';

$differentPassword = $normalUser;
$differentPassword['password2'] = '!@#!@#asfasf';

$simplePassword = $normalUser;
$simplePassword['password1'] = '123456';
$simplePassword['password2'] = '123456';

r($user->checkPasswordTest($normalUser))         && p('password')    && e('无报错');                     //正常的用户密码
r($user->checkPasswordTest($differentPassword))  && p('password:0')  && e('两次密码应该相同。');         //两次密码不相同的情况
r($user->checkPasswordTest($weakPassword))       && p('password1:0') && e('您的密码强度小于系统设定。'); //密码强度小于系统设定
r($user->checkPasswordTest($simplePassword))     && p('password1:0') && e('密码不能使用【123456,password,12345,12345678,qwerty,123456789,1234,1234567,abc123,111111,123123】这些常用弱口令。'); //使用常见简单密码，给出错误提示
system("./ztest init");
