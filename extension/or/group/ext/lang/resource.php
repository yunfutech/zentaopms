<?php
$lang->resource = new stdclass();

$lang->moduleOrder[0]  = 'index';
$lang->moduleOrder[5]  = 'my';
$lang->moduleOrder[10] = 'todo';
$lang->moduleOrder[15] = 'demandpool';
$lang->moduleOrder[20] = 'demand';
$lang->moduleOrder[25] = 'market';
$lang->moduleOrder[30] = 'marketreport';
$lang->moduleOrder[35] = 'marketresearch';
$lang->moduleOrder[40] = 'product';
$lang->moduleOrder[45] = 'branch';
$lang->moduleOrder[50] = 'roadmap';
$lang->moduleOrder[55] = 'requirement';
$lang->moduleOrder[60] = 'charter';
$lang->moduleOrder[65] = 'doc';
$lang->moduleOrder[70] = 'admin';
$lang->moduleOrder[75] = 'mail';

/* My module. */
$lang->resource->my = new stdclass();
$lang->resource->my->index           = 'indexAction';
$lang->resource->my->todo            = 'todoAction';
$lang->resource->my->calendar        = 'calendarAction';
$lang->resource->my->work            = 'workAction';
$lang->resource->my->audit           = 'audit';
$lang->resource->my->contribute      = 'contributeAction';
$lang->resource->my->project         = 'project';
$lang->resource->my->profile         = 'profileAction';
$lang->resource->my->uploadAvatar    = 'uploadAvatar';
$lang->resource->my->preference      = 'preference';
$lang->resource->my->dynamic         = 'dynamicAction';
$lang->resource->my->editProfile     = 'editProfile';
$lang->resource->my->changePassword  = 'changePassword';
$lang->resource->my->manageContacts  = 'manageContacts';
$lang->resource->my->deleteContacts  = 'deleteContacts';
$lang->resource->my->score           = 'score';
$lang->resource->my->team            = 'team';
$lang->resource->my->doc             = 'doc';
$lang->resource->my->execution       = 'execution';

$lang->my->methodOrder[1]  = 'index';
$lang->my->methodOrder[5]  = 'todo';
$lang->my->methodOrder[10] = 'work';
$lang->my->methodOrder[15] = 'contribute';
$lang->my->methodOrder[20] = 'project';
$lang->my->methodOrder[25] = 'profile';
$lang->my->methodOrder[30] = 'uploadAvatar';
$lang->my->methodOrder[35] = 'preference';
$lang->my->methodOrder[40] = 'dynamic';
$lang->my->methodOrder[45] = 'editProfile';
$lang->my->methodOrder[50] = 'changePassword';
$lang->my->methodOrder[55] = 'manageContacts';
$lang->my->methodOrder[60] = 'deleteContacts';
$lang->my->methodOrder[65] = 'score';
$lang->my->methodOrder[70] = 'unbind';
$lang->my->methodOrder[75] = 'team';
$lang->my->methodOrder[80] = 'execution';
$lang->my->methodOrder[85] = 'doc';
$lang->my->methodOrder[90] = 'audit';

/* Todo. */
$lang->resource->todo = new stdclass();
$lang->resource->todo->create       = 'create';
$lang->resource->todo->createcycle  = 'createCycle';
$lang->resource->todo->batchCreate  = 'batchCreate';
$lang->resource->todo->edit         = 'edit';
$lang->resource->todo->batchEdit    = 'batchEdit';
$lang->resource->todo->view         = 'view';
$lang->resource->todo->delete       = 'delete';
$lang->resource->todo->export       = 'export';
$lang->resource->todo->start        = 'start';
$lang->resource->todo->finish       = 'finish';
$lang->resource->todo->batchFinish  = 'batchFinish';
$lang->resource->todo->import2Today = 'import2Today';
$lang->resource->todo->assignTo     = 'assignAction';
$lang->resource->todo->activate     = 'activate';
$lang->resource->todo->close        = 'close';
$lang->resource->todo->batchClose   = 'batchClose';

/* Product. */
$lang->resource->product = new stdclass();
$lang->resource->product->index           = 'indexAction';
$lang->resource->product->requirement     = 'requirement';
$lang->resource->product->create          = 'create';
$lang->resource->product->view            = 'view';
$lang->resource->product->edit            = 'edit';
$lang->resource->product->batchEdit       = 'batchEdit';
$lang->resource->product->delete          = 'delete';
$lang->resource->product->roadmap         = 'roadmap';
$lang->resource->product->track           = 'track';
$lang->resource->product->dynamic         = 'dynamic';
$lang->resource->product->project         = 'project';
$lang->resource->product->close           = 'closeAction';
$lang->resource->product->activate        = 'activateAction';
$lang->resource->product->updateOrder     = 'orderAction';
$lang->resource->product->all             = 'list';
$lang->resource->product->kanban          = 'kanban';
$lang->resource->product->manageLine      = 'manageLine';
$lang->resource->product->export          = 'exportAction';
$lang->resource->product->whitelist       = 'whitelist';
$lang->resource->product->addWhitelist    = 'addWhitelist';
$lang->resource->product->unbindWhitelist = 'unbindWhitelist';

