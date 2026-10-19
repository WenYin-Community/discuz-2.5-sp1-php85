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

if($action == 'prune') {

	if(!submitcheck('prunesubmit')) {

		require DISCUZ_ROOT.'./include/forum.php';

		if($adminid == 1 || $adminid == 2 || ($adminid>3 || $adminglobal)) {
			$forumselect = '<select name="forums"><option value="">&nbsp;&nbsp;> '.$lang['select'].'</option>'.
				'<option value="">&nbsp;</option><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option>'.
				'<option value="">&nbsp;</option>'.forumselect().'</select>';
		} else {
			$forumselect = $comma = '';
			$query = $db->query("SELECT fid, name, moderator FROM $table_forums");
			while($forum = $db->fetch_array($query)) {
				if(modcheck($discuz_user)) {
					$forumselect .= $comma.$forum['name'];
					$comma = ', ';
				}
			}
			$forumselect = $forumselect ? $forumselect : $lang['none'];
		}

		include CP_TPL.'prune_post.php';

	} else {

		if(($starttime == '0' && $endtime == '0') || (!$forums && $adminid != 3)) {
			cpmsg('prune_range_invalid');
		}

		$keywords = trim($keywords);
		$users = trim($users);
		if($keywords == '' && $useip == '' && $users == '') {
			cpmsg('prune_condition_isnull');
		}

		$sql = "SELECT p.fid, p.tid, p.pid, p.authorid, t.tid AS isfirstpost FROM $table_posts p LEFT JOIN $table_threads t ON t.tid=p.tid AND t.dateline=p.dateline AND t.authorid=p.authorid WHERE 1";

		if($adminid == 1 || $adminid == 2) {
			if($forums != 'all') {
				$sql .= " AND p.fid='$forums'";
			}
		} else {
			$forums = '0';
			$query = $db->query("SELECT fid, moderator FROM $table_forums");
			while($forum = $db->fetch_array($query)) {
				$forums .= modcheck($discuz_user) ? ",$forum[fid]" : NULL;
			}
			$sql .= " AND p.fid IN ($forums)";
		}

		if($users != '') {
			$uids = '0';
			$query = $db->query("SELECT uid FROM $table_members WHERE BINARY username IN ('".str_replace(',', '\',\'', str_replace(' ', '', $users))."')");
			while($member = $db->fetch_array($query)) {
				$uids .= ",$member[uid]";
			}
			$sql .= " AND p.authorid IN ($uids)";
		}
		if($useip != '') {
			$sql .= " AND p.useip LIKE '".str_replace('*', '%', $useip)."'";
		}
		if($keywords != '') {
			$sqlkeywords = '';
			$or = '';
			$keywords = explode(',', str_replace(' ', '', $keywords));
			for($i = 0; $i < count($keywords); $i++) {
				$sqlkeywords .= " $or p.subject LIKE '%".$keywords[$i]."%' OR p.message LIKE '%".$keywords[$i]."%'";
				$or = 'OR';
			}
			$sql .= " AND ($sqlkeywords)";
		}

		if($starttime != '0') {
			$starttime = strtotime($starttime);
			$sql .= " AND p.dateline>'$starttime'";
		}
		if($adminid == 1 && $endtime != gmdate('Y-n-j', $timestamp + $timeoffset * 3600)) {
			if($endtime != '0') {
				$endtime = strtotime($endtime);
				$sql .= " AND p.dateline<'$endtime'";
			}
		} else {
			$endtime = $timestamp;
		}
		if(($adminid == 2 && $endtime - $starttime > 86400 * 8) || ($adminid > 2 && $endtime - $starttime > 86400 * 4)) {
			cpmsg('prune_mod_range_illegal');
		}

		$prune = array();
		$tidsdelete = $pids = '0';
		$query = $db->query($sql);
		while($post = $db->fetch_array($query)) {
			$prune['forums'][] = $post['fid'];
			$prune['thread'][$post['tid']]++;

			$pids .= ",$post[pid]";
			$tidsdelete .= $post['isfirstpost'] ? ",$post[tid]" : NULL;
		}

		if($pids) {
			require DISCUZ_ROOT.'./include/post.php';

			$query = $db->query("SELECT attachment FROM $table_attachments WHERE pid IN ($pids) OR tid IN ($tidsdelete)");
			while($attach = $db->fetch_array($query)) {
				@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
			}

			foreach($prune['thread'] as $tid => $decrease) {
				$db->query("UPDATE $table_threads SET replies=replies-$decrease WHERE tid='$tid'");
			}

			if(!$donotupdatemember) {
				$uids = $comma = '';
				$query = $db->query("SELECT authorid FROM $table_posts WHERE pid IN ($pids) OR tid IN ($tidsdelete)");
				while($post = $db->fetch_array($query)) {
					$uids .= "$comma$post[authorid]";
					$comma = ',';
				}
				updatemember('-', $uids, $deletedcredits);
			}

			$db->query("DELETE FROM $table_attachments WHERE pid IN ($pids) OR tid IN ($tidsdelete)");
			$db->query("DELETE FROM $table_threads WHERE tid IN ($tidsdelete)");
			$db->query("DELETE FROM $table_polls WHERE tid IN ($tidsdelete)");
			$deletedthreads = $db->affected_rows();
			$db->query("DELETE FROM $table_posts WHERE pid IN ($pids) OR tid IN ($tidsdelete)");
			$deletedposts = $db->affected_rows();

			foreach(array_unique($prune['forums']) as $fid) {
				updateforumcount($fid);
			}

		}

		$deletedthreads = intval($deletedthreads);
		$deletedposts = intval($deletedposts);
		cpmsg('prune_succeed');

	}

} elseif($action == 'pmprune') {

	if(!submitcheck('prunesubmit')) {

		include CP_TPL.'prune_pm.php';

	} else {

		if($days == '') {
			cpmsg('prune_pm_range_invalid');
		} else {
			$pruneuser = ' AND (';
			$prunenew = $or = '';

			$prunedate = $timestamp - (86400 * $days);
			$arruser = explode(',', str_replace(' ', '', $users));
			for($i = 0; $i < count($arruser); $i++) {
				$arruser[$i] = trim($arruser[$i]);
				if($arruser[$i]) {
					$pruneuser .= $or."BINARY msgfrom='$arruser[$i]'";
					$or = ' OR ';
				}
			}
			if($pruneuser == ' AND (') {
				$pruneuser = '';
			} else {
				$pruneuser .= ')';
			}
			if($ignorenew) {
				$prunenew = " AND new='0'";
			}

			$db->query("DELETE FROM $table_pms WHERE dateline<='$prunedate' $pruneuser $prunenew");
			$num = $db->affected_rows();
			cpmsg('prune_pm_succeed');
		}
	}

}

?>