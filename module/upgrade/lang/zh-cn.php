<?php
/**
 * The upgrade module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $Id: zh-cn.php 5119 2013-07-12 08:06:42Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->upgrade->common          = '升级';
$lang->upgrade->start           = '开始';
$lang->upgrade->result          = '升级结果';
$lang->upgrade->fail            = '升级失败';
$lang->upgrade->successTip      = '升级成功';
$lang->upgrade->success         = "<p><i class='icon icon-check-circle'></i></p><p>恭喜您！您的禅道已经成功升级。</p>";
$lang->upgrade->tohome          = '访问禅道';
$lang->upgrade->license         = '禅道项目管理软件已更换授权协议至 Z PUBLIC LICENSE(ZPL) 1.2';
$lang->upgrade->warnning        = '警告';
$lang->upgrade->checkExtension  = '检查插件';
$lang->upgrade->consistency     = '一致性检查';
$lang->upgrade->warnningContent = <<<EOT
<p>升级有危险，请先备份数据库，以防万一。</p>
<pre>
1. 可以通过phpMyAdmin进行备份。
2. 使用mysql命令行的工具。
   $> mysqldump -u <span class='text-danger'>username</span> -p <span class='text-danger'>dbname</span> > <span class='text-danger'>filename</span>
   要将上面红色的部分分别替换成对应的用户名和禅道系统的数据库名。
   比如： mysqldump -u root -p zentao >zentao.bak
</pre>
EOT;

$lang->upgrade->createFileWinCMD   = '打开命令行，执行<strong style="color:#ed980f">echo > %s</strong>';
$lang->upgrade->createFileLinuxCMD = '在命令行执行: <strong style="color:#ed980f">touch %s</strong>';
$lang->upgrade->setStatusFile      = '<h4>升级之前请先完成下面的操作：</h4>
                                      <ul style="line-height:1.5;font-size:13px;">
                                      <li>%s</li>
                                      <li>或者删掉"<strong style="color:#ed980f">%s</strong>" 这个文件 ，重新创建一个<strong style="color:#ed980f">ok.txt</strong>文件，不需要内容。</li>
                                      </ul>
                                      <p><strong style="color:red">我已经仔细阅读上面提示且完成上述工作，<a href="#" onclick="location.reload()">继续更新</a></strong></p>';

$lang->upgrade->selectVersion  = '选择版本';
$lang->upgrade->continue       = '继续';
$lang->upgrade->noteVersion    = "务必选择正确的版本，否则会造成数据丢失。";
$lang->upgrade->fromVersion    = '原来的版本';
$lang->upgrade->toVersion      = '升级到';
$lang->upgrade->confirm        = '确认要执行的SQL语句';
$lang->upgrade->sureExecute    = '确认执行';
$lang->upgrade->forbiddenExt   = '以下插件与新版本不兼容，已经自动禁用：';
$lang->upgrade->updateFile     = '需要更新附件信息。';
$lang->upgrade->noticeSQL      = '检查到你的数据库跟标准不一致，尝试修复失败。请执行以下SQL语句，再刷新页面检查。';
$lang->upgrade->afterDeleted   = '请执行上面命令删除文件， 删除后刷新！';
$lang->upgrade->mergeProgram   = '数据迁移';
$lang->upgrade->mergeTips      = '数据迁移提示';
$lang->upgrade->toPMS15Guide   = '禅道开源版15版本升级';
$lang->upgrade->toPRO10Guide   = '禅道专业版10版本升级';
$lang->upgrade->toBIZ5Guide    = '禅道企业版5版本升级';
$lang->upgrade->toMAXGuide     = '禅道旗舰版版本升级';
$lang->upgrade->to15Desc       = <<<EOD
<p>尊敬的用户，禅道从15版本开始系统功能做了重大升级，主要改动如下：</p>
<p><strong>一、增加了项目集概念</strong></p>
<p>项目集是一组相互关联，且被协调管理的项目集合，处于最高层级，属于战略层面的概念。它可以进行多层级管理，帮助管理者站在宏观的视角去制定战略方向和分配资源。</p>
<p><strong>二、明确了产品和项目概念</strong></p>
<p>产品是定义做什么，主要管理需求；项目是定义如何做，主要是在规定的时间、预算和质量目标范围内完成项目的各种工作，可以通过敏捷迭代的方式，也可以通过瀑布阶段的方式，属于战役层面的管理。</p>
<p><strong>三、增加了项目模型概念</strong></p>
<p>新版本在敏捷管理模型的基础上增加了瀑布管理模型（旗舰版提供），后续还会支持看板管理模型，帮助项目团队按需选择适合的项目管理方式。</p>
<p><strong>四、增加了执行概念</strong></p>
<p>新版本中，根据选择管理模型的不同，一个项目可以包含多个迭代/冲刺或阶段，我们把多个迭代/冲刺或阶段统称为执行，通过执行去完成项目的任务，交付最终的结果。</p>
<p><strong>五、调整了导航结构</strong></p>
<p>将一级导航调整到了界面左侧，同时增加了多应用切换的全新交互体验。</p>
<br/>
<p>您可以在线体验最新版本的功能，以决定是否启用新的模式：<a class='text-info' href='http://zentaomax.demo.zentao.net' target='_blank'>最新版演示demo</a></p>
<p>您还可以下载新版本功能介绍PPT：<a class='text-info' href='https://dl.cnezsoft.com/zentao/zentaoconcept.pdf' target='_blank'>最新版功能介绍PPT</a></p>
<video src="https://dl.cnezsoft.com/vedio/program0716.mp4"  width="100%" controls ="controls"></video>
<p style="text-align:center"><small>禅道15.0版本介绍</small></p>
<br/>
<p><strong>请问您计划如何使用禅道的新版本呢？</strong></p>
EOD;

$lang->upgrade->mergeProgramDesc = <<<EOD
<p>接下来我们会把之前历史{$lang->productCommon}和{$lang->projectCommon}数据迁移到项目集和项目下，迁移的情况如下：</p><br />
<h4>情况一：以{$lang->productCommon}线组织的{$lang->productCommon}和{$lang->projectCommon} </h4>
<p>可以将整个{$lang->productCommon}线及其下面的{$lang->productCommon}和{$lang->projectCommon}迁移到一个项目集中，当然您也可以根据需要分开迁移。</p>
<h4>情况二：以{$lang->productCommon}组织的{$lang->projectCommon} </h4>
<p>可以选择多个{$lang->productCommon}及其下面的{$lang->projectCommon}迁移到一个项目集中，也可以选择某一个{$lang->productCommon}和{$lang->productCommon}下面的{$lang->projectCommon}迁移到项目集中。</p>
<h4>情况三：独立的{$lang->projectCommon}</h4>
<p>可以选择若干个{$lang->projectCommon}迁移到一个项目集中，也可以独立迁移。</p>
<h4>情况四：关联多个{$lang->productCommon}的{$lang->projectCommon}</h4>
<p>可以选择这些{$lang->projectCommon}归属于某个新项目下。</p>
EOD;

$lang->upgrade->to15Mode['classic'] = '经典管理模式';
$lang->upgrade->to15Mode['new']     = '全新项目集管理模式';

$lang->upgrade->selectedModeTips['classic'] = '后续您还可以在后台-自定义里面切换为全新项目集管理的模式。';
$lang->upgrade->selectedModeTips['new']     = '切换为项目集管理模式需要对之前的数据进行归并处理，系统会引导您完成这个操作。';

$lang->upgrade->line          = '产品线';
$lang->upgrade->allLines      = "所有{$lang->productCommon}线";
$lang->upgrade->program       = '目标项目集和项目';
$lang->upgrade->existProgram  = '已有项目集';
$lang->upgrade->existProject  = '已有项目';
$lang->upgrade->existLine     = '已有' . $lang->productCommon . '线';
$lang->upgrade->product       = $lang->productCommon;
$lang->upgrade->project       = '迭代';
$lang->upgrade->repo          = '版本库';
$lang->upgrade->mergeRepo     = '归并版本库';
$lang->upgrade->setProgram    = '设置项目所属项目集';
$lang->upgrade->dataMethod    = '数据迁移方式';
$lang->upgrade->begin         = '开始日期';
$lang->upgrade->end           = '结束日期';
$lang->upgrade->selectProject = '目标项目';
$lang->upgrade->programName   = '项目集名称';
$lang->upgrade->projectName   = '项目名称';

$lang->upgrade->newProgram         = '新建';
$lang->upgrade->editedName         = '调整后名称';
$lang->upgrade->projectEmpty       = '所属项目不能为空！';
$lang->upgrade->mergeSummary       = "尊敬的用户，您的系统中共有%s个{$lang->productCommon}，%s个{$lang->projectCommon}等待迁移。";
$lang->upgrade->mergeByProductLine = "以{$lang->productCommon}线组织的{$lang->productCommon}和{$lang->projectCommon}：将整个{$lang->productCommon}线及其下面的{$lang->productCommon}和{$lang->projectCommon}归并到一个项目集和项目中，也可以分开归并。";
$lang->upgrade->mergeByProduct     = "以{$lang->productCommon}组织的{$lang->projectCommon}：可以选择多个{$lang->productCommon}及其下面的{$lang->projectCommon}归并到一个项目集和项目中，也可以选择某一个{$lang->productCommon}将其下面所属的{$lang->projectCommon}归并到项目集和项目中。";
$lang->upgrade->mergeByProject     = "独立的{$lang->projectCommon}：可以选择若干{$lang->projectCommon}归并到一个项目中，也可以独立归并。";
$lang->upgrade->mergeByMoreLink    = "关联多个{$lang->productCommon}的{$lang->projectCommon}：选择一个或多个{$lang->projectCommon}归并到一个项目集和项目中。";
$lang->upgrade->mergeRepoTips      = "将选中的版本库归并到所选产品下。";
$lang->upgrade->needBuild4Add      = '本次升级需要创建索引。请到 [后台->系统->重建索引] 页面，重新创建索引。';
$lang->upgrade->errorEngineInnodb  = '您当前的数据库不支持使用InnoDB数据表引擎，请修改为MyISAM后重试。';
$lang->upgrade->duplicateProject   = "同一个项目集内项目名称不能重复，请调整重名的项目名称";
$lang->upgrade->upgradeTips        = "历史删除数据不参与升级，升级后将不支持还原，请知悉";

$lang->upgrade->projectType['project']   = "把历史的{$lang->projectCommon}作为项目升级";
$lang->upgrade->projectType['execution'] = "把历史的{$lang->projectCommon}作为执行升级";

$lang->upgrade->createProjectTip = <<<EOT
<p>升级后历史的{$lang->projectCommon}一一对应新版本中的项目。</p>
<p>系统会根据历史{$lang->projectCommon}分别创建一个与该{$lang->projectCommon}同名的执行，并将之前{$lang->projectCommon}的任务、需求、Bug等数据迁移至执行中。</p>
EOT;

$lang->upgrade->createExecutionTip = <<<EOT
<p>系统会把历史的{$lang->projectCommon}作为执行进行升级。</p>
<p>升级后历史的{$lang->projectCommon}数据将对应新版本中项目下的执行。</p>
EOT;

include dirname(__FILE__) . '/version.php';
