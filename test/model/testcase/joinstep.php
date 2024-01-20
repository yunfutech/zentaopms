#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/testcase.class.php';
su('admin');

/**

title=测试 testcaseModel->joinStep();
cid=1
pid=1

测试获取步骤 1 2 的字符串 >> 用例步骤描述1 EXPECT:这是用例预期结果1 用例步骤描述2 EXPECT:这是用例预期结果2
测试获取步骤 3 4 的字符串 >> 用例步骤描述3 EXPECT:这是用例预期结果3 用例步骤描述4 EXPECT:这是用例预期结果4
测试获取步骤 5 6 的字符串 >> 用例步骤描述5 EXPECT:这是用例预期结果5 用例步骤描述6 EXPECT:这是用例预期结果6
测试获取步骤 7 8 的字符串 >> 用例步骤描述7 EXPECT:这是用例预期结果7 用例步骤描述8 EXPECT:这是用例预期结果8
测试获取步骤 9 10 的字符串 >> 用例步骤描述9 EXPECT:这是用例预期结果9 用例步骤描述10 EXPECT:这是用例预期结果10

*/

$stepIDList = array('1,2', '3,4', '5,6', '7,8', '9,10');

$testcase = new testcaseTest();

r($testcase->joinStepTest($stepIDList[0])) && p() && e('用例步骤描述1 EXPECT:这是用例预期结果1 用例步骤描述2 EXPECT:这是用例预期结果2');   // 测试获取步骤 1 2 的字符串
r($testcase->joinStepTest($stepIDList[1])) && p() && e('用例步骤描述3 EXPECT:这是用例预期结果3 用例步骤描述4 EXPECT:这是用例预期结果4');   // 测试获取步骤 3 4 的字符串
r($testcase->joinStepTest($stepIDList[2])) && p() && e('用例步骤描述5 EXPECT:这是用例预期结果5 用例步骤描述6 EXPECT:这是用例预期结果6');   // 测试获取步骤 5 6 的字符串
r($testcase->joinStepTest($stepIDList[3])) && p() && e('用例步骤描述7 EXPECT:这是用例预期结果7 用例步骤描述8 EXPECT:这是用例预期结果8');   // 测试获取步骤 7 8 的字符串
r($testcase->joinStepTest($stepIDList[4])) && p() && e('用例步骤描述9 EXPECT:这是用例预期结果9 用例步骤描述10 EXPECT:这是用例预期结果10'); // 测试获取步骤 9 10 的字符串