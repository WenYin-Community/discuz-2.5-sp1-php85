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

if($action == 'announcements') {

	if(!submitcheck('announcesubmit') && !submitcheck('addsubmit') && !$edit && !$extr) {

		$announcements = '';
		$query = $db->query("SELECT * FROM $table_announcements ORDER BY displayorder, starttime DESC, id DESC");
		while($announce = $db->fetch_array($query)) {
			$disabled = $adminid != 1 && $announce['author'] != $discuz_userss ? 'disabled' : NULL;
			$announce['starttime'] = $announce['starttime'] ? gmdate("$dateformat", $announce['starttime'] + $timeoffset * 3600) : $lang['unlimited'];
			$announce['endtime'] = $announce['endtime'] ? gmdate("$dateformat", $announce['endtime'] + $timeoffset * 3600) : $lang['unlimited'];
			if ($announce['posturl']){
					$announce_editurl = '<a href="'.$announce['posturl'].'" target="_blank"><b>'.$lang['announce_click_url'].'</b></a>';
			}else{
					$announce_editurl = "<a href=\"admincp.php?action=announcements&edit={$announce['id']}\">".cutstr(strip_tags($announce['message']), 20)."</a>";
			}
			$announcements .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$announce['id']}\" $disabled></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><a href=\"./viewpro.php?username=".rawurlencode($announce['author'])."\" target=\"_blank\">{$announce['author']}</a></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><a href=\"admincp.php?action=announcements&edit={$announce['id']}\" $disabled>{$announce['subject']}</a></td>\n".
				"<td bgcolor=\"".ALTBG2."\">$announce_editurl</td>\n".
				"<td bgcolor=\"".ALTBG1."\">{$announce['starttime']}</td>\n".
				"<td bgcolor=\"".ALTBG2."\">{$announce['endtime']}</td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"2\" name=\"displayordernew[{$announce['id']}]\" value=\"{$announce['displayorder']}\" $disabled></td></tr>\n";
		}
		$newstarttime = gmdate('Y-n-j', $timestamp + $timeoffset * 3600);
		include CP_TPL.'announcements_main.php';

	} elseif($edit || $extr) {

		$edit =  empty($edit) ? $extr : $edit ;

		$query = $db->query("SELECT * FROM $table_announcements WHERE id='$edit' AND ('$adminid'='1' OR author='$discuz_user')");
		if(!$announce = $db->fetch_array($query)) {
			cpmsg('announce_nonexistence');
		}

		if(!submitcheck('editsubmit')) {

			$announce['starttime'] = $announce['starttime'] ? gmdate('Y-n-j', $announce['starttime'] + $timeoffset * 3600) : "";
			$announce['endtime'] = $announce['endtime'] ? gmdate('Y-n-j', $announce['endtime'] + $timeoffset * 3600) : "";
			include DISCUZ_ROOT .'admin/tpl/announcements_edit.php';
		} else {
			$newsubject = dhtmlspecialchars($subjectnew);
			$newposturl = dhtmlspecialchars($posturlnew);
			if(strpos($starttimenew, '-')) {
				$time = explode('-', $starttimenew);
				$starttimenew = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
			} else {
				$starttimenew = 0;
			}
			if(strpos($endtimenew, '-')) {
				$time = explode('-', $endtimenew);
				$endtimenew = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
			} else {
				$endtimenew = 0;
			}

			if(!$starttimenew) {
				cpmsg('announce_start_time_invalid');
			
			} elseif(!trim($subjectnew) || (!trim($messagenew) && !trim($posturlnew))) {
				cpmsg('announce_invalid');
			
			} else {
				$db->query("UPDATE $table_announcements SET subject='$subjectnew', posturl='$posturlnew', starttime='$starttimenew', endtime='$endtimenew', message='$messagenew' WHERE id='$edit'");
				updatecache('announcements');
				updatecache('announcements_forum');
				cplog('SAVE');
				cpmsg('announce_succeed', 'admincp.php?action=announcements');
			}
		}

	} elseif(submitcheck('announcesubmit')) {

		if(is_array($delete)) {
			$ids = $comma = '';
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ',';
			}
			$db->query("DELETE FROM $table_announcements WHERE id IN ($ids) AND ('$adminid'='1' OR author='$discuz_user')");
		}

		if(is_array($displayordernew)) {
			foreach($displayordernew as $id => $displayorder) {
				$db->query("UPDATE $table_announcements SET displayorder='$displayorder' WHERE id='$id' AND ('$adminid'='1' OR author='$discuz_user')");
			}
		}
		cplog('SAVE');
		updatecache('announcements');
		updatecache('announcements_forum');
		cpmsg('announce_update_succeed', 'admincp.php?action=announcements');

	} elseif(submitcheck('addsubmit')) {

		$newsubject = dhtmlspecialchars($newsubject);
		$newposturl = dhtmlspecialchars($newposturl);

		if(strpos($newstarttime, '-')) {
			$time = explode('-', $newstarttime);
			$newstarttime = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
		} else {
			$newstarttime = 0;
		}
		if(strpos($newendtime, '-')) {
			$time = explode('-', $newendtime);
			$newendtime = gmmktime(0, 0, 0, $time[1], $time[2], $time[0]) - $timeoffset * 3600;
		} else {
			$newendtime = 0;
		}

		if(!$newstarttime) {
			cpmsg('announce_start_time_invalid');
		} elseif(!trim($newsubject) || (!trim($newmessage) && !trim($newposturl))) {
			cpmsg('announce_invalid');
		} else {
			$db->query("INSERT INTO $table_announcements (author, subject, posturl, starttime, endtime, message)
				VALUES ('$discuz_user', '$newsubject', '$newposturl', '$newstarttime', '$newendtime', '$newmessage')");
			updatecache('announcements');
			updatecache('announcements_forum');
			cplog('SAVE');
			cpmsg('announce_succeed', 'admincp.php?action=announcements');
		}
	}

}

?>