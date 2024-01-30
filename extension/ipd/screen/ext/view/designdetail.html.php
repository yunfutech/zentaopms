<html lang="zh-cmn-Hans">
  <head>
    <script>self["MonacoEnvironment"] = (function (paths) {
          return {
            globalAPI: false,
            getWorkerUrl : function (moduleId, label) {
              var result =  paths[label];
              if (/^((http:)|(https:)|(file:)|(\/\/))/.test(result)) {
                var currentUrl = String(window.location);
                var currentOrigin = currentUrl.substr(0, currentUrl.length - window.location.hash.length - window.location.search.length - window.location.pathname.length);
                if (result.substring(0, currentOrigin.length) !== currentOrigin) {
                  var js = '/*' + label + '*/importScripts("' + result + '");';
                  var blob = new Blob([js], { type: 'application/javascript' });
                  return URL.createObjectURL(blob);
                }
              }
              return result;
            }
          };
        })({
  "editorWorkerService": "/monacoeditorwork/editor.worker.bundle.js",
  "typescript": "/monacoeditorwork/ts.worker.bundle.js",
  "json": "/monacoeditorwork/json.worker.bundle.js",
  "html": "/monacoeditorwork/html.worker.bundle.js",
  "javascript": "/monacoeditorwork/ts.worker.bundle.js",
  "handlebars": "/monacoeditorwork/html.worker.bundle.js",
  "razor": "/monacoeditorwork/html.worker.bundle.js"
          });

location.hash = '#/chart/home/' + parent.window.screen.id;
window.chartData  = parent.window.screen.chartData;
window.dimensions = parent.window.dimensions;
window.dimension  = parent.window.dimension;
window.scopeList  = parent.window.scopeList;
window.treeData   = parent.window.treeData;

window.saveAsDraft   = parent.window.saveAsDraft;
window.saveAsPublish = parent.window.saveAsPublish;
window.backBrowse    = parent.window.backBrowse;
window.fullscreen    = parent.window.fullScreen;
window.fieldConfig   = parent.window.fieldConfig;
window.selectOptionApi   = parent.window.createLink('screen', 'ajaxGetOptions');
window.fetchChartApi = parent.window.fetchChartApi;
</script>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta
      name="viewport"
      content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0"
    />
    <link rel="icon" href="./favicon.ico" />

    <script type="module" crossorigin src="<?php echo $this->app->getWebRoot();?>static/js/index.js"></script>
    <style>
    .n-layout-scroll-container {height: unset !important;}
    </style>
  </head>
  <body>
    <div id="appProvider" style="display: none;"></div>
    <div id="app">
      <div class="first-loading-wrp">
        <div class="loading-wrp">
          <span class="dot dot-spin"><i></i><i></i><i></i><i></i></span>
        </div>
      </div>
    </div>
  </body>
</html>

