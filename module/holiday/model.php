<?php
/**
 * The model file of holiday module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     holiday
 * @version     $Id
 * @link        http://www.zentao.net
 */
class holidayModel extends model
{
    /**
     * Get holiday by id.
     *
     * @param  int    $id
     * @access public
     * @return object
     */
    public function getById($id)
    {
        return $this->dao->select('*')->from(TABLE_HOLIDAY)->where('id')->eq($id)->fetch();
    }

    /**
     * Get holiday list.
     *
     * @param  string $year
     * @param  string $type
     * @access public
     * @return object
     */
    public function getList($year = '', $type = 'all')
    {
        return $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('1')
            ->beginIf(!empty($year))
            ->andWhere('year', true)->eq($year)
            ->orWhere('begin')->like("$year-%")
            ->orWhere('end')->like("$year-%")
            ->markright(1)
            ->fi()
            ->beginIf($type != 'all' && $type)->andWhere('type')->eq($type)->fi()
            ->fetchAll('id');
    }

    /**
     * Get year pairs.
     *
     * @access public
     * @return array
     */
    public function getYearPairs()
    {
        return $this->dao->select('year,year')->from(TABLE_HOLIDAY)->groupBy('year')->orderBy('year_desc')->fetchPairs();
    }

    /**
     * Create a holiday.
     *
     * @access public
     * @return int
     */
    public function create()
    {
        $holiday = fixer::input('post')->get();
        $holiday->year = substr($holiday->begin, 0, 4);
        if($holiday->year and helper::isZeroDate($holiday->year)) return dao::$errors['begin'][] = sprintf($this->lang->error->date, $this->lang->holiday->begin);
        if($holiday->end and helper::isZeroDate($holiday->end))  return dao::$errors['end'][]   = sprintf($this->lang->error->date, $this->lang->holiday->end);

        $this->dao->insert(TABLE_HOLIDAY)->data($holiday)
            ->autoCheck()
            ->batchCheck($this->config->holiday->require->create, 'notempty')
            ->check('end', 'ge', $holiday->begin)
            ->exec();
        if(dao::isError()) return false;

        $beginDate = $this->post->begin;
        $endDate   = $this->post->end;

        /* Update project. */
        $this->updateProgramPlanDuration($beginDate, $endDate);
        $this->updateProjectRealDuration($beginDate, $endDate);

        /* Update task. */
        $this->updateTaskPlanDuration($beginDate, $endDate);
        $this->updateTaskRealDuration($beginDate, $endDate);

        return $this->dao->lastInsertID();
    }

    /**
     * Edit holiday.
     *
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function update($id)
    {
        $holiday = fixer::input('post')->get();
        $holiday->year = substr($holiday->begin, 0, 4);
        if(helper::isZeroDate($holiday->year)) return dao::$errors['begin'][] = sprintf($this->lang->error->date, $this->lang->holiday->begin);
        if(helper::isZeroDate($holiday->end))  return dao::$errors['end'][]   = sprintf($this->lang->error->date, $this->lang->holiday->end);

        $this->dao->update(TABLE_HOLIDAY)
            ->data($holiday)
            ->autoCheck()
            ->batchCheck($this->config->holiday->require->edit, 'notempty')
            ->check('end', 'ge', $holiday->begin)
            ->where('id')->eq($id)
            ->exec();

        if(!dao::isError())
        {
            $beginDate = $this->post->begin;
            $endDate   = $this->post->end;

            /* Update project. */
            $this->updateProgramPlanDuration($beginDate, $endDate);
            $this->updateProjectRealDuration($beginDate, $endDate);

