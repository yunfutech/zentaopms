<?php
/**
 * The modules entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class modulesEntry extends Entry
{
    /**
     * Get method.
     *
     * @access public
     * @return void
     */
    public function get()
    {
        $objectType = $this->param('type', '');
        $objectID   = $this->param('id', '');

        if(!$objectType or !$objectID) return $this->sendError(400, 'Need id and type.');
        if(!in_array($objectType, array('story', 'task', 'bug', 'case'))) return $this->sendError(400, 'Type is not allowed');

        if($objectType == 'task')
        {
            $control = $this->loadController('tree', 'browsetask');
            $control->browseTask($objectID);
        }
        else
        {
            $control = $this->loadController('tree', 'browse');
            $control->browse($objectID, $objectType);
        }
        $data = $this->getData();
        if(isset($data->status) and $data->status == 'success') return $this->send(200, array('modules' => $data->data->tree));

        return $this->send400(isset($data->message) ? $data->message: 'error');
    }
}
