<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class='cell'>
      <form method='post'>
        <?php $canBatchEdit = common::hasPriv('task', 'batchEdit'); ?>
        <div class="row" id='conditions'>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->project->project_type; ?></span>
              <?php echo html::select('project_type', $lang->project->project_typeList, $project_type, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-3'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'><?php echo $lang->report->beginAndEnd; ?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
              <span class='input-group-addon fix-border'><?php echo $lang->report->to; ?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
          <div class='col-sm-3'>
            <?php echo html::a($this->createLink('report', 'projectboard', "begin={$pre['start']}&end={$pre['end']}"), '上一周', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('report', 'projectboard', "begin={$cur['start']}&end={$cur['end']}"), '本周', '', "class='btn btn-primary next'"); ?>
            <?php echo html::a($this->createLink('report', 'projectboard', "begin={$next['start']}&end={$next['end']}"), '下一周', '', "class='btn btn-primary next'"); ?>
          </div>
        </div>
      </form>
    </div>
    <?php if (empty($projects)) : ?>
      <div class="cell">
        <div class="table-empty-tip">
          <p><span class="text-muted"><?php echo $lang->error->noData; ?></span></p>
        </div>
      </div>
    <?php else : ?>
      <div class='cell'>
        <div class='panel'>
          <div class="panel-heading">
            <div class="panel-title"><?php echo $title; ?></div>
            <nav class="panel-actions btn-toolbar"></nav>
          </div>
          <div data-ride='table' style="width: 100%; overflow-x: scroll">
            <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
              <thead>
                <tr class='colhead text-center'>
                  <th class="w-250px">迭代</th>
                  <th class="w-50px">优先级</th>
                  <th class="w-180px">关联项目</th>
                  <th class="w-100px">需求数</th>
                  <th class="w-100px">预计工时</th>
                  <th class="w-100px">需求完成数</th>
                  <th class="w-100px">已完成需求工时</th>
                  <th class="w-100px">项目进度</th>
                  <th class="w-100px">实际工时</th>
                  <th class="w-100px">实际工时/预计工时</th>
                  <th class="w-100px">人员</th>
                  <th class="w-100px">消耗工时</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($projects as $project) : ?>
                  <tr class="text-left">
                    <td class='text-left' rowspan="<?php echo $project->usersCount;?>">
                      <div class='task-detail'>
                        <?php echo html::a($this->createLink('project', 'task', "projectID={$project->id}"), "<span class=''>{$project->name}</span>"); ?>
                      </div>
                    </td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>">
                      <span class='pri pri_<?php echo $project->pri; ?>'><?php echo $project->pri; ?></span>
                    </td>
                    <td class='text-left' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->products; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->allStoiresCount; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->manHour; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->allDoneStoiresCount; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->doneStoriesEstimate; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->schedule* 100 . '%'; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->doneManHour; ?></td>
                    <td class='text-center' rowspan="<?php echo $project->usersCount; ?>"><?php echo $project->accuracy * 100 . '%'; ?></td>
                    <?php if (empty($project->users)): ?>
                      <td class='text-center'></td>
                      <td class='text-center'></td>
                    <?php else: ?>
                      <td class='text-center'><?php echo zget($users, array_keys($project->users)[0]); ?></td>
                      <td class='text-center'><?php echo array_values($project->users)[0]; ?></td>
                    <?php endif; ?>
                  </tr>
                  <?php if (count($project->users) > 1): ?>
                    <?php foreach (array_slice($project->users, 1) as $user => $consumed) : ?>
                      <tr class="text-left">
                        <td class='text-center'><?php echo zget($users, $user); ?></td>
                        <td class='text-center'><?php echo $consumed; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>