function changeView(view)
{
    var link = createLink('execution', 'taskKanban', "executionID=" + executionID + '&type=' + view);
    location.href = link;
}

/**
 * Render user avatar
 * @param {String|{account: string, avatar: string}} user User account or user object
 * @returns {string}
 */
function renderUserAvatar(user, objectType, objectID, size)
{
    var avatarSizeClass = 'avatar-' + (size || 'sm');
    var $noPrivAndNoAssigned = $('<div class="avatar has-text ' + avatarSizeClass + ' avatar-circle" title="' + noAssigned + '" style="background: #ccc"><i class="icon icon-person"></i></div>');
    if(objectType == 'task')
    {
        if(!priv.canAssignTask && !user) return $noPrivAndNoAssigned;
        var link = createLink('task', 'assignto', 'executionID=' + executionID + '&id=' + objectID, '', true);
    }
    if(objectType == 'story')
    {
        if(!priv.canAssignStory && !user) return $noPrivAndNoAssigned;
        var link = createLink('story', 'assignto', 'id=' + objectID, '', true);
    }
    if(objectType == 'bug')
    {
        if(!priv.canAssignBug && !user) return $noPrivAndNoAssigned;
        var link = createLink('bug', 'assignto', 'id=' + objectID, '', true);
    }

    if(!user) return $('<a class="avatar has-text ' + avatarSizeClass + ' avatar-circle iframe" title="' + noAssigned + '" style="background: #ccc" href="' + link + '"><i class="icon icon-person"></i></a>');

    if(typeof user === 'string') user = {account: user};
    if(!user.avatar && window.userList && window.userList[user.account]) user = window.userList[user.account];

    var $noPrivAvatar = $('<div class="avatar has-text ' + avatarSizeClass + ' avatar-circle" />').avatar({user: user});
    if(objectType == 'task'  && !priv.canAssignTask)  return $noPrivAvatar;
    if(objectType == 'story' && !priv.canAssignStory) return $noPrivAvatar;
    if(objectType == 'bug'   && !priv.canAssignBug)   return $noPrivAvatar;

    return $('<a class="avatar has-text ' + avatarSizeClass + ' avatar-circle iframe" title="' + user.realname + '" href="' + link + '"/>').avatar({user: user});
}

/**
 * Render deadline
 * @param {String|Date} deadline Deadline
 * @returns {JQuery}
 */
function renderDeadline(deadline)
{
    if(deadline == '0000-00-00') return;

    var date = $.zui.createDate(deadline);
    var now  = new Date();
    now.setHours(0);
    now.setMinutes(0);
    now.setSeconds(0);
    now.setMilliseconds(0);
    var isEarlyThanToday = date.getTime() < now.getTime();
    var deadlineDate     = $.zui.formatDate(date, 'MM-dd');

    return $('<span class="info info-deadline"/>').text(deadlineLang + ' ' + deadlineDate).addClass(isEarlyThanToday ? 'text-red' : 'text-muted');
}

/**
 * Render story item
 * @param {Object} item  Story item object
 * @param {JQuery} $item Kanban item element
 * @param {Object} col   Column object
 * @returns {JQuery} $item Kanban item element
 */
function renderStoryItem(item, $item, col)
{
    var scaleSize = window.kanbanScaleSize;
    if(+$item.attr('data-scale-size') !== scaleSize) $item.empty().attr('data-scale-size', scaleSize);

    if(scaleSize <= 3)
    {
        var $title = $item.find('.title');
        if(!$title.length)
        {
            $title = $('<a class="title iframe" data-width="95%">' + (scaleSize <= 1 ? '<i class="icon icon-lightbulb text-muted"></i> ' : '') + '<span class="text"></span></a>')
                    .attr('href', $.createLink('story', 'view', 'storyID=' + item.id, '', true));
            $title.appendTo($item);
        }
        $title.attr('title', item.title).find('.text').text(item.title);
    }

    if(scaleSize <= 2)
    {
        var idHtml     = scaleSize <= 1 ? ('<span class="info info-id text-muted">#' + item.id + '</span>') : '';
        var priHtml    = '<span class="info info-pri label-pri label-pri-' + item.pri + '" title="' + item.pri + '">' + item.pri + '</span>';
        var hoursHtml  = (item.estimate && scaleSize <= 1) ? ('<span class="info info-estimate text-muted">' + item.estimate + 'h</span>') : '';
        var avatarHtml = renderUserAvatar(item.assignedTo, 'story', item.id);
        var $infos = $item.find('.infos');
        if(!$infos.length) $infos = $('<div class="infos"></div>');
        $infos.html([idHtml, priHtml, hoursHtml].join(''));

        $infos[scaleSize <= 1 ? 'append' : 'prepend'](avatarHtml);
        if(scaleSize <= 1) $infos.appendTo($item);
        else if(scaleSize === 2) $infos.prependTo($item);
        else $infos.prependTo($item.find('.title'));
    }
    else if(scaleSize === 4)
    {
        $item.html(renderUserAvatar(item.assignedTo, 'story', item.id, 'md'));
    }

    if(scaleSize <= 1)
    {
        var $actions = $item.find('.actions');
        if(!$actions.length && item.menus && item.menus.length)
        {
            $actions = $([
                '<div class="actions">',
                    '<a data-contextmenu="story" data-col="' + col.type + '">',
                        '<i class="icon icon-ellipsis-v"></i>',
                    '</a>',
                '</div>'
            ].join('')).appendTo($item);
        }
    }

    return $item.attr('data-type', 'story').addClass('kanban-item-story');
}

