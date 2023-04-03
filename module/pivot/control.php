<?php
/**
 * The control file of pivot module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     pivot
 * @version     $Id: control.php 4622 2013-03-28 01:09:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class pivot extends control
{
    /**
     * The index of pivot, goto project deviation.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('preview'));
    }

    /**
     * Preview a pivot.
     *
     * @param  int    $dimension
     * @param  string $group
     * @param  string $module
     * @param  string $method
     * @param  string $params
     * @access public
     * @return void
     */
    public function preview($dimension = 0, $group = '', $module = 'pivot', $method = '', $params = '')
    {
        $this->prepare4Preview($dimension, $group, $module, $method, $params);
        $this->display();
    }

    /**
     * Project deviation pivot.
     *
     * @access public
     * @return void
     */
    public function projectDeviation($begin = 0, $end = 0)
    {
        $this->session->set('executionList', $this->app->getURI(true), 'execution');

        $begin = date('Y-m-d', ($begin ? strtotime($begin) : time() - (date('j') - 1) * 24 * 3600));
        $end   = date('Y-m-d', ($end   ? strtotime($end)   : time() + (date('t') - date('j')) * 24 * 3600));

        $this->view->title      = $this->lang->pivot->projectDeviation;
        $this->view->position[] = $this->lang->pivot->projectDeviation;

        $this->view->executions = $this->pivot->getExecutions($begin, $end);
        $this->view->begin      = $begin;
        $this->view->end        = $end;
        $this->view->submenu    = 'project';
        $this->display();
    }

    /**
     * Product information pivot.
     *
     * @params string $conditions
     * @access public
     * @return void
     */
    public function productSummary($conditions = '')
    {
        $this->app->loadLang('story');
        $this->app->loadLang('product');
        $this->app->loadLang('productplan');
        $this->session->set('productList', $this->app->getURI(true), 'product');

        $this->view->title      = $this->lang->pivot->productSummary;
        $this->view->position[] = $this->lang->pivot->productSummary;
        $this->view->products   = $this->pivot->getProducts($conditions);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->submenu    = 'product';
        $this->view->conditions = $conditions;
        $this->display();
    }

    /**
     * Bug create pivot.
     *
     * @param  int    $begin
     * @param  int    $end
     * @param  int    $product
     * @param  int    $execution
     * @access public
     * @return void
     */
    public function bugCreate($begin = 0, $end = 0, $product = 0, $execution = 0)
    {
        $this->app->loadLang('bug');
        $begin = $begin == 0 ? date('Y-m-d', strtotime('last month', strtotime(date('Y-m',time()) . '-01 00:00:01'))) : date('Y-m-d', strtotime($begin));
        $end   = $end == 0   ? date('Y-m-d', strtotime('now')) : $end = date('Y-m-d', strtotime($end));

        $this->view->title      = $this->lang->pivot->bugCreate;
        $this->view->position[] = $this->lang->pivot->bugCreate;
        $this->view->begin      = $begin;
        $this->view->end        = $end;
        $this->view->bugs       = $this->pivot->getBugs($begin, $end, $product, $execution);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->executions = array('' => '') + $this->pivot->getProjectExecutions();
        $this->view->products   = array('' => '') + $this->loadModel('product')->getPairs('', 0, '', 'all');
        $this->view->execution  = $execution;
        $this->view->product    = $product;
        $this->view->submenu    = 'test';
        $this->display();
    }

    /**
     * Bug assign pivot.
     *
     * @access public
     * @return void
     */
    public function bugAssign()
    {
        $this->session->set('productList', $this->app->getURI(true), 'product');

        $this->view->title      = $this->lang->pivot->bugAssign;
        $this->view->position[] = $this->lang->pivot->bugAssign;
        $this->view->submenu    = 'test';
        $this->view->assigns    = $this->pivot->getBugAssign();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Workload pivot.
     *
     * @param string $begin
     * @param string $end
     * @param int    $days
     * @param int    $workday
     * @param int    $dept
     * @param int    $assign
     *
     * @access public
     * @return void
     */
    public function workload($begin = '', $end = '', $days = 0, $workday = 0, $dept = 0, $assign = 'assign')
    {
        if($_POST)
        {
            $data    = fixer::input('post')->get();
            $begin   = $data->begin;
            $end     = $data->end;
            $dept    = $data->dept;
            $days    = $data->days;
            $assign  = $data->assign;
            $workday = $data->workday;
        }

        $this->app->loadConfig('execution');
        $this->session->set('executionList', $this->app->getURI(true), 'execution');

        $begin  = $begin ? strtotime($begin) : time();
        $end    = $end   ? strtotime($end)   : time() + (7 * 24 * 3600);
        $end   += 24 * 3600;
        $beginWeekDay = date('w', $begin);
        $begin  = date('Y-m-d', $begin);
        $end    = date('Y-m-d', $end);

        if(empty($workday))$workday = $this->config->execution->defaultWorkhours;
        $diffDays = helper::diffDate($end, $begin);
        if($days > $diffDays) $days = $diffDays;
        if(empty($days))
        {
            $weekDay = $beginWeekDay;
            $days    = $diffDays;
            for($i = 0; $i < $diffDays; $i++,$weekDay++)
            {
                $weekDay = $weekDay % 7;
                if(($this->config->execution->weekend == 2 and $weekDay == 6) or $weekDay == 0) $days --;
            }
        }

        $this->view->title      = $this->lang->pivot->workload;
        $this->view->position[] = $this->lang->pivot->workload;

        $this->view->workload = $this->pivot->getWorkload($dept, $assign);
        $this->view->users    = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->begin    = $begin;
        $this->view->end      = date('Y-m-d', strtotime($end) - 24 * 3600);
        $this->view->days     = $days;
        $this->view->workday  = $workday;
        $this->view->dept     = $dept;
        $this->view->assign   = $assign;
        $this->view->allHour  = $days * $workday;
        $this->view->submenu  = 'staff';
        $this->display();
    }
}