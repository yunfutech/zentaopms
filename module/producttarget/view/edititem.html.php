<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->producttargetitem->edit;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->producttargetitem->name;?></th>
          <td><?php echo html::input("name", $producttargetitem->name, "class='form-control' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttargetitem->intro;?></th>
          <td colspan='2'><?php echo html::input('intro', $producttargetitem->intro, "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttargetitem->acceptance;?></th>
          <td colspan='2'><?php echo html::input('acceptance', $producttargetitem->acceptance, "class='form-control' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->producttargetitem->completion;?></th>
          <td colspan='2'><?php echo html::input('completion', $producttargetitem->completion, "class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='4' class='text-center form-actions'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
