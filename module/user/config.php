<?php
$config->user = new stdclass();
$config->user->create = new stdclass();
$config->user->edit   = new stdclass();

$config->user->create->requiredFields = 'account,realname,password,password1,password2';
$config->user->edit->requiredFields   = 'account,realname';

$config->user->customBatchCreateFields = 'dept,email,gender,commiter,join,skype,qq,dingding,weixin,mobile,slack,whatsapp,phone,address,zipcode';
$config->user->customBatchEditFields   = 'dept,email,commiter,skype,qq,dingding,weixin,mobile,slack,whatsapp,phone,address,zipcode';

$config->user->custom = new stdclass();
$config->user->custom->batchCreateFields = 'dept,join,email,gender';
$config->user->custom->batchEditFields   = 'dept,join,email,commiter';

$config->user->contactField = 'mobile,phone,qq,dingding,weixin,skype,whatsapp,slack';
$config->user->failTimes    = 6;
$config->user->lockMinutes  = 10;
$config->user->batchCreate  = 10;

$config->user->mobileList = [
    'zhangwenbin' => '18010150668',
    'zengjunyu'=> '18610350482',
    'chengyao' => '18515290431',
    'guoxiaoxue' => '13051385258',
    'jiaxianfu' => '15600167432',
    'litong' => '15175168170',
    'liuzhiming' => '13159878130',
    'luojiacheng' => '17745135716',
    'qiaoyanshuo' => '15010901729',
    'sunliangchen' => '15904924518',
    'tianye' => '18612524354',
    'wangwanqin' => '15235358168',
    'wangjia' => '18610614136',
    'wangqi' => '13244316774',
    'wubosheng' => '18617840244',
    'wujunhan' => '18846198878',
    'xinjie' => '15313125668',
    'yanshenwei' => '18531436930',
    'zhaoyaping' => '18832610995',
    'zhouhao' => '18514587645',
    'zhoujianxing' => '13261681892',
    'zhaojiapeng' => '17338131617'
];
