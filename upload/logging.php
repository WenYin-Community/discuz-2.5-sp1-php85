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
1 在线列表重复的问题
*/

define('CURRSCRIPT', 'logging');
require './include/common.php';

$action = isset($action) ? $action : '';

if($action == 'logout') {

	if($adminid <>0){
		$query = $db->query("DELETE FROM $table_adminsessions WHERE uid='$discuz_uid' OR  dateline<$timestamp-3600", 'SILENT');
	}

	clearcookies();
	$styleid = $_DCACHE['settings']['styleid'];
	showmessage('logout_succeed', dreferer());

} elseif($action == 'login') {

	if(!submitcheck('loginsubmit', 1)) {

		$discuz_action = 6;

		$referer = dreferer();
		$styleselect = '';
		$query = $db->query("SELECT styleid, name FROM $table_styles WHERE available='1'");
		while($styleinfo = $db->fetch_array($query)) {
			$styleselect .= "<option value=\"{$styleinfo['styleid']}\">{$styleinfo['name']}</option>\n";
		}

		$year_checked = $day_checked = $hour_checked = $task_checked = $month_checked = '';
		switch(isset($_COOKIE['_cookietime']) ? ($_COOKIE['_cookietime'] ?? '') : '') {
			case '31536000': $year_checked = 'checked'; break;
			case '86400': $day_checked = 'checked'; break;
			case '3600': $hour_checked = 'checked'; break;
			case '0': $task_checked = 'checked'; break;
			default: $month_checked = 'checked';
		}

		include template('login');

	} else {

		$discuz_uid = $adminid = 0;
		$discuz_user = $discuz_pw = $discuz_secques = '';

		$loginperm = logincheck();
		if(!$loginperm) {
			showmessage('login_strike');
		}

		$secques = ($questionid && $answer) ? quescrypt($questionid, $answer): '';
		$passwordhash = md5($password);
		$errorlog = "$timestamp\t$username\t".substr($passwordhash, 0, 2);
		for($i = 3; $i < strlen($passwordhash); $i++) {
			$errorlog .= '*';
		}
		$errorlog .= substr($passwordhash, -1)."\t".($secques ? "Ques #$questionid" : '')."\t$onlineip\n";

		$query = $db->query("SELECT m.uid AS discuz_uid, m.username AS discuz_user, m.password AS discuz_pw, m.adminid, m.groupid, m.credit, m.secques, m.styleid AS styleidmem, m.lastvisit, m.lastpost, u.type as usertype, u.creditshigher, u.creditslower, u.allowinvisible
					FROM $table_members m LEFT JOIN $table_usergroups u USING (groupid)
					WHERE username='$username'");
		$memberrow = $db->fetch_array($query);
		if(is_array($memberrow) && dpassword_verify($password, $memberrow['discuz_pw']) && $memberrow['secques'] == $secques) {
			// 历史 md5 或旧算法: 登录成功后自动升级为 password_hash
			if(dpassword_needs_rehash($memberrow['discuz_pw'])) {
				$newhash = dpassword_hash($password);
				$db->query("UPDATE $table_members SET password='$newhash' WHERE uid='$memberrow[discuz_uid]'");
				$memberrow['discuz_pw'] = $newhash;
			}
			// 登录必须覆盖已有的同名全局变量(adminid/groupid/discuz_uid 等), 因此用 EXTR_OVERWRITE
			extract($memberrow, EXTR_OVERWRITE);
		} else {
			// 登录失败: 预置为空值, 后续分支按未登录处理
			$discuz_uid = $discuz_user = $discuz_pw = $adminid = $groupid = $credit = $loginperm = $usertype = '';
		}

		if($bbclosed && $adminid != 1) {
			showmessage($closedreason ? $closedreason : 'board_closed');
		}

		loginrecord($discuz_uid, $loginperm);

		if($discuz_uid) {
			$groupidadd = '';
			if($discuz_uid && $usertype == 'member' && ($credit < $creditshigher || $credit > $creditslower)) {
				$query = $db->query("SELECT groupid FROM $table_usergroups WHERE type='member' AND $credit>=creditshigher AND $credit<creditslower");
				$groupid_new = $db->result($query, 0);

				if($groupid_new && $groupid != $groupid_new) {
					$groupid = $groupid_new;
					$groupidadd =", groupid='$groupid_new'";
				}
			}
			$discuz_userss = $discuz_user;
			$discuz_user = addslashes($discuz_user);

			if($allowinvisible && $loginmode == 'invisible') {
				$invisibleadd = ', invisible=\'1\'';
			} elseif($loginmode == 'normal') {
				$invisibleadd = ', invisible=\'0\'';
			} else {
				$invisibleadd = '';
			}

			$styleid = empty($_POST['styleid']) ? ($styleidmem ? $styleidmem :
					$_DCACHE['settings']['styleid']) : ($_POST['styleid'] ?? '');
			
			$_cookietime = isset($_POST['cookietime']) ? ($_POST['cookietime'] ?? '') :
					(!empty($_COOKIE['_cookietime']) ? ($_COOKIE['_cookietime'] ?? '') : 0);
			$cookietime = empty($_cookietime) ? 0 : $timestamp + $_cookietime;

			setcookie('_cookietime', $_cookietime, $timestamp + 31536000, $cookiepath, $cookiedomain);
			setcookie('_discuz_uid', $discuz_uid, $cookietime, $cookiepath, $cookiedomain, false, true);
			setcookie('_discuz_pw', $discuz_pw, $cookietime, $cookiepath, $cookiedomain, false, true);
			setcookie('_discuz_secques', $secques, $cookietime, $cookiepath, $cookiedomain, false, true);

			$db->query("UPDATE $table_members SET lastvisit=lastactivity, lastactivity='$timestamp' $groupidadd $invisibleadd WHERE uid='$discuz_uid'", 'UNBUFFERED');

			$db->query("DELETE FROM $table_sessions WHERE uid='$discuz_uid'", 'UNBUFFERED'); 
			$sessionupdated=0;
			showmessage('login_succeed', dreferer());
		} else {
			@$fp = fopen(DISCUZ_ROOT.'./forumdata/illegallog.php', 'a');
			@flock($fp, 2);
			@fwrite($fp, $errorlog);
			@fclose($fp);
			showmessage('login_invalid', NULL, 'HALTED');
		}

	}

}

function logincheck() {
	/* returned value
		1=nonexistence;
		2=within limitation;
		3=record expired
	*/

	global $db, $table_failedlogins, $onlineip, $timestamp;
	$query = $db->query("SELECT count, lastupdate FROM $table_failedlogins WHERE ip='$onlineip'");
	if($login = $db->fetch_array($query)) {
		if($timestamp - $login['lastupdate'] > 900) {
			return 3;
		} elseif($login['count'] < 5) {
			return 2;
		} else {
			return 0;
		}
	} else {
		return 1;
	}
}

function loginrecord($valid, $permission) {
	global $db, $table_failedlogins, $onlineip, $timestamp;
	if(!$valid) {
		switch($permission) {
			case 1:	$db->query("INSERT INTO $table_failedlogins (ip, count, lastupdate)
					VALUES ('$onlineip', '1', '$timestamp')");
				break;
			case 2: $db->query("UPDATE $table_failedlogins SET count=count+1, lastupdate='$timestamp' WHERE ip='$onlineip'");
				break;
			case 3: $db->query("UPDATE $table_failedlogins SET count='1', lastupdate='$timestamp' WHERE ip='$onlineip'");
				$db->query("DELETE FROM $table_failedlogins WHERE lastupdate<$timestamp-901", 'UNBUFFERED');
				break;
		}
	}
}

?>