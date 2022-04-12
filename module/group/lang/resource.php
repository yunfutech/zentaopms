<?php
/**
 * The all avaliabe actions in ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     group
 * @version     $Id$
 * @link        http://www.zentao.net
 */

/* Module order. */
$lang->moduleOrder[0]   = 'index';
$lang->moduleOrder[5]   = 'my';
$lang->moduleOrder[10]  = 'todo';

$lang->moduleOrder[15]  = 'program';
$lang->moduleOrder[20]  = 'personnel';
$lang->moduleOrder[25]  = 'product';
$lang->moduleOrder[30]  = 'story';
$lang->moduleOrder[35]  = 'productplan';
$lang->moduleOrder[40]  = 'release';

$lang->moduleOrder[45]  = 'project';
$lang->moduleOrder[50]  = 'projectstory';
$lang->moduleOrder[55]  = 'execution';
$lang->moduleOrder[56]  = 'kanban';
$lang->moduleOrder[57]  = 'programplan';
$lang->moduleOrder[60]  = 'task';
$lang->moduleOrder[65]  = 'build';
$lang->moduleOrder[66]  = 'design';

$lang->moduleOrder[70]  = 'qa';
$lang->moduleOrder[75]  = 'bug';
$lang->moduleOrder[80]  = 'testcase';
$lang->moduleOrder[85]  = 'testtask';
$lang->moduleOrder[90]  = 'testsuite';
$lang->moduleOrder[95]  = 'testreport';
$lang->moduleOrder[100] = 'caselib';
$lang->moduleOrder[105] = 'automation';

$lang->moduleOrder[110] = 'doc';
$lang->moduleOrder[115] = 'report';

$lang->moduleOrder[120] = 'company';
$lang->moduleOrder[125] = 'dept';
$lang->moduleOrder[130] = 'group';
$lang->moduleOrder[135] = 'user';

$lang->moduleOrder[140] = 'admin';
$lang->moduleOrder[142] = 'stage';
$lang->moduleOrder[145] = 'extension';
$lang->moduleOrder[150] = 'custom';
$lang->moduleOrder[155] = 'action';

$lang->moduleOrder[160] = 'mail';
$lang->moduleOrder[165] = 'svn';
$lang->moduleOrder[170] = 'git';
$lang->moduleOrder[175] = 'search';
$lang->moduleOrder[180] = 'tree';
$lang->moduleOrder[185] = 'api';
$lang->moduleOrder[190] = 'file';
$lang->moduleOrder[195] = 'misc';
$lang->moduleOrder[200] = 'backup';
$lang->moduleOrder[205] = 'cron';
$lang->moduleOrder[210] = 'dev';
$lang->moduleOrder[215] = 'message';
$lang->moduleOrder[220] = 'gitlab';
$lang->moduleOrder[225] = 'mr';

$lang->resource = new stdclass();

/* Index module. */
$lang->resource->index = new stdclass();
$lang->resource->index->index = 'index';

$lang->index->methodOrder[0] = 'index';

/* My module. */
$lang->resource->my = new stdclass();
$lang->resource->my->index           = 'indexAction';
$lang->resource->my->todo            = 'todo';
$lang->resource->my->calendar        = 'calendarAction';
$lang->resource->my->work            = 'workAction';
$lang->resource->my->contribute      = 'contributeAction';
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
$lang->resource->my->requirement     = 'requirement';
$lang->resource->my->story           = 'story';
$lang->resource->my->task            = 'task';
$lang->resource->my->bug             = 'bug';
$lang->resource->my->doc             = 'doc';
$lang->resource->my->testtask        = 'testtask';
$lang->resource->my->testcase        = 'testcase';
$lang->resource->my->execution       = 'execution';

$lang->my->methodOrder[1]   = 'index';
$lang->my->methodOrder[5]   = 'todo';
$lang->my->methodOrder[10]  = 'work';
$lang->my->methodOrder[15]  = 'contribute';
$lang->my->methodOrder[20]  = 'project';
$lang->my->methodOrder[25]  = 'profile';
$lang->my->methodOrder[30]  = 'uploadAvatar';
$lang->my->methodOrder[35]  = 'preference';
$lang->my->methodOrder[40]  = 'dynamic';
$lang->my->methodOrder[45]  = 'editProfile';
$lang->my->methodOrder[50]  = 'changePassword';
$lang->my->methodOrder[55]  = 'manageContacts';
$lang->my->methodOrder[60]  = 'deleteContacts';
$lang->my->methodOrder[65]  = 'score';
$lang->my->methodOrder[70]  = 'unbind';
$lang->my->methodOrder[75]  = 'team';
$lang->my->methodOrder[80]  = 'requirement';
$lang->my->methodOrder[85]  = 'story';
$lang->my->methodOrder[90]  = 'task';
$lang->my->methodOrder[95]  = 'bug';
$lang->my->methodOrder[100] = 'testtask';
$lang->my->methodOrder[105] = 'testcase';
$lang->my->methodOrder[110] = 'execution';
$lang->my->methodOrder[115] = 'doc';

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

/* Personnel . */
$lang->resource->personnel = new stdclass();
$lang->resource->personnel->accessible      = 'accessible';
$lang->resource->personnel->invest          = 'invest';
$lang->resource->personnel->whitelist       = 'whitelist';
$lang->resource->personnel->addWhitelist    = 'addWhitelist';
$lang->resource->personnel->unbindWhitelist = 'unbindWhitelist';

$lang->personnel->methodOrder[5]  = 'accessible';
$lang->personnel->methodOrder[10] = 'invest';
$lang->personnel->methodOrder[15] = 'whitelist';
$lang->personnel->methodOrder[20] = 'addWhitelist';
$lang->personnel->methodOrder[25] = 'unbindWhitelist';

