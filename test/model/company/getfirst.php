#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/company.class.php';
su('admin');

/**

title=测试companyModel->getFirstTest();
cid=1
pid=1

公司信息第一条查询 >> 易软天创网络科技有限公司

*/

$company = new companyTest();
r($company->getFirstTest()) && p('name')  && e('易软天创网络科技有限公司'); // 公司信息第一条查询