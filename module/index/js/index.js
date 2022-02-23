(function()
{
    if(showFeatures)
    {
        /* Show features dialog. */
        new $.zui.ModalTrigger({url: $.createLink('misc', 'features'), type: 'iframe', width: 900, showHeader: false, backdrop: 'static'}).show();
    }

    /* Init variables */
    var openedApps      = {}; // Key-value to save appCode-app pairs
    var appsMap         = {}; // Key-value to save opened appCode-app pairs
    var openedAppZIndex = 10; // Last opened app z-index
    var defaultApp;           // Default app code
    var lastOpenedApp;        // Last opened app code

    /**
     * Init apps menu list
     */
    function initAppsMenu()
    {
        var $helpLink = $('#helpLink');
        appsMap.help =
        {
            code:     'help',
            icon:     'icon-help',
            url:      manualUrl || $helpLink.attr('href'),
            external: true,
            text:     manualText || $helpLink.text(),
            appUrl:  config.webRoot + '#app=help'
        };
        var $menuMainNav = $('#menuMainNav').empty();
        window.appsMenuItems.forEach(function(item)
        {
            if(item === 'divider') return $menuMainNav.append('<li class="divider"></li>');

            var $link= $('<a data-pos="menu"></a>')
                .attr('data-app', item.code)
                .attr('data-toggle', 'tooltip')
                .attr('class', 'show-in-app')
                .html(item.title);

            item.icon = ($link.find('.icon').attr('class') || '').replace('icon ', '');
            item.text = $link.text().trim();
            $link.html('<i class="icon ' + item.icon + '"></i><span class="text">' + item.text + '</span>');
            if(item.code === 'devops') $link.find('.text').addClass('num');
            appsMap[item.code] = item;

            $('<li></li>').attr('data-app', item.code)
                .append($link)
                .appendTo($menuMainNav);

            $link.tooltip({title: item.text, container: 'body', placement: 'right', tipClass: 'menu-tip'});

            if(!defaultApp) defaultApp = item.code;
        });

        appsMap['search'] =
        {
            opened:     false,
            code:       'search',
            group:      'search',
            icon:       'icon-search',
            methodName: 'index',
            moduleName: 'search',
            text:       searchCommon,
            title:      '<i class="icon icon-search"></i> ' + searchCommon,
            url:        '/index.php?m=search&f=index',
            vars:       ''
        };
    }

    /**
     * Get app code from url
     * @param {String} urlOrModuleName Url string
     * @return {String}
     */
    function getAppCodeFromUrl(urlOrModuleName)
    {
        var code = window.navGroup[urlOrModuleName];
        if(code) return code;

        var link = $.parseLink(urlOrModuleName);
        if(!link.moduleName || link.isOnlyBody || (link.moduleName === 'index' && link.methodName === 'index')) return '';

        if(link.hash && link.hash.indexOf('app=') === 0) return link.hash.substr(4);

        /* Handling special situations */
        var moduleName      = link.moduleName;
        var methodName      = link.methodName;
        if (moduleName === 'index' && methodName === 'index') return 'my';

        var methodLowerCase = methodName.toLowerCase();
        if(moduleName === 'doc')
        {
            if(link.prj) return 'project';

            if((link.params.from || link.params.$3) == 'product')
            {
                if(['objectlibs', 'showfiles', 'browse', 'view', 'edit', 'delete', 'create'].includes(methodLowerCase)) return 'product';
            }
            return 'doc';
        }
        if(['caselib', 'testreport', 'testsuite', 'testtask', 'testcase', 'bug', 'qa'].includes(moduleName))
        {
            return link.prj ? 'project' : 'qa';
        }
        if(moduleName === 'report')
        {
            if(['usereport', 'editreport', 'deletereport', 'custom'].includes(methodLowerCase) && link.params.from)
            {
                return 'system';
            }
            else
            {
                return link.prj ? 'project' : 'report';
            }
        }
        if(moduleName === 'story' && methodLowerCase === 'zerocase')
        {
            return link.params.from == 'project' ? 'project' : 'qa';
        }
        if(moduleName === 'execution' && methodLowerCase === 'all')
        {
            return (link.params.from || link.params.$3) == 'project' ? 'project' : 'execution';
        }
        if(moduleName === 'issue' || moduleName === 'risk' || moduleName === 'opportunity' || moduleName === 'pssp' || moduleName === 'auditplan' || moduleName === 'meeting' || moduleName === 'nc')
        {
            if(link.params.$2 == 'my' || link.params.from == 'my') return 'my';
            if(link.params.$2 == 'project' || link.params.from == 'project') return 'project';
            if(link.params.$2 == 'execution' || link.params.from == 'execution') return 'execution';
        }
        if(moduleName === 'product')
        {
            if(methodLowerCase === 'create' && (link.params.programID || link.params.$1)) return 'program';
            if(methodLowerCase === 'edit' && (link.params.programID || link.params.$4)) return 'program';
            if(methodLowerCase === 'batchedit') return 'program';
            var moduleGroup = link.params.moduleGroup ? link.params.moduleGroup : link.params.$2;
            if(methodLowerCase === 'showerrornone' && (moduleGroup || moduleGroup)) return moduleGroup;
        }
        if(moduleName === 'stakeholder')
        {
            if(methodLowerCase === 'create' && (link.params.programID || link.params.$1)) return 'program';
        }
        if(moduleName === 'user')
        {
            if(['todo', 'todocalendar', 'effortcalendar', 'effort', 'task', 'todo', 'story', 'bug', 'testtask', 'testcase', 'execution', 'dynamic', 'profile', 'view', 'issue', 'risk'].includes(methodLowerCase)) return 'system';
        }
        if(moduleName === 'my')
        {
            if(['team'].includes(methodLowerCase)) return 'system';
        }
        if(moduleName === 'company') if(methodLowerCase == 'browse') return 'admin';
        if(moduleName === 'opportunity' || moduleName === 'risk' || moduleName == 'trainplan') if(methodLowerCase == 'view') return 'project';
        if(moduleName === 'tree')
        {
            if(methodLowerCase === 'browse')
            {
                var viewType = link.params.view || link.params.$2;
                if(['bug', 'case', 'caselib'].includes(viewType)) return link.params.$5 === 'project' ? 'project' : 'qa';

                if(viewType === 'doc' && (link.params.from === 'product' || link.params.$5 == 'product')) return 'product';
                if(viewType === 'doc' && (link.params.from === 'project' || link.params.$5 == 'project')) return 'project';
                if(viewType === 'doc')   return 'doc';
                if(viewType === 'story') return 'product';
            }
            else if(methodLowerCase === 'browsetask')
            {
                return 'project';
            }
        }
        if(moduleName === 'search' && methodLowerCase === 'buildindex') return 'admin';

        code = window.navGroup[moduleName] || moduleName || urlOrModuleName;
        return appsMap[code] ? code : '';
    }

    /**
     * Open tab of app
     * @param {string} [url]   Url to open
     * @param {string} [appCode] The code of target app to open
     * @return {void}
     */
    function openTab(url, appCode)
    {
        /* Check params */
        if(!appCode)
        {
            if(appsMap[url])
            {
                appCode = url;
                url     = '';
            }
            else
            {
                appCode = getAppCodeFromUrl(url);
                if(!appCode) return false;
            }
        }

        /* Create pate app object and store it */
        var app = openedApps[appCode];
        if(app)
        {
            if(app.$iframe && app.$iframe.length)
            {
                var iframe = app.$iframe[0];
                if(iframe && iframe.contentDocument && iframe.contentWindow && iframe.contentWindow.$)
                {
                    var result = iframe.contentWindow.$(iframe.contentDocument).triggerHandler('openapp.apps', [app, url]);
                    if (result === false) {
                        return 'cancel';
                    }
                }
            }
        }
        else
        {
            var $iframe = $(
            [
                '<iframe',
                    'id="appIframe-' + appCode + '"',
                    'name="app-' + appCode + '"',
                    'allowfullscreen="true"',
                    'frameborder="no"',
                    'allowtransparency="true"',
                    'scrolling="auto"',
                    'style="width: 100%; height: 100%; left: 0px;"',
                '/>'
            ].join(' '));
            var $app = $('<div class="app-container" id="app-' + appCode + '"></div>')
                .append($iframe)
                .appendTo('#apps');

            app = $.extend({$iframe: $iframe, $app: $app, code: appCode, opened: true}, appsMap[appCode]);
            openedApps[appCode] = app;

            /* If first show without url, then use the default url */
            if(!url) url = appsMap[appCode].url;

            var iframe = $iframe.get(0);
            iframe.onload = iframe.onreadystatechange = function(e)
            {
                $app.trigger('loadapp', app);
            };
        }

        /* Set tab cookie */
        $.cookie('tab', appCode, {expires: config.cookieLife, path: config.webRoot});

        /* Highlight at main menu */
        var $menuMainNav   = $('#menuMainNav,#menuMoreNav');
        var $lastActiveNav = $menuMainNav.find('li.active');
        if($lastActiveNav.data('app') !== appCode)
        {
            $lastActiveNav.removeClass('active');
            $menuMainNav.find('li[data-app="' + appCode + '"]').addClass('active');
        }

        /* Show page app and update iframe source */
        if(url) reloadApp(appCode, url, true);
        app.zIndex = openedAppZIndex++;
        app.$app.show().css('z-index', app.zIndex);

        /* Update task bar */
        var $bars = $('#bars');
        var $bar  = $('#appBar-' + appCode);
        if(!$bar.length)
        {
            var $link= $('<a data-pos="bar"></a>')
                .attr('data-app', appCode)
                .attr('class', 'show-in-app')
                .html('<span>' + app.text + '</span>');
            var barCount = $('#bars li').length;

            if(barCount) $bar = $('<li class="divider"></li>').appendTo($bars);
            $bar = $('<li></li>').attr('data-app', appCode)
                .attr('id', 'appBar-' + appCode)
                .append($link)
                .appendTo($bars);
        }
        var $lastActiveBar = $bars.find('li.active');
        if($lastActiveBar.data('app') !== appCode)
        {
            $lastActiveBar.removeClass('active');
            $bars.find('li[data-app="' + appCode + '"]').addClass('active');
        }
        app.$bar = $bar;

        /* Update others app state */
        for(var theCode in openedApps)
        {
            if(theCode !== appCode) openedApps[theCode].show = false;
        }

        /* Update current app state */
        app.show = true;
        if(lastOpenedApp !== appCode)
        {
            lastOpenedApp = appCode;
            updateAppUrl(appCode, null, null, true);
        }

        app.$app.trigger('showapp', app);

        return true;
    }

    /**
     * Get last opened app
     * @param {boolean} [onlyShowed] If set to true then only get last app from apps are showed
     * @returns {object} The opened app info object
     */
    function getLastApp(onlyShowed)
    {
        var lastShowIndex = 0;
        var lastApp = null;
        for(var appCode in openedApps)
        {
            var app = openedApps[appCode];
            if((!onlyShowed || app.show) && lastShowIndex < app.zIndex && !app.closed)
            {
                lastShowIndex = app.zIndex;
                lastApp = app;
            }
        }
        return lastApp;
    }

    /**
     * Hide app
     * @param {string} appCode The app code of target app to hide
     * @return {void}
     */
    function hideTab(appCode)
    {
        var app = openedApps[appCode];
        if(!app || !app.show) return;

        app.$app.hide();
        app.show = false;
        lastOpenedApp = null;

        app.$app.trigger('hideapp', app);

        /* Active last app */
        var lastApp = getLastApp(true) || getLastApp();
        showTab(lastApp ? lastApp.code : defaultApp);
    }

    /**
     * Show tab of app
     * @param {string} appCode The app code of target app to show
     * @return {void}
     */
    function showTab(appCode)
    {
        return openTab('', appCode);
    }

    /**
     * Toggle app
     * @param {string} appCode The app code of target app to toggle
     * @return {void}
     */
    function toggleApp(appCode)
    {
        var app = openedApps[appCode];
        if(!app || app.code !== lastOpenedApp) showTab(appCode);
        else hideTab(appCode);
    }

    /**
     * Close app
     * @param {string} appCode The app code of target app to close
     */
    function closeApp(appCode)
    {
        appCode = appCode || lastOpenedApp;
        var app = openedApps[appCode];
        if(!app) return;

        if(app.$iframe && app.$iframe.length)
        {
            var iframe = app.$iframe[0];
            if(iframe && iframe.contentDocument && iframe.contentWindow && iframe.contentWindow.$)
            {
                var result = iframe.contentWindow.$(iframe.contentDocument).triggerHandler('closeapp.apps', [app]);
                if (result === false) {
                    return 'cancel';
                }
            }
        }

        var appKeys = Object.keys(openedApps)
        if(appKeys[0] == appCode)
        {
            $("#bars li.divider:first").remove();
        }
        else
        {
            $("#appBar-" + appCode).prev().remove();
        }

        app.closed = true;
        hideTab(appCode);
        app.$app.remove();
        app.$bar.remove();
        delete openedApps[appCode];

        var firstClass = $("#bars li:first").attr('class');
        if(firstClass == 'divider') $("#bars li.divider:first").remove();

        app.$app.trigger('closeapp', app);
    }

    /**
     * Reload app
     * @param {string}         appCode           The app code of target app to reload
     * @param {string|boolean} [url]             The new url to load, it's optional
     * @param {boolean}        [notTriggerEvent] Skip trigger event
     * @return {void}
     */
    function reloadApp(appCode, url, notTriggerEvent)
    {
        var app = openedApps[appCode];
        if(!app) return;

        if(url === true) url = app.url;
        var iframe = app.$iframe[0];

        try
        {
            if(url) iframe.contentWindow.location.assign(url);
            else iframe.contentWindow.location.reload(true);
        }
        catch(_)
        {
            iframe.src = url || app.url || iframe.src;
        }

        if(!notTriggerEvent) app.$app.trigger('reloadapp', app);
    }

    /**
     * Update browser url and title for the given app
     * @param {string} appCode         The app code of target app to update url
     * @param {string|boolean} [url]   The new url of the app
     * @param {string|boolean} [title] The new title of the app
     * @param {boolean}        [push]  Use push instead of replace
     * @return {void}
     */
    function updateAppUrl(appCode, url, title, push)
    {
        // if(window.TUTORIAL) return;
        var app = openedApps[appCode];
        if(!app || lastOpenedApp !== appCode) return;

        if(url) app.appUrl = url;
        else url = app.appUrl || app.url;
        if(title) app.appTitle = title;
        else title = app.appTitle || app.text;

        if(url && url.indexOf('#') < 0 && getAppCodeFromUrl(url) !== appCode) url = url + '#app=' + appCode;
        if(location.href !== url)
        {
            history[push ? 'pushState' : 'replaceState']({app: appCode}, title, url);
        }
        document.title = title;
    }

    /* Bind helper methods to global object "$.apps" */
    $.apps = window.apps =
    {
        show:       showTab,
        open:       openTab,
        hide:       hideTab,
        toggle:     toggleApp,
        close:      closeApp,
        reload:     reloadApp,
        updateUrl:  updateAppUrl,
        getAppCode: getAppCodeFromUrl,
        getLastApp: getLastApp,
        openedApps: openedApps,
        appsMap:    appsMap
    };

    /**
     * Refresh more menu in #menuNav
     * @return {void}
     */
    function refreshMoreMenu()
    {
        var $mainNav       = $('#menuMainNav');
        var $list          = $('#menuMoreList');
        var $menuNav       = $('#menuNav');
        var $menuItems     = $mainNav.children('li');
        var itemHeight     = $menuItems.first().outerHeight();
        var maxHeight      = $menuNav.height() - 10;
        var showMoreMenu   = false;
        var currentHeight  = itemHeight;
        var moreMenuHeight = 12;

        $menuItems.each(function()
        {
            var $item     = $(this);
            var isDivider = $item.hasClass('divider');
            var height    = isDivider ? 17 : itemHeight;
            currentHeight += height;

            if(currentHeight > maxHeight)
            {
                $item.addClass('hidden');
                if(!showMoreMenu)
                {
                    showMoreMenu = true;
                    $list.empty();

                    var $prevItem = $item.prev();
                    if($prevItem.hasClass('divider')) $prevItem.addClass('hidden');

                    if(isDivider) return;
                }
                moreMenuHeight += isDivider ? 13 : 32;
                $list.append($item.clone().removeClass('hidden'));
            }
            else
            {
                $item.removeClass('hidden');
            }
        });

        /* The magic number "111" is the space between dropdown trigger
           btn and the bottom of screen */
        var listStyle = {maxHeight: 'initial', top: moreMenuHeight > 111 ? 111 - moreMenuHeight : ''};
        if($list[0].getBoundingClientRect)
        {
            var btnBounding = $list.prev('a')[0].getBoundingClientRect();
            if(btnBounding.height)
            {
                var winHeight = $(window).height();
                if(winHeight < moreMenuHeight)
                {
                    listStyle.maxHeight = winHeight;
                    listStyle.overflow = 'auto';
                    listStyle.top = 5 - btnBounding.top;
                }
                else if(moreMenuHeight > (winHeight - btnBounding.top))
                {
                    listStyle.top = winHeight - btnBounding.top - moreMenuHeight + 5;
                }
            }
        }
        $list.css(listStyle);
        $menuNav.toggleClass('show-more-nav', showMoreMenu);

        if(showMoreMenu && !$list.data('listened-click'))
        {
            $list.data('listened-click', true).on('click', function()
            {
                $list.addClass('hidden');
                setTimeout(function(){$list.removeClass('hidden')}, 200);
            });
        }
    }

    /* Init after current page load */
    $(function()
    {
        initAppsMenu();

        /* Bind events */
        $(document).on('click', '.open-in-app,.show-in-app', function(e)
        {
            var $link = $(this);
            if($link.is('[data-modal],[data-toggle][data-toggle!="tooltip"],.iframe,.not-in-app')) return;
            var url = $link.hasClass('show-in-app') ? '' : ($link.attr('href') || $link.data('url'));
            if(url && url.indexOf('onlybody=yes') > 0) return;
            if(openTab(url, $link.data('app')))
            {
                e.preventDefault();
                if($link.closest('#userNav').length)
                {
                    var $menu = $('#userNav .dropdown-menu').addClass('hidden');
                    setTimeout(function(){$menu.removeClass('hidden')}, 200);
                }
            }
        }).on('contextmenu', '.open-in-app,.show-in-app', function(event)
        {
            var $btn  = $(this);
            var appCode = $btn.data('app');
            if(!appCode) return;

            var lang  = window.appsLang;
            var app   = openedApps[appCode];
            var items = [{label: lang.open, disabled: app && lastOpenedApp === appCode, onClick: function(){showTab(appCode)}}];
            if(app)
            {
                items.push({label: lang.reload, onClick: function(){reloadApp(appCode)}});
                if(appCode !== 'my') items.push({label: lang.close, onClick: function(){closeApp(appCode)}});
            }

            var options = {event: event, onClickItem: function(_item, _$item, e){e.preventDefault();}};
            var pos = $btn.data('pos');
            if(pos)
            {
                var bounding = $btn.closest('li')[0].getBoundingClientRect();
                if(pos === 'bar')
                {
                    options.x = bounding.left;
                    options.y = bounding.top - (appCode === 'my' ? 65 : 92);
                }
                else
                {
                    options.x = bounding.right - 10;
                    options.y = bounding.top;
                }
            }
            var $dropdown = $btn.closest('.dropdown');
            if($dropdown.length)
            {
                $dropdown.addClass('open');
                options.onHide = function(){$dropdown.removeClass('open');}
            }

            $.zui.ContextMenu.show(items, options);
            event.preventDefault();
        });

        window.addEventListener('popstate', function(event)
        {
            if(event.state && lastOpenedApp !== event.state.app) openTab(event.state.app);
        });

        /* Redirect or open default app after document load */
        var defaultOpenUrl = window.defaultOpen;
        if(!defaultOpenUrl && location.hash.indexOf('#app=') === 0)
        {
            defaultOpenUrl = decodeURIComponent(location.hash.substr(5));
        }

        openTab(defaultOpenUrl ? defaultOpenUrl : defaultApp);

        /* Refresh more menu on window resize */
        $(window).on('resize', refreshMoreMenu);
        refreshMoreMenu();
        setTimeout(refreshMoreMenu, 500);
    });
}());

