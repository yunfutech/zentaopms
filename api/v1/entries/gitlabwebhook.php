<?php
/**
 * The repo entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      xiawenlong <xiawenlong@cnezsoft.com>
 * @package     repo
 * @version     1
 * @link        http://www.zentao.net
 */
class gitlabWebhookEntry extends baseEntry
{

    /**
     * Repo webhook.
     *
     * @access public
     * @return void
     */
    public function post()
    {
        $repoID = $this->param('repoID');
        if(empty($repoID)) return;

        $this->loadModel('repo');

        $repo = $this->repo->getRepoByID($repoID);
        if(empty($repo)) return;

        $headers = getallheaders(); /* Fetch all HTTP request headers. */
        $event   = isset($headers['X-Gitlab-Event']) ? $headers['X-Gitlab-Event'] : '';
        $token   = isset($headers['X-Gitlab-Token']) ? $headers['X-Gitlab-Token'] : '';
        if(empty($event) || empty($token)) return;

        $this->repo->handleWebhook($event, $token, $this->requestBody, $repo);
    }

}
