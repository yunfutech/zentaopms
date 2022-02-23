<?php
/* Actions. */
$lang->kanban->create              = 'Create Kanban';
$lang->kanban->createSpace         = 'Create Space';
$lang->kanban->editSpace           = 'Edit Space';
$lang->kanban->closeSpace          = 'Close Space';
$lang->kanban->deleteSpace         = 'Delete Space';
$lang->kanban->sortSpace           = 'Sort Space';
$lang->kanban->edit                = 'Edit Kanban';
$lang->kanban->view                = 'View Kanban';
$lang->kanban->close               = 'Close Kanban';
$lang->kanban->delete              = 'Delete Kanban';
$lang->kanban->createRegion        = 'Create Region';
$lang->kanban->editRegion          = 'Edit Region';
$lang->kanban->sortRegion          = 'Sort Region';
$lang->kanban->sortGroup           = 'Sort Group';
$lang->kanban->deleteRegion        = 'Delete Region';
$lang->kanban->createLane          = 'Create Lane';
$lang->kanban->editLane            = 'Edit Lane';
$lang->kanban->sortLane            = 'Sort Lane';
$lang->kanban->laneHeight          = 'Lane Height';
$lang->kanban->setLaneHeight       = 'Set Lane Height';
$lang->kanban->columnWidth         = 'Column Width';
$lang->kanban->setColumnWidth      = 'Set Column Width';
$lang->kanban->deleteLane          = 'Delete Lane';
$lang->kanban->createColumn        = 'Create Column';
$lang->kanban->editColumn          = 'Edit Column';
$lang->kanban->sortColumn          = 'Sort Column';
$lang->kanban->deleteColumn        = 'Delete Column';
$lang->kanban->createCard          = 'Create Card';
$lang->kanban->editCard            = 'Edit Card';
$lang->kanban->finished            = 'Finished';
$lang->kanban->finishCard          = 'Finish Card';
$lang->kanban->activateCard        = 'Activate Card';
$lang->kanban->viewCard            = 'View Card';
$lang->kanban->archiveCard         = 'Archive Card';
$lang->kanban->sortCard            = 'Sort Card';
$lang->kanban->copyCard            = 'Copy Card';
$lang->kanban->moveCard            = 'Move Card';
$lang->kanban->cardColor           = 'Card Color';
$lang->kanban->setCardColor        = 'Set Card Color';
$lang->kanban->deleteCard          = 'Delete Card';
$lang->kanban->assigntoCard        = 'Assign';
$lang->kanban->setting             = 'Setting';
$lang->kanban->enableArchived      = 'Enable Archived';
$lang->kanban->archive             = 'Archive';
$lang->kanban->performable         = 'Set done function';
$lang->kanban->doneFunction        = 'Done function';
$lang->kanban->splitColumn         = 'Split Column';
$lang->kanban->createColumnOnLeft  = 'Create Column On Left';
$lang->kanban->createColumnOnRight = 'Create Column On Right';
$lang->kanban->copyColumn          = 'Copy Column';
$lang->kanban->archiveColumn       = 'Archive Column';
$lang->kanban->spaceCommon         = 'Kanban Space';
$lang->kanban->styleCommon         = 'Style';
$lang->kanban->copy                = 'Copy';
$lang->kanban->custom              = 'Custom';
$lang->kanban->archived            = 'Archived';
$lang->kanban->viewArchivedCard    = 'View archived card';
$lang->kanban->viewArchivedColumn  = 'View archived column';
$lang->kanban->archivedColumn      = 'Archived Column';
$lang->kanban->archivedCard        = 'Archived Card';
$lang->kanban->restoreColumn       = 'Restore Column';
$lang->kanban->restoreCard         = 'Restore Card';
$lang->kanban->restore             = 'Restore';
$lang->kanban->child               = 'Child';
$lang->kanban->batchCreateCard     = 'Batchcreate Card';
$lang->kanban->import              = 'Import';
$lang->kanban->importAB            = 'Import ';
$lang->kanban->showClosed          = 'Closed';
$lang->kanban->importCard          = 'Card';
$lang->kanban->importPlan          = 'Plan';
$lang->kanban->importRelease       = 'Release';
$lang->kanban->importExecution     = $lang->execution->common;
$lang->kanban->importBuild         = 'Build';
$lang->kanban->allKanban           = 'All Kanban';
$lang->kanban->allProjects         = 'All ' . ($this->config->systemMode == 'classic' ? $lang->executionCommon : 'Projects');
$lang->kanban->allProducts         = 'All Products';

