<?php
class tutorialTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('tutorial');
    }

    /**
     * Check novice.
     * 
     * @access public
     * @return void
     */
    public function checkNoviceTest()
    {
        $this->app->user->modifyPassword = 1;
        $objects = $this->objectModel->checkNovice();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial product pairs.
     * 
     * @access public
     * @return void
     */
    public function getProductPairsTest()
    {
        $objects = $this->objectModel->getProductPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get module pairs for tutorial.
     * 
     * @access public
     * @return void
     */
    public function getModulePairsTest()
    {
        $objects = $this->objectModel->getModulePairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial product.
     * 
     * @access public
     * @return void
     */
    public function getProductTest()
    {
        $objects = $this->objectModel->getProduct();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get product stats for tutorial.
     * 
     * @access public
     * @return void
     */
    public function getProductStatsTest()
    {
        $objects = $this->objectModel->getProductStats();

        if(dao::isError()) return dao::getError();

        return $objects[0][0]['products'];
    }

    /**
     * Get project for tutorial.
     * 
     * @access public
     * @return void
     */
    public function getProjectTest()
    {
        $objects = $this->objectModel->getProject();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial project pairs.
     * 
     * @access public
     * @return void
     */
    public function getProjectPairsTest()
    {
        $objects = $this->objectModel->getProjectPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get project stats for tutorial.
     * 
     * @param  string $browseType 
     * @access public
     * @return void
     */
    public function getProjectStatsTest($browseType = '')
    {
        $objects = $this->objectModel->getProjectStats($browseType = '');

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial stories.
     * 
     * @access public
     * @return void
     */
    public function getStoriesTest()
    {
        $objects = $this->objectModel->getStories();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial Execution pairs.
     * 
     * @access public
     * @return void
     */
    public function getExecutionPairsTest()
    {
        $objects = $this->objectModel->getExecutionPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial execution.
     * 
     * @access public
     * @return void
     */
    public function getExecutionTest()
    {
        $objects = $this->objectModel->getExecution();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial execution products.
     * 
     * @access public
     * @return void
     */
    public function getExecutionProductsTest()
    {
        $objects = $this->objectModel->getExecutionProducts();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial execution stories.
     * 
     * @access public
     * @return void
     */
    public function getExecutionStoriesTest()
    {
        $objects = $this->objectModel->getExecutionStories();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial execution story pairs.
     * 
     * @access public
     * @return void
     */
    public function getExecutionStoryPairsTest()
    {
        $objects = $this->objectModel->getExecutionStoryPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial team members.
     * 
     * @access public
     * @return void
     */
    public function getTeamMembersTest()
    {
        $objects = $this->objectModel->getTeamMembers();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get team members pairs.
     * 
     * @access public
     * @return void
     */
    public function getTeamMembersPairsTest()
    {
        $objects = $this->objectModel->getTeamMembersPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorial user pairs.
     * 
     * @access public
     * @return void
     */
    public function getUserPairsTest()
    {
        $objects = $this->objectModel->getUserPairs();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get tutorialed.
     * 
     * @access public
     * @return void
     */
    public function getTutorialedTest()
    {
        $objects = $this->objectModel->getTutorialed();

        if(dao::isError()) return dao::getError();

        return $objects;
    }
}
