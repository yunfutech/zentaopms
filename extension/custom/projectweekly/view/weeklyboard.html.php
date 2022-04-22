<?php include '../../../../module/common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('projectweekly', 'weeklyboard', "week=0&project=0&user=0"), $lang->projectweekly->all, '', "class='btn btn-primary next'"); ?>
    <?php echo html::a($this->createLink('projectweekly', 'weeklyboard', "week={$lastWeek}&product={$product}&user={$user}"), $lang->projectweekly->lastWeek, '', "class='btn btn-primary next'"); ?>
    <?php echo html::a($this->createLink('projectweekly', 'weeklyboard', "week={$thisWeek}&product={$product}&user={$user}"), $lang->projectweekly->thisWeek, '', "class='btn btn-primary next'"); ?>
    <?php echo html::a($this->createLink('projectweekly', 'weeklyboard', "week={$nextWeek}&product={$product}&user={$user}"), $lang->projectweekly->nextWeek, '', "class='btn btn-primary next'"); ?>
    <div class="input-group space w-250px">
      <span class='input-group-addon'><?php echo $lang->projectweekly->week; ?></span>
      <?php echo html::select('week', $weeks, $week, "onchange=changeParams() class='form-control chosen'"); ?>
    </div>
    <div class="input-group space w-150px">
      <span class='input-group-addon'><?php echo $lang->projectweekly->project; ?></span>
      <?php echo html::select('project', $projects, $project, 'onchange=changeParams() class="form-control chosen"'); ?>
    </div>
    <div class="input-group space w-150px">
      <span class='input-group-addon'><?php echo $lang->projectweekly->productDirector; ?></span>
      <?php echo html::select('user', $users, $user, 'onchange=changeParams() class="form-control chosen"'); ?>
    </div>
  </div>
</div>
<div id="mainContent">
  <?php if (empty($weeklies)) : ?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->projectweekly->noWeekly; ?></span></p>
    </div>
  <?php else : ?>
    <form class="main-table table-task">
      <table class="table has-sort-head table-fixed">
        <?php $vars = "week=$week&project=$project&user={$user}&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
        <thead>
          <tr>
            <th class="c-id">
              <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?>
            </th>
            <th class='c-name'><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectweekly->weeklyName); ?></th>
            <th class='c-date w-150px'><?php common::printOrderLink('realname', $orderBy, $vars, $lang->projectweekly->account); ?></th>
            <th class='c-date w-150px'><?php common::printOrderLink('date', $orderBy, $vars, $lang->projectweekly->createdDate); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($weeklies as $weekly) : ?>
            <tr>
              <td class="c-id">
                <?php printf('%03d', $weekly->id); ?>
              </td>
              <td class='c-name' title="<?php echo $weekly->name; ?>"><?php echo html::a($this->createLink('projectweekly', 'view', "weeklyID=$weekly->id&projectID=$weekly->projectID"), $weekly->name, $target = "_blank"); ?></td>
              <td class='c-date'><?php echo $weekly->realname ?></td>
              <td class='c-date'><?php echo $weekly->createdDatetime ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs'); ?>
      </div>
    </form>
  <?php endif; ?>
</div>
<?php include '../../../../module/common/view/footer.html.php';?>