/* Fields. */
$lang->kanban->space          = 'Space';
$lang->kanban->name           = 'Name';
$lang->kanban->archived       = 'Archive';
$lang->kanban->owner          = 'Owner';
$lang->kanban->team           = 'Team';
$lang->kanban->desc           = 'Description';
$lang->kanban->acl            = 'ACL';
$lang->kanban->whitelist      = 'White List';
$lang->kanban->status         = 'Status';
$lang->kanban->createdBy      = 'Created By';
$lang->kanban->createdDate    = 'Created Date';
$lang->kanban->lastEditedBy   = 'Edited By';
$lang->kanban->lastEditedDate = 'Edited Date';
$lang->kanban->closed         = 'Closed';
$lang->kanban->closedBy       = 'Closed By';
$lang->kanban->closedDate     = 'Closed Date';
$lang->kanban->empty          = 'No Kanban';
$lang->kanban->teamSumCount   = '%s people in total';
$lang->kanban->cardCount      = 'Card Count';

$lang->kanban->createColumnOnLeft  = 'Create Column On Left';
$lang->kanban->createColumnOnRight = 'Create Column On Right';

$lang->kanban->accessDenied  = "You have no access to the kanban.";
$lang->kanban->confirmDelete = 'Do you want to delete this?';
$lang->kanban->cardCountTip  = 'Please enter the number of cards';

$lang->kanban->selectedKanban  = 'Please select Kanban';
$lang->kanban->selectedProduct = 'Please select Product';
$lang->kanban->selectedProject = 'Please select ' . ($this->config->systemMode == 'classic' ? $lang->executionCommon : 'Project');
$lang->kanban->selectedLane    = 'Target Lane';

$lang->kanban->aclGroup['open']    = 'Open';
$lang->kanban->aclGroup['private'] = 'Private';
$lang->kanban->aclGroup['extend']  = 'Extend';

$lang->kanban->aclList['extend']  = 'Extend (Accessible with space view permissions)';
$lang->kanban->aclList['private'] = 'Private (For the kanban team, whitelist members and space owner only)';

$lang->kanban->archiveList['0'] = 'Disable';
$lang->kanban->archiveList['1'] = 'Enable';

$lang->kanban->enableFinished['0'] = 'Disable';
$lang->kanban->enableFinished['1'] = 'Enable';

$lang->kanban->type = array();
$lang->kanban->type['all']   = "All KanBan";
$lang->kanban->type['story'] = "Story KanBan";
$lang->kanban->type['task']  = "Task KanBan";
$lang->kanban->type['bug']   = "Bug KanBan";

$lang->kanban->group = new stdClass();

$lang->kanban->group->all = array();
$lang->kanban->group->story = array();
$lang->kanban->group->story['default']    = "Default";
$lang->kanban->group->story['pri']        = "Story Priority";
$lang->kanban->group->story['category']   = "Story Category";
$lang->kanban->group->story['module']     = "Story Module";
$lang->kanban->group->story['source']     = "Story Source";
$lang->kanban->group->story['assignedTo'] = "Assigned To";

$lang->kanban->group->task = array();
$lang->kanban->group->task['default']    = "Default";
$lang->kanban->group->task['pri']        = "Task Priority";
$lang->kanban->group->task['type']       = "Task Type";
$lang->kanban->group->task['module']     = "Task Module";
$lang->kanban->group->task['assignedTo'] = "Assigned To";
$lang->kanban->group->task['story']      = "Story";

$lang->kanban->group->bug = array();
$lang->kanban->group->bug['default']    = "Default";
$lang->kanban->group->bug['pri']        = "Bug Priority";
$lang->kanban->group->bug['severity']   = "Bug Severity";
$lang->kanban->group->bug['module']     = "Bug Module";
$lang->kanban->group->bug['type']       = "Bug Type";
$lang->kanban->group->bug['assignedTo'] = "Assigned To";

