<?php
$builder = new stdclass();

$builder->company     = array('rows' => 2,    'extends' => array('company'));
$builder->user        = array('rows' => 1000, 'extends' => array('user'));
$builder->todo        = array('rows' => 2000, 'extends' => array('todo'));
$builder->todocycle   = array('rows' => 5, 'extends' => array('todo','todocycle'));
$builder->effort      = array('rows' => 100, 'extends' => array('effort'));
$builder->usergroup   = array('rows' => 600, 'extends' => array('usergroup'));
$builder->usercontact = array('rows' => 61, 'extends' => array('usercontact'));
$builder->userview    = array('rows' => 400, 'extends' => array('userview'));
$builder->dept        = array('rows' => 100,  'extends' => array('dept'));
$builder->action      = array('rows' => 100,  'extends' => array('action'));

$builder->program      = array('rows' => 10, 'extends' => array('project', 'program'));
$builder->project      = array('rows' => 90, 'extends' => array('project', 'project'));
$builder->projectalone = array('rows' => 20, 'extends' => array('project', 'projectalone'));
$builder->sprint       = array('rows' => 600, 'extends' => array('project', 'execution'));

$builder->story        = array('rows' => 400, 'extends' => array('story'));
$builder->childstory   = array('rows' => 50, 'extends' => array('story','childstory'));
$builder->storyreview  = array('rows' => 100, 'extends' => array('storyreview'));
$builder->storymodule  = array('rows' => 800, 'extends' => array('module','storymodule'));
$builder->storyplan    = array('rows' => 400, 'extends' => array('planstory'));
$builder->storystage   = array('rows' => 450, 'extends' => array('storystage'));
$builder->storyspec    = array('rows' => 570, 'extends' => array('storyspec'));
$builder->relation     = array('rows' => 12, 'extends' => array('relation'));
$builder->task         = array('rows' => 600, 'extends' => array('task','task'));
$builder->taskmore     = array('rows' => 300, 'extends' => array('task','moretask'));
$builder->taskspec     = array('rows' => 600, 'extends' => array('taskspec'));
$builder->taskmodule   = array('rows' => 1800, 'extends' => array('module','taskmodule'));
$builder->taskestimate = array('rows' => 600, 'extends' => array('taskestimate'));
$builder->taskson      = array('rows' => 10,  'extends' => array('task', 'taskson'));
$builder->case         = array('rows' => 400, 'extends' => array('case'));
$builder->casestep     = array('rows' => 400, 'extends' => array('casestep'));
$builder->bug          = array('rows' => 300, 'extends' => array('bug'));
$builder->morebug      = array('rows' => 15, 'extends' => array('bug','morebug'));

$builder->testtask   = array('rows' => 10, 'extends' => array('testtask'));
$builder->testresult = array('rows' => 40, 'extends' => array('testresult'));
$builder->testrun    = array('rows' => 40, 'extends' => array('testrun'));

$builder->product             = array('rows' => 100, 'extends' => array('product'));
$builder->productalone        = array('rows' => 20, 'extends' => array('product','productalone'));
$builder->productline         = array('rows' => 20,  'extends' => array('module', 'productline'));
$builder->productplan         = array('rows' => 70, 'extends' => array('productplan'));
$builder->branch              = array('rows' => 240, 'extends' => array('branch'));
$builder->projectproduct      = array('rows' => 200, 'extends' => array('projectproduct'));
$builder->projectproductalone = array('rows' => 28, 'extends' => array('projectproduct','projectproductalone'));
$builder->projectstory        = array('rows' => 200, 'extends' => array('projectstory'));
$builder->executionstory      = array('rows' => 180, 'extends' => array('projectstory','executionstory'));

$builder->kanbanspace  = array('rows' => 50, 'extends' => array('kanbanspace'));
$builder->kanban       = array('rows' => 100, 'extends' => array('kanban'));
$builder->kanbanregion = array('rows' => 100, 'extends' => array('kanbanregion','kanbanregion'));
$builder->kanbangroup  = array('rows' => 100, 'extends' => array('kanbangroup','kanbangroup'));
$builder->kanbanlane   = array('rows' => 100, 'extends' => array('kanbanlane','kanbanlane'));
$builder->kanbancolumn = array('rows' => 400, 'extends' => array('kanbancolumn','kanbancolumn'));
$builder->kanbancard   = array('rows' => 800, 'extends' => array('kanbancard','kanbancard'));
$builder->kanbancell   = array('rows' => 400, 'extends' => array('kanbancell','kanbancell'));

$builder->kanbanregionproject = array('rows' => 180, 'extends' => array('kanbanregion','kanbanregionproject'));
$builder->kanbangroupproject  = array('rows' => 540, 'extends' => array('kanbangroup','kanbangroupproject'));
$builder->kanbanlaneproject   = array('rows' => 540, 'extends' => array('kanbanlane','kanbanlaneproject'));
$builder->kanbancolumnproject = array('rows' => 4860, 'extends' => array('kanbancolumn','kanbancolumnproject'));
$builder->kanbancellproject   = array('rows' => 4860, 'extends' => array('kanbancell','kanbancellproject'));


$builder->team        = array('rows' => 400, 'extends' => array('team'));
$builder->teamtask    = array('rows' => 20, 'extends' => array('team', 'teamtask'));
$builder->stakeholder = array('rows' => 1, 'extends' => array('stakeholder'));
$builder->stageson    = array('rows' => 30, 'extends' => array('project', 'executionson'));

$builder->doclib     = array('rows' => 900, 'extends' => array('doclib'));
$builder->doc        = array('rows' => 900, 'extends' => array('doc'));
$builder->doccontent = array('rows' => 900, 'extends' => array('doccontent'));

$builder->build   = array('rows' => 20, 'extends' => array('build'));
$builder->release = array('rows' => 8, 'extends' => array('release'));

$builder->webhook = array('rows' => 7, 'extends' => array('webhook'));
$builder->entry   = array('rows' => 1, 'extends' => array('entry'));

$builder->pipeline = array('rows' => 2,  'extends' => array('pipeline'));
$builder->repo     = array('rows' => 1,  'extends' => array('repo'));
$builder->job      = array('rows' => 2,  'extends' => array('job'));
$builder->mr       = array('rows' => 1,  'extends' => array('mr'));
$builder->oauth    = array('rows' => 90, 'extends' => array('oauth'));

$builder->vm         = array('rows' => 1,  'extends' => array('vm'));
$builder->vmtemplate = array('rows' => 1,  'extends' => array('vmtemplate'));
$builder->host       = array('rows' => 1,  'extends' => array('host'));
$builder->asset      = array('rows' => 1,  'extends' => array('asset'));
