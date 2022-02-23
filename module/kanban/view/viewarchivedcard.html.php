<?php
/**
 * The viewarchivedcard file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     kanban
 * @version     $Id: viewarchivedcard.html.php 935 2021-12-22 15:44:24Z $
 * @link        https://www.zentao.net
 */
?>
<style>
#archivedCards {top: 50px; right: -400px; width: 400px; position: fixed; z-index: 1050; background-color: rgb(255,255,255);}
#archivedCards .panel .panel-body {overflow: auto; padding: 10px;}
#archivedCards .kanban-item {border: 1px solid #ddd; border-radius: 4px; padding: 5px;}
#archivedCards .kanban-item > .title {display: block; max-height: 38px; overflow: hidden; color: inherit; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}
#archivedCards .kanban-item > .title:hover {color: #2272eb}
#archivedCards .kanban-item > .info {margin-top: 5px; position: relative;}
#archivedCards .kanban-item > .info > .pri {height: 14px; border-width: 1px; font-size: 12px; line-height: 12px; min-width: 14px; padding: 0 1px;}
#archivedCards .kanban-item > .info > .time {margin-left: 10px; font-size: 12px}
#archivedCards .kanban-item > .info > .user {position: absolute; right: 0; top: 0}
#archivedCards .card-actions {position: relative; padding: 15px 10px; padding-top: 0px;}
#archivedCards .card-actions > .btn {display: block;}
[lang^='en'] #archivedCards .card-actions > .btn {width: 55px;}
#archivedCards .card-actions > .btn + .btn {margin-top: 10px;}
#archivedCards .info > .time {background-color: rgba(0, 0, 0, 0.15);}
#archivedCards .info > .users > span {display: inline-block; color: transparent; width: 2px; height: 2px; background-color: #8990a2; position:sticky; top: 3px; margin: 0 7px; border-radius: 50%; line-height: 32px;}
#archivedCards .info > .users > span:before {left: -4px; content: ''; display: block; position: absolute; width: 2px; height: 2px; background-color: #8990a2; top: 0px; border-radius: 50%;}
#archivedCards .info > .users > span:after {right: -4px; content: ''; display: block; position: absolute; width: 2px; height: 2px; background-color: #8990a2; top: 0px; border-radius: 50%;}
#archivedCards .info > .users .avatar {display: inline-block; position: relative; border-radius: 50%; top: -5px; margin:  5px; right: -7px; margin-left: -4px;}
#archivedCards .cardName {word-wrap: break-word;}
#archivedCards .card-item .red {background-color: #b10b0b;}
#archivedCards .card-item .yellow {background-color: #cfa227;}
#archivedCards .card-item .green {background-color: #2a5f29;}
#archivedCards .card-item .green-name .cardName {color: #2a5f29 !important;}
#archivedCards .card-item .has-color .cardName,
#archivedCards .card-item .has-color .actions .icon-more-v,
#archivedCards .card-item .has-color .info > .label-pri,
#archivedCards .card-item .has-color .info > .estimate,
#archivedCards .card-item .has-color .info > .label-light {color: #FFFFFF;}
#archivedCards .card-item .has-color .info > .label-pri {border-color: #FFFFFF;}
</style>
<div class='panel'>
  <div class='panel-heading text-center'>
    <strong><?php echo $lang->kanban->archivedCard;?></strong>
    <button type="button" class="close" aria-hidden="true">×</button>
  </div>
  <div class='panel-body'>
    <?php if(empty($cards)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->kanbancard->empty;?></span></p>
    </div>
    <?php else:?>
    <?php foreach($cards as $card):?>
    <div class='card-item' data-card='<?php echo $card->id;?>'>
      <div class='col-xs-10'>
        <?php
        $color = '';
        if($card->color == '#b10b0b') $color = 'has-color red';
        if($card->color == '#cfa227') $color = 'has-color yellow';
        if($card->color == '#2a5f29') $color = 'has-color green';
        ?>
        <?php
        $nameColor = '';
        if($card->status == 'done' and $card->color != '#2a5f29') $nameColor = 'green-name';
        ?>
        <?php
        $labelColor = 'background-color: #2a5f29';
        if($card->color == '#2a5f29') $labelColor = 'background-color: #FFFFFF; color: #2a5f29';
        ?>
        <div class="kanban-item <?php echo $nameColor;?> <?php echo $color;?>" data-id="<?php echo $card->id;?>">
        <?php if($card->status == 'done'):?>
        <div class="label" style="<?php echo $labelColor;?>"><?php echo $lang->kanban->finished;?></div>
        <?php endif;?>
          <?php echo html::a($this->createLink('kanban', 'viewCard', "cardID=$card->id", '', true), $card->name, '', "class='cardName iframe' data-toggle='modal' data-width='80%' title='$card->name'");?>
          <div class="info">
            <span class="pri label-pri label-pri-<?php echo $card->pri;?>"><?php echo $card->pri;?></span>
            <?php if($card->estimate and $card->estimate != 0) echo "<span class='text-gray'>{$card->estimate}h</span>";?>
            <?php if(helper::isZeroDate($card->end) and !helper::isZeroDate($card->begin)):?>
            <span class="time label label-light"><?php echo date("m/d", strtotime($card->begin)) . $lang->kanbancard->beginAB;?></span>
            <?php endif;?>
            <?php if(helper::isZeroDate($card->begin) and !helper::isZeroDate($card->end)):?>
            <span class="time label label-light"><?php echo date("m/d", strtotime($card->end)) . $lang->kanbancard->deadlineAB;?></span>
            <?php endif;?>
            <?php if(!helper::isZeroDate($card->begin) and !helper::isZeroDate($card->end)):?>
            <span class="time label label-light"><?php echo date("m/d", strtotime($card->begin)) . ' ~ ' . date("m/d", strtotime($card->end));?></span>
            <?php endif;?>
            <?php
            $assignedToList = explode(',', $card->assignedTo);
            $count          = 0;
            $members        = '';
            ?>
            <?php
            foreach($assignedToList as $index => $account)
            {
                if(!isset($users[$account]) or !isset($usersAvatar[$account]))
                {
                    unset($assignedToList[$index]);
                    continue;
                }
                $members .= $users[$account] . ',';
            }
            $userCount = count($assignedToList);
            ?>
            <?php if($userCount > 0):?>
            <div class='users pull-right' title="<?php echo trim($members, ',');?>">
            <?php
            foreach($assignedToList as $account)
            {
                if($count > 2) continue;
                echo html::smallAvatar(array('avatar' => $usersAvatar[$account], 'account' => $account), 'avatar-circle avatar-' . zget($userIdPairs, $account));
                $count ++;
            }
            ?>
            <?php if($count > 2) echo "<span>...</span>";?>
            </div>
            <?php endif;?>
          </div>
        </div>
      </div>
      <div class='col-xs-2 card-actions'>
        <?php
        $canRestore = commonModel::hasPriv('kanban', 'restoreCard');
        $canDelete  = commonModel::hasPriv('kanban', 'deleteCard');

        if($canRestore) echo html::a(inlink('restoreCard', "cardID={$card->id}"), $lang->kanban->restore, '', "class='btn btn-xs btn-primary' target='hiddenwin'");

        if($canDelete) echo html::a($this->createLink('kanban', 'deleteCard', "cardID=$card->id"), $lang->delete, '', "class='btn btn-xs delete-card' target='hiddenwin'");
        ?>
      </div>
    </div>
    <?php endforeach;?>
    <?php endif;?>
  </div>
</div>
<script>
$(function()
{
    $('#archivedCards .panel .close').click(function()
    {
        $('#archivedCards').animate({right: -400}, 500);
    });
})
</script>
