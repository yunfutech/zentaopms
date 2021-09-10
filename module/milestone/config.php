<?php

$config->milestone = new stdclass();

$config->milestone->editor = new stdclass();
$config->milestone->editor->create = array('id' => 'comment', 'tools' => 'simpleTools');
$config->milestone->editor->edit = array('id' => 'comment', 'tools' => 'simpleTools');

$config->milestone->create   = new stdclass();
$config->milestone->create->requiredFields      = 'name,date';
$config->milestone->edit     = new stdclass();
$config->milestone->edit->requiredFields        = $config->milestone->create->requiredFields;