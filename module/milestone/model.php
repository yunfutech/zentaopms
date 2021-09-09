<?php
class milestoneModel extends model
{
    const DAILY_LINE_ID = 162;
    /**
     * 创建项目时创建默认里程碑
     */
    public function createDefault($productID, $line) {
        if (intval($line) == $this::DAILY_LINE_ID) {
            return;
        }
        foreach($this->lang->milestone->defaultNames as $name){
            $milestone = new stdclass();
            $milestone->name = $name;
            $milestone->product = $productID;
            $milestone->isContract = 0;
            $milestone->pri = 4;
            $milestone->completed = 0;
            $milestone->createdBy = $this->app->user->account;
            $this->dao->insert(TABLE_MILESTONE)->data($milestone)->exec();
        }
    }

    /**
     * 获取项目全部里程碑
     */
    public function getAll($productID, $pager, $orderBy) {
        return $this->dao->select('*')->from(TABLE_MILESTONE)
            ->where('product')->eq($productID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 根据id获取里程碑
     */
    public function getById($milestoneID) {
        return $this->dao->select('*')->from(TABLE_MILESTONE)
            ->where('id')->eq($milestoneID)
            ->fetch();
    }

    /**
     * 创建里程碑
     */
    public function create($productID)
    {
        $milestone = fixer::input('post')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->setDefault('product', $productID)
            ->stripTags($this->config->milestone->editor->create['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $this->dao->insert(TABLE_MILESTONE)->data($milestone)->exec();
    }

    /**
     * 编辑里程碑
     */
    public function edit($milestoneID)
    {
        $milestone = fixer::input('post')
            ->stripTags($this->config->milestone->editor->create['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $this->dao->update(TABLE_MILESTONE)->data($milestone)->where('id')->eq($milestoneID)->exec();
    }

    /**
     * 删除里程碑
     */
    public function delete($milestoneID)
    {
        $this->dao->delete()->from(TABLE_MILESTONE)->where('id')->eq(intval($milestoneID))->exec();
        return !dao::isError();
    }
}