<?php
/**
 * The html productlist file of productlist method of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/sortable.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->product->featureBar['all'] as $key => $label):?>
    <?php $recTotalLabel = $browseType == $key ? " <span class='label label-light label-badge'>{$recTotal}</span>" : '';?>
    <?php echo html::a(inlink("all", "browseType=$key&orderBy=$orderBy"), "<span class='text'>{$label}</span>" . $recTotalLabel, '', "class='btn btn-link' id='{$key}Tab'");?>
    <?php endforeach;?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('product', 'export', "status=$browseType&orderBy=$orderBy", "<i class='icon-export muted'> </i>" . $lang->export, '', "class='btn btn-link export'")?>
    <?php common::printLink('product', 'manageLine', '', "<i class='icon-edit'></i> &nbsp;" . $lang->product->line, '', 'class="btn btn-link iframe"', '', true);?>
    <?php common::printLink('product', 'create', '', '<i class="icon icon-plus"></i>' . $lang->product->create, '', 'class="btn btn-primary create-product-btn"');?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <?php if(empty($productStructure)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->product->noProduct;?></span></p>
  </div>
  <?php else:?>
  <div class="main-col">
    <form class="main-table table-product" data-ride="table" data-nested='true' id="productListForm" method="post" action='<?php echo inLink('batchEdit', '');?>' data-preserve-nested='false' data-expand-nest-child='true'>
      <?php
      $canOrder     = common::hasPriv('product', 'updateOrder');
      $canBatchEdit = common::hasPriv('product', 'batchEdit');
      ?>
      <table id="productList" class="table has-sort-head table-nested table-fixed">
        <?php $vars = "browseType=$browseType&orderBy=%s";?>
        <thead>
          <tr class="text-center">
            <?php if($canBatchEdit):?>
            <th class='text-left c-checkbox' rowspan="2">
              <?php echo "<div class='checkbox-primary check-all' title='{$this->lang->selectAll}'><label></label></div>";?>
            </th>
            <?php endif;?>
            <th class='table-nest-title text-left c-name' rowspan="2">
              <a class='table-nest-toggle table-nest-toggle-global' data-expand-text='<?php echo $lang->expand; ?>' data-collapse-text='<?php echo $lang->collapse; ?>'></a>
              <?php common::printOrderLink('name', $orderBy, $vars, $lang->product->name);?>
            </th>
            <?php if($this->config->URAndSR):?>
            <th class='c-requirement' colspan="4"><?php echo $lang->story->requirement;?></th>
            <?php endif;?>
            <th class="c-story" colspan="4"><?php echo $lang->story->story;?></th>
            <th class="c-bug" colspan="3"><?php echo $lang->bug->common;?></th>
            <th class="c-plan"  rowspan="2"><?php echo $lang->product->plan;?></th>
            <th class="c-release"  rowspan="2"><?php echo $lang->product->release;?></th>
            <?php
            $extendFields = $this->product->getFlowExtendFields();
            foreach($extendFields as $extendField) echo "<th rowspan='2'>{$extendField->name}</th>";
            ?>
            <th class='c-actions' rowspan="2"><?php echo $lang->actions;?></th>
          </tr>
          <tr class="text-center">
            <?php if($this->config->URAndSR):?>
            <th style="border-left: 1px solid #ddd;"><?php echo $lang->story->draft;?></th>
            <th><?php echo $lang->story->activate;?></th>
            <th><?php echo $lang->story->change;?></th>
            <th><div class='en-wrap-text'><?php echo $lang->story->completeRate;?></div></th>
            <?php endif;?>
            <th style="border-left: 1px solid #ddd;"><?php echo $lang->story->draft;?></th>
            <th><?php echo $lang->story->activate;?></th>
            <th><?php echo $lang->story->change;?></th>
            <th><div class='en-wrap-text'><?php echo $lang->story->completeRate;?></div></th>
            <th style="border-left: 1px solid #ddd;"><?php echo $lang->bug->activate;?></th>
            <th><?php echo $lang->close;?></th>
            <th><?php echo $lang->bug->fixedRate;?></th>
          </tr>
        </thead>
        <tbody id="productTableList">
        <?php $lineNames = array();?>
        <?php foreach($productStructure as $programID => $program):?>
        <?php
        $trAttrs  = "data-id='program.$programID' data-parent='0' data-nested='true'";
        $trClass  = 'is-top-level table-nest-child text-center';
        $trAttrs .= " class='$trClass'";
        ?>
          <?php if(isset($program['programName']) and $config->systemMode == 'new'):?>
          <tr class="row-program" <?php echo $trAttrs;?>>
            <?php if($canBatchEdit):?>
            <td class='c-checkbox'><div class='checkbox-primary program-checkbox'><label></label></div></td>
            <?php endif;?>
            <td class='text-left table-nest-title' title="<?php echo $program['programName']?>">
              <span class="table-nest-icon icon table-nest-toggle"></span>
              <?php echo $program['programName']?>
            </td>
            <?php if($this->config->URAndSR):?>
            <td><?php echo $program['draftRequirements'];?></td>
            <td><?php echo $program['activeRequirements'];?></td>
            <td><?php echo $program['changedRequirements'];?></td>
            <td><?php echo $program['totalRequirements'] == 0 ? 0 : round($program['closedRequirements'] / $program['totalRequirements'], 3) * 100;?>%</td>
            <?php endif;?>
            <td><?php echo $program['draftStories'];?></td>
            <td><?php echo $program['activeStories'];?></td>
            <td><?php echo $program['changedStories'];?></td>
            <td><?php echo $program['totalStories'] == 0 ? 0 : round($program['closedStories'] / $program['totalStories'], 3) * 100;?>%</td>
            <td><?php echo $program['unResolvedBugs'];?></td>
            <td><?php echo $program['closedBugs'];?></td>
            <td><?php echo ($program['unResolvedBugs'] + $program['fixedBugs']) == 0 ? 0 : round($program['fixedBugs'] / ($program['unResolvedBugs'] + $program['fixedBugs']), 3) * 100;?>%</td>
            <td><?php echo $program['plans'];?></td>
            <td><?php echo $program['releases'];?></td>
            <?php foreach($extendFields as $extendField) echo "<td></td>";?>
            <td></td>
          </tr>
          <?php unset($program['programName']);?>
          <?php endif;?>

          <?php foreach($program as $lineID => $line):?>
          <?php if(isset($line['lineName'])):?>
          <?php $lineNames[] = $line['lineName'];?>
          <?php
          if($this->config->systemMode == 'new' and $programID)
          {
              $trAttrs  = "data-id='line.$lineID' data-parent='program.$programID'";
              $trAttrs .= " data-nest-parent='program.$programID' data-nest-path='program.$programID,line.$lineID'" . "class='text-center'";
          }
          else
          {
              $trAttrs  = "data-id='line.$lineID' data-parent='0' data-nested='true'";
              $trClass  = 'is-top-level table-nest-child text-center';
              $trAttrs .= " class='$trClass'";
          }
          ?>
          <tr class="row-line" <?php echo $trAttrs;?>>
            <?php if($canBatchEdit):?>
            <td class='c-checkbox'><div class='checkbox-primary program-checkbox'><label></label></div></td>
            <?php endif;?>
            <td class='text-left table-nest-title' title="<?php echo $line['lineName']?>">
              <span class="table-nest-icon icon table-nest-toggle"></span>
              <?php echo $line['lineName']?>
            </td>
            <?php if($this->config->URAndSR):?>
            <td><?php echo $line['draftRequirements'];?></td>
            <td><?php echo $line['activeRequirements'];?></td>
            <td><?php echo $line['changedRequirements'];?></td>
            <td><?php echo $line['totalRequirements'] == 0 ? 0 : round($line['closedRequirements'] / $line['totalRequirements'], 3) * 100;?>%</td>
            <?php endif;?>
            <td><?php echo $line['draftStories'];?></td>
            <td><?php echo $line['activeStories'];?></td>
            <td><?php echo $line['changedStories'];?></td>
            <td><?php echo $line['totalStories'] == 0 ? 0 : round($line['closedStories'] / $line['totalStories'], 3) * 100;?>%</td>
            <td><?php echo $line['unResolvedBugs'];?></td>
            <td><?php echo $line['closedBugs'];?></td>
            <td><?php echo ($line['unResolvedBugs'] + $line['fixedBugs']) == 0 ? 0 : round($line['fixedBugs'] / ($line['unResolvedBugs'] + $line['fixedBugs']), 3) * 100;?>%</td>
            <td><?php echo $line['plans'];?></td>
            <td><?php echo $line['releases'];?></td>
            <?php foreach($extendFields as $extendField) echo "<td></td>";?>
            <td></td>
          </tr>
          <?php unset($line['lineName']);?>
          <?php endif;?>

          <?php if(isset($line['products']) and is_array($line['products'])):?>
          <?php foreach($line['products'] as $productID => $product):?>
          <?php
          $totalStories      = $product->stories['active'] + $product->stories['closed'] + $product->stories['draft'] + $product->stories['changed'];
          $totalRequirements = $product->requirements['active'] + $product->requirements['closed'] + $product->requirements['draft'] + $product->requirements['changed'];

          $trClass = '';
          if($product->line)
          {
              $path = "line.$product->line,$product->id";
              if($this->config->systemMode == 'new' and $product->program) $path = "program.$product->program,$path";
              $trAttrs  = "data-id='$product->id' data-parent='line.$product->line'";
              $trClass .= ' is-nest-child  table-nest';
              $trAttrs .= " data-nest-parent='line.$product->line' data-nest-path='$path'";
          }
          elseif($product->program and $this->config->systemMode == 'new')
          {
              $trAttrs  = "data-id='$product->id' data-parent='program.$product->program'";
              $trClass .= ' is-nest-child  table-nest';
              $trAttrs .= " data-nest-parent='program.$product->program' data-nest-path='program.$product->program,$product->id'";
          }
          else
          {
              $trAttrs  = "data-id='$product->id' data-parent='0'";
              $trClass .= ' no-nest';
          }
          $trAttrs .= " class='$trClass'";
          ?>
          <tr class="row-product" <?php echo $trAttrs;?>>
            <?php if($canBatchEdit):?>
            <td class='c-checkbox'><?php echo html::checkbox('productIDList', array($product->id => ''));?></td>
            <?php endif;?>
            <td class="c-name text-left table-nest-title" title='<?php echo $product->name?>'>
              <?php
              $productLink = html::a($this->createLink('product', 'browse', 'product=' . $product->id), $product->name);
              echo "<span class='table-nest-icon icon icon-product'></span>" . $productLink;
              ?>
            </td>
            <?php if($this->config->URAndSR):?>
            <td><?php echo $product->requirements['draft'];?></td>
            <td><?php echo $product->requirements['active'];?></td>
            <td><?php echo $product->requirements['changed'];?></td>
            <td><?php echo $totalRequirements == 0 ? 0 : round($product->requirements['closed'] / $totalRequirements, 3) * 100;?>%</td>
            <?php endif;?>
            <td><?php echo $product->stories['draft'];?></td>
            <td><?php echo $product->stories['active'];?></td>
            <td><?php echo $product->stories['changed'];?></td>
            <td><?php echo $totalStories == 0 ? 0 : round($product->stories['closed'] / $totalStories, 3) * 100;?>%</td>
            <td><?php echo $product->unResolved;?></td>
            <td><?php echo $product->closedBugs;?></td>
            <td><?php echo ($product->unResolved + $product->fixedBugs) == 0 ? 0 : round($product->fixedBugs / ($product->unResolved + $product->fixedBugs), 3) * 100;?>%</td>
            <td><?php echo $product->plans;?></td>
            <td><?php echo $product->releases;?></td>
            <?php foreach($extendFields as $extendField) echo "<td>" . $this->loadModel('flow')->getFieldValue($extendField, $product) . "</td>";?>
            <td class='c-actions sort-handler'>
              <?php common::printIcon('product', 'edit', "product=$product->id", $product, 'list', 'edit');?>
              <?php if($canOrder):?>
              <i class="icon icon-move text-blue"></i>
              <?php endif;?>
            </td>
          </tr>
          <?php endforeach;?>
          <?php endif;?>
          <?php endforeach;?>
        <?php endforeach;?>
        </tbody>
      </table>
      <?php if(!empty($product) and $canBatchEdit):?>
      <div class='table-footer'>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <div class="table-actions btn-toolbar">
        <?php
        $actionLink = $this->createLink('product', 'batchEdit');
        echo html::commonButton($lang->edit, "data-form-action='$actionLink'");
        ?>
        </div>
      </div>
      <?php endif;?>
    </form>
  </div>
  <?php endif;?>
</div>
<?php js::set('orderBy', $orderBy)?>
<?php js::set('browseType', $browseType)?>
<?php if(commonModel::isTutorialMode()): ?>
<style>
#productListForm {overflow: hidden;}
#productList .table-nest-title {width: 200px;}
</style>
<?php endif; ?>
<?php include '../../common/view/footer.html.php';?>
