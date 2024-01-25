<?php
/**
 * The build view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: build.html.php 4262 2013-01-24 08:48:56Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../../module/common/view/header.html.php';?>
<?php include '../../../../module/common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->projectweekly->edit;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <?php echo html::input("projectID", $projectID, "class='form-control' required");?>
      <script>document.getElementById("projectID").style="display:none";</script>
      <table class='table table-form'>
        <tr>
          <td colspan='4'>
            <h2><?php echo $lang->projectweekly->name;?></h2>
            <?php echo html::input("name", $weekly->name, "class='form-control' required");?>
          </td>
        </tr>
        <tr>
            <td colspan="4">
                <h2><?php echo $lang->projectweekly->overview;?></h2>
                <?php echo html::textarea('overview', $weekly->overview, "rows='6' class='form-control kindeditor' hidefocus='true'");?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <h2><?php echo $lang->projectweekly->question;?></h2>
                <?php echo html::textarea('question', $weekly->question, "rows='6' class='form-control kindeditor' hidefocus='true'");?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <h2><?php echo $lang->projectweekly->weekjob;?></h2>
                <?php echo html::textarea('weekjob', $weekly->weekjob, "rows='6' class='form-control kindeditor' hidefocus='true'");?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <h2><?php echo $lang->projectweekly->weekplan;?></h2>
                <?php echo html::textarea('weekplan', $weekly->weekplan, "rows='6' class='form-control kindeditor' hidefocus='true'");?>
            </td>
        </tr>
        <tr>
          <td  colspan='4' class='text-center form-actions'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../../../module/common/view/footer.html.php';?>