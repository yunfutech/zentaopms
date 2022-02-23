<?php
/* Fields. */
$lang->program->id           = 'ID';
$lang->program->name         = 'Name';
$lang->program->template     = 'Template';
$lang->program->category     = 'Type';
$lang->program->desc         = 'Description';
$lang->program->status       = 'Status';
$lang->program->PM           = 'Manager';
$lang->program->budget       = 'Budget';
$lang->program->budgetUnit   = 'Budget Unit';
$lang->program->begin        = 'Begin';
$lang->program->end          = 'End';
$lang->program->stage        = 'Stage';
$lang->program->type         = 'Type';
$lang->program->pri          = 'Priority';
$lang->program->parent       = 'Parent';
$lang->program->exchangeRate = 'Exchange Rate';
$lang->program->openedBy     = 'OpenedBy';
$lang->program->openedDate   = 'OpenedDate';
$lang->program->closedBy     = 'ClosedBy';
$lang->program->closedDate   = 'ClosedDate';
$lang->program->canceledBy   = 'CanceledBy';
$lang->program->canceledDate = 'CanceledDate';
$lang->program->team         = 'Team';
$lang->program->order        = 'Rank';
$lang->program->days         = 'Days';
$lang->program->acl          = 'Access Control';
$lang->program->whitelist    = 'WhiteList';
$lang->program->deleted      = 'Deleted';

/* Actions. */
$lang->program->common                  = 'Program';
$lang->program->index                   = 'Home';
$lang->program->create                  = 'Create Program';
$lang->program->createGuide             = 'Select Template';
$lang->program->edit                    = 'Edit Program';
$lang->program->browse                  = 'Programs';
$lang->program->kanbanAction            = 'Kanban';
$lang->program->view                    = 'Program Detail';
$lang->program->copy                    = 'Copy Program';
$lang->program->product                 = 'Products';
$lang->program->project                 = 'Program Project List';
$lang->program->all                     = 'All Programs';
$lang->program->start                   = 'Start';
$lang->program->finish                  = 'Finish';
$lang->program->suspend                 = 'Suspend';
$lang->program->delete                  = 'Delete';
$lang->program->close                   = 'Close';
$lang->program->activate                = 'Activate';
$lang->program->export                  = 'Export';
$lang->program->stakeholder             = 'Stakeholder';
$lang->program->createStakeholder       = 'Create Stakeholder';
$lang->program->unlinkStakeholder       = 'Unlink Stakeholder';
$lang->program->batchUnlinkStakeholders = 'Batch Remove Stakeholder';
$lang->program->unlink                  = 'Unlink';
$lang->program->updateOrder             = 'Rank';
$lang->program->unbindWhitelist         = 'Unbind Whitelist';
$lang->program->importStakeholder       = 'Import from program';
$lang->program->manageMembers           = 'Program Team';
$lang->program->confirmChangePRJUint    = 'Synchronize the budget unit of the subprograms and the projects of the program? If yes, please the current exchange rate.';
$lang->program->exRateNotNegative       = 'The『exchange rate』should not be negative.';
$lang->program->changePRJUnit           = 'Update the budget unit of the project';

$lang->program->progress         = 'Progress';
$lang->program->children         = 'Child';
$lang->program->allInvest        = 'Input';
$lang->program->teamCount        = 'Team';
$lang->program->longTime         = 'Long-Term';
$lang->program->moreProgram      = 'More program';
$lang->program->stakeholderType  = 'Stakeholder type';
$lang->program->parentBudget     = 'Parent program surplus budget：';
$lang->program->isStakeholderKey = 'Key stakeholder';

$lang->program->stakeholderTypeList['inside']  = 'Inside';
$lang->program->stakeholderTypeList['outside'] = 'Outside';

$lang->program->noProgram          = 'No program.';
$lang->program->showClosed         = 'Closed';
$lang->program->tips               = 'If a parent item set is selected, products under that parent item set can be associated. If no item set is selected, a product with the same name as the item is created by default and associated with that item.';
$lang->program->confirmBatchUnlink = "Do you want to batch unlink these stakeholders?";
$lang->program->beginLetterParent  = "Parent begin date: %s, begin date should be >                  = parent begin date.";
$lang->program->endGreaterParent   = "Parent end date: %s, end date should be <                      = parent end date.";
$lang->program->beginGreateChild   = "Child earliest begin: %s, parent begin date <                  = child earliest begin date.";
$lang->program->endLetterChild     = "Child latest end: %s, parent end date >                        = child latest end date.";
$lang->program->closeErrorMessage  = 'There are subprograms or projects that are not closed';
$lang->program->hasChildren        = 'The program has a child program or the project exists and can not be deleted.';
$lang->program->hasProduct         = 'The program has products exist and can not be deleted.';
$lang->program->confirmDelete      = "Do you want to delete it?";
$lang->program->readjustTime       = 'Change the program begin&end date.';
$lang->program->accessDenied       = 'You have no access to the program.';
$lang->program->beyondParentBudget = 'The remaining budget of the owned program has been exceeded.';

$lang->program->endList[31]  = 'One month';
$lang->program->endList[93]  = 'Trimester';
$lang->program->endList[186] = 'Half yearly';
$lang->program->endList[365] = 'One year';
$lang->program->endList[999] = 'Longtime';

$lang->program->aclList['private'] = "Private (accessible to project portfolio holders and stakeholders, stakeholders can follow up maintenance)";
$lang->program->aclList['open']    = "Default(user who can visit the program can access it)";

$lang->program->subAclList['private'] = "Private (accessible to this program set leader and stakeholders, stakeholders can follow up maintenance)";
$lang->program->subAclList['open']    = "Default (user who can visit the program can access it)";
$lang->program->subAclList['program'] = "Open within the program (accessible to all higher-level program directors and affiliates, as well as to this program director and affiliates)";

$lang->program->subAcls['private'] = 'Private';
$lang->program->subAcls['open']    = 'Default';
$lang->program->subAcls['program'] = 'Open within the program';

$lang->program->authList['extend'] = 'Inherit (program privileges and company privileges)';
$lang->program->authList['reset']  = 'Reset (program privileges only)';

$lang->program->statusList['wait']      = 'Waiting';
$lang->program->statusList['doing']     = 'Doing';
$lang->program->statusList['suspended'] = 'Suspended';
$lang->program->statusList['closed']    = 'Closed';

$lang->program->featureBar['all'] = 'All';

$lang->program->kanban = new stdclass();
$lang->program->kanban->common             = 'Program Kanban';
$lang->program->kanban->typeList['my']     = 'My Programs';
$lang->program->kanban->typeList['others'] = 'Others';

$lang->program->kanban->openProducts    = 'Open Products';
$lang->program->kanban->unexpiredPlans  = 'Unexpired Plans';
$lang->program->kanban->waitingProjects = 'Waiting Projects';
$lang->program->kanban->doingProjects   = 'Ongoing Projects';
$lang->program->kanban->doingExecutions = 'Ongoing Executions';
$lang->program->kanban->normalReleases  = 'Normal Releases';

$lang->program->kanban->laneColorList = array('#32C5FF', '#006AF1', '#9D28B2', '#FF8F26', '#FFC20E', '#00A78E', '#7FBB00', '#424BAC', '#C0E9FF', '#EC2761');
