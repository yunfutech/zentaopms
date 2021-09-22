<?php

$config->producttarget = new stdclass();

$config->producttarget->editor = new stdclass();
$config->producttarget->editor->create = array('id' => 'cause', 'tools' => 'simpleTools');
$config->producttarget->editor->edit = array('id' => 'cause', 'tools' => 'simpleTools');

$config->producttarget->create   = new stdclass();
$config->producttarget->create->requiredFields      = 'name,date';
$config->producttarget->edit     = new stdclass();
$config->producttarget->edit->requiredFields        = $config->producttarget->create->requiredFields;

$config->producttargetitem = new stdclass();
$config->producttargetitem->batchCreate = 10;
$config->producttargetitem->batchCreateFields = 'name,acceptance';
$config->producttargetitem->edit     = new stdclass();
$config->producttargetitem->edit->requiredFields  = $config->producttargetitem->batchCreateFields;
