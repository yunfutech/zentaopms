<?php
class im extends control
{
    /**
     * Server start.
     *
     * @access public
     * @return void
     */
    public function sysServerStart()
    {
        $this->im->setXxdStartTime();
        $this->im->userResetStatus();
        $this->im->userReindexPinyin();
        $this->im->chatInitSystemChat();
        $this->im->conferenceResetStatus();
        $this->im->updateLastPoll();

        $output = new stdClass();
        $output->result  = 'success';
        $output->version = $this->config->version;

        if(isset($this->config->fileEncryptionKey)) $output->fileKey = $this->config->fileEncryptionKey;

        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Get serverInfo api.
     *
     * @param  string    $account
     * @param  string    $password
     * @param  string    $apiVersion
     * @param  int       $userID
     * @param  string    $version
     * @param  string    $device
     * @access public
     * @return void
     */
    public function sysGetServerInfo($account, $password, $apiVersion = '', $userID = 0, $version = '', $device = 'desktop')
    {
        $this->app->input['device'] = $device; // set device into input for further uses.

        if(version_compare($version, $this->config->minClientVerson, '<'))
        {
            $output = new stdclass();
            $output->result = 'fail';
            $output->data   = 'Illegal Request.';
            $output->message = sprintf($this->lang->im->errorClientVersionNotSupport, $version, $this->config->minClientVerson);
            return $this->app->output($this->app->encrypt($output));
        }

        $user = $this->im->userIdentify($account, $password);
        if(!is_array($user)) $this->im->userAddAction($account, 'loginXuanxuan', 'fail');

        if(!$user)
        {
            $output = new stdclass();
            $output->result = 'fail';
            $output->data   = 'Illegal Request.';
            $output->message = $this->lang->user->loginFailed;
            return $this->app->output($this->app->encrypt($output));
        }

        if(is_string($user) && in_array($user, array('locked', 'banned')))
        {
            $output = new stdclass();
            $output->result = 'fail';
            $output->data   = $user;
            return $this->app->output($this->app->encrypt($output));
        }
        if($user == 'invalid_token')
        {
            $output = new stdclass();
            $output->result  = 'fail';
            $output->data    = 'Invalid Token.';
            $output->message = $this->lang->user->tokenInvalid;
            return $this->app->output($this->app->encrypt($output));
        }

        $upgradeInfo = $this->loadModel('client')->getUpgrade($version);

        $outputData = new stdclass();
        $outputData->clientUpdate            = empty($upgradeInfo) ? null : $upgradeInfo;
        $outputData->backend                 = $this->config->xuanxuan->backend;
        $outputData->permissions             = extCommonModel::getLicensePropertyValue('permissions');
        $outputData->backendURL              = $this->im->getServer() . $this->config->webRoot;
        $outputData->dismissedGroupLife      = isset($this->config->dismissedGroupLife) ? $this->config->dismissedGroupLife : 90; // Unit: Day
        $outputData->requestType             = $this->config->requestType;
        $outputData->requestFix              = $this->config->requestFix;

        /* Send owt configuration if available. */
        $owt = $this->loadModel('owt')->getConfiguration('client');
        if(!empty($owt)) $outputData->owt = $owt;

        /* Pushing related information for mobile devices.*/

        /* Send api scheme if client api version mismatch server api version. */
        $currentApiVersion = $this->config->maps['$version'];
        if($currentApiVersion != $apiVersion) $outputData->apiScheme = $this->im->getApiScheme();

        /* Send server local timestamp to client. */
        $outputData->serverTime = (double)(microtime(true) * 1000);

        /* Include generated token if there is one. (user was logon using password) */
        if(isset($user->token)) $outputData->authToken = $user->token;
        $outputData->authTokenLifetime   = (int)zget($this->config->xuanxuan, 'tokenLifetime', 30);
        $outputData->authTokenAuthWindow = (int)zget($this->config->xuanxuan, 'tokenAuthWindow', 20);
        if(isset($user->tokenNeedRenew)) $outputData->tokenNeedRenew = $user->tokenNeedRenew;

        /* Integration related stuff. */
        if(isset($this->config->integration))
        {
            $integratedFeatures = array();
            if(zget($this->config->integration->office, 'officeEnabled')) $integratedFeatures[] = 'collaboraOffice';
            $outputData->integration = $integratedFeatures;
        }

        $output = new stdclass();
        $output->result  = 'success';
        $output->users   = array($user->id);
        $output->userID  = $user->id;
        $output->method  = 'sysgetserverinfo';
        $output->device  = $device;
        $output->lang    = zget($this->app->input, 'lang');
        $output->rid     = zget($this->app->input, 'rid');
        $output->version = $this->config->version;
        $output->data    = $outputData;

        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Get depts dept and roles list
     *
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function sysGetDepts($userID)
    {
        $this->app->loadLang('user');

        $allDepts = $this->loadModel('dept')->getListByType('dept');
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Get depts fail'), 'messageResponsePack');

        $depts = array();
        foreach($allDepts as $id => $dept)
        {
            $depts[$id] = array('name' => $dept->name, 'order' => (int)$dept->order, 'parent' => (int)$dept->parent);
        }

        $data = new stdclass();
        $data->depts = $depts;
        $data->roles = $this->lang->user->roleList;
        unset($data->roles['']);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $data;

        return $this->im->sendOutput($output, 'sysgetdeptsResponse');
    }

    /**
     * Login.
     *
     * @param  string   $account  user account
     * @param  string   $password encrypted password
     * @param  object   $options  { simple: 0 | 1, status: 'online' | 'busy' | 'away'}
     * @param  int      $userID
     * @param  string   $version
     * @param  string   $device   desktop | mobile
     * @access public
     * @return void
     */
    public function userLogin($account = '', $password = '', $options = array(), $userID = 0, $version = '', $device = 'desktop')
    {
        $this->app->input['device'] = $device; // set device into input for further uses.

        $user   = $this->im->userIdentify($account, $password);
        $lang   = $this->app->input['lang'];
        $simple = $options && isset($options->simple) && $options->simple;

        if(!$user) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->user->loginFailed), 'messageResponsePack');

        $comment = json_encode(array('version' => 'xuanxuan-v' . (empty($version) ? '?' : $version)));
        if(is_string($user) && in_array($user, array('locked', 'banned', 'invalid_token')))
        {
            $this->im->userAddAction($account, $simple ? 'reconnectXuanxuan' : 'loginXuanxuan', 'fail', $comment);
            return $this->im->sendOutput(array('result' => 'fail', 'data' => $user));
        }

        $loginInfo = new stdclass();
        $loginInfo->result = 'success';
        $loginInfo->users  = $user->id;
        $loginInfo->method = 'userlogin';
        $loginInfo->device = $device;
        $loginInfo->lang   = $lang;

        /* Save client status and client lang of the user.*/
        $userData = new stdclass();
        $userData->id           = $user->id;
        $userData->clientStatus = 'online';
        $userData->clientLang   = $this->session->clientLang;
        $user = $this->im->userUpdate($userData);

        $this->im->userAddAction($user->id, $simple ? 'reconnectXuanxuan' : 'loginXuanxuan', 'success', $comment);

        /* Append signed time, backendUrl and status to user. */
        $user->status     = 'online';

        $loginInfo->data = $user;

        $loginInfo = $this->im->formatOutput($loginInfo, 'userloginResponse', $returnRaw = true);

        $userChatList = $this->im->chatGetListByUserID($user->id);
        $chatList     = $this->im->getChatListOutput($user->id, true, $userChatList);
        $conferences  = $this->im->getOpenConferencesOutput($user->id, true, $userChatList, true);

        $output = array($loginInfo);
        if(!empty($chatList))    $output[] = $chatList;
        if(!empty($conferences)) $output[] = $conferences;

        $response = $this->im->appendResponseHeader($output, $user->id, $user->id);

        $this->im->userUpdateDevice($user->id, $device, 'login');

        return $this->app->output($this->app->encrypt($response));
    }

