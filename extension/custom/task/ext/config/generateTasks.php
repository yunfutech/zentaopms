<?php

$config->task->generateTask = new stdClass();
$config->task->generateTask->defaultType = 'discuss';
$config->task->generateTask->defaultEstimate = 0.5;
$config->task->generateTask->defaultLeft = 0.5;
$config->task->generateTask->defaultConnsumed = 0;
$config->task->generateTask->blacklist = [];
$config->task->generateTask->deptArr = [
    9 => [
        'project' => 706,   # 管理层 => 项目管理2022,
        'execution' => 907,
    ],
    10 => [
        'project' => 707,   # 销售部 => 云孚销售2022
        'execution' => 917,
    ],
    19 => [
        'project' => 704,   # 市场部 => 市场工作2022
        'execution' => 911,
    ]
];
