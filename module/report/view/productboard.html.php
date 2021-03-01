<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <?php if (empty($products)): ?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData; ?></span></p>
      </div>
    </div>
    <?php else: ?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title"><?php echo $title; ?></div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
      </div>
      <div data-ride='table'>
        <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
          <thead>
            <tr class='colhead text-center'>
              <th class="w-300px"><?php echo $lang->report->boardProduct->productName ?></th>
              <th class="w-200px"><?php echo $lang->report->boardProduct->allStoiresCount ?></th>
              <th class="w-200px"><?php echo $lang->report->boardProduct->manHour ?></th>
              <th class="w-200px"><?php echo $lang->report->boardProduct->doneStoriesCount ?></th>
              <th class="w-200px"><?php echo $lang->report->boardProduct->doneManHour ?></th>
              <th class="w-200px"><?php echo $lang->report->boardProduct->schedule ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr class="text-left">
                  <td class='text-center'><?php echo $product->name; ?></td>
                  <td class='text-center'><?php echo $product->allStoiresCount; ?></td>
                  <td class='text-center'><?php echo $product->manHour > 0 ? $product->manHour: 0; ?></td>
                  <td class='text-center'><?php echo $product->doneStoriesCount; ?></td>
                  <td class='text-center'><?php echo $product->doneManHour > 0 ? $product->doneManHour : 0; ?></td>
                  <td class='text-center'><?php echo $product->manHour > 0 ? round(floatval($product->doneManHour) / floatval($product->manHour), 2) : 0; ?></td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
