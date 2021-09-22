<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php $browseLink = $this->createLink('product', 'producttarget', "productID=" . $productID)?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $producttarget->id?></span>
      <span class="text"><?php echo $producttarget->name?></span>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <?php
    $link = $this->createLink('producttarget', 'edit', "producttargetID=$producttarget->id&productID=$productID");
    if(common::hasPriv('producttarget', 'edit')) echo html::a($link, "<i class='icon icon-edit'></i> {$lang->producttarget->edit}", '', "class='btn btn-primary'");
    ?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-2">
    <div id="side" class='cell'>
      <summary class="detail-title"><?php echo $lang->producttarget->base;?></summary>
      <div class="detail-content">
        <table class='table table-data'>
          <tr>
            <th><?php echo $lang->producttarget->name;?></th>
            <td><?php echo $producttarget->name;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->productName;?></th>
            <td title="<?php echo $product->name;?>"><?php echo html::a($this->createLink('product', 'browse', "productID=$productID"), $product->name);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->lastTarget;?></th>
            <td><?php echo $producttarget->lastTarget;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->target;?></th>
            <td><?php echo $producttarget->target; ?> </td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->confidence;?></th>
            <td><?php echo zget($lang->producttarget->confidenceList, $producttarget->confidence);?> </td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->middle;?></th>
            <td><?php echo $producttarget->middle; ?> </td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->performance;?></th>
            <td><?php echo $producttarget->performance; ?> </td>
          </tr>
          <tr>
            <th><?php echo $lang->producttarget->cause;?></th>
            <td><?php echo $producttarget->cause; ?> </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="main-col col-10">
    <div class='cell'>
      <?php if(empty($producttargetitems)):?>
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->producttargetitem->noData;?></span></p>
      </div>
      <?php else:?>
      <form class="main-table table-task">
        <table class="table has-sort-head table-fixed">
          <?php $vars = "producttargetID=$producttarget->id&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
          <thead>
            <tr>
              <th class="c-id w-30px">
                <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
              </th>
              <th class='w-150px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->producttargetitem->name);?></th>
              <th class='w-150px'><?php common::printOrderLink('intro', $orderBy, $vars, $lang->producttargetitem->intro);?></th>
              <th class='w-150px'><?php common::printOrderLink('acceptance', $orderBy, $vars, $lang->producttargetitem->acceptance);?></th>
              <th class='w-100px'><?php common::printOrderLink('completion', $orderBy, $vars, $lang->producttargetitem->completion);?></th>
              <th class='c-actions-1'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($producttargetitems as $producttargetitem):?>
            <tr>
              <td class="c-id"><?php echo $producttargetitem->id?></td>
              <td><?php echo $producttargetitem->name?></td>
              <td><?php echo $producttargetitem->intro?></td>
              <td><?php echo $producttargetitem->acceptance?></td>
              <td><?php echo $producttargetitem->completion?></td>
              <td class='c-actions'>
                <?php
                  if(common::hasPriv('producttarget', 'editItem')) common::printIcon('producttarget', 'editItem', "producttargetitemID=$producttargetitem->id&producttargetID=$producttarget->id", $producttarget, 'list', 'edit');
                ?>
                <?php
                  if(common::hasPriv('producttarget', 'deleteItem')) common::printIcon('producttarget', 'deleteItem', "producttargetitemID=$producttargetitem->id&producttargetID=$producttarget->id", $producttarget, 'list', 'trash', 'hiddenwin');
                ?>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
        <div class="table-footer">
          <?php $pager->show('right', 'pagerjs');?>
        </div>
      </form>
      <?php endif;?>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php
        common::printIcon('producttarget', 'batchCreateItem', "producttargetID=$producttarget->id", $task, 'button', 'plus', '', '', false, '', $lang->producttargetitem->bulkCreate);
        ?>
      </div>
    </div>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
