<?php
/**
 * The personal view file of attend module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     attend
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<div id='featurebar'>
  <ul class='nav'>
    <?php foreach($lang->my->featureBar['review'] as $reviewType => $reviewName):?>
    <?php $class = $type == $reviewType? "class='active'" : ''?>
    <?php echo "<li $class>" . html::a(inlink('review', "type=$reviewType&orderBy=$orderBy"), $reviewName, '',"data-app={$app->tab}") . '</li>';?>
    <?php endforeach;?>
  </ul>
</div>
<?php js::set('type', $type)?>
<?php js::set('confirmReview', $lang->attend->confirmReview);?>
<?php $appendClass = $type == 'all' ? 'table-noFixedHeader' : 'table-fixedHeader'?>
<?php if(($type == 'all' && !empty($attends)) || $type == 'attend'):?>
<?php if($type == 'attend' and empty($attends)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div> <?php else:?>
<div class='panel'>
  <table class='table table-hover table-striped table-sorter table-data table-fixed text-center <?php echo $appendClass?>'>
    <thead>
      <tr class='text-center'>
        <?php if($type == 'all'): ?>
          <th class='w-80px'><?php echo $lang->my->reviewModule;?></th>
        <?php else:?>
          <th class='w-80px'><?php echo $lang->attend->id;?></th>
        <?php endif;?>
        <th class='w-100px'><?php echo $lang->attend->account;?></th>
        <th class='w-100px'><?php echo $lang->attend->date;?></th>
        <th class='w-80px'><?php echo $lang->attend->manualIn;?></th>
        <th class='w-80px'><?php echo $lang->attend->manualOut;?></th>
        <th class='w-100px'><?php echo $lang->attend->reason;?></th>
        <th><?php echo $lang->attend->desc;?></th>
        <th class='w-100px'><?php echo $lang->attend->status;?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($attends as $attend):?>
    <tr>
      <?php if($type == 'all'): ?>
        <td><?php echo $lang->attend->common;?></td>
      <?php else:?>
        <td><?php echo $attend->id;?></td>
      <?php endif;?>
      <td><?php echo zget($users, $attend->account);?></td>
      <td><?php echo $attend->date?></td>
      <td><?php echo substr($attend->manualIn, 0, 5)?></td>
      <td><?php echo substr($attend->manualOut, 0, 5)?></td>
      <td><?php echo zget($lang->attend->reasonList, $attend->reason)?></td>
      <td><?php echo $attend->desc?></td>
      <td><?php echo zget($lang->attend->statusList, $attend->status)?></td>
      <td>
        <?php extCommonModel::printLink('attend', 'review', "attendID={$attend->id}&status=pass",   $lang->attend->reviewStatusList['pass'],   "data-status='pass' data-toggle='ajax'")?>
        <?php extCommonModel::printLink('attend', 'review', "attendID={$attend->id}&status=reject", $lang->attend->reviewStatusList['reject'], "data-toggle='modal'")?>
      </td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<?php endif;?>
<?php endif;?>

<?php if($type == 'leave'):?>
<?php if(empty($leaveList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div>
<?php else:?>
<div class='panel'>
  <table class='table table-data table-hover text-center table-fixed tablesorter <?php echo $appendClass?>' id='leaveTable'>
    <thead>
      <tr class='text-center'>
        <?php $vars = "type={$type}&orderBy=%s";?>
        <th class='w-80px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->leave->id);?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->leave->createdBy);?></th>
        <th class='w-90px'><?php echo $lang->user->dept;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->leave->type);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->leave->begin);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->leave->end);?></th>
        <th class='w-140px'><?php commonModel::printOrderLink('backDate', $orderBy, $vars, $lang->leave->backDate);?></th>
        <th><?php echo $lang->leave->desc;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->leave->status);?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($leaveList as $leave):?>
    <tr>
      <td><?php echo $leave->id;?></td>
      <td><?php echo zget($users, $leave->createdBy);?></td>
      <td><?php echo zget($deptList, $leave->dept);?></td>
      <td><?php echo zget($this->lang->leave->typeList, $leave->type);?></td>
      <td><?php echo $leave->begin . ' ' . substr($leave->start, 0, 5);?></td>
      <td><?php echo $leave->end . ' ' . substr($leave->finish, 0, 5);?></td>
      <td><?php echo formatTime($leave->backDate);?></td>
      <td title='<?php echo $leave->desc?>'><?php echo $leave->desc;?></td>
      <td class='leave-<?php echo $leave->status?>'><?php echo zget($this->lang->leave->statusList, $leave->status);?></td>
      <td>
        <?php $mode = $leave->status == 'pass' ? 'back' : '';?>
        <?php extCommonModel::printLink('leave', 'review', "id={$leave->id}&status=pass&mode=$mode",   $lang->leave->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('leave', 'review', "id={$leave->id}&status=reject&mode=$mode", $lang->leave->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<?php endif;?>
<?php endif;?>

<?php if($type == 'makeup'):?>
<?php if(empty($makeupList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div>
<?php else:?>
<div class='panel'>
  <table class='table table-data table-hover text-center table-fixed tablesorter <?php echo $appendClass?>' id='makeupTable'>
    <thead>
      <tr class='text-center'>
        <?php $vars = "type={$type}&orderBy=%s";?>
        <th class='w-80px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->makeup->id);?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->makeup->createdBy);?></th>
        <th class='w-90px'><?php echo $lang->user->dept;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->makeup->type);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->makeup->begin);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->makeup->end);?></th>
        <th><?php echo $lang->makeup->desc;?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($makeupList as $makeup):?>
    <tr>
      <td><?php echo $makeup->id;?></td>
      <td><?php echo zget($users, $makeup->createdBy);?></td>
      <td><?php echo zget($deptList, $makeup->dept);?></td>
      <td><?php echo zget($this->lang->makeup->typeList, $makeup->type);?></td>
      <td><?php echo $makeup->begin . ' ' . substr($makeup->start, 0, 5);?></td>
      <td><?php echo $makeup->end . ' ' . substr($makeup->finish, 0, 5);?></td>
      <td title='<?php echo $makeup->desc?>'><?php echo $makeup->desc;?></td>
      <td>
        <?php extCommonModel::printLink('makeup', 'review', "id={$makeup->id}&status=pass",   $lang->makeup->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('makeup', 'review', "id={$makeup->id}&status=reject", $lang->makeup->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<?php endif;?>
<?php endif;?>

<?php if($type == 'overtime'):?>
<?php if(empty($overtimeList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div>
<?php else:?>
<div class='panel'>
  <table class='table table-data table-hover text-center table-fixed tablesorter <?php echo $appendClass?>' id='overtimeTable'>
    <thead>
      <tr class='text-center'>
        <?php $vars = "type={$type}&orderBy=%s";?>
        <th class='w-80px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->overtime->id);?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->overtime->createdBy);?></th>
        <th class='w-90px'><?php echo $lang->user->dept;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->overtime->type);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->overtime->begin);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->overtime->end);?></th>
        <th><?php echo $lang->overtime->desc;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->overtime->status);?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($overtimeList as $overtime):?>
    <tr>
      <td><?php echo $overtime->id;?></td>
      <td><?php echo zget($users, $overtime->createdBy);?></td>
      <td><?php echo zget($deptList, $overtime->dept);?></td>
      <td><?php echo zget($this->lang->overtime->typeList, $overtime->type);?></td>
      <td><?php echo $overtime->begin . ' ' . substr($overtime->start, 0, 5);?></td>
      <td><?php echo $overtime->end . ' ' . substr($overtime->finish, 0, 5);?></td>
      <td title='<?php echo $overtime->desc?>'><?php echo $overtime->desc;?></td>
      <td class='overtime-<?php echo $overtime->status?>'><?php echo zget($this->lang->overtime->statusList, $overtime->status);?></td>
      <td>
        <?php extCommonModel::printLink('overtime', 'review', "id={$overtime->id}&status=pass",   $lang->overtime->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('overtime', 'review', "id={$overtime->id}&status=reject", $lang->overtime->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<?php endif;?>
<?php endif;?>

<?php if($type == 'lieu'):?>
<?php if(empty($lieuList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div>
<?php else:?>
<div class='panel'>
  <table class='table table-data table-hover text-center table-fixed tablesorter <?php echo $appendClass?>' id='lieuTable'>
    <thead>
      <tr class='text-center'>
        <?php $vars = "type={$type}&orderBy=%s";?>
        <th class='w-80px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->lieu->id);?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->lieu->createdBy);?></th>
        <th class='w-90px'><?php echo $lang->user->dept;?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->lieu->begin);?></th>
        <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->lieu->end);?></th>
        <th><?php echo $lang->lieu->desc;?></th>
        <th class='w-80px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->lieu->status);?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($lieuList as $lieu):?>
    <tr>
      <td><?php echo $lieu->id;?></td>
      <td><?php echo zget($users, $lieu->createdBy);?></td>
      <td><?php echo zget($deptList, $lieu->dept);?></td>
      <td><?php echo $lieu->begin . ' ' . substr($lieu->start, 0, 5);?></td>
      <td><?php echo $lieu->end . ' ' . substr($lieu->finish, 0, 5);?></td>
      <td title='<?php echo $lieu->desc?>'><?php echo $lieu->desc;?></td>
      <td class='lieu-<?php echo $lieu->status?>'><?php echo zget($this->lang->lieu->statusList, $lieu->status);?></td>
      <td>
        <?php extCommonModel::printLink('lieu', 'review', "id={$lieu->id}&status=pass",   $lang->lieu->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('lieu', 'review', "id={$lieu->id}&status=reject", $lang->lieu->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<?php endif;?>
<?php endif;?>

<?php if($type == 'all' and ($leaveList or $makeupList or $overtimeList or $lieuList)):?>
<div class='panel'>
  <table class='table table-data table-hover text-center table-fixed tablesorter <?php echo $appendClass?>' id='leaveTable'>
    <thead>
      <tr class='text-center'>
        <th class='w-80px'><?php echo $lang->my->reviewModule;?></th>
        <th class='w-80px'><?php echo $lang->leave->createdBy;?></th>
        <th class='w-90px'><?php echo $lang->user->dept;?></th>
        <th class='w-100px'><?php echo $lang->leave->type;?></th>
        <th class='w-120px'><?php echo $lang->leave->begin;?></th>
        <th class='w-120px'><?php echo $lang->leave->end;?></th>
        <th class='w-60px'><?php echo $lang->leave->hours;?></th>
        <th><?php echo $lang->leave->desc;?></th>
        <th class='w-100px'><?php echo $lang->leave->status;?></th>
        <th class='w-100px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($leaveList as $leave):?>
    <tr>
      <td><?php echo $lang->leave->common;?></td>
      <td><?php echo zget($users, $leave->createdBy);?></td>
      <td><?php echo zget($deptList, $leave->dept);?></td>
      <td><?php echo zget($this->lang->leave->typeList, $leave->type);?></td>
      <td><?php echo $leave->begin . ' ' . substr($leave->start, 0, 5);?></td>
      <td><?php echo $leave->end . ' ' . substr($leave->finish, 0, 5);?></td>
      <td><?php echo $leave->hours;?></td>
      <td title='<?php echo $leave->desc?>'>
      <?php echo $leave->desc;?>
      </td>
      <td class='leave-<?php echo $leave->status?>'><?php echo zget($this->lang->leave->statusList, $leave->status);?></td>
      <td>
        <?php $mode = $leave->status == 'pass' ? 'back' : '';?>
        <?php extCommonModel::printLink('leave', 'review', "id={$leave->id}&status=pass&mode=$mode",   $lang->leave->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('leave', 'review', "id={$leave->id}&status=reject&mode=$mode", $lang->leave->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>

    <?php foreach($makeupList as $makeup):?>
    <tr>
      <td><?php echo $lang->makeup->common;?></td>
      <td><?php echo zget($users, $makeup->createdBy);?></td>
      <td><?php echo zget($deptList, $makeup->dept);?></td>
      <td><?php echo zget($this->lang->makeup->typeList, $makeup->type);?></td>
      <td><?php echo $makeup->begin . ' ' . substr($makeup->start, 0, 5);?></td>
      <td><?php echo $makeup->end . ' ' . substr($makeup->finish, 0, 5);?></td>
      <td><?php echo $makeup->hours;?></td>
      <td title='<?php echo $makeup->desc?>'><?php echo $makeup->desc;?></td>
      <td class='leave-<?php echo $makeup->status?>'><?php echo zget($this->lang->leave->statusList, $makeup->status);?></td>
      <td>
        <?php extCommonModel::printLink('makeup', 'review', "id={$makeup->id}&status=pass",   $lang->makeup->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('makeup', 'review', "id={$makeup->id}&status=reject", $lang->makeup->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>

    <?php foreach($overtimeList as $overtime):?>
    <tr>
      <td><?php echo $lang->overtime->common;?></td>
      <td><?php echo zget($users, $overtime->createdBy);?></td>
      <td><?php echo zget($deptList, $overtime->dept);?></td>
      <td><?php echo zget($this->lang->overtime->typeList, $overtime->type);?></td>
      <td><?php echo $overtime->begin . ' ' . substr($overtime->start, 0, 5);?></td>
      <td><?php echo $overtime->end . ' ' . substr($overtime->finish, 0, 5);?></td>
      <td><?php echo $overtime->hours;?></td>
      <td title='<?php echo $overtime->desc?>'><?php echo $overtime->desc;?></td>
      <td class='overtime-<?php echo $overtime->status?>'><?php echo zget($this->lang->overtime->statusList, $overtime->status);?></td>
      <td>
        <?php extCommonModel::printLink('overtime', 'review', "id={$overtime->id}&status=pass",   $lang->overtime->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('overtime', 'review', "id={$overtime->id}&status=reject", $lang->overtime->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>

    <?php foreach($lieuList as $lieu):?>
    <tr>
      <td><?php echo $lang->lieu->common;?></td>
      <td><?php echo zget($users, $lieu->createdBy);?></td>
      <td><?php echo zget($deptList, $lieu->dept);?></td>
      <td><?php echo $lang->my->lieu;?></td>
      <td><?php echo $lieu->begin . ' ' . substr($lieu->start, 0, 5);?></td>
      <td><?php echo $lieu->end . ' ' . substr($lieu->finish, 0, 5);?></td>
      <td><?php echo $lieu->hours;?></td>
      <td title='<?php echo $lieu->desc?>'><?php echo $lieu->desc;?></td>
      <td class='lieu-<?php echo $lieu->status?>'><?php echo zget($this->lang->lieu->statusList, $lieu->status);?></td>
      <td>
        <?php extCommonModel::printLink('lieu', 'review', "id={$lieu->id}&status=pass",   $lang->lieu->statusList['pass'],   "data-status='pass' data-toggle='ajax'");?>
        <?php extCommonModel::printLink('lieu', 'review', "id={$lieu->id}&status=reject", $lang->lieu->statusList['reject'], "data-toggle='modal'");?>
      </td>
    </tr>
    <?php endforeach;?>

  </table>
</div>
<?php elseif($type == 'all' and empty($attends)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
  </p>
</div>
<?php endif;?>
<script>
$(function()
{
    $('[data-toggle=ajax]').click(function()
    {
        if($(this).hasClass('disabled')) return false;
        var status = $(this).data('status');
        if(status == 'undefined' || confirm(confirmReview[status]))
        {
            $.get($(this).prop('href'), function(response)
            {
                if(response.message) $.zui.messager.success(response.message);
                if(response.result == 'success') location.reload();
                return false;
            }, 'json');
        }
        return false;
    });
});

</script>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
