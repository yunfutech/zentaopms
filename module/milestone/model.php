<?php
class milestoneModel extends model
{
    const DAILY_LINE_ID = 162;
    /**
     * 创建项目时创建默认里程碑
     */
    public function createDefault($productID, $line)
    {
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
    public function getByProductID($productID, $pager, $sort)
    {
        return $this->dao->select('*')->from(TABLE_MILESTONE)
            ->where('product')->eq($productID)
            ->andWhere('deleted')->eq(0)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getAll($pager, $sort, $begin, $productID=0, $line=0, $isContract='', $completed='')
    {
        return $this->dao->select('t1.*, CONVERT(t2.name USING gbk) as productName, CONVERT(t3.name USING gbk) as productLine')->from(TABLE_MILESTONE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_MODULE)->alias('t3')->on('t2.line = t3.id')
            ->where('date')->ge($begin)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($productID > 0)
            ->andWhere('t1.product')->eq($productID)
            ->fi()
            ->beginIF($line > 0)
            ->andWhere('t2.line')->eq($line)
            ->fi()
            ->beginIF($isContract != '')
            ->andWhere('t1.isContract')->eq($isContract)
            ->fi()
            ->beginIF($completed != '')
            ->andWhere('t1.completed')->eq($completed)
            ->fi()
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 根据id获取里程碑
     */
    public function getById($milestoneID)
    {
        return $this->dao->select('*')->from(TABLE_MILESTONE)
            ->where('id')->eq($milestoneID)
            ->fetch();
    }

    /**
     * 创建里程碑
     */
    public function create($productID)
    {
        $requiredFields = $this->config->milestone->create->requiredFields;
        $milestone = fixer::input('post')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->setDefault('product', $productID)
            ->setIF(strpos($requiredFields, 'name') !== false, 'name', $this->post->name)
            ->setIF(strpos($requiredFields, 'date') !== false, 'date', $this->post->date)
            ->setIF($this->post->isContract == '1', 'pri', '0')
            ->stripTags($this->config->milestone->editor->create['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $this->dao->insert(TABLE_MILESTONE)->data($milestone)
            ->batchCheck($requiredFields, 'notempty')
            ->exec();
    }

    /**
     * 编辑里程碑
     */
    public function edit($milestoneID)
    {
        $requiredFields = $this->config->milestone->edit->requiredFields;
        $milestone = fixer::input('post')
            ->setIF(strpos($requiredFields, 'name') !== false, 'name', $this->post->name)
            ->setIF(strpos($requiredFields, 'date') !== false, 'date', $this->post->date)
            ->stripTags($this->config->milestone->editor->create['id'], $this->config->allowedTags)
            ->setIF($this->post->isContract == '1', 'pri', '0')
            ->remove('uid')
            ->get();
        $this->dao->update(TABLE_MILESTONE)->data($milestone)
            ->batchCheck($requiredFields, 'notempty')
            ->where('id')
            ->eq($milestoneID)
            ->exec();
    }

    /**
     * 删除里程碑
     */
    public function delete($milestoneID)
    {
        $this->dao->update(TABLE_MILESTONE)->set('deleted')->eq(1)->where('id')->eq(intval($milestoneID))->exec();
        return !dao::isError();
    }

    public function deleteAll($productID)
    {
        $this->dao->update(TABLE_MILESTONE)->set('deleted')->eq(1)->where('product')->eq(intval($productID))->exec();
        return !dao::isError();
    }
}