    /**
     * Logout.
     *
     * @param  bool   $normal
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function userLogout($normal = false, $userID = 0)
    {
        $output = new stdClass();

        $onlineUserIdList = array_keys($this->im->userGetList($status = 'online'));
        if(!in_array($userID, $onlineUserIdList))
        {
            $this->im->userAddAction($userID, $normal ? 'logoutXuanxuan' : 'disconnectXuanxuan', 'fail');
            $output->result = 'fail';
            $output->data   = $this->im->userGetByID($userID);
            $output->users  = $onlineUserIdList;
            return $this->app->output($this->app->encrypt($output));
        }

        $user = new stdclass();
        $user->id           = $userID;
        $user->clientStatus = 'offline';
        $user = $this->im->userUpdate($user);

        $user->status = $user->clientStatus;

        $this->im->userAddAction($userID, $normal ? 'logoutXuanxuan' : 'disconnectXuanxuan', 'success');

        $this->im->conferenceRemoveUserFromConferences($userID);

        $this->im->userUpdateDevice($user->id, isset($this->app->input['device']) ? $this->app->input['device'] : 'default');

        session_destroy();

        $onlineUsers      = $this->im->userGetList($status = 'online');
        $onlineUserIdList = array_keys($onlineUsers);

        $output->result = 'success';
        $output->data   = $user;
        $output->users  = $onlineUserIdList;
        return $this->im->sendOutput($output, 'userlogoutResponse');
    }

    /**
     * Get user list.
     *
     * @param  array  $identities     array of userIDs or accounts.
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function userGetList($identities, $userID = 0)
    {
        $output = $this->im->getUserListOutput($identities, $userID, $returnRaw = false);
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Get user id list by dept with pager and sort rule
     *
     * @param  string   $deptID
     * @param  object   $pager
     * @param  string   $orderBy
     * @param  array    $exclude
     * @param  int      $userID
     * @access public
     * @return array
     */
    public function userGetListByDept($deptID, $pager, $orderBy, $exclude = array(), $onlySelf = false, $userID = 0)
    {
        if(empty($pager))   $pager = new stdclass();
        if(empty($orderBy)) $orderBy = 'id_asc';

        if(!isset($pager->pageID))     $pager->pageID     = 1;
        if(!isset($pager->recPerPage)) $pager->recPerPage = 50;
        if(!isset($pager->recTotal))   $pager->recTotal   = 0;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($pager->recTotal, $pager->recPerPage, $pager->pageID);

        $list = $this->im->user->getIDListByDept($deptID, $exclude, $pager, $orderBy, $onlySelf);
        $idList = array();
        foreach($list as $id) $idList[] = (int)$id;

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $idList;
        $output->pager  = new stdclass();
        $output->pager->recPerPage = $pager->recPerPage;
        $output->pager->pageID     = $pager->pageID;
        $output->pager->recTotal   = $pager->recTotal;
        $output->pager->data       = array('dept' => $deptID, 'orderBy' => $orderBy);

        return $this->im->sendOutput($output, 'usergetlistbydeptResponse');
    }

