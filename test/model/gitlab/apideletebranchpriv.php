#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试 gitlabModel::apiDeleteBranchPriv();
cid=1
pid=1

使用空的gitlabID,projectID,分支名称删除保护分支 >> return false
使用正确的gitlabID、保护分支信息，错误的projectID删除保护分支 >> 404 Project Not Found
通过gitlabID,projectID,分支名称正确删除保护分支 >> return true
使用错误的保护分支信息删除保护分支 >> 404 Not found

*/

$gitlab = $tester->loadModel('gitlab');

$gitlabID  = 0;
$projectID = 0;
$branch    = '';

$result = $gitlab->apiDeleteBranchPriv($gitlabID, $projectID, $branch);
if($result === false) $result = 'return false';
r($result) && p() && e('return false'); //使用空的gitlabID,projectID,分支名称删除保护分支

$gitlabID = 1;
$branch   = 'master';
$result   = $gitlab->apiDeleteBranchPriv($gitlabID, $projectID, $branch);
r($result) && p('message') && e('404 Project Not Found'); //使用正确的gitlabID、保护分支信息，错误的projectID删除保护分支

$projectID = 1565;
$result    = $gitlab->apiDeleteBranchPriv($gitlabID, $projectID, $branch);
if(!$result or substr($result->message, 0, 2) == '20') $result = 'return true';
r($result) && p() && e('return true');  //通过gitlabID,projectID,分支名称正确删除保护分支

$branch = 'masters';
$result = $gitlab->apiDeleteBranchPriv($gitlabID, $projectID, $branch);
r($result) && p('message') && e('404 Not found'); //使用错误的保护分支信息删除保护分支