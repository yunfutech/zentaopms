<?php include '../../common/view/header.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
    <a href="<?php echo $backLink;?>" class="btn btn-secondary">
      <i class="icon icon-back icon-sm"></i> <?php echo $lang->goback;?>
    </a>
    <?php
      $link = $this->createLink('target', 'createDataset', "projectID=$projectID");
      echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->createDataset}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>

<div id="mainContent" class="main-row fade">
<div data-ride='table'>
    <table class='table table-condensed table-striped table-bordered table-fixed no-margin'>
      <thead>
        <tr class="text-center">
          <th class='w-120px'>id</th>
          <th class='w-120px'><?php echo $lang->target->name;?></th>
          <th class='w-120px'><?php echo $lang->target->type;?></th>
          <th class='w-120px'><?php echo $lang->target->source;?></th>
          <th class='w-120px'><?php echo $lang->target->size;?></th>
          <th class='w-120px'><?php echo $lang->target->handle;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($datasets as $dataset):?>
          <tr class="text-center">
            <td><?php echo $dataset->id;?></td>
            <td><?php echo $dataset->name;?></td>
            <td><?php echo $dataset->type;?></td>
            <td><?php echo $dataset->source;?></td>
            <td><?php echo $dataset->size;?></td>
            <td>
              <?php
                $link = $this->createLink('target', 'editDataset', "projectID=$projectID&category=$dataset->id");
                echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->editDataset}", '', "class='btn btn-primary'");
              ?>
              <?php
                $link = $this->createLink('target', 'deleteDataset', "projectID=$projectID&category=$dataset->id");
                echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->deleteDataset}", '', "class='btn btn-primary'");
              ?>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>