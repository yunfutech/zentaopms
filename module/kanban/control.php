<?php
/**
 * The control file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@easycorp.ltd>
 * @package     kanban
 * @version     $Id: control.php 4460 2021-10-26 11:03:02Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.net
 */
class kanban extends control
{
    /**
     * Kanban space.
     *
     * @param  string $browseType all|my|other|closed
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function space($browseType = 'private', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title         = $this->lang->kanbanspace->common;
        $this->view->spaceList     = $this->kanban->getSpaceList($browseType, $pager, $this->cookie->showClosed);
        $this->view->unclosedSpace = $this->kanban->getCanViewObjects('kanbanspace', 'noclosed');
        $this->view->browseType    = $browseType;
        $this->view->pager         = $pager;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->userIdPairs   = $this->user->getPairs('noletter|nodeleted|showid');
        $this->view->usersAvatar   = $this->user->getAvatarPairs();

        $this->display();
    }

    /**
     * Create a space.
     *
     * @param  string $type
     * @access public
     * @return void
     */
    public function createSpace($type = 'private')
    {
        if(!empty($_POST))
        {
            $spaceID = $this->kanban->createSpace($type);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanSpace', $spaceID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        unset($this->lang->kanbanspace->featureBar['involved']);

        $this->view->users    = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->type     = $type;
        $this->view->typeList = $this->lang->kanbanspace->featureBar;

        $this->display();
    }

    /**
     * Edit a space.
     *
     * @param  int    $spaceID
     * @access public
     * @return void
     */
    public function editSpace($spaceID)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->updateSpace($spaceID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanbanSpace', $spaceID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->space = $this->kanban->getSpaceById($spaceID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');

        $this->display();
    }

