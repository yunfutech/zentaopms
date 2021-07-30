<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content">
  <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
  <a href="<?php echo $backLink;?>" class="btn btn-secondary">
    <i class="icon icon-back icon-sm"></i> <?php echo $lang->goback;?>
  </a>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->target->createRecord;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th>日期</th>
            <td>
              <?php echo html::input("time", '', "class='form-control form-datetime' required");?>
            </td>
          </tr>
          <tr>
            <th><i class="icon icon-exclamation-sign"></i></th>
            <td colspan="3" style="color: #EA644A">
              指标输入需保留三位小数
            </td>
          </tr>
          <tr>
            <th>准确率</th>
            <td>
              <?php echo html::input('precision', '', "class='form-control' required");?>
            </td>
            <th>召回率</th>
            <td>
              <?php echo html::input('recall', '', "class='form-control' required");?>
            </td>
            <th>F1</th>
            <td>
              <?php echo html::input('f1', '', "class='form-control' required");?>
            </td>
          </tr>
          <tr>
            <th>方案</th>
            <td colspan='5'>
              <?php echo html::textarea('solution', '', "style='height: 300px;' class='form-control kindeditor disabled-ie-placeholder' hidefocus='true' required");?>
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