$lang->product->methodOrder[0]   = 'index';
$lang->product->methodOrder[5]   = 'browse';
$lang->product->methodOrder[6]   = 'requirement';
$lang->product->methodOrder[10]  = 'create';
$lang->product->methodOrder[15]  = 'view';
$lang->product->methodOrder[20]  = 'edit';
$lang->product->methodOrder[25]  = 'batchEdit';
$lang->product->methodOrder[35]  = 'delete';
$lang->product->methodOrder[40]  = 'roadmap';
$lang->product->methodOrder[45]  = 'track';
$lang->product->methodOrder[50]  = 'dynamic';
$lang->product->methodOrder[55]  = 'project';
$lang->product->methodOrder[65]  = 'close';
$lang->product->methodOrder[70]  = 'activate';
$lang->product->methodOrder[75]  = 'updateOrder';
$lang->product->methodOrder[80]  = 'all';
$lang->product->methodOrder[85]  = 'kanban';
$lang->product->methodOrder[90]  = 'manageLine';
$lang->product->methodOrder[95]  = 'build';
$lang->product->methodOrder[100] = 'export';
$lang->product->methodOrder[105] = 'whitelist';
$lang->product->methodOrder[110] = 'addWhitelist';
$lang->product->methodOrder[115] = 'unbindWhitelist';

/* Branch. */
$lang->resource->branch = new stdclass();
$lang->resource->branch->manage      = 'manage';
$lang->resource->branch->create      = 'createAction';
$lang->resource->branch->edit        = 'editAction';
$lang->resource->branch->close       = 'closeAction';
$lang->resource->branch->activate    = 'activateAction';
$lang->resource->branch->sort        = 'sort';
$lang->resource->branch->batchEdit   = 'batchEdit';
$lang->resource->branch->mergeBranch = 'mergeBranchAction';

$lang->branch->methodOrder[0]  = 'manage';
$lang->branch->methodOrder[5]  = 'create';
$lang->branch->methodOrder[10] = 'edit';
$lang->branch->methodOrder[15] = 'close';
$lang->branch->methodOrder[20] = 'activate';
$lang->branch->methodOrder[25] = 'sort';
$lang->branch->methodOrder[30] = 'batchEdit';
$lang->branch->methodOrder[35] = 'mergeBranch';

/* Requirement. */
$lang->resource->requirement = new stdclass();
$lang->resource->requirement->create             = 'create';
$lang->resource->requirement->batchCreate        = 'batchCreate';
$lang->resource->requirement->edit               = 'editAction';
$lang->resource->requirement->linkStory          = 'linkStory';
$lang->resource->requirement->batchEdit          = 'batchEdit';
$lang->resource->requirement->export             = 'exportAction';
$lang->resource->requirement->delete             = 'deleteAction';
$lang->resource->requirement->view               = 'view';
$lang->resource->requirement->change             = 'changeAction';
$lang->resource->requirement->review             = 'reviewAction';
$lang->resource->requirement->submitReview       = 'submitReview';
$lang->resource->requirement->batchReview        = 'batchReview';
$lang->resource->requirement->recall             = 'recall';
$lang->resource->requirement->assignTo           = 'assignAction';
$lang->resource->requirement->close              = 'closeAction';
$lang->resource->requirement->batchClose         = 'batchClose';
$lang->resource->requirement->activate           = 'activateAction';
$lang->resource->requirement->report             = 'reportAction';
$lang->resource->requirement->batchChangeBranch  = 'batchChangeBranch';
$lang->resource->requirement->batchAssignTo      = 'batchAssignTo';
$lang->resource->requirement->batchChangeModule  = 'batchChangeModule';
$lang->resource->requirement->linkRequirements   = 'linkRequirementsAB';
$lang->resource->requirement->batchChangeRoadmap = 'batchChangeRoadmap';
$lang->resource->requirement->exportTemplate     = 'exportTemplate';
$lang->resource->requirement->import             = 'importCase';
$lang->resource->requirement->relation           = 'relation';

$lang->requirement->methodOrder[5]   = 'create';
$lang->requirement->methodOrder[10]  = 'batchCreate';
$lang->requirement->methodOrder[15]  = 'edit';
$lang->requirement->methodOrder[20]  = 'export';
$lang->requirement->methodOrder[25]  = 'delete';
$lang->requirement->methodOrder[30]  = 'view';
$lang->requirement->methodOrder[35]  = 'change';
$lang->requirement->methodOrder[40]  = 'review';
$lang->requirement->methodOrder[44]  = 'submitReview';
$lang->requirement->methodOrder[45]  = 'batchReview';
$lang->requirement->methodOrder[50]  = 'recall';
$lang->requirement->methodOrder[55]  = 'close';
$lang->requirement->methodOrder[60]  = 'batchClose';
$lang->requirement->methodOrder[65]  = 'assignTo';
$lang->requirement->methodOrder[70]  = 'batchAssignTo';
$lang->requirement->methodOrder[75]  = 'activate';
$lang->requirement->methodOrder[80]  = 'report';
$lang->requirement->methodOrder[85]  = 'linkStory';
$lang->requirement->methodOrder[90]  = 'batchChangeBranch';
$lang->requirement->methodOrder[95]  = 'batchChangeModule';
$lang->requirement->methodOrder[100] = 'linkRequirements';
$lang->requirement->methodOrder[105] = 'exportTemplate';
$lang->requirement->methodOrder[110] = 'import';
$lang->requirement->methodOrder[115] = 'relation';