    /*
     * Close a space.
     *
     * @param  int    $spaceID
     * @access public
     * @return void
     */
    public function closeSpace($spaceID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->kanban->closeSpace($spaceID);

            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->action->create('kanbanSpace', $spaceID, 'closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            die(js::reload('parent.parent'));
        }

        $this->view->space   = $this->kanban->getSpaceById($spaceID);
        $this->view->actions = $this->action->getList('kanbanSpace', $spaceID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Delete a space.
     *
     * @param  int    $spaceID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteSpace($spaceID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteSpace', "spaceID=$spaceID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANSPACE, $spaceID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a kanban.
     *
     * @param  int    $spaceID
     * @param  string $type
     * @access public
     * @return void
     */
    public function create($spaceID = 0, $type = 'private')
    {
        if(!empty($_POST))
        {
            $kanbanID = $this->kanban->create();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanban', $kanbanID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        unset($this->lang->kanbanspace->featureBar['involved']);

        $space      = $this->kanban->getSpaceById($spaceID);
        $spaceUsers = $spaceID == 0 ? ',' : trim($space->owner) . ',' . trim($space->team);
        $users      = $this->loadModel('user')->getPairs('noclosed|nodeleted', '', 0, $spaceUsers);
        $whitelist  = (isset($space->whitelist) and !empty($space->whitelist)) ? $space->whitelist : ',';

        $this->view->users      = $users;
        $this->view->whitelist  = $this->user->getPairs('noclosed|nodeleted', '', 0, $whitelist);
        $this->view->spaceID    = $spaceID;
        $this->view->spacePairs = array(0 => '') + $this->kanban->getSpacePairs($type);
        $this->view->type       = $type;
        $this->view->typeList   = $this->lang->kanbanspace->featureBar;

        $this->display();
    }

    /**
     * Edit a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function edit($kanbanID = 0)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->update($kanbanID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanban', $kanbanID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $kanban     = $this->kanban->getByID($kanbanID);
        $space      = $this->kanban->getSpaceById($kanban->space);
        $spaceUsers = trim($space->owner) . ',' . trim($space->team);
        $users      = $this->loadModel('user')->getPairs('noclosed|nodeleted', '', 0, $spaceUsers);
        $whitelist  = (isset($space->whitelist) and !empty($space->whitelist)) ? $space->whitelist : ',';

        $this->view->users      = $users;
        $this->view->whitelist  = $this->user->getPairs('noclosed|nodeleted', '', 0, $whitelist);
        $this->view->spacePairs = array(0 => '') + array($kanban->space => $space->name) + $this->kanban->getSpacePairs($space->type);
        $this->view->kanban     = $kanban;
        $this->view->type       = $space->type;

        $this->display();
    }

    /*
     * Close a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function close($kanbanID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->kanban->close($kanbanID);

            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->action->create('kanban', $kanbanID, 'closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            die(js::reload('parent.parent'));
        }

        $this->view->kanban  = $this->kanban->getByID($kanbanID);
        $this->view->actions = $this->action->getList('kanban', $kanbanID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

     /**
     * View a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function view($kanbanID)
    {
        $kanban = $this->kanban->getByID($kanbanID);
        $users  = $this->loadModel('user')->getPairs('noletter|nodeleted');

        if(!$kanban)
        {
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'code' => 404, 'message' => '404 Not found'));
            die(js::error($this->lang->notFound) . js::locate($this->createLink('kanban', 'space')));
        }

        $kanbanIdList = $this->kanban->getCanViewObjects();
        if(!$this->app->user->admin and !in_array($kanbanID, $kanbanIdList)) die(js::error($this->lang->kanban->accessDenied) . js::locate('back'));

        $space = $this->kanban->getSpaceByID($kanban->space);

        $this->kanban->setSwitcher($kanban);
        $this->kanban->setHeaderActions($kanban);

        $userList    = array();
        $avatarPairs = $this->dao->select('account, avatar')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();
        foreach($avatarPairs as $account => $avatar)
        {
            if(!$avatar) continue;
            $userList[$account]['avatar'] = $avatar;
        }

        $this->view->users    = $users;
        $this->view->title    = $this->lang->kanban->view;
        $this->view->regions  = $this->kanban->getKanbanData($kanbanID);
        $this->view->userList = $userList;
        $this->view->kanban   = $kanban;

        $this->display();
    }

    /**
     * Delete a kanban.
     *
     * @param  int    $kanbanID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function delete($kanbanID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'delete', "kanbanID=$kanbanID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBAN, $kanbanID);
            die(js::locate($this->createLink('kanban', 'space'), 'parent'));
        }
    }

    /**
     * Create a region.
     *
     * @param  int    $kanbanID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function createRegion($kanbanID, $from = 'kanban')
    {
        if(!empty($_POST))
        {
            $kanban       = $from == 'execution' ? $this->loadModel('execution')->getByID($kanbanID) : $this->kanban->getByID($kanbanID);
            $copyRegionID = (int)$_POST['region'];
            unset($_POST['region']);

            $regionID = $this->kanban->createRegion($kanban, '', $copyRegionID, $from);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $regions     = $this->kanban->getRegionPairs($kanbanID, 0, $from);
        $regionPairs = array();
        foreach($regions as $regionID => $region)
        {
            $regionPairs[$regionID] = $this->lang->kanban->copy . $region . $this->lang->kanban->styleCommon;
        }

        $this->view->regions = array('custom' => $this->lang->kanban->custom) + $regionPairs;
        $this->display();
    }

    /*
     * Edit a region
     *
     * @param  int    $regionID
     * @access public
     * @return void
     */
    public function editRegion($regionID = 0)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->updateRegion($regionID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanbanregion', $regionID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->region  = $this->kanban->getRegionByID($regionID);

        $this->display();
    }

    /**
     * Sort regions.
     *
     * @param  string $regions
     * @access public
     * @return void
     */
    public function sortRegion($regions = '')
    {
        if(empty($regions)) return;
        $regionIdList = explode(',', trim($regions, ','));

        $order = 1;
        foreach($regionIdList as $regionID)
        {
            $this->dao->update(TABLE_KANBANREGION)->set('`order`')->eq($order)->where('id')->eq($regionID)->exec();
            $order++;
        }

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * Sort group.
     *
     * @param  int    $region
     * @param  int    $groups
     * @access public
     * @return void
     */
    public function sortGroup($region, $groups)
    {
        $groups = array_filter(explode(',', trim($groups, ',')));
        if(empty($groups)) return $this->send(array('result' => 'fail', 'message' => 'No groups to sort.'));

        $this->kanban->sortGroup($region, $groups);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        return $this->send(array('result' => 'success'));
    }

    /**
     * Delete a region
     *
     * @param  int    $regionID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteRegion($regionID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanbanregion->confirmDelete, $this->createLink('kanban', 'deleteRegion', "regionID=$regionID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANREGION, $regionID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a lane for a kanban.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function createLane($kanbanID, $regionID, $from = 'kanban')
    {
        if(!empty($_POST))
        {
            $laneID = $this->kanban->createLane($kanbanID, $regionID, $lane = null);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanLane', $laneID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->lanes    = $this->kanban->getLanePairsByRegion($regionID, $from == 'kanban' ? 'all' : 'story');
        $this->view->from     = $from;
        $this->view->regionID = $regionID;

        if($from == 'kanban') $this->display();
        if($from == 'execution') $this->display('kanban', 'createexeclane');
    }

    /**
     * Sort lanes.
     *
     * @param  int    $regionID
     * @param  string $lanes
     * @access public
     * @return array
     */
    public function sortLane($regionID, $lanes = '')
    {
        if(empty($lanes)) return;
        $lanes = explode(',', trim($lanes, ','));

        $order = 1;
        foreach($lanes as $laneID)
        {
            $this->dao->update(TABLE_KANBANLANE)->set('`order`')->eq($order)->where('id')->eq($laneID)->andWhere('region')->eq($regionID)->exec();
            $order++;
        }
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * Sort columns.
     *
     * @param  int    $regionID
     * @param  int    $kanbanID
     * @param  string $columns
     * @access public
     * @return array|string
     */
    public function sortColumn($regionID, $kanbanID, $columns = '')
    {
        if(empty($columns)) return;
        $columns =  explode(',', trim($columns, ','));

        $order = 1;
        foreach($columns as $columnID) $this->dao->update(TABLE_KANBANCOLUMN)->set('`order`')->eq($order++)->where('id')->eq($columnID)->andWhere('region')->eq($regionID)->exec();

        $kanbanGroup = $this->kanban->getKanbanData($kanbanID);

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        return print(json_encode($kanbanGroup));
    }

    /**
     * Set lane height.
     *
     * @param  int    $kanbanID
     * @param  string $from     kanban|execution
     * @access public
     * @return void
     */
    public function setLaneHeight($kanbanID, $from = 'kanban')
    {
        if(!empty($_POST))
        {
            $this->kanban->setLaneHeight($kanbanID, $from);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $kanban = $from == 'execution' ? $this->loadModel('execution')->getByID($kanbanID) : $this->kanban->getByID($kanbanID);

        $this->view->heightType   = $kanban->displayCards > 2 ? 'custom' : 'auto';
        $this->view->displayCards = $kanban->displayCards ? $kanban->displayCards : '';

        $this->display();
    }

    /**
     * Set column width.
     *
     * @param  int    $kanbanID
     * @param  string $from     kanban|execution
     * @access public
     * @return void
     */
    public function setColumnWidth($kanbanID, $from = 'kanban')
    {
        if(!empty($_POST))
        {
            $this->kanban->setColumnWidth($kanbanID, $from);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $kanban = $from == 'execution' ? $this->loadModel('execution')->getByID($kanbanID) : $this->kanban->getByID($kanbanID);

        $this->view->kanban = $kanban;

        $this->display();
    }

    /**
     * Delete a lane.
     *
     * @param  int    $laneID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function deleteLane($laneID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            $laneType   = $this->kanban->getLaneById($laneID)->type;
            $confirmTip = in_array($laneType, array('story', 'task', 'bug')) ? sprintf($this->lang->kanbanlane->confirmDeleteTip, $this->lang->{$laneType}->common) : $this->lang->kanbanlane->confirmDelete;

            die(js::confirm($confirmTip, $this->createLink('kanban', 'deleteLane', "laneID=$laneID&confirm=yes"), ''));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANLANE, $laneID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a column for a kanban.
     *
     * @param  int    $columnID
     * @param  string $position left|right
     * @access public
     * @return void
     */
    public function createColumn($columnID, $position = 'left')
    {
        $column = $this->kanban->getColumnByID($columnID);

        if($_POST)
        {
            $order    = $position == 'left' ? $column->order : $column->order + 1;
            $columnID = $this->kanban->createColumn($column->region, null, $order, $column->parent);
            if(dao::isError()) $this->send(array('message' => dao::getError(), 'result' => 'fail'));

            $this->loadModel('action')->create('kanbanColumn', $columnID, 'Created');
            $this->send(array('message' => $this->lang->saveSuccess, 'result' => 'success', 'locate' => 'parent'));
        }

        $this->view->title    = $this->lang->kanban->createColumn;
        $this->view->column   = $column;
        $this->view->position = $position;
        $this->display();
    }

    /**
     * Split column.
     *
     * @access public
     * @return void
     */
    public function splitColumn($columnID)
    {
        if(!empty($_POST))
        {
            $this->kanban->splitColumn($columnID);
            if(dao::isError()) $this->send(array('message' => dao::getError(), 'result' => 'fail'));
            $this->send(array('message' => $this->lang->saveSuccess, 'result' => 'success', 'locate' => 'parent'));
        }

        $this->display();
    }

    /**
     * Archive a column.
     *
     * @param  int    $columnID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function archiveColumn($columnID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanbancolumn->confirmArchive, $this->createLink('kanban', 'archiveColumn', "columnID=$columnID&confirm=yes")));
        }
        else
        {
            $column = $this->kanban->getColumnById($columnID);
            if($column->parent)
            {
                $children = $this->dao->select('count(*) as count')->from(TABLE_KANBANCOLUMN)
                    ->where('parent')->eq($column->parent)
                    ->andWhere('id')->ne($column->id)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('archived')->eq('0')
                    ->fetch('count');

                if(!$children) $this->dao->update(TABLE_KANBANCOLUMN)->set('parent')->eq(0)->where('id')->eq($column->parent)->exec();
            }

            $this->kanban->archiveColumn($columnID);
            if(dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('action')->create('kanbancolumn', $columnID, 'archived');

            if(isonlybody()) die(js::reload('parent.parent'));
            die(js::reload('parent'));
        }
    }

    /**
     * Restore a column.
     *
     * @param  int    $columnID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function restoreColumn($columnID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanbancolumn->confirmRestore, $this->createLink('kanban', 'restoreColumn', "columnID=$columnID&confirm=yes"), ''));
        }
        else
        {
            $this->kanban->restoreColumn($columnID);
            if(dao::isError()) die(js::error(dao::getError()));

            $this->loadModel('action')->create('kanbancolumn', $columnID, 'restore');
            die(js::reload('parent'));
        }
    }

    /**
     * View archived columns.
     *
     * @param  int    $regionID
     * @access public
     * @return void
     */
    public function viewArchivedColumn($regionID)
    {
        $columns     = $this->kanban->getColumnsByObject('region', $regionID, '');
        $columnsData = array();
        foreach($columns as $column)
        {
            if($column->archived == 0) continue;
            if($column->parent > 0 and isset($columns[$column->parent]))
            {
                if(empty($columnsData[$column->parent])) $columnsData[$column->parent] = $columns[$column->parent];
                $columnsData[$column->parent]->child[$column->id] = $column;
            }
            elseif($column->parent <= 0)
            {
                if(empty($columnsData[$column->id])) $columnsData[$column->id] = $column;
            }
        }

        $this->view->columns = $columnsData;

        $this->display();
    }

    /**
     * Delete a column.
     *
     * @param  int    $columnID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function deleteColumn($columnID, $confirm = 'no')
    {
        $column = $this->kanban->getColumnById($columnID);
        if($confirm == 'no')
        {
            $confirmLang = $column->parent > 0 ? $this->lang->kanbancolumn->confirmDeleteChild : $this->lang->kanbancolumn->confirmDelete;
            die(js::confirm($confirmLang, $this->createLink('kanban', 'deleteColumn', "columnID=$columnID&confirm=yes")));
        }
        else
        {
            if($column->parent > 0) $this->kanban->processCards($column);

            $this->dao->delete()->from(TABLE_KANBANCOLUMN)->where('id')->eq($columnID)->exec();

            die(js::reload('parent'));
        }
    }

    /**
     * Create a card.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $columnID
     * @access public
     * @return void
     */
    public function createCard($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0)
    {
        if($_POST)
        {
            $cardID = $this->kanban->createCard($kanbanID, $regionID, $groupID, $columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('kanbancard', $cardID, 'created');

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $kanban      = $this->kanban->getById($kanbanID);
        $kanbanUsers = $kanbanID == 0 ? ',' : trim($kanban->owner) . ',' . trim($kanban->team);
        $users       = $this->loadModel('user')->getPairs('noclosed|nodeleted', '', 0, $kanbanUsers);

        $this->view->users     = $users;
        $this->view->lanePairs = $this->kanban->getLanePairsByGroup($groupID);

        $this->display();
    }

    /**
     * Batch create cards.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $laneID
     * @param  int    $columnID
     * @access public
     * @return void
     */
    public function batchCreateCard($kanbanID = 0, $regionID = 0, $groupID = 0, $laneID = 0, $columnID = 0)
    {
        $kanban   = $this->kanban->getById($kanbanID);
        $backLink = $this->createLink('kanban', 'view', "kanbanID=$kanbanID");
        $this->kanban->setSwitcher($kanban);

        if($_POST)
        {
            $this->kanban->batchCreateCard($kanbanID, $regionID, $groupID, $columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $backLink));
        }

        $kanbanUsers = $kanbanID == 0 ? ',' : trim($kanban->owner) . ',' . trim($kanban->team);
        $users       = $this->loadModel('user')->getPairs('noclosed|nodeleted', '', 0, $kanbanUsers);

        $lanePairs = $this->kanban->getLanePairsByGroup($groupID);

        $lanePairs['ditto'] = $this->lang->kanbancard->ditto;

        $this->view->title     = $this->lang->kanban->batchCreateCard;
        $this->view->users     = $users;
        $this->view->lanePairs = $lanePairs;

        $this->display();
    }

    /**
     * Edit a card.
     *
     * @param  int    $cardID
     * @access public
     * @return void
     */
    public function editCard($cardID)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->updateCard($cardID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanbanCard', $cardID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $card        = $this->kanban->getCardByID($cardID);
        $kanban      = $this->kanban->getById($card->kanban);
        $kanbanUsers = $card->kanban == 0 ? ',' : trim($kanban->owner) . ',' . trim($kanban->team);
        $users       = $this->loadModel('user')->getPairs('noclosed|nodeleted', '', 0, $kanbanUsers);

        $this->view->card     = $card;
        $this->view->actions  = $this->action->getList('kanbancard', $cardID);
        $this->view->users    = $users;

        $this->display();
    }

    /**
     * Finish a card.
     *
     * @param  int    $cardID
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function finishCard($cardID, $kanbanID)
    {
        $this->loadModel('action');

        $oldCard = $this->kanban->getCardByID($cardID);
        $this->dao->update(TABLE_KANBANCARD)->set('status')->eq('done')->where('id')->eq($cardID)->exec();
        $card = $this->kanban->getCardByID($cardID);

        $changes = common::createChanges($oldCard, $card);

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $actionID = $this->action->create('kanbanCard', $cardID, 'finished');
        $this->action->logHistory($actionID, $changes);

        if(isonlybody()) return print(js::reload('parent.parent'));

        $kanbanGroup = $this->kanban->getKanbanData($kanbanID);
        return print(json_encode($kanbanGroup));
    }

    /**
     * Activate a card.
     *
     * @param  int    $cardID
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function activateCard($cardID, $kanbanID)
    {
        $this->loadModel('action');

        $oldCard = $this->kanban->getCardByID($cardID);
        $this->dao->update(TABLE_KANBANCARD)->set('status')->eq('doing')->where('id')->eq($cardID)->exec();
        $card = $this->kanban->getCardByID($cardID);

        $changes = common::createChanges($oldCard, $card);

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $actionID = $this->action->create('kanbanCard', $cardID, 'activated');
        $this->action->logHistory($actionID, $changes);

        if(isonlybody()) return print(js::reload('parent.parent'));

        $kanbanGroup = $this->kanban->getKanbanData($kanbanID);
        return print(json_encode($kanbanGroup));
    }

    /**
     * View a card.
     *
     * @param  int    $cardID
     * @access public
     * @return void
     */
    public function viewCard($cardID)
    {
        $this->loadModel('action');

        $card   = $this->kanban->getCardByID($cardID);
        $kanban = $this->kanban->getByID($card->kanban);
        $space  = $this->kanban->getSpaceById($kanban->space);

        $this->view->card        = $card;
        $this->view->actions     = $this->action->getList('kanbancard', $cardID);
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->space       = $space;
        $this->view->kanban      = $kanban;
        $this->view->usersAvatar = $this->user->getAvatarPairs();

        $this->display();
    }

    /**
     * Move a card.
     *
     * @param  int    $cardID
     * @param  int    $fromColID
     * @param  int    $toColID
     * @param  int    $fromLaneID
     * @param  int    $toLaneID
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function moveCard($cardID, $fromColID, $toColID, $fromLaneID, $toLaneID, $kanbanID = 0)
    {
        $this->kanban->moveCard($cardID, $fromColID, $toColID, $fromLaneID, $toLaneID, $kanbanID);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $kanbanGroup = $this->kanban->getKanbanData($kanbanID);
        die(json_encode($kanbanGroup));
    }

    /**
     * Import card.
     *
     * @param  int $kanbanID
     * @param  int $regionID
     * @param  int $groupID
     * @param  int $columnID
     * @param  int $selectedKanbanID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importCard($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0, $selectedKanbanID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $cards2Imported = $this->kanban->getCards2Import($selectedKanbanID, $kanbanID, $pager);

        if($_POST)
        {
            $importedIDList = $this->kanban->importCard($kanbanID, $regionID, $groupID, $columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            foreach($importedIDList as $cardID)
            {
                $this->loadModel('action')->create('kanbancard', $cardID, 'importedcard', '', $cards2Imported[$cardID]->kanban);
            }

            return print(js::locate($this->createLink('kanban', 'view', "kanbanID=$kanbanID"), 'parent.parent'));
        }

        /* Find Kanban other than this kanban. */
        $kanbanPairs = $this->kanban->getKanbanPairs();
        unset($kanbanPairs[$kanbanID]);

        $this->view->cards2Imported   = $cards2Imported;
        $this->view->kanbanPairs      = array($this->lang->kanban->allKanban) + $kanbanPairs;
        $this->view->lanePairs        = $this->kanban->getLanePairsByGroup($groupID);
        $this->view->users            = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->pager            = $pager;
        $this->view->selectedKanbanID = $selectedKanbanID;
        $this->view->kanbanID         = $kanbanID;
        $this->view->regionID         = $regionID;
        $this->view->groupID          = $groupID;
        $this->view->columnID         = $columnID;

        $this->display();
    }

    /**
     * Import plan.
     *
     * @param  int $kanbanID
     * @param  int $regionID
     * @param  int $groupID
     * @param  int $columnID
     * @param  int $selectedProductID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importPlan($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0, $selectedProductID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if($_POST)
        {
            $importedIDList = $this->kanban->importObject($kanbanID, $regionID, $groupID, $columnID, 'productplan');
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            foreach($importedIDList as $cardID => $planID)
            {
                $this->loadModel('action')->create('kanbancard', $cardID, 'importedProductplan', '', $planID);
            }

            return print(js::locate($this->createLink('kanban', 'view', "kanbanID=$kanbanID"), 'parent.parent'));
        }

        $this->loadModel('product');
        $this->loadModel('productplan');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $productPairs      = $this->product->getPairs();
        $selectedProductID = empty($selectedProductID) ? key($productPairs) : $selectedProductID;

        $this->view->products          = $productPairs;
        $this->view->selectedProductID = $selectedProductID;
        $this->view->lanePairs         = $this->kanban->getLanePairsByGroup($groupID);
        $this->view->plans2Imported    = $this->productplan->getList($selectedProductID, 0, 'all', $pager, 'begin_desc', 'skipparent');
        $this->view->pager             = $pager;
        $this->view->kanbanID          = $kanbanID;
        $this->view->regionID          = $regionID;
        $this->view->groupID           = $groupID;
        $this->view->columnID          = $columnID;

        $this->display();
    }

    /**
     * Import release.
     *
     * @param  int $kanbanID
     * @param  int $regionID
     * @param  int $groupID
     * @param  int $columnID
     * @param  int $selectedProductID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importRelease($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0, $selectedProductID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if($_POST)
        {
            $importedIDList = $this->kanban->importObject($kanbanID, $regionID, $groupID, $columnID, 'release');
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            foreach($importedIDList as $cardID => $releaseID)
            {
                $this->loadModel('action')->create('kanbancard', $cardID, 'importedRelease', '', $releaseID);
            }

            return print(js::locate($this->createLink('kanban', 'view', "kanbanID=$kanbanID"), 'parent.parent'));
        }

        $this->loadModel('product');
        $this->loadModel('release');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->products          = array($this->lang->kanban->allProducts) + $this->product->getPairs();
        $this->view->selectedProductID = $selectedProductID;
        $this->view->lanePairs         = $this->kanban->getLanePairsByGroup($groupID);
        $this->view->releases2Imported = $this->release->getList($selectedProductID, 'all', 'all', 't1.date_desc', $pager);
        $this->view->pager             = $pager;
        $this->view->kanbanID          = $kanbanID;
        $this->view->regionID          = $regionID;
        $this->view->groupID           = $groupID;
        $this->view->columnID          = $columnID;

        $this->display();
    }

    /**
     * Import build.
     *
     * @param  int $kanbanID
     * @param  int $regionID
     * @param  int $groupID
     * @param  int $columnID
     * @param  int $selectedProjectID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importBuild($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0, $selectedProjectID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if($_POST)
        {
            $importedIDList = $this->kanban->importObject($kanbanID, $regionID, $groupID, $columnID, 'build');
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            foreach($importedIDList as $cardID => $buildID)
            {
                $this->loadModel('action')->create('kanbancard', $cardID, 'importedBuild', '', $buildID);
            }

            return print(js::locate($this->createLink('kanban', 'view', "kanbanID=$kanbanID"), 'parent.parent'));
        }

        $this->loadModel('build');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $builds2Imported = array();
        $projects        = array($this->lang->kanban->allProjects);
        if($this->config->systemMode == 'classic')
        {
            $projects        += $this->loadModel('execution')->getPairs();
            $builds2Imported  = $this->build->getExecutionBuilds($selectedProjectID, '', '', 't1.date_desc,t1.id_desc', $pager);
        }
        else
        {
            $projects        += $this->loadModel('project')->getPairsByProgram('', 'all', false, 'order_asc', 'kanban');
            $builds2Imported  = $this->build->getProjectBuilds($selectedProjectID, 'all', 0, 't1.date_desc,t1.id_desc', $pager);
        }

        $this->view->projects          = $projects;
        $this->view->selectedProjectID = $selectedProjectID;
        $this->view->builds2Imported   = $builds2Imported;
        $this->view->lanePairs         = $this->kanban->getLanePairsByGroup($groupID);
        $this->view->users             = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->pager             = $pager;
        $this->view->kanbanID          = $kanbanID;
        $this->view->regionID          = $regionID;
        $this->view->groupID           = $groupID;
        $this->view->columnID          = $columnID;

        $this->display();
    }

    /**
     * Import execution.
     *
     * @param  int $kanbanID
     * @param  int $regionID
     * @param  int $groupID
     * @param  int $columnID
     * @param  int $selectedProjectID
     * @param  int $recTotal
     * @param  int $recPerPage
     * @param  int $pageID
     * @access public
     * @return void
     */
    public function importExecution($kanbanID = 0, $regionID = 0, $groupID = 0, $columnID = 0, $selectedProjectID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        if($_POST)
        {
            $importedIDList = $this->kanban->importObject($kanbanID, $regionID, $groupID, $columnID, 'execution');
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            foreach($importedIDList as $cardID => $executionID)
            {
                $this->loadModel('action')->create('kanbancard', $cardID, 'importedExecution', '', $executionID);
            }

            return print(js::locate($this->createLink('kanban', 'view', "kanbanID=$kanbanID"), 'parent.parent'));
        }

        $this->loadModel('project');
        $this->loadModel('execution');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->projects            = array($this->lang->kanban->allProjects) + $this->project->getPairsByProgram();
        $this->view->selectedProjectID   = $selectedProjectID;
        $this->view->lanePairs           = $this->kanban->getLanePairsByGroup($groupID);
        $this->view->executions2Imported = $this->project->getStats($selectedProjectID, 'undone', 0, 0, 30, 'id_asc', $pager);
        $this->view->users               = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->pager               = $pager;
        $this->view->kanbanID            = $kanbanID;
        $this->view->regionID            = $regionID;
        $this->view->groupID             = $groupID;
        $this->view->columnID            = $columnID;

        $this->display();
    }

    /**
     * Set a card's color.
     *
     * @param  int   $cardID
     * @param  int   $color
     * @param  int   $kanbanID
     * @access public
     * @return string
     */
    public function setCardColor($cardID, $color, $kanbanID)
    {
        $this->kanban->updateCardColor($cardID, $color);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $kanbanGroup = $this->kanban->getKanbanData($kanbanID);
        die(json_encode($kanbanGroup));
    }

    /**
     * Sort cards.
     *
     * @param  int    $kanbanID
     * @param  int    $laneID
     * @param  int    $columnID
     * @param  string $cards
     * @access public
     * @return void
     */
    public function sortCard($kanbanID, $laneID, $columnID, $cards = '')
    {
        if(empty($cards)) return;

        $this->dao->update(TABLE_KANBANCELL)->set('cards')->eq(",$cards,")->where('kanban')->eq($kanbanID)->andWhere('lane')->eq($laneID)->andWhere('`column`')->eq($columnID)->exec();

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * Archive a card.
     *
     * @param  int    $cardID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function archiveCard($cardID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanbancard->confirmArchive, $this->createLink('kanban', 'archiveCard', "cardID=$cardID&confirm=yes")));
        }
        else
        {
            $changes = $this->kanban->archiveCard($cardID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('kanbancard', $cardID, 'archived');
            $this->action->logHistory($actionID, $changes);

            if(isonlybody()) die(js::reload('parent.parent'));
            die(js::reload('parent'));
        }
    }

    /**
     * View archived cards.
     *
     * @param  int    $regionID
     * @access public
     * @return void
     */
    public function viewArchivedCard($regionID)
    {
        $this->view->cards       = $this->kanban->getCardsByObject('region', $regionID, 1);
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|nodeleted');
        $this->view->userIdPairs = $this->user->getPairs('noletter|nodeleted|showid');
        $this->view->usersAvatar = $this->user->getAvatarPairs();

        $this->display();
    }

    /**
     * Restore a card.
     *
     * @param  int    $cardID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function restoreCard($cardID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            $card   = $this->kanban->getCardByID($cardID);
            $column = $this->dao->select('*')->from(TABLE_KANBANCOLUMN)->where('id')->eq($card->column)->fetch();
            if($column->archived or $column->deleted) die(js::alert(sprintf($this->lang->kanbancard->confirmRestoreTip, $column->name)));

            die(js::confirm(sprintf($this->lang->kanbancard->confirmRestore, $column->name), $this->createLink('kanban', 'restoreCard', "cardID=$cardID&confirm=yes"), ''));
        }
        else
        {
            $this->kanban->restoreCard($cardID);

            $changes = $this->kanban->restoreCard($cardID);
            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->loadModel('action')->create('kanbancard', $cardID, 'restore');
            $this->action->logHistory($actionID, $changes);

            die(js::reload('parent'));
        }
    }

	/**
	 * Delete a card.
	 *
	 * @param  int    $cardID
	 * @param  string $confirm no|yes
	 * @access public
	 * @return void
	 */
    public function deleteCard($cardID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanbancard->confirmDelete, $this->createLink('kanban', 'deleteCard', "cardID=$cardID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANCARD, $cardID);

            if(isonlybody()) die(js::reload('parent.parent'));
            die(js::reload('parent'));
        }
    }

    /**
     * Set WIP.
     *
     * @param  int    $columnID
     * @param  int    $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setWIP($columnID, $executionID = 0, $from = 'kanban')
    {
        $column = $this->kanban->getColumnById($columnID);
        if($_POST)
        {
            $this->kanban->setWIP($columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbancolumn', $columnID, 'Edited', '', $executionID);

            if($from == 'RDKanban')
            {
                if(dao::isError()) return $this->sendError(dao::getError());
                $regionID   = $column->region;
                $kanbanData = $this->loadModel('kanban')->getRDKanban($executionID, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                $kanbanData = json_encode($kanbanData);

                return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.updateKanban($kanbanData, $regionID)"));
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->app->loadLang('story');

        if(!$column) die(js::error($this->lang->notFound) . js::locate($this->createLink('execution', 'kanban', "executionID=$executionID")));

        $title  = isset($column->parentName) ? $column->parentName . '/' . $column->name : $column->name;

        $this->view->title  = $title . $this->lang->colon . $this->lang->kanban->setWIP . '(' . $this->lang->kanban->WIP . ')';
        $this->view->column = $column;
        $this->view->from   = $from;

        if($from != 'kanban') $this->view->status = zget($this->config->kanban->{$column->laneType . 'ColumnStatusList'}, $column->type);
        $this->display();
    }

    /**
     * Set lane info.
     *
     * @param  int    $laneID
     * @param  int    $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setLane($laneID, $executionID = 0, $from = 'kanban')
    {
        if($_POST)
        {
            $this->kanban->setLane($laneID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanlane', $laneID, 'Edited', '', $executionID);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $lane = $this->kanban->getLaneById($laneID);
        if(!$lane) die(js::error($this->lang->notFound) . js::locate($this->createLink('execution', 'kanban', "executionID=$executionID")));

        $this->view->title = $from == 'kanban' ? $this->lang->edit . '“' . $lane->name . '”' . $this->lang->kanbanlane->common : zget($this->lang->kanban->laneTypeList, $lane->type) . $this->lang->colon . $this->lang->kanban->setLane;
        $this->view->lane  = $lane;
        $this->view->from  = $from;

        $this->display();
    }

    /**
     * Set lane column info.
     *
     * @param  int $columnID
     * @param  int $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setColumn($columnID, $executionID = 0, $from = 'kanban')
    {
        $column = $this->kanban->getColumnByID($columnID);

        if($_POST)
        {
            $changes = $this->kanban->updateLaneColumn($columnID, $column);
            if(dao::isError()) return $this->sendError(dao::getError());
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('kanbancolumn', $columnID, 'Edited', '', $executionID);
                $this->action->logHistory($actionID, $changes);
            }

            if($from == 'RDKanban')
            {
                if(dao::isError()) return $this->sendError(dao::getError());
                $regionID   = $column->region;
                $kanbanData = $this->loadModel('kanban')->getRDKanban($executionID, $this->session->execLaneType ? $this->session->execLaneType : 'all', 'id_desc', $regionID);
                $kanbanData = json_encode($kanbanData);

                return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.updateKanban($kanbanData, $regionID)"));
            }

            return $this->sendSuccess(array('locate' => 'parent'));
        }

        $this->view->column = $column;
        $this->view->title  = $column->name . $this->lang->colon . $this->lang->kanban->setColumn;
        $this->display();
    }

    /**
     * Setup done function.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function performable($kanbanID)
    {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_KANBAN)->set('performable')->eq($_POST['performable'])->exec();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->kanban = $this->kanban->getByID($kanbanID);

        $this->display();
    }

    /**
     * Set archived.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function enableArchived($kanbanID)
    {
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_KANBAN)->set('archived')->eq($_POST['archived'])->exec();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->kanban = $this->kanban->getByID($kanbanID);

        $this->display();
    }

    /**
     * AJAX: Update the cards sorting of the lane column.
     *
     * @param  string $laneType story|bug|task
     * @param  int    $columnID
     * @param  string $orderBy id_desc|id_asc|pri_desc|pri_asc|lastEditedDate_desc|lastEditedDate_asc|deadline_desc|deadline_asc|assignedTo_asc
     * @access public
     * @return void
     */
    public function ajaxCardsSort($laneType, $columnID, $orderBy = 'id_desc')
    {
        $oldCards = array();
        $column   = $this->dao->select('parent,cards')->from(TABLE_KANBANCOLUMN)->where('id')->eq($columnID)->fetch();

        /* Get the cards of the kanban column. */
        if($column->parent == -1)
        {
            $childColumns = $this->dao->select('id,cards')->from(TABLE_KANBANCOLUMN)->where('parent')->eq($columnID)->fetchAll();
            foreach($childColumns as $childColumn)
            {
                $oldCards[$childColumn->id] = $childColumn->cards;
            }
        }
        else
        {
            $oldCards[$columnID] = $column->cards;
        }

        /* Update Kanban column card order. */
        $table = $this->config->objectTables[$laneType];
        foreach($oldCards as $colID => $cards)
        {
            if(empty($cards)) continue;
            $objects = $this->dao->select('id')->from($table)
                ->where('id')->in($cards)
                ->orderBy($orderBy)
                ->fetchPairs('id');

            $objectIdList = ',' . implode(',', $objects) . ',';
            $this->dao->update(TABLE_KANBANCOLUMN)->set('cards')->eq($objectIdList)->where('id')->eq($colID)->exec();
        }
        echo true;
    }

    /**
     * Ajax move card.
     *
     * @param  int    $cardID
     * @param  int    $fromColID
     * @param  int    $toColID
     * @param  int    $fromLaneID
     * @param  int    $toLaneID
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $groupBy
     * @param  int    $regionID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function ajaxMoveCard($cardID = 0, $fromColID = 0, $toColID = 0, $fromLaneID = 0, $toLaneID = 0, $executionID = 0, $browseType = 'all', $groupBy = '', $regionID = 0, $orderBy = '')
    {
        $fromCell = $this->dao->select('id, cards')->from(TABLE_KANBANCELL)
            ->where('kanban')->eq($executionID)
            ->andWhere('lane')->eq($fromLaneID)
            ->andWhere('`column`')->eq($fromColID)
            ->fetch();

        $toCell = $this->dao->select('id, cards')->from(TABLE_KANBANCELL)
            ->where('kanban')->eq($executionID)
            ->andWhere('lane')->eq($toLaneID)
            ->andWhere('`column`')->eq($toColID)
            ->fetch();

        $fromCards = str_replace(",$cardID,", ',', $fromCell->cards);
        $fromCards = $fromCards == ',' ? '' : $fromCards;
        $toCards   = ",$cardID," . ltrim($toCell->cards, ',');

        $this->dao->update(TABLE_KANBANCELL)->set('cards')->eq($fromCards)
            ->where('kanban')->eq($executionID)
            ->andWhere('lane')->eq($fromLaneID)
            ->andWhere('`column`')->eq($fromColID)
            ->exec();

        $this->dao->update(TABLE_KANBANCELL)->set('cards')->eq($toCards)
            ->where('kanban')->eq($executionID)
            ->andWhere('lane')->eq($toLaneID)
            ->andWhere('`column`')->eq($toColID)
            ->exec();

        $kanbanGroup = $regionID == 0 ? $this->kanban->getExecutionKanban($executionID, $browseType, $groupBy) : $this->kanban->getRDKanban($executionID, $browseType, $orderBy, $regionID, $groupBy);
        die(json_encode($kanbanGroup));
    }

    /**
     * Change the order through the lane move up and down.
     *
     * @param  int     $executionID
     * @param  string  $currentType
     * @param  string  $targetType
     * @access public
     * @return void
     */
    public function laneMove($executionID, $currentType, $targetType)
    {
        if(empty($targetType)) return false;

        $this->kanban->updateLaneOrder($executionID, $currentType, $targetType);

        if(!dao::isError())
        {
            $laneID = $this->dao->select('id')->from(TABLE_KANBANLANE)->where('execution')->eq($executionID)->andWhere('type')->eq($currentType)->fetch('id');
            $this->loadModel('action')->create('kanbanlane', $laneID, 'Moved');
        }

        die(js::locate($this->createLink('execution', 'kanban', 'executionID=' . $executionID . '&type=all'), 'parent'));
    }

    /**
     * Ajax get contact users.
     *
     * @param  int    $contactListID
     * @access public
     * @return string
     */
    public function ajaxGetContactUsers($contactListID)
    {
        $this->loadModel('user');
        $list = $contactListID ? $this->user->getContactListByID($contactListID) : '';

        $users = $this->user->getPairs('devfirst|nodeleted|noclosed', $list ? $list->userList : '', $this->config->maxCount);

        if(!$contactListID) return print(html::select('team[]', $users, '', "class='form-control chosen' multiple"));

        return print(html::select('team[]', $users, $list->userList, "class='form-control chosen' multiple"));
    }

    /**
     * Ajax get kanban menu.
     *
     * @param  int    $kanbanID
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function ajaxGetKanbanMenu($kanbanID, $moduleName, $methodName)
    {
        $kanbanIdList = $this->kanban->getCanViewObjects();
        $spacePairs   = $this->kanban->getSpacePairs('showClosed');

        $this->view->kanbanList = $this->dao->select('*')->from(TABLE_KANBAN)
            ->where('deleted')->eq('0')
            ->andWhere('id')->in($kanbanIdList)
            ->andWhere('space')->in(array_keys($spacePairs))
            ->fetchGroup('space');

        $this->view->kanbanID  = $kanbanID;
        $this->view->spaceList = $spacePairs;
        $this->view->module    = $moduleName;
        $this->view->method    = $methodName;
        $this->display();
    }

    /**
     * Ajax get lanes by region id.
     *
     * @param  int    $regionID
     * @param  string $type all|story|task|bug
     * @access public
     * @return string
     */
    public function ajaxGetLanes($regionID, $type = 'all')
    {
        $lanes = $this->kanban->getLanePairsByRegion($regionID, $type);

        if(empty($lanes)) return;
        return print(html::select('otherLane', $lanes, '', "class='form-control'"));
    }

    /**
     * Import.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function import($kanbanID)
    {
        if(!empty($_POST))
        {
            $this->kanban->import($kanbanID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $kanban = $this->kanban->getByID($kanbanID);

        $this->view->enableImport  = empty($kanban->object) ? 'off' : 'on';
        $this->view->importObjects = empty($kanban->object) ? array() : explode(',', $kanban->object);

        $this->display();
    }
}