global $config;
if($config->systemMode == 'new')
{
    $lang->resource->my->project = 'project';

    /* Design. */
    $lang->resource->design = new stdclass();
    $lang->resource->design->browse       = 'browse';
    $lang->resource->design->view         = 'view';
    $lang->resource->design->create       = 'create';
    $lang->resource->design->batchCreate  = 'batchCreate';
    $lang->resource->design->edit         = 'edit';
    $lang->resource->design->assignTo     = 'assignTo';
    $lang->resource->design->delete       = 'delete';
    $lang->resource->design->linkCommit   = 'linkCommit';
    $lang->resource->design->viewCommit   = 'viewCommit';
    $lang->resource->design->unlinkCommit = 'unlinkCommit';
    $lang->resource->design->revision     = 'revision';

    $lang->design->methodOrder[5]  = 'browse';
    $lang->design->methodOrder[10] = 'view';
    $lang->design->methodOrder[15] = 'create';
    $lang->design->methodOrder[20] = 'batchCreate';
    $lang->design->methodOrder[25] = 'edit';
    $lang->design->methodOrder[30] = 'assignTo';
    $lang->design->methodOrder[35] = 'delete';
    $lang->design->methodOrder[40] = 'linkCommit';
    $lang->design->methodOrder[45] = 'viewCommit';
    $lang->design->methodOrder[50] = 'unlinkCommit';
    $lang->design->methodOrder[55] = 'revision';

    /* Program. */
    $lang->resource->program = new stdclass();
    $lang->resource->program->browse                  = 'browse';
    $lang->resource->program->kanban                  = 'kanbanAction';
    $lang->resource->program->view                    = 'view';
    $lang->resource->program->product                 = 'product';
    $lang->resource->program->create                  = 'create';
    $lang->resource->program->edit                    = 'edit';
    $lang->resource->program->start                   = 'start';
    $lang->resource->program->suspend                 = 'suspend';
    $lang->resource->program->activate                = 'activate';
    $lang->resource->program->close                   = 'close';
    $lang->resource->program->delete                  = 'delete';
    $lang->resource->program->project                 = 'project';
    $lang->resource->program->stakeholder             = 'stakeholder';
    $lang->resource->program->createStakeholder       = 'createStakeholder';
    $lang->resource->program->unlinkStakeholder       = 'unlinkStakeholder';
    $lang->resource->program->batchUnlinkStakeholders = 'batchUnlinkStakeholders';
    $lang->resource->program->unbindWhitelist         = 'unbindWhitelist';
    $lang->resource->program->export                  = 'export';
    $lang->resource->program->updateOrder             = 'updateOrder';

    $lang->program->methodOrder[5]   = 'browse';
    $lang->program->methodOrder[10]  = 'kanban';
    $lang->program->methodOrder[15]  = 'view';
    $lang->program->methodOrder[20]  = 'product';
    $lang->program->methodOrder[25]  = 'create';
    $lang->program->methodOrder[30]  = 'edit';
    $lang->program->methodOrder[35]  = 'view';
    $lang->program->methodOrder[40]  = 'start';
    $lang->program->methodOrder[45]  = 'suspend';
    $lang->program->methodOrder[50]  = 'activate';
    $lang->program->methodOrder[55]  = 'close';
    $lang->program->methodOrder[60]  = 'delete';
    $lang->program->methodOrder[65]  = 'project';
    $lang->program->methodOrder[70]  = 'stakeholder';
    $lang->program->methodOrder[75]  = 'createStakeholder';
    $lang->program->methodOrder[80]  = 'unlinkStakeholder';
    $lang->program->methodOrder[85]  = 'batchUnlinkStakeholders';
    $lang->program->methodOrder[90]  = 'unbindWhitelist';
    $lang->program->methodOrder[95]  = 'export';
    $lang->program->methodOrder[100] = 'updateOrder';

    /* Program plan. */
    $lang->resource->programplan = new stdclass();
    $lang->resource->programplan->create = 'create';
    $lang->resource->programplan->edit   = 'edit';

    $lang->programplan->methodOrder[0] = 'create';
    $lang->programplan->methodOrder[5] = 'edit';

    /* Project. */
    $lang->resource->project = new stdclass();
    $lang->resource->project->index               = 'index';
    $lang->resource->project->browse              = 'browse';
    $lang->resource->project->kanban              = 'kanban';
    $lang->resource->project->programTitle        = 'moduleOpen';
    $lang->resource->project->create              = 'create';
    $lang->resource->project->edit                = 'edit';
    $lang->resource->project->batchEdit           = 'batchEdit';
    $lang->resource->project->group               = 'group';
    $lang->resource->project->createGroup         = 'createGroup';
    $lang->resource->project->managePriv          = 'managePriv';
    $lang->resource->project->manageMembers       = 'manageMembers';
    $lang->resource->project->manageGroupMember   = 'manageGroupMember';
    $lang->resource->project->copyGroup           = 'copyGroup';
    $lang->resource->project->editGroup           = 'editGroup';
    $lang->resource->project->start               = 'start';
    $lang->resource->project->suspend             = 'suspend';
    $lang->resource->project->close               = 'close';
    $lang->resource->project->activate            = 'activate';
    $lang->resource->project->delete              = 'delete';
    $lang->resource->project->view                = 'view';
    $lang->resource->project->whitelist           = 'whitelist';
    $lang->resource->project->addWhitelist        = 'addWhitelist';
    $lang->resource->project->unbindWhitelist     = 'unbindWhitelist';
    $lang->resource->project->manageProducts      = 'manageProducts';
    $lang->resource->project->dynamic             = 'dynamic';
    $lang->resource->project->build               = 'build';
    $lang->resource->project->qa                  = 'qa';
    $lang->resource->project->bug                 = 'bug';
    $lang->resource->project->testcase            = 'testcase';
    $lang->resource->project->testtask            = 'testtask';
    $lang->resource->project->testreport          = 'testreport';
    $lang->resource->project->execution           = 'execution';
    $lang->resource->project->export              = 'export';
    $lang->resource->project->createGuide         = 'createGuide';
    $lang->resource->project->updateOrder         = 'updateOrder';
    $lang->resource->project->team                = 'teamAction';
    $lang->resource->project->unlinkMember        = 'unlinkMember';

    $lang->project->methodOrder[0]   = 'index';
    $lang->project->methodOrder[5]   = 'browse';
    $lang->project->methodOrder[10]  = 'kanban';
    $lang->project->methodOrder[15]  = 'projectTitle';
    $lang->project->methodOrder[20]  = 'create';
    $lang->project->methodOrder[25]  = 'edit';
    $lang->project->methodOrder[30]  = 'batchEdit';
    $lang->project->methodOrder[35]  = 'group';
    $lang->project->methodOrder[40]  = 'createGroup';
    $lang->project->methodOrder[45]  = 'managePriv';
    $lang->project->methodOrder[50]  = 'manageMembers';
    $lang->project->methodOrder[55]  = 'manageGroupMember';
    $lang->project->methodOrder[60]  = 'copyGroup';
    $lang->project->methodOrder[65]  = 'editGroup';
    $lang->project->methodOrder[70]  = 'start';
    $lang->project->methodOrder[75]  = 'suspend';
    $lang->project->methodOrder[80]  = 'close';
    $lang->project->methodOrder[85]  = 'activate';
    $lang->project->methodOrder[90]  = 'updateOrder';
    $lang->project->methodOrder[95]  = 'delete';
    $lang->project->methodOrder[100] = 'view';
    $lang->project->methodOrder[105] = 'whitelist';
    $lang->project->methodOrder[110] = 'addWhitelist';
    $lang->project->methodOrder[115] = 'unbindWhitelist';
    $lang->project->methodOrder[120] = 'manageProducts';
    $lang->project->methodOrder[125] = 'view';
    $lang->project->methodOrder[130] = 'dynamic';
    $lang->project->methodOrder[135] = 'build';
    $lang->project->methodOrder[140] = 'qa';
    $lang->project->methodOrder[145] = 'bug';
    $lang->project->methodOrder[150] = 'testcase';
    $lang->project->methodOrder[155] = 'testtask';
    $lang->project->methodOrder[160] = 'testreport';
    $lang->project->methodOrder[165] = 'execution';
    $lang->project->methodOrder[170] = 'export';
    $lang->project->methodOrder[175] = 'createGuide';
    $lang->project->methodOrder[180] = 'updateOrder';
    $lang->project->methodOrder[185] = 'team';
    $lang->project->methodOrder[190] = 'unlinkMember';

    $lang->resource->projectbuild = new stdclass();
    $lang->resource->projectbuild->browse = 'browse';

    $lang->projectbuild->methodOrder[5] = 'browse';

    /* Project Story. */
    $lang->resource->projectstory = new stdclass();
    $lang->resource->projectstory->story             = 'story';
    $lang->resource->projectstory->track             = 'trackAction';
    $lang->resource->projectstory->view              = 'view';
    $lang->resource->projectstory->linkStory         = 'linkStory';
    $lang->resource->projectstory->unlinkStory       = 'unlinkStory';
    $lang->resource->projectstory->batchUnlinkStory  = 'batchUnlinkStory';
    $lang->resource->projectstory->importplanstories = 'importplanstories';

    $lang->projectstory->methodOrder[5]  = 'story';
    $lang->projectstory->methodOrder[10] = 'track';
    $lang->projectstory->methodOrder[15] = 'view';
    $lang->projectstory->methodOrder[20] = 'linkStory';
    $lang->projectstory->methodOrder[25] = 'unlinkStory';
    $lang->projectstory->methodOrder[23] = 'importplanstories';

    /* Release. */
    $lang->resource->projectrelease = new stdclass();
    $lang->resource->projectrelease->browse           = 'browseAction';
    $lang->resource->projectrelease->create           = 'create';
    $lang->resource->projectrelease->edit             = 'edit';
    $lang->resource->projectrelease->delete           = 'delete';
    $lang->resource->projectrelease->view             = 'view';
    $lang->resource->projectrelease->export           = 'export';
    $lang->resource->projectrelease->linkStory        = 'linkStory';
    $lang->resource->projectrelease->unlinkStory      = 'unlinkStory';
    $lang->resource->projectrelease->batchUnlinkStory = 'batchUnlinkStory';
    $lang->resource->projectrelease->linkBug          = 'linkBug';
    $lang->resource->projectrelease->unlinkBug        = 'unlinkBug';
    $lang->resource->projectrelease->batchUnlinkBug   = 'batchUnlinkBug';
    $lang->resource->projectrelease->changeStatus     = 'changeStatus';
    $lang->resource->projectrelease->notify           = 'notify';

    $lang->projectrelease->methodOrder[5]  = 'browse';
    $lang->projectrelease->methodOrder[10] = 'create';
    $lang->projectrelease->methodOrder[15] = 'edit';
    $lang->projectrelease->methodOrder[20] = 'delete';
    $lang->projectrelease->methodOrder[25] = 'view';
    $lang->projectrelease->methodOrder[35] = 'export';
    $lang->projectrelease->methodOrder[40] = 'linkStory';
    $lang->projectrelease->methodOrder[45] = 'unlinkStory';
    $lang->projectrelease->methodOrder[50] = 'batchUnlinkStory';
    $lang->projectrelease->methodOrder[55] = 'linkBug';
    $lang->projectrelease->methodOrder[60] = 'unlinkBug';
    $lang->projectrelease->methodOrder[65] = 'batchUnlinkBug';
    $lang->projectrelease->methodOrder[70] = 'changeStatus';
    $lang->projectrelease->methodOrder[75] = 'notify';

    /* Stage. */
    $lang->resource->stage = new stdclass();
    $lang->resource->stage->browse      = 'browse';
    $lang->resource->stage->create      = 'create';
    $lang->resource->stage->batchCreate = 'batchCreate';
    $lang->resource->stage->edit        = 'edit';
    $lang->resource->stage->setType     = 'setType';
    $lang->resource->stage->delete      = 'delete';

    $lang->stage->methodOrder[5]  = 'browse';
    $lang->stage->methodOrder[10] = 'create';
    $lang->stage->methodOrder[15] = 'batchCreate';
    $lang->stage->methodOrder[20] = 'edit';
    $lang->stage->methodOrder[25] = 'setType';
    $lang->stage->methodOrder[30] = 'delete';

    /* Stakeholer. */
    $lang->resource->stakeholder = new stdclass();
    $lang->resource->stakeholder->browse       = 'browse';
    $lang->resource->stakeholder->create       = 'create';
    $lang->resource->stakeholder->batchCreate  = 'batchCreate';
    $lang->resource->stakeholder->edit         = 'edit';
    $lang->resource->stakeholder->delete       = 'delete';
    $lang->resource->stakeholder->view         = 'viewAction';
    $lang->resource->stakeholder->issue        = 'issue';
    $lang->resource->stakeholder->viewIssue    = 'viewIssueAction';
    $lang->resource->stakeholder->communicate  = 'communicate';
    $lang->resource->stakeholder->expect       = 'expect';
    $lang->resource->stakeholder->expectation  = 'expectation';
    $lang->resource->stakeholder->deleteExpect = 'deleteExpect';
    $lang->resource->stakeholder->createExpect = 'createExpect';
    $lang->resource->stakeholder->editExpect   = 'editExpect';
    $lang->resource->stakeholder->viewExpect   = 'viewExpect';
    $lang->resource->stakeholder->userIssue    = 'userIssue';

    $lang->stakeholder->methodOrder[5]  = 'browse';
    $lang->stakeholder->methodOrder[10] = 'create';
    $lang->stakeholder->methodOrder[13] = 'batchCreate';
    $lang->stakeholder->methodOrder[15] = 'edit';
    $lang->stakeholder->methodOrder[25] = 'delete';
    $lang->stakeholder->methodOrder[30] = 'view';
    $lang->stakeholder->methodOrder[35] = 'issue';
    $lang->stakeholder->methodOrder[40] = 'viewIssue';
    $lang->stakeholder->methodOrder[45] = 'communicate';
    $lang->stakeholder->methodOrder[50] = 'expect';
    $lang->stakeholder->methodOrder[55] = 'expectation';
    $lang->stakeholder->methodOrder[60] = 'deleteExpect';
    $lang->stakeholder->methodOrder[65] = 'createExpect';
    $lang->stakeholder->methodOrder[70] = 'editExpect';
    $lang->stakeholder->methodOrder[75] = 'viewExpect';
    $lang->stakeholder->methodOrder[80] = 'userIssue';
}

/* Product. */
$lang->resource->product = new stdclass();
$lang->resource->product->index          = 'indexAction';
$lang->resource->product->browse         = 'browse';
$lang->resource->product->create         = 'create';
$lang->resource->product->view           = 'view';
$lang->resource->product->edit           = 'edit';
$lang->resource->product->batchEdit      = 'batchEdit';
$lang->resource->product->delete         = 'delete';
$lang->resource->product->roadmap        = 'roadmap';
$lang->resource->product->dynamic        = 'dynamic';
$lang->resource->product->project        = 'project';
$lang->resource->product->dashboard      = 'dashboard';
$lang->resource->product->close          = 'closeAction';
$lang->resource->product->updateOrder    = 'orderAction';
$lang->resource->product->all            = 'list';
$lang->resource->product->kanban         = 'kanban';
$lang->resource->product->manageLine     = 'manageLine';
$lang->resource->product->build          = 'build';
$lang->resource->product->export         = 'exportAction';
$lang->resource->product->whitelist      = 'whitelist';
$lang->resource->product->addWhitelist   = 'addWhitelist';
$lang->resource->product->unbindWhitelist = 'unbindWhitelist';

