<?php
class chat extends model
{
    /**
     * Get a chat by gid.
     *
     * @param  string $gid
     * @param  bool   $getMembers
     * @param  bool   $format   format the chat or return its data as is.
     * @access public
     * @return object
     */
    public function getByGid($gid = '', $getMembers = false, $format = true)
    {
        $chat = $this->dao->select('*')->from(TABLE_IM_CHAT)->where('gid')->eq($gid)->fetch();

        if($chat && $format)
        {
            $chat = $this->format($chat);
            if($getMembers) $chat->members = $this->getMembers($gid);
        }

        return $chat;
    }

    /**
     * Get public chat list that user not join.
     *
     * @param  int   $userID
     * @access public
     * @return array
     */
    public function getPublicList($userID)
    {
        $joinedChats = $this->dao->select('cgid')->from(TABLE_IM_CHATUSER)
            ->where('user')->eq($userID)
            ->andWhere('quit')->eq('0000-00-00 00:00:00')
            ->fetchAll();

        $joinedChats = array_map(function($chat) { return $chat->cgid; }, $joinedChats);

        $chats = $this->dao->select('*')->from(TABLE_IM_CHAT)
            ->where('public')->eq(true)
            ->andWhere('dismissDate')->eq('0000-00-00 00:00:00')
            ->andWhere('gid')->notin($joinedChats)
            ->fetchAll();

        return $this->format($chats);
    }

    /**
     * Get chat gid list by userID.
     *
     * @param  int    $userID
     * @access public
     * @return array
     */
    public function getGidListByUserID($userID = 0)
    {
        $systemChatGidList = $this->dao->select('gid')->from(TABLE_IM_CHAT)
            ->where('type')->eq('system')
            ->fetchPairs('gid');
        $gidList = $this->dao->select('t1.gid')->from(TABLE_IM_CHAT)->alias('t1')
            ->leftJoin(TABLE_IM_CHATUSER)->alias('t2')->on('t2.cgid=t1.gid')
            ->where('t2.user')->eq($userID)
            ->andWhere('t2.quit')->eq('0000-00-00 00:00:00')
            ->fetchPairs('gid');
        return array_merge($systemChatGidList, $gidList);
    }

    /**
     * Get chat list by userID.
     *
     * @param  int    $userID
     * @access public
     * @return array
     */
    public function getListByUserID($userID = 0)
    {
        $limit = isset($this->config->dismissedGroupLife) ? $this->config->dismissedGroupLife : 90;
        $chats = $this->dao->select('chat.*, cu.star, cu.hide, cu.mute, cu.freeze, cu.category, cu.lastReadMessage')
            ->from(TABLE_IM_CHAT)->alias('chat')
            ->leftJoin(TABLE_IM_CHATUSER)->alias('cu')->on('chat.gid=cu.cgid')
            ->where('cu.user')->eq($userID)
            ->andWhere('cu.quit')->eq('0000-00-00 00:00:00')
            ->andWhere('chat.dismissDate', true)->eq('0000-00-00 00:00:00')
            ->orWhere('chat.dismissDate')->gt(date(DT_DATETIME1, strtotime("-{$limit} month")))
            ->markRight(1)
            ->fetchAll();

        if(!isset($this->config->disableSystemGroupChat) || !$this->config->disableSystemGroupChat)
        {
            $systemChat = $this->dao->select('*, 0 as star, 0 as hide, 0 as mute, 0 as freeze, 0 as category, 0 as lastReadMessage')
                ->from(TABLE_IM_CHAT)
                ->where('type')->eq('system')
                ->fetch();
            $chats[] = $systemChat;
        }
        return $this->format($chats);
    }

    /**
     * Get chat by gid and verify if user is in the chat.
     *
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return object|bool
     */
    public function getByGidForUser($gid, $userID)
    {
        $chat = $this->getByGid($gid, true);
        if(empty($chat)) return false;
        if(!in_array($userID, $chat->members)) return false;

        return $chat;
    }

