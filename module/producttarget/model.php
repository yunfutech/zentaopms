<?php
class producttargetModel extends model
{
    /**
     * 创建月目标
     */
    public function create($productID)
    {
        $requiredFields = $this->config->producttarget->create->requiredFields;
        $producttarget = fixer::input('post')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->setDefault('product', $productID)
            ->setIF(strpos($requiredFields, 'name') !== false, 'name', $this->post->name)
            ->stripTags($this->config->producttarget->editor->create['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $this->dao->insert(TABLE_PRODUCTTARGET)->data($producttarget)
            ->batchCheck($requiredFields, 'notempty')
            ->exec();
    }

    /**
     * 编辑月目标
     */
    public function edit($producttargetID)
    {
        $requiredFields = $this->config->producttarget->edit->requiredFields;
        $producttarget = fixer::input('post')
            ->stripTags($this->config->producttarget->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $this->dao->update(TABLE_PRODUCTTARGET)->data($producttarget)
            ->batchCheck($requiredFields, 'notempty')
            ->where('id')
            ->eq($producttargetID)
            ->exec();
    }

    /**
     * 删除月目标
     */
    public function delete($producttargetID)
    {
        $this->dao->update(TABLE_PRODUCTTARGET)->set('deleted')->eq(1)->where('id')->eq(intval($producttargetID))->exec();
        return !dao::isError();
    }

    public function getByProductID($productID, $pager, $sort)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTTARGET)
            ->where('product')->eq($productID)
            ->andWhere('deleted')->eq(0)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 根据id获取里程碑
     */
    public function getById($producttargetID)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTTARGET)
            ->where('id')->eq($producttargetID)
            ->fetch();
    }

    public function getReport() {}

    /**
     * 批量创建目标
     */
    public function batchCreateItem($producttargetID)
    {
        $items    = fixer::input('post')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->get();
        $data = array();
        $requiredFields = $this->config->producttargetitem->batchCreateFields;
        foreach($items->name as $i => $name)
        {
            if(empty($items->name[$i])) continue;

            $data[$i]            = new stdclass();
            $data[$i]->name      = $items->name[$i];
            $data[$i]->intro     = $items->intro[$i];
            $data[$i]->acceptance      = $items->acceptance[$i];
            $data[$i]->completion      = $items->completion[$i];
            $data[$i]->target    = $producttargetID;
            $data[$i]->createdBy    = $items->createdBy;
            $data[$i]->createdDate    = $items->createdDate;
            if(strpos($requiredFields, 'name') !== false and empty($items->name[$i])) $data[$i]->name = '';
            if(strpos($requiredFields, 'acceptance') !== false and empty($items->acceptance[$i])) $data[$i]->acceptance = '';
        }


        foreach($data as $i => $task)
        {
            $this->dao->insert(TABLE_PRODUCTTARGETITEM)->data($task)
                ->autoCheck()
                ->batchCheck($requiredFields, 'notempty')
                ->exec();

            if(dao::isError()) die(js::error(dao::getError()));
        }
    }

    /**
     * 获取所有目标
     */
    public function getItems($producttargetID, $pager, $sort)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTTARGETITEM)
            ->where('deleted')->eq(0)
            ->andWhere('target')->eq(intval($producttargetID))
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 删除目标
     */
    public function deleteItem($producttargetitemID)
    {
        $this->dao->update(TABLE_PRODUCTTARGETITEM)->set('deleted')->eq(1)->where('id')->eq(intval($producttargetitemID))->exec();
        return !dao::isError();
    }

    /**
     * 更新目标
     */
    public function editItem($producttargetitemID)
    {
        $requiredFields = $this->config->producttargetitem->edit->requiredFields;
        $producttargetitem = fixer::input('post')
            ->remove('uid')
            ->get();
        $this->dao->update(TABLE_PRODUCTTARGETITEM)->data($producttargetitem)
            ->batchCheck($requiredFields, 'notempty')
            ->where('id')
            ->eq($producttargetitemID)
            ->exec();
    }

    /**
     * 获取目标
     */
    public function getItemById($producttargetitemID)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTTARGETITEM)
            ->where('id')->eq($producttargetitemID)
            ->fetch();
    }
}
