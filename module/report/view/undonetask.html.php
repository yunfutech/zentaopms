<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
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
                <th class="w-50px" >任务id</th>
                <th class="w-50px" ><?php echo $lang->report->user; ?></th>
                <th class="w-150px">项目名</th>
                <th class="w-150px">任务名</th>
                <th class="w-20px">预计时间</th>
                <th class="w-50px center">截止日</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr class="text-left">
                    <td><?php echo $task->id; ?></td>
                    <td><?php echo $users[$task->assignedTo]; ?></td>
                    <td><?php echo html::a($this->createLink('project', 'task', "projectID={$task->project}"), "{$task->projectName}"); ?></td>
                    <td><?php echo html::a($this->createLink('task', 'view', "taskID={$task->id}"), "{$task->name}"); ?></td>
                    <td class="text-center"><?php echo $task->estimate; ?></td>
                    <td class="text-center"><?php echo $task->deadline; ?></td>
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
