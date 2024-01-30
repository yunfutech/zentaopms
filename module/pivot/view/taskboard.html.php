<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <form method='post'>
        <?php $canBatchEdit = common::hasPriv('task', 'batchEdit'); ?>
        <div class="row" id='conditions'>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->povit->dept; ?></span>
              <?php echo html::select('dept', $depts, $dept, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->povit->PP; ?></span>
              <?php echo html::select('pp', $pps, $pp, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->povit->project; ?></span>
              <?php echo html::select('project', $projects, $project, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'>日期选择</span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('date', $date, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
          <div class='col-sm-3'>
            <?php echo html::a($this->createLink('pivot', 'taskboard', "date={$prev_day}&dept={$dept}&project={$project}"), '上一天', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('pivot', 'taskboard', "date={$toady}&dept={$dept}&project={$project}"), '今天', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('pivot', 'taskboard', "date={$next_day}&dept={$dept}&project={$project}"), '下一天', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('pivot', 'export', "&dept={$dept}&project={$project}"), "<i class='icon icon-export muted'> </i>导出", '', "class='btn btn-primary downtask-btn'"); ?>
          </div>
        </div>
      </form>
    </div>
    <?php if (empty($user2detail)) : ?>
      <div class="cell">
        <div class="table-empty-tip">
          <p><span class="text-muted"><?php echo $lang->error->noData; ?></span></p>
        </div>
      </div>
    <?php else : ?>
      <form id='myTaskForm' class="table-task" data-ride="table" method="post">
        <div class='cell'>
          <div class='panel'>
            <div class="panel-heading">
              <div class="panel-title"><?php echo $title; ?></div>
              <nav class="panel-actions btn-toolbar"></nav>
            </div>
            <div data-ride='table' class='bigbox'>
              <?php if (count($short) > 0) : ?>
                <div class='red workload2'>
                  <span>任务不饱和：</span>
                  <?php foreach ($short as $user => $all) : ?>
                    <span class='user'><?php echo $users[$user]; ?>(<?php echo $all; ?>)</span>
                  <?php endforeach ?>
                </div>
              <?php endif ?>
              <?php if (count($exceed) > 0) : ?>
                <div class='red workload2'>
                  <span>任务超负荷：</span>
                  <?php foreach ($exceed as $user => $all) : ?>
                    <span class='user'><?php echo $users[$user]; ?>(<?php echo $all; ?>)</span>
                  <?php endforeach ?>
                </div>
              <?php endif ?>
              <table class='table table-bordered table-fixed no-margin' id="workload">
                <thead>
                  <tr class='colhead text-center'>
                    <th class="w-60px"><?php echo $lang->report->user; ?></th>
                    <th class="w-120px">完成度<a href="javascript:;" title="实际消耗/预计消耗/全部任务"><i class="icon-question-sign"></i></a></th>
                    <th>详情</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($user2detail as $account => $detail) : ?>
                    <?php foreach ($detail['tasks'] as $index => $task) : ?>
                      <tr class="text-left">
                        <?php if ($index == 0) : ?>
                          <td class='td-line' rowspan="<?php echo count($detail['tasks']); ?>"><?php echo $users[$account]; ?></td>
                          <td class='td-line' style='position:relative' rowspan="<?php echo count($detail['tasks']); ?>">
                            <div class='content'><?php echo $detail['consumed']; ?>/<?php echo $detail['complete']; ?>/<?php echo $detail['all']; ?></div>
                            <div style='width:<?php echo intval($detail['complete'] / $detail['all'] * 100) ?>%' class='fg <?php if ($detail['all'] > 10) : echo 'fgred';
                                                                                                                            elseif ($detail['all'] > 8) : echo 'fgorange';
                                                                                                                            elseif ($detail['all'] == 8) : echo 'fggreen';
                                                                                                                            elseif ($detail['all'] < 8) : echo 'fgblue'; ?><?php endif; ?>'></div>
                            <div class='bg <?php if ($detail['all'] > 10) : echo 'bgred';
                                            elseif ($detail['all'] > 8) : echo 'bgorange';
                                            elseif ($detail['all'] == 8) : echo 'bggreen';
                                            elseif ($detail['all'] < 8) : echo 'bgblue'; ?><?php endif; ?>'></div>
                          </td>
                        <?php endif; ?>
                        <td class=' <?php if ($index == count($detail['tasks']) - 1) : echo 'td-line'; ?><?php endif; ?>'>
                          <div class='task-detail'>
                            <span class='overview'><?php echo $task->consumed; ?> / <?php echo $task->estimate; ?></span>
                            <?php echo html::a($this->createLink('execution', 'task', "executionID={$task->execution}"), "<span class='project-name'><span  class='pri pri_{$task->pri}'>{$task->pri}</span>{$task->executionName}</span>"); ?>
                            <?php if ($task->moduleId) : ?>
                              <?php echo html::a($this->createLink('execution', 'task', "executionID=$task->execution&status=unclosed&param=$task->moduleId"), "<span class='module-name'>{$task->moduleName}</span>") ?>
                            <?php else : ?>
                              <span class='no-module-name'>&nbsp;</span>
                            <?php endif; ?>
                            <?php if ($task->storyID) : ?>
                              <?php echo html::a($this->createLink('story', 'view', "storyID=$task->storyID"), "<span class='story-name'>{$task->storyTitle}</span>") ?>
                            <?php else : ?>
                              <span class='no-story-name'>&nbsp;</span>
                            <?php endif; ?>
                            <span class='taskstatus status-<?php echo $task->status; ?>'><?php echo zget($lang->task->statusList, $task->status) ?></span>
                            <?php if ($canBatchEdit) : ?>
                              <div class="checkbox-primary checkbox">
                                <input type='checkbox' name='taskIDList[]' id='taskIDList-<?php echo $task->id; ?>' value='<?php echo $task->id; ?>' />
                                <label for='taskIDList-<?php echo $task->id; ?>'></label>
                              </div>
                            <?php endif; ?>
                            <?php echo html::a(
                              $this->createLink('task', 'view', "taskID={$task->id}"),
                              "<span class='task-name'>&nbsp;<span class='pri pri_{$task->taskpri}'>{$task->taskpri}&nbsp;</span>&nbsp;{$task->name}</span>"
                            ); ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <?php if ($canBatchEdit) : ?>
                <div class="table-footer fixed-footer">
                  <div class="table-actions btn-toolbar">
                    <?php
                    if ($canBatchEdit) {
                      $actionLink = $this->createLink('task', 'batchEdit', "projectID=0");
                      echo html::commonButton($lang->edit, "onclick=\"setFormAction('$actionLink')\"");
                    }
                    ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>