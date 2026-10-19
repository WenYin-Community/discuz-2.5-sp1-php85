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

require DISCUZ_ROOT.'./include/post.php';

cpheader();

if(!$operation) {

		require DISCUZ_ROOT.'./include/forum.php';
		$forumselect = '<select name="forum"><option value="">&nbsp;&nbsp;> '.$lang['select'].'</option>'.
			'<option value="">&nbsp;</option>'.forumselect().'</select>';

		if(!empty($forum)) {
			$forumselect = preg_replace("/(\<option value=\"$forum\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
		}

		$checksticky = array(0 => '', 1 => '', 2 => '');
		$checkdigest = array(0 => '', 1 => '', 2 => '');
		$checkattach = array(0 => '', 1 => '', 2 => '');
		$checksticky[intval($sticky)] = 'checked';
		$checkdigest[intval($digest)] = 'checked';
		$checkattach[intval($attach)] = 'checked';
		include CP_TPL.'moderate_search.php';

} else {

	if(!isset($tids)) {
		$tids = implode(',', $tidarray);
	}

	if($operation == 'move') {

		$query = $db->query("SELECT fid FROM $table_forums WHERE type<>'group' AND fid='$forum'");
		if(!$db->result($query, 0)) {
			cpmsg('moderate_move_invalid');
		}

		$db->query("UPDATE $table_threads SET fid='$forum' WHERE tid IN ($tids)");
		$db->query("UPDATE $table_posts SET fid='$forum' WHERE tid IN ($tids)");

		foreach(explode(',', $fids.','.$forum) as $fid) {
			updateforumcount(intval($fid));
		}

		cpmsg('moderate_succeed');

	} elseif($operation == 'delete') {

		$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid IN ($tids)");
		while($attach = $db->fetch_array($query)) {
			@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
		}

		if(!$donotupdatemember) {
			$uids = $comma = '';
			$query = $db->query("SELECT authorid FROM $table_posts WHERE tid IN ($tids)");
			while($post = $db->fetch_array($query)) {
				$uids .= $comma.$post['authorid'];
				$comma = ',';
			}
			updatemember('-', $uids, $deletedcredits);
		}

		$db->query("DELETE FROM $table_attachments WHERE tid IN ($tids)");
		$db->query("DELETE FROM $table_posts WHERE tid IN ($tids)");
		$db->query("DELETE FROM $table_threads WHERE tid IN ($tids)");
		$db->query("DELETE FROM $table_polls WHERE tid IN ($tids)");

		foreach(explode(',', $fids) as $fid) {
			updateforumcount(intval($fid));
		}

		cpmsg('moderate_succeed');

	} elseif($operation == 'deleteattach') {

		$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid IN ($tids)");
		while($attach = $db->fetch_array($query)) {
			@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
		}
		$db->query("UPDATE $table_threads SET attachment='0' WHERE tid IN ($tids)");
		$db->query("uPDATE $table_posts SET aid='0' WHERE tid IN ($tids)");

		cpmsg('moderate_succeed');

	} elseif($operation == 'stick') {

		$db->query("UPDATE $table_threads SET displayorder='$stick_level' WHERE tid IN ($tids)");
		cpmsg('moderate_succeed');

	} elseif($operation == 'adddigest') {

		$db->query("UPDATE $table_threads SET digest='$digest_level' WHERE tid IN ($tids)");
		cpmsg('moderate_succeed');

	}

}

if(submitcheck('searchsubmit')) {

	$sql = '';

	if(!empty($forum)) {
		$sql .= " AND fid='$forum'";
	}

	if($viewsless != '') {
		$sql .= " AND views<'$viewsless'";
	}
	if($viewsmore != '') {
		$sql .= " AND views>'$viewsmore'";
	}

	if($repliesless != '') {
		$sql .= " AND replies<'$repliesless'";
	}
	if($repliesmore != '') {
		$sql .= " AND replies>'$repliesmore'";
	}

	if($beforedays != '') {
		$sql .= " AND dateline<'$timestamp'-'$beforedays'*86400";
	}
	if($noreplydays != '') {
		$sql .= " AND lastpost<'$timestamp'-'$noreplydays'*86400";
	}

	if(trim($keywords)) {
		$sqlkeywords = '';
		$or = '';
		$keywords = explode(',', str_replace(' ', '', $keywords));
		for($i = 0; $i < count($keywords); $i++) {
			$sqlkeywords .= " $or subject LIKE '%".$keywords[$i]."%'";
			$or = 'OR';
		}
		$sql .= " AND ($sqlkeywords)";
	}

	if(trim($users)) {
		$sql .= " AND BINARY author IN ('".str_replace(',', '\',\'', str_replace(' ', '', $users))."')";
	}

	if($sticky == 1) {
		$sql .= " AND displayorder>'0'";
	} elseif($sticky == 2) {
		$sql .= " AND displayorder='0'";
	}
	if($digest == 1) {
		$sql .= " AND digest>'0'";
	} elseif($digest == 2) {
		$sql .= " AND digest='0'";
	}
	if($attach == 1) {
		$sql .= " AND attachment>'0'";
	} elseif($attach == 2) {
		$sql .= " AND attachment='0'";
	}

	$fids = array();
	$tids = $threadcount = '0';
	if($sql) {
		if($detail) {
			$threads = '';
			$query = $db->query("SELECT fid, tid, subject, authorid, author, views, replies, lastpost FROM $table_threads WHERE 1 $sql");
			while($thread = $db->fetch_array($query)) {
				$fids[] = $thread['fid'];
				$tids .= ','.$thread['tid'];
				$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);
				$threads .= "<tr><td align=\"center\" bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"tidarray[]\" value=\"{$thread['tid']}\" checked>\n".
					"<td bgcolor=\"".ALTBG2."\"><a href=\"viewthread.php?tid={$thread['tid']}\" target=\"_blank\">{$thread['subject']}</a></td>\n".
					"<td align=\"center\" bgcolor=\"".ALTBG1."\"><a href=\"forumdisplay.php?fid={$thread['fid']}\" target=\"_blank\">{$_DCACHE['forums'][$thread['fid']]['name']}</a></td>\n".
					"<td align=\"center\" bgcolor=\"".ALTBG2."\"><a href=\"viewpro.php?uid={$thread['authorid']}\" target=\"_blank\">{$thread['author']}</a></td>\n".
					"<td align=\"center\" bgcolor=\"".ALTBG1."\">{$thread['replies']}</td>\n".
					"<td align=\"center\" bgcolor=\"".ALTBG2."\">{$thread['views']}</td>\n".
					"<td align=\"center\" bgcolor=\"".ALTBG1."\">{$thread['lastpost']}</td></tr>\n";
			}
		} else {
			$query = $db->query("SELECT fid, tid FROM $table_threads WHERE 1 $sql");
			while($thread = $db->fetch_array($query)) {
				$fids[] = $thread['fid'];
				$tids .= ','.$thread['tid'];
			}
		}
		$threadcount = $db->num_rows($query);
	}
	$fids = implode(',', array_unique($fids));

	include CP_TPL.'moderate_result.php';
}

?>