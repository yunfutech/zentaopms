<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
  <div class='cell'>
      <form method='post'>
        <div class="row" id='conditions'>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->dept; ?></span>
              <?php echo html::select('dept', $depts, $dept, "class='form-control chosen' onchange='changeParams(this)'"); ?>
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
                <th class="w-100px" ><?php echo $lang->report->user; ?></th>
                <th>项目名</th>
                <th>任务名</th>
                <th class="w-100px">任务状态</th>
                <th class="w-100px">预计时间</th>
                <th class="w-100px">截止日期</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $account => $load): ?>
                <?php foreach ($load['detail'] as $index => $list): ?>
                    <tr class="text-left">
                        <?php if ($index == 0): ?>

                        <td rowspan="<?php echo count($load['detail']); ?>"><?php echo $users[$account]; ?></td>
                        <?php endif;?>
                        <td>
                                <div class='task-detail'>
                                    <?php echo html::a($this->createLink('project', 'task', "projectID={$list->project}"), "<span class=''><span  class='pri pri_{$list->pri}'>{$list->pri}</span>{$list->projectName}</span>"); ?>

                                </div>
                        </td>
                        <td><?php echo html::a($this->createLink('task', 'view', "taskID={$list->id}"), "<span class=''><span class='pri pri_{$list->taskpri}'>{$list->taskpri}</span>{$list->name}</span>"); ?></td>
                        <td class='text-center status-<?php echo $list->status; ?>'><?php echo zget($lang->task->statusList, $list->status) ?></td>
                        <td class='text-center'><?php echo $list->estimate; ?></td>
                        <td class='text-center'><?php echo $list->deadline; ?></td>
                    </tr>
                <?php endforeach;?>
                <?php if (count($load['detail']) == 0): ?>
                <tr class="text-left">
                        <td><?php echo $users[$account]; ?></td>
                        <td>无</td>
                        <td>无</td>
                        <td class='text-center'>无</td>
                        <td class='text-center'>无</td>
                        <td class='text-center'>无</td>
                    </tr>
                <?php endif;?>
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
