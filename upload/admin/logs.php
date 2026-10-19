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
1 批量主题管理时候的连接问题
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

$logheader=$logbody='';
$logs = array();
$logspan = $timestamp - 86400 * 15;
$lpp = empty($lpp) ? 50 : $lpp;

if(!in_array($action, array('illegallog', 'karmalog', 'modslog', 'cplog'))) {
	cpmsg('undefined_action');
}

$filename = DISCUZ_ROOT.'./forumdata/'.$action.'.php';
@$logfile = file($filename);
@$fp = fopen($filename, 'w');
@flock($fp, 2);
@fwrite($fp, "<?PHP exit('Access Denied'); ?".">\n");

foreach($logfile as $logrow) {
	if(intval($logrow) > $logspan && strpos($logrow, "\t")) {
		$logs[] = $logrow;
		@fwrite($fp, $logrow."\n");
	}
}
@fclose($fp);

if(!$page) {
	$page = 1;
}

$start = ($page - 1) * $lpp;
$logs = array_reverse($logs);

if(empty($keyword)) {
	$num = count($logs);
	$multipage = multi($num, $lpp, $page, "admincp.php?action=$action&lpp=$lpp");

	for($i = 0; $i < $start; $i++) {
		unset($logs[$i]);
	}
	for($i = $start + $lpp; $i < $num; $i++) {
		unset($logs[$i]);
	}
} else {
	foreach($logs as $key => $value) {
		if(strpos($value, $keyword) === FALSE) {
			unset($logs[$key]);
		}
	}
	$multipage = '';
}

$lognames = array('illegallog' => 'logs_passwd', 'karmalog' => 'logs_karma', 'modslog' => 'logs_moderate', 'cplog' => 'logs_cp');

if(in_array($action, array('karmalog', 'modslog', 'cplog'))) {
	$usergroup = array();
	$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups");
	while($group = $db->fetch_array($query)) {
		$usergroup[$group['groupid']] = $group['grouptitle'];
	}
}