/* Doc. */
$lang->resource->doc = new stdclass();
$lang->resource->doc->index          = 'index';
$lang->resource->doc->mySpace        = 'mySpace';
$lang->resource->doc->myView         = 'myView';
$lang->resource->doc->myCollection   = 'myCollection';
$lang->resource->doc->myCreation     = 'myCreation';
$lang->resource->doc->myEdited       = 'myEdited';
$lang->resource->doc->createLib      = 'createLib';
$lang->resource->doc->editLib        = 'editLib';
$lang->resource->doc->deleteLib      = 'deleteLib';
$lang->resource->doc->create         = 'create';
$lang->resource->doc->edit           = 'edit';
$lang->resource->doc->view           = 'view';
$lang->resource->doc->delete         = 'delete';
$lang->resource->doc->deleteFile     = 'deleteFile';
$lang->resource->doc->collect        = 'collectAction';
$lang->resource->doc->productSpace   = 'productSpace';
$lang->resource->doc->projectSpace   = 'projectSpace';
$lang->resource->doc->teamSpace      = 'teamSpace';
$lang->resource->doc->showFiles      = 'showFiles';
$lang->resource->doc->addCatalog     = 'addCatalog';
$lang->resource->doc->editCatalog    = 'editCatalog';
$lang->resource->doc->sortCatalog    = 'sortCatalog';
$lang->resource->doc->deleteCatalog  = 'deleteCatalog';
$lang->resource->doc->displaySetting = 'displaySetting';
$lang->resource->doc->mine2export    = 'mine2export';
$lang->resource->doc->product2export = 'product2export';
$lang->resource->doc->custom2export  = 'custom2export';
$lang->resource->doc->exportFiles    = 'exportFiles';

$lang->doc->methodOrder[5]   = 'index';
$lang->doc->methodOrder[10]  = 'mySpace';
$lang->doc->methodOrder[15]  = 'myView';
$lang->doc->methodOrder[20]  = 'myCollection';
$lang->doc->methodOrder[25]  = 'myCreation';
$lang->doc->methodOrder[30]  = 'myEdited';
$lang->doc->methodOrder[35]  = 'createLib';
$lang->doc->methodOrder[40]  = 'editLib';
$lang->doc->methodOrder[45]  = 'deleteLib';
$lang->doc->methodOrder[50]  = 'create';
$lang->doc->methodOrder[55]  = 'edit';
$lang->doc->methodOrder[60]  = 'view';
$lang->doc->methodOrder[65]  = 'delete';
$lang->doc->methodOrder[70]  = 'deleteFile';
$lang->doc->methodOrder[75]  = 'collect';
$lang->doc->methodOrder[80]  = 'productSpace';
$lang->doc->methodOrder[85]  = 'projectSpace';
$lang->doc->methodOrder[90]  = 'teamSpace';
$lang->doc->methodOrder[95]  = 'showFiles';
$lang->doc->methodOrder[100] = 'addCatalog';
$lang->doc->methodOrder[105] = 'editCatalog';
$lang->doc->methodOrder[110] = 'sortCatalog';
$lang->doc->methodOrder[115] = 'deleteCatalog';
$lang->doc->methodOrder[120] = 'displaySetting';

/* Custom. */
$lang->resource->custom = new stdclass();
$lang->resource->custom->set                = 'set';
$lang->resource->custom->product            = 'productName';
$lang->resource->custom->execution          = 'executionCommon';
$lang->resource->custom->required           = 'required';
$lang->resource->custom->restore            = 'restore';
$lang->resource->custom->flow               = 'flow';
$lang->resource->custom->timezone           = 'timezone';
$lang->resource->custom->setStoryConcept    = 'setStoryConcept';
$lang->resource->custom->editStoryConcept   = 'editStoryConcept';
$lang->resource->custom->browseStoryConcept = 'browseStoryConcept';
$lang->resource->custom->setDefaultConcept  = 'setDefaultConcept';
$lang->resource->custom->deleteStoryConcept = 'deleteStoryConcept';
$lang->resource->custom->kanban             = 'kanban';
$lang->resource->custom->code               = 'code';
$lang->resource->custom->hours              = 'hours';
$lang->resource->custom->percent            = 'percent';
$lang->resource->custom->limitTaskDate      = 'limitTaskDateAction';

