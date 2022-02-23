<?php
/**
 * The execution entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class executionEntry extends Entry
{
    /**
     * GET method.
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function get($executionID)
    {
        $fields = $this->param('fields');

        $control = $this->loadController('execution', 'view');
        $control->view($executionID);

        $data = $this->getData();
        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $execution = $this->format($data->data->execution, 'openedBy:user,openedDate:time,lastEditedBy:user,lastEditedDate:time,closedBy:user,closedDate:time,canceledBy:user,canceledDate:time,PM:user,PO:user,RD:user,QD:user,whitelist:userList,begin:date,end:date,realBegan:date,realEnd:date,deleted:bool');

        $execution->progress    = ($execution->totalConsumed + $execution->totalLeft) ? round($execution->totalConsumed / ($execution->totalConsumed + $execution->totalLeft) * 100, 1) : 0;
        $execution->teamMembers = array_values((array)$data->data->teamMembers);
        $execution->products    = array();
        foreach($data->data->products as $productID => $executionProduct)
        {
            $product = new stdclass();
            $product->id = $executionProduct->id;
            $product->name = $executionProduct->name;
            $product->plans = array();
            foreach($executionProduct->plans as $planID)
            {
                $plan = new stdclass();
                $plan->id = $planID;
                $plan->name = $data->data->planGroups->{$productID}->{$planID};
                $product->plans[] = $plan;
            }
            $execution->products[] = $product;
        }

        $this->loadModel('testcase');
        $execution->caseReview = ($this->config->testcase->needReview or !empty($this->config->testcase->forceReview));

        if(!$fields) $this->send(200, $execution);

        $users = $data->data->users;

        /* Set other fields. */
        $fields = explode(',', strtolower($fields));
        foreach($fields as $field)
        {
            switch($field)
            {
                case 'modules':
                    $control = $this->loadController('tree', 'browsetask');
                    $control->browsetask($executionID);
                    $data = $this->getData();
                    if(isset($data->status) and $data->status == 'success')
                    {
                        $execution->modules = $data->data->tree;
                    }
                    break;
                case 'moduleoptionmenu':
                    $execution->moduleOptionMenu = $this->loadModel('tree')->getTaskOptionMenu($executionID, 0, 0, 'allModule');
                    break;
                case 'members':
                    $execution->members = $this->loadModel('user')->getTeamMemberPairs($executionID, 'execution', 'nodeleted');;
                    unset($execution->members['']);
                    break;
                case 'stories':
                    $stories = $this->loadModel('story')->getExecutionStories($executionID);
                    foreach($stories as $storyID => $story) $stories[$storyID] = $this->filterFields($story, 'id,title,module,pri,status,stage,estimate');

                    $execution->stories = array_values($stories);
                    break;
                case 'actions':
                    $actions = $data->data->actions;
                    $execution->actions = $this->loadModel('action')->processActionForAPI($actions, $users, $this->lang->execution);
                    break;
                case "dynamics":
                    $dynamics = $data->data->dynamics;
                    $execution->dynamics = $this->loadModel('action')->processDynamicForAPI($dynamics);
                    break;
            }
        }

        return $this->send(200, $execution);
    }

    /**
     * PUT method.
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function put($executionID)
    {
        $oldExecution = $this->loadModel('execution')->getByID($executionID);

        /* Set $_POST variables. */
        $fields = 'project,code,name,begin,end,lifetime,desc,days,acl,status';
        $this->batchSetPost($fields, $oldExecution);

        $this->setPost('whitelist', $this->request('whitelist', explode(',', $oldExecution->whitelist)));

        $control = $this->loadController('execution', 'edit');
        $control->edit($executionID);

        $data = $this->getData();
        if(isset($data->result) and $data->result == 'fail') return $this->sendError(400, $data->message);
        if(!isset($data->result)) return $this->sendError(400, 'error');

        $execution = $this->execution->getByID($executionID);
        $this->send(200, $this->format($execution, 'openedBy:user,openedDate:time,lastEditedBy:user,lastEditedDate:time,closedBy:user,closedDate:time,canceledBy:user,canceledDate:time,PM:user,PO:user,RD:user,QD:user,whitelist:userList,begin:date,end:date,realBegan:date,realEnd:date,deleted:bool'));
    }

    /**
     * DELETE method.
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function delete($executionID)
    {
        $control = $this->loadController('execution', 'delete');
        $control->delete($executionID, 'true');

        $this->getData();
        $this->sendSuccess(200, 'success');
    }
}