/**
 * Render bug item
 * @param {Object} item  Bug item object
 * @param {JQuery} $item Kanban item element
 * @param {Object} col   Column object
 * @returns {JQuery} $item Kanban item element
 */
function renderBugItem(item, $item, col)
{
    var scaleSize = window.kanbanScaleSize;
    if(+$item.attr('data-scale-size') !== scaleSize) $item.empty().attr('data-scale-size', scaleSize);

    if(scaleSize <= 3)
    {
        var $title = $item.find('.title');
        if(!$title.length)
        {
            $title = $('<a class="title iframe" data-width="95%">' + (scaleSize <= 1 ? '<i class="icon icon-bug text-muted"></i> ' : '') + '<span class="text"></span></a>')
                    .attr('href', $.createLink('bug', 'view', 'bugID=' + item.id, '', true));
            $title.appendTo($item);
        }
        $title.attr('title', item.title).find('.text').text(item.title);
    }

    if(scaleSize <= 2)
    {
        var idHtml       = scaleSize <= 1 ? ('<span class="info info-id text-muted">#' + item.id + '</span>') : '';
        var severityHtml = scaleSize <= 1 ? ('<span class="info info-severity label-severity" data-severity="' + item.severity + '" title="' + item.severity + '"></span>') : '';
        var priHtml      = '<span class="info info-pri label-pri label-pri-' + item.pri + '" title="' + item.pri + '">' + item.pri + '</span>';
        var avatarHtml   = renderUserAvatar(item.assignedTo, 'bug', item.id);

        var $infos = $item.find('.infos');
        if(!$infos.length) $infos = $('<div class="infos"></div>');
        $infos.html([idHtml, severityHtml, priHtml].join(''));
        if(item.deadline && scaleSize <= 1) $infos.append(renderDeadline(item.deadline));
        $infos[scaleSize <= 1 ? 'append' : 'prepend'](avatarHtml);

        if(scaleSize <= 1) $infos.appendTo($item);
        else if(scaleSize === 2) $infos.prependTo($item);
        else $infos.prependTo($item.find('.title'));
    }
    else if(scaleSize === 4)
    {
        $item.html(renderUserAvatar(item.assignedTo, 'bug', item.id, 'md'));
    }

    if(scaleSize <= 1)
    {
        var $actions = $item.find('.actions');
        if(!$actions.length && item.menus && item.menus.length)
        {
            $actions = $([
                '<div class="actions">',
                    '<a data-contextmenu="bug" data-col="' + col.type + '">',
                        '<i class="icon icon-ellipsis-v"></i>',
                    '</a>',
                '</div>'
            ].join('')).appendTo($item);
        }
    }

    return $item.attr('data-type', 'bug').addClass('kanban-item-bug');
}

/**
 * Render task item
 * @param {Object} item  Task item object
 * @param {JQuery} $item Kanban item element
 * @param {Object} col   Column object
 * @returns {JQuery} $item Kanban item element
 */
function renderTaskItem(item, $item, col)
{
    var scaleSize = window.kanbanScaleSize;
    if(+$item.attr('data-scale-size') !== scaleSize)  $item.empty().attr('data-scale-size', scaleSize);

    if(scaleSize <= 3)
    {
        var $title = $item.find('.title');
        if(!$title.length)
        {
            $title = $('<a class="title iframe" data-width="95%">' + (scaleSize <= 1 ? '<i class="icon icon-checked text-muted"></i> ' : '') + '<span class="text"></span></a>')
                    .attr('href', $.createLink('task', 'view', 'taskID=' + item.id, '', true));
            $title.appendTo($item);
        }
        $title.attr('title', item.name).find('.text').text(item.name);
    }

    if(scaleSize <= 2)
    {
        var idHtml     = scaleSize <= 1 ? ('<span class="info info-id text-muted">#' + item.id + '</span>') : '';
        var priHtml    = '<span class="info info-pri label-pri label-pri-' + item.pri + '" title="' + item.pri + '">' + item.pri + '</span>';
        var hoursHtml  = (item.estimate && scaleSize <= 1) ? ('<span class="info info-estimate text-muted">' + item.estimate + 'h</span>') : '';
        var avatarHtml = renderUserAvatar(item.assignedTo, 'task', item.id);

        var $infos = $item.find('.infos');
        if(!$infos.length) $infos = $('<div class="infos"></div>');
        $infos.html([idHtml, priHtml, hoursHtml].join(''));
        if(item.deadline && scaleSize <= 1) $infos.append(renderDeadline(item.deadline));
        $infos[scaleSize <= 1 ? 'append' : 'prepend'](avatarHtml);

        if(scaleSize <= 1) $infos.appendTo($item);
        else if(scaleSize === 2) $infos.prependTo($item);
        else $infos.prependTo($item.find('.title'));
    }
    else if(scaleSize === 4)
    {
        $item.html(renderUserAvatar(item.assignedTo, 'task', item.id, 'md'));
    }

    if(scaleSize <= 1)
    {
        var $actions = $item.find('.actions');
        if(!$actions.length && item.menus && item.menus.length)
        {
            $actions = $([
                '<div class="actions">',
                    '<a data-contextmenu="task" data-col="' + col.type + '">',
                        '<i class="icon icon-ellipsis-v"></i>',
                    '</a>',
                '</div>'
            ].join('')).appendTo($item);
        }
    }

    $item.attr('data-type', 'task').addClass('kanban-item-task');

    return $item;
}

