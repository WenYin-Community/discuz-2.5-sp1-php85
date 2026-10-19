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
1 对分割后的主题缺少html转义处理
2 跳转的url参数问题
3 批量删除主题时，没有删除对应附件
*/

require './include/common.php';
require_once DISCUZ_ROOT.'./include/post.php';

$discuz_action = 151;

$tid   = $tid ? $tid :'';
$page  = intval($page);
$fpage = intval($fpage);

if(!isset($thread) || !is_array($thread)) {
	$thread = array('subject' => '');
}
if($tid) {
	$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
	$thread = $db->fetch_array($query);
	if(!is_array($thread)) {
		$thread = array('subject' => '');
	}
	$thread['subject'] .= $action == 'delthread' ? ", etc." : NULL;
}

if(!empty($forum['type']) && $forum['type'] == 'forum') {
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid&page=$fpage\">{$forum['name']}</a> &raquo; <a href=\"viewthread.php?tid=$tid\">{$thread['subject']}</a> ";
	$navtitle = ' - '.strip_tags($forum['name']).' - '.$thread['subject'];
} else {
	$query = $db->query("SELECT name, fid, moderator FROM $table_forums WHERE fid='{$forum['fup']}'");
	$fup = $db->fetch_array($query);
	if(!is_array($fup)) {
		$fup = array('fid' => '', 'name' => '');
	}
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid={$fup['fid']}\">{$fup['name']}</a> &raquo; <a href=\"forumdisplay.php?fid=$fid&page=$fpage\">{$forum['name']}</a> &raquo; <a href=\"viewthread.php?tid=$tid\">{$thread['subject']}</a> ";
	$navtitle = ' - '.strip_tags($fup['name']).' - '.strip_tags($forum['name']).' - '.$thread['subject'];
}

if(!$discuz_user || !$discuz_pw || !modcheck($discuz_user)) {
	showmessage('admin_nopermission', NULL, 'HALTED');
}

$fupadd = $fup ? "OR (fid='{$fup['fid']}' && type<>'group')" : NULL;

