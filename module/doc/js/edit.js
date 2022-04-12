$(function()
{
    setTimeout(function()
    {
        if(needUpdateContent && confirm(confirmUpdateContent))
        {
            $('#content').html(draft);
            editor = KindEditor.instances[0];
            editor.html('' + draft);
            $('.kindeditor-ph').remove();
        }
    }, 100)

    $('#top-submit').click(function()
    {
        $(this).addClass('disabled');
        $('form').submit();
    })
    toggleAcl($('input[name="acl"]:checked').val(), 'doc');
    $('input[name="type"]').change(function()
    {
        var type = $(this).val();
        if(type == 'text')
        {
            $('#contentBox').removeClass('hidden');
            $('#urlBox').addClass('hidden');
        }
        else if(type == 'url')
        {
            $('#contentBox').addClass('hidden');
            $('#urlBox').removeClass('hidden');
        }
    });

    $('#subNavbar li[data-id="doc"]').addClass('active');

    /* Automatically save document contents. */
    setInterval("saveDraft()", 60 * 1000);

    $(document).on("mouseup", 'span[data-name="fullscreen"]', function()
    {
        if(config.onlybody == 'no')
        {
            if($(this).hasClass('ke-selected'))
            {
                $('#submit').removeClass('fullscreen-save')
                $('#submit').addClass('btn-wide')
            }
            else
            {
                $('#submit').addClass('fullscreen-save')
                $('#submit').removeClass('btn-wide')
            }
        }
    });

    $(document).on("mouseup", 'a[title="Fullscreen"],.icon-columns', function()
    {
        if(config.onlybody == 'no')
        {
            setTimeout(function()
            {
                if($('a[title="Fullscreen"]').hasClass('active'))
                {
                    $('#submit').addClass('markdown-fullscreen-save')
                    $('#submit').removeClass('btn-wide')
                    $('.fullscreen').css('height', '50px');
                    $('.fullscreen').css('padding-top', '15px');
                    $('.CodeMirror-fullscreen').css('top', '50px');
                    $('.editor-preview-side').css('top', '50px');
                    $('#submit').data('placement', 'bottom');
                }
                else
                {
                    $('#submit').removeClass('markdown-fullscreen-save')
                    $('#submit').addClass('btn-wide')
                    $('.editor-toolbar').css('height', '30px');
                    $('.editor-toolbar').css('padding-top', '1px');
                    $('.CodeMirror').css('top', '0');
                    $('.editor-preview-side').css('top', '30px');
                    $('#submit').data('placement', 'right');
                }
            }, 200);
        }
    });
})

/**
 * Save draft doc.
 *
 * @access public
 * @return void
 */
function saveDraft()
{
    var content = $('#content').val();
    var link    = createLink('doc', 'ajaxSaveDraft', 'docID=' + docID);
    $.post(link, {content: content});
}
