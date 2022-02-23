<?php
/**
 * The admin view file of owt module of XXB.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZOSL (https://zpl.pub/page/zoslv1.html)
 * @author      Wenrui LI <liwenrui@easycorp.ltd>
 * @package     owt
 * @version     $Id$
 * @link        https://xuanim.com
 */
?>
<?php
include '../../common/view/header.html.php';
?>
<div class='panel-content'>
  <ul class="nav nav-tabs">
    <?php foreach($this->config->owt->owtTabList as $tabIndex):?>
    <li class="<?php if($type == $tabIndex || ($type == 'edit' && $tabIndex == 'server')) echo 'active';?>">
      <a href="<?php echo '#' . $tabIndex . 'Content'?>" data-toggle="tab"><?php echo $lang->owt->$tabIndex;?></a>
    </li>
    <?php endforeach;?>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade <?php echo $type == 'server' || $type == 'edit' ? 'active in' : '';?>" id="serverContent">
      <form method='post' id='owtAdminForm' class='form-ajax<?php if($enabled) echo ' owt-enabled';?>'>
        <table class='table table-form'>
          <tr>
            <th class="w-120px"><?php echo $lang->owt->enabled;?></th>
            <td class="w-400px">
              <?php if($type != 'edit'): ?>
              <div class="checkbox-primary disabled <?php if($enabled) echo 'checked';?>">
                <label><?php echo $lang->owt->enabledTip;?></label>
              </div>
              <?php else: ?>
              <div class="checkbox-primary">
                <input type="checkbox" name="enabled" id='enabled' value="true" <?php if($enabled) echo 'checked';?> <?php if($type != 'edit') echo 'disabled';?>>
                <label for='enabled'><?php echo $lang->owt->enabledTip;?></label>
              </div>
              <?php endif; ?>
            </td>
            <td></td>
          </tr>
          <?php if($type == 'edit' || $enabled): ?>
          <tr class='owt-row'>
            <th class="w-120px"><?php echo $lang->owt->serverAddr;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'){?>
                <div class='required required-wrapper'></div>
                <?php echo html::input('serverAddr', $serverAddr, "class='form-control'");?>
              <?php } else { echo empty($serverAddr) ? $lang->owt->notset : $serverAddr; }?>
            </td>
            <td><?php echo $lang->owt->serverAddrTip;?></td>
          </tr>
          <tr class='owt-row'>
            <th class="w-120px"><?php echo $lang->owt->https;?></th>
            <td class="w-400px">
              <?php if($type != 'edit'): ?>
              <div class="checkbox-primary disabled <?php if($https) echo 'checked';?>">
                <label><?php echo $lang->owt->httpsTip;?></label>
              </div>
              <?php else: ?>
              <div class="checkbox-primary">
                <input type="checkbox" name="https" id='https' value="true" <?php if($https) echo 'checked';?> <?php if($type != 'edit') echo 'disabled';?>>
                <label for='https'><?php echo $lang->owt->httpsTip;?></label>
              </div>
              <?php endif; ?>
            </td>
            <td></td>
          </tr>
          <tr class='owt-row'>
            <th class="w-120px"><?php echo $lang->owt->apiPort;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'){?>
                <div class='required required-wrapper'></div>
                <input type="number" name="apiPort" id="apiPort" <?php echo empty($apiPort) ? '' : "value='$apiPort'";?> min="1" max="65535" class="form-control">
              <?php } else { echo empty($apiPort) ? $lang->owt->notset : $apiPort; }?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->owt->apiPortTip : '';?></td>
          </tr>
          <tr class='owt-row'>
            <th class="w-120px"><?php echo $lang->owt->mgmtPort;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'){?>
                <div class='required required-wrapper'></div>
                <input type="number" name="mgmtPort" id="mgmtPort" <?php echo empty($mgmtPort) ? '' : "value='$mgmtPort'";?> min="1" max="65535" class="form-control">
              <?php } else { echo empty($mgmtPort) ? $lang->owt->notset : $mgmtPort; }?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->owt->mgmtPortTip : '';?></td>
          </tr>
          <tr class='owt-row'>
            <th class="w-120px"><?php echo $lang->owt->serviceId;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'){?>
                <div class='required required-wrapper'></div>
                <?php echo html::input('serviceId', $serviceId, "class='form-control'");?>
              <?php } else { echo empty($serviceId) ? $lang->owt->notset : $serviceId; }?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->owt->serviceIdTip : '';?></td>
          </tr>
          <tr class='owt-row'>
            <th class="w-120px vtop"><?php echo $lang->owt->serviceKey;?></th>
            <td class="w-400px code wrapper">
              <?php if($type == 'edit'){?>
                <div class='required required-wrapper'></div>
                <?php echo html::textarea('serviceKey', $serviceKey, "class='form-control' style='height:100px;'");?>
              <?php } else { echo empty($serviceKey) ? $lang->owt->notset : $serviceKey; }?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->owt->serviceKeyTip : '';?></td>
          </tr>
          <?php endif; ?>
          <tr>
            <th></th>
            <td colspan='2'>
              <?php if($type == 'edit') echo html::submitButton();?>
              <?php if($type != 'edit') echo '<a class="btn btn-primary" href="' . helper::createLink('owt', 'admin', 'type=edit') . '">' . $lang->edit;?>
            </td>
          </tr>
        </table>
      </form>
    </div>
    <div class="tab-pane fade <?php echo $type == 'video' ? 'active in' : '';?>" id="videoContent">
      <form method='post' id='ajaxForm' class='form-ajax' action=<?php echo $this->createLink("owt", 'admin', 'type=video');?>>
        <table class='table table-form'>
          <tr>
            <th class="w-120px"><?php echo $lang->owt->resolutionWidth;?></th>
            <td class="code w-100px">
              <input type="number" name="resolutionWidth" id="resolutionWidth" <?php echo "placeholder='{$lang->owt->placeholder->resolutionWidth}'" ;echo empty($resolutionWidth) ? '' : "value='$resolutionWidth'";?> min="320" max="1280" class="form-control">
            </td>
            <td><?php echo $lang->owt->resolutionWidthTip;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->owt->resolutionHeight;?></th>
            <td class="code w-100px">
              <input type="number" name="resolutionHeight" id="resolutionHeight" <?php echo "placeholder='{$lang->owt->placeholder->resolutionHeight}'" ;echo empty($resolutionHeight) ? '' : "value='$resolutionHeight'";?> min="240" max="720" class="form-control">
            <td><?php echo $lang->owt->resolutionHeightTip;?></td>
          </tr>
          <tr><th></th><td></td></tr>
          <tr>
            <th></th>
            <td colspan='2'>
              <?php echo html::submitButton();?>
            </td>
          </tr>
        </table>
      </form>


    </div>
  </div>
</div>
<style>
.owt-row {display: none}
#owtAdminForm.owt-enabled .owt-row {display: table-row}
</style>
<script>
$(function()
{
    $.setAjaxForm('#owtAdminForm');
    $('#enabled').on('change', function()
    {
        $('#owtAdminForm').toggleClass('owt-enabled', $('#enabled').is(':checked'));
    });
});
</script>
<?php include '../../common/view/footer.html.php';?>
