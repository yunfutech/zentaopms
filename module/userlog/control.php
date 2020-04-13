<?php

class userlog extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('dept');
    }

    public function view($userlogId)
    {
        $userlog = $this->userlog->getUserlogById($userlogId);

        $hyperdown    = $this->app->loadClass('hyperdown');
        $userlog->content = $hyperdown->makeHtml($userlog->content);

        $this->view->title      = $this->view->userlog->name . $this->lang->colon .$this->lang->userlog->edit;
        $this->view->position[] = $this->lang->userlog->view;

        $this->view->userlog = $userlog;
        $this->view->userlogId = $userlogId;
        $this->display();
    }

    public function edit($userlogId)
    {
        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $this->userlog->edit($userlogId, $_POST);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['locate']  = inlink('view', 'userlogId=' . $userlogId);
            $this->send($response);
        }
        $userlog = $this->userlog->getUserlogById($userlogId);
        $this->view->userlog = $userlog;

        $this->view->title      = $this->view->userlog->name . $this->lang->colon .$this->lang->userlog->edit;
        $this->view->position[] = $this->lang->userlog->edit;

        $this->view->userlogId = $userlogId;
        $this->display();
    }

    public function finish($userlogId)
    {
        $response['result']  = 'success';
        $response['message'] = $this->lang->userlog->finishSuccess;
        $this->userlog->finish($userlogId);
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $response['locate']  = $this->createLink('my', 'userlog', "type=daily");
        $this->send($response);
    }

    /**
     * 生成日报
     */
    public function generateDaily()
    {
        $response['result']  = 'success';
        $response['message'] = $this->lang->userlog->generateSuccess;
        $user = $this->app->user;
        $tasks = $this->userlog->getTasksByUser($user->account, $this->userlog->daily);
        $todayTasks = $this->formatedTasks($tasks['todayTasks']);
        $tomorrowTasks = $this->formatedTasks($tasks['tomorrowTasks']);
        $dailyTasks = ['todayTasks' => $todayTasks, 'tomorrowTasks' => $tomorrowTasks];
        $this->userlog->create($user->account, $dailyTasks, $this->userlog->daily, $user->realname);
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        $response['locate']  = $this->createLink('my', 'userlog', "type=daily");
        $this->send($response);
    }

    /**
     * 生成周报
     */
    public function generateWeekly()
    {
        $users = $this->dept->getUsers($this->userlog->getDepts());
        foreach ($users as $user) {
            $tasks = $this->userlog->getTasksByUser($user->account, $this->userlog->weekly);
            $thisWeekTasks = $this->formatedTasks($tasks['thisWeekTasks']);
            $nextWeekTasks = $this->formatedTasks($tasks['nextWeekTasks']);
            $weeklyTasks = ['thisWeekTasks' => $thisWeekTasks, 'nextWeekTasks' => $nextWeekTasks];
            $this->userlog->create($user->account, $weeklyTasks, $this->userlog->weekly, $user->realname);
        }
    }

    /**
     * 按照项目和迭代分组
     */
    private function formatedTasks($tasks)
    {
        $formatedTasks = [];
        foreach ($tasks as $task) {
            if (!array_key_exists($task->productName, $formatedTasks)) {
                $formatedTasks[$task->productName] = [];
            }
            if (!array_key_exists($task->projectName, $formatedTasks[$task->productName])) {
                $formatedTasks[$task->productName][$task->projectName] = [];
                array_push($formatedTasks[$task->productName][$task->projectName], $task->name);
            } else {
                array_push($formatedTasks[$task->productName][$task->projectName], $task->name);
            }
        }
        return $formatedTasks;
    }
}