	/**
	 * Check if user is committer of a chat.
	 *
	 * @param  object    $message
	 * @param  int       $userID
	 * @param  object    $chat
	 * @access public
	 * @return object|bool  $output | true
	 */
	public function isCommitter($message, $userID, $chat)
	{
        $members = explode('&', $message->cgid);

		$output = new stdclass();
		$output->result = 'fail';
		$output->users   = $userID;

        if(!$chat)
		{
			$output->data = new stdclass();
			$output->data->gid      = $message->cgid;
			$output->data->messages = $this->lang->im->notExist;
			return $output;
		}

        if(count($members) == 2 and !in_array($userID, $members))
		{
            $output->message = $this->lang->im->notInChat;
			return $output;
		}

		if(!empty($chat->dismissDate))
		{
			$output->data = new stdclass();
			$output->data->gid      = $message->cgid;
			$output->data->messages = $this->lang->im->chatHasDismissed;
			return $output;
		}

		/* Check if user is in the group. */
		if($chat->type == 'group' and $message->type == 'normal')
		{
			$members = $this->getMembers($chat->gid);
			if(!in_array($message->user, $members))
			{
				$output->data = new stdclass();
				$output->data->gid      = $message->cgid;
				$output->data->messages = $this->lang->im->notInGroup;
				return $output;
			}
        }

		/* Check if user is in committers. */
		if(!empty($chat->committers))
		{
			$committers = explode(',', $chat->committers);
			if(!in_array($userID, $committers))
			{
				$output->data = new stdclass();
				$output->data->gid      = $message->cgid;
				$output->data->messages = $this->lang->im->cantChat;
				return $output;
			}
		}

		return true;
    }

    /**
     * Check if user is admin in a chat.
     *
     * @param  string|object $chat
     * @param  int           $userID
     * @access public
     * @return boolean
     */
    public function isAdmin($chat, $userID)
    {
        if(strpos(is_string($chat) ? $chat : $chat->gid, '&') !== false) return true;

        if(is_string($chat)) $chat = $this->getByGid($chat, false, false);
        if(isset($chat->admins) && (is_array($chat->admins) ? in_array($userID, $chat->admins) : strpos($chat->admins, ",$userID,")) !== false) return true;

        if($chat->createdBy === 'system')
        {
            $sysAdmins = $this->dao->select('id')->from(TABLE_USER)->where('admin')->eq('super')->fetchPairs();
            return in_array($userID, $sysAdmins);
        }

        $creatorID = $this->dao->select('id')->from(TABLE_USER)
            ->where('account')->eq(empty($chat->ownedBy) ? $chat->createdBy : $chat->ownedBy)
            ->fetch('id');
        return $userID == $creatorID;
    }

    /**
     * Get group pairs of all chat.
     *
     * @access public
     * @return array
     */
    public function getGroupPairs()
    {
        return $this->dao->select('gid, name')->from(TABLE_IM_CHAT)
            ->where('type')->eq('group')
            ->andWhere('dismissDate')->eq('0000-00-00 00:00:00')
            ->fetchPairs();
    }

    /**
     * Get user pairs of one chat group.
     *
     * @param  string $gid
     * @access public
     * @return array
     */
    public function getUserPairs($gid = '')
    {
        $userIdList = $this->dao->select('user')->from(TABLE_IM_CHATUSER)
            ->where('quit')->eq('0000-00-00 00:00:00')
            ->beginIF($gid)->andWhere('cgid')->eq($gid)->fi()
            ->fetchPairs();

        return $this->dao->select('id, realname')->from(TABLE_USER)->where('id')->in($userIdList)->fetchPairs();
    }

    /**
     * Create a chat.
     *
     * @param  string $gid
     * @param  string $name
     * @param  string $type
     * @param  array  $members
     * @param  int    $subjectID
     * @param  bool   $public
     * @param  int    $userID
     * @access public
     * @return object
     */
    public function create($gid = '', $name = '', $type = '', $members = array(), $subjectID = 0, $public = false, $userID = 0)
    {
        $user = $this->loadModel('im')->user->getByID($userID);

        $chat = new stdclass();
        $chat->gid         = $gid;
        $chat->name        = $name;
        $chat->type        = $type;
        $chat->subject     = $subjectID;
        $chat->createdBy   = !empty($user->account) ? $user->account : '';
        $chat->createdDate = helper::now();

        if($public) $chat->public = 1;

        $this->dao->insert(TABLE_IM_CHAT)->data($chat)->exec();

        /* Add members to chat. */
        foreach($members as $member) $this->join($gid, $member);

        return $this->getByGid($gid, true);
    }

    /**
     * Update a chat.
     *
     * @param  object $chat
     * @param  int    $userID
     * @access public
     * @return object
     */
    public function update($chat = null, $userID = 0)
    {
        if($chat)
        {
            $user = $this->loadModel('im')->user->getByID($userID);
            $chat->editedBy   = !empty($user->account) ? $user->account : '';
            $chat->editedDate = helper::now();
            if(is_array($chat->admins))         $chat->admins = implode(',', $chat->admins);
            if(is_array($chat->pinnedMessages)) $chat->pinnedMessages = implode(',', $chat->pinnedMessages);
            $this->dao->update(TABLE_IM_CHAT)->data($chat)->where('gid')->eq($chat->gid)->batchCheck($this->config->im->require->edit, 'notempty')->exec();
        }

        /* Return the changed chat. */
        return $this->getByGid($chat->gid, true);
    }