$lang->product->methodOrder[0]   = 'index';
$lang->product->methodOrder[5]   = 'browse';
$lang->product->methodOrder[10]  = 'create';
$lang->product->methodOrder[15]  = 'view';
$lang->product->methodOrder[20]  = 'edit';
$lang->product->methodOrder[25]  = 'batchEdit';
$lang->product->methodOrder[35]  = 'delete';
$lang->product->methodOrder[40]  = 'roadmap';
$lang->product->methodOrder[45]  = 'dynamic';
$lang->product->methodOrder[50]  = 'project';
$lang->product->methodOrder[55]  = 'dashboard';
$lang->product->methodOrder[60]  = 'close';
$lang->product->methodOrder[65]  = 'updateOrder';
$lang->product->methodOrder[70]  = 'all';
$lang->product->methodOrder[75]  = 'kanban';
$lang->product->methodOrder[80]  = 'manageLine';
$lang->product->methodOrder[85]  = 'build';
$lang->product->methodOrder[90]  = 'export';
$lang->product->methodOrder[95]  = 'whitelist';
$lang->product->methodOrder[100] = 'addWhitelist';
$lang->product->methodOrder[105] = 'unbindWhitelist';

/* Branch. */
$lang->resource->branch = new stdclass();
$lang->resource->branch->manage      = 'manage';
$lang->resource->branch->create      = 'createAction';
$lang->resource->branch->edit        = 'editAction';
$lang->resource->branch->close       = 'closeAction';
$lang->resource->branch->activate    = 'activateAction';
$lang->resource->branch->sort        = 'sort';
$lang->resource->branch->delete      = 'delete';
$lang->resource->branch->batchEdit   = 'batchEdit';
$lang->resource->branch->setDefault  = 'setDefaultAction';
$lang->resource->branch->mergeBranch = 'mergeBranchAction';

$lang->branch->methodOrder[0]  = 'manage';
$lang->branch->methodOrder[5]  = 'create';
$lang->branch->methodOrder[10] = 'edit';
$lang->branch->methodOrder[15] = 'close';
$lang->branch->methodOrder[20] = 'activate';
$lang->branch->methodOrder[25] = 'sort';
$lang->branch->methodOrder[30] = 'delete';
$lang->branch->methodOrder[35] = 'batchEdit';
$lang->branch->methodOrder[40] = 'setDefault';
$lang->branch->methodOrder[45] = 'mergeBranch';

/* Story. */
$lang->resource->story = new stdclass();
$lang->resource->story->create             = 'create';
$lang->resource->story->batchCreate        = 'batchCreate';
$lang->resource->story->edit               = 'editAction';
$lang->resource->story->linkStory          = 'linkStory';
$lang->resource->story->batchEdit          = 'batchEdit';
$lang->resource->story->export             = 'exportAction';
$lang->resource->story->delete             = 'deleteAction';
$lang->resource->story->view               = 'view';
$lang->resource->story->change             = 'changeAction';
$lang->resource->story->review             = 'reviewAction';
$lang->resource->story->batchReview        = 'batchReview';
$lang->resource->story->recall             = 'recall';
$lang->resource->story->assignTo           = 'assignAction';
$lang->resource->story->close              = 'closeAction';
$lang->resource->story->batchClose         = 'batchClose';
$lang->resource->story->activate           = 'activateAction';
$lang->resource->story->tasks              = 'tasks';
$lang->resource->story->bugs               = 'bugs';
$lang->resource->story->cases              = 'cases';
$lang->resource->story->zeroCase           = 'zeroCase';
$lang->resource->story->report             = 'reportAction';
$lang->resource->story->batchChangePlan    = 'batchChangePlan';
$lang->resource->story->batchChangeBranch  = 'batchChangeBranch';
$lang->resource->story->batchChangeStage   = 'batchChangeStage';
$lang->resource->story->batchAssignTo      = 'batchAssignTo';
$lang->resource->story->batchChangeModule  = 'batchChangeModule';
$lang->resource->story->batchToTask        = 'batchToTask';
$lang->resource->story->track              = 'trackAB';
$lang->resource->story->processStoryChange = 'processStoryChange';

$lang->story->methodOrder[5]   = 'create';
$lang->story->methodOrder[10]  = 'batchCreate';
$lang->story->methodOrder[15]  = 'edit';
$lang->story->methodOrder[20]  = 'export';
$lang->story->methodOrder[25]  = 'delete';
$lang->story->methodOrder[30]  = 'view';
$lang->story->methodOrder[35]  = 'change';
$lang->story->methodOrder[40]  = 'review';
$lang->story->methodOrder[45]  = 'batchReview';
$lang->story->methodOrder[50]  = 'recall';
$lang->story->methodOrder[55]  = 'close';
$lang->story->methodOrder[60]  = 'batchClose';
$lang->story->methodOrder[65]  = 'batchChangePlan';
$lang->story->methodOrder[70]  = 'batchChangeStage';
$lang->story->methodOrder[75]  = 'assignTo';
$lang->story->methodOrder[80]  = 'batchAssignTo';
$lang->story->methodOrder[85]  = 'activate';
$lang->story->methodOrder[90]  = 'tasks';
$lang->story->methodOrder[95]  = 'bugs';
$lang->story->methodOrder[100] = 'cases';
$lang->story->methodOrder[105] = 'zeroCase';
$lang->story->methodOrder[110] = 'report';
$lang->story->methodOrder[115] = 'linkStory';
$lang->story->methodOrder[120] = 'batchChangeBranch';
$lang->story->methodOrder[125] = 'batchChangeModule';
$lang->story->methodOrder[130] = 'batchToTask';
$lang->story->methodOrder[135] = 'track';
$lang->story->methodOrder[140] = 'processStoryChange';

/* Product plan. */
$lang->resource->productplan = new stdclass();
$lang->resource->productplan->browse            = 'browse';
$lang->resource->productplan->create            = 'create';
$lang->resource->productplan->edit              = 'edit';
$lang->resource->productplan->delete            = 'delete';
$lang->resource->productplan->view              = 'view';
$lang->resource->productplan->linkStory         = 'linkStory';
$lang->resource->productplan->unlinkStory       = 'unlinkStory';
$lang->resource->productplan->batchUnlinkStory  = 'batchUnlinkStory';
$lang->resource->productplan->linkBug           = 'linkBug';
$lang->resource->productplan->unlinkBug         = 'unlinkBug';
$lang->resource->productplan->batchUnlinkBug    = 'batchUnlinkBug';
$lang->resource->productplan->batchEdit         = 'batchEdit';
$lang->resource->productplan->start             = 'start';
$lang->resource->productplan->finish            = 'finish';
$lang->resource->productplan->close             = 'close';
$lang->resource->productplan->activate          = 'activate';
$lang->resource->productplan->batchChangeStatus = 'batchChangeStatus';

$lang->productplan->methodOrder[5]  = 'browse';
$lang->productplan->methodOrder[10] = 'create';
$lang->productplan->methodOrder[15] = 'edit';
$lang->productplan->methodOrder[20] = 'delete';
$lang->productplan->methodOrder[25] = 'view';
$lang->productplan->methodOrder[30] = 'linkStory';
$lang->productplan->methodOrder[35] = 'unlinkStory';
$lang->productplan->methodOrder[40] = 'batchUnlinkStory';
$lang->productplan->methodOrder[45] = 'linkBug';
$lang->productplan->methodOrder[50] = 'unlinkBug';
$lang->productplan->methodOrder[55] = 'batchUnlinkBug';
$lang->productplan->methodOrder[60] = 'batchEdit';
$lang->productplan->methodOrder[65] = 'start';
$lang->productplan->methodOrder[70] = 'finish';
$lang->productplan->methodOrder[75] = 'close';
$lang->productplan->methodOrder[80] = 'activate';
$lang->productplan->methodOrder[85] = 'batchChangeStatus';

/* Release. */
$lang->resource->release = new stdclass();
$lang->resource->release->browse           = 'browse';
$lang->resource->release->create           = 'create';
$lang->resource->release->edit             = 'edit';
$lang->resource->release->delete           = 'delete';
$lang->resource->release->view             = 'view';
$lang->resource->release->export           = 'export';
$lang->resource->release->linkStory        = 'linkStory';
$lang->resource->release->unlinkStory      = 'unlinkStory';
$lang->resource->release->batchUnlinkStory = 'batchUnlinkStory';
$lang->resource->release->linkBug          = 'linkBug';
$lang->resource->release->unlinkBug        = 'unlinkBug';
$lang->resource->release->batchUnlinkBug   = 'batchUnlinkBug';
$lang->resource->release->changeStatus     = 'changeStatus';
$lang->resource->release->notify           = 'notify';

$lang->release->methodOrder[5]  = 'browse';
$lang->release->methodOrder[10] = 'create';
$lang->release->methodOrder[15] = 'edit';
$lang->release->methodOrder[20] = 'delete';
$lang->release->methodOrder[25] = 'view';
$lang->release->methodOrder[35] = 'export';
$lang->release->methodOrder[40] = 'linkStory';
$lang->release->methodOrder[45] = 'unlinkStory';
$lang->release->methodOrder[50] = 'batchUnlinkStory';
$lang->release->methodOrder[55] = 'linkBug';
$lang->release->methodOrder[60] = 'unlinkBug';
$lang->release->methodOrder[65] = 'batchUnlinkBug';
$lang->release->methodOrder[70] = 'changeStatus';
$lang->release->methodOrder[75] = 'notify';

/* Kanban */
$lang->resource->kanban = new stdclass();
$lang->resource->kanban->space              = 'spaceCommon';
$lang->resource->kanban->createSpace        = 'createSpace';
$lang->resource->kanban->editSpace          = 'editSpace';
$lang->resource->kanban->closeSpace         = 'closeSpace';
$lang->resource->kanban->deleteSpace        = 'deleteSpace';
$lang->resource->kanban->sortSpace          = 'sortSpace';
$lang->resource->kanban->create             = 'create';
$lang->resource->kanban->edit               = 'edit';
$lang->resource->kanban->view               = 'view';
$lang->resource->kanban->close              = 'close';
$lang->resource->kanban->delete             = 'delete';
$lang->resource->kanban->createRegion       = 'createRegion';
$lang->resource->kanban->editRegion         = 'editRegion';
$lang->resource->kanban->performable        = 'performable';
$lang->resource->kanban->sortRegion         = 'sortRegion';
$lang->resource->kanban->sortGroup          = 'sortGroup';
$lang->resource->kanban->deleteRegion       = 'deleteRegion';
$lang->resource->kanban->createLane         = 'createLane';
$lang->resource->kanban->setLane            = 'setLane';
$lang->resource->kanban->sortLane           = 'sortLane';
$lang->resource->kanban->deleteLane         = 'deleteLane';
$lang->resource->kanban->createColumn       = 'createColumn';
$lang->resource->kanban->splitColumn        = 'splitColumn';
$lang->resource->kanban->archiveColumn      = 'archiveColumn';
$lang->resource->kanban->restoreColumn      = 'restoreColumn';
$lang->resource->kanban->setColumn          = 'setColumn';
$lang->resource->kanban->setWIP             = 'setWIP';
$lang->resource->kanban->sortColumn         = 'sortColumn';
$lang->resource->kanban->deleteColumn       = 'deleteColumn';
$lang->resource->kanban->createCard         = 'createCard';
$lang->resource->kanban->editCard           = 'editCard';
$lang->resource->kanban->viewCard           = 'viewCard';
$lang->resource->kanban->sortCard           = 'sortCard';
$lang->resource->kanban->archiveCard        = 'archiveCard';
$lang->resource->kanban->assigntoCard       = 'assigntoCard';
//$lang->resource->kanban->copyCard           = 'copyCard';
$lang->resource->kanban->deleteCard         = 'deleteCard';
$lang->resource->kanban->moveCard           = 'moveCard';
$lang->resource->kanban->setCardColor       = 'setCardColor';
$lang->resource->kanban->laneMove           = 'laneMove';
$lang->resource->kanban->viewArchivedColumn = 'viewArchivedColumn';
$lang->resource->kanban->viewArchivedCard   = 'viewArchivedCard';
$lang->resource->kanban->restoreCard        = 'restoreCard';
$lang->resource->kanban->setLaneHeight      = 'setLaneHeight';
$lang->resource->kanban->setColumnWidth     = 'setColumnWidth';
$lang->resource->kanban->batchCreateCard    = 'batchCreateCard';
$lang->resource->kanban->import             = 'import';
$lang->resource->kanban->enableArchived     = 'enableArchived';

