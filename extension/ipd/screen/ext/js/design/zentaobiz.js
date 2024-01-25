window.fetchChartApi = createLink('screen', 'ajaxGetChart');
function saveAsDraft(storageInfo)
{
    save(storageInfo, 'draft');
}

function saveAsPublish(storageInfo)
{
    window.storageInfo = storageInfo;
    exitFullScreen();
    $('#publish').trigger('click');
}

function save(storageInfo, status)
{
    $.post(createLink('screen', 'design', 'screenID=' + screen.id), {scheme: JSON.stringify(storageInfo), status: status}, function(resp)
    {
        resp = JSON.parse(resp);

        zuiMessage(resp.result, resp.message);
        if(resp.result == 'success')
        {
            setTimeout(function()
            {
                exitFullScreen();
                window.location.href = resp.locate;
            }, 2000);
        }
    });
}

function backBrowse()
{
    exitFullScreen();
    window.location.href = backLink;
}

/**
 * Zui messager alert.
 *
 * @param  string result  success|fail
 * @param  string mes
 * @access public
 * @return void
 */
function zuiMessage(result, mes)
{
    var icon = result == 'success' ? 'check-circle' : 'exclamation-sign';
    var type = result == 'success' ? 'success' : 'danger';

    var message = new $.zui.Messager(mes,
    {
        html: true,
        icon: icon,
        type: type,
        close: true,
    });

    message.show();
}

/**
 * Display the kanban in full screen.
 *
 * @access public
 * @return void
 */
function fullScreen()
{
    var element       = document.getElementById('screenContainer');
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
    if(requestMethod)
    {
        var afterEnterFullscreen = function()
        {
            $.cookie('isFullScreen', 1);
        }

        var whenFailEnterFullscreen = function()
        {
            exitFullScreen();
        }

        try
        {
            var result = requestMethod.call(element);
            if(result && (typeof result.then === 'function' || result instanceof window.Promise))
            {
                result.then(afterEnterFullscreen).catch(whenFailEnterFullscreen);
            }
            else
            {
                afterEnterFullscreen();
            }
        }
        catch (error)
        {
            whenFailEnterFullscreen(error);
        }
    }
}

/**
 * Exit full screen.
 *
 * @access public
 * @return void
 */
function exitFullScreen()
{
    $.cookie('isFullScreen', 0);
    let exitFullScreen = document.exitFullScreen ||
    document.mozCancelFullScreen ||
    document.webkitExitFullscreen ||
    document.msExitFullscreen;
    if (exitFullScreen) {
      exitFullScreen.call(document);
    }
}

document.addEventListener('fullscreenchange', function (e)
{
    if(!document.fullscreenElement) exitFullScreen();
});

document.addEventListener('webkitfullscreenchange', function (e)
{
    if(!document.webkitFullscreenElement) exitFullScreen();
});

document.addEventListener('mozfullscreenchange', function (e)
{
    if(!document.mozFullScreenElement) exitFullScreen();
});

document.addEventListener('msfullscreenChange', function (e)
{
    if(!document.msfullscreenElement) exitFullScreen();
});
