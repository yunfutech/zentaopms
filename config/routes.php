<?php
/*
 * The routes for API.
 */
$routes = array();

$routes['/tokens']   = 'tokens';
$routes['/langs']    = 'langs';
$routes['/views']    = 'views';
$routes['/groups']   = 'groups';
$routes['/ping']     = 'ping';
$routes['/comments'] = 'comments';

$routes['/tabs/:module'] = 'tabs';

$routes['/files']     = 'files';
$routes['/files/:id'] = 'file';

$routes['/configurations']       = 'configs';
$routes['/configurations/:name'] = 'config';

$routes['/programs/:id/products'] = 'products';
$routes['/products']              = 'products';
$routes['/products/:id']          = 'product';

$routes['/productlines']     = 'productLines';
$routes['/productlines/:id'] = 'productLine';

$routes['/productplans']                   = 'productPlans';
$routes['/products/:id/plans']             = 'productPlans';
$routes['/productplans/:id']               = 'productPlan';
$routes['/productplans/:id/linkstories']   = 'productPlanLinkStories';
$routes['/productplans/:id/unlinkstories'] = 'productPlanUnlinkStories';
$routes['/productplans/:id/linkbugs']      = 'productPlanLinkBugs';
$routes['/productplans/:id/unlinkbugs']    = 'productPlanUnlinkBugs';

$routes['/releases']              = 'releases';
$routes['/products/:id/releases'] = 'releases';
$routes['/projects/:id/releases'] = 'projectReleases';
$routes['/releases/:id']          = 'release';

$routes['/stories']                = 'stories';
$routes['/products/:id/stories']   = 'stories';
$routes['/projects/:id/stories']   = 'projectStories';
$routes['/executions/:id/stories'] = 'executionStories';
$routes['/stories/:id']            = 'story';
$routes['/stories/:id/change']     = 'storyChange';

$routes['/products/:id/bugs']   = 'bugs';
$routes['/projects/:id/bugs']   = 'projectBugs';
$routes['/executions/:id/bugs'] = 'executionBugs';
$routes['/bugs']                = 'bugs';
$routes['/bugs/:id']            = 'bug';

$routes['/programs/:id/projects'] = 'projects';
$routes['/products/:id/projects'] = 'productProjects';
$routes['/projects']              = 'projects';
$routes['/projects/:id']          = 'project';

$routes['/projects/:id/executions'] = 'executions';
$routes['/executions']              = 'executions';
$routes['/executions/:id']          = 'execution';

$routes['/executions/:id/tasks'] = 'tasks';
$routes['/tasks']                = 'tasks';
$routes['/tasks/:id']            = 'task';
$routes['/tasks/:id/assignto']   = 'taskAssignTo';
$routes['/tasks/:id/start']      = 'taskStart';
$routes['/tasks/:id/finish']     = 'taskFinish';

$routes['/users']     = 'users';
$routes['/users/:id'] = 'user';
$routes['/user']      = 'user';

$routes['/programs']     = 'programs';
$routes['/programs/:id'] = 'program';

$routes['/programs/:id/stakeholders'] = 'stakeholders';

$routes['/products/:id/issues'] = 'productIssues';
$routes['/projects/:id/issues'] = 'issues';
$routes['/issues']              = 'issues';
$routes['/issues/:id']          = 'issue';

$routes['/todos']              = 'todos';
$routes['/todos/:id']          = 'todo';
$routes['/todos/:id/finish']   = 'todoFinish';
$routes['/todos/:id/activate'] = 'todoActivate';

$routes['/projects/:id/builds']   = 'builds';
$routes['/executions/:id/builds'] = 'executionBuilds';
$routes['/builds']                = 'builds';
$routes['/builds/:id']            = 'build';

$routes['/products/:id/testcases']   = 'testcases';
$routes['/projects/:id/testcases']   = 'projectCases';
$routes['/executions/:id/testcases'] = 'executionCases';
$routes['/testcases']                = 'testcases';
$routes['/testcases/:id']            = 'testcase';

$routes['/projects/:projectID/testtasks'] = 'testtasks';
$routes['/testtasks']                     = 'testtasks';
$routes['/testtasks/:id']                 = 'testtask';

$routes['/projects/:projectID/risks'] = 'risks';
$routes['/risks']                     = 'risks';
$routes['/risks/:id']                 = 'risk';

$routes['/projects/:id/meetings'] = 'meetings';
$routes['/meetings']              = 'meetings';
$routes['/meetings/:id']          = 'meeting';

$routes['/departments']     = 'departments';
$routes['/departments/:id'] = 'department';

$routes['/doclibs']      = 'doclibs';
$routes['/doclibs/:id']  = 'docs';
$routes['/docs']         = 'docs';
$routes['/docs/:id']     = 'doc';

$routes['/repos']       = 'repos';
$routes['/repos/rules'] = 'reporules';

$routes['/reports'] = 'reports';

$routes['/z/folders']           = 'zfolders';
$routes['/z/folders/:id']       = 'zfolder';
$routes['/z/files/:id']         = 'zfile';
$routes['/z/files/:id/content'] = 'zfileContent';

$routes['/gitlab/webhook'] = 'gitlabWebhook';

$config->routes = $routes;