if($action == 'illegallog') {

	$logheader= "<tr class=\"header\" align=\"center\"><td>{$lang['logs_passwd_username']}</td><td>{$lang['logs_passwd_password']}</td><td>{$lang['logs_passwd_security']}</td><td>{$lang['ip']}</td><td>{$lang['time']}</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = gmdate('y-n-j H:i', $log[0] + $timeoffset * 3600);
		$log[1] = stripslashes($log[1]);
		if(strtolower($log[1]) == strtolower($discuz_userss)) {
			$log[1] = "<b>$log[1]</b>";
		}

		$logbody .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">$log[1]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[2]</td><td bgcolor=\"".ALTBG1."\">$log[3]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[4]</td><td bgcolor=\"".ALTBG1."\">$log[0]</td></tr>\n";
	}

} elseif($action == 'karmalog') {

	$logheader= "<tr class=\"header\" align=\"center\"><td width=\"15%\">{$lang['username']}</td><td width=\"12%\">{$lang['usergroup']}</td><td width=\"18%\">{$lang['time']}</td><td width=\"15%\">{$lang['logs_karma_username']}</td><td width=\"8%\">{$lang['logs_karma_rating']}</td><td width=\"28%\">{$lang['subject']}</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = gmdate('y-n-j H:i', $log[0] + $timeoffset * 3600);
		$log[1] = "<a href=\"viewpro.php?username=".rawurlencode($log[1])."\" target=\"_blank\">$log[1]";
		$log[2] = $usergroup[$log[2]];
		$log[3] = "<a href=\"viewpro.php?username=".rawurlencode($log[3])."\" target=\"_blank\">$log[3]</a>";
		if($log[3] == $discuz_userss) {
			$log[3] = "<b>$log[3]</b>";
		}
		$log[4] = $log[4] < 0 ? "<b>$log[4]</b>" : $log[4];
		$log[6] = "<a href=\"./viewthread.php?tid=$log[5]\" target=\"_blank\" title=\"$log[6]\">".cutstr($log[6], 20)."</a>";

		$logbody .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">$log[1]</a></td><td bgcolor=\"".ALTBG2."\">$log[2]</td>\n".
			"<td bgcolor=\"".ALTBG1."\">$log[0]</td><td bgcolor=\"".ALTBG2."\">$log[3]</td>\n".
			"<td bgcolor=\"".ALTBG1."\">$log[4]</td><td bgcolor=\"".ALTBG2."\">$log[6]</td></tr>\n";
	}

} elseif($action == 'modslog') {

	$logheader = "<tr class=\"header\" align=\"center\"><td width=\"10%\">{$lang['operator']}</td><td width=\"15%\">{$lang['usergroup']}</td><td width=\"10%\">{$lang['ip']}</td><td width=\"18%\">{$lang['time']}</td><td width=\"15%\">{$lang['forum']}</td><td width=\"19%\">{$lang['thread']}</td><td width=\"13%\">{$lang['action']}</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = gmdate('y-n-j H:i', $log[0] + $timeoffset * 3600);
		$log[1] = stripslashes($log[1]);
		if($log[1] != $discuz_user) {
			$log[1] = "<b>$log[1]</b>";
		}
		$log[2] = $usergroup[$log[2]];
		$log[5] = "<a href=\"./forumdisplay.php?fid=$log[4]\" target=\"_blank\">$log[5]</a>";
		if ($log[6]) {
			$log[7] = "<a href=\"./viewthread.php?tid=$log[6]\" target=\"_blank\" title=\"$log[7]\">".cutstr($log[7], 15)."</a>";
		} else {
			$logTmp = explode(",", str_replace("'", "", str_replace(' ', '', $log[7])));
			$isTids = TRUE;
			$log7Tmp = '';
			$logLengthTmp = 0;
			foreach ($logTmp as $tidTmp) {
				if (!is_numeric($tidTmp)) {
					$isTids = FALSE;
					break;
				} else {
					if ($logLengthTmp <= 12) {
						$log7Tmp .= "<a href=\"./viewthread.php?tid=$tidTmp\" target=\"_blank\" title=\"$log[7]\">".$tidTmp."&nbsp;</a>";
						$logLengthTmp += strlen($tidTmp)+1;
					} else {
						$log7Tmp .= "...";
						break;
					}
				}
			}
			if ($isTids && $log7Tmp) {
				$log[7] = $log7Tmp;
			} else {
				$log[7] = "<a href=\"./viewthread.php?tid=$log[6]\" target=\"_blank\" title=\"$log[7]\">".cutstr($log[7], 15)."</a>";
			}
		} 
		$mod_log_action ='mod_'.trim($log[8]);
		$log[8] = $lang[$mod_log_action]?$lang[$mod_log_action]:$log[8];

		$logbody .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">$log[1]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[2]</td><td bgcolor=\"".ALTBG1."\">$log[3]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[0]</td><td bgcolor=\"".ALTBG1."\">$log[5]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[7]</td><td bgcolor=\"".ALTBG1."\">$log[8]</td></tr>\n";
	}

} elseif($action == 'cplog') {

	$logheader = "<tr class=\"header\" align=\"center\"><td width=\"10%\">{$lang['operator']}</td><td width=\"10%\">{$lang['usergroup']}</td><td width=\"10%\">{$lang['ip']}</td><td width=\"18%\">{$lang['time']}</td><td width=\"15%\">{$lang['action']}</td><td width=\"37%\">{$lang['other']}</td><td width=\"10%\">actived</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = gmdate('y-n-j H:i', $log[0] + $timeoffset * 3600);
		$log[1] = stripslashes($log[1]);
		if($log[1] != $discuz_user) {
			$log[1] = "<b>$log[1]</b>";
		}
		$log[2] = $usergroup[$log[2]];

		$logbody .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">$log[1]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[2]</td><td bgcolor=\"".ALTBG1."\">$log[3]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[0]</td><td bgcolor=\"".ALTBG1."\">$log[4]</td>\n".
			"<td bgcolor=\"".ALTBG2."\">$log[5]</td>".
			"<td bgcolor=\"".ALTBG2."\">$log[6]</td></tr>\n";
	}

}
if($logbody) {
	include DISCUZ_ROOT.'admin/tpl/loglist.php';
}elseif($logheader){
	$logheader = "</tr>\n<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">NO record found!</td></tr>\n";
	include DISCUZ_ROOT.'admin/tpl/loglist.php';
}

?>
