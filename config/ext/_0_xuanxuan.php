<?php
$config->minClientVerson  = '4.0';             // 最低支持的客户端版本。The min client version of XXB support for

$config->xuanxuan = new stdclass();
$config->xuanxuan->version     = '5.0.2';
$config->xuanxuan->backend     = 'zentao';
$config->xuanxuan->backendLang = 'zh-cn';
$config->xuanxuan->key         = '';       //Set a 32 byte string as your key.

$config->maps = array();
$config->xxd  = new stdclass();

if(!defined('TABLE_IM_CHAT'))             define('TABLE_IM_CHAT',             '`' . $config->db->prefix . 'im_chat`');
if(!defined('TABLE_IM_CHATUSER'))         define('TABLE_IM_CHATUSER',         '`' . $config->db->prefix . 'im_chatuser`');
if(!defined('TABLE_IM_CLIENT'))           define('TABLE_IM_CLIENT',           '`' . $config->db->prefix . 'im_client`');
if(!defined('TABLE_IM_MESSAGE'))          define('TABLE_IM_MESSAGE',          '`' . $config->db->prefix . 'im_message`');
if(!defined('TABLE_IM_MESSAGESTATUS'))    define('TABLE_IM_MESSAGESTATUS',    '`' . $config->db->prefix . 'im_messagestatus`');
if(!defined('TABLE_IM_QUEUE'))            define('TABLE_IM_QUEUE',            '`' . $config->db->prefix . 'im_queue`');
if(!defined('TABLE_IM_CONFERENCE'))       define('TABLE_IM_CONFERENCE',       '`' . $config->db->prefix . 'im_conference`');
if(!defined('TABLE_IM_CONFERENCEACTION')) define('TABLE_IM_CONFERENCEACTION', '`' . $config->db->prefix . 'im_conferenceaction`');
if(!defined('TABLE_IM_USERDEVICE'))       define('TABLE_IM_USERDEVICE',       '`' . $config->db->prefix . 'im_userdevice`');

if(!defined('TABLE_IM_MESSAGE_BACKUP'))     define('TABLE_IM_MESSAGE_BACKUP',     '`' . $config->db->prefix . 'im_message_backup`');
if(!defined('TABLE_IM_MESSAGE_INDEX'))      define('TABLE_IM_MESSAGE_INDEX',      '`' . $config->db->prefix . 'im_message_index`');
if(!defined('TABLE_IM_CHAT_MESSAGE_INDEX')) define('TABLE_IM_CHAT_MESSAGE_INDEX', '`' . $config->db->prefix . 'im_chat_message_index`');

