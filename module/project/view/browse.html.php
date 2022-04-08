<?php
/**
 * The prjbrowse view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: prjbrowse.html.php 4769 2013-05-05 07:24:21Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<?php
js::set('orderBy', $orderBy);
js::set('programID', $programID);
js::set('browseType', $browseType);
js::set('param', $param);
js::set('orderBy', $orderBy);
js::set('recTotal', $recTotal);
js::set('recPerPage', $recPerPage);
js::set('pageID', $pageID);
?>
<?php
if($projectType == 'bycard')
{
    include 'browsebycard.html.php';
}
else
{
    include 'browsebylist.html.php';
}
?>
<?php include '../../common/view/footer.html.php';?>
