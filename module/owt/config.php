<?php
$config->owt->owtTabList = array('server', 'video');

$config->owt->require = new stdclass();
$config->owt->require->edit  = 'enabled,serviceId,serviceKey,serverAddr,apiPort,mgmtPort';