    /**
     * Get deleted users with their user ids.
     *
     * @param  array  $idList
     * @param  int    $userID
     * @return void
     * @deprecated Use "userGetList" instead
     */
    public function userGetDeleted($idList, $userID)
    {
        $output = $this->im->getUserListOutput($idList, $userID);
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Search for user with account / realname / pinyin in group / dept.
     *
     * @param  string  $search
     * @param  object  $options   {"chat": "3b320201-52e1-4061-90b2-23e750e8b6e0", "dept": 5, "limit": 51, "exclude": [3, 5, 7], "pager": {"pageID": 1, "recPerPage": 50, "recTotal": 0}}
     * @param  boolean $returnID
     * @param  integer $userID
     * @access public
     * @return void
     */
    public function userSearch($search, $options = array(), $returnID = false, $userID = 0)
    {
        $pager = null;
        if(property_exists($options, 'pager'))
        {
            $this->app->loadClass('pager', $static = true);
            $pager = pager::init(isset($options->pager->recTotal) ? $options->pager->recTotal : 0, isset($options->pager->recPerPage) ? $options->pager->recPerPage : 50, isset($options->pager->pageID) ? $options->pager->pageID : 1);
            unset($options->pager);
        }

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $this->im->userSearch($search, $options, $returnID, $pager);
        $output->pager  = $pager;
        return $this->im->sendOutput($output, $returnID ? 'usersearchidResponse' : 'usersearchResponse');
    }

    /**
     * Change a user.
     *
     * @param  array  $user
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function userUpdate($user = array(), $userID = 0)
    {
        $user = (object)$user;
        $user->id = $userID;
        if(isset($user->status) && !empty($user->status))
        {
            $user->clientStatus = $user->status;
            unset($user->status);
        }
        $user  = $this->im->userUpdate($user);

        if(dao::isError())
        {
            $this->im->userAddAction($userID, 'edit', 'fail');
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Update user fail'), 'messageResponsePack');
        }

        $users = $this->im->userGetList($status = 'online');

        $this->im->userAddAction($userID, 'edit', 'success');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $user;

        return $this->im->sendOutput($output, 'userupdateResponse');
    }

    /**
     * Upload or download settings.
     *
     * @param  string               $account
     * @param  string|array|object  $settings
     * @param  int                  $userID
     * @access public
     * @return void
     */
    public function userSyncSettings($account = '', $settings = '', $userID = 0)
    {
        $settingsObj  = new stdclass();
        $userSettings = json_decode($this->loadModel('setting')->getItem("owner=system&module=chat&section=settings&key=$account")) ?: new stdClass();
        if(is_object($settings))
        {
            /* Upload settings. */
            $settingsObj = $settings;
            foreach($settings as $key => $value) $userSettings->$key = $value;
            $this->setting->setItem("system.chat.settings.$account", helper::jsonEncode($userSettings));
        }
        elseif(is_array($settings))
        {
            /* Download the specified settings. */
            foreach($settings as $key) $settingsObj->$key = zget($userSettings, $key, '');
        }
        else
        {
            /* Download all settings. */
            $settingsObj = $userSettings;
        }

        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Save settings fail'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $settingsObj;
        return $this->im->sendOutput($output, 'usersyncsettingsResponse');
    }

    /**
     * Set push device token to user table.
     *
     * @param  string $deviceToken
     * @param  string $deviceType android|ios
     * @param  int $userID
     * @access public
     * @return void
     */
    public function userSetDeviceToken($deviceToken = '', $deviceType = 'android', $userID = 0)
    {
        $result = $this->loadModel('user')->setDeviceToken($deviceToken, $deviceType, $userID);
        return $this->im->sendOutput(array('result' => $result, 'users' => array($userID)), 'usersetdevicetokenResponse');
    }

    /**
     * Get a new auth token for user's device.
     *
     * @param  string      $deviceType   desktop|mobile|android|ios, optional
     * @param  string      $deviceID
     * @param  int         $userID
     * @access public
     * @return string|bool
     */
    public function userGetAuthToken($deviceType = '', $deviceID = '', $userID = 0)
    {
        $tokenObj = $this->im->userGetAuthToken($userID, $deviceType, $deviceID);

        $output = new stdclass();
        $output->result = empty($tokenObj) ? 'fail' : 'success';
        $output->users  = array($userID);
        $output->data   = !empty($tokenObj) ? $tokenObj->token : '';
        return $this->im->sendOutput($output, 'usergetauthtokenResponse');
    }

    /**
     * Renew or just generate auth token for user's device.
     *
     * @param  int         $userID
     * @param  string      $deviceType
     * @param  string      $deviceID
     * @access public
     * @return object|bool
     */
    public function userRenewAuthToken($deviceType = '', $deviceID = '', $userID = 0)
    {
        $tokenObj = $this->im->userRenewAuthToken($userID, $deviceType, $deviceID);

        $output = new stdClass();
        $output->result = empty($tokenObj) ? 'fail' : 'success';
        $output->users  = array($userID);
        $output->data   = !empty($tokenObj) ? $tokenObj->token : '';
        return $this->im->sendOutput($output, 'usergetauthtokenResponse');
    }

    /**
     * Get public chat list that user not join.
     *
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatGetPublicList($userID = 0)
    {
        $chatList = $this->im->chatGetPublicList($userID);

        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Get public chat list fail'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;
        $output->data   = $chatList;

        return $this->im->sendOutput($output, 'chatgetpubliclistResponse');
    }

    /**
     * Get chat list of a user.
     *
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatGetList($userID = 0)
    {
        $output = $this->im->getChatListOutput($userID);
        return $this->im->sendOutput($output);
    }

    /**
     * Get message count and last message id of chat.
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatGetMessageInfo($gid, $userID = 0)
    {
        $chat = $this->im->chatGetByGid($gid, false, false);
        $members = $this->im->chatGetMembers($chat);
        if(is_array($members) && !in_array($userID, $members)) $this->im->sendOutput(array('result' => 'fail', 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $chatInfo = new stdclass();
        $chatInfo->lastMessage  = intval($chat->lastMessage);
        $chatInfo->messageCount = $this->im->chatgetMessageCount($gid);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $chatInfo;

        return $this->im->sendOutput($output, 'chatgetmessageinfoResponse');
    }

    /**
     * Get last messages of chats by gids.
     *
     * @param  string|array $cgids
     * @param  int          $userID
     * @access public
     * @return void
     */
    public function chatGetLastMessage($cgids, $userID)
    {
        if(is_string($cgids)) $cgids = array($cgids);

        /* Check if user is in those chats. */
        $userChatList = $this->im->chatGetGidListByUserID($userID);
        $cgids = array_intersect($cgids, $userChatList);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $this->im->messageGetLast($cgids);

        return $this->im->sendOutput($output, 'chatgetlastmessageResponse');
    }

    /**
     * Get members of a chat.
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatGetMembers($gid = '', $userID = 0)
    {
        $members = $this->im->chatGetMembers($gid);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Get member list fail'));

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);

        $data = new stdclass();
        $data->gid     = $gid;
        $data->members = $members;

        $output->data = $data;
        return $this->im->sendOutput($output, 'chatgetmembersResponse');
    }

    /**
     * Create a chat.
     *
     * @param  string $gid
     * @param  string $name
     * @param  string $type
     * @param  array  $members
     * @param  int    $subjectID
     * @param  bool   $public    true: the chat is public | false: the chat isn't public.
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatCreate($gid = '', $name = '', $type = 'group', $members = array(), $subjectID = 0, $public = false, $userID = 0)
    {
        if($gid == 'notification' or $gid == 'littlexx') return $this->im->sendOutput(array('result' => 'success', 'users' =>$userID));

        $chat = $this->im->chat->getByGid($gid, true);

        if(!$chat)
        {
            $name = strip_tags($name);
            $chat = $this->im->chat->create($gid, $name, $type, $members, $subjectID, $public, $userID);
        }

        $users = $this->im->userGetList($status = 'online', $chat->members);

        if(dao::isError())
        {
            if($type == 'group') $this->im->chatAddAction(0, 'create', $userID, 'fail');
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Create chat fail'), 'messageResponsePack');
        }

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = array_keys($users);
        $output->data   = $chat;

        if($type == 'group')
        {
            $this->im->chatAddAction($chat->id, 'create', $userID, 'success');
            $broadcast = $this->im->messageCreateBroadcast('createChat', $chat, array_keys($users), $userID);
            if($broadcast) $output = array($output, $broadcast);

            return $this->im->sendOutputGroup($output);
        }

        return $this->im->sendOutput($output, 'chatcreateResponse');
    }

    /**
     * Set admins of a chat.
     *
     * @param  string $gid
     * @param  array  $admins
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatAddAdmins($gid = '', $admins = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        $account = $this->dao->select('account')->from(TABLE_USER)->where('id')->eq($userID)->fetch('account');
        if(empty($chat->ownedBy) && $chat->createdBy != $account) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');
        if(!empty($chat->ownedBy) && $chat->ownedBy != $account)  return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');

        $chat    = $this->im->chatAddAdmins($gid, $admins, $userID);
        $users   = $this->im->userGetList($status = 'online', $chat->members);
        $comment = json_encode(array('admins' => $admins));

        $this->im->chatAddAction($chat->id, 'addAdmins', $userID, 'success', $comment);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $chat;
        return $this->im->sendOutput($output, 'chataddadminsResponse');
    }

    /**
     * Remove admins of a chat.
     *
     * @param  string $gid
     * @param  array  $users
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatRemoveAdmins($gid = '', $admins = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        $account = $this->dao->select('account')->from(TABLE_USER)->where('id')->eq($userID)->fetch('account');
        if(empty($chat->ownedBy) && $chat->createdBy != $account) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');
        if(!empty($chat->ownedBy) && $chat->ownedBy != $account)  return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');

        $chat    = $this->im->chatRemoveAdmins($gid, $admins, $userID);
        $users   = $this->im->userGetList($status = 'online', $chat->members);
        $comment = json_encode(array('admins' => $admins));

        $this->im->chatAddAction($chat->id, 'removeAdmins', $userID, 'success', $comment);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $chat;
        return $this->im->sendOutput($output, 'chatremoveadminsResponse');
    }

    /**
     * Pin messages of a chat.
     *
     * @param  string $gid
     * @param  array  $messageIds
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatPinMessages($gid = '', $messageIds = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        if(!$this->im->chatIsAdmin($chat, $userID)) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notAdmin), 'messageResponsePack');

        $chat  = $this->im->chatPinMessages($chat, $messageIds);
        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = array('cgid' => $gid, 'pinned' => $messageIds, 'allPinned' => $chat->pinnedMessages);
        return $this->im->sendOutput($output, 'chatpinmessagesResponse');
    }

    /**
     * Unpin messages of a chat.
     *
     * @param  string $gid
     * @param  array  $messageIds
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatUnpinMessages($gid = '', $messageIds = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        if(!$this->im->chatIsAdmin($chat, $userID)) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notAdmin), 'messageResponsePack');

        $chat  = $this->im->chatUnpinMessages($chat, $messageIds);
        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = array('cgid' => $gid, 'unpinned' => $messageIds, 'allPinned' => $chat->pinnedMessages);
        return $this->im->sendOutput($output, 'chatunpinmessagesResponse');
    }

    /**
     * Join a chat.
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatJoin($gid = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');
        if($chat->public == '0')   return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notPublic), 'messageResponsePack');

        $this->im->chatJoin($gid, $userID);

        $chat  = $this->im->chat->getByGid($gid, true);
        $users = $this->im->userGetList($status = 'online', $chat->members);
        $users = array_keys($users);
        $users[] = $userID;

        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Join chat failed.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = $users;
        $output->data   = $chat;

        $broadcast = $this->im->messageCreateBroadcast('joinChat', $chat, $users, $userID);
        if($broadcast)
        {
            $output = array($output, $broadcast);
            return $this->im->sendOutputGroup($output);
        }
        return $this->im->sendOutput($output);
    }

    /**
     * Leave a chat.
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatLeave($gid = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');

        $this->im->chatLeave($gid, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Leave chat failed.'), 'messageResponsePack');

        $chat  = $this->im->chat->getByGid($gid, true);
        $users = $this->im->userGetList($status = 'online', $chat->members);
        $users = array_keys($users);
        $users[] = $userID;

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = $users;
        $output->data   = $chat;

        if(empty($chat->dismissDate))
        {
            $broadcast = $this->im->messageCreateBroadcast('leaveChat', $chat, $users, $userID);
            if($broadcast)
            {
                $output = array($output, $broadcast);
                return $this->im->sendOutputGroup($output);
            }
        }

        return $this->im->sendOutput($output, 'chatleaveResponse');
    }

    /**
     * Change the name of a chat.
     *
     * @param  string $gid
     * @param  string $name
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatRename($gid = '', $name = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        $isPrivateChat = $gid == "$userID&$userID";
        if($chat->type != 'group' && $chat->type != 'system' && !$isPrivateChat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');
        if(mb_strlen($name, 'utf8') > 16) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->chatNameTooLong), 'messageResponsePack');

        $chat->name = strip_tags($name);
        $chatID  = $chat->id;
        $comment = json_encode(array('name' => $name));
        $chat  = $this->im->chatUpdate($chat, $userID);
        if(dao::isError())
        {
            $this->im->chatAddAction($chatID, 'rename', $userID, 'fail', $comment);
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Rename chat fail.'), 'messageResponsePack');
        }
        $this->im->chatAddAction($chatID, 'rename', $userID, 'success', $comment);
        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = array_keys($users);
        $output->data   = $chat;

        $broadcast = $this->im->messageCreateBroadcast($isPrivateChat ? 'renamePrivate' : 'renameChat', $chat, array_keys($users), $userID);
        if($broadcast)
        {
            $output = array($output, $broadcast);
            return $this->im->sendOutputGroup($output);
        }

        return $this->im->sendOutput($output);
    }

    /**
     * Dismiss a chat
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatDismiss($gid = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');
        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');
        $account = $this->dao->select('account')->from(TABLE_USER)->where('id')->eq($userID)->fetch('account');
        if(empty($chat->ownedBy) && $chat->createdBy != $account) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');
        if(!empty($chat->ownedBy) && $chat->ownedBy != $account)  return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupCreator), 'messageResponsePack');

        $chat->dismissDate = helper::now();
        $chatID = $chat->id;
        $chat   = $this->im->chatUpdate($chat, $userID);
        if(dao::isError())
        {
            $this->im->chatAddAction($chatID, 'dismiss', $userID, 'fail');
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Dismiss chat fail.'), 'messageResponsePack');
        }
        $this->im->chatAddAction($chatID, 'dismiss', $userID, 'success');

        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = array_keys($users);
        $output->data   = $chat;

        $broadcast = $this->im->messageCreateBroadcast('dismissChat', $chat, array_keys($users), $userID);
        if($broadcast)
        {
            $output = array($output, $broadcast);
            return $this->im->sendOutputGroup($output);
        }

        return $this->im->sendOutput($output);
    }

    /**
     * Change the committers of a chat
     *
     * @param  string $gid
     * @param  string $committers
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatSetCommitters($gid = '', $committers = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');
        if($chat->type != 'group' && $chat->type != 'system') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');

        if($committers == '$ADMINS')
        {
            if(empty($chat->admins))
            {
                $user = $this->loadModel('user')->getByAccount($chat->createdBy);
                $chat->committers = $user->id;
            }
            else
            {
                $chat->committers = $chat->admins;
            }
        }
        else
        {
            $chat->committers = $committers;
        }

        $chat  = $this->im->chatUpdate($chat, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Set chat committers fail.'), 'messageResponsePack');
        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $chat;

        return $this->im->sendOutput($output, 'chatsetcommittersResponse');
    }

    /**
     * Change a chat to be public or not.
     *
     * @param  string   $gid
     * @param  string   $visible 0|1
     * @param  int      $userID
     * @access public
     * @return void
     */
    public function chatSetVisibility($gid = '', $visible = '1', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);

        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');
        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');

        $chatID = $chat->id;
        $chat->public         = $visible ? 1 : 0;
        $chat->lastActiveTime = empty($chat->lastActiveTime) ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', $chat->lastActiveTime);
        $chat  = $this->im->chatUpdate($chat, $userID);
        if(dao::isError())
        {
            $this->im->chatAddAction($chatID, $visible ? 'bepublic' : 'beprivate', $userID, 'fail');
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Set chat visibility fail.'), 'messageResponsePack');
        }
        $this->im->chatAddAction($chatID, $visible ? 'bepublic' : 'beprivate', $userID, 'success');

        $users = $this->im->userGetList($status = 'online', $chat->members);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $chat;

        return $this->im->sendOutput($output, 'chatsetvisibilityResponse');
    }

