<?php
class projectweeklyModel extends model
{
    public function getWeeklyByProject($projectID, $pager, $sort)
    {
        return $this->dao->select('*')
            ->from(TABLE_PROJECTWEEKLY)
            ->where('projectID')->eq($projectID)
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
    }

    public function getWeeklyById($weeklyID)
    {
        return $this->dao->select('*')->from(TABLE_PROJECTWEEKLY)
            ->where('id')->eq($weeklyID)
            ->fetch();
    }

    public function generateWeekly($projectID, $productName)
    {
        $weekly = new stdClass();
        $weekly->name = strval(date('Y')) . '年第' . strval(date('W') . '周-' . $productName . '-' . '项目周报');
        $weekly->overview = '';
        $weekly->question = '';
        $weekly->weekjob = '';
        $weekly->weekplan = '';
        $weekly->createdDatetime = helper::now();
        $weekly->projectID = $projectID;
        $weekly->account = $this->app->user->account;
        $this->dao->insert(TABLE_PROJECTWEEKLY)->data($weekly)->exec();
        return $this->dao->lastInsertID();
    }

    public function edit($weeklyID)
    {
        $this->dao->update(TABLE_PROJECTWEEKLY)
            ->set('overview')->eq($_POST['overview'])
            ->set('question')->eq($_POST['question'])
            ->set('weekjob')->eq($_POST['weekjob'])
            ->set('weekplan')->eq($_POST['weekplan'])
            ->where('id')->eq($weeklyID)->exec();
    }
}