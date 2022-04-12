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
<?php js::set('vars', "keyword=%s&orderBy=id_desc&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID=1")?>
<?php js::set('gitlabID', $gitlabID)?>
<div id="mainMenu" class="clearfix">
  <div class='pull-left'>
    <?php echo html::a($this->createLink('gitlab', 'browse'), "<i class='icon icon-back icon-sm'></i> " . $lang->goback, '', "class='btn btn-secondary'");?>
  </div>
  <div id="sidebarHeader">
    <div class="title"><?php echo $this->lang->gitlab->common . ':' . $gitlab->name; ?></div>
  </div>
  <div class="btn-toolbar pull-left">
    <div>
      <form id='gitlabprojectForm' method='post'>
      <?php echo html::input('keyword', $keyword, "class='form-control' placeholder='{$lang->gitlab->placeholderSearch}' style='display: inline-block;width:auto;margin:0 10px'");?>
      <a id="projectSearch" class="btn btn-primary"><?php echo $lang->gitlab->search?></a>
      </form>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('gitlab', 'createProject')) common::printLink('gitlab', 'createProject', "gitlabID=$gitlabID", "<i class='icon icon-plus'></i> " . $lang->gitlab->project->create, '', "class='btn btn-primary'");?>
  </div>
</div>
<?php if(empty($gitlabProjectList)):?>
<div class="table-empty-tip">
  <p>
    <span class="text-muted"><?php echo $lang->noData;?></span>
    <?php if(empty($keyword) and common::hasPriv('gitlab', 'createProject')):?>
    <?php echo html::a($this->createLink('gitlab', 'createProject', "gitlabID=$gitlabID"), "<i class='icon icon-plus'></i> " . $lang->gitlab->project->create, '', "class='btn btn-info'");?>
    <?php endif;?>
  </p>
</div>
<?php else:?>
<div id='mainContent' class='main-row'>
  <form class='main-table' id='ajaxForm' method='post'>
    <table id='gitlabProjectList' class='table has-sort-head table-fixed'>
      <?php $vars = "gitlabID={$gitlabID}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
      <thead>
        <tr>
          <th class='c-id'><?php common::printOrderLink('id', $orderBy, $vars, $lang->gitlab->id);?></th>
          <th class='c-name text-left'><?php common::printOrderLink('name', $orderBy, $vars, $lang->gitlab->project->name);?></th>
          <th class='text-left'></th>
          <th class='text-left'><?php echo $lang->gitlab->lastUpdate;?></th>
          <th class='c-actions-6'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($gitlabProjectList as $id => $gitlabProject): ?>
        <tr class='text'>
          <td class='text-center'><?php echo $gitlabProject->id;?></td>
          <td class='text-c-name' title='<?php echo $gitlabProject->name;?>'><?php echo $gitlabProject->name_with_namespace;?></td>
          <td class='text text-c-counts'>
            <span title="<?php echo $lang->gitlab->project->star;?>"><i class="icon icon-star"></i> <?php echo $gitlabProject->star_count;?></span>
            <span title="<?php echo $lang->gitlab->project->fork;?>"><i class="icon icon-code-fork"></i> <?php echo $gitlabProject->forks_count;?></span>
          </td>
          <td class='text' title='<?php echo substr($gitlabProject->last_activity_at, 0, 10);?>'><?php echo substr($gitlabProject->last_activity_at, 0, 10);?></td>
          <td class='c-actions text-left'>
            <?php
            $maintainerClass  = $gitlabProject->isMaintainer ? '' : 'disabled';
            $ownerClass       = $gitlabProject->adminer ? '' : 'disabled';
            $emptyBranchClass = $gitlabProject->default_branch ? '' : 'disabled';
            common::printLink('gitlab', 'browseBranch', "gitlabID=$gitlabID&projectID=$gitlabProject->id", "<i class='icon icon-treemap'></i> ", '', "title='{$lang->gitlab->browseBranch}' class='btn {$emptyBranchClass}'");
            common::printLink('gitlab', 'browseBranchPriv', "gitlabID=$gitlabID&projectID=$gitlabProject->id", "<i class='icon icon-branch-lock'></i> ", '', "title='{$lang->gitlab->branch->accessLevel}' class='btn {$maintainerClass} {$emptyBranchClass}'");
            common::printLink('gitlab', 'browseTag', "gitlabID=$gitlabID&projectID=$gitlabProject->id", "<i class='icon icon-tag'></i> ", '', "title='{$lang->gitlab->browseTag}' class='btn {$emptyBranchClass}'");
            common::printLink('gitlab', 'browseTagPriv', "gitlabID=$gitlabID&projectID=$gitlabProject->id", "<i class='icon icon-tag-lock'></i> ", '', "title='{$lang->gitlab->browseTagPriv}' class='btn {$maintainerClass} {$emptyBranchClass}'");
            common::printLink('gitlab', 'editProject', "gitlabID=$gitlabID&projectID=$gitlabProject->id", "<i class='icon icon-edit'></i> ", '', "title='{$lang->gitlab->project->edit}' class='btn {$ownerClass}'");
            if(common::hasPriv('gitlab', 'delete')) echo html::a($this->createLink('gitlab', 'deleteProject', "gitlabID=$gitlabID&projectID=$gitlabProject->id"), '<i class="icon-trash"></i>', 'hiddenwin', "title='{$lang->gitlab->deleteProject}' class='btn {$ownerClass}'");
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <?php if($gitlabProjectList):?>
    <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
    <?php endif;?>
  </form>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
