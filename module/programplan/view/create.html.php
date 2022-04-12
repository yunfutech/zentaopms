<?php
/**
 * The create of programplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     programplan
 * @version     $Id: create.html.php 4903 2013-06-26 05:32:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('browseType', $type);?>
<style>.icon-help{margin-left: 3px;}</style>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <span class='btn btn-link btn-active-text'>
      <?php
      $title = $lang->programplan->create;
      if($planID) $title = $programPlan->name . $lang->project->stage . '（' . $programPlan->begin . $lang->project->to . $programPlan->end . '）';
      echo "<span class='text'>{$title}</span>";
      ?>
    </span>
    <div class='btn-group'>
      <?php $viewName = $productID != 0 ? zget($productList,$productID) : current($productList);?>
      <a href='javascript:;' class='btn btn-link btn-limit' data-toggle='dropdown'><span class='text' title='<?php echo $viewName;?>'><?php echo $viewName;?></span> <span class='caret'></span></a>
      <ul class='dropdown-menu' style='max-height:240px; max-width: 300px; overflow-y:auto'>
        <?php
          foreach($productList as $key => $product)
          {
            echo "<li>" . html::a($this->createLink('programplan', 'create', "projectID=$project->id&productID=$key"), $product) . "</li>";
          }
        ?>
      </ul>
    </div>
  </div>
</div>
<?php $hideAttribute = $planID == 0 ? '' : ' hidden'?>
<?php $class = $planID == 0 ? '' : "disabled='disabled'"?>
<?php $name = $planID == 0 ? $lang->programplan->name : $lang->programplan->subStageName;?>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <div class="btn-toolbar pull-right">
      <?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=programplan&section=custom&key=createFields')?>
      <?php include '../../common/view/customfield.html.php';?>
    </div>
  </div>
  <form class='main-form form-ajax' method='post' id='planForm' enctype='multipart/form-data'>
    <div class='table-responsive'>
      <table class='table table-form'>
        <thead>
          <tr class='text-center'>
            <th class='c-name required'><?php echo $name;?></th>
            <th class='c-pm <?php echo zget($visibleFields, 'PM', ' hidden') . zget($requiredFields, 'PM', '', ' required');?>'><?php echo $lang->programplan->PM;?></th>
            <th class='c-percent <?php echo zget($visibleFields, 'percent', ' hidden') . zget($requiredFields, 'percent', '', ' required');?>'>
              <?php echo $lang->programplan->percent;?>
              <?php if($planID):?>
              <i class='icon-help' data-toggle='popover' data-trigger='focus hover' data-placement='right' data-tip-class='text-muted popover-sm' data-container="body" data-content="<?php echo $lang->programplan->workloadTips;?>"></i>
              <?php endif;?>
            </th>
            <th class='c-attribute <?php echo zget($visibleFields, 'attribute', ' hidden') . zget($requiredFields, 'attribute', '', ' required');?> <?php echo $hideAttribute?>'><?php echo $lang->programplan->attribute;?></th>
            <th class='c-acl <?php echo zget($visibleFields, 'acl', ' hidden') . zget($requiredFields, 'acl', '', ' required');?>'><?php echo $lang->programplan->acl;?></th>
            <th class='w-110px <?php echo zget($visibleFields, 'milestone', ' hidden') . zget($requiredFields, 'milestone', '', ' required');?>'><?php echo $lang->programplan->milestone;?></th>
            <th class='c-date required'><?php echo $lang->programplan->begin;?></th>
            <th class='c-date required'><?php echo $lang->programplan->end;?></th>
            <th class='c-date <?php echo zget($visibleFields, 'realBegan', ' hidden') . zget($requiredFields, 'realBegan', '', ' required');?>'><?php echo $lang->programplan->realBegan;?></th>
            <th class='c-date <?php echo zget($visibleFields, 'realEnd', ' hidden') . zget($requiredFields, 'realEnd', '', ' required');?>'><?php echo $lang->programplan->realEnd;?></th>
            <?php if($this->config->edition == 'max'):?>
            <th class='w-110px'><?php echo $lang->programplan->output;?></th>
            <?php endif;?>
            <th class="c-action text-center w-110px"> <?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 0;?>
          <?php if(empty($plans) and $planID == 0):?>
            <?php foreach($stages as $stage):?>
            <tr>
              <td><input type='text' name='names[<?php echo $i;?>]' id='names<?php echo $i;?>' value='<?php echo $stage->name;?>' class='form-control' /></td>
              <td <?php echo zget($visibleFields, 'PM', ' hidden') . zget($requiredFields, 'PM', '', ' required');?>><?php echo html::select("PM[$i]", $PMUsers, '', "class='form-control chosen'");?></td>
              <td <?php echo zget($visibleFields, 'percent', ' hidden') . zget($requiredFields, 'percent', '', ' required');?>>
                <div class='input-group'>
                  <input type='text' name='percents[<?php echo $i;?>]' id='percent<?php echo $i;?>' value='<?php echo $stage->percent;?>' class='form-control'/>
                  <span class='input-group-addon'>%</span>
                </div>
              </td>
              <td class='<?php echo $hideAttribute?> <?php echo zget($visibleFields, 'attribute', ' hidden') . zget($requiredFields, 'attribute', '', ' required');?>'><?php echo html::select("attributes[$i]", $lang->stage->typeList, $stage->type, "class='form-control'");?></td>
              <td class='<?php echo zget($visibleFields, 'acl', ' hidden') . zget($requiredFields, 'acl', '', ' required');?>'><?php echo html::select("acl[$i]", $lang->execution->aclList, 'open', "class='form-control' $class");?></td>
              <td <?php echo zget($visibleFields, 'milestone', ' hidden') . zget($requiredFields, 'milestone', '', ' required');?>><?php echo html::radio("milestone[$i]", $lang->programplan->milestoneList, 0);?></td>
              <td><input type='text' name='begin[<?php echo $i;?>]' id='begin<?php echo $i;?>' value='' class='form-control form-date' /></td>
              <td><input type='text' name='end[<?php echo $i;?>]' id='end<?php echo $i;?>' value='' class='form-control form-date' /></td>
              <td <?php echo zget($visibleFields, 'realBegan', ' hidden') . zget($requiredFields, 'realBegan', '', ' required');?>><input type='text' name='realBegan[<?php echo $i;?>]' id='realBegan<?php echo $i;?>' value='' class='form-control form-date' /></td>
              <td <?php echo zget($visibleFields, 'realEnd', ' hidden') . zget($requiredFields, 'realEnd', '', ' required');?>><input type='text' name='realEnd[<?php echo $i;?>]' id='realEnd<?php echo $i;?>' value='' class='form-control form-date' /></td>
              <?php if($this->config->edition == 'max'):?>
              <td><?php echo html::select("output[$i][]", $documentList, '', "class='form-control chosen' multiple");?></td>
              <?php endif;?>
              <td class='c-actions text-left'>
                <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
                <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
              </td>
            </tr>
            <?php $i ++;?>
            <?php endforeach;?>
          <?php endif;?>
          <?php if(!empty($plans)):?>
            <?php foreach($plans as $plan):?>
            <?php $disabled = $plan->setMilestone ? '' : "disabled='disabled'"?>
            <?php echo html::hidden("planIDList[$i]", $plan->id);?>
            <tr>
              <td><input type='text' name="names[<?php echo $i;?>]" id='names<?php echo $i;?>' value='<?php echo $plan->name;?>' class='form-control' /></td>
              <td <?php echo zget($visibleFields, 'PM', ' hidden') . zget($requiredFields, 'PM', '', ' required');?>><?php echo html::select("PM[$i]", $PMUsers, $plan->PM, "class='form-control chosen'");?></td>
              <td <?php echo zget($visibleFields, 'percent', ' hidden') . zget($requiredFields, 'percent', '', ' required');?>>
                <div class='input-group'>
                  <input type='text' name='percents[<?php echo $i;?>]' id='percent<?php echo $i;?>' value='<?php echo $plan->percent;?>' class='form-control' />
                  <span class='input-group-addon'>%</span>
                </div>
              </td>
              <td class='<?php echo $hideAttribute?> <?php echo zget($visibleFields, 'attribute', ' hidden') . zget($requiredFields, 'attribute', '', ' required');?>'><?php echo html::select("attributes[$i]", $lang->stage->typeList, $plan->attribute, "class='form-control'");?></td>
              <td <?php echo zget($visibleFields, 'acl', ' hidden') . zget($requiredFields, 'acl', '', ' required');?>><?php echo html::select("acl[$i]", $lang->execution->aclList, $plan->acl, "class='form-control' $class");?></td>
              <td <?php echo zget($visibleFields, 'milestone', ' hidden') . zget($requiredFields, 'milestone', '', ' required');?>><?php echo html::radio("milestone[$i]", $lang->programplan->milestoneList, $plan->milestone, $disabled);?></td>
              <td><input type='text' name='begin[<?php echo $i;?>] ' id='begin<?php echo $i;?>' value='<?php echo $plan->begin;?>' class='form-control form-date' /></td>
              <td><input type='text' name='end[<?php echo $i;?>]' id='end<?php echo $i;?>' value='<?php echo $plan->end;?>' class='form-control form-date' /></td>
              <td <?php echo zget($visibleFields, 'realBegan', ' hidden') . zget($requiredFields, 'realBegan', '', ' required');?>><input type='text' name='realBegan[<?php echo $i;?>] ' id='realBegan<?php echo $i;?>' value='<?php echo $plan->realBegan;?>' class='form-control form-date' /></td>
              <td <?php echo zget($visibleFields, 'realEnd', ' hidden') . zget($requiredFields, 'realEnd', '', ' required');?>><input type='text' name='realEnd[<?php echo $i;?>]' id='realEnd<?php echo $i;?>' value='<?php echo $plan->realEnd;?>' class='form-control form-date' /></td>
              <?php if($this->config->edition == 'max'):?>
              <?php $option = empty($plan->output) ? 0 : explode(',', $plan->output);?>
              <td><?php echo html::select("output[$i][]", $documentList, $option, "class='form-control chosen' multiple");?></td>
              <?php endif;?>
              <td class='c-actions text-left'>
                <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
              </td>
            </tr>
            <?php $i ++;?>
            <?php endforeach;?>
          <?php endif;?>
          <?php for($j = 0; $j < 5; $j ++):?>
          <tr class='addedItem'>
            <td><input type='text' name='names[<?php echo $i;?>]' id='names<?php echo $i;?>' value='' class='form-control' /></td>
            <td <?php echo zget($visibleFields, 'PM', ' hidden') . zget($requiredFields, 'PM', '', ' required');?>><?php echo html::select("PM[$i]", $PMUsers, '', "class='form-control chosen'");?></td>
            <td <?php echo zget($visibleFields, 'percent', ' hidden') . zget($requiredFields, 'percent', '', ' required');?>>
              <div class='input-group'>
                <input type='text' name='percents[<?php echo $i;?>]' id='percent<?php echo $i;?>' value='' class='form-control' />
                <span class='input-group-addon'>%</span>
              </div>
            </td>
            <td class='<?php echo $hideAttribute?> <?php echo zget($visibleFields, 'attribute', ' hidden') . zget($requiredFields, 'attribute', '', ' required');?>'><?php echo html::select("attributes[$i]", $lang->stage->typeList, '', "class='form-control'");?></td>
            <td <?php echo zget($visibleFields, 'acl', ' hidden') . zget($requiredFields, 'acl', '', ' required');?>><?php echo html::select("acl[$i]", $lang->execution->aclList, 'open', "class='form-control' $class");?></td>
            <td <?php echo zget($visibleFields, 'milestone', ' hidden') . zget($requiredFields, 'milestone', '', ' required');?>><?php echo html::radio("milestone[$i]", $lang->programplan->milestoneList, 0);?></td>
            <td><input type='text' name='begin[<?php echo $i;?>] ' id='begin<?php echo $i;?>' value='' class='form-control form-date' /></td>
            <td><input type='text' name='end[<?php echo $i;?>]' id='end<?php echo $i;?>' value='' class='form-control form-date' /></td>
            <td <?php echo zget($visibleFields, 'realBegan', ' hidden') . zget($requiredFields, 'realBegan', '', ' required');?>><input type='text' name='realBegan[<?php echo $i;?>] ' id='realBegan<?php echo $i;?>' value='' class='form-control form-date' /></td>
            <td <?php echo zget($visibleFields, 'realEnd', ' hidden') . zget($requiredFields, 'realEnd', '', ' required');?>><input type='text' name='realEnd[<?php echo $i;?>]' id='realEnd<?php echo $i;?>' value='' class='form-control form-date' /></td>
            <?php if($this->config->edition == 'max'):?>
            <td><?php echo html::select("output[$i][]", $documentList, '', "class='form-control chosen' multiple");?></td>
            <?php endif;?>
            <td class='c-actions text-left'>
              <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
              <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
            </td>
          </tr>
          <?php $i ++;?>
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <?php $colspan = $planID == 0 ? $colspan : $colspan - 1;?>
            <td colspan='<?php echo $colspan?>' class='text-center form-actions'><?php echo html::submitButton() . ' ' . html::backButton(); ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php js::set('itemIndex', $i);?>
  </form>
</div>
<div>
  <?php $i = '%i%';?>
  <table class='hidden'>
    <tr id='addItem' class='hidden'>
      <td><input type='text' name='<?php echo "names[$i]";?>' id='names<?php echo $i;?>' class='form-control' /></td>
      <td <?php echo zget($visibleFields, 'PM', ' hidden') . zget($requiredFields, 'PM', '', ' required');?>><?php echo html::select("PM[$i]", $PMUsers, '', "class='form-control chosen' id='PM$i'");?></td>
      <?php echo html::hidden("planIDList[$i]", 0);?>
      <td <?php echo zget($visibleFields, 'percent', ' hidden') . zget($requiredFields, 'percent', '', ' required');?>>
        <div class='input-group'>
          <input type='text' name='<?php echo "percents[$i]";?>' id='percent<?php echo $i;?>' class='form-control' />
          <span class='input-group-addon'>%</span>
        </div>
      </td>
      <td class='<?php echo $hideAttribute?> <?php echo zget($visibleFields, 'attribute', ' hidden') . zget($requiredFields, 'attribute', '', ' required');?>'><?php echo html::select("attributes[$i]", $lang->stage->typeList, '', "class='form-control'");?></td>
      <td <?php echo zget($visibleFields, 'acl', ' hidden') . zget($requiredFields, 'acl', '', ' required');?>><?php echo html::select("acl[$i]", $lang->execution->aclList, 'open', "class='form-control' $class");?></td>
      <td <?php echo zget($visibleFields, 'milestone', ' hidden') . zget($requiredFields, 'milestone', '', ' required');?>><?php echo html::radio("milestone[$i]", $lang->programplan->milestoneList, 0);?></td>
      <td><input type='text' name='<?php echo "begin[$i]";?>' id='begin<?php echo $i;?>' class='form-control form-date' /></td>
      <td><input type='text' name='<?php echo "end[$i]";?>' id='end<?php echo $i;?>' class='form-control form-date' /></td>
      <td <?php echo zget($visibleFields, 'realBegan', ' hidden') . zget($requiredFields, 'realBegan', '', ' required');?>><input type='text' name='<?php echo "realBegan[$i]";?>' id='realBegan<?php echo $i;?>' class='form-control form-date' /></td>
      <td <?php echo zget($visibleFields, 'realEnd', ' hidden') . zget($requiredFields, 'realEnd', '', ' required');?>><input type='text' name='<?php echo "realEnd[$i]";?>' id='realEnd<?php echo $i;?>' class='form-control form-date' /></td>
      <?php if($this->config->edition == 'max'):?>
      <td><?php echo html::select("output[$i][]", $documentList, '', "class='form-control chosen ' multiple");?></td>
      <?php endif;?>
      <td class='c-actions'>
        <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
        <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
      </td>
    </tr>
  </table>
</div>
<script>$('[data-toggle="popover"]').popover();</script>
<?php include '../../common/view/footer.html.php';?>
