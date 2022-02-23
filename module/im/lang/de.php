<?php
$lang->im->settings = 'Xuanxuan Settings';
$lang->im->debug    = 'Debug';

$lang->im->version         = 'Version';
$lang->im->backendLang     = 'Server Language';
$lang->im->key             = 'Key';
$lang->im->systemGroup     = 'System';
$lang->im->url             = 'URL';
$lang->im->pollingInterval = 'Polling interval';
$lang->im->createKey       = 'New';
$lang->im->connector       = ', ';
$lang->im->viewDebug       = 'View Debug';
$lang->im->log             = 'Log';
$lang->im->xxdStatus       = 'XXD Status';
$lang->im->debugInfo       = 'Debug';
$lang->im->serverInfo      = 'Server Info';
$lang->im->errorInfo       = 'Error';
$lang->im->xxbConfigError  = 'XXB is error.';
$lang->im->id              = 'ID';
$lang->im->checkUpdate     = 'Check update';
$lang->im->xxcVersion      = 'XXC Version';
$lang->im->xxdVersion      = 'XXD Version';
$lang->im->xxbVersion      = 'XXB Version';
$lang->im->xxcDesc         = 'Upgrade description';
$lang->im->xxcReadme       = 'Update log';
$lang->im->strategy        = 'Upgrade strategy';
$lang->im->download        = 'Download';
$lang->im->notVersion      = 'The %s format is incorrect and must consist of digits and dots, such as "2.5.0" or "2.5"';
$lang->im->notempty        = 'Cannot be empty.';

$lang->im->strategies['force']    = 'Force';
$lang->im->strategies['optional'] = 'Optional';

$lang->im->disabled = 'Disabled';
$lang->im->enabled  = 'Enabled';

$lang->im->debugStatus[0] = $lang->im->disabled;
$lang->im->debugStatus[1] = $lang->im->enabled;

$lang->im->xxdServer       = 'Server';
$lang->im->createKey       = 'New';
$lang->im->downloadXXD     = 'Download XXD';
$lang->im->listenIP        = 'Listen IP';
$lang->im->chatPort        = 'Chat Port';
$lang->im->uploadFileSize  = 'File Size';
$lang->im->downloadPackage = 'Full Package';
$lang->im->downloadConfig  = 'Only Config';
$lang->im->changeSetting   = 'Change Settings';
$lang->im->downloadConf    = 'Download Config';
$lang->im->day             = 'd';
$lang->im->hours           = 'h';
$lang->im->minute          = 'm';
$lang->im->secs            = 's';
$lang->im->tokenLifetime   = 'Token Lifetime';
$lang->im->tokenAuthWindow = 'Token Auth Time Window';

$lang->im->logLevel        = 'Log Level';
$lang->im->logLevelSimple  = 'Simple';
$lang->im->logLevelDetail  = 'Detail';
$lang->im->logLevelOptions = array($lang->im->logLevelSimple, $lang->im->logLevelDetail);

$lang->im->notAdmin         = 'You are not admin of chat.';
$lang->im->notGroupCreator  = 'You are not creator of chat.';
$lang->im->notSystemChat    = 'It is not a system chat.';
$lang->im->notGroupChat     = 'It is not a group chat.';
$lang->im->notPublic        = 'It is not a public chat.';
$lang->im->cantChat         = 'No rights to chat.';
$lang->im->chatHasDismissed = 'The chat group has been dismissed.';
$lang->im->needLogin        = 'You need login first.';
$lang->im->notExist         = 'Chat do not exist.';
$lang->im->changeRenameTo   = 'Rename chat to ';
$lang->im->multiChats       = 'Messages belong to different chats.';
$lang->im->notInGroup       = 'You are not in this chat group.';
$lang->im->notInChat        = 'Unable to send message to unrelated chats.';
$lang->im->notSameUser      = 'Unable to send message as someone else.';
$lang->im->errorKey         = 'The key should be a 32 byte string including letters or numbers.';
$lang->im->debugTips        = '<br>%s with administrator to get more information.';
$lang->im->noLogFile        = 'No log file.';
$lang->im->noFopen          = 'Function fopen disabled. Find the following file to see log : %s.';
$lang->im->defaultKey       = 'Do not use default <strong>key</strong>.';
$lang->im->owtIsDisabled    = 'Conference functionality is disabled.';
$lang->im->chatNameTooLong  = 'Chat name is too long.';

