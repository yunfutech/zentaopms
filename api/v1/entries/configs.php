<?php
/**
 * The configs entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class configsEntry extends baseEntry
{
    /**
     * GET method.
     *
     * @access public
     * @return void
     */
    public function get()
    {
        $configs = array();

        $configs[] = array('key' => 'language',   'value' => $this->config->default->lang);
        $configs[] = array('key' => 'version',    'value' => $this->config->version);
        $configs[] = array('key' => 'charset',    'value' => $this->config->charset);
        $configs[] = array('key' => 'timezone',   'value' => $this->config->timezone);
        $configs[] = array('key' => 'systemMode', 'value' => $this->config->systemMode);

        $this->send(200, $configs);
    }
}
