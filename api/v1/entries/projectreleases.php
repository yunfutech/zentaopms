<?php
/**
 * The projectreleases entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class projectReleasesEntry extends entry
{
     /**
     * GET method.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function get($projectID = 0)
    {
        if(empty($projectID)) $projectID = $this->param('project');
        if(empty($projectID)) return $this->sendError(400, 'Need project id.');

        $control = $this->loadController('projectrelease', 'browse');
        $control->browse($projectID, $this->param('execution', 0), $this->param('status', 'all'), $this->param('order', 't1.date_desc'));

        /* Response */
        $data = $this->getData();

        if(isset($data->status) and $data->status == 'success')
        {
            $result   = array();
            $releases = $data->data->releases;
            foreach($releases as $release) $result[] = $this->format($release, 'deleted:bool,date:date,mailto:userList');

            return $this->send(200, array('total' => count($result), 'releases' => $result));
        }

        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);
        return $this->sendError(400, 'error');
    }

    /**
     * POST method.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function post($projectID = 0)
    {
        $project = $this->loadModel('project')->getByID($projectID);
        if(!$project) return $this->send404();

        $fields = 'name,build,product,date,notify,mailto';
        $this->batchSetPost($fields);

        $this->setPost('desc', $this->request('desc', ''));

        $control = $this->loadController('projectrelease', 'create');
        $this->requireFields('name,date');

        $control->create($projectID);

        $data = $this->getData();
        if(isset($data->result) and $data->result == 'fail') return $this->sendError(400, $data->message);
        if(isset($data->result) and!isset($data->id)) return $this->sendError(400, $data->message);

        $release = $this->loadModel('projectrelease')->getByID($data->id);

        $this->send(201, $release);
    }
}
