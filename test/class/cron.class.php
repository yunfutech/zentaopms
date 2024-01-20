<?php
class cronTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('cron');
    }

    /**
     * Get by id test.
     *
     * @param  int    $cronID
     * @access public
     * @return object
     */
    public function getByIdTest($cronID)
    {
        $objects = $this->objectModel->getById($cronID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get crons test.
     *
     * @param  string $params
     * @access public
     * @return array
     */
    public function getCronsTest($params = '')
    {
        $objects = $this->objectModel->getCrons($params);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Parse crons test.
     *
     * @param  array    $crons
     * @access public
     * @return array
     */
    public function parseCronTest($crons)
    {
        $objects = $this->objectModel->parseCron($crons);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Change cron status test.
     *
     * @param  int    $cronID
     * @param  string $status
     * @param  bool   $changeTime
     * @access public
     * @return bool
     */
    public function changeStatusTest($cronID, $status, $changeTime = false)
    {
        $objects = $this->objectModel->changeStatus($cronID, $status, $changeTime);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Change cron status to running test.
     *
     * @param int    $cronID
     * @access public
     * @return bool|int
     */
    public function changeStatusRunningTest($cronID)
    {
        $objects = $this->objectModel->changeStatusRunning($cronID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Log cron test.
     *
     * @param  string    $log
     * @access public
     * @return void
     */
    public function logCronTest($log)
    {
        $objects = $this->objectModel->logCron($log);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get last execed time test.
     *
     * @access public
     * @return string
     */
    public function getLastTimeTest()
    {
        $objects = $this->objectModel->getLastTime();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Runable cron test.
     *
     * @access public
     * @return bool
     */
    public function runableTest()
    {
        $objects = $this->objectModel->runable();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Check change cron test.
     *
     * @access public
     * @return bool
     */
    public function checkChangeTest()
    {
        $objects = $this->objectModel->checkChange();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Create cron test.
     *
     * @param  array $param
     * @access public
     * @return int
     */
    public function createTest($param)
    {
        foreach($param as $k => $v) $_POST[$k] = $v;
        $cronID = $this->objectModel->create();
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getById($cronID);
        return $objects;
    }

    /**
     * Update cron test.
     *
     * @param  int    $cronID
     * @access public
     * @return bool
     */
    public function updateTest($cronID, $param)
    {
        foreach($param as $k => $v) $_POST[$k] = $v;
        $objects = $this->objectModel->update($cronID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getById($cronID);
        return $objects;
    }

    /**
     * Check cron rule test.
     *
     * @param  object $cron
     * @access public
     * @return string
     */
    public function checkRuleTest($cron)
    {
        $objects = $this->objectModel->checkRule($cron);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Mark cron status test.
     * 
     * @param mixed $status 
     * @param int $configID 
     * @access public
     * @return void
     */
    public function markCronStatusTest($status, $configID = 0)
    {
        $objects = $this->objectModel->markCronStatus($status, $configID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get config id test.
     * 
     * @access public
     * @return void
     */
    public function getConfigIDTest()
    {
        $objects = $this->objectModel->getConfigID();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get current cron status test.
     *
     * @access public
     * @return int
     */
    public function getTurnonTest()
    {
        $objects = $this->objectModel->getTurnon();

        if(dao::isError()) return dao::getError();

        return $objects;
    }
}
