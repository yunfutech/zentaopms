<?php
/**
 * The testcases entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class testcasesEntry extends entry
{
    /**
     * GET method.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function get($productID = 0)
    {
        if(empty($productID)) $productID = $this->param('product', 0);
        if(empty($productID)) return $this->sendError(400, 'Need product id.');

        $type     = 'all';
        $param    = 0;
        $moduleID = $this->param('module', 0);
        if($moduleID)
        {
            $type  = 'byModule';
            $param = $moduleID;
        }

        $control = $this->loadController('testcase', 'browse');
        $control->browse($productID, $this->param('branch', ''), $type, $param, $this->param('order', 'id_desc'), 0, $this->param('limit', 20), $this->param('page', 1));

        $data = $this->getData();

        if(isset($data->status) and $data->status == 'success')
        {
            $cases  = $data->data->cases;
            $pager  = $data->data->pager;
            $result = array();
            foreach($cases as $case)
            {
                $case->statusName = $this->lang->testcase->statusList[$case->status];
                $result[] = $this->format($case, 'openedBy:user,openedDate:time,lastEditedBy:user,lastEditedDate:time,lastRunDate:time,scriptedDate:date,reviewedBy:user,reviewedDate:date,deleted:bool');
            }

            return $this->send(200, array('page' => $pager->pageID, 'total' => $pager->recTotal, 'limit' => $pager->recPerPage, 'testcases' => $result));
        }

        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);
        return $this->sendError(400, 'error');
    }

    /**
     * POST method.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function post($productID = 0)
    {
        if(!$productID) $productID = $this->param('product');
        if(!$productID and isset($this->requestBody->product)) $productID = $this->requestBody->product;
        if(!$productID) return $this->sendError(400, 'Need product id.');

        $fields = 'module,type,stage,story,title,precondition,pri';
        $this->batchSetPost($fields);
        $this->setPost('product', $productID);

        /* Set steps and expects. */
        if(isset($this->requestBody->steps))
        {
            $steps    = array();
            $expects  = array();
            $stepType = array();
            foreach($this->requestBody->steps as $step)
            {
                $type = isset($step->type) ? $step->type : 'step';

                $steps[]    = $step->desc;
                $expects[]  = $type == 'group' ? '' : $step->expect;
                $stepType[] = $type;
            }
            $this->setPost('steps',    $steps);
            $this->setPost('expects',  $expects);
            $this->setPost('stepType', $stepType);
        }

        $control = $this->loadController('testcase', 'create');
        $this->requireFields('title,type,pri,steps');

        $control->create(0);

        $data = $this->getData();
        if(isset($data->result) and $data->result == 'fail') return $this->sendError(400, $data->message);
        if(isset($data->result) and !isset($data->id)) return $this->sendError(400, $data->message);

        $case = $this->loadModel('testcase')->getByID($data->id);
        $case->steps = (isset($case->steps) and !empty($case->steps)) ? array_values($case->steps) : array();

        $this->send(200, $this->format($case, 'openedBy:user,openedDate:time,lastEditedBy:user,lastEditedDate:time,lastRunDate:time,scriptedDate:date,reviewedBy:user,reviewedDate:date,deleted:bool'));
    }
}