$lang->kanban->methodOrder[5]   = 'space';
$lang->kanban->methodOrder[10]  = 'createSpace';
$lang->kanban->methodOrder[15]  = 'editSpace';
$lang->kanban->methodOrder[20]  = 'closeSpace';
$lang->kanban->methodOrder[25]  = 'deleteSpace';
$lang->kanban->methodOrder[30]  = 'sortSpace';
$lang->kanban->methodOrder[35]  = 'create';
$lang->kanban->methodOrder[40]  = 'edit';
$lang->kanban->methodOrder[45]  = 'view';
$lang->kanban->methodOrder[50]  = 'close';
$lang->kanban->methodOrder[55]  = 'delete';
$lang->kanban->methodOrder[60]  = 'createRegion';
$lang->kanban->methodOrder[65]  = 'editRegion';
$lang->kanban->methodOrder[70]  = 'sortRegion';
$lang->kanban->methodOrder[72]  = 'sortGroup';
$lang->kanban->methodOrder[75]  = 'deleteRegion';
$lang->kanban->methodOrder[80]  = 'createLane';
$lang->kanban->methodOrder[85]  = 'setLane';
$lang->kanban->methodOrder[90]  = 'sortLane';
$lang->kanban->methodOrder[95]  = 'deleteLane';
$lang->kanban->methodOrder[100] = 'createColumn';
$lang->kanban->methodorder[105] = 'splitColumn';
$lang->kanban->methodorder[110] = 'restoreColumn';
$lang->kanban->methodOrder[115] = 'setColumn';
$lang->kanban->methodOrder[120] = 'setWIP';
$lang->kanban->methodOrder[125] = 'sortColumn';
$lang->kanban->methodOrder[130] = 'deleteColumn';
$lang->kanban->methodOrder[135] = 'createCard';
$lang->kanban->methodOrder[140] = 'editCard';
$lang->kanban->methodOrder[145] = 'viewCard';
$lang->kanban->methodOrder[150] = 'sortCard';
$lang->kanban->methodOrder[155] = 'archivedCard';
//$lang->kanban->methodOrder[160] = 'copyCard';
$lang->kanban->methodOrder[165] = 'deleteCard';
$lang->kanban->methodOrder[170] = 'assigntoCard';
$lang->kanban->methodOrder[175] = 'moveCard';
$lang->kanban->methodOrder[180] = 'setCardColor';
$lang->kanban->methodOrder[185] = 'laneMove';
$lang->kanban->methodorder[190] = 'cardsSort';
$lang->kanban->methodOrder[195] = 'viewArchivedColumn';
$lang->kanban->methodorder[200] = 'viewArchivedCard';
$lang->kanban->methodorder[205] = 'archiveColumn';
$lang->kanban->methodorder[210] = 'restoreCard';
$lang->kanban->methodorder[215] = 'setLaneHeight';
$lang->kanban->methodorder[220] = 'setColumnWidth';
$lang->kanban->methodOrder[225] = 'batchCreateCard';
$lang->kanban->methodorder[230] = 'import';
$lang->kanban->methodorder[235] = 'enableArchived';

/* Execution. */
$lang->resource->execution = new stdclass();
$lang->resource->execution->view              = 'view';
$lang->resource->execution->browse            = 'browse';
$lang->resource->execution->create            = 'createExec';
$lang->resource->execution->edit              = 'editAction';
$lang->resource->execution->batchedit         = 'batchEditAction';
$lang->resource->execution->start             = 'startAction';
$lang->resource->execution->activate          = 'activateAction';
$lang->resource->execution->putoff            = 'delayAction';
$lang->resource->execution->suspend           = 'suspendAction';
$lang->resource->execution->close             = 'closeAction';
$lang->resource->execution->delete            = 'deleteAB';
$lang->resource->execution->task              = 'task';
$lang->resource->execution->grouptask         = 'groupTask';
$lang->resource->execution->importtask        = 'importTask';
$lang->resource->execution->importplanstories = 'importPlanStories';
$lang->resource->execution->importBug         = 'importBug';
$lang->resource->execution->story             = 'story';
$lang->resource->execution->build             = 'build';
//$lang->resource->execution->qa                = 'qa';
$lang->resource->execution->testtask          = 'testtask';
$lang->resource->execution->testcase          = 'testcase';
$lang->resource->execution->bug               = 'bug';
$lang->resource->execution->testreport        = 'testreport';
$lang->resource->execution->burn              = 'burn';
$lang->resource->execution->computeBurn       = 'computeBurnAction';
$lang->resource->execution->fixFirst          = 'fixFirst';
$lang->resource->execution->burnData          = 'burnData';
$lang->resource->execution->team              = 'teamAction';
$lang->resource->execution->doc               = 'doc';
$lang->resource->execution->dynamic           = 'dynamic';
$lang->resource->execution->manageProducts    = 'manageProducts';
//$lang->resource->execution->manageChilds    = 'manageChilds';
$lang->resource->execution->manageMembers     = 'manageMembers';
$lang->resource->execution->unlinkMember      = 'unlinkMember';
$lang->resource->execution->linkStory         = 'linkStory';
$lang->resource->execution->unlinkStory       = 'unlinkStory';
$lang->resource->execution->batchUnlinkStory  = 'batchUnlinkStory';
$lang->resource->execution->updateOrder       = 'updateOrder';
$lang->resource->execution->taskKanban        = 'taskKanban';
$lang->resource->execution->printKanban       = 'printKanbanAction';
$lang->resource->execution->tree              = 'treeAction';
$lang->resource->execution->treeTask          = 'treeOnlyTask';
$lang->resource->execution->treeStory         = 'treeOnlyStory';
$lang->resource->execution->all               = 'allExecutionAB';
$lang->resource->execution->export            = 'exportAction';
$lang->resource->execution->storyKanban       = 'storyKanban';
$lang->resource->execution->storySort         = 'storySort';
$lang->resource->execution->whitelist         = 'whitelist';
$lang->resource->execution->addWhitelist      = 'addWhitelist';
$lang->resource->execution->unbindWhitelist   = 'unbindWhitelist';
$lang->resource->execution->storyEstimate     = 'storyEstimate';
$lang->resource->execution->executionkanban   = 'kanbanAction';
$lang->resource->execution->kanban            = 'RDKanban';
//if($config->systemMode == 'classic') $lang->resource->project->list = 'list';

//$lang->execution->methodOrder[0]   = 'index';
//if($config->systemMode == 'classic') $lang->project->methodOrder[1] = 'list';
$lang->execution->methodOrder[5]   = 'view';
$lang->execution->methodOrder[10]  = 'browse';
$lang->execution->methodOrder[15]  = 'create';
$lang->execution->methodOrder[20]  = 'edit';
$lang->execution->methodOrder[25]  = 'batchedit';
$lang->execution->methodOrder[30]  = 'start';
$lang->execution->methodOrder[35]  = 'activate';
$lang->execution->methodOrder[40]  = 'putoff';
$lang->execution->methodOrder[45]  = 'suspend';
$lang->execution->methodOrder[50]  = 'close';
$lang->execution->methodOrder[60]  = 'delete';
$lang->execution->methodOrder[65]  = 'task';
$lang->execution->methodOrder[70]  = 'grouptask';
$lang->execution->methodOrder[75]  = 'importtask';
$lang->execution->methodOrder[80]  = 'importplanstories';
$lang->execution->methodOrder[85]  = 'importBug';
$lang->execution->methodOrder[90]  = 'story';
$lang->execution->methodOrder[95]  = 'build';
$lang->execution->methodOrder[100] = 'qa';
$lang->execution->methodOrder[105] = 'testcase';
$lang->execution->methodOrder[110] = 'bug';
$lang->execution->methodOrder[115] = 'testtask';
$lang->execution->methodOrder[120] = 'testreport';
$lang->execution->methodOrder[125] = 'burn';
$lang->execution->methodOrder[130] = 'computeBurn';
$lang->execution->methodOrder[135] = 'fixFirst';
$lang->execution->methodOrder[140] = 'burnData';
$lang->execution->methodOrder[145] = 'team';
//$lang->execution->methodOrder[130] = 'doc';
$lang->execution->methodOrder[150] = 'dynamic';
$lang->execution->methodOrder[155] = 'manageProducts';
$lang->execution->methodOrder[160] = 'manageMembers';
$lang->execution->methodOrder[165] = 'unlinkMember';
$lang->execution->methodOrder[170] = 'linkStory';
$lang->execution->methodOrder[175] = 'unlinkStory';
$lang->execution->methodOrder[180] = 'batchUnlinkStory';
$lang->execution->methodOrder[185] = 'updateOrder';
$lang->execution->methodOrder[190] = 'taskKanban';
$lang->execution->methodOrder[195] = 'printKanban';
$lang->execution->methodOrder[210] = 'tree';
$lang->execution->methodOrder[215] = 'treeTask';
$lang->execution->methodOrder[220] = 'treeStory';
$lang->execution->methodOrder[225] = 'all';
$lang->execution->methodOrder[230] = 'export';
$lang->execution->methodOrder[235] = 'storyKanban';
$lang->execution->methodOrder[240] = 'storySort';
$lang->execution->methodOrder[245] = 'whitelist';
$lang->execution->methodOrder[250] = 'addWhitelist';
$lang->execution->methodOrder[255] = 'unbindWhitelist';
$lang->execution->methodOrder[260] = 'storyEstimate';
$lang->execution->methodOrder[265] = 'executionkanban';
$lang->execution->methodOrder[270] = 'kanban';