$lang->custom->methodOrder[10] = 'set';
$lang->custom->methodOrder[15] = 'product';
$lang->custom->methodOrder[20] = 'execution';
$lang->custom->methodOrder[25] = 'required';
$lang->custom->methodOrder[30] = 'restore';
$lang->custom->methodOrder[35] = 'flow';
$lang->custom->methodOrder[45] = 'timezone';
$lang->custom->methodOrder[50] = 'setStoryConcept';
$lang->custom->methodOrder[55] = 'editStoryConcept';
$lang->custom->methodOrder[60] = 'browseStoryConcept';
$lang->custom->methodOrder[65] = 'setDefaultConcept';
$lang->custom->methodOrder[70] = 'deleteStoryConcept';
$lang->custom->methodOrder[75] = 'kanban';
$lang->custom->methodOrder[80] = 'code';
$lang->custom->methodOrder[85] = 'hours';
$lang->custom->methodOrder[90] = 'percent';
$lang->custom->methodOrder[95] = 'limitTaskDate';

/* Company. */
$lang->resource->company = new stdclass();
$lang->resource->company->browse = 'browse';
$lang->resource->company->edit   = 'edit';
$lang->resource->company->view   = 'view';
$lang->resource->company->dynamic= 'dynamic';

$lang->company->methodOrder[5]  = 'browse';
$lang->company->methodOrder[15] = 'edit';
$lang->company->methodOrder[25] = 'dynamic';

/* Department. */
$lang->resource->dept = new stdclass();
$lang->resource->dept->browse      = 'browse';
$lang->resource->dept->updateOrder = 'updateOrder';
$lang->resource->dept->manageChild = 'manageChildAction';
$lang->resource->dept->edit        = 'edit';
$lang->resource->dept->delete      = 'delete';

$lang->dept->methodOrder[5]  = 'browse';
$lang->dept->methodOrder[10] = 'updateOrder';
$lang->dept->methodOrder[15] = 'manageChild';
$lang->dept->methodOrder[20] = 'edit';
$lang->dept->methodOrder[25] = 'delete';

/* Group. */
$lang->resource->group = new stdclass();
$lang->resource->group->browse              = 'browseAction';
$lang->resource->group->create              = 'create';
$lang->resource->group->edit                = 'edit';
$lang->resource->group->copy                = 'copy';
$lang->resource->group->delete              = 'delete';
$lang->resource->group->manageView          = 'manageView';
$lang->resource->group->managePriv          = 'managePriv';
$lang->resource->group->manageMember        = 'manageMember';
$lang->resource->group->manageProjectAdmin  = 'manageProjectAdmin';
//$lang->resource->group->editManagePriv      = 'editManagePriv';
//$lang->resource->group->managePrivPackage   = 'managePrivPackage';
//$lang->resource->group->createPrivPackage   = 'createPrivPackage';
//$lang->resource->group->editPrivPackage     = 'editPrivPackage';
//$lang->resource->group->deletePrivPackage   = 'deletePrivPackage';
//$lang->resource->group->sortPrivPackages    = 'sortPrivPackages';
//$lang->resource->group->addRelation         = 'addRelation';
//$lang->resource->group->deleteRelation      = 'deleteRelation';
//$lang->resource->group->batchDeleteRelation = 'batchDeleteRelation';
//$lang->resource->group->createPriv          = 'createPriv';
//$lang->resource->group->editPriv            = 'editPriv';
//$lang->resource->group->deletePriv          = 'deletePriv';
//$lang->resource->group->batchChangePackage  = 'batchChangePackage';

$lang->group->methodOrder[5]   = 'browse';
$lang->group->methodOrder[10]  = 'create';
$lang->group->methodOrder[15]  = 'edit';
$lang->group->methodOrder[20]  = 'copy';
$lang->group->methodOrder[25]  = 'delete';
$lang->group->methodOrder[30]  = 'managePriv';
$lang->group->methodOrder[35]  = 'manageMember';
$lang->group->methodOrder[40]  = 'manageProjectAdmin';
$lang->group->methodOrder[45]  = 'editManagePriv';
$lang->group->methodOrder[50]  = 'managePrivPackage';
$lang->group->methodOrder[55]  = 'createPrivPackage';
$lang->group->methodOrder[60]  = 'editPrivPackage';
$lang->group->methodOrder[65]  = 'deletePrivPackage';
$lang->group->methodOrder[70]  = 'sortPrivPackages';
$lang->group->methodOrder[75]  = 'batchChangePackage';
$lang->group->methodOrder[80]  = 'addRelation';
$lang->group->methodOrder[85]  = 'deleteRelation';
$lang->group->methodOrder[90]  = 'batchDeleteRelation';
$lang->group->methodOrder[95]  = 'createPriv';
$lang->group->methodOrder[100] = 'editPriv';
$lang->group->methodOrder[105] = 'deletePriv';

