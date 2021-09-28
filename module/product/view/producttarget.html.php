<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-right">
    <?php
      $link = $this->createLink('producttarget', 'create', "productID=$productID");
      if(common::hasPriv('producttarget', 'create')) echo html::a($link, "<i class='icon icon-plus'></i> {$lang->producttarget->create}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>
<div id="mainContent">
  <?php if(empty($targets)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->producttarget->noData;?></span></p>
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
          <th class='w-450px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->producttarget->name);?></th>
          <th class='w-150px'><?php common::printOrderLink('confidence', $orderBy, $vars, $lang->producttarget->confidence);?></th>
          <th class='w-150px'><?php common::printOrderLink('target', $orderBy, $vars, $lang->producttarget->target);?></th>
          <th class='w-150px'><?php common::printOrderLink('middle', $orderBy, $vars, $lang->producttarget->middle);?></th>
          <th class='w-150px'><?php common::printOrderLink('performance', $orderBy, $vars, $lang->producttarget->performance);?></th>
          <th class='w-150px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->producttarget->createdDate);?></th>
          <th class='c-actions-2'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($targets as $producttarget):?>
        <tr>
          <td class="c-id"><?php echo $producttarget->id?></td>
          <td title="<?php echo $producttarget->name;?>"><?php echo html::a($this->createLink('producttarget', 'view', "producttargetID=$producttarget->id"), $producttarget->name);?></td>
          <td><?php echo zget($lang->producttarget->confidenceList, $producttarget->confidence)?></td>
          <td><?php echo $producttarget->target . '%'?></td>
          <td><?php echo $producttarget->middle . '%'?></td>
          <td><?php echo $producttarget->performance . '%'?></td>
          <td><?php echo $producttarget->createdDate . '%'?></td>
          <td class='c-actions'>
            <?php
              if(common::hasPriv('producttarget', 'edit')) common::printIcon('producttarget', 'edit', "producttargetID=$producttarget->id&productID=$productID", $producttarget, 'list');
            ?>
            <?php
              if(common::hasPriv('producttarget', 'delete')) common::printIcon('producttarget', 'delete', "producttargetID=$producttarget->id&productID=$productID", $producttarget, 'list', 'trash', 'hiddenwin');
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
