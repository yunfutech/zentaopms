<?php

class target extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
    }

    public function category($projectID)
    {
        $categories = $this->target->getCategories();
        $this->view->categories = $categories;
        $this->view->projectID = $projectID;
        $this->display();
    }

    public function createCategory($projectID)
    {
        if (!empty($_POST)) {
            $res = $this->target->addCategory($_POST['name']);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate'] = $this->createLink('target', 'category', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $this->display();
    }

    public function editCategory($projectID, $categoryID)
    {
        if (!empty($_POST)) {
            $res = $this->target->editCategory($categoryID, $_POST['name']);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = '更新成功';
                $response['locate'] = $this->createLink('target', 'category', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $name = $this->target->getCategoryById($categoryID)->name;
        $this->view->name = $name;
        $this->display();
    }

    public function deleteCategory($projectID, $categoryID)
    {
        $res = $this->target->deleteCategory($categoryID);
        if ($res) {
            $response['result']  = 'success';
            $response['message'] = '删除成功';
            $response['locate'] = $this->createLink('target', 'category', "projectID=$projectID");
            $this->send($response);
        } else {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    public function dataset($projectID)
    {
        $datasets = $this->target->getDatasets();
        $this->view->datasets = $datasets;
        $this->view->projectID = $projectID;
        $this->display();
    }

    public function createDataset($projectID)
    {
        if (!empty($_POST)) {
            $res = $this->target->addDataset($_POST);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate'] = $this->createLink('target', 'dataset', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $this->display();
    }

    public function editDataset($projectID, $datasetID)
    {
        if (!empty($_POST)) {
            $res = $this->target->editDataset($datasetID, $_POST);
            if ($res) {
                $response['result']  = 'success';
                $response['message'] = '更新成功';
                $response['locate'] = $this->createLink('target', 'dataset', "projectID=$projectID");
                $this->send($response);
            } else {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
        }
        $dataset = $this->target->getDatasetById($datasetID);
        $this->view->dataset = $dataset;
        $this->display();
    }

    public function deleteDataset($projectID, $datasetID)
    {
        $res = $this->target->deleteDataset($datasetID);
        if ($res) {
            $response['result']  = 'success';
            $response['message'] = '删除成功';
            $response['locate'] = $this->createLink('target', 'dataset', "projectID=$projectID");
            $this->send($response);
        } else {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    public function module($projectID)
    {
        if (!empty($_POST))
        {
            $res = $this->target->addModule($_POST, $projectID);
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
        $categories = $this->target->getCategories();
        foreach ($categories as $category) {
            $view_categories[$category->id] = $category->name;
        }
        $this->view->categories = $view_categories;
        $this->display();
    }

    public function experiment($projectID)
    {
        if(!empty($_POST))
        {
            $res = $this->target->addExperiment($_POST);
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
        $view_categories = [];
        $view_datasets = [];
        $view_modules = [];
        $categories = $this->target->getCategories();
        $datasets = $this->target->getDatasets();
        $modules = $this->target->getModules($projectID);
        foreach ($categories as $category) {
            $view_categories[$category->id] = $category->name;
        }
        foreach ($datasets as $dataset) {
            $view_datasets[$dataset->id] = $dataset->name;
        }
        foreach ($modules as $module) {
            $view_modules[$module->id] = $module->name;
        }
        $this->view->categories = $view_categories;
        $this->view->datasets = $view_datasets;
        $this->view->modules = $view_modules;
        $this->display();
    }

    public function editExperiment($projectID, $experimentID)
    {
        if(!empty($_POST))
        {
            $res = $this->target->addExperiment($_POST);
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
        $view_categories = [];
        $view_datasets = [];
        $view_modules = [];
        $categories = $this->target->getCategories();
        $datasets = $this->target->getDatasets();
        $modules = $this->target->getModules($projectID);
        foreach ($categories as $category) {
            $view_categories[$category->id] = $category->name;
        }
        foreach ($datasets as $dataset) {
            $view_datasets[$dataset->id] = $dataset->name;
        }
        foreach ($modules as $module) {
            $view_modules[$module->id] = $module->name;
        }
        $this->view->categories = $view_categories;
        $this->view->datasets = $view_datasets;
        $this->view->modules = $view_modules;
        $this->display();
    }

    public function record($projectID, $experimentID)
    {
        if(!empty($_POST))
        {
            $res = $this->target->addRecord($_POST, $experimentID);
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

        $this->display();
    }
}