    /**
     * Touch a chat (as on Linux), changes its editedDate to now.
     *
     * This method is currently meant to track members' join/leave events, which will not get synced during login.
     *
     * @param  string  $gid
     * @return boolean
     */
    public function touch($gid)
    {
        $this->dao->update(TABLE_IM_CHAT)->set('editedDate')->eq(helper::now())->where('gid')->eq($gid)->exec();
        return !dao::isError();
    }

    /**
     * Init the system chat.
     *
     * @access public
     * @return bool
     */
    public function initSystemChat()
    {
        if(!isset($this->config->disableSystemGroupChat) || !$this->config->disableSystemGroupChat)
        {
            $chat = $this->dao->select('*')->from(TABLE_IM_CHAT)->where('type')->eq('system')->fetch();
            if(!$chat)
            {
                $chat = new stdclass();
                $chat->gid         = imModel::createGID();
                $chat->name        = $this->lang->im->systemGroup;
                $chat->type        = 'system';
                $chat->createdBy   = 'system';
                $chat->createdDate = helper::now();

                $this->dao->insert(TABLE_IM_CHAT)->data($chat)->exec();
            }
            return !dao::isError();
        }
        return true;
    }

    /**
     * Join a chat.
     *
     * @param  string   $gid
     * @param  int      $userID
     * @access public
     * @return bool|int return userID if already joined, else return result.
     */
    public function join($gid = '', $userID = 0)
    {
        $this->touch($gid);

        $data = $this->dao->select('*')->from(TABLE_IM_CHATUSER)->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->fetch();
        if($data)
        {
            /* If user hasn't quit the chat then return. */
            if($data->quit == '0000-00-00 00:00:00') return $userID;

            /* If user has quited the chat then update the record. */
            $data = new stdclass();
            $data->join = helper::now();
            $data->quit = '0000-00-00 00:00:00';
            $this->dao->update(TABLE_IM_CHATUSER)->data($data)->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->exec();

            return !dao::isError();
        }

        /* Create a new record of user's chat info. */
        $data = new stdclass();
        $data->cgid = $gid;
        $data->user = $userID;
        $data->join = helper::now();
        $this->dao->insert(TABLE_IM_CHATUSER)->data($data)->exec();

        /* Update order field. */
        $id = $this->dao->lastInsertID();
        $this->dao->update(TABLE_IM_CHATUSER)->set('`order`')->eq($id)->where('id')->eq($id)->exec();

        return !dao::isError();
    }

    /**
     * leave a chat.
     *
     * @param  int    $gid
     * @param  int    $userID
     * @access public
     * @return bool
     */
    public function leave($gid, $userID)
    {
        $this->dao->update(TABLE_IM_CHATUSER)->set('quit')->eq(helper::now())->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->exec();
        $this->removeAdmins($gid, array($userID));
        $this->loadModel('im')->conferenceRemoveUserFromChat($gid, $userID);
        $this->touch($gid);

        return !dao::isError();
    }

    /**
     * Format chats.
     *
     * @param  mixed  $chats  object | array
     * @access public
     * @return object | array
     */
    public function format($chats)
    {
        $isObject = false;
        if(is_object($chats))
        {
            $isObject = true;
            $chats    = array($chats);
        }

        $userID = $this->app->session->userID;

        foreach($chats as $chat)
        {
            if(!$chat) continue;
            $chat->id              = (int)$chat->id;
            $chat->subject         = (int)$chat->subject;
            $chat->createdDate     = $chat->createdDate == '0000-00-00 00:00:00' ? 0 : strtotime($chat->createdDate);
            $chat->editedDate      = $chat->editedDate == '0000-00-00 00:00:00' ? 0 : strtotime($chat->editedDate);
            $chat->lastActiveTime  = $chat->lastActiveTime == '0000-00-00 00:00:00' ? 0 : strtotime($chat->lastActiveTime);
            $chat->dismissDate     = $chat->dismissDate == '0000-00-00 00:00:00' ? 0 : strtotime($chat->dismissDate);
            $chat->lastMessage     = (int)$chat->lastMessage;
            $chat->admins          = array_values(array_map('intval', array_filter(explode(',', $chat->admins))));
            $chat->pinnedMessages  = array_values(array_map('intval', array_filter(explode(',', $chat->pinnedMessages))));

            if($chat->type == 'one2one' && $chat->gid != "$userID&$userID") $chat->name = '';

            if(isset($chat->star))            $chat->star   = (bool)$chat->star;
            if(isset($chat->hide))            $chat->hide   = (bool)$chat->hide;
            if(isset($chat->mute))            $chat->mute   = (bool)$chat->mute;
            if(isset($chat->public))          $chat->public = (bool)$chat->public;
            if(isset($chat->freeze))          $chat->freeze = (bool)$chat->freeze;
            if(isset($chat->lastReadMessage)) $chat->lastReadMessage = (int)$chat->lastReadMessage;
        }

        if($isObject) return reset($chats);

        return $chats;
    }

