<?php
$config->maps['$version'] = '7.2.0';
$config->maps['pager'] = array('type' => 'object', 'name' => 'pager');
$config->maps['pager']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['pager']['dataType'][] = array('name' => 'recTotal', 'type' => 'basic');
$config->maps['pager']['dataType'][] = array('name' => 'pageID', 'type' => 'basic');
$config->maps['pager']['dataType'][] = array('name' => 'recPerPage', 'type' => 'basic');
$config->maps['pager']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['memberStatus'] = array('name' => 'memberStatus', 'type' => 'basic', 'options' => array('','offline','online','busy','away','meeting'), 'dataType' => array());
$config->maps['basicMember'] = array('type' => 'object', 'name' => 'basicMember');
$config->maps['basicMember']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['basicMember']['dataType'][] = array('name' => 'account', 'type' => 'basic');
$config->maps['basicMember']['dataType'][] = array('name' => 'status', 'type' => $config->maps['memberStatus']['type'], 'options' => $config->maps['memberStatus']['options'], 'dataType' => &$config->maps['memberStatus']['dataType']);
$config->maps['basicMember']['dataType'][] = array('name' => 'realname', 'type' => 'basic');
$config->maps['basicMember']['dataType'][] = array('name' => 'dept', 'type' => 'basic');
$config->maps['member'] = array('type' => 'object', 'name' => 'member');
$config->maps['member']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'account', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'status', 'type' => $config->maps['memberStatus']['type'], 'options' => $config->maps['memberStatus']['options'], 'dataType' => &$config->maps['memberStatus']['dataType']);
$config->maps['member']['dataType'][] = array('name' => 'realname', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'avatar', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'gender', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'dept', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'role', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'signed', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'email', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'mobile', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'phone', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'site', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'admin', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'realnames', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'deleted', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'weixin', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'address', 'type' => 'basic');
$config->maps['member']['dataType'][] = array('name' => 'qq', 'type' => 'basic');
$config->maps['userUpdateData'] = array('type' => 'object', 'name' => 'userUpdateData');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'account', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'status', 'type' => $config->maps['memberStatus']['type'], 'options' => $config->maps['memberStatus']['options'], 'dataType' => &$config->maps['memberStatus']['dataType']);
$config->maps['userUpdateData']['dataType'][] = array('name' => 'password', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'address', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'gender', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'mobile', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'phone', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'qq', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'realname', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'weixin', 'type' => 'basic');
$config->maps['userUpdateData']['dataType'][] = array('name' => 'email', 'type' => 'basic');
$config->maps['extension'] = array('type' => 'object', 'name' => 'extension');
$config->maps['extension']['dataType'][] = array('name' => 'name', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'entryID', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'md5', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'download', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'displayName', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'abbrName', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'webViewUrl', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'logo', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'optional', 'type' => 'basic');
$config->maps['extension']['dataType'][] = array('name' => 'enable', 'type' => 'basic');
$config->maps['extensionList'] = array('name' => 'extensionList', 'type' => 'list', 'dataType' => &$config->maps['extension']);
$config->maps['notification'] = array('type' => 'object', 'name' => 'notification');
$config->maps['notification']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'title', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'date', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'sender', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'actions', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'url', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'cgid', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'content', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'subtitle', 'type' => 'basic');
$config->maps['notification']['dataType'][] = array('name' => 'contentType', 'type' => 'basic', 'options' => array('text','plain'));
$config->maps['notification']['dataType'][] = array('name' => 'type', 'type' => 'basic', 'options' => array('notification'));
$config->maps['chatMessage'] = array('type' => 'object', 'name' => 'chatMessage');
$config->maps['chatMessage']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'cgid', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'user', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'date', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'content', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'index', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['chatMessage']['dataType'][] = array('name' => 'type', 'type' => 'basic', 'options' => array('','normal','broadcast','notification'));
$config->maps['chatMessage']['dataType'][] = array('name' => 'contentType', 'type' => 'basic', 'options' => array('','text','plain','image','file','emoticon','code','object'));
$config->maps['chatMessage']['dataType'][] = array('name' => 'deleted', 'type' => 'basic');
$config->maps['chatMembers'] = array('type' => 'object', 'name' => 'chatMembers');
$config->maps['chatMembers']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['chatMembers']['dataType'][] = array('name' => 'members', 'type' => 'basic');
$config->maps['chatMemberDetail'] = array('type' => 'object', 'name' => 'chatMemberDetail');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'account', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'join', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'lastSeen', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'lastPost', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'isOwner', 'type' => 'basic');
$config->maps['chatMemberDetail']['dataType'][] = array('name' => 'isAdmin', 'type' => 'basic');
$config->maps['chatstar'] = array('type' => 'object', 'name' => 'chatstar');
$config->maps['chatstar']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['chatstar']['dataType'][] = array('name' => 'star', 'type' => 'basic');
$config->maps['chatMessageList'] = array('name' => 'chatMessageList', 'type' => 'list', 'dataType' => &$config->maps['chatMessage']);
$config->maps['sendingChatMessage'] = array('type' => 'object', 'name' => 'sendingChatMessage');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'cgid', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'user', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'content', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'type', 'type' => 'basic', 'options' => array('','normal','broadcast','notification'));
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'contentType', 'type' => 'basic', 'options' => array('','text','plain','image','file','emoticon','code','object'));
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'date', 'type' => 'basic');
$config->maps['sendingChatMessage']['dataType'][] = array('name' => 'deleted', 'type' => 'basic');
$config->maps['sendingChatMessageList'] = array('name' => 'sendingChatMessageList', 'type' => 'list', 'dataType' => &$config->maps['sendingChatMessage']);
$config->maps['basicChat'] = array('type' => 'object', 'name' => 'basicChat');
$config->maps['basicChat']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'createdDate', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'createdBy', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'ownedBy', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'name', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'lastActiveTime', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'lastMessage', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'public', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'archiveDate', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'star', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'freeze', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'mute', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'hide', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'editedDate', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'type', 'type' => 'basic', 'options' => array('group','one2one','system','robot'));
$config->maps['basicChat']['dataType'][] = array('name' => 'dismissDate', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'committers', 'type' => 'basic', 'options' => array('','$ADMINS','$ALL'));
$config->maps['basicChat']['dataType'][] = array('name' => 'admins', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'pinnedMessages', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'lastReadMessage', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'mergedDate', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'mergedChats', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'lastReadMessageIndex', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'lastMessageInfo', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'adminInvite', 'type' => 'basic');
$config->maps['basicChat']['dataType'][] = array('name' => 'avatar', 'type' => 'basic');
$config->maps['chat'] = $config->maps['basicChat'];
$config->maps['chat']['dataType'][] = array('name' => 'members', 'type' => 'basic');
$config->maps['basicChatList'] = array('name' => 'basicChatList', 'type' => 'list', 'dataType' => &$config->maps['basicChat']);
$config->maps['chatList'] = array('name' => 'chatList', 'type' => 'list', 'dataType' => &$config->maps['chat']);
$config->maps['basicPack'] = array('type' => 'object', 'name' => 'basicPack');
$config->maps['basicPack']['dataType'][] = array('name' => 'rid', 'type' => 'basic');
$config->maps['basicPack']['dataType'][] = array('name' => 'method', 'type' => 'basic', 'options' => array('messagesend','syssessionid','usergetlist','chatgetlist','userlogin','userlogout','userupdate','usersyncsettings','userkickoff','messageretract','messageupdate','chattyping','chatcreate','chatrename','chatinvite','chatkick','chatjoin','chatleave','chatstar','chatunstar','chatmute','chatunmute','chatfreeze','chatunfreeze','chathide','chatarchive','chatdismiss','chatgethistory','chatgetpubliclist','chatsetcategory','chatrenamecategory','chatdeletecategory','chataddadmins','chatremoveadmins','chatsetcommitters','chatsetvisibility','chatsetconfig','chatsearch','chatsetavatar','chatsetlastreadmessage','chatsetlastreadmessagebyindex','syncusers','syncnotifications','syncofflinemessages','extensiongetlist','fileupload','filedownload','usersetdevicetoken','sysgetserverinfo','sysserverstart','ping'));
$config->maps['basicPack']['dataType'][] = array('name' => 'module', 'type' => 'basic', 'options' => array('','im','server','sys','chat','entry','user','extension'));
$config->maps['basicPack']['dataType'][] = array('name' => 'device', 'type' => 'basic', 'options' => array('desktop','mobile'));
$config->maps['requestPack'] = $config->maps['basicPack'];
$config->maps['requestPack']['dataType'][] = array('name' => 'params', 'type' => 'basic');
$config->maps['requestPack']['dataType'][] = array('name' => 'userID', 'type' => 'basic');
$config->maps['requestPack']['dataType'][] = array('name' => 'lang', 'type' => 'basic', 'options' => array('zh-cn','zh-tw','en'));
$config->maps['requestPack']['dataType'][] = array('name' => 'version', 'type' => 'basic', 'options' => array('4.0.beta3','4.0'));
$config->maps['responsePack'] = $config->maps['basicPack'];
$config->maps['responsePack']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['responsePack']['dataType'][] = array('name' => 'result', 'type' => 'basic', 'options' => array('success','fail'));
$config->maps['responsePack']['dataType'][] = array('name' => 'message', 'type' => 'basic');
$config->maps['messageResponsePack'] = $config->maps['responsePack'];
$config->maps['messageResponsePack']['dataType'][] = array('name' => 'code', 'type' => 'basic');
$config->maps['memberResponsePack'] = $config->maps['responsePack'];
$config->maps['memberResponsePack']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['member']['type'], 'dataType' => &$config->maps['member']['dataType']);
$config->maps['chatResponsePack'] = $config->maps['responsePack'];
$config->maps['chatResponsePack']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['chat']['type'], 'dataType' => &$config->maps['chat']['dataType']);
$config->maps['chatListResponsePack'] = $config->maps['responsePack'];
$config->maps['chatListResponsePack']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['basicChatList']['type'], 'dataType' => &$config->maps['basicChatList']['dataType']);
$config->maps['messageListRequestPack'] = $config->maps['requestPack'];
$config->maps['messageListRequestPack']['dataType'][4] = array('name' => 'params', 'type' => 'list', 'dataType' => &$config->maps['sendingChatMessageList']);
$config->maps['messageListResponsePack'] = $config->maps['responsePack'];
$config->maps['messageListResponsePack']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['chatMessageList']['type'], 'dataType' => &$config->maps['chatMessageList']['dataType']);
$config->maps['sysgetdeptsResponse'] = $config->maps['responsePack'];
$config->maps['sysgetdeptsResponse']['dataType'][] = array('name' => 'roles', 'type' => 'basic');
$config->maps['userloginResponse'] = $config->maps['memberResponsePack'];
$config->maps['userloginResponse']['name'] = 'userloginResponse';
$config->maps['userlogoutResponse'] = $config->maps['memberResponsePack'];
$config->maps['userlogoutResponse']['name'] = 'userlogoutResponse';
$config->maps['usergetlistResponse'] = $config->maps['responsePack'];
$config->maps['usergetlistResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['member']);
$config->maps['chatSearchResult'] = array('type' => 'object', 'name' => 'chatSearchResult');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'id', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'gid', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'name', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'public', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'groupOwner', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'createdDate', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'archiveDate', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'lastActiveTime', 'type' => 'basic');
$config->maps['chatSearchResult']['dataType'][] = array('name' => 'userCount', 'type' => 'basic');
$config->maps['chatsearchResponse'] = $config->maps['responsePack'];
$config->maps['chatsearchResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['chatSearchResult']);
$config->maps['chatsearchResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['usergetbasiclistResponse'] = $config->maps['responsePack'];
$config->maps['usergetbasiclistResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['basicMember']);
$config->maps['usergetbasiclistResponse']['dataType'][] = array('name' => 'roles', 'type' => 'basic');
$config->maps['usergetbasiclistResponse']['dataType'][] = array('name' => 'depts', 'type' => 'basic');
$config->maps['usergetbasiclistResponse']['dataType'][] = array('name' => 'basic', 'type' => 'basic');
$config->maps['usergetdeletedRequest'] = array('type' => 'object', 'name' => 'usergetdeletedRequest');
$config->maps['usergetdeletedResponse'] = $config->maps['usergetlistResponse'];
$config->maps['usergetdeletedResponse']['name'] = 'usergetdeletedResponse';
$config->maps['usersearchResponse'] = $config->maps['usergetlistResponse'];
$config->maps['usersearchResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['usersearchidResponse'] = $config->maps['responsePack'];
$config->maps['usersearchidResponse']['dataType'][4] = array('name' => 'data', 'type' => 'basic');
$config->maps['usersearchidResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['chatgetlistResponse'] = $config->maps['chatListResponsePack'];
$config->maps['chatgetlistResponse']['name'] = 'chatgetlistResponse';
$config->maps['chatgetmembersResponse'] = $config->maps['responsePack'];
$config->maps['chatgetmembersResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['chatMembers']['type'], 'dataType' => &$config->maps['chatMembers']['dataType']);
$config->maps['chatgetmemberdetailsResponse'] = $config->maps['responsePack'];
$config->maps['chatgetmemberdetailsResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['chatMemberDetail']);
$config->maps['chatgetmemberdetailsResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['chatstarResponse'] = $config->maps['responsePack'];
$config->maps['chatstarResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['chatstar']['type'], 'dataType' => &$config->maps['chatstar']['dataType']);
$config->maps['chatgetlastmessageResponse'] = $config->maps['messageListResponsePack'];
$config->maps['chatgetlastmessageResponse']['name'] = 'chatgetlastmessageResponse';
$config->maps['chatMessageInfoPack'] = array('type' => 'object', 'name' => 'chatMessageInfoPack');
$config->maps['chatMessageInfoPack']['dataType'][] = array('name' => 'lastMessage', 'type' => 'basic');
$config->maps['chatMessageInfoPack']['dataType'][] = array('name' => 'messageCount', 'type' => 'basic');
$config->maps['chatgetmessageinfoResponse'] = $config->maps['responsePack'];
$config->maps['chatgetmessageinfoResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['chatMessageInfoPack']['type'], 'dataType' => &$config->maps['chatMessageInfoPack']['dataType']);
$config->maps['usergetlistbydeptResponse'] = $config->maps['responsePack'];
$config->maps['usergetlistbydeptResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['messagesendRequest'] = $config->maps['messageListRequestPack'];
$config->maps['messagesendRequest']['name'] = 'messagesendRequest';
$config->maps['messagesendResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagesendResponse']['name'] = 'messagesendResponse';
$config->maps['messageretractRequest'] = $config->maps['messageListRequestPack'];
$config->maps['messageretractRequest']['name'] = 'messageretractRequest';
$config->maps['messageretractResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messageretractResponse']['name'] = 'messageretractResponse';
$config->maps['messagegetlistResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagegetlistResponse']['name'] = 'messagegetlistResponse';
$config->maps['messagegetlistbyindexesResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagegetlistbyindexesResponse']['name'] = 'messagegetlistbyindexesResponse';
$config->maps['messagesyncResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagesyncResponse']['name'] = 'messagesyncResponse';
$config->maps['messagesyncidResponse'] = $config->maps['responsePack'];
$config->maps['messagesyncidResponse']['dataType'][4] = array('name' => 'data', 'type' => 'basic');
$config->maps['messagesyncmissedResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagesyncmissedResponse']['name'] = 'messagesyncmissedResponse';
$config->maps['messagesyncsinceofflineResponse'] = $config->maps['messageListResponsePack'];
$config->maps['messagesyncsinceofflineResponse']['name'] = 'messagesyncsinceofflineResponse';
$config->maps['syncofflinemessagesResponse'] = $config->maps['messageListResponsePack'];
$config->maps['syncofflinemessagesResponse']['name'] = 'syncofflinemessagesResponse';
$config->maps['syncnotificationsResponse'] = $config->maps['responsePack'];
$config->maps['syncnotificationsResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['notification']);
$config->maps['userupdateRequest'] = $config->maps['requestPack'];
$config->maps['userupdateRequest']['dataType'][4] = array('name' => 'params', 'type' => 'list', 'dataType' => &$config->maps['userUpdateData']);
$config->maps['chatcreateResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatcreateResponse']['name'] = 'chatcreateResponse';
$config->maps['chatjoinResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatjoinResponse']['name'] = 'chatjoinResponse';
$config->maps['chatleaveResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatleaveResponse']['name'] = 'chatleaveResponse';
$config->maps['chatrenameResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatrenameResponse']['name'] = 'chatrenameResponse';
$config->maps['chatinviteResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatinviteResponse']['name'] = 'chatinviteResponse';
$config->maps['chatkickResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatkickResponse']['name'] = 'chatkickResponse';
$config->maps['chatchangeownershipResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatchangeownershipResponse']['name'] = 'chatchangeownershipResponse';
$config->maps['chatsetvisibilityResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatsetvisibilityResponse']['name'] = 'chatsetvisibilityResponse';
$config->maps['chatsetconfigResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatsetconfigResponse']['name'] = 'chatsetconfigResponse';
$config->maps['chataddadminsResponse'] = $config->maps['chatResponsePack'];
$config->maps['chataddadminsResponse']['name'] = 'chataddadminsResponse';
$config->maps['chatremoveadminsResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatremoveadminsResponse']['name'] = 'chatremoveadminsResponse';
$config->maps['chatsetcommittersResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatsetcommittersResponse']['name'] = 'chatsetcommittersResponse';
$config->maps['chatarchiveResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatarchiveResponse']['name'] = 'chatarchiveResponse';
$config->maps['chatdismissResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatdismissResponse']['name'] = 'chatdismissResponse';
$config->maps['chatsetavatarResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatsetavatarResponse']['name'] = 'chatsetavatarResponse';
$config->maps['chatgetbygidResponse'] = $config->maps['chatResponsePack'];
$config->maps['chatgetbygidResponse']['name'] = 'chatgetbygidResponse';
$config->maps['chatgethistoryResponse'] = $config->maps['messageListResponsePack'];
$config->maps['chatgethistoryResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['chatgetpubliclistResponse'] = $config->maps['chatListResponsePack'];
$config->maps['chatgetpubliclistResponse']['name'] = 'chatgetpubliclistResponse';
$config->maps['extensiongetlistResponse'] = $config->maps['responsePack'];
$config->maps['extensiongetlistResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['extensionList']['type'], 'dataType' => &$config->maps['extensionList']['dataType']);
$config->maps['entry/visitRequest'] = $config->maps['requestPack'];
$config->maps['entry/visitRequest']['dataType'][4] = array('name' => 'params', 'type' => 'basic');
$config->maps['errormessageResponse'] = $config->maps['messageResponsePack'];
$config->maps['errormessageResponse']['name'] = 'errormessageResponse';
$config->maps['conferenceAction'] = array('type' => 'object', 'name' => 'conferenceAction');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'room', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'type', 'type' => 'basic', 'options' => array('create','join','close','leave','invite','publish'));
$config->maps['conferenceAction']['dataType'][] = array('name' => 'invitee', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'participants', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'date', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'user', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'device', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'data', 'type' => 'basic');
$config->maps['conferenceAction']['dataType'][] = array('name' => 'actions', 'type' => 'list', 'dataType' => &$config->maps['conferenceAction']);
$config->maps['conferenceAction']['dataType'][] = array('name' => 'number', 'type' => 'basic');
$config->maps['conference'] = array('type' => 'object', 'name' => 'conference');
$config->maps['conference']['dataType'][] = array('name' => 'cgid', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'room', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'status', 'type' => 'basic', 'options' => array('closed','open','notStarted'));
$config->maps['conference']['dataType'][] = array('name' => 'openedBy', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'openedDate', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'participants', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'invitee', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'actions', 'type' => 'list', 'dataType' => &$config->maps['conferenceAction']);
$config->maps['conference']['dataType'][] = array('name' => 'topic', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'startTime', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'endTime', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'password', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'type', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'number', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'subscribers', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'note', 'type' => 'basic');
$config->maps['conference']['dataType'][] = array('name' => 'reminderTime', 'type' => 'basic');
$config->maps['conferencecreateResponse'] = $config->maps['responsePack'];
$config->maps['conferencecreateResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['conference']['type'], 'dataType' => &$config->maps['conference']['dataType']);
$config->maps['conferencecreatedetachedResponse'] = $config->maps['responsePack'];
$config->maps['conferencecreatedetachedResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['conference']['type'], 'dataType' => &$config->maps['conference']['dataType']);
$config->maps['conferenceactionResponse'] = $config->maps['responsePack'];
$config->maps['conferenceactionResponse']['dataType'][4] = array('name' => 'data', 'type' => $config->maps['conferenceAction']['type'], 'dataType' => &$config->maps['conferenceAction']['dataType']);
$config->maps['conferenceinviteResponse'] = $config->maps['conferenceactionResponse'];
$config->maps['conferenceinviteResponse']['name'] = 'conferenceinviteResponse';
$config->maps['conferencejoinResponse'] = $config->maps['conferenceactionResponse'];
$config->maps['conferencejoinResponse']['name'] = 'conferencejoinResponse';
$config->maps['conferenceleaveResponse'] = $config->maps['conferenceactionResponse'];
$config->maps['conferenceleaveResponse']['name'] = 'conferenceleaveResponse';
$config->maps['conferencecloseResponse'] = $config->maps['conferenceactionResponse'];
$config->maps['conferencecloseResponse']['name'] = 'conferencecloseResponse';
$config->maps['conferencesyncactionResponse'] = $config->maps['conferenceactionResponse'];
$config->maps['conferencesyncactionResponse']['name'] = 'conferencesyncactionResponse';
$config->maps['syncconferencesResponse'] = $config->maps['responsePack'];
$config->maps['syncconferencesResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['conference']);
$config->maps['conferenceGetByNumberResponse'] = $config->maps['responsePack'];
$config->maps['conferenceGetByNumberResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['conference']);
$config->maps['conferenceGetByConditionResponse'] = $config->maps['responsePack'];
$config->maps['conferenceGetByConditionResponse']['dataType'][4] = array('name' => 'data', 'type' => 'list', 'dataType' => &$config->maps['conference']);
$config->maps['conferenceGetByConditionResponse']['dataType'][] = array('name' => 'pager', 'type' => $config->maps['pager']['type'], 'dataType' => &$config->maps['pager']['dataType']);
$config->maps['usersubscribeRequest'] = array('type' => 'object', 'name' => 'usersubscribeRequest');
$config->maps['usersubscribeRequest']['dataType'][] = array('name' => 'rid', 'type' => 'basic');
$config->maps['usersubscribeRequest']['dataType'][] = array('name' => 'type', 'type' => 'basic');
$config->maps['usersubscribeRequest']['dataType'][] = array('name' => 'objects', 'type' => 'basic');
$config->maps['usersubscribeRequest']['dataType'][] = array('name' => 'userID', 'type' => 'basic');