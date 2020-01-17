<?php

/**
 * Get user year logins.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return int
 */
public function getUserYearLogins($account, $year)
{
    return $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where('actor')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->andWhere('action')->eq('login')->fetch('count');
}

/**
 * Get user year actions.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return int
 */
public function getUserYearActions($account, $year)
{
    return $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where('actor')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->fetch('count');
}

/**
 * Get user year efforts.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return object
 */
public function getUserYearEfforts($account, $year)
{
    return $this->dao->select('count(*) as count, sum(consumed) as consumed')->from(TABLE_TASKESTIMATE)->where('account')->eq($account)->andWhere('LEFT(date, 4)')->eq($year)->fetch();
}

/**
 * Get user year story.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearStory($products, $account, $year)
{
    $stories = $this->dao->select('*')->from(TABLE_STORY)->where('openedBy')->eq($account)->andWhere('product')->in(array_keys($products))->andWhere('LEFT(openedDate, 4)')->eq($year)->andWhere('deleted')->eq(0)->fetchAll();

    $storyInfo = array();
    $storyInfo['count'] = 0;
    $storyInfo['pri']   = array();
    $storyInfo['stage'] = array();
    $storyInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($stories as $story)
    {
        $storyInfo['count'] ++;

        if(!isset($storyInfo['pri'][$story->pri])) $storyInfo['pri'][$story->pri] = 0;
        $storyInfo['pri'][$story->pri] ++;

        if(!isset($storyInfo['stage'][$story->stage])) $storyInfo['stage'][$story->stage] = 0;
        $storyInfo['stage'][$story->stage] ++;

        $month = (int)substr($story->openedDate, 5, 2) - 1;
        $storyInfo['month'][$month] ++;
    }

    return $storyInfo;
}

/**
 * Get user year created bugs.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearCreatedBugs($account, $year)
{
    $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->andWhere('deleted')->eq(0)->fetchAll();

    $bugInfo = array();
    $bugInfo['count'] = 0;
    $bugInfo['pri']   = array();
    $bugInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($bugs as $bug)
    {
        $bugInfo['count'] ++;

        if(!isset($bugInfo['pri'][$bug->pri])) $bugInfo['pri'][$bug->pri] = 0;
        $bugInfo['pri'][$bug->pri] ++;

        $month = (int)substr($bug->openedDate, 5, 2) - 1;
        $bugInfo['month'][$month] ++;
    }
    return $bugInfo;
}

/**
 * Get user year resolved bugs.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearResolvedBugs($account, $year)
{
    $bugs = $this->dao->select('*')->from(TABLE_BUG)->where('resolvedBy')->eq($account)->andWhere('LEFT(resolvedDate, 4)')->eq($year)->fetchAll();

    $bugInfo = array();
    $bugInfo['count'] = 0;
    $bugInfo['pri']   = array();
    $bugInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($bugs as $bug)
    {
        $bugInfo['count'] ++;

        if(!isset($bugInfo['pri'][$bug->pri])) $bugInfo['pri'][$bug->pri] = 0;
        $bugInfo['pri'][$bug->pri] ++;

        $month = (int)substr($bug->resolvedDate, 5, 2) - 1;
        $bugInfo['month'][$month] ++;
    }
    return $bugInfo;
}

/**
 * Get user year finished tasks.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearFinishedTasks($account, $year)
{
    $tasks = $this->dao->select('id,pri,finishedDate')->from(TABLE_TASK)
        ->where('LEFT(finishedDate, 4)')->eq($year)
        ->andWhere('finishedBy')->eq($account)
        ->fetchAll('id');
    $tasks += $this->dao->select('t1.id,t1.pri,t2.date as finishedDate')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
        ->leftJoin(TABLE_TEAM)->alias('t3')->on("t1.id=t3.root and t3.type='task'")
        ->where('t2.actor')->eq($account)
        ->andWhere('LEFT(t2.date, 4)')->eq($year)
        ->andWhere('t2.action')->eq('finished')
        ->andWhere('t3.account')->eq($account)
        ->fetchAll('id');

    $taskInfo = array();
    $taskInfo['count'] = 0;
    $taskInfo['pri']   = array();
    $taskInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($tasks as $task)
    {
        $taskInfo['count'] ++;

        if(!isset($taskInfo['pri'][$task->pri])) $taskInfo['pri'][$task->pri] = 0;
        $taskInfo['pri'][$task->pri] ++;

        $month = (int)substr($task->finishedDate, 5, 2) - 1;
        $taskInfo['month'][$month] ++;
    }
    return $taskInfo;
}

/**
 * Get user year created cases.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearCreatedCases($account, $year)
{
    $cases = $this->dao->select('*')->from(TABLE_CASE)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchAll();

    $caseInfo = array();
    $caseInfo['count'] = 0;
    $caseInfo['pri']   = array();
    $caseInfo['month'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($cases as $case)
    {
        $caseInfo['count'] ++;

        if(!isset($caseInfo['pri'][$case->pri])) $caseInfo['pri'][$case->pri] = 0;
        $caseInfo['pri'][$case->pri] ++;

        $month = (int)substr($case->openedDate, 5, 2) - 1;
        $caseInfo['month'][$month] ++;
    }
    return $caseInfo;
}

/**
 * Get user year products.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearProducts($account, $year)
{
    $products = $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
        ->where('LEFT(createdDate, 4)')->eq($year)
        ->andWhere('createdBy', true)->eq($account)
        ->orWhere('PO')->eq($account)
        ->orWhere('QD')->eq($account)
        ->orWhere('RD')->eq($account)
        ->markRight(1)
        ->fetchAll('id');

    $storyProducts = $this->dao->select('DISTINCT product')->from(TABLE_STORY)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');
    $planProducts  = $this->dao->select('DISTINCT t1.product')->from(TABLE_PRODUCTPLAN)->alias('t1')
        ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='productplan'")
        ->where('LEFT(t2.date, 4)')->eq($year)
        ->andWhere('t2.actor')->eq($account)
        ->andWhere('t2.action')->eq('opened')
        ->groupBy('t1.product')
        ->fetchPairs('product', 'product');
    $products += $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
        ->where('id')->in($storyProducts + $planProducts)
        ->fetchAll('id');
    return $products;
}

/**
 * Get plans by products.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getPlansByProducts($products, $account, $year)
{
    $planGroups = $this->dao->select('DISTINCT t1.*')->from(TABLE_PRODUCTPLAN)->alias('t1')
        ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='productplan'")
        ->where('t1.product')->in(array_keys($products))
        ->andWhere('LEFT(t2.date, 4)')->eq($year)
        ->andWhere('t2.actor')->eq($account)
        ->andWhere('t2.action')->eq('opened')
        ->fetchGroup('product', 'id');

    foreach($planGroups as $productID => $plans) $planGroups[$productID] = count($plans);
    return $planGroups;
}

/**
 * Get stories by products.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getStoriesByProducts($products, $account, $year)
{
    return $this->dao->select('product, count(*) as stories')->from(TABLE_STORY)
        ->where('product')->in(array_keys($products))
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->andWhere('openedBy')->eq($account)
        ->groupBy('product')
        ->fetchPairs('product', 'stories');
}

/**
 * Get user year projects.
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearProjects($account, $year)
{
    $projects = $this->dao->select('id,name,status')->from(TABLE_PROJECT)->where('1=1')
        ->andWhere('LEFT(begin, 4)', true)->eq($year)
        ->orWhere('LEFT(end, 4)')->le($year)
        ->markRight(1)
        ->andWhere('openedBy', true)->eq($account)
        ->orWhere('PO')->eq($account)
        ->orWhere('PM')->eq($account)
        ->orWhere('QD')->eq($account)
        ->orWhere('RD')->eq($account)
        ->markRight(1)
        ->fetchAll('id');

    $teamProjects = $this->dao->select('*')->from(TABLE_TEAM)->where('type')->eq('project')->andWhere('account')->eq($account)->andWhere('LEFT(`join`, 4)')->eq($year)->fetchPairs('root', 'root');
    $taskProjects = $this->dao->select('DISTINCT t1.project')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
        ->where('t2.actor')->eq($account)
        ->andWhere('LEFT(t2.date, 4)')->eq($year)
        ->andWhere('t2.action')->eq('finished')
        ->fetchPairs('project', 'project');

    $projects += $this->dao->select('id,name,status')->from(TABLE_PROJECT)
        ->where('id')->in($teamProjects + $taskProjects)
        ->fetchAll('id');
    return $projects;
}

/**
 * Get finished story by projects 
 * 
 * @param  array  $projects 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getFinishedStoryByProjects($projects, $account, $year)
{
    $storyGroups = $this->dao->select('t1.*')->from(TABLE_PROJECTSTORY)->alias('t1')
        ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
        ->leftJoin(TABLE_TASK)->alias('t3')->on('t1.story=t3.story')
        ->leftJoin(TABLE_ACTION)->alias('t4')->on("t3.id=t4.objectID and t4.objectType='task'")
        ->where('t1.project')->in(array_keys($projects))
        ->andWhere('LEFT(t4.date, 4)')->eq($year)
        ->andWhere('t4.actor')->eq($account)
        ->andWhere('t4.action')->eq('finished')
        ->fetchGroup('project', 'story');

    foreach($storyGroups as $projectID => $stories) $storyGroups[$projectID] = count($stories);
    return $storyGroups;
}

/**
 * Get finished task by projects.
 * 
 * @param  array  $projects 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getFinishedTaskByProjects($projects, $account, $year)
{
    $tasks = $this->dao->select('*')->from(TABLE_TASK)
        ->where('LEFT(finishedDate, 4)')->eq($year)
        ->andWhere('finishedBy')->eq($account)
        ->andWhere('project')->in(array_keys($projects))
        ->fetchAll('id');
    $tasks += $this->dao->select('t1.*')->from(TABLE_TASK)->alias('t1')
        ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='task'")
        ->leftJoin(TABLE_TEAM)->alias('t3')->on("t1.id=t3.root and t3.type='task'")
        ->where('t2.actor')->eq($account)
        ->andWhere('LEFT(t2.date, 4)')->eq($year)
        ->andWhere('t2.action')->eq('finished')
        ->andWhere('t3.account')->eq($account)
        ->andWhere('t1.project')->in(array_keys($projects))
        ->fetchAll('id');

    $taskGroups = array();
    foreach($tasks as $taskID => $task) $taskGroups[$task->project][$taskID] = $task;
    foreach($taskGroups as $projectID => $tasks) $taskGroups[$projectID] = count($tasks);
    return $taskGroups;
}

/**
 * Get resolved bug by projects.
 * 
 * @param  array  $projects 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getResolvedBugByProjects($projects, $account, $year)
{
    return $this->dao->select('project, count(*) as bugs')->from(TABLE_BUG)
        ->where('project')->in(array_keys($projects))
        ->andWhere('LEFT(resolvedDate, 4)')->eq($year)
        ->andWhere('resolvedBy')->eq($account)
        ->groupBy('project')
        ->fetchPairs('project', 'bugs');
}

/**
 * Get stat by projects.
 * 
 * @param  array $projects 
 * @access public
 * @return array
 */
