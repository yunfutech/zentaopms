<?php

class targetModel extends model
{
    // 获取全部类别
    public function getCategories()
    {
        return $this->dao->select('*')->from(TABLE_TARGET_CATEGORY)->fetchAll();
    }

    public function getCategoriesByProjectID($project_id)
    {
        return $this->dao->select('t2.id, t2.name')->from(TABLE_TARGET_EXPERIMENT)->alias('t1')->leftJoin(TABLE_TARGET_CATEGORY)->alias('t2')->on('t1.cid = t2.id')->where('t1.pid')->eq($project_id)->fetchAll();
    }

    // 通过id获取类别
    public function getCategoryById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_CATEGORY)->where('id')->eq($id)->fetch();
    }

    // 添加类别
    public function addCategory($name)
    {
        $category = new stdClass();
        $category->name = $name;
        return $this->dao->insert(TABLE_TARGET_CATEGORY)->data($category)->exec();
    }

    // 编辑类别
    public function editCategory($id, $name)
    {
        return $this->dao->update(TABLE_TARGET_CATEGORY)->set('name')->eq($name)->where('id')->eq($id)->exec();
    }

    // 删除类别
    public function deleteCategory($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_CATEGORY)->where('id')->eq($id)->exec();
    }

    // 获取全部数据集
    public function getDatasets()
    {
        return $this->dao->select('*')->from(TABLE_TARGET_DATASET)->fetchAll();
    }

    public function getDatasetsByProjectID($project_id)
    {
        return $this->dao->select('t2.id, t2.name')->from(TABLE_TARGET_EXPERIMENT)->alias('t1')->leftJoin(TABLE_TARGET_DATASET)->alias('t2')->on('t1.did = t2.id')->where('t1.pid')->eq($project_id)->fetchAll();
    }

    // 通过id获取数据集
    public function getDatasetById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_DATASET)->where('id')->eq($id)->fetch();
    }

    // 添加数据集
    public function addDataset($params)
    {
        $dataset = new stdClass();
        $dataset->name = $params['name'];
        $dataset->source = $params['source'];
        $dataset->type = $params['type'] == 1? '测试集' : '开发集';
        $dataset->size = $params['size'];
        return $this->dao->insert(TABLE_TARGET_DATASET)->data($dataset)->exec();
    }

    // 编辑数据集
    public function editDataset($id, $data)
    {
        $data['type'] = $data['type'] == 1? '测试集' : '开发集';
        return $this->dao->update(TABLE_TARGET_DATASET)->data($data)->where('id')->eq($id)->exec();
    }

    // 删除数据集
    public function deleteDataset($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_DATASET)->where('id')->eq($id)->exec();
    }

    // 通过id获取Performance
    public function getPerformanceById($id)
    {
        return $this->dao->select('*')->from(TABLE_TARGET_PERFORMANCE)->where('id')->eq($id)->fetch();
    }


    // 添加Performance
    public function addPerformance($data)
    {
        $performance = new stdClass();
        $performance->precision_ = $data['precision'];
        $performance->recall = $data['recall'];
        $performance->f1 = $data['f1'];
        $this->dao->insert(TABLE_TARGET_PERFORMANCE)->data($performance)->exec();
        return $this->dao->lastInsertID();
    }

    // 编辑Performance
    public function editPerformance($id, $data)
    {
        return $this->dao->update(TABLE_TARGET_PERFORMANCE)->data($data)->where('id')->eq($id)->exec();
    }

    // 删除Performance
    public function deletePerformance($id)
    {
        return $this->dao->delete()->from(TABLE_TARGET_PERFORMANCE)->where('id')->eq($id)->exec();
    }

    // 添加目标
    public function addTarget($data)
    {
        $target = new stdClass();
        $target->deadline = $data['deadline'];
        $target->pid = $data['pid'];
        $this->dao->insert(TABLE_TARGET_TARGET)->data($target)->exec();
        return $this->dao->lastInsertID();
    }

    // 通过id获取目标
    public function getTargetById($id)
    {
        $target = $this->dao->select('*')->from(TABLE_TARGET_TARGET)->where('id')->eq($id)->fetch();
        $pid = $target->pid;
        $performance = $this->getPerformanceById($pid);
        $target->performance = $performance;
        return $target;
    }

    // 编辑目标
    public function editTarget($id, $data)
    {
        $target = $this->dao->select('*')->from(TABLE_TARGET_TARGET)->where('id')->eq($id)->fetchAll();
        $pid = $target->pid;
        $this->editPerformance($pid, $data);
        $this->dao->update(TABLE_TARGET_TARGET)->set('time')->eq($data['time'])->where('id')->eq($id)->exec();
    }

    // 删除目标
    public function deleteTarget($id)
    {
        $pid = $this->getTargetById($id)->pid;
        $this->dao->delete()->from(TABLE_TARGET_TARGET)->where('id')->eq($id)->exec();
        $this->deletePerformance($pid);
        return true;
    }

    // 添加记录
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

    // 获取实验全部记录
    public function getRecord($ids)
    {
        $records = $this->dao->select('*')->from(TABLE_TARGET_RECORD)->where('id')->in($ids)->orderBy('time desc')->fetchAll();
        foreach($records as $record) {
            $performance = $this->getPerformanceById($record->pid);
            $record->performance = $performance;
        }
        return $records;
    }

    // 通过id获取记录
    public function getRecordById($id)
    {
        $record = $this->dao->select('*')->from(TABLE_TARGET_RECORD)->where('id')->eq($id)->fetch();
        $performance = $this->getPerformanceById($record->pid);
        $record->performance = $performance;
        return $record;
    }

    // 编辑记录
    public function editRecord($data, $id)
    {
        $record = $this->dao->select('*')->from(TABLE_TARGET_RECORD)->where('id')->eq($id)->fetch();
        $pid = $record->pid;
        $performance = [];
        $performance['precision_'] = $data['precision_'];
        $performance['recall'] = $data['recall'];
        $performance['f1'] = $data['f1'];
        $this->editPerformance($pid, $performance);
        $this->dao->update(TABLE_TARGET_RECORD)->set('time')->eq($data['time'])->set('solution')->eq($data['solution'])->where('id')->eq($id)->exec();
        return true;
    }

    // 删除记录
    public function deleteRecord($id)
    {
        $pid = $this->getRecordById($id)->pid;
        $this->dao->delete()->from(TABLE_TARGET_RECORD)->where('id')->eq($id)->exec();
        $this->deletePerformance($pid);
        return true;
    }

    // 获取实验
    public function getExperiment($project_id, $category_id)
    {
        $experiments = $this->dao->select('*')
            ->from(TABLE_TARGET_EXPERIMENT)
            ->where('pid')->eq($project_id)
            ->beginIF(intval($category_id) !== 0)->andWhere('cid')->eq($category_id)->fi()
            ->orderBy('id desc')
            ->fetchAll();
        foreach ($experiments as $experiment) {
            $experiment->category = $this->getCategoryById($experiment->cid);
            $experiment->dataset = $this->getDatasetById($experiment->did);
            $experiment->target = $this->getTargetById($experiment->tid);
            $rids = explode(',', $experiment->rid);
            $experiment->record = $this->getRecord($rids);
        }
        return $experiments;
    }

    // 通过id获取实验
    public function getExperimentById($id)
    {
        $experiments = $this->dao->select('*')->from(TABLE_TARGET_EXPERIMENT)->where('id')->eq($id)->fetch();
        return $experiments;
    }

    // 编辑实验
    public function editExperiment($data, $id)
    {
        $old_tid = $this->getExperimentById($id)->tid;
        $pid = $this->addPerformance($data);
        $data['pid'] = $pid;
        $tid = $this->addTarget($data);
        $set = ['cid' => $data['category'], 'did' => $data['dataset'], 'tid' => $tid];
        $this->dao->update(TABLE_TARGET_EXPERIMENT)->data($set)->where('id')->eq($id)->exec();
        return $this->deleteTarget($old_tid);
    }

    // 删除实验
    public function deleteExperiment($id)
    {
        $experiment = $this->getExperimentById($id);
        $tid = $experiment->tid;
        $rids = explode($experiment->rid, ',');
        $this->dao->delete()->from(TABLE_TARGET_EXPERIMENT)->where('id')->eq($id)->exec();
        $this->deleteTarget($tid);
        foreach ($rids as $rid) {
            if ($rid) {
                $this->deleteRecord($rid);
            }
        }
        return true;
    }

    // 添加记录id
    public function addRid($ids, $id)
    {
        $rid = join(',', $ids);
        $res = $this->dao->update(TABLE_TARGET_EXPERIMENT)->set('rid')->eq($rid)->where('id')->eq($id)->exec();
        return $res;
    }

    // 添加实验
    public function addExperiment($post, $project_id)
    {
        $cid = $post['category'];
        $did = $post['dataset'];
        $pid = $this->addPerformance($post);
        $post['pid'] = $pid;
        $tid = $this->addTarget($post);
        $experiment = new stdClass();
        $experiment->cid = $cid;
        $experiment->did = $did;
        $experiment->tid = $tid;
        $experiment->pid = $project_id;
        $this->dao->insert(TABLE_TARGET_EXPERIMENT)->data($experiment)->exec();
        return $this->dao->lastInsertID();
    }
}