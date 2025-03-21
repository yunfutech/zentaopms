function showLink(roadmapID, type, orderBy, param)
{
    var method = 'linkUR';
    loadURL(createLink('roadmap', method, 'roadmapID=' + roadmapID + (typeof(param) == 'undefined' ? '' : param) + (typeof(orderBy) == 'undefined' ? '' : "&orderBy=" + orderBy)), type)

    $('.actions').find("a[href*='" + type + "']").addClass('hidden');
}

/**
 * Load URL.
 *
 * @param  string $url
 * @param  string $type
 * @access public
 * @return void
 */
function loadURL(url, type)
{
    $.get(url, function(data)
    {
        var $pane = $('#stories');
        $pane.find('.main-table').hide();
        var $linkBox = $pane.find('.linkBox').html(data).removeClass('hidden');
        $linkBox.html(data).removeClass('hidden');
        $linkBox.find('[data-ride="table"]').table();
        $linkBox.find('[data-ride="pager"]').pager();
        $linkBox.find('[data-ride="pager"] li a.pager-item').click(function()
        {
            loadURL($(this).attr('href'), type);
            return false;
        });
        $linkBox.find('[data-ride="pager"] .pager-size-menu a[data-size]').off('click');
        $linkBox.find('[data-ride="pager"] .pager-size-menu a[data-size]').click(function()
        {
            line = $linkBox.find('[data-ride="pager"]').attr('data-link-creator');
            line = line.replace('{recPerPage}', $(this).attr('data-size')).replace('{page}', $linkBox.find('[data-ride="pager"]').attr('data-page'));
            $.cookie($linkBox.find('[data-ride="pager"]').attr('data-page-cookie'), $(this).attr('data-size'), {expires:config.cookieLife, path:config.webRoot});
            loadURL(line, type);
            return false;
        });
        $.toggleQueryBox(true, $linkBox.find('#queryBox'));
    });
}

$(function()
{
    /* Recalculate the maximum width of the title. */
    var maxWidth = $('#mainMenu').width() - $('#actionsBox').width() - $('#mainMenu .btn-toolbar .page-title .label-info').width() - 177;
    $('#mainMenu .btn-toolbar .page-title .text').css('max-width', maxWidth.toString() + 'px');

    if(link == 'true') showLink(roadmapID, type, orderBy, param);
    var infoShowed = false;
    $('.nav.nav-tabs a[data-toggle="tab"]').on('shown.zui.tab', function(e)
    {
        var href = $(e.target).attr('href');
        var tabPane = $(href + '.tab-pane');
        if(tabPane.size() == 0) return;
        var formID = tabPane.find('.linkBox').find('form:last');
        if(formID.size() == 0) formID = tabPane.find('form:last');
        if(href == '#roadmapInfo' && !infoShowed)
        {
            $('#roadmapInfo img').each(function()
            {
                var $tr = $('#roadmapInfo .detail-content .table-data tbody tr:first');
                width   = $tr.width() - $tr.find('th').width();
                if($(this).parent().prop('tagName').toLowerCase() == 'a') $(this).unwrap();
                setImageSize($(this), width, 0);
            });

            infoShowed = true;
        }
    });

    $('#storyList').on('sort.sortable', function(e, data)
    {
        var list = '';
        for(i = 0; i < data.list.length; i++) list += $(data.list[i].item).attr('data-id') + ',';
        $.post(createLink('roadmap', 'ajaxStorySort', 'roadmapID=' + roadmapID), {'stories' : list, 'orderBy' : orderBy, 'pageID' : storyPageID, 'recPerPage' : storyRecPerPage, 'recTotal' : storyRecTotal}, function()
        {
            var $target = $(data.element[0]);
            $target.hide();
            $target.fadeIn(1000);
            order = 'order_desc';
            history.pushState({}, 0, createLink('roadmap', 'view', "roadmapID=" + roadmapID + '&type=story&orderBy=' + order));
        });
    });

    $('.table-story').table(
    {
        statisticCreator: function(table)
        {
            var $checkedRows = table.getTable().find(table.isDataTable ? '.datatable-row-left.checked' : 'tbody>tr.checked');
            var $originTable = table.isDataTable ? table.$.find('.datatable-origin') : null;
            var checkedTotal = $checkedRows.length;
            if(!checkedTotal) return;

            var checkedEstimate = 0;
            var checkedCase     = 0;
            var rateCount       = checkedTotal;
            $checkedRows.each(function()
            {
                var $row = $(this);
                if($originTable)
                {
                    $row = $originTable.find('tbody>tr[data-id="' + $row.data('id') + '"]');
                }
                var data = $row.data();
                checkedEstimate += data.estimate;

                if(data.cases > 0)
                {
                    checkedCase += 1;
                }
                else if(data.children != undefined && data.children > 0)
                {
                    rateCount -= 1;
                }
            });

            var rate = '0%';
            if(rateCount) rate = Math.round(checkedCase / rateCount * 100) + '%';

            if(checkedTotal == 0) return storySummary;
            return checkedSummary.replace('%total%', checkedTotal)
                  .replace('%estimate%', checkedEstimate.toFixed(1))
                  .replace('%rate%', rate);
        }
    });

    $('#batchUnlinkBtn').click(function()
    {
        if($('#batchUnlinkBtn').hasClass('disabled')) return false;

        var storyIdList = $("input[name^='storyIdList']:checked").map(function()
        {
            return $(this).val();
        }).get();

        var confirmMsg = confirm(confirmBatchUnlinkStory);
        if(confirmMsg == true)
        {
            $.post(createLink('roadmap', 'batchUnlinkUR', "roadmapID=" + roadmapID), {"storyIdList": storyIdList}, function(data)
            {
                if(data) alert(data);
                if(storyListSession) location.href = storyListSession;
                location.href = createLink('roadmap', 'view', "roadmapID=" + roadmapID);
            })
        }
        else
        {
            return false;
        }
    });

    if(canBatchChangeRoadmap)
    {
        $("#storyList input[id^=storyIdList]").on("click", function()
        {
            if($('#roadmapURList .table-actions .dropdown-menu').css('display') != 'none')
            {
                $('#roadmapURList .table-actions .dropdown-toggle').trigger('click');
            }
        });

        $('.table-actions.btn-toolbar button.dropdown-toggle').click(function()
        {
            if(!canBatchChangeRoadmap) return;
            var selectRows     = $('#storyList tr.checked').map(function(){return $(this).data('id')}).get();
            var selectBranches = new Set();
            for(var row of selectRows)
            {
                var branch = roadmapStories[row].branch;
                selectBranches.add(branch);
            }
            branches = Array.from(selectBranches).join();

            link = createLink('roadmap', 'ajaxGetChangeRoadmaps', 'product=' + product + '&roadmapID=' + roadmapID + '&branch=' + branches);
            $('#roadmapRows').load(link, function()
            {
                if($('#roadmapRows li').length <= 9 )
                {
                    $('#searchMenu').hide();
                }
                else
                {
                    $('#searchMenu').show();
                }
            });
        });
    };
});
