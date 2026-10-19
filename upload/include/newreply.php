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
//fix:  BY pk0909
/*
1 子论坛新贴未影响父论坛
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 12;
$repquote = isset($repquote) ? $repquote : '';

if(!$discuz_uid && !((!$forum['replyperm'] && $allowpost) || ($forum['replyperm'] && strstr($forum['replyperm'], "\t$groupid\t")))) {
	header('Location: logging.php?action=login&referer='.rawurlencode("$PHP_SELF?action=$action&fid=$fid&tid=$tid&pid=$pid&repquote=$repquote"));
	exit();
} elseif(empty($forum['allowreply'])) {
	if(!$forum['replyperm'] && !$allowpost) {
		showmessage('group_nopermission', NULL, 'HALTED');
	} elseif($forum['replyperm'] && !strstr($forum['replyperm'], "\t$groupid\t")) {
		showmessage('post_forum_newreply_nopermission', NULL, 'HALTED');
	}
}

if(empty($thread)) {
	showmessage('thread_nonexistence');
}

if(!submitcheck('replysubmit')) {

	if($repquote) {
		$query = $db->query("SELECT tid, fid, author, message, useip, dateline FROM $table_posts WHERE pid='$repquote'");
		$thaquote = $db->fetch_array($query);
		if($thaquote['tid'] != $tid) {
			showmessage('undefined_action', NULL, 'HALTED');
		}
		$quotefid = $thaquote['fid'];
		$message = $thaquote['message'];

		$time = gmdate("$dateformat $timeformat", $thaquote['dateline'] + ($timeoffset * 3600));
		$message = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is", "[b]**** Hidden message by originally poster *****[/b]", $message);
		$message = preg_replace("/(\[quote])(.*)(\[\/quote])/siU", "", $message);
		$message = cutstr(dhtmlspecialchars($message), 200);

		$thaquote['useip'] = substr($thaquote['useip'], 0, strrpos($thaquote['useip'], '.')).'.x';
		$thaquote['author'] = $thaquote['author'] ? "[i]{$thaquote['author']}[/i]" : "[i]Guest[/i] from {$thaquote['useip']}";
		$message = preg_replace("/\n{2}\[ Last edited by .+? on .+? at .+? \]$/s", '', $message);
		$message = "[quote]Originally posted by {$thaquote['author']} at $time:\n$message [/quote]\n";
	}
	
	$multiattach = $allowpostattach && $attach_replypost ? array_fill(1,$attach_replypost,'1'):0;

	include template('post_newreply');

} else {

	if($subject == '' && $message == '') {
		showmessage('post_sm_isnull');
	}

	if($post_invalid = checkpost()) {
		showmessage($post_invalid);
	}

	if(checkflood()) {
		showmessage('post_flood_ctrl');
	}

	if($thread['closed'] && !$ismoderator) {
		showmessage('post_thread_closed');
	}

	$lastnotifytime = $timestamp - 86400;
	$emails = $comma = '';
	$query = $db->query("SELECT email FROM $table_subscriptions WHERE uid<>'$discuz_uid' AND tid='$tid' AND lastnotify<'$lastnotifytime'");
	while($subs = $db->fetch_array($query)) {
		$emails .= $comma.$subs['email'];
		$comma = ', ';
	}
	if($emails) {
		sendmail($emails, 'email_notify_subject', 'email_notify_message');
		$db->query("UPDATE $table_subscriptions SET lastnotify=$timestamp WHERE tid='$tid' AND lastnotify<'$lastnotifytime'", 'UNBUFFERED');
	}
	if($emailnotify && $discuz_uid) {
		$db->query("INSERT INTO $table_subscriptions (uid, email, tid)
				VALUES ('$discuz_uid', '$email', '$tid')", 'SILENT');
	}

	$paid = $last_attach_type = $aid = $tmark ='';
	if($allowpostattach) {
		$post_attaches = attach_upload();
		if(is_array($post_attaches) && count($post_attaches) && $allowpostattach) {
			foreach( $post_attaches as $v) {
				$db->query("INSERT INTO $table_attachments (tid, uid ,creditsrequire, filename, filetype, filesize, attachment, dateline, downloads)
				VALUES ('$tid', '$discuz_uid', '{$v['creditsrequire']}', '{$v['filename']}', '{$v['filetype']}', '{$v['filesize']}', '{$v['attachment']}', '$timestamp', '0')");
				$v['aid'] = $db->insert_id();
				$aid .= $tmark.''.$v['aid'];
				$tmark = ' ,';
				$last_attach_type = fileext($v['filename'])."\t".$v['filetype'];
				$paid ++;
			}
		}
	}
	unset($post_attaches);

	$bbcodeoff = checkbbcodes($message, $bbcodeoff);
	$smileyoff = checksmilies($message, $smileyoff);

	$db->query("INSERT INTO $table_posts (fid, tid, aid, author, authorid, subject, dateline, message, useip, usesig, bbcodeoff, smileyoff, parseurloff)
			VALUES ('$fid', '$tid', '$paid', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$usesig', '$bbcodeoff', '$smileyoff', '$parseurloff')");
	$pid = $db->insert_id();
	if($aid) {
		$db->query("UPDATE $table_attachments SET pid='$pid' WHERE aid in ($aid)", 'UNBUFFERED');
		$db->query("UPDATE $table_threads SET lastposter='$discuz_user', lastpost='$timestamp', replies=replies+1, attachment='".attachtype($last_attach_type, 'id')."' WHERE tid='$tid' AND fid='$fid'", 'UNBUFFERED');
	} else {
		$db->query("UPDATE $table_threads SET lastposter='$discuz_user', lastpost='$timestamp', replies=replies+1 WHERE tid='$tid' AND fid='$fid'", 'UNBUFFERED');
	}

	updatemember('+', $discuz_uid, $replycredits);

	$db->query("UPDATE $table_forums SET lastpost='".addslashes($thread['subject'])."\t$timestamp\t$discuz_user', posts=posts+1 WHERE fid='$fid'", 'UNBUFFERED');
	if ($forum['fup'] && $forum['type'] == 'sub' && !$forum['viewperm']) {
		$db->query("UPDATE $table_forums SET lastpost='".addslashes($thread['subject'])."\t$timestamp\t$discuz_user'  WHERE fid='{$forum['fup']}'", 'UNBUFFERED');
	}

	@$topicpages = ceil(($thread['replies'] + 2) / $ppp);

	showmessage('post_reply_succeed', "viewthread.php?tid=$tid&pid=$pid&page=$topicpages#pid$pid");
}

?>