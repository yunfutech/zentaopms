<?php
$lang->custom->execution       = 'Kanban';
$lang->custom->closedExecution = 'Closed ' . $lang->custom->execution;
$lang->custom->notice->readOnlyOfExecution = "If Change Forbidden, any change on tasks, builds, efforts and stories of the closed {$lang->executionCommon} is also forbidden.";

$lang->custom->moduleName['execution'] = $lang->custom->execution;

$lang->custom->object = array();
$lang->custom->object['project']   = 'Project';
$lang->custom->object['execution'] = $lang->custom->execution;
$lang->custom->object['story']     = $lang->SRCommon;
$lang->custom->object['task']      = 'Task';
$lang->custom->object['todo']      = 'Todo';
$lang->custom->object['user']      = 'User';
$lang->custom->object['block']     = 'Block';

$lang->custom->task = new stdClass();
$lang->custom->task->fields['priList'] = 'Priority';

$lang->custom->story = new stdClass();
$lang->custom->story->fields['priList']          = 'Priority';
$lang->custom->story->fields['reasonList']       = 'Close Reason';
$lang->custom->story->fields['statusList']       = 'Status';
$lang->custom->story->fields['reviewRules']      = 'Review Rules';
$lang->custom->story->fields['reviewResultList'] = 'Review Result';
$lang->custom->story->fields['review']           = 'Need Review';

$lang->custom->system = array('required');
