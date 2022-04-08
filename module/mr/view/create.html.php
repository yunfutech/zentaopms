<?php

/**
 * The create view file of mr module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @author      Guodong
 * @package     mr
 * @version     $Id: create.html.php $
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col main-content'>
    <div class='center-block'>
      <div class='main-header'>
        <h2><?php echo $lang->mr->create;?></h2>
      </div>
      <form id='mrForm' method='post' class='form-ajax'>
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->gitlab->common;?></th>
            <td class='required'><?php echo html::select('gitlabID', array('') + $gitlabHosts, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th style="white-space: nowrap;"><?php echo $lang->mr->sourceProject;?></th>
            <td>
              <div class='input-group required'>
                <?php echo html::select('sourceProject', array(''), '', "class='form-control chosen'");?>
                <span class='input-group-addon fix-border'><?php echo $lang->mr->sourceBranch ?></span>
                <?php echo html::select('sourceBranch', array(''), '', "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th style="white-space: nowrap;"><?php echo $lang->mr->targetProject;?></th>
            <td>
              <div class='input-group required'>
                <?php echo html::select('targetProject', array(''), '', "class='form-control chosen'");?>
                <span class='input-group-addon fix-border'><?php echo $lang->mr->targetBranch ?></span>
                <?php echo html::select('targetBranch', array(''), '', "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->mr->title;?></th>
            <td class='required'><?php echo html::input('title', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->mr->description;?></th>
            <td colspan='1'><?php echo html::textarea('description', '', "rows='3' class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->devops->repo;?></th>
            <td colspan='1' class='required'><?php echo html::select('repoID', array(''), '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->mr->removeSourceBranch;?></th>
            <td colspan='1'>
              <div class="checkbox-primary">
                <input type="checkbox" name="removeSourceBranch" value="1" id="removeSourceBranch">
                <label for="removeSourceBranch"></label>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->mr->needCI;?></th>
            <td colspan='1'>
              <div class="checkbox-primary">
                <input type="checkbox" name="needCI" value="1" id="needCI">
                <label for="needCI"></label>
              </div>
            </td>
          </tr>
          <tr class='hidden'>
            <th><?php echo $lang->job->common;?></th>
            <td colspan='1' class='required'><?php echo html::select('jobID', array(''), '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->mr->assignee;?></th>
            <td><?php echo html::select('assignee', $users, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <td colspan='2' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php if(!isonlybody()) echo html::a(inlink('browse', ""), $lang->goback, '', 'class="btn btn-wide"');?>
            </td>
            <td></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
