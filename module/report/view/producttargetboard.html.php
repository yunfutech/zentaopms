<?php include '../../common/view/header.html.php'; ?>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <div class="row" id='conditions'>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->month; ?></span>
            <div class='datepicker-wrapper datepicker-date'>
              <?php echo html::input('month', $month, "class='form-control form-date' style='padding-right:10px' onchange='changeParams(this)'"); ?>
            </div>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->productLine; ?></span>
            <?php echo html::select('line', $lines, $line, 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->productDirector; ?></span>
            <?php echo html::select('director', $directors, $director, 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->report->product; ?></span>
            <?php echo html::select('productID', $products, $productID, 'onchange=changeParams(this) class="form-control chosen"');?>
          </div>
        </div>
        <div class='col-sm-3'>
          <?php echo html::a($this->createLink('report', 'producttargetboard', "productID=$productID&line=$line&month=$preMonth&director=$director"), '上月', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'producttargetboard', "productID=$productID&line=$line&month=$thisMonth&director=$director"), '本月', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'producttargetboard', "productID=$productID&line=$line&month=$nextMonth&director=$director"), '下月', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'exportTarget', "productID=$productID&line=$line&month=$month&director=$director"), "<i class='icon icon-export muted'> </i>导出", '', "class='btn btn-primary download-btn'"); ?>
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
        <table class='table table-hover table-bordered table-striped table-condensed'>
          <thead>
            <tr>
              <th rowspan="2" class='w-100px text-center'><?php common::printOrderLink('productLine', $orderBy, $vars, $lang->producttarget->productLine);?></th>
              <th rowspan="2" class='w-160px text-center'><?php common::printOrderLink('productName', $orderBy, $vars, $lang->producttarget->productName);?></th>
              <th rowspan="2" class='w-50px text-center'><?php common::printOrderLink('productPri', $orderBy, $vars, $lang->producttarget->productPri);?></th>
              <th rowspan="2" class='w-70px text-center'><?php common::printOrderLink('director', $orderBy, $vars, $lang->product->director);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('manHour', $orderBy, $vars, $lang->report->boardProduct->manHour);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('doneManHour', $orderBy, $vars, $lang->report->boardProduct->doneManHour);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('accuracy', $orderBy, $vars, $lang->report->boardProduct->accuracy);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('lastTarget', $orderBy, $vars, $lang->producttarget->lastTarget);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('target', $orderBy, $vars, $lang->producttarget->target );?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('performance', $orderBy, $vars, $lang->producttarget->performance);?></th>
              <th rowspan="2" class='w-60px text-center'><?php common::printOrderLink('deviation', $orderBy, $vars, $lang->producttarget->deviation );?></th>
              <th colspan="4" class="text-center"><?php echo $lang->producttargetitem->common;?></th>
            </tr>
            <tr>
              <th class='w-100px text-center'><?php echo $lang->producttargetitem->name;?></th>
              <th class='w-150px text-center'><?php echo $lang->producttargetitem->intro;?></th>
              <th class='w-150px text-center'><?php echo $lang->producttargetitem->acceptance;?></th>
              <th class='w-150px text-center'><?php echo $lang->producttargetitem->completion;?></th>
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
                <td rowspan="<?php echo $targetRowsapn;?>">
                  <?php echo html::a($this->createLink('product', 'view', "productID={$target->productID}"), "$name"); ?>
                </td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>">
                  <span class='pri pri_<?php echo $target->productPri?>'><?php echo $target->productPri?></span>
                </td>
                <td rowspan="<?php echo $targetRowsapn;?>"><?php echo zget($users, $target->director)?></td>
                <?php
                  $manHour = $id2hour[$target->productID]['manHour'];
                  $doneManHour = $id2hour[$target->productID]['doneManHour'];
                  $accuracy = $id2hour[$target->productID]['accuracy'];
                ?>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $manHour ? $manHour: 0?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $doneManHour ? $doneManHour: 0?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $accuracy ? $accuracy: 0?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->lastTarget . '%'?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->target . '%'?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo $target->performance . '%'?></td>
                <td class='text-center' rowspan="<?php echo $targetRowsapn;?>"><?php echo ($target->performance - $target->target) . '%'?></td>
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
                    <td><?php echo $item->acceptance?></td>
                    <td><?php echo $item->completion?></td>
                  </tr>
                  <?php endforeach;?>
                <?php endif;?>
              <?php endforeach;?>
            <?php endforeach;?>
          </tbody>
        </table>
      </form>
      <?php endif;?>
    </div>
  </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
