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

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$visitor['agent'] = ($_SERVER['HTTP_USER_AGENT'] ?? '');
$visitor['month'] = date('Ym');
$visitor['week']  = date('w');
$visitor['hour']  = date('H');

if(!$sessionexists) {
	if(strpos($visitor['agent'], 'MSIE')) {
		$visitor['browser'] = 'MSIE';
	} elseif(strpos($visitor['agent'], 'Netscape')) {
		$visitor['browser'] = 'Netscape';
	} elseif(strpos($visitor['agent'], 'Lynx')) {
		$visitor['browser'] = 'Lynx';
	} elseif(strpos($visitor['agent'], 'Opera')) {
		$visitor['browser'] = 'Opera';
	} elseif(strpos($visitor['agent'], 'Konqueror')) {
		$visitor['browser'] = 'Konqueror';
	} elseif(substr($visitor['agent'], 0, 7) == 'Mozilla') {
		$visitor['browser'] = 'Mozilla';
	} else {
		$visitor['browser'] = 'Other';
	}

	if(strpos($visitor['agent'], 'Win')) {
		$visitor['os'] = 'Windows';
	} elseif(strpos($visitor['agent'], 'Mac')) {
		$visitor['os'] = 'Mac';
	} elseif(strpos($visitor['agent'], 'Linux')) {
		$visitor['os'] = 'Linux';
	} elseif(strpos($visitor['agent'], 'FreeBSD')) {
		$visitor['os'] = 'FreeBSD';
	} elseif(strpos($visitor['agent'], 'SunOS')) {
		$visitor['os'] = 'SunOS';
	} elseif(strpos($visitor['agent'], 'BeOS')) {
		$visitor['os'] = 'BeOS';
	} elseif(strpos($visitor['agent'], 'OS/2')) {
		$visitor['os'] = 'OS/2';
	} elseif(strpos($visitor['agent'], 'AIX')) {
		$visitor['os'] = 'AIX';
	} else {
		$visitor['os'] = 'Other';
	}
	$visitorsadd = "OR (type='browser' AND var='{$visitor['browser']}') OR (type='os' AND var='{$visitor['os']}')";
	$visitorsadd .= $discuz_user ? " OR (type='total' AND var='members')" : " OR (type='total' AND var='guests')";
	$updatedrows = 7;
} else {
	$visitorsadd = '';
	$updatedrows = 4;
}

$db->query("UPDATE $table_stats SET count=count+1 WHERE (type='total' AND var='hits') $visitorsadd OR (type='month' AND var='{$visitor['month']}') OR (type='week' AND var='{$visitor['week']}') OR (type='hour' AND var='{$visitor['hour']}')");

if($updatedrows > $db->affected_rows()) {
	$db->query("INSERT INTO $table_stats (type, var, count)
			VALUES ('month', '{$visitor['month']}', '1')", 'SILENT');
}

?>