$lang->kanban->WIP                = 'WIP';
$lang->kanban->setWIP             = 'WIP Settings';
$lang->kanban->WIPStatus          = 'WIP Status';
$lang->kanban->WIPStage           = 'WIP Stage';
$lang->kanban->WIPType            = 'WIP Type';
$lang->kanban->WIPCount           = 'WIP Count';
$lang->kanban->noLimit            = 'No Limit ∞';
$lang->kanban->setLane            = 'Lane Settings';
$lang->kanban->laneName           = 'Lane Name';
$lang->kanban->laneColor          = 'Lane Color';
$lang->kanban->setColumn          = 'Column Settings';
$lang->kanban->columnName         = 'Column Name';
$lang->kanban->columnColor        = 'Column Color';
$lang->kanban->moveUp             = 'Swimlane Up';
$lang->kanban->moveDown           = 'Swimlane Down';
$lang->kanban->laneMove           = 'Swimlane Move';
$lang->kanban->laneGroup          = 'Lane Group';
$lang->kanban->cardsSort          = 'Cards Sortting';
$lang->kanban->more               = 'More';
$lang->kanban->moreAction         = 'More Action';
$lang->kanban->noGroup            = 'None';
$lang->kanban->limitExceeded      = 'Limit Exceeded';
$lang->kanban->fullScreen         = 'Full Screen';
$lang->kanban->setting            = 'Setting';
$lang->kanban->my                 = 'My';
$lang->kanban->other              = 'Other';

$lang->kanban->error = new stdclass();
$lang->kanban->error->mustBeInt       = 'The WIPs must be positive integer.';
$lang->kanban->error->parentLimitNote = 'The WIPs in the parent column cannot be < the sum of the WIPs in the child column.';
$lang->kanban->error->childLimitNote  = 'The sum of products in the child column cannot be > the number of products in the parent column.';
$lang->kanban->error->importObjNotEmpty = 'Please select at least one import object.';

$lang->kanban->importList = array();
$lang->kanban->importList['off'] = 'Import is not enabled';
$lang->kanban->importList['on']  = 'Enable the import function, you can only import content that you have permission to view.';

$lang->kanban->importObjectList = array();
$lang->kanban->importObjectList['plans']      = 'Product Plan';
$lang->kanban->importObjectList['releases']   = 'Release';
$lang->kanban->importObjectList['builds']     = 'Build';
$lang->kanban->importObjectList['executions'] = 'Execution';
$lang->kanban->importObjectList['cards']      = 'Other Kanban Cards';

$lang->kanban->defaultColumn = array();
$lang->kanban->defaultColumn['wait']   = 'wait';
$lang->kanban->defaultColumn['doing']  = 'doing';
$lang->kanban->defaultColumn['done']   = 'done';
$lang->kanban->defaultColumn['closed'] = 'close';

$lang->kanban->laneTypeList = array();
$lang->kanban->laneTypeList['story'] = $lang->SRCommon;
$lang->kanban->laneTypeList['bug']   = 'Bug';
$lang->kanban->laneTypeList['task']  = 'Task';

$lang->kanban->storyColumn = array();
$lang->kanban->storyColumn['backlog']    = 'Backlog';
$lang->kanban->storyColumn['ready']      = 'Ready';
$lang->kanban->storyColumn['develop']    = 'Development';
$lang->kanban->storyColumn['developing'] = 'Doing';
$lang->kanban->storyColumn['developed']  = 'Done';
$lang->kanban->storyColumn['test']       = 'Testing';
$lang->kanban->storyColumn['testing']    = 'Doing';
$lang->kanban->storyColumn['tested']     = 'Done';
$lang->kanban->storyColumn['verified']   = 'Verified';
$lang->kanban->storyColumn['released']   = 'Released';
$lang->kanban->storyColumn['closed']     = 'Closed';

$lang->kanban->bugColumn = array();
$lang->kanban->bugColumn['unconfirmed'] = 'Unconfirmed';
$lang->kanban->bugColumn['confirmed']   = 'Confirmed';
$lang->kanban->bugColumn['resolving']   = 'Resolving';
$lang->kanban->bugColumn['fixing']      = 'Doing';
$lang->kanban->bugColumn['fixed']       = 'Done';
$lang->kanban->bugColumn['test']        = 'Test';
$lang->kanban->bugColumn['testing']     = 'Doing';
$lang->kanban->bugColumn['tested']      = 'Done';
$lang->kanban->bugColumn['closed']      = 'Closed';

