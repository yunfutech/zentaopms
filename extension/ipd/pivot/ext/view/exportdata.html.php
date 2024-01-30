<?php js::import($this->app->getWebRoot() . 'js/sheetjs/xlsx.full.min.js');?>
<?php js::import($this->app->getWebRoot() . 'js/filesaver/filesaver.js');?>
<?php $this->app->loadLang('file');?>
<?php js::set('untitled', $lang->file->untitled);?>
<div class="modal fade" id='export'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">× </span></button>
        <h2 class="modal-title" style='font-weight: bold;'><?php echo $lang->export;?></h2>
      </div>
      <div class="modal-body">
        <div style="margin: 20px 0px;">
        <table class="table table-form" style="padding:30px">
          <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->setFileName;?></th>
            <td><?php echo html::input('fileName', '', "class='form-control' autofocus placeholder='{$lang->file->untitled}'");?></td>
            <td>
              <?php
              echo html::select('fileType',  $config->pivot->fileType, '', 'class="form-control"');
              ?>
            </td>
            <td><button class='btn btn-primary' onclick='exportData()'><?php echo $lang->save;?></button></td>
          </tr>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(function()
{
    /* Page is not initialized before clicking export will have bug. */
    $('.btn-export').removeClass('hidden');
})
</script>
