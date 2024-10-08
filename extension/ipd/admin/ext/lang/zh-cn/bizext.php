<?php
$lang->admin->license       = '授权信息';
$lang->admin->uploadLicense = '替换授权';

$lang->admin->licenseInfo['alllife'] = '终生';
$lang->admin->licenseInfo['nouser']  = '不限人数';

$lang->admin->property = new stdclass();
$lang->admin->property->companyName = '公司名称';
$lang->admin->property->startDate   = '授权时间';
$lang->admin->property->expireDate  = '到期时间';
$lang->admin->property->user        = '授权人数';
$lang->admin->property->ip          = '授权IP';
$lang->admin->property->mac         = '授权MAC';
$lang->admin->property->domain      = '授权域名';

$lang->admin->notWritable = '<code>%s</code> 目录不可写，请修改目录权限正确后，刷新。';
$lang->admin->notZip      = '请上传zip文件。';

global $config;
if($config->vision == 'rnd')
{
    $lang->admin->menuList->system['subMenu']['license'] = array('link' => "授权信息|admin|license|");
    $lang->admin->menuList->system['menuOrder']['25']    = 'license';
    $lang->admin->menuList->system['dividerMenu']        = str_replace(',safe,', ',license,', $lang->admin->menuList->system['dividerMenu']);
}