    /**
     * Add admins of a chat.
     *
     * @param  string $gid
     * @param  array  $admins
     * @param  int    $userID
     * @access public
     * @return object
     */
    public function addAdmins($gid = '', $admins = array())
    {
        $chat      = $this->getByGid($gid);
        $adminList = $chat->admins;
        $adminList = array_filter(array_unique(array_merge($adminList, $admins)));
        $adminList = implode(',', $adminList);
        $this->dao->update(TABLE_IM_CHAT)->set('admins')->eq(',' . $adminList . ',')->where('gid')->eq($gid)->exec();

        return $this->getByGid($gid, true);
    }

    /**
     * Remove admins of a chat.
     *
     * @param  string $gid
     * @param  array  $users
     * @access public
     * @return object
     */
    public function removeAdmins($gid = '', $users = array())
    {
        $chat      = $this->getByGid($gid);
        $adminList = $chat->admins;
        $adminList = array_filter(array_diff($adminList, $users));
        $adminList = implode(',', $adminList);
        $this->dao->update(TABLE_IM_CHAT)->set('admins')->eq(',' . $adminList . ',')->where('gid')->eq($gid)->exec();

        return $this->getByGid($gid, true);
    }

    /**
     * Pin messages of a chat.
     *
     * @param  string|object $chat
     * @param  array         $messageIds
     * @access public
     * @return object
     */
    public function pinMessages($chat, $messageIds)
    {
        if(is_string($chat)) $chat = $this->getByGid($chat);
        $pinnedMessages = $chat->pinnedMessages;

        if(!empty($pinnedMessages))
        {
            $pinnedMessagesData = $this->loadModel('im')->messageGetList('', $pinnedMessages);
            foreach($pinnedMessagesData as $msg) if($msg->deleted) $pinnedMessages = array_diff($pinnedMessages, array($msg->id));
        }

        $pinnedMessagesLimit = isset($this->config->pinnedMessagesLimit) ? $this->config->pinnedMessagesLimit : 10;
        if(count($pinnedMessages) >= $pinnedMessagesLimit) $pinnedMessages = array_slice($pinnedMessages, $pinnedMessagesLimit - count($pinnedMessages) + count($messageIds));

        $pinnedMessages = array_filter(array_unique(array_merge($pinnedMessages, $messageIds)));
        $pinnedMessages = implode(',', $pinnedMessages);
        $this->dao->update(TABLE_IM_CHAT)->set('pinnedMessages')->eq(',' . $pinnedMessages . ',')->where('gid')->eq($chat->gid)->exec();

        return $this->getByGid($chat->gid, true);
    }

     /**
     * Unpin messages of a chat.
     *
     * @param  string|object $chat
     * @param  array         $messageIDs
     * @access public
     * @return object
     */
    public function unpinMessages($chat, $messageIds)
    {
        if(is_string($chat)) $chat = $this->getByGid($chat);
        $pinnedMessages = $chat->pinnedMessages;
        $pinnedMessages = array_filter(array_diff($pinnedMessages, $messageIds));
        $pinnedMessages = implode(',', $pinnedMessages);
        $this->dao->update(TABLE_IM_CHAT)->set('pinnedMessages')->eq(',' . $pinnedMessages . ',')->where('gid')->eq($chat->gid)->exec();

        return $this->getByGid($chat->gid, true);
    }

    /**
     * Star or unstar a chat.
     *
     * @param  string $star
     * @param  string $gid
     * @param  int    $userID
     * @access public
     * @return object
     */
    public function star($star = '1', $gid = '', $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('star')->eq($star)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return $this->getByGid($gid, true);
    }

