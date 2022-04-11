<?php
/**
 * The story assignto entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class storyAssignToEntry extends Entry
{
    /**
     * POST method.
     *
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function post($storyID)
    {
        $story = $this->loadModel('story')->getByID($storyID);

        $fields = 'assignedTo';
        $this->batchSetPost($fields);

        $control = $this->loadController('story', 'assignTo');
        $control->assignTo($storyID);

        $data = $this->getData();
        if(!$data) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $story = $this->story->getByID($storyID);

        $this->send(200, $this->format($story, 'openedBy:user,openedDate:time,assignedTo:user,assignedDate:time,reviewedBy:user,reviewedDate:time,lastEditedBy:user,lastEditedDate:time,closedBy:user,closedDate:time,deleted:bool,mailto:userList'));
    }
}
