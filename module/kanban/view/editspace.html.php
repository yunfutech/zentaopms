<?php
/**
 * The editspace file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     kanban
 * @version     $Id: editspace.html.php 935 2021-12-08 15:46:24Z $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2><?php echo $lang->kanban->editSpace;?></h2>
  </div>
  <form class='form-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
      <tr>
        <th><?php echo $lang->kanbanspace->type;?></th>
        <td><?php echo html::select('type', $typeList, $defaultType, "onchange='changeType(this.value)' class='form-control chosen'");?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->kanbanspace->name;?></th>
        <td><?php echo html::input('name', $space->name, "class='form-control'");?></td>
        <td></td>
      </tr>
      <tr id='ownerBox' class="<?php echo $space->type == 'private' ? 'hidden' : '';?>">
        <th><?php echo $lang->kanbanspace->owner;?></th>
        <td><?php echo html::select('owner', $users, $space->owner, "class='form-control chosen'");?></td>
      </tr>
      <tr id='teamBox' class="<?php echo $space->type == 'private' ? 'hidden' : '';?>">
        <th><?php echo $lang->kanbanspace->team;?></th>
        <td colspan='2'>
          <div class="input-group">
            <?php echo html::select('team[]', $users, $team, "class='form-control chosen' multiple data-drop_direction='down'");?>
            <?php echo $this->fetch('my', 'buildContactLists');?>
          </div>
        </td>
      </tr>
      <tr>
        <th><?php echo $lang->kanbanspace->desc;?></th>
        <td colspan='2'>
          <?php echo $this->fetch('user', 'ajaxPrintTemplates', 'type=kanbanSpace&link=desc');?>
          <?php echo html::textarea('desc', $space->desc, "rows='10' class='form-control'");?>
        </td>
      </tr>
      <tr id="whitelistBox" class="<?php echo $space->type == 'private' ? '' : 'hidden';?>">
        <th><?php echo $lang->whitelist;?></th>
        <td><?php echo html::select('whitelist[]', $users, $space->whitelist, 'class="form-control chosen" multiple');?></td>
      </tr>
      <tr>
        <td colspan='3' class='text-center form-actions'>
          <?php echo html::submitButton();?>
        </td>
      </tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
