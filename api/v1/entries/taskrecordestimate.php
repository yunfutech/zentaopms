<?php
/**
 * The task recordEstimate entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class taskRecordEstimateEntry extends Entry
{
    /**
     * GET method.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function get($taskID)
    {
        if($this->config->edition != 'open')
        {
            $control = $this->loadController('effort', 'createForObject');
            $control->createForObject('task', $taskID);
        }
        else
        {
            $control = $this->loadController('task', 'recordEstimate');
            $control->recordEstimate($taskID);
        }

        $data = $this->getData();
        if(!$data) return $this->error('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $effort = array();
        if($this->config->edition != 'open' and $data->data->efforts)   $effort = $data->data->efforts;
        if($this->config->edition == 'open' and $data->data->estimates) $effort = $data->data->estimates;
        $this->send(200, array('effort' => $effort));

    }

    /**
     * POST method.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function post($taskID)
    {
        if($this->config->edition != 'open')
        {
            $fields = 'id,dates,consumed,left,objectType,objectID,work';
            $this->batchSetPost($fields);
            $control = $this->loadController('effort', 'createForObject');
            $control->createForObject('task', $taskID);
        }
        else
        {
            $fields = 'id,dates,consumed,left,work';
            $this->batchSetPost($fields);
            $control = $this->loadController('task', 'recordEstimate');
            $control->recordEstimate($taskID);
        }

        $data = $this->getData();
        if(!$data) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $task = $this->loadModel('task')->getById($taskID);

        $this->send(200, $this->format($task, 'deadline:date,openedBy:user,openedDate:time,assignedTo:user,assignedDate:time,realStarted:time,finishedBy:user,finishedDate:time,closedBy:user,closedDate:time,canceledBy:user,canceledDate:time,lastEditedBy:user,lastEditedDate:time,deleted:bool,mailto:userList'));
    }
}