/* Add column renderer */
addColumnRenderer('story', renderStoryItem);
addColumnRenderer('bug',   renderBugItem);
addColumnRenderer('task',  renderTaskItem);

/**
 * Render column count
 * @param {JQuery} $count Kanban count element
 * @param {number} count  Column cards count
 * @param {number} col    Column object
 * @param {Object} kanban Kanban intance
 */
function renderColumnCount($count, count, col)
{
    var text = count + '/' + (col.limit < 0 ? '<i class="icon icon-infinite"></i>' : col.limit);
    $count.html(text + '<i class="icon icon-arrow-up"></i>');
}

/**
 * Render header column
 * @param {JQuery} $col    Header column element
 * @param {Object} col     Header column object
 * @param {JQuery} $header Header element
 * @param {Object} kanban  Kanban object
 */
function renderHeaderCol($col, col, $header, kanban)
{
    if(col.asParent) $col = $col.children('.kanban-header-col');
    var $actions = $('<div class="actions" />');
    var printStoryButton =  printTaskButton = printBugButton = false;
    if(priv.canCreateStory || priv.canBatchCreateStory || priv.canLinkStory || priv.canLinkStoryByPlan) printStoryButton = true;
    if(priv.canCreateTask  || priv.canBatchCreateTask) printTaskButton = true;
    if(priv.canCreateBug   || priv.canBatchCreateBug)  printBugButton  = true;

    if((col.type === 'backlog' && printStoryButton) || (col.type === 'wait' && printTaskButton) || (col.type == 'unconfirmed' && printBugButton))
    {
        $actions.append([
                '<a data-contextmenu="columnCreate" data-type="' + col.type + '" data-kanban="' + kanban.id + '" data-parent="' + (col.parentType || '') +  '" class="text-primary">',
                '<i class="icon icon-expand-alt"></i>',
                '</a>'
        ].join(''));
    }

    $actions.append([
            '<a data-contextmenu="column" title="' + kanbanLang.moreAction + '" data-type="' + col.type + '" data-kanban="' + kanban.id + '" data-parent="' + (col.parentType || '') +  '">',
            '<i class="icon icon-ellipsis-v"></i>',
            '</a>'
    ].join(''));
    $actions.appendTo($col);
}

/**
 * Render lane name
 * @param {JQuery} $name    Name element
 * @param {Object} lane     Lane object
 * @param {JQuery} $kanban  $kanban element
 * @param {Object} columns  Kanban columns
 * @param {Object} kanban   Kanban object
 */
function renderLaneName($name, lane, $kanban, columns, kanban)
{
    if(lane.id != 'story' && lane.id != 'task' && lane.id != 'bug') return false;
    if(!$name.children('.actions').length && (priv.canSetLane || priv.canMoveLane))
    {
        $([
            '<div class="actions" title="' + kanbanLang.moreAction + '">',
                '<a data-contextmenu="lane" data-lane="' + lane.id + '" data-kanban="' + kanban.id + '">',
                    '<i class="icon icon-ellipsis-v"></i>',
                '</a>',
            '</div>'
        ].join('')).appendTo($name);
    }
}

/**
 * Updata kanban data
 * @param {string} kanbanID Kanban id
 * @param {Object} data     Kanban data
 */
function updateKanban(kanbanID, data)
{
    var $kanban = $('#kanban-' + kanbanID);
    if(!$kanban.length) return;

    $kanban.data('zui.kanban').render(data);
}

/**
 * Create kanban in page
 * @param {string} kanbanID Kanban id
 * @param {Object} data     Kanban data
 * @param {Object} options  Kanban options
 */
