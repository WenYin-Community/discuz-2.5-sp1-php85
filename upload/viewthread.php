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
1 发帖数衡量级别
2 转入最新帖
*/

define('CURRSCRIPT',  'viewthread');

require_once './include/common.php';
require_once DISCUZ_ROOT.'./include/forum.php';
require_once DISCUZ_ROOT.'./include/discuzcode.php';

$discuz_action = 3;

if ($tid && $forum){
	$thread = $forum;
}elseif($fid && defined('ViewLastPost')) {
	$followforum = '';
	foreach($_DCACHE['forums'] as $ffid => $fforum) {
		if($fforum['fup'] == $fid && $fforum['type'] == 'sub') {
			$followforum .= ','.$ffid;
		}
	}
	$followforumadd = $followforum ? "fid in ($fid{$followforum})" : "fid=$fid";
	$query = $db->query("SELECT * FROM $table_threads WHERE $followforumadd ORDER BY lastpost DESC LIMIT 1");
	if(!$thread = $db->fetch_array($query)) {
		showmessage('thread_nonexistence');
	}
	header("Location: {$boardurl}viewthread.php?tid=$thread[tid]&sid=$sid");
	exit();
} else {
	showmessage('thread_nonexistence');
}

if (defined('ViewLastPost')) {
		$page = @ceil(($thread['replies'] + 1) / $ppp);
}

$tid = $thread['tid'];
$codecount = 0;
$oldtopics = $_COOKIE['oldtopics'] ? $_COOKIE['oldtopics'] : "\t";
if(!strstr($oldtopics, "\t$tid\t")) {
	$oldtopics .= "$tid\t";
	setcookie('oldtopics', $oldtopics, $timestamp + 900, $cookiepath, $cookiedomain);
}

