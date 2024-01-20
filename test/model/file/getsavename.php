#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/file.class.php';
su('admin');

/**

title=测试 fileModel->getSaveName();
cid=1
pid=1

获取 202205/1.txt 的实际存储名称 >> 202205/1
获取 202204/2.png 的实际存储名称 >> 202204/2
获取 202203/3.mp4 的实际存储名称 >> 202203/3
获取 202202/4.zip 的实际存储名称 >> 202202/4
获取 202201/5 的实际存储名称 >> 202201/5

*/

$pathnames = array('202205/1.txt', '202204/2.png', '202203/3.mp4', '202202/4.zip', '202201/5');

$file = new fileTest();

r($file->getSaveNameTest($pathnames[0])) && p() && e('202205/1'); // 获取 202205/1.txt 的实际存储名称
r($file->getSaveNameTest($pathnames[1])) && p() && e('202204/2'); // 获取 202204/2.png 的实际存储名称
r($file->getSaveNameTest($pathnames[2])) && p() && e('202203/3'); // 获取 202203/3.mp4 的实际存储名称
r($file->getSaveNameTest($pathnames[3])) && p() && e('202202/4'); // 获取 202202/4.zip 的实际存储名称
r($file->getSaveNameTest($pathnames[4])) && p() && e('202201/5'); // 获取 202201/5 的实际存储名称