/* Task. */
$lang->resource->task = new stdclass();
$lang->resource->task->create             = 'create';
$lang->resource->task->edit               = 'edit';
$lang->resource->task->assignTo           = 'assignAction';
$lang->resource->task->start              = 'startAction';
$lang->resource->task->pause              = 'pauseAction';
$lang->resource->task->restart            = 'restartAction';
$lang->resource->task->finish             = 'finishAction';
$lang->resource->task->cancel             = 'cancelAction';
$lang->resource->task->close              = 'closeAction';
$lang->resource->task->batchCreate        = 'batchCreate';
$lang->resource->task->batchEdit          = 'batchEdit';
$lang->resource->task->batchClose         = 'batchClose';
$lang->resource->task->batchCancel        = 'batchCancel';
$lang->resource->task->batchAssignTo      = 'batchAssignTo';
$lang->resource->task->batchChangeModule  = 'batchChangeModule';
$lang->resource->task->activate           = 'activateAction';
$lang->resource->task->delete             = 'deleteAction';
$lang->resource->task->view               = 'view';
$lang->resource->task->export             = 'exportAction';
$lang->resource->task->confirmStoryChange = 'confirmStoryChange';
$lang->resource->task->recordEstimate     = 'recordEstimateAction';
$lang->resource->task->editEstimate       = 'editEstimate';
$lang->resource->task->deleteEstimate     = 'deleteEstimate';
$lang->resource->task->report             = 'reportChart';

$lang->task->methodOrder[5]   = 'create';
$lang->task->methodOrder[10]  = 'batchCreate';
$lang->task->methodOrder[15]  = 'batchEdit';
$lang->task->methodOrder[20]  = 'edit';
$lang->task->methodOrder[25]  = 'assignTo';
$lang->task->methodOrder[30]  = 'batchAssignTo';
$lang->task->methodOrder[35]  = 'start';
$lang->task->methodOrder[40]  = 'pause';
$lang->task->methodOrder[45]  = 'restart';
$lang->task->methodOrder[50]  = 'finish';
$lang->task->methodOrder[55]  = 'cancel';
$lang->task->methodOrder[60]  = 'close';
$lang->task->methodOrder[65]  = 'batchClose';
$lang->task->methodOrder[70]  = 'activate';
$lang->task->methodOrder[75]  = 'delete';
$lang->task->methodOrder[80]  = 'view';
$lang->task->methodOrder[85]  = 'export';
$lang->task->methodOrder[90]  = 'confirmStoryChange';
$lang->task->methodOrder[95]  = 'recordEstimate';
$lang->task->methodOrder[100] = 'editEstimate';
$lang->task->methodOrder[105] = 'deleteEstimate';
$lang->task->methodOrder[110] = 'report';
$lang->task->methodOrder[115] = 'batchChangeModule';

/* Build. */
$lang->resource->build = new stdclass();
$lang->resource->build->create           = 'create';
$lang->resource->build->edit             = 'edit';
$lang->resource->build->delete           = 'delete';
$lang->resource->build->view             = 'view';
$lang->resource->build->linkStory        = 'linkStory';
$lang->resource->build->unlinkStory      = 'unlinkStory';
$lang->resource->build->batchUnlinkStory = 'batchUnlinkStory';
$lang->resource->build->linkBug          = 'linkBug';
$lang->resource->build->unlinkBug        = 'unlinkBug';
$lang->resource->build->batchUnlinkBug   = 'batchUnlinkBug';

$lang->build->methodOrder[5]  = 'create';
$lang->build->methodOrder[10] = 'edit';
$lang->build->methodOrder[15] = 'delete';
$lang->build->methodOrder[20] = 'view';
$lang->build->methodOrder[25] = 'linkStory';
$lang->build->methodOrder[30] = 'unlinkStory';
$lang->build->methodOrder[35] = 'batchUnlinkStory';
$lang->build->methodOrder[40] = 'linkBug';
$lang->build->methodOrder[45] = 'unlinkBug';
$lang->build->methodOrder[50] = 'batchUnlinkBug';

/* QA. */
$lang->resource->qa = new stdclass();
$lang->resource->qa->index = 'indexAction';

$lang->qa->methodOrder[0] = 'index';

/* Bug. */
$lang->resource->bug = new stdclass();
$lang->resource->bug->browse             = 'browse';
$lang->resource->bug->create             = 'create';
$lang->resource->bug->batchCreate        = 'batchCreate';
$lang->resource->bug->confirmBug         = 'confirmAction';
$lang->resource->bug->batchConfirm       = 'batchConfirm';
$lang->resource->bug->view               = 'view';
$lang->resource->bug->edit               = 'edit';
$lang->resource->bug->linkBugs           = 'linkBugs';
$lang->resource->bug->batchEdit          = 'batchEdit';
$lang->resource->bug->batchClose         = 'batchClose';
$lang->resource->bug->assignTo           = 'assignAction';
$lang->resource->bug->batchAssignTo      = 'batchAssignTo';
$lang->resource->bug->resolve            = 'resolveAction';
$lang->resource->bug->batchResolve       = 'batchResolve';
$lang->resource->bug->activate           = 'activateAction';
$lang->resource->bug->batchActivate      = 'batchActivate';
$lang->resource->bug->close              = 'closeAction';
$lang->resource->bug->report             = 'reportAction';
$lang->resource->bug->export             = 'exportAction';
$lang->resource->bug->confirmStoryChange = 'confirmStoryChange';
$lang->resource->bug->delete             = 'deleteAction';
$lang->resource->bug->batchChangeModule  = 'batchChangeModule';
$lang->resource->bug->batchChangeBranch  = 'batchChangeBranch';
$lang->resource->bug->batchChangePlan    = 'batchChangePlan';

$lang->bug->methodOrder[0]   = 'index';
$lang->bug->methodOrder[5]   = 'browse';
$lang->bug->methodOrder[10]  = 'create';
$lang->bug->methodOrder[15]  = 'batchCreate';
$lang->bug->methodOrder[20]  = 'batchEdit';
$lang->bug->methodOrder[25]  = 'confirmBug';
$lang->bug->methodOrder[30]  = 'batchConfirm';
$lang->bug->methodOrder[35]  = 'view';
$lang->bug->methodOrder[40]  = 'edit';
$lang->bug->methodOrder[45]  = 'assignTo';
$lang->bug->methodOrder[50]  = 'batchAssignTo';
$lang->bug->methodOrder[55]  = 'resolve';
$lang->bug->methodOrder[60]  = 'batchResolve';
$lang->bug->methodOrder[65]  = 'batchClose';
$lang->bug->methodOrder[67]  = 'batchActivate';
$lang->bug->methodOrder[70]  = 'activate';
$lang->bug->methodOrder[75]  = 'close';
$lang->bug->methodOrder[80]  = 'report';
$lang->bug->methodOrder[85]  = 'export';
$lang->bug->methodOrder[90]  = 'confirmStoryChange';
$lang->bug->methodOrder[95]  = 'delete';
$lang->bug->methodOrder[100] = 'linkBugs';
$lang->bug->methodOrder[105] = 'batchChangeModule';
$lang->bug->methodOrder[110] = 'batchChangeBranch';

/* Test case. */
$lang->resource->testcase = new stdclass();
$lang->resource->testcase->browse             = 'browse';
$lang->resource->testcase->groupCase          = 'groupCase';
$lang->resource->testcase->create             = 'create';
$lang->resource->testcase->batchCreate        = 'batchCreate';
$lang->resource->testcase->createBug          = 'createBug';
$lang->resource->testcase->view               = 'view';
$lang->resource->testcase->edit               = 'edit';
$lang->resource->testcase->linkCases          = 'linkCases';
$lang->resource->testcase->batchEdit          = 'batchEdit';
$lang->resource->testcase->delete             = 'deleteAction';
$lang->resource->testcase->batchDelete        = 'batchDelete';
$lang->resource->testcase->export             = 'exportAction';
$lang->resource->testcase->exportTemplet      = 'exportTemplet';
$lang->resource->testcase->import             = 'importAction';
$lang->resource->testcase->showImport         = 'showImport';
$lang->resource->testcase->confirmChange      = 'confirmChange';
$lang->resource->testcase->confirmStoryChange = 'confirmStoryChange';
$lang->resource->testcase->batchChangeModule  = 'batchChangeModule';
$lang->resource->testcase->batchChangeBranch  = 'batchChangeBranch';
$lang->resource->testcase->bugs               = 'bugs';
$lang->resource->testcase->review             = 'review';
$lang->resource->testcase->batchReview        = 'batchReview';
$lang->resource->testcase->importFromLib      = 'importFromLib';
$lang->resource->testcase->batchCaseTypeChange = 'batchCaseTypeChange';
$lang->resource->testcase->confirmLibcaseChange    = 'confirmLibcaseChange';
$lang->resource->testcase->ignoreLibcaseChange     = 'ignoreLibcaseChange';
$lang->resource->testcase->batchConfirmStoryChange = 'batchConfirmStoryChange';

$lang->testcase->methodOrder[0]   = 'index';
$lang->testcase->methodOrder[5]   = 'browse';
$lang->testcase->methodOrder[10]  = 'groupCase';
$lang->testcase->methodOrder[15]  = 'create';
$lang->testcase->methodOrder[20]  = 'batchCreate';
$lang->testcase->methodOrder[25]  = 'createBug';
$lang->testcase->methodOrder[30]  = 'view';
$lang->testcase->methodOrder[35]  = 'edit';
$lang->testcase->methodOrder[40]  = 'delete';
$lang->testcase->methodOrder[45]  = 'export';
$lang->testcase->methodOrder[50]  = 'confirmChange';
$lang->testcase->methodOrder[55]  = 'confirmStoryChange';
$lang->testcase->methodOrder[60]  = 'batchEdit';
$lang->testcase->methodOrder[65]  = 'batchDelete';
$lang->testcase->methodOrder[70]  = 'batchChangeModule';
$lang->testcase->methodOrder[75]  = 'batchChangeBranch';
$lang->testcase->methodOrder[80]  = 'linkCases';
$lang->testcase->methodOrder[90]  = 'bugs';
$lang->testcase->methodOrder[95]  = 'review';
$lang->testcase->methodOrder[100] = 'batchReview';
$lang->testcase->methodOrder[105] = 'batchConfirmStoryChange';
$lang->testcase->methodOrder[110] = 'importFromLib';
$lang->testcase->methodOrder[115] = 'batchCaseTypeChange';

