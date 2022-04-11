#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/bug.class.php';
su('admin');

/**

title=bugModel->confirm();
cid=1
pid=1

确认指派人变化的bug >> assignedTo,admin,user92;confirmed,0,1
确认类型变化的bug   >> type,install,codeerror;confirmed,0,1
确认已确认的bug     >> 0
确认优先级变化的bug >> status,resolved,active;pri,3,2
确认bug             >> status,closed,active

*/

$bugIDlist = array('1','3','4','51','81');

$bug1  = array('assignedTo' => 'user92', 'status' => 'active', 'type' => 'codeerror', 'pri' => '1');
$bug3  = array('assignedTo' => 'admin' , 'status' => 'active', 'type' => 'codeerror', 'pri' => '3');
$bug4  = array('assignedTo' => 'admin' , 'status' => 'active', 'type' => 'security',  'pri' => '4');
$bug51 = array('assignedTo' => 'dev1'  , 'status' => 'active', 'type' => 'standard',  'pri' => '2');
$bug81 = array('assignedTo' => 'test1' , 'status' => 'active', 'type' => 'others',    'pri' => '1');

$bug = new bugTest();
r($bug->confirmTest($bugIDlist[0],$bug1))  && p('0:field,old,new;1:field,old,new') && e('assignedTo,admin,user92;confirmed,0,1'); // 确认指派人变化的bug
r($bug->confirmTest($bugIDlist[1],$bug3))  && p('0:field,old,new;1:field,old,new') && e('assignedTo,admin,user94;confirmed,0,1'); // 确认类型变化的bug
r($bug->confirmTest($bugIDlist[2],$bug4))  && p('0:field,old,new')                 && e('assignedTo,admin,user95');               // 确认已确认的bug
r($bug->confirmTest($bugIDlist[3],$bug51)) && p('0:field,old,new;1:field,old,new') && e('assignedTo,dev1,user96;pri,3,2');        // 确认优先级变化的bug
r($bug->confirmTest($bugIDlist[4],$bug81)) && p('0:field,old,new')                 && e('assignedTo,test1,user97');               // 确认bug
system("./ztest init");
