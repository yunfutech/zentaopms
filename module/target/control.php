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
        $this->view->projectID = $projectID;
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

    public function experiment($projectID)
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
        $view_categories = [];
        $view_datasets = [];
        $categories = $this->target->getCategories();
        $datasets = $this->target->getDatasets();
        foreach ($categories as $category) {
            $view_categories[$category->id] = $category->name;
        }
        foreach ($datasets as $dataset) {
            $view_datasets[$dataset->id] = $dataset->name;
        }
        $this->view->projectID = $projectID;
        $this->view->categories = $view_categories;
        $this->view->datasets = $view_datasets;
        $this->display();
    }

    public function editExperiment($projectID, $experimentID)
    {
        if(!empty($_POST))
        {
            $res = $this->target->editExperiment($_POST, $experimentID);
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
        $datasets = $this->target->getDatasets();
        foreach ($datasets as $dataset) {
            $view_datasets[$dataset->id] = $dataset->name;
        }
        $categories = $this->target->getCategories();
        foreach ($categories as $category) {
            $view_categories[$category->id] = $category->name;
        }

        $experiment = $this->target->getExperimentById($experimentID);
        $target = $this->target->getTargetById($experiment->tid);

        $this->view->categories = $view_categories;
        $this->view->datasets = $view_datasets;
        $this->view->experiment = $experiment;
        $this->view->target = $target;
        $this->display();
    }

    public function deleteExperiment($projectID, $experimentID)
    {
        $res = $this->target->deleteExperiment($experimentID);
        if ($res) {
            $response['result']  = 'success';
            $response['message'] = '删除成功';
            $response['locate'] = $this->createLink('project', 'target', "projectID=$projectID");
            $this->send($response);
        } else {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
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

    public function editRecord($projectID, $recordID)
    {
        if(!empty($_POST))
        {
            $res = $this->target->editRecord($_POST, $recordID);
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

        $this->view->record = $this->target->getRecordById($recordID);
        $this->display();
    }

    public function deleteRecord($projectID, $recordID)
    {
        $res = $this->target->deleteRecord($recordID);
        if ($res) {
            $response['result']  = 'success';
            $response['message'] = '删除成功';
            $response['locate'] = $this->createLink('project', 'target', "projectID=$projectID");
            $this->send($response);
        } else {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }
}

// 更新experiment表cid替换mid，增加pid
// update zt_target_experiment as t1 set t1.cid = (select t2.cid from zt_target_module as t2 where t2.id = t1.mid), t1.pid = (select t2.pid from zt_target_module as t2 where t2.id = t1.mid)