#!/usr/bin/env php
<?php
/**

title=count_of_doing_program
timeout=0
cid=1

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('program', true, 4)->gen(356, true, false);

$metric = new metricTest();
$calc = $metric->calcMetric(__FILE__);

r($calc->getResult()) && p('0:value') && e('45'); // 测试进行中的项目集数量