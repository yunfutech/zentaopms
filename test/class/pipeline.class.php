<?php
class pipelineTest
{
    /**  
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        global $tester;
        
        $this->objectModel = $tester->loadModel('pipeline');
    }

    /**
     * Get a pipeline by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getByIDTest($id)
    {
        $objects = $this->objectModel->getByID($id);

        if(dao::isError()) return dao::getError();

        return $objects;
    }
    
    /** 
     * Get pipeline list.
     *
     * @param  string $type jenkins|gitlab
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getListTest($type = 'jenkins', $orderBy = 'id_desc', $pager = null)
    {
        $objects = $this->objectModel->getList($type, $orderBy, $pager);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get pipeline pairs
     *
     * @param  int    $data 
     * @access public
     * @return array 
     */
    public function getPairs($data)
    {
        $objects = $this->objectModel->getPairs($data['type']);

        if(empty($objects))    return '没有获取到数据'; 
        if(isset($data['id'])) return $objects[$data['id']]; 
        if(dao::isError())     return dao::getError();

        return $objects;
    }

    /**
     * Create a pipeline.
     *
     * @param  int    $type 
     * @param  int    $param 
     * @access public
     * @return void
     */
    public function createTest($type, $param)
    {
        foreach($param as $k => $v) $_POST[$k] = $v;
        $objects = $this->objectModel->create($type);
        unset($_POST);
        
        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getById($objects);

        return $objects;
    }

    /**
     * Update a pipeline.
     *
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function updateTest($id)
    {
        $objects = $this->objectModel->update($id);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($id);

        return $objects;
    }

    /**
     * Delete one record.
     *
     * @param  string $id     the id to be deleted
     * @param  string $object the action object
     * @access public
     * @return int
     */
    public function deleteTest($id, $object = 'gitlab')
    {
        $objects = $this->objectModel->delete($id, $object = 'gitlab');

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getById($id);

        return $objects;
    }
}
