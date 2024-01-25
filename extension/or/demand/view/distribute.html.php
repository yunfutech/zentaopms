<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2>
        <span class='label label-id'><?php echo $demand->id;?></span>
        <span title='<?php echo $demand->title;?>'><?php echo $demand->title;?></span>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-100px'><?php echo $lang->demand->distributeProduct;?></th>
            <td class='w-p45 productsBox'>
              <div class='row'>
                <div class='input-group required'>
                  <?php echo html::select('product', $products, $demand->product, "onchange='loadProductBranches(this.value);' class='form-control chosen'");?>
                    <span class='input-group-addon fix-border fix-padding'></span>
                  <?php if(common::hasPriv('product', 'create') and empty($products)):?>
                    <span class='input-group-addon newProduct'>
                      <?php echo html::checkBox('newProduct', $lang->demand->addProduct, '', "onchange=addNewProduct(this);");?>
                    </span>
                  <?php endif;?>
                </div>
              </div>
              <div class="input-group addProduct hidden">
                <?php echo html::input('productName', '', "class='form-control'");?>
                <span class='input-group-addon required'><?php echo html::checkBox('newProduct', $lang->demand->addProduct, '', "onchange=addNewProduct(this);");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th class='w-100px'><?php echo $lang->demand->roadmap;?></th>
            <td class='w-p45 roadmapBox' id='roadmapBox'>
              <div class='row'>
                <div class='input-group '>
                  <?php echo html::select('roadmap', $roadmaps, '', 'class="form-control picker-select"');?>
                    <span class='input-group-addon fix-border fix-padding'></span>
                  <?php if(common::hasPriv('roadmap', 'create') and empty($roadmaps) and !empty($products)):?>
                    <span class='input-group-addon newRoadmap hidden'>
                      <?php echo html::checkBox('newRoadmap', $lang->demand->addRoadmap, '', "onchange=addNewRoadmap(this);");?>
                    </span>
                  <?php endif;?>
                </div>
              </div>
              <div class="input-group addRoadmap hidden required">
                <?php echo html::input('roadmapName', '', "class='form-control'");?>
                <span class='input-group-addon'><?php echo html::checkBox('newRoadmap', $lang->demand->addRoadmap, '', "onchange=addNewRoadmap(this);");?></span>
              </div>
            </td>
          </tr>
          <tr class='roadmapStart hidden'>
            <th><?php echo $lang->demand->roadmapStart;?></th>
            <td><div class='datepicker-wrapper required'><?php echo html::input('roadmapStart', '', "class='form-control form-date'");?></div></td>
          </tr>
          <tr class='roadmapEnd hidden'>
            <th><?php echo $lang->demand->roadmapEnd;?></th>
            <td><div class='datepicker-wrapper required'><?php echo html::input('roadmapEnd', '', "class='form-control form-date'");?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->demand->distribute);?></td>
          </tr>
        </tbody>
      </table>
    </form>
    <hr/>
    <?php include $app->getModuleRoot() . 'common/view/action.html.php';?>
  </div>
</div>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
