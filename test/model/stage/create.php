#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/stage.class.php';
su('admin');

zdTable('stage')->gen(0);

/**

title=测试 stageModel->create();
cid=1
pid=1

测试新建瀑布项目一个需求阶段 >> 新建的需求,1,request
测试新建瀑布项目一个设计阶段 >> 新建的设计,2,design
测试新建瀑布项目一个开发阶段 >> 新建的开发,3,dev
测试新建瀑布项目一个测试阶段 >> 新建的测试,4,qa
测试新建瀑布项目一个发布阶段 >> 新建的发布,5,release
测试新建瀑布项目一个总结评审阶段 >> 新建的总结评审,6,review
测试新建瀑布项目一个其他阶段 >> 新建的其他,7,other
测试新建瀑布项目名称为空 >> 『阶段名称』不能为空。
测试新建瀑布项目类型为空 >> 『工作量占比』不能为空。
测试新建瀑布项目百分比为空 >> 『阶段类型』不能为空。
测试新建瀑布项目非数字的百分比 >> 工作量占比应当是数字
测试新建瀑布项目总百分比超过100 >> 工作量占比累计不应当超过100%
测试新建融合瀑布项目一个需求阶段 >> 新建的需求,1,request
测试新建融合瀑布项目一个设计阶段 >> 新建的设计,2,design
测试新建融合瀑布项目一个开发阶段 >> 新建的开发,3,dev
测试新建融合瀑布项目一个测试阶段 >> 新建的测试,4,qa
测试新建融合瀑布项目一个发布阶段 >> 新建的发布,5,release
测试新建融合瀑布项目一个总结评审阶段 >> 新建的总结评审,6,review
测试新建融合瀑布项目一个其他阶段 >> 新建的其他,7,other
测试新建融合瀑布项目名称为空 >> 『阶段名称』不能为空。
测试新建融合瀑布项目类型为空 >> 『工作量占比』不能为空。
测试新建融合瀑布项目百分比为空 >> 『阶段类型』不能为空。
测试新建融合瀑布项目非数字的百分比 >> 工作量占比应当是数字
测试新建融合瀑布项目总百分比超过100 >> 工作量占比累计不应当超过100%

*/

$stage1 = new stdclass();
$stage1->name    = '新建的需求';
$stage1->percent = 1;
$stage1->type    = 'request';

$stage2 = new stdclass();
$stage2->name    = '新建的设计';
$stage2->percent = 2;
$stage2->type    = 'design';

$stage3 = new stdclass();
$stage3->name    = '新建的开发';
$stage3->percent = 3;
$stage3->type    = 'dev';

$stage4 = new stdclass();
$stage4->name    = '新建的测试';
$stage4->percent = 4;
$stage4->type    = 'qa';

$stage5 = new stdclass();
$stage5->name    = '新建的发布';
$stage5->percent = 5;
$stage5->type    = 'release';

$stage6 = new stdclass();
$stage6->name    = '新建的总结评审';
$stage6->percent = 6;
$stage6->type    = 'review';

$stage7 = new stdclass();
$stage7->name    = '新建的其他';
$stage7->percent = 7;
$stage7->type    = 'other';

$emptyName = new stdclass();
$emptyName->name    = '';
$emptyName->percent = 7;
$emptyName->type    = 'other';

$emptyPercent = new stdclass();
$emptyPercent->name    = '1';
$emptyPercent->percent = 0;
$emptyPercent->type    = 'other';

$emptyType = new stdclass();
$emptyType->name    = '1';
$emptyType->percent = 7;
$emptyType->type    = '';

$IllegalPercent = new stdclass();
$IllegalPercent->name    = '一个名称';
$IllegalPercent->percent = 'a';
$IllegalPercent->type    = 'other';

$percentOver = new stdclass();
$percentOver->name    = '一个名称';
$percentOver->percent = 101;
$percentOver->type    = 'other';

$stage = new stageTest();

