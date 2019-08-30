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
          <div class='col-sm-2'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'>日期选择</span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('date', $date, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
          <div class='col-sm-3'>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$prev_day}&dept={$dept}"), 上一天, '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$toady}&dept={$dept}"), 今天, '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$next_day}&dept={$dept}"), 下一天, '', "class='btn btn-primary next'"); ?>
          </div>
        </div>
      </form>
    </div>
    <?php if (empty($workload)): ?>
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
                <th class="w-150px">概览</th>
                <th><?php echo $lang->report->task; ?></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($workload as $account => $load): ?>
                <tr class="text-left">
                    <td><?php echo $users[$account]; ?></td>
                    <td style='position:relative'>
                        <div class='content'>任务：<?php echo $load['complete']; ?>/<?php echo $load['all']; ?></div>
                        <div style='width:<?php echo intval($load['complete'] / $load['all'] * 100) ?>%' class='fg <?php if ($load['all'] > 10): echo 'fgred';elseif ($load['all'] > 8): echo 'fgorange';elseif ($load['all'] == 8): echo 'fggreen';elseif ($load['all'] < 8): echo 'fgblue';?><?php endif;?>'></div>
                        <div class='bg <?php if ($load['all'] > 10): echo 'bgred';elseif ($load['all'] > 8): echo 'bgorange';elseif ($load['all'] == 8): echo 'bggreen';elseif ($load['all'] < 8): echo 'bgblue';?><?php endif;?>'></div>
                    </td>
                    <td>
                        <?php foreach ($load['detail'] as $list): ?>
                            <div class='task-detail'>
                                <span class='overview'><?php echo $list->consumed; ?></span> /
                                <span class='overview'><?php echo $list->estimate; ?></span>
                                <?php echo html::a($this->createLink('project', 'task', "projectID={$list->project}"), "<span class='project-name'><span  class='pri pri_{$list->pri}'>{$list->pri}</span>{$list->projectName}</span>"); ?>
                                <?php echo html::a($this->createLink('task', 'view', "taskID={$list->id}"), "<span class='task-name'><span class='pri pri_{$list->taskpri}'>{$list->taskpri}</span>{$list->name}</span>"); ?>
                            </div>
                        <?php endforeach;?>
                    </td>
                </tr>
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
