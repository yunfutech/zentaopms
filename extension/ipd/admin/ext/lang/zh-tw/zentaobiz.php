<?php
global $config;
$lang->admin->property = new stdclass();
$lang->admin->property->companyName = '公司名稱';
$lang->admin->property->startDate   = '授權時間';
$lang->admin->property->expireDate  = '到期時間';
if($config->visions != ',lite,') $lang->admin->property->user = '研發用戶人數';
$lang->admin->property->lite        = '迅捷版用戶人數';
$lang->admin->property->ip          = '授權IP';
$lang->admin->property->mac         = '授權MAC';
$lang->admin->property->domain      = '授權域名';

$lang->admin->menuList->system['subMenu']['libreoffice'] = array('link' => 'Office|custom|libreoffice|');
$lang->admin->menuList->system['menuOrder']['60']        = 'libreoffice';

$lang->admin->menuList->feature['subMenu']['feedback'] = array('link' => "反饋|custom|required|module=feedback", 'exclude' => 'set,required');
$lang->admin->menuList->feature['menuOrder']['35']     = 'feedback';

$lang->admin->menuList->feature['tabMenu']['feedback']['feedback'] = array('link' => "反饋|custom|required|module=feedback", 'links' => array('custom|set|module=feedback&field=review'), 'exclude' => 'custom-set,custom-required');
$lang->admin->menuList->feature['tabMenu']['feedback']['ticket']   = array('link' => "工單|custom|required|module=ticket", 'exclude' => 'custom-set,custom-required');

if($config->vision == 'lite') unset($lang->admin->menuList->feature['subMenu']['feedback'], $lang->admin->menuList->feature['menuOrder']['35']);
