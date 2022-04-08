<?php
/**
 * The model file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: model.php 5118 2013-07-12 07:41:41Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class productModel extends model
{
    /**
     * Get product module menu.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  string $extra
     * @param  string $branch
     * @param  string $module
     * @param  string $moduleType
     *
     * @access public
     * @return string
     */
    public function getModuleNav($products, $productID, $extra, $branch, $module = 0, $moduleType = '')
    {
        $currentModule = $this->app->getModuleName();
        $currentMethod = $this->app->getMethodName();

        /* init currentModule and currentMethod for report and story. */
        if($currentModule == 'story')
        {
            $storyMethods = ",track,create,batchcreate,batchclose,";
            if(strpos($storyMethods, "," . $currentMethod . ",") === false) $currentModule = 'product';
            if($currentMethod == 'view' || $currentMethod == 'change' || $currentMethod == 'review') $currentMethod = 'browse';
        }
        if($currentMethod == 'report') $currentMethod = 'browse';

        $selectHtml = $this->select($products, $productID, $currentModule, $currentMethod, $extra, $branch, $module, $moduleType);

        $pageNav  = '';
        $isMobile = $this->app->viewType == 'mhtml';
        if($isMobile)
        {
            $pageNav  = html::a(helper::createLink('product', 'index'), $this->lang->product->index) . $this->lang->colon;
            $pageNav .= $selectHtml;
        }
        else
        {
            $pageNav = $selectHtml;
        }

        return $pageNav;
    }

    /**
     * Create the select code of products.
     *
     * @param  array       $products
     * @param  int         $productID
     * @param  string      $currentModule
     * @param  string      $currentMethod
     * @param  string      $extra
     * @param  int|string  $branch
     * @param  int         $module
     * @param  string      $moduleType
     * @param  bool        $withBranch      true|false
     *
     * @access public
     * @return string
     */
    public function select($products, $productID, $currentModule, $currentMethod, $extra = '', $branch = '', $module = 0, $moduleType = '', $withBranch = true)
    {
        $isQaModule = (strpos(',project,execution,', ",{$this->app->tab},") !== false and strpos(',bug,testcase,testtask,ajaxselectstory,', ",{$this->app->rawMethod},") !== false and isset($products[0])) ? true : false;

        $this->app->loadLang('product');
        if(!$isQaModule and !$productID)
        {
            unset($this->lang->product->menu->settings['subMenu']->branch);
            return;
        }
        $isMobile = $this->app->viewType == 'mhtml';

        setcookie("lastProduct", $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($productID) $currentProduct = $this->getById($productID);

        if($isQaModule and $this->app->tab == 'project')
        {
            if($this->app->tab == 'project')   $extra = $currentMethod == 'testcase' ? $extra : $this->session->project;
            if($this->app->tab == 'execution') $extra = $this->session->execution;
        }

        if($isQaModule and !$productID)
        {
            $currentProduct = new stdclass();
            $currentProduct->name = $products[$productID];
            $currentProduct->type = 'normal';
        }
        $this->session->set('currentProductType', $currentProduct->type);

        $output = '';
        if(!empty($products))
        {
            $dropMenuLink = helper::createLink($isQaModule ? 'bug' : 'product', 'ajaxGetDropMenu', "objectID=$productID&module=$currentModule&method=$currentMethod&extra=$extra");
            $output  = "<div class='btn-group angle-btn'><div class='btn-group'><button data-toggle='dropdown' type='button' class='btn btn-limit' id='currentItem' title='{$currentProduct->name}'><span class='text'>{$currentProduct->name}</span> <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
            $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
            $output .= "</div></div>";
            if($isMobile) $output = "<a id='currentItem' href=\"javascript:showSearchMenu('product', '$productID', '$currentModule', '$currentMethod', '$extra')\"><span class='text'>{$currentProduct->name}</span> <span class='icon-caret-down'></span></a><div id='currentItemDropMenu' class='hidden affix enter-from-bottom layer'></div>";

            if($currentProduct->type == 'normal' || !$withBranch) unset($this->lang->product->menu->settings['subMenu']->branch);
            if($currentProduct->type != 'normal' && $currentModule != 'programplan' && $withBranch)
            {
                $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$currentProduct->type]);
                $this->lang->product->menu->settings['subMenu']->branch = str_replace('@branch@', $this->lang->product->branch, $this->lang->product->menu->settings['subMenu']->branch);

                $branches   = $this->loadModel('branch')->getPairs($productID, 'all');
                $branchName = isset($branches[$branch]) ? $branches[$branch] : $branches[0];
                if(!$isMobile)
                {
                    $dropMenuLink = helper::createLink('branch', 'ajaxGetDropMenu', "objectID=$productID&branch=$branch&module=$currentModule&method=$currentMethod&extra=$extra");
                    $output .= "<div class='btn-group'><button id='currentBranch' data-toggle='dropdown' type='button' class='btn btn-limit'>{$branchName} <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
                    $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
                    $output .= "</div></div>";
                }
                else
                {
                    $output .= "<a id='currentBranch' href=\"javascript:showSearchMenu('branch', '$productID', '$currentModule', '$currentMethod', '$extra')\">{$branchName} <span class='icon-caret-down'></span></a><div id='currentBranchDropMenu' class='hidden affix enter-from-bottom layer'></div>";
                }
            }

            if(!$isMobile) $output .= '</div>';
        }

        return $output;
    }

    /**
     * Save the product id user last visited to session.
     *
     * @param  int   $productID
     * @param  array $products
     * @access public
     * @return int
     */
    public function saveState($productID, $products)
    {
        if($productID == 0 and $this->cookie->preProductID)   $productID = $this->cookie->preProductID;
        if(($productID == 0 and $this->session->product == '') or !isset($products[$productID])) $productID = key($products);
        $this->session->set('product', (int)$productID, $this->app->tab);

        if(!isset($products[$this->session->product]))
        {
            $product = $this->getById($productID);
            if(empty($product)) $productID = key($products);
            $this->session->set('product', (int)$productID, $this->app->tab);
            if($productID && strpos(",{$this->app->user->view->products},", ",{$productID},") === false) $this->accessDenied();
        }

        setcookie('preProductID', (int)$productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $this->session->product)
        {
            $this->cookie->set('preBranch', 0);
            setcookie('preBranch', 0, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        }
        return $this->session->product;
    }

    /**
     * Check privilege.
     *
     * @param  int    $product
     * @access public
     * @return bool
     */
    public function checkPriv($productID)
    {
        if(empty($productID)) return false;

        /* Is admin? */
        if($this->app->user->admin) return true;
        return (strpos(",{$this->app->user->view->products},", ",{$productID},") !== false);
    }

    /**
     * Show accessDenied response.
     *
     * @access private
     * @return void
     */
    public function accessDenied()
    {
        if(defined('TUTORIAL')) return true;

        echo(js::alert($this->lang->product->accessDenied));

        if(!$this->server->http_referer) die(js::locate(helper::createLink('product', 'index')));

        $loginLink = $this->config->requestType == 'GET' ? "?{$this->config->moduleVar}=user&{$this->config->methodVar}=login" : "user{$this->config->requestFix}login";
        if(strpos($this->server->http_referer, $loginLink) !== false) die(js::locate(helper::createLink('product', 'index')));

        die(js::locate('back'));
    }

    /**
     * Get product by id.
     *
     * @param  int    $productID
     * @access public
     * @return object
     */
    public function getById($productID)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProduct();
        $product = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
        if(!$product) return false;

        return $this->loadModel('file')->replaceImgURL($product, 'desc');
    }

    /**
     * Get by idList.
     *
     * @param  array    $productIDList
     * @access public
     * @return array
     */
    public function getByIdList($productIDList)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCT)->where('id')->in($productIDList)->fetchAll('id');
    }

    /**
     * Get products.
     *
     * @param  int    $programID
     * @param  string $status
     * @param  int    $limit
     * @param  int    $line
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getList($programID = 0, $status = 'all', $limit = 0, $line = 0)
    {
        return $this->dao->select('t1.id as id,t1.*')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($programID)->andWhere('t1.program')->eq($programID)->fi()
            ->beginIF($line > 0)->andWhere('t1.line')->eq($line)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t1.id')->in($this->app->user->view->products)->fi()
            ->beginIF($status == 'noclosed')->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF($status != 'all' and $status != 'noclosed' and $status != 'involved')->andWhere('t1.status')->in($status)->fi()
            ->beginIF($status == 'involved')
            ->andWhere('t1.PO', true)->eq($this->app->user->account)
            ->orWhere('t1.QD')->eq($this->app->user->account)
            ->orWhere('t1.RD')->eq($this->app->user->account)
            ->orWhere('t1.createdBy')->eq($this->app->user->account)
            ->markRight(1)
            ->fi()
            ->orderBy('t1.order_asc')
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->fetchAll('id');
    }

    /**
     * Get product pairs.
     *
     * @param  string $mode
     * @param  string $programID
     * @return array
     */
    public function getPairs($mode = '', $programID = 0)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProductPairs();

        $orderBy  = !empty($this->config->product->orderBy) ? $this->config->product->orderBy : 'isClosed';
        $products = $this->dao->select('*,  IF(INSTR(" closed", status) < 2, 0, 1) AS isClosed')
            ->from(TABLE_PRODUCT)
            ->where(1)
            ->beginIF(strpos($mode, 'all') === false)->andWhere('deleted')->eq(0)->fi()
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->orderBy($orderBy)
            ->fetchPairs('id', 'name');
        return $products;
    }

    /**
     * Get product pairs by project.
     *
     * @param  int    $projectID
     * @param  int    $status   all|noclosed
     * @access public
     * @return array
     */
    public function getProductPairsByProject($projectID = 0, $status = 'all')
    {
        $products = empty($projectID) ? $this->getList() : $this->getProducts($projectID, $status);
        $pairs    = array();
        if(!empty($products))
        {
            foreach($products as $product) $pairs[$product->id] = $product->name;
        }

        return $pairs;
    }

    /**
     * Get product pairs by project model.
     *
     * @param  string $model all|scrum|waterfall
     * @access public
     * @return array
     */
    public function getPairsByProjectModel($model)
    {
        return $this->dao->select('t3.id as id, t3.name as name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product=t3.id')
            ->where('t3.deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('t3.id')->in($this->app->user->view->products)->fi()
            ->beginIF($model != 'all')->andWhere('t2.model')->eq($model)
            ->fetchPairs('id', 'name');
    }

    /**
     * Get products by project.
     *
     * @param  int    $projectID
     * @param  int    $status   all|noclosed
     * @param  string $orderBy
     * @param  bool   $withBranch
     * @access public
     * @return array
     */
    public function getProducts($projectID = 0, $status = 'all', $orderBy = '', $withBranch = true)
    {
        if(defined('TUTORIAL'))
        {
            if(!$withBranch) return $this->loadModel('tutorial')->getProductPairs();
            return $this->loadModel('tutorial')->getExecutionProducts();
        }

        $projectProducts = $this->dao->select('t1.branch, t1.plan, t2.*')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')
            ->on('t1.product = t2.id')
            ->where('t2.deleted')->eq(0)
            ->beginIF(!empty($projectID))->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->products)->fi()
            ->beginIF(strpos($status, 'noclosed') !== false)->andWhere('t2.status')->ne('closed')->fi()
            ->orderBy($orderBy . 't2.order asc')
            ->fetchAll('id');

        $products = array();
        foreach($projectProducts as $product)
        {
            if(!$withBranch)
            {
                $products[$product->id] = $product->name;
                continue;
            }

            if(!isset($products[$product->id]))
            {
                $products[$product->id] = $product;
                $products[$product->id]->branches = array();
                $products[$product->id]->plans    = array();
            }
            $products[$product->id]->branches[$product->branch] = $product->branch;
            if($product->plan) $products[$product->id]->plans[$product->plan] = $product->plan;

            unset($product->branch);
            unset($product->plan);
        }

        return $products;
    }

    /**
     * Get product id by project.
     *
     * @param  int    $projectID
     * @param  bool   $isFirst
     * @access public
     * @return array
     */
    public function getProductIDByProject($projectID, $isFirst = true)
    {
        $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)
            ->where('1=1')
            ->beginIF(!$this->app->user->admin)->andWhere('product')->in($this->app->user->view->products)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->fetchPairs();

        if($isFirst === false) return $products;
        return empty($products) ? 0 : current($products);
    }

    /**
     * Get grouped products.
     *
     * @access public
     * @return void
     */
    public function getStatusGroups()
    {
        $products = $this->dao->select('id, name, status')->from(TABLE_PRODUCT)->where('deleted')->eq(0)->fetchGroup('status');
    }

    /**
     * Get ordered products.
     *
     * @param  string $status
     * @param  int    $num
     * @access public
     * @return array
     */
    public function getOrderedProducts($status, $num = 0, $projectID = 0)
    {
        $products = array();
        if($projectID)
        {
            $pairs    = $this->getProducts($projectID, $status == 'normal' ? 'noclosed' : '');
            $products = $this->getByIdList(array_keys($pairs));
        }
        else
        {
            $products = $this->getList('', $status, $num);
        }

        if(empty($products)) return $products;

        $lines = $this->getLinePairs();
        $productList = array();
        foreach($lines as $id => $name)
        {
            foreach($products as $key => $product)
            {
                if($product->line == $id)
                {
                    $product->name = $name . '/' . $product->name;
                    $productList[] = $product;
                    unset($products[$key]);
                }
            }
        }
        $productList = array_merge($productList, $products);

        $products = $mineProducts = $otherProducts = $closedProducts = array();
        foreach($productList as $product)
        {
            if(!$this->app->user->admin and !$this->checkPriv($product->id)) continue;
            if($product->status == 'normal' and $product->PO == $this->app->user->account)
            {
                $mineProducts[$product->id] = $product;
            }
            elseif($product->status == 'normal' and $product->PO != $this->app->user->account)
            {
                $otherProducts[$product->id] = $product;
            }
            elseif($product->status == 'closed')
            {
                $closedProducts[$product->id] = $product;
            }
        }
        $products = $mineProducts + $otherProducts + $closedProducts;

        if(empty($num)) return $products;
        return array_slice($products, 0, $num, true);
    }

    /**
     * Get Multi-branch product pairs.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getMultiBranchPairs($programID = 0)
    {
        return $this->dao->select('id')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->andWhere('type')->in('branch,platform')
            ->fetchPairs();
    }

    /*
     * Get product switcher.
     *
     * @param  int         $productID
     * @param  string      $extra
     * @param  int|string  $branch
     * @access public
     * @return void
     */
    public function getSwitcher($productID = 0, $extra = '', $branch = '')
    {
        $currentModule = $this->app->moduleName;
        $currentMethod = $this->app->methodName;

        /* Init currentModule and currentMethod for report and story. */
        if($currentModule == 'story')
        {
            $storyMethods = ",track,create,batchcreate,batchclose,zerocase,";
            if(strpos($storyMethods, "," . $currentMethod . ",") === false) $currentModule = 'product';
            if($currentMethod == 'view' or $currentMethod == 'change' or $currentMethod == 'review') $currentMethod = 'browse';
        }
        if($currentModule == 'testcase' and strpos(',view,edit,', ",$currentMethod,") !== false) $currentMethod = 'browse';
        if($currentModule == 'bug' and $currentMethod == 'edit') $currentMethod = 'browse';
        if($currentMethod == 'report') $currentMethod = 'browse';

        $currentProductName = $this->lang->product->common;
        if($productID)
        {
            $currentProduct     = $this->getById($productID);
            $currentProductName = $currentProduct->name;
            $this->session->set('currentProductType', $currentProduct->type);
        }

        $fromModule   = $this->lang->navGroup->qa == 'qa' ? 'qa' : '';
        $dropMenuLink = helper::createLink($this->app->tab == 'qa' ? 'product' : $this->app->tab, 'ajaxGetDropMenu', "objectID=$productID&module=$currentModule&method=$currentMethod&extra=$extra&from=$fromModule");

        if($this->app->viewType == 'mhtml' and $productID) return $this->getModuleNav(array($productID => $currentProductName), $productID, $extra, $branch);

        $output  = "<div class='btn-group header-btn' id='swapper'><button data-toggle='dropdown' type='button' class='btn' id='currentItem' title='{$currentProductName}'><span class='text'>{$currentProductName}</span> <span class='caret' style='margin-bottom: -1px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";

        $notNormalProduct = (isset($currentProduct->type) and $currentProduct->type != 'normal');
        if($notNormalProduct)
        {
            $isShowBranch = false;
            if($currentModule == 'story' and $currentMethod == 'track') $isShowBranch = true;
            if($currentModule == 'tree' and $currentMethod == 'browse') $isShowBranch = true;
            if($currentModule == 'product' and strpos($this->config->product->showBranchMethod, $currentMethod) !== false) $isShowBranch = true;
            if($this->app->tab == 'qa' and strpos(',testsuite,testreport,testtask,', ",$currentModule,") === false) $isShowBranch = true;
            if($this->app->tab == 'qa' and $currentModule == 'testtask' and strpos(',create,edit,browseunits,importunitresult,unitcases,', ",$currentMethod,") === false) $isShowBranch = true;
            if($isShowBranch)
            {
                $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$currentProduct->type]);
                $branches     = $this->loadModel('branch')->getPairs($productID, 'all');
                $branchName   = isset($branches[$branch]) ? $branches[$branch] : $branches[0];
                $dropMenuLink = helper::createLink('branch', 'ajaxGetDropMenu', "objectID=$productID&branch=$branch&module=$currentModule&method=$currentMethod&extra=$extra");

                $output .= "<div class='btn-group header-btn'><button id='currentBranch' data-toggle='dropdown' type='button' class='btn'><span class='text'>{$branchName}</span> <span class='caret' style='margin-bottom: -1px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
                $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
                $output .= "</div></div>";
            }
        }

        return $output;
    }

    /**
     * Create a product.
     *
     * @access public
     * @return int
     */
    public function create()
    {
        $product = fixer::input('post')
            ->setDefault('status', 'normal')
            ->setDefault('line', 0)
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->setDefault('createdVersion', $this->config->version)
            ->setIF($this->post->acl == 'open', 'whitelist', '')
            ->stripTags($this->config->product->editor->create['id'], $this->config->allowedTags)
            ->join('whitelist', ',')
            ->join('reviewer', ',')
            ->remove('uid,newLine,lineName,contactListMenu')
            ->get();

        if(!empty($_POST['lineName']))
        {
            /* Insert product line. */
            $maxOrder = $this->dao->select("max(`order`) as maxOrder")->from(TABLE_MODULE)->where('type')->eq('line')->fetch('maxOrder');
            $maxOrder = $maxOrder ? $maxOrder + 10 : 0;

            $line = new stdClass();
            $line->type   = 'line';
            $line->parent = 0;
            $line->grade  = 1;
            $line->name   = $this->post->lineName;
            $line->root   = $this->config->systemMode == 'new' ? $product->program : 0;
            $line->order  = $maxOrder;
            $this->dao->insert(TABLE_MODULE)->data($line)->exec();

            $lineID = $this->dao->lastInsertID();
            $path   = ",$lineID,";
            $this->dao->update(TABLE_MODULE)->set('path')->eq($path)->where('id')->eq($lineID)->exec();

            if(dao::isError()) return false;
            $product->line = $lineID;
        }

        $product        = $this->loadModel('file')->processImgURL($product, $this->config->product->editor->create['id'], $this->post->uid);
        $programID = isset($product->program) ? $product->program : '';
        $this->dao->insert(TABLE_PRODUCT)->data($product)->autoCheck()
            ->batchCheck($this->config->product->create->requiredFields, 'notempty')
            ->checkIF((!empty($product->name) and $this->config->systemMode == 'new'), 'name', 'unique', "`program` = $programID")
            ->checkIF(!empty($product->code), 'code', 'unique')
            ->exec();

        if(!dao::isError())
        {
            $productID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $productID, 'product');
            $this->dao->update(TABLE_PRODUCT)->set('`order`')->eq($productID * 5)->where('id')->eq($productID)->exec();

            $whitelist = explode(',', $product->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'product', $productID);
            if($product->acl != 'open') $this->loadModel('user')->updateUserView($productID, 'product');

            /* Create doc lib. */
            $this->app->loadLang('doc');
            $lib = new stdclass();
            $lib->product = $productID;
            $lib->name    = $this->lang->doclib->main['product'];
            $lib->type    = 'product';
            $lib->main    = '1';
            $lib->acl     = 'default';
            $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();

            return $productID;
        }
    }

    /**
     * Update a product.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function update($productID)
    {
        $productID  = (int)$productID;
        $oldProduct = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
        if($oldProduct->bind) $this->config->product->edit->requiredFields = 'name';

        $product = fixer::input('post')
            ->setDefault('line', 0)
            ->join('whitelist', ',')
            ->join('reviewer', ',')
            ->stripTags($this->config->product->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid,changeProjects,contactListMenu')
            ->get();

        if($this->config->systemMode == 'new')
        {
            if($product->program != $oldProduct->program)
            {
                /* Link the projects stories under this product. */
                $unmodifiableProjects = $this->dao->select('t1.*')->from(TABLE_PROJECTSTORY)->alias('t1')
                    ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                    ->where('t1.product')->eq($productID)
                    ->andWhere('t2.type')->eq('project')
                    ->andWhere('t2.deleted')->eq('0')
                    ->fetchPairs('project', 'product');
                if(!empty($unmodifiableProjects))
                {
                    dao::$errors[] = $this->lang->product->changeProgramError;
                    return false;
                }
            }
        }

        $product   = $this->loadModel('file')->processImgURL($product, $this->config->product->editor->edit['id'], $this->post->uid);
        $programID = isset($product->program) ? $product->program : '';
        $this->dao->update(TABLE_PRODUCT)->data($product)->autoCheck()
            ->batchCheck($this->config->product->edit->requiredFields, 'notempty')
            ->checkIF((!empty($product->name) and $this->config->systemMode == 'new'), 'name', 'unique', "id != $productID and `program` = $programID")
            ->checkIF(!empty($product->code), 'code', 'unique', "id != $productID")
            ->where('id')->eq($productID)
            ->exec();

        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $productID, 'product');
            $whitelist = explode(',', $product->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'product', $productID);
            if($product->acl != 'open') $this->loadModel('user')->updateUserView($productID, 'product');
            if($product->type == 'normal' and $oldProduct->type != 'normal') $this->loadModel('branch')->unlinkBranch4Project($productID);

            return common::createChanges($oldProduct, $product);
        }
    }

    /**
     * Batch update products.
     *
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $products    = array();
        $allChanges  = array();
        $data        = fixer::input('post')->get();
        $oldProducts = $this->getByIdList($this->post->productIDList);
        $nameList    = array();

        $extendFields = $this->getFlowExtendFields();
        foreach($data->productIDList as $productID)
        {
            $productName = $data->names[$productID];

            $productID = (int)$productID;
            $products[$productID] = new stdClass();
            if($this->config->systemMode == 'new' and isset($data->programs[$productID])) $products[$productID]->program = (int)$data->programs[$productID];
            $products[$productID]->name    = $productName;
            $products[$productID]->line    = (int)$data->lines[$productID];
            $products[$productID]->PO      = $data->POs[$productID];
            $products[$productID]->QD      = $data->QDs[$productID];
            $products[$productID]->RD      = $data->RDs[$productID];
            $products[$productID]->type    = $data->types[$productID];
            $products[$productID]->status  = $data->statuses[$productID];
            $products[$productID]->desc    = strip_tags($this->post->descs[$productID], $this->config->allowedTags);
            $products[$productID]->acl     = $data->acls[$productID];

            foreach($extendFields as $extendField)
            {
                $products[$productID]->{$extendField->field} = $this->post->{$extendField->field}[$productID];
                if(is_array($products[$productID]->{$extendField->field})) $products[$productID]->{$extendField->field} = join(',', $products[$productID]->{$extendField->field});

                $products[$productID]->{$extendField->field} = htmlSpecialString($products[$productID]->{$extendField->field});
                $message = $this->checkFlowRule($extendField, $products[$productID]->{$extendField->field});
                if($message) die(js::alert($message));
            }
        }
        if(dao::isError()) die(js::error(dao::getError()));

        $unlinkProducts = array();
        foreach($products as $productID => $product)
        {
            $oldProduct = $oldProducts[$productID];
            if($this->config->systemMode == 'new') $programID  = !isset($product->program) ? $oldProduct->program : (empty($product->program) ? 0 : $product->program);

            $this->dao->update(TABLE_PRODUCT)
                ->data($product)
                ->autoCheck()
                ->batchCheck($this->config->product->edit->requiredFields , 'notempty')
                ->checkIF((!empty($product->name) and $this->config->systemMode == 'new'), 'name', 'unique', "id != $productID and `program` = $programID")
                ->where('id')->eq($productID)
                ->exec();
            if(dao::isError()) die(js::error('product#' . $productID . dao::getError(true)));

            /* When acl is open, white list set empty. When acl is private,update user view. */
            if($product->acl == 'open') $this->loadModel('personnel')->updateWhitelist('', 'product', $productID);
            if($product->acl != 'open') $this->loadModel('user')->updateUserView($productID, 'product');
            if($product->type == 'normal' and $oldProduct->type != 'normal') $unlinkProducts[] = $productID;

            $allChanges[$productID] = common::createChanges($oldProduct, $product);
        }

        if(!empty($unlinkProducts)) $this->loadModel('branch')->unlinkBranch4Project($unlinkProducts);

        return $allChanges;
    }

    /**
     * Close product.
     *
     * @param  int    $productID.
     * @access public
     * @return void
     */
    public function close($productID)
    {
        $oldProduct = $this->getById($productID);
        $now        = helper::now();
        $product= fixer::input('post')
            ->setDefault('status', 'closed')
            ->remove('comment')->get();

        $this->dao->update(TABLE_PRODUCT)->data($product)
            ->autoCheck()
            ->where('id')->eq((int)$productID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldProduct, $product);
    }

    /**
     * Manage line.
     *
     * @access public
     * @return void
     */
    public function manageLine()
    {
        $data = fixer::input('post')->get();

        $line = new stdClass();
        $line->type   = 'line';
        $line->parent = 0;
        $line->grade  = 1;

        $maxOrder = $this->dao->select("max(`order`) as maxOrder")->from(TABLE_MODULE)->where('type')->eq('line')->fetch('maxOrder');
        $maxOrder = $maxOrder ? $maxOrder : 0;
        foreach($data->modules as $id => $name)
        {
            if(empty($name)) continue;
            if($this->config->systemMode == 'new' and empty($data->programs[$id]))
            {
                dao::$errors[] = $this->lang->product->programEmpty;
                return false;
            }

            $line->name  = strip_tags(trim($name));
            $line->root  = $data->programs[$id];

            if(is_numeric($id))
            {
                $maxOrder += 10;
                $line->order = $maxOrder;

                $this->dao->insert(TABLE_MODULE)->data($line)->exec();
                $lineID = $this->dao->lastInsertID();
                $path   = ",$lineID,";
                $this->dao->update(TABLE_MODULE)->set('path')->eq($path)->where('id')->eq($lineID)->exec();
            }
            else
            {
                $lineID = str_replace('id', '', $id);
                $this->dao->update(TABLE_MODULE)->data($line)->where('id')->eq($lineID)->exec();
            }
        }
    }

    /**
     * Get stories.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $queryID
     * @param  int    $moduleID
     * @param  string $type requirement|story
     * @param  string $sort
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getStories($productID, $branch, $browseType, $queryID, $moduleID, $type = 'story', $sort = 'id_desc', $pager = null)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getStories();

        $this->loadModel('story');

        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildID($moduleID) : '0';

        $browseType = $browseType == 'bybranch' ? 'bymodule' : $browseType;
        $browseType = ($browseType == 'bymodule' and $this->session->storyBrowseType and $this->session->storyBrowseType != 'bysearch') ? $this->session->storyBrowseType : $browseType;

        /* Get stories by browseType. */
        $stories = array();
        if($browseType == 'unclosed')
        {
            $unclosedStatus = $this->lang->story->statusList;
            unset($unclosedStatus['closed']);
            $stories = $this->story->getProductStories($productID, $branch, $modules, array_keys($unclosedStatus), $type, $sort, true, '', $pager);
        }
        if($browseType == 'unplan')       $stories = $this->story->getByPlan($productID, $queryID, $modules, '', $type, $sort, $pager);
        if($browseType == 'allstory')     $stories = $this->story->getProductStories($productID, $branch, $modules, 'all', $type, $sort, true, '', $pager);
        if($browseType == 'bymodule')     $stories = $this->story->getProductStories($productID, $branch, $modules, 'all', $type, $sort, true, '', $pager);
        if($browseType == 'bysearch')     $stories = $this->story->getBySearch($productID, $branch, $queryID, $sort, '', $type, '', $pager);
        if($browseType == 'assignedtome') $stories = $this->story->getByAssignedTo($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'openedbyme')   $stories = $this->story->getByOpenedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'reviewedbyme') $stories = $this->story->getByReviewedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'reviewbyme')   $stories = $this->story->getByReviewBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'closedbyme')   $stories = $this->story->getByClosedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'draftstory')   $stories = $this->story->getByStatus($productID, $branch, $modules, 'draft', $type, $sort, $pager);
        if($browseType == 'activestory')  $stories = $this->story->getByStatus($productID, $branch, $modules, 'active', $type, $sort, $pager);
        if($browseType == 'changedstory') $stories = $this->story->getByStatus($productID, $branch, $modules, 'changed', $type, $sort, $pager);
        if($browseType == 'willclose')    $stories = $this->story->get2BeClosed($productID, $branch, $modules, $type, $sort, $pager);
        if($browseType == 'closedstory')  $stories = $this->story->getByStatus($productID, $branch, $modules, 'closed', $type, $sort, $pager);

        return $stories;
    }

    /**
     * Batch get story stage.
     *
     * @param  array  $stories.
     * @access public
     * @return array
     */
    public function batchGetStoryStage($stories)
    {
        /* Set story id list. */
        $storyIdList = array();
        foreach($stories as $story) $storyIdList[$story->id] = $story->id;

        return $this->loadModel('story')->batchGetStoryStage($storyIdList);
    }

    /**
     * Build search form.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  int    $actionURL
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function buildSearchForm($productID, $products, $queryID, $actionURL, $branch = 0)
    {
        $productIdList = ($this->app->tab == 'project' and empty($productID)) ? array_keys($products) : $productID;
        $branchParam   = ($this->app->tab == 'project' and empty($productID)) ? '' : $branch;

        $this->config->product->search['actionURL'] = $actionURL;
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['params']['plan']['values'] = $this->loadModel('productplan')->getPairs($productIdList, $branchParam);

        $product = ($this->app->tab == 'project' and empty($productID)) ? $products : array($productID => $products[$productID]);
        $this->config->product->search['params']['product']['values'] = $product + array('all' => $this->lang->product->allProduct);

        /* Get modules. */
        $this->loadModel('tree');
        if($this->app->tab == 'project')
        {
            if($productID)
            {
                $modules          = array();
                $branchList       = $this->loadModel('branch')->getPairs($productID, '', $this->session->project);
                $branchModuleList = $this->tree->getOptionMenu($productID, 'story', 0, array_keys($branchList));
                foreach($branchModuleList as $branchID => $branchModules) $modules += $branchModules;
            }
            else
            {
                $moduleList  = array();
                $modules     = array('' => '/');
                $branchGroup = $this->loadModel('execution')->getBranchByProduct(array_keys($products), $this->session->project, '');
                foreach($products as $productID => $productName)
                {
                    if(isset($branchGroup[$productID]))
                    {
                        $branchModuleList = $this->tree->getOptionMenu($productID, 'story', 0, array_keys($branchGroup[$productID]));
                        foreach($branchModuleList as $branchID => $branchModules) $moduleList += $branchModules;
                    }
                    else
                    {
                        $moduleList = $this->tree->getOptionMenu($productID, 'story', 0, $branch);
                    }

                    foreach($moduleList as $moduleID => $moduleName)
                    {
                        if(empty($moduleID)) continue;
                        $modules[$moduleID] = $productName . $moduleName;
                    }
                }
            }
        }
        else
        {
            $modules = $this->tree->getOptionMenu($productID, 'story', 0, $branch);
        }
        $this->config->product->search['params']['module']['values'] = $modules;

        $productInfo = $this->getById($productID);
        if(!$productID or $productInfo->type == 'normal' or $this->app->tab == 'assetlib')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$productInfo->type]);
            $this->config->product->search['params']['branch']['values']  = array('' => '', '0' => $this->lang->branch->main) + $this->loadModel('branch')->getPairs($productID, 'noempty');
        }

        $this->loadModel('search')->setSearchParams($this->config->product->search);
    }

    /**
     * Get project pairs by product.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $appendProject
     * @access public
     * @return array
     */
    public function getProjectPairsByProduct($productID, $branch = 0, $appendProject = 0)
    {
        $projects = $this->dao->select('t2.id,t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->beginIF($branch !== '')->andWhere('t1.branch')->in($branch)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy('order_asc')
            ->fetchPairs('id', 'name');

        if($appendProject) $projects += $this->dao->select('id,name')->from(TABLE_PROJECT)->where('id')->in($appendProject)->fetchPairs('id', 'name');
        return $projects;
    }

    /**
     * Get project list by product.
     *
     * @param  int       $productID
     * @param  string    $browseType
     * @param  int       $branch
     * @param  int       $involved
     * @param  string    $orderBy
     * @access public
     * @return array
     */
    public function getProjectListByProduct($productID, $browseType = 'all', $branch = 0, $involved = 0, $orderBy = 'order_desc')
    {
        $projectList = $this->dao->select('t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->beginIF($this->config->systemMode == 'new')->andWhere('t2.type')->eq('project')->fi()
            ->beginIF($this->config->systemMode == 'classic')->andWhere('t2.type')->eq('sprint')->fi()
            ->beginIF($browseType != 'all')->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF(!$this->app->user->admin and $this->config->systemMode == 'new')->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->beginIF(!$this->app->user->admin and $this->config->systemMode != 'new')->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
            ->beginIF($this->cookie->involved or $involved)
            ->andWhere('t2.openedBy', true)->eq($this->app->user->account)
            ->orWhere('t2.PM')->eq($this->app->user->account)
            ->markRight(1)
            ->fi()
            ->beginIF($branch !== '' and $branch !== 'all')->andWhere('t1.branch')->in($branch)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll('id');

        /* Determine how to display the name of the program. */
        $programList = $this->loadModel('program')->getParentPairs('', 'noclosed');
        foreach($projectList as $id => $project)
        {
            $projectList[$id]->programName = $project->parent ? zget($programList, $project->parent, '') : '';
            $projectList[$id]->programName = preg_replace('/\//', '', $projectList[$id]->programName, 1);
        }

        return $projectList;
    }

    /**
     * Get project stats by product.
     *
     * @param  int       $productID
     * @param  string    $browseType
     * @param  int       $branch
     * @param  int       $involved
     * @param  string    $orderBy
     * @access public
     * @return array
     */
    public function getProjectStatsByProduct($productID, $browseType = 'all', $branch = 0, $involved = 0, $orderBy = 'order_desc')
    {
        $projects = $this->getProjectListByProduct($productID, $browseType, $branch, $involved, $orderBy);
        if(empty($projects)) return array();

        $projectKeys = array_keys($projects);
        $stats       = array();
        $hours       = array();
        $emptyHour   = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);

        /* Get all tasks and compute totalEstimate, totalConsumed, totalLeft, progress according to them. */
        if($this->config->systemMode == 'new')
        {
            $tasks = $this->dao->select('id, project, estimate, consumed, `left`, status, closedReason')
                ->from(TABLE_TASK)
                ->where('project')->in($projectKeys)
                ->andWhere('parent')->lt(1)
                ->andWhere('deleted')->eq(0)
                ->fetchGroup('project', 'id');
        }
        else
        {
            $tasks = $this->dao->select('id, execution, estimate, consumed, `left`, status, closedReason')
                ->from(TABLE_TASK)
                ->where('execution')->in($projectKeys)
                ->andWhere('parent')->lt(1)
                ->andWhere('deleted')->eq(0)
                ->fetchGroup('execution', 'id');
        }
        /* Compute totalEstimate, totalConsumed, totalLeft. */
        foreach($tasks as $projectID => $projectTasks)
        {
            $hour = (object)$emptyHour;
            foreach($projectTasks as $task)
            {
                if($task->status != 'cancel')
                {
                    $hour->totalEstimate += $task->estimate;
                    $hour->totalConsumed += $task->consumed;
                }
                if($task->status != 'cancel' and $task->status != 'closed') $hour->totalLeft += $task->left;
            }
            $hours[$projectID] = $hour;
        }

        /* Compute totalReal and progress. */
        foreach($hours as $hour)
        {
            $hour->totalEstimate = round($hour->totalEstimate, 1) ;
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft     = round($hour->totalLeft, 1);
            $hour->totalReal     = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress      = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 2) * 100 : 0;
        }

        /* Get the number of project teams. */
        $teams = $this->dao->select('root,count(*) as teams')->from(TABLE_TEAM)
            ->where('root')->in($projectKeys)
            ->andWhere('type')->eq('project')
            ->groupBy('root')
            ->fetchAll('root');

        /* Process projects. */
        foreach($projects as $key => $project)
        {
            if($project->end == '0000-00-00') $project->end = '';

            /* Judge whether the project is delayed. */
            if($project->status != 'done' and $project->status != 'closed' and $project->status != 'suspended')
            {
                $delay = helper::diffDate(helper::today(), $project->end);
                if($delay > 0) $project->delay = $delay;
            }

            /* Process the hours. */
            $project->hours = isset($hours[$project->id]) ? $hours[$project->id] : (object)$emptyHour;

            $project->teamCount = isset($teams[$project->id]) ? $teams[$project->id]->teams : 0;
            $stats[] = $project;
        }
        return $stats;
    }

    /**
     * Get executions by product and project.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $orderBy
     * @param  int    $projectID
     * @param  string $mode stagefilter or empty
     * @access public
     * @return array
     */
    public function getExecutionPairsByProduct($productID, $branch = 0, $orderBy = 'id_asc', $projectID = 0, $mode = '')
    {
        if(empty($productID)) return array();
        if(empty($projectID) or $this->config->systemMode == 'classic') return $this->getAllExecutionPairsByProduct($productID, $branch);

        $project = $this->loadModel('project')->getByID($projectID);
        $orderBy = $project->model == 'waterfall' ? 'begin_asc,id_asc' : 'begin_desc,id_desc';

        $executions = $this->dao->select('t2.id,t2.name,t2.grade,t2.parent,t2.attribute')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.project')->eq($projectID)
            ->beginIF($branch !== '')->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll('id');

        /* The waterfall project needs to show the hierarchy and remove the parent stage. */
        $executionList = array('0' => '');
        if($project->model == 'waterfall')
        {
            foreach($executions as $id => $execution)
            {
                if($execution->grade == 2 and isset($executions[$execution->parent]))
                {
                    $execution->name = $executions[$execution->parent]->name . '/' . $execution->name;
                    $executions[$execution->parent]->children[$id] = $execution->name;
                    unset($executions[$id]);
                }
            }

            foreach($executions as $execution)
            {
                if(isset($execution->children))
                {
                    $executionList = $executionList + $execution->children;
                    continue;
                }
                if(strpos($mode, 'stagefilter') !== false and in_array($execution->attribute, array('request', 'design', 'review'))) continue; // Some stages of waterfall not need.
                $executionList[$execution->id] = $execution->name;
            }
        }
        else
        {
            foreach($executions as $execution) $executionList[$execution->id] = $execution->name;
        }

        return $executionList;
    }

    /**
     * Get execution pairs by product.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return array
     */
    public function getAllExecutionPairsByProduct($productID, $branch = 0)
    {
        if(empty($productID)) return array();
        $executions = $this->dao->select('t2.id,t2.project,t2.name,t2.grade,t2.parent')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->in('stage,sprint')
            ->beginIF($branch)->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->fetchAll('id');

        $projectIdList = array();
        foreach($executions as $id => $execution) $projectIdList[$execution->project] = $execution->project;

        $executionPairs = array(0 => '');
        $projectPairs   = $this->loadModel('project')->getPairsByIdList($projectIdList, 'all');
        foreach($executions as $id => $execution)
        {
            if($execution->grade == 2 && isset($executions[$execution->parent]))
            {
                $execution->name = $projectPairs[$execution->project] . '/' . $executions[$execution->parent]->name . '/' . $execution->name;
                $executions[$execution->parent]->children[$id] = $execution->name;
                unset($executions[$id]);
            }
        }

        foreach($executions as $execution)
        {
            if(isset($execution->children))
            {
                $executionPairs = $executionPairs + $execution->children;
                continue;
            }
           if($this->config->systemMode == 'new' and isset($projectPairs[$execution->project])) $executionPairs[$execution->id] = $projectPairs[$execution->project] . '/' . $execution->name;
           if($this->config->systemMode == 'classic') $executionPairs[$execution->id] = $execution->name;
        }

        return $executionPairs;
    }

    /**
     * Get roadmap of a proejct.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $count
     * @access public
     * @return array
     */
    public function getRoadmap($productID, $branch = 0, $count = 0)
    {
        $plans    = $this->loadModel('productplan')->getList($productID, $branch);
        $releases = $this->loadModel('release')->getList($productID, $branch);
        $roadmap  = array();
        $total    = 0;

        $parents      = array();
        $orderedPlans = array();
        foreach($plans as $planID => $plan)
        {
            if($plan->parent == '-1')
            {
                $parents[$planID] = $plan->title;
                unset($plans[$planID]);
                continue;
            }
            if((!helper::isZeroDate($plan->end) and strtotime($plan->end) - time() <= 0) or $plan->end == '2030-01-01') continue;
            $orderedPlans[$plan->end][] = $plan;
        }

        krsort($orderedPlans);
        foreach($orderedPlans as $plans)
        {
            krsort($plans);
            foreach($plans as $plan)
            {
                if($plan->parent > 0 and isset($parents[$plan->parent])) $plan->title = $parents[$plan->parent] . ' / ' . $plan->title;

                $year = substr($plan->end, 0, 4);
                $roadmap[$year][$plan->branch][] = $plan;
                $total++;

                if($count > 0 and $total >= $count) return $this->processRoadmap($roadmap);
            }
        }

        $orderedReleases = array();
        foreach($releases as $release) $orderedReleases[$release->date][] = $release;

        krsort($orderedReleases);
        foreach($orderedReleases as $releases)
        {
            krsort($releases);
            foreach($releases as $release)
            {
                $year = substr($release->date, 0, 4);
                $roadmap[$year][$release->branch][] = $release;
                $total++;

                if($count > 0 and $total >= $count) return $this->processRoadmap($roadmap);
            }
        }

        if($count > 0) return $this->processRoadmap($roadmap);

        $groupRoadmap = array();
        foreach($roadmap as $year => $branchRoadmaps)
        {
            foreach($branchRoadmaps as $branch => $roadmaps)
            {
                $totalData = count($roadmaps);
                $rows      = ceil($totalData / 8);
                $maxPerRow = ceil($totalData / $rows);

                $groupRoadmap[$year][$branch] = array_chunk($roadmaps, $maxPerRow);
                foreach($groupRoadmap[$year][$branch] as $row => $rowRoadmaps) krsort($groupRoadmap[$year][$branch][$row]);
            }
        }

        /* Get last 5 roadmap. */
        $lastKeys    = array_slice(array_keys($groupRoadmap), 0, 5);
        $lastRoadmap = array();
        $lastRoadmap['total'] = 0;
        foreach($lastKeys as $key)
        {
            if($key == '2030')
            {
                $lastRoadmap[$this->lang->productplan->future] = $groupRoadmap[$key];
            }
            else
            {
                $lastRoadmap[$key] = $groupRoadmap[$key];
            }

            foreach($groupRoadmap[$key] as $branchRoadmaps) $lastRoadmap['total'] += (count($branchRoadmaps, 1) - count($branchRoadmaps));
        }

        return $lastRoadmap;
    }

    /**
     * Process roadmap.
     *
     * @param  array  $roadmap
     * @access public
     * @return array
     */
    public function processRoadmap($roadmapGroups)
    {
        $newRoadmap = array();
        foreach($roadmapGroups as $year => $branchRoadmaps)
        {
            foreach($branchRoadmaps as $branch => $roadmaps)
            {
                foreach($roadmaps as $roadmap) $newRoadmap[] = $roadmap;
            }
        }
        krsort($newRoadmap);
        return $newRoadmap;
    }

    /**
     * Get team members of a product from projects.
     *
     * @param  object   $product
     * @access public
     * @return array
     */
    public function getTeamMemberPairs($product)
    {
        $members[$product->PO] = $product->PO;
        $members[$product->QD] = $product->QD;
        $members[$product->RD] = $product->RD;
        $members[$product->createdBy] = $product->createdBy;

        /* Set projects and teams as static thus we can only query sql one times. */
        static $projects, $teams;
        if(empty($projects))
        {
            $projects = $this->dao->select('t1.project, t1.product')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t2.deleted')->eq(0)
                ->fetchGroup('product', 'project');
        }
        if(empty($teams))
        {
            $teams = $this->dao->select('t1.root, t1.account')->from(TABLE_TEAM)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.root = t2.id')
                ->where('t2.deleted')->eq(0)
                ->andWhere('t1.type')->eq('project')
                ->fetchGroup('root', 'account');
        }

        if(!isset($projects[$product->id])) return $members;
        $productProjects = $projects[$product->id];

        $projectTeams = array();
        foreach(array_keys($productProjects) as $projectID) $projectTeams = array_merge($projectTeams, array_keys($teams[$projectID]));

        return array_flip(array_merge($members, $projectTeams));
    }

    /**
     * Get product stat by id
     *
     * @param  int    $productID
     * @param  string $storyType
     * @access public
     * @return object|bool
     */
    public function getStatByID($productID, $storyType = 'story')
    {
        if(!$this->checkPriv($productID)) return false;

        $product = $this->getById($productID);
        if(empty($product)) return false;

        $stories = $this->dao->select('product, status, count(status) AS count')->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq($storyType)
            ->andWhere('product')->eq($productID)
            ->groupBy('product, status')
            ->fetchAll('status');

        /* Padding the stories to sure all status have records. */
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $stories[$status] = isset($stories[$status]) ? $stories[$status]->count : 0;
        }

        $plans    = $this->dao->select('count(*) AS count')->from(TABLE_PRODUCTPLAN)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->andWhere('end')->gt(helper::now())->fetch();
        $builds   = $this->dao->select('count(*) AS count')->from(TABLE_BUILD)->where('product')->eq($productID)->andWhere('deleted')->eq(0)->fetch();
        $cases    = $this->dao->select('count(*) AS count')->from(TABLE_CASE)->where('product')->eq($productID)->andWhere('deleted')->eq(0)->fetch();
        $bugs     = $this->dao->select('count(*) AS count')->from(TABLE_BUG)->where('product')->eq($productID)->andWhere('deleted')->eq(0)->fetch();
        $docs     = $this->dao->select('count(*) AS count')->from(TABLE_DOC)->where('product')->eq($productID)->andWhere('deleted')->eq(0)->fetch();
        $releases = $this->dao->select('count(*) AS count')->from(TABLE_RELEASE)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $projects = $this->dao->select('count("t1.*") AS count')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->fetch();

        $executions = $this->dao->select('count("t1.*") AS count')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->andWhere('t2.type')->in('sprint,stage,kanban')
            ->fetch();

        $product->stories    = $stories;
        $product->plans      = $plans      ? $plans->count : 0;
        $product->releases   = $releases   ? $releases->count : 0;
        $product->builds     = $builds     ? $builds->count : 0;
        $product->cases      = $cases      ? $cases->count : 0;
        $product->projects   = $projects   ? $projects->count : 0;
        $product->executions = $executions ? $executions->count : 0;
        $product->bugs       = $bugs       ? $bugs->count : 0;
        $product->docs       = $docs       ? $docs->count : 0;

        $closedTotal = $this->dao->select('count(id) AS count')->from(TABLE_STORY)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->andWhere('status')->eq('closed')->fetch('count');
        $allTotal    = $this->dao->select('count(id) AS count')->from(TABLE_STORY)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch('count');
        $product->progress = empty($closedTotal) ? 0 : round($closedTotal / $allTotal * 100, 1);

        return $product;
    }

    /**
     * Get product stats.
     *
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $status
     * @param  int    $line
     * @param  string $storyType requirement|story
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getStats($orderBy = 'order_asc', $pager = null, $status = 'noclosed', $line = 0, $storyType = 'story', $programID = 0)
    {
        $this->loadModel('report');
        $this->loadModel('story');
        $this->loadModel('bug');

        $products = $this->getList($programID, $status, $limit = 0, $line);
        if(empty($products)) return array();

        $productKeys = array_keys($products);
        if($orderBy == 'program_asc' and $this->config->systemMode == 'new')
        {
            $products = $this->dao->select('t1.id as id, t1.*')->from(TABLE_PRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
                ->where('t1.id')->in($productKeys)
                ->orderBy('t2.order_asc, t1.line_desc, t1.order_desc')
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $products = $this->dao->select('*')->from(TABLE_PRODUCT)
                ->where('id')->in($productKeys)
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        $linePairs = $this->getLinePairs();
        foreach($products as $product) $product->lineName = zget($linePairs, $product->line, '');

        $stories = $this->dao->select('product, status, type, count(status) AS count')
            ->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('story')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product, status')
            ->fetchGroup('product', 'status');

        $requirements = $this->dao->select('product, status, type, count(status) AS count')
            ->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('requirement')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product, status')
            ->fetchGroup('product', 'status');

        /* Padding the stories to sure all products have records. */
        $emptyStory = array_keys($this->lang->story->statusList);
        foreach($productKeys as $productID)
        {
            if(!isset($stories[$productID]))      $stories[$productID]      = $emptyStory;
            if(!isset($requirements[$productID])) $requirements[$productID] = $emptyStory;
        }

        /* Padding the stories to sure all status have records. */
        foreach($stories as $key => $story)
        {
            foreach(array_keys($this->lang->story->statusList) as $status)
            {
                $story[$status] = isset($story[$status]) ? $story[$status]->count : 0;
            }
            $stories[$key] = $story;
        }
        foreach($requirements as $key => $requirement)
        {
            foreach(array_keys($this->lang->story->statusList) as $status)
            {
                $requirement[$status] = isset($requirement[$status]) ? $requirement[$status]->count : 0;
            }
            $requirements[$key] = $requirement;
        }

        if($storyType == 'requirement') $stories = $requirements;

        $plans = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productKeys)
            ->andWhere('end')->gt(helper::now())
            ->groupBy('product')
            ->fetchPairs();

        $releases = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

        $bugs = $this->dao->select('product,count(*) AS conut')
            ->from(TABLE_BUG)
            ->where('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->groupBy('product')
            ->fetchPairs();

        $unResolved = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('status')->eq('active')
            ->andWhere('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->groupBy('product')
            ->fetchPairs();

        $fixedBugs = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('status')->eq('closed')
            ->andWhere('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->andWhere('resolution')->eq('fixed')
            ->groupBy('product')
            ->fetchPairs();

        $closedBugs = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('status')->eq('closed')
            ->andWhere('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->groupBy('product')
            ->fetchPairs();

        $this->app->loadClass('date', true);
        $weekDate     = date::getThisWeek();
        $thisWeekBugs = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('openedDate')->between($weekDate['begin'], $weekDate['end'])
            ->andWhere('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->groupBy('product')
            ->fetchPairs();

        $assignToNull = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('assignedTo')->eq('')
            ->andWhere('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->groupBy('product')
            ->fetchPairs();

        if(empty($programID))
        {
            $programKeys = array(0 => 0);
            foreach($products as $product) $programKeys[] = $product->program;
            $programs = $this->dao->select('id,name')->from(TABLE_PROGRAM)
                ->where('id')->in(array_unique($programKeys))
                ->fetchPairs();

            foreach($products as $product) $product->programName = isset($programs[$product->program]) ? $programs[$product->program] : '';
        }

        $stats = array();
        foreach($products as $key => $product)
        {
            $product->stories      = $stories[$product->id];
            $product->requirements = $requirements[$product->id];
            $product->plans        = isset($plans[$product->id])    ? $plans[$product->id]    : 0;
            $product->releases     = isset($releases[$product->id]) ? $releases[$product->id] : 0;

            $product->bugs         = isset($bugs[$product->id]) ? $bugs[$product->id] : 0;
            $product->unResolved   = isset($unResolved[$product->id]) ? $unResolved[$product->id] : 0;
            $product->closedBugs   = isset($closedBugs[$product->id]) ? $closedBugs[$product->id] : 0;
            $product->fixedBugs    = isset($fixedBugs[$product->id]) ? $fixedBugs[$product->id] : 0;
            $product->thisWeekBugs = isset($thisWeekBugs[$product->id]) ? $thisWeekBugs[$product->id] : 0;
            $product->assignToNull = isset($assignToNull[$product->id]) ? $assignToNull[$product->id] : 0;

            $closedTotal       = $product->stories['closed'] + $product->requirements['closed'];
            $allTotal          = array_sum($product->stories) + array_sum($product->requirements);
            $product->progress = empty($closedTotal) ? 0 : round($closedTotal / $allTotal * 100, 1);

            $stats[] = $product;
        }

        return $stats;
    }

    /**
     * Get stats for product kanban.
     *
     * @access public
     * @return array
     */
    public function getStats4Kanban()
    {
        $date = date('Y-m-d');
        $this->loadModel('program');

        $productList = $this->getList();
        $programList = $this->program->getTopPairs('', '', true);
        $projectList = $this->program->getProjectStats(0, 'doing');

        $projectProduct = $this->dao->select('t1.product,t1.project')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
            ->where('t1.product')->in(array_keys($productList))
            ->andWhere('t1.project')->in($this->app->user->view->projects)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.status')->eq('doing')
            ->andWhere('t2.deleted')->eq('0')
            ->fetchGroup('product', 'project');

        $planList = $this->dao->select('id,product,title,parent,begin,end')->from(TABLE_PRODUCTPLAN)
            ->where('product')->in(array_keys($productList))
            ->andWhere('deleted')->eq(0)
            ->andWhere('end')->ge($date)
            ->andWhere('parent')->ne(-1)
            ->orderBy('begin desc')
            ->fetchGroup('product', 'id');

        $executionListKey = $this->config->systemMode == 'new' ? 'project' : 'productID';
        $executionList    = $this->dao->select('t1.product as productID,t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.project=t2.id')
            ->where('type')->in('stage,sprint')
            ->beginIF($this->config->systemMode == 'new')->andWhere('t2.project')->in(array_keys($projectList))->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t1.project')->in($this->app->user->view->sprints)->fi()
            ->andWhere('t1.product')->in(array_keys($productList))
            ->andWhere('status')->eq('doing')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchGroup($executionListKey, 'id');

        $projectLatestExecutions = array();
        $latestExecutionList     = array();
        if($this->config->systemMode == 'new')
        {
            foreach($executionList as $projectID => $executions)
            {
                /* Used to computer execution progress. */
                $latestExecutionList[key($executions)] = current($executions);

                /* Used for display in page. */
                $projectLatestExecutions[$projectID] = current($executions);
            }
        }

        if($this->config->systemMode == 'classic')
        {
            foreach($executionList as $productID => $executions)
            {
                foreach($executions as $executionID => $execution) $latestExecutionList[$executionID] = $execution;
            }
        }

        $hourList = $this->loadModel('project')->computerProgress($latestExecutionList);

        $releaseList = $this->dao->select('id,product,name,marker')->from(TABLE_RELEASE)
            ->where('deleted')->eq('0')
            ->andWhere('product')->in(array_keys($productList))
            ->andWhere('status')->eq('normal')
            ->fetchGroup('product', 'id');

        return array('programList' => $programList, 'productList' => $productList, 'planList' => $planList, 'projectList' => $projectList, 'executionList' => $executionList, 'projectProduct' => $projectProduct, 'projectLatestExecutions' => $projectLatestExecutions, 'hourList' => $hourList, 'releaseList' => $releaseList);
    }

    /**
     * Get product line pairs.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function getLinePairs($programID = 0)
    {
        return $this->dao->select('id,name')->from(TABLE_MODULE)
            ->where('type')->eq('line')
            ->beginIF($programID)->andWhere('root')->eq($programID)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
    }

    /**
     * Get the summary of product's stories.
     *
     * @param  array    $stories
     * @param  string   $storyType  story|requirement
     * @access public
     * @return string.
     */
    public function summary($stories, $storyType = 'story')
    {
        $totalEstimate = 0.0;
        $storyIdList   = array();

        $rateCount = 0;
        $allCount  = 0;
        foreach($stories as $key => $story)
        {
            if(!empty($story->type) && $story->type != $storyType) continue;

            $totalEstimate += $story->estimate;
            /* When the status is not closed or closedReason is done or postponed then add cases rate..*/
            if(
                $story->status != 'closed' or
                ($story->status == 'closed' and ($story->closedReason == 'done' or $story->closedReason == 'postponed'))
            )
            {
                $storyIdList[] = $story->id;
                $rateCount ++;
            }

            $allCount ++;
            if(!empty($story->children))
            {
                foreach($story->children as $child)
                {
                    if($child->type != $storyType) continue;

                    if(
                        $child->status != 'closed' or
                        ($child->status == 'closed' and ($child->closedReason == 'done' or $child->closedReason == 'postponed'))
                    )
                    {
                        $storyIdList[] = $child->id;
                        $rateCount ++;
                    }
                    $allCount ++;
                }
            }
        }

        $cases = $this->dao->select('story')->from(TABLE_CASE)->where('story')->in($storyIdList)->andWhere('deleted')->eq(0)->fetchAll('story');
        $rate  = count($stories) == 0 || $rateCount == 0 ? 0 : round(count($cases) / $rateCount, 2);

        $storyCommon = $this->lang->SRCommon;
        if($storyType == 'requirement') $storyCommon = $this->lang->URCommon;
        if($storyType == 'story')       $storyCommon = $this->lang->SRCommon;

        return sprintf($this->lang->product->storySummary, $allCount,  $storyCommon, $totalEstimate, $rate * 100 . "%");
    }

    /**
     * Statistics program data.
     *
     * @param  object $productStats
     * @access public
     * @return array
     */
    public function statisticProgram($productStats)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProductStats();

        $productStructure = array();

        foreach($productStats as $product)
        {
            $productStructure[$product->program][$product->line]['products'][$product->id] = $product;
            if($product->line)
            {
                /* Line name. */
                $productStructure[$product->program][$product->line]['lineName'] = $product->lineName;
                $productStructure[$product->program][$product->line] = $this->statisticData('line', $productStructure, $product);
            }

            if($product->program)
            {
                /* Init vars. */
                /* Program name. */
                $productStructure[$product->program]['programName'] = $product->programName;
                $productStructure[$product->program] = $this->statisticData('program', $productStructure, $product);
            }
        }
        return $productStructure;
    }

    /**
     * Statistic product data.
     *
     * @param  string $type
     * @param  array  $productStructure
     * @param  object $product
     * @access public
     * @return void
     */
    public function statisticData($type = 'program', $productStructure = array(), $product = null)
    {
        if(empty($productStructure)) return $productStructure;

        /* Init vars. */
        $data = $type == 'program' ? $productStructure[$product->program] : $productStructure[$product->program][$product->line];
        foreach($this->config->product->statisticFields as $key => $fields)
        {
            /* Get the total number of requirements and stories. */
            if(strpos('stories|requirements', $key) !== false)
            {
                $totalObjects = 0;
                foreach($product->$key as $status => $number)
                {
                    if(isset($this->lang->story->statusList[$status])) $totalObjects += $number;
                }

                $fieldType = $key == 'stories' ? 'Stories' : 'Requirements';
                if(!isset($data['total' . $fieldType])) $data['total' . $fieldType] = 0;
                $data['total' . $fieldType] += $totalObjects;
            }
            elseif($key == 'bugs')
            {
                $fieldType = 'Bugs';
            }

            foreach($fields as $field)
            {
                if(!isset($data[$field])) $data[$field] = 0;

                $status = $field;
                if(strpos($field, 'Requirements') !== false or strpos($field, 'Stories') !== false or $field == 'unResolvedBugs')
                {
                    $length = strpos($field, $fieldType);
                    $status = substr($field, 0, $length);
                }

                if(strpos('requirements|stories', $key) !== false)
                {
                    $objects = $product->$key;
                    $data[$field] += $objects[$status];
                }
                else
                {
                    $data[$field] += $product->$status;
                }
            }
        }
        return $data;
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object $product
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($product, $action)
    {
        $action = strtolower($action);

        if($action == 'close') return $product->status != 'closed';

        return true;
    }

    /**
     * Create the link from module,method,extra,branch.
     *
     * @param  string  $module
     * @param  string  $method
     * @param  string  $extra
     * @param  bool    $branch
     * @access public
     * @return void
     */
    public function getProductLink($module, $method, $extra, $branch = false)
    {
        $link = '';
        if(strpos(',programplan,product,roadmap,bug,testcase,testtask,story,qa,testsuite,testreport,build,projectrelease,projectstory,', ',' . $module . ',') !== false)
        {
            if($module == 'product' && $method == 'project')
            {
                $link = helper::createLink($module, $method, "status=all&productID=%s" . ($branch ? "&branch=%s" : ''));
            }
            elseif($module == 'product' && ($method == 'doc' or $method == 'view'))
            {
                $link = helper::createLink($module, $method, "productID=%s");
            }
            elseif($module == 'product' && $method == 'dynamic')
            {
                $link = helper::createLink($module, $method, "productID=%s&type=$extra");
            }
            elseif($module == 'product' && $method == 'create')
            {
                $link = helper::createLink($module, 'browse', "productID=%s&type=$extra");
            }
            elseif($module == 'qa' && $method == 'index')
            {
                $link = helper::createLink('bug', 'browse', "productID=%s" . ($branch ? "&branch=%s" : ''));
            }
            elseif($module == 'product' && ($method == 'browse' or $method == 'index' or $method == 'all'))
            {
                $link = helper::createLink($module, 'browse', "productID=%s" . ($branch ? "&branch=%s" : '&branch=0') . "&browseType=&param=0&$extra");
            }
            elseif($module == 'programplan')
            {
                $extra = $extra ? $extra : 'gantt';
                $link  = helper::createLink($module, 'browse', "projectID=%s&productID=%s&type=$extra");
            }
            elseif($module == 'story' && $method == 'report')
            {
                $link = helper::createLink($module, 'report', "productID=%s" . ($branch ? "&branch=%s" : '&branch=0') . "&extra=$extra");
            }
            elseif($module == 'testtask')
            {
                $extra = $method != 'browse' ? '' : "&extra=$extra";
                if(strtolower($method) == 'browseunits')
                {
                    $methodName = 'browseUnits';
                    $param      = '&browseType=newest&orderBy=id_desc&recTotal=0&recPerPage=0&pageID=1';
                    $param     .= $this->app->tab == 'project' ? "&projectID={$this->session->project}" : '';
                }
                else
                {
                    $methodName = 'browse';
                    $param      = ($branch ? "&branch=%s" : '&branch=0') . $extra;
                }

                $link = helper::createLink($module, $methodName, "productID=%s" . $param);
            }
            elseif($module == 'bug' && $method == 'view')
            {
                $link = helper::createLink('bug', 'browse', "productID=%s" . ($branch ? "&branch=%s" : '&branch=0') . "&extra=$extra");
            }
            elseif($module == 'testsuite' && !in_array($method, array('browse', 'create')))
            {
                $link = helper::createLink('testsuite', 'browse', "productID=%s");
            }
            elseif(($module == 'testcase' and $method == 'groupCase') or ($module == 'story' and $method == 'zeroCase') and $this->app->tab == 'project')
            {
                parse_str($extra, $output);
                $projectID = isset($output['projectID']) ? $output['projectID'] : 0;
                $link      = helper::createLink($module, $method, "productID=%s&branch=" . ($branch ? "%s" : '') . "&groupBy=&projectID=$projectID") . "#app=project";
            }
            elseif($module == 'testcase' and $method == 'browse')
            {
                $link = helper::createLink('testcase', 'browse', "productID=%s" . ($branch ? "&branch=%s" : '&branch=all') . "&browseType=$extra");
            }
            elseif($module == 'testreport' and ($method == 'create' or $method == 'edit'))
            {
                $vars   = $method == 'edit' ? "objectID=%s" : "objectID=&objectType=testtask&extra=%s";
                $method = $method == 'edit' ? 'browse' : $method;
                $link   = helper::createLink($module, $method, $vars);
            }
            else
            {
                $link = helper::createLink($module, $method, "productID=%s" . ($branch ? "&branch=%s" : ''));
            }
        }
        else if($module == 'productplan' || $module == 'release')
        {
            if($method != 'browse' && $method != 'create') $method = 'browse';
            $link = helper::createLink($module, $method, "productID=%s" . ($branch ? "&branch=%s" : ''));
        }
        else if($module == 'tree')
        {
            $link = helper::createLink($module, $method, "productID=%s&type=$extra&currentModuleID=0" . ($branch ? "&branch=%s" : ''));
        }
        else if($module == 'branch')
        {
            $link = helper::createLink($module, $method, "productID=%s");
        }
        else if($module == 'doc')
        {
            if($method == 'create' or $method == 'edit') $method = 'tableContents';
            $link = helper::createLink('doc', $method, "type=product&objectID=%s&from=product");
        }
        elseif($module == 'design')
        {
            return helper::createLink('design', 'browse', "productID=%s");
        }
        elseif($module == 'project' and $method == 'testcase')
        {
            $params = explode(',', $extra);
            return helper::createLink('project', 'testcase', "projectID={$params[0]}&productID=%s&branch=all&browseType={$params[1]}");
        }
        elseif($module == 'project' or $module == 'execution')
        {
            $objectID = $module == 'project' ? 'projectID' : 'executionID';
            return helper::createLink($module, $method, "$objectID=$extra&productID=%s");
        }

        return $link;
    }

    /**
     * Setting parameters for link.
     *
     * @param  string $module
     * @param  string $link
     * @param  int    $projectID
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function setParamsForLink($module, $link, $projectID, $productID)
    {
        $linkHtml = strpos('programplan', $module) !== false ? sprintf($link, $projectID, $productID) : sprintf($link, $productID);
        return $linkHtml;
    }

    /**
     * get the latest project of the product.
     *
     * @param  int     $productID
     * @access public
     * @return object
     */
    public function getLatestProject($productID)
    {
        return $this->dao->select('t2.id, t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq((int)$productID)
            ->andWhere('t2.status')->ne('closed')
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy('t2.begin desc')
            ->limit(1)
            ->fetch();
    }

    /**
     * Change the projects set of the program.
     *
     * @param  int   $productID
     * @param  array $singleLinkProjects
     * @param  array $multipleLinkProjects
     * @access public
     * @return void
     */
    public function updateProjects($productID, $singleLinkProjects = array(), $multipleLinkProjects = array())
    {
        $programID = $_POST['program'];
        foreach($singleLinkProjects as $projectID => $projectName)
        {
            if($projectName)
            {
                $this->dao->update(TABLE_PROJECT)
                    ->set('parent')->eq($programID)
                    ->set('path')->eq(',' . $programID . ',' . $projectID . ',')
                    ->where('id')->eq($projectID)
                    ->exec();
            }
        }

        foreach($multipleLinkProjects as $projectID => $projectName)
        {
            if(strpos($_POST['changeProjects'], ',' . $projectID . ',') !== false)
            {
                $this->dao->delete()->from(TABLE_PROJECTPRODUCT)
                    ->where('project')->eq($projectID)
                    ->andWhere('product')->ne($productID)
                    ->exec();

                $this->dao->update(TABLE_PROJECT)
                    ->set('parent')->eq($programID)
                    ->set('path')->eq(',' . $programID . ',' . $projectID . ',')
                    ->where('id')->eq($projectID)
                    ->exec();

                $this->loadModel('action')->create('project', $projectID, 'Managed', '', $productID);
            }
            else
            {
                $this->dao->delete()->from(TABLE_PROJECTPRODUCT)
                    ->where('project')->eq($projectID)
                    ->andWhere('product')->eq($productID)
                    ->exec();

                $newProducts = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs('product','product');
                $this->loadModel('action')->create('project', $projectID, 'Managed', '', join(',', $newProducts));
            }
        }
    }

    /**
     *
     * Set menu.
     *
     * @param int         $productID
     * @param int|string  $branch
     * @param int         $module
     * @param string      $moduleType
     * @param string      $extra
     *
     * @access public
     * @return void
     */
    public function setMenu($productID, $branch = '', $module = 0, $moduleType = '', $extra = '')
    {
        if(!$this->app->user->admin and strpos(",{$this->app->user->view->products},", ",$productID,") === false and $productID != 0 and !defined('TUTORIAL')) die(js::error($this->lang->product->accessDenied) . js::locate('back'));

        $product = $this->getByID($productID);
        $params  = array('branch' => $branch);
        common::setMenuVars('product', $productID, $params);
        if(!$product) return;

        $this->lang->switcherMenu = $this->getSwitcher($productID, $extra, $branch);

        if($product->type == 'normal')
        {
            unset($this->lang->product->menu->settings['subMenu']->branch);
        }
        else
        {
            $branchLink = $this->lang->product->menu->settings['subMenu']->branch['link'];
            $this->lang->product->menu->settings['subMenu']->branch['link'] = str_replace('@branch@', $this->lang->product->branchName[$product->type], $branchLink);
            $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
        }
    }
}
