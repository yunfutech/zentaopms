<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
  <div class='cell'>
      <form method='post'>
      <?php $canBatchEdit = common::hasPriv('task', 'batchEdit');?>
        <div class="row" id='conditions'>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->dept; ?></span>
              <?php echo html::select('dept', $depts, $dept, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'>日期选择</span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('date', $date, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php if (empty($tasks)): ?>
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
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
            <thead>
              <tr class='colhead text-center'>
                <th class="w-300px">姓名</th>
                <th class="w-100px">总计</th>
                <th class="">迭代</th>
                <th class="w-100px">工时</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $user => $usertasks): ?>
                <?php $i = 0; ?>
                <?php foreach ($usertasks['tasks'] as $index => $list): ?>
                <tr class="text-left">
                    <?php if ($i == 0): ?>
                    <td class='text-center' rowspan="<?php echo count($usertasks['tasks']); ?>"><?php echo $users[$user]; ?></td>
                    <td class='text-center' rowspan="<?php echo count($usertasks['tasks']); ?>"><?php echo $usertasks['consumed']; ?></td>
                    <?php endif;?>
                    <td class='text-center'>
                    <?php echo $index; ?>
                    </td>
                    <td class='text-center'>
                    <?php echo $list; ?>
                    </td>
                </tr>
                <?php $i++; ?>
                <?php endforeach;?>
            <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
