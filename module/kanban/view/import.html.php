<?php
/**
 * The import file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Fangzhou Hu <hufangzhou@easycorp.ltd>
 * @package     kanban
 * @version     $Id: import.html.php 935 2022-01-19 14:15:24Z hufangzhou@easycorp.ltd $
 * @link        https://www.zentao.net
 */
?>

<?php include '../../common/view/header.lite.html.php';?>
<?php js::set('enableImport', $enableImport);?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <?php echo "<span>" . $lang->kanban->import . '</span>';?>
      </h2>
    </div>
    <form method='post' class="load-indicator main-form form-ajax" target='hiddenwin' id='importForm'>
      <table align='center' class='table table-form'>
        <tr>
          <td colspan='2'>
            <label class="radio-inline">
              <input type='radio' name='import' value='off' <?php echo $enableImport == 'off' ? "checked='checked'" : ''; ?> id='importoff'/>
              <?php echo $lang->kanban->importList['off'];?>
            </label>
          </td>
        </tr>
        <tr>
          <td colspan='2'>
            <label class="radio-inline">
              <input type='radio' name='import' value='on' <?php echo $enableImport == 'on' ? "checked='checked'" : ''; ?> id='importon'/>
              <?php echo $lang->kanban->importList['on'];?>
            </label>
          </td>
        </tr>
        <tr>
          <td colspan='2'><?php echo html::checkbox('importObjectList', $lang->kanban->importObjectList, $importObjects);?></td>
        </tr>
        <tr id='emptyTip' class='hidden'><td colspan='3' style='color: red;'><?php echo $lang->kanban->error->importObjNotEmpty;?></td></tr>
        <tr>
          <td class='form-actions'><?php echo html::submitButton();?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
