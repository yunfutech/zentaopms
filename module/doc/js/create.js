$(function()
{
    toggleAcl($('[name=acl]').val(), 'doc');
    setTimeout(function(){initPage(docType)}, 50);
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
    if(typeof(window.editor) != 'undefined')
    {
        $('.ke-toolbar .ke-outline:last').after("<span data-name='unlink' class='ke-outline' title='Markdown' onclick='toggleEditor(\"markdown\")' style='font-size: unset; line-height: unset;'>Markdown</span>");
    }

    $(document).on("mouseup", 'span[data-name="fullscreen"]', function()
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
    });

    $(document).on("mouseup", 'a[title="Fullscreen"],.icon-columns', function()
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
            }
            else
            {
                $('#submit').removeClass('markdown-fullscreen-save')
                $('#submit').addClass('btn-wide')
                $('.editor-toolbar').css('height', '30px');
                $('.editor-toolbar').css('padding-top', '1px');
                $('.CodeMirror').css('top', '0');
                $('.editor-preview-side').css('top', '30px');
            }
        }, 200);
    });
})

function toggleEditor(type)
{
    if(type == 'html')
    {
        $('.contenthtml').removeClass('hidden');
        $('.contentmarkdown').addClass('hidden');
    }
    else if(type == 'markdown')
    {
        $('.contenthtml').addClass('hidden');
        $('.contentmarkdown').removeClass('hidden');
    }
    $('#contentType').val(type);
    return false;
}

function initPage(type)
{
    if(type == 'html' || type == 'markdown')
    {
        if(type == 'markdown')
        {
            $('#contentBox .contentmarkdown').removeClass('hidden');
            $('#contentBox .contenthtml').addClass('hidden');
            $('#contentBox #contentType').val(type);
        }
    }
    else if(type == 'url')
    {
        $('#contentBox').addClass('hidden');
        $('#urlBox').removeClass('hidden');
    }
    if(type == 'word' || type == 'ppt' || type == 'excel')
    {
        $('#contentBox').hide();
        $('#urlBox').hide();
    }
}
