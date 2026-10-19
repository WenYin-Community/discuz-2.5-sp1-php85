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

$app = 35;

if(!submitcheck('deletesubmit') && !submitcheck('searchsubmit')) {
	require DISCUZ_ROOT.'./include/forum.php';
	include DISCUZ_ROOT.'./admin/tpl/attachments_home.php';
} elseif(submitcheck('searchsubmit')) {

	require DISCUZ_ROOT.'./include/attachment.php';

	$sql = "a.pid=p.pid";

	if($forum && $forum != 'all') {
		$sql .= " AND p.fid='$forum'";
	} elseif($forum != 'all') {
		cpmsg('attachments_forum_invalid');
	}
	if($daysold) {
		$sql .= " AND p.dateline<='".($timestamp - (86400 * $daysold))."'";
	}
	if($author) {
		$sql .= " AND p.author='$author'";
	}
	if($filename) {
		$sql .= " AND a.filename LIKE '%$filename%'";
	}
	if($sizeless) {
		$sql .= " AND a.filesize<'$sizeless'";
	}
	if($sizemore) {
		$sql .= " AND a.filesize>'$sizemore' ";
	}
	if($dlcountless) {
		$sql .= " AND a.downloads<'$dlcountless'";
	}
	if($dlcountmore) {
		$sql .= " AND a.downloads>'$dlcountmore'";
	}

	$attachments = '';
	$query = $db->query("SELECT a.*, p.fid, p.author, t.tid, t.tid, t.subject, f.name AS fname FROM $table_attachments a, $table_posts p, $table_threads t, $table_forums f WHERE t.tid=a.tid AND f.fid=p.fid AND $sql");
	while($attachment = $db->fetch_array($query)) {
		$matched = file_exists(DISCUZ_ROOT."./$attachdir/{$attachment['attachment']}") ? NULL : "<b>{$lang['attachments_lost']}</b><br>";
		$attachsize = sizecount($attachment['filesize']);
		if(!$nomatched || ($nomatched && $matched)) {
			$attachments .= "<tr><td bgcolor=\"".ALTBG1."\" width=\"45\" align=\"center\" valign=\"middle\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$attachment['aid']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\" width=\"20%\"><b>{$attachment['filename']}</b><br><a href=\"attachment.php?aid={$attachment['aid']}\" target=\"_blank\">[{$lang['attachments_download']}]</a></td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\" width=\"20%\">$matched<a href=\"$attachurl/{$attachment['attachment']}\" class=\"smalltxt\" target=\"_blank\">{$attachment['attachment']}</a></td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\" width=\"8%\">{$attachment['author']}</td>\n".
				"<td bgcolor=\"".ALTBG1."\" valign=\"middle\" width=\"25%\"><a href=\"viewthread.php?tid={$attachment['tid']}\" target=\"_blank\"><b>".cutstr($attachment['subject'], 18)."</b></a><br>{$lang['forum']}:<a href=\"forumdisplay.php?fid={$attachment['fid']}\" target=\"_blank\">{$attachment['fname']}</a></td>\n".
				"<td bgcolor=\"".ALTBG2."\" valign=\"middle\" width=\"18%\" align=\"center\">$attachsize</td>\n".
				"<td bgcolor=\"".ALTBG1."\" valign=\"middle\" width=\"7%\" align=\"center\">{$attachment['downloads']}</td></tr>\n";
		}
	}

	include DISCUZ_ROOT.'./admin/tpl/attachments_list.php';

} elseif(submitcheck('deletesubmit')) {

	if(	$ids = implode_ids( $delete )) {

		$tids = $pids = $comma1 = $comma2 = '';
		$pids_array = $chang_post = array();

		$query = $db->query("SELECT tid, pid, attachment FROM $table_attachments WHERE aid IN ($ids)");

		while($attach = $db->fetch_array($query)) {
			@unlink("$attachdir/{$attach['attachment']}");
			if (!in_array($attach['pid'],$pids_array)){
				$tids .= "$comma1'{$attach['tid']}'";
				$comma1 = ',';
				$pids .= "$comma2'{$attach['pid']}'";
				$pids_array[] = $attach['pid'];
				$comma2 = ',';
			}
		}
		$db->query("DELETE FROM $table_attachments WHERE aid IN ($ids)", 'UNBUFFERED');

		$query = $db->query("SELECT filename, filetype, filesize, attachment, creditsrequire, dateline FROM $table_attachments WHERE pid IN ($pids)");
		while($attach = $db->fetch_array($query)) {
			$chang_post["{$attach['pid']}"][]=$attach;
		}
	
		$z_pid ='0';
		foreach ($pids_array as $aaid){
			$tempaid = $chang_post["$aaid"];
			if ($tempaid && is_array($tempaid)){
				$tempaid = addslashes(serialize($tempaid));
				$db->query("UPDATE $table_posts SET aid='$tempaid' WHERE pid='$aaid'", 'UNBUFFERED');
			}else{
				$z_pid .=','.$aaid;
			}
		}
		unset($pids_array, $chang_post, $attach);
		if ($z_pid){
			$db->query("UPDATE $table_posts SET aid='0' WHERE pid IN ($z_pid)");
		}
		$attachtids = '0';
		$query = $db->query("SELECT tid, filetype FROM $table_attachments WHERE tid IN ($tids) GROUP BY tid ORDER BY pid DESC");
		while($attach = $db->fetch_array($query)) {
			$db->query("UPDATE $table_threads SET attachment='{$attach['filetype']}' WHERE tid='{$attach['tid']}'", 'UNBUFFERED');
			$attachtids .= ", '{$attach['tid']}'";
		}
		$db->query("UPDATE $table_threads SET attachment='' WHERE tid IN ($tids)".($attachtids ? " AND tid NOT IN ($attachtids)" : NULL), 'UNBUFFERED');

		cpmsg('attachments_edit_succeed');
	} else {
		cpmsg('attachments_edit_invalid');
	}
}

?>