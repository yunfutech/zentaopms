<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->milestone->create;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->milestone->name;?></th>
          <td colspan='1'><?php echo html::input("name", $milestone->name, "class='form-control' required");?></td>
          <th><?php echo $lang->milestone->date;?></th>
          <td><?php echo html::input('date', $milestone->date, "class='form-control form-datetime'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->milestone->isContract;?></th>
          <td><?php echo html::select('isContract', $lang->milestone->isContractList, '', "class='form-control chosen'");?></td>
          <th><?php echo $lang->milestone->pri;?></th>
          <td colspan='1'>
          <?php
              $priList = $lang->milestone->priList;
          ?>
          <?php echo html::select('pri', (array)$priList, '3', "class='form-control'");?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->milestone->completed;?></th>
          <td><?php echo html::select('completed', $lang->milestone->completedList, '', "class='form-control chosen'");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->milestone->comment;?></th>
          <td colspan='3'>
            <div class='comment'><?php echo html::textarea('comment', $milestone->comment, "style='width:100%;height:200px'");?></div>
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