$lang->im->xxdServerTip       = 'XXD server address contains protocol and host and port，such as http://192.168.1.35 or http://www.backend.com, that can not be 127.0.0.1.';
$lang->im->xxdServerEmpty     = 'XXD server address is empty.';
$lang->im->xxdServerError     = 'XXD server address can not be 127.0.0.1.';
$lang->im->xxdSchemeError     = 'Server address should started with http:// or https://.';
$lang->im->xxdPortError       = 'Server address should contain valid port and the default is <strong>11443</strong>.';
$lang->im->xxdPollIntTip      = 'Polling interval should be a number equal to or greater than 5, the default value is 60 for 60 seconds.';
$lang->im->xxdPollIntErr      = 'Polling interval should be a number equal to or greater than 5.';
$lang->im->xxdFileSizeErr     = 'File size should be equal to or greater than 0.';
$lang->im->tokenLifetimeErr   = 'Token lifetime should be equal to or greater than 1.';
$lang->im->tokenAuthWindowErr = 'Token auth time window should be equal to or greater than 20.';
$lang->im->errorSSLCrt        = 'SSL certificate cannot be empty';
$lang->im->errorSSLKey        = 'SSL key cannot be empty';
$lang->im->xxdAESTip          = 'This only affects server-side AES encryption between XXB and XXD.';

$lang->im->errorClientVersionNotSupport = 'The client version is too low (%s), and the minimum version supported by the current server is %s. Please download the latest version from the official website.https://xuanim.com';

$lang->im->broadcast = new stdclass();
$lang->im->broadcast->createChat           = '%s created the group [%s](#/chats/groups/%s).';
$lang->im->broadcast->changeChatOwnership  = 'Group [%s](#/chats/groups/%s) owner changed to %s.';
$lang->im->broadcast->joinChat             = '%s joined.';
$lang->im->broadcast->leaveChat            = '%s quited.';
$lang->im->broadcast->renameChat           = '%s renamed the group to [%s](#/chats/groups/%s).';
$lang->im->broadcast->renamePrivate        = 'Chat is renamed to [%s](#/chats/recents/%s)。';
$lang->im->broadcast->inviteUser           = '%s invited %s to join.';
$lang->im->broadcast->dismissChat          = '%s dismissed the group.';

$lang->im->broadcast->createConference           = '%s started a conference.';
$lang->im->broadcast->closeConference            = '%s closed the conference.';
$lang->im->broadcast->createConferenceInvitation = '%s invited %s to join the conference.';
$lang->im->broadcast->conferenceInviteeOccupied  = '%s is busy, try again later.';

$lang->im->conference = new stdclass();
$lang->im->conference->userBusy    = 'Line busy, try again later.';
$lang->im->conference->userOffline = 'User is offline.';

$lang->im->xxd = new stdclass();
$lang->im->xxd->os             = 'OS';
$lang->im->xxd->ip             = 'Listen IP';
$lang->im->xxd->chatPort       = 'Chat Port';
$lang->im->xxd->commonPort     = 'Common Port';
$lang->im->xxd->https          = 'HTTPS';
$lang->im->xxd->aes            = 'Server-side AES';
$lang->im->xxd->uploadFileSize = 'File Size';
$lang->im->xxd->maxOnlineUser  = 'Max Online User Counts';
$lang->im->xxd->sslcrt         = 'SSL Crt';
$lang->im->xxd->sslkey         = 'SSL Key';
$lang->im->xxd->max            = 'Max';

$lang->im->httpsOptions['on']  = $lang->im->enabled;
$lang->im->httpsOptions['off'] = $lang->im->disabled;

$lang->im->aesOptions['on']  = $lang->im->enabled;
$lang->im->aesOptions['off'] = $lang->im->disabled;

$lang->im->placeholder = new stdclass();
$lang->im->placeholder->xxd = new stdclass();
$lang->im->placeholder->xxd->ip             = 'Listen to the server IP address, Default 0.0.0.0';
$lang->im->placeholder->xxd->chatPort       = 'The port on which the chat client communicates';
$lang->im->placeholder->xxd->commonPort     = 'Generic port used for client login verification and for file upload and download';
$lang->im->placeholder->xxd->https          = 'HTTPS';
$lang->im->placeholder->xxd->uploadFileSize = 'Upload file size';
$lang->im->placeholder->xxd->maxOnlineUser  = 'Maximum number of user online';
$lang->im->placeholder->xxd->sslcrt         = 'Copy the certificate content here';
$lang->im->placeholder->xxd->sslkey         = 'Copy the certificate key here';

$lang->im->notify = new stdclass();
$lang->im->notify->setReceiver = 'Not set receiver type, it must be users or chat group.';
$lang->im->notify->setGroup    = 'Should set chat group.';
$lang->im->notify->setUserList = 'Should set user list.';
$lang->im->notify->setSender   = 'Should set sender info.';
$lang->im->notify->setTitle    = 'Notification title not set.';

$lang->im->osList['win_i386']      = 'Windows_i386';
$lang->im->osList['win_x86_64']    = 'Windows_x86_64';
$lang->im->osList['linux_i386']    = 'Linux_i386';
$lang->im->osList['linux_x86_64']  = 'Linux_x86_64';
$lang->im->osList['darwin_x86_64'] = 'macOS';

$lang->pinnedMessages = new stdclass();
$lang->pinnedMessages->limit = 'The number of pinned messages has reached the limit';