    /**
     * Star or cancel star a chat.
     *
     * @param  bool   $star true: star a chat | false: cancel star a chat.
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatStar($star = true, $gid = '', $userID = 0)
    {
        $this->im->chatStar($star, $gid, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Operate fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gid  = $gid;
        $output->data->star = $star;
        return $this->im->sendOutput($output, 'chatstarResponse');
    }

    /**
     * Hide or display a chat.
     *
     * @param  bool   $hide true: hide a chat | false: display a chat.
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatHide($hide = true, $gid = '', $userID = 0)
    {
        $this->im->chatHide($hide, $gid, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Toggle chat fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gid  = $gid;
        $output->data->hide = $hide;

        return $this->im->sendOutput($output, 'chathideResponse');
    }

    /**
     * Mute a chat.
     *
     * @param  string $gid
     * @param  bool   $mute true: mute a chat | false: cacel mute a chat.
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatMute($mute = true, $gid = '', $userID = 0)
    {
        $this->im->chatMute($mute, $gid, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Mute chat fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gid  = $gid;
        $output->data->mute = $mute;

        return $this->im->sendOutput($output, 'chatmuteResponse');
    }

    /**
     * Freeze a chat.
     *
     * @param  string $gid
     * @param  bool   $freeze true: freeze a chat | false: unfreeze a chat.
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatFreeze($freeze = true, $gid = '', $userID = 0)
    {
        $this->im->chatFreeze($freeze, $gid, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Set chat freeze fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gid    = $gid;
        $output->data->freeze = $freeze;

        return $this->im->sendOutput($output, 'chatfreezeResponse');
    }

    /**
     * Set category for a chat
     *
     * @param  array $gids
     * @param  string $category
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatSetCategory($gids = array(), $category = '', $userID = 0)
    {
        $this->im->chat->setCategory($gids, $category, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Set chat category fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gids     = $gids;
        $output->data->category = $category;

        return $this->im->sendOutput($output, 'chatsetcategoryResponse');
    }

    /**
     * Invite members to a chat or kick members from a chat.
     *
     * @param  string $gid
     * @param  array  $members
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatInvite($gid = '', $members = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');
        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');

        $joinedMembers = array();
        foreach($members as $member)
        {
            $result = $this->im->chatJoin($gid, $member);
            if(is_int($result)) $joinedMembers[] = $result;
        }

        $chat->members = $this->im->chatGetMembers($gid);
        $users         = $this->im->userGetList($status = 'online', $chat->members);
        $comment       = json_encode(array('members' => $members));

        if(dao::isError())
        {
            $this->im->chatAddAction($chat->id, 'invite', $userID, 'fail', $comment);
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Invite chat member fail.'), 'messageResponsePack');
        }
        $this->im->chatAddAction($chat->id, 'invite', $userID, 'success', $comment);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = $this->app->getMethodName();
        $output->users  = array_keys($users);
        $output->data   = $chat;

        $members = array_diff($members, $joinedMembers);
        if(empty($members)) return $this->im->sendOutput($output, 'chatinviteResponse');

        $broadcast = $this->im->messageCreateBroadcast('inviteUser', $chat, array_keys($users), $userID, $members);
        if($broadcast)
        {
            $output = array($output, $broadcast);
            return $this->im->sendOutputGroup($output);
        }

        return $this->im->sendOutput($output, 'chatinviteResponse');
    }

    /**
     * Kick members from a chat.
     *
     * @param  string $gid
     * @param  array  $users
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatKick($gid = '', $users = array(), $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid);
        if(!$chat) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notExist), 'messageResponsePack');

        if($chat->type != 'group') return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notGroupChat), 'messageResponsePack');

        foreach($users as $user) $this->im->chatLeave($gid, $user);

        $chat->members = $this->im->chatGetMembers($gid);
        $members = $this->im->userGetList($status = 'online', $chat->members);
        $members = array_keys($members);
        $members = array_merge($members, $users);
        $comment = json_encode(array('members' => $users));

        if(dao::isError())
        {
            $this->im->chatAddAction($chat->id, 'kick', $userID, 'fail', $comment);
            return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Kick chat member fail.'), 'messageResponsePack');
        }
        $this->im->chatAddAction($chat->id, 'kick', $userID, 'success', $comment);

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $members;
        $output->data   = $chat;

        return $this->im->sendOutput($output, 'chatkickResponse');
    }

    /**
     * Get history messages of a chat.
     *
     * @param  string $gid
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @param  int    $recTotal
     * @param  bool   $continued
     * @param  int    $startDate
     * @param  int    $userID
     * @access public
     * @return void
     * @deprecated The method is unnecessary in new versions.
     */
    public function chatGetHistory($gid = '', $recPerPage = 20, $pageID = 1, $recTotal = 0, $continued = false, $startDate = 0, $userID = 0)
    {
        if($startDate) $startDate = date('Y-m-d H:i:s', $startDate);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        if($gid)
        {
            $messageList = $this->im->message->getListByCgid($gid,  $pager, $startDate);
        }
        else
        {
            $messageList = $this->im->message->getList('', $idList = array(), $pager, $startDate);
        }

        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Get history fail'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array($userID);
        $output->data   = $messageList;

        $output->pager = new stdclass();
        $output->pager->recPerPage = $pager->recPerPage;
        $output->pager->pageID     = $pager->pageID;
        $output->pager->recTotal   = $pager->recTotal;
        $output->pager->gid        = $gid;
        $output->pager->continued  = $continued;
        return $this->im->sendOutput($output, 'chatgethistoryResponse');
    }

