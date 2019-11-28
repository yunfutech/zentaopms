<?php

$config->target->create = new stdclass();
$config->target->experiment = new stdclass();
$config->target->create->requiredFields = 'precision,recall,f1';


$config->target->experiment->requiredFields = 'precision,recall,f1';