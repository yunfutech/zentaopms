<?php
/**
 * The testcase browse mobile view file of my module of ZenTaoPMS.
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
  echo html::a($this->createLink('my', 'testtask', 'type=wait'),  $lang->testtask->wait);
  echo html::a($this->createLink('my', 'testtask', 'type=done'),  $lang->testtask->done);
  echo html::a($this->createLink('my', 'testcase', "type=assigntome"),  $lang->testcase->assignToMe);
  echo html::a($this->createLink('my', 'testcase', "type=openedbyme"),  $lang->testcase->openedByMe);
  ?>
  <a class='moreMenu hidden' data-display='dropdown' data-placement='beside-bottom'><?php echo $lang->more;?></a>
  <div id='moreMenu' class='list dropdown-menu'></div>
</nav>

<div class='heading'>
  <div class='title'>
    <a id='sortTrigger' class='text-right sort-trigger' data-display data-target='#sortPanel' data-backdrop='true'><i class='icon icon-sort'></i>&nbsp;<span class='sort-name'><?php echo $lang->sort ?></span></a>
  </div>
</div>

<section id='page' class='section list-with-actions list-with-pager'>
  <?php $refreshUrl = $this->createLink('my', 'testcase', "type={$type}&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}");?>
  <div class='box' data-page='<?php echo $pager->pageID;?>' data-refresh-url='<?php echo $refreshUrl;?>'>
    <table class='table bordered'>
      <thead>
        <tr>
          <th><?php echo $lang->testcase->title;?> </th>
          <th class='text-center w-80px'><?php echo $lang->testcase->type;?> </th>
          <th class='text-center w-80px'><?php echo $lang->testcase->status;?> </th>
        </tr>
      </thead>
      <?php foreach($cases as $case):?>
      <tr class='text-center' data-url='<?php echo $this->createLink('testcase', 'view', "caseID={$case->id}");?>' data-id='<?php echo $case->id;?>'>
        <td class='text-left'><?php echo $case->title;?></td>
        <td><?php echo zget($lang->testcase->typeList, $case->type);?></td>
        <td class='testcase-<?php echo $case->status?>'><?php echo zget($lang->testcase->statusList, $case->status);?></td>
      </tr>
      <?php endforeach;?>
    </table>
  </div>

  <nav class='nav justify pager'>
    <?php $pager->show($align = 'justify');?>
  </nav>
</section>

<div class='list sort-panel hidden affix enter-from-bottom layer' id='sortPanel'>
  <?php
  $vars = "type={$type}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
  $sortOrders = array('id', 'pri', 'title', 'type', 'openedDate', 'openedBy');
  if($type != 'assigntome') $sortOrders[] = 'status';
  foreach ($sortOrders as $sortOrder)
  {
      commonModel::printOrderLink($sortOrder, $orderBy, $vars, '<i class="icon icon-sort-indicator"></i>' . ($lang->testcase->{$sortOrder}));
  }
  ?>
</div>

<script>
$('#menu > a').removeClass('active').filter('[href*="<?php echo $type ?>"]').addClass('active');
</script>
<?php include "../../common/view/m.footer.html.php"; ?>
