<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content">
  <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
  <a href="<?php echo $backLink;?>" class="btn btn-secondary">
    <i class="icon icon-back icon-sm"></i><?php echo $lang->goback;?>
  </a>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->target->editDataset;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->target->name;?></th>
            <td>
              <?php echo html::input("name", $dataset->name, "class='form-control'");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->target->type;?></th>
            <td>
              <?php echo html::select("type", [1=> '测试集', 2=>'开发集'], $dataset->type, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->target->source;?></th>
            <td>
              <?php echo html::input("source", $dataset->source, "class='form-control'");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->target->size;?></th>
            <td>
              <?php echo html::input("size", $dataset->size, "class='form-control'");?>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center form-actions">
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