/* User. */
$lang->resource->user = new stdclass();
$lang->resource->user->create            = 'create';
$lang->resource->user->batchCreate       = 'batchCreate';
$lang->resource->user->view              = 'view';
$lang->resource->user->edit              = 'edit';
$lang->resource->user->unlock            = 'unlock';
$lang->resource->user->delete            = 'delete';
$lang->resource->user->todo              = 'todo';
$lang->resource->user->story             = 'story';
$lang->resource->user->task              = 'task';
$lang->resource->user->bug               = 'bug';
$lang->resource->user->testTask          = 'testTask';
$lang->resource->user->testCase          = 'testCase';
$lang->resource->user->execution         = 'execution';
$lang->resource->user->dynamic           = 'dynamic';
$lang->resource->user->profile           = 'profile';
$lang->resource->user->batchEdit         = 'batchEdit';
$lang->resource->user->unbind            = 'unbind';
$lang->resource->user->setPublicTemplate = 'setPublicTemplate';
$lang->resource->user->export            = 'export';
$lang->resource->user->exportTemplate    = 'exportTemplate';
$lang->resource->user->import            = 'import';

$lang->user->methodOrder[5]  = 'create';
$lang->user->methodOrder[7]  = 'batchCreate';
$lang->user->methodOrder[10] = 'view';
$lang->user->methodOrder[15] = 'edit';
$lang->user->methodOrder[20] = 'unlock';
$lang->user->methodOrder[25] = 'delete';
$lang->user->methodOrder[30] = 'todo';
$lang->user->methodOrder[35] = 'task';
$lang->user->methodOrder[40] = 'bug';
$lang->user->methodOrder[45] = 'project';
$lang->user->methodOrder[60] = 'dynamic';
$lang->user->methodOrder[70] = 'profile';
$lang->user->methodOrder[75] = 'batchEdit';
$lang->user->methodOrder[80] = 'unbind';
$lang->user->methodOrder[85] = 'setPublicTemplate';

/* Tree. */
$lang->resource->tree = new stdclass();
$lang->resource->tree->browse      = 'browse';
$lang->resource->tree->browseTask  = 'browseTask';
$lang->resource->tree->updateOrder = 'updateOrder';
$lang->resource->tree->manageChild = 'manageChild';
$lang->resource->tree->edit        = 'edit';
$lang->resource->tree->fix         = 'fix';
$lang->resource->tree->delete      = 'delete';

$lang->tree->methodOrder[5]  = 'browse';
$lang->tree->methodOrder[10] = 'browseTask';
$lang->tree->methodOrder[15] = 'updateOrder';
$lang->tree->methodOrder[20] = 'manageChild';
$lang->tree->methodOrder[25] = 'edit';
$lang->tree->methodOrder[30] = 'delete';

/* Search. */
$lang->resource->search = new stdclass();
$lang->resource->search->buildForm   = 'buildForm';
$lang->resource->search->buildQuery  = 'buildQuery';
$lang->resource->search->saveQuery   = 'saveQuery';
$lang->resource->search->deleteQuery = 'deleteQuery';
$lang->resource->search->index       = 'index';
$lang->resource->search->buildIndex  = 'buildIndex';

$lang->search->methodOrder[5]  = 'buildForm';
$lang->search->methodOrder[10] = 'buildQuery';
$lang->search->methodOrder[15] = 'saveQuery';
$lang->search->methodOrder[20] = 'deleteQuery';
$lang->search->methodOrder[30] = 'index';
$lang->search->methodOrder[35] = 'buildIndex';

/* Admin. */
$lang->resource->admin = new stdclass();
$lang->resource->admin->index           = 'index';
$lang->resource->admin->safe            = 'safeIndex';
$lang->resource->admin->checkWeak       = 'checkWeak';
$lang->resource->admin->sso             = 'ssoAction';
$lang->resource->admin->register        = 'register';
$lang->resource->admin->resetPWDSetting = 'resetPWDSetting';
$lang->resource->admin->tableEngine     = 'tableEngine';

$lang->admin->methodOrder[0]  = 'index';
$lang->admin->methodOrder[10] = 'safeIndex';
$lang->admin->methodOrder[15] = 'checkWeak';
$lang->admin->methodOrder[20] = 'sso';
$lang->admin->methodOrder[25] = 'register';
$lang->admin->methodOrder[35] = 'resetPWDSetting';
$lang->admin->methodOrder[40] = 'tableEngine';

$lang->resource->file = new stdclass();
$lang->resource->file->download     = 'download';
$lang->resource->file->edit         = 'edit';
$lang->resource->file->delete       = 'delete';
$lang->resource->file->uploadImages = 'uploadImages';
$lang->resource->file->setPublic     = 'setPublic';

$lang->file->methodOrder[5]  = 'download';
$lang->file->methodOrder[10] = 'edit';
$lang->file->methodOrder[15] = 'delete';
$lang->file->methodOrder[20] = 'uploadImages';
$lang->file->methodOrder[25] = 'setPublic';

$lang->resource->charter = new stdclass();
$lang->resource->charter->browse   = 'browse';
$lang->resource->charter->create   = 'create';
$lang->resource->charter->edit     = 'edit';
$lang->resource->charter->view     = 'view';
$lang->resource->charter->delete   = 'delete';
$lang->resource->charter->review   = 'review';
$lang->resource->charter->close    = 'close';
$lang->resource->charter->activate = 'activate';
$lang->resource->charter->loadRoadmapStories = 'loadStories';

