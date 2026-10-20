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

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('IN_ADMINCP',TRUE);

if(empty($_GET['action']) || !empty($_GET['direct'])) {
	$action = empty($_GET['action']) ? 'home' : ($_GET['action'] ?? '');
	$extr = ($_GET['extr'] ?? '');
	$sid = ($_COOKIE['sid'] ?? '') ? ($_COOKIE['sid'] ?? '') : ($_GET['sid'] ?? '');
	include './admin/tpl/index.php';
	exit();
}

require './include/common.php';
require DISCUZ_ROOT.'./admin/global.php';
require DISCUZ_ROOT.'./include/cache.php';

define('CP_TPL',DISCUZ_ROOT.'./admin/tpl/');

$discuz_action = 161;

include language('admincp');

if($action == 'menu') {
	include DISCUZ_ROOT.'./admin/menu.php';
}elseif($action == 'top') {
	include CP_TPL.'top.php';
} else {

	if(!$discuz_uid || $adminid < 1) {
		$cpaccess = 0;
	} else {

		if(!$discuz_secques && !$is_sessionuser) {
			cpheader();
			cpmsg('secques_invalid');
		}
		
		$ipcheck = " AND ip='$onlineip'"; 

		$query = $db->query("SELECT errorlog FROM $table_adminsessions WHERE uid='$discuz_uid' AND dateline>$timestamp-3600 ".$ipcheck, 'SILENT');
		if($db->error()) {
			$db->query("DROP TABLE IF EXISTS $table_adminsessions");
			$db->query("CREATE TABLE $table_adminsessions (
					uid mediumint(8) NOT NULL default '0',
					ip char(20) NOT NULL default '',
					dateline int(10) unsigned NOT NULL default '0',
					errorlog tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (uid,ip,dateline)
					)");
			$cpaccess = 1;
		} else {
			if($session = $db->fetch_array($query)) {
				if($session['errorlog'] == -1) {
					$db->query("UPDATE $table_adminsessions SET dateline='$timestamp' ,ip='$onlineip' WHERE uid='$discuz_uid'", 'UNBUFFERED');
					$cpaccess = 2;
				}elseif($session['errorlog'] <= 3) {
					$cpaccess = 1;
					$cpsuccess ='F';
				} else {
					$cpaccess = 0;
					$cpsuccess ='F';
				}
			} else {
				$db->query("DELETE FROM $table_adminsessions WHERE uid='$discuz_uid' OR dateline<$timestamp-3600");
				$db->query("INSERT INTO $table_adminsessions (uid, ip, dateline, errorlog)
						VALUES ('$discuz_uid', '$onlineip','$timestamp', '1')");
				$cpaccess = 1;
				$cpsuccess ='F';
			}
		}

	}

	if ($cpaccess=='0') {
		$extra = '<b>PERMISSION DENIED</b>'; 
		cplog();
		clearcookies();
		cpheader();
		cpmsg('noaccess');
	}elseif($cpaccess == 1) {
		if (($_POST['cploginsubmit'] ?? '')){
			if ($admin_password && dpassword_verify($admin_password, $discuz_pw)){
				$cpnologin =3;
				$db->query("UPDATE $table_adminsessions SET errorlog='-1' WHERE uid='$discuz_uid'");
				$extra = '<b>Loggin ADMINCP<b>';
				$cpsuccess ='T';
			}else{
				$cpnologin =2;
				$db->query("UPDATE $table_adminsessions SET errorlog=errorlog+1 WHERE uid='$discuz_uid'");
				$extra = '<b>Loggin ADMINCP error</b>#'.intval($session['errorlog']);
			}
		}else{
			$cpnologin =1;
			$extra = 'ADMINCP Password Required</b>';
		}
		
		if ($cpnologin <3){
				$session['errorlog'] ++;
				cplog();
				$action = empty($action) ? 'home' : $action;
				cpheader();
				include CP_TPL.'login.php';
				cpfooter();
				dexit();
		}
	}elseif($cpaccess=='2'){
		$extra = $semicolon = '';
		if(is_array($_GET)) {
			foreach($_GET as $key => $val) {
				if($key != 'action' && $key != 'sid') {
					$extra .= dhtmlspecialchars("$semicolon$key=$val");
					$semicolon = '; ';
				}
			}
		}
	}
	cplog();

	$cpscript = '';
	if($adminid == 1) {
		if($action == 'home') {
			$cpscript = 'home.php';
		}elseif($action == 'settings') {
			$cpscript = 'settings.php';
		}elseif($action == 'forumadd' || $action == 'forumsedit' || $action == 'forumsmerge' || $action == 'forumdetail' || $action == 'forumdelete') {
			$cpscript = 'forums.php';
		}elseif($action == 'addmember' || $action == 'members' || $action == 'access' || $action == 'memberprofile' || $action == 'ipban') {
			$cpscript = 'members.php';
		}elseif($action == 'usergroups' || $action == 'admingroups' || $action == 'ranks') {
			$cpscript = 'groups.php';
		}elseif($action == 'announcements') {
			$cpscript = 'announcements.php';
		}elseif($action == 'styles') {
			$cpscript = 'styles.php';
		}elseif($action == 'templates' || $action == 'tpladd' || $action == 'tpledit') {
			$cpscript = 'templates.php';
		}elseif($action == 'forumlinks' || $action == 'onlinelist' || $action == 'censor' || $action ==  'discuzcodes' || $action == 'smilies' || $action ==  'icons' || $action == 'attachtypes' || $action == 'updatecache' || $action ==  'logout') {
			$cpscript = 'misc.php';
		}elseif($action == 'export' || $action == 'import' || $action == 'runquery'  || $action == 'optimize') {
			$cpscript = 'database.php';
		}elseif($action == 'attachments') {
			$cpscript = 'attachments.php';
		}elseif($action == 'counter') {
			$cpscript = 'counter.php';
		}elseif($action == 'moderate') {
			$cpscript = 'moderate.php';
		}elseif($action == 'prune' || $action == 'pmprune') {
			$cpscript = 'prune.php';
		}elseif($action == 'newsletter') {
			$cpscript = 'newsletter.php';
		}elseif($action == 'illegallog' || $action == 'karmalog' || $action == 'modslog' ||    $action == 'cplog') {
			$cpscript = 'logs.php';
		}elseif($action == 'plugin') {
			$cpscript = 'plugin.php';
		}
	}elseif($adminid >1 ) {
		if($action == 'home') {
			$cpscript = 'home.php';
		}elseif((($allowedituser || $allowbanuser) && $action == 'mod_members') || ($allowbanip    && $action == 'ipban')) {
			$cpscript = 'members.php';
		}elseif($allowpostannounce && $action == 'announcements') {
			$cpscript = 'announcements.php';
		}elseif(($allowcensorword && $action == 'censor') || $action == 'logout') {
			$cpscript = 'misc.php';
		}elseif($allowmassprune && $action == 'prune') {
			$cpscript = 'prune.php';
		}elseif($allowviewlog && ($action == 'karmalog' || $action == 'modslog')) {
			$cpscript = 'logs.php';
		}
	}

	if($cpscript) {
		require DISCUZ_ROOT.'./admin/'.$cpscript;
	} else {
		cpheader();
		cpmsg('noaccess');
	}
	cpfooter();
}
output();

function cplog($success=''){
	global $action,$timestamp, $discuz_user, $adminid, $onlineip, $extra,$lang,$cpsuccess,$cpaccess,$groupid;
	$safeaction = dhtmlspecialchars($action);
	if($safeaction && !in_array($safeaction, array('main', 'top', 'menu', 'illegallog',   'karmalog', 'modslog', 'cplog', 'home')) || $cpaccess<2){
		@$fp = fopen(DISCUZ_ROOT.'./forumdata/cplog.php', 'a');
		@flock($fp, 2);
		@fwrite($fp, "$timestamp\t$discuz_user\t$groupid\t$onlineip\t$safeaction\t$extra\t$cpsuccess\n");
		@fclose($fp);
	}
}
?>