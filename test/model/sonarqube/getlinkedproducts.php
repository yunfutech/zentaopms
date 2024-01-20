#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试 sonarqubeModel::getLinkedProducts();
cid=1
pid=1

通过错误的sonarqubeID和projectKey获取关联产品ID >> return false
通过正确的sonarqubeID和projectKey获取关联产品ID >> 1

*/

$sonarqube = $tester->loadModel('sonarqube');

$sonarqubeID = 0;
$projectKey  = '';
$result      = $sonarqube->getLinkedProducts($sonarqubeID, $projectKey);
if(empty($result)) $result = 'return false';
r($result) && p() && e('return false'); //通过错误的sonarqubeID和projectKey获取关联产品ID

$sonarqubeID = 2;
$projectKey  = 'zentaopms';
$result      = $sonarqube->getLinkedProducts($sonarqubeID, $projectKey);
r($result) && p() && e('1'); //通过正确的sonarqubeID和projectKey获取关联产品ID