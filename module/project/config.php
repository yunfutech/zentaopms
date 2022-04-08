<?php
$config->project = new stdclass();
$config->project->editor = new stdclass();

$config->project->editor->create   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->edit     = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->close    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->suspend  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->start    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->activate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->view     = array('id' => 'lastComment', 'tools' => 'simpleTools');

$config->project->list = new stdclass();
$config->project->list->exportFields = 'id,parent,code,name,linkedProducts,status,begin,end,budget,PM,end,desc';

$config->project->create = new stdclass();
$config->project->edit   = new stdclass();
$config->project->create->requiredFields = 'name,code,begin,end';
$config->project->edit->requiredFields   = 'name,code,begin,end';

$config->project->start   = new stdclass();
$config->project->start->requiredFields = 'realBegan';

$config->project->close   = new stdclass();
$config->project->close->requiredFields = 'realEnd';

$config->project->sortFields         = new stdclass();
$config->project->sortFields->id     = 'id';
$config->project->sortFields->begin  = 'begin';
$config->project->sortFields->end    = 'end';
$config->project->sortFields->status = 'status';
$config->project->sortFields->budget = 'budget';

global $lang;
$config->project->datatable = new stdclass();
$config->project->datatable->defaultField = array('id', 'name', 'code', 'PM', 'status', 'begin', 'end', 'budget', 'teamCount','estimate','consume', 'progress', 'actions');

$config->project->datatable->fieldList['id']['title']    = 'ID';
$config->project->datatable->fieldList['id']['fixed']    = 'left';
$config->project->datatable->fieldList['id']['width']    = '60';
$config->project->datatable->fieldList['id']['required'] = 'yes';
$config->project->datatable->fieldList['id']['pri']      = '1';

$config->project->datatable->fieldList['name']['title']    = 'name';
$config->project->datatable->fieldList['name']['fixed']    = 'left';
$config->project->datatable->fieldList['name']['width']    = 'auto';
$config->project->datatable->fieldList['name']['minWidth'] = '180';
$config->project->datatable->fieldList['name']['required'] = 'yes';
$config->project->datatable->fieldList['name']['sort']     = 'no';
$config->project->datatable->fieldList['name']['pri']      = '1';

$config->project->datatable->fieldList['code']['title']    = 'code';
$config->project->datatable->fieldList['code']['fixed']    = 'left';
$config->project->datatable->fieldList['code']['width']    = '100';
$config->project->datatable->fieldList['code']['minWidth'] = '180';
$config->project->datatable->fieldList['code']['required'] = 'no';
$config->project->datatable->fieldList['code']['sort']     = 'no';
$config->project->datatable->fieldList['code']['pri']      = '1';

$config->project->datatable->fieldList['PM']['title']    = 'PM';
$config->project->datatable->fieldList['PM']['fixed']    = 'no';
$config->project->datatable->fieldList['PM']['width']    = '80';
$config->project->datatable->fieldList['PM']['required'] = 'yes';
$config->project->datatable->fieldList['PM']['sort']     = 'no';
$config->project->datatable->fieldList['PM']['pri']      = '2';

$config->project->datatable->fieldList['status']['title']    = 'status';
$config->project->datatable->fieldList['status']['fixed']    = 'left';
$config->project->datatable->fieldList['status']['width']    = '80';
$config->project->datatable->fieldList['status']['required'] = 'no';
$config->project->datatable->fieldList['status']['sort']     = 'yes';
$config->project->datatable->fieldList['status']['pri']      = '2';

$config->project->datatable->fieldList['begin']['title']    = 'begin';
$config->project->datatable->fieldList['begin']['fixed']    = 'no';
$config->project->datatable->fieldList['begin']['width']    = '90';
$config->project->datatable->fieldList['begin']['required'] = 'no';
$config->project->datatable->fieldList['begin']['pri']      = '9';

$config->project->datatable->fieldList['end']['title']    = 'end';
$config->project->datatable->fieldList['end']['fixed']    = 'no';
$config->project->datatable->fieldList['end']['width']    = '90';
$config->project->datatable->fieldList['end']['required'] = 'no';
$config->project->datatable->fieldList['end']['pri']      = '3';

$config->project->datatable->fieldList['budget']['title']    = 'budget';
$config->project->datatable->fieldList['budget']['fixed']    = 'no';
$config->project->datatable->fieldList['budget']['width']    = '80';
$config->project->datatable->fieldList['budget']['required'] = 'yes';
$config->project->datatable->fieldList['budget']['pri']      = '3';

