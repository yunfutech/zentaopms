<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/markdown.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a('javascript:history.go(-1)', "<i class='icon icon-back icon-sm'></i> " . $lang->goback, '', "class='btn btn-primary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $userlog->id;?></span><span class="text" title='<?php echo $userlog->name;?>'><?php echo $userlog->name;?></span>
    </div>
  </div>
</div>
<div id='mainContent' class='main-content'>
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail no-padding">
        <div class="detail-content article-content no-margin no-padding">
          <?php
            {echo $userlog->content; }
          ?>
        </div>
      </div>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack('javascript:history.go(-1)');?>
        <?php
          common::printIcon('userlog', 'edit', "userlogId=$userlog->id", $userlog);
          ?>
      </div>
    </div>
  </div>
</div>

<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>
