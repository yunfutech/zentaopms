#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/company.class.php';
su('admin');

/**

title=测试companyModel->getByIDTest();
cid=1
pid=1

根据id查询公司 >> 易软天创网络科技有限公司
错误id查询 >> 0

*/

$companyIDList = array('0', '1');

$company = new companyTest();
r($company->getByIDTest($companyIDList[1])) && p('name') && e('易软天创网络科技有限公司'); // 根据id查询公司
r($company->getByIDTest($companyIDList[0])) && p()       && e('0');                        // 错误id查询