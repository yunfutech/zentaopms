<?php
/**
 * The activate of opportunity module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     opportunity
 * @version     $Id: activate.html.php 4903 2021-05-27 14:54:59Z tsj $
 * @link        https://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . "common/view/header.html.php";?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
 <div class='main-header'>
    <h2>
      <span class='prefix label-id'><strong><?php echo $opportunity->id;?></strong></span>
      <?php echo "<span title='$opportunity->name'>" . $opportunity->name . '</span>';?>
    </h2>
  </div> 
  <div class="modal-body" style="min-height: 282px; overflow: auto;">
    <form class='load-indicator main-form' method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tbody>
          <tr>
            <th class='w-100px'><?php echo $lang->opportunity->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $users, $this->app->user->account, "class='form-control chosen'");?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control kindeditor' hidefocus='true'");?></td>
          </tr>
          <tr>
            <td class='text-center form-actions' colspan='3'><?php echo html::submitButton(); ?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include $app->getModuleRoot() . "common/view/footer.html.php";?>