$config->xuanxuan->enabledMethods['im']['sysserverstart']          = 'sysServerStart';
$config->xuanxuan->enabledMethods['im']['sysgetserverinfo']        = 'sysGetServerInfo';
$config->xuanxuan->enabledMethods['im']['sysgetdepts']             = 'sysGetDepts';
$config->xuanxuan->enabledMethods['im']['userlogin']               = 'userLogin';
$config->xuanxuan->enabledMethods['im']['userlogout']              = 'userLogout';
$config->xuanxuan->enabledMethods['im']['usergetlist']             = 'userGetList';
$config->xuanxuan->enabledMethods['im']['usergetlistbydept']       = 'userGetListByDept';
$config->xuanxuan->enabledMethods['im']['usergetdeleted']          = 'userGetDeleted';
$config->xuanxuan->enabledMethods['im']['usersearch']              = 'userSearch';
$config->xuanxuan->enabledMethods['im']['userupdate']              = 'userUpdate';
$config->xuanxuan->enabledMethods['im']['usersyncsettings']        = 'userSyncSettings';
$config->xuanxuan->enabledMethods['im']['usersetdevicetoken']      = 'userSetDeviceToken';
$config->xuanxuan->enabledMethods['im']['usergetauthtoken']        = 'userGetAuthToken';
$config->xuanxuan->enabledMethods['im']['userrenewauthtoken']      = 'userRenewAuthToken';
$config->xuanxuan->enabledMethods['im']['chatgetpubliclist']       = 'chatGetPublicList';
$config->xuanxuan->enabledMethods['im']['chatgetlist']             = 'chatGetList';
$config->xuanxuan->enabledMethods['im']['chatgetmembers']          = 'chatGetMembers';
$config->xuanxuan->enabledMethods['im']['chatgetmessageinfo']      = 'chatGetMessageInfo';
$config->xuanxuan->enabledMethods['im']['chatcreate']              = 'chatCreate';
$config->xuanxuan->enabledMethods['im']['chataddadmins']           = 'chatAddAdmins';
$config->xuanxuan->enabledMethods['im']['chatremoveadmins']        = 'chatRemoveAdmins';
$config->xuanxuan->enabledMethods['im']['chatjoin']                = 'chatJoin';
$config->xuanxuan->enabledMethods['im']['chatleave']               = 'chatLeave';
$config->xuanxuan->enabledMethods['im']['chatrename']              = 'chatRename';
$config->xuanxuan->enabledMethods['im']['chatdismiss']             = 'chatDismiss';
$config->xuanxuan->enabledMethods['im']['chatsetcommitters']       = 'chatSetCommitters';
$config->xuanxuan->enabledMethods['im']['chatsetvisibility']       = 'chatSetVisibility';
$config->xuanxuan->enabledMethods['im']['chatstar']                = 'chatStar';
$config->xuanxuan->enabledMethods['im']['chathide']                = 'chatHide';
$config->xuanxuan->enabledMethods['im']['chatmute']                = 'chatMute';
$config->xuanxuan->enabledMethods['im']['chatfreeze']              = 'chatFreeze';
$config->xuanxuan->enabledMethods['im']['chatsetcategory']         = 'chatSetCategory';
$config->xuanxuan->enabledMethods['im']['chatinvite']              = 'chatInvite';
$config->xuanxuan->enabledMethods['im']['chatkick']                = 'chatKick';
$config->xuanxuan->enabledMethods['im']['chatchangeownership']     = 'chatChangeOwnership';
$config->xuanxuan->enabledMethods['im']['chatpinmessages']         = 'chatPinMessages';
$config->xuanxuan->enabledMethods['im']['chatunpinmessages']       = 'chatUnpinMessages';
$config->xuanxuan->enabledMethods['im']['chatgethistory']          = 'chatGetHistory';
$config->xuanxuan->enabledMethods['im']['chatsetlastreadmessage']  = 'chatSetLastReadMessage';
$config->xuanxuan->enabledMethods['im']['messageretract']          = 'messageRetract';
$config->xuanxuan->enabledMethods['im']['messagesend']             = 'messageSend';
$config->xuanxuan->enabledMethods['im']['conferencegetbychat']     = 'conferenceGetByChat';
$config->xuanxuan->enabledMethods['im']['conferencecreate']        = 'conferenceCreate';
$config->xuanxuan->enabledMethods['im']['conferencejoin']          = 'conferenceJoin';
$config->xuanxuan->enabledMethods['im']['conferenceinvite']        = 'conferenceInvite';
$config->xuanxuan->enabledMethods['im']['conferenceleave']         = 'conferenceLeave';
$config->xuanxuan->enabledMethods['im']['conferenceclose']         = 'conferenceClose';
$config->xuanxuan->enabledMethods['im']['extensiongetlist']        = 'extensionGetList';
$config->xuanxuan->enabledMethods['im']['syncofflinemessages']     = 'syncOfflineMessages';
$config->xuanxuan->enabledMethods['im']['syncnotifications']       = 'syncNotifications';
$config->xuanxuan->enabledMethods['im']['syncusers']               = 'syncUsers';
$config->xuanxuan->enabledMethods['im']['syncdepts']               = 'syncDepts';
$config->xuanxuan->enabledMethods['im']['fileupload']              = 'fileUpload';
$config->xuanxuan->enabledMethods['im']['todoupsert']              = 'todoUpsert';
$config->xuanxuan->enabledMethods['im']['todogetlist']             = 'todoGetList';
$config->xuanxuan->enabledMethods['im']['updatelastpoll']          = 'updateLastPoll';
$config->xuanxuan->enabledMethods['im']['checkpasswordchanges']    = 'checkpasswordchanges';
$config->xuanxuan->enabledMethods['im']['messagesyncsinceoffline'] = 'messageSyncSinceOffline';
$config->xuanxuan->enabledMethods['im']['messagesyncmissed']       = 'messageSyncMissed';
$config->xuanxuan->enabledMethods['im']['chatgetlastmessage']      = 'chatGetLastMessage';
$config->xuanxuan->enabledMethods['im']['messagesync']             = 'messageSync';
$config->xuanxuan->enabledMethods['im']['messagegetlist']          = 'messageGetList';
$config->xuanxuan->enabledMethods['im']['maintenance']             = 'maintenance';
$config->xuanxuan->enabledMethods['entry']['visit']                = 'visit';

// Please use lowercase keys in enabledMethods.
