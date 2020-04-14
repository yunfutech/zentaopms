<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/markdown.html.php';?>

<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->productweekly->edit;?></h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->productweekly->name;?></th>
          <td colspan='3'><?php echo html::input("name", $weekly->name, "class='form-control' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->productweekly->content;?></th>
          <td colspan='3'>
            <div class='contentmarkdown'><?php echo html::textarea('content', $weekly->content, "style='width:100%;height:200px'");?></div>
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
