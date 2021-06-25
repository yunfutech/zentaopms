<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <form method='post'>
        <?php $canBatchEdit = common::hasPriv('task', 'batchEdit'); ?>
        <div class="row" id='conditions'>
          <div class='col-sm-3'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'><?php echo $lang->report->beginAndEnd; ?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
              <span class='input-group-addon fix-border'><?php echo $lang->report->to; ?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
          <div class='col-sm-3'>
            <?php echo html::a($this->createLink('report', 'productboard', "begin={$pre['start']}&end={$pre['end']}"), '上一周', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('report', 'productboard', "begin={$cur['start']}&end={$cur['end']}"), '本周', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('report', 'productboard', "begin={$next['start']}&end={$next['end']}"), '下一周', '', "class='btn btn-primary next'"); ?>
          </div>
        </div>
      </form>
    </div>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
            <div class="table-row">
              <div class="col-sm-1 text-middle"><?php echo $title; ?></div>
              <div class="col-sm-1 text-middle text-right text-gray"><?php echo $lang->product->status . '：' ?></div>
              <div class='col-sm-2' id="status">
                <div class="radio inline-block">
                  <label for="<?php echo '已关闭'; ?>">
                    <input type="radio" name="status" value="<?php echo 'noclosed'; ?>" id="<?php echo 'noclosed'; ?>" <?php if ($status == 'noclosed') echo "checked='checked'" ?> />
                    <?php echo '未关闭' ?>
                  </label>
                </div>
                <div class="radio inline-block">
                  <label for="<?php echo '已关闭'; ?>">
                    <input type="radio" name="status" value="<?php echo 'closed'; ?>" id="<?php echo 'closed'; ?>" <?php if ($status == 'closed') echo "checked='checked'" ?> />
                    <?php echo '已关闭' ?>
                  </label>
                </div>
              </div>
              <div class="col-sm-1 text-middle text-right text-gray"><?php echo $lang->product->line . '：' ?></div>
              <div class='col-xs-12 text-middle' id='selectLines'>
                <?php foreach ($lines as $line) : ?>
                  <div class="checkbox-primary inline-block">
                    <input type="checkbox" value="<?php echo $line; ?>" id="<?php echo $line; ?>" <?php if (strpos($selectLines, $line) !== false) echo "checked='checked'" ?> />
                    <label for="<?php echo $line; ?>"><?php echo $line ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
      </div>
      <?php if (empty($products)) : ?>
        <div class="cell">
          <div class="table-empty-tip">
            <p><span class="text-muted"><?php echo $lang->error->noData; ?></span></p>
          </div>
        </div>
      <?php else : ?>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
            <thead>
              <tr class='colhead text-center'>
                <th class="w-80px"><?php echo $lang->report->boardProduct->productLine ?></th>
                <th class="w-200px"><?php echo $lang->report->boardProduct->productName ?></th>
                <th class="w-100px"><?php echo $lang->product->director ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->productPO ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->allStoiresCount ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->manHour ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->doneStoriesCount ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->doneStoriesEstimate ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->schedule ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->doneManHour ?></th>
                <th class="w-100px"><?php echo $lang->report->boardProduct->accuracy; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product) : ?>
                <tr class="text-left">
                  <td class='text-center'><?php echo $product->line; ?></td>
                  <td class='text-center'>
                    <?php echo html::a($this->createLink('product', 'view', "productID={$product->id}"), "$product->name"); ?>
                  </td>
                  <td class='text-center'><?php echo zget($users, $product->director, ''); ?></td>
                  <td class='text-center'><?php echo zget($users, $product->PO, ''); ?></td>
                  <td class='text-center'><?php echo $product->allStoiresCount; ?></td>
                  <td class='text-center'><?php echo round($product->manHour, 2); ?></td>
                  <td class='text-center'><?php echo $product->doneStoriesCount; ?></td>
                  <td class='text-center'><?php echo round($product->doneStoriesEstimate, 2); ?></td>
                  <td class='text-center'><?php echo $product->schedule > 0 ? strval($product->schedule * 100) . '%' : '0%'; ?></td>
                  <td class='text-center'><?php echo round($product->doneManHour, 2); ?></td>
                  <td class='text-center'><?php echo $product->accuracy > 0 ? strval($product->accuracy * 100) . '%' : '0%'; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
    </div>
  <?php endif; ?>
  </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>