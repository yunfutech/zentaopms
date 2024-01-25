<?php
/**
 * The control file of screen module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2022 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@cnezsoft.com>
 * @package     task
 * @version     $Id: control.php 5106 2022-11-18 17:15:54Z $
 * @link        https://www.zentao.net
 */
class screen extends control
{
    /**
     * Browse screen list.
     *
     * @param  int $dimensionID
     * @access public
     * @return void
     */
    public function browse($dimensionID = 0)
    {
        $dimensionID = $this->loadModel('dimension')->setSwitcherMenu($dimensionID);
        $this->checkShowGuide();

        $this->view->title      = $this->lang->screen->common;
        $this->view->screens    = $this->screen->getList($dimensionID);
        $this->view->dimension  = $dimensionID;
        $this->display();
    }

    /**
     * Check whether show guide.
     *
     * @access public
     * @return void
     */
    public function checkShowGuide()
    {
        $this->app->loadLang('admin');
        $this->loadModel('setting');

        $isUpdate = $this->setting->getItem("owner=system&module=bi&key=update2BI");
        if(empty($isUpdate))
        {
            $this->view->showGuide = false;
            return;
        }

        $lang     = (strpos($this->app->getClientLang(), 'zh') !== false) ? 'zh' : 'en';
        $version  = $this->config->edition == 'pms' ? 'pms' : 'biz';
        $imageURL = "static/images/bi_guide_{$version}_{$lang}.png";

        $moduleKey   = $version . 'Guide';
        $guides      = $this->setting->getItem("owner=system&module=bi&key={$moduleKey}");
        $haveSeen    = explode(',', $guides);
        $afterSeen   = array_merge($haveSeen, array($this->app->user->account));
        $this->setting->setItem("system.bi.{$moduleKey}", implode(',', array_unique($afterSeen)));

        $this->view->showGuide  = in_array($this->app->user->account, $haveSeen) ? false : true;
        $this->view->imageURL   = $imageURL;
        $this->view->version    = $version;
    }

    /**
     * View screen.
     *
     * @param  int $screenID
     * @param  int $year
     * @param  int $month
     * @param  int $dept
     * @param  string $account
     * @access public
     * @return void
     */
    public function view($screenID, $year = 0, $month = 0, $dept = 0, $account = '')
    {
        if(empty($year))  $year  = date('Y');
        if(empty($month)) $month = date('n');

        if($screenID == 3)
        {
            echo $this->fetch('report', 'annualData', "year=$year&month=$month&dept=$dept&account=$account");
            return;
        }

        $screen = $this->screen->getByID($screenID, $year, $month, $dept, $account);

        $this->view->title  = $screen->name;
        $this->view->screen = $screen;

        if($screenID == 5)
        {
            $this->loadModel('execution');
            $this->view->executions = $this->screen->getBurnData();
            $this->view->date       = date('Y-m-d H:i:s');
            $this->display('screen', 'burn');
        }
        else
        {
            $this->view->year    = $year;
            $this->view->month   = $month;
            $this->view->dept    = $dept;
            $this->view->account = $account;
            $this->display();
        }
    }

    /**
     * Ajax get chart.
     *
     * @access public
     * @return void
     */
    public function ajaxGetChart()
    {
        if(!empty($_POST))
        {
            $chartID      = $this->post->sourceID;
            $type         = $this->post->type;
            $queryType    = isset($_POST['queryType']) ? $this->post->queryType : 'filter';

            $type = ($type == 'Tables' or $type == 'pivot') ? 'pivot' : 'chart';

            $table = $type == 'chart' ? TABLE_CHART : TABLE_PIVOT;
            $chart = $this->dao->select('*')->from($table)->where('id')->eq($chartID)->fetch();

            $filterFormat = '';
            if($queryType == 'filter')
            {
                $filterParams = json_decode($this->post->filters, true);
                $filters      = json_decode($chart->filters, true);
                $mergeFilters = array();

                foreach($filters as $index => $filter)
                {
                    $default = isset($filterParams[$index]['default']) ? $filterParams[$index]['default'] : null;
                    $filterType = $filter['type'];
                    if($filterType == 'date' or $filterType == 'datetime')
                    {
                        if(isset($filter['from']) and $filter['from'] == 'query')
                        {
                            if(is_numeric($default)) $default = date('Y-m-d H:i:s', $default / 1000);
                        }
                        else
                        {
                            if(is_array($default))
                            {
                                $begin = $default[0];
                                $end   = $default[1];

                                $begin = date('Y-m-d H:i:s', $begin / 1000);
                                $end = date('Y-m-d H:i:s', $end / 1000);

                                $default = array('begin' => $begin, 'end' => $end);
                            }
                            else
                            {
                                $default = array('begin' => '', 'end' => '');
                            }
                        }

                    }
                    $filter['default'] = $default;
                    $mergeFilters[] = $filter;
                }

                if($table == TABLE_PIVOT)
                {
                    list($sql, $filterFormat) = $this->loadModel($type)->getFilterFormat($chart->sql, $mergeFilters);
                    $chart->sql = $sql;
                }
                else
                {
                    $filterFormat = $this->loadModel($type)->getFilterFormat($mergeFilters);
                }
            }

            $chartData = $this->screen->genComponentData($chart, $type, null, $filterFormat);
            print(json_encode($chartData));
        }
    }
}
