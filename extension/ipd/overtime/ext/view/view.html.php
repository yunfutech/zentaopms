<?php
/**
 * The view file of overtime module of ZDOO.
 *
 * @copyright   Copyright 2009-2018 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     overtime
 * @version     $Id$
 * @link        http://www.zdoo.com
 */
?>
<?php include '../../../common/view/header.modal.html.php';?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php';?>
<?php js::set('confirmReview', $lang->overtime->confirmReview)?>
<table class='table table-bordered'>
  <tr>
    <th><?php echo $lang->overtime->status;?></th>
    <td class='overtime-<?php echo $overtime->status;?>'><?php echo $lang->overtime->statusList[$overtime->status];?></td>
    <th><?php echo $lang->overtime->type?></th>
    <td><?php echo zget($lang->overtime->typeList, $overtime->type);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->begin?></th>
    <td><?php echo formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2);?></td>
    <th><?php echo $lang->overtime->end?></th>
    <td><?php echo formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->hours?></th>
    <td colspan='3'><?php echo $overtime->hours . $lang->overtime->hoursTip;?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->desc?></th>
    <td colspan='3'><?php echo $overtime->desc;?></td>
  </tr>
  <?php if($overtime->status == 'reject' and $overtime->rejectReason):?>
  <tr>
    <th><?php echo $lang->overtime->rejectReason;?></th>
    <td colspan='3'><?php echo $overtime->rejectReason;?></td>
  </tr>
  <?php endif;?>
  <tr>
    <th><?php echo $lang->overtime->createdBy;?></th>
    <td><?php echo zget($users, $overtime->createdBy);?></td>
    <th><?php echo $lang->overtime->reviewedBy;?></th>
    <td id='reviewedBy'><?php echo zget($users, $overtime->reviewedBy);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->createdDate;?></th>
    <td><?php echo formatTime($overtime->createdDate, DT_DATETIME1);?></td>
    <th><?php echo $lang->overtime->reviewedDate;?></th>
    <td><?php echo formatTime($overtime->reviewedDate, DT_DATETIME1);?></td>
  </tr>
</table>
<?php echo $this->fetch('action', 'history', "objectType=overtime&objectID=$overtime->id");?>
<div class='page-actions'>
  <?php
  if($type == 'personal')
  {
      $switchLabel = $overtime->status == 'wait' ? $lang->overtime->cancel : $lang->overtime->commit;
      if(strpos(',wait,draft,', ",$overtime->status,") !== false)
      {
          extCommonModel::printLink('oa.overtime', 'switchstatus', "id=$overtime->id", $switchLabel, "class='switch-status btn'");
      }
      if(strpos(',wait,draft,reject,', ",$overtime->status,") !== false)
      {
          echo "<div class='btn-group'>";
          extCommonModel::printLink('oa.overtime', 'edit',   "id=$overtime->id", $lang->edit,   "class='btn loadInModal'");
          extCommonModel::printLink('oa.overtime', 'delete', "id=$overtime->id", $lang->delete, "class='btn deleteOvertime'");
          echo '</div>';
      }
  }
  elseif(strpos(',wait,doing,', ",$overtime->status,") !== false)
  {
      extCommonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=pass",   $lang->overtime->statusList['pass'],   "class='btn reviewPass'");
      extCommonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=reject", $lang->overtime->statusList['reject'], "class='btn loadInModal'");
  }
  echo baseHTML::a('#', $lang->goback, "class='btn' data-dismiss='modal'");
  ?>
</div>
<?php include '../../../common/view/footer.modal.html.php';?>

