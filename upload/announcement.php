<?php
/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2001-2005 Comsenz Technology Ltd (www.discuz.com)                ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (tech@discuz.com) Cnteacher (cnteacher@discuz.com) ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

require './include/common.php';
require DISCUZ_ROOT.'./include/discuzcode.php';
include DISCUZ_ROOT.'./forumdata/cache/cache_viewthread.php';

$discuz_action  = 21;
$total          =  0;
$id             = intval($id);
$ppp            = 10;

$query = $db->query("SELECT id, starttime, endtime FROM $table_announcements ");
while($announce = $db->fetch_array($query)) {
	if($timestamp >= $announce['starttime'] && ($timestamp <= $announce['endtime'] || !$announce['endtime'])) {
		$total++;
		if($announce['id'] == $id) {
			$page = ceil($total / $ppp);
		}
	}
}

if($total != $db->num_rows($query)) {
	$db->query("DELETE FROM $table_announcements WHERE endtime<>'0' AND endtime<'$timestamp'");
	require_once DISCUZ_ROOT.'./include/cache.php';
	updatecache('announcements');
	updatecache('announcements_forum');
}

if(!$total) {
	showmessage('announcement_nonexistence');
}

$page = intval($page) ? intval($page) : 1;
$start_limit = ($page - 1) * $ppp;

$multipage = multi($total, $ppp, $page, 'announcement.php?p=1'); 

$announcelist = array();
$query = $db->query("SELECT a.* , m.uid, m.groupid, m.regdate, m.avatar, m.credit, m.postnum , m.site, m.customstatus, m.avatarwidth, m.avatarheight  
						FROM $table_announcements a 
						LEFT JOIN $table_members m ON m.username=a.author
						WHERE a.starttime<'$timestamp' AND a.endtime='0' OR a.endtime>'$timestamp' 
						ORDER BY displayorder, starttime DESC, id DESC LIMIT $start_limit, $ppp");

while($announce = $db->fetch_array($query)) {
	$announce['starttime'] = gmdate($dateformat, $announce['starttime'] + $timeoffset * 3600);
	$announce['regdate'] = gmdate($dateformat, $announce['regdate'] + $timeoffset * 3600);
	$announce['authortitle'] =$_DCACHE['usergroups'][$announce['groupid']]['grouptitle'];
	$announce['stars'] = $_DCACHE['usergroups'][$announce['groupid']]['stars'];

	if($_DCACHE['usergroups'][$announce['groupid']]['groupavatar']) {
		$announce['avatar'] = '<img src="'.$_DCACHE['usergroups'][$announce['groupid']]['groupavatar'].'" border="0">';
	} elseif($_DCACHE['usergroups'][$announce['groupid']]['allowavatar'] && $announce['avatar']) {
		$announce['avatar'] = '<img src="'.$announce['avatar'].'" width="'.$announce['avatarwidth'].'" height="'.$announce['avatarheight'].'" border="0">';
	} else {
		$announce['avatar'] = '';
	}

	$announce['subject'] = strip_tags($announce['subject']);
	if ($announce['posturl']){
		$announce['message'] .= "\nLink to : [url]".$announce['posturl']."[/url]";
	}

	$announce['message'] = postify($announce['message'], 0, 0, 0, 1, 1, 1, 1);
	$announcelist[] = $announce;
}

include template('announcement');

?>