$lang->resource->demandpool = new stdclass();
$lang->resource->demandpool->browse   = 'browse';
$lang->resource->demandpool->create   = 'create';
$lang->resource->demandpool->edit     = 'edit';
$lang->resource->demandpool->view     = 'view';
$lang->resource->demandpool->close    = 'close';
$lang->resource->demandpool->activate = 'activate';
$lang->resource->demandpool->delete   = 'delete';

$lang->resource->demand = new stdclass();
$lang->resource->demand->browse         = 'browse';
$lang->resource->demand->create         = 'create';
$lang->resource->demand->batchCreate    = 'batchCreate';
$lang->resource->demand->edit           = 'edit';
$lang->resource->demand->view           = 'view';
$lang->resource->demand->assignTo       = 'assignedTo';
$lang->resource->demand->change         = 'change';
$lang->resource->demand->review         = 'review';
$lang->resource->demand->submitReview   = 'submitReview';
$lang->resource->demand->recall         = 'recall';
$lang->resource->demand->delete         = 'delete';
$lang->resource->demand->close          = 'close';
$lang->resource->demand->activate       = 'activate';
$lang->resource->demand->distribute     = 'distribute';
$lang->resource->demand->export         = 'export';
$lang->resource->demand->exportTemplate = 'exportTemplate';
$lang->resource->demand->import         = 'import';

$lang->resource->roadmap = new stdclass();
$lang->resource->roadmap->browse           = 'browse';
$lang->resource->roadmap->view             = 'view';
$lang->resource->roadmap->linkUR           = 'linkUR';
$lang->resource->roadmap->create           = 'create';
$lang->resource->roadmap->edit             = 'edit';
$lang->resource->roadmap->close            = 'close';
$lang->resource->roadmap->delete           = 'delete';
$lang->resource->roadmap->activate         = 'activate';
$lang->resource->roadmap->unlinkUR         = 'unlinkUR';
$lang->resource->roadmap->batchUnlinkUR    = 'batchUnlinkUR';

/* Mail. */
$lang->resource->mail = new stdclass();
$lang->resource->mail->index       = 'index';
$lang->resource->mail->detect      = 'detectAction';
$lang->resource->mail->edit        = 'edit';
$lang->resource->mail->save        = 'saveAction';
$lang->resource->mail->test        = 'test';
$lang->resource->mail->reset       = 'resetAction';
$lang->resource->mail->browse      = 'browse';
$lang->resource->mail->delete      = 'delete';
$lang->resource->mail->resend      = 'resendAction';
$lang->resource->mail->batchDelete = 'batchDelete';

$lang->mail->methodOrder[5]  = 'index';
$lang->mail->methodOrder[10] = 'detect';
$lang->mail->methodOrder[15] = 'edit';
$lang->mail->methodOrder[20] = 'save';
$lang->mail->methodOrder[25] = 'test';
$lang->mail->methodOrder[30] = 'reset';
$lang->mail->methodOrder[35] = 'browse';
$lang->mail->methodOrder[40] = 'delete';
$lang->mail->methodOrder[45] = 'batchDelete';
$lang->mail->methodOrder[50] = 'resend';

$lang->resource->message = new stdclass();
$lang->resource->message->index   = 'index';
$lang->resource->message->browser = 'browser';
$lang->resource->message->setting = 'setting';

$lang->message->methodOrder[5]  = 'index';
$lang->message->methodOrder[10] = 'browser';
$lang->message->methodOrder[15] = 'setting';

/* Webhook. */
$lang->resource->webhook = new stdclass();
$lang->resource->webhook->browse     = 'list';
$lang->resource->webhook->create     = 'create';
$lang->resource->webhook->edit       = 'edit';
$lang->resource->webhook->delete     = 'delete';
$lang->resource->webhook->log        = 'logAction';
$lang->resource->webhook->bind       = 'bind';
$lang->resource->webhook->chooseDept = 'chooseDept';

$lang->webhook->methodOrder[5]  = 'browse';
$lang->webhook->methodOrder[10] = 'create';
$lang->webhook->methodOrder[15] = 'edit';
$lang->webhook->methodOrder[20] = 'delete';
$lang->webhook->methodOrder[25] = 'log';
$lang->webhook->methodOrder[30] = 'bind';
$lang->webhook->methodOrder[35] = 'chooseDept';

$lang->resource->sms        = new stdclass();
$lang->resource->sms->index = 'index';
$lang->resource->sms->test  = 'test';
$lang->resource->sms->reset = 'reset';

$lang->resource->my->effort         = 'effortAction';
$lang->resource->company->effort    = 'companyEffort';
$lang->resource->company->alleffort = 'allEffort';

