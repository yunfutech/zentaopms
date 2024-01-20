<?php
class jobTest
{
    public function __construct()
    {
        global $tester;
        $this->objectModel = $tester->loadModel('job');
    }

    /**
     * Test get job by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getByIdTest($id)
    {
        $object = $this->objectModel->getById($id);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get job list.
     *
     * @param  string $orderBy
     * @param  int    $pager
     * @access public
     * @return object
     */
    public function getListTest($orderBy = 'id_desc', $pager = null)
    {
        $objects = $this->objectModel->getList($orderBy, $pager);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get list by repo id.
     *
     * @param  int    $repoID
     * @access public
     * @return object
     */
    public function getListByRepoIDTest($repoID)
    {
        return $this->objectModel->getListByRepoID($repoID);
    }

    /**
     * Test get job pairs.
     *
     * @param  int    $repoID
     * @param  string $engine
     * @access public
     * @return object
     */
    public function getPairsTest($repoID, $engine = '')
    {
        $objects = $this->objectModel->getPairs($repoID, $engine);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get job list by trigger type.
     *
     * @param  string $triggerType
     * @param  array  $repoIdList
     * @access public
     * @return object
     */
    public function getListByTriggerTypeTest($triggerType, $repoIdList = array())
    {
        $objects = $this->objectModel->getListByTriggerType($triggerType, $repoIdList);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get trigger config.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getTriggerConfigTest($id)
    {

        $job = $this->objectModel->getById($id);

        $object = $this->objectModel->getTriggerConfig($job);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get trigger group.
     *
     * @param  string $triggerType
     * @param  array  $repoIdList
     * @access public
     * @return array
     */
    public function getTriggerGroupTest($triggerType, $repoIdList)
    {
        $array = $this->objectModel->getTriggerGroup($triggerType, $repoIdList);

        if(dao::isError()) return dao::getError();

        $group = array();
        if($triggerType == 'tag')    $group = isset($array[1]) ? $array[1] : array();
        if($triggerType == 'commit') $group = isset($array[2]) ? $array[2] : array();

        return $group;
    }

    /**
     * Test create object.
     *
     * @param  array  $params
     * @access public
     * @return object
     */
    public function createObject($params)
    {
        $createFields['name']        = '这是一个JOB3';
        $createFields['engine']      = 'gitlab';
        $createFields['repo']        = '1';
        $createFields['reference']   = 'test_case';
        $createFields['product']     = '1';
        $createFields['frame']       = 'phpunit';
        $createFields['triggerType'] = 'commit';

        foreach($createFields as $field => $defaultValue) $_POST[$field] = $defaultValue;

        foreach($params as $key => $value) $_POST[$key] = $value;

        $objectID   = $this->objectModel->create();
        unset($_POST);
        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($objectID);
        return $object;
    }

    /**
     * Test update object.
     *
     * @param  int    $jobId
     * @param  array  $params
     * @access public
     * @return string
     */
    public function updateObject($jobId, $params = array())
    {
        global $tester;
        $object = $tester->dbh->query("SELECT * FROM " . TABLE_JOB  ." WHERE id = $jobId")->fetch();

        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($params)))
            {
                $_POST[$field] = $params[$field];
            }
            else
            {
                $_POST[$field] = $value;
            }
        }
        $_POST['jkServer'] = 3;
        $_POST['jkTask']   = 'dave';

        $result = $this->objectModel->update($jobId);
        unset($_POST);
        if(dao::isError()) return dao::getError()[0];

        return $result ? 'Job更新成功' : 'Job更新失败';
    }

    /**
     * Test exec job.
     *
     * @param  int    $id
     * @param  array  $extraParam
     * @access public
     * @return object
     */
    public function execTest($id, $extraParam = array())
    {
        $object = $this->objectModel->exec($id, $extraParam);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test exec jenkins pipeline.
     *
     * @param  int    $jobID
     * @param  object $repo
     * @param  int    $compileID
     * @param  array  $extraParam
     * @access public
     * @return object
     */
    public function execJenkinsPipelineTest($jobID, $repo, $compileID, $extraParam = array())
    {
        $job = $this->objectModel->getById($jobID);

        $object = $this->objectModel->execJenkinsPipeline($job, $repo, $compileID, $extraParam);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test exec gitlab pipeline.
     *
     * @param  int    $jobID
     * @access public
     * @return object
     */
    public function execGitlabPipelineTest($jobID)
    {
        $job = $this->objectModel->getById($jobID);

        $object = $this->objectModel->execGitlabPipeline($job);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get last tag by repo.
     *
     * @param  int    $repoId
     * @param  int    $jobId
     * @access public
     * @return string
     */
    public function getLastTagByRepoTest($jobId)
    {
        global $tester;
        $job    = $this->objectModel->getById($jobId);
        $repo   = $tester->loadModel('repo')->getRepoById($job->repo);
        $object = $this->objectModel->getLastTagByRepo($repo, $job);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get sonarqube by RepoID.
     *
     * @param  array  $repoIDList
     * @param  int    $jobID
     * @param  bool   $showDeleted
     * @access public
     * @return array
     */
    public function getSonarqubeByRepoTest($repoIDList, $jobID = 0, $showDeleted = false)
    {
        $array = $this->objectModel->getSonarqubeByRepo($repoIDList, $jobID, $showDeleted);

        if(dao::isError()) return dao::getError();

        return $array;
    }

    /**
     * Test get job pairs by sonarqube projectkeys.
     *
     * @param  int    $sonarqubeID
     * @param  array  $projectKeys
     * @param  bool   $emptyShowAll
     * @param  bool   $showDeleted
     * @access public
     * @return array
     */
    public function getJobBySonarqubeProjectTest($sonarqubeID, $projectKeys = array(), $emptyShowAll = false, $showDeleted = false)
    {
        $array = $this->objectModel->getJobBySonarqubeProject($sonarqubeID, $projectKeys, $emptyShowAll, $showDeleted);

        if(dao::isError()) return dao::getError();

        return $array;
    }

}
