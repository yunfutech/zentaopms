<?php include '../../common/view/header.html.php'; ?>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <div class="row" id='conditions'>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->productLine; ?></span>
            <?php echo html::select('line', $lines, $line, 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->product; ?></span>
            <?php echo html::select('productID', $products, $productID, 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->begin; ?></span>
            <div class='datepicker-wrapper datepicker-date'>
              <?php echo html::input('begin', $begin, "class='form-control form-date' style='padding-right:10px' onchange='changeParams(this)'"); ?>
            </div>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->isContract; ?></span>
            <div class='datepicker-wrapper datepicker-date'>
              <?php echo html::select('isContract', array('' => '') + $this->lang->milestone->isContractList, $isContract, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?>
            </div>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->completed; ?></span>
            <div class='datepicker-wrapper datepicker-date'>
              <?php echo html::select('completed', array('' => '') + $this->lang->milestone->completedList, $completed, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="cell">
      <?php if(empty($data)):?>
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->noMilestones;?></span></p>
      </div>
      <?php else:?>
      <form class="main-table table-task">
        <table class='table has-sort-head table-hover table-bordered table-striped'>
          <?php
            $begin = implode('', explode('-', $begin));
            $vars = "orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&productID=$productID&line=$line&begin=$begin&isContract=$isContract&completed=$completed";
          ?>
          <thead>
            <tr>
              <th class='w-150px'><?php common::printOrderLink('productLine', $orderBy, $vars, $lang->report->productLine);?></th>
              <th class='w-200px'><?php common::printOrderLink('productName', $orderBy, $vars, $lang->report->product);?></th>
              <th class='w-450px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->milestone->name);?></th>
              <th class='w-150px'><?php common::printOrderLink('date', $orderBy, $vars, $lang->milestone->date);?></th>
              <th class='w-150px'><?php common::printOrderLink('isContract', $orderBy, $vars, $lang->milestone->isContract);?></th>
              <th class='w-150px'><?php common::printOrderLink('pri', $orderBy, $vars, $lang->milestone->pri);?></th>
              <th class='w-150px'><?php common::printOrderLink('completed', $orderBy, $vars, $lang->milestone->completed);?></th>
              <th><?php common::printOrderLink('comment', $orderBy, $vars, $lang->milestone->comment);?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($data as $line => $products):?>
            <tr>
              <td rowspan="<?php echo $rowspanArr['line' . $line];?>"><?php echo $line?></td>
              <?php $names = array_keys($products);?>
              <?php foreach ($products as $name => $milestones):?>
                <?php
                  $productRowsapn = $rowspanArr['product' . $name];;
                ?>
                <?php if ($name != $names[0]):?>
                <tr>
                <?php endif;?>
                <td rowspan="<?php echo $productRowsapn;?>"><?php echo $name?></td>
                <?php foreach ($milestones as $key => $milestone):?>
                  <?php if ($key != 0):?>
                  <tr>
                  <?php endif;?>
                  <td><?php echo $milestone->name?></td>
                  <td><?php echo $milestone->date?></td>
                  <td><?php echo $milestone->isContract ? '是' : '否'?></td>
                  <td><?php echo $milestone->pri?></td>
                  <td><?php echo $milestone->completed ? '是' : '否'?></td>
                  <td><?php echo $milestone->comment?></td>
                </tr>
                <?php endforeach;?>
              <?php endforeach;?>
            <?php endforeach;?>
          </tbody>
        </table>
        <div class="table-footer">
          <?php $pager->show('right', 'pagerjs');?>
        </div>
      </form>
      <?php endif;?>
    </div>
  </div>
</div>

<?php echo html::hidden('orderBy', $orderBy);?>
<?php echo html::hidden('recTotal', $recTotal);?>
<?php echo html::hidden('recPerPage', $recPerPage);?>
<?php echo html::hidden('pageID', $pageID);?>

<?php include '../../common/view/footer.html.php'; ?>
