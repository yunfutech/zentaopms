<?php include '../../common/view/header.html.php';?>

<div id="mainContent" class="main-content fade">
  <?php
    $requiredFields = array();
    foreach(explode(',', $config->producttargetitem->batchCreateFields) as $field)
    {
        if($field)
        {
            $requiredFields[$field] = '';
        }
    }
  ?>
  <form method='post' class='load-indicator batch-actions-form form-ajax' enctype='multipart/form-data' id="batchCreateForm">
    <div class="table-responsive">
      <table class="table table-form table-fixed" id="tableBody">
        <thead>
          <tr>
            <th class='w-30px'><?php echo $lang->idAB;?></th>
            <th class='w-300px c-name required has-btn'><?php echo $lang->producttargetitem->name;?></span></th>
            <th class='w-130px<?php echo zget($requiredFields, 'intro', '', ' required');?>'><?php echo $lang->producttargetitem->intro;?></th>
            <th class='w-130px<?php echo zget($requiredFields, 'acceptance', '', ' required');?>'><?php echo $lang->producttargetitem->acceptance;?></th>
            <th class='w-130px<?php echo zget($requiredFields, 'completion', '', ' required');?>'><?php echo $lang->producttargetitem->completion;?></th>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 0; $i < $config->producttargetitem->batchCreate; $i++):?>
          <tr>
            <td class='text-center'><?php echo $i + 1;?></td>
            <td><?php echo html::input("name[$i]", '', "class='form-control'");?></td>
            <td><?php echo html::input("intro[$i]", '', "class='form-control'");?></td>
            <td><?php echo html::input("acceptance[$i]", '', "class='form-control'");?></td>
            <td><?php echo html::input("completion[$i]", '', "class='form-control'");?></td>
          </tr>
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='<?php echo $colspan?>' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>

<?php include '../../common/view/footer.html.php';?>
