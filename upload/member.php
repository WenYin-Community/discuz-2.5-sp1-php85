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

if($action == 'online') {

	$discuz_action = 31;
	@include language('actions');

	$page = (int)( intval($page) ?  $page : 1 );
	$start = ($page - 1) * $memberperpage;

	$query = $db->query("SELECT COUNT(*) FROM $table_sessions");
	$num = $db->result($query, 0);
	$multipage = multi($num, $memberperpage, $page, "member.php?action=online");

	$onlinelist = array();
	$query = $db->query("SELECT s.*, f.name, t.subject FROM $table_sessions s
				LEFT JOIN $table_forums f ON s.fid=f.fid
				LEFT JOIN $table_threads t ON s.tid=t.tid
				WHERE s.invisible='0'
				ORDER BY s.lastactivity DESC LIMIT $start, $memberperpage");
	while($online = $db->fetch_array($query)){
		$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + ($timeoffset * 3600));
		$online['action'] = $actioncode[$online['action']];
		$online['subject'] = $online['subject'] ? cutstr($online['subject'], 35) : NULL;
		$online['ip'] = $online['ip1'].'.'.$online['ip2'].'.'.$online['ip3'].'.'.$online['ip4'];

		$onlinelist[] = $online;
	}

	include template('whosonline');

} elseif($action == 'list') {

	$discuz_action = 41;
	if(!$memliststatus) {
		showmessage('member_list_disable', NULL, 'HALTED');
	}

	if(!$order || !in_array($order, array('regdate', 'username', 'gender', 'credit'))) {
		$order = 'regdate';
	}

	$page = (int)( intval($page) ?  $page : 1 );
	$start_limit = ($page - 1) * $memberperpage;

	$sql = '';
	if($admins == 'yes') {
		$sql .= " WHERE adminid>0 ";
	} else {
		$admins = '';
		$sql .= $srchmem ? " WHERE BINARY username LIKE '%$srchmem%' OR username='$srchmem'" : NULL;
	}

	$desc = $desc ? 'desc' : '';
	$query = $db->query("SELECT COUNT(*) FROM $table_members $sql");
	$multipage = multi($db->result($query, 0), $memberperpage, $page, "member.php?action=list&srchmem=".rawurlencode($srchmem)."&order=$order&admins=$admins".($desc ? "&desc=$desc" : NULL));

	$memberlist = array();
	$query = $db->query("SELECT uid, username, gender, email, site, location, regdate, lastvisit, postnum, credit, showemail FROM $table_members $sql ORDER BY ".($order == 'username' ? 'BINARY username' : $order)." $desc LIMIT $start_limit, $memberperpage");
	while ($member = $db->fetch_array($query)) {
		$member['usernameenc'] = rawurlencode($member['username']);
		$member['regdate'] = gmdate($dateformat, $member['regdate'] + $timeoffset * 3600 );
		$member['site'] = str_replace('http://', '', $member['site']);
		$member['lastvisit'] = gmdate("$dateformat $timeformat", $member['lastvisit'] + ($timeoffset * 3600));
		$memberlist[] = $member;
	}

	include template('memberlist');

} elseif($action == 'markread') {

	if($discuz_user) {
		$db->query("UPDATE $table_members SET lastvisit='$timestamp' WHERE uid='$discuz_uid'");
	}

	showmessage('mark_read_succeed', 'index.php');

} elseif($action == 'emailverify') {

	$query = $db->query("SELECT identifying FROM $table_members WHERE uid='$discuz_uid' AND adminid='0' AND groupid='8'");
	if(!$member = $db->fetch_array($query)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	list($dateline,,) = explode("\t", $member['identifying']);
	if($timestamp - $dateline < 86400) {
		showmessage('email_verify_invalid');
	}

	$idstring = random(6);
	$db->query("UPDATE $table_members SET identifying='$timestamp\t2\t$idstring'");

	sendmail($email, 'email_verify_subject', 'email_verify_message');
	showmessage('email_verify_succeed');

} elseif($action == 'activate' && $uid && $id) {

	$query = $db->query("SELECT username, credit, identifying FROM $table_members WHERE uid='$uid' AND groupid='8'");
	$member = $db->fetch_array($query);

	list($dateline, $operation, $idstring) = explode("\t", $member['identifying']);

	if($operation == 2 && $idstring == $id) {
		$query = $db->query("SELECT groupid FROM $table_usergroups WHERE type='member' AND {$member['credit']}>=creditshigher AND {$member['credit']}<creditslower LIMIT 1");
		$db->query("UPDATE $table_members SET groupid='".$db->result($query, 0)."', identifying='' WHERE uid='$uid'");
		showmessage('activate_succeed', 'index.php');
	} else {
		showmessage('activate_illegal', NULL, 'HALTED');
	}

} elseif($action == 'lostpasswd') {

	$discuz_action = 141;

	if(!submitcheck('lostpwsubmit')) {
		include template('lostpasswd');
	} else {
		$secques = quescrypt($questionid, $answer);
		$query = $db->query("SELECT uid, username, adminid, email FROM $table_members WHERE username='$username' AND secques='$secques' AND email='$email'");
		if(!$member = $db->fetch_array($query)) {
			showmessage('getpasswd_account_notmatch', NULL, 'HALTED');
		} elseif($member['adminid'] == 1 || $member['adminid'] == 2) {
			showmessage('getpasswd_account_invalid', NULL, 'HALTED');
		}

		$idstring = random(6);
		$db->query("UPDATE $table_members SET identifying='$timestamp\t1\t$idstring' WHERE uid='{$member['uid']}'");

		sendmail($member['email'], 'get_passwd_subject', 'get_passwd_message');
		showmessage('getpasswd_send_succeed');
	}

} elseif($action == 'getpasswd' && $uid && $id) {

	$discuz_action = 141;

	$query = $db->query("SELECT username, identifying FROM $table_members WHERE uid='$uid'");
	$member = $db->fetch_array($query);

	list($dateline, $operation, $idstring) = explode("\t", $member['identifying']);

	if($dateline < $timestamp - 86400 * 3 || $operation != 1 || $idstring != $id) {
		showmessage('getpasswd_illegal', NULL, 'HALTED');
	}

	if(!submitcheck('getpwsubmit') || $newpasswd1 != $newpasswd2) {
		include template('getpasswd');
	} else {
		$password = dpassword_hash($newpasswd1);
		$query = $db->query("UPDATE $table_members SET password='$password', identifying='' WHERE uid='$uid'");
		showmessage('getpasswd_succeed');
	}	

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

?>