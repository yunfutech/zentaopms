<?php

class productweeklyModel extends model
{
    public function getWeeklyByProduct($productID, $pager, $sort)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTWEEKLY)
            ->where('product')->eq($productID)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getWeekly($pager, $sort)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCTWEEKLY)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
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
        $weekly->name = strval(date('Y-m-d') . '-' . $productName . '-' . '项目周报');
        $weekly->content = $content;
        $weekly->date = helper::now();
        $weekly->product = $productID;
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
            . '| 目标 | 目标完成度 | 实际完成度 | 备注 |' . PHP_EOL
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