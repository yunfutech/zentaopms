<?php
/**
 * The to20 view file of install module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     install
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.lite.html.php';?>
<div class='container'>
  <div class='panel' style='padding:50px 300px'>
    <form method='post'>
      <h1 class='text-center'><?php echo $title;?></h1>
      <div class='panel-body'>
        <?php echo $lang->install->introductionContent;?>
        <video class='hidden' src="<?php echo $lang->install->guideVideo;?>"  width="100%" controls ="controls"></video>
        <div class='text-center'>
          <h2><?php echo $lang->install->howToUse;?></h2>
          <?php $systemMode = isset($lang->upgrade->to15Mode['classic']) ? 'classic' : 'new';?>
          <div class='select-mode'><?php echo html::radio('mode', $lang->install->modeList, $systemMode);?></div>
          <div id='selectedModeTips' class='text-info'><?php echo $lang->upgrade->selectedModeTips[$systemMode];?></div>
        </div>
      </div>
      <hr/>
      <div class='panel-footer text-center'>
        <?php echo html::submitButton($lang->install->next);?>
      </div>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.lite.html.php';?>
