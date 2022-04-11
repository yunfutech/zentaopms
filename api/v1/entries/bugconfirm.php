<?php
/**
 * The bug confirm entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class bugConfirmEntry extends Entry
{
    /** 
     * POST method.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     **/
    public function post($bugID)
    {   
        $bug = $this->loadModel('bug')->getById($bugID);

        $fields = 'assignedTo,mailto,comment,pri,type';
        $this->batchSetPost($fields);

        $control = $this->loadController('bug', 'confirmBug');
        $control->confirmBug($bugID);

        $data = $this->getData();
        if(!$data) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $bug = $this->bug->getById($bugID);

        $this->send(200, $this->format($story, 'openedBy:user,openedDate:time,assignedTo:user,assignedDate:time,reviewedBy:user,reviewedDate:time,lastEditedBy:user,lastEditedDate:time,closedBy:user,closedDate:time,deleted:bool,mailto:userList'));
    }   
}