$lang->kanban->taskColumn = array();
$lang->kanban->taskColumn['wait']       = 'Wait';
$lang->kanban->taskColumn['develop']    = 'Develop';
$lang->kanban->taskColumn['developing'] = 'Developing';
$lang->kanban->taskColumn['developed']  = 'Developed';
$lang->kanban->taskColumn['pause']      = 'Pause';
$lang->kanban->taskColumn['canceled']   = 'Canceled';
$lang->kanban->taskColumn['closed']     = 'Closed';

$lang->kanbanspace = new stdclass();
$lang->kanbanspace->common         = 'Kanban Space';
$lang->kanbanspace->name           = 'Name';
$lang->kanbanspace->owner          = 'Owner';
$lang->kanbanspace->team           = 'Team';
$lang->kanbanspace->desc           = 'Description';
$lang->kanbanspace->acl            = 'ACL';
$lang->kanbanspace->whitelist      = 'White List';
$lang->kanbanspace->status         = 'Status';
$lang->kanbanspace->createdBy      = 'Created By';
$lang->kanbanspace->createdDate    = 'Created Date';
$lang->kanbanspace->lastEditedBy   = 'Edited By';
$lang->kanbanspace->lastEditedDate = 'Edited Date';
$lang->kanbanspace->closedBy       = 'Closed By';
$lang->kanbanspace->closedDate     = 'Closed Date';
$lang->kanbanspace->type           = 'Type';

$lang->kanbanspace->empty = 'No Space';

$lang->kanbanspace->aclList['open']    = 'Open (Accessible with kanban view permissions)';
$lang->kanbanspace->aclList['private'] = 'Private (For the space owner, team and whitelist members only)';

$lang->kanbanspace->featureBar['private']     = 'Private Space';
$lang->kanbanspace->featureBar['cooperation'] = 'Cooperation Space';
$lang->kanbanspace->featureBar['public']      = 'Public Space';
$lang->kanbanspace->featureBar['involved']    = 'Involved';

$lang->kanbancolumn = new stdclass();
$lang->kanbancolumn->name       = $lang->kanban->columnName;
$lang->kanbancolumn->limit      = $lang->kanban->WIPCount;
$lang->kanbancolumn->color      = 'Column Color';
$lang->kanbancolumn->childName  = 'Name';
$lang->kanbancolumn->childColor = 'Color';
$lang->kanbancolumn->empty      = 'No Column';

$lang->kanbancolumn->confirmArchive     = 'Are you sure to archive this column? After archiving the column, the column and all cards in the column will be hidden. You can view the archived columns in the Region - Archived.';
$lang->kanbancolumn->confirmDelete      = 'Are you sure to delete this column? After deleting the column, all cards in this column will also be deleted.';
$lang->kanbancolumn->confirmDeleteChild = 'Are you sure to delete this column? After deleting a column, all cards in the column will be moved to the parent column.';
$lang->kanbancolumn->confirmRestore     = 'Are you sure you want to restore this Kanban column? After restoring the Kanban column, the Kanban column and all tasks in the Kanban column will be restored to the previous position at the same time.';

$lang->kanbanlane = new stdclass();
$lang->kanbanlane->name      = $lang->kanban->laneName;
$lang->kanbanlane->common    = 'Lane';
$lang->kanbanlane->default   = 'Default Lane';
$lang->kanbanlane->column    = 'Lane Kanban Column';
$lang->kanbanlane->otherlane = 'Select Existed Lane';
$lang->kanbanlane->color     = 'Lane Color';
$lang->kanbanlane->WIPType   = 'Lane WIP Type';

$lang->kanbanlane->confirmDelete    = 'Are you sure to delete this lane? After deleting the lane, all data (columns and cards) in the lane will also be deleted.';
$lang->kanbanlane->confirmDeleteTip = 'Are you sure to delete this lane? After deleting the lane, all %s in the lane will be hidden.';

$lang->kanbanlane->modeList['sameAsOther'] = 'Use the same Kanban column';
$lang->kanbanlane->modeList['independent'] = 'Independent Kanban column';

