<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->milestone->edit;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->milestone->name;?></th>
          <td colspan='1'><?php echo html::input("name", $milestone->name, "class='form-control' required");?></td>
          <th><?php echo $lang->milestone->date;?></th>
          <td><?php echo html::input('date', $milestone->date, "class='form-control form-date' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->milestone->isContract;?></th>
          <td><?php echo html::select('isContract', $lang->milestone->isContractList, $milestone->isContract, "class='form-control chosen' onChange=changeContract(this.value)");?></td>
          <th><?php echo $lang->milestone->pri;?></th>
          <td colspan='1'>
          <?php
              $priList = $lang->milestone->priList;
          ?>
          <?php if($milestone->isContract == '1'):?>
          <?php echo html::select('pri', (array)$priList, $milestone->pri, "class='form-control' disabled");?>
          <?php else:?>
          <?php echo html::select('pri', (array)$priList, $milestone->pri, "class='form-control'");?>
          <?php endif;?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->milestone->completed;?></th>
          <td><?php echo html::select('completed', $lang->milestone->completedList, $milestone->completed, "class='form-control chosen'");?></td><td></td>
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
