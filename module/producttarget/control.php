<?php

class producttarget extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);

        $this->loadModel('product');
    }

    /**
     * 创建里程碑
     */
    public function create($productID)
    {
        $product = $this->product->getById($productID);
        $oldTargets = $this->producttarget->getOldTargets($productID);
        $thisMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime("$thisMonth -1 month"));
        if (isset($oldTargets[$lastMonth])) {
            $this->view->lastTarget = $oldTargets[$lastMonth]->performance;
        }
        $this->view->name = $product->name . '月目标';
        $this->view->month = date('Y-m');
        $this->view->oldTargets = $oldTargets;

        if (!empty($_POST)) {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->producttarget->create($productID);
            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('product', 'producttarget', "productID=" . $productID);
            $this->send($response);
        }
        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $this->view->title      = $this->lang->producttarget->create;
        $this->view->position[] = $this->lang->producttarget->create;

        $this->view->productID = $productID;
        $this->display();
    }

    /**
     * 编辑月目标
     */
    public function edit($producttargetID, $productID)
    {
        if(!empty($_POST))
        {
            if ($_POST['performance']!= 0 && $_POST['cause'] == '' && $_POST['target'] != $_POST['performance']) {
                die(js::alert('请输入进度偏差原因'));
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->producttarget->edit($producttargetID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('product', 'producttarget', "productID=" . $productID);
            $this->send($response);
        }
        $producttarget = $this->producttarget->getById($producttargetID);

        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $this->view->title      = $this->lang->producttarget->edit;
        $this->view->position[] = $this->lang->producttarget->edit;

        $this->view->producttarget = $producttarget;
        $this->view->productID = $productID;
        $this->display();
    }

    /**
     * 删除月目标
     */
    public function delete($producttargetID, $productID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->producttarget->confirmDelete, $this->createLink('producttarget', 'delete', "producttargetID=" . $producttargetID . "&productID=" . $productID . "&confirm=yes")));
        }
        else
        {
            $this->producttarget->delete($producttargetID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            die(js::locate($this->createLink('product', 'producttarget', "productID=" . $productID), 'parent'));
        }
    }

    /**
     * 查看月目标详情
     */
    public function view($producttargetID, $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $producttarget = $this->producttarget->getById($producttargetID);
        $productID = $producttarget->product;
        $product   = $this->product->getById($productID);

        $this->app->loadClass('pager', $static=true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $producttargetitems = $this->producttarget->getItems($producttargetID, $pager, $sort);
        $this->view->producttargetitems = $producttargetitems;

        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);
        $this->view->title      = $this->lang->producttarget->view;
        $this->view->position[] = $this->lang->producttarget->view;

        $this->view->producttarget = $producttarget;
        $this->view->productID = $productID;
        $this->view->product = $product;

        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * 批量创建本月目标
     */
    public function batchCreateItem($producttargetID)
    {
        if (!empty($_POST)) {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->producttarget->batchCreateItem($producttargetID);
            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('producttarget', 'view', "producttargetID=" . $producttargetID);
            $this->send($response);
        }

        $producttarget = $this->producttarget->getById($producttargetID);


        $productID = $producttarget->product;
        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $this->view->title      = $this->lang->producttargetitem->create;
        $this->view->position[] = $this->lang->producttargetitem->create;

        $this->view->productID = $productID;
        $this->display();
    }

    /**
     * 删除目标
     */
    public function deleteItem($producttargetitemID, $producttargetID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->producttarget->confirmDelete, $this->createLink('producttarget', 'deleteItem', "producttargetitemID=" . $producttargetitemID . "&producttargetID=" . $producttargetID . "&confirm=yes")));
        }
        else
        {
            $this->producttarget->deleteItem($producttargetitemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            die(js::locate($this->createLink('producttarget', 'view', "producttargetID=" . $producttargetID), 'parent'));
        }
    }

    /**
     * 编辑目标
     */
    public function editItem($producttargetitemID, $producttargetID)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->producttarget->editItem($producttargetitemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('producttarget', 'view', "producttargetID=" . $producttargetID);
            $this->send($response);
        }
        $producttarget = $this->producttarget->getById($producttargetID);
        $producttargetitem = $this->producttarget->getItemById($producttargetitemID);

        $productID = $producttarget->product;

        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $this->view->title      = $this->lang->producttargetitem->edit;
        $this->view->position[] = $this->lang->producttargetitem->edit;

        $this->view->producttargetitem = $producttargetitem;
        $this->display();
    }
}