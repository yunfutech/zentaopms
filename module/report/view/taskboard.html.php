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
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->productDirector; ?></span>
              <?php echo html::select('director', $directors, $director, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->report->product; ?></span>
              <?php echo html::select('product', $products, $product, "class='form-control chosen' onchange='changeParams(this)'"); ?>
            </div>
          </div>
          <div class='col-sm-2'>
            <div class='input-group input-group-sm'>
              <span class='input-group-addon'>日期选择</span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('date', $date, "class='form-control' style='padding-right:10px' onchange='changeParams(this)'"); ?></div>
            </div>
          </div>
          <div class='col-sm-3'>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$prev_day}&dept={$dept}"), '上一天', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$toady}&dept={$dept}"), '今天', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'taskboard', "date={$next_day}&dept={$dept}"), '下一天', '', "class='btn btn-primary next'"); ?>
          <?php echo html::a($this->createLink('report', 'export', "&dept={$dept}"), "<i class='icon icon-export muted'> </i>导出", '', "class='btn btn-primary download-btn'"); ?>
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
    <form id='myTaskForm' class="table-task" data-ride="table" method="post">
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title"><?php echo $title; ?></div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
        <div data-ride='table' class='bigbox'>
        <?php if (count($short) > 0): ?>
        <div class='red workload2'>
        <span>任务不饱和：</span>
        <?php foreach ($short as $user => $all): ?>
        <span class='user'><?php echo $users[$user]; ?>(<?php echo $all; ?>)</span>
        <?php endforeach?>
        </div>
        <?php endif?>
        <?php if (count($exceed) > 0): ?>
        <div class='red workload2'>
        <span>任务超负荷：</span>
        <?php foreach ($exceed as $user => $all): ?>
        <span class='user'><?php echo $users[$user]; ?>(<?php echo $all; ?>)</span>
        <?php endforeach?>
        </div>
        <?php endif?>
          <table class='table table-bordered table-fixed no-margin' id="workload">
            <thead>
              <tr class='colhead text-center'>
                <th class="w-60px" ><?php echo $lang->report->user; ?></th>
                <th class="w-120px">完成度<a href="javascript:;"  title="实际消耗/预计消耗/全部任务"><i class="icon-question-sign"></i></a></th>
                <th><?php echo $lang->report->task; ?></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($workload as $account => $load): ?>
                <?php foreach ($load['detail'] as $index => $list): ?>
                <tr class="text-left">
                    <?php if ($index == 0): ?>
                    <td class='td-line' rowspan="<?php echo count($load['detail']); ?>"><?php echo $users[$account]; ?></td>
                    <td class='td-line' style='position:relative' rowspan="<?php echo count($load['detail']); ?>">
                        <div class='content'><?php echo $load['consumed']; ?>/<?php echo $load['complete']; ?>/<?php echo $load['all']; ?></div>
                        <div style='width:<?php echo intval($load['complete'] / $load['all'] * 100) ?>%' class='fg <?php if ($load['all'] > 10): echo 'fgred';elseif ($load['all'] > 8): echo 'fgorange';elseif ($load['all'] == 8): echo 'fggreen';elseif ($load['all'] < 8): echo 'fgblue';?><?php endif;?>'></div>
                        <div class='bg <?php if ($load['all'] > 10): echo 'bgred';elseif ($load['all'] > 8): echo 'bgorange';elseif ($load['all'] == 8): echo 'bggreen';elseif ($load['all'] < 8): echo 'bgblue';?><?php endif;?>'></div>
                    </td>
                    <?php endif;?>
                    <td class=' <?php if ($index == count($load['detail']) - 1): echo 'td-line';?><?php endif;?>'>
                        <div class='task-detail'>
                            <span class='overview'><?php echo $list->consumed; ?> / <?php echo $list->estimate; ?></span>
                            <?php echo html::a($this->createLink('project', 'task', "projectID={$list->project}"), "<span class='project-name'><span  class='pri pri_{$list->pri}'>{$list->pri}</span>{$list->projectName}</span>"); ?>
                            <?php if ($list->moduleId):?>
                            <?php echo html::a($this->createLink('project', 'task', "projectID=$list->project&browseType=byModule&param=$list->moduleId"), "<span class='module-name'>{$list->moduleName}</span>") ?>
                            <?php else:?>
                            <span class='no-module-name'>&nbsp;</span>
                            <?php endif;?>
                            <?php if ($list->storyID):?>
                            <?php echo html::a($this->createLink('story', 'view', "storyID=$list->storyID"), "<span class='story-name'>{$list->storyTitle}</span>") ?>
                            <?php else:?>
                            <span class='no-story-name'>&nbsp;</span>
                            <?php endif;?>
                            <span class='taskstatus status-<?php echo $list->status; ?>'><?php echo zget($lang->task->statusList, $list->status) ?></span>
                            <?php if ($canBatchEdit): ?>
                            <div class="checkbox-primary checkbox" >
                                <input type='checkbox' name='taskIDList[]' id='taskIDList-<?php echo $list->id; ?>' value='<?php echo $list->id; ?>' />
                                <label for='taskIDList-<?php echo $list->id; ?>'></label>
                            </div>
                            <?php endif;?>
                            <?php echo html::a($this->createLink('task', 'view', "taskID={$list->id}"),
                            "<span class='task-name'>&nbsp;<span class='pri pri_{$list->taskpri}'>{$list->taskpri}&nbsp;</span>&nbsp;{$list->name}</span>"); ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach;?>
            <?php endforeach;?>
            </tbody>
          </table>
          <?php if ($canBatchEdit): ?>
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
            <?php endif;?>
        </div>
      </div>
    </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
