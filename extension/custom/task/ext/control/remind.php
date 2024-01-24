<?php

/**
 * 云孚任务定时提醒功能
 */
class myTask extends task
{

    public function remind($sendWx = 1, $sendMail=1)
    {
        $today = helper::today();

        $mentionedUsers = [];
        $user2cnt = [];

        $users = $this->task->getRemindUsers();

        $lessUsers = [];   # 少于8小时员工
        $moreUsers = [];   # 多于10小时员工

        $less_count = 0;
        $deleyTasksRank = array();

        foreach ($users as $user) {
            $estimate = $this->task->getUserTaskEstimate($user->account, $today);
            if (!$estimate) {
                continue;
            }
            $user->estimate = $estimate;
            if (intval($estimate) < $this->config->task->remind->target) {
                $less_count += 1;
                array_push($lessUsers, $user);
            } else if (intval($estimate) > $this->config->task->remind->overtime) {
                array_push($moreUsers, $user);
            }

            $delayTasksCount = $this->task->getUserDelayTasksCount($user->account, $today);
            // var_dump($this->dao->sqlobj);
            if ($delayTasksCount == 0) {
                continue;
            }
            array_push($mentionedUsers, $user->account);
            $user2cnt[$user->realname] = $delayTasksCount * $this->config->exerciseNum->delayTask;
            array_push($deleyTasksRank, ['name' => $user->realname, 'delay_count' => $delayTasksCount, 'train_count' => '+' . strval(10 * $delayTasksCount)]);
        }

        $deleyTasksRank = $this->sortDeleyTasksRank($deleyTasksRank);

        $delayProjects = $this->task->getDelayExecution();
        foreach ($delayProjects as $project) {
            if (array_key_exists($project->realname, $user2cnt)) {
                $user2cnt[$project->realname] += $project->cnt *  $this->config->exerciseNum->delayProject;
            } else {
                $user2cnt[$project->realname] = $project->cnt *  $this->config->exerciseNum->delayProject;
            }
            array_push($mentionedUsers, $project->account);
        }
        // var_dump($this->dao->sqlobj);

        $unresolvedBugs = $this->task->getUnresolvedBugs();
        $countUserBugs = $this->task->countUserBugs();

        // 节假日
        if ($less_count / count($users) >= 0.5) {
            echo '节假日\n';
            exit();
        }
        $summary = '';
        if (!empty($lessUsers)) {
            $summary .= '任务不饱和(运动+20)：';
            foreach ($lessUsers as $lessUser) {
                if (array_key_exists($lessUser->realname, $user2cnt)) {
                    $user2cnt[$lessUser->realname] += $this->config->exerciseNum->lackEstimate;
                } else {
                    $user2cnt[$lessUser->realname] = $this->config->exerciseNum->lackEstimate;
                }
                array_push($mentionedUsers, $lessUser->account);
                $summary .= $lessUser->realname . '(' . strval($lessUser->estimate) . ')';
                if ($lessUser != $lessUsers[count($lessUsers) - 1]) {
                    $summary .= '、';
                }
            }
        }
        if (!empty($moreUsers)) {
            $summary .= '<br />任务超负荷：';
            foreach ($moreUsers as $moreUser) {
                $summary .= $moreUser->realname . '(' . strval($moreUser->estimate) . ')';
                if ($moreUser != $moreUsers[count($moreUsers) - 1]) {
                    $summary .= '、';
                }
            }
        }
        $this->view->summary = $summary;
        $this->view->countUserBugs = $countUserBugs;
        $this->view->unresolvedBugs = $unresolvedBugs;
        $this->view->deleyTasksRank = $deleyTasksRank;
        $this->view->delayProjects = $delayProjects;
        $this->view->delayProjectsNum = $this->config->exerciseNum->delayProject;
        $this->display();

        if (intval($sendWx) == 1) {
            $wxParams = $this->getWxMsgParams($user2cnt, $mentionedUsers);
            $this->sendQywx($wxParams['text'], $wxParams['markdown'], $wxParams['mentionedUsers']);
        }

        if (intval($sendMail) == 1) {
            $this->loadModel('mail');

            $viewFile = $this->app->getExtensionRoot() . 'custom/task/ext/view/remind.html.php';
            ob_start();
            include $viewFile;
            $mailContent = ob_get_contents();
            ob_end_clean();

            $to_mail = $this->config->task->remind->to;

            $emails[$to_mail] = new stdClass();
            $emails[$to_mail]->realname = $this->config->task->remind->from;
            $emails[$to_mail]->email = $this->config->task->remind->email;

            $this->mail->send($to_mail, $this->config->task->remind->subject, $mailContent, '', false, $emails);

            if ($this->mail->isError()) {
                echo "发送失败: \n";
                a($this->mail->getError());
            } else {
                echo "发送成功\n";
            }
        }
    }

    /**
     * 延期任务排行榜排序
     */
    private function sortDeleyTasksRank($deleyTasksRank)
    {

        $cntArray = array();
        foreach ($deleyTasksRank as $key => $value) {
            $cntArray[$key] = $value['delay_count'];
        }
        array_multisort($cntArray, SORT_DESC, $deleyTasksRank);
        return $deleyTasksRank;
    }

    /**
     * 获取微信信息参数
     */
    private function getWxMsgParams($user2cnt, $mentionedUsers)
    {
        $markdown = '## # 总计' . PHP_EOL;
        arsort($user2cnt);
        foreach ($user2cnt as $user => $cnt) {
            $markdown .= '###### - ' . $user . ' ' . strval($cnt) . PHP_EOL;
        }
        $mentionedUsers = array_unique($mentionedUsers);
        $text = '任务不符合规范或有延期迭代，按下方统计结果做运动, 详情见邮件内容';
        return ['text' => $text, 'markdown' => $markdown, 'mentionedUsers' => $mentionedUsers];
    }

    /**
     * 请求微信机器人api
     */
    public function sendQywx($text, $markdown, $mentionedUsers)
    {
        if (empty($mentionedUsers)) {
            return;
        }
        $url = $this->config->task->remind->wsGroup;
        $postData = [
            'msgtype' => 'text',
            "text" => [
                'content' => $text,
                'mentioned_list' => ['@all']
            ]
        ];
        $postData2 = [
            'msgtype' => 'markdown',
            "markdown" => [
                'content' => $markdown,
                'mentioned_list' => ['@all']
            ]
        ];
        $this->post($url, $postData, $json = true);
        $this->post($url, $postData2, $json = true);
    }

    public function post($url, $data = [], $json = false)
    {
        if ($json) {
            $str = 'application/json';
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $str = 'application/x-www-form-urlencoded';
            $data = http_build_query($data);
        }
        $options['http'] = array(
            'timeout' => 5,
            'method'  => 'POST',
            'header'  => "Content-Type: $str;charset=utf-8",
            'content' => $data,
        );
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}