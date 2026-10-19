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
1 图标排列问题
2 子论坛新贴未影响父论坛
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 11;

if(empty($forum['fid']) || $forum['type'] == 'group') {
	showmessage('forum_nonexistence');
}

if(!$discuz_uid && !((!$forum['postperm'] && $allowpost) || ($forum['postperm'] && strstr($forum['postperm'], "\t$groupid\t")))) {
	header('Location: logging.php?action=login&referer='.rawurlencode("$PHP_SELF?action=$action&fid=$fid&poll=$poll"));
	exit();
} elseif(!$forum['allowpost']) {
	if(!$forum['postperm'] && !$allowpost) {
		showmessage('group_nopermission', NULL, 'HALTED');
	} elseif($forum['postperm'] && !strstr($forum['postperm'], "\t$groupid\t")) {
		showmessage('post_forum_newthread_nopermission', NULL, 'HALTED');
	}
}

if(!submitcheck('topicsubmit')) {

	$icons = '';
	if(is_array($_DCACHE['icons'])) {
		$key = 1;
		foreach($_DCACHE['icons'] as $id => $icon) {
			$icons .= ' <input type="radio" name="iconid" value="'.$id.'"><img src="'.SMDIR.'/'.$icon.'">';
			$icons .= !(++$key % 10) ? '<br>' : '';
		}
	}
	
	$multiattach = $allowpostattach && $attach_newpost ? array_fill(1,$attach_newpost,'1'):0;

	include template('post_newthread');

} else {

	if(!$subject || !$message) {
		showmessage('post_sm_isnull');
	}

	if($post_invalid = checkpost()) {
		showmessage($post_invalid);
	}

	if(checkflood()) {
		showmessage('post_flood_ctrl');
	}

	$displayorder = ($ismoderator && $toptopic) ? 1 : 0;
	$digest = ($ismoderator && $addtodigest && $allowdigest) ? 1 : 0;
	$viewperm = $allowsetviewperm ? $viewperm : 0;

	if($poll == 'yes' && $allowpostpoll && trim($polloptions)) {
		$poll = 1;
		$pollarray = array();
		$polloptions = explode("\n", $polloptions);
		if(count($polloptions) > $maxpolloptions) {
			showmessage('post_poll_option_toomany');
		}

		foreach($polloptions as $polloption) {
			$polloption = trim($polloption);
			if($polloption) {
				$pollarray['options'][] = array($polloption, 0);
			}
		}
		$pollarray['multiple'] = $multiplepoll;
		$pollarray['voters'] = array();
		$pollopts = addslashes(serialize($pollarray));
	} else {
		$poll = 0;
		$pollopts = '';
	}

	$paid = $last_attach_type = $aid = $tmark ='';
	$post_attaches = array();
	if ($allowpostattach){
		$post_attaches = attach_upload();
		if(is_array($post_attaches) && count($post_attaches)) {
			foreach( $post_attaches as $v) {
				$db->query("INSERT INTO $table_attachments (uid, creditsrequire, filename, filetype, filesize, attachment, dateline, downloads)
					VALUES ('$discuz_uid', '$v[creditsrequire]', '$v[filename]', '$v[filetype]', '$v[filesize]', '$v[attachment]', '$timestamp','0')");
				$v['aid'] = $db->insert_id();
				$aid .= $tmark.''.$v['aid'];
				$tmark = ' ,';
				$last_attach_type = strtolower(fileext($v['filename']))."\t".$v['filetype'];
				$paid ++;
			}
		}
	}
	unset($post_attaches);

	$db->query("INSERT INTO $table_threads (fid, creditsrequire, iconid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, poll, attachment)
		VALUES ('$fid', '$viewperm', '$iconid', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$timestamp', '$discuz_user', '$displayorder', '$digest', '$poll', '".attachtype($last_attach_type, 'id')."')");
	$tid = $db->insert_id();

	if($poll) {
		$db->query("INSERT INTO $table_polls (tid, pollopts)
			VALUES ('$tid', '$pollopts')");
	}

	$bbcodeoff = checkbbcodes($message, $bbcodeoff);
	$smileyoff = checksmilies($message, $smileyoff);

	$db->query("INSERT INTO $table_posts (fid, tid, aid, author, authorid, subject, dateline, message, useip, usesig, bbcodeoff, smileyoff, parseurloff)
		VALUES ('$fid', '$tid', '$paid', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$usesig', '$bbcodeoff', '$smileyoff', '$parseurloff')");
	$pid = $db->insert_id();

	if($aid) {
		$db->query("UPDATE $table_attachments SET tid='$tid', pid='$pid' WHERE aid in ($aid)", 'UNBUFFERED');
	}

	updatemember('+', $discuz_uid, $postcredits);

	$db->query("UPDATE $table_forums SET lastpost='$subject\t$timestamp\t$discuz_user', threads=threads+1, posts=posts+1 WHERE fid='$fid'", 'UNBUFFERED');
	if ($forum['fup'] && $forum['type'] == 'sub' && !$forum['viewperm']) {
		$db->query("UPDATE $table_forums SET lastpost='$subject\t$timestamp\t$discuz_user'  WHERE fid='$forum[fup]'", 'UNBUFFERED');
	}

	if($emailnotify && $discuz_uid) {
		$query = $db->query("SELECT tid FROM $table_subscriptions WHERE uid='$discuz_uid' AND tid='$tid'");
		if(!$db->result($query, 0)) {
			$db->query("INSERT INTO $table_subscriptions (uid, email, tid)
				VALUES ('$discuz_uid', '$email', '$tid')", 'UNBUFFERED');
		}
	}
	showmessage('post_newthread_succeed', "viewthread.php?tid=$tid");
}

?>