<?php
/**
 * The space file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     kanban
 * @version     $Id: space.html.php 935 2021-12-07 14:31:24Z $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix table-row">
  <div class="btn-toolBar pull-left">
    <?php foreach($lang->kanbanspace->featureBar as $key => $label):?>
    <?php $active = $browseType == $key ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($browseType == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a(inlink('space', "browseType=$key"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
    <?php echo html::checkbox('showClosed', array('1' => $lang->kanban->showClosed), '', $this->cookie->showClosed ? 'checked=checked' : '');?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(!empty($unclosedSpace) and $browseType != 'involved') common::printLink('kanban', 'create', "spaceID=0&type={$browseType}", '<i class="icon icon-plus"></i> ' . $lang->kanban->create, '', 'class="btn btn-secondary iframe" data-width="75%"', '', true);?>
    <?php if($browseType != 'involved')common::printLink('kanban', 'createSpace', "type={$browseType}", '<i class="icon icon-plus"></i> ' . $lang->kanban->createSpace, '', 'class="btn btn-primary iframe" data-width="75%"', '', true);?>
  </div>
</div>
<div id='mainContent'>
  <?php if(empty($spaceList)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->kanbanspace->empty;?></span></p>
  </div>
  <?php else:?>
  <?php foreach($spaceList as $space):?>
  <?php $kanbanCount = 1;?>
  <div class='row cell' id='spaceList'>
    <div class='space'>
      <div class='row menu'>
        <div class='spaceTitle pull-left'>
          <div><i class='icon-cube'></i></div>
          <div>
            <h4 title="<?php echo $space->name;?>">
              <?php if($space->status == 'closed'):?>
              <span class="label label-closed"><?php echo $lang->kanban->closed;?></span>
              <?php endif;?>
              <?php echo $space->name;?>
            </h4>
          </div>
        </div>
        <div class='spaceActions pull-right'>
          <?php $class = $space->status == 'closed' ? 'disabled' : '';?>
          <?php if($space->status != 'closed' and $browseType != 'involved' and !empty($unclosedSpace)) common::printLink('kanban', 'create', "spaceID={$space->id}&type={$space->type}", '<i class="icon icon-plus"></i> ' . $lang->kanban->create, '', "class='iframe' data-width='75%'", '', true);?>
          <?php common::printLink('kanban', 'editSpace', "spaceID={$space->id}", '<i class="icon icon-cog-outline"></i> ' . $lang->kanban->setting, '', "class='iframe' data-width='75%'", '', true);?>
          <?php common::printLink('kanban', 'closeSpace', "spaceID={$space->id}", '<i class="icon icon-off"></i> ' . $lang->close, '', "class='iframe {$class}'", '', true);?>
          <?php common::printLink('kanban', 'deleteSpace', "spaceID={$space->id}", '<i class="icon icon-trash"></i> ' . $lang->delete, 'hiddenwin', '', '', true);?>
        </div>
      </div>
      <?php if(isset($space->kanbans)):?>
      <div class='kanbans row'>
      <?php foreach($space->kanbans as $kanbanID => $kanban):?>
        <div class='col' data-id='<?php echo $kanbanID?>'>
          <div class='panel' data-url='<?php echo $this->createLink('kanban', 'view', "kanbanID=$kanbanID");?>'>
            <div class='panel-heading'>
              <div class='kanban-name'>
                <?php if($kanban->status == 'closed'):?>
                <span class="label label-closed"><?php echo $lang->kanban->closed;?></span>
                <?php endif;?>
                <strong title='<?php echo $kanban->name;?>'><?php echo $kanban->name;?></strong>
              </div>
              <?php $canActions = (common::hasPriv('kanban','edit') or common::hasPriv('kanban','close') or common::hasPriv('kanban','delete'));?>
              <?php if($canActions):?>
              <div class='kanban-actions kanban-actions<?php echo $kanbanID;?>'>
                <div class='dropdown'>
                  <?php echo html::a('javascript:;', "<i class='icon icon-ellipsis-v'></i>", '', "data-toggle='dropdown' class='btn btn-link'");?>
                  <ul class='dropdown-menu <?php echo $kanbanCount % 4 == 0 ? 'pull-left' : 'pull-right';?>'>
                    <?php
                    if(common::hasPriv('kanban','edit'))
                    {
                        echo '<li>';
                        common::printLink('kanban', 'edit',   "kanbanID={$kanban->id}", '<i class="icon icon-edit"></i> ' . $lang->kanban->edit, '', "class='iframe' data-width='75%'", '', true);
                        echo '</li>';
                    }
                    if(common::hasPriv('kanban','close'))
                    {
                        $class = $kanban->status == 'closed' ? 'disabled' : '';
                        echo "<li class='{$class}'>";
                        common::printLink('kanban', 'close',  "kanbanID={$kanban->id}", '<i class="icon icon-off"></i> ' . $lang->kanban->close, '', "class='iframe {$class}' data-width='75%'", '', true);
                        echo '</li>';
                    }
                    if(common::hasPriv('kanban','delete'))
                    {
                        echo '<li>';
                        common::printLink('kanban', 'delete', "kanbanID={$kanban->id}", '<i class="icon icon-trash"></i> ' . $lang->kanban->delete, 'hiddenwin');
                        echo '</li>';
                    }
                    ?>
                  </ul>
                </div>
              </div>
              <?php endif;?>
              <?php $kanbanCount ++;?>
            </div>
            <div class='panel-body'>
              <div class='kanban-desc' title="<?php echo strip_tags(htmlspecialchars_decode($kanban->desc));?>"><?php echo strip_tags(htmlspecialchars_decode($kanban->desc));?></div>
              <div class='kanban-footer'>
              <?php $count     = 0;?>
              <?php $teamPairs = array_filter(explode(',', $kanban->team));?>
              <?php
              foreach($teamPairs as $index => $team)
              {
                  if(!isset($users[$team])) unset($teamPairs[$index]);
              }
              ?>
              <?php $teamCount = count($teamPairs);?>
                <div class="clearfix">
                  <?php if(!empty($teamPairs)):?>
                  <div class='kanban-members pull-left'>
                    <?php foreach($teamPairs as $member):?>
                    <?php
                    if($count > 2) break;
                    if(!isset($users[$member]))
                    {
                        $teamCount --;
                        continue;
                    }
                    $count ++;
                    ?>
                    <div title="<?php echo $users[$member];?>">
                      <?php echo html::middleAvatar(array('avatar' => $usersAvatar[$member], 'account' => $member, 'name' => $users[$member]), 'avatar-circle avatar-' . zget($userIdPairs, $member)); ?>
                    </div>
                    <?php endforeach;?>
                    <?php if($teamCount > 3):?>
                    <?php if($teamCount > 4) echo '<span>…</span>';?>
                    <div title="<?php echo $users[end($teamPairs)];?>">
                      <?php echo html::middleAvatar(array('avatar' => $usersAvatar[end($teamPairs)], 'account' => end($teamPairs), 'name' => $users[end($teamPairs)]), 'avatar-circle avatar-' . zget($userIdPairs, end($teamPairs))); ?>
                    </div>
                    <?php endif;?>
                  </div>
                  <?php endif;?>
                  <div class='kanban-members-total pull-left'><?php echo sprintf($lang->kanban->teamSumCount, $teamCount);?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach;?>
      </div>
      <?php else:?>
      <div class="table-empty-tip <?php if($this->cookie->theme == 'blue') echo 'noBorder';?>">
        <p><span class="text-muted"><?php echo $lang->kanban->empty;?></span></p>
      </div>
      <?php endif;?>
    </div>
  </div>
  <?php endforeach;?>
  <?php endif;?>
</div>
<?php if(!empty($spaceList)):?>
<div id='spaceListFooter' class='table-footer'>
  <?php $pager->show('right', 'pagerjs');?>
</div>
<?php endif?>
<?php include '../../common/view/footer.html.php';?>
