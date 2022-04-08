<?php
/**
 * The editlib file of doc module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     doc
 * @version     $Id: editlib.html.php 975 2010-07-29 03:30:25Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<?php js::set('doclibID', $lib->id);?>
<?php if($lib->type == 'product'):?>
<style> .chosen-container .chosen-results {max-height: 180px;}</style>
<?php else:?>
<style> .chosen-container .chosen-results {max-height: 145px;}</style>
<?php endif;?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
    <h2>
      <span class='prefix'><?php echo html::icon($lang->icons['doclib']);?></span>
      <?php echo $lib->type != 'book' ? $lang->doc->editLib : $lang->doc->editBook;?>
    </h2>
  </div>
  <form method='post' class='load-indicator main-form form-ajax'>
    <table class='table table-form'>
      <?php if(!empty($lib->product)):?>
      <tr>
        <th class='w-130px'><?php echo $lang->doc->product?></th>
        <td><?php echo $product->name?></td>
      </tr>
      <?php endif;?>
      <?php if(!empty($lib->execution)):?>
      <tr>
        <th class='w-130px'><?php echo $lang->doc->execution?></th>
        <td><?php echo $execution->name?></td>
      </tr>
      <?php endif;?>
      <tr>
        <th class='w-130px'><?php echo $lib->type != 'book' ? $lang->doc->libName : $lang->doc->bookName;?></th>
        <td>
          <?php echo html::input('name', $lib->name, "class='form-control'");?>
          <span class='hidden'><?php echo html::radio('type', $lang->doc->libTypeList, $lib->type);?></span>
        </td>
      </tr>
      <tr>
        <th><?php echo $lang->doclib->control;?></th>
        <?php if($lib->type == 'product' or $lib->type == 'execution'):?>
        <td>
          <?php echo html::radio('acl', $lang->doclib->aclListA, $lib->acl, "onchange='toggleAcl(this.value, \"lib\")'")?>
          <span class='text-info' id='noticeAcl'><?php echo $lang->doc->noticeAcl['lib'][$lib->type][$lib->acl];?></span>
        </td>
        <?php else:?>
        <td>
          <?php echo html::radio('acl', $lang->doclib->aclListB, $lib->acl, "onchange='toggleAcl(this.value, \"lib\")'")?>
          <span class='text-info' id='noticeAcl'><?php echo $lang->doc->noticeAcl['lib'][$lib->type][$lib->acl];?></span>
        </td>
        <?php endif;?>
      </tr>
      <tr id='whiteListBox' class='hidden'>
        <th><?php echo $lang->doc->whiteList?></th>
        <td>
          <div id='groupBox' class='input-group'>
            <span class='input-group-addon groups-addon'><?php echo $lang->doclib->group?></span>
            <?php echo html::select('groups[]', $groups, $lib->groups, "class='form-control chosen' multiple")?>
          </div>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->doclib->user?></span>
            <?php echo html::select('users[]', $users, $lib->users, "class='form-control chosen' multiple")?>
          </div>
        </td>
      </tr>
      <tr>
        <td class='text-center form-actions' colspan='2'><?php echo html::submitButton();?></td>
      </tr>
    </table>
  </form>
</div>
<?php js::set('noticeAcl', $lang->doc->noticeAcl['lib']);?>
<?php js::set('libType', $lib->type);?>
<?php include '../../common/view/footer.lite.html.php';?>
