<?php
/**
 * The settings view file of attend module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     attend
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
  <style>
  #menuActions{float:right !important; margin-top: -60px !important;}
  .input-group-required > .required::after, .required-wrapper.required::after {top:12px !important;}
  .modal-body .table {margin-bottom:0px !important;}
  </style>
  <div id='featurebar'>
    <ul class='nav'>
    <?php
    $methodName = strtolower($this->app->getMethodName());
    foreach($lang->attend->featureBar['personal'] as $type => $name)
    {
        $class = strtolower($type) == $methodName ? "class='active'" : '';
        if(common::hasPriv('attend', $type)) echo "<li id='$type' $class>" . html::a($this->createLink('attend', $type), $name) . '</li>';
    }
    ?>
    </ul>
  </div>

<?php include $app->getModuleRoot() . 'common/view/chosen.html.php';?>
<?php if(!$module):?>
<div class='with-side'>
  <div class='side'>
    <nav class='menu leftmenu'>
      <ul class='nav nav-primary'>
        <li><?php extCommonModel::printLink('attend', 'settings', '', "{$lang->attend->settings}");?></li>
        <li><?php extCommonModel::printLink('attend', 'personalsettings', '', $lang->attend->personalSettings);?></li>
        <li><?php extCommonModel::printLink('attend', 'setmanager', '', "{$lang->attend->setManager}");?></li>
      </ul>
    </nav>
  </div>
  <div class='main'>
<?php endif;?>
    <div class='panel'>
      <div class='panel-heading'><?php echo $lang->attend->setManager;?></div>
      <div class='panel-body'>
        <form id='ajaxForm' method='post'>
          <table class='table table-form table-condensed w-p40'>
            <?php if(!empty($deptList)):?>
            <?php foreach($deptList as $id => $dept):?>
            <tr>
              <th class='w-150px'><?php echo $dept->name;?></th>
              <td class='w-300px'><?php echo html::select("dept[$id]", $users, trim($dept->manager, ','), "class='form-control chosen'")?></td>
            </tr>
            <?php endforeach;?>
            <tr>
              <th></th>
              <td><?php echo baseHTML::submitButton();?></td>
            </tr>
            <?php else:?>
            <tr>
              <th></th>
              <td><?php extCommonModel::printLink('team.tree', 'browse', 'type=dept', $lang->attend->setDept, "class='btn btn-primary setDept'");?></td>
            </tr>
            <?php endif;?>
          </table>
        </form>
      </div>
    </div>
<?php if(!$module):?>
  </div>
</div>
<?php endif;?>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
