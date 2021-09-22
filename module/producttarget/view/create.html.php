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
          <td colspan='1'><?php echo html::input("name", '', "class='form-control' required");?></td>
          <th><?php echo $lang->producttarget->confidence;?></th>
          <td colspan='1'>
          <?php echo html::select('confidence', $lang->producttarget->confidenceList, '0', "class='form-control' required");?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->lastTarget;?></th>
          <td colspan='1'><?php echo html::input("lastTarget", '', "class='form-control'");?></td>
          <th><?php echo $lang->producttarget->target;?></th>
          <td colspan='1'><?php echo html::input("target", '', "class='form-control' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->middle;?></th>
          <td colspan='1'><?php echo html::input("middle", '', "class='form-control'");?></td>
          <th><?php echo $lang->producttarget->performance;?></th>
          <td colspan='1'><?php echo html::input("performance", '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttarget->cause;?></th>
          <td colspan='3'>
            <div class='cause'><?php echo html::textarea('cause', '', "style='width:100%;height:200px'");?></div>
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

<?php include '../../common/view/footer.html.php';?>
