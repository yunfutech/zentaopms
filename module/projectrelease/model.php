<?php
/**
 * The model file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php
class projectreleaseModel extends model
{
    /**
     * Get release by id.
     *
     * @param  int    $releaseID
     * @param  bool   $setImgSize
     * @access public
     * @return object
     */
    public function getByID($releaseID, $setImgSize = false)
    {
        $release = $this->dao->select('t1.*, t2.id as buildID, t2.filePath, t2.scmPath, t2.name as buildName, t2.execution, t3.name as productName, t3.type as productType')
            ->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.build = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->where('t1.id')->eq((int)$releaseID)
            ->orderBy('t1.id DESC')
            ->fetch();
        if(!$release) return false;

        $this->loadModel('file');
        $release = $this->file->replaceImgURL($release, 'desc');
        $release->files = $this->file->getByObject('release', $releaseID);
        if(empty($release->files))$release->files = $this->file->getByObject('build', $release->buildID);
        if($setImgSize) $release->desc = $this->file->setImgSize($release->desc);
        return $release;
    }

    /**
     * Get list of releases.
     *
     * @param  int    $projectID
     * @param  string $type
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getList($projectID, $type = 'all', $orderBy = 't1.date_desc')
    {
        return $this->dao->select('t1.*, t2.name as productName, t3.id as buildID, t3.name as buildName, t3.execution, t4.name as executionName')
            ->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_BUILD)->alias('t3')->on('t1.build = t3.id')
            ->leftJoin(TABLE_EXECUTION)->alias('t4')->on('t3.execution = t4.id')
            ->where('t1.project')->eq((int)$projectID)
            ->beginIF($type != 'all')->andWhere('t1.status')->eq($type)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * Get last release.
     *
     * @param  int    $projectID
     * @access public
     * @return bool | object
     */
    public function getLast($projectID)
    {
        return $this->dao->select('id, name')->from(TABLE_RELEASE)
            ->where('project')->eq((int)$projectID)
            ->orderBy('date DESC')
            ->limit(1)
            ->fetch();
    }

    /**
     * Get released builds from project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getReleasedBuilds($projectID)
    {
        $releases = $this->dao->select('build')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('project')->eq($projectID)
            ->fetchAll('build');
        return array_keys($releases);
    }

    /**
     * Create a release.
     *
     * @access public
     * @return int
     */
    public function create($projectID = 0)
    {
        /* Init vars. */
        $productID = $this->post->product;
        $branch    = $this->post->branch;
        $buildID   = 0;
        $projectID = $projectID ? $projectID : $this->session->project;

        /* Check build if build is required. */
        if(strpos($this->config->release->create->requiredFields, 'build') !== false and $this->post->build == false) return dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->release->build);

        /* Check date must be not more than today. */
        if($this->post->date > date('Y-m-d')) return dao::$errors[] = $this->lang->release->errorDate;

        if($this->post->build)
        {
            $build     = $this->loadModel('build')->getByID($this->post->build);
            $productID = $build->product;
            $branch    = $build->branch;
        }

        $release = fixer::input('post')
            ->add('project', $projectID)
            ->add('product', (int)$productID)
            ->add('branch',  (int)$branch)
            ->setDefault('stories', '')
            ->join('stories', ',')
            ->join('bugs', ',')
            ->join('mailto', ',')
            ->setIF($this->post->build == false, 'build', $buildID)
            ->setIF($productID, 'product', $productID)
            ->setIF($branch, 'branch', $branch)
            ->stripTags($this->config->release->editor->create['id'], $this->config->allowedTags)
            ->remove('allchecker,files,labels,uid,sync')
            ->get();

        /* Auto create build when release is not link build. */
        if(empty($release->build) and $release->name)
        {
            $build = $this->dao->select('*')->from(TABLE_BUILD)
                ->where('deleted')->eq('0')
                ->andWhere('name')->eq($release->name)
                ->andWhere('product')->eq($productID)
                ->andWhere('branch')->eq($branch)
                ->fetch();
            if($build)
            {
                return dao::$errors['build'] = sprintf($this->lang->release->existBuild, $release->name);
            }
            else
            {
                $build = new stdclass();
                $build->project   = $projectID;
                $build->product   = (int)$productID;
                $build->branch    = (int)$branch;
                $build->name      = $release->name;
                $build->date      = $release->date;
                $build->builder   = $this->app->user->account;
                $build->desc      = $release->desc;
                $build->execution = 0;

                $build = $this->loadModel('file')->processImgURL($build, $this->config->release->editor->create['id']);
                $this->dao->insert(TABLE_BUILD)->data($build)
                    ->autoCheck()
                    ->check('name', 'unique', "product = '{$productID}' AND branch = '{$branch}' AND deleted = '0'")
                    ->batchCheck('name', 'notempty')
                    ->exec();
                if(dao::isError()) return false;

                $buildID = $this->dao->lastInsertID();
                $release->build = $buildID;
            }
        }

        if($release->build)
        {
            $buildInfo = $this->dao->select('branch, stories, bugs')->from(TABLE_BUILD)->where('id')->eq($release->build)->fetch();
            $release->branch = $buildInfo->branch;
            if($this->post->sync == 'true')
            {
                $release->stories = $buildInfo->stories;
                $release->bugs    = $buildInfo->bugs;
            }
        }

        $release = $this->loadModel('file')->processImgURL($release, $this->config->release->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_RELEASE)->data($release)
            ->autoCheck()
            ->batchCheck($this->config->release->create->requiredFields, 'notempty')
            ->check('name', 'unique', "product = {$release->product} AND branch = {$release->branch} AND deleted = '0'");

        if(dao::isError())
        {
            if(!empty($buildID)) $this->dao->delete()->from(TABLE_BUILD)->where('id')->eq($buildID)->exec();
            return false;
        }

        $this->dao->exec();

        if(dao::isError())
        {
            if(!empty($buildID)) $this->dao->delete()->from(TABLE_BUILD)->where('id')->eq($buildID)->exec();
        }
        else
        {
            $releaseID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $releaseID, 'release');
            $this->file->saveUpload('release', $releaseID);
            $this->loadModel('score')->create('release', 'create', $releaseID);

            /* Set stage to released. */
            if($release->stories)
            {
                $this->loadModel('story');
                $this->loadModel('action');

                $storyIDList = array_filter(explode(',', $release->stories));
                foreach($storyIDList as $storyID)
                {
                    $this->story->setStage($storyID);

                    $this->action->create('story', $storyID, 'linked2release', '', $releaseID);
                }
            }

            return $releaseID;
        }

        return false;
    }

    /**
     * Update a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function update($releaseID)
    {
        /* Init vars. */
        $releaseID  = (int)$releaseID;
        $oldRelease = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->eq($releaseID)->fetch();
        $branch     = $this->dao->select('branch')->from(TABLE_BUILD)->where('id')->eq((int)$this->post->build)->fetch('branch');

        $release = fixer::input('post')->stripTags($this->config->release->editor->edit['id'], $this->config->allowedTags)
            ->add('branch',  (int)$branch)
            ->join('mailto', ',')
            ->setIF(!$this->post->marker, 'marker', 0)
            ->cleanInt('product')
            ->remove('files,labels,allchecker,uid')
            ->get();

        $release = $this->loadModel('file')->processImgURL($release, $this->config->release->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_RELEASE)->data($release)
            ->autoCheck()
            ->batchCheck($this->config->release->edit->requiredFields, 'notempty')
            ->check('name', 'unique', "id != '$releaseID' AND product = '{$release->product}' AND branch = '$branch' AND deleted = '0'")
            ->where('id')->eq((int)$releaseID)
            ->exec();
        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $releaseID, 'release');
            return common::createChanges($oldRelease, $release);
        }
    }

    /**
     * Link stories
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function linkStory($releaseID)
    {
        $release = $this->getByID($releaseID);
        $product = $this->loadModel('product')->getByID($release->product);

        foreach($this->post->stories as $i => $storyID)
        {
            if(strpos(",{$release->stories},", ",{$storyID},") !== false) unset($_POST['stories'][$i]);
        }

        $release->stories .= ',' . join(',', $this->post->stories);
        $this->dao->update(TABLE_RELEASE)->set('stories')->eq($release->stories)->where('id')->eq((int)$releaseID)->exec();

        if($release->stories)
        {
            $this->loadModel('story');
            $this->loadModel('action');
            foreach($this->post->stories as $storyID)
            {
                /* Reset story stagedBy field for auto compute stage. */
                $this->dao->update(TABLE_STORY)->set('stagedBy')->eq('')->where('id')->eq($storyID)->exec();
                if($product->type != 'normal') $this->dao->update(TABLE_STORYSTAGE)->set('stagedBy')->eq('')->where('story')->eq($storyID)->andWhere('branch')->eq($release->branch)->exec();

                $this->story->setStage($storyID);

                $this->action->create('story', $storyID, 'linked2release', '', $releaseID);
            }
        }
    }

    /**
     * Unlink story
     *
     * @param  int    $releaseID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory($releaseID, $storyID)
    {
        $release = $this->getByID($releaseID);
        $release->stories = trim(str_replace(",$storyID,", ',', ",$release->stories,"), ',');
        $this->dao->update(TABLE_RELEASE)->set('stories')->eq($release->stories)->where('id')->eq((int)$releaseID)->exec();
        $this->loadModel('action')->create('story', $storyID, 'unlinkedfromrelease', '', $releaseID);
    }

    /**
     * Link bugs.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @access public
     * @return void
     */
    public function linkBug($releaseID, $type = 'bug')
    {
        $release = $this->getByID($releaseID);

        $field = $type == 'bug' ? 'bugs' : 'leftBugs';

        foreach($this->post->bugs as $i => $bugID)
        {
            if(strpos(",{$release->$field},", ",{$bugID},") !== false) unset($_POST['bugs'][$i]);
        }

        $release->$field .= ',' . join(',', $this->post->bugs);
        $this->dao->update(TABLE_RELEASE)->set($field)->eq($release->$field)->where('id')->eq((int)$releaseID)->exec();

        $this->loadModel('action');
        foreach($this->post->bugs as $bugID) $this->action->create('bug', $bugID, 'linked2release', '', $releaseID);
    }

    /**
     * Judge btn is clickable or not.
     *
     * @param  int    $release
     * @param  string $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($release, $action)
    {
        $action = strtolower($action);

        if($action == 'notify') return $release->bugs or $release->stories;
        return true;
    }
}
