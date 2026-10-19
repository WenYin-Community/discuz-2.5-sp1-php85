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
1 PHP5.X 环境下无法发短消息
2 转发的问题
3 可删除对方已读pm
*/

define('CURRSCRIPT',  'pm');

require './include/common.php';
require DISCUZ_ROOT.'./include/discuzcode.php';

$discuz_action = 101;

$navtitle = '';

if(empty($discuz_uid)) {
	showmessage('not_loggedin', NULL, 'HALTED');
} elseif($maxpmnum == 0) {
	showmessage('group_nopermission', NULL, 'HALTED');
}

$query = $db->query("SELECT COUNT(*) FROM $table_pms WHERE msgfromid='$discuz_uid' AND folder='outbox'");
$pm_outbox = $db->result($query, 0);

$query = $db->query("SELECT COUNT(*) FROM $table_pms WHERE msgtoid='$discuz_uid' AND folder='inbox'");
$pm_inbox = $db->result($query, 0);

$pm_total = $pm_outbox + $pm_inbox ;

@$storage_percent = round((100 * $pm_total / $maxpmnum) + 1).'%';

if(empty($action)) {

	$page = intval($page) ? intval($page) : 1;
	$start_limit = ($page - 1) * $tpp;

	if($folder == 'outbox') {
		$pmnum = $pm_outbox;
		$query = $db->query("SELECT p.*, m.username AS msgto FROM $table_pms p
					LEFT JOIN $table_members m ON m.uid=p.msgtoid
					WHERE msgfromid='$discuz_uid' AND folder='outbox'
					ORDER BY p.dateline DESC LIMIT $start_limit, $tpp");
	} elseif($folder == 'track') {
		$query = $db->query("SELECT COUNT(*) FROM $table_pms WHERE msgfromid='$discuz_uid' AND folder='inbox'");
		$pmnum = $db->result($query, 0);
		$query = $db->query("SELECT p.*, m.username AS msgto FROM $table_pms p
					LEFT JOIN $table_members m ON m.uid=p.msgtoid
					WHERE msgfromid='$discuz_uid' AND folder='inbox'
					ORDER BY p.dateline DESC LIMIT $start_limit, $tpp");
	} else {
		$folder = 'inbox';
		$pmnum = $pm_inbox;
		$query = $db->query("SELECT * FROM $table_pms WHERE msgtoid='$discuz_uid' AND folder='inbox' ORDER BY dateline DESC LIMIT $start_limit, $tpp");
	}

	$multipage = multi($pmnum, $tpp, $page, "pm.php?folder=$folder");

	$pmlist = array();
	while($pm = $db->fetch_array($query)) {
		$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
		$pm['subject'] = $pm['new'] ? "<b>{$pm['subject']}</b>" : $pm['subject'];
		$pmlist[] = $pm;
	}

} elseif($action == 'view') {

	if($pm_total > $maxpmnum) {
		showmessage('pm_box_isfull', 'pm.php');
	}
	$discuz_action = 102;
	$codecount = 0;

	$query = $db->query("SELECT p.*, m.username AS msgto FROM $table_pms p
				LEFT JOIN $table_members m ON m.uid=p.msgtoid
				WHERE pmid='$pmid' AND (msgtoid='$discuz_uid' OR msgfromid='$discuz_uid')");
	if(!$pm = $db->fetch_array($query)) {
		showmessage('pm_nonexistence');
	}

	if($pm['new'] && !($pm['msgfromid'] == $discuz_uid && $pm['msgtoid'] != $discuz_uid && $pm['folder'] == 'inbox')) {
		$db->query("UPDATE $table_pms SET new='0' WHERE pmid='$pmid'");
	}

	$folder = $folder == 'track' ? $folder : $pm['folder'];

	$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
	$pm['message'] = postify($pm['message'], 0, 0, 1, 0, 1, 1);

} elseif($action == 'send') {

	if($pm_total > $maxpmnum) {
		showmessage('pm_box_isfull', 'pm.php');
	}

	if(!submitcheck('pmsubmit')) {
		$discuz_action = 103;
		$buddylist = array();
		$query = $db->query("SELECT b.buddyid, m.username AS buddyname FROM $table_buddys b
					LEFT JOIN $table_members m ON m.uid=b.buddyid
					WHERE b.uid='$discuz_uid'");
		while($buddy = $db->fetch_array($query)) {
			$buddylist[] = $buddy;
		}

		$subject = $message = '';
		if($pmid) {

			$query = $db->query("SELECT * FROM $table_pms WHERE pmid='$pmid' AND (msgtoid='$discuz_uid' OR msgfromid='$discuz_uid')");
			$pm = $db->fetch_array($query);

			$pm['subject'] = $message = preg_replace("/^(Re:|Fw:)\s*/", "", $pm['subject']);
			$username = $pm['msgfrom'];

			if($do == 'reply') {
				$discuz_action = 104;
				$subject = "Re: {$pm['subject']}";
				$message = '[quote]'.dhtmlspecialchars(trim(preg_replace("/(\[quote])(.*)(\[\/quote])/siU", '', $pm['message']))).'[/quote]'."\n";
				$touser = $pm['msgfrom'];
			} elseif($do == 'forward') {
				$discuz_action = 105;
				$subject = "Fw: {$pm['subject']}";
				$message = '[quote]'.dhtmlspecialchars($pm['message']).'[/quote]'."\n";
				$touser = $pm['msgfrom'];
			}

		} elseif($uid) {

			$query = $db->query("SELECT username FROM $table_members WHERE uid='$uid'");
			$touser = dhtmlspecialchars($db->result($query, 0));

		} else {
			$touser = dhtmlspecialchars($touser);
		}

	} else {
		$discuz_action = 106;

		$floodctrl = $floodctrl * 2;
		if($floodctrl && !$disablepostctrl && $timestamp - $lastpost < $floodctrl) {
			showmessage('pm_flood_ctrl');
		}

		$subject = cutstr(dhtmlspecialchars(censor($subject)),73);
		$message = trim(censor(parseurl($message)));
		if (strlen($message) > 10000 || strlen($message) < 10 ){
				showmessage('pm_message_toolong');
		}

		if(empty($msgto)) {
			$msgto = is_array($msgtobuddys) ? $msgtobuddys : array();
		} else {
			$msgtoid = array();
			$query = $db->query("SELECT uid, username FROM $table_members WHERE username='$msgto'");
			while($member = $db->fetch_array($query)) {
				if(addslashes($member['username']) == $msgto) {
					$msgtoid[] = $member['uid'];
					break;
				}
			}

			if(!$msgtoid) {
				showmessage('pm_send_nonexistence');
			}


			if (is_array($msgtobuddys) && count($msgtobuddys)){
			    $msgto = array_merge($msgtoid, $msgtobuddys);
			}else{
			    $msgto = $msgtoid;
			} 

		}

		$msgto_count = count($msgto);
		$maxpmsend = ceil($maxpmnum / 10);
		if($msgto_count > $maxpmsend) {
			showmessage('pm_send_toomany');
		}
		if(!$msgto_count || !$subject) {
			showmessage('pm_send_invalid');
		}

		$uids = implode_ids( $msgto );

		$ignorenum = 0;
		$query = $db->query("SELECT username, ignorepm FROM $table_members WHERE uid IN ($uids)");
		if($db->num_rows($query) != $msgto_count) {
			showmessage('undefined_action');
		}
		while($member = $db->fetch_array($query)) {
			if(preg_match("/(,|^)\s*".preg_quote($discuz_user, '/')."\s*(,|$)/i", $member['ignorepm'])) {
				showmessage('pm_send_ignore');
			}
		}
		

		foreach($msgto as $uid) {
			$db->query("INSERT INTO $table_pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$uid', 'inbox', '1', '$subject', '$timestamp', '$message')");
		}
		$db->query("UPDATE LOW_PRIORITY $table_members SET newpm='1' WHERE uid IN ($uids)", 'UNBUFFERED');

		if($floodctrl) {
			$db->query("UPDATE $table_members SET lastpost='$timestamp' WHERE uid='$discuz_uid'");
		}

		if($saveoutbox) {
			$db->query("INSERT INTO $table_pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$msgto[0]', 'outbox', '1', '$subject', '$timestamp', '$message')");
		}
		showmessage('pm_send_succeed', 'pm.php');
	}

} elseif($action == 'delete') {

	$msg_field = $folder == 'inbox' ? 'msgtoid' : 'msgfromid';
	$checkadd = $folder == 'track' ? " AND new='1'" : '';
	if(!$pmid) {
		if($pmids = implode_ids( $delete )) {
			$db->query("DELETE FROM $table_pms WHERE $msg_field='$discuz_uid' AND pmid IN ($pmids) $checkadd");
		}
	} else {
		$db->query("DELETE FROM $table_pms WHERE $msg_field='$discuz_uid' AND pmid='$pmid' $checkadd");
	}

	showmessage('pm_delete_succeed', "pm.php?folder=$folder");

} elseif($action == 'download' && !empty($pmid)) {
	$discuz_action = 107;

	$query = $db->query("SELECT * FROM $table_pms WHERE pmid='$pmid' AND (msgtoid='$discuz_uid' OR msgfromid='$discuz_uid')");
	if(!$pm = $db->fetch_array($query)) {
		showmessage('pm_nonexistence');
	}
	$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
	$query = $db->query("SELECT username FROM $table_members WHERE uid='{$pm['msgtoid']}'");
	$pm['msgto'] = dhtmlspecialchars($db->result($query, 0));
	$export = "Discuz! Private Message Export".PHP_NEXTLINE.
		"Date:\t\t{$pm['dateline']}".PHP_NEXTLINE.
		"From:\t\t{$pm['msgfrom']}".PHP_NEXTLINE.
		"To:\t\t{$pm['msgto']}".PHP_NEXTLINE.
		"Subject:\t{$pm['subject']}".PHP_NEXTLINE.PHP_NEXTLINE.
		"{$pm['message']}".PHP_NEXTLINE.PHP_NEXTLINE.PHP_NEXTLINE.
		"Welcome to $bbname ($boardurl)";

	ob_end_clean();
	header('Content-Encoding: none');
	header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
	header('Content-Disposition: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ').'filename="pm_'.$discuz_user.'_'.$pmid.'.txt"');
	header('Content-Length: '.strlen($export));
	header('Pragma: no-cache');
	header('Expires: 0');

	echo $export;
	dexit();

} elseif($action == 'ignore') {

	if(!submitcheck('ignoresubmit')) {
		$query = $db->query("SELECT ignorepm FROM $table_members WHERE uid='$discuz_uid'");
		$ignorepm = $db->result($query, 0);
	} else {
		$db->query("UPDATE $table_members SET ignorepm='$ignorelist' WHERE uid='$discuz_uid'");
		showmessage('pm_ignore_succeed', 'pm.php');
	}

}

include template('pm');

?>