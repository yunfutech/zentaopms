<?php
/**
 * The edit view file of sonarqube module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Zeng<zenggang@cnezsoft.com>
 * @package     sonarqube
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-row'>
  <div class='main-col main-content'>
    <div class='center-block'>
      <div class='main-header'>
        <h2><?php echo $lang->sonarqube->editServer;?></h2>
      </div>
      <form id='sonarqubeForm' method='post' class='form-ajax'>
        <table class='table table-form'>
          <tr>
            <th class="c-name"><?php echo $lang->sonarqube->name;?></th>
            <td class="c-input"><?php echo html::input('name', isset($sonarqube->name) ? $sonarqube->name : '', "class='form-control' placeholder='{$lang->sonarqube->placeholder->name}'");?></td>
            <td class="tips-git"></td>
          </tr>
          <tr>
            <th><?php echo $lang->sonarqube->url;?></th>
            <td><?php echo html::input('url', isset($sonarqube->url) ? $sonarqube->url : '', "class='form-control' placeholder='{$lang->sonarqube->placeholder->url}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->sonarqube->account;?></th>
            <td><?php echo html::input('account', isset($sonarqube->account) ? $sonarqube->account : '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->sonarqube->password;?></th>
            <td><?php echo html::password('password', isset($sonarqube->password) ? $sonarqube->password : '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th></th>
            <td class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php if(!isonlybody()) echo html::a(inlink('browse', ""), $lang->goback, '', 'class="btn btn-wide"');?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>