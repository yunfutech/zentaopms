<?php

class target extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
    }

    public function create($projectID='')
    {
        $this->loadModel('project');
        $project   = $this->project->getById($projectID);
        $projectID = $project->id;

        /* Header and position. */
        $title      = $project->name . $this->lang->colon . $this->lang->project->target;
        $position[] = html::a($this->createLink('project', 'browse', "projectID=$projectID"), $project->name);
        $position[] = $this->lang->project->target;

        $this->view->title       = $title;
        $this->view->position    = $position;
        $this->view->projectID   = $projectID;
        $this->view->projectName = $project->name;

        $this->display();
    }

    public function category()
    {
        $this->display();
    }

    public function dataset()
    {
        $this->display();
    }

    public function module($projectID=0)
    {
        if(!empty($_POST))
        {
            $res = $this->target->addExperiment($_POST, $projectID);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate'] = $this->createLink('project', 'target', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $category_names = [];
        $category_ids = [];
        $dataset_names = [];
        $dataset_ids = [];
        $categories = $this->target->getCategories();
        $datasets = $this->target->getDatasets();
        foreach ($categories as $category) {
            array_push($category_names, $category->name);
            array_push($category_ids, $category->id);
        }
        foreach ($datasets as $dataset) {
            array_push($dataset_names, $dataset->name);
            array_push($dataset_ids, $dataset->id);
        }
        $this->view->category_names = $category_names;
        $this->view->category_ids = $category_ids;
        $this->view->dataset_names = $dataset_names;
        $this->view->dataset_ids = $dataset_ids;
        $this->display();
    }

    public function record($projectID=0)
    {
        if(!empty($_POST))
        {
            $res = $this->target->addRecord($_POST);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate'] = $this->createLink('project', 'target', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $module_names = [];
        $module_ids = [];
        $experiments =$this->target->getExperiment($projectID);
        foreach ($experiments as $experimen) {
            array_push($module_names, $this->target->getModuleById($experimen->mid)->name);
            array_push($module_ids, $this->target->getModuleById($experimen->mid)->id);
        }
        $this->view->module_names = $module_names;
        $this->view->module_ids = $module_ids;
        $this->display();
    }
}