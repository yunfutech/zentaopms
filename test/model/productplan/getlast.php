#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/productplan.class.php';

$productplan = new Productplan('admin');

$getLast = array();
$getLast[0] = 1;
$getLast[1] = 2;
$getLast[2] = 200;

r($productplan->getLast($getLast[0])) && p('id') && e('3'); //获取id为1的最后一个产品
r($productplan->getLast($getLast[1])) && p('id') && e('6'); //获取id为2的最后一个产品
r($productplan->getLast($getLast[2])) && p('id') && e('0'); //获取不存在的id返回布尔值
?>