/* Test task. */
$lang->resource->testtask = new stdclass();
$lang->resource->testtask->create           = 'create';
$lang->resource->testtask->browse           = 'browse';
$lang->resource->testtask->view             = 'viewAction';
$lang->resource->testtask->cases            = 'casesAction';
$lang->resource->testtask->groupCase        = 'groupCase';
$lang->resource->testtask->edit             = 'edit';
$lang->resource->testtask->start            = 'startAction';
$lang->resource->testtask->close            = 'closeAction';
$lang->resource->testtask->delete           = 'delete';
$lang->resource->testtask->batchAssign      = 'batchAssign';
$lang->resource->testtask->linkcase         = 'linkCase';
$lang->resource->testtask->unlinkcase       = 'lblUnlinkCase';
$lang->resource->testtask->batchUnlinkCases = 'batchUnlinkCases';
$lang->resource->testtask->runcase          = 'lblRunCase';
$lang->resource->testtask->results          = 'resultsAction';
$lang->resource->testtask->batchRun         = 'batchRun';
$lang->resource->testtask->activate         = 'activateAction';
$lang->resource->testtask->block            = 'blockAction';
$lang->resource->testtask->report           = 'reportAction';
$lang->resource->testtask->browseUnits      = 'browseUnits';
$lang->resource->testtask->unitCases        = 'unitCases';
$lang->resource->testtask->importUnitResult = 'importUnitResult';

$lang->testtask->methodOrder[0]   = 'index';
$lang->testtask->methodOrder[5]   = 'create';
$lang->testtask->methodOrder[10]  = 'browse';
$lang->testtask->methodOrder[15]  = 'view';
$lang->testtask->methodOrder[20]  = 'cases';
$lang->testtask->methodOrder[25]  = 'groupCase';
$lang->testtask->methodOrder[30]  = 'edit';
$lang->testtask->methodOrder[35]  = 'start';
$lang->testtask->methodOrder[40]  = 'activate';
$lang->testtask->methodOrder[45]  = 'block';
$lang->testtask->methodOrder[50]  = 'close';
$lang->testtask->methodOrder[55]  = 'delete';
$lang->testtask->methodOrder[60]  = 'batchAssign';
$lang->testtask->methodOrder[65]  = 'linkcase';
$lang->testtask->methodOrder[70]  = 'unlinkcase';
$lang->testtask->methodOrder[75]  = 'runcase';
$lang->testtask->methodOrder[80]  = 'results';
$lang->testtask->methodOrder[85]  = 'batchUnlinkCases';
$lang->testtask->methodOrder[90]  = 'report';
$lang->testtask->methodOrder[95]  = 'browseUnits';
$lang->testtask->methodOrder[100] = 'unitCases';
$lang->testtask->methodOrder[105] = 'importUnitResult';

$lang->resource->testreport = new stdclass();
$lang->resource->testreport->browse     = 'browse';
$lang->resource->testreport->create     = 'create';
$lang->resource->testreport->view       = 'view';
$lang->resource->testreport->delete     = 'delete';
$lang->resource->testreport->edit       = 'edit';

$lang->testreport->methodOrder[0]  = 'browse';
$lang->testreport->methodOrder[5]  = 'create';
$lang->testreport->methodOrder[10] = 'view';
$lang->testreport->methodOrder[15] = 'delete';
$lang->testreport->methodOrder[20] = 'edit';

$lang->resource->testsuite = new stdclass();
$lang->resource->testsuite->index            = 'index';
$lang->resource->testsuite->browse           = 'browse';
$lang->resource->testsuite->create           = 'create';
$lang->resource->testsuite->view             = 'view';
$lang->resource->testsuite->edit             = 'edit';
$lang->resource->testsuite->delete           = 'delete';
$lang->resource->testsuite->linkCase         = 'linkCase';
$lang->resource->testsuite->unlinkCase       = 'unlinkCaseAction';
$lang->resource->testsuite->batchUnlinkCases = 'batchUnlinkCases';

$lang->testsuite->methodOrder[0]  = 'index';
$lang->testsuite->methodOrder[5]  = 'browse';
$lang->testsuite->methodOrder[10] = 'create';
$lang->testsuite->methodOrder[15] = 'view';
$lang->testsuite->methodOrder[20] = 'edit';
$lang->testsuite->methodOrder[25] = 'delete';
$lang->testsuite->methodOrder[30] = 'linkCase';
$lang->testsuite->methodOrder[35] = 'unlinkCase';
$lang->testsuite->methodOrder[40] = 'batchUnlinkCases';

$lang->resource->caselib = new stdclass();
$lang->resource->caselib->index            = 'index';
$lang->resource->caselib->browse           = 'browseAction';
$lang->resource->caselib->create           = 'create';
$lang->resource->caselib->edit             = 'edit';
$lang->resource->caselib->delete           = 'deleteAction';
$lang->resource->caselib->view             = 'view';
$lang->resource->caselib->createCase       = 'createCase';
$lang->resource->caselib->batchCreateCase  = 'batchCreateCase';
$lang->resource->caselib->exportTemplet    = 'exportTemplet';
$lang->resource->caselib->import           = 'importAction';
$lang->resource->caselib->showImport       = 'showImport';

$lang->caselib->methodOrder[0]  = 'index';
$lang->caselib->methodOrder[5]  = 'browse';
$lang->caselib->methodOrder[10] = 'create';
$lang->caselib->methodOrder[15] = 'edit';
$lang->caselib->methodOrder[20] = 'delete';
$lang->caselib->methodOrder[25] = 'view';
$lang->caselib->methodOrder[30] = 'createCase';
$lang->caselib->methodOrder[35] = 'batchCreateCase';
$lang->caselib->methodOrder[40] = 'exportTemplet';
$lang->caselib->methodOrder[45] = 'import';
$lang->caselib->methodOrder[50] = 'showImport';

$lang->resource->automation = new stdclass();
$lang->resource->automation->browse = 'browse';

$lang->automation->methodOrder[0] = 'browse';

$lang->resource->repo                  = new stdclass();
$lang->resource->repo->browse          = 'browseAction';
$lang->resource->repo->view            = 'view';
$lang->resource->repo->log             = 'log';
$lang->resource->repo->revision        = 'revisionAction';
$lang->resource->repo->blame           = 'blameAction';
$lang->resource->repo->create          = 'createAction';
$lang->resource->repo->edit            = 'editAction';
$lang->resource->repo->delete          = 'delete';
$lang->resource->repo->showSyncCommit  = 'showSyncCommit';
$lang->resource->repo->diff            = 'diffAction';
$lang->resource->repo->download        = 'downloadAction';
$lang->resource->repo->maintain        = 'maintain';
$lang->resource->repo->setRules        = 'setRules';
$lang->resource->repo->apiGetRepoByUrl = 'apiGetRepoByUrl';

$lang->repo->methodOrder[5]  = 'create';
$lang->repo->methodOrder[10] = 'edit';
$lang->repo->methodOrder[15] = 'delete';
$lang->repo->methodOrder[20] = 'showSyncCommit';
$lang->repo->methodOrder[25] = 'maintain';
$lang->repo->methodOrder[30] = 'browse';
$lang->repo->methodOrder[35] = 'view';
$lang->repo->methodOrder[40] = 'diff';
$lang->repo->methodOrder[45] = 'log';
$lang->repo->methodOrder[50] = 'revision';
$lang->repo->methodOrder[55] = 'blame';
$lang->repo->methodOrder[60] = 'download';
$lang->repo->methodOrder[65] = 'setRules';
$lang->repo->methodOrder[70] = 'apiGetRepoByUrl';

$lang->resource->ci = new stdclass();
$lang->resource->ci->commitResult       = 'commitResult';
$lang->resource->ci->checkCompileStatus = 'checkCompileStatus';

$lang->ci->methodOrder[5]  = 'commitResult';
$lang->ci->methodOrder[10] = 'checkCompileStatus';

$lang->resource->compile = new stdclass();
$lang->resource->compile->browse = 'browse';
$lang->resource->compile->logs   = 'logs';

$lang->compile->methodOrder[5]  = 'browse';
$lang->compile->methodOrder[10] = 'logs';

$lang->resource->jenkins = new stdclass();
$lang->resource->jenkins->browse = 'browseAction';
$lang->resource->jenkins->create = 'create';
$lang->resource->jenkins->edit   = 'edit';
$lang->resource->jenkins->delete = 'deleteAction';

$lang->jenkins->methodOrder[5]  = 'browse';
$lang->jenkins->methodOrder[10] = 'create';
$lang->jenkins->methodOrder[15] = 'edit';
$lang->jenkins->methodOrder[20] = 'delete';

$lang->resource->job = new stdclass();
$lang->resource->job->browse = 'browseAction';
$lang->resource->job->create = 'create';
$lang->resource->job->edit   = 'edit';
$lang->resource->job->delete = 'delete';
$lang->resource->job->exec   = 'exec';
$lang->resource->job->view   = 'view';

$lang->job->methodOrder[5]  = 'browse';
$lang->job->methodOrder[10] = 'create';
$lang->job->methodOrder[15] = 'edit';
$lang->job->methodOrder[20] = 'delete';
$lang->job->methodOrder[25] = 'exec';

/* Doc. */
$lang->resource->doc = new stdclass();
$lang->resource->doc->index         = 'index';
$lang->resource->doc->browse        = 'browse';
$lang->resource->doc->createLib     = 'createLib';
$lang->resource->doc->editLib       = 'editLib';
$lang->resource->doc->deleteLib     = 'deleteLib';
$lang->resource->doc->create        = 'create';
$lang->resource->doc->view          = 'view';
$lang->resource->doc->edit          = 'edit';
$lang->resource->doc->delete        = 'delete';
$lang->resource->doc->deleteFile    = 'deleteFile';
$lang->resource->doc->allLibs       = 'allLibs';
$lang->resource->doc->objectLibs    = 'objectLibs';
$lang->resource->doc->collect       = 'collectAction';
$lang->resource->doc->tableContents = 'tableContents';
$lang->resource->doc->showFiles     = 'showFiles';

$lang->doc->methodOrder[0]  = 'index';
$lang->doc->methodOrder[5]  = 'browse';
$lang->doc->methodOrder[10] = 'createLib';
$lang->doc->methodOrder[15] = 'editLib';
$lang->doc->methodOrder[20] = 'deleteLib';
$lang->doc->methodOrder[25] = 'create';
$lang->doc->methodOrder[30] = 'view';
$lang->doc->methodOrder[35] = 'edit';
$lang->doc->methodOrder[40] = 'delete';
$lang->doc->methodOrder[45] = 'deleteFile';
$lang->doc->methodOrder[50] = 'allLibs';
$lang->doc->methodOrder[55] = 'objectLibs';
$lang->doc->methodOrder[60] = 'collect';
$lang->doc->methodOrder[65] = 'tableContents';
$lang->doc->methodOrder[70] = 'showFiles';

