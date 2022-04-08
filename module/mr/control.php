<?php
class mr extends control
{
    /**
     * The mr constructor.
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
     * Browse mr.
     *
     * @param  string $mode
     * @param  string $param
     * @param  int    $objectID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($mode = 'all', $param = 'all', $objectID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager  = new pager($recTotal, $recPerPage, $pageID);

        $projects = $this->mr->getAllGitlabProjects();
        $MRList   = $this->mr->getList($mode, $param, $orderBy, $pager, empty($projects) ? false : $projects);

        /* Save current URI to session. */
        $this->session->set('mrList', $this->app->getURI(true), 'repo');

        /* Sync GitLab MR to ZenTao Database. */
        $MRList = $this->mr->batchSyncMR($MRList);

        /* Check whether Mr is linked with the product. */
        $this->loadModel('gitlab');
        foreach($MRList as $MR)
        {
            $product        = $this->mr->getMRProduct($MR);
            $MR->linkButton = empty($product) ? false : true;
        }

        /* Load lang from compile module */
        $this->app->loadLang('compile');

        $openIDList = array();
        if(!$this->app->user->admin) $openIDList = $this->loadModel('gitlab')->getGitLabListByAccount($this->app->user->account);

        $this->view->title      = $this->lang->mr->common . $this->lang->colon . $this->lang->mr->browse;
        $this->view->MRList     = $MRList;
        $this->view->projects   = $projects;
        $this->view->pager      = $pager;
        $this->view->mode       = $mode;
        $this->view->param      = $param;
        $this->view->objectID   = $objectID;
        $this->view->orderBy    = $orderBy;
        $this->view->openIDList = $openIDList;
        $this->display();
    }

    /**
     * Create MR function.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $result = $this->mr->create();
            return $this->send($result);
        }

        $gitlabHosts = $this->loadModel('gitlab')->getPairs();
        $gitlabUsers = $this->gitlab->getGitLabListByAccount();
        foreach($gitlabHosts as $gitlabID=> $gitlabHost)
        {
            if(!$this->app->user->admin and !isset($gitlabUsers[$gitlabID])) unset($gitlabHosts[$gitlabID]);
        }

        $this->app->loadLang('repo'); /* Import lang in repo module. */
        $this->app->loadLang('compile');
        $this->view->title       = $this->lang->mr->create;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->jobList     = $this->loadModel('job')->getList();
        $this->view->gitlabHosts = $gitlabHosts;
        $this->display();
    }

    /**
     * Create MR function by api.
     *
     * @access public
     * @return void
     */
    public function apiCreate()
    {
        if($_POST)
        {
            $this->mr->apiCreate();

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = join("\n", dao::getError());
            }

            return $this->send($response);
        }
    }

    /**
     * Edit MR function.
     *
     * @access public
     * @return void
     */
    public function edit($MRID)
    {
        if($_POST)
        {
            $result = $this->mr->update($MRID);
            return $this->send($result);
        }

        $MR = $this->mr->getByID($MRID);
        if(isset($MR->gitlabID)) $rawMR = $this->mr->apiGetSingleMR($MR->gitlabID, $MR->targetProject, $MR->mriid);
        $this->view->title = $this->lang->mr->edit;
        $this->view->MR    = $MR;
        $this->view->rawMR = isset($rawMR) ? $rawMR : false;
        if(!isset($rawMR->id) or (isset($rawMR->message) and $rawMR->message == '404 Not found') or empty($rawMR)) return $this->display();

        $branchList       = $this->loadModel('gitlab')->getBranches($MR->gitlabID, $MR->targetProject);
        $targetBranchList = array();
        foreach($branchList as $branch) $targetBranchList[$branch] = $branch;

        /* Fetch user list both in Zentao and current GitLab project. */
        $bindedUsers     = $this->gitlab->getUserIdRealnamePairs($MR->gitlabID);
        $rawProjectUsers = $this->gitlab->apiGetProjectUsers($MR->gitlabID, $MR->targetProject);

        $users = array();
        foreach($rawProjectUsers as $rawProjectUser)
        {
            if(!empty($bindedUsers[$rawProjectUser->id])) $users[$rawProjectUser->id] = $bindedUsers[$rawProjectUser->id];
        }

        $gitlabUsers = $this->gitlab->getUserAccountIdPairs($MR->gitlabID);

        /* Check permissions. */
        if(!$this->app->user->admin)
        {
            $groupIDList = array(0 => 0);
            $groups      = $this->gitlab->apiGetGroups($MR->gitlabID, 'name_asc', 'developer');
            foreach($groups as $group) $groupIDList[] = $group->id;
            $sourceProject = $this->gitlab->apiGetSingleProject($MR->gitlabID, $MR->sourceProject);
            $isDeveloper   = $this->gitlab->checkUserAccess($MR->gitlabID, 0, $sourceProject, $groupIDList, 'developer');

            if(!isset($gitlabUsers[$this->app->user->account]) or !$isDeveloper) return print(js::alert($this->lang->mr->errorLang[3]) . js::locate($this->createLink('mr', 'browse')));
        }

        /* Import lang for required modules. */
        $this->loadModel('repo');
        $this->loadModel('job');
        $this->loadModel('compile');

        $repoList    = array();
        $rawRepoList = $this->repo->getGitLabRepoList($MR->gitlabID, $MR->sourceProject);
        foreach($rawRepoList as $rawRepo) $repoList[$rawRepo->id] = "[$rawRepo->id] $rawRepo->name";

        $jobList = array();
        if($MR->repoID)
        {
            $rawJobList = $this->job->getListByRepoID($MR->repoID);
            foreach($rawJobList as $rawJob) $jobList[$rawJob->id] = "[$rawJob->id] $rawJob->name";
        }

        $this->view->repoList = $repoList;
        $this->view->jobList  = !empty($MR->repoID) ? $jobList : array();

        $this->view->title            = $this->lang->mr->edit;
        $this->view->MR               = $MR;
        $this->view->targetBranchList = $targetBranchList;
        $this->view->users            = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->assignee         = $MR->assignee;
        $this->view->reviewer         = zget($gitlabUsers, $MR->reviewer, '');

        $this->display();
    }

    /**
     * Delete a MR.
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function delete($id, $confirm = 'no')
    {
        if($confirm != 'yes') die(js::confirm($this->lang->mr->confirmDelete, inlink('delete', "id=$id&confirm=yes")));

        $MR = $this->mr->getByID($id);

        if($MR->synced)
        {
           $res = $this->mr->apiDeleteMR($MR->gitlabID, $MR->targetProject, $MR->mriid);
           if(isset($res->message)) return print(js::alert($this->mr->convertApiError($res->message)));
        }
        $this->dao->delete()->from(TABLE_MR)->where('id')->eq($id)->exec();

        die(js::locate(inlink('browse'), 'parent'));
    }

    /**
     * View a MR.
     *
     * @param  int $id
     * @access public
     * @return void
     */
    public function view($id)
    {
        $MR = $this->mr->getByID($id);
        if(!$MR) die(js::error($this->lang->notFound) . js::locate($this->createLink('mr', 'browse')));
        if(isset($MR->gitlabID)) $rawMR = $this->mr->apiGetSingleMR($MR->gitlabID, $MR->targetProject, $MR->mriid);
        if($MR->synced and (!isset($rawMR->id) or (isset($rawMR->message) and $rawMR->message == '404 Not found') or empty($rawMR))) return $this->display();

        $this->loadModel('gitlab');
        $this->loadModel('job');

        /* Sync MR from GitLab to ZentaoPMS. */
        $MR = $this->mr->apiSyncMR($MR);
        $sourceProject = $this->gitlab->apiGetSingleProject($MR->gitlabID, $MR->sourceProject);
        $targetProject = $this->gitlab->apiGetSingleProject($MR->gitlabID, $MR->targetProject);
        $sourceBranch  = $this->gitlab->apiGetSingleBranch($MR->gitlabID, $MR->sourceProject, $MR->sourceBranch);
        $targetBranch  = $this->gitlab->apiGetSingleBranch($MR->gitlabID, $MR->targetProject, $MR->targetBranch);

        $projectOwner = true;
        if(isset($MR->gitlabID) and !$this->app->user->admin)
        {
            $openID = $this->gitlab->getUserIDByZentaoAccount($MR->gitlabID, $this->app->user->account);
            if(!$projectOwner and isset($sourceProject->owner->id) and $sourceProject->owner->id == $openID) $projectOwner = true;
        }

        $this->view->sourceProjectName = $sourceProject->name_with_namespace;
        $this->view->targetProjectName = $targetProject->name_with_namespace;
        $this->view->sourceProjectURL  = isset($sourceBranch->web_url) ? $sourceBranch->web_url : '';
        $this->view->targetProjectURL  = isset($targetBranch->web_url) ? $targetBranch->web_url : '';

        /* Those variables are used to render $lang->mr->commandDocument. */
        $this->view->httpRepoURL = $sourceProject->http_url_to_repo;
        $this->view->branchPath  = $sourceProject->path_with_namespace . '-' . $MR->sourceBranch;

        /* Get mr linked list. */
        $this->app->loadLang('productplan');
        $product  = $this->mr->getMRProduct($MR);

        $this->view->compile      = $this->loadModel('compile')->getById($MR->compileID);
        $this->view->compileJob   = $MR->jobID ? $this->job->getById($MR->jobID) : false;
        $this->view->projectOwner = $projectOwner;

        $this->view->title   = $this->lang->mr->view;
        $this->view->MR      = $MR;
        $this->view->rawMR   = isset($rawMR) ? $rawMR : false;
        $this->view->product = $product;
        $this->view->stories = $this->mr->getLinkList($MR->id, $product->id, 'story');
        $this->view->bugs    = $this->mr->getLinkList($MR->id, $product->id, 'bug');
        $this->view->tasks   = $this->mr->getLinkList($MR->id, $product->id, 'task');

        $this->display();
    }

    /**
     * Crontab sync MR from GitLab API to Zentao database, default time 5 minutes to execute once.
     *
     * @access public
     * @return void
     */
    public function syncMR()
    {
        $MRList = $this->mr->getList();
        $this->mr->batchSyncMR($MRList);

        if(dao::isError())
        {
            echo json_encode(dao::getError());
            return true;
        }

        echo 'success';
    }

    /**
     * Accept a MR.
     *
     * @param  int    $MRID
     * @access public
     * @return void
     */
    public function accept($MRID)
    {
        $MR = $this->mr->getByID($MRID);

        /* Judge that if this MR can be accepted. */
        if(isset($MR->needCI) and $MR->needCI == '1')
        {
            $compileStatus = empty($MR->compileID) ? 'fail' : $this->loadModel('compile')->getByID($MR->compileID)->status;

            if(isset($compileStatus) and $compileStatus != 'success')
            {
                return $this->send(array('result' => 'fail', 'message' => $this->lang->mr->needCI, 'locate' => helper::createLink('mr', 'view', "mr={$MRID}")));
            }
        }
        if(isset($MR->needApproved) and $MR->needApproved == '1')
        {
            if($MR->approvalStatus != 'approved')
            {
                return $this->send(array('result' => 'fail', 'message' => $this->lang->mr->needApproved, 'locate' => helper::createLink('mr', 'view', "mr={$MRID}")));
            }
        }

        /* Accept MR by using the mapped user in GitLab. */
        $sudoUser = $this->mr->getSudoUsername($MR->gitlabID, $MR->targetProject);

        if(isset($MR->gitlabID))
        {
            if(!empty($sudoUser)) $rawMR = $this->mr->apiAcceptMR($MR->gitlabID, $MR->targetProject, $MR->mriid, $sudoUser);
            if(empty($sudoUser))  $rawMR = $this->mr->apiAcceptMR($MR->gitlabID, $MR->targetProject, $MR->mriid);
        }
        if(isset($rawMR->state) and $rawMR->state == 'merged')
        {
            ///* Force reload when locate to the url. */
            //$random = uniqid();
            //return $this->send(array('result' => 'success', 'message' => $this->lang->mr->mergeSuccess, 'locate' => helper::createLink('mr', 'browse', "random={$random}")));

            $this->mr->logMergedAction($MR);

            return $this->send(array('result' => 'success', 'message' => $this->lang->mr->mergeSuccess, 'locate' => helper::createLink('mr', 'browse')));
        }

        /* The type of variable `$rawMR->message` is string. This is different with apiCreateMR. */
        if(isset($rawMR->message))
        {
            $errorMessage = $this->mr->convertApiError($rawMR->message);
            return $this->send(array('result' => 'fail', 'message' => sprintf($this->lang->mr->apiError->sudo, $errorMessage), 'locate' => helper::createLink('mr', 'view', "mr={$MRID}")));
        }

        return $this->send(array('result' => 'fail', 'message' => $this->lang->mr->mergeFailed, 'locate' => helper::createLink('mr', 'view', "mr={$MRID}")));
    }

    /**
     * View diff between MR source and target branches.
     *
     * @param  int    $MRID
     * @access public
     * @return void
     */
    public function diff($MRID, $encoding = '')
    {
        $this->app->loadLang('productplan');
        $this->app->loadLang('bug');
        $this->app->loadLang('task');

        $encoding = empty($encoding) ? 'utf-8' : $encoding;
        $encoding = strtolower(str_replace('_', '-', $encoding)); /* Revert $config->requestFix in $encoding. */

        $MR = $this->mr->getByID($MRID);
        $this->view->title = $this->lang->mr->viewDiff;
        $this->view->MR    = $MR;

        $rawMR = null;
        if($MR->synced)
        {
            $rawMR = $this->mr->apiGetSingleMR($MR->gitlabID, $MR->targetProject, $MR->mriid);
            if(!isset($rawMR->id) or (isset($rawMR->message) and $rawMR->message == '404 Not found') or empty($rawMR)) return $this->display();
        }
        $this->view->rawMR = $rawMR;

        $diffs   = $this->mr->getDiffs($MR, $encoding);
        $arrange = $this->cookie->arrange ? $this->cookie->arrange : 'inline';

        if($this->server->request_method == 'POST')
        {
            if($this->post->arrange)
            {
                $arrange = $this->post->arrange;
                setcookie('arrange', $arrange);
            }
            if($this->post->encoding) $encoding = $this->post->encoding;
        }

        if($arrange == 'appose')
        {
            foreach($diffs as $diffFile)
            {
                if(empty($diffFile->contents)) continue;
                foreach($diffFile->contents as $content)
                {
                    $old = array();
                    $new = array();
                    foreach($content->lines as $line)
                    {
                        if($line->type != 'new') $old[$line->oldlc] = $line->line;
                        if($line->type != 'old') $new[$line->newlc] = $line->line;
                    }
                    $content->old = $old;
                    $content->new = $new;
                }
            }
        }

        $this->view->repo     = $this->loadModel('repo')->getRepoByID($MR->repoID);
        $this->view->repoID   = $MR->repoID;
        $this->view->diffs    = $diffs;
        $this->view->encoding = $encoding;
        $this->view->arrange  = $arrange;
        $this->display();
    }

    /**
     * Approval for this MR.
     *
     * @param  int    $MRID
     * @param  string $action
     * @return void
     */
    public function approval($MRID, $action = 'approve')
    {
        $MR = $this->mr->getByID($MRID);

        if($_POST)
        {
            $comment = $this->post->comment;
            $result  = $this->mr->approve($MR, $action, $comment);
            return $this->send($result);
        }

        $showCompileResult = false;
        if(!empty($MR->compileStatus))
        {
            $showCompileResult = true;
            $this->app->loadLang('compile'); /* Import lang. */
            $this->view->compileUrl = $this->createLink('job', 'view', "jobID={$MR->jobID}&compileID={$MR->compileID}");
        }
        $this->view->showCompileResult = $showCompileResult;

        $this->view->MR      = $MR;
        $this->view->action  = $action;
        $this->view->actions = $this->loadModel('action')->getList('mrapproval', $MRID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Close this MR.
     *
     * @param  int $MRID
     * @return void
     */
    public function close($MRID)
    {
        $MR = $this->mr->getByID($MRID);
        $result = $this->mr->close($MR);

        return $this->send($result);
    }

    /**
     * Reopen this MR.
     *
     * @param  int $MRID
     * @return void
     */
    public function reopen($MRID)
    {
        $MR = $this->mr->getByID($MRID);
        return $this->send($this->mr->reopen($MR));
    }

    /**
     * link MR list.
     *
     * @param  int    $MRID
     * @param  string $type
     * @param  string $orderBy
     * @param  string $link
     * @param  string $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @return void
     */
    public function link($MRID, $type = 'story', $orderBy = 'id_desc', $link = 'false', $param = '', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        $this->app->loadLang('productplan');
        $this->app->loadLang('bug');
        $this->app->loadLang('task');

        $MR       = $this->mr->getByID($MRID);
        $product  = $this->mr->getMRProduct($MR);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $storyPager = new pager(0, $recPerPage, $type == 'story' ? $pageID : 1);
        $bugPager   = new pager(0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $taskPager  = new pager(0, $recPerPage, $type == 'task' ? $pageID : 1);

        $stories = $this->mr->getLinkList($MRID, $product->id, 'story', $orderBy, $storyPager);
        $bugs    = $this->mr->getLinkList($MRID, $product->id, 'bug', $orderBy, $bugPager);
        $tasks   = $this->mr->getLinkList($MRID, $product->id, 'task', $orderBy, $taskPager);

        $this->view->title        = $this->lang->mr->common . $this->lang->colon . $this->lang->mr->link;
        $this->view->MR           = $MR;
        $this->view->canBeChanged = true;
        $this->view->modulePairs  = $this->loadModel('tree')->getOptionMenu($product->id, 'story');
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->stories      = $stories;
        $this->view->bugs         = $bugs;
        $this->view->tasks        = $tasks;
        $this->view->product      = $product;
        $this->view->storyPager   = $storyPager;
        $this->view->bugPager     = $bugPager;
        $this->view->taskPager    = $taskPager;
        $this->view->type         = $type;
        $this->view->orderBy      = $orderBy;
        $this->view->link         = $link;
        $this->view->param        = $param;
        $this->display();
    }

    /**
     * Link story to mr.
     *
     * @param int    $MRID
     * @param int    $productID
     * @param string $browseType
     * @param int    $param
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     * @access public
     * @return void
     */
    public function linkStory($MRID, $productID = 0, $browseType = '', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->mr->link($MRID, $productID, 'story');

            if(dao::isError()) die(js::error(dao::getError()));
            die(js::locate(inlink('link', "MRID=$MRID&type=story&orderBy=$orderBy"), 'parent'));
        }

        $this->loadModel('story');
        $this->app->loadLang('productplan');

        $product = $this->loadModel('product')->getById($productID);
        $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'story');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $storyStatusList = $this->lang->story->statusList;
        unset($storyStatusList['closed']);
        $queryID         = ($browseType == 'bySearch') ? (int) $param : 0;

        unset($this->config->product->search['fields']['product']);
        $this->config->product->search['actionURL']                   = $this->createLink('mr', 'link', "$MRID=$MRID&type=story&orderBy=$orderBy&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->product->search['queryID']                     = $queryID;
        $this->config->product->search['style']                       = 'simple';
        $this->config->product->search['params']['product']['values'] = array($product) + array('all' => $this->lang->product->allProductsOfProject);
        $this->config->product->search['params']['plan']['values']    = $this->loadModel('productplan')->getForProducts(array($productID => $productID));
        $this->config->product->search['params']['module']['values']  = $modules;
        $this->config->product->search['params']['status']            = array('operator' => '=', 'control' => 'select', 'values' => $storyStatusList);

        if($product->type == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->product->setMenu($productID, 0);
            $this->config->product->search['fields']['branch']           = $this->lang->product->branch;
            $branches                                                    = array('' => '') + $this->loadModel('branch')->getPairs($productID, 'noempty');
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        $MR             = $this->mr->getByID($MRID);
        $relatedStories = $this->mr->getCommitedLink($MR->gitlabID, $MR->targetProject, $MR->mriid, 'story');

        $linkedStories = $this->mr->getLinkList($MRID, $product->id, 'story');
        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($productID, 0, $queryID, 'id', '', 'story', array_keys($linkedStories), $pager);
        }
        else
        {
            $allStories = $this->story->getProductStories($productID, 0, $moduleID   = '0', $status     = 'draft,active,changed', 'story', 'id_desc', $hasParent  = false, array_keys($linkedStories), $pager);
        }

        $this->view->modules        = $modules;
        $this->view->users          = $this->loadModel('user')->getPairs('noletter');
        $this->view->allStories     = $allStories;
        $this->view->relatedStories = $relatedStories;
        $this->view->product        = $product;
        $this->view->MRID           = $MRID;
        $this->view->browseType     = $browseType;
        $this->view->param          = $param;
        $this->view->orderBy        = $orderBy;
        $this->view->pager          = $pager;
        $this->display();
    }

    /**
     * Link bug to mr.
     *
     * @param int    $MRID
     * @param int    $productID
     * @param string $browseType
     * @param int    $param
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     * @access public
     * @return void
     */
    public function linkBug($MRID, $productID = 0, $browseType = '', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['bugs']))
        {
            $this->mr->link($MRID, $productID, 'bug');

            if(dao::isError()) die(js::error(dao::getError()));
            die(js::locate(inlink('link', "MRID=$MRID&type=bug&orderBy=$orderBy"), 'parent'));
        }

        $this->loadModel('bug');
        $this->app->loadLang('productplan');
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        $product = $this->loadModel('product')->getById($productID);
        $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'bug');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $this->config->bug->search['actionURL']                         = $this->createLink('mr', 'link', "$MRID=$MRID&type=bug&orderBy=$orderBy&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->bug->search['queryID']                           = $queryID;
        $this->config->bug->search['style']                             = 'simple';
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getForProducts(array($productID => $productID));
        $this->config->bug->search['params']['module']['values']        = $modules;
        $this->config->bug->search['params']['execution']['values']     = $this->product->getExecutionPairsByProduct($productID);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getBuildPairs($productID, $branch = 'all', $params = '');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->loadModel('build')->getBuildPairs($productID, $branch = 'all', $params = '');

        unset($this->config->bug->search['fields']['product']);
        if($product->type == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $this->product->setMenu($productID, 0);
            $this->config->bug->search['fields']['branch']           = $this->lang->product->branch;
            $branches                                                = array('' => '') + $this->loadModel('branch')->getPairs($productID, 'noempty');
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        $MR          = $this->mr->getByID($MRID);
        $relatedBugs = $this->mr->getCommitedLink($MR->gitlabID, $MR->targetProject, $MR->mriid, 'bug');

        $linkedBugs = $this->mr->getLinkList($MRID, $product->id, 'bug');
        if($browseType == 'bySearch')
        {
            $allBugs = $this->bug->getBySearch($productID, 0, $queryID, 'id_desc', array_keys($linkedBugs), $pager);
        }
        else
        {
            $allBugs = $this->bug->getActiveBugs($productID, 0, '0', array_keys($linkedBugs), $pager);
        }

        $this->view->modules     = $modules;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->allBugs     = $allBugs;
        $this->view->relatedBugs = $relatedBugs;
        $this->view->product     = $product;
        $this->view->MRID        = $MRID;
        $this->view->browseType  = $browseType;
        $this->view->param       = $param;
        $this->view->orderBy     = $orderBy;
        $this->view->pager       = $pager;
        $this->display();
    }

    /**
     * Link task to mr.
     *
     * @param int    $MRID
     * @param int    $productID
     * @param string $browseType
     * @param int    $param
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     * @access public
     * @return void
     */
    public function linkTask($MRID, $productID = 0, $browseType = 'unclosed', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['tasks']))
        {
            $this->mr->link($MRID, $productID, 'task');

            if(dao::isError()) die(js::error(dao::getError()));
            die(js::locate(inlink('link', "MRID=$MRID&type=task&orderBy=$orderBy"), 'parent'));
        }

        $this->loadModel('execution');
        $this->loadModel('product');
        $this->app->loadLang('task');

        /* Set browse type. */
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;

        $product = $this->loadModel('product')->getById($productID);
        $modules = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'task');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $this->config->execution->search['actionURL']                     = $this->createLink('mr', 'link', "$MRID=$MRID&type=task&orderBy=$orderBy&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->execution->search['queryID']                       = $queryID;
        $this->config->execution->search['params']['module']['values']    = $modules;
        $this->config->execution->search['params']['execution']['values'] = $this->product->getExecutionPairsByProduct($productID);
        $this->loadModel('search')->setSearchParams($this->config->execution->search);

        $MR           = $this->mr->getByID($MRID);
        $relatedTasks = $this->mr->getCommitedLink($MR->gitlabID, $MR->targetProject, $MR->mriid, 'task');
        $linkedTasks  = $this->mr->getLinkList($MRID, $product->id, 'task');

        /* Get executions by product. */
        $productExecutions   = $this->product->getExecutionPairsByProduct($productID);
        $productExecutionIDs = array_filter(array_keys($productExecutions));
        $this->config->execution->search['params']['execution']['values'] = array_filter($productExecutions);

        /* Get tasks by executions. */
        $allTasks = array();
        foreach($productExecutionIDs as $productExecutionID)
        {
            $tasks    = $this->execution->getTasks(0, $productExecutionID, array(), $browseType, $queryID, 0, $orderBy, null);
            $allTasks = array_merge($tasks, $allTasks);
        }
        /* Filter linked tasks. */
        $linkedTaskIDs = array_keys($linkedTasks);
        foreach($allTasks as $key => $task)
        {
            if(in_array($task->id, $linkedTaskIDs)) unset($allTasks[$key]);
        }

        /* Page the records. */
        $pager->setRecTotal(count($allTasks));
        $pager->setPageTotal();
        if($pager->pageID > $pager->pageTotal) $pager->setPageID($pager->pageTotal);
        $count    = 1;
        $limitMin = ($pager->pageID - 1) * $pager->recPerPage;
        $limitMax = $pager->pageID * $pager->recPerPage;
        foreach($allTasks as $key => $task)
        {
            if($count <= $limitMin or $count > $limitMax) unset($allTasks[$key]);

            $count ++;
        }

        $this->view->modules      = $modules;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->allTasks     = $allTasks;
        $this->view->relatedTasks = $relatedTasks;
        $this->view->product      = $product;
        $this->view->MRID         = $MRID;
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->display();
    }

    /**
     * UnLink an mr link.
     *
     * @param int    $MRID
     * @param int    $productID
     * @param string $type
     * @param int    $linkID
     * @param string $confirm
     * @access public
     * @return mix
     */
    public function unlink($MRID, $productID, $type, $linkID, $confirm = 'no')
    {
        $this->app->loadLang('productplan');

        if($confirm == 'no')
        {
            die(js::confirm($this->lang->productplan->confirmUnlinkStory, $this->createLink('mr', 'unlink', "MRID=$MRID&productID=$productID&linkID=$linkID&type=$type&confirm=yes")));
        }
        else
        {
            $this->mr->unlink($MRID, $productID, $type, $linkID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                return $this->send($response);
            }
            die(js::reload('parent'));
        }
    }

    /**
     * Add a review for this review.
     *
     * @param  int    $repoID
     * @param  int    $mr
     * @param  int    $v1
     * @param  int    $v2
     * @access public
     * @return void
     */
    public function addReview($repoID, $mr, $v1, $v2)
    {
        /* Handle the exception that when $repoID is empty. */
        if($repoID == "0") $this->send(array());

        $this->loadModel('repo');
        if(!empty($_POST))
        {
            if($this->post->reviewType == 'bug')  $result = $this->mr->saveBug($repoID, $mr, $v1, $v2);
            if($this->post->reviewType == 'task') $result = $this->mr->saveTask($repoID, $mr, $v1, $v2);
            if($result['result'] == 'fail') die(json_encode($result));

            $objectID = $result['id'];
            $repo     = $this->repo->getRepoById($repoID);
            /* Handle the exception that when $repo is empty. */
            if(empty($repo) or empty($result)) $this->send(json_encode(array()));

            $location = sprintf($this->lang->repo->reviewLocation, $this->post->entry ? base64_decode($this->post->entry) : '', $repo->SCM != 'Subversion' ? substr($v2, 0, 10) : $v2, $this->post->begin, $this->post->end);
            $link     = $this->createLink('mr', 'diff', "mr=$mr") . '#L' . $this->post->begin;

            $actionID = $this->loadModel('action')->create($this->post->reviewType, $objectID, 'repoCreated', '', html::a($link, $location));
            $this->loadModel('mail')->sendmail($objectID, $actionID);

            echo json_encode($result);
        }
    }

    /**
     * AJAX: Get MR target projects.
     *
     * @param  int    $gitlabID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function ajaxGetMRTargetProjects($gitlabID, $projectID)
    {
        $this->loadModel('gitlab');

        /* First step: get forks. Only get first level forks(not recursively). */
        $projects = $this->gitlab->apiGetForks($gitlabID, $projectID);

        /* Second step: get project itself. */
        $projects[] = $this->gitlab->apiGetSingleProject($gitlabID, $projectID);

        /* Last step: find its upstream recursively. */
        $project = $this->gitlab->apiGetUpstream($gitlabID, $projectID);
        if(!empty($project)) $projects[] = $project;

        while(!empty($project) and isset($project->id))
        {
            $project = $this->gitlab->apiGetUpstream($gitlabID, $project->id);
            if(empty($project)) break;
            $projects[] = $project;
        }

        $groupIDList = array(0 => 0);
        $groups      = $this->gitlab->apiGetGroups($gitlabID, 'name_asc', 'developer');
        foreach($groups as $group) $groupIDList[] = $group->id;
        foreach($projects as $key => $project)
        {
            if($this->gitlab->checkUserAccess($gitlabID, 0, $project, $groupIDList, 'developer') == false) unset($projects[$key]);
        }

        if(!$projects) return $this->send(array('message' => array()));

        $options = "<option value=''></option>";
        foreach($projects as $project)
        {
            $options .= "<option value='{$project->id}' data-name='{$project->name}'>{$project->name_with_namespace}</option>";
        }

        $this->send($options);
    }

    /**
     * AJAX: Get repo list.
     *
     * @param  int $gitlabID
     * @param  int $projectID
     * @return void
     */
    public function ajaxGetRepoList($gitlabID, $projectID)
    {
        $this->loadModel('repo');
        $repoList = $this->repo->getGitLabRepoList($gitlabID, $projectID);

        if(!$repoList) return $this->send(array('message' => array()));
        $options = "<option value=''></option>";
        foreach($repoList as $repo) $options .= "<option value='{$repo->id}' data-name='{$repo->name}'>[{$repo->id}] {$repo->name}</option>";
        $this->send($options);
    }

    /**
     * AJAX: Get job list.
     *
     * @param  int $repoID
     * @return void
     */
    public function ajaxGetJobList($repoID)
    {
        $this->loadModel('job');
        $jobList = $this->job->getListByRepoID($repoID);

        if(!$jobList) return $this->send(array('message' => array()));
        $options = "<option value=''></option>";
        foreach($jobList as $job) $options .= "<option value='{$job->id}' data-name='{$job->name}'>[{$job->id}] {$job->name}</option>";
        $this->send($options);
   }

   /**
    * AJAX: Get compile list.
    *
    * @param  int $jobID
    * @return void
    */
   public function ajaxGetCompileList($jobID)
   {
        $this->loadModel('compile');
        $compileList = $this->compile->getListByJobID($jobID);

        if(!$compileList) return $this->send(array('message' => array()));
        $options = "<option value=''></option>";
        foreach($compileList as $compile) $options .= "<option value='{$compile->id}' data-name='{$compile->name}'>[{$compile->id}] [{$this->lang->compile->statusList[$compile->status]}] {$compile->name}</option>";
        $this->send($options);
   }

   /**
    * Ajax check same opened mr for source branch.
    *
    * @param  int    $gitlabID
    * @access public
    * @return void
    */
   public function ajaxCheckSameOpened($gitlabID)
   {
       $sourceProject = $this->post->sourceProject;
       $sourceBranch  = $this->post->sourceBranch;
       $targetProject = $this->post->targetProject;
       $targetBranch  = $this->post->targetBranch;

       $result = $this->mr->checkSameOpened($gitlabID, $sourceProject, $sourceBranch, $targetProject, $targetBranch);
       die(json_encode($result));
   }
}
