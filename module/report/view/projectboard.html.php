<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
  <div class='cell'>
      <form method='post'>
      <?php $canBatchEdit = common::hasPriv('task', 'batchEdit');?>
        <div class="row" id='conditions' class="<?php echo $date; ?>">
          <div class='col-sm-2'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'>日期选择</span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('date', $date, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php if (empty($projects)): ?>
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
                <th class="w-300px">迭代</th>
                <th class="w-50px">优先级</th>
                <th class="">消耗总工时</th>
                <th class="w-100px">人员</th>
                <th class="w-100px">消耗工时</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project): ?>
                <?php $i = 0; ?>
                <?php foreach ($project['tasks'] as $index => $list): ?>
                <tr class="text-left">
                    <?php if ($i == 0): ?>
                    <td class='text-center' rowspan="<?php echo count($project['tasks']); ?>">
                        <div class='task-detail'>
                            <?php echo html::a($this->createLink('project', 'task', "projectID={$project['id']}"), "<span class=''>{$project['name']}</span>"); ?>
                        </div>
                    </td>
                    <td  class='text-center'>
                    <span class='pri pri_{$project["pri"]}'><?php echo $project['pri']; ?></span>
                    </td>
                    <td class='text-center' rowspan="<?php echo count($project['tasks']); ?>"><?php echo $project['consumed']; ?></td>
                    <?php endif;?>
                    <td class='text-center'><?php echo $users[$index]; ?></td>
                    <td class='text-center'><?php echo $list; ?></td>
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
