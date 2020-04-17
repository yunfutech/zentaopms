<?php include '../../common/view/header.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
    <a href="<?php echo $backLink;?>" class="btn btn-secondary">
      <i class="icon icon-back icon-sm"></i> <?php echo $lang->goback;?>
    </a>
    <?php
      $link = $this->createLink('target', 'createCategory', "projectID=$projectID");
      echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->createCategory}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>

<div id="mainContent" class="main-row fade">
<div data-ride='table'>
    <table class='table table-condensed table-striped table-bordered table-fixed no-margin'>
      <thead>
        <tr class="text-center">
          <th class='w-120px'>id</th>
          <th class='w-120px'><?php echo $lang->target->category;?></th>
          <th class='w-120px'><?php echo $lang->target->handle;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $category):?>
          <tr class="text-center">
            <td><?php echo $category->id;?></td>
            <td><?php echo $category->name;?></td>
            <td>
              <?php
                $link = $this->createLink('target', 'editCategory', "projectID=$projectID&category=$category->id");
                echo html::a($link, "<i class='icon icon-plus'></i> {$lang->target->editCategory}", '', "class='btn btn-primary'");
              ?>
              <?php
                $link = $this->createLink('target', 'deleteCategory', "projectID=$projectID&category=$category->id");
                echo html::a($link, "<i class='icon icon-trash'></i> {$lang->target->deleteCategory}", '', "class='btn btn-primary'");
              ?>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