$lang->kanbanlane->heightTypeList['auto']   = 'Adaptive (Adaptive according to the card height)';
$lang->kanbanlane->heightTypeList['custom'] = 'Custom (Customize lane height based on number of cards)';

$lang->kanbancolumn->fluidBoardList['0'] = 'Fixed';
$lang->kanbancolumn->fluidBoardList['1'] = 'Auto Width';

$lang->kanbanlane->error = new stdclass();
$lang->kanbanlane->error->mustBeInt = 'The number of cards must be a positive integer greater than 2.';

$lang->kanbanregion = new stdclass();
$lang->kanbanregion->name    = 'Region Name';
$lang->kanbanregion->default = 'Default Region';
$lang->kanbanregion->style   = 'Region Style';

$lang->kanbanregion->confirmDelete = 'Are you sure to delete this region? After deleting this region, all data in this region will be deleted.';

$lang->kanbancard = new stdclass();
$lang->kanbancard->create  = 'Create Card';
$lang->kanbancard->edit    = 'Edit Card';
$lang->kanbancard->view    = 'View Card';
$lang->kanbancard->archive = 'Archive';
$lang->kanbancard->assign  = 'Assign';
$lang->kanbancard->copy    = 'Copy';
$lang->kanbancard->delete  = 'Delete';

$lang->kanbancard->name            = 'Card Name';
$lang->kanbancard->legendBasicInfo = 'Basic Info';
$lang->kanbancard->legendLifeTime  = 'Card Life';
$lang->kanbancard->space           = 'Space';
$lang->kanbancard->kanban          = 'Kanban';
$lang->kanbancard->lane            = 'Lane';
$lang->kanbancard->column          = 'Column';
$lang->kanbancard->assignedTo      = 'Assignee';
$lang->kanbancard->beginAndEnd     = 'Begin & End';
$lang->kanbancard->begin           = 'Begin';
$lang->kanbancard->end             = 'End';
$lang->kanbancard->pri             = 'Priority';
$lang->kanbancard->desc            = 'Description';
$lang->kanbancard->estimate        = 'Estimate';
$lang->kanbancard->createdBy       = 'Created By';
$lang->kanbancard->createdDate     = 'Created Date';
$lang->kanbancard->lastEditedBy    = 'Last Edited By';
$lang->kanbancard->lastEditedDate  = 'Last Edited Date';
$lang->kanbancard->archivedBy      = 'Archived By';
$lang->kanbancard->archivedDate    = 'Archived Date';
$lang->kanbancard->lblHour         = 'h';
$lang->kanbancard->noAssigned      = 'No Assigned';
$lang->kanbancard->deadlineAB      = 'DL';
$lang->kanbancard->beginAB         = 'Begin';
$lang->kanbancard->to              = '~';
$lang->kanbancard->archived        = 'Archived';
$lang->kanbancard->empty           = 'No Card';
$lang->kanbancard->ditto           = 'Ditto';

$lang->kanbancard->confirmArchive    = 'Are you sure to archive this card? After archiving the card, it will be hidden from the column and you can view it in the Region - Archived.';
$lang->kanbancard->confirmDelete     = 'Are you sure to delete this card? After deleting the card, it will be deleted from the Kanban. You can only view it in the system recycle bin.';
$lang->kanbancard->confirmRestore    = "Are you sure you want to restore this card? After the card is restored, the card will be restored to the '%s' Kanban column.";
$lang->kanbancard->confirmRestoreTip = "The card's column has been archived or deleted, please restore '%s' column first.";

$lang->kanbancard->priList[1] = 1;
$lang->kanbancard->priList[2] = 2;
$lang->kanbancard->priList[3] = 3;
$lang->kanbancard->priList[4] = 4;

$lang->kanbancard->colorList['#fff']    = 'Default';
$lang->kanbancard->colorList['#b10b0b'] = 'Blocking';
$lang->kanbancard->colorList['#cfa227'] = 'Warning';
$lang->kanbancard->colorList['#2a5f29'] = 'Urgent';

$lang->kanbancard->error = new stdClass();
$lang->kanbancard->error->recordMinus = 'Estimate cannot be negative!';
$lang->kanbancard->error->endSmall    = '"End Date" cannot be less than "Begin Date"';