    /**
     * Set last read message for a chat
     *
     * @param  string $gid
     * @param  int    $lastReadMessageID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatSetLastReadMessage($gid, $lastReadMessageID = 0, $userID = 0)
    {
        $this->im->chatSetLastReadMessage($gid, $lastReadMessageID, $userID);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => "Set last read message fail for chat $gid."), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $userID;

        $output->data = new stdclass();
        $output->data->gid    = $gid;
        $output->data->id     = $lastReadMessageID;

        return $this->im->sendOutput($output, 'chatsetlastreadmessageResponse');
    }

    /**
     * Change ownership of group.
     *
     * @param  string $gid
     * @param  int    $ownerUserID  new owner id
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function chatChangeOwnership($gid, $ownerUserID, $userID = 0)
    {
        $chat = $this->im->chatGetByGid($gid, true);
        if(!in_array($userID, $chat->members) || !in_array($ownerUserID, $chat->members)) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Cannot change ownership of chat, new owner and current user must be in the chat.'), 'messageResponsePack');

        $result = $this->im->chatChangeOwnership($chat, $ownerUserID, $userID);
        if(empty($result)) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Change ownership failed.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = $chat->members;
        $output->data   = $result;

        $users = $this->im->userGetList($status = 'online', $chat->members);
        $this->im->chatAddAction($chat->id, 'changeOwnership', $userID, 'success');
        $broadcast = $this->im->messageCreateBroadcast('changeChatOwnership', $chat, array_keys($users), $ownerUserID, $chat->members);
        if($broadcast) $output = array($output, $broadcast);

        return $this->im->sendOutputGroup($output);
    }

    /**
     * Retract a message.
     *
     * @param  array  $messages
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageRetract($messages = array(), $userID = 0)
    {
        $chats = array();

        foreach($messages as $key => $message)
        {
            $message = (object) $message;
            $chats[$message->cgid] = $message->cgid;
            if(isset($message->type) && $message->type == 'broadcast') unset($messages[$key]);
        }

        $message     = (object) current($messages);
        $chat        = $this->im->chat->getByGid($message->cgid, $getMembers = true);
        $onlineUsers = $this->im->userGetList($status = 'online', $chat->members);

        $messages = $this->im->messageRetract($message->gid);
        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Retract message fail.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($onlineUsers);
        $output->data   = $messages;
        return $this->im->sendOutput($output, 'messageretractResponse');
    }

    /**
     * Send message to a chat.
     *
     * @param  array  $messages
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageSend($messages = array(), $userID = 0)
    {
        /* Check if the messages belong to the same chat. */
        $chats = array();
        foreach($messages as $key => $message)
        {
            $message = (object) $message;
            $chats[$message->cgid] = $message->cgid;
            if(isset($message->type) && $message->type == 'broadcast') unset($messages[$key]);
        }

        $message = (object) current($messages);

        $members = explode('&', $message->cgid);
        $isOne2OneChat = (count($members) == 2);

        if($message->user != $userID) return $this->im->sendOutput(array('result' => 'fail', 'message' => $this->lang->im->notSameUser), 'messageResponsePack');

        $chat = $this->im->chat->getByGid($message->cgid, $getMembers = true);

        $newChat = false;
        if(!$chat && $isOne2OneChat)
        {
            $newChat = true;
            $chat    = $this->im->chatcreate($message->cgid, '', 'one2one', $members, 0, false, $userID);
            if(dao::isError())
            {
                return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Create chat fail.'), 'messageResponsePack');
            }
        }

        /* Check whether the logon user can send message in chat. */
        $isCommitter = $this->im->chatIsCommitter($message, $userID, $chat);
        if($isCommitter !== true) return $this->im->sendOutput($isCommitter, 'messageResponsePack');

        /* If message is a bulletin, check if user is an admin. */
        if($message->type == 'bulletin')
        {
            $isAdmin = $this->im->chatIsAdmin($chat, $userID);
            if(!$isAdmin) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Only admins can send bulletin messages.'), 'messageResponsePack');
        }

        $onlineUsers  = array($userID);
        $offlineUsers = array();
        $users = $this->im->userGetList($status = '', $chat->members);
        foreach($users as $id => $user)
        {
            if($id == $userID) continue;
            if($user->clientStatus == 'offline') $offlineUsers[] = $id;
            if($user->clientStatus != 'offline') $onlineUsers[]  = $id;
        }

