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

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

if(!submitcheck('forumsubmit', 1) && !submitcheck('membersubmit', 1) && !submitcheck('threadsubmit', 1)) {

	include CP_TPL.'counter.php';

} elseif(submitcheck('forumsubmit', 1)) {

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=counter&current=$next&pertask=$pertask&forumsubmit=yes";
	$processed = 0;

	$queryf = $db->query("SELECT fid FROM $table_forums WHERE type<>'group' LIMIT $current, $pertask");
	while($forum = $db->fetch_array($queryf)) {
		$processed = 1;

		$query = $db->query("SELECT COUNT(*) FROM $table_threads WHERE fid='{$forum['fid']}'");
		$threadnum = $db->result($query, 0);
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE fid='{$forum['fid']}'");
		$postnum = $db->result($query, 0);

		$query = $db->query("SELECT subject, lastpost, lastposter FROM $table_threads WHERE fid='{$forum['fid']}' ORDER BY lastpost DESC LIMIT 1");
		$thread = $db->fetch_array($query);
		$lastpost = addslashes("{$thread['subject']}\t{$thread['lastpost']}\t{$thread['lastposter']}");

		$db->query("UPDATE $table_forums SET threads='$threadnum', posts='$postnum', lastpost='$lastpost' WHERE fid='{$forum['fid']}'");
	}

	if($processed) {
		cpmsg("{$lang['counter_forum']}: {$lang['counter_processing']}", $nextlink);
	} else {
		$db->query("UPDATE $table_forums SET threads='0', posts='0' WHERE type='group'");
		cpmsg('counter_forum_succeed');
	}

} elseif(submitcheck('threadsubmit', 1)) {

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=counter&current=$next&pertask=$pertask&threadsubmit=yes";
	$processed = 0;

	$queryt = $db->query("SELECT tid FROM $table_threads LIMIT $current, $pertask");
	while($threads = $db->fetch_array($queryt)) {
		$processed = 1;
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='{$threads['tid']}'");
		$replynum = $db->result($query, 0);
		$replynum--;
		$db->query("UPDATE $table_threads SET replies='$replynum' WHERE tid='{$threads['tid']}'");
	}

	if($processed) {
		cpmsg("{$lang['counter_thread']}: {$lang['counter_processing']}", $nextlink);
	} else {
		cpmsg('counter_thread_succeed');
	}

} elseif(submitcheck('membersubmit', 1)) {

	if(!$current) {
		$current = 0;
	}
	$pertask = intval($pertask);
	$current = intval($current);
	$next = $current + $pertask;
	$nextlink = "admincp.php?action=counter&current=$next&pertask=$pertask&membersubmit=yes";
	$processed = 0;

	$queryt = $db->query("SELECT uid FROM $table_members LIMIT $current, $pertask");
	while($mem = $db->fetch_array($queryt)) {
		$processed = 1;
		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE authorid='{$mem['uid']}'");
		$db->query("UPDATE $table_members SET postnum='".$db->result($query, 0)."' WHERE uid='{$mem['uid']}'");
	}

	if($processed) {
		cpmsg("{$lang['counter_member']}: {$lang['counter_processing']}", $nextlink);
	} else {
		cpmsg('counter_member_succeed');
	}
}

?>