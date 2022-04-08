<?php
/**
 * The file entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class fileEntry extends Entry
{
    /**
     * GET method.
     *
     * @access public
     * @return void
     */
    public function get($fileID)
    {
        $control = $this->loadController('file', 'download');
        $control->download($fileID);

        $data = $this->getData();
        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $this->send(200, $data);
    }
    /**
     * PUT method.
     *
     * @access public
     * @return void
     */
    public function put($fileID)
    {
        $uid = $this->param('uid', '');
        $action = $this->param('action', '');
        if($action == 'remove') unset($_SESSION['album']['used'][$uid][$fileID]);

        $this->send(200, array('id' => $fileID));
    }
}
