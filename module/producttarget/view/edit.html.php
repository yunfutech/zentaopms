<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->producttarget->edit;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->producttarget->name;?></th>
          <td colspan='4'><?php echo html::input("name", $producttarget->name, "class='form-control' readonly");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->confidence;?></th>
          <td colspan='1'>
          <?php echo html::select('confidence', $lang->producttarget->confidenceList, '0', "class='form-control' required");?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->lastTarget;?></th>
          <td colspan='2'>
            <div class="input-group">
              <?php echo html::input("lastTarget", $producttarget->lastTarget, "class='form-control' readonly");?>
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <th><?php echo $lang->producttarget->target;?></th>
          <td colspan='2'>
            <div class="input-group">
              <?php echo html::input("target", $producttarget->target, "class='form-control' readonly");?>
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <th><?php echo $lang->producttarget->middle;?></th>
          <td colspan='2'>
            <div class="input-group">
              <?php echo html::input("middle", $producttarget->middle, "class='form-control' oninput='clearNoNum(this)'");?>
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <th><?php echo $lang->producttarget->performance;?></th>
          <td colspan='2'>
            <div class="input-group">
              <?php echo html::input("performance", $producttarget->performance, "class='form-control' oninput='clearNoNum(this)'");?>
              <span class="input-group-addon">%</span>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->cause;?></th>
          <td colspan='12'>
            <div class='cause'><?php echo html::textarea('cause', $producttarget->cause, "style='width:100%;height:200px'");?></div>
          </td>
        </tr>
        <tr>
          <td colspan='12' class='text-center form-actions'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
