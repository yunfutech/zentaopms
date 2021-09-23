<?php include '../../common/view/header.html.php'; ?>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <div class="row" id='conditions'>
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
            <span class='input-group-addon'><?php echo $lang->report->end; ?></span>
            <div class='datepicker-wrapper datepicker-date'>
              <?php echo html::input('end', $end, "class='form-control form-date' style='padding-right:10px' onchange='changeParams(this)'"); ?>
            </div>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->productLine; ?></span>
            <?php echo html::select('line', $lines, '', 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->product; ?></span>
            <?php echo html::select('productID', $products, '', 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <?php echo html::a($this->createLink('report', 'projectboard', "begin={$pre['start']}&end={$pre['end']}"), '上月', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'projectboard', "begin={$cur['start']}&end={$cur['end']}"), '本月', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'projectboard', "begin={$next['start']}&end={$next['end']}"), '下月', '', "class='btn btn-primary next'"); ?>
        </div>
      </div>
    </div>

    <div class="cell">
      <?php if(empty($data)):?>
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->producttarget->noData;?></span></p>
      </div>
      <?php else:?>
      <form>
        <table class='table has-sort-head table-hover table-bordered table-striped table-fixed table-condensed'>
          <?php
            $begin = implode('', explode('-', $begin));
            $vars = "orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&productID=$productID&line=$line&begin=$begin&end=$end";
          ?>
          <thead>
            <tr>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('productLine', $orderBy, $vars, $lang->producttarget->productLine);?></th>
              <th rowspan="2" class='w-150px'><?php common::printOrderLink('productName', $orderBy, $vars, $lang->producttarget->productName);?></th>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('director', $orderBy, $vars, $lang->product->director);?></th>
              <th rowspan="2" class='w-80px'><?php common::printOrderLink('manHour', $orderBy, $vars, $lang->report->boardProduct->manHour);?></th>
              <th rowspan="2" class='w-80px'><?php common::printOrderLink('doneManHour', $orderBy, $vars, $lang->report->boardProduct->doneManHour);?></th>
              <th rowspan="2" class='w-120px'><?php common::printOrderLink('accuracy', $orderBy, $vars, $lang->report->boardProduct->accuracy);?></th>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('lastTarget', $orderBy, $vars, $lang->producttarget->lastTarget);?></th>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('target', $orderBy, $vars, $lang->producttarget->target );?></th>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('performance', $orderBy, $vars, $lang->producttarget->performance);?></th>
              <th rowspan="2" class='w-100px'><?php common::printOrderLink('deviation', $orderBy, $vars, $lang->producttarget->deviation );?></th>
              <th colspan="4" class='w-400px'><?php echo $lang->producttargetitem->common;?></th>
            </tr>
            <tr>
              <th class='w-150px'><?php echo $lang->producttargetitem->name;?></th>
              <th class='w-300px'><?php echo $lang->producttargetitem->intro;?></th>
              <th class='w-300px'><?php echo $lang->producttargetitem->acceptance;?></th>
              <th class='w-300px'><?php echo $lang->producttargetitem->completion;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $line => $products):?>
            <tr>
              <td rowspan="<?php echo $rowspanArr['line' . $line];?>"><?php echo $line?></td>
              <?php $names = array_keys($products);?>
              <?php foreach ($products as $name => $target):?>
                <?php
                  $targetRowsapn = $rowspanArr['product' . $name];;
                ?>
                <?php if ($name != $names[0]):?>
                  <tr>
                <?php endif;?>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $name?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->director?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->manHour?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->doneManHour?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->accuracy?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->lastTarget?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->target?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->performance?></td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->deviation?></td>
                <?php if (empty($target->items)):?>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  </tr>
                <?php else:?>
                  <?php foreach ($target->items as $key => $item):?>
                  <?php if($key !== 0):?>
                  <tr>
                  <?php endif;?>
                    <td><?php echo $item->name?></td>
                    <td><?php echo $item->intro?></td>
                    <td><?php echo $item->name?></td>
                    <td><?php echo $item->name?></td>
                  </tr>
                  <?php endforeach;?>
                <?php endif;?>
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
