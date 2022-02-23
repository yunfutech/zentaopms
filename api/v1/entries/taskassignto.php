<?php
/**
 * The task assignto entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class taskAssignToEntry extends Entry
{
    /**
     * POST method.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function post($taskID)
    {
        $task = $this->loadModel('task')->getByID($taskID);

        $fields = 'assignedTo,comment,left';
        $this->batchSetPost($fields);

        $control = $this->loadController('task', 'assignTo');
        $this->requireFields('assignedTo');

        $control->assignTo($task->execution, $taskID);

        $data = $this->getData();
        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $task = $this->loadModel('task')->getByID($taskID);

        $this->send(200, $this->format($task, 'openedDate:time,assignedDate:time,realStarted:time,finishedDate:time,canceledDate:time,closedDate:time,lastEditedDate:time'));
    }
}
