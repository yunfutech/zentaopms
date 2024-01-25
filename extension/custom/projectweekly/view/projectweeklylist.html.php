<?php
/**
 * The build view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: build.html.php 4262 2013-01-24 08:48:56Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../../module/common/view/header.html.php';?>
<div id="mainMenu" class="clearfix table-row">
  <div class="btn-toolbar pull-right">
    <?php if(common::canModify('project', $project)) common::printLink('projectweekly', 'generateWeekly', "projectID=$projectID", "<i class='icon icon-plus'></i> " . $lang->projectweekly->generateWeekly, '', "class='btn btn-primary' id='createBuild'");?>
  </div>
</div>
<div id="mainContent">
  <?php if(empty($weeklies)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->projectweekly->noWeekly;?></span></p>
  </div>
  <?php else:?>
  <form class="main-table table-task">
    <table class="table has-sort-head table-fixed">
      <?php $vars = "projectID=$projectID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
      <thead>
        <tr>
          <th class="c-id">
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectweekly->weeklyName);?></th>
          <th class='w-150px'><?php common::printOrderLink('account', $orderBy, $vars, $lang->projectweekly->account);?></th>
          <th class='w-150px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->projectweekly->createdDate);?></th>
          <th class='c-actions-1'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($weeklies as $weekly):?>
        <tr>
          <td class="c-id">
            <?php printf('%03d', $weekly->id);?>
          </td>
          <td title="<?php echo $weekly->name;?>"><?php echo html::a($this->createLink('projectweekly', 'view', "weeklyID=$weekly->id&projectID=$projectID"), $weekly->name);?></td>
          <td><?php echo $weekly->account?></td>
          <td><?php echo $weekly->createdDatetime?></td>
          <td class='c-actions'>
            <?php
              common::printIcon('projectweekly', 'edit', "weeklyID=$weekly->id&projectID=$projectID", $weekly, 'list');
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <div class="table-footer">
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php endif;?>
</div>
<?php include '../../../../module/common/view/footer.html.php';?>