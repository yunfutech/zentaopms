#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/dev.class.php';

/**

title=测试 devModel::parseCommonLang();
cid=1
pid=1

解析空字符串 >> null
解析产品列表字符串 >> 产品列表
解析$PRODUCTCOMMON列表字符串 >> 列表
解析$PROJECTCOMMON列表字符串 >> 列表
解析$EXECUTIONCOMMON列表字符串 >> 列表
解析$URCOMMON列表字符串 >> 列表
解析$SRCOMMON列表字符串 >> 列表

*/

$devTester = new devTest();
r($devTester->parseCommonLangTest(''))                     && p()  && e("null");     // 解析空字符串
r($devTester->parseCommonLangTest('产品列表'))             && p()  && e("产品列表"); // 解析产品列表字符串
r($devTester->parseCommonLangTest('$PRODUCTCOMMON列表'))   && p(1) && e('列表');     // 解析$PRODUCTCOMMON列表字符串
r($devTester->parseCommonLangTest('$PROJECTCOMMON列表'))   && p(1) && e('列表');     // 解析$PROJECTCOMMON列表字符串
r($devTester->parseCommonLangTest('$EXECUTIONCOMMON列表')) && p(1) && e('列表');     // 解析$EXECUTIONCOMMON列表字符串
r($devTester->parseCommonLangTest('$URCOMMON列表'))        && p(1) && e('列表');     // 解析$URCOMMON列表字符串
r($devTester->parseCommonLangTest('$SRCOMMON列表'))        && p(1) && e('列表');     // 解析$SRCOMMON列表字符串
