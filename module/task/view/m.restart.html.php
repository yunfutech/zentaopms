<?php
/**
 * The restart mobile view file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Fei Chen <chenfei@cnezsoft.com>
 * @package     task
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<div class='heading divider'>
  <span class='title'><strong><?php echo $lang->task->restart;?></strong> #<?php echo $task->id . ' ' . $task->name;?></span>
  <nav class='nav'>
    <a data-dismiss='display'><i class='icon-remove muted'></i></a>
  </nav>
</div>
<form class='has-padding content' method='post' target='hiddenwin' action='<?php echo $this->createLink('task', 'restart', "taskID=$task->id")?>' id='restartForm' data-form-refresh='#page'>
  <div class='control'>
    <label for='realStarted'><?php echo $lang->task->realStarted;?></label>
    <input type='date' class='input' id='realStarted' name='realStarted' value='<?php echo $task->realStarted?>'>
  </div>
  <div class='control'>
    <label for='consumed'><?php echo $lang->task->consumed;?></label>
    <?php echo html::input('consumed', $task->consumed, "class='input'");?></div>
  </div>
  <div class='control'>
    <label for='left'><?php echo $lang->task->left;?></label>
    <?php echo html::input('left', $task->left, "class='input'");?></div>
  </div>
  <div class='control'>
    <label for='comment'><?php echo $lang->comment;?></label>
    <?php echo html::textarea('comment', '', 'rows=2 class="textarea"');?></td>
  </div>
</form>
<div class='footer has-padding'>
  <button type='button' id='submitButton' class='btn primary'><?php echo $lang->save ?></button>
</div>
<script>
$(function()
{
    $('#submitButton').click(function(){$('#restartForm').submit()});
})
</script>
