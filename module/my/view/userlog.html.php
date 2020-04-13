<?php include '../../common/view/header.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $recTotalLabel = " <span class='label label-light label-badge'>{$pager->recTotal}</span>";
    if ($isHaveDaily) {
      echo html::a(inlink('userlog', "type=daily"), "<span class='text'>{$lang->userlog->daily}</span>" . ($type == 'daily' ? $recTotalLabel : ''), '', "class='btn btn-link" . ($type == 'daily' ? ' btn-active-text' : '') . "'");
    }
    echo html::a(inlink('userlog', "type=weekly"), "<span class='text'>{$lang->userlog->weekly}</span>"   . ($type == 'weekly'   ? $recTotalLabel : ''),   '', "class='btn btn-link" . ($type == 'weekly'   ? ' btn-active-text' : '') . "'");
    ?>
  </div>
  <?php if ($type == 'daily'):?>
    <div class="btn-toolbar pull-right">
      <?php $disabled = $isFinished? 'disabled' : ''?>
      <?php common::printLink('userlog', 'generateDaily', '', "<i class='icon icon-plus'></i> " . $lang->userlog->generateDaily, '', "class='btn btn-primary' data-width='80%'  $disabled");?>
    </div>
  <?php endif;?>
</div>
<div id="mainContent">
  <?php if(empty($userlogs)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->task->noTask;?></span></p>
  </div>
  <?php else:?>
  <form class="main-table table-task">
    <table class="table has-sort-head table-fixed">
      <?php $vars = "type=$type&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
      <thead>
        <tr>
          <th class="c-id">
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th class='c-name'><?php common::printOrderLink('name', $orderBy, $vars, $lang->userlog->name);?></th>
          <th class='c-date w-150px'><?php common::printOrderLink('date', $orderBy, $vars, $lang->userlog->date);?></th>
          <th class='c-actions-2'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($userlogs as $userlog):?>
        <tr>
          <td class="c-id">
            <?php printf('%03d', $userlog->id);?>
          </td>
          <td class='c-name' title="<?php echo $userlog->name;?>"><?php echo html::a($this->createLink('userlog', 'view', "userlogId=$userlog->id"), $userlog->name);?></td>
          <td class='c-date'><?php echo $userlog->date?></td>
          <td class='c-actions'>
            <?php
              if ($type == 'daily') {
                if ($userlog->status == 0) {
                  common::printIcon('userlog', 'finish', "userlogId=$userlog->id", $userlog, 'list');
                } else {
                  common::printIcon('userlog', 'finish', "userlogId=$userlog->id", $userlog, 'list', '', '', '', '', 'disabled');
                }
              }
              if ($userlog->status == 0) {
                common::printIcon('userlog', 'edit',   "userlogId=$userlog->id", $userlog, 'list');
              } else {
                common::printIcon('userlog', 'edit',   "userlogId=$userlog->id", $userlog, 'list', '', '', '', '', 'disabled');
              }
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <div class="table-footer">
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>