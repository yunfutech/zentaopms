$(function()
{
    $('#storyList').on('sort.sortable', function(e, data)
    {
        var list = '';
        for(i = 0; i < data.list.length; i++) list += $(data.list[i].item).attr('data-id') + ',';
        $.post(createLink('project', 'storySort', 'projectID=' + projectID), {'storys' : list, 'orderBy' : orderBy}, function()
        {
            var $target = $(data.element[0]);
            $target.hide();
            $target.fadeIn(1000);
            order = 'order_asc'
            history.pushState({}, 0, createLink('project', 'story', "projectID=" + projectID + '&orderBy=' + order));
        });
    });

    $('#module' + moduleID).parent().addClass('active');
    $('#product' + productID).addClass('active');
    $('#branch' + branchID).addClass('active');
    $(document).on('click', "#storyList tbody tr, .table-footer .check-all, #storyList thead .check-all", function(){showCheckedSummary();});
    $(document).on('change', "#storyList :checkbox", function(){showCheckedSummary();});

    $('#toTaskButton').on('click', function ()
    {
        var planID = $('#plan').val();
        if(planID)
        {
            parent.location.href = createLink('project', 'importPlanStories', 'projectID=' + projectID + '&planID=' + planID);
        }
    })
    $('.sorter-false a').unwrap();
});

function showCheckedSummary()
{
    var $summary = $('#main #mainContent form.main-table .table-header .table-statistic');

    var checkedTotal    = 0;
    var checkedEstimate = 0;
    var checkedCase     = 0;
    var checkedConsumed = 0;
    var checkedProgress = 0;
    var checkedYesCompletion = 0;
    var checkedWeekCompletion = 0;
    var stoires = [];
    $('[name^="storyIdList"]').each(function()
    {
        if($(this).prop('checked'))
        {
            checkedTotal += 1;
            var taskID = $(this).val();
            $tr = $("#storyList tbody tr[data-id='" + taskID + "']");
            stoires.push($tr.data())
            checkedEstimate += Number($tr.data('estimate'));
            checkedConsumed += Number($tr.data('consumed'));
            if(Number($tr.data('cases')) > 0) checkedCase += 1;
        }
    });
    stoires.forEach(item => {
        var weight = item.estimate / checkedEstimate
        if (item.progress >= 1) {
            item.progress = 1
        }
        if (item.weekcompletion >= 1) {
            item.weekcompletion = 1
        }
        if (item.yestodaycompletion >= 1) {
            item.yestodaycompletion = 1
        }
        if (checkedEstimate != 0) {
            checkedProgress += weight * item.progress
            checkedYesCompletion += weight * item.yestodaycompletion
            checkedWeekCompletion += weight * item.weekcompletion
        }
    });
    console.log(checkedEstimate, checkedConsumed, checkedProgress)
    if(checkedTotal > 0)
    {
        rate    = Math.round(checkedCase / checkedTotal * 10000) / 100 + '' + '%';
        checkedEstimate =checkedEstimate.toFixed(2);
        checkedConsumed =checkedConsumed.toFixed(2);
        checkedProgress = Math.round(checkedProgress * 10000) / 100  + '' + '%';
        checkedYesCompletion = Math.round(checkedYesCompletion * 10000) / 100  + '' + '%';
        checkedWeekCompletion = Math.round(checkedWeekCompletion * 10000) / 100  + '' + '%';
        summary = checkedSummary.replace('%total%', checkedTotal)
          .replace('%estimate%', checkedEstimate)
          .replace('%rate%', rate)
          .replace('%consumed%', checkedConsumed)
          .replace('%progress%', checkedProgress)
          .replace('%yestodayCompletion%', checkedYesCompletion)
          .replace('%weekCompletion%', checkedWeekCompletion)
        $summary.html(summary);
    }
}
