<?php
/**
 * The create lib view of assetlib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     assetlib
 * @version     $Id: create.html.php 40
 * @link        https://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $title;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tr>
          <th><?php echo $lang->assetlib->name;?></th>
          <td><?php echo html::input('name', '', "class='form-control' required");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->assetlib->desc;?></th>
          <td><?php echo html::textarea('desc', '', "rows=10 class='form-control'");?></td>
        </tr>
        <tr>
          <td class='text-center form-actions' colspan='2'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>