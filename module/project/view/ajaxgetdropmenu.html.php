<?php js::set('projectID', $projectID);?>
<?php js::set('module', $module);?>
<?php js::set('method', $method);?>
<style>
#navTabs {position: sticky; top: 0; background: #fff; z-index: 950;}
#navTabs>li {padding: 0px 10px; display: inline-block}
#navTabs>li>span {display: inline-block;}
#navTabs>li>a {padding: 8px 0px; display: inline-block}
#navTabs>li.active>a {font-weight: 700; color: #0c64eb;}
#navTabs>li.active>a:before {position: absolute; right: 0; bottom: -1px; left: 0; display: block; height: 2px; content: ' '; background: #0c64eb;}
#navTabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {border: none;}

#tabContent {margin-top: 10px; z-index: 900;}
.projectTree ul {list-style: none; margin: 0}
.projectTree .projects>ul {padding-left: 7px;}
.projectTree .projects>ul>li>div {display: flex; flex-flow: row nowrap; justify-content: flex-start; align-items: center;}
.projectTree .projects>ul>li label {background: rgba(255,255,255,0.5); line-height: unset; color: #838a9d; border: 1px solid #d8d8d8; border-radius: 2px; padding: 1px 4px;}
.projectTree li a i.icon {font-size: 15px !important;}
.projectTree li a i.icon:before {min-width: 16px !important;}
.projectTree li .label {position: unset; margin-bottom: 0;}
.projectTree li>a, div.hide-in-search>a {display: block; padding: 2px 10px 2px 5px; overflow: hidden; line-height: 20px; text-overflow: ellipsis; white-space: nowrap; border-radius: 4px;}
.projectTree li>a.selected {color: #e9f2fb; background-color: #0c64eb;}
.projectTree .tree li>.list-toggle {line-height: 24px;}
.projectTree .tree li.has-list.open:before {content: unset;}

#swapper li>div.hide-in-search>a:focus, #swapper li>div.hide-in-search>a:hover {color: #838a9d; cursor: default;}
a.projectName:focus, a.projectName:hover {background: #0c64eb; color: #fff !important;}

#swapper li > a {padding-top: 4px; padding-bottom: 4px;}
#swapper li {padding-top: 0; padding-bottom: 0;}
#swapper .tree li>.list-toggle {top: -1px;}
</style>
<?php
$projectCounts      = array();
$projectNames       = array();
$tabActive          = '';
$myProjects         = 0;
$others             = 0;
$dones              = 0;

foreach($projects as $programID => $programProjects)
{
    $projectCounts[$programID]['myProject'] = 0;
    $projectCounts[$programID]['others']    = 0;
    $projectCounts[$programID]['closed']    = 0;

    foreach($programProjects as $project)
    {
        if($project->status != 'done' and $project->status != 'closed' and $project->PM == $this->app->user->account) $projectCounts[$programID]['myProject'] ++;
        if($project->status != 'done' and $project->status != 'closed' and !($project->PM == $this->app->user->account)) $projectCounts[$programID]['others'] ++;
        if($project->status == 'done' or $project->status == 'closed') $projectCounts[$programID]['closed'] ++;
        $projectNames[] = $project->name;
    }
}
$projectsPinYin = common::convert2Pinyin($projectNames);

$myProjectsHtml     = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';
$normalProjectsHtml = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';
$closedProjectsHtml = $config->systemMode == 'new' ? '<ul class="tree tree-angles" data-ride="tree">' : '<ul class="noProgram">';

foreach($projects as $programID => $programProjects)
{
    /* Add the program name before project. */
    if(isset($programs[$programID]) and $config->systemMode == 'new')
    {
        $programName = zget($programs, $programID);

        if($projectCounts[$programID]['myProject']) $myProjectsHtml  .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
        if($projectCounts[$programID]['others']) $normalProjectsHtml .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
        if($projectCounts[$programID]['closed']) $closedProjectsHtml .= '<li><div class="hide-in-search"><a class="text-muted" title="' . $programName . '">' . $programName . '</a> <label class="label">' . $lang->program->common . '</label></div><ul>';
    }

    foreach($programProjects as $index => $project)
    {
        $selected = $project->id == $projectID ? 'selected' : '';
        $link     = helper::createLink('project', 'index', "projectID=%s", '', '', $project->id);
        $icon     = '<i class="icon icon-sprint"></i> ';

        if($project->model != 'scrum') $icon = "<i class='icon icon-{$project->model}'></i> ";
        $projectName = $icon . $project->name;

        if($project->status != 'done' and $project->status != 'closed' and $project->PM == $this->app->user->account)
        {
            $myProjectsHtml .= '<li>' . html::a(sprintf($link, $project->id), $projectName, '', "class='$selected projectName' title='{$project->name}' data-key='" . zget($projectsPinYin, $project->name, '') . "'") . '</li>';

            if($selected == 'selected') $tabActive = 'myProject';

            $myProjects ++;
        }
        else if($project->status != 'done' and $project->status != 'closed' and !($project->PM == $this->app->user->account))
        {
            $normalProjectsHtml .= '<li>' . html::a(sprintf($link, $project->id), $projectName, '', "class='$selected projectName' title='{$project->name}' data-key='" . zget($projectsPinYin, $project->name, '') . "'") . '</li>';

            if($selected == 'selected') $tabActive = 'other';

            $others ++;
        }
        else if($project->status == 'done' or $project->status == 'closed')
        {
            $closedProjectsHtml .= '<li>' . html::a(sprintf($link, $project->id), $projectName, '', "class='$selected projectName' title='$project->name' data-key='" . zget($projectsPinYin, $project->name, '') . "'") . '</li>';

            if($selected == 'selected') $tabActive = 'closed';
        }

        /* If the project is the last one in the program, print the closed label. */
        if(isset($programs[$programID]) and !isset($programProjects[$index + 1]))
        {
            if($projectCounts[$programID]['myProject']) $myProjectsHtml     .= '</ul></li>';
            if($projectCounts[$programID]['others'])    $normalProjectsHtml .= '</ul></li>';
            if($projectCounts[$programID]['closed'])    $closedProjectsHtml .= '</ul></li>';
        }
    }
}
$myProjectsHtml     .= '</ul>';
$normalProjectsHtml .= '</ul>';
$closedProjectsHtml .= '</ul>';
?>

<div class="table-row">
  <div class="table-col col-left">
    <div class='list-group'>
      <?php $tabActive = ($myProjects and ($tabActive == 'closed' or $tabActive == 'myProject')) ? 'myProject' : 'other';?>
      <?php if($myProjects): ?>
      <ul class="nav nav-tabs" id="navTabs">
        <li class="<?php if($tabActive == 'myProject') echo 'active';?>"><?php echo html::a('#myProject', $lang->project->myProject, '', "data-toggle='tab' class='not-list-item not-clear-menu'");?><span class="label label-light label-badge"><?php echo $myProjects;?></span><li>
        <li class="<?php if($tabActive == 'other') echo 'active';?>"><?php echo html::a('#other', $lang->project->other, '', "data-toggle='tab' class='not-list-item not-clear-menu'")?><span class="label label-light label-badge"><?php echo $others;?></span><li>
      </ul>
      <?php endif;?>
      <div class="tab-content projectTree" id="tabContent">
        <div class="tab-pane projects <?php if($tabActive == 'myProject') echo 'active';?>" id="myProject">
          <?php echo $myProjectsHtml;?>
        </div>
        <div class="tab-pane projects <?php if($tabActive == 'other') echo 'active';?>" id="other">
          <?php echo $normalProjectsHtml;?>
        </div>
      </div>
    </div>
    <div class="col-footer">
      <?php //echo html::a(helper::createLink('project', 'browse', 'programID=0&browseType=all'), '<i class="icon icon-cards-view muted"></i> ' . $lang->project->all, '', 'class="not-list-item"'); ?>
      <a class='pull-right toggle-right-col not-list-item'><?php echo $lang->project->doneProjects?><i class='icon icon-angle-right'></i></a>
    </div>
  </div>
  <div class="table-col col-right projectTree">
   <div class='list-group projects'><?php echo $closedProjectsHtml;?></div>
  </div>
</div>
<script>scrollToSelected();</script>
<script>
$(function()
{
    $('.nav-tabs li span').hide();
    $('.nav-tabs li.active').find('span').show();

    $('.nav-tabs>li a').click(function()
    {
        if($('#swapper input[type="search"]').val() == '')
        {
            $(this).siblings().show();
            $(this).parent().siblings('li').find('span').hide();
        }
    })

    $('#swapper [data-ride="tree"]').tree('expand');

    $('#swapper #dropMenu .search-box').on('onSearchChange', function(event, value)
    {
        if(value != '')
        {
            $('div.hide-in-search').siblings('i').addClass('hide-in-search');
            $('.nav-tabs li span').hide();
        }
        else
        {
            $('div.hide-in-search').siblings('i').removeClass('hide-in-search');
            $('li.has-list div.hide-in-search').removeClass('hidden');
            $('.nav-tabs li.active').find('span').show();
        }
    })
})
</script>
