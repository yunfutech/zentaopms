<?php
/**
 * The control file of gitlab module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chenqi <chenqi@cnezsoft.com>
 * @package     product
 * @version     $Id: ${FILE_NAME} 5144 2020/1/8 8:10 下午 chenqi@cnezsoft.com $
 * @link        http://www.zentao.net
 */
class gitlab extends control
{
    /**
     * The gitlab constructor.
     * @param string $moduleName
     * @param string $methodName
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);

        /* This is essential when changing tab(menu) from gitlab to repo. */
        /* Optional: common::setMenuVars('devops', $this->session->repoID); */
        $this->loadModel('ci')->setMenu();
    }

    /**
     * Browse gitlab.
     *
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Admin user don't need bind. */
        $gitlabList = $this->gitlab->getList($orderBy, $pager);
        $myGitLabs  = $this->gitlab->getGitLabListByAccount();

        foreach($gitlabList as $gitlab)
        {
            $token = $this->gitlab->apiGetCurrentUser($gitlab->url, $gitlab->token);
            $gitlab->isAdminToken = (isset($token->is_admin) and $token->is_admin);
            $gitlab->isBindUser   = true;
            if(!$this->app->user->admin and !isset($myGitLabs[$gitlab->id])) $gitlab->isBindUser = false;
        }

        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browse;
        $this->view->gitlabList = $gitlabList;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * create a gitlab.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $this->checkToken();
            $gitlabID = $this->gitlab->create();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action');
            $actionID = $this->action->create('gitlab', $gitlabID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        $this->view->title = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->lblCreate;

        $this->display();
    }

    /**
     * view a gitlab.
     * @param  int    $id
     * @access public
     * @return void
     */
    public function view($id)
    {
        $gitlab = $this->gitlab->getByID($id);

        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->view;
        $this->view->gitlab     = $gitlab;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions    = $this->loadModel('action')->getList('gitlab', $id);
        $this->view->preAndNext = $this->loadModel('common')->getPreAndNextObject('pipeline', $id);
        $this->display();
    }

    /**
     * Edit a gitlab.
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $oldGitLab = $this->gitlab->getByID($id);

        if($_POST)
        {
            $this->checkToken();
            $this->gitlab->update($id);
            $gitLab = $this->gitlab->getByID($id);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action');
            $actionID = $this->action->create('gitlab', $id, 'edited');
            $changes  = common::createChanges($oldGitLab, $gitLab);
            $this->action->logHistory($actionID, $changes);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        $this->view->title  = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->edit;
        $this->view->gitlab = $oldGitLab;

        $this->display();
    }

    /**
     * Bind gitlab user to zentao users.
     *
     * @param  int     $gitlabID
     * @access public
     * @return void
     */
    public function bindUser($gitlabID)
    {
        $userPairs = $this->loadModel('user')->getPairs('noclosed|noletter');

        $gitlab = $this->gitlab->getByID($gitlabID);
        $user   = $this->gitlab->apiGetCurrentUser($gitlab->url, $gitlab->token);
        if(!isset($user->is_admin) or !$user->is_admin) die(js::alert($this->lang->gitlab->tokenLimit) . js::locate($this->createLink('gitlab', 'edit', array('gitlabID' => $gitlabID))));

        if($_POST)
        {
            $users       = $this->post->zentaoUsers;
            $accountList = array();
            $repeatUsers = array();
            foreach($users as $openID => $user)
            {
                if(empty($user)) continue;
                if(isset($accountList[$user])) $repeatUsers[] = zget($userPairs, $user);
                $accountList[$user] = $openID;
            }

            if(count($repeatUsers)) return $this->send(array('result' => 'fail', 'message' => sprintf($this->lang->gitlab->bindUserError, join(',', $repeatUsers))));

            $user = new stdclass;
            $user->providerID   = $gitlabID;
            $user->providerType = 'gitlab';

            /* Delete binded users and save new relationship. */
            $this->dao->delete()->from(TABLE_OAUTH)->where('providerType')->eq($user->providerType)->andWhere('providerID')->eq($user->providerID)->exec();
            foreach($users as $openID => $account)
            {
                if(!$account) continue;
                $user->account = $account;
                $user->openID  = $openID;

                $this->dao->delete()
                    ->from(TABLE_OAUTH)
                    ->where('openID')->eq($user->openID)
                    ->andWhere('providerType')->eq($user->providerType)
                    ->andWhere('providerID')->eq($user->providerID)
                    ->andWhere('account')->eq($user->account)
                    ->exec();

                $this->dao->insert(TABLE_OAUTH)->data($user)->exec();
            }
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $zentaoUsers = $this->dao->select('account,email,realname')->from(TABLE_USER)->fetchAll('account');

        $this->view->title         = $this->lang->gitlab->bindUser;
        $this->view->userPairs     = $userPairs;
        $this->view->gitlabUsers   = $this->gitlab->apiGetUsers($gitlabID);
        $this->view->bindedUsers   = $this->gitlab->getUserAccountIdPairs($gitlabID);
        $this->view->matchedResult = $this->gitlab->getMatchedUsers($gitlabID, $this->view->gitlabUsers, $zentaoUsers);
        $this->display();
    }

    /**
     * Bind product and gitlab projects.
     *
     * @param  int    $gitlabID
     * @access public
     * @return void
     */
    public function bindProduct($gitlabID)
    {
        $this->view->projectPairs = $this->gitlab->getProjectPairs($gitlabID);
        $this->view->title        = $this->lang->gitlab->bindProduct;
        $this->display();
    }

    /**
     * Delete a gitlab.
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function delete($id, $confirm = 'no')
    {
        if($confirm != 'yes') die(js::confirm($this->lang->gitlab->confirmDelete, inlink('delete', "id=$id&confirm=yes")));

        $oldGitLab = $this->gitlab->getByID($id);
        $this->loadModel('action');
        $this->gitlab->delete(TABLE_PIPELINE, $id);

        $gitLab   = $this->gitlab->getByID($id);
        $actionID = $this->action->create('gitlab', $id, 'deleted');
        $changes  = common::createChanges($oldGitLab, $gitLab);
        $this->action->logHistory($actionID, $changes);
        die(js::reload('parent'));
    }

    /**
     * Check post token has admin permissions.
     *
     * @access protected
     * @return void
     */
    protected function checkToken()
    {
        $gitlab = fixer::input('post')->trim('url,token')->get();
        $this->dao->update('gitlab')->data($gitlab)->batchCheck($this->config->gitlab->create->requiredFields, 'notempty');
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        if(strpos($gitlab->url, 'http') !== 0) return $this->send(array('result' => 'fail', 'message' => array('url' => array(sprintf($this->lang->gitlab->hostError, $this->config->gitlab->minCompatibleVersion)))));
        if(!$gitlab->token) return $this->send(array('result' => 'fail', 'message' => array('token' => array($this->lang->gitlab->tokenError))));

        $user = $this->gitlab->apiGetCurrentUser($gitlab->url, $gitlab->token, true);

        if(!is_object($user)) return $this->send(array('result' => 'fail', 'message' => array('url' => array(sprintf($this->lang->gitlab->hostError, $this->config->gitlab->minCompatibleVersion)))));
        if(!isset($user->is_admin) or !$user->is_admin) return $this->send(array('result' => 'fail', 'message' => array('token' => array($this->lang->gitlab->tokenError))));

        /* Verify version compatibility. */
        $result = $this->gitlab->getVersion($gitlab->url, $gitlab->token);
        if(empty($result) or !isset($result->version) or (version_compare($result->version, $this->config->gitlab->minCompatibleVersion, '<'))) return $this->send(array('result' => 'fail', 'message' => array('url' => array($this->lang->gitlab->notCompatible))));
    }

    /**
     * Webhook api.
     *
     * @access public
     * @return void
     */
    public function webhook()
    {
        $this->gitlab->webhookCheckToken();
        $product = $this->get->product;
        $gitlab  = $this->get->gitlab;
        $project = $this->get->project;

        $input       = file_get_contents('php://input');
        $requestBody = json_decode($input);
        $result      = $this->gitlab->webhookParseBody($requestBody, $gitlab);

        $logFile = $this->app->getLogRoot() . 'webhook.'. date('Ymd') . '.log.php';
        if(!file_exists($logFile)) file_put_contents($logFile, '<?php die(); ?' . '>');

        $fh = @fopen($logFile, 'a');
        if($fh)
        {
            fwrite($fh, date('Ymd H:i:s') . ": " . $this->app->getURI() . "\n");
            fwrite($fh, "JSON: \n  " . $input . "\n");
            fwrite($fh, "Parsed object: {$result->issue->objectType} :\n  " . print_r($result->object, true) . "\n");
            fclose($fh);
        }

        if($result->action == 'updateissue' and isset($result->changes->assignees)) $this->gitlab->webhookAssignIssue($result);

        //if($result->action = 'reopenissue') $this->gitlab->webhookIssueReopen($gitlab, $result);

        if($result->action == 'closeissue') $this->gitlab->webhookCloseIssue($result);

        if($result->action == 'updateissue') $this->gitlab->webhookSyncIssue($gitlab, $result);

        $this->view->result = 'success';
        $this->view->status = 'ok';
        $this->view->data   = $result->object;
        $this->display();
    }

    /**
     * Browse gitlab group.
     *
     * @param  int     $gitlabID
     * @param  string  $orderBy
     * @access public
     * @return void
     */
    public function browseGroup($gitlabID, $orderBy = 'name_asc')
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));
        }

        $groups      = $this->gitlab->apiGetGroups($gitlabID, $orderBy);
        $adminGroups = $this->gitlab->apiGetGroups($gitlabID, $orderBy, 'owner');

        $adminGroupIDList = array();
        foreach($adminGroups as $group) $adminGroupIDList[] = $group->id;

        $this->view->title            = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseGroup;
        $this->view->gitlabID         = $gitlabID;
        $this->view->gitlab           = $this->gitlab->getByID($gitlabID);
        $this->view->gitlabGroupList  = $groups;
        $this->view->adminGroupIDList = $adminGroupIDList;
        $this->view->orderBy          = $orderBy;
        $this->display();
    }

    /**
     * Creat a gitlab group.
     *
     * @param  int     $gitlabID
     * @access public
     * @return void
     */
    public function createGroup($gitlabID)
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));
        }

        if($_POST)
        {
            $this->gitlab->createGroup($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseGroup', "gitlabID=$gitlabID")));
        }

        $gitlab = $this->gitlab->getByID($gitlabID);

        $this->view->title    = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->group->create;
        $this->view->gitlab   = $gitlab;
        $this->view->gitlabID = $gitlabID;
        $this->display();
    }

    /**
     * Edit a gitlab group.
     *
     * @param  int     $gitlabID
     * @param  int     $userID
     * @access public
     * @return void
     */
    public function editGroup($gitlabID, $groupID)
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $members = $this->gitlab->apiGetGroupMembers($gitlabID, $groupID, $openID);
            if(empty($members) or $members[0]->access_level < $this->config->gitlab->accessLevel['owner']) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        if($_POST)
        {
            $this->gitlab->editGroup($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseGroup', "gitlabID=$gitlabID")));
        }

        $gitlab = $this->gitlab->getByID($gitlabID);
        $group  = $this->gitlab->apiGetSingleGroup($gitlabID, $groupID);

        $this->view->title    = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->group->edit;
        $this->view->gitlab   = $gitlab;
        $this->view->group    = $group;
        $this->view->gitlabID = $gitlabID;
        $this->display();
    }

    /**
     * Delete a gitlab group.
     *
     * @param  int    $gitlabID
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function deleteGroup($gitlabID, $groupID, $confirm = 'no')
    {
        if($confirm != 'yes') return print(js::confirm($this->lang->gitlab->group->confirmDelete , inlink('deleteGroup', "gitlabID=$gitlabID&groupID=$groupID&confirm=yes")));

        $group    = $this->gitlab->apiGetSingleGroup($gitlabID, $groupID);
        $response = $this->gitlab->apiDeleteGroup($gitlabID, $groupID);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$response or substr($response->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabgroup', $groupID, 'deleted', '', $group->name);
            return print(js::reload('parent'));
        }

        $errorKey = array_search($response->message, $this->lang->gitlab->apiError);
        $result   = $errorKey === false ? $response->message : zget($this->lang->gitlab->errorLang, $errorKey);
        return print(js::alert($result));
    }

    /**
     * Manage a gitlab group members.
     *
     * @param  int    $gitlabID
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function manageGroupMembers($gitlabID, $groupID)
    {
        if($_POST)
        {
            $data = fixer::input('post')->get();

            $ids = array_filter($data->ids);
            if(count($ids) != count(array_unique($ids))) return $this->send(array('result' => 'fail', 'message' => $this->lang->gitlab->group->repeatError));
            $newMembers = array();
            foreach($ids as $key => $id)
            {
                /* Check whether each member has selected accesslevel. */
                if(empty($data->levels[$key])) return $this->send(array('result' => 'fail', 'message' => $this->lang->gitlab->group->memberAccessLevel . $this->lang->gitlab->group->emptyError ));

                $newMembers[$id] = (object)array('access_level'=>$data->levels[$key], 'expires_at'=>$data->expires[$key]);
            }

            $currentMembers = $this->gitlab->apiGetGroupMembers($gitlabID, $groupID);

            /* Get the updated,deleted data. */
            $addedMembers = $deletedMembers = $updatedMembers = array();
            foreach($currentMembers as $currentMember)
            {
                $memberID = $currentMember->id;
                if(empty($newMembers[$memberID]))
                {
                    $deletedMembers[] = $memberID;
                }
                else
                {
                    if($newMembers[$memberID]->access_level != $currentMember->access_level or $newMembers[$memberID]->expires_at != $currentMember->expires_at)
                    {
                        $updatedData = new stdClass();
                        $updatedData->user_id      = $memberID;
                        $updatedData->access_level = $newMembers[$memberID]->access_level;
                        $updatedData->expires_at   = $newMembers[$memberID]->expires_at;
                        $updatedMembers[] = $updatedData;
                    }
                }
            }
            /* Get the added data. */
            foreach($newMembers as $id => $newMember)
            {
                $exist = false;
                foreach($currentMembers as $currentMember)
                {
                    if($currentMember->id == $id)
                    {
                        $exist = true;
                        break;
                    }
                }
                if($exist == false)
                {
                    $addedData = new stdClass();
                    $addedData->user_id      = $id;
                    $addedData->access_level = $newMembers[$id]->access_level;
                    $addedData->expires_at   = $newMembers[$id]->expires_at;
                    $addedMembers[] = $addedData;
                }
            }

            foreach($addedMembers as $addedMember)
            {
                $this->gitlab->apiCreateGroupMember($gitlabID, $groupID, $addedMember);
            }

            foreach($updatedMembers as $updatedMember)
            {
                $this->gitlab->apiUpdateGroupMember($gitlabID, $groupID, $updatedMember);
            }

            foreach($deletedMembers as $deletedMemberID)
            {
                $this->gitlab->apiDeleteGroupMember($gitlabID, $groupID, $deletedMemberID);
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseGroup', "gitlabID=$gitlabID")));
        }

        /* Get gitlab users data. */
        $gitlabUserList = $this->gitlab->apiGetUsers($gitlabID, true);
        $gitlabUsers    = array(''=>'');
        foreach($gitlabUserList as $gitlabUser)
        {
            $gitlabUsers[$gitlabUser->id] = $gitlabUser->realname;
        }

        $accessLevels   = $this->lang->gitlab->accessLevels;
        $currentMembers = $this->gitlab->apiGetGroupMembers($gitlabID, $groupID);

        $this->view->title          = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->group->manageMembers;
        $this->view->currentMembers = $currentMembers;
        $this->view->gitlabUsers    = $gitlabUsers;
        $this->view->gitlabID       = $gitlabID;
        $this->display();
    }

    /**
     * Browse gitlab user.
     *
     * @param  int     $gitlabID
     * @access public
     * @return void
     */
    public function browseUser($gitlabID, $orderBy = 'id_desc')
    {
        $this->view->title          = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseUser;
        $this->view->gitlabID       = $gitlabID;
        $this->view->gitlabUserList = $this->gitlab->apiGetUsers($gitlabID, false, $orderBy);
        $this->view->orderBy        = $orderBy;
        $this->display();
    }

    /**
     * Creat a gitlab user.
     *
     * @param  int     $gitlabID
     * @access public
     * @return void
     */
    public function createUser($gitlabID)
    {
        if($_POST)
        {
            $this->gitlab->createUser($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseUser', "gitlabID=$gitlabID")));
        }

        $userPairs = $this->loadModel('user')->getPairs('noclosed|noletter');

        $this->view->title     = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->user->create;
        $this->view->userPairs = $userPairs;
        $this->view->gitlabID  = $gitlabID;
        $this->display();
    }

    /**
     * Edit a gitlab user.
     *
     * @param  int     $gitlabID
     * @param  int     $userID
     * @access public
     * @return void
     */
    public function editUser($gitlabID, $userID)
    {
        if($_POST)
        {
            $this->gitlab->editUser($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseUser', "gitlabID=$gitlabID")));
        }

        $userPairs         = $this->loadModel('user')->getPairs('noclosed|noletter');
        $user              = $this->gitlab->apiGetSingleUser($gitlabID, $userID);
        $zentaoBindAccount = $this->dao->select('account')->from(TABLE_OAUTH)->where('providerType')->eq('gitlab')->andWhere('providerID')->eq($gitlabID)->andWhere('openID')->eq($user->id)->fetch('account');

        $this->view->title             = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->user->edit;
        $this->view->user              = $user;
        $this->view->userPairs         = $userPairs;
        $this->view->zentaoBindAccount = $zentaoBindAccount;
        $this->view->gitlabID          = $gitlabID;
        $this->display();
    }

    /**
     * Delete a gitlab user.
     *
     * @param  int    $gitlabID
     * @param  int    $userID
     * @access public
     * @return void
     */
    public function deleteUser($gitlabID, $userID, $confirm = 'no')
    {
        if($confirm != 'yes') die(js::confirm($this->lang->gitlab->user->confirmDelete , inlink('deleteUser', "gitlabID=$gitlabID&userID=$userID&confirm=yes")));

        $user    = $this->gitlab->apiGetSingleUser($gitlabID, $userID);
        $reponse = $this->gitlab->apiDeleteUser($gitlabID, $userID);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$reponse or substr($reponse->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabuser', $userID, 'deleted', '', $user->name);

            /* Delete user bind. */
            $this->dao->delete()->from(TABLE_OAUTH)->where('providerType')->eq('gitlab')->andWhere('providerID')->eq($gitlabID)->andWhere('openID')->eq($userID)->exec();

            die(js::reload('parent'));
        }

        die(js::alert($reponse->message));
    }

    /**
     * Browse gitlab project.
     *
     * @param  int    $gitlabID
     * @param  string $keyword
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseProject($gitlabID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $openID = 0;
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));
        }

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $keyword = fixer::input('post')->setDefault('keyword', '')->get('keyword'); // Fix bug#16741.

        $result = $this->gitlab->apiGetProjectsPager($gitlabID, $keyword, $orderBy, $pager);

        /* Get group id list by gitlab user that the user is maintainer. */
        $groupIDList = array(0 => 0);
        $groups      = $this->gitlab->apiGetGroups($gitlabID, 'name_asc', 'maintainer');
        foreach($groups as $group) $groupIDList[] = $group->id;

        foreach($result['projects'] as $key => $project)
        {
            $project->adminer = (bool)$this->app->user->admin;
            if(!$project->adminer and isset($project->owner) and $project->owner->id == $openID) $project->adminer = true;

            $project->isMaintainer = $this->gitlab->checkUserAccess($gitlabID, $project->id, $project, $groupIDList, 'maintainer');
        }

        $gitlab = $this->gitlab->getByID($gitlabID);

        $this->view->gitlab            = $gitlab;
        $this->view->keyword           = urldecode(urldecode($keyword));
        $this->view->pager             = $result['pager'];
        $this->view->title             = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseProject;
        $this->view->gitlabID          = $gitlabID;
        $this->view->gitlabProjectList = $result['projects'];
        $this->view->orderBy           = $orderBy;
        $this->display();
    }

    /**
     * Creat a gitlab project.
     *
     * @param  int     $gitlabID
     * @access public
     * @return void
     */
    public function createProject($gitlabID)
    {
        if($_POST)
        {
            $this->gitlab->createProject($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseProject', "gitlabID=$gitlabID")));
        }

        $gitlab = $this->gitlab->getByID($gitlabID);
        $user   = $this->gitlab->apiGetCurrentUser($gitlab->url, $gitlab->token);

        /* Get namespaces data */
        $namespacesList = $this->gitlab->apiGetNamespaces($gitlabID);
        $namespaces = array();
        foreach($namespacesList as $namespace)
        {
            if($namespace->kind == 'user' and $namespace->path == $user->username) $namespaces[$namespace->id] = $namespace->path;
            if($namespace->kind == 'group') $namespaces[$namespace->id] = $namespace->path;
        }

        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->project->create;
        $this->view->gitlab     = $gitlab;
        $this->view->user       = $user;
        $this->view->namespaces = $namespaces;
        $this->view->gitlabID   = $gitlabID;
        $this->display();
    }

    /**
     * Edit a gitlab project.
     *
     * @param  int     $gitlabID
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function editProject($gitlabID, $projectID)
    {
        if($_POST)
        {
            $this->gitlab->editProject($gitlabID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseProject', "gitlabID=$gitlabID")));
        }

        $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);

        $this->view->title    = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->project->edit;
        $this->view->project  = $project;
        $this->view->gitlabID = $gitlabID;
        $this->display();
    }

    /**
     * Delete a gitlab project.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function deleteProject($gitlabID, $projectID, $confirm = 'no')
    {
        if($confirm != 'yes') die(js::confirm($this->lang->gitlab->project->confirmDelete , inlink('deleteProject', "gitlabID=$gitlabID&projectID=$projectID&confirm=yes")));

        $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
        $reponse = $this->gitlab->apiDeleteProject($gitlabID, $projectID);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$reponse or substr($reponse->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabproject', $projectID, 'deleted', '', $project->name);
            die(js::reload('parent'));
        }

        die(js::alert($reponse->message));
    }

    /**
     * Browse gitlab branch.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseBranch($gitlabID, $projectID, $orderBy = 'name_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->session->set('gitlabBranchList', $this->app->getURI(true));

        $branchList = array();
        $result = $this->gitlab->apiGetBranches($gitlabID, $projectID);
        foreach($result as $gitlabBranch)
        {
            $branch = new stdClass();
            $branch->name              = $gitlabBranch->name;
            $branch->lastCommitter     = $gitlabBranch->commit->committer_name;
            $branch->lastCommittedDate = date('Y-m-d H:i:s', strtotime($gitlabBranch->commit->committed_date));

            $branchList[] = $branch;
        }

        /* Data sort. */
        list($order, $sort) = explode('_', $orderBy);
        $orderList = array();
        foreach($branchList as $branch)
        {
            $orderList[] = $branch->$order;
        }
        array_multisort($orderList, $sort == 'desc' ? SORT_DESC : SORT_ASC, $branchList);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal   = count($branchList);
        $pager      = new pager($recTotal, $recPerPage, $pageID);
        $branchList = array_chunk($branchList, $pager->recPerPage);

        $this->view->gitlab            = $this->gitlab->getByID($gitlabID);
        $this->view->pager             = $pager;
        $this->view->title             = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseBranch;
        $this->view->gitlabID          = $gitlabID;
        $this->view->projectID         = $projectID;
        $this->view->gitlabBranchList  = empty($branchList) ? $branchList: $branchList[$pageID - 1];
        $this->view->orderBy           = $orderBy;
        $this->display();
    }

    /**
     * Creat a gitlab branch.
     *
     * @param  int     $gitlabID
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function createBranch($gitlabID, $projectID)
    {
        if($_POST)
        {
            $this->gitlab->createBranch($gitlabID, $projectID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $locate = $this->session->gitlabBranchList ? $this->session->gitlabBranchList : inlink('browseBranch', "gitlibID=$gitlabID&projectID=$projectID");
            return $this->send(array('result' => 'success', 'message' => $this->lang->gitlab->createSuccess, 'locate' => $locate));
        }

        /* Get branches by api. */
        $branches = $this->gitlab->apiGetBranches($gitlabID, $projectID);
        if(!is_array($branches)) $branches= array();

        $branchPairs = array();
        foreach($branches as $branch) $branchPairs[$branch->name] = $branch->name;

        $this->view->title       = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->createBranch;
        $this->view->gitlabID    = $gitlabID;
        $this->view->projectID   = $projectID;
        $this->view->branchPairs = $branchPairs;
        $this->display();
    }

    /**
     * Browse gitlab protect branch.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseBranchPriv($gitlabID, $projectID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);

        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        $keyword  = fixer::input('post')->setDefault('keyword', '')->get('keyword');
        $branches = $this->gitlab->apiGetBranchPrivs($gitlabID, $projectID, $keyword, $orderBy);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal   = count($branches);
        $pager      = new pager($recTotal, $recPerPage, $pageID);
        $branchList = array_chunk($branches, $pager->recPerPage);

        $this->view->keyword    = $keyword;
        $this->view->pager      = $pager;
        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseBranchPriv;
        $this->view->levelLang  = $this->lang->gitlab->branch->branchCreationLevelList;
        $this->view->gitlabID   = $gitlabID;
        $this->view->projectID  = $projectID;
        $this->view->project    = $project;
        $this->view->orderBy    = $orderBy;
        $this->view->branchList = empty($branchList) ? $branchList: $branchList[$pageID - 1];
        $this->display();
    }

    /**
     * Set a gitlab protect branch.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $branch
     * @access public
     * @return void
     */
    public function createBranchPriv($gitlabID, $projectID, $branch = '')
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        /* Fix error when request type is PATH_INFO and the branch name contains '-'.*/
        if($branch) $branch = str_replace('*', '-', $branch);

        if($_POST)
        {
            $this->gitlab->createBranchPriv($gitlabID, $projectID, $branch);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseBranchPriv', "gitlabID=$gitlabID&projectID=$projectID")));
        }

        $branchPriv = new stdClass();
        $branchPriv->name               = '';
        $branchPriv->mergeAccessLevel = 40; // Initialize data, and the operation authority is the maintainers by default.
        $branchPriv->pushAccessLevel  = 40; // Initialize data, and the operation authority is the maintainers by default.

        $title  = $this->lang->gitlab->createBranchPriv;

        if($branch)
        {
            $title      = $this->lang->gitlab->editBranchPriv;
            $branchPriv = $this->gitlab->apiGetSingleBranchPriv($gitlabID, $projectID, $branch);
            $branchPriv->mergeAccessLevel = $this->gitlab->checkAccessLevel($branchPriv->merge_access_levels);
            $branchPriv->pushAccessLevel  = $this->gitlab->checkAccessLevel($branchPriv->push_access_levels);
        }

        $gitlabBranches  = $this->gitlab->apiGetBranches($gitlabID, $projectID);
        $protectBranches = $this->gitlab->apiGetBranchPrivs($gitlabID, $projectID, '', 'name_asc');
        $protectNames    = array_keys($protectBranches);

        $branches = array();
        foreach($gitlabBranches as $oneBranch)
        {
            if(!in_array($oneBranch->name, $protectNames) || $oneBranch->name == $branch) $branches[$oneBranch->name] = $oneBranch->name;
        }

        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $title;
        $this->view->pageTitle  = $title;
        $this->view->gitlabID   = $gitlabID;
        $this->view->branch     = $branch;
        $this->view->projectID  = $projectID;
        $this->view->branches   = $branches;
        $this->view->branchPriv = $branchPriv;
        $this->display();
    }

    /**
     * Edit a gitlab branch protect.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $branch
     * @access public
     * @return void
     */
    public function editBranchPriv($gitlabID, $projectID, $branch)
    {
        echo $this->fetch('gitlab', 'createBranchPriv', "gitlabID=$gitlabID&projectID=$projectID&branch=$branch");
    }

    /**
     * Delete a gitlab protect branch.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $branch
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteBranchPriv($gitlabID, $projectID, $branch, $confirm = 'no')
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        if($confirm != 'yes')
        {
            $branch = urlencode($branch);
            die(js::confirm($this->lang->gitlab->branch->confirmDelete , inlink('deleteBranchPriv', "gitlabID=$gitlabID&projectID=$projectID&branch=$branch&confirm=yes")));
        }

        /* Fix error when request type is PATH_INFO and the branch name contains '-'.*/
        $branch  = str_replace('*', '-', $branch);
        $reponse = $this->gitlab->apiDeleteBranchPriv($gitlabID, $projectID, $branch);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$reponse or substr($reponse->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabbranchPriv', $branch, 'deleted', '', $branch);
            die(js::reload('parent'));
        }

        die(js::alert($reponse->message));
    }

    /**
     * Browse gitlab tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseTag($gitlabID, $projectID, $orderBy = 'updated_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);

        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));
        }

        $this->session->set('gitlabTagList', $this->app->getURI(true));
        $keyword = fixer::input('post')->setDefault('keyword', '')->get('keyword');

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $tagList = array();
        $result  = $this->gitlab->apiGetTags($gitlabID, $projectID, $orderBy, $keyword, $pager);
        foreach($result as $gitlabTag)
        {
            $tag = new stdClass();
            $tag->name          = $gitlabTag->name;
            $tag->lastCommitter = $gitlabTag->commit->committer_name;
            $tag->updated       = date('Y-m-d H:i:s', strtotime($gitlabTag->commit->committed_date));
            $tag->protected     = $gitlabTag->protected;

            $tagList[] = $tag;
        }

        $this->view->gitlab        = $this->gitlab->getByID($gitlabID);
        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseTag;
        $this->view->gitlabID      = $gitlabID;
        $this->view->projectID     = $projectID;
        $this->view->keyword       = $keyword;
        $this->view->project       = $project;
        $this->view->gitlabTagList = $tagList;
        $this->view->orderBy       = $orderBy;
        $this->display();
    }

    /**
     * Browse gitlab protect tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browseTagPriv($gitlabID, $projectID, $orderBy = 'name_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);

        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        $this->session->set('gitlabTagPrivList', $this->app->getURI(true));
        $keyword = fixer::input('post')->setDefault('keyword', '')->get('keyword');

        $gitlabTags = array();
        $allTags    = $this->gitlab->apiGetTags($gitlabID, $projectID);
        foreach($allTags as $tag)
        {
            $gitlabTags[$tag->name] = $tag;
        }

        $tagList           = array();
        $gitlabProtectTags = $this->gitlab->apiGetTagPrivs($gitlabID, $projectID);
        foreach($gitlabProtectTags as $gitlabProtectTag)
        {
            $tag = new stdClass();
            $tag->name          = $gitlabProtectTag->name;
            $tag->lastCommitter = isset($gitlabTags[$tag->name]) ? $gitlabTags[$tag->name]->commit->committer_name : '';
            $tag->accessLevels  = $gitlabProtectTag->create_access_levels;

            $tagList[] = $tag;
        }

        /* Data search. */
        if($keyword)
        {
            foreach($tagList as $key => $tag)
            {
                if(strpos($tag->name, $keyword) === false) unset($tagList[$key]);
            }
            $tagList = array_values($tagList);
        }

        /* Data sort. */
        list($order, $sort) = explode('_', $orderBy);
        $orderList = array();
        foreach($tagList as $tag) $orderList[] = $tag->$order;
        array_multisort($orderList, $sort == 'desc' ? SORT_DESC : SORT_ASC, $tagList);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal = count($tagList);
        $pager    = new pager($recTotal, $recPerPage, $pageID);
        $tagList  = array_chunk($tagList, $pager->recPerPage);

        $this->view->gitlab        = $this->gitlab->getByID($gitlabID);
        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->browseTagPriv;
        $this->view->gitlabID      = $gitlabID;
        $this->view->projectID     = $projectID;
        $this->view->keyword       = $keyword;
        $this->view->project       = $project;
        $this->view->gitlabTagList = empty($tagList) ? $tagList: $tagList[$pageID - 1];
        $this->view->orderBy       = $orderBy;
        $this->display();
    }

    /**
     * Set a gitlab protect tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function createTagPriv($gitlabID, $projectID)
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        if($_POST)
        {
            $this->gitlab->createTagPriv($gitlabID, $projectID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseTagPriv', "gitlabID=$gitlabID&projectID=$projectID")));
        }

        $gitlabTags   = $this->gitlab->apiGetTags($gitlabID, $projectID);
        $protectTags  = $this->gitlab->apiGetTagPrivs($gitlabID, $projectID, '', 'name_asc');
        $protectNames = array_keys($protectTags);

        $tags = array();
        foreach($gitlabTags as $tag)
        {
            if(!in_array($tag->name, $protectNames)) $tags[$tag->name] = $tag->name;
        }

        $this->view->title      = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->createTagPriv;
        $this->view->gitlabID   = $gitlabID;
        $this->view->projectID  = $projectID;
        $this->view->tags       = $tags;
        $this->display();
    }

    /**
     * Edit a gitlab protect tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $tag
     * @access public
     * @return void
     */
    public function editTagPriv($gitlabID, $projectID, $tag = '')
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        /* Fix error when request type is PATH_INFO and the tag name contains '-'.*/
        $tag = str_replace('*', '-', $tag);

        if($_POST)
        {
            $this->gitlab->createTagPriv($gitlabID, $projectID, $tag);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseTagPriv', "gitlabID=$gitlabID&projectID=$projectID")));
        }

        $tagPriv = $this->gitlab->apiGetSingleTagPriv($gitlabID, $projectID, $tag);
        $tagPriv->createAccessLevel = $this->gitlab->checkAccessLevel($tagPriv->create_access_levels);

        $this->view->title     = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->editTagPriv;
        $this->view->gitlabID  = $gitlabID;
        $this->view->projectID = $projectID;
        $this->view->tagPriv   = $tagPriv;
        $this->view->tag       = $tag;
        $this->display();
    }

    /**
     * Delete a gitlab protect tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $tag
     * @access public
     * @return void
     */
    public function deleteTagPriv($gitlabID, $projectID, $tag)
    {
        if(!$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($gitlabID, $this->app->user->account);
            if(!$openID) return print(js::alert($this->lang->gitlab->mustBindUser) . js::locate($this->createLink('gitlab', 'browse')));

            $project = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);
            if(!$this->gitlab->checkUserAccess($gitlabID, $projectID, $project)) return print(js::alert($this->lang->gitlab->noAccess) . js::locate($this->createLink('gitlab', 'browse')));
        }

        /* Fix error when request type is PATH_INFO and the tag name contains '-'.*/
        $tag     = str_replace('*', '-', $tag);
        $reponse = $this->gitlab->apiDeleteTagPriv($gitlabID, $projectID, $tag);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$reponse or substr($reponse->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabtagpriv', 0, 'deleted', '', $tag);
            die(js::reload('parent'));
        }

        die(js::alert($reponse->message));
    }

    /**
     * Import gitlab issue to zentaopms.
     *
     * @param  int    $repoID
     * @access public
     * @return void
     */
    public function importIssue($repoID)
    {
        $repo          = $this->loadModel('repo')->getRepoByID($repoID);
        $productIDList = explode(',', $repo->product);
        $gitlabID      = $repo->gitlab;
        $projectID     = $repo->project;

        $gitlab = $this->gitlab->getByID($gitlabID);
        if($gitlab) $user = $this->gitlab->apiGetCurrentUser($gitlab->url, $gitlab->token);
        if(empty($user->is_admin)) die(js::alert($this->lang->gitlab->tokenLimit) . js::locate($this->createLink('gitlab', 'edit', array('gitlabID' => $gitlabID))));

        if($_POST)
        {
            $executionList  = $this->post->executionList;
            $objectTypeList = $this->post->objectTypeList;
            $productList    = $this->post->productList;

            $failedIssues = array();
            foreach($executionList as $issueID => $executionID)
            {
                if(empty($executionID) and $productList[$issueID] != 0) return $this->send(array('result' => 'fail', 'message' => $this->lang->gitlab->importIssueError, 'locate' => $this->server->http_referer));
            }

            foreach($executionList as $issueID => $executionID)
            {
                if(empty($executionID)) continue;

                $objectType = $objectTypeList[$issueID];

                $issue             = $this->gitlab->apiGetSingleIssue($gitlabID, $projectID, $issueID);
                $issue->objectType = $objectType;
                $issue->objectID   = 0; // Meet the required parameters for issueToZentaoObject.
                if(isset($issue->assignee)) $issue->assignee_id = $issue->assignee->id;
                $issue->updated_by_id = $issue->author->id; // Here can be replaced by current zentao user.

                $object            = $this->gitlab->issueToZentaoObject($issue, $gitlabID);
                $object->product   = $productList[$issueID];
                $object->execution = $executionID;
                $clonedObject      = clone $object;

                if($objectType == 'task')  $objectID = $this->loadModel('task')->createTaskFromGitlabIssue($clonedObject, $executionID);
                if($objectType == 'bug')   $objectID = $this->loadModel('bug')->createBugFromGitlabIssue($clonedObject, $executionID);
                if($objectType == 'story') $objectID = $this->loadModel('story')->createStoryFromGitlabIssue($clonedObject, $executionID);

                if($objectID)
                {
                    $this->loadModel('action')->create($objectType, $objectID, 'ImportFromGitlab', '', $issueID);

                    $object->id = $objectID;
                    $this->gitlab->saveImportedIssue($gitlabID, $projectID, $objectType, $objectID, $issue, $object);
                }
                else
                {
                    $failedIssues[] = $issue->iid;
                }
            }

            if($failedIssues) return $this->send(array('result' => 'success', 'message' => $this->lang->gitlab->importIssueWarn, 'locate' => $this->server->http_referer));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $savedIssueIDList = $this->dao->select('BID as issueID')->from(TABLE_RELATION)
            ->where('relation')->eq('gitlab')
            ->andWhere('BType')->eq('issue')
            ->andWhere('BVersion')->eq($projectID)
            ->andWhere('extra')->eq($gitlabID)
            ->fetchAll('issueID');

        /* 'not[iids]' option in gitlab API has a issue when iids is too long. */
        $gitlabIssues = $this->gitlab->apiGetIssues($gitlabID, $projectID, '&state=opened');
        if(!is_array($gitlabIssues)) $gitlabIssues = array();
        foreach($gitlabIssues as $index => $issue)
        {
            foreach($savedIssueIDList as $savedIssueID)
            {
                if($issue->iid == $savedIssueID->issueID)
                {
                    unset($gitlabIssues[$index]);
                    break;
                }
            }
        }

        $products = array('' => '');
        $this->loadModel("product");
        foreach($productIDList as $productID) $products[$productID] = $this->product->getByID($productID)->name;

        $this->view->title           = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->importIssue;
        $this->view->importable      = empty($gitlabIssues) ? false : true;
        $this->view->products        = $products;
        $this->view->gitlabID        = $gitlabID;
        $this->view->gitlabProjectID = $projectID;
        $this->view->objectTypes     = $this->config->gitlab->objectTypes;
        $this->view->gitlabIssues    = $gitlabIssues;

        $this->display();
    }

    /**
     * Create Webhook by repoID.
     *
     * @param  int    $repoID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function createWebhook($repoID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->gitlab->confirmAddWebhook, $this->createLink('gitlab', 'createWebhook', "repoID=$repoID&confirm=yes")));
        }
        else
        {
            $repo = $this->loadModel('repo')->getRepoByID($repoID);
            $res  = $this->gitlab->addPushWebhook($repo);

            if($res or is_array($res))
            {
                die(js::alert($this->lang->gitlab->addWebhookSuccess));
            }
            else
            {
                die(js::error($this->lang->gitlab->failCreateWebhook));
            }
        }
    }

    /**
     * Manage a gitlab project members.
     *
     * @param  int    $repoID
     * @access public
     * @return void
     */
    public function manageProjectMembers($repoID)
    {
        if($_POST)
        {
            $data = fixer::input('post')->get();

            $accounts = array_filter($data->accounts);
            if(count($accounts) != count(array_unique($accounts))) return $this->send(array('result' => 'fail', 'message' => $this->lang->gitlab->group->repeatError));

            $repo        = $this->loadModel('repo')->getRepoByID($repoID);
            $users       = $this->loadModel('user')->getPairs('noletter|noempty|nodeleted');
            $bindedUsers = $this->dao->select('account,openID')
                ->from(TABLE_OAUTH)
                ->where('providerType')->eq('gitlab')
                ->andWhere('providerID')->eq($repo->gitlab)
                ->fetchPairs();

            if(empty($repo->acl))
            {
                $repo->acl = new stdClass();
                $repo->acl->users = array();
            }

            $newGitlabMembers = array();
            foreach($accounts as $key => $account)
            {
                /* If the user has set permissions, check whether they are bound. */
                if(!empty($data->levels[$key]))
                {
                    if(!isset($bindedUsers[$account])) return $this->send(array('result' => 'fail', 'message' => $users[$account] . ' ' . $this->lang->gitlab->project->notbindedError));
                    $newGitlabMembers[$bindedUsers[$account]] = (object) array('access_level' => $data->levels[$key], 'expires_at' => $data->expires[$key]);
                }
            }

            $gitlabCurrentMembers = $this->gitlab->apiGetProjectMembers($repo->gitlab, $repo->project);

            $addedMembers = $updatedMembers = $deletedMembers = array();
            /* Get the updated data. */
            foreach($gitlabCurrentMembers as $gitlabCurrentMember)
            {
                $memberID = isset($gitlabCurrentMember->id) ? $gitlabCurrentMember->id : 0;
                if(!isset($newGitlabMembers[$memberID])) continue;
                if($newGitlabMembers[$memberID]->access_level != $gitlabCurrentMember->access_level or $newGitlabMembers[$memberID]->expires_at != $gitlabCurrentMember->expires_at)
                {
                    $updatedData = new stdClass();
                    $updatedData->user_id      = $memberID;
                    $updatedData->access_level = $newGitlabMembers[$memberID]->access_level;
                    $updatedData->expires_at   = $newGitlabMembers[$memberID]->expires_at;
                    $updatedMembers[] = $updatedData;
                }
            }
            /* Get the added data. */
            foreach($newGitlabMembers as $id => $newMember)
            {
                $exist = false;
                foreach($gitlabCurrentMembers as $gitlabCurrentMember)
                {
                    if($gitlabCurrentMember->id == $id)
                    {
                        $exist = true;
                        break;
                    }
                }
                if($exist == false)
                {
                    $addedData = new stdClass();
                    $addedData->user_id      = $id;
                    $addedData->access_level = $newGitlabMembers[$id]->access_level;
                    $addedData->expires_at   = $newGitlabMembers[$id]->expires_at;
                    $addedMembers[] = $addedData;
                }
            }
            /* Get the deleted data. */
            $originalUsers = $repo->acl->users;
            foreach($originalUsers as $user)
            {
                if(!in_array($user, $accounts) and isset($bindedUsers[$user]))
                {
                    $exist = false;
                    foreach($gitlabCurrentMembers as $gitlabCurrentMember)
                    {
                        if($gitlabCurrentMember->id == $bindedUsers[$user])
                        {
                            $exist            = true;
                            $deletedMembers[] = $gitlabCurrentMember->id;
                            break;
                        }
                    }
                }
            }

            foreach($addedMembers as $addedMember)
            {
                $this->gitlab->apiCreateProjectMember($repo->gitlab, $repo->project, $addedMember);
            }

            foreach($updatedMembers as $updatedMember)
            {
                $this->gitlab->apiUpdateProjectMember($repo->gitlab, $repo->project, $updatedMember);
            }

            foreach($deletedMembers as $deletedMemberID)
            {
                $this->gitlab->apiDeleteProjectMember($repo->gitlab, $repo->project, $deletedMemberID);
            }

            $repo->acl->users = array_values($accounts);
            $this->dao->update(TABLE_REPO)->data(array('acl'=>json_encode($repo->acl)))->where('id')->eq($repoID)->exec();
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => helper::createLink('repo', 'maintain')));
        }

        $repo           = $this->loadModel('repo')->getRepoByID($repoID);
        $users          = $this->loadModel('user')->getPairs('noletter|noempty|nodeleted|noclosed');
        $projectMembers = $this->gitlab->apiGetProjectMembers($repo->gitlab, $repo->project);
        if(!is_array($projectMembers)) $projectMembers = array();

        /* Get users accesslevel. */
        $userAccessData = array();
        $bindedUsers    = $this->dao->select('openID,account')
            ->from(TABLE_OAUTH)
            ->where('providerType')->eq('gitlab')
            ->andWhere('providerID')->eq($repo->gitlab)
            ->fetchPairs();

        foreach($projectMembers as $projectMember)
        {
            if(isset($projectMember->id) and isset($bindedUsers[$projectMember->id]))
            {
                $account                  = $bindedUsers[$projectMember->id];
                $userAccessData[$account] = $projectMember;
            }
        }

        $this->view->title          = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->manageProjectMembers;
        $this->view->userAccessData = $userAccessData;
        $this->view->users          = $users;
        $this->view->repo           = $repo;
        $this->display();
    }

    /**
     * AJAX: Get executions by productID.
     *
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function ajaxGetExecutionsByProduct($productID)
    {
        if(!$productID) return $this->send(array('message' => array()));

        $executions = $this->loadModel('product')->getAllExecutionPairsByProduct($productID);
        $options    = "<option value=''></option>";
        foreach($executions as $index =>$execution)
        {
            $options .= "<option title='{$execution}' value='{$index}' data-name='{$execution}'>{$execution}</option>";
        }
        return $this->send($options);
    }

    /**
     * AJAX: Get project branches.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function ajaxGetProjectBranches($gitlabID, $projectID)
    {
        if(!$gitlabID or !$projectID) return $this->send(array('message' => array()));

        $branches = $this->gitlab->apiGetBranches($gitlabID, $projectID);
        $options  = "<option value=''></option>";
        foreach($branches as $branch)
        {
            $options .= "<option value='{$branch->name}'>{$branch->name}</option>";
        }
        $this->send($options);
    }

    /**
     * AJAX: Get MR user pairs to select assignee_ids and reviewer_ids.
     * Attention: The user must be a member of the GitLab project.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function ajaxGetMRUserPairs($gitlabID, $projectID)
    {
        if(!$gitlabID) return $this->send(array('message' => array()));

        $bindedUsers     = $this->gitlab->getUserIdRealnamePairs($gitlabID);
        $rawProjectUsers = $this->gitlab->apiGetProjectUsers($gitlabID, $projectID);
        $users           = array();
        foreach($rawProjectUsers as $rawProjectUser)
        {
            if(!empty($bindedUsers[$rawProjectUser->id])) $users[$rawProjectUser->id] = $bindedUsers[$rawProjectUser->id];
        }

        $options  = "<option value=''></option>";
        foreach($users as $index => $user)
        {
            $options .= "<option value='{$index}'>{$user}</option>";
        }
        $this->send($options);
    }

    /**
     * Create a gitlab tag.
     *
     * @param  int $gitlabID
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function createTag($gitlabID, $projectID)
    {
        if($_POST)
        {
            $this->gitlab->createTag($gitlabID, $projectID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browseTag', "gitlabID=$gitlabID&projectID=$projectID")));
        }

        $gitlabBranches = $this->gitlab->apiGetBranches($gitlabID, $projectID);
        $branches       = array();
        foreach($gitlabBranches as $branch)
        {
            $branches[$branch->name] = $branch->name;
        }

        $this->view->title     = $this->lang->gitlab->common . $this->lang->colon . $this->lang->gitlab->createTag;
        $this->view->gitlabID  = $gitlabID;
        $this->view->projectID = $projectID;
        $this->view->branches  = $branches;
        $this->display();
    }

    /**
     * Delete a gitlab tag.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @param  string $tagName
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function deleteTag($gitlabID, $projectID, $tagName = '', $confirm = 'no')
    {
        if($confirm != 'yes') die(js::confirm($this->lang->gitlab->tag->confirmDelete , inlink('deleteTag', "gitlabID=$gitlabID&projectID=$projectID&tagName=$tagName&confirm=yes")));

        /* Fix error when request type is PATH_INFO and the tag name contains '-'.*/
        $tagName = str_replace('*', '-', $tagName);
        $reponse = $this->gitlab->apiDeleteTag($gitlabID, $projectID, $tagName);

        /* If the status code beginning with 20 is returned or empty is returned, it is successful. */
        if(!$reponse or substr($reponse->message, 0, 2) == '20')
        {
            $this->loadModel('action')->create('gitlabtag', $projectID, 'deleted', '', $project->name);
            die(js::reload('parent'));
        }

        die(js::alert($reponse->message));
    }
}
