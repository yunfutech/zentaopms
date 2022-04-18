<?php

/**
 * 自动生成验收会
 */
class myTask extends task
{
    public function generatetasks($day = '')
    {
        $users = $this->task->getMeetingUsers();
        $days = $this->getDays($day);
        $now = helper::now();
        foreach ($users as $user) {
            foreach ($days as $day) {
                $projectID = $this->config->task->generateTask->deptArr[$user->dept]['project'];
                $executionID = $this->config->task->generateTask->deptArr[$user->dept]['execution'];
                $task = [
                    'project' => $projectID,
                    'execution' => $executionID,
                    'name' => '',
                    'type' => $this->config->task->generateTask->defaultType,
                    'estimate' => $this->config->task->generateTask->defaultEstimate,
                    'consumed' => $this->config->task->generateTask->defaultConnsumed,
                    'left' => $this->config->task->generateTask->defaultLeft,
                    'deadline' => $day,
                    'subStatus' => '',
                    'color' => '',
                    'desc' => '',
                    'openedBy' => $user->account,
                    'assignedTo' => $user->account,
                    'openedDate' => $now,
                    'assignedDate' => $now
                ];
                $task['name'] = '早会' . $day;
                $this->task->generateTask($task);
                $task['name'] = '验收会' . $day;
                $this->task->generateTask($task);
            }
        }
        echo "生成会议任务成功";
    }

    private function getDays($day)
    {
        if (empty($day)) {
            $days = $this->getNextweekDays();
        } else {
            $post_days = explode(',', $day);
            $days = [];
            foreach ($post_days as $day) {
                array_push($days, date('Y-m-d', strtotime($day)));
            }
        }
        return $days;
    }

    private function getNextweekDays()
    {
        $days = [];
        $weeks = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($weeks as $week) {
            array_push($days, date('Y-m-d', strtotime('next ' . $week)));
        }
        return $days;
    }
}