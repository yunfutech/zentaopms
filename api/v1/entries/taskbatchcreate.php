<?php
/**
 * The task batch create entry point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 */
class taskBatchCreateEntry extends Entry
{
    /**
     * POST method.
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function post($executionID = 0)
    {
        if(!$executionID) $executionID = $this->param('execution', 0);
        if(!$executionID) return $this->send400('Need execution id.');

        $storyID  = $this->param('story', 0);
        $moduleID = $this->param('module', 0);
        $taskID   = $this->param('task', 0);

        if(!isset($this->requestBody->tasks))
        {
            return $this->send400('Need tasks.');
        }

        $modules    = array();
        $parents    = array();
        $names      = array();
        $colors     = array();
        $types      = array();
        $estimates  = array();
        $estStarted = array();
        $deadlines  = array();
        $desc       = array();
        $pri        = array();
        $stories    = array();
        foreach($this->request('tasks') as $task)
        {
            if(!isset($task->name) or !isset($task->type)) return $this->send400('Task must have name and type.');

            $modules[]    = isset($task->module)     ? $task->module     : $moduleID;
            $parents[]    = isset($task->parent)     ? $task->parent     : $taskID;
            $names[]      = $task->name;
            $colors[]     = isset($task->color)      ? $task->color      : '';
            $types[]      = $task->type;
            $estimates[]  = isset($task->estimate)   ? $task->estimate   : 0;
            $estStarted[] = isset($task->estStarted) ? $task->estStarted : 0;
            $deadlines[]  = isset($task->deadline)   ? $task->deadline   : null;
            $desc[]       = isset($task->desc)       ? $task->desc       : '';
            $pri[]        = isset($task->pri)        ? $task->pri        : 0;
            $stories[]    = isset($task->story)      ? $task->story      : $storyID;
        }
        $this->setPost('module',     $modules);
        $this->setPost('parent',     $parents);
        $this->setPost('name',       $names);
        $this->setPost('color',      $colors);
        $this->setPost('type',       $types);
        $this->setPost('estimate',   $estimates);
        $this->setPost('estStarted', $estStarted);
        $this->setPost('deadline',   $deadlines);
        $this->setPost('desc',       $desc);
        $this->setPost('pri',        $pri);
        $this->setPost('story',      $stories);

        $control = $this->loadController('task', 'batchCreate');
        $control->batchCreate($executionID, $storyID, $moduleID, $taskID);

        $data = $this->getData();
        if(!$data) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        if(!$taskID) return $this->send(200, array());
        $task = $this->loadModel('task')->getById($taskID);
        return $this->send(200, array('task' => $task));
    }
}