function createKanban(kanbanID, data, options)
{
    var $kanban      = $('#kanban-' + kanbanID);
    var displayCards = window.displayCards == 'undefined' ? 2 : window.displayCards;
    if($kanban.length) return updateKanban(kanbanID, data);

    $kanban = $('<div id="kanban-' + kanbanID + '" data-id="' + kanbanID + '"></div>').appendTo('#kanbans');
    $kanban.kanban($.extend({data: data, calcColHeight: calcColHeight, displayCards: displayCards}, options));
}

function fullScreen()
{
    var element       = document.getElementById('kanbanContainer');
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
    if(requestMethod)
    {
        var afterEnterFullscreen = function()
        {
            $('#kanbanContainer').addClass('scrollbar-hover');
            $('.actions').hide();
            $('#kanbanContainer a.iframe').each(function()
            {
                if($(this).hasClass('iframe'))
                {
                    var href = $(this).attr('href');
                    $(this).removeClass('iframe');
                    $(this).attr('href', 'javascript:void(0)');
                    $(this).attr('href-bak', href);
                }
            })
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
    $('#kanbanContainer').removeClass('scrollbar-hover');
    $('.actions').show();
    $('#kanbanContainer a').each(function()
    {
        var hrefBak = $(this).attr('href-bak');
        if(hrefBak)
        {
            $(this).addClass('iframe');
            $(this).attr('href', hrefBak);
        }
    })
    $.cookie('isFullScreen', 0);
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

/* Define drag and drop rules */
if(!window.kanbanDropRules)
{
    window.kanbanDropRules =
    {
        story:
        {
            backlog: ['ready'],
            ready: ['backlog'],
        },
        bug:
        {
            'unconfirmed': ['confirmed', 'fixing', 'fixed'],
            'confirmed': ['fixing', 'fixed'],
            'fixing': ['fixed'],
            'fixed': ['testing', 'tested', 'fixing'],
            'testing': ['tested', 'closed', 'fixing'],
            'tested': ['closed', 'fixing'],
            'closed': ['fixing'],
        },
        task:
        {
            'wait': ['developing', 'developed', 'canceled', 'closed'],
            'developing': ['developed', 'pause', 'canceled'],
            'developed': ['closed'],
            'pause': ['developing'],
            'canceled': ['developing'],
            'closed': ['developing'],
        }
    }
}

/*
 * Find drop columns
 * @param {JQuery} $element Drag element
 * @param {JQuery} $root Dnd root element
 */
function findDropColumns($element, $root)
{
    var $col        = $element.closest('.kanban-col');
    var col         = $col.data();
    var kanbanID    = $root.data('id');
    var kanbanRules = window.kanbanDropRules ? window.kanbanDropRules[kanbanID] : null;

    if(!kanbanRules) return $root.find('.kanban-lane-col:not([data-type="' + col.type + '"])');

    var colRules = kanbanRules[col.type];
    var lane     = $col.closest('.kanban-lane').data('lane');
    return $root.find('.kanban-lane-col').filter(function()
    {
        if(!colRules) return false;
        if(colRules === true) return true;
        if($.cookie('isFullScreen') == 1) return false;

        var $newCol = $(this);
        var newCol = $newCol.data();
        if(newCol.id === col.id) return false;

        var $newLane = $newCol.closest('.kanban-lane');
        var newLane = $newLane.data('lane');
        var canDropHere = colRules.indexOf(newCol.type) > -1 && newLane.id === lane.id;
        if(canDropHere) $newCol.addClass('can-drop-here');
        return canDropHere;
    });
}

/**
 * Change card's type by changing column.
 *
 * @param  int    $cardID
 * @param  int    $fromColID
 * @param  int    $toColID
 * @param  int    $fromLaneID
 * @param  int    $toLaneID
 * @param  string $cardType
 * @param  string $fromColType
 * @param  string $toColType
 * @access public
 * @return void
 */
function changeCardColType(cardID, fromColID, toColID, fromLaneID, toLaneID, cardType, fromColType, toColType)
{
    var objectID   = cardID;
    var showIframe = false;
    var moveCard   = false;

    /* Task lane. */
    if(cardType == 'task')
    {
        if(toColType == 'developed')
        {
            if((fromColType == 'developing' || fromColType == 'wait') && priv.canFinishTask)
            {
                var link   = createLink('task', 'finish', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'pause')
        {
            if(fromColType == 'developing' && priv.canPauseTask)
            {
                var link = createLink('task', 'pause', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'developing')
        {
            if((fromColType == 'pause' || fromColType == 'canceled' || fromColType == 'closed' || fromColType == 'developed') && priv.canActivateTask)
            {
                var link = createLink('task', 'activate', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
            if(fromColType == 'wait' && priv.canStartTask)
            {
                var link = createLink('task', 'start', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'canceled')
        {
            if((fromColType == 'developing' || fromColType == 'wait' || fromColType == 'pause') && priv.canCancelTask)
            {
                var link = createLink('task', 'cancel', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'closed')
        {
            if((fromColType == 'developed' || fromColType == 'canceled') && priv.canCloseTask)
            {
                var link = createLink('task', 'close', 'taskID=' + objectID, '', true);
                showIframe = true;
            }
        }
    }

    /* Bug lane. */
    if(cardType == 'bug')
    {
        if(toColType == 'confirmed')
        {
            if(fromColType == 'unconfirmed' && priv.canConfirmBug)
            {
                var link = createLink('bug', 'confirmBug', 'bugID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'fixing')
        {
            if(fromColType == 'confirmed' || fromColType == 'unconfirmed') moveCard = true;
            if((fromColType == 'closed' || fromColType == 'fixed' || fromColType == 'testing' || fromColType == 'tested') && priv.canActivateBug)
            {
                var link = createLink('bug', 'activate', 'bugID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'fixed')
        {
            if(fromColType == 'fixing' || fromColType == 'confirmed' || fromColType == 'unconfirmed')
            {
                var link = createLink('bug', 'resolve', 'bugID=' + objectID, '', true);
                showIframe = true;
            }
        }
        else if(toColType == 'testing')
        {
            if(fromColType == 'fixed') moveCard = true;
        }
        else if(toColType == 'tested')
        {
            if(fromColType == 'fixed' || fromColType == 'testing') moveCard = true;
        }
        else if(toColType == 'closed')
        {
            if(fromColType == 'testing' || fromColType == 'tested')
            {
                var link = createLink('bug', 'close', 'bugID=' + objectID, '', true);
                showIframe = true;
            }
        }

        if(moveCard)
        {
            var link  = createLink('kanban', 'ajaxMoveCard', 'cardID=' + objectID + '&fromColID=' + fromColID + '&toColID=' + toColID + '&fromLaneID=' + fromLaneID + '&toLaneID=' + toLaneID + '&execitionID=' + executionID + '&browseType=' + browseType + '&groupBy=' + groupBy);
            $.get(link, function(data)
            {
                if(data)
                {
                    kanbanGroup = $.parseJSON(data);
                    if(groupBy == 'default')
                    {
                        updateKanban('bug', kanbanGroup.bug);
                    }
                    else
                    {
                        updateKanban(browseType, kanbanGroup[groupBy]);
                    }
                }
            })
        }
    }

    /* Story lane. */
    if(cardType == 'story')
    {
        if(toColType == 'ready' || toColType == 'backlog')
        {
            var link  = createLink('kanban', 'ajaxMoveCard', 'cardID=' + objectID + '&fromColID=' + fromColID + '&toColID=' + toColID + '&fromLaneID=' + fromLaneID + '&toLaneID=' + toLaneID + '&execitionID=' + executionID + '&browseType=' + browseType + '&groupBy=' + groupBy);
            $.get(link, function(data)
            {
                if(data)
                {
                    kanbanGroup = $.parseJSON(data);
                    if(groupBy == 'default')
                    {
                        updateKanban('story', kanbanGroup.story);
                    }
                    else
                    {
                        updateKanban(browseType, kanbanGroup[groupBy]);
                    }
                }
            })
        }
    }

    if(showIframe)
    {
        var modalTrigger = new $.zui.ModalTrigger({type: 'iframe', width: '80%', url: link});
        modalTrigger.show();
    }
}

/**
 * Handle drop task.
 *
 * @param  object $element
 * @param  object $event
 * @param  object $kanban
 * @access public
 * @return void
 */
function handleDropTask($element, event, kanban)
{
    if(!event.target) return;

    var $card    = $element;
    var $oldCol  = $card.closest('.kanban-col');
    var $newCol  = $(event.target).closest('.kanban-col');
    var oldCol   = $oldCol.data();
    var newCol   = $newCol.data();
    var oldLane  = $oldCol.closest('.kanban-lane').data('lane');
    var newLane  = $newCol.closest('.kanban-lane').data('lane');
    var cardType = $card.find('.kanban-card').data('type');

    if(oldCol.id === newCol.id && newLane.id === oldLane.id) return false;

    var cardID      = $card.data().id;
    var fromColType = $oldCol.data('type');
    var toColType   = $newCol.data('type');

    changeCardColType(cardID, oldCol.id, newCol.id, oldLane.id, newLane.id, cardType, fromColType, toColType);
}

var kanbanActionHandlers =
{
    dropItem: handleDropTask
};

/**
 * Handle kanban action.
 *
 * @param  string $action
 * @param  object $element
 * @param  object $event
 * @param  object $kanban
 * @access public
 * @return void
 */
function handleKanbanAction(action, $element, event, kanban)
{
    if(groupBy && groupBy != 'default') return false;
    $('.kanban').attr('data-action-enabled', action);
    var handler = kanbanActionHandlers[action];
    if(handler) handler($element, event, kanban);
}

/**
 * Handle finish drop task
 * @param {Object} event Event object
 * @returns {void}
 */
function handleFinishDrop(event)
{
    $('#kanbans').find('.can-drop-here').removeClass('can-drop-here');
}

/** Handle sort cards in column */
function handleSortColCards()
{
    /* TODO: handle sort cards from column contextmenu */
    return false;
}

/**
 * Create column menu
 * @returns {Object[]}
 */
function createColumnMenu(options)
{
    var $col     = options.$trigger.closest('.kanban-col');
    var col      = $col.data('col');
    var kanbanID = options.kanban;

	var items = [];
	if(priv.canEditName) items.push({label: executionLang.editName, url: $.createLink('kanban', 'setColumn', 'col=' + col.id + '&executionID=' + executionID + '&from=execution'), className: 'iframe', attrs: {'data-width': '500px'}})
	if(priv.canSetWIP) items.push({label: executionLang.setWIP, url: $.createLink('kanban', 'setWIP', 'col=' + col.id + '&executionID=' + executionID + '&from=execution'), className: 'iframe', attrs: {'data-width': '500px'}})
	//if(priv.canSortCards) items.push({label: executionLang.sortColumn, items: ['按ID倒序', '按ID顺序'], className: 'iframe', onClick: handleSortColCards})
    return items;
}

/**
 * Create column create button menu
 * @returns {Object[]}
 */
function createColumnCreateMenu(options)
{
    var $col  = options.$trigger.closest('.kanban-col');
    var col   = $col.data('col');
    var items = [];

    if(col.laneType == 'story')
    {
        if(priv.canCreateStory) items.push({label: storyLang.create, url: $.createLink('story', 'create', 'productID=' + productID, '', true), className: 'iframe'});
        if(priv.canBatchCreateStory) items.push({label: executionLang.batchCreateStroy, url: $.createLink('story', 'batchcreate', 'productID=' + productID + '&branch=0&moduleID=0&storyID=0&executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '90%'}});
        if(priv.canLinkStory) items.push({label: executionLang.linkStory, url: $.createLink('execution', 'linkStory', 'executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '90%'}});
        if(priv.canLinkStoryByPlan) items.push({label: executionLang.linkStoryByPlan, url: '#linkStoryByPlan', 'attrs' : {'data-toggle': 'modal'}});
    }
    else if(col.laneType == 'bug')
    {
        if(priv.canCreateBug) items.push({label: bugLang.create, url: $.createLink('bug', 'create', 'productID=0&moduleID=0&extra=executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '80%'}});
        if(priv.canBatchCreateBug) items.push({label: bugLang.batchCreate, url: $.createLink('bug', 'batchcreate', 'productID=' + productID + '&moduleID=0&executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '90%'}});
    }
    else
    {
        if(priv.canCreateTask) items.push({label: taskLang.create, url: $.createLink('task', 'create', 'executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '80%'}});
        if(priv.canBatchCreateTask) items.push({label: taskLang.batchCreate, url: $.createLink('task', 'batchcreate', 'executionID=' + executionID, '', true), className: 'iframe', attrs: {'data-width': '80%'}});
    }
    return items;
}

/**
 * Create lane menu
 * @returns {Object[]}
 */
function createLaneMenu(options)
{
    var $lane            = options.$trigger.closest('.kanban-lane');
    var $kanban          = $lane.closest('.kanban');
    var lane             = $lane.data('lane');
    var kanbanID         = options.kanban;
    var upTargetKanban   = $kanban.prev('.kanban').length ? $kanban.prev('.kanban').data('id') : '';
    var downTargetKanban = $kanban.next('.kanban').length ? $kanban.next('.kanban').data('id') : '';

    var items = [];
    if(priv.canSetLane)  items.push({label: kanbanLang.setLane, icon: 'edit', url: $.createLink('kanban', 'setLane', 'lane=' + lane.laneID + '&executionID=' + executionID + '&from=execution'), className: 'iframe'});
    if(priv.canMoveLane) items.push(
        {label: kanbanLang.moveUp, icon: 'arrow-up', url: $.createLink('kanban', 'laneMove', 'executionID=' + executionID + '&currentLane=' + lane.id + '&targetLane=' + upTargetKanban), className: 'iframe', disabled: !$kanban.prev('.kanban').length},
        {label: kanbanLang.moveDown, icon: 'arrow-down', url: $.createLink('kanban', 'laneMove', 'executionID=' + executionID + '&currentLane=' + lane.id + '&targetLane=' + downTargetKanban), className: 'iframe', disabled: !$kanban.next('.kanban').length}
    );

    var bounds = options.$trigger[0].getBoundingClientRect();
    items.$options = {x: bounds.right, y: bounds.top};
    return items;
}

/**
 * Create story menu
 * @returns {Object[]}
 */
function createStoryMenu(options)
{
    var $card = options.$trigger.closest('.kanban-item');
    var story = $card.data('item');

    var items = [];
    $.each(story.menus, function()
    {
        var item = {label: this.label, icon: this.icon, url: this.url, attrs: {'data-toggle': 'modal', 'data-type': 'iframe'}};
        if(this.size) item.attrs['data-width'] = this.size;

        if(this.icon == 'unlink') item = {label: this.label, icon: this.icon, url: this.url, attrs: {'target': 'hiddenwin'}};
        items.push(item);
    });

    return items;
}

/**
 * Create bug menu
 * @returns {Object[]}
 */
function createBugMenu(options)
{
    var $card = options.$trigger.closest('.kanban-item');
    var bug   = $card.data('item');

    var items = [];
    $.each(bug.menus, function()
    {
        var item = {label: this.label, icon: this.icon, url: this.url, attrs: {'data-toggle': 'modal', 'data-type': 'iframe'}};
        if(this.size) item.attrs['data-width'] = this.size;

        items.push(item);
    });

    return items;
}

 /**
 * Create task menu
 * @returns {Object[]}
 */
function createTaskMenu(options)
{
    var $card = options.$trigger.closest('.kanban-item');
    var task  = $card.data('item');

    var items = [];
    $.each(task.menus, function()
    {
        var item = {label: this.label, icon: this.icon, url: this.url, attrs: {'data-toggle': 'modal', 'data-type': 'iframe'}};
        if(this.size) item.attrs['data-width'] = this.size;

        items.push(item);
    });

    return items;
}

/** Resize kanban container size */
function resizeKanbanContainer()
{
    var $container = $('#kanbanContainer');
    var maxHeight = window.innerHeight - 98 - 15;
    if($.cookie('isFullScreen') == 1) maxHeight = window.innerHeight - 15;
    $container.children('.panel-body').css('max-height', maxHeight);
}

/* Define menu creators */
window.menuCreators =
{
    column:       createColumnMenu,
    columnCreate: createColumnCreateMenu,
    lane:         createLaneMenu,
    story:        createStoryMenu,
    bug:          createBugMenu,
    task:         createTaskMenu,
};

/* Set kanban affix container */
window.kanbanAffixContainer = '#kanbanContainer>.panel-body';

/* Overload kanban default options */
$.extend($.fn.kanban.Constructor.DEFAULTS,
{
    onRender: function()
    {
        var maxWidth = 0;
        $('#kanbans .kanban-board').each(function()
        {
            maxWidth = Math.max(maxWidth, $(this).outerWidth());
        });
        $('#kanbans').css('min-width', maxWidth);
    }
});

/** Get card height */
function getCardHeight()
{
    return [59, 59, 62, 62, 47][window.kanbanScaleSize];
}

/** Change kanban scale size */
function changeKanbanScaleSize(newScaleSize)
{
    var newScaleSize = Math.max(1, Math.min(4, newScaleSize));
    if(newScaleSize === window.kanbanScaleSize) return;

    window.kanbanScaleSize = newScaleSize;
    $.zui.store.set('executionKanbanScaleSize', newScaleSize);
    $('#kanbanScaleSize').text(newScaleSize);
    $('#kanbanScaleControl .btn[data-type="+"]').attr('disabled', newScaleSize >= 4 ? 'disabled' : null);
    $('#kanbanScaleControl .btn[data-type="-"]').attr('disabled', newScaleSize <= 1 ? 'disabled' : null);

    $('#kanbans').children('.kanban').each(function()
    {
        var kanban = $(this).data('zui.kanban');
        if(!kanban) return;
        kanban.setOptions({cardsPerRow: newScaleSize, cardHeight: getCardHeight()});
    });

    return newScaleSize;
}

/* Example code: */
$(function()
{
    $.cookie('isFullScreen', 0);

    window.kanbanScaleSize = +$.zui.store.get('executionKanbanScaleSize', 1);
    $('#kanbanScaleSize').text(window.kanbanScaleSize);
    $('#kanbanScaleControl .btn[data-type="+"]').attr('disabled', window.kanbanScaleSize >= 4 ? 'disabled' : null);
    $('#kanbanScaleControl .btn[data-type="-"]').attr('disabled', window.kanbanScaleSize <= 1 ? 'disabled' : null);

    /* Common options */　
    var commonOptions =
    {
        maxColHeight:         'auto',
        minColWidth:          240,
        maxColWidth:          240,
        cardHeight:           getCardHeight(),
        showCount:            true,
        showZeroCount:        true,
        fluidBoardWidth:      fluidBoard,
        cardsPerRow:          window.kanbanScaleSize,
        virtualize:           true,
        onAction:             handleKanbanAction,
        virtualRenderOptions: {container: '#kanbanContainer>.panel-body'},
        droppable:
        {
            target:       findDropColumns,
            finish:       handleFinishDrop,
            mouseButton: 'left'
        },
        onRenderHeaderCol: renderHeaderCol,
        onRenderLaneName:  renderLaneName,
        onRenderCount:     renderColumnCount
    };

    if(groupBy != 'default') commonOptions.droppable = false;

    /* Create kanban */
    if(groupBy == 'default')
    {
        var kanbanLane = '';
        for(var i in kanbanList)
        {
            if(kanbanList[i] == 'story') kanbanLane = kanbanGroup.story;
            if(kanbanList[i] == 'bug')   kanbanLane = kanbanGroup.bug;
            if(kanbanList[i] == 'task')  kanbanLane = kanbanGroup.task;

            if(browseType == kanbanList[i] || browseType == 'all') createKanban(kanbanList[i], kanbanLane, commonOptions);
        }
    }
    else
    {
        /* Create kanban by group. */
        createKanban(browseType, kanbanGroup[groupBy], commonOptions);
    }

    /* Init iframe modals */
    $(document).on('click', '#kanbans .iframe,.contextmenu-menu .iframe', function(event)
    {
        var $link = $(this);
        if($link.data('zui.modaltrigger')) return;
        $link.modalTrigger({show: true});
        event.preventDefault();
    });

    /* Init contextmenu */
    $('#kanbans').on('click', '[data-contextmenu]', function(event)
    {
        var $trigger    = $(this);
        var menuType    = $trigger.data('contextmenu');

        var menuCreator = window.menuCreators[menuType];
        if(!menuCreator) return;

        var options = $.extend({event: event, $trigger: $trigger}, $trigger.data());
        var items   = menuCreator(options);
        if(!items || !items.length) return;

        $.zui.ContextMenu.show(items, items.$options || {event: event});
    });

    /* Make kanbanScaleControl works */
    $('#kanbanScaleControl').on('click', '.btn', function()
    {
        changeKanbanScaleSize(window.kanbanScaleSize + ($(this).data('type') === '+' ? 1 : -1));
    });

    /* Resize kanban container on window resize */
    resizeKanbanContainer();
    $(window).on('resize', resizeKanbanContainer);

    /* Hide contextmenu when page scroll */
    $(window).on('scroll', function()
    {
        $.zui.ContextMenu.hide();
    });

    $('#toStoryButton').on('click', function()
    {
        var planID = $('#plan').val();
        if(planID)
        {
            location.href = createLink('execution', 'importPlanStories', 'executionID=' + executionID + '&planID=' + planID + '&productID=0&fromMethod=kanban');
        }
    });

    $('#type_chosen .chosen-single span').prepend('<i class="icon-kanban"></i>');
    $('#group_chosen .chosen-single span').prepend(kanbanLang.laneGroup + ': ');

    /* Ajax update kanban. */
    var lastUpdateData;
    setInterval(function()
    {
        $.get(createLink('execution', 'ajaxUpdateKanban', "executionID=" + executionID + "&entertime=" + entertime + "&browseType=" + browseType + "&groupBy=" + groupBy), function(data)
        {
            if(data && lastUpdateData !== data)
            {
                lastUpdateData = data;
                kanbanGroup = $.parseJSON(data);
                if(groupBy == 'default')
                {
                    var kanbanLane = '';
                    for(var i in kanbanList)
                    {
                        if(kanbanList[i] == 'story') kanbanLane = kanbanGroup.story;
                        if(kanbanList[i] == 'bug')   kanbanLane = kanbanGroup.bug;
                        if(kanbanList[i] == 'task')  kanbanLane = kanbanGroup.task;

                        if(browseType == kanbanList[i] || browseType == 'all') updateKanban(kanbanList[i], kanbanLane);
                    }
                }
                else
                {
                    updateKanban(browseType, kanbanGroup[groupBy]);
                }
            }
        });
    }, 10000);
});

$('#type').change(function()
{
    var type = $('#type').val();
    if(type != 'all')
    {
        $('.c-group').show();
        $.get(createLink('execution', 'ajaxGetGroup', 'type=' + type), function(data)
        {
            $('#group_chosen').remove();
            $('#group').replaceWith(data);
            $('#group').chosen();
        })
    }

    var link = createLink('execution', 'taskKanban', "executionID=" + executionID + '&type=' + type);
    location.href = link;
});

$('.c-group').change(function()
{
    $('.c-group').show();

    var type  = $('#type').val();
    var group = $('#group').val();
    var link  = createLink('execution', 'taskKanban', 'executionID=' + executionID + '&type=' + type + '&orderBy=order_asc' + '&groupBy=' + group);
    location.href = link;
});

/** Calculate column height */
function calcColHeight(col, lane, colCards, colHeight, kanban)
{
    var options = kanban.options;
    if(!options.displayCards) return 0;

    var displayCards = +(options.displayCards || 2);

    if (typeof displayCards !== 'number' || displayCards < 2) displayCards = 2;
    return (displayCards * (options.cardHeight + options.cardSpace) + options.cardSpace);
}

/* Hide contextmenu when page scroll */
$('.panel-body').scroll(function()
{
    $.zui.ContextMenu.hide();
});
