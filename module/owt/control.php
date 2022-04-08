<?php
/**
 * The control file of owt module of XXB.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZOSL (https://zpl.pub/page/zoslv1.html)
 * @author      Wenrui LI <liwenrui@easycorp.ltd>
 * @package     owt
 * @version     $Id$
 * @link        https://xuanim.com
 */
?>
<?php
class owt extends control
{
    /**
     * View and set owt configuration.
     *
     * @param  string $type type could be 'server', 'video', 'edit'
     * @access public
     * @return void
     */
    public function admin($type = 'server')
    {
        if(!empty($_POST))
        {
            if($type == 'video')
            {
                $result = $this->owt->setVideoConfiguration($_POST);
                //update all room video config now
                $data = $this->owt->batchUpdateRoom();
            }
            else
            {
                $result = $this->owt->setConfiguration($_POST);
            }
            $this->send($result);
        }

        $owt = $this->owt->getConfiguration();

        $this->view->type             = $type;
        $this->view->enabled          = isset($owt->enabled) && $owt->enabled == 'true';
        $this->view->serviceId        = isset($owt->serviceid) ? $owt->serviceid : '';
        $this->view->serviceKey       = isset($owt->servicekey) ? $owt->servicekey : '';
        $this->view->serverAddr       = isset($owt->serveraddr) ? $owt->serveraddr : '';
        $this->view->apiPort          = isset($owt->apiport) ? $owt->apiport : '';
        $this->view->mgmtPort         = isset($owt->mgmtport) ? $owt->mgmtport : '';
        $this->view->https            = !isset($owt->https) || $owt->https == 'true';
        $this->view->resolutionWidth  = isset($owt->resolutionwidth) ? $owt->resolutionwidth : '';
        $this->view->resolutionHeight = isset($owt->resolutionheight) ? $owt->resolutionheight : '';

        $this->display();
    }
}
