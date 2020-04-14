<?php

class productweekly extends control
{
    public function view($weeklyID, $productID)
    {
        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $weekly = $this->productweekly->getWeeklyById($weeklyID);

        $hyperdown    = $this->app->loadClass('hyperdown');
        $weekly->content = $hyperdown->makeHtml($weekly->content);

        $this->view->title      = $this->lang->productweekly->view;
        $this->view->position[] = $this->lang->productweekly->view;

        $this->view->weekly = $weekly;
        $this->view->weeklyID = $weeklyID;
        $this->view->productID = $productID;
        $this->display();
    }

    public function edit($weeklyID, $productID)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->productweekly->edit($weeklyID, $_POST);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = inlink('view', 'weeklyID=' . $weeklyID . '&productID=' . $productID);
            $this->send($response);
        }
        $this->loadModel('product')->setMenu($this->loadModel('product')->getPairs('nocode'), $productID);

        $weekly = $this->productweekly->getWeeklyById($weeklyID);
        $this->view->weekly = $weekly;

        $this->view->title      = $this->lang->productweekly->edit;
        $this->view->position[] = $this->lang->productweekly->edit;

        $this->view->weeklyID = $weeklyID;
        $this->view->productID = $productID;
        $this->display();
    }

    public function generateWeekly($productID)
    {
        $response['result']  = 'success';
        $response['message'] = $this->lang->product->generateSuccess;
        $product   = $this->loadModel('product')->getById($productID);
        $this->productweekly->generateWeekly($productID, $product->name);
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $response['locate']  = $this->createLink('product', 'weekly', "productID=" . $productID);
        $this->send($response);
    }
}