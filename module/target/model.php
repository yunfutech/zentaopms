<?php

class targetModel extends model
{
    public function getCategories()
    {
        return $this->dao->select('*')->from(TABLE_TARGET_CATEGORY)->fetchAll();
    }

    public function getCategoryById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_CATEGORY)->where('id')->eq($id)->fetch();
    }

    public function addCategory($name)
    {
        $category = new stdClass();
        $category->name = $name;
        return $this->dao->insert(TABLE_TARGET_CATEGORY)->data($category)->exec();
    }

    public function editCategory($id, $name)
    {
        return $this->dao->update(TABLE_TARGET_CATEGORY)->set('name')->eq($name)->where('id')->eq($id)->exec();
    }

    public function deleteCategory($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_CATEGORY)->where('id')->eq($id)->exec();
    }

    public function getDatasets()
    {
        return $this->dao->select('*')->from(TABLE_TARGET_DATASET)->fetchAll();
    }

    public function getDatasetById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_DATASET)->where('id')->eq($id)->fetch();
    }

    public function addDataset($params)
    {
        $dataset = new stdClass();
        $dataset->name = $params['name'];
        $dataset->source = $params['source'];
        $dataset->size = $params['size'];
        return $this->dao->insert(TABLE_TARGET_DATASET)->data($dataset)->exec();
    }

    public function editDataset($id, $data)
    {
        return $this->dao->update(TABLE_TARGET_DATASET)->data($data)->where('id')->eq($id)->exec();
    }

    public function deleteDataset($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_DATASET)->where('id')->eq($id)->exec();
    }


    public function getModules($project_id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_MODULE)->where('pid')->eq($project_id)->fetchAll();
    }

    public function getModuleById($id)
    {
        $module = $this->dao->select('*')->from(TABLE_TARGET_MODULE)->where('id')->eq($id)->fetch();
        $category = $this->getCategoryById($module->cid);
        $module->category = $category;
        return $module;
    }

    public function addModule($data, $project_id)
    {
        $module = new stdClass();
        $module->cid = $data['category'];
        $module->name = $data['name'];
        $module->pid = $project_id;
        $this->dao->insert(TABLE_TARGET_MODULE)->data($module)->exec();
        return $this->dao->lastInsertID();
    }

    public function editModule($id, $data)
    {
        return $this->dao->update(TABLE_TARGET_MODULE)->data($data)->where('id')->eq($id)->exec();
    }

    public function deleteModule($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_MODULE)->where('id')->eq($id)->exec();
    }

    public function getPerformanceById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_PERFORMANCE)->where('id')->eq($id)->fetch();
    }

    public function addPerformance($data)
    {
        $performance = new stdClass();
        $performance->precision_ = $data['precision'];
        $performance->recall = $data['recall'];
        $performance->f1 = $data['f1'];
        $this->dao->insert(TABLE_TARGET_PERFORMANCE)->data($performance)->exec();
        return $this->dao->lastInsertID();
    }

    public function editPerformance($id, $data)
    {
        $performance = new stdClass();
        $performance->precision_ = $data['precision'];
        $performance->recall = $data['recall'];
        $performance->f1 = $data['f1'];
        return $this->dao->update(TABLE_TARGET_PERFORMANCE)->data($performance)->where('id')->eq($id)->exec();
    }

    public function addTarget($data)
    {
        $target = new stdClass();
        $target->deadline = $data['deadline'];
        $target->pid = $data['pid'];
        $this->dao->insert(TABLE_TARGET_TARGET)->data($target)->exec();
        return $this->dao->lastInsertID();
    }

    public function getTargetById($id)
    {
        $target = $this->dao->select('*')->from(TABLE_TARGET_TARGET)->where('id')->eq($id)->fetch();
        $pid = $target->pid;
        $performance = $this->getPerformanceById($pid);
        $target->performance = $performance;
        return $target;
    }

    public function editTarget($id, $data)
    {
        $target = $this->dao->select('*')->from(TABLE_TARGET_TARGET)->where('id')->eq($id)->fetchAll();
        $pid = $target->pid;
        $this->editPerformance($pid, $data);
        $this->dao->update(TABLE_TARGET_TARGET)->set('time')->eq($data['time'])->where('id')->eq($id)->exec();
    }

    public function addRecord($data, $eid)
    {
        $record = new stdClass();
        $record->time = $data['time'];
        $record->solution = $data['solution'];
        $pid = $this->addPerformance($data);
        $record->pid = $pid;
        $this->dao->insert(TABLE_TARGET_RECORD)->data($record)->exec();
        $rid = $this->dao->lastInsertID();
        $experiment = $this->getExperimentByID($eid);
        $rids = explode(',', $experiment->rid);
        array_push($rids, $rid);
        $res = $this->addRid($rids, $experiment->id);
        return $res;
    }

    public function getRecord($ids)
    {
        $records = $this->dao->select('*')->from(TABLE_TARGET_RECORD)->where('id')->in($ids)->fetchAll();
        foreach($records as $record) {
            $performance = $this->getPerformanceById($record->pid);
            $record->performance = $performance;
        }
        return $records;
    }

    public function editRecord($id, $data)
    {
        $record = $this->dao->select('*')->from(TABLE_TARGET_RECORD)->where('id')->eq($id)->fetchAll();
        $pid = $record->pid;
        $performance = new stdClass();
        $performance->precision = $data['precision'];
        $performance->recall = $data['recall'];
        $performance->f1 = $data['f1'];
        $this->editPerformance($pid, $performance);
        $this->dao->update(TABLE_TARGET_RECORD)->set('time')->eq($data['time'])->set('solution')->eq($data['solution'])->where('id')->eq($id)->exec();
    }

    public function deleteRecord($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_RECORD)->where('id')->eq($id)->exec();
    }

    public function getExperiment($project_id)
    {
        $modules = $this->getModules($project_id);
        $ids = [];
        foreach ($modules as $module) {
            array_push($ids, $module->id);
        }
        $experiments = $this->dao->select('*')->from(TABLE_TARGET_EXPERIMENT)->where('mid')->in($ids)->fetchAll();
        foreach ($experiments as $experiment) {
            $experiment->module = $this->getModuleById($experiment->mid);
            $experiment->dataset = $this->getDatasetById($experiment->did);
            $experiment->target = $this->getTargetById($experiment->tid);
            $rids = explode(',', $experiment->rid);
            $experiment->record = $this->getRecord($rids);
        }
        return $experiments;
    }

    public function getExperimentById($id)
    {
        $experiments = $this->dao->select('*')->from(TABLE_TARGET_EXPERIMENT)->where('id')->eq($id)->fetch();
        return $experiments;
    }

    public function deleteExperiment($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_EXPERIMENT)->where('id')->eq($id)->exec();
    }

    public function addRid($ids, $id)
    {
        $rid = join(',', $ids);
        $res = $this->dao->update(TABLE_TARGET_EXPERIMENT)->set('rid')->eq($rid)->where('id')->eq($id)->exec();
        return $res;
    }

    public function addExperiment($data)
    {
        $mid = $data['module'];
        $did = $data['dataset'];
        $pid = $this->addPerformance($data);
        $data['pid'] = $pid;
        $tid = $this->addTarget($data);
        $experiment = new stdClass();
        $experiment->mid = $mid;
        $experiment->did = $did;
        $experiment->tid = $tid;
        $this->dao->insert(TABLE_TARGET_EXPERIMENT)->data($experiment)->exec();
        return $this->dao->lastInsertID();
    }
}