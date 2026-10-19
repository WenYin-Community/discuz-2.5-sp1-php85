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
1 用户名限制的问题
*/
require './include/common.php';
// PHP 8: 注册表单的可选字段未提交时预置为空值(与原逻辑的假值一致)
foreach(array('questionid', 'answer', 'dateformatnew', 'timeformatnew', 'tppnew', 'pppnew', 'year', 'month',
	'day', 'msn', 'oicq', 'icq', 'yahoo', 'site', 'bio', 'locationnew', 'gendernew', 'showemail',
	'newsletter', 'styleidnew', 'timeoffsetnew') as $regvar) {
	if(!isset($$regvar)) {
		$$regvar = '';
	}
}
unset($regvar);

$discuz_action = 5;

if($discuz_uid) {
	showmessage('login_succeed', 'index.php');
}

if(!$regstatus) {
	showmessage('register_disable');
}

$query = $db->query("SELECT * FROM $table_settings WHERE variable IN ('censoruser', 'doublee', 'bbrules', 'bbrulestxt', 'welcomemsg', 'welcomemsgtxt')");
while($setting = $db->fetch_array($query)) {
	${$setting['variable']} = $setting['value'];
}

$query = $db->query("SELECT groupid, allowcstatus, allowavatar, allowsigbbcode, allowsigimgcode, maxsigsize FROM $table_usergroups WHERE ".($regverify == 1 ? "groupid='8'" : "creditshigher<=0 AND 0<creditslower LIMIT 1"));
$groupinfo = $db->fetch_array($query);
if(!submitcheck('regsubmit')) {

	if($bbrules && !submitcheck('rulesubmit')) {
		$bbrulestxt = nl2br("\n$bbrulestxt\n\n");
	} else {

		$enctype = NULL;

		$styleselect = $dayselect = '';
		$query = $db->query("SELECT styleid, name FROM $table_styles WHERE available='1'");
		while($styleinfo = $db->fetch_array($query)) {
			$styleselect .= '<option value="'.$styleinfo['styleid'].'">'.$styleinfo['name'].'</option>'."\n";
		}

		for($num = 1; $num <= 31; $num++) {
			$dayselect .= '<option value="'.$num.'">'.$num.'</option>';
		}

		$bbcodeis = $groupinfo['allowsigbbcode'] ? 'On' : 'Off';
		$imgcodeis = $groupinfo['allowsigimgcode'] ? 'On' : 'Off';
		$check24 = $check12 = '';
		$timeformat == 'H:i' ? $check24 = 'checked' : $check12 = 'checked';
		$toselect = array();
		foreach(array('-12','-11','-10','-9','-8','-7','-6','-5','-4','-3.5','-3','-2','-1','0','1','2','3','3.5','4','4.5','5','5.5','5.75','6','6.5','7','8','9','9.5','10','11','12') as $tzk) {
			$toselect[$tzk] = '';
		}
		$toselect[(float)strval($_DCACHE['settings']['timeoffset'])] = 'selected="selected"';

		$dateformatorig = $dateformat;
		$dateformatorig = str_replace('n', 'mm', $dateformatorig);
		$dateformatorig = str_replace('j', 'dd', $dateformatorig);
		$dateformatorig = str_replace('y', 'yy', $dateformatorig);
		$dateformatorig = str_replace('Y', 'yyyy', $dateformatorig);

	}

	include template('register');

} else {

	require DISCUZ_ROOT.'./include/discuzcode.php';

	$email = trim($email);
	$username = trim($username);

	if(strlen($username) > 15) {
		showmessage('profile_username_toolang');
	}

	if($password != $password2) {
		showmessage('profile_passwd_notmatch');
	}


	if(preg_match("/^\s*$|^c:\\con\\con$||[%,\*\"\s\t\<\>\&]|^游客|^Guest/is", $username) || @preg_match('/^'.str_replace(array('\\*', ',', ' '), array('.*', '|', ''), preg_quote($censoruser, '/')).'$/i', $username)) {
		showmessage('profile_username_illegal');
	}

	if(!$password || $password != addslashes($password)) {
		showmessage('profile_passwd_illegal');
	}

	if(!isemail($email)) {
		showmessage('profile_email_illegal');
	}

	$emailadd = !$doublee ? "OR email='$email'" : '';
	$regctrladd = $regctrl ? "OR (regip='$onlineip' AND regdate>$timestamp-'$regctrl'*3600)" : '';
	$query = $db->query("SELECT username, email, regip FROM $table_members WHERE username='$username' $emailadd $regctrladd LIMIT 1");
	if($member = $db->fetch_array($query)) {
		if(addslashes($member['email']) == $email) {
			showmessage('profile_email_duplicate');
		} elseif($regctrl && $member['regip'] == $onlineip) {
			showmessage('register_ctrl');
		} else {
			showmessage('profile_username_duplicate');
		}
	}

	$password = md5($password);
	$secques = quescrypt($questionid, $answer);

	$dateformatnew = str_replace('mm', 'n', $dateformatnew);
	$dateformatnew = str_replace('dd', 'j', $dateformatnew);
	$dateformatnew = str_replace('yyyy', 'Y', $dateformatnew);
	$dateformatnew = str_replace('yy', 'y', $dateformatnew);
	$timeformatnew = $timeformatnew == '24' ? 'H:i' : 'h:i A';

	$tppnew = intval($tppnew);
	$pppnew = intval($pppnew);
	$tppnew = ($tppnew > 30 || $tppnew < 0) ? 0 : $tppnew;
	$pppnew = ($pppnew > 15 || $pppnew < 0) ? 0 : $pppnew;

	$year=intval($year);$month=intval($month);$day=intval($day);
	$bday = ($month && $day && $year) ? "$year-$month-$day" : '';

	$msn = isemail($msn)? $msn : '';
	$oicq = intval($oicq) ? intval($oicq) : '';
	$icq = intval($icq) > 5000 && strlen($icq) < 16 ?intval($icq) : '';
	$yahoo = dhtmlspecialchars($yahoo);

	$site = trim(str_replace('http://', '', $site));
	$site = $site ? dhtmlspecialchars('http://'.$site) : '';

	$bio = cutstr(censor(dhtmlspecialchars($bio)),800);
	$locationnew = cutstr(censor(dhtmlspecialchars($locationnew)), 28);

	$idstring = random(6);
	$identifyingnew = $regverify == 1 ? "$timestamp\t2\t$idstring" : '';

	$db->query("INSERT INTO $table_members (username, password, secques, gender, adminid, groupid, regip, regdate, lastvisit, lastactivity, postnum, credit, email, site, icq, oicq, yahoo, msn, location, bday, bio, signature, customstatus, tpp, ppp, styleid, dateformat, timeformat, showemail, newsletter, invisible, timeoffset, identifying )
		VALUES ('$username', '$password', '$secques', '$gendernew', '0', '{$groupinfo['groupid']}', '$onlineip', '$timestamp', '$timestamp', '$timestamp', '0', '0', '$email', '$site', '$icq', '$oicq', '$yahoo', '$msn', '$locationnew', '$bday', '$bio', '', '', '$tppnew', '$pppnew', '$styleidnew', '$dateformatnew', '$timeformatnew', '$showemail', '$newsletter', '0', '$timeoffsetnew', '$identifyingnew' )");
	$uid = $db->insert_id();

	if($welcomemsg && !empty($welcomemsgtxt)) {
		$welcomtitle = "Welcome to $bbname!";
		$welcomemsgtxt = addslashes($welcomemsgtxt);
		$db->query("INSERT INTO $table_pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
			VALUES ('System Message', '0', '$uid', 'inbox', '1', '$welcomtitle', '$timestamp','$welcomemsgtxt')");
		$db->query("UPDATE $table_members SET newpm='1' WHERE uid='$uid'");
	}

	require DISCUZ_ROOT.'./include/cache.php';

	updatecache('settings');

	$discuz_uid = $uid;
	$discuz_user = $username;
	$discuz_userss = stripslashes($discuz_user);
	$discuz_pw = $password;
	$discuz_secques = $secques;
	$groupid = $groupinfo['groupid'];
	$styleid = $styleid ? $styleid : $_DCACHE['settings']['styleid'];

	setcookie('cookietime', 2592000, $timestamp + 86400 * 365, $cookiepath, $cookiedomain);
	setcookie('_discuz_uid', $discuz_uid, $timestamp + 2592000, $cookiepath, $cookiedomain, false, true);
	setcookie('_discuz_pw', $discuz_pw, $timestamp + 2592000, $cookiepath, $cookiedomain, false, true);
	setcookie('_discuz_secques', $discuz_secques, $timestamp + 2592000, $cookiepath, $cookiedomain, false, true);
	if($regverify == 1){
		sendmail($email, 'email_verify_subject', 'email_verify_message');
		showmessage('profile_email_verify');
	} else {
		showmessage('register_succeed', dreferer());
	}

}

?>