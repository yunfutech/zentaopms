<?php
class mailTest
{
    /**
     * __construct. 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
         global $tester;
         global $dao;
         $this->dao         = $dao;
         $this->tester      = $tester;
         $this->objectModel = $tester->loadModel('mail');
    }

    /**
     * AutoDetect. 
     * 
     * @param  int    $email 
     * @access public
     * @return object
     */
    public function autoDetectTest($email)
    {
        $objects = $this->objectModel->autoDetect($email);

        if(dao::isError())  return dao::getError();
        if(!$objects->host) return '没有检测到相关信息';
        
        return $objects;
    }

    /**
     * Get config from provider. 
     * 
     * @param  int    $domain 
     * @param  int    $username 
     * @access public
     * @return object
     */
    public function getConfigFromProviderTest($domain, $username)
    {
        $object = $this->objectModel->getConfigFromProvider($domain, $username);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Get config by MXRR. 
     * 
     * @param  int    $domain 
     * @param  int    $username 
     * @access public
     * @return object
     */
    public function getConfigByMXRRTest($domain, $username)
    {
        $object = $this->objectModel->getConfigByMXRR($domain, $username);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Get config by detecting SMTP. 
     * 
     * @param  int    $domain 
     * @param  int    $username 
     * @param  int    $port 
     * @access public
     * @return object
     */
    public function getConfigByDetectingSMTPTest($domain, $username, $port)
    {
        $object = $this->objectModel->getConfigByDetectingSMTP($domain, $username, $port);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Set MTA. 
     * 
     * @access public
     * @return object
     */
    public function setMTATest()
    {
        $object = $this->objectModel->setMTA();

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Set sendmail. 
     * 
     * @access public
     * @return object
     */
    public function setSendMailTest()
    {
        $object = $this->objectModel->setSendMail();

        if(dao::isError()) return dao::getError();

        return $object;
    }


    /**
     * Send. 
     * 
     * @param  int    $toList 
     * @param  int    $subject 
     * @param  string $body 
     * @param  string $ccList 
     * @param  int    $includeMe 
     * @param  array  $emails 
     * @access public
     * @return object
     */
    public function sendTest($toList, $subject, $body = '', $ccList = '', $includeMe = false, $emails = array())
    {
        $object = $this->objectModel->send($toList, $subject, $body, $ccList, $includeMe, $emails);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Set CC. 
     * 
     * @param  int    $ccList 
     * @param  int    $emails 
     * @access public
     * @return object
     */
    public function setCCTest($ccList, $emails)
    {
        $this->objectModel->setCC($ccList, $emails);

        return $this->setMTATest();
    }

    /**
     * Set subject. 
     * 
     * @param  int    $subject 
     * @access public
     * @return object
     */
    public function setSubjectTest($subject)
    {
        $this->objectModel->setSubject($subject);

        return $this->setMTATest();
    }

    /**
     * Set body. 
     * 
     * @param  int    $body 
     * @access public
     * @return object
     */
    public function setBodyTest($body)
    {
        $this->objectModel->setBody($body);

        return $this->setMTATest();
    }

    /**
     * Mail exist. 
     * 
     * @access public
     * @return object
     */
    public function mailExistTest()
    {
        $object = $this->objectModel->mailExist();

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Add queue. 
     * 
     * @param  int    $toList 
     * @param  int    $subject 
     * @param  string $body 
     * @param  string $ccList 
     * @param  int    $includeMe 
     * @access public
     * @return object
     */
    public function addQueueTest($toList, $subject, $body = '', $ccList = '', $includeMe = false)
    {
        $this->objectModel->addQueue($toList, $subject, $body, $ccList, $includeMe);

        if($toList and $subject)
        {
            $id     = $this->dao->lastInsertID();
            $object = $this->dao->select('*')->from(TABLE_NOTIFY)->where('id')->eq($id)->fetch(); 
        }

        if(!$object)       return '没有数据提交';
        if(dao::isError()) return dao::getError();
        
        return $object; 
    }

    /**
     * Get queue. 
     * 
     * @param  string $status 
     * @param  string $orderBy 
     * @param  int    $pager 
     * @access public
     * @return object
     */
    public function getQueueTest($status = '', $orderBy = 'id_desc', $pager = null)
    {
        $objects = $this->objectModel->getQueue($status = '', $orderBy = 'id_desc', $pager = null);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get queue by id. 
     * 
     * @param  int    $queueID 
     * @access public
     * @return object
     */
    public function getQueueByIdTest($queueID)
    {
        $object = $this->objectModel->getQueueById($queueID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Merge mails. 
     * 
     * @param  int    $user 
     * @access public
     * @return object
     */
    public function mergeMailsTest($user)
    {
        $mails = $this->dao->select('*')->from(TABLE_NOTIFY)
            ->where('objectType')->eq('mail')
            ->andWhere('toList')->eq($user)
            ->fetchAll();
        
        $object = $this->objectModel->mergeMails($mails);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Get subject. 
     * 
     * @param  int    $objectType 
     * @param  int    $object 
     * @param  int    $title 
     * @param  int    $actionType 
     * @access public
     * @return object
     */
    public function getSubjectTest($objectType, $object, $title, $actionType)
    {
        $object = $this->tester->loadModel($objectType)->getByID($object);
        $object = $this->objectModel->getSubject($objectType, $object, $title, $actionType);

        if(dao::isError()) return dao::getError();

        return $object;
    }
}