(function()
{
    $.toggleMenu = function(toggle)
    {
        var $body = $('body');
        if (toggle === undefined) toggle = $body.hasClass('menu-hide');
        $body.toggleClass('menu-hide', !toggle).toggleClass('menu-show', !!toggle);
        $.cookie('hideMenu', String(!toggle), {expires: config.cookieLife, path: config.webRoot});
    };

    $(function()
    {
        /* Click to show more. */
        $(document).on('click', '.menu-toggle', function()
        {
            $.toggleMenu();
            var $menu = $('#userNav .dropdown-menu').addClass('hidden');
            setTimeout(function(){$menu.removeClass('hidden')}, 200);
        });

        $('.menu-toggle').each(function()
        {
            $(this).attr('data-toggle', 'tooltip').tooltip({container: 'body', placement: 'right', tipClass: 'menu-tip', title: function()
            {
                return $(this).data($('body').hasClass('menu-hide') ? 'unfoldText' : 'collapseText');
            }});
        });
    });
}());

$.extend(
{
    gotoObject:function()
    {
        objectType  = $('#searchType').attr('value');
        objectValue = $('#globalSearchInput').attr('value');

        if(objectType && objectValue)
        {
            var reg = /[^0-9]/;
            if(reg.test(objectValue) || objectType == 'all')
            {
                var searchLink = createLink('search', 'index') + (config.requestType == 'PATH_INFO' ? '?' : '&') + 'words=' + objectValue;
                $.apps.open(searchLink);
            }
            else
            {
                var types        = objectType.split('-');
                var searchModule = types[0];
                var searchMethod = typeof(types[1]) == 'undefined' ? 'view' : types[1];
                var searchLink   = createLink(searchModule, searchMethod, "id=" + objectValue);
                var assetType    = 'story,issue,risk,opportunity,doc';
                if(assetType.indexOf(searchModule) > -1)
                {
                    var link = createLink('index', 'ajaxGetViewMethod' , 'objectID=' + objectValue + '&objectType=' + searchModule);
                    $.get(link, function(data)
                    {
                        if(data)
                        {
                            searchModule = 'assetlib';
                            searchMethod = data;
                            searchLink   = createLink(searchModule, searchMethod, "id=" + objectValue);
                        }
                        $.apps.open(searchLink);
                    });
                }
                else
                {
                    $.apps.open(searchLink);
                }
            }

            $.post(createLink('index', 'ajaxClearObjectSession'), {objectType: objectType});
            $('#globalSearchInput').click();
        }
    }
});

