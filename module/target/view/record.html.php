<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content">
  <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
  <a href="<?php echo $backLink;?>" class="btn btn-secondary">
    <i class="icon icon-back icon-sm"></i> <?php echo $lang->goback;?>
  </a>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo '创建记录';?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th>模块</th>
            <td>
              <?php echo html::select("module", $module_names, $module_ids, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>日期</th>
            <td>
              <?php echo html::input("time", '', "class='form-control form-datetime' required");?>
            </td>
          </tr>
          <tr>
            <th>准确率</th>
            <td>
              <?php echo html::input('precision', '', "class='form-control'");?>
            </td>
            <th>召回率</th>
            <td>
              <?php echo html::input('recall', '', "class='form-control'");?>
            </td>
            <th>f1</th>
            <td>
              <?php echo html::input('f1', '', "class='form-control'");?>
            </td>
          </tr>
          <tr>
            <th>方案</th>
            <td>
              <?php echo html::textarea('solution', $solution, "style='height: 100px' rows='4' class='form-control kindeditor disabled-ie-placeholder' hidefocus='true'");?>
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
