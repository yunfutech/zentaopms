<?php
$config->message->objectTypes = array();
$config->message->objectTypes['product'] = array('opened', 'edited', 'closed', 'undeleted');
$config->message->objectTypes['story']   = array('opened', 'edited', 'commented', 'changed', 'reviewed', 'closed', 'activated', 'assigned');

$config->message->available = array();
$config->message->available['mail']['story']    = $config->message->objectTypes['story'];
$config->message->available['webhook']          = $config->message->objectTypes;
$config->message->available['message']['story'] = $config->message->objectTypes['story'];
