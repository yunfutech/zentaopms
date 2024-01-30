$(function()
{
    $('[data-toggle="popover"]').popover();

    var $saveButton      = $('#saveButton');
    var $saveDraftButton = $('#saveDraftButton');
    $saveButton.on('click', function(e)
    {
        $saveButton.attr('type', 'submit').attr('disabled', true);
        $saveDraftButton.attr('disabled', true);

        var storyStatus = !$('#reviewer').val() || $('#needNotReview').is(':checked') ? 'pass' : 'reviewing';
        $('<input />').attr('type', 'hidden').attr('name', 'status').attr('value', storyStatus).appendTo('#dataform');
        $('#dataform').submit();
        e.preventDefault();

        setTimeout(function()
        {
            if($saveButton.attr('disabled') == 'disabled')
            {
                setTimeout(function()
                {
                    $saveButton.attr('type', 'button').removeAttr('disabled');
                    $saveDraftButton.removeAttr('disabled');
                }, 10000);
            }
            else
            {
                $saveDraftButton.removeAttr('disabled');
            }
        }, 100);
    });

    $saveDraftButton.on('click', function(e)
    {
        $saveButton.attr('disabled', true);
        $saveDraftButton.attr('type', 'submit').attr('disabled', true);

        storyStatus = 'draft';
        if(typeof(page) != 'undefined' && page == 'change') storyStatus = 'changing';
        if(typeof(page) !== 'undefined' && page == 'edit' && $('#status').val() == 'changing') storyStatus = 'changing';
        $('<input />').attr('type', 'hidden').attr('name', 'status').attr('value', storyStatus).appendTo('#dataform');
        $('#dataform').submit();
        e.preventDefault();

        setTimeout(function()
        {
            if($saveDraftButton.attr('disabled') == 'disabled')
            {
                setTimeout(function()
                {
                    $saveButton.removeAttr('disabled');
                    $saveDraftButton.attr('type', 'button').removeAttr('disabled');
                }, 10000);
            }
            else
            {
                $saveButton.removeAttr('disabled');
            }
        }, 100);
    });
})

function changePool(poolID)
{
    loadAssignedTo(poolID);
    loadReviewer(poolID);
}

/**
 * Load assignedTo.
 *
 * @access public
 * @return void
 */
function loadAssignedTo(poolID)
{
    var link = createLink('demand', 'ajaxGetOptions', 'poolID=' + poolID + '&type=assignedTo');
    $.post(link, function(html)
    {
        $('#assignedTo').replaceWith(html);
        $('#assignedToBox .picker').remove();
        $('#assignedTo').picker();
    });
}

function loadReviewer(poolID)
{
    var link = createLink('demand', 'ajaxGetOptions', 'poolID=' + poolID + '&type=reviewer');
    $.post(link, function(html)
    {
        $('#reviewer').replaceWith(html);
        $('#reviewerBox .picker').remove();
        $('#reviewer').picker();
    });
}
