<?php
/**
 * The createregion file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     kanban
 * @version     $Id: createregion.html.php 935 2021-12-15 16:23:24Z $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><span><?php echo $lang->kanban->createRegion;?></span></h2>
    </div>
    <form method='post' enctype='multipart/form-data' class='main-form form-ajax'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->kanbanregion->name;?></th>
          <td><?php echo html::input('name', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->kanbanregion->style;?></th>
          <td><?php echo html::radio('region', $regions, 'custom');?></td>
        </tr>
        <tr>
          <td colspan='2' class='form-actions text-center'>
            <?php echo html::submitButton();?>
            <?php echo html::commonButton($lang->cancel, "data-dismiss='modal'", 'btn btn-wide');?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
