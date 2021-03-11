$(function()
{
    if($('#storyList thead th.c-title').width() < 150) $('#storyList thead th.c-title').width(150);

    // Fix state dropdown menu position
    $('.c-stage > .dropdown').each(function()
    {
        var $this = $(this);
        var menuHeight = $(this).find('.dropdown-menu').outerHeight();
        var $tr = $this.closest('tr');
        var height = 0;
        while(height < menuHeight)
        {
            var $next = $tr.next('tr');
            if(!$next.length) break;
            height += $next.outerHeight;
        }
        if(height < menuHeight)
        {
            $this.addClass('dropup');
        }
    });

    $(document).on('click', "#storyList tbody tr, .table-footer .check-all, #storyList thead .check-all", function(){showCheckedSummary();});
    $(document).on('change', "#storyList :checkbox", function(){showCheckedSummary();});
});

function showCheckedSummary()
{
    var $summary = $('#main #mainContent form.main-table .table-statistic');

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
    if(checkedTotal > 0)
    {
        rate = Math.round(checkedCase / checkedTotal * 10000) / 100 + '' + '%';
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