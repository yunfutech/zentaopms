<?php
/**
 * The create branch view file of gitlab module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@zcorp.ltd>
 * @package     gitlab
 * @version     $Id$
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col main-content'>
    <div class='center-block'>
      <div class='main-header'>
        <h2><?php echo $lang->gitlab->createBranch;?></h2>
      </div>
      <form id='gitlabForm' method='post' class='form-ajax'>
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->gitlab->branch->name;?></th>
            <td><?php echo html::input('branch', '', "class='form-control'");?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->gitlab->branch->from;?></th>
            <td><?php echo html::select('ref', $branchPairs, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th></th>
            <td class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php $backLink = $this->session->gitlabBranchList ? $this->session->gitlabBranchList : inlink('browseBranch', "gitlibID=$gitlabID&projectID=$projectID");?>
              <?php if(!isonlybody()) echo html::a($backLink, $lang->goback, '', 'class="btn btn-wide"');?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
