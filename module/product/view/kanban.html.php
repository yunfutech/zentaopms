<?php
/**
 * The html product kanban file of kanban method of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2021-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     ZenTaoPMS
 * @version     $Id
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kanban.html.php';?>
<?php if(empty($kanbanList)):?>
<div class="table-empty-tip cell">
  <p class="text-muted"><?php echo $lang->product->noProduct;?></p>
</div>
<?php else:?>
<div id='kanbanList'>
  <?php foreach($kanbanList as $type => $programs):?>
  <?php if(empty($programs)) continue;?>
  <div class='panel kanban-panel'>
    <div class='panel-heading'>
      <strong><?php echo $type == 'my' ? $lang->product->myProduct : $lang->product->otherProduct;?></strong>
    </div>
    <div class='panel-body'>
      <div id='kanban-<?php echo $type;?>' class='kanban'></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php
$kanbanColumns = array();
$kanbanColumns['unclosedProduct'] = array('name' => $lang->product->unclosedProduct, 'type' => 'unclosedProduct');
$kanbanColumns['unexpiredPlan']   = array('name' => $lang->product->unexpiredPlan, 'type' => 'unexpiredPlan');
if($config->systemMode == 'new')
{
    $kanbanColumns['doingProject']    = array('name' => $lang->product->doingProject, 'type' => 'doingProject');
    $kanbanColumns['doingExecution']  = array('name' => $lang->product->doingExecution, 'type' => 'doingExecution');
}
else
{
    $kanbanColumns['doingExecution']  = array('name' => $lang->product->doingClassicExecution, 'type' => 'doingExecution');
}
$kanbanColumns['normalRelease']   = array('name' => $lang->product->normalRelease, 'type' => 'normalRelease');
$userPrivs = array();
$userPrivs['product']     = common::hasPriv('product', 'browse');
$userPrivs['productplan'] = common::hasPriv('productplan', 'view');
$userPrivs['project']     = common::hasPriv('project', 'index');
$userPrivs['execution']   = common::hasPriv('execution', 'task');
$userPrivs['release']     = common::hasPriv('release', 'view');
js::set('isClassicMode',    $config->systemMode != 'new');
js::set('kanbanColumns',    array_values($kanbanColumns));
js::set('userPrivs',        $userPrivs);
js::set('kanbanList',       $kanbanList);
js::set('programList',      $programList);
js::set('productList',      $productList);
js::set('planList',         $planList);
js::set('projectList',      $projectList);
js::set('projectProduct',   $projectProduct);
js::set('latestExecutions', $latestExecutions);
js::set('classicExecution', $executionList);
js::set('releaseList',      $releaseList);
js::set('hourList',         $hourList);
js::set('doingText',        $lang->product->doing);
?>
<?php endif; ?>
<?php include '../../common/view/footer.html.php';?>
