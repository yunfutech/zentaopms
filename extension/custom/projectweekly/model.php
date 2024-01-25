<?php
class projectweeklyModel extends model
{
    public function getWeekly($pager, $sort, $week = 0, $project = 0, $user = 0)
    {
        $sql =  $this->dao->select('t1.*, t2.realname')
            ->from(TABLE_PROJECTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account= t2.account')
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t1.projectID=t3.id')
            ->where(1)
            ->beginIF(is_string($user) && $user != '' && $user != '0')
            ->andWhere('t3.PP')->eq($user)
            ->fi()
            ->beginIF($week != 0)
            ->andWhere('YEARWEEK(t1.createdDatetime, 1)')->eq($week)
            ->fi()
            ->beginIF($project != 0)
            ->andWhere('t1.projectID')->eq($project)
            ->fi()
            ->orderBy($sort)
            ->page($pager)
            ->fetchAll();
        return $sql;
    }

    public function getWeeklyProjects()
    {
        $projects = $this->dao->select('t2.id, t2.name')
            ->from(TABLE_PROJECTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.projectID=t2.id')
            ->fetchAll();
        $result = [0 => '全部'];
        foreach ($projects as $project) {
            $result[$project->id] = $project->name;
        }
        $result = array_unique($result);
        return $result;
    }

    public function getWeeklyDirectors()
    {
        $directors = $this->dao->select('t3.account, t3.realname')
            ->from(TABLE_PROJECTWEEKLY)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.projectID=t2.id')
            ->leftJoin(TABLE_USER)->alias('t3')->on('t2.PP=t3.account')
            ->fetchAll();
        $result = [0 => '全部'];
        foreach ($directors as $director) {
            $result[$director->account] = $director->realname;
        }
        return $result;
    }

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