public function getStatByProjects($projects)
{
    $projectStat = array();
    $projectStat['count']     = 0;
    $projectStat['doing']     = 0;
    $projectStat['done']      = 0;
    $projectStat['suspended'] = 0;
    foreach($projects as $project)
    {
        $projectStat['count']++;
        if($project->status == 'closed' or $project->status == 'done') $projectStat['done']++;
        if($project->status == 'wait' or $project->status == 'doing') $projectStat['doing']++;
        if($project->status == 'suspended') $projectStat['suspended']++;
    }
    return $projectStat;
}

/**
 * Get stat by products.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getStatByProducts($products, $account, $year)
{
    $allStories = $this->dao->select('product, count(*) as count')->from(TABLE_STORY)
        ->where('product')->in(array_keys($products))
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->groupBy('product')
        ->fetchPairs('product', 'count');
    $mineStories = $this->dao->select('product, count(*) as count')->from(TABLE_STORY)
        ->where('product')->in(array_keys($products))
        ->andWhere('openedBy')->eq($account)
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->groupBy('product')
        ->fetchPairs('product', 'count');

    $productStat = array();
    foreach($products as $productID => $product)
    {
        $productStat[$productID]['name']  = $product->name;
        $productStat[$productID]['count'] = zget($allStories, $productID, 0);
        $productStat[$productID]['mine']  = zget($mineStories, $productID, 0);
    }

    return $productStat;
}

/**
 * Get effort for month 
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getEffort4Month($account, $year)
{
    $efforts = $this->dao->select('*')->from(TABLE_TASKESTIMATE)->where('account')->eq($account)
        ->andWhere('LEFT(date, 4)')->eq($year)
        ->orderBy('date')
        ->fetchAll();

    $months = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    foreach($efforts as $effort)
    {
        $month = (int)substr($effort->date, 5, 2) - 1;
        $months[$month] += $effort->consumed;
    }
    return $months;
}

/**
 * Get user year products for qa 
 * 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getUserYearProducts4QA($account, $year)
{
    $bugProducts  = $this->dao->select('product')->from(TABLE_BUG)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');
    $caseProducts = $this->dao->select('product')->from(TABLE_CASE)->where('openedBy')->eq($account)->andWhere('LEFT(openedDate, 4)')->eq($year)->fetchPairs('product', 'product');

    $products = $this->dao->select('id,name,status')->from(TABLE_PRODUCT)
        ->where('id')->in($bugProducts + $caseProducts)
        ->fetchAll('id');
    return $products;
}

/**
 * Get bug stat by products.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getBugStatByProducts($products, $account, $year)
{
    $allBugs = $this->dao->select('product, count(*) as count')->from(TABLE_BUG)
        ->where('product')->in(array_keys($products))
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->groupBy('product')
        ->fetchPairs('product', 'count');
    $mineBugs = $this->dao->select('product, count(*) as count')->from(TABLE_BUG)
        ->where('product')->in(array_keys($products))
        ->andWhere('openedBy')->eq($account)
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->groupBy('product')
        ->fetchPairs('product', 'count');

    $productStat = array();
    foreach($products as $productID => $product)
    {
        $productStat[$productID]['name']  = $product->name;
        $productStat[$productID]['count'] = zget($allBugs, $productID, 0);
        $productStat[$productID]['mine']  = zget($mineBugs, $productID, 0);
    }

    return $productStat;
}

/**
 * Get created bug by products.
 * 
 * @param  array  $products 
 * @param  string $account 
 * @param  int    $year 
 * @access public
 * @return array
 */
public function getCreatedBugByProducts($products, $account, $year)
{
    return $this->dao->select('product, count(*) as bugs')->from(TABLE_BUG)
        ->where('product')->in(array_keys($products))
        ->andWhere('LEFT(openedDate, 4)')->eq($year)
        ->andWhere('openedBy')->eq($account)
        ->groupBy('product')
        ->fetchPairs('product', 'bugs');
}
