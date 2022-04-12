<?php
/**
 * The batch create view of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     story
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->gitlab->bindUser;?></h2>
  </div>
  <form method='post' class='load-indicator main-form form-ajax' enctype='multipart/form-data'>
    <div class="table-responsive">
      <table class="table table-borderless w-600px">
        <thead>
          <tr>
            <th colspan='2'><?php echo $lang->gitlab->gitlabAccount;?></th>
            <th class='w-150px'><?php echo $lang->gitlab->zentaoAccount;?></th>
            <th class='w-150px'><?php echo $lang->gitlab->bindingStatus;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($gitlabUsers as $gitlabUser):?>
          <?php if(isset($gitlabUser->zentaoAccount)) continue;?>
          <?php echo html::hidden("gitlabUserNames[$gitlabUser->id]", $gitlabUser->realname);?>
          <tr>
            <td class='w-60px'><?php echo html::image($gitlabUser->avatar, "height=40");?></td>
            <td class='text-left'>
              <strong><?php echo $gitlabUser->realname;?></strong>
              <br>
              <?php echo $gitlabUser->account;?>
              <?php if($gitlabUser->email) echo " &lt;" . $gitlabUser->email . "&gt;";?>
            </td>
            <td><?php echo html::select("zentaoUsers[$gitlabUser->id]", $userPairs, '', "class='form-control select chosen'" );?></td>
            <td><?php echo $lang->gitlab->notBind;?></td>
          </tr>
          <?php endforeach;?>
          <?php foreach($gitlabUsers as $gitlabUser):?>
          <?php if(!isset($gitlabUser->zentaoAccount)) continue;?>
          <?php echo html::hidden("gitlabUserNames[$gitlabUser->id]", $gitlabUser->realname);?>
          <tr>
            <td class='w-60px'><?php echo html::image($gitlabUser->avatar, "height=40");?></td>
            <td>
              <strong><?php echo $gitlabUser->realname;?></strong>
              <br>
              <?php echo $gitlabUser->account;?>
              <?php if($gitlabUser->email) echo " &lt;" . $gitlabUser->email . "&gt;";?>
            </td>
            <td><?php echo html::select("zentaoUsers[$gitlabUser->id]", $userPairs, $gitlabUser->zentaoAccount, "class='form-control select chosen'" );?></td>
            <td>
              <?php if(isset($bindedUsers[$gitlabUser->zentaoAccount])):?>
              <?php $zentaoAccount = zget($userPairs, $gitlabUser->zentaoAccount, '');?>
              <?php if(!empty($zentaoAccount)):?>
              <?php echo $lang->gitlab->binded;?>
              <?php else:?>
              <?php echo '<span class="text-red">' . $lang->gitlab->bindedError . '</span>';?>
              <?php endif;?>
              <?php else:?>
              <?php echo $lang->gitlab->notBind;?>
              <?php endif;?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-center form-actions">
              <?php echo html::submitButton();?>
              <?php if(!isonlybody()) echo html::a(inlink('browse', ""), $lang->goback, '', 'class="btn btn-wide"');?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