if(!isset($lang->resource->effort)) $lang->resource->effort = new stdclass();
$lang->resource->effort->batchCreate     = 'batchCreate';
$lang->resource->effort->createForObject = 'createForObject';
$lang->resource->effort->edit            = 'edit';
$lang->resource->effort->batchEdit       = 'batchEdit';
$lang->resource->effort->view            = 'view';
$lang->resource->effort->delete          = 'delete';
$lang->resource->effort->export          = 'exportAction';
$lang->resource->effort->calendar        = 'calendarAction';

$lang->resource->user->effort = 'effort';

/* Action. */
$lang->resource->action = new stdclass();
$lang->resource->action->trash    = 'trashAction';
$lang->resource->action->undelete = 'undeleteAction';
$lang->resource->action->hideOne  = 'hideOneAction';
$lang->resource->action->hideAll  = 'hideAll';
$lang->resource->action->comment  = 'comment';
$lang->resource->action->editComment = 'editComment';

$lang->action->methodOrder[5]  = 'trash';
$lang->action->methodOrder[10] = 'undelete';
$lang->action->methodOrder[15] = 'hideOne';
$lang->action->methodOrder[20] = 'hideAll';
$lang->action->methodOrder[25] = 'comment';
$lang->action->methodOrder[30] = 'editComment';

$lang->resource->backup = new stdclass();
$lang->resource->backup->index       = 'index';
$lang->resource->backup->backup      = 'backup';
$lang->resource->backup->restore     = 'restoreAction';
$lang->resource->backup->change      = 'change';
$lang->resource->backup->delete      = 'delete';
$lang->resource->backup->setting     = 'settingAction';
$lang->resource->backup->rmPHPHeader = 'rmPHPHeader';

$lang->backup->methodOrder[5]  = 'index';
$lang->backup->methodOrder[10] = 'backup';
$lang->backup->methodOrder[15] = 'restore';
$lang->backup->methodOrder[20] = 'delete';
$lang->backup->methodOrder[25] = 'setting';
$lang->backup->methodOrder[30] = 'rmPHPHeader';

$lang->resource->market = new stdclass();
$lang->resource->market->browse = 'browse';
$lang->resource->market->create = 'create';
$lang->resource->market->edit   = 'edit';
$lang->resource->market->view   = 'view';
$lang->resource->market->delete = 'delete';

$lang->market->methodOrder[5]  = 'browse';
$lang->market->methodOrder[10] = 'create';
$lang->market->methodOrder[15] = 'edit';
$lang->market->methodOrder[20] = 'view';
$lang->market->methodOrder[25] = 'delete';

$lang->resource->marketreport = new stdclass();
$lang->resource->marketreport->all     = 'all';
$lang->resource->marketreport->browse  = 'browse';
$lang->resource->marketreport->create  = 'create';
$lang->resource->marketreport->edit    = 'edit';
$lang->resource->marketreport->view    = 'view';
$lang->resource->marketreport->delete  = 'delete';
$lang->resource->marketreport->publish = 'publish';

$lang->marketreport->methodOrder[5]  = 'all';
$lang->marketreport->methodOrder[10] = 'browse';
$lang->marketreport->methodOrder[15] = 'create';
$lang->marketreport->methodOrder[20] = 'edit';
$lang->marketreport->methodOrder[25] = 'view';
$lang->marketreport->methodOrder[30] = 'delete';
$lang->marketreport->methodOrder[35] = 'publish';

$lang->resource->marketresearch = new stdclass();
$lang->resource->marketresearch->all                = 'all';
$lang->resource->marketresearch->browse             = 'browse';
$lang->resource->marketresearch->create             = 'create';
$lang->resource->marketresearch->edit               = 'edit';
$lang->resource->marketresearch->view               = 'view';
$lang->resource->marketresearch->activate           = 'activate';
$lang->resource->marketresearch->start              = 'start';
$lang->resource->marketresearch->close              = 'close';
$lang->resource->marketresearch->team               = 'teamAction';
$lang->resource->marketresearch->manageMembers      = 'manageMembers';
$lang->resource->marketresearch->unlinkMember       = 'unlinkMember';
$lang->resource->marketresearch->reports            = 'reports';
$lang->resource->marketresearch->delete             = 'delete';
$lang->resource->marketresearch->stage              = 'stage';
$lang->resource->marketresearch->createStage        = 'createStage';
$lang->resource->marketresearch->editStage          = 'editStage';
$lang->resource->marketresearch->batchStage         = 'batchStage';
$lang->resource->marketresearch->startStage         = 'startStage';
$lang->resource->marketresearch->deleteStage        = 'deleteStage';
$lang->resource->marketresearch->closeStage         = 'closeStage';
$lang->resource->marketresearch->activateStage      = 'activateStage';
$lang->resource->marketresearch->createTask         = 'createTask';
$lang->resource->marketresearch->closeTask          = 'closeTask';
$lang->resource->marketresearch->startTask          = 'startTask';
$lang->resource->marketresearch->finishTask         = 'finishTask';
$lang->resource->marketresearch->deleteTask         = 'deleteTask';
$lang->resource->marketresearch->cancelTask         = 'cancelTask';
$lang->resource->marketresearch->activateTask       = 'activateTask';
$lang->resource->marketresearch->taskAssignTo       = 'taskAssignTo';
$lang->resource->marketresearch->viewTask           = 'viewTask';
$lang->resource->marketresearch->recordTaskEstimate = 'recordTaskEstimate';
$lang->resource->marketresearch->batchCreateTask    = 'batchCreateTask';
$lang->resource->marketresearch->editTask           = 'editTask';


