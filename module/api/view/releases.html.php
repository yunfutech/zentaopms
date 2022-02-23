<?php
/**
 * The index view file of doc module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Thanatos <thanatos915@163.com>
 * @package     doc
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-row fade'>
  <div class='main-col'>
    <div class="cell" id="queryBox" data-module='user'></div>
    <div id='mainMenu' class='clearfix'>
      <div class='main-header'>
        <h2><?php echo $lang->api->managePublish;?></h2>
      </div>
    </div>
    <form class='main-table table-user' data-ride='table' method='post' data-checkable='false' id='userListForm'>
      <table class='table has-sort-head' id='userList'>
        <thead>
          <tr>
            <?php $vars = "libID={$libID}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
            <th class='c-id'><?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?></th>
            <th class='c-version'><?php common::printOrderLink('version', $orderBy, $vars, $lang->api->version);?></th>
            <th class='c-name'><?php echo $lang->api->desc;?></th>
            <th class='c-user'><?php common::printOrderLink('addedBy', $orderBy, $vars, $lang->api->structAddedBy);?></th>
            <th class='c-date'><?php common::printOrderLink('addedDate', $orderBy, $vars, $lang->api->structAddedDate);?></th>
            <th class='c-actions'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($releases as $release):?>
          <tr>
            <td><?php printf('%03d', $release->id);?></td>
            <td><?php echo $release->version;?></td>
            <td><?php echo $release->desc;?></td>
            <td><?php echo zget($users, $release->addedBy, '');?></td>
            <td class="c-date"><?php echo $release->addedDate;?></td>
            <td class='c-actions'>
              <?php if(common::hasPriv('api', 'deleteRelease')) echo html::a($this->createLink('api', 'deleteRelease', "libID=$libID&id=$release->id"), '<i class="icon-trash"></i>', 'hiddenwin', "title='{$lang->api->deleteRelease}' class='btn'");?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
parent.$('#triggerModal .modal-content .modal-header .close').click(function()
{
    parent.location.reload();
});
</script>
<?php include '../../common/view/footer.html.php';?>
