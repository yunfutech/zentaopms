<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-right">
    <?php
      $link = $this->createLink('milestone', 'create', "productID=$productID");
      if(common::hasPriv('milestone', 'create')) echo html::a($link, "<i class='icon icon-plus'></i> {$lang->milestone->create}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>
<div id="mainContent">
  <?php if(empty($milestones)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->noMilestones;?></span></p>
  </div>
  <?php else:?>
  <form class="main-table table-task">
    <table class="table has-sort-head table-fixed">
      <?php $vars = "productID=$productID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
      <thead>
        <tr>
          <th class="c-id">
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th class='w-450px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->milestone->name);?></th>
          <th class='w-150px'><?php common::printOrderLink('date', $orderBy, $vars, $lang->milestone->date);?></th>
          <th class='w-150px'><?php common::printOrderLink('isContract', $orderBy, $vars, $lang->milestone->isContract);?></th>
          <th class='w-150px'><?php common::printOrderLink('pri', $orderBy, $vars, $lang->milestone->pri);?></th>
          <th class='w-150px'><?php common::printOrderLink('completed', $orderBy, $vars, $lang->milestone->completed);?></th>
          <th><?php common::printOrderLink('comment', $orderBy, $vars, $lang->milestone->comment);?></th>
          <th class='c-actions-2'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($milestones as $milestone):?>
        <tr>
          <td class="c-id"><?php echo $milestone->id?></td>
          <td><?php echo $milestone->name?></td>
          <td><?php echo $milestone->date?></td>
          <td><?php echo $milestone->isContract ? '是' : '否'?></td>
          <td><?php echo $milestone->pri?></td>
          <td><?php echo $milestone->completed ? '是' : '否'?></td>
          <td><?php echo $milestone->comment?></td>
          <td class='c-actions'>
            <?php
              if(common::hasPriv('milestone', 'edit')) common::printIcon('milestone', 'edit', "milestoneID=$milestone->id&productID=$productID", $milestone, 'list');
            ?>
            <?php
              if(common::hasPriv('milestone', 'delete')) common::printIcon('milestone', 'delete', "milestoneID=$milestone->id&productID=$productID", $milestone, 'list', 'trash', 'hiddenwin');
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
<?php include '../../common/view/footer.html.php';?>
