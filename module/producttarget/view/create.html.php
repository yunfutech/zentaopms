<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->producttarget->create;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->producttarget->name;?></th>
          <td colspan='3'><?php echo html::input("name", $name, "class='form-control' readonly");?></td>
          <th><?php echo $lang->producttarget->month;?></th>
          <td colspan='2'><input type="month" name="month" id="month" value="<?php echo $month?>" class="form-control" autocomplete="off" onchange="changeMonth(this.value)"></td>
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
                <?php if (!is_null($lastTarget)):?>
                  <?php echo html::input("lastTarget", $lastTarget, "class='form-control' readonly oninput='clearNoNum(this)'");?>
                <?php else:?>
                  <?php echo html::input("lastTarget", '', "class='form-control' oninput='clearNoNum(this)'");?>
                <?php endif;?>
                <span class="input-group-addon">%</span>
              </div>
            </td>
          </div>
          <th><?php echo $lang->producttarget->target;?></th>
          <td colspan='2'>
            <div class="input-group">
              <?php echo html::input("target", '', "class='form-control' required oninput='clearNoNum(this)'");?>
              <span class="input-group-addon">%</span>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->cause;?></th>
          <td colspan='12'>
            <div class='cause'><?php echo html::textarea('cause', '', "style='width:100%;height:200px'");?></div>
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
<?php echo html::hidden('oldTargets', json_encode($oldTargets))?>