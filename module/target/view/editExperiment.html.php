<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content">
  <?php $backLink = $this->session->{$viewType . 'List'} ? $this->session->{$viewType . 'List'} : 'javascript:history.go(-1)';?>
  <a href="<?php echo $backLink;?>" class="btn btn-secondary">
    <i class="icon icon-back icon-sm"></i> <?php echo $lang->goback;?>
  </a>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->target->createExperiment;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th>模块</th>
            <td>
              <?php echo html::select("module", $modules, $experiment->mid, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>类别</th>
            <td>
              <?php echo html::select("category", $categories, $experiment->cid, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>数据集</th>
            <td>
              <?php echo html::select("dataset", $datasets, $experiment->did, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>日期</th>
            <td>
              <?php echo html::input("deadline", $experiment->deadline, "class='form-control form-datetime' required");?>
            </td>
          </tr>
          <tr>
            <th>目标准确率</th>
            <td>
              <?php echo html::input('precision', $experiment->precision, "class='form-control'");?>
            </td>
            <th>目标召回率</th>
            <td>
              <?php echo html::input('recall', $experiment->recall, "class='form-control'");?>
            </td>
            <th>目标F1</th>
            <td>
              <?php echo html::input('f1', $experiment->fi, "class='form-control'");?>
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