$config->project->datatable->fieldList['teamCount']['title']    = 'teamCount';
$config->project->datatable->fieldList['teamCount']['fixed']    = 'no';
$config->project->datatable->fieldList['teamCount']['width']    = '70';
$config->project->datatable->fieldList['teamCount']['required'] = 'no';
$config->project->datatable->fieldList['teamCount']['sort']     = 'no';
$config->project->datatable->fieldList['teamCount']['pri']      = '8';

$config->project->datatable->fieldList['estimate']['title']    = 'estimate';
$config->project->datatable->fieldList['estimate']['fixed']    = 'no';
$config->project->datatable->fieldList['estimate']['width']    = '70';
$config->project->datatable->fieldList['estimate']['maxWidth'] = '80';
$config->project->datatable->fieldList['estimate']['required'] = 'no';
$config->project->datatable->fieldList['estimate']['sort']     = 'no';
$config->project->datatable->fieldList['estimate']['pri']      = '8';

$config->project->datatable->fieldList['consume']['title']    = 'consume';
$config->project->datatable->fieldList['consume']['fixed']    = 'no';
$config->project->datatable->fieldList['consume']['width']    = '80';
$config->project->datatable->fieldList['consume']['maxWidth'] = '80';
$config->project->datatable->fieldList['consume']['required'] = 'no';
$config->project->datatable->fieldList['consume']['sort']     = 'no';
$config->project->datatable->fieldList['consume']['pri']      = '7';

$config->project->datatable->fieldList['progress']['title']    = 'progress';
$config->project->datatable->fieldList['progress']['fixed']    = 'no';
$config->project->datatable->fieldList['progress']['width']    = '60';
$config->project->datatable->fieldList['progress']['required'] = 'no';
$config->project->datatable->fieldList['progress']['sort']     = 'no';
$config->project->datatable->fieldList['progress']['pri']      = '6';

$config->project->datatable->fieldList['actions']['title']    = 'actions';
$config->project->datatable->fieldList['actions']['fixed']    = 'right';
$config->project->datatable->fieldList['actions']['width']    = '180';
$config->project->datatable->fieldList['actions']['required'] = 'yes';
$config->project->datatable->fieldList['actions']['pri']      = '1';

$config->project->checkList = new stdclass();
$config->project->checkList->scrum     = array('bug', 'execution', 'build', 'doc', 'release', 'testtask', 'case');
$config->project->checkList->waterfall = array('execution', 'design', 'doc', 'bug', 'case', 'build', 'release', 'testtask');
$config->project->maxCheckList = new stdclass();
$config->project->maxCheckList->scrum     = array('bug', 'execution', 'build', 'doc', 'release', 'testtask', 'case', 'issue', 'risk', 'meeting');
$config->project->maxCheckList->waterfall = array('execution', 'design', 'doc', 'bug', 'case', 'build', 'release', 'testtask', 'review', 'build', 'researchplan', 'issue', 'risk', 'opportunity', 'auditplan', 'gapanalysis', 'meeting');

$config->project->excludedPriv['project']    = array('index', 'browse', 'kanban', 'create', 'batchEdit', 'qa', 'updateOrder', 'createGuide', 'programTitle', 'export');
$config->project->excludedPriv['story']      = array('report', 'linkStory', 'batchChangeBranch', 'batchChangeModule', 'batchToTask', 'processStoryChange', 'track', 'tasks', 'bugs', 'cases');
$config->project->excludedPriv['bug']        = array('browse', 'batchChangePlan', 'batchCreate', 'batchEdit', 'batchConfirm', 'batchResolve', 'batchClose', 'batchActivate', 'report', 'batchChangeModule', 'batchChangeBranch');
$config->project->excludedPriv['testcase']   = array('browse', 'batchChangeModule', 'batchChangeBranch');
$config->project->excludedPriv['testtask']   = array('browse', 'view', 'start', 'activate', 'block', 'close');
$config->project->excludedPriv['doc']        = array('browse', 'view', 'catalog', 'index');
$config->project->excludedPriv['repo']       = array('edit', 'delete', 'maintain', 'setRules');
$config->project->excludedPriv['testreport'] = array('browse');
$config->project->excludedPriv['auditplan']  = array('delete');
if(!isset($config->maxVersion)) $config->project->excludedPriv['stakeholder'] = array('issue', 'viewIssue', 'userIssue');