            /* Update task. */
            $this->updateTaskPlanDuration($beginDate, $endDate);
            $this->updateTaskRealDuration($beginDate, $endDate);
        }
        return !dao::isError();
    }

    /**
     * Get holidays by begin and end.
     *
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return array
     */
    public function getHolidays($begin, $end)
    {
        $records = $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('type')->eq('holiday')
            ->andWhere('begin')->le($end)
            ->andWhere('end')->ge($begin)
            ->fetchAll('id');

        $naturalDays = $this->getDaysBetween($begin, $end);

        $holidays = array();
        foreach($records as $record)
        {
            $dates    = $this->getDaysBetween($record->begin, $record->end);
            $holidays = array_merge($holidays, $dates);
        }

        return array_intersect($naturalDays, $holidays);
    }

    /**
     * Get working days.
     *
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return array
     */
    public function getWorkingDays($begin = '', $end = '')
    {
        $records = $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('type')->eq('working')
            ->andWhere('begin')->le($end)
            ->andWhere('end')->ge($begin)
            ->fetchAll('id');

        $workingDays = array();
        foreach($records as $record)
        {
            $dates = $this->getDaysBetween($record->begin, $record->end);
            $workingDays = array_merge($workingDays, $dates);
        }
        return $workingDays;
    }

    /**
     * Get actual working days.
     *
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return array
     */
    public function getActualWorkingDays($begin, $end)
    {
        if(empty($begin) or empty($end) or $begin == '0000-00-00' or $end == '0000-00-00') return array();

        $actualDays = array();
        $currentDay = $begin;

        $holidays    = $this->getHolidays($begin, $end);
        $workingDays = $this->getWorkingDays($begin, $end);
        $weekend     = isset($this->config->project->weekend) ? $this->config->project->weekend : 2;

        /* When the start date and end date are the same. */
        if($begin == $end)
        {
            if(in_array($begin, $workingDays)) return $actualDays[] = $begin;
            if(in_array($begin, $holidays))    return $actualDays;

            $w = date('w', strtotime($begin));
            if($weekend == 2)
            {
                if($w == 0 or $w == 6) return $actualDays;
            }
            else
            {
                if($w == 0) return $actualDays;
            }

            $actualDays[] = $begin;
            return $actualDays;
        }

        for($i = 0; $currentDay < $end; $i ++)
        {
            $currentDay = date('Y-m-d', strtotime("$begin + $i days"));
            $w          = date('w', strtotime($currentDay));

            if(in_array($currentDay, $workingDays))
            {
                $actualDays[] = $currentDay;
                continue;
            }

            if(in_array($currentDay, $holidays)) continue;
            if($weekend == 2)
            {
                if($w == 0 or $w == 6) continue;
            }
            else
            {
                if($w == 0) continue;
            }
            $actualDays[] = $currentDay;
        }

        return $actualDays;
    }

    /**
     * Get diff days.
     *
     * @param  varchar $begin
     * @param  varchar $end
     * @access public
     * @return bool
     */
    public function getDaysBetween($begin, $end)
    {
        $beginTime = strtotime($begin);
        $endTime   = strtotime($end);
        $days      = ($endTime - $beginTime) / 86400;

        $dateList  = array();
        for($i = 0; $i <= $days; $i ++) $dateList[] = date('Y-m-d', strtotime("+$i days", $beginTime));

        return $dateList;
    }

    /**
     * Judge if is holiday.
     *
     * @param  string $date
     * @access public
     * @return bool
     */
    public function isHoliday($date)
    {
        $record = $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('type')->eq('holiday')
            ->andWhere('begin')->le($date)
            ->andWhere('end')->ge($date)
            ->fetch();
        return !empty($record);
    }

    /**
     * Judge if is working days.
     *
     * @param  string $date
     * @access public
     * @return bool
     */
    public function isWorkingDay($date)
    {
        $record = $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('type')->eq('working')
            ->andWhere('begin')->le($date)
            ->andWhere('end')->ge($date)
            ->fetch();
        return !empty($record);
    }

    /**
     * Update project plan duration.
     *
     * @param  string $beginDate
     * @param  string $endDate
     * @access public
     * @return void
     */
    public function updateProgramPlanDuration($beginDate, $endDate)
    {
        $updateProjectList = $this->dao->select('id, begin, end')
            ->from(TABLE_PROJECT)
            ->where('begin')->between($beginDate, $endDate)
            ->orWhere('end')->between($beginDate, $endDate)
            ->orWhere("(begin < '$beginDate' AND end > '$endDate')")
            ->andWhere('status')->ne('done')
            ->fetchAll();

        foreach($updateProjectList as $project)
        {
            $realDuration = $this->getActualWorkingDays($project->begin, $project->end);
            $realDuration = count($realDuration);

            $this->dao->update(TABLE_PROJECT)->set('planDuration')->eq($realDuration)->where('id')->eq($project->id)->exec();
        }
    }

    /**
     * Update project real duration.
     *
     * @param  string $beginDate
     * @param  string $endDate
     * @access public
     * @return void
     */
    public function updateProjectRealDuration($beginDate, $endDate)
    {
        $updateProjectList = $this->dao->select('id, realBegan, realEnd')
            ->from(TABLE_PROJECT)
            ->where('realBegan')->between($beginDate, $endDate)
            ->orWhere('realEnd')->between($beginDate, $endDate)
            ->orWhere("(realBegan < '$beginDate' AND realEnd > '$endDate')")
            ->andWhere('status')->ne('done')
            ->fetchAll();

        foreach($updateProjectList as $project)
        {
            $realDuration = $this->getActualWorkingDays($project->realBegan, $project->realEnd);
            $realDuration = count($realDuration);

            $this->dao->update(TABLE_PROJECT)->set('realDuration')->eq($realDuration)->where('id')->eq($project->id)->exec();
        }
    }

    /**
     * Update task plan duration.
     *
     * @param  string $beginDate
     * @param  string $endDate
     * @access public
     * @return void
     */
    public function updateTaskPlanDuration($beginDate, $endDate)
    {
        $updateTaskList = $this->dao->select('id, estStarted, deadline')
            ->from(TABLE_TASK)
            ->where('estStarted')->between($beginDate, $endDate)
            ->orWhere('deadline')->between($beginDate, $endDate)
            ->orWhere("(estStarted < '$beginDate' AND deadline > '$endDate')")
            ->andWhere('status') ->ne('done')
            ->fetchAll();

        foreach($updateTaskList as $task)
        {
            $planduration = $this->getActualWorkingDays($task->estStarted, $task->deadline);
            $planduration = count($planduration);

            $this->dao->update(TABLE_TASK)->set('planduration')->eq($planduration)->where('id')->eq($task->id)->exec();
        }
    }

    /**
     * Update task real duration.
     *
     * @param  string $beginDate
     * @param  string $endDate
     * @access public
     * @return void
     */
    public function updateTaskRealDuration($beginDate, $endDate)
    {
        $updateTaskList = $this->dao->select('id, realStarted, finishedDate')
            ->from(TABLE_TASK)
            ->where('realStarted')->between($beginDate, $endDate)
            ->orWhere("date_format(finishedDate,'%Y-%m-%d')")->between($beginDate, $endDate)
            ->orWhere("(realStarted < '$beginDate' AND date_format(finishedDate,'%Y-%m-%d') > '$endDate')")
            ->andWhere('status')->ne('done')
            ->fetchAll();

        foreach($updateTaskList as $task)
        {
            $realDuration = $this->getActualWorkingDays($task->realStarted, date('Y-m-d',strtotime($task->finishedDate)));
            $realDuration = count($realDuration);

            $this->dao->update(TABLE_TASK)->set('realDuration')->eq($realDuration)->where('id')->eq($task->id)->exec();
        }
    }
}
