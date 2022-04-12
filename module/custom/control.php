<?php
/**
 * The control file of custom of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class custom extends control
{
    /**
     * Index
     *
     * @access public
     * @return void
     */
    public function index()
    {
        if(($this->config->systemMode == 'new') and common::hasPriv('custom', 'set'))
        {
            return print(js::locate(inlink('set', "module=project&field=" . key($this->lang->custom->project->fields))));
        }

        if(common::hasPriv('custom', 'product'))   return print(js::locate(inlink('product')));
        if(common::hasPriv('custom', 'execution')) return print(js::locate(inlink('execution')));

        foreach($this->lang->custom->system as $sysObject)
        {
            if(common::hasPriv('custom', $sysObject)) return print(js::locate(inlink($sysObject)));
        }
    }

    /**
     * Custom
     *
     * @param  string $module
     * @param  string $field
     * @param  string $lang
     * @access public
     * @return void
     */
    public function set($module = 'story', $field = 'priList', $lang = '')
    {
        if(empty($lang)) $lang = $this->app->getClientLang();
        if($module == 'user' and $field == 'priList') $field = 'statusList';
        if($module == 'block' and $field == 'priList')$field = 'closed';
        $currentLang = $this->app->getClientLang();

        $this->app->loadLang($module);
        $fieldList = zget($this->lang->$module, $field, '');

        if($module == 'project' and $field == 'unitList')
        {
            $this->app->loadConfig($module);
            $unitList = zget($this->config->$module, 'unitList', '');
            $this->view->unitList        = explode(',', $unitList);
            $this->view->defaultCurrency = zget($this->config->$module, 'defaultCurrency', 'CNY');
        }
        if($module == 'story' and $field == 'reviewRules')
        {
            $this->app->loadConfig($module);
            $this->view->reviewRule     = zget($this->config->$module, 'reviewRules', '1');
            $this->view->users          = $this->loadModel('user')->getPairs('noclosed|nodeleted');
            $this->view->superReviewers = zget($this->config->$module, 'superReviewers', '');
        }
        if(($module == 'story' or $module == 'testcase') and $field == 'review')
        {
            $this->app->loadConfig($module);
            $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
            $this->view->needReview     = zget($this->config->$module, 'needReview', 1);
            $this->view->forceReview    = zget($this->config->$module, 'forceReview', '');
            $this->view->forceNotReview = zget($this->config->$module, 'forceNotReview', '');
        }
        if($module == 'task' and $field == 'hours')
        {
            $this->app->loadConfig('execution');
            $this->view->weekend   = $this->config->execution->weekend;
            $this->view->workhours = $this->config->execution->defaultWorkhours;
        }
        if($module == 'bug' and $field == 'longlife')
        {
            $this->app->loadConfig('bug');
            $this->view->longlife = $this->config->bug->longlife;
        }
        if($module == 'block' and $field == 'closed')
        {
            $this->loadModel('block');
            $closedBlock = isset($this->config->block->closed) ? $this->config->block->closed : '';

            $this->view->blockPairs  = $this->block->getClosedBlockPairs($closedBlock);
            $this->view->closedBlock = $closedBlock;
        }
        if($module == 'user' and $field == 'deleted')
        {
            $this->app->loadConfig('user');
            $this->view->showDeleted = isset($this->config->user->showDeleted) ? $this->config->user->showDeleted : '0';
        }

        if(strtolower($this->server->request_method) == "post")
        {
            if($module == 'project' and $field == 'unitList')
            {
                $data = fixer::input('post')->join('unitList', ',')->get();
                if(empty($data->unitList)) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->currencyNotEmpty));
                if(empty($data->defaultCurrency)) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->defaultNotEmpty));
                $this->loadModel('setting')->setItems("system.$module", $data);
            }
            elseif($module == 'story' and $field == 'review')
            {
                $data = fixer::input('post')->join('forceReview', ',')->get();
                $this->loadModel('setting')->setItems("system.$module@{$this->config->vision}", $data);
            }
            elseif($module == 'story' and $field == 'reviewRules')
            {
                $data = fixer::input('post')->join('superReviewers', ',')->get();
                $this->loadModel('setting')->setItems("system.$module@{$this->config->vision}", $data);
            }
            elseif($module == 'testcase' and $field == 'review')
            {
                $review = fixer::input('post')->get();
                if($review->needReview) $data = fixer::input('post')->join('forceNotReview', ',')->remove('forceReview')->get();
                if(!$review->needReview) $data = fixer::input('post')->join('forceReview', ',')->remove('forceNotReview')->get();
                $this->loadModel('setting')->setItems("system.$module", $data);

                $reviewCase = isset($review->reviewCase) ? $review->reviewCase : 0;
                if($review->needReview == 0 and $reviewCase)
                {
                    $waitCases = $this->loadModel('testcase')->getByStatus(0, 0, 'all', 'wait');
                    $this->testcase->batchReview(array_keys($waitCases), 'pass');
                }
            }
            elseif($module == 'task' and $field == 'hours')
            {
                $this->loadModel('setting')->setItems('system.execution', fixer::input('post')->get());
            }
            elseif($module == 'bug' and $field == 'longlife')
            {
                $this->loadModel('setting')->setItems('system.bug', fixer::input('post')->get());
            }
            elseif($module == 'block' and $field == 'closed')
            {
                $data = fixer::input('post')->join('closed', ',')->get();
                $this->loadModel('setting')->setItem('system.block.closed', zget($data, 'closed', ''));
            }
            elseif($module == 'user' and $field == 'contactField')
            {
                $data = fixer::input('post')->join('contactField', ',')->get();
                if(!isset($data->contactField)) $data->contactField = '';
                $this->loadModel('setting')->setItem('system.user.contactField', $data->contactField);
            }
            elseif($module == 'user' and $field == 'deleted')
            {
                $data = fixer::input('post')->get();
                $this->loadModel('setting')->setItem('system.user.showDeleted', $data->showDeleted);
            }
            else
            {
                $lang = $_POST['lang'];
                $oldCustoms = $this->custom->getItems("lang=$lang&module=$module&section=$field");
                foreach($_POST['keys'] as $index => $key)
                {
                    if(!empty($key)) $key = trim($key);
                    /* Invalid key. It should be numbers. (It includes severityList in bug module and priList in story, task, bug, testcasea, testtask and todo module.) */
                    if($field == 'priList' or $field == 'severityList')
                    {
                        if(!is_numeric($key) or $key > 255) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidNumberKey));
                    }
                    if(!empty($key) and !isset($oldCustoms[$key]) and $key != 'n/a' and !validater::checkREG($key, '/^[a-z_A-Z_0-9]+$/')) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStringKey));

                    /* The length of roleList in user module and typeList in todo module is less than 10. check it when saved. */
                    if($field == 'roleList' or $module == 'todo' and $field == 'typeList')
                    {
                        if(strlen($key) > 10) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['ten']));
                    }

                    /* The length of sourceList in story module and typeList in task module is less than 20, check it when saved. */
                    if($field == 'sourceList' or $module == 'task' and $field == 'typeList')
                    {
                        if(strlen($key) > 20) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twenty']));
                    }

                    /* The length of stageList in testcase module is less than 255, check it when saved. */
                    if($module == 'testcase' and $field == 'stageList' and strlen($key) > 255) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['twoHundred']));

                    /* The length of field that in bug and testcase module and reasonList in story and task module is less than 30, check it when saved. */
                    if($module == 'bug' or $field == 'reasonList' or $module == 'testcase')
                    {
                        if(strlen($key) > 30) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->invalidStrlen['thirty']));
                    }
                }

                $this->custom->deleteItems("lang=$lang&module=$module&section=$field&vision={$this->config->vision}");
                $data = fixer::input('post')->get();
                foreach($data->keys as $index => $key)
                {
                    //if(!$system and (!$value or !$key)) continue; //Fix bug #951.

                    $value  = $data->values[$index];
                    $system = $data->systems[$index];
                    $this->custom->setItem("{$lang}.{$module}.{$field}.{$key}.{$system}", $value);
                }
            }
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('custom', 'set', "module=$module&field=$field&lang=" . str_replace('-', '_', isset($this->config->langs[$lang]) ? $lang : 'all'))));
        }

        /* Check whether the current language has been customized. */
        $lang = str_replace('_', '-', $lang);
        $dbFields = $this->custom->getItems("lang=$lang&module=$module&section=$field&vision={$this->config->vision}");
        if(empty($dbFields)) $dbFields = $this->custom->getItems("lang=" . ($lang == $currentLang ? 'all' : $currentLang) . "&module=$module&section=$field");
        if($dbFields)
        {
            $dbField = reset($dbFields);
            if($lang != $dbField->lang)
            {
                $lang = str_replace('-', "_", $dbField->lang);
                foreach($fieldList as $key => $value)
                {
                    if(isset($dbFields[$key]) and $value != $dbFields[$key]->value) $fieldList[$key] = $dbFields[$key]->value;
                }
            }
        }

        $this->view->title       = $this->lang->custom->common . $this->lang->colon . $this->lang->$module->common;
        $this->view->position[]  = $this->lang->custom->common;
        $this->view->position[]  = $this->lang->$module->common;
        $this->view->fieldList   = $fieldList;
        $this->view->dbFields    = $dbFields;
        $this->view->field       = $field;
        $this->view->lang2Set    = str_replace('_', '-', $lang);
        $this->view->module      = $module;
        $this->view->currentLang = $currentLang;
        $this->view->canAdd      = strpos($this->config->custom->canAdd[$module], $field) !== false;

        $this->display();
    }

    /**
     * Restore the default lang. Delete the related items.
     *
     * @param  string $module
     * @param  string $field
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function restore($module, $field, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->custom->confirmRestore, inlink('restore', "module=$module&field=$field&confirm=yes")));

        if($module == 'user' and $field == 'contactField')
        {
            $this->loadModel('setting')->deleteItems("module=$module&key=$field");
        }
        else
        {
            $this->custom->deleteItems("module=$module&section=$field");
        }
        return print(js::reload('parent'));
    }

    /**
     * Set working mode function.
     *
     * @access public
     * @return void
     */
    public function working()
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem('system.common.global.flow', $this->post->flow);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->custom->working;
        $this->view->position[] = $this->lang->custom->working;
        $this->display();
    }

    /**
     * Set Required.
     *
     * @param  string $moduleName
     * @access public
     * @return void
     */
    public function required($moduleName = '')
    {
        if(empty($moduleName)) $moduleName = current($this->config->custom->requiredModules);

        if($this->server->request_method == 'POST')
        {
            $this->custom->saveRequiredFields($moduleName);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('required', "moduleName=$moduleName")));
        }

        foreach($this->config->custom->requiredModules as $requiredModule) $this->app->loadLang($requiredModule);

        /* Get this module requiredFields. */
        $this->loadModel($moduleName);
        if($moduleName == 'user') $this->app->loadModuleConfig($moduleName);
        $requiredFields = $this->custom->getRequiredFields($this->config->$moduleName);

        if($moduleName == 'doc')
        {
            unset($requiredFields['createlib']);
            unset($requiredFields['editlib']);
        }

        $this->view->title      = $this->lang->custom->required;
        $this->view->position[] = $this->lang->custom->required;

        $this->view->requiredFields = $requiredFields;
        $this->view->moduleName     = $moduleName;
        $this->display();
    }

    /**
     * Set score display switch
     *
     * @access public
     * @return void
     */
    public function score()
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem('system.common.global.scoreStatus', $this->post->score);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->custom->score;
        $this->view->position[] = $this->lang->custom->common;
        $this->view->position[] = $this->view->title;
        $this->display();
    }

    /**
     * Timezone.
     *
     * @access public
     * @return void
     */
    public function timezone()
    {
        if(strtolower($_SERVER['REQUEST_METHOD']) == "post")
        {
            $this->loadModel('setting')->setItems('system.common', fixer::input('post')->get());
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        unset($this->lang->admin->menu->custom['subModule']);
        $this->lang->admin->menu->system['subModule'] = 'custom';

        $this->view->title = $this->lang->custom->timezone;
        $this->view->position[] = $this->lang->custom->timezone;
        $this->display();
    }

    /**
     * Browse story concept.
     *
     * @access public
     * @return void
     */
    public function browseStoryConcept()
    {
        $this->view->title      = $this->lang->custom->browseStoryConcept;
        $this->view->position[] = $this->lang->custom->browseStoryConcept;
        $this->view->URSRList   = $this->custom->getURSRList();

        $this->display();
    }

    /**
     * Set story concept.
     *
     * @access public
     * @return void
     */
    public function setStoryConcept()
    {
        if($_POST)
        {
            $result = $this->custom->setURAndSR();
            if(!$result) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->URSREmpty));

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->title      = $this->lang->custom->setStoryConcept;
        $this->view->position[] = $this->lang->custom->setStoryConcept;

        $this->display();
    }

    /**
     * Edit story concept.
     * @param  int    $key
     *
     * @access public
     * @return void
     */
    public function editStoryConcept($key = 0)
    {
        if($_POST)
        {
            $result = $this->custom->updateURAndSR($key);
            if(!$result) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->notice->URSREmpty));

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $lang = $this->app->getClientLang();
        $URSR = $this->dao->select('`value`')->from(TABLE_LANG)
            ->where('lang')->eq($lang)
            ->andWhere('module')->eq('custom')
            ->andWhere('section')->eq('URSRList')
            ->andWhere('`key`')->eq($key)
            ->fetch('value');

        $this->view->URSR = json_decode($URSR);
        $this->display();
    }

    /**
     * Set story concept.
     *
     * @param  int   $key
     * @access public
     * @return void
     */
    public function setDefaultConcept($key = 0)
    {
        $this->loadModel('setting')->setItem('system.custom.URSR', $key);
        return print(js::reload('parent'));
    }

    /**
     * Delete story concept.
     *
     * @param  int    $key
     * @param  string $confirm yse|no
     * @access public
     * @return void
     */
    public function deleteStoryConcept($key = 0, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->custom->notice->confirmDelete, $this->createLink('custom', 'deleteStoryConcept', "key=$key&confirm=yes"), '');
            die;
        }
        else
        {
            $lang = $this->app->getClientLang();
            $this->custom->deleteItems("lang=$lang&section=URSRList&key=$key");

            $defaultConcept = $this->loadModel('setting')->getItem('owner=system&module=custom&key=URSR');
            $this->dao->update(TABLE_CONFIG)
                ->set('`value`')->eq($defaultConcept)
                ->where('module')->eq('common')
                ->andWhere('`key`')->eq('URSR')
                ->andWhere('`value`')->eq($key)
                ->exec();

            return print(js::locate(inlink('browseStoryConcept'), 'parent'));
        }
    }

    /**
     * Set whether the execution is read-only.
     *
     * @access public
     * @return void
     */
    public function execution()
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem("system.common.CRExecution@{$this->config->vision}", $this->post->execution);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->custom->execution;
        $this->view->position[] = $this->lang->custom->common;
        $this->view->position[] = $this->view->title;

        $this->display();
    }

    /**
     * Set whether the product is read-only.
     *
     * @access public
     * @return void
     */
    public function product()
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem('system.common.CRProduct', $this->post->product);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->custom->product;
        $this->view->position[] = $this->lang->custom->common;
        $this->view->position[] = $this->view->title;

        $this->display();
    }

    /**
     * Set flow.
     *
     * @access public
     * @return void
     */
    public function flow()
    {
        if($_POST)
        {
            $this->custom->setConcept();
            $this->loadModel('setting')->setItem('system.custom.URAndSR', $this->post->URAndSR);
            if($this->config->edition != 'max') $this->loadModel('setting')->setItem('system.custom.hourPoint', $this->post->hourPoint);

            $this->app->loadLang('common');
            $locate = $this->config->systemMode == 'new' ? inlink('flow') : 'top';

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $this->view->title      = $this->lang->custom->flow;
        $this->view->position[] = $this->lang->custom->flow;
        $this->display();
    }

    /**
     * Set Mode.
     *
     * @access public
     * @return void
     */
    public function mode()
    {
        $mode = zget($this->config->global, 'mode', 'classic');
        if($this->post->mode and $this->post->mode != $mode) // If mode value change.
        {
            $mode = fixer::input('post')->get('mode');
            $this->loadModel('setting')->setItem('system.common.global.mode', $mode);
            $this->setting->setItem('system.common.global.changedMode', 'yes');

            $sprintConcept = isset($this->config->custom->sprintConcept) ? $this->config->custom->sprintConcept : '0';
            if($mode == 'new')
            {
                if($sprintConcept == 2) $this->setting->setItem('system.custom.sprintConcept', 1);
                if($sprintConcept == 1) $this->setting->setItem('system.custom.sprintConcept', 0);
                return print(js::locate($this->createLink('upgrade', 'mergeTips'), 'parent'));
            }
            else
            {
                if($sprintConcept == 0) $this->setting->setItem('system.custom.sprintConcept', 1);
                if($sprintConcept == 1) $this->setting->setItem('system.custom.sprintConcept', 2);
                return print(js::reload('top'));
            }
        }

        if($mode == 'new')
        {
            if(isset($this->config->global->upgradeStep) and $this->config->global->upgradeStep == 'mergeProgram') return print(js::locate($this->createLink('upgrade', 'mergeProgram'), 'parent'));

            unset($_SESSION['upgrading']);
        }

        $this->app->loadLang('upgrade');

        $this->view->title       = $this->lang->custom->mode;
        $this->view->position[]  = $this->lang->custom->common;
        $this->view->position[]  = $this->view->title;
        $this->view->mode        = $mode;
        $this->view->changedMode = zget($this->config->global, 'changedMode', 'no');

        $this->display();
    }

    /**
     * Ajax save custom fields.
     *
     * @param  string $module
     * @param  string $section
     * @param  string $key
     * @access public
     * @return void
     */
    public function ajaxSaveCustomFields($module, $section, $key)
    {
        $account = $this->app->user->account;
        if($this->server->request_method == 'POST')
        {
            $fields  = $this->post->fields;
            if(is_array($fields)) $fields = join(',', $fields);
            $this->loadModel('setting')->setItem("$account.$module.$section.$key", $fields);
        }
        else
        {
            $this->loadModel('setting')->deleteItems("owner=$account&module=$module&section=$section&key=$key");
        }
        return print(js::reload('parent'));
    }

    /**
     * Custom menu view
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return void
     */
    public function ajaxMenu($module = 'main', $method = '')
    {
        $this->view->module = $module;
        $this->view->method = $method;
        $this->display();
    }

    /**
     * Ajax set menu
     *
     * @param  string $module
     * @param  string $method
     * @param  string $menus
     * @access public
     * @return void
     */
    public function ajaxSetMenu($module = 'main', $method = '', $menus = '')
    {
        if($_POST)
        {
            if(!empty($_POST['menus']))  $menus  = $_POST['menus'];
            if(!empty($_POST['module'])) $module = $_POST['module'];
            if(!empty($_POST['method'])) $method = $_POST['method'];
        }
        elseif(!empty($menus))
        {
            $menus = header::safe64Decode($menus);
        }

        if(empty($menus)) return $this->send(array('result' => 'fail', 'message' => $this->lang->custom->saveFail));

        if(is_array($menus))
        {
            foreach($menus as $menu)
            {
                $menu = json_decode($menu);
                $this->custom->saveCustomMenu($menu->value, $menu->module, isset($menu->method) ? $menu->method : '');
            }
        }
        else
        {
            $this->custom->saveCustomMenu($menus, $module, $method);
        }

        return $this->send(array('result' => 'success'));
    }

    /**
     * Ajax get menu
     *
     * @param  string $module
     * @param  string $method
     * @param  string $type
     * @access public
     * @return void
     */
    public function ajaxGetMenu($module = 'main', $method = '', $type = '')
    {
        if($this->config->global->flow == 'full')     $this->loadModel('execution')->setMenu(array(), 0);
        if($type === 'all')
        {
            $menu = array();
            $menu['main'] = customModel::getModuleMenu('main', true);
            if($method)
            {
                $this->app->loadLang($module);
                customModel::mergeFeatureBar($module, $method);
                /* Mark search query item. */
                if(isset($this->lang->$module->featureBar[$method]))
                {
                    foreach($this->lang->$module->featureBar[$method] as $barKey => $barValue)
                    {
                        if(strpos($barKey, 'QUERY') === 0)$this->lang->$module->featureBar[$method][$barKey] = "<i class='icon icon-search'></i> " . $barValue;
                    }
                }
            }
            if($module !== 'main')
            {
                $menu['module']  = array();
                $menu['feature'] = array();
                if(!isset($this->config->custom->noModuleMenu[$module]))
                {
                    $menu['module']  = customModel::getModuleMenu($module, true);
                    $menu['feature'] = customModel::getFeatureMenu($module, $method);
                }
                $menu['moduleName'] = $module;
                $menu['methodName'] = $method;
            }
        }
        else
        {
            $menu = !empty($method) ? customModel::getFeatureMenu($module, $method) : customModel::getModuleMenu($module, true);
        }
        return print(str_replace("'", '\u0027', json_encode(array('result' => $menu ? 'success' : 'fail', 'menu' => $menu))));
    }

    /**
     * Ajax restore menu.
     *
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function ajaxRestoreMenu($setPublic = 0, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->custom->confirmRestore, inlink('ajaxRestoreMenu', "setPublic=$setPublic&confirm=yes")));

        $account = $this->app->user->account;
        $this->loadModel('setting')->deleteItems("owner={$account}&module=common&section=customMenu");
        if($setPublic) $this->setting->deleteItems("owner=system&module=common&section=customMenu");
        return print(js::reload('parent.parent'));
    }

    /**
     * Ajax set doc setting.
     *
     * @access public
     * @return void
     */
    public function ajaxSetDoc()
    {
        if($this->server->request_method == 'POST')
        {
            $data = fixer::input('post')->join('showLibs', ',')->get();
            if(isset($data->showLibs)) $data = $data->showLibs;
            $this->loadModel('setting')->setItem("{$this->app->user->account}.doc.custom.showLibs", $data);
            return print(js::reload('parent'));
        }
    }

    /**
     * Reset required.
     *
     * @param  srting $module
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function resetRequired($module, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->custom->confirmRestore, inlink('resetRequired', "module=$module&confirm=yes")));

        $this->loadModel('setting')->deleteItems("owner=system&module={$module}&key=requiredFields");
        return print(js::reload('parent.parent'));
    }
}