/* Mail. */
$lang->resource->mail = new stdclass();
$lang->resource->mail->index  = 'index';
$lang->resource->mail->detect = 'detectAction';
$lang->resource->mail->edit   = 'edit';
$lang->resource->mail->save   = 'saveAction';
$lang->resource->mail->test   = 'test';
$lang->resource->mail->reset  = 'resetAction';
$lang->resource->mail->browse = 'browse';
$lang->resource->mail->delete = 'delete';
$lang->resource->mail->resend = 'resendAction';
$lang->resource->mail->batchDelete   = 'batchDelete';
$lang->resource->mail->sendCloud     = 'sendCloud';
$lang->resource->mail->sendcloudUser = 'sendcloudUser';
$lang->resource->mail->ztCloud       = 'ztCloud';

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
$lang->mail->methodOrder[55] = 'sendCloud';
$lang->mail->methodOrder[60] = 'sendcloudUser';
$lang->mail->methodOrder[65] = 'ztCloud';

/* Custom. */
$lang->resource->custom = new stdclass();
$lang->resource->custom->index              = 'index';
$lang->resource->custom->set                = 'set';
$lang->resource->custom->product            = 'product';
$lang->resource->custom->execution          = 'execution';
$lang->resource->custom->required           = 'required';
$lang->resource->custom->restore            = 'restore';
$lang->resource->custom->flow               = 'flow';
$lang->resource->custom->working            = 'working';
$lang->resource->custom->setPublic          = 'setPublic';
$lang->resource->custom->timezone           = 'timezone';
$lang->resource->custom->setStoryConcept    = 'setStoryConcept';
$lang->resource->custom->editStoryConcept   = 'editStoryConcept';
$lang->resource->custom->browseStoryConcept = 'browseStoryConcept';
$lang->resource->custom->setDefaultConcept  = 'setDefaultConcept';
$lang->resource->custom->deleteStoryConcept = 'deleteStoryConcept';

$lang->custom->methodOrder[5]  = 'index';
$lang->custom->methodOrder[10] = 'set';
$lang->custom->methodOrder[15] = 'product';
$lang->custom->methodOrder[20] = 'execution';
$lang->custom->methodOrder[25] = 'required';
$lang->custom->methodOrder[30] = 'restore';
$lang->custom->methodOrder[35] = 'flow';
$lang->custom->methodOrder[40] = 'working';
$lang->custom->methodOrder[45] = 'setPublic';
$lang->custom->methodOrder[50] = 'timezone';
$lang->custom->methodOrder[55] = 'setStoryConcept';
$lang->custom->methodOrder[60] = 'editStoryConcept';
$lang->custom->methodOrder[65] = 'browseStoryConcept';
$lang->custom->methodOrder[70] = 'setDefaultConcept';
$lang->custom->methodOrder[75] = 'deleteStoryConcept';

$lang->resource->datatable = new stdclass();
$lang->resource->datatable->setGlobal = 'setGlobal';

$lang->datatable->methodOrder[5]  = 'setGlobal';

/* Subversion. */
$lang->resource->svn = new stdclass();
$lang->resource->svn->diff    = 'diff';
$lang->resource->svn->cat     = 'cat';
$lang->resource->svn->apiSync = 'apiSync';

$lang->svn->methodOrder[5]  = 'diff';
$lang->svn->methodOrder[10] = 'cat';
$lang->svn->methodOrder[15] = 'apiSync';

/* GitLab. */
$lang->resource->gitlab = new stdclass();
$lang->resource->gitlab->browse               = 'browse';
$lang->resource->gitlab->create               = 'create';
$lang->resource->gitlab->edit                 = 'edit';
$lang->resource->gitlab->view                 = 'view';
$lang->resource->gitlab->importIssue          = 'importIssue';
$lang->resource->gitlab->delete               = 'delete';
$lang->resource->gitlab->bindUser             = 'bindUser';
$lang->resource->gitlab->bindProduct          = 'bindProduct';
$lang->resource->gitlab->browseProject        = 'browseProject';
$lang->resource->gitlab->createProject        = 'createProject';
$lang->resource->gitlab->editProject          = 'editProject';
$lang->resource->gitlab->deleteProject        = 'deleteProject';
$lang->resource->gitlab->browseGroup          = 'browseGroup';
$lang->resource->gitlab->createGroup          = 'createGroup';
$lang->resource->gitlab->editGroup            = 'editGroup';
$lang->resource->gitlab->deleteGroup          = 'deleteGroup';
$lang->resource->gitlab->manageGroupMembers   = 'manageGroupMembers';
$lang->resource->gitlab->browseUser           = 'browseUser';
$lang->resource->gitlab->createUser           = 'createUser';
$lang->resource->gitlab->editUser             = 'editUser';
$lang->resource->gitlab->deleteUser           = 'deleteUser';
$lang->resource->gitlab->createBranch         = 'createBranch';
$lang->resource->gitlab->browseBranch         = 'browseBranch';
$lang->resource->gitlab->webhook              = 'webhook';
$lang->resource->gitlab->createWebhook        = 'createWebhook';
$lang->resource->gitlab->manageProjectMembers = 'manageProjectMembers';
$lang->resource->gitlab->browseBranchPriv     = 'browseBranchPriv';
$lang->resource->gitlab->createBranchPriv     = 'createBranchPriv';
$lang->resource->gitlab->editBranchPriv       = 'editBranchPriv';
$lang->resource->gitlab->deleteBranchPriv     = 'deleteBranchPriv';
$lang->resource->gitlab->browseTag            = 'browseTag';
$lang->resource->gitlab->createTag            = 'createTag';
$lang->resource->gitlab->deleteTag            = 'deleteTag';
$lang->resource->gitlab->browseTagPriv        = 'browseTagPriv';
$lang->resource->gitlab->createTagPriv        = 'createTagPriv';
$lang->resource->gitlab->editTagPriv          = 'editTagPriv';
$lang->resource->gitlab->deleteTagPriv        = 'deleteTagPriv';

$lang->gitlab->methodOrder[5]   = 'browse';
$lang->gitlab->methodOrder[10]  = 'create';
$lang->gitlab->methodOrder[15]  = 'edit';
$lang->gitlab->methodOrder[20]  = 'view';
$lang->gitlab->methodOrder[25]  = 'importIssue';
$lang->gitlab->methodOrder[30]  = 'delete';
$lang->gitlab->methodOrder[35]  = 'bindUser';
$lang->gitlab->methodOrder[45]  = 'browseProject';
$lang->gitlab->methodOrder[50]  = 'createProject';
$lang->gitlab->methodOrder[55]  = 'editProject';
$lang->gitlab->methodOrder[60]  = 'deleteProject';
$lang->gitlab->methodOrder[65]  = 'browseGroup';
$lang->gitlab->methodOrder[70]  = 'createGroup';
$lang->gitlab->methodOrder[75]  = 'editGroup';
$lang->gitlab->methodOrder[80]  = 'deleteGroup';
$lang->gitlab->methodOrder[85]  = 'manageGroupMembers';
$lang->gitlab->methodOrder[90]  = 'browseUser';
$lang->gitlab->methodOrder[95]  = 'createUser';
$lang->gitlab->methodOrder[100] = 'editUser';
$lang->gitlab->methodOrder[105] = 'deleteUser';
$lang->gitlab->methodOrder[110] = 'createBranch';
$lang->gitlab->methodOrder[115] = 'browseBranch';
$lang->gitlab->methodOrder[120] = 'webhook';
$lang->gitlab->methodOrder[125] = 'createWebhook';
$lang->gitlab->methodOrder[130] = 'manageProjectMembers';
$lang->gitlab->methodOrder[135] = 'browseTag';
$lang->gitlab->methodOrder[140] = 'browseTagPriv';
$lang->gitlab->methodOrder[145] = 'deleteTagPriv';

/* SonarQube. */
$lang->resource->sonarqube = new stdclass();
$lang->resource->sonarqube->browse        = 'browse';
$lang->resource->sonarqube->create        = 'create';
$lang->resource->sonarqube->edit          = 'edit';
$lang->resource->sonarqube->delete        = 'delete';
$lang->resource->sonarqube->browseProject = 'browseProject';
$lang->resource->sonarqube->createProject = 'createProject';
$lang->resource->sonarqube->deleteProject = 'deleteProject';
$lang->resource->sonarqube->execJob       = 'execJob';
$lang->resource->sonarqube->reportView    = 'reportView';
$lang->resource->sonarqube->browseIssue   = 'browseIssue';

$lang->sonarqube->methodOrder[5]  = 'browse';
$lang->sonarqube->methodOrder[10] = 'create';
$lang->sonarqube->methodOrder[15] = 'edit';
$lang->sonarqube->methodOrder[20] = 'delete';
$lang->sonarqube->methodOrder[25] = 'browseProject';
$lang->sonarqube->methodOrder[30] = 'createProject';
$lang->sonarqube->methodOrder[35] = 'deleteProject';
$lang->sonarqube->methodOrder[40] = 'execJob';
$lang->sonarqube->methodOrder[45] = 'reportView';
$lang->sonarqube->methodOrder[50] = 'browseIssue';

/* merge request. */
$lang->resource->mr = new stdclass();
$lang->resource->mr->create    = 'create';
$lang->resource->mr->apiCreate = 'apiCreate';
$lang->resource->mr->browse    = 'browse';
$lang->resource->mr->edit      = 'edit';
$lang->resource->mr->delete    = 'delete';
$lang->resource->mr->view      = 'view';
$lang->resource->mr->accept    = 'accept';
$lang->resource->mr->diff      = 'viewDiff';
$lang->resource->mr->link      = 'linkList';
$lang->resource->mr->linkStory = 'linkStory';
$lang->resource->mr->linkBug   = 'linkBug';
$lang->resource->mr->linkTask  = 'linkTask';
$lang->resource->mr->unlink    = 'unlink';
$lang->resource->mr->approval  = 'approval';
$lang->resource->mr->close     = 'close';
$lang->resource->mr->reopen    = 'reopen';
$lang->resource->mr->addReview = 'addReview';

$lang->mr->methodOrder[10] = 'create';
$lang->mr->methodOrder[15] = 'browse';
$lang->mr->methodOrder[20] = 'edit';
$lang->mr->methodOrder[25] = 'delete';
$lang->mr->methodOrder[35] = 'view';
$lang->mr->methodOrder[45] = 'accept';
$lang->mr->methodOrder[50] = 'diff';
$lang->mr->methodOrder[55] = 'link';
$lang->mr->methodOrder[60] = 'linkStory';
$lang->mr->methodOrder[65] = 'linkBug';
$lang->mr->methodOrder[70] = 'linkTask';
$lang->mr->methodOrder[75] = 'unlink';
$lang->mr->methodOrder[80] = 'approval';
$lang->mr->methodOrder[85] = 'close';
$lang->mr->methodOrder[90] = 'reopen';
$lang->mr->methodOrder[95] = 'addReview';

/* Git. */
$lang->resource->git = new stdclass();
$lang->resource->git->diff    = 'diff';
$lang->resource->git->cat     = 'cat';
$lang->resource->git->apiSync = 'apiSync';