r($stage->createTest($stage1))                          && p('name,percent,type') && e('新建的需求,1,request');         // 测试新建瀑布项目一个需求阶段
r($stage->createTest($stage2))                          && p('name,percent,type') && e('新建的设计,2,design');          // 测试新建瀑布项目一个设计阶段
r($stage->createTest($stage3))                          && p('name,percent,type') && e('新建的开发,3,dev');             // 测试新建瀑布项目一个开发阶段
r($stage->createTest($stage4))                          && p('name,percent,type') && e('新建的测试,4,qa');              // 测试新建瀑布项目一个测试阶段
r($stage->createTest($stage5))                          && p('name,percent,type') && e('新建的发布,5,release');         // 测试新建瀑布项目一个发布阶段
r($stage->createTest($stage6))                          && p('name,percent,type') && e('新建的总结评审,6,review');      // 测试新建瀑布项目一个总结评审阶段
r($stage->createTest($stage7))                          && p('name,percent,type') && e('新建的其他,7,other');           // 测试新建瀑布项目一个其他阶段
r($stage->createTest($emptyName))                       && p('name:0')            && e('『阶段名称』不能为空。');       // 测试新建瀑布项目名称为空
r($stage->createTest($emptyPercent))                    && p('percent:0')         && e('『工作量占比』不能为空。');     // 测试新建瀑布项目类型为空
r($stage->createTest($emptyType))                       && p('type:0')            && e('『阶段类型』不能为空。');       // 测试新建瀑布项目百分比为空
r($stage->createTest($IllegalPercent))                  && p('message:0')         && e('工作量占比应当是数字');         // 测试新建瀑布项目非数字的百分比
r($stage->createTest($percentOver))                     && p('message:0')         && e('工作量占比累计不应当超过100%'); // 测试新建瀑布项目总百分比超过100
r($stage->createTest($stage1, 'waterfallplus'))         && p('name,percent,type') && e('新建的需求,1,request');         // 测试新建融合瀑布项目一个需求阶段
r($stage->createTest($stage2, 'waterfallplus'))         && p('name,percent,type') && e('新建的设计,2,design');          // 测试新建融合瀑布项目一个设计阶段
r($stage->createTest($stage3, 'waterfallplus'))         && p('name,percent,type') && e('新建的开发,3,dev');             // 测试新建融合瀑布项目一个开发阶段
r($stage->createTest($stage4, 'waterfallplus'))         && p('name,percent,type') && e('新建的测试,4,qa');              // 测试新建融合瀑布项目一个测试阶段
r($stage->createTest($stage5, 'waterfallplus'))         && p('name,percent,type') && e('新建的发布,5,release');         // 测试新建融合瀑布项目一个发布阶段
r($stage->createTest($stage6, 'waterfallplus'))         && p('name,percent,type') && e('新建的总结评审,6,review');      // 测试新建融合瀑布项目一个总结评审阶段
r($stage->createTest($stage7, 'waterfallplus'))         && p('name,percent,type') && e('新建的其他,7,other');           // 测试新建融合瀑布项目一个其他阶段
r($stage->createTest($emptyName, 'waterfallplus'))      && p('name:0')            && e('『阶段名称』不能为空。');       // 测试新建融合瀑布项目名称为空
r($stage->createTest($emptyPercent, 'waterfallplus'))   && p('percent:0')         && e('『工作量占比』不能为空。');     // 测试新建融合瀑布项目类型为空
r($stage->createTest($emptyType, 'waterfallplus'))      && p('type:0')            && e('『阶段类型』不能为空。');       // 测试新建融合瀑布项目百分比为空
r($stage->createTest($IllegalPercent, 'waterfallplus')) && p('message:0')         && e('工作量占比应当是数字');         // 测试新建融合瀑布项目非数字的百分比
r($stage->createTest($percentOver, 'waterfallplus'))    && p('message:0')         && e('工作量占比累计不应当超过100%'); // 测试新建融合瀑布项目总百分比超过100
