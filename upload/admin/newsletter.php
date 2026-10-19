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

if(!submitcheck('newslettersubmit')) {

	$count = 0;
	$usergroups = '';
	$admingroups = '';

	$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups WHERE groupid not in('5','6','7') ORDER BY type, creditslower"); 
	while($group = $db->fetch_array($query)) {
		$usergroups .= ($count++ % 3 == 0 ? '</tr><tr>' : '').
			"<td width=\"33%\" nowrap><input type=\"checkbox\" name=\"usersendto[]\" value=\"$group[groupid]\">$group[grouptitle]</td>";
	}

	$count = 0;
	$query = $db->query("SELECT admingid, admintitle FROM $table_admingroups ORDER BY admingid"); 
	while($group = $db->fetch_array($query)) {
		$admingroups .= ($count++ % 3 == 0 ? '</tr><tr>' : '').
			"<td width=\"33%\" nowrap><input type=\"checkbox\" name=\"adminsendto[]\" value=\"$group[admingid]\">$group[admintitle]</td>";
	}

	include CP_TPL.'newsletter.php';

} else {
	if ($sendtype == 'user'){
		$sendto = &$usersendto;
		$querygroup = 'groupid';
	}elseif($sendtype == 'admin'){
		$sendto = &$adminsendto;
		$querygroup = 'adminid';
	}else{
		cpmsg('newsletter_send_to_invalid');
	}
	if(is_array($sendto) && count($sendto)) {
		$ids = "'".implode("','", $sendto)."'";
	} else {
		cpmsg('newsletter_send_to_invalid');
	}

	if(!$subject || !$message) {
		cpmsg('newsletter_sm_invalid');
	}

	$subject = "<b>[{$lang['newsletter']}]</b> ".$subject;

	$emails = '';
	$query = $db->query("SELECT uid, email FROM $table_members WHERE $querygroup IN ($ids) AND newsletter='1'");
	while($member = $db->fetch_array($query)) {
		if($sendvia == 'pm') {
			$db->query("INSERT INTO $table_pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$member[uid]', 'inbox', '1', '$subject', '$timestamp', '$message')");
		} elseif($sendvia == 'email') {
			$emails .= $comma.$member['email'];
			$comma = ',';
		}
	}

	if($sendvia == 'pm') {
		$db->query("UPDATE $table_members SET newpm='1' WHERE $querygroup IN ($ids) AND newsletter='1'");
	} elseif($sendvia == 'email') {
		sendmail($emails, $subject, $message);
	}

	cpmsg('newsletter_succeed');

}

?>