$lang->git->methodOrder[5]  = 'diff';
$lang->git->methodOrder[10] = 'cat';
$lang->git->methodOrder[15] = 'apiSync';

/* Company. */
$lang->resource->company = new stdclass();
$lang->resource->company->index  = 'index';
$lang->resource->company->browse = 'browse';
$lang->resource->company->edit   = 'edit';
$lang->resource->company->view   = 'view';
$lang->resource->company->dynamic= 'dynamic';

$lang->company->methodOrder[0]  = 'index';
$lang->company->methodOrder[5]  = 'browse';
$lang->company->methodOrder[15] = 'edit';
$lang->company->methodOrder[25] = 'dynamic';

/* Department. */
$lang->resource->dept = new stdclass();
$lang->resource->dept->browse      = 'browse';
$lang->resource->dept->updateOrder = 'updateOrder';
$lang->resource->dept->manageChild = 'manageChild';
$lang->resource->dept->edit        = 'edit';
$lang->resource->dept->delete      = 'delete';

$lang->dept->methodOrder[5]  = 'browse';
$lang->dept->methodOrder[10] = 'updateOrder';
$lang->dept->methodOrder[15] = 'manageChild';
$lang->dept->methodOrder[20] = 'edit';
$lang->dept->methodOrder[25] = 'delete';

/* Group. */
$lang->resource->group = new stdclass();
$lang->resource->group->browse             = 'browse';
$lang->resource->group->create             = 'create';
$lang->resource->group->edit               = 'edit';
$lang->resource->group->copy               = 'copy';
$lang->resource->group->delete             = 'delete';
$lang->resource->group->manageView         = 'manageView';
$lang->resource->group->managePriv         = 'managePriv';
$lang->resource->group->manageMember       = 'manageMember';
$lang->resource->group->manageProjectAdmin = 'manageProjectAdmin';

$lang->group->methodOrder[5]  = 'browse';
$lang->group->methodOrder[10] = 'create';
$lang->group->methodOrder[15] = 'edit';
$lang->group->methodOrder[20] = 'copy';
$lang->group->methodOrder[25] = 'delete';
$lang->group->methodOrder[30] = 'managePriv';
$lang->group->methodOrder[35] = 'manageMember';
$lang->group->methodOrder[40] = 'manageProjectAdmin';

/* User. */
$lang->resource->user = new stdclass();
$lang->resource->user->create         = 'create';
$lang->resource->user->batchCreate    = 'batchCreate';
$lang->resource->user->view           = 'view';
$lang->resource->user->edit           = 'edit';
$lang->resource->user->unlock         = 'unlock';
$lang->resource->user->delete         = 'delete';
$lang->resource->user->todo           = 'todo';
$lang->resource->user->story          = 'story';
$lang->resource->user->task           = 'task';
$lang->resource->user->bug            = 'bug';
$lang->resource->user->testTask       = 'testTask';
$lang->resource->user->testCase       = 'testCase';
$lang->resource->user->execution      = 'execution';
$lang->resource->user->dynamic        = 'dynamic';
$lang->resource->user->cropAvatar     = 'cropAvatar';
$lang->resource->user->profile        = 'profile';
$lang->resource->user->batchEdit      = 'batchEdit';
$lang->resource->user->unbind         = 'unbind';
$lang->resource->user->setPublicTemplate = 'setPublicTemplate';

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
$lang->user->methodOrder[65] = 'cropAvatar';
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

/* Report. */
$lang->resource->report = new stdclass();
$lang->resource->report->index            = 'index';
$lang->resource->report->projectDeviation = 'projectDeviation';
$lang->resource->report->productSummary   = 'productSummary';
$lang->resource->report->bugCreate        = 'bugCreate';
$lang->resource->report->bugAssign        = 'bugAssign';
$lang->resource->report->workload         = 'workload';
$lang->resource->report->annualData       = 'annual';

$lang->report->methodOrder[0]  = 'index';
$lang->report->methodOrder[5]  = 'projectDeviation';
$lang->report->methodOrder[10] = 'productSummary';
$lang->report->methodOrder[15] = 'bugCreate';
$lang->report->methodOrder[20] = 'workload';
$lang->report->methodOrder[25] = 'annual';

/* Search. */
$lang->resource->search = new stdclass();
$lang->resource->search->buildForm   = 'buildForm';
$lang->resource->search->buildQuery  = 'buildQuery';
$lang->resource->search->saveQuery   = 'saveQuery';
$lang->resource->search->deleteQuery = 'deleteQuery';
$lang->resource->search->select      = 'select';
$lang->resource->search->index       = 'index';
$lang->resource->search->buildIndex  = 'buildIndex';

$lang->search->methodOrder[5]  = 'buildForm';
$lang->search->methodOrder[10] = 'buildQuery';
$lang->search->methodOrder[15] = 'saveQuery';
$lang->search->methodOrder[20] = 'deleteQuery';
$lang->search->methodOrder[25] = 'select';
$lang->search->methodOrder[30] = 'index';
$lang->search->methodOrder[35] = 'buildIndex';

/* Admin. */
$lang->resource->admin = new stdclass();
$lang->resource->admin->index     = 'index';
$lang->resource->admin->checkDB   = 'checkDB';
$lang->resource->admin->safe      = 'safeIndex';
$lang->resource->admin->checkWeak = 'checkWeak';
$lang->resource->admin->sso       = 'ssoAction';
$lang->resource->admin->register  = 'register';
$lang->resource->admin->ztCompany = 'ztCompany';

$lang->admin->methodOrder[0]  = 'index';
$lang->admin->methodOrder[5]  = 'checkDB';
$lang->admin->methodOrder[10] = 'safeIndex';
$lang->admin->methodOrder[15] = 'checkWeak';
$lang->admin->methodOrder[20] = 'sso';
$lang->admin->methodOrder[25] = 'register';
$lang->admin->methodOrder[30] = 'ztCompany';

/* Extension. */
$lang->resource->extension = new stdclass();
$lang->resource->extension->browse     = 'browseAction';
$lang->resource->extension->obtain     = 'obtain';
$lang->resource->extension->structure  = 'structureAction';
$lang->resource->extension->install    = 'install';
$lang->resource->extension->uninstall  = 'uninstallAction';
$lang->resource->extension->activate   = 'activateAction';
$lang->resource->extension->deactivate = 'deactivateAction';
$lang->resource->extension->upload     = 'upload';
$lang->resource->extension->erase      = 'eraseAction';
$lang->resource->extension->upgrade    = 'upgrade';

$lang->extension->methodOrder[5]  = 'browse';
$lang->extension->methodOrder[10] = 'obtain';
$lang->extension->methodOrder[15] = 'structure';
$lang->extension->methodOrder[20] = 'install';
$lang->extension->methodOrder[25] = 'uninstall';
$lang->extension->methodOrder[30] = 'activate';
$lang->extension->methodOrder[35] = 'deactivate';
$lang->extension->methodOrder[40] = 'upload';
$lang->extension->methodOrder[45] = 'erase';
$lang->extension->methodOrder[50] = 'upgrade';

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

/* Others. */
$lang->resource->api = new stdclass();
$lang->resource->api->index         = 'index';
$lang->resource->api->createLib     = 'createLib';
$lang->resource->api->editLib       = 'editLib';
$lang->resource->api->deleteLib     = 'deleteLib';
$lang->resource->api->createRelease = 'createRelease';
$lang->resource->api->releases      = 'releases';
$lang->resource->api->deleteRelease = 'deleteRelease';
$lang->resource->api->struct        = 'struct';
$lang->resource->api->createStruct  = 'createStruct';
$lang->resource->api->editStruct    = 'editStruct';
$lang->resource->api->deleteStruct  = 'deleteStruct';
$lang->resource->api->create        = 'create';
$lang->resource->api->edit          = 'edit';
$lang->resource->api->delete        = 'delete';

$lang->resource->api->getModel     = 'getModel';
$lang->resource->api->debug        = 'debug';
$lang->resource->api->sql          = 'sql';

$lang->api->methodOrder[0]  = 'index';
$lang->api->methodOrder[5]  = 'createLib';
$lang->api->methodOrder[10] = 'editLib';
$lang->api->methodOrder[15] = 'deleteLib';
$lang->api->methodOrder[20] = 'createRelease';
$lang->api->methodOrder[25] = 'releases';
$lang->api->methodOrder[30] = 'deleteRelease';
$lang->api->methodOrder[35] = 'struct';
$lang->api->methodOrder[40] = 'createStruct';
$lang->api->methodOrder[45] = 'editStruct';
$lang->api->methodOrder[50] = 'deleteStruct';
$lang->api->methodOrder[55] = 'create';
$lang->api->methodOrder[60] = 'edit';
$lang->api->methodOrder[65] = 'delete';
$lang->api->methodOrder[70] = 'getModel';
$lang->api->methodOrder[75] = 'debug';
$lang->api->methodOrder[80] = 'sql';

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

$lang->resource->misc = new stdclass();
$lang->resource->misc->ping = 'ping';

$lang->misc->methodOrder[5] = 'ping';

$lang->resource->message = new stdclass();
$lang->resource->message->index   = 'index';
$lang->resource->message->browser = 'browser';
$lang->resource->message->setting = 'setting';

$lang->message->methodOrder[5]  = 'index';
$lang->message->methodOrder[10] = 'browser';
$lang->message->methodOrder[15] = 'setting';

$lang->resource->action = new stdclass();
$lang->resource->action->trash    = 'trash';
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
$lang->resource->backup->restore     = 'restore';
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

$lang->resource->cron = new stdclass();
$lang->resource->cron->index       = 'index';
$lang->resource->cron->turnon      = 'turnon';
$lang->resource->cron->create      = 'createAction';
$lang->resource->cron->edit        = 'edit';
$lang->resource->cron->toggle      = 'toggle';
$lang->resource->cron->delete      = 'delete';
$lang->resource->cron->openProcess = 'restart';

$lang->cron->methodOrder[5]  = 'index';
$lang->cron->methodOrder[10] = 'turnon';
$lang->cron->methodOrder[15] = 'create';
$lang->cron->methodOrder[20] = 'edit';
$lang->cron->methodOrder[25] = 'toggle';
$lang->cron->methodOrder[30] = 'delete';
$lang->cron->methodOrder[35] = 'openProcess';

$lang->resource->dev = new stdclass();
$lang->resource->dev->api       = 'api';
$lang->resource->dev->db        = 'db';
$lang->resource->dev->editor    = 'editor';
$lang->resource->dev->translate = 'translate';

$lang->dev->methodOrder[5]  = 'api';
$lang->dev->methodOrder[10] = 'db';
$lang->dev->methodOrder[15] = 'editor';
$lang->dev->methodOrder[20] = 'translate';

include (dirname(__FILE__) . '/changelog.php');
