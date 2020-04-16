<?php include '../../common/view/header.html.php';?>
<div id="mainContent">
  <div class='cell'>
    <div class="row" id='conditions'>
      <div class='col-sm-2'>
        <div class='input-group'>
          <span class='input-group-addon'><?php echo $lang->report->week; ?></span>
          <?php echo html::select('week', $weeks, $week - 1, "class='form-control chosen' onchange='changeParams()'"); ?>
        </div>
      </div>
    </div>
  </div>
  <?php if(empty($weeklies)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->report->noWeekly;?></span></p>
  </div>
  <?php else:?>
  <form class="main-table table-task">
    <table class="table has-sort-head table-fixed">
      <?php $vars = "orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
      <thead>
        <tr>
          <th class="c-id">
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th class='c-name'><?php common::printOrderLink('name', $orderBy, $vars, $lang->report->weeklyName);?></th>
          <th class='c-date w-150px'><?php common::printOrderLink('realname', $orderBy, $vars, $lang->report->account);?></th>
          <th class='c-date w-150px'><?php common::printOrderLink('date', $orderBy, $vars, $lang->report->createdDate);?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($weeklies as $weekly):?>
        <tr>
          <td class="c-id">
            <?php printf('%03d', $weekly->id);?>
          </td>
          <td class='c-name' title="<?php echo $weekly->name;?>"><?php echo html::a($this->createLink('productweekly', 'view', "weeklyID=$weekly->id&productID=$weekly->product"), $weekly->name);?></td>
          <td class='c-date'><?php echo $weekly->realname?></td>
          <td class='c-date'><?php echo $weekly->date?></td>
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