        /* Create messages. */
        $messages = $this->im->messageCreate($messages, $userID);
        if(empty($messages)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No message created.'), 'messageResponsePack');
        $this->im->messageSaveOfflineList($messages, $offlineUsers);

        /* push message to offline users */

        if(dao::isError()) return $this->im->sendOutput(array('result' => 'fail', 'message' => 'Send message fail'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'messagesend';
        $output->users  = $onlineUsers;
        $output->data   = $messages;

        if($newChat)
        {
            $chatOutput = new stdclass();
            $chatOutput->module = 'im';
            $chatOutput->method = 'chatcreate';
            $chatOutput->result = 'success';
            $chatOutput->users  = $onlineUsers;
            $chatOutput->data   = $chat;

            return $this->im->sendOutputGroup(array($chatOutput, $output));
        }

        return $this->im->sendOutput($output, 'messagesendResponse');
    }

    /**
     * Get messages from chat by ID.
     *
     * @param  string $cgid
     * @param  array  $idList
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageGetList($cgid, $idList, $userID = 0)
    {
        $members = $this->im->chatGetMembers($cgid, true);
        if(is_array($members) && !in_array($userID, $members)) $this->im->sendOutput(array('result' => 'fail', 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $messages = $this->im->messageGetList($cgid, $idList);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'messagegetlist';
        $output->users  = array($userID);
        $output->data   = $messages;

        return $this->im->sendOutput($output, 'messagegetlistResponse');
    }

    /**
     * Sync message from $fromID.
     *
     * @param  string $cgid
     * @param  int    $fromID
     * @param  bool   $reverse
     * @param  int    $limit
     * @param  bool   $returnID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageSync($cgid, $fromID, $reverse = false, $limit = 50, $returnID = false, $userID = 0)
    {
        $members = $this->im->chatGetMembers($cgid, true);
        if(is_array($members) && !in_array($userID, $members)) $this->im->sendOutput(array('result' => 'fail', 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $messages = $this->im->messageGetListAroundIDForUser($cgid, $fromID, $reverse, $limit, $userID, $returnID);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'messagesync';
        $output->users  = array($userID);
        $output->data   = $messages;

        return $this->im->sendOutput($output, $returnID ? 'messagesyncidResponse' :'messagesyncResponse');
    }

    /**
     * Sync messages since last logout.
     *
     * @param  bool   $full   whether to lookup partitions or not.
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageSyncSinceOffline($full = false, $userID = 0)
    {
        $messages = $this->im->messageGetOfflineList($full, $userID);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'messagesyncsinceoffline';
        $output->users  = array($userID);
        $output->data   = $messages;

        return $this->im->sendOutput($output, 'messagesyncsinceofflineResponse');
    }

    /**
     * Sync missed messages. (Messages that are read but not synced to client.)
     *
     * @param  int    $lastKnownMessage
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function messageSyncMissed($lastKnownMessage, $userID = 0)
    {
        $messages = $this->im->messageGetMissedByLastKnown($lastKnownMessage, $userID);

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'messagesyncmissed';
        $output->users  = array($userID);
        $output->data   = $messages;

        return $this->im->sendOutput($output, 'messagesyncmissedResponse');
    }

    /**
     * Get conference of chat, returns confernce object if conference is open.
     *
     * @param  string $chatID
     * @param  int    $userID
     * @param  string $version
     * @param  string $device
     * @return void
     */
    public function conferenceGetByChat($chatID, $userID = 0, $version = '', $device = 'desktop')
    {
        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $output = new stdclass();
        $output->result = 'success';
        $output->method = 'conferencegetbychat';
        $output->users  = array($userID);
        $output->data   = array();

        $conference = $this->im->conferenceGetByChatID($chatID);
        if(empty($conference) || $conference->status == 'closed') return $this->im->sendOutput($output, 'conferencegetbychatResponse');

        $output->data = array($conference);
        return $this->im->sendOutput($output, 'conferencegetbychatResponse');
    }

    /**
     * Create a conference for a chat.
     *
     * @param  string $chatID
     * @param  array  $invitee
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function conferenceCreate($chatID, $invitee = array(), $userID = 0, $version = '', $device = 'desktop')
    {
        $this->loadModel('owt');
        if(!$this->owt->isEnabled()) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => $this->lang->im->owtIsDisabled), 'messageResponsePack');

        $privateChatMembers = explode('&', $chatID);
        $isOne2OneChat = (count($privateChatMembers) == 2);

        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat))
        {
            if($isOne2OneChat)
            {
                $chat = $this->im->chatcreate($chatID, '', 'one2one', $privateChatMembers, 0, false, $userID);
                $chatCreateOutput = new stdclass();
                $chatCreateOutput->result = 'success';
                $chatCreateOutput->method = 'chatcreate';
                $chatCreateOutput->users  = $privateChatMembers;
                $chatCreateOutput->data   = $chat;
            }
            else
            {
                return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');
            }
        }

        $broadcasts = array();
        if($isOne2OneChat)
        {
            $targetUserArray = array_diff($privateChatMembers, array($userID));
            $targetUserID = current($targetUserArray);
            $targetUser = $this->im->userGetByID($targetUserID);
            if($this->im->conferenceIsUserOccupied($targetUserID, $chatID) || $targetUser->status == 'offline')
            {
                array_push($broadcasts, $this->im->messageCreateBroadcast('createConference', $chat, $privateChatMembers, $userID));
                if($targetUser->status != 'offline') array_push($broadcasts, $this->im->messageCreateBroadcast('conferenceInviteeOccupied', $chat, $privateChatMembers, $targetUserID));

                $failOutput = new stdclass();
                $failOutput->method  = 'conferencecreate';
                $failOutput->result  = 'fail';
                $failOutput->users   = $privateChatMembers;
                $failOutput->message = $targetUser->status == 'offline' ? $this->lang->im->conference->userOffline : $this->lang->im->conference->userBusy;
                $failOutput->data    = array('actions' => array());
                $outputGroup = array_merge(array($failOutput), $broadcasts);

                return $this->im->sendOutputGroup($outputGroup);
            }
        }

        $inviteeData = empty($invitee) ? implode(',', $chat->members) : implode(',', $invitee);
        $conference = $this->im->conferenceCreate($chatID, $inviteeData, $userID);
        if(empty($conference)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Create conference fail.'), 'messageResponsePack');

        $participants = explode(',', $conference->participants);
        if(!in_array((int)$userID, $participants)) $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Could not join the conference.', 'data' => $chatID), 'messageResponsePack');
        if(count($participants) > 1)
        {
            $conferenceAction = new stdClass();
            $conferenceAction->room         = $conference->rid;
            $conferenceAction->type         = 'join';
            $conferenceAction->participants = $conference->participants;
            $conferenceAction->invitee      = $conference->invitee;
            $conferenceAction->date         = strtotime(helper::now());
            $conferenceAction->user         = (int)$userID;
            $conferenceAction->device       = $device;
            return $this->im->sendOutput(array('result' => 'success', 'method' => 'conferencejoin', 'users' => $chat->members, 'data' => $conferenceAction), 'conferencejoinResponse');
        }

        $conferenceData = new stdClass();
        $conferenceData->cgid         = $chatID;
        $conferenceData->room         = $conference->rid;
        $conferenceData->status       = $conference->status;
        $conferenceData->openedBy     = (int)$userID;
        $conferenceData->openedDate   = strtotime(helper::now());
        $conferenceData->participants = $conference->participants;
        $conferenceData->invitee      = $conference->invitee;

        $conferenceAction = new stdClass();
        $conferenceAction->room         = $conferenceData->room;
        $conferenceAction->type         = 'create';
        $conferenceAction->participants = $conferenceData->participants;
        $conferenceAction->invitee      = $conference->invitee;
        $conferenceAction->date         = $conferenceData->openedDate;
        $conferenceAction->user         = (int)$userID;
        $conferenceAction->device       = $device;

        $conferenceData->actions = array($conferenceAction);

        $output = new stdClass();
        $output->method = 'conferencecreate';
        $output->users  = $chat->members;
        $output->result = 'success';
        $output->data   = $conferenceData;

        $broadcast = empty($invitee) ? $this->im->messageCreateBroadcast('createConference', $chat, $chat->members, $userID) : $this->im->messageCreateBroadcast('createConferenceInvitation', $chat, $invitee, $userID, $invitee, true);
        if(!empty($invitee)) foreach($invitee as $user) if($this->im->conferenceIsUserOccupied($user)) array_push($broadcasts, $this->im->messageCreateBroadcast('conferenceInviteeOccupied', $chat, $invitee, $user, $invitee, true));
        array_push($broadcasts, $broadcast);

        $output = array($output);
        if(!empty($broadcasts)) $output = array_merge($output, $broadcasts);
        if(isset($chatCreateOutput)) array_unshift($output, $chatCreateOutput);

        return $this->im->sendOutputGroup($output);
    }

    /**
     * Invite members to join a conference.
     *
     * @param  string $chatID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function conferenceInvite($chatID, $newInvitee = array(), $userID = 0, $version = '', $device = 'desktop') {
        $this->loadModel('owt');
        if(!$this->owt->isEnabled()) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => $this->lang->im->owtIsDisabled), 'messageResponsePack');

        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $conference = $this->im->conferenceGetByChatID($chatID);
        if(empty($conference)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such conference.'), 'messageResponsePack');

        $invitee = $this->im->conferenceAddInvitee($chatID, $newInvitee);
        if(empty($invitee)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Invitation failed.', 'data' => array('cgid' => $chatID)), 'messageResponsePack');

        $this->im->conferenceSaveAction($conference->rid, 'invite', $userID, $device);

        $conferenceAction = new stdClass();
        $conferenceAction->room         = $conference->rid;
        $conferenceAction->type         = 'invite';
        $conferenceAction->invitee      = $invitee;
        $conferenceAction->participants = $conference->participants;
        $conferenceAction->date         = strtotime(helper::now());
        $conferenceAction->user         = (int)$userID;
        $conferenceAction->device       = $device;

        $output = new stdClass();
        $output->result = 'success';
        $output->users  = $chat->members;
        $output->data   = $conferenceAction;

        $output = array($output);
        $broadcast = $this->im->messageCreateBroadcast('createConferenceInvitation', $chat, $newInvitee, $userID, $newInvitee, true);
        array_push($output, $broadcast);

        $invitee = array_filter(explode(',', $invitee));
        foreach($invitee as $user) if($this->im->conferenceIsUserOccupied($user)) array_push($output, $this->im->messageCreateBroadcast('conferenceInviteeOccupied', $chat, $newInvitee, $user, $newInvitee, true));

        return $this->im->sendOutputGroup($output);
    }

    /**
     * Join a conference in a chat.
     *
     * @param  string $chatID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function conferenceJoin($chatID, $userID, $version = '', $device = 'desktop')
    {
        $this->loadModel('owt');
        if(!$this->owt->isEnabled()) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => $this->lang->im->owtIsDisabled), 'messageResponsePack');

        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $conference = $this->im->conferenceGetByChatID($chatID);
        if(empty($conference)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such conference.'), 'messageResponsePack');

        $participants = $this->im->conferenceAddParticipant($chatID, $userID);
        if(empty($participants)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Could not join the conference.', 'data' => array('cgid' => $chatID)), 'messageResponsePack');

        $this->im->conferenceSaveAction($conference->rid, 'join', $userID, $device);

        $conferenceAction = new stdClass();
        $conferenceAction->room         = $conference->rid;
        $conferenceAction->type         = 'join';
        $conferenceAction->participants = $participants;
        $conferenceAction->invitee      = $conference->invitee;
        $conferenceAction->date         = strtotime(helper::now());
        $conferenceAction->user         = (int)$userID;
        $conferenceAction->device       = $device;

        return $this->im->sendOutput(array('result' => 'success', 'users' => $chat->members, 'data' => $conferenceAction), 'conferencejoinResponse');
    }

    /**
     * Leave a conference in a chat.
     *
     * @param  string $chatID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function conferenceLeave($chatID, $userID, $version = '', $device = 'desktop')
    {
        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $conference = $this->im->conferenceGetByChatID($chatID);
        if(empty($conference)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such conference.'), 'messageResponsePack');

        if($conference->status == 'closed') $alreadyClosed = true;

        $participants = $this->im->conferenceRemoveParticipant($chatID, $userID);
        $this->im->conferenceSaveAction($conference->rid, 'leave', $userID);

        $conferenceAction = new stdClass();
        $conferenceAction->room         = $conference->rid;
        $conferenceAction->type         = 'leave';
        $conferenceAction->participants = $participants;
        $conferenceAction->invitee      = $conference->invitee;
        $conferenceAction->date         = strtotime(helper::now());
        $conferenceAction->user         = (int)$userID;
        $conferenceAction->device       = $device;

        $participants = explode(',', $participants);
        $participants = array_filter($participants);
        if(!isset($alreadyClosed) && (empty($participants) || count($participants) == 0))
        {
            $this->im->conferenceClose($chatID, $userID);
            $conferenceAction->type         = 'close';
            $conferenceAction->participants = array();

            $output = new stdClass();
            $output->method = 'conferenceclose';
            $output->users  = $chat->members;
            $output->result = 'success';
            $output->data   = $conferenceAction;

            $broadcast = $this->im->messageCreateBroadcast('closeConference', $chat, $chat->members, $userID, array(), true);
            if($broadcast) $output = array($output, $broadcast);

            return $this->im->sendOutputGroup($output);
        }

        return $this->im->sendOutput(array('result' => 'success', 'users' => $chat->members, 'data' => $conferenceAction), 'conferenceleaveResponse');
    }

    /**
     * Close a conference in a chat.
     *
     * @param  string $chatID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function conferenceClose($chatID, $userID, $device = 'desktop')
    {
        $chat = $this->im->chatGetByGidForUser($chatID, $userID);
        if(empty($chat)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such chat, or you are not in the chat.'), 'messageResponsePack');

        $conference = $this->im->conferenceGetByChatID($chatID);
        if(empty($conference)) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'No such conference.'), 'messageResponsePack');

        $closeResult = $this->im->conferenceClose($chatID, $userID);
        if(!$closeResult) return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Close conference failed.'), 'messageResponsePack');

        $conferenceAction = new stdClass();
        $conferenceAction->room         = $conference->rid;
        $conferenceAction->type         = 'close';
        $conferenceAction->participants = '';
        $conferenceAction->invitee      = $conference->invitee;
        $conferenceAction->date         = strtotime(helper::now());
        $conferenceAction->user         = (int)$userID;
        $conferenceAction->device       = $device;

        $output = new stdClass();
        $output->method = 'conferenceclose';
        $output->users  = $chat->members;
        $output->result = 'success';
        $output->data   = $conferenceAction;

        $broadcast = $this->im->messageCreateBroadcast('closeConference', $chat, $chat->members, $userID, array(), true);
        if($broadcast) $output = array($output, $broadcast);

        return $this->im->sendOutputGroup($output);
    }

    /**
     * Get extensions.
     *
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function extensionGetList($userID = 0)
    {
        $output = new stdclass();
        $output->result = 'success';
        $output->data   = $this->im->getExtensionList($userID);
        $output->users  = array($userID);
        return $this->im->sendOutput($output, 'extensiongetlistResponse');
    }

    /**
     * Get latest notification and offline user.
     * @param array $offline
     * @param array $sendfail
     * @access public
     * @return void
     */
    public function syncNotifications($offline = array(), $sendfail = array())
    {
        if(!empty($offline))  $this->im->userSetOffline($offline);
        if(!empty($sendfail)) $this->im->messageSendFailures($sendfail);

        /* Push notifications to mobile clients. */

        $output = new stdClass();
        if(dao::isError())
        {
            $output->result  = 'fail';
            $output->message = 'Get notify fail.';
        }
        else
        {
            ini_set('memory_limit', '256M'); // Set memory limit to avoid OOMs.
            $output->result = 'success';
            $output->data   = $this->im->messageGetNotifyList();
        }
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Check user change, and update their pinyin of realname.
     *
     * @access public
     * @return void
     */
    public function syncUsers()
    {
        $this->im->updateLastPoll();

        $changedUsers = $this->im->userHasChanges();
        if(!empty($changedUsers))
        {
            $this->im->userReindexPinyin($changedUsers);
            $changedUsers = $this->im->userGetList('', $changedUsers, false);
        }

        $output = new stdClass();
        $output->result = 'success';
        $output->data   = empty($changedUsers) ? array() : $changedUsers;
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Check for dept changes, and provide sysgetdepts output if needed.
     *
     * @access public
     * @return void
     */
    public function syncDepts()
    {
        $deptChanges = $this->im->userHasChanges('dept');
        if(empty($deptChanges)) return $this->app->output($this->app->encrypt(array('result' => 'success', 'data' => '')));

        $getDeptsOutput = $this->fetch('im', 'sysGetDepts', array(0));
        $getDeptsOutput = explode("\n", $getDeptsOutput);
        $getDeptsOutput = $getDeptsOutput[count($getDeptsOutput) - 1];
        $getDeptsOutput = addslashes($getDeptsOutput); // Otherwise Go might not be able to parse such JSON.

        return $this->app->output($this->app->encrypt(array('result' => 'success', 'data' => $getDeptsOutput)));
    }

    /**
     * Upload file.
     *
     * @param  string $fileName
     * @param  string $path
     * @param  int    $size
     * @param  int    $time
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function fileUpload($fileName = '', $path = '', $size = 0, $time = 0, $gid = '', $userID = 0)
    {
        $chat = $this->im->chat->getByGid($gid, true);
        if(!$chat) return $this->app->output($this->app->encrypt(array('result' => 'fail', 'message' => $this->lang->im->notExist)));

        $users  = $this->im->userGetList($status = 'online', $chat->members);
        $fileID = $this->im->uploadFile($fileName, $path, $size, $time, $userID, $users, $chat);

        if(dao::isError()) return $this->app->output($this->app->encrypt(array('result' => 'fail', 'message' => 'Upload file fail.')));
        $output = new stdclass();
        $output->result = 'success';
        $output->users  = array_keys($users);
        $output->data   = $fileID;
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Create, edit or delte todo
     * @param  object $todo
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function todoUpsert($todo, $userID = 0)
    {
        $user = $this->im->userGetByID($userID);
        if(is_object($todo))
        {
            if(isset($todo->id))
            {
                if($todo->delete)
                {
                    $todo = $this->loadModel('todo')->getById($todo->id);
                    if($todo->account != $user->account)
                    {
                        return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'Cannot delete todo item witch not yours.', 'data' => $todo), 'messageResponsePack');
                    }
                    else
                    {
                        $this->dao->delete()->from(TABLE_TODO)->where('id')->eq($todo->id)->exec();
                        if(dao::isError())
                        {
                            return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => dao::getError()), 'messageResponsePack');
                        }
                        else
                        {
                            $this->loadModel('action')->create('todo', $todo->id, 'deleted', 'success', '', '', $user->account);

                            $output = new stdClass();
                            $output->result = 'success';
                            $output->users  = array($userID);
                            $output->data   = $todo;
                            return $this->im->sendOutput($output, 'todoupsertResponse');
                        }
                    }
                }
                else
                {
                    $_POST = (array)$todo;
                    $changes = $this->loadModel('todo')->update($todo->id);
                    if(dao::isError())
                    {
                        return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => dao::getError()), 'messageResponsePack');
                    }
                    else
                    {
                        $actionID = $this->loadModel('action')->create('todo', $todo->id, 'edit', 'success', '', '', $user->account);
                        $this->action->logHistory($actionID, $changes);

                        $output = new stdClass();
                        $output->result = 'success';
                        $output->users  = array($userID);
                        $output->data   = $todo;
                        return $this->im->sendOutput($output, 'todoupsertResponse');
                    }
                }
            }
            else
            {
                $_POST  = (array)$todo;
                $todoID = $this->loadModel('todo')->create($todo->date, $user->account);
                if(dao::isError())
                {
                    return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => dao::getError()), 'messageResponsePack');
                }
                else
                {
                    $this->loadModel('action')->create('todo', $todoID, 'created', 'success', '', '', $user->account);
                    $todo->id = $todoID;

                    $output = new stdClass();
                    $output->result = 'success';
                    $output->users  = array($userID);
                    $output->data   = $todo;
                    return $this->im->sendOutput($output, 'todoupsertResponse');
                }
            }
        }
        else
        {
            return $this->im->sendOutput(array('result' => 'fail', 'users' => $userID, 'message' => 'The todo param is not an object.'), 'messageResponsePack');
        }
    }

    /**
     * Get todo list.
     *
     * @param  string $mode
     * @param  string $orderBy
     * @param  string $status
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function todoGetList($mode = 'all', $status = 'unclosed', $orderBy = 'date_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $userID = 0)
    {
        $user = $this->im->userGetByID($userID);
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        if($mode == 'future')
        {
            $todos = $this->loadModel('todo')->getList('self', $user->account, 'future', empty($status) ? 'unclosed' : $status, $orderBy, $pager);
        }
        else if($mode == 'all')
        {
            $todos = $this->loadModel('todo')->getList('self', $user->account, 'all', empty($status) ? 'all' : $status, $orderBy, $pager);
        }
        else if($mode == 'undone')
        {
            $todos = $this->loadModel('todo')->getList('self', $user->account, 'before', empty($status) ? 'undone' : $status, $orderBy, $pager);
        }
        else
        {
            $todos = $this->loadModel('todo')->getList($mode, $user->account, 'all', empty($status) ? 'unclosed' : $status, $orderBy, $pager);
        }

        $output = new stdclass();
        $output->data   = $todos;
        $output->result = 'success';
        $output->users  = array($userID);
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Get chat group pairs.
     *
     * @access public
     * @return void
     */
    public function getGroupChats()
    {
        $response = array();
        $response['result'] = 'success';

        $groupPairs = $this->im->chatGetGroupPairs();
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            $response['data'] = $groupPairs;
        }

        die(json_encode($response));
    }

    /**
     * Get all user pairs or users of one chat group.
     *
     * @param  string $gid
     * @access public
     * @return void
     */
    public function getChatUsers($gid = '')
    {
        $response = array();
        $response['result'] = 'success';

        $userPairs = $this->im->chatGetUserPairs($gid);

        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            $response['data'] = $userPairs;
        }

        die(json_encode($response));
    }

    /**
     * Send notification to users' notification center.
     *
     * @access public
     * @return void
     */
    public function sendNotification()
    {
        /* Parse input data. */
        $input = file_get_contents("php://input");
        $data = json_decode($input);

        $response = array('result' => 'success', 'message' => '');

        if(empty($data->users))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setUserList;
            die(json_encode($response));
        }

        if(empty($data->sender))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setSender;
            die(json_encode($response));
        }

        if(empty($data->title))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setTitle;
            die(json_encode($response));
        }

