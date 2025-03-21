<?php
/**
 * The create view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<?php js::set('programID', $programID);?>
<?php js::set('systemMode', $this->config->systemMode);?>
<?php js::set('manageLinePriv', common::hasPriv('product', 'manageLine'));?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->product->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax<?php if(defined('TUTORIAL')) echo ' not-watch';?>" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->program->common;?></th>
            <td><?php echo html::select('program', $programs, $programID, "class='form-control chosen' onchange='setParentProgram(this.value)'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->name;?></th>
            <td><?php echo html::input('name', '', "class='form-control input-product-title' required");?></td><td></td>
          </tr>
          <?php if(isset($config->setCode) and $config->setCode == 1):?>
          <tr>
            <th><?php echo $lang->product->code;?></th>
            <td><?php echo html::input('code', '', "class='form-control' required");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->product->PMT;?></th>
            <td><?php echo html::select('PMT[]', $users, $app->user->account, "class='form-control picker-select' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->reviewer;?></th>
            <td><?php echo html::select('reviewer[]', $users, '', "class='form-control picker-select' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->type;?></th>
            <td>
              <?php
              $proudctTypeList = array();
              foreach($lang->product->typeList as $key => $type) $productTypeList[$key] = $type . zget($lang->product->typeTips, $key, '');
              ?>
              <?php echo html::select('type', $productTypeList, 'normal', "class='form-control'");?>
            </td>
            <td></td>
          </tr>
          <tr class='hide'>
            <th><?php echo $lang->product->status;?></th>
            <td><?php echo html::hidden('status', 'normal');?></td>
            <td></td>
          </tr>
          <?php $this->printExtendFields('', 'table');?>
          <tr>
            <th><?php echo $lang->product->desc;?></th>
            <td colspan='2'>
              <?php echo $this->fetch('user', 'ajaxPrintTemplates', "type=product&link=desc");?>
              <?php echo html::textarea('desc', '', "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->product->acl;?></th>
            <td colspan='2'><?php echo nl2br(html::radio('acl', $lang->product->aclList, 'private', "onclick='setWhite(this.value);'", 'block'));?></td>
          </tr>
          <tr id="whitelistBox">
            <th><?php echo $lang->whitelist;?></th>
            <td colspan='1'>
              <div class='input-group'>
                <?php echo html::select('whitelist[]', $users, '', 'class="form-control picker-select" multiple');?>
                <?php echo $this->fetch('my', 'buildContactLists', "dropdownName=whitelist");?>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::hidden('status', 'wait');?>
              <?php echo html::submitButton();?>
              <?php echo $gobackLink ? html::a($gobackLink, $lang->goback, '', 'class="btn btn-wide"') : html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