/* Initialize global search. */
$(function()
{
    var reg           = /[^0-9]/;
    var $searchbox    = $('#searchbox');
    var $typeSelector = $searchbox.find('.input-group-btn');
    var $dropmenu     = $typeSelector.children('.dropdown-menu');
    var $searchQuery  = $('#globalSearchInput');

    var toggleMenu = function(show)
    {
        $searchbox.toggleClass('open', show);
        $dropmenu.toggleClass('show', show).toggleClass('in', show);
        if(show) $dropmenu.show();
        else $dropmenu.hide();
    };

    var hideMenu = function(){toggleMenu(false);};

    var refreshMenu = function()
    {
        var val        = $searchQuery.val();
        var searchType = changeSearchObject();
        if(val !== null && val !== "")
        {
            var isQuickGo = !reg.test(val);
            $dropmenu.toggleClass('show-quick-go', isQuickGo);
            var $typeAll = $dropmenu.find('li.search-type-all > a');
            $typeAll.text(searchAB + ' ' + val);
            if(isQuickGo)
            {
                $typeAll.closest('li').removeClass('active');
                $dropmenu.removeClass('with-active').find('li:not(.search-type-all) > a').each(function()
                {
                    var $this = $(this);
                    var isActiveType = $this.data('value') === searchType && searchType !== 'all';
                    $this.closest('li').toggleClass('selected active', isActiveType);
                    $this.text($this.data('name') + ' #' + (val.length > 7 ? (val.substr(0, 7) + '...') : val));
                    if(isActiveType) $dropmenu.addClass('with-active');
                });
            }
            else
            {
                $dropmenu.find('li.active').removeClass('active');
                $typeAll.closest('li').addClass('active');
            }
            toggleMenu(true);
        }
        else
        {
            hideMenu();
        }
    };

    $dropmenu = $dropmenu.appendTo($searchbox);
    $dropmenu.on('click', 'a', function(e)
    {
        $('#searchType').val($(this).data('value'));
        $.gotoObject();
        e.stopPropagation();
    }).find('li > a').each(function()
    {
        var $this = $(this);
        $this.attr('data-name', $this.text());
    });

    var $allItem = $dropmenu.find('li > a[data-value="all"]');
    if($allItem.length)
    {
        $allItem.closest('li').addClass('search-type-all').prependTo($dropmenu);
    }

    $searchQuery.on('change keyup paste input propertychange', refreshMenu).on('focus', function()
    {
        setTimeout(refreshMenu, 300);
    });

    $(document).on('click', hideMenu);

    $(document).on('click', function()
    {
        $("#upgradeContent").hide();
    });

    $("#upgradeContent").click(function(event)
    {
        event.stopPropagation();
    });

    $("#bizLink").click(function(event)
    {
        var $upgradeContent = $('#upgradeContent').toggle();
        if(!$upgradeContent.is(':hidden'))
        {
            getLatestVersion();
            event.stopPropagation();
        }
    });

    $('.has-avatar').hover(function(event)
    {
        $('.contextmenu').attr('class', 'contextmenu');
        $('.contextmenu-menu').attr('class', 'contextmenu-menu fade');
    });

    $('#bars').mousedown(function()
    {
        $('#globalSearchInput').click();
    });
});

/* Change the search object according to the module and method. */
function changeSearchObject()
{
    var appInfo = $.apps.getLastApp();
    var appPageModuleName = appInfo.$iframe[0].contentWindow.config.currentModule;
    var appPageMethodName = appInfo.$iframe[0].contentWindow.config.currentMethod;

    var searchType = appPageModuleName;
    if(appPageModuleName == 'product' && appPageMethodName == 'browse') var searchType = 'story';

    var projectMethod = 'task|story|bug|build';
    if(appPageModuleName == 'project' && projectMethod.indexOf(appPageMethodName) != -1) var searchType = appPageMethodName;

    if(appPageModuleName == 'my' || appPageModuleName == 'user') var searchType = appPageMethodName;

    if(searchObjectList.indexOf(',' + searchType + ',') == -1) var searchType = 'bug';

    if(searchType == 'program')    var searchType = 'program-product';
    if(searchType == 'deploystep') var searchType = 'deploy-viewstep';

    $("#searchType").val(searchType);
    $('#searchTypeMenu li:first').attr('class', 'search-type-all');

    return searchType;
}

function getLatestVersion()
{
    $('#globalSearchInput').click();
    $('#upgradeContent').toggle();
}
