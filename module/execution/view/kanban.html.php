<?php
/**
 * The kanban file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Mengyi Liu<liumengyi@easycorp.ltd>
 * @package     execution
 * @version     $Id: kanban.html.php 935 2022-01-11 16:49:24Z $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kanban.html.php';?>

<?php
$laneCount = 0;
if($groupBy == 'default')
{
    foreach($regions as $region) $laneCount += $region->laneCount;
}

js::set('regions', $regions);
js::set('browseType', $browseType);
js::set('kanbanData', $kanbanData);
js::set('orderBy', $orderBy);
js::set('groupBy', $groupBy);
js::set('execution', $execution);
js::set('productID', $productID);
js::set('kanbanLang', $lang->kanban);
js::set('kanbanlaneLang', $lang->kanbanlane);
js::set('storyLang', $lang->story);
js::set('executionLang', $lang->execution);
js::set('bugLang', $lang->bug);
js::set('taskLang', $lang->task);
js::set('deadlineLang', $lang->task->deadlineAB);
js::set('kanbancolumnLang', $lang->kanbancolumn);
js::set('kanbancardLang', $lang->kanbancard);
js::set('executionID', $execution->id);
js::set('laneCount', $laneCount);
js::set('userList', $userList);
js::set('noAssigned', $lang->kanbancard->noAssigned);
js::set('users', $users);
js::set('entertime', time());
js::set('displayCards', $execution->displayCards);
js::set('productNum', $productNum);
js::set('fluidBoard', $execution->fluidBoard);
js::set('colorListLang', $lang->kanbancard->colorList);
js::set('colorList', $this->config->kanban->cardColorList);
js::set('projectID', $projectID);
js::set('vision', $this->config->vision);
js::set('productCount', count($productNames));
js::set('executionID', $execution->id);

$canSortRegion       = commonModel::hasPriv('kanban', 'sortRegion') && count($regions) > 1;
$canEditRegion       = commonModel::hasPriv('kanban', 'editRegion');
$canDeleteRegion     = commonModel::hasPriv('kanban', 'deleteRegion');
$canCreateLane       = commonModel::hasPriv('kanban', 'createLane');
$canCreateTask       = common::hasPriv('task', 'create');
$canBatchCreateTask  = common::hasPriv('task', 'batchCreate');
$canCreateBug        = common::hasPriv('bug', 'create');
$canBatchCreateBug   = common::hasPriv('bug', 'batchCreate');
$canImportBug        = common::hasPriv('execution', 'importBug');
$canCreateStory      = ($productID and common::hasPriv('story', 'create'));
$canBatchCreateStory = ($productID and common::hasPriv('story', 'batchCreate'));
$canLinkStory        = ($productID and common::hasPriv('execution', 'linkStory'));
$canLinkStoryByPlan  = ($productID and common::hasPriv('execution', 'importplanstories'));
$hasStoryButton      = ($canCreateStory or $canBatchCreateStory or $canLinkStory or $canLinkStoryByPlan);
$hasTaskButton       = ($canCreateTask or $canBatchCreateTask or $canImportBug);
$hasBugButton        = ($canCreateBug or $canBatchCreateBug);

js::set('priv',
    array(
        'canCreateTask'         => $canCreateTask,
        'canBatchCreateTask'    => $canBatchCreateTask,
        'canImportBug'          => $canImportBug,
        'canCreateBug'          => $canCreateBug,
        'canBatchCreateBug'     => $canBatchCreateBug,
        'canCreateStory'        => $canCreateStory,
        'canBatchCreateStory'   => $canBatchCreateStory,
        'canLinkStory'          => $canLinkStory,
        'canLinkStoryByPlan'    => $canLinkStoryByPlan,
        'canAssignBug'          => common::hasPriv('bug', 'assignto'),
        'canConfirmBug'         => common::hasPriv('bug', 'confirmBug'),
        'canResolveBug'         => common::hasPriv('bug', 'resolve'),
        'canCopyBug'            => common::hasPriv('bug', 'create'),
        'canEditBug'            => common::hasPriv('bug', 'edit'),
        'canDeleteBug'          => common::hasPriv('bug', 'delete'),
        'canActivateBug'        => common::hasPriv('bug', 'activate'),
        'canAssignTask'         => common::hasPriv('task', 'assignto'),
        'canFinishTask'         => common::hasPriv('task', 'finish'),
        'canPauseTask'          => common::hasPriv('task', 'pause'),
        'canCancelTask'         => common::hasPriv('task', 'cancel'),
        'canCloseTask'          => common::hasPriv('task', 'close'),
        'canActivateTask'       => common::hasPriv('task', 'activate'),
        'canActivateStory'      => common::hasPriv('story', 'activate'),
        'canStartTask'          => common::hasPriv('task', 'start'),
        'canRestartTask'        => common::hasPriv('task', 'restart'),
        'canEditTask'           => common::hasPriv('task', 'edit'),
        'canDeleteTask'         => common::hasPriv('task', 'delete'),
        'canRecordEstimateTask' => common::hasPriv('task', 'recordEstimate'),
        'canToStoryBug'         => common::hasPriv('story', 'create'),
        'canAssignStory'        => common::hasPriv('story', 'assignto'),
        'canEditStory'          => common::hasPriv('story', 'edit'),
        'canDeleteStory'        => common::hasPriv('story', 'delete'),
        'canChangeStory'        => common::hasPriv('story', 'change'),
        'canUnlinkStory'        => common::hasPriv('execution', 'unlinkStory'),
    )
);
js::set('hasStoryButton', $hasStoryButton);
js::set('hasBugButton', $hasBugButton);
js::set('hasTaskButton', $hasTaskButton);
?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <div class="input-control space c-type">
      <?php echo html::select('type', $lang->kanban->type, $browseType, 'class="form-control chosen" data-max_drop_width="215"');?>
    </div>
    <?php if($browseType != 'all'):?>
    <div class="input-control space c-group">
      <?php echo html::select('group',  $lang->kanban->group->$browseType, $groupBy, 'class="form-control chosen" data-max_drop_width="215"');?>
    </div>
    <?php endif;?>
  </div>
  <div class='input-group pull-left not-fix-input-group' id='kanbanScaleControl'>
    <span class='input-group-btn'>
      <button class='btn btn-icon' type='button' data-type='-'><i class='icon icon-minuse-solid-circle text-muted'></i></button>
    </span>
    <span class='input-group-addon'>
      <span id='kanbanScaleSize'>1</span><?php echo $lang->execution->kanbanCardsUnit; ?>
    </span>
    <span class='input-group-btn'>
      <button class='btn btn-icon' type='button' data-type='+'><i class='icon icon-plus-solid-circle text-muted'></i></button>
    </span>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php
    echo html::a('javascript:fullScreen()', "<i class='icon-fullscreen muted'></i> " . $lang->kanban->fullScreen, '', "class='btn btn-link'");
    $actions = '';
    $printSettingBtn = (common::hasPriv('kanban', 'createRegion') or (common::hasPriv('kanban', 'setLaneHeight')) or (common::hasPriv('kanban', 'setColumnWidth')) or common::hasPriv('execution', 'edit') or common::hasPriv('execution', 'close') or common::hasPriv('execution', 'delete') or !empty($executionActions));

    if($printSettingBtn)
    {
        $actions .= html::a('javascript:;', "<i class='icon icon-cog-outline'></i>" . $lang->kanban->setting, '', "data-toggle='dropdown' class='btn btn-link'");
        $actions .= "<ul id='kanbanActionMenu' class='dropdown-menu pull-right'>";
        $width    = $this->app->getClientLang() == 'en' ? '70%' : '60%';
        if(common::hasPriv('kanban', 'createRegion')) $actions .= '<li>' . html::a(helper::createLink('kanban', 'createRegion', "kanbanID=$execution->id&from=execution", '', true), '<i class="icon icon-plus"></i>' . $lang->kanban->createRegion, '', "class='iframe btn btn-link text-left'") . '</li>';
        if(common::hasPriv('kanban', 'setLaneHeight')) $actions .= '<li>' . html::a(helper::createLink('kanban', 'setLaneHeight', "kanbanID=$execution->id&from=execution", '', true), '<i class="icon icon-size-height"></i>' . $lang->kanban->laneHeight, '', "class='iframe btn btn-link text-left' data-width=$width") . '</li>';
        if(common::hasPriv('kanban', 'setColumnWidth')) $actions .= '<li>' . html::a(helper::createLink('kanban', 'setColumnWidth', "kanbanID=$execution->id&from=execution", '', true), '<i class="icon icon-size-width"></i>' . $lang->kanban->columnWidth, '', "class='iframe btn btn-link text-left' data-width=30%") . '</li>';
        $kanbanActions = '';
        if(common::hasPriv('execution', 'edit')) $kanbanActions .= '<li>' . html::a(helper::createLink('execution', 'edit', "executionID=$execution->id", '', true), '<i class="icon icon-edit"></i>' . $lang->kanban->edit, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'start')) $kanbanActions .= '<li class="startButton hidden">' . html::a(helper::createLink('execution', 'start', "executionID=$execution->id&from=kanban", '', true), '<i class="icon icon-play"></i>' . $lang->execution->start, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'putoff')) $kanbanActions .= '<li class="putoffButton hidden">' . html::a(helper::createLink('execution', 'putoff', "executionID=$execution->id&from=kanban", '', true), '<i class="icon icon-calendar"></i>' . $lang->execution->putoff, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'suspend')) $kanbanActions .= '<li class="suspendButton hidden">' . html::a(helper::createLink('execution', 'suspend', "executionID=$execution->id&from=kanban", '', true), '<i class="icon icon-pause"></i>' . $lang->execution->suspend, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'close')) $kanbanActions .= '<li class="closeButton hidden">' . html::a(helper::createLink('execution', 'close', "executionID=$execution->id&from=kanban", '', true), '<i class="icon icon-off"></i>' . $lang->execution->close, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'activate')) $kanbanActions .= '<li class="activateButton hidden">' . html::a(helper::createLink('execution', 'activate', "executionID=$execution->id&from=kanban", '', true), '<i class="icon icon-magic"></i>' . $lang->execution->activate, '', "class='iframe btn btn-link text-left' data-width='75%'") . '</li>';
        if(common::hasPriv('execution', 'delete')) $kanbanActions .= '<li>' . html::a(helper::createLink('execution', 'delete', "executionID=$execution->id"), '<i class="icon icon-trash"></i>' . $lang->delete, 'hiddenwin', "class='btn btn-link text-left'") . '</li>';
        if($kanbanActions)
        {
            $actions .= ((common::hasPriv('kanban', 'createRegion') or common::hasPriv('kanban', 'setLaneHeight') or common::hasPriv('kanban', 'setColumnWidth')) and (common::hasPriv('execution', 'edit') or common::hasPriv('execution', 'delete') or !empty($executionActions))) ? "<div class='divider'></div>" . $kanbanActions : $kanbanActions;
        }
        $actions .= "</ul>";
    }

    echo $actions;
    ?>
    <?php if($canCreateTask or $canBatchCreateTask or $canImportBug or $canCreateBug or $canBatchCreateBug or $canCreateStory or $canBatchCreateStory or $canLinkStory or $canLinkStoryByPlan):?>
    <div class='dropdown' id='createDropdown'>
      <button class='btn btn-primary' type='button' data-toggle='dropdown'><i class='icon icon-plus'></i> <?php echo $this->lang->create;?> <span class='caret'></span></button>
      <ul class='dropdown-menu pull-right'>
        <?php if($canCreateStory) echo '<li>' . html::a(helper::createLink('story', 'create', "productID=$productID&branch=0&moduleID=0&story=0&execution=$execution->id", '', true), $lang->execution->createStory, '', "class='iframe'") . '</li>';?>
        <?php
        if($canBatchCreateStory)
        {
            if(count($productNames) > 1)
            {
                echo '<li>' . html::a('#batchCreateStory', $lang->execution->batchCreateStory, '', 'data-toggle="modal"') . '</li>';
            }
            else
            {
                echo '<li>' . html::a(helper::createLink('story', 'batchCreate', "productID=$productID&branch=$branchID&moduleID=0&story=0&execution=$execution->id", '', true), $lang->execution->batchCreateStory, '', "class='iframe' data-width='90%'") . '</li>';
            }
        }
        ?>
        <?php if($canLinkStory) echo '<li>' . html::a(helper::createLink('execution', 'linkStory', "execution=$execution->id", '', true), $lang->execution->linkStory, '', "class='iframe' data-width='90%'") . '</li>';?>
        <?php if($canLinkStoryByPlan) echo '<li>' . html::a('#linkStoryByPlan', $lang->execution->linkStoryByPlan, '', 'data-toggle="modal"') . '</li>';?>
        <?php if($hasStoryButton and $hasBugButton) echo '<li class="divider"></li>';?>
        <?php if($canCreateBug) echo '<li>' . html::a(helper::createLink('bug', 'create', "productID=$productID&branch=0&extra=executionID=$execution->id", '', true), $lang->bug->create, '', "class='iframe'") . '</li>';?>
        <?php if($canBatchCreateBug)
        {

            $batchCreateBugLink = '<li>' . html::a(helper::createLink('bug', 'batchCreate', "productID=$productID&branch=$branchID&executionID=$execution->id", '', true), $lang->bug->batchCreate, '', "class='iframe'") . '</li>';
            if($productNum > 1) $batchCreateBugLink = '<li>' . html::a('#batchCreateBug', $lang->bug->batchCreate, '', "data-toggle='modal'") . '</li>';
            echo $batchCreateBugLink;
        }?>
        <?php if(($hasStoryButton or $hasBugButton) and $hasTaskButton) echo '<li class="divider"></li>';?>
        <?php if($canCreateTask) echo '<li>' . html::a(helper::createLink('task', 'create', "execution=$execution->id", '', true), $lang->task->create, '', "class='iframe'") . '</li>';?>
        <?php if($canImportBug) echo '<li>' . html::a(helper::createLink('execution', 'importBug', "executionID=$execution->id", '', true), $lang->execution->importBug, '', "class='iframe' data-width=80%") . '</li>';?>
        <?php if($canBatchCreateTask) echo '<li>' . html::a(helper::createLink('task', 'batchCreate', "execution=$execution->id", '', true), $lang->execution->batchCreateTask, '', "class='iframe'") . '</li>';?>
      </ul>
    </div>
    <?php endif;?>
  </div>
</div>
<?php if($groupBy == 'default'):?>
<div class='panel' id='kanbanContainer'>
  <div class='panel-body'>
    <div id="kanban" data-id='<?php echo $execution->id;?>'>
      <?php foreach($regions as $region):?>
      <div class="region<?php if($canSortRegion) echo ' sort';?>" data-id="<?php echo $region->id;?>">
        <div class="region-header dropdown">
          <strong><?php echo $region->name;?></strong>
          <a class="text-muted"><i class="icon icon-chevron-double-up" data-id="<?php echo $region->id;?>"></i></a>
          <div class='region-actions'>
            <?php if($canEditRegion || $canCreateLane || $canDeleteRegion):?>
            <button class="btn btn-link action" type="button" data-toggle="dropdown"><i class="icon icon-ellipsis-v"></i></button>
            <ul class="dropdown-menu pull-right">
              <?php if($canEditRegion) echo '<li>' . html::a(helper::createLink('kanban', 'editRegion', "regionID={$region->id}", '', 1), '<i class="icon icon-edit"></i>' . $this->lang->kanban->editRegion, '', 'class="iframe" data-toggle="modal" data-width="600px"') . '</li>';?>
              <?php if($canCreateLane) echo '<li>' . html::a(helper::createLink('kanban', 'createLane', "executionID={$execution->id}&regionID={$region->id}&from=execution", '', 1), '<i class="icon icon-plus"></i>' . $this->lang->kanban->createLane, '', "class='iframe'") . '</li>';?>
              <?php if($canDeleteRegion and count($regions) > 1) echo '<li>' . html::a(helper::createLink('kanban', 'deleteRegion', "regionID={$region->id}"), '<i class="icon icon-trash"></i>' . $this->lang->kanban->deleteRegion, "hiddenwin") . '</li>';?>
            </ul>
            <?php endif;?>
          </div>
        </div>
        <div id='kanban<?php echo $region->id;?>' data-id='<?php echo $region->id;?>' class='kanban'></div>
      </div>
      <?php endforeach;?>
    </div>
  </div>
</div>
<?php else:?>
<div class='panel' id='kanbanContainer'>
  <div class='panel-body region'>
    <div id='kanban' class='kanban'></div>
  </div>
</div>
<?php endif;?>
<div class="modal fade" id="linkStoryByPlan">
  <div class="modal-dialog mw-500px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-close"></i></button>
        <h4 class="modal-title"><?php echo $lang->execution->linkStoryByPlan;?></h4><?php echo str_replace($lang->executionCommon, $kanban, $lang->execution->linkStoryByPlanTips);?>
      </div>
      <div class="modal-body">
        <div class='input-group'>
          <?php echo html::select('plan', $allPlans, '', "class='form-control chosen' id='plan'");?>
          <span class='input-group-btn'><?php echo html::commonButton($lang->execution->linkStory, "id='toStoryButton'", 'btn btn-primary');?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="batchCreateStory">
  <div class="modal-dialog mw-500px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-close"></i></button>
        <h4 class="modal-title"><?php echo $lang->execution->batchCreateStoryTips;?></h4>
      </div>
      <div class="modal-body">
        <div class='input-group'>
          <?php echo html::select('products', $productNames, '', "class='form-control chosen' id='products'");?>
          <span class='input-group-btn'><?php echo html::a(helper::createLink('story', 'batchCreate', "productID=$productID&branch=$branchID&moduleID=0&story=0&execution=$execution->id", '', true), $lang->execution->batchCreateStory, '', "class='btn btn-primary iframe' data-width='90%' id='batchCreateStoryButton' data-dismiss='modal'");?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="batchCreateBug">
  <div class="modal-dialog mw-500px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-close"></i></button>
        <h4 class="modal-title"><?php echo $lang->bug->product;?></h4>
      </div>
      <div class="modal-body">
        <div class='input-group'>
          <?php echo html::select('productName', $productNames, '', "class='form-control chosen' id='product'");?>
          <span class='input-group-btn'><?php echo html::a(helper::createLink('bug', 'batchCreate', 'productID=' . key($productNames) . '&branch=' . $branchID . '&executionID=' . $executionID,     '', true), $lang->bug->batchCreate, '', "id='batchCreateBugButton' class='btn btn-primary iframe' data-dismiss='modal' data-width='90%'");?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
