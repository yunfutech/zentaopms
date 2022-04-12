<?php
/**
 * The reporules entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class repoRulesEntry extends entry
{
    /**
     * GET method.
     *
     * @access public
     * @return void
     */
    public function get()
    {
        $control = $this->loadController('repo', 'ajaxGetRules');
        $control->ajaxGetRules();

        $data = $this->getData();

        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $this->send(200, $data->rules);
    }
}
