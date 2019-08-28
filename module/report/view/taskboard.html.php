<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
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
                <th class="w-100px">任务总计</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($workload as $account => $load): ?>
                <tr class="text-left">
                    <td><?php echo $users[$account]; ?></td>
                    <td style='position:relative'>
                        <div class='content'>任务：<?php echo $load['complete']; ?>/<?php echo $load['all']; ?></div>
                        <div style='width:<?php echo intval($load['complete'] / $load['all'] * 100) ?>%' class='fg <?php if ($load['all'] > 10): echo 'fgred';elseif ($load['all'] > 8): echo 'fgorange';elseif ($load['all'] <= 8): echo 'fggreen';?><?php endif;?>'></div>
                        <div class='bg <?php if ($load['all'] > 10): echo 'bgred';elseif ($load['all'] > 8): echo 'bgorange';elseif ($load['all'] <= 8): echo 'bggreen';?><?php endif;?>'></div>
                    </td>
                    <td>
                        <?php foreach ($load['detail'] as $list): ?>
                            <div>
                                [<?php echo $list->projectName; ?>]
                                [<?php echo $list->name; ?>] ---
                                <?php echo $list->consumed; ?>/<?php echo $list->estimate; ?>
                            </div>
                        <?php endforeach;?>
                    </td>
                    <td><?php echo $load['all']; ?>小时</td>
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
