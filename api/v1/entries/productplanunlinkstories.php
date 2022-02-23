<?php
/**
 * The productplanunlinkstories entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class productplanUnlinkStoriesEntry extends entry
{
    /**
     * POST method.
     *
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function post($planID)
    {
        $productplan = $this->loadModel('productplan');
        foreach($this->request('stories', array()) as $storyID)
        {
            $productplan->unlinkStory($storyID, $planID);
            if(dao::isError()) return $this->sendError('error');
        }

        $control = $this->loadController('productplan', 'view');
        $control->view($planID);

        $data = $this->getData();
        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $plan = $data->data->plan;
        $plan->stories = $data->data->planStories;
        $plan->bugs    = $data->data->planBugs;

        $plan = $this->format($plan, 'begin:date,end:date,deleted:bool,stories:array,bugs:array');

        return $this->send(200, $plan);
    }
}
