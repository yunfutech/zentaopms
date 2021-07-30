<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content">
  <?php $backLink = $this->createLink('project', 'target', 'project=' . $projectID, '');?>
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
            <th>类别</th>
            <td>
              <?php echo html::select("category", $categories, 1, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>数据集</th>
            <td>
              <?php echo html::select("dataset", $datasets, 1, "class='form-control chosen'");?>
            </td>
          </tr>
          <tr>
            <th>日期</th>
            <td>
              <?php echo html::input("deadline", date('Y-m-d'), "class='form-control form-date' required");?>
            </td>
          </tr>
          <tr>
            <th><i class="icon icon-exclamation-sign"></i></th>
            <td colspan="3" style="color: #EA644A">
              指标输入需保留三位小数
            </td>
          </tr>
          <tr>
            <th>目标准确率</th>
            <td>
              <?php echo html::input('precision', '', "class='form-control'");?>
            </td>
            <th>目标召回率</th>
            <td>
              <?php echo html::input('recall', '', "class='form-control'");?>
            </td>
            <th>目标F1</th>
            <td>
              <?php echo html::input('f1', '', "class='form-control'");?>
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
