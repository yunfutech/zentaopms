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
          <div data-ride='table'>
            <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id="workload">
              <thead>
                <tr class='colhead text-center'>
                  <th class="w-400px">迭代</th>
                  <th class="w-100px">优先级</th>
                  <th class="">消耗总工时</th>
                  <th class="w-300px">人员</th>
                  <th class="w-200px">消耗工时</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($projects as $project) : ?>
                  <?php $i = 0; ?>
                  <?php foreach ($project['tasks'] as $index => $list) : ?>
                    <tr class="text-left">
                      <?php if ($i == 0) : ?>
                        <td class='text-center' rowspan="<?php echo count($project['tasks']); ?>">
                          <div class='task-detail'>
                            <?php echo html::a($this->createLink('project', 'task', "projectID={$project['id']}"), "<span class=''>{$project['name']}</span>"); ?>
                          </div>
                        </td>
                        <td class='text-center' rowspan="<?php echo count($project['tasks']); ?>">
                          <span class='pri pri_<?php echo $project["pri"]; ?>'><?php echo $project['pri']; ?></span>
                        </td>
                        <td class='text-center' rowspan="<?php echo count($project['tasks']); ?>"><?php echo $project['consumed']; ?></td>
                      <?php endif; ?>
                      <td class='text-center'>
                        <?php if (array_key_exists($index, $users)) : ?>
                          <?php echo $users[$index]; ?>
                        <?php else : ?>
                          <?php echo $index; ?>
                        <?php endif; ?>
                      </td>
                      <td class='text-center'><?php echo $list; ?></td>
                    </tr>
                    <?php $i++; ?>
                  <?php endforeach; ?>
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