if($forum['type'] == 'forum') {
	$navigation .= "&raquo; <a href=\"forumdisplay.php?fid=$fid&page=$fpage\"> $forum[name]</a> &raquo; $thread[subject]";
	$navtitle .= ' - '.strip_tags($forum['name']).' - '.$thread['subject'];
} else {
	$query = $db->query("SELECT fid, name, moderator FROM $table_forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation .= "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid&page=$fpage\"> $forum[name]</a> &raquo; $thread[subject]";
	$navtitle .= ' - '.strip_tags($fup['name']).' - '.strip_tags($forum['name']).' - '.$thread['subject'];
}

$ismoderator = modcheck($discuz_user);

if(!$forum['allowview']) {
	if(!$forum['viewperm'] && !$allowview) {
		showmessage('group_nopermission', NULL, 'HALTED');
	} elseif($forum['viewperm'] && !strstr($forum['viewperm'], "\t$groupid\t")) {
		showmessage('forum_nopermission', NULL, 'HALTED');
	}
}

if($thread['creditsrequire'] && $thread['creditsrequire'] > $credit && !$ismoderator && ($thread['authorid'] !=$discuz_uid)) {
	showmessage('thread_nopermission', NULL, 'HALTED');
}

if($forum['password'] && $forum['password'] != $_COOKIE["fidpw$fid"]) {
	header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	exit();
}

if(!$action && $tid) {

	if($discuz_uid && $newpm) {
		require DISCUZ_ROOT.'./include/pmprompt.php';
	}

	$highlightstatus = str_replace('+', '', $highlight) ? 1 : 0;
	$karmaoptions = '';
	if($allowkarma && $maxkarmarate) {
		$offset = ceil($maxkarmarate / 6);
		for($vote = - $maxkarmarate + $offset; $vote <= $maxkarmarate; $vote += $offset) {
			$votenum = $vote > 0 ? '+'.$vote : $vote;
			$karmaoptions .= $vote ? "<option value=\"$vote\">$votenum</option>\n" : NULL;
		}
	}
	unset($vote, $votenum, $offset);
	
	$pageMax = ceil(($thread['replies']+1) / $ppp);
	$page = intval($page) ? intval($page) : 1;
	$page = $page > $pageMax ? $pageMax : $page; 
	$start_limit = ($page - 1) * $ppp;



	$fpage = intval($fpage);
	$multipage = multi($thread['replies'] + 1, $ppp, $page, "viewthread.php?tid=$tid&fpage=$fpage&highlight=".rawurlencode($highlight));

	$thread[subject] = cutstr($thread[subject],77);
	$polloptions = array();
	if($thread['poll']) {
		$query = $db->query("SELECT pollopts FROM $table_polls WHERE tid='$tid'");
		$pollopts = unserialize($db->result($query, 0));
		if (is_array($pollopts) && count($pollopts)){
			foreach($pollopts['options'] as $option) {
				$polloptions[] = array(	
					'option'	=> dhtmlspecialchars(stripslashes($option[0])),
					'votes'		=> $option[1],
					'width'		=> @round($option[1] * 300 / $pollopts['max']) + 2,
					'percent'	=> @sprintf ("%01.2f", $option[1] * 100 / $pollopts['total'])
				);
			}
			$allowvote = $allowvote && $discuz_uid && (empty($thread['closed']) || $alloweditpoll) && !in_array($discuz_user, $pollopts['voters']);
			$optiontype = $pollopts['multiple'] ? 'checkbox' : 'radio';
		}else{
			unset ($pollopts);
		}
	}

	$altbg1 = ALTBG1;
	$altbg2 = ALTBG2;
	$postlist = $attachelist = array();
	$newpostanchor = $topiccount = $have_attachment = 0;
	$postcount = $start_limit; 
	$post_phpcodecount = -1;
	$post_user_info_sql = 'm.username, m.gender, m.groupid, m.adminid, m.regdate, m.lastactivity, m.postnum, m.credit, m.email, m.site, m.icq, m.oicq, m.yahoo, m.msn, m.location, m.avatar, m.avatarwidth, m.avatarheight, m.signature, m.customstatus, m.showemail'; 

	$query = $db->query("
					SELECT p.*, $post_user_info_sql 
					FROM $table_posts p
					LEFT JOIN $table_members m ON m.uid=p.authorid
					WHERE p.tid='$tid' ORDER BY dateline LIMIT $start_limit, $ppp");

	while($post = $db->fetch_array($query)) {

		if(!$newpostanchor && $post['dateline'] > $lastvisit) {
			$post['newpostanchor'] = '<a name="newpost"></a>';
			$newpostanchor = 1;
		} else {
			$post['newpostanchor'] = '';
		}
		$topiccount ++;
		$post['postcount'] = $postcount+1;
		$post['thisbg'] = ${'altbg'.($postcount++ % 2 + 1)};
		$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + $timeoffset * 3600);

		if($post['username']) {
			
			if($userstatusby > 0 ){
				if($userstatusby == 2 && $post['adminid'] == 0) {
					foreach($_DCACHE['ranks'] as $rank) {
						if($post['postnum'] > $rank['postshigher']) {
							$post['authortitle'] = $rank['ranktitle'];
							$post['stars'] = $rank['stars'];
							break;
						}
					}
				}else{
					$post['authortitle'] = $_DCACHE['usergroups'][$post['groupid']]['grouptitle'];
					$post['stars'] = $_DCACHE['usergroups'][$post['groupid']]['stars'];
				}
			}

			$post['regdate'] = gmdate($dateformat, $post['regdate'] + $timeoffset * 3600);

			if($_DCACHE['usergroups'][$post['groupid']]['groupavatar']) {
				$post['avatar'] = '<img src="'.$_DCACHE['usergroups'][$post['groupid']]['groupavatar'].'" border="0">';
			} elseif($_DCACHE['usergroups'][$post['groupid']]['allowavatar'] && $post['avatar']) {
				$post['avatar'] = '<img src="'.$post['avatar'].'" width="'.$post['avatarwidth'].'" height="'.$post['avatarheight'].'" border="0">';
			} else {
				$post['avatar'] = '';
			}
		} else {
			if(!$post['authorid']) {
				$post['useip'] = substr($post['useip'], 0, strrpos($post['useip'], '.')).'.x';
			}
			$post['postnum'] = $post['credit'] = $post['regdate'] = 'N/A';
		}

		$post['karma'] = '';
		if($post['rate'] && $post['ratetimes']) {
			$rateimg = $post['rate'] > 0 ? 'agree.gif' : 'disagree.gif';
			for($i = 0; $i < round(abs($post['rate']) / $post['ratetimes']); $i++) {
				$post['karma'] .= '<img src="'.IMGDIR.'/'.$rateimg.'" align="right">';
			}
		}

		$post['subject'] = $post['subject'] ? $post['subject'] : NULL;
		$post['message'] = postify($post['message'], $post['smileyoff'], $post['bbcodeoff'], $forum['allowsmilies'], $forum['allowhtml'], $forum['allowbbcode'], $forum['allowimgcode']);
		$post['signature'] = $post['usesig'] && $post['signature'] ? postify($post['signature'], 1, 0, 0, 0, $_DCACHE['usergroups'][$post['groupid']]['allowsigbbcode'], $_DCACHE['usergroups'][$post['groupid']]['allowsigimgcode']) : NULL;

		if(!$have_attachment && $post['aid']) $have_attachment = 1 ;

		$postlist[] = $post;
	}
	
	if (empty($postlist)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if ($have_attachment) {
		require_once DISCUZ_ROOT.'./include/attachment.php';
		$query = $db->query("select * from $table_attachments where tid='$tid' ORDER BY aid");
		while($attach = $db->fetch_array($query)) {
			$attach[checkid] = substr(md5($attach['filesize']),0,5);
			$extension = strtolower(fileext($attach['filename']));
			$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
			if($attachimgpost && in_array($extension, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp'))) {
				$attach['attachimg'] = 1;
			} else {
				$attach['attachimg'] = 0;
			}
			$attach['attachsize'] = sizecount($attach['filesize']);
			$attach['dateline'] = $attach['dateline']?gmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600): '';
			$attachelist["$attach[pid]"][] = $attach;
		}
	}

	$forumselect = $forumjump ? forumselect() : NULL;

	$usesigcheck = $signature ? 'checked' : NULL;
	$allowpost = (!$forum['postperm'] && $allowpost) || ($forum['postperm'] && strstr($forum['postperm'], "\t$groupid\t")) || $forum['allowpost'];
	$allowpostreply = (!$thread['closed'] || $ismoderator) && (!$delayreply || ($delayreply && (($timestamp - $thread[dateline] ) < $delayreply * 86400)) || $ismoderator) && ((!$forum['replyperm'] && $allowpost) || ($forum['replyperm'] && strstr($forum['replyperm'], "\t$groupid\t")) || $forum['allowreply']);


	if($delayviewcount) {
		$logfile = DISCUZ_ROOT.'./forumdata/viewcount.log';
		if(substr($timestamp, -2) == '00') {
			require DISCUZ_ROOT.'./include/misc.php';
			updateviews();
		}

		if(@$fp = fopen($logfile, 'a')) {
			fwrite($fp, "$tid\n");
			fclose($fp);
		}elseif($adminid == 1) {
			showmessage('view_log_invalid');
		}
	} else {
		$db->query("UPDATE LOW_PRIORITY $table_threads SET views=views+1 WHERE tid='$tid'", 'UNBUFFERED');
	}

	include template('viewthread');

} elseif($action == 'printable' && $tid) {

	require DISCUZ_ROOT.'./include/printable.php';

}

?>