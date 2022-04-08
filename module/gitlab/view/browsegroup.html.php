<?php
/**
 * The browse view file of gitlab module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     gitlab
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class='pull-left'>
    <?php echo html::linkButton('<i class="icon icon-back icon-sm"></i> ' . $lang->goback, $this->createLink('gitlab', 'browse'), 'self', '','btn btn-secondary');?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('gitlab', 'create')) common::printLink('gitlab', 'createGroup', "gitlabID=$gitlabID", "<i class='icon icon-plus'></i> " . $lang->gitlab->group->create, '', "class='btn btn-primary'");?>
  </div>
</div>
<?php if(empty($gitlabGroupList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
    <?php if(common::hasPriv('gitlab', 'create')):?>
    <?php echo html::a($this->createLink('gitlab', 'createGroup', "gitlabID=$gitlabID"), "<i class='icon icon-plus'></i> " . $lang->gitlab->group->create, '', "class='btn btn-info'");?>
    <?php endif;?>
  </p>
</div>
<?php else:?>
<div id='mainContent' class='main-row'>
  <form class='main-table' id='ajaxForm' method='post'>
    <table id='gitlabGroupList' class='table has-sort-head table-fixed'>
      <thead>
        <tr>
          <?php $vars = "gitlabID=$gitlabID&orderBy=%s";?>
          <th class='c-id'><?php common::printOrderLink('id', $orderBy, $vars, $lang->gitlab->group->id);?></th>
          <th class='c-name text-left'><?php common::printOrderLink('name', $orderBy, $vars, $lang->gitlab->group->name);?></th>
          <th class='text-left'><?php common::printOrderLink('path', $orderBy, $vars, $lang->gitlab->group->path);?></th>
          <th class='text-left'><?php echo $lang->gitlab->group->createOn;?></th>
          <th class='c-actions-3'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($gitlabGroupList as $id => $gitlabGroup): ?>
        <tr class='text'>
          <td class='text-center'><?php echo $gitlabGroup->id;?></td>
          <td class='text-c-name' title='<?php echo $gitlabGroup->name;?>'>
            <?php $groupName = current(common::convert2Pinyin(array($gitlabGroup->name))); ?>
            <?php echo html::avatar($groupName[0], 20); ?>
            <span><?php echo $gitlabGroup->name;?></span>
          </td>
          <td class='text' title='<?php echo $gitlabGroup->path;?>'><?php echo html::a($gitlab->url . '/' . $gitlabGroup->path, $gitlabGroup->path, '_target');?></td>
          <td class='text' title='<?php echo substr($gitlabGroup->created_at, 0, 10);?>'><?php echo substr($gitlabGroup->created_at, 0, 10);?></td>
          <td class='c-actions text-left'>
            <?php
            $adminClass = ($app->user->admin or in_array($gitlabGroup->id, $adminGroupIDList)) ? '' : 'disabled';
            common::printLink('gitlab', 'manageGroupMembers', "gitlabID=$gitlabID&groupID=$gitlabGroup->id", "<i class='icon icon-team'></i> ", '',"title='{$lang->gitlab->group->manageMembers}' class='btn'");
            common::printLink('gitlab', 'editGroup', "gitlabID=$gitlabID&groupID=$gitlabGroup->id", "<i class='icon icon-edit'></i> ", '', "title='{$lang->gitlab->group->edit}' class='btn {$adminClass}'");
            if(common::hasPriv('gitlab', 'delete')) echo html::a($this->createLink('gitlab', 'deleteGroup', "gitlabID=$gitlabID&groupID=$gitlabGroup->id"), '<i class="icon-trash"></i>', 'hiddenwin', "title='{$lang->gitlab->deleteGroup}' class='btn {$adminClass}'");
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </form>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
