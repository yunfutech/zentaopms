<?php

class milestone extends control
{
    /**
     * 创建里程碑
     */
    public function create($productID)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->milestone->create($productID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('product', 'milestone', "productID=" . $productID);
            $this->send($response);
        }
        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $this->view->title      = $this->lang->milestone->create;
        $this->view->position[] = $this->lang->milestone->create;

        $this->view->productID = $productID;
        $this->display();
    }

    /**
     * 编辑里程碑
     */
    public function edit($milestoneID, $productID)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->milestone->edit($milestoneID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = $this->createLink('product', 'milestone', "productID=" . $productID);
            $this->send($response);
        }
        $milestone = $this->milestone->getById($milestoneID);

        $this->view->title      = $this->lang->milestone->create;
        $this->view->position[] = $this->lang->milestone->create;

        $this->view->milestone = $milestone;
        $this->view->productID = $productID;
        $this->display();
    }

    /**
     * 删除里程碑
     */
    public function delete($milestoneID, $productID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->milestone->confirmDelete, $this->createLink('milestone', 'delete', "milestoneID=" . $milestoneID . "&productID=" . $productID . "&confirm=yes")));
        }
        else
        {
            $this->milestone->delete($milestoneID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            die(js::locate($this->createLink('product', 'milestone', "productID=" . $productID), 'parent'));
        }
    }
}