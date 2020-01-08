<?php include '../../common/view/header.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
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
          <?php echo $lang->target->allCategory?>
        </div>
      </div>
      <div class="cell">
        <div class="left-cell-text">
          <?php
            $categoryHtml = "<ul class='cate-ul'>";
            foreach ($categories as $category) {
              if ($category->id == $currentCategory) {
                $categoryHtml .= "<li class='cate-active' id='cate-". strval($category->id) ."' onclick='changeCate($category->id, $projectID)'>" . $category->name. '</li>';
              } else {
                $categoryHtml .= "<li onclick='changeCate($category->id, $projectID)'>" . $category->name. '</li>';
              }
            }
            echo $categoryHtml . '</ul>';
          ?>
        </div>
        <div class="text-center">
          <?php common::printLink('target', 'category', "projectID=$projectID", $lang->target->manageCategory, '', "class='btn btn-info btn-wide'");?>
          <hr class="space-sm" />
        </div>
      </div>
    </div>
    <div class="left-col">
      <div class="left-title">
        <div class="left-text">
          <?php echo $lang->target->allDataset?>
        </div>
      </div>
      <div class="cell">
        <?php foreach ($datasets as $dataset):?>
          <div class="left-cell-text">
            <?php echo $dataset->name;?>
          </div>
        <?php endforeach;?>
        <div class="text-center">
          <?php common::printLink('target', 'dataset', "projectID=$projectID", $lang->target->manageDataset, '', "class='btn btn-info btn-wide'");?>
          <hr class="space-sm" />
        </div>
      </div>
    </div>
  </div>

  <div data-ride='table'>
    <table class='target_table table table-condensed table-striped table-bordered table-fixed no-margin with-footer-fixed'>
      <thead>
        <tr class="text-center">
          <th class='w-100px' rowspan="2"><?php echo $lang->target->category;?></th>
          <th colspan="2"><?php echo $lang->target->dataset;?></th>
          <th colspan="4"><?php echo $lang->target->target;?></th>
          <th colspan="6"><?php echo $lang->target->record;?></th>
          <th class="w-80px" rowspan="2"><?php echo $lang->target->handle;?></th>
        </tr>
        <tr class="text-center">
          <th class='w-120px'><?php echo $lang->target->name;?></th>
          <th class='w-60px'><?php echo $lang->target->size;?></th>
          <th class='w-80px'><?php echo $lang->target->deadline;?></th>
          <th class='w-60px'><?php echo $lang->target->precision;?></th>
          <th class='w-60px'><?php echo $lang->target->recall;?></th>
          <th class='w-60px'><?php echo $lang->target->f1;?></th>
          <th class='w-80px'><?php echo $lang->target->time;?></th>
          <th class="w-60px"><?php echo $lang->target->precision;?></th>
          <th class="w-60px"><?php echo $lang->target->recall;?></th>
          <th class="w-60px"><?php echo $lang->target->f1;?></th>
          <th><?php echo $lang->target->solution;?></th>
          <th class="w-60px"><?php echo $lang->target->handle;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($experiments as $experiment):?>
          <tr class="text-center">
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->category->name;?></td>
            <?php if ($experiment->dataset->type == '测试集'):?> 
              <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><span class="label-pri label-pri-1" style=" margin-top: -3px;margin-right: 10px">测</span><?php echo $experiment->dataset->name;?></td>
            <?php elseif ($experiment->dataset->type == '开发集'):?>
              <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><span class="label-pri label-pri-2" style=" margin-top: -3px;margin-right: 10px">开</span><?php echo $experiment->dataset->name;?></td>
            <?php else:?>
              <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->dataset->name;?></td>
            <?php endif;?>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->dataset->size;?></td>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->target->deadline;?></td>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->target->performance->precision_;?></td>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->target->performance->recall;?></td>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?><?php echo $experiment->target->performance->f1;?></td>
            <?php if (!empty($experiment->record)):?>
              <td class="time-td"><?php echo $experiment->record[0]->time;?></td>
              <td class='<?php if ($experiment->record[0]->performance->precision_ >= $experiment->target->performance->precision_): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $experiment->record[0]->performance->precision_;?></td>
              <td class='<?php if ($experiment->record[0]->performance->recall >= $experiment->target->performance->recall): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $experiment->record[0]->performance->recall;?></td>
              <td class='<?php if ($experiment->record[0]->performance->f1 >= $experiment->target->performance->f1): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $experiment->record[0]->performance->f1;?></td>
              <td><?php echo $experiment->record[0]->solution;?></td>
              <td>
                <?php
                  if (count($experiment->record) == 1) {
                    echo "<button data-rowspan=$experiment->recordLen disabled class='edit-btn btn btn-xs btn-primary record-more' id='more-". strval($experiment->id) . "'>展开</button>";
                  } else {
                    echo "<button data-rowspan=$experiment->recordLen class='edit-btn btn btn-xs btn-primary record-more' id='more-". strval($experiment->id) . "'>展开</button>";
                  }
                ?>
                <?php
                  $link = $this->createLink('target', 'editRecord', "projectID=$projectID&record=".$experiment->record[0]->id);
                  echo html::a($link, "{$lang->target->editRecord}", '', "class='edit-btn btn btn-xs btn-primary'");
                ?>
                <br>
                <?php
                  $link = $this->createLink('target', 'deleteRecord', "projectID=$projectID&record=".$experiment->record[0]->id);
                  echo html::a($link, "{$lang->target->deleteRecord}", '', "class='btn btn-xs btn-primary'");
                ?>
              </td>
            <?php else:?>
              <?php echo "<td class='target-td-$experiment->id' colspan='6' rowspan='1'>"?></td>
            <?php endif;?>
            <?php echo "<td class='target-td-$experiment->id' rowspan='1'>"?>
              <?php
                $link = $this->createLink('target', 'record', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->createRecord}", '', "class='edit-btn btn btn-xs btn-primary'");
              ?>
              <br>
              <?php
                $link = $this->createLink('target', 'editExperiment', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->editExperiment}", '', "class='edit-btn btn btn-xs btn-primary'");
              ?>
              <br>
              <?php
                $link = $this->createLink('target', 'deleteExperiment', "projectID=$projectID&experiment=$experiment->id");
                echo html::a($link, "{$lang->target->deleteExperiment}", '', "class='btn btn-xs btn-primary'");
              ?>
            </td>
          </tr>
          <?php foreach ($experiment->surplusRecord as $record):?>
            <?php echo "<tr style='display:none' class='text-center sur-record-" . strval($experiment->id) . "'>"?>
              <td class='time-td'><?php echo $record->time;?></td>
              <td class='<?php if ($record->performance->precision_ >= $experiment->target->performance->precision_): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $record->performance->precision_;?></td>
              <td class='<?php if ($record->performance->recall >= $experiment->target->performance->recall): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $record->performance->recall;?></td>
              <td class='<?php if ($record->performance->f1 >= $experiment->target->performance->f1): 
                                  echo 'bggreen';
                               else:
                                echo 'bgred';?>
                          <?php endif;?>'><?php echo $record->performance->f1;?></td>
              <td><?php echo $record->solution;?></td>
              <td>
                <?php
                  $link = $this->createLink('target', 'editRecord', "projectID=$projectID&record=$record->id");
                  echo html::a($link, "{$lang->target->editRecord}", '', "class='edit-btn btn btn-xs btn-primary'");
                ?>
                <br>
                <?php
                  $link = $this->createLink('target', 'deleteRecord', "projectID=$projectID&record=$record->id");
                  echo html::a($link, "{$lang->target->deleteRecord}", '', "class='btn btn-xs btn-primary'");
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
