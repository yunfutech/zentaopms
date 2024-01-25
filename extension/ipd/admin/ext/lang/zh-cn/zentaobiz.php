<?php
global $config;
$lang->admin->property = new stdclass();
$lang->admin->property->companyName = '公司名称';
$lang->admin->property->startDate   = '授权时间';
$lang->admin->property->expireDate  = '到期时间';
if($config->visions != ',lite,') $lang->admin->property->user = '研发用户人数';
$lang->admin->property->lite        = '迅捷版用户人数';
$lang->admin->property->ip          = '授权IP';
$lang->admin->property->mac         = '授权MAC';
$lang->admin->property->domain      = '授权域名';

$lang->admin->menuList->system['subMenu']['libreoffice'] = array('link' => 'Office|custom|libreoffice|');
$lang->admin->menuList->system['menuOrder']['60']        = 'libreoffice';

$lang->admin->menuList->feature['subMenu']['feedback'] = array('link' => "反馈|custom|required|module=feedback", 'exclude' => 'set,required');
$lang->admin->menuList->feature['menuOrder']['35']     = 'feedback';

$lang->admin->menuList->feature['tabMenu']['feedback']['feedback'] = array('link' => "反馈|custom|required|module=feedback", 'links' => array('custom|set|module=feedback&field=review'), 'exclude' => 'custom-set,custom-required');
$lang->admin->menuList->feature['tabMenu']['feedback']['ticket']   = array('link' => "工单|custom|required|module=ticket", 'exclude' => 'custom-set,custom-required');

if($config->vision == 'lite') unset($lang->admin->menuList->feature['subMenu']['feedback'], $lang->admin->menuList->feature['menuOrder']['35']);
