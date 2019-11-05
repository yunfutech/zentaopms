<?php include '../../common/view/header.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
      $link = $this->createLink('target', 'module', "projectID=$projectID");
      echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->createModule}", '', "class='btn btn-primary'");
    ?>
    <?php
      $link = $this->createLink('target', 'experiment', "projectID=$projectID");
      echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->createExperiment}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>

<div id="mainContent" class="main-row fade">
  <div class="side-col" id="sidebar">
    <div class="left-col">
      <div class="left-title">
        <div class="left-text">
          <?php echo $lang->target->category->all?>
        </div>
      </div>
      <div class="cell">
        <?php foreach ($categories as $category):?>
          <div class="left-cell-text">
            <?php echo $category->name;?>
          </div>
        <?php endforeach;?>
        <div class="text-center">
          <?php common::printLink('target', 'category', "projectID=$projectID", $lang->target->category->manage, '', "class='btn btn-info btn-wide'");?>
          <hr class="space-sm" />
        </div>
      </div>
    </div>
    <div class="left-col">
      <div class="left-title">
        <div class="left-text">
          <?php echo $lang->target->dataset->all?>
        </div>
      </div>
      <div class="cell">
        <?php foreach ($datasets as $dataset):?>
          <div class="left-cell-text">
            <?php echo $dataset->name;?>
          </div>
        <?php endforeach;?>
        <div class="text-center">
          <?php common::printLink('target', 'dataset', "projectID=$projectID", $lang->target->dataset->manage, '', "class='btn btn-info btn-wide'");?>
          <hr class="space-sm" />
        </div>
      </div>
    </div>
  </div>

  <div data-ride='table'>
    <table class='table table-condensed table-striped table-bordered table-fixed no-margin'>
      <thead>
        <tr class="text-center">
          <th class='w-70px' rowspan="2"><?php echo $lang->target->category->name;?></th>
          <th class='w-70px' rowspan="2"><?php echo $lang->target->module;?></th>
          <th class='w-150px' colspan="2"><?php echo $lang->target->dataset->name;?></th>
          <th class="w-200px" colspan="4"><?php echo $lang->target->target;?></th>
          <th class="w-450px" colspan="6"><?php echo $lang->target->record;?></th>
          <th class="w-300px" rowspan="2"><?php echo $lang->target->handle;?></th>
        </tr>
        <tr class="text-center">
          <td><?php echo $lang->target->name;?></td>
          <td><?php echo $lang->target->size;?></td>
          <td><?php echo $lang->target->time;?></td>
          <td><?php echo $lang->target->precision;?></td>
          <td><?php echo $lang->target->recall;?></td>
          <td><?php echo $lang->target->f1;?></td>
          <td><?php echo $lang->target->time;?></td>
          <td><?php echo $lang->target->solution;?></td>
          <td><?php echo $lang->target->precision;?></td>
          <td><?php echo $lang->target->recall;?></td>
          <td><?php echo $lang->target->f1;?></td>
          <td><?php echo $lang->target->handle;?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($experiments as $experiment):?>
          <tr class="text-center">
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->module->category->name;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->module->name;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->dataset->name;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->dataset->size;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->target->deadline;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->target->performance->precision_;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->target->performance->recall;?></td>
            <td rowspan="<?php echo $experiment->recordLen;?>"><?php echo $experiment->target->performance->f1;?></td>
            <?php if (!empty($experiment->record)):?>
              <td><?php echo $experiment->record[0]->time;?></td>
              <td><?php echo $experiment->record[0]->solution;?></td>
              <td><?php echo $experiment->record[0]->performance->precision_;?></td>
              <td><?php echo $experiment->record[0]->performance->recall;?></td>
              <td><?php echo $experiment->record[0]->performance->f1;?></td>
              <td>
                <?php
                  $link = $this->createLink('target', 'editRecord', "projectID=$projectID&experiment=$experiment->id");
                  echo html::a($link, "{$lang->target->editRecord}", '', "class='btn btn-primary'");
                ?>
                <?php
                  $link = $this->createLink('target', 'deleteRecord', "projectID=$projectID&experiment=$experiment->id");
                  echo html::a($link, "{$lang->target->deleteRecord}", '', "class='btn btn-primary'");
                ?>
              </td>
            <?php else:?>
              <td colspan="6" rowspan="1"></td>
            <?php endif;?>
            <td rowspan="<?php echo $experiment->recordLen;?>">
              <?php
                $link = $this->createLink('target', 'record', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->createRecord}", '', "class='btn btn-primary'");
              ?>
              <?php
                $link = $this->createLink('target', 'editExperiment', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->editExperiment}", '', "class='btn btn-primary'");
              ?>
              <?php
                $link = $this->createLink('target', 'deleteExperiment', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->deleteExperiment}", '', "class='btn btn-primary'");
              ?>
            </td>
          </tr>
          <?php foreach ($experiment->surplusRecord as $record):?>
            <tr class="text-center">
              <td><?php echo $record->time;?></td>
              <td><?php echo $record->solution;?></td>
              <td><?php echo $record->performance->precision_;?></td>
              <td><?php echo $record->performance->recall;?></td>
              <td><?php echo $record->performance->f1;?></td>
              <td rowspan="<?php echo $experiment->recordLen;?>">
                <?php
                  $link = $this->createLink('target', 'editRecord', "projectID=$projectID&experiment=$experiment->id");
                  echo html::a($link, "{$lang->target->editRecord}", '', "class='btn btn-primary'");
                ?>
                <?php
                  $link = $this->createLink('target', 'deleteRecord', "projectID=$projectID&experiment=$experiment->id");
                  echo html::a($link, "{$lang->target->deleteRecord}", '', "class='btn btn-primary'");
                ?>
            </td>
            </tr>
          <?php endforeach;?>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
