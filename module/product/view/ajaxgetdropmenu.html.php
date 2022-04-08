<?php js::set('productID', $productID);?>
<?php js::set('module', $module);?>
<?php js::set('method', $method);?>
<?php js::set('extra', $extra);?>
<style>
#navTabs {position: sticky; top: 0; background: #fff; z-index: 950;}
#navTabs>li {padding: 0px 10px; display: inline-block}
#navTabs>li>span {display: inline-block;}
#navTabs>li>a {padding: 8px 0px; display: inline-block}
#navTabs>li.active>a {font-weight: 700; color: #0c64eb;}
#navTabs>li.active>a:before {position: absolute; right: 0; bottom: -1px; left: 0; display: block; height: 2px; content: ' '; background: #0c64eb; }
#navTabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {border: none;}

#tabContent {margin-top: 10px; z-index: 900;}
.productTree ul {list-style: none; margin: 0}
.productTree .products>ul {padding-left: 7px;}
.productTree .products>ul>li>div {display: flex; flex-flow: row nowrap; justify-content: flex-start; align-items: center;}
.productTree .products>ul>li label {background: rgba(255,255,255,0.5); line-height: unset; color: #838a9d; border: 1px solid #d8d8d8; border-radius: 2px; padding: 1px 4px;}
.productTree li a i.icon {font-size: 15px !important;}
.productTree li a i.icon:before {min-width: 16px !important;}
.productTree li .label {position: unset; margin-bottom: 0;}
.productTree li>a, div.hide-in-search>a {display: block; padding: 2px 10px 2px 5px; overflow: hidden; line-height: 20px; text-overflow: ellipsis; white-space: nowrap; border-radius: 4px;}
.productTree li>a.selected {color: #e9f2fb; background-color: #0c64eb;}
.productTree .tree li>.list-toggle {line-height: 24px;}
.productTree .tree li.has-list.open:before {content: unset;}

#swapper li>div.hide-in-search>a:focus, #swapper li>div.hide-in-search>a:hover {color: #838a9d; cursor: default;}
a.productName:focus, a.productName:hover {background: #0c64eb; color: #fff !important;}

#swapper li > a {padding-top: 4px; padding-bottom: 4px;}
#swapper li {padding-top: 0; padding-bottom: 0;}
#swapper .tree li>.list-toggle {top: -1px;}

#subHeader .tree ul {display: block;}
</style>
<?php
$productCounts      = array();
$productNames       = array();
$tabActive          = '';
$myProducts         = 0;
$others             = 0;
$closeds            = 0;

foreach($products as $programID => $programProducts)
{
    $productCounts[$programID]['myProduct'] = 0;
    $productCounts[$programID]['others']    = 0;
    $productCounts[$programID]['closed']    = 0;

    foreach($programProducts as $product)
    {
        if($product->status == 'normal' and $product->PO == $this->app->user->account) $productCounts[$programID]['myProduct'] ++;
        if($product->status == 'normal' and !($product->PO == $this->app->user->account)) $productCounts[$programID]['others'] ++;
        if($product->status == 'closed') $productCounts[$programID]['closed'] ++;
        $productNames[] = $product->name;
    }
}
$productsPinYin = common::convert2Pinyin($productNames);

$myProductsHtml     = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';
$normalProductsHtml = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';
$closedProductsHtml = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';

foreach($products as $programID => $programProducts)
{
    /* Add the program name before project. */
    if($programID and $config->systemMode == 'new')
    {
        $programName = zget($programs, $programID);

        if($productCounts[$programID]['myProduct']) $myProductsHtml  .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
        if($productCounts[$programID]['others']) $normalProductsHtml .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
        if($productCounts[$programID]['closed']) $closedProductsHtml .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
    }

    foreach($programProducts as $index => $product)
    {
        $selected    = $product->id == $productID ? 'selected' : '';
        $productName = $product->line ? zget($lines, $product->line, '') . ' / ' . $product->name : $product->name;
        $linkHtml    = $this->product->setParamsForLink($module, $link, $projectID, $product->id);
        $locateTab   = ($module == 'testtask' and $method == 'browseUnits' and $app->tab == 'project') ? '' : "data-app='$app->tab'";

        if($product->status == 'normal' and $product->PO == $this->app->user->account)
        {
            $myProductsHtml .= '<li>' . html::a($linkHtml, $productName, '', "class='$selected productName' title='{$productName}' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$app->tab'") . '</li>';

            if($selected == 'selected') $tabActive = 'myProduct';

            $myProducts ++;
        }
        else if($product->status == 'normal' and !($product->PO == $this->app->user->account))
        {
            $normalProductsHtml .= '<li>' . html::a($linkHtml, $productName, '', "class='$selected productName' title='{$productName}' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$app->tab'") . '</li>';

            if($selected == 'selected') $tabActive = 'other';

            $others ++;
        }
        else if($product->status == 'closed')
        {
            $closedProductsHtml .= '<li>' . html::a($linkHtml, $productName, '', "class='$selected productName' title='$productName' class='closed' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$app->tab'") . '</li>';

            if($selected == 'selected') $tabActive = 'closed';
        }

        /* If the programID is greater than 0, the product is the last one in the program, print the closed label. */
        if($programID and !isset($programProducts[$index + 1]))
        {
            if($productCounts[$programID]['myProduct']) $myProductsHtml     .= '</ul></li>';
            if($productCounts[$programID]['others'])    $normalProductsHtml .= '</ul></li>';
            if($productCounts[$programID]['closed'])    $closedProductsHtml .= '</ul></li>';
        }
    }
}
$myProductsHtml     .= '</ul>';
$normalProductsHtml .= '</ul>';
$closedProductsHtml .= '</ul>';
?>
<div class="table-row">
  <div class="table-col col-left">
    <div class='list-group'>
      <?php $tabActive = ($myProducts and ($tabActive == 'closed' or $tabActive == 'myProduct')) ? 'myProduct' : 'other';?>
      <?php if($myProducts): ?>
      <ul class="nav nav-tabs" id="navTabs">
        <li class="<?php if($tabActive == 'myProduct') echo 'active';?>"><?php echo html::a('#myProduct', $lang->product->mine, '', "data-toggle='tab' class='not-list-item not-clear-menu'");?><span class="label label-light label-badge"><?php echo $myProducts;?></span><li>
        <li class="<?php if($tabActive == 'other') echo 'active';?>"><?php echo html::a('#other', $lang->product->other, '', "data-toggle='tab' class='not-list-item not-clear-menu'")?><span class="label label-light label-badge"><?php echo $others;?></span><li>
      </ul>
      <?php endif;?>
      <div class="tab-content productTree" id="tabContent">
        <div class="tab-pane products <?php if($tabActive == 'myProduct') echo 'active';?>" id="myProduct">
          <?php echo $myProductsHtml;?>
        </div>
        <div class="tab-pane products <?php if($tabActive == 'other') echo 'active';?>" id="other">
          <?php echo $normalProductsHtml;?>
        </div>
      </div>
    </div>
    <div class="col-footer">
      <?php //echo html::a(helper::createLink('product', 'all'), '<i class="icon icon-cards-view muted"></i> ' . $lang->product->all, '', 'class="not-list-item"'); ?>
      <?php //echo html::a(helper::createLink('project', 'browse', 'programID=0&browseType=all'), '<i class="icon icon-cards-view muted"></i> ' . $lang->project->all, '', 'class="not-list-item"'); ?>
      <a class='pull-right toggle-right-col not-list-item'><?php echo $lang->product->closed?><i class='icon icon-angle-right'></i></a>
    </div>
  </div>
  <div class="table-col col-right productTree">
   <div class='list-group products'><?php echo $closedProductsHtml;?></div>
  </div>
</div>
<script>scrollToSelected();</script>
<script>
$(function()
{
    $('.nav-tabs li span').hide();
    $('.nav-tabs li.active').find('span').show();

    $('.nav-tabs>li a').click(function()
    {
        if($('#swapper input[type="search"]').val() == '')
        {
            $(this).siblings().show();
            $(this).parent().siblings('li').find('span').hide();
        }
    })

    $('#swapper [data-ride="tree"]').tree('expand');

    $('#swapper #dropMenu .search-box').on('onSearchChange', function(event, value)
    {
        if(value != '')
        {
            $('div.hide-in-search').siblings('i').addClass('hide-in-search');
            $('.nav-tabs li span').hide();
        }
        else
        {
            $('div.hide-in-search').siblings('i').removeClass('hide-in-search');
            $('li.has-list div.hide-in-search').removeClass('hidden');
            $('.nav-tabs li.active').find('span').show();
        }
    })
})
</script>
