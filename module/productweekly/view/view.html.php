<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/markdown.html.php';?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('product', 'weekly', "productID=" . $productID), "<i class='icon icon-back icon-sm'></i> " . $lang->goback, '', "class='btn btn-primary'");?>
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
        <div class="detail-content article-content no-margin no-padding">
          <?php
            {echo $weekly->content;}
          ?>
        </div>
      </div>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($this->createLink('product', 'weekly', "productID=" . $productID));?>
        <?php
          common::printIcon('productWeekly', 'edit', "weeklyID=$weekly->id&productID=$productID", $weekly, 'button', 'edit');
          ?>
      </div>
    </div>
  </div>
</div>

<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>
