<?php
class giteaTest
{
    public $tester;

    public function __construct()
    {
        global $tester;
        $this->tester = $tester;
        $this->gitea  = $this->tester->loadModel('gitea');
    }

    /**
     * Get by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getByID($id)
    {
        $gitea = $this->gitea->getByID($id);
        if(empty($gitea)) return 0;
        return $gitea;
    }

    /**
     * Get gitea list.
     *
     * @param  string $orderBy
     * @access public
     * @return object
     */
    public function getList($orderBy = 'id_desc')
    {
        $gitea = $this->gitea->getList($orderBy);
        if(empty($gitea)) return 0;
        return array_shift($gitea);
    }

    /**
     * Get gitea pairs
     *
     * @return string
     */
    public function getPairs()
    {
        $pairs = $this->gitea->getPairs();
        return key($pairs);
    }

    /**
     * Get gitea tasks.
     *
     * @param  int    $id
     * @access public
     * @return array
     */
    public function getTasks($id)
    {
        $tasks = $this->gitea->getTasks($id);
        if(empty($tasks)) return 0;
        return $tasks;
    }

    /**
     * Create a gitea.
     *
     * @access public
     * @return object|string
     */
    public function create()
    {
        $giteaID = $this->gitea->create();
        if(dao::isError())
        {
            $errors = dao::getError();
            return key($errors);
        }

        return $this->gitea->getById($giteaID);
    }

    /**
     * Update a gitea.
     *
     * @param  int    $id
     * @access public
     * @return object|string
     */
    public function update($id)
    {
        $this->gitea->update($id);
        if(dao::isError())
        {
            $errors = dao::getError();
            return key($errors);
        }

        return $this->gitea->getById($id);
    }
}
