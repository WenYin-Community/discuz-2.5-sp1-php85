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

$discuz_action = 7;

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

if(!isset($action)) {

	$action = '';
	$query = $db->query("SELECT avatar, avatarwidth, avatarheight FROM $table_members WHERE uid='$discuz_uid'");
	$avatar = $db->fetch_array($query);

	$buddyonline = $buddyoffline = array();
	$query = $db->query("SELECT b.buddyid AS uid, m.username, s.username AS onlineuser, s.invisible FROM $table_buddys b
				LEFT JOIN $table_members m ON m.uid=b.buddyid
				LEFT JOIN $table_sessions s ON s.uid=m.uid
				WHERE b.uid='$discuz_uid'");
	while($buddy = $db->fetch_array($query)) {
		$buddyuser = array('uid' => $buddy['uid'], 'username' => ($buddy['username'] ? $buddy['username'] : 'User was Deleted'));
		if($buddy['onlineuser'] && !$buddy['invisible']) {
			$buddyonline[] = $buddyuser;
		} else {
			$buddyoffline[] = $buddyuser;
		}
	}

	$avatar = $avatar['avatar'] ? "<img src=\"{$avatar['avatar']}\" width=\"{$avatar['avatarwidth']}\" height=\"{$avatar['avatarheight']}\" border=\"0\">" : '&nbsp;';

	$msgexists = 0;
	$msglist = array();
	$query = $db->query("SELECT * FROM $table_pms WHERE msgtoid='$discuz_uid' AND folder='inbox' ORDER BY dateline DESC LIMIT 0, 5");
	while($message = $db->fetch_array($query)) {
		$msgexists = 1;
		$message['dateline'] = gmdate("$dateformat $timeformat", $message['dateline'] + $timeoffset * 3600);
		$message['subject'] = $message['new'] ? "<b>{$message['subject']}</b>" : $message['subject'];

		$msglist[] = $message;
	}

	$subsexists = $attachexists= 0;
	$subslist = $attlist = array();
	$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name FROM $table_subscriptions s, $table_threads t, $table_forums f WHERE t.tid=s.tid AND f.fid=t.fid AND s.uid='$discuz_uid' ORDER BY t.lastpost DESC LIMIT 0, 5");
	while($subs = $db->fetch_array($query)) {
		$subsexists = 1;
		$subs['lastposterenc'] = rawurlencode($subs['lastposter']);
		$subs['lastpost'] = gmdate("$dateformat $timeformat", $subs['lastpost'] + $timeoffset * 3600);

		$subslist[] = $subs;
	}

	$query = $db->query("SELECT sum(filesize) FROM $table_attachments where uid = '$discuz_uid'");
	$total_attach = $db->result($query, 0);

	if ($total_attach){
		require_once DISCUZ_ROOT.'./include/attachment.php';
		$total_attach = sizecount($db->result($query, 0));
		$query = $db->query("SELECT a.aid, a.filename, a.filesize, a.downloads, a.dateline, t.tid,  t.subject FROM $table_attachments a, $table_threads t WHERE t.tid=a.tid AND  a.uid='$discuz_uid' ORDER BY a.dateline DESC LIMIT 0, 5");
		while($download = $db->fetch_array($query)) {
			$attachexists = 1;
			$download['checkid'] = substr(md5($download['filesize']),0,5);
			$download['filesize'] = sizecount($download['filesize']);
			$download['dateline'] = gmdate("$dateformat $timeformat", $download['dateline'] + $timeoffset * 3600);
			$attlist[] = $download;
		}
	}
	include template('memcp_home');

} elseif($action == 'profile') {

	if(!submitcheck('editsubmit')) {

		$query = $db->query("SELECT * FROM $table_members WHERE uid='$discuz_uid'");
		$member = $db->fetch_array($query);

		$enctype = $allowavatar == 3 ? 'enctype="multipart/form-data"' : NULL;
		$invisiblechecked = $member['invisible'] ? 'checked="checked"' : NULL;
		$emailchecked = $member['showemail'] ? 'checked="checked"' : NULL;
		$newschecked = $member['newsletter'] ? 'checked="checked"' : NULL;
		$tppchecked = array($member['tpp'] => 'selected="selected"');
		$pppchecked = array($member['ppp'] => 'selected="selected"');
		$toselect = array();
		foreach(array('-12', '-11', '-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3.5', '-3', '-2', '-1', '0', '1', '2', '3', '3.5', '4', '4.5', '5', '5.5', '5.75', '6', '6.5', '7', '8', '9', '9.5', '10', '11', '12') as $tzk) {
			$toselect[$tzk] = '';
		}
		$toselect[strval((float)$member['timeoffset'])] = 'selected="selected"';

		if($member['gender'] == 1) {
			$checkmale = 'checked';
		} elseif($member['gender'] == 2) {
			$checkfemale = 'checked';
		} else {
			$checkunknown = 'checked';
		}

		$styleselect = '';
		$query = $db->query("SELECT styleid, name FROM $table_styles WHERE available='1'");
		while($style = $db->fetch_array($query)) {
			$styleselect .= "<option value=\"{$style['styleid']}\" ".
				($style['styleid'] == $member['styleid'] ? 'selected="selected"' : NULL).
				">{$style['name']}</option>\n";
		}

		$bday = explode('-', $member['bday']);
		$bday[0] = $bday[0] == '0000' ? '' : $bday[0];
		$month = array(intval($bday[1]) => "selected=\"selected\"");

		$dayselect = '';
		for($num = 1; $num <= 31; $num++) {
			$dayselect .= "<option value=\"$num\" ".($bday[2] == $num ? 'selected="selected"' : NULL).">$num</option>\n";
		}

		if(substr(trim($member['avatar']), 0, 14) == 'customavatars/' && !file_exists(DISCUZ_ROOT.'./'.$member['avatar'])) {
			$db->query("UPDATE $table_members SET avatar='' WHERE username='$discuz_user'");
			$member['avatar'] = '';
		}

		$member['dateformat'] = str_replace('n', 'mm', $member['dateformat']);
		$member['dateformat'] = str_replace('j', 'dd', $member['dateformat']);
		$member['dateformat'] = str_replace('y', 'yy', $member['dateformat']);
		$member['dateformat'] = str_replace('Y', 'yyyy', $member['dateformat']);
		$member['timeformat'] == 'H:i' ? $check24 = 'checked="checked"' : $check12 = 'checked="checked"';
		
		$imgcodeis = $allowsigimgcode ? 'On' : 'Off';
		$bbcodeis = $allowsigbbcode ? 'On' : 'Off';

		include template('memcp_profile');

	} else {

		require DISCUZ_ROOT.'./include/discuzcode.php';

		if($newpassword) {
			if(md5($oldpassword) != $discuz_pw) {
				showmessage('profile_passwd_wrong', NULL, 'HALTED');
			} elseif(!$newpassword || $newpassword != addslashes($newpassword)) {
				showmessage('profile_passwd_illegal');
			}
			$newpassword = md5($newpassword);
			$newpasswdadd = ", password='$newpassword'";
		} else {
			$newpasswdadd = '';
		}

		if ($questionidnew == -1 || (!$discuz_secques && $questionidnew == 0 && strlen($answernew) == 0)) {
			$secquesnew = $discuz_secques;
		} else {
			if(md5($oldpassword) != $discuz_pw) {
				showmessage('secques_change_passwd_wrong', NULL, 'HALTED');
			}
			$secquesnew = quescrypt($questionidnew, $answernew);
		}

		if(($adminid > 0) && !$secquesnew) {
			showmessage('profile_admin_security_invalid');
		}

		$emailchange = $sendusermail = $identifyingadd = '';
		if(!isemail($emailnew)) showmessage('profile_email_illegal');
		$query = $db->query("SELECT email FROM $table_members WHERE uid='$discuz_uid'");
		$emailold = $db->result($query, 0);
		if($emailnew != $emailold) {
			$emailchange = TRUE;
			if(md5($oldpassword) != $discuz_pw) {
				showmessage('mail_change_passwd_wrong', NULL, 'HALTED');
			}
			if(!$doublee) {
				$query = $db->query("SELECT uid FROM $table_members WHERE email='$emailnew' LIMIT 1");
				if($db->result($query, 0)) {
					showmessage('profile_email_duplicate');
				}
			}
			if($regverify == 1 && $adminid == 0 && $groupid > 7 ) {
				$idstring = random(6);
				$identifyingadd = ", groupid='8', identifying='$timestamp\t2\t$idstring'";
			}
		}


		$signew = censor($signew);
		if($maxsigsize && strlen($signew) > $maxsigsize) {
			showmessage('profile_sig_toolang');
		}

		if($allowavatar == 2 || $allowavatar == 3) {
			if($allowavatar == 3) {
				if(!((function_exists('is_uploaded_file') && !is_uploaded_file($customavatar)) || !($customavatar != 'none' && $customavatar && trim($customavatar_name)))) {
					$avatarext = strtolower(fileext($customavatar_name));
					if(!in_array($avatarext, array('gif', 'jpg', 'png'))) {
						showmessage('profile_avatar_invalid');
					}
					$avatarnew = 'customavatars/'.$discuz_uid.'.'.$avatarext;
					$avatartarget = DISCUZ_ROOT.'./'.$avatarnew;
					if($customavatar_size > 102400 && $customavatar_size > $maxattachsize) {
						showmessage('post_attachment_toobig');
					}
					if(!@copy($customavatar, $avatartarget)) {
						@move_uploaded_file($customavatar, $avatartarget);
					}
					$avatarimagesize = @getimagesize($avatartarget);
					if(!$avatarimagesize || ($maxavatarsize && @filesize($avatartarget) > $maxavatarsize)) {
						@unlink($avatartarget);
						showmessage($avatarimagesize ? 'profile_avatar_toobig' : 'profile_avatar_invalid');
					}
				}
			}
			$avatarnew = dhtmlspecialchars(trim($avatarnew));
			$avatarext = strtolower(fileext($avatarnew));

			if($avatarnew) {
				if(!preg_match("/^((customavatars\/\d+\.[a-z]+)|(images\/avatars\/.+?)|(http:\/\/.+?))$/i", $avatarnew)
					|| !in_array($avatarext, array('gif', 'jpg', 'png'))) {
					showmessage('profile_avatar_invalid');
				}
				if($avatarwidthnew == '*' || $avatarheightnew == '*') {
					$avatarwidthnew = $avatarheightnew = round(2 * $maxavatarpixel / 3);
					@list($avatarwidthnew, $avatarheightnew) = $avatarimagesize ? $avatarimagesize : getimagesize($avatarnew);
				}
				
				if (!$avatarwidthnew || !$avatarheightnew) $avatarwidthnew = $avatarheightnew = 80; 
				$maxsize = max($avatarwidthnew, $avatarheightnew);
				if($maxsize > $maxavatarpixel) {
					$avatarwidthnew = $avatarwidthnew * $maxavatarpixel / $maxsize;
					$avatarheightnew = $avatarheightnew * $maxavatarpixel / $maxsize;
				}
			}
			$avataradd = ", avatar='$avatarnew', avatarwidth='$avatarwidthnew', avatarheight='$avatarheightnew'";
		} else {
			$avataradd = '';
		}

		if(!is_numeric($icqnew) || strlen($icqnew) < 5 || strlen($icqnew) > 15) {
			$icqnew = '';
		}

		$yahoonew = dhtmlspecialchars($yahoonew);
		$msnnew   = isemail($msnnew)? $msnnew : '';
		$oicqnew  = intval($oicqnew) ? intval($oicqnew) : '';
		$icqnew   = intval($icqnew) > 5000 && strlen($icqnew) < 16 ?intval($icqnew) : '';
		$timeformatnew = $timeformatnew == '12' ? 'h:i A' : 'H:i';

		$tppnew = intval($tppnew);
		$pppnew = intval($pppnew);
		$tppnew = ($tppnew > 30 || $tppnew < 0) ? 0 : $tppnew;
		$pppnew = ($pppnew > 15 || $pppnew < 0) ? 0 : $pppnew;

		$sitenew = trim(str_replace('http://', '', $sitenew));
		$sitenew = $sitenew ? dhtmlspecialchars('http://'.$sitenew) : '';

		$bionew = cutstr(censor(dhtmlspecialchars($bionew)),800);
		
		$year=intval($year);$month=intval($month);$day=intval($day);
		$bdaynew = ($month && $day && $year) ? dhtmlspecialchars("$year-$month-$day") : '';

		$dateformatnew = str_replace('mm', 'n', $dateformatnew);
		$dateformatnew = str_replace('dd', 'j', $dateformatnew);
		$dateformatnew = str_replace('yyyy', 'Y', $dateformatnew);
		$dateformatnew = str_replace('yy', 'y', $dateformatnew);

		$invisiblenew = $allowinvisible && $invisiblenew ? 1 : 0;

		$locationnew  = cutstr(censor(dhtmlspecialchars($locationnew)), 28);
		$cstatusadd   = $allowcstatus ? ", customstatus='".cutstr(censor(dhtmlspecialchars($cstatusnew)), 28)."'" : '';


		$db->query("UPDATE $table_members SET secques='$secquesnew', gender='$gendernew', email='$emailnew', site='$sitenew', oicq='$oicqnew',
			location='$locationnew', bio='$bionew', signature='$signew', showemail='$showemailnew', timeoffset='$timeoffsetnew',
			icq='$icqnew', yahoo='$yahoonew', styleid='$styleidnew', bday='$bdaynew', tpp='$tppnew', ppp='$pppnew',
			newsletter='$newsletternew', invisible='$invisiblenew', timeformat='$timeformatnew', msn='$msnnew',
			dateformat='$dateformatnew' $avataradd $cstatusadd $newpasswdadd $identifyingadd WHERE uid='$discuz_uid'");

		$styleid = $styleidnew;

		if ($emailchange){
			$db->query("DELETE from $table_subscriptions where uid='$discuz_uid'");
		}

		if($identifyingadd) {
			sendmail($emailnew, 'email_verify_subject', 'email_verify_message');
			showmessage('profile_email_verify');
		} else {
			showmessage('profile_succeed', 'memcp.php');
		}
	}

} elseif($action == 'favorites') {

	if($favadd && !submitcheck('favsubmit')) {

		$query = $db->query("SELECT tid FROM $table_favorites WHERE tid='$favadd' AND uid='$discuz_uid' LIMIT 1");
		if($db->result($query, 0)) {
			showmessage('favorite_exists'); 
		} else {
			$db->query("INSERT INTO $table_favorites (uid, tid)
				VALUES ('$discuz_uid', '$favadd')");
			showmessage('favorite_add_succeed', dreferer());
		}

	} elseif(!$favadd && !submitcheck('favsubmit')) {

		$favlist = array();
		$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name FROM $table_favorites fav, $table_threads t, $table_forums f WHERE fav.tid=t.tid AND fav.uid='$discuz_uid' AND t.fid=f.fid ORDER BY t.lastpost DESC");
		while($fav = $db->fetch_array($query)) {
			$fav['lastposterenc'] = rawurlencode($fav['lastposter']);
			$fav['lastpost'] = gmdate("$dateformat $timeformat", $fav['lastpost'] + $timeoffset * 3600);
			$favlist[] = $fav;
		}

		include template('memcp_misc');

	} elseif(!$favadd && submitcheck('favsubmit')) {

		if ($ids = implode_ids($delete)){
			$db->query("DELETE FROM $table_favorites WHERE uid='$discuz_uid' AND tid IN ($ids)");
		}
		showmessage('favorite_update_succeed', dreferer());
	}

} elseif($action == 'subscriptions') {

	if($subadd && !submitcheck('subsubmit')) {

		$query = $db->query("SELECT tid FROM $table_subscriptions WHERE tid='$subadd' AND uid='$discuz_uid' LIMIT 1");
		if($db->result($query, 0)) {
			showmessage('subscription_exists');
		} else {
			$db->query("INSERT INTO $table_subscriptions (uid, email, tid, lastnotify)
				VALUES ('$discuz_uid', '$email', '$subadd', '')");
			showmessage('subscription_add_succeed', dreferer());
		}

	} elseif(!$subadd && !submitcheck('subsubmit')) {

		$subslist = array();
		$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name FROM $table_subscriptions s, $table_threads t, $table_forums f WHERE t.tid=s.tid AND f.fid=t.fid AND s.uid='$discuz_uid' ORDER BY t.lastpost DESC");
		while($subs = $db->fetch_array($query)) {
			$subs['lastposterenc'] = rawurlencode($subs['lastposter']);
			$subs['lastpost'] = gmdate("$dateformat $timeformat", $subs['lastpost'] + $timeoffset * 3600);
			$subslist[] = $subs;
		}

		include template('memcp_misc');

	} elseif(!$subadd && submitcheck('subsubmit')) {

		if($ids = implode_ids($delete)){
			$db->query("DELETE FROM $table_subscriptions WHERE uid='$discuz_uid' AND tid IN ($ids)");
		}
		showmessage('subscription_update_succeed', dreferer());
	}

} elseif($action == 'viewavatars') {

	if(!$allowavatar) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!submitcheck('avasubmit')) {

		$app = 16;
		$avatarsdir = DISCUZ_ROOT.'./images/avatars';
		if(!$page) {
			$page = 1;
		}

		$query = $db->query("SELECT avatar FROM $table_members WHERE uid='$discuz_uid'");
		$member = $db->fetch_array($query);
		$avatarlist = '';
		$num = 0;
		if(is_dir($avatarsdir)) {
			$adir = dir($avatarsdir);
			while($entry = $adir->read()) {
				if(in_array(strtolower(fileext($entry)), array('gif', 'jpg', 'png')) && is_file("$avatarsdir/$entry")) {
					$avatars[++$num] = $entry;
				}
			}
			$adir->close();
		} else {
			showmessage('profile_avatardir_nonexistence');
		}

		$start = ($page - 1) * $app;
		$end = ($start + $app > $num) ? ($num) : ($start + $app - 1);

		$multipage = multi($num, $app, $page, 'memcp.php?action=viewavatars');
		for($i = $start; $i <= $end; $i += 4) {
			$avatarlist .= "<tr>\n";
			for($j = 0; $j < 4; $j++) {
				$thisbg = ($thisbg == ALTBG1) ? ALTBG2 : ALTBG1;
				$avatarlist .= "<td bgcolor=\"$thisbg\" width=\"25%\" align=\"center\">";
				if($avatars[$i + $j] && ($i + $j)) {
					$avatarlist .= "<img src=\"images/avatars/".$avatars[$i + $j]."\"></td>\n";
				} else {
					$avatarlist .= "&nbsp;</td>\n";
				}
			}
			$avatarlist .= "</tr><tr>\n";
			for($j = 0; $j < 4; $j++) {
				$avatarlist .= "<td bgcolor=\"$thisbg\" width=\"25%\" align=\"center\">";
				if($avatars[$i + $j] && ($i + $j)) {
					if(strpos($member['avatar'], $avatars[$i + $j])) {
						$checked = 'checked';
					} else {
						$checked = '';
					}
					$avatarlist .= "<input type=\"radio\" value=\"images/avatars/".$avatars[$i + $j]."\" name=\"avatarnew\" $checked>".$avatars[$i + $j]."\n";
				} elseif($i + $j == 0) {
					if(!$member['avatar']) {
						$checked = 'checked';
					}
					$avatarlist .= "<input type=\"radio\" value=\"\" name=\"avatarnew\" $checked><span class=\"bold\">None</span>\n";
				} else {
					$avatarlist .= "&nbsp;</td>\n";
				}
				$thisbg = ($thisbg == ALTBG1) ? ALTBG2 : ALTBG1;
			}
			$avatarlist .= "</tr><tr><td bgcolor=\"".ALTBG1."\" colspan=\"4\" height=\"1\"></td></tr>\n\n";
		}

		include template('memcp_misc');

	} else {

		@list($avatarwidthnew, $avatarheightnew) = getimagesize($avatarnew);
		
		if (!$avatarwidthnew || !$avatarheightnew) $avatarwidthnew = $avatarheightnew = 80; 
		$maxsize = max($avatarwidthnew, $avatarheightnew);
		if($maxsize > $maxavatarpixel) {
			$avatarwidthnew = $avatarwidthnew * $maxavatarpixel / $maxsize;
			$avatarheightnew = $avatarheightnew * $maxavatarpixel / $maxsize;
		}
		$db->query("UPDATE $table_members SET avatar='$avatarnew', avatarwidth='$avatarwidthnew', avatarheight='$avatarheightnew' WHERE uid='$discuz_uid'");
		showmessage('profile_avatar_succeed', 'memcp.php?action=profile');

	}

} elseif($action == 'buddylist') {

	if(empty($delete)) {
		$query = $db->query("SELECT uid FROM $table_members WHERE ".(isset($buddyid) ? "uid='$buddyid'" : "username='$buddy'"));
		$buddyid = $db->result($query, 0);
		if(!$buddyid) {
			showmessage('buddy_add_nonexistence');
		}

		$query = $db->query("SELECT buddyid FROM $table_buddys WHERE uid='$discuz_uid' AND buddyid='$buddyid' LIMIT 1");
		if($db->result($query, 0)) {
			showmessage('buddy_add_invalid');
		}

		$db->query("INSERT INTO $table_buddys VALUES ('$discuz_uid', '$buddyid')");
		showmessage('buddy_update_succeed', 'memcp.php');
	} else {
		$db->query("DELETE FROM $table_buddys WHERE uid='$discuz_uid' AND buddyid='$delete'");
		showmessage('buddy_update_succeed', 'memcp.php');
	}

}

?>