    /**
     * Hide or display a chat.
     *
     * @param  string   $hide
     * @param  string   $gid
     * @param  int      $userID
     * @access public
     * @return bool
     */
    public function hide($hide = '1', $gid = '', $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('hide')->eq($hide)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Mute or unmute a chat.
     *
     * @param  string   $mute
     * @param  string   $gid
     * @param  int      $userID
     * @access public
     * @return bool
     */
    public function mute($mute = '1', $gid = '', $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('mute')->eq($mute)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Freeze or unfreeze a chat.
     *
     * @param  string   $freeze
     * @param  string   $gid
     * @param  int      $userID
     * @access public
     * @return bool
     */
    public function freeze($freeze = '1', $gid = '', $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('freeze')->eq($freeze)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Set category for a chat
     *
     * @param  array  $gids
     * @param  string $category
     * @param  int    $userID
     * @access public
     * @return boolean
     */
    public function setCategory($gids = array(), $category = '', $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('category')->eq($category)
            ->where('cgid')->in($gids)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Get member list of one chat.
     *
     * @param  string|object $chat             chat gid or chat object.
     * @param  bool          $trueOnSystemChat return true if chat type is system.
     * @access public
     * @return array
     */
    public function getMembers($chat, $trueOnSystemChat = false)
    {
        if(is_string($chat)) $chat = $this->getByGid($chat);
        if(!$chat) return array();

        if($chat->type == 'system')
        {
            if($trueOnSystemChat) return true;
            $memberList = $this->dao->select('id')->from(TABLE_USER)->where('deleted')->eq('0')->fetchPairs();
        }
        else
        {
            $memberList = $this->dao->select('user')->from(TABLE_IM_CHATUSER)->alias('tcu')
                ->leftJoin(TABLE_USER)->alias('tu')->on('tcu.user = tu.id')
                ->where('tcu.quit')->eq('0000-00-00 00:00:00')
                ->andWhere('tu.deleted')->eq('0')
                ->andWhere('cgid')->eq($chat->gid)
                ->fetchPairs();
        }

        $members = array();
        foreach($memberList as $member) $members[] = (int)$member;

        return $members;
    }

    /**
     * Get count of messages for a chat.
     *
     * @param  string $gid
     * @access public
     * @return int
     */
    public function getMessageCount($gid)
    {
        $masterTableCount = $this->dao->select('count(*)')->from(TABLE_IM_MESSAGE)->where('cgid')->eq($gid)->fetch('count(*)');
        $partitionsMessageCount = $this->dao->select('sum(`count`)')->from(TABLE_IM_CHAT_MESSAGE_INDEX)->where('gid')->eq($gid)->fetch('sum(`count`)');

        return $masterTableCount + $partitionsMessageCount;
    }

    /**
     * Add chat action.
     *
     * @param  int      $chatId
     * @param  string   $action
     * @param  int      $actionId
     * @param  string   $result
     * @param  string   $comment
     */
    public function addAction($chatId, $action, $actorId, $result, $comment = '')
    {
        if(!$this->loadModel('action')->checkLogLevel('chat', $action)) return;

        $account = $this->dao->select('account')
			->from(TABLE_USER)
			->where('id')->eq($actorId)
			->fetch('account');
        $actor   = !empty($account) ? $account : '';
        $extra   = json_encode(array('actorId' => $actorId));
        $this->loadModel('action')->create('chat', $chatId, $action, $result, $comment, $extra, $actor);
    }

    /**
     * Set last read message for a chat.
     *
     * @param  string   $gid
     * @param  int      $lastReadMessageID
     * @param  int      $userID
     * @access public
     * @return bool
     */
    public function setLastReadMessage($gid, $lastReadMessageID, $userID)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('lastReadMessage')->eq($lastReadMessageID)
            ->where('cgid')->eq($gid)
            ->andWhere('lastReadMessage')->lt($lastReadMessageID)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Change ownership of chat.
     *
     * @param  object       $chat
     * @param  int          $ownerUserID  new owner id
     * @param  int          $userID
     * @access public
     * @return bool|object  returns chat on success, returns false on fail
     */
    public function changeOwnership($chat, $ownerUserID, $userID)
    {
        if($ownerUserID == $userID) return false;

        $currentUserAccount = $this->dao->select('account')->from(TABLE_USER)->where('id')->eq($userID)->fetch('account');
        if(empty($currentUserAccount) || (!empty($chat->ownedBy) && $chat->ownedBy != $currentUserAccount) || (empty($chat->ownedBy) && $chat->createdBy != $currentUserAccount)) return false;

        $ownerAccount = $this->dao->select('account')->from(TABLE_USER)->where('id')->eq($ownerUserID)->fetch('account');
        if(empty($ownerAccount)) return false;

        $this->dao->update(TABLE_IM_CHAT)
            ->set('ownedBy')->eq($ownerAccount)
            ->where('gid')->eq($chat->gid)
            ->exec();

        return dao::isError() ? false : $this->getByGid($chat->gid, true);
    }
}
