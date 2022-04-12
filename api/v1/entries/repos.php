<?php
/**
 * The repos entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class reposEntry extends entry
{
    /**
     * GET method.
     *
     * @access public
     * @return void
     */
    public function get()
    {
        $control = $this->loadController('repo', 'maintain');
        $control->maintain(0, $this->param('order', 'id_desc'), 0, $this->param('limit', 100), $this->param('page', 1));

        /* Response */
        $data = $this->getData();
        if(isset($data->status) and $data->status == 'success')
        {
            $result = array();
            $pager  = $data->data->pager;
            $repos  = $data->data->repoList;
            foreach($repos as $repo) $result[] = $this->format($repo, 'deleted:bool,lastSync:datetime,synced:bool,product:idList');

            return $this->send(200, array('page' => $pager->pageID, 'total' => $pager->recTotal, 'limit' => $pager->recPerPage, 'repos' => $result));
        }

        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);
        return $this->sendError(400, 'error');
    }
}
