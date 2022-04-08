<?php
/**
 * The model file of owt module of XXB.
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
class owtModel extends model
{
    /**
     * Make a header with signature for the use of owt api requests.
     *
     * @return array
     */
    function makeHeader()
    {
        $this->loadModel('setting');

        /* $cnonce should be an int between 0 and 99999. */
        $cnonce = rand(0, 99999);

        /* $timestamp should include milliseconds. */
        $timestamp = round(microtime(true) * 1000);

        $auth = array();
        $auth['Mauth realm']            = 'http://webrtc.intel.com';
        $auth['mauth_signature_method'] = 'HMAC_SHA256';
        $auth['mauth_serviceid']        = $this->setting->getItem("owner=system&module=owt&section=common&key=serviceid");
        $auth['mauth_cnonce']           = $cnonce;
        $auth['mauth_timestamp']        = $timestamp;

        /* Generate the signature and convert to base64. */
        $rawSignature = hash_hmac('sha256', $timestamp . ',' . $cnonce, $this->setting->getItem("owner=system&module=owt&section=common&key=servicekey"));
        $auth['mauth_signature'] = base64_encode($rawSignature);

        /* Implode the keys and values of the $auth array into a string connected by comma and linebreak along with contentType json. */
        return array('Authorization: ' . urldecode(http_build_query($auth, '', ',')), 'Content-Type: application/json');
    }

    /**
     * Make request to owt server with given path, method and params.
     *
     * @param  string       $path
     * @param  string       $method
     * @param  string|array $params
     * @return string
     */
    function makeRequest($path, $method = 'GET', $params = '')
    {
        /* Convert params to JSON string if it is object or array. */
        if(is_array($params)) $params = json_encode($params);

        $this->loadModel('setting');
        $owtServer   = $this->setting->getItem("owner=system&module=owt&section=common&key=serveraddr");
        $owtMgmtPort = $this->setting->getItem("owner=system&module=owt&section=common&key=mgmtport");
        $owtHttps    = $this->setting->getItem("owner=system&module=owt&section=common&key=https");
        $owtHttps    = $owtHttps != 'false';

        /* Prepare curl handler and set its params. */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ($owtHttps ? 'https' : 'http') . "://{$owtServer}:{$owtMgmtPort}{$path}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->makeHeader());
        /* Allow the specification of other methods like DELETE and PUT. */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        /* Allow "unsafe" connections. */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /* Prevent curl from long waiting. */
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        /* Execute request and return result. */
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * Create a room with given name in owt server.
     *
     * @param  string $name
     * @access public
     * @return array
     */
    public function createRoom($name)
    {
        $roomConfig = array('name' => $name);
        $data = $this->makeRequest('/v1/rooms', 'POST', $roomConfig);
        $room = json_decode($data);
        if(isset($room->error)) return false;
        // if xxb video config, then update room
        $owt = $this->queryVideoConfig();
        if(!empty($owt) && !empty($room)) $this->updateRoomByVideoConfig($owt, $room);
        return $room;
    }

    /**
     * Batch update room by video config.
     *
     * @access public
     * @return void
     */
    public function batchUpdateRoom()
    {
        $data = $this->listRooms();
        $roomListInfo = json_decode($data);
        $owt = $this->queryVideoConfig();
        foreach($roomListInfo as $roomInfo) $this->updateRoomByVideoConfig($owt, $roomInfo);
    }

    /**
     * Quey room video config.
     *
     * @access public
     * @return object
     */
    public function queryVideoConfig()
    {
        $owt = new stdclass();
        $configItems = $this->loadModel('setting')->getItems("owner=system&module=owt&section=common&key=resolutionwidth,resolutionheight");
        foreach($configItems as $configItem) $owt->{$configItem->key} = $configItem->value;
        return $owt;
    }

    /**
     * Update room  by video config.
     *
     * @access public
     * @return array
     */
    public function updateRoomByVideoConfig($owt, $room)
    {
        if(!empty($owt->resolutionwidth) && !empty($owt->resolutionheight))
        {
            $room->views[0]->video->parameters->resolution->width  = $owt->resolutionwidth;
            $room->views[0]->video->parameters->resolution->height = $owt->resolutionheight;
        }
        return $this->updateRoom($room->id, json_encode($room));
    }

    /**
     * Get a room by given id from owt server.
     *
     * @param  string $roomId
     * @access public
     * @return array
     */
    public function getRoom($roomId)
    {
        $data = $this->makeRequest("/v1/rooms/$roomId");
        $room = json_decode($data);
        if(isset($room->error)) return false;
        return $room;
    }

    /**
     * Create room list from owt server.
     *
     * @access public
     * @return array
     */
    public function listRooms()
    {
        return $this->makeRequest('/v1/rooms');
    }

    /**
     * Delete a room by given id in owt server.
     *
     * @param  string $roomId
     * @access public
     * @return array
     */
    public function deleteRoom($roomId)
    {
        return $this->makeRequest("/v1/rooms/$roomId", 'DELETE');
    }

    /**
     * Update a room with given id and room data in owt server.
     *
     * @param  string       $roomId
     * @param  string|array $roomData
     * @access public
     * @return array
     */
    public function updateRoom($roomId, $roomData)
    {
        return $this->makeRequest("/v1/rooms/$roomId", 'PUT', $roomData);
    }

    /**
     * Get current owt configuration.
     *
     * @param  string $type to get config for xxc, type should be 'client'.
     * @access public
     * @return object
     */
    public function getConfiguration($type = '')
    {
        $owt = new stdclass();
        $configItems = $this->loadModel('setting')->getItems("owner=system&module=owt&section=common&key=enabled,serviceid,servicekey,serveraddr,apiport,mgmtport,https,resolutionwidth,resolutionheight");
        foreach($configItems as $configItem) $owt->{$configItem->key} = $configItem->value;

        if($type == 'client')
        {
            if(!isset($owt->enabled) || $owt->enabled == 'false' || empty($owt->serveraddr) || empty($owt->serviceid) || empty($owt->servicekey)) return false;

            $conf = new stdClass();
            $conf->host  = $owt->serveraddr;
            $conf->https = !(isset($owt->https) && $owt->https === 'false');
            $conf->api   = ($conf->https ? 'https' : 'http') . "://{$owt->serveraddr}:{$owt->apiport}";
            return $conf;
        }

        return $owt;
    }

    /**
     * Check and set incoming owt configuration.
     *
     * @param  array $config
     * @access public
     * @return void
     */
    public function setConfiguration()
    {
        $post = fixer::input('post')
            ->setIF($this->post->serviceId  != '', 'serviceId',  trim($this->post->serviceId))
            ->setIF($this->post->serviceKey != '', 'serviceKey', trim($this->post->serviceKey))
            ->setIF($this->post->serverAddr != '', 'serverAddr', trim($this->post->serverAddr))
            ->setIF($this->post->apiPort    != '', 'apiPort',    trim($this->post->apiPort))
            ->setIF($this->post->mgmtPort   != '', 'mgmtPort',   trim($this->post->mgmtPort))
            ->get();

        $errors = array();
        foreach(array('serviceId', 'serviceKey', 'serverAddr', 'apiPort', 'mgmtPort') as $item)
        {
            if(empty($post->{$item})) $errors[$item][]  = $this->lang->owt->inputError->{$item};
        }
        if(!empty($errors)) return array('result' => 'fail', 'message' => $errors);

        $this->loadModel('setting');
        $this->setting->setItem('system.owt.common.enabled',    !isset($post->enabled) || empty($post->enabled) ? 'false' : 'true');
        $this->setting->setItem('system.owt.common.https',      !isset($post->https) || empty($post->https) ? 'false' : 'true');
        $this->setting->setItem('system.owt.common.serviceid',  $post->serviceId);
        $this->setting->setItem('system.owt.common.servicekey', $post->serviceKey);
        $this->setting->setItem('system.owt.common.serveraddr', $post->serverAddr);
        $this->setting->setItem('system.owt.common.apiport',    $post->apiPort);
        $this->setting->setItem('system.owt.common.mgmtport',   $post->mgmtPort);

        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('admin'));
    }

    /**
     * Check and set incoming owt video configuration.
     *
     * @param  array $config
     * @access public
     * @return void
     */
    public function setVideoConfiguration()
    {
        $post = fixer::input('post')
            ->setIF($this->post->resolutionWidth  != '', 'resolutionWidth',  trim($this->post->resolutionWidth))
            ->setIF($this->post->resolutionHeight != '', 'resolutionHeight', trim($this->post->resolutionHeight))
            ->get();

        $errors = array();
        foreach(array('resolutionWidth', 'resolutionHeight') as $item)
        {
            if(empty($post->{$item})) $errors[$item][]  = $this->lang->owt->inputError->{$item};
        }
        if(!empty($errors)) return array('result' => 'fail', 'message' => $errors);

        $this->loadModel('setting');
        $this->setting->setItem('system.owt.common.resolutionwidth', $post->resolutionWidth);
        $this->setting->setItem('system.owt.common.resolutionheight', $post->resolutionHeight);

        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('admin'));
    }

    /**
     * Check if owt functionality is enabled.
     *
     * @access public
     * @return boolean
     */
    public function isEnabled()
    {
        return filter_var($this->loadModel('setting')->getItem("owner=system&module=owt&section=common&key=enabled"), FILTER_VALIDATE_BOOLEAN);
    }
}
