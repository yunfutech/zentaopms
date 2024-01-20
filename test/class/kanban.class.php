<?php
class kanbanTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('kanban');
    }

    /**
     * Test create a kanban group.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @access public
     * @return object
     */
    public function createGroupTest($kanbanID, $regionID)
    {
        $objectID = $this->objectModel->createGroup($kanbanID, $regionID);

        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->findByID($objectID)->from(TABLE_KANBANGROUP)->fetch();
        return $object;
    }

    /**
     * Test create a default kanban region.
     *
     * @param  int    $kanbanID
     * @access public
     * @return object
     */
    public function createDefaultRegionTest($kanban)
    {
        $objectID = $this->objectModel->createDefaultRegion($kanban);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getRegionByID($objectID);
        return $object;
    }

    /**
     * Test create a region.
     *
     * @param  object $kanban
     * @param  object $region
     * @param  int    $copyRegionID
     * @access public
     * @return object
     */
    public function createRegionTest($kanban, $region = null, $copyRegionID = 0)
    {
        if(!is_null($region))
        {
            if(empty($copyRegionID))
            {
                foreach($region as $key => $value) $_POST[$key] = $value;
                $region = null;
            }

            $objectID = $this->objectModel->createRegion($kanban, $region, $copyRegionID);
        }
        else
        {
            $objectID = $this->objectModel->createDefaultRegion($kanban);
        }

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getRegionByID($objectID);
        return $object;
    }

    /**
     * Test create default kanban lanes.
     *
     * @param  int    $regionID
     * @param  int    $groupID
     * @access public
     * @return object
     */
    public function createDefaultLaneTest($regionID, $groupID)
    {
        $objectID = $this->objectModel->createDefaultLane(null, $regionID, $groupID);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getLaneByID($objectID);
        return $object;
    }

    /**
     * Test create default kanban columns.
     *
     * @param  int    $regionID
     * @param  int    $groupID
     * @access public
     * @return int
     */
    public function createDefaultColumnsTest($regionID, $groupID)
    {
        $this->objectModel->createDefaultColumns(null, $regionID, $groupID);


        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`region`')->eq($regionID)->andWhere('`group`')->eq($groupID)->andWhere('`type`')->like('column%')->fetchAll();
        return count($objects);
    }

    /**
     * Test create a column.
     *
     * @param  object $param
     * @access public
     * @return object
     */
    public function createColumnTest($param)
    {
        $regionID = 1;

        foreach($param as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->createColumn($regionID, null, 0, $param->parent);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getColumnByID($objectID);
        return $object;
    }

    /**
     * Test split column.
     *
     * @param  int    $columnID
     * @param  array  $param
     * @access public
     * @return int
     */
    public function splitColumnTest($columnID, $param)
    {
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objects = $this->objectModel->splitColumn($columnID);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        $childs = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`parent`')->eq($columnID)->fetchAll();
        return count($childs);
    }

    /**
     * Test create a card.
     *
     * @param  array  $param
     * @access public
     * @return object
     */
    public function createCardTest($param)
    {
        $kanbanID = 1;
        $regionID = 1;
        $groupID  = 1;
        $columnID = 1;

        $_POST['lane']  = 1;
        $_POST['begin'] = date('Y-m-d', strtotime("-3 day"));
        $_POST['end']   = date('Y-m-d', strtotime("+3 day"));
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->createCard($kanbanID, $regionID, $groupID, $columnID);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getCardByID($objectID);
        return $object;
    }

    /**
     * Test import card.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $columnID
     * @param  array  $cards
     * @param  int    $targetLane
     * @access public
     * @return object
     */
    public function importCardTest($kanbanID, $regionID, $groupID, $columnID, $cards, $targetLane)
    {
        $_POST['cards']      = $cards;
        $_POST['targetLane'] = $targetLane;

        $this->objectModel->importCard($kanbanID, $regionID, $groupID, $columnID);

        unset($_POST);
        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('`lane`')->eq($targetLane)->andWhere('`column`')->eq($columnID)->andWhere('kanban')->eq($kanbanID)->andWhere('type')->eq('common')->fetch();
        return $object;
    }

    /**
     * Test import object.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $columnID
     * @param  string $objectType
     * @param  array  $param
     * @access public
     * @return object
     */
    public function importObjectTest($kanbanID, $regionID, $groupID, $columnID, $objectType, $param)
    {
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objects = $this->objectModel->importObject($kanbanID, $regionID, $groupID, $columnID, $objectType);

        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('`lane`')->eq($param['targetLane'])->andWhere('`column`')->eq($columnID)->andWhere('kanban')->eq($kanbanID)->andWhere('type')->eq('common')->fetch();
        return $object;
    }

    /**
     * Test batch create kanban cards.
     *
     * @param  array  $param
     * @param  bool   $endLtBegin
     * @access public
     * @return object
     */
    public function batchCreateCardTest($param = array(), $endLtBegin = false)
    {
        $kanbanID = 1;
        $regionID = 1;
        $groupID  = 1;
        $columnID = 1;
        $batchCreateFields['name']       = array('0' => '批量创建卡片1', '1' => '批量创建卡片2', '2' => '批量创建卡片3', '3' => '批量创建卡片4');
        $batchCreateFields['lane']       = array('0' => '1', '1' => 'ditto', '2' => 'ditto', '3' => 'ditto');
        $batchCreateFields['assignedTo'] = array();
        $batchCreateFields['estimate']   = array('0' => '1', '1' => '2', '2' => '3', '3' => '4');
        $batchCreateFields['begin']      = array('0' => date('Y-m-d', time()), '1' => date('Y-m-d', time()), '2' => '', '3' => '');
        $batchCreateFields['end']        = !$endLtBegin ? array('0' => date('Y-m-d', time()), '1' => '', '2' => '', '3' => '') : array('0' => date('Y-m-d', strtotime("-1 day")), '1' => date('Y-m-d', strtotime("-1 day")), '2' => date('Y-m-d', strtotime("-2 day")), '3' => date('Y-m-d', strtotime("-3 day")));
        $batchCreateFields['desc']       = array('0' => '描述1', '1' => '描述2', '2' => '描述3', '3' => '描述4');
        $batchCreateFields['pri']        = array('0' => '1', '1' => '2', '2' => '3', '3' => '4');
        $batchCreateFields['beginDitto'] = array('1' => 'on', '2' => 'on', '3' => 'on', '4' => 'on');
        $batchCreateFields['endDitto']   = array('1' => 'on', '2' => 'on', '3' => 'on', '4' => 'on');

        foreach($batchCreateFields as $field => $defaultValue) $_POST[$field] = $defaultValue;

        foreach($param as $key => $value) $_POST[$key] = $value;

        $this->objectModel->batchCreateCard($kanbanID, $regionID, $groupID, $columnID);

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('kanban')->eq($kanbanID)->andWhere('lane')->eq(1)->andWhere('`column`')->eq($columnID)->andWhere('type')->eq('common')->fetch();

        unset($_POST);

        if(dao::isError()) return dao::getError()[0];

        return $object;
    }

    /**
     * Test get kanban by id.
     *
     * @param  int    $kanbanID
     * @access public
     * @return object
     */
    public function getByIDTest($kanbanID)
    {
        $object = $this->objectModel->getByID($kanbanID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get kanban data.
     *
     * @param  int    $kanbanID
     * @access public
     * @return string
     */
    public function getKanbanDataTest($kanbanID)
    {
        $objects = $this->objectModel->getKanbanData($kanbanID);

        if(dao::isError()) return dao::getError();

        $columnCount = 0;
        $laneCount   = 0;
        $cardCount   = 0;
        foreach($objects as $regions)
        {
            foreach($regions->groups as $group)
            {
                foreach($group->lanes as $lane) $cardCount += count($lane->items);

                $columnCount += count($group->columns);
                $laneCount += count($group->lanes);
            }
        }
        return 'columns:' . $columnCount . ', lanes:' . $laneCount . ', cards:' . $cardCount;
    }

    /**
     * Test get plan kanban.
     *
     * @param  int    $productID
     * @param  int    $branchID
     * @access public
     * @return string
     */
    public function getPlanKanbanTest($productID, $branchID = 0)
    {
        global $tester;
        $product = $tester->loadModel('product')->getByID($productID);

        $tester->loadModel('productplan');
        $planGroup = $product->type == 'normal' ? $tester->productplan->getList($product->id, 0, 'all', '', 'begin_desc', 'skipparent') : $tester->productplan->getGroupByProduct($product->id, 'skipParent', '', 'begin_desc');

        $objects = $this->objectModel->getPlanKanban($product, $branchID, $planGroup);

        if(dao::isError()) return dao::getError();

        $laneCount   = 0;
        $cardCount   = 0;
        foreach($objects->lanes as $lane)
        {
            foreach($lane->items as $item) $cardCount += count($item);
        }
        return 'lanes:' . count($objects->lanes) . ', cards:' . $cardCount;
    }

    /**
     * Test get a RD kanban data.
     *
     * @param  int    $executionID
     * @param  string $browseType
     * @param  int    $regionID
     * @access public
     * @return string
     */
    public function getRDKanbanTest($executionID, $browseType = 'all', $regionID = 0)
    {
        $objects = $this->objectModel->getRDKanban($executionID, $browseType, 'id_desc', $regionID);

        if(dao::isError()) return dao::getError();

        $columnCount = 0;
        $laneCount   = 0;
        $cardCount   = 0;
        foreach($objects as $regions)
        {
            foreach($regions->groups as $group)
            {
                $columnCount += count($group->columns);
                $laneCount += count($group->lanes);
                foreach($group->lanes as $lane)
                {
                    foreach($lane->items as $item) $cardCount += count($item);
                }
            }
        }
        return 'columns:' . $columnCount . ', lanes:' . $laneCount . ', cards:' . $cardCount;
    }

    /**
     * Test get region by id.
     *
     * @param  int    $regionID
     * @access public
     * @return object
     */
    public function getRegionByIDTest($regionID)
    {
        $object = $this->objectModel->getRegionByID($regionID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get ordered region pairs.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  string $from
     * @access public
     * @return array
     */
    public function getRegionPairsTest($kanbanID, $regionID = 0, $from = 'kanban')
    {
        $objects = $this->objectModel->getRegionPairs($kanbanID, $regionID, $from);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get kanban id by region id.
     *
     * @param  int    $regionID
     * @access public
     * @return int
     */
    public function getKanbanIDByRegionTest($regionID)
    {
        $object = $this->objectModel->getKanbanIDByRegion($regionID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get kanban group by regions.
     *
     * @param  string $regions
     * @access public
     * @return int
     */
    public function getGroupGroupByRegionsTest($regions)
    {
        $objects = $this->objectModel->getGroupGroupByRegions($regions);

        if(dao::isError()) return dao::getError();

        $count = 0;
        foreach($objects as $object) $count += count($object);

        return $count;
    }

    public function getLaneGroupByRegionsTest($regions, $browseType = 'all')
    {
        $objects = $this->objectModel->getLaneGroupByRegions($regions, $browseType);

        if(dao::isError()) return dao::getError();

        $count = 0;
        foreach($objects as $object) $count += count($object);
        return $count;
    }

    /**
     * Test get lane pairs by group id.
     *
     * @param  int    $groupID
     * @param  string $orderBy
     * @access public
     * @return string
     */
    public function getLanePairsByGroupTest($groupID, $orderBy = '`order`_asc')
    {
        $objects = $this->objectModel->getLanePairsByGroup($groupID, $orderBy = '`order`_asc');

        if(dao::isError()) return dao::getError();

        $names = implode(',', $objects);
        return $names;
    }

    public function getColumnGroupByRegionsTest($regions, $order = 'order')
    {
        $objects = $this->objectModel->getColumnGroupByRegions($regions, $order = 'order');

        if(dao::isError()) return dao::getError();

        $count = 0;
        foreach($objects as $object) $count += count($object);
        return $count;
    }

    /**
     * Test get card group by kanban id.
     *
     * @param  int    $kanbanID
     * @access public
     * @return int
     */
    public function getCardGroupByKanbanTest($kanbanID)
    {
        $objects = $this->objectModel->getCardGroupByKanban($kanbanID);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get imported cards.
     *
     * @param  int    $kanbanID
     * @param  string $fromType
     * @param  int    $archived
     * @param  int    $regionID
     * @access public
     * @return string
     */
    public function getImportedCardsTest($kanbanID, $fromType, $archived = 0, $regionID = 0)
    {
        global $tester;
        $cards = $tester->dao->select('*')->from(TABLE_KANBANCARD)
            ->where('deleted')->eq(0)
            ->andWhere('kanban')->eq($kanbanID)
            ->andWhere('archived')->eq($archived)
            ->andWhere('fromID')->eq(0)
            ->beginIF($regionID)->andWhere('region')->eq($regionID)->fi()
            ->fetchAll('id');

        $objects = $this->objectModel->getImportedCards($kanbanID, $cards, $fromType, $archived, $regionID);

        if(dao::isError()) return dao::getError();

        $ids = '';
        foreach($objects as $object) $ids .= ',' . $object->id;
        return $ids;
    }

    /**
     * Test get RD column group by regions.
     *
     * @param  int    $regions
     * @param  array  $groupIDList
     * @access public
     * @return int
     */
    public function getRDColumnGroupByRegionsTest($regions, $groupIDList = array())
    {
        $objects = $this->objectModel->getRDColumnGroupByRegions($regions, $groupIDList);

        if(dao::isError()) return dao::getError();

        $count = 0;
        foreach($objects as $object) $count += count($object);
        return $count;
    }

    /**
     * Test get card group by execution id.
     *
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $orderBy
     * @access public
     * @return int
     */
    public function getCardGroupByExecutionTest($executionID, $browseType = 'all')
    {
        $objects = $this->objectModel->getCardGroupByExecution($executionID, $browseType);

        if(dao::isError()) return dao::getError();

        $cardCount = 0;
        foreach($objects as $lane)
        {
            foreach($lane as $type) $cardCount += count($type);
        }
        return $cardCount;
    }

    /**
     * Test get Kanban by execution id.
     *
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $groupBy
     * @access public
     * @return string
     */
    public function getExecutionKanbanTest($executionID, $browseType = 'all', $groupBy = 'default')
    {
        $objects = $this->objectModel->getExecutionKanban($executionID, $browseType, $groupBy);

        if(empty($objects))
        {
            $this->objectModel->createExecutionLane($executionID, $browseType, $groupBy);
            $objects = $this->objectModel->getExecutionKanban($executionID, $browseType, $groupBy);
        }

        if(dao::isError()) return dao::getError();

        $columnCount = 0;
        $laneCount   = 0;
        $cardCount   = 0;
        foreach($objects as $types)
        {
            foreach($types['lanes'] as $lane)
            {
                foreach($lane['cards'] as $card) $cardCount += count($card);
            }
            $columnCount += count($types['columns']);
            $laneCount += count($types['lanes']);
        }
        return 'columns:' . $columnCount . ', lanes:' . $laneCount . ', cards:' . $cardCount;
    }

    /**
     * Test get kanban for group view.
     *
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $groupBy
     * @access public
     * @return int
     */
    public function getKanban4GroupTest($executionID, $browseType, $groupBy)
    {
        $objects = $this->objectModel->getKanban4Group($executionID, $browseType, $groupBy);

        if(empty($objects))
        {
            $this->objectModel->createExecutionLane($executionID, $browseType, $groupBy);
            $objects = $this->objectModel->getKanban4Group($executionID, $browseType, $groupBy);
        }

        if(dao::isError()) return dao::getError();

        $laneCount   = 0;
        foreach($objects as $types) $laneCount += count($types['lanes']);

        return 'lanes:' . $laneCount;
    }

    /**
     * Test get kanban for group view.
     *
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $groupBy
     * @access public
     * @return void
     */
    public function getLanes4GroupTest($executionID, $browseType, $groupBy)
    {
        global $tester;
        /* Get group objects. */
        if($browseType == 'story') $cardList = $tester->loadModel('story')->getExecutionStories($executionID, 0, 0, 't1.`order`_desc', 'allStory');
        if($browseType == 'bug')   $cardList = $tester->loadModel('bug')->getExecutionBugs($executionID);
        if($browseType == 'task')  $cardList = $tester->loadModel('execution')->getKanbanTasks($executionID, "id");
        $objects = $this->objectModel->getLanes4Group($executionID, $browseType, $groupBy, $cardList);

        if(empty($objects))
        {
            $this->objectModel->createExecutionLane($executionID, $browseType, $groupBy);
            $objects = $this->objectModel->getLanes4Group($executionID, $browseType, $groupBy);
        }

        if(dao::isError()) return dao::getError();

        $names = '';
        foreach($objects as $object) $names .= ',' . $object->name;
        return $names;
    }

    /**
     * Test get space list.
     *
     * @param  string $user
     * @param  string $browseType
     * @access public
     * @return int
     */
    public function getSpaceListTest($user, $browseType)
    {
        su($user);
        $objects = $this->objectModel->getSpaceList($browseType);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get space pairs.
     *
     * @param  int    $user
     * @param  string $browseType
     * @access public
     * @return int
     */
    public function getSpacePairsTest($user, $browseType = 'private')
    {
        su($user);
        $objects = $this->objectModel->getSpacePairs($browseType);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get Kanban pairs.
     *
     * @param  string $user
     * @access public
     * @return int
     */
    public function getKanbanPairsTest($user)
    {
        su($user);
        $objects = $this->objectModel->getKanbanPairs();

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get can view objects.
     *
     * @param  string $user
     * @param  string $objectType
     * @param  string $param
     * @access public
     * @return void
     */
    public function getCanViewObjectsTest($user, $objectType = 'kanban', $param = '')
    {
        su($user);
        $objects = $this->objectModel->getCanViewObjects($objectType, $param);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test create a space.
     *
     * @param  array   $param
     * @access public
     * @return object
     */
    public function createSpaceTest($param)
    {
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->createSpace();

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getSpaceByID($objectID);
        return $object;
    }

    /**
     * Test update a space.
     *
     * @param  int    $spaceID
     * @param  string $type
     * @param  array  $param
     * @access public
     * @return array
     */
    public function updateSpaceTest($spaceID, $type = '', $param = array())
    {
        global $tester;
        $object = $tester->dao->select('`type`,`name`,`owner`,`team`,`desc`,`whitelist`')->from(TABLE_KANBANSPACE)->where('`id`')->eq($spaceID)->fetch();
        $object->team      = explode(',', $object->team);
        $object->whitelist = explode(',', $object->whitelist);

        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($param)))
            {
                $_POST[$field] = $param[$field];
            }
            else
            {
                $_POST[$field] = $value;
            }
        }

        $change = $this->objectModel->updateSpace($spaceID, $type);
        if($change == array()) $change = '没有数据更新';

        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $change;
    }

    /**
     * Test get lane pairs by region id.
     *
     * @param  int    $regionID
     * @param  string $type
     * @access public
     * @return string
     */
    public function getLanePairsByRegionTest($regionID, $type = 'all')
    {
        global $tester;


        foreach($param as $key => $value) $_POST[$key] = $value;

        $objects = $this->objectModel->getLanePairsByRegion($regionID, $type);

        if(dao::isError()) return dao::getError();

        $names = implode(',', $objects);
        return $names;
    }

    /**
     * Test get lane group by regionid.
     *
     * @param  int    $regionID
     * @param  string $type
     * @access public
     * @return int
     */
    public function getLaneGroupByRegionTest($regionID, $type = 'all')
    {
        $objects = $this->objectModel->getLaneGroupByRegion($regionID, $type);

        if(dao::isError()) return dao::getError();

        return count($objects[$regionID]);
    }

    /**
     * Test create a lane.
     *
     * @param  object $object
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @access public
     * @return object
     */
    public function createLaneTest($object, $kanbanID, $regionID)
    {
        $lane = isset($object->id) ? $this->objectModel->getLaneByID($object->id) : null;
        unset($object->id);
        unset($lane->id);

        foreach($object as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->createLane($kanbanID, $regionID, $lane);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getLaneByID($objectID);
        return $object;
    }

    /**
     * Test create a kanban.
     *
     * @param  array  $param
     * @access public
     * @return object
     */
    public function createTest($param = array())
    {
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->create();

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($objectID);
        return $object;
    }

    /**
     * Test update a kanban.
     *
     * @param  int    $kanbanID
     * @param  array  $param
     * @access public
     * @return array
     */
    public function updateTest($kanbanID, $param)
    {
        global $tester;
        $object = $tester->dao->select('`space`,`name`,`owner`,`team`,`desc`')->from(TABLE_KANBAN)->where('`id`')->eq($kanbanID)->fetch();
        $object->team = explode(',', $object->team);

        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($param)))
            {
                $_POST[$field] = $param[$field];
            }
            else
            {
                $_POST[$field] = $value;
            }
        }

        $change = $this->objectModel->update($kanbanID);
        if($change == array()) $change = '没有数据更新';
        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $change;
    }

    /**
     * Test add execution Kanban lanes and columns.
     *
     * @param  int    $executionID
     * @param  string $type
     * @param  string $groupBy
     * @access public
     * @return int
     */
    public function createExecutionLaneTest($executionID, $type = 'all', $groupBy = 'default')
    {
        $this->objectModel->createExecutionLane($executionID, $type, $groupBy);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANLANE)
            ->where('execution')->eq($executionID)
            ->beginIF($type != 'all')->andWhere('type')->eq($type)->fi()
            ->fetchAll();
        return count($objects);
    }

    /**
     * Test create execution columns.
     *
     * @param  int    $laneID
     * @param  string $type
     * @param  int    $executionID
     * @access public
     * @return int
     */
    public function createExecutionColumnsTest($laneID, $type, $executionID)
    {
        $this->objectModel->createExecutionColumns($laneID, $type, $executionID);

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCELL)
            ->where('kanban')->eq($executionID)
            ->andWhere('lane')->eq($laneID)
            ->andWhere('type')->eq($type)
            ->fetchAll();

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test add kanban cell.
     *
     * @param  int    $kanbanID
     * @param  int    $laneID
     * @param  int    $colID
     * @param  int    $type
     * @param  int    $cardID
     * @access public
     * @return object
     */
    public function addKanbanCellTest($kanbanID, $laneID, $colID, $type, $cardID = 0)
    {
        $this->objectModel->addKanbanCell($kanbanID, $laneID, $colID, $type, $cardID);


        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('kanban')->eq($kanbanID)->andWhere('lane')->eq($laneID)->andWhere('`column`')->eq($colID)->andWhere('type')->eq($type)->fetch();
        return $object;
    }

    /**
     * Test remove kanban cell.
     *
     * @param  string $type
     * @param  int    $removeCardID
     * @param  array  $kanbanList
     * @access public
     * @return string
     */
    public function removeKanbanCellTest($type, $removeCardID, $kanbanList)
    {
        $this->objectModel->removeKanbanCell($type, $removeCardID, $kanbanList);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('kanban')->in($kanbanList)->andWhere('type')->eq($type)->fetchAll();
        $cards = '';
        foreach($objects as $object) $cards .= $object->id . ':' . $object->cards . '; ';
        $cards = trim($cards, '; ');
        $cards = str_replace(':,', ':', $cards);
        $cards = str_replace(':;', ';', $cards);
        return $cards;
    }

    /**
     * Test create rd kanban.
     *
     * @param  object $execution
     * @access public
     * @return string
     */
    public function createRDKanbanTest($execution)
    {
        $this->objectModel->createRDKanban($execution);

        if(dao::isError()) return dao::getError();

        global $tester;
        $regions = $tester->dao->select('*')->from(TABLE_KANBANREGION)->where('`kanban`')->eq($execution->id)->fetchAll('id');
        $lanes   = $tester->dao->select('*')->from(TABLE_KANBANLANE)->where('`execution`')->eq($execution->id)->andWhere('`type`')->ne('common')->fetchAll('id');
        $regionIDList = implode($regions, ',');
        $columns = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`region`')->in($regionIDList)->fetchAll('id');
        return count($regions) . ',' . count($lanes) . ',' . count($columns);
    }

    /**
     * Test create default RD region.
     *
     * @param  object $execution
     * @access public
     * @return object
     */
    public function createRDRegionTest($execution)
    {
        $objectID = $this->objectModel->createRDRegion($execution);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getRegionByID($objectID);
        return $object;
    }

    /**
     *
     * Test create default RD lanes.
     *
     * @param  int    $executionID
     * @param  int    $regionID
     * @access public
     * @return int
     */
    public function createRDLaneTest($executionID, $regionID)
    {
        $this->objectModel->createRDLane($executionID, $regionID);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANLANE)->where('`region`')->eq($regionID)->andWhere('`execution`')->eq($executionID)->fetchAll();
        return count($objects);
    }

    /**
     * Test create default RD columns.
     *
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $laneID
     * @param  string $laneType
     * @param  int    $executionID
     * @access public
     * @return int
     */
    public function createRDColumnTest($regionID, $groupID, $laneID, $laneType, $executionID)
    {
        $this->objectModel->createRDColumn($regionID, $groupID, $laneID, $laneType, $executionID);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`region`')->eq($regionID)->andWhere('`group`')->eq($groupID)->fetchAll();
        return count($objects);
    }

    /**
     * Test update a region.
     *
     * @param  int    $regionID
     * @param  string $name
     * @access public
     * @return array
     */
    public function updateRegionTest($regionID, $name)
    {
        $_POST['name']  = $name;

        $change = $this->objectModel->updateRegion($regionID);
        if($change == array()) $change = '没有数据更新';

        unset($_POST);
        if(dao::isError()) return dao::getError();

        return $change;
    }

    /**
     * Test update kanban lane.
     *
     * @param  int    $executionID
     * @param  string $laneType
     * @param  int     $cardID
     * @access public
     * @return string
     */
    public function updateLaneTest($executionID, $laneType, $cardID = 0)
    {
        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANLANE)->where('type')->ne('common')->andWhere('execution')->eq($executionID)->fetch();
        if(empty($objects)) $this->objectModel->createExecutionLane($executionID);

        $this->objectModel->updateLane($executionID, $laneType, $cardID);

        if(dao::isError()) return dao::getError();

        $objects = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('kanban')->eq($executionID)->andWhere('type')->eq($laneType)->fetchAll();
        $cards = '';
        foreach($objects as $object) $cards .= $object->cards;
        $cards = preg_replace('#,+#', ',', $cards);
        return $cards;
    }

    /**
     * Test refresh column cards.
     *
     * @param  int    $laneID
     * @access public
     * @return string
     */
    public function refreshCardsTest($laneID)
    {
        $lane = $this->objectModel->getLaneByID($laneID);
        $this->objectModel->refreshCards($lane);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('`lane`')->eq($laneID)->fetchAll();

        $cards = '';
        foreach($objects as $object) $cards .= $object->id . ':' . $object->cards . '; ';
        $cards = trim($cards, '; ');
        $cards = str_replace(':,', ':', $cards);
        $cards = str_replace(':;', ';', $cards);
        return $cards;
    }

    /**
     * Test update a column.
     *
     * @param  int    $columnID
     * @param  string $name
     * @param  string $color
     * @access public
     * @return array
     */
    public function updateLaneColumnTest($columnID, $name, $color)
    {
        $_POST['name']  = $name;
        $_POST['color'] = $color;

        $column = $this->objectModel->getColumnByID($columnID);
        $change = $this->objectModel->updateLaneColumn($columnID, $column);
        if($change == array()) $change = '没有数据更新';

        unset($_POST);
        if(dao::isError()) return dao::getError();

        return $change;
    }

    /**
     * Test activate a card.
     *
     * @param  int    $cardID
     * @param  int    $progress
     * @access public
     * @return object
     */
    public function activateCardTest($cardID, $progress)
    {
        global $tester;
        $tester->post->progress = $progress;

        $this->objectModel->activateCard($cardID);

        unset($this->post);

        if(dao::isError()) return dao::getError()[0];

        $object = $this->objectModel->getCardByID($cardID);
        return $object;
    }

    /**
     * Test update a card.
     *
     * @param  int    $cardID
     * @param  array  $param
     * @access public
     * @return array
     */
    public function updateCardTest($cardID, $param)
    {
        global $tester;
        $object = $tester->dao->select('`name`,`desc`,`assignedTo`,`begin`,`end`,`pri`,`estimate`, `progress`')->from(TABLE_KANBANCARD)->where('`id`')->eq($cardID)->fetch();
        $object->assignedTo = explode(',', $object->assignedTo);

        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($param)))
            {
                $_POST[$field] = $param[$field];
            }
            else
            {
                $_POST[$field] = $value;
            }
        }

        $change = $this->objectModel->updateCard($cardID);
        if($change == array()) $change = '没有数据更新';
        unset($_POST);


        if(dao::isError()) return dao::getError()[0];

        return $change;
    }

    /**
     * Test set WIP.
     *
     * @param  int    $columnID
     * @param  int    $limit
     * @param  int    $WIPCount
     * @access public
     * @return object
     */
    public function setWIPTest($columnID, $limit, $WIPCount)
    {
        $_POST['limit'] = $limit;
        if($WIPCount == '-1') $_POST['WIPCount'] = $WIPCount;
        if($WIPCount == '-1') $_POST['noLimit']  = $WIPCount;

        $this->objectModel->setWIP($columnID);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getColumnByID($columnID);
        return $object;
    }

    /**
     * Test set a lane.
     *
     * @param  int    $laneID
     * @access public
     * @return object
     */
    public function setLaneTest($laneID, $name, $color)
    {
        $_POST['name']  = $name;
        $_POST['color'] = $color;

        $this->objectModel->setLane($laneID);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getLaneByID($laneID);
        return $object;
    }

    /**
     * Test set kanban lane height.
     *
     * @param  int    $kanbanID
     * @param  string $heightType
     * @param  int    $displayCards
     * @param  string $from
     * @access public
     * @return object
     */
    public function setLaneHeightTest($kanbanID, $heightType, $displayCards, $from = 'kanban')
    {
        $_POST['heightType']   = $heightType;
        $_POST['displayCards'] = $displayCards;

        $this->objectModel->setLaneHeight($kanbanID, $from);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        $table = $tester->config->objectTables[$from];
        $object = $tester->dao->select('*')->from($table)->where('id')->eq($kanbanID)->fetch();
        return $object;
    }

    /**
     * Test set column width.
     *
     * @param  int    $kanbanID
     * @param  string $from
     * @access public
     * @return object
     */
    public function setColumnWidthTest($kanbanID, $fluidBoard, $from = 'kanban')
    {
        $_POST['fluidBoard'] = $fluidBoard;

        $this->objectModel->setColumnWidth($kanbanID, $from);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        $table = $tester->config->objectTables[$from];
        $object = $tester->dao->select('*')->from($table)->where('id')->eq($kanbanID)->fetch();
        return $object;
    }

    /**
     * Test sort kanban group;
     *
     * @param  int    $region
     * @param  array  $groups
     * @access public
     * @return string
     */
    public function sortGroupTest($region, $groups)
    {
        $this->objectModel->sortGroup($region, $groups);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_KANBANGROUP)->where('region')->eq($region)->orderBy('order_asc')->fetchAll('id');
        return implode(array_keys($objects), ',');
    }

    /**
     * Test move a card.
     *
     * @param  int    $cardID
     * @param  int    $fromColID
     * @param  int    $toColID
     * @param  int    $fromLaneID
     * @param  int    $toLaneID
     * @param  int    $kanbanID
     * @access public
     * @return object
     */
    public function moveCardTest($cardID, $fromColID, $toColID, $fromLaneID, $toLaneID, $kanbanID = 0)
    {
        $objects = $this->objectModel->moveCard($cardID, $fromColID, $toColID, $fromLaneID, $toLaneID, $kanbanID);

        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('`lane`')->eq($toLaneID)->andWhere('`column`')->eq($toColID)->andWhere('`type`')->eq('common')->fetch();
        return $object;
    }

    /**
     * Test update a card's color.
     *
     * @param  int    $cardID
     * @param  string $color
     * @access public
     * @return object
     */
    public function updateCardColorTest($cardID, $color)
    {
        $this->objectModel->updateCardColor($cardID, $color);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getCardByID($cardID);
        return $object;
    }

    /**
     * Test archive a column.
     *
     * @param  int    $columnID
     * @access public
     * @return array
     */
    public function archiveColumnTest($columnID)
    {
        $this->objectModel->archiveColumn($columnID);

        $object = $this->objectModel->getColumnByID($columnID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test restore a column.
     *
     * @param  int    $columnID
     * @access public
     * @return object
     */
    public function restoreColumnTest($columnID)
    {
        $this->objectModel->restoreColumn($columnID);

        if(dao::isError()) return dao::getError();

        global $tester;
        $object = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`id`')->eq($columnID)->fetch();
        return $object;
    }

    /**
     * Test archive a card.
     *
     * @param  int    $cardID
     * @access public
     * @return array
     */
    public function archiveCardTest($cardID)
    {
        $changes = $this->objectModel->archiveCard($cardID);

        if(dao::isError()) return dao::getError();

        return $changes;
    }

    /**
     * Test restore a card.
     *
     * @param  int    $cardID
     * @access public
     * @return array
     */
    public function restoreCardTest($cardID)
    {
        $objects = $this->objectModel->restoreCard($cardID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    public function processCardsTest($columnID)
    {
        $column  = $this->objectModel->getColumnByID($columnID);
        $this->objectModel->processCards($column);

        if(dao::isError()) return dao::getError();

        global $tester;
        $nodes   = $tester->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('`parent`')->eq($column->parent)->andWhere('`id`')->ne($columnID)->fetchAll('id');
        $objects = $tester->dao->select('*')->from(TABLE_KANBANCELL)->where('`column`')->in(array_keys($nodes))->fetchAll();

        $cards = '';
        foreach($objects as $object) $cards .= $object->id . ':' . $object->cards . '; ';
        $cards = trim($cards, '; ');
        return $cards;
    }

    /**
     * Test get space by id.
     *
     * @param  int    $spaceID
     * @access public
     * @return object
     */
    public function getSpaceByIdTest($spaceID)
    {
        $object = $this->objectModel->getSpaceById($spaceID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get kanban group by space id list.
     *
     * @param  string $spaceIdList
     * @param  string $kanbanIdList
     * @access public
     * @return int
     */
    public function getGroupBySpaceListTest($spaceIdList, $kanbanIdList = '')
    {
        $objects = $this->objectModel->getGroupBySpaceList($spaceIdList, $kanbanIdList);

        if(dao::isError()) return dao::getError();

        $count = 0;
        if(empty($kanbanIdList))
        {
            foreach($objects as $object) $count += count($object);
        }
        else
        {
            $count += count($objects);
        }
        return $count;
    }

    /**
     * Test get group list by region id.
     *
     * @param  int    $region
     * @access public
     * @return object
     */
    public function getGroupListTest($region)
    {
        $objects = $this->objectModel->getGroupList($region);

        if(dao::isError()) return dao::getError();

        $kanban = '';
        foreach($objects as $object) $kanban .= ',' . $object->kanban;
        return $kanban;
    }

    /**
     * Test get column by id.
     *
     * @param  int    $columnID
     * @access public
     * @return object
     */
    public function getColumnByIDTest($columnID)
    {
        $object = $this->objectModel->getColumnByID($columnID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get columns by object id.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  int    $archived
     * @param  string $deleted
     * @access public
     * @return int
     */
    public function getColumnsByObjectTest($objectType = '', $objectID = 0, $archived = 0, $deleted = '0')
    {
        $objects = $this->objectModel->getColumnsByObject($objectType, $objectID, $archived, $deleted);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get column ID by lane ID.
     *
     * @param  int    $laneID
     * @param  string $columnType
     * @access public
     * @return string
     */
    public function getColumnIDByLaneIDTest($laneID, $columnType)
    {
        $object = $this->objectModel->getColumnIDByLaneID($laneID, $columnType);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get lane by id.
     *
     * @param  int    $laneID
     * @access public
     * @return object
     */
    public function getLaneByIdTest($laneID)
    {
        $object = $this->objectModel->getLaneById($laneID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get object group list.
     *
     * @param  int    $executionID
     * @param  string $type
     * @param  string $groupBy
     * @access public
     * @return array
     */
    public function getObjectGroupTest($executionID, $type, $groupBy)
    {
        $objects = $this->objectModel->getObjectGroup($executionID, $type, $groupBy);

        if(dao::isError()) return dao::getError();

        $objects = implode(',', $objects);
        return $objects;
    }

    /**
     * Test get card by id.
     *
     * @param  int    $cardID
     * @access public
     * @return object
     */
    public function getCardByIDTest($cardID)
    {
        $object = $this->objectModel->getCardByID($cardID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get cards by object id.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $archived
     * @param  string $deleted
     * @access public
     * @return int
     */
    public function getCardsByObjectTest($objectType = '', $objectID = 0, $archived = '0', $deleted = '0')
    {
        $objects = $this->objectModel->getCardsByObject($objectType, $objectID, $archived, $deleted);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get cards to import.
     *
     * @param  int     $kanbanID
     * @param  int    $excludedID
     * @param  object $pager
     * @access public
     * @return int
     */
    public function getCards2ImportTest($kanbanID = 0, $excludedID = 0, $pager = null)
    {
        $objects = $this->objectModel->getCards2Import($kanbanID, $excludedID, $pager);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test get Kanban cards menus by execution id.
     *
     * @param  int    $executionID
     * @param  string $objecType
     * @access public
     * @return int
     */
    public function getKanbanCardMenuTest($executionID, $objecType)
    {
        global $tester;
        /* Get group objects. */
        if($objecType == 'story') $objectGroup['story'] = $tester->loadModel('story')->getExecutionStories($executionID, 0, 0, 't1.`order`_desc', 'allStory');
        if($objecType == 'bug')   $objectGroup['bug']   = $tester->loadModel('bug')->getExecutionBugs($executionID);
        if($objecType == 'task')  $objectGroup['task']  = $tester->loadModel('execution')->getKanbanTasks($executionID, "id");

        $objects = array();
        /* Get objects cards menus. */
        if($objecType == 'story') $objects = $this->objectModel->getKanbanCardMenu($executionID, $objectGroup['story'], 'story');
        if($objecType == 'bug')   $objects = $this->objectModel->getKanbanCardMenu($executionID, $objectGroup['bug'], 'bug');
        if($objecType == 'task')  $objects = $this->objectModel->getKanbanCardMenu($executionID, $objectGroup['task'], 'task');

        if(dao::isError()) return dao::getError();

        $count = 0;
        foreach($objects as $object) $count += count($object);
        return $count;
    }

    /**
     * Get toList and ccList.
     *
     * @param  object $card
     * @access public
     * @return array
     */
    public function getToAndCcListTest($card)
    {
        $objects = $this->objectModel->getToAndCcList($card);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test check if user can execute an action.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @param  string $action
     * @access public
     * @return int
     */
    public function isClickableTest($objectType, $objectID, $action)
    {
        $functionName = 'get' . $objectType . 'ById';
        $object       = $this->objectModel->$functionName($objectID);

        $result = $this->objectModel->isClickable($object, $action);

        if(dao::isError()) return dao::getError();

        return $result ? 1 : 2;
    }

    /**
     * Test import.
     *
     * @param  int $kanbanID
     * @access public
     * @return object
     */
    public function importTest($kanbanID, $import, $importObjectList)
    {
        $_POST['import']           = $import;
        $_POST['importObjectList'] = $importObjectList;

        $this->objectModel->import($kanbanID);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($kanbanID);
        $object->object = !empty($object->object) ? $object->object : 0;
        return $object;
    }
}
