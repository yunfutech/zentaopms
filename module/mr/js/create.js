$(function()
{
    $('#gitlabID').change(function()
    {
        var gitlabID = $('#gitlabID').val();
        if(gitlabID == '') return false;

        var url = createLink('repo', 'ajaxgetgitlabprojects', "gitlabID=" + gitlabID + "&projectIdList=&filter=IS_DEVELOPER");
        $.get(url, function(response)
        {
            $('#sourceProject').html('').append(response);
            $('#sourceProject').chosen().trigger("chosen:updated");;
        });
   });

    $('#sourceProject,#targetProject').change(function()
    {
        var gitlabID      = $('#gitlabID').val();
        var sourceProject = $(this).val();
        var branchSelect  = $(this).parents('td').find('select[name*=Branch]');
        var branchUrl     = createLink('gitlab', 'ajaxgetprojectbranches', "gitlabID=" + gitlabID + "&projectID=" + sourceProject);
        $.get(branchUrl, function(response)
        {
            branchSelect.html('').append(response);
            branchSelect.chosen().trigger("chosen:updated");;
        });

    });

    $('#sourceProject').change(function()
    {
        var gitlabID      = $('#gitlabID').val();
        var sourceProject = $(this).val();
        var projectUrl    = createLink('mr', 'ajaxGetMRTargetProjects', "gitlabID=" + gitlabID + "&projectID=" + sourceProject);
        $.get(projectUrl, function(response)
        {
            $('#targetProject').html('').append(response);
            $('#targetProject').chosen().trigger("chosen:updated");;
        });

        var repoUrl = createLink('mr', 'ajaxGetRepoList', "gitlabID=" + gitlabID + "&projectID=" + sourceProject);
        $.get(repoUrl, function(response)
        {
            $('#repoID').html('').append(response);
            $('#repoID').chosen().trigger("chosen:updated");;
        });
    });

    $('#sourceBranch,#targetBranch').change(function()
    {
        var sourceProject = $('#sourceProject').val();
        var sourceBranch  = $('#sourceBranch').val();
        var targetProject = $('#targetProject').val();
        var targetBranch  = $('#targetBranch').val();
        if(!sourceProject || !sourceBranch || !targetProject || !targetBranch) return false;

        var $this    = $(this);
        var gitlabID = $('#gitlabID').val();
        var repoUrl  = createLink('mr', 'ajaxCheckSameOpened', "gitlabID=" + gitlabID);
        $.post(repoUrl, {"sourceProject": sourceProject, "sourceBranch": sourceBranch, "targetProject": targetProject, "targetBranch": targetBranch}, function(response)
        {
            response = $.parseJSON(response);
            if(response.result == 'fail')
            {
                alert(response.message);
                $this.val('').trigger('chosen:updated');
                return false;
            }
        });
    });

    /*
    $('#targetProject').change(function()
    {
        targetProject = $(this).val();
        var gitlabID = $('#gitlabID').val();
        var assignee = $("#assignee").parents('td').find('select[name*=assignee]');
        var reviewer = $("#reviewer").parents('td').find('select[name*=reviewer]');
        usersUrl = createLink('gitlab', 'ajaxgetmruserpairs', "gitlabID=" + gitlabID + "&projectID=" + targetProject);
        $.get(usersUrl, function(response)
        {
            assignee.html('').append(response);
            assignee.chosen().trigger("chosen:updated");;
            reviewer.html('').append(response);
            reviewer.chosen().trigger("chosen:updated");;
        });
    });
    */

    $('#repoID').change(function()
    {
        var repoID = $(this).val();
        var jobUrl = createLink('mr', 'ajaxGetJobList', "repoID=" + repoID);
        $.get(jobUrl, function(response)
        {
            $('#jobID').html('').append(response);
            $('#jobID').chosen().trigger("chosen:updated");;
        });
    });

    $('#jobID').change(function()
    {
        var jobID      = $(this).val();
        var compileUrl = createLink('mr', 'ajaxGetCompileList', "job=" + jobID);
        $.get(compileUrl, function(response)
        {
            $('#compile').html('').append(response);
            $('#compile').chosen().trigger("chosen:updated");;
        });
    });

    $("#needCI").change(function()
    {
        if(this.checked == false)
        {
            $("#jobID").prop("disabled", true);
            $('#jobID').chosen().trigger("chosen:updated");;
            $("#jobID").parent().parent().addClass('hidden');
        }
        if(this.checked == true)
        {
            $("#jobID").prop("disabled", false);
            $('#jobID').chosen().trigger("chosen:updated");;
            $("#jobID").parent().parent().removeClass('hidden');
        }
    });
});
