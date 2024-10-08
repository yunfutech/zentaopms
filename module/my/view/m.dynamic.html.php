<?php
/**
 * The dynamic browse mobile view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2016 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Fei Chen <chenfei@cnezsoft.com>
 * @package     my
 * @version     $Id: index.html.php 3830 2016-05-18 09:34:17Z liugang $
 * @link        http://www.zentao.net
 */
?>

<?php
$bodyClass = 'with-menu-top';
include "../../common/view/m.header.html.php";
?>

<nav id='menu' class='menu nav affix dock-top canvas'>
  <?php 
  echo html::a(inlink('dynamic', "type=today"),      $lang->action->dynamic->today);
  echo html::a(inlink('dynamic', "type=yesterday"),  $lang->action->dynamic->yesterday);
  echo html::a(inlink('dynamic', "type=twodaysago"), $lang->action->dynamic->twoDaysAgo);
  echo html::a(inlink('dynamic', "type=thisweek"),   $lang->action->dynamic->thisWeek);
  echo html::a(inlink('dynamic', "type=lastweek"),   $lang->action->dynamic->lastWeek);
  echo html::a(inlink('dynamic', "type=thismonth"),  $lang->action->dynamic->thisMonth);
  echo html::a(inlink('dynamic', "type=lastmonth"),  $lang->action->dynamic->lastMonth);
  echo html::a(inlink('dynamic', "type=all"),        $lang->action->dynamic->all);
  ?>
  <a class='moreMenu hidden' data-display='dropdown' data-placement='beside-bottom'><?php echo $lang->more;?></a>
  <div id='moreMenu' class='list dropdown-menu'></div>
</nav>

<div class='heading'>
  <nav class='nav fluid'>
    <a id='sortTrigger' class='text-right sort-trigger' data-display data-target='#sortPanel' data-backdrop='true'><i class='icon icon-sort'></i>&nbsp;<span class='sort-name'><?php echo $lang->sort ?></span></a>
  </nav>
</div>

<section id='page' class='section list-with-actions list-with-pager'>
  <?php $refreshUrl = $this->createLink('my', 'dynamic', "type=$type&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}");?>
  <div class='box' data-page='<?php echo $pager->pageID ?>' data-refresh-url='<?php echo $refreshUrl ?>'>
    <table class='table bordered'>
      <?php $firstAction = '';?>
      <?php foreach($dateGroups as $date => $actions): ?>
      <?php if($direction == 'next') $actions = array_reverse($actions);?>
      <?php foreach($actions as $action): ?>
      <?php if(empty($firstAction)) $firstAction = $action;?>
      <tr data-id='<?php echo $action->id;?>'>
        <td class='w-70px'><?php echo isset($users[$action->actor]) ? $users[$action->actor] : $action->actor; ?></td>
        <td>
          <?php echo $action->actionLabel;?>
          <?php if(strpos(',login,logout,', ",$action->action,") === false):?>
          <?php echo ' ' . $action->objectLabel;?>
          <a class='text-link' href='<?php echo $action->objectLink ?>'><?php echo $action->objectName ?></a>
          <?php endif;?>
        </td>
        <td class='w-120px'><?php echo $action->date ?></td>
      </tr>
      <?php endforeach;?>
      <?php endforeach;?>
    </table>
  </div>
  <nav class='nav justify pager'>
    <?php if(!empty($firstAction)):?>
    <?php
    $firstDate = date('Y-m-d', strtotime($firstAction->originalDate) + 24 * 3600);
    $lastDate  = substr($action->originalDate, 0, 10);
    $hasPre    = $this->action->hasPreOrNext($firstDate, 'pre');
    $hasNext   = $this->action->hasPreOrNext($lastDate, 'next');
    $preLink   = $hasPre ? inlink('dynamic', "type=$type&recTotal={$pager->recTotal}&date=" . strtotime($firstDate) . '&direction=pre') : 'javascript:;';
    $nextLink  = $hasNext ? inlink('dynamic', "type=$type&recTotal={$pager->recTotal}&date=" . strtotime($lastDate) . '&direction=next') : 'javascript:;';
    ?>
    <?php if($hasPre or $hasNext):?>
    <ul class="pager pager-justify">
      <li class="previous <?php if($hasPre == false):?> disabled <?php endif;?>"><a href="<?php echo $preLink?>">« 上一页</a></li>
      <li class="caption"><strong></strong> <strong></strong> </li>
      <li class="next <?php if($hasNext == false):?> disabled <?php endif;?>"><a href="<?php echo $nextLink;?>">下一页 »</a></li>
    </ul>
    <?php endif;?>
    <?php endif;?>
  </nav>
</section>

<div class='list sort-panel hidden affix enter-from-bottom layer' id='sortPanel'>
  <?php
  $vars = "type=$type&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
  $sortOrders = array('id', 'date', 'actor', 'action', 'objectType');
  foreach ($sortOrders as $sortOrder)
  {
      commonModel::printOrderLink($sortOrder, $orderBy, $vars, '<i class="icon icon-sort-indicator"></i>' . ($sortOrder == 'id' ? $lang->action->actionID : $lang->action->{$sortOrder}));
  }
  ?>
</div>

<script>
$('#menu > a').removeClass('active').filter('[href*="<?php echo $type ?>"]').addClass('active');
</script>
<?php include "../../common/view/m.footer.html.php"; ?>