        $users       = $data->users;
        $sender      = $data->sender;
        $title       = $data->title;
        $subtitle    = $data->subtitle ?: '';
        $content     = $data->content ?: '';
        $contentType = $data->contentType ?: 'text';
        $url         = $data->url ?: '';
        $actions     = $data->actions ?: array();

        $result = $this->im->messageCreateNotify($users, $title, $subtitle, $content, $contentType, $url, $actions, $sender);
        if(!$result)
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }

        die(json_encode($response));
    }

    /**
     * Send notification to users' notification center.
     *
     * @access public
     * @return void
     */
    public function sendChatMessage()
    {
        /* Parse input data. */
        $input = file_get_contents("php://input");
        $data = json_decode($input);

        $response = array('result' => 'success', 'message' => '');

        if(empty($data->gid))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setGroup;
            die(json_encode($response));
        }

        if(empty($data->sender))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setSender;
            die(json_encode($response));
        }

        if(empty($data->title))
        {
            $response['result']  = 'fail';
            $response['message'] = $this->lang->im->notify->setTitle;
            die(json_encode($response));
        }

        $gid         = $data->gid;
        $sender      = $data->sender;
        $title       = $data->title;
        $subtitle    = $data->subtitle ?: '';
        $content     = $data->content ?: '';
        $contentType = $data->contentType ?: 'text';
        $url         = $data->url ?: '';
        $actions     = $data->actions ?: array();

        $result = $this->im->messageCreateNotify($gid, $title, $subtitle, $content, $contentType, $url, $actions, $sender);
        if(!$result)
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }

        die(json_encode($response));
    }

    /**
     * Sync foreign user data into xuan database.
     *
     * @access public
     * @return void
     */
    public function syncUsersData()
    {
        /* Parse input data, array of users. */
        $input = file_get_contents("php://input");
        $data = json_decode($input);

        $this->loadModel('user');

        /* Get existing user list. */
        $existingAccounts = array_map(function($u) {return $u->account;}, $this->user->getList($dept = null, $mode = 'all'));

        $response = array('result' => 'success', 'message' => '', 'fails' => array());

        /* Get departments mapping configuration. */
        $deptsMapping = isset($this->config->im->depts) ? json_decode($this->config->im->depts->mapping) : new stdClass();

        foreach($data as $user)
        {
            /* Try to find dept id in mapping configuration and replace. */
            if(isset($user->dept) && isset($deptsMapping->{$user->dept})) $user->dept = $deptsMapping->{$user->dept};

            if(in_array($user->account, $existingAccounts))
            {
                /* Update user with new data. */
                $result = $this->user->apiUpdate($user);
            }
            else
            {
                /* Create user with the data. */
                $result = $this->user->apiCreate($user);
            }

            /* Push account to fails on failure. */
            if(!$result) $response['fails'][] = $user->account;
        }

        if(!empty($response['fails']))
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            unset($response['fails']);
            unset($response['message']);
        }

        die(json_encode($response));
    }

    /**
     * Sync foreign department data into xuan database.
     *
     * @access public
     * @return void
     */
    public function syncDeptsData()
    {
        /* Parse post data, array of depts. */
        $input = file_get_contents("php://input");
        $data = json_decode($input);

        $response = array('result' => 'success', 'message' => '', 'fails' => array());

        /* Fetch depts mapping table. */
        $mapping = isset($this->config->im->depts) ? json_decode($this->config->im->depts->mapping) : new stdClass();

        $this->loadModel('dept');
        foreach($data as $dept)
        {
            /* Set type as dept. */
            $dept->type = 'dept';

            /* If parent is set, use parent's mapping id. */
            if($dept->parent != 0) $dept->parent = $mapping->{$dept->parent};

            /* Create or update a dept and get its record id. */
            $deptRecordID = $this->dept->apiUpsertDept($dept, isset($mapping->{$dept->id}) ? $mapping->{$dept->id} : null);

            /* Break on error. */
            if(!$deptRecordID)
            {
                $response['fails'][] = $dept->id;
                continue;
            }

            /* Store acutal record id in $mapping. */
            $mapping->{$dept->id} = $deptRecordID;
        }

        /* Save mapping configuration. */
        $this->loadModel('setting')->setItem('system.im.depts.mapping', json_encode($mapping));

        if(!empty($response['fails']))
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            unset($response['fails']);
            unset($response['message']);
        }

        die(json_encode($response));
    }

    /**
     * Debug xuanxuan.
     *
     * @param  string $source
     * @access public
     * @return void
     */
    public function debug($source = 'x_php')
    {
        if(RUN_MODE != 'front') return $this->app->output('Access Denied');

        $this->view->title          = $this->lang->im->debug;
        $this->view->source         = $source;
        $this->view->xxdStatus      = $this->im->getXxdStatus();
        $this->view->checkXXBConfig = $this->im->checkXXBConfig();
        $this->display();
    }

    /**
     * Read content of log file and display.
     *
     * @access public
     * @return void
     */
    public function showLog()
    {
        $logFile = $this->app->getLogRoot() . 'xuanxuan.' . date('Ymd') . '.log.php';
        if(!file_exists($logFile)) $this->send(array('result' => 'fail', 'message' => $this->lang->im->noLogFile));

        if(!function_exists('fopen')) $this->send(array('result' => 'fail', 'message' => $this->lang->im->noFopen));

        $line = $this->config->im->logLine;
        $pos  = -2;
        $eof  = '';
        $log  = '';
        $fp   = fopen($logFile, 'r');
        while($line > 0)
        {
            while($eof != "\n")
            {
                if(!fseek($fp, $pos, SEEK_END))
                {
                    $eof = fgetc($fp);
                    $pos--;
                }
                else
                {
                    break;
                }
            }
            $log .= fgets($fp) . '<br>';
            $eof  = '';
            $line--;
        }

        $this->send(array('result' => 'success', 'logs' => $log));
    }

    /**
     * Update last polling time record.
     *
     * @access public
     * @return void
     */
    public function updateLastPoll()
    {
        $this->im->updateLastPoll();
        return $this->im->sendOutput(array('result' => 'success'));
    }

    /**
     * Check for users that changed their password but did not re-login.
     *
     * @access public
     * @return void
     */
    public function checkPasswordChanges()
    {
        $output = new stdClass();
        $output->module = 'im';
        $output->method = 'checkpasswordchanges';
        $output->result = 'success';
        $output->data   = $this->im->getKickList();
        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Run nightly maintenance jobs.
     *
     * @access public
     * @return void
     */
    public function maintenance()
    {
        $output = new stdClass();
        $output->module = 'im';
        $output->method = 'maintenance';
        $output->result = 'success';

        /* Reindex users' pinyin of realname. */
        $this->im->userReindexPinyin();

        /* Run message table partition job. */
        $partitionMark = $this->im->messageMarkOngoingPartition(true);
        if($partitionMark)
        {
            set_time_limit(0);
            if($this->im->messageNeedPartition()) $this->im->messagePartitionTable();
            $this->im->messageMarkOngoingPartition(false);
        }

        return $this->app->output($this->app->encrypt($output));
    }

    /**
     * Donwload XXD package.
     *
     * @param  string xxd package name.
     * @access public
     * @return void
     */
    public function downloadXxdPackage($xxdFileName)
    {
        set_time_limit(0);
        $version      = $this->config->xuanxuan->version;
        $xxdDirectory = $this->app->tmpRoot . 'xxd' . DS . $version;
        $xxdFile      = fopen($xxdDirectory . DS . $xxdFileName, 'rb');

        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: " . filesize ($xxdDirectory . DS . $xxdFileName));
        Header("Content-Disposition: attachment; filename=" . $xxdFileName);

        echo fread($xxdFile, filesize($xxdDirectory . DS . $xxdFileName));
        fclose($xxdFile);
    }

    /**
     * Authorize and redirect user to url.
     *
     * @param  string $account
     * @param  string $token
     * @param  string $device
     * @param  string $url
     * @return void
     */
    public function authorize($account = '', $token = '', $device = '', $url = '')
    {
        if(!empty($url)) $url = str_replace('_', $this->config->requestFix, $url);
        if(empty($account) || empty($token)) return $this->app->output('Invalid params. Please provide account, token and url.');

        $user = $this->im->userIdentifyWithToken($account, $token, $device);
        if(!$user || is_string($user)) return $this->app->output('Invalid token.');

        if(empty($url)) return $this->app->output('Authorized, but no url to redirect to.');

        $user = $this->loadModel('user')->getByAccount($account);
        $user = $this->user->login($account, $user->password);
        header("Location: $url");
        return $this->app->output();
    }
}
