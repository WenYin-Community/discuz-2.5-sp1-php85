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

if($tid) {
	$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
	$thread = $db->fetch_array($query);
}

if($forum['type'] == 'forum') {
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a> ";
	$navtitle = ' - '.strip_tags($forum['name']).' - '.$thread['subject'];
} elseif($forum['type'] == 'sub') {
	$query = $db->query("SELECT name, fid FROM $table_forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a> ";
	$navtitle = ' - '.strip_tags($fup['name']).' - '.strip_tags($forum['name']).' - '.$thread['subject'];
}


if($action == 'votepoll') {

	if(!$discuz_user || !$allowvote) {
		showmessage('group_nopermission', NULL, 'HALTED');
	}

	$query = $db->query("SELECT pollopts FROM $table_polls WHERE tid='$tid'");
	$pollarray = unserialize($db->result($query, 0));
	if(!is_array($pollarray) || !$pollarray) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!empty($thread['closed'])) {
		showmessage('thread_poll_closed');
	}

	if(in_array($discuz_user, $pollarray['voters'])) {
		showmessage('thread_poll_voted');
	}

	if(!is_array($pollanswers) || count($pollanswers) < 1) {
		showmessage('thread_poll_invalid');
	}

	if(empty($pollarray['multiple']) && count($pollanswers) > 1) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	foreach($pollanswers as $id) {
		if(isset($pollarray['options'][$id][0])) {
			if(++$pollarray['options'][$id][1] > $pollarray['max']) {
				$pollarray['max'] = $pollarray['options'][$id][1];
			}
			$pollarray['total']++;
		} else {
			showmessage('undefined_action', NULL, 'HALTED');
		}
	}

	$pollarray['voters'][] = $discuz_user;

	$pollopts = addslashes(serialize($pollarray));
	$db->query("UPDATE $table_polls SET pollopts='$pollopts' WHERE tid='$tid'", 'UNBUFFERED');
	$db->query("UPDATE $table_threads SET lastpost='$timestamp' WHERE tid='$tid'", 'UNBUFFERED');

	showmessage('thread_poll_succeed', "viewthread.php?tid=$tid");

} elseif($action == 'emailfriend') {

	if(!submitcheck('sendsubmit')) {

		$discuz_action = 122;
		$threadurl = "{$boardurl}viewthread.php?tid=$tid";

		$query = $db->query("SELECT email FROM $table_members WHERE uid='$discuz_uid'");
		$email = $db->result($query, 0);

		include template('emailfriend');

	} else {

		if(empty($fromname) || empty($fromemail) || empty($sendtoname) || empty($sendtoemail)) {
			showmessage('email_friend_invalid');
		}

		sendmail($sendtoemail, $subject, $message, "$fromname <$fromemail>");

		showmessage('email_friend_succeed', "viewthread.php?tid=$tid");

	}

} elseif($action == 'karma' && $pid) {

	$discuz_action = 121;

	if(!$allowkarma || !$maxkarmarate) {
		showmessage('group_nopermission', NULL, 'HALTED');
	}

	$score = intval($score);
	$offset = ceil($maxkarmarate / 6);
	$minkarmarate = $offset - $maxkarmarate;
	if($score < $minkarmarate || $score > $maxkarmarate) {
		showmessage('thread_karma_range_invalid');
	}


	$query = $db->query("SELECT SUM(score) FROM $table_karmalog WHERE uid='$discuz_uid' AND dateline>=".($timestamp-86400));
    if($maxrateperday &&  $maxrateperday <= $db->result($query, 0)) {
        showmessage('thread_karma_ctrl');
    }
    if ($timestamp % 10 == 1) {
        $db->query("DELETE FROM $table_karmalog WHERE dateline<".($timestamp-2592000), 'UNBUFFERED');
    } 


	$query = $db->query("SELECT author, authorid, dateline FROM $table_posts WHERE pid='$pid'");
	if(!$post = $db->fetch_array($query)) {
		showmessage('undefined_action');
	} elseif($post['authorid'] == $discuz_uid) {
		showmessage('thread_karma_member_invalid');
	} elseif($delaykarma && ($timestamp - $post['dateline'] > $delaykarma) && $adminid != 1 ){
		showmessage('thread_karma_timeout');
	}

	$query = $db->query("SELECT pid FROM $table_karmalog WHERE uid='$discuz_uid' AND pid='$pid' LIMIT 1");
	if($db->result($query, 0)) {
		showmessage('thread_karma_duplicate');
	}

	if(!submitcheck('karmasubmit')) {

		include template('karma');

	} else {

		$score = intval($score);
		if($score >= 0) {
			$score = "+$score";
		}

		$query = $db->query("SELECT m.adminid, u.groupid FROM $table_members m
					LEFT JOIN $table_usergroups u ON type='member' AND m.credit$score>=u.creditshigher AND m.credit$score<u.creditslower
					WHERE uid='$post[authorid]'");
		$member = $db->fetch_array($query);
		$groupidadd = $member['adminid'] == 0 ? ", groupid='$member[groupid]'" : NULL;

		$db->query("UPDATE $table_members SET credit=credit$score $groupidadd WHERE uid='$post[authorid]'", 'UNBUFFERED');
		$db->query("INSERT INTO $table_karmalog (uid, pid, dateline, score)
			VALUES ('$discuz_uid', '$pid', '$timestamp', '".abs($score)."')", 'UNBUFFERED');

		$ratetimes = round($maxkarmarate / 5);
		$db->query("UPDATE $table_posts SET rate=rate$score, ratetimes=ratetimes+$ratetimes WHERE pid='$pid'", 'UNBUFFERED');

		@$fp = fopen(DISCUZ_ROOT.'./forumdata/karmalog.php', 'a');
		@flock($fp, 2);
		@fwrite($fp, "$timestamp\t$discuz_user\t$groupid\t$post[author]\t$score\t$tid\t$thread[subject]\n");
		@fclose($fp);

		showmessage('thread_karma_succeed', "viewthread.php?tid=$tid");

	}

} elseif($action == 'report') {

	if(!$reportpost) {
		showmessage('thread_report_disabled');
	}

	if(!$discuz_user) {
		showmessage('not_loggedin', NULL, 'HALTED');
	}

	if(!$thread || !is_numeric($pid)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!submitcheck('reportsubmit')) {

		$discuz_action = 123;
		include template('reportpost');

	} else {

		$posturl = "{$boardurl}viewthread.php?tid=$tid".($page || $pid ? "&page=$page#pid$pid" : NULL);

		$message = "Someone has reported the following post to you, please visit: \[url\]$posturl\[/url\]\n\nHis/Her reasons are: $reason";

		$reportto = array();
		$conditions = "username IN ('".str_replace(',', '\',\'', str_replace(' ', '', $forum['moderator']))."') OR adminid='1' OR adminid='2'";
		$query = $db->query("SELECT uid FROM $table_members WHERE $conditions");
		while($member = $db->fetch_array($query)) {
			$reportto[] = $member['uid'];
		}

		foreach($reportto as $moderatorid) {
			$db->query("INSERT INTO $table_pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$moderatorid', 'inbox', '1', 'Report post to you from $discuz_user...', '$timestamp', '$message')");
		}
		$db->query("UPDATE $table_members SET newpm='1' WHERE $conditions");

		showmessage('thread_report_succeed', "viewthread.php?tid=$tid&page=$page&fpage=$fpage");

	}

}elseif($action == 'Information') {
	showmessage(base64_decode('PHRhYmxlIHdpZHRoPSIzMDAiIGFsaWduPSJjZW50ZXIiPjx0cj48dGQgY29sc3Bhbj0iMiI+RGlzY3V6ISBCb2FyZCBJbmZvcm1hdGlvbiA8YnI+PGJyPjwvdGQ+PC90cj48dHI+PHRkPlByb2R1Y3QtbmFtZTwvdGQ+PHRkPkRpc2N1eiEgRnJlZSAyLlg8L3RkPjwvdHI+Cjx0cj48dGQ+QXV0aG9yPC90ZD48dGQ+Q3Jvc3NkYXksQ250ZWFjaGVyLFBLMDkwOTwvdGQ+PC90cj4KPHRyPjx0ZD5TdXBwb3J0PC90ZD48dGQ+SHR0cDovL3d3dy5kaXNjdXoubmV0PC90ZD48L3RyPgo8dHI+PHRkPkZyZWUtU3VwcG9ydDwvdGQ+PHRkPkh0dHA6Ly93d3cuZnJlZWRpc2N1ei5uZXQ8L3RkPjwvdHI+CjwvdGFibGU+'));

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

?>