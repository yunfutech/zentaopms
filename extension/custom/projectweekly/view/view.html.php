<?php
/**
 * The build view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: build.html.php 4262 2013-01-24 08:48:56Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../../module/common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('projectweekly', 'projectweeklylist', "projectID=" . $projectID), "<i class='icon icon-back icon-sm'></i> " . $lang->goback, '', "class='btn btn-primary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $weekly->id;?></span><span class="text" title='<?php echo $weekly->name;?>'><?php echo $weekly->name;?></span>
    </div>
  </div>
</div>
<div id='mainContent' class='main-content'>
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail no-padding">
        <h2><?php echo $lang->projectweekly->overview ?></h2>
        <div class="detail-content article-content no-margin no-padding">
            <?php
            {echo $weekly->overview;}
            ?>
        </div>
        <h2><?php echo $lang->projectweekly->question ?></h2>
        <div class="detail-content article-content no-margin no-padding">
            <?php
            {echo $weekly->question;}
            ?>
        </div>
        <h2><?php echo $lang->projectweekly->weekjob ?></h2>
        <div class="detail-content article-content no-margin no-padding">
            <?php
            {echo $weekly->weekjob;}
            ?>
        </div>
        <h2><?php echo $lang->projectweekly->weekplan ?></h2>
        <div class="detail-content article-content no-margin no-padding">
          <?php
            {echo $weekly->weekplan;}
          ?>
        </div>
      </div>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($this->createLink('projectweekly', 'projectweeklylist', "projectID=" . $projectID));?>
        <?php
          common::printIcon('projectweekly', 'edit', "weeklyID=$weekly->id&projectID=$projectID", $weekly, 'button', 'edit');
          ?>
      </div>
    </div>
  </div>
</div>
<?php include '../../../../module/common/view/footer.html.php';?>