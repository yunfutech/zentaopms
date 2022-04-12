<?php
/**
 * The edit view of productplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     productplan
 * @version     $Id: edit.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('weekend', $config->execution->weekend);?>
<?php js::import($jsRoot . 'misc/date.js');?>
<?php js::set('today', helper::today());?>
<?php js::set('oldBranch', $oldBranch);?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo html::a(inlink('view', "id=$plan->id"), $plan->title, '', "title='$plan->title'");?></strong></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' target='hiddenwin' id='dataform'>
      <table class='table table-form'>
        <tbody>
          <tr>
            <th><?php echo $lang->productplan->product;?></th>
            <td class='muted'><?php echo $product->name;?></td><td></td><td></td>
          </tr>
          <?php if($product->type != 'normal' and $plan->parent != '-1'):?>
          <tr>
            <th><?php echo $lang->product->branch;?></th>
            <td><?php echo html::select('branch', $branchTagOption, $plan->branch, "onchange='getConflictStories($plan->id, this.value); 'class='form-control'");?></td><td></td><td></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->productplan->title;?></th>
            <td><?php echo html::input('title', $plan->title, "class='form-control' required");?></td><td></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->productplan->status;?></th>
            <?php $disabled = $plan->parent == -1 ? "disabled='disabled'" : '' ;?>
            <td><?php echo html::select('status', array_slice($lang->productplan->statusList,($plan->status == 'wait' ? 0 : 1)), $plan->status, "class='form-control chosen' $disabled");?></td>
          </tr>
          <tr>
            <?php $checked = ($plan->begin  == $config->productplan->future and $plan->end == $config->productplan->future) ? "checked='checked'" : '';?>
            <th><?php echo $lang->productplan->begin;?></th>
            <td><?php echo html::input('begin', $plan->begin != $config->productplan->future ? formatTime($plan->begin) : '', "class='form-control form-date'");?></td>
            <td>
              <div class='checkbox-primary' id='checkBox'>
                <input type='checkbox' id='future' name='future' value='1' <?php echo $checked;?> />
                <label for='future'><?php echo $lang->productplan->future;?></label>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->productplan->end;?></th>
            <td><?php echo html::input('end', $plan->end != $config->productplan->future ? formatTime($plan->end) : '', "class='form-control form-date'");?></td>
            <?php $deltaValue = $plan->end == $config->productplan->future ? 0 : (strtotime($plan->end) - strtotime($plan->begin)) / 3600 / 24 + 1;?>
            <td colspan='2'><?php echo html::radio('delta', $lang->productplan->endList , $deltaValue, "onclick='computeEndDate(this.value)'");?></td>
          </tr>
          <?php $this->printExtendFields($plan, 'table', 'columns=3');?>
          <tr>
            <th><?php echo $lang->productplan->desc;?></th>
            <td colspan='3'><?php echo html::textarea('desc', htmlSpecialString($plan->desc), "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
          </tr>
          <tr>
            <td colspan='4' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
              <?php echo html::hidden('product', $product->id);?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
