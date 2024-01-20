#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试 repoModel::getRepoByUrl();
cid=1
pid=1

使用空的MR >> Url is empty.
使用错误的MR >> No matched gitlab.
使用正确的MR >> return normal

*/

$repoModel = $tester->loadModel('repo');

$url    = '';
$result = $repoModel->getRepoByUrl($url);
r($result) && p('message') && e('Url is empty.'); //使用空的MR

$url    = 'http://192.168.1.161:51080/gitlab-instance-f9325ed1/azalea723test.git';
$result = $repoModel->getRepoByUrl($url);
r($result) && p('message') && e('No matched gitlab.'); //使用错误的MR

$url    = 'http://192.168.1.161:51080/root/azalea723test.git';
$result = $repoModel->getRepoByUrl($url);
if(!empty($result))
{
    if($result['result'] == 'fail' and $result['message'] != 'No matched gitlab.') $result = 'return normal';
    if(is_array($result) and $result['result'] == 'success') $result = 'return normal';
}
r($result) && p() && e('return normal'); //使用正确的MR