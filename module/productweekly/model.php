<?php

class productweeklyModel extends model
{
    public function getWeeklyByProduct($productID, $pager, $sort)
    {
        return $this->dao->select('t1.*, t2.realname')
            ->from(TABLE_PRODUCTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account= t2.account')
            ->where('t1.product')->eq($productID)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getWeekly($pager, $sort, $week=0, $product=0)
    {
        $year = date('Y');
        return $this->dao->select('t1.*, t2.realname')
            ->from(TABLE_PRODUCTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account= t2.account')
            ->beginIF($week != 0)
            ->where('YEARWEEK(t1.date, 1)')->eq($year . $week)
            ->fi()
            ->beginIF($product != 0)
            ->andWhere('t1.product')->eq($product)
            ->fi()
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getWeeklyProducts()
    {
        $products = $this->dao->select('t2.id, t2.name')
            ->from(TABLE_PRODUCTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product=t2.id')
            ->fetchAll();
        $result = [0 => '项目'];
        foreach ($products as $product) {
            $result[$product->id] = $product->name;
        }
        $result = array_unique($result);
        return $result;
    }

    public function getWeeklyById($weeklyID)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTWEEKLY)
            ->where('id')->eq($weeklyID)
            ->fetch();
    }

    public function generateWeekly($productID, $productName)
    {
        $content = $this->generateMarkdown();
        $weekly = new stdClass();
        $weekly->name = strval(date('Y')) . '年第' . strval(date('W') . '周-' . $productName . '-' . '项目周报');
        $weekly->content = $content;
        $weekly->date = helper::now();
        $weekly->product = $productID;
        $weekly->account = $this->app->user->account;
        $this->dao->insert(TABLE_PRODUCTWEEKLY)->data($weekly)->exec();
        return $this->dao->lastInsertID();
    }

    public function edit($id, $post)
    {
        return $this->dao->update(TABLE_PRODUCTWEEKLY)
            ->set('name')->eq($post['name'])
            ->set('content')->eq($post['content'])
            ->where('id')->eq($id)
            ->exec();
    }

    private function generateMarkdown()
    {
        $content = '### 项目概括' . PHP_EOL
            . '- [请输入概况]' . PHP_EOL . PHP_EOL
            . '### 本周完成工作' . PHP_EOL
            . '| 目标 | 目标完成度 | 实际完成度 | 备注 |' . PHP_EOL
            . '| --- | --- | --- | --- |' . PHP_EOL
            . '| | | | |' . PHP_EOL . PHP_EOL
            . '### 本周临时工作' . PHP_EOL
            . '| 目标 | 目标完成度 | 实际完成度 | 备注 |' . PHP_EOL
            . '| --- | --- | --- | --- |' . PHP_EOL
            . '| | | | |' . PHP_EOL . PHP_EOL
            . '### 下周工作计划' . PHP_EOL
            . '| 目标 | 当前完成度 | 目标完成度 | 备注 |' . PHP_EOL
            . '| --- | --- | --- | --- |' . PHP_EOL
            . '| | | | |' . PHP_EOL . PHP_EOL
            . '### 备注' . PHP_EOL
            . '- [其他事宜]' . PHP_EOL;
        return $content;
    }

    public function checkFinished($productID)
    {
        $isFinished = false;
        $weeklies = $this->dao->select('*')->from(TABLE_PRODUCTWEEKLY)
            ->where('product')->eq($productID)
            ->fetchAll();
        if (!empty($weeklies)) {
            $weekly = $weeklies[0];
            $date =  substr($weekly->date, 0, 11);
            $firstday = date('Y-m-d', strtotime('this week'));
            if ($date > $firstday) {
                $isFinished = true;
            }
        }
        return $isFinished;
    }

    public function checkPri($productID)
    {
        $pri = 4;
        $projectIDs = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)->where('product')->eq($productID)->fetchAll();
        if (!empty($projectIDs)) {
            $projectID = $projectIDs[0]->project;
            $project = $this->dao->select('pri')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();
            $pri = $project->pri;
        }
        return $pri;
    }
}