$lang->marketresearch->methodOrder[5]  = 'all';
$lang->marketresearch->methodOrder[10] = 'browse';
$lang->marketresearch->methodOrder[15] = 'create';
$lang->marketresearch->methodOrder[20] = 'edit';
$lang->marketresearch->methodOrder[25] = 'view';
$lang->marketresearch->methodOrder[30] = 'activate';
$lang->marketresearch->methodOrder[35] = 'start';
$lang->marketresearch->methodOrder[40] = 'close';
$lang->marketresearch->methodOrder[45] = 'team';
$lang->marketresearch->methodOrder[50] = 'manageMembers';
$lang->marketresearch->methodOrder[55] = 'unlinkMember';
$lang->marketresearch->methodOrder[60] = 'reports';
$lang->marketresearch->methodOrder[65] = 'delete';
$lang->marketresearch->methodOrder[70] = 'stage';
$lang->marketresearch->methodOrder[75] = 'createStage';
$lang->marketresearch->methodOrder[80] = 'editStage';
$lang->marketresearch->methodOrder[85] = 'batchStage';
$lang->marketresearch->methodOrder[90] = 'deleteStage';
$lang->marketresearch->methodOrder[95] = 'closeStage';

$lang->marketresearch->methodOrder[100] = 'activateStage';
$lang->marketresearch->methodOrder[105] = 'createTask';
$lang->marketresearch->methodOrder[110] = 'closeTask';
$lang->marketresearch->methodOrder[115] = 'startTask';
$lang->marketresearch->methodOrder[120] = 'finishTask';
$lang->marketresearch->methodOrder[125] = 'deleteTask';
$lang->marketresearch->methodOrder[130] = 'cancelTask';
$lang->marketresearch->methodOrder[135] = 'activateTask';
$lang->marketresearch->methodOrder[140] = 'taskAssignTo';
$lang->marketresearch->methodOrder[145] = 'viewTask';
$lang->marketresearch->methodOrder[150] = 'recordTaskEstimate';
$lang->marketresearch->methodOrder[155] = 'batchCreateTask';
$lang->marketresearch->methodOrder[160] = 'editTask';

/* AI methods. */
$lang->resource->ai = new stdclass();
$lang->resource->ai->models                 = 'modelBrowse';
$lang->resource->ai->editModel              = 'modelEdit';
$lang->resource->ai->testConnection         = 'modelTestConnection';
$lang->resource->ai->chat                   = 'chat';

$lang->resource->contact            = new stdclass();
$lang->resource->programstakeholder = new stdclass();
$lang->resource->researchplan       = new stdclass();
$lang->resource->workestimation     = new stdclass();
$lang->resource->gapanalysis        = new stdclass();
$lang->resource->executionview      = new stdclass();
$lang->resource->managespace        = new stdclass();
$lang->resource->systemteam         = new stdclass();
$lang->resource->systemschedule     = new stdclass();
$lang->resource->systemeffort       = new stdclass();
$lang->resource->systemdynamic      = new stdclass();
$lang->resource->systemcompany      = new stdclass();
$lang->resource->pipeline           = new stdclass();
$lang->resource->devopssetting      = new stdclass();
$lang->resource->featureswitch      = new stdclass();
$lang->resource->importdata         = new stdclass();
$lang->resource->systemsetting      = new stdclass();
$lang->resource->staffmanage        = new stdclass();
$lang->resource->modelconfig        = new stdclass();
$lang->resource->featureconfig      = new stdclass();
$lang->resource->doctemplate        = new stdclass();
$lang->resource->notifysetting      = new stdclass();
$lang->resource->bidesign           = new stdclass();
$lang->resource->personalsettings   = new stdclass();
$lang->resource->projectsettings    = new stdclass();
$lang->resource->dataaccess         = new stdclass();
$lang->resource->executiongantt     = new stdclass();
$lang->resource->executionkanban    = new stdclass();
$lang->resource->executionburn      = new stdclass();
$lang->resource->executioncfd       = new stdclass();
$lang->resource->executionstory     = new stdclass();
$lang->resource->executionqa        = new stdclass();
$lang->resource->executionsettings  = new stdclass();
$lang->resource->generalcomment     = new stdclass();
$lang->resource->generalping        = new stdclass();
$lang->resource->generaltemplate    = new stdclass();
$lang->resource->generaleffort      = new stdclass();
$lang->resource->productsettings    = new stdclass();
$lang->resource->projectreview      = new stdclass();
$lang->resource->projecttrack       = new stdclass();
$lang->resource->projectqa          = new stdclass();