if($action == 'moderate') {
	if(!is_array($moderate) || !count($moderate)) {
		showmessage('admin_moderate_nothread');
	}elseif(!$operation){
		showmessage('admin_moderate_nooperation');
	}
	$tids = implode_ids($moderate);
	$query = $db->query("SELECT * FROM $table_threads WHERE tid IN($tids) ");
	if(!submitcheck('moderatesubmit')){
		$threadlist = array();
		while($thread = $db->fetch_array($query)) {
			if($thread['fid'] == $fid){
				$thread['lastposterenc'] = rawurlencode($thread['lastposter']);
				if($thread['attachment']) {
					require_once DISCUZ_ROOT.'./include/attachment.php';
					$thread['attachment'] = attachtype($thread['attachment']).' ';
				} else {
					$thread['attachment'] = '';
				}
				$thread['subject'] = cutstr($thread['subject'],77);
				$thread['dateline'] = gmdate($dateformat, $thread['dateline'] + $timeoffset * 3600);
				$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);
				$threadlist[] = $thread;
			}
		}
		
		if(!$threadlist) showmessage('admin_moderate_nothread');

		if($operation == 'move') {
			require_once DISCUZ_ROOT.'./include/forum.php';
			$forumselect = forumselect();
		}
		include template('topicadmin_moderate');
	
	}else{
	
		if($operation == 'move' && $allowmove) {
			if(!$moveto) {
				showmessage('admin_move_invalid');
			}

			accesscheck($query);

			$displayorderadd = !$adminglobal ? ", displayorder='0'" : NULL;

			$db->query("UPDATE $table_threads SET fid='$moveto' $displayorderadd WHERE tid IN($tids)");
			$db->query("UPDATE $table_posts SET fid='$moveto' WHERE tid IN($tids) ");

			if ($forum['type'] == 'sub') {
				$query= $db->query("SELECT fup FROM $table_forums WHERE fid='$fid' LIMIT 1");
				$fup = $db->result($query, 0);
				updateforumcount($fup);
			}
			modlog();
			updateforumcount($moveto);
			updateforumcount($fid);
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$page");

		}elseif($operation == 'stick' && $allowtop){
			accesscheck($query);
			if($level < 0 || $level > 3) {
				showmessage('undefined_action');
			}
			$db->query("UPDATE $table_threads SET displayorder='$level' WHERE tid IN ($tids)");
			modlog();
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$page");

		}elseif($operation == 'delete' && $allowdelpost) {
			accesscheck($query);
			$uids = $comma = '';
			$haveattach = 0;
			$query = $db->query("SELECT authorid ,aid FROM $table_posts WHERE tid IN ($tids)");
			while($post = $db->fetch_array($query)) {
				$uids .= "$comma{$post['authorid']}";
				$comma = ',';
				if ($post['aid']) $haveattach++;
			}
			updatemember('-', $uids, $deletedcredits);

			if ($haveattach){
				$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid IN ($tids)");
				while($attach = $db->fetch_array($query)) {
					@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
				}
				$db->query("DELETE FROM $table_attachments WHERE tid IN ($tids)");
			}
			
			$db->query("DELETE FROM $table_threads WHERE tid IN ($tids)");
			$db->query("DELETE FROM $table_polls WHERE tid IN ($tids)");
			$db->query("DELETE FROM $table_posts WHERE tid IN ($tids)");

			updateforumcount($fid);

			modlog();
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$page");

		}elseif($operation == 'close' && $allowclose){
			accesscheck($query);
			$close = $type ? 1 : 0;
			$db->query("UPDATE $table_threads SET closed='$close' WHERE tid in($tids)");
			modlog();
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$page");

		}elseif($operation == 'digest' && $allowdigest){
			if($level < 0 || $level > 3) {
				showmessage('undefined_action', NULL, 'HALTED');
			}
			while($thread = $db->fetch_array($query)) {
				if ($thread['fid'] == $fid && $thread['digest']<>$level){
					$digest_mark=($level-intval($thread['digest']))*$digestcredits;
					$db->query("UPDATE $table_threads SET digest='$level' WHERE tid='{$thread['tid']}'");
					if($digest_mark && $discuz_uid != $thread['authorid']) {
						$db->query("UPDATE $table_members SET credit=credit".($digest_mark > 0 ? '+' : '')."$digest_mark WHERE uid='{$thread['authorid']}'");
					}
				}
			}
			modlog();
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$page");
		}else{
			showmessage('admin_nopermission', NULL, 'HALTED');
		}
	}

} elseif($action == 'delpost' && $allowdelpost) {

	if(!is_array($delete) || !count($delete)) {
		showmessage('admin_delpost_invalid');
	}

	if(!submitcheck('delpostsubmit')) {

		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid'");
		if(count((array)$delete) < $db->result($query, 0)) {

			$deleteid = '';
			foreach($delete as $id) {
				$deleteid .= '<input type="hidden" name="delete[]" value="'.$id.'">';
			}

			include template('topicadmin_delpost');
			
		} else {
			header("Location: {$boardurl}topicadmin.php?action=delete&fid=$fid&tid=$tid&page=$page&fpage=$fpage");
		}

	} else {

		$pids = implode_ids( $delete );

		$uids = $comma = '';
		$actionpost = $totalpost = $aids = 0;
		$query = $db->query("SELECT pid, authorid, aid FROM $table_posts WHERE tid='$tid'");
		$totalpost = $db->num_rows($query);
		while($post = $db->fetch_array($query)) {
			if (in_array($post['pid'], $delete)){
				$uids .= "$comma{$post['authorid']}";
				$comma = ',';
				$actionpost ++;
				if ($post['aid']) $aids .=','.$post['pid'];
			}
		}

		if ($actionpost < 1) {
			showmessage('admin_delpost_invalid');
		}elseif($actionpost <> count((array)$delete)){
			showmessage('admin_moderate_accesserror', NULL, 'HALTED');
		}elseif($actionpost >= $totalpost ){
			header("Location: {$boardurl}topicadmin.php?action=delete&fid=$fid&tid=$tid&page=$page&fpage=$fpage");
		}
		
		updatemember('-', $uids, $deletedcredits);

		if ($aids){
			require_once DISCUZ_ROOT.'./include/attachment.php';
			$query = $db->query("SELECT pid, attachment, filetype FROM $table_attachments WHERE pid IN ($aids)");
			while($attach = $db->fetch_array($query)) {
					@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
			}
			$db->query("DELETE FROM $table_attachments WHERE pid IN ($aids)");
			updatethread_type($tid , $thread['attachment']);
		}

		$db->query("DELETE FROM $table_posts WHERE pid IN ($pids)");
		updatethreadcount($tid);
		updateforumcount($fid);

		modlog();
		showmessage('admin_succeed', "viewthread.php?tid=$tid&page=$page&fpage=$fpage");

	}

} elseif($action == 'highlight' && $allowhighlight) {
	if(!submitcheck('highlightsubmit')) {

		$string = sprintf('%02d', $thread['highlight']);
		$stylestr = sprintf('%03b', $string[0]);

		for($i = 1; $i <= 3; $i++) {
			$stylecheck[$i] = $stylestr[$i - 1] ? 'checked' : NULL;
		}
		$colorcheck = array($string[1] => 'checked');
		
		include template('topicadmin_highlight');

	} else {

		$stylebin = '';
		for($i = 1; $i <= 3; $i++) {
			$stylebin .= empty($highlight_style[$i]) ? '0' : '1';
		}
		$highlight_style = bindec($stylebin);

		if($highlight_style < 0 || $highlight_style > 7 || $highlight_color < 0 || $highlight_color > 8) {
			showmessage('undefined_action', NULL, 'HALTED');
		}
		$db->query("UPDATE $table_threads SET highlight='$highlight_style$highlight_color' WHERE tid='$tid'");

		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} elseif($action == 'digest' && $allowdigest) {
	if(!submitcheck('digestsubmit')) {

		include template('topicadmin_digest');

	} else {

		if($level < 0 || $level > 3) {
			showmessage('undefined_action', NULL, 'HALTED');
		}
		$digest_mark=($level-intval($thread['digest']))*$digestcredits;

		$db->query("UPDATE $table_threads SET digest='$level' WHERE tid='$tid'");

		if($digest_mark && $discuz_uid != $thread['authorid'] ) {
			$db->query("UPDATE $table_members SET credit=credit".($digest_mark > 0 ? '+' : '')."$digest_mark WHERE uid='{$thread['authorid']}'");
		}
		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} elseif($action == 'recount') {

	$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid'");
	$replies = $db->result($query, 0) - 1;

	$query  = $db->query("SELECT author, dateline FROM $table_posts WHERE tid='$tid' ORDER BY dateline DESC LIMIT 1");
	$post = $db->fetch_array($query);

	$db->query("UPDATE $table_threads SET replies='$replies', lastpost='{$post['dateline']}', lastposter='".addslashes($post['author'])."' WHERE tid='$tid'");
	showmessage('admin_succeed', "viewthread.php?tid=$tid&fpage=$fpage");

} elseif($action == 'delete'  && $allowdelpost) {
	if(!submitcheck('deletesubmit')) {

		include template('topicadmin_delete');

	} else {

		$uids = $comma = '';
		$query = $db->query("SELECT authorid,aid FROM $table_posts WHERE tid='$tid'");
		$aids = 0;
		while($post = $db->fetch_array($query)) {
			$uids .= "$comma{$post['authorid']}";
			$comma = ',';
			if ($post['aid']) $aids++;
		}
		updatemember('-', $uids, $deletedcredits);

		$db->query("DELETE FROM $table_threads WHERE tid='$tid'");
		$db->query("DELETE FROM $table_posts WHERE tid='$tid'");
		
		if ($aids){
			$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid='$tid'");
			while($attach = $db->fetch_array($query)) {
				@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$attach['attachment']);
			}
			$db->query("DELETE FROM $table_attachments WHERE tid='$tid'");
		}
		
		updateforumcount($fid);
		if ($forum['type'] == 'sub') {
			updateforumcount($fup['fid']);
		}

		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} elseif($action == 'close' && $allowclose) {

	if(!submitcheck('closesubmit')) {

		include template('topicadmin_openclose');

	} else {
		$openclose = $thread['closed'] ? 0 : 1;
		$db->query("UPDATE $table_threads SET closed='$openclose' WHERE tid='$tid' AND fid='$fid'");
		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");
	}

} elseif($action == 'move'  && $allowmove) {

	if(!submitcheck('movesubmit')) {

		require_once DISCUZ_ROOT.'./include/forum.php';

		$forumselect = forumselect();
		include template('topicadmin_move');

	} else {

		if(!$moveto) {
			showmessage('admin_move_invalid');
		}

		$displayorderadd = !$adminglobal ? ", displayorder='0'" : NULL;
		if($type == 'normal') {
			$db->query("UPDATE $table_threads SET fid='$moveto' $displayorderadd WHERE tid='$tid' AND fid='$fid'");
			$db->query("UPDATE $table_posts SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
		} else {
			$db->query("INSERT INTO $table_threads (fid, creditsrequire, iconid, author, authorid, subject, dateline, lastpost, lastposter, views, replies, displayorder, digest, closed, poll, attachment)
				VALUES ('{$thread['fid']}', '{$thread['creditsrequire']}', '{$thread['iconid']}', '".addslashes($thread['author'])."', '{$thread['authorid']}', '{$thread['subject']}', '{$thread['dateline']}', '{$thread['lastpost']}', '{$thread['lastposter']}', '0', '0', '0', '0', '{$thread['tid']}', '0', '0')");

			$db->query("UPDATE $table_threads SET fid='$moveto' $displayorderadd WHERE tid='$tid' AND fid='$fid'");
			$db->query("UPDATE $table_posts SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
		}

		if ($forum['type'] == 'sub') {
			$query= $db->query("SELECT fup FROM $table_forums WHERE fid='$fid' LIMIT 1");
			$fup = $db->result($query, 0);
			updateforumcount($fup);
		}

		modlog();
		updateforumcount($moveto);
		updateforumcount($fid);
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");
	}

} elseif($action == 'top' && $allowtop) {

	if(!submitcheck('topsubmit')) {

		include template('topicadmin_topuntop');

	} else {

		if($level < 0 || $level > 3) {
			showmessage('undefined_action');
		}
		$db->query("UPDATE $table_threads SET displayorder='$level' WHERE tid='$tid' AND fid='$fid'");

		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} elseif($action == 'getip' && $allowviewip) {

	require_once DISCUZ_ROOT.'./include/misc.php';

	$query = $db->query("SELECT m.adminid, p.useip FROM $table_posts p
				LEFT JOIN $table_members m ON m.uid=p.authorid
				WHERE pid='$pid' AND tid='$tid'");
	if(!$member = $db->fetch_array($query)) {
		showmessage('thread_nonexistence', NULL, 'HALTED');
	} elseif(($member['adminid'] == 1 && $adminid > 1) || ($member['adminid'] == 2 && $adminid > 2)) {
		showmessage('admin_getip_nopermission', NULL, 'HALTED');
	}

	$member['iplocation'] = convertip($member['useip']);

	include template('topicadmin_getip');

} elseif($action == 'bump') {

	if(!submitcheck('bumpsubmit')) {

		include template('topicadmin_bump');

	} else {

		$query = $db->query("SELECT subject, lastposter, lastpost FROM $table_threads WHERE tid='$tid' LIMIT 1");
		$thread = $db->fetch_array($query);
		$thread['lastposter'] = addslashes($thread['lastposter']);
		$db->query("UPDATE $table_threads SET lastpost='$timestamp' WHERE tid='$tid' AND fid='$fid'");
		$db->query("UPDATE $table_forums SET lastpost='{$thread['subject']}\t$timestamp\t{$thread['lastposter']}' WHERE fid='$fid' $fupadd");

		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} elseif($action == 'split' && $allowsplit) {

	require_once DISCUZ_ROOT.'./include/discuzcode.php';
	if(!submitcheck('splitsubmit')) {


		$replies = $thread['replies'];
		if($replies <= 0) {
			showmessage('admin_split_invalid');
		}

		$postlist = array();
		$query = $db->query("SELECT * FROM $table_posts WHERE tid='$tid' ORDER BY dateline");
		while($post = $db->fetch_array($query)) {
			$post['message'] = postify($post['message'], $post['smileyoff'], $post['bbcodeoff']);
			$postlist[] = $post;
		}

		include template('topicadmin_split');

	} else {

		if(!trim($subject)) {
			showmessage('admin_split_subject_invalid');
		}
		$subject = $subject ? dhtmlspecialchars(censor(trim($subject))) :'';

		$pids = implode_ids( $split );

		if($pids) {

			$db->query("INSERT INTO $table_threads (fid, subject) VALUES ('$fid', '$subject')");
			$newtid = $db->insert_id();

			$db->query("UPDATE $table_posts SET tid='$newtid' WHERE pid IN ($pids)");
			$db->query("UPDATE $table_attachments SET tid='$newtid' WHERE pid IN ($pids)");

			$query = $db->query("SELECT author, authorid, dateline FROM $table_posts WHERE tid='$tid' ORDER BY dateline ASC LIMIT 1");
			$fpost = $db->fetch_array($query);
			$db->query("UPDATE $table_threads SET author='{$fpost['author']}', authorid='{$fpost['authorid']}', dateline='{$fpost['dateline']}' WHERE tid='$tid'");

			$query = $db->query("SELECT author, authorid, dateline FROM $table_posts WHERE tid='$newtid' ORDER BY dateline ASC LIMIT 1");
			$fpost = $db->fetch_array($query);
			$db->query("UPDATE $table_threads SET author='{$fpost['author']}', authorid='{$fpost['authorid']}', dateline='{$fpost['dateline']}' WHERE tid='$newtid'");

			updatethreadcount($tid);
			updatethreadcount($newtid);
			updateforumcount($fid);

			modlog();
			showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

		} else {
			showmessage('admin_split_new_invalid');
		}
	}

} elseif($action == 'merge' && $allowmerge) {

	if(!submitcheck('mergesubmit')) {

		include template('topicadmin_merge');

	} else {

		$query = $db->query("SELECT fid, views, replies FROM $table_threads WHERE tid='$othertid'");
		if(!$other = $db->fetch_array($query)) {
			showmessage('admin_merge_nonexistence');
		}
		if(!$adminglobal && $other['fid'] != $forum['fid']) {
			showmessage('admin_merge_invalid');
		}

		$other['views'] = intval($other['views']);
		$other['replies']++;

		$db->query("UPDATE $table_posts SET tid='$tid' WHERE tid='$othertid'");
		$postsmerged = $db->affected_rows();

		$db->query("UPDATE $table_attachments SET tid='$tid' WHERE tid='$othertid'");
		$db->query("DELETE FROM $table_threads WHERE tid='$othertid'");
		$db->query("UPDATE $table_threads SET views=views+{$other['views']}, replies=replies+{$other['replies']} WHERE tid='$tid'");
		
		if($fid == $other['fid']) {
			$db->query("UPDATE $table_forums SET threads=threads-1 WHERE fid='$fid' $fupadd");
		} else {
			$db->query("UPDATE $table_forums SET threads=threads-1, posts=posts-$postsmerged WHERE fid='{$other['fid']}'");
			$db->query("UPDATE $table_forums SET posts=$posts+$postsmerged WHERE fid='$fid' $fupadd");
		}

		modlog();
		showmessage('admin_succeed', "forumdisplay.php?fid=$fid&page=$fpage");

	}

} else {

	showmessage('admin_nopermission', NULL, 'HALTED');

}

function modlog($action = '') {
	global $discuz_user, $groupid, $adminid, $onlineip, $timestamp, $forum, $thread, $operation, $tids;

	if(!$action) {
		$action = $GLOBALS['action'];
	}
	if (!$thread['tid']) $thread['tid']=intval($GLOBALS['tid']);
	if ($action == 'moderate'){
		$action .='_'.$operation;
		$thread['subject'] = $tids;
		$thread['tid'] = 0;
	}
	@$fp = fopen(DISCUZ_ROOT.'./forumdata/modslog.php', 'a');
	@flock($fp, 2);
	@fwrite($fp, "$timestamp\t$discuz_user\t$groupid\t$onlineip\t{$forum['fid']}\t{$forum['name']}\t{$thread['tid']}\t{$thread['subject']}\t$action\n");
	@fclose($fp);
}

function accesscheck($query) {
	global $db,$fid;
	while($thread = $db->fetch_array($query)) {
		if($thread['fid'] <> $fid){
			showmessage('admin_moderate_accesserror', NULL, 'HALTED');
		}
	}
}

?>