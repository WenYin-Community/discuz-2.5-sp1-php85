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
1 公告作者连接问题
2 accessmask的问题
*/

define('CURRSCRIPT',  'forumdisplay');

require './include/common.php';
require DISCUZ_ROOT.'./include/forum.php';

$discuz_action = 2;

if(isset($showoldetails)) {
	switch ($showoldetails) {
		case 'no': setcookie('onlinedetail', 0, $timestamp + 86400 * 365, $cookiepath, $cookiedomain); break;
		case 'yes': setcookie('onlinedetail', 1, $timestamp + 86400 * 365, $cookiepath, $cookiedomain); break;
	}
} else {
	$showoldetails = false;
}

if(empty($forum['fid']) || (!empty($forum['type']) && $forum['type'] == 'group') || ($adminid<>1 && empty($forum['status']))) {
	showmessage('forum_nonexistence', NULL, 'HALTED');
}

if($forum['type'] == 'forum') {
	$navigation = "&raquo; {$forum['name']}";
	$navtitle = ' - '.strip_tags($forum['name']);
} else {
	$forumup = $_DCACHE['forums'][$forum['fup']]['name'];
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid={$forum['fup']}\">$forumup</a> &raquo; {$forum['name']}";
	$navtitle = ' - '.strip_tags($forumup).' - '.strip_tags($forum['name']);
}

if($forum['password'] && $action == 'pwverify') {
	if($pw != $forum['password']) {
		showmessage('forum_passwd_incorrect', NULL, 'HALTED');
	} else {
		setcookie("fidpw$fid", $pw, 0, $cookiepath, $cookiedomain);
		showmessage('forum_passwd_correct', "forumdisplay.php?fid=$fid");
	}
}

if($forum['viewperm'] && !strstr($forum['viewperm'], "\t$groupid\t") && !$forum['allowview']) {
	showmessage('forum_nopermission', NULL, 'HALTED');
}

if(!empty($forum['password']) && $forum['password'] != $_COOKIE["fidpw$fid"]) {
	include template('forumdisplay_passwd');
	exit();
}

$ismoderator = modcheck($discuz_user);
$moderatedby = moddisplay($forum['moderator'], 'forumdisplay');

$postcredits = $forum['postcredits'] != -1 ? $forum['postcredits'] : $postcredits;
$replycredits = $forum['replycredits'] != -1 ? $forum['replycredits'] : $replycredits;

$subexists = 0;
foreach($_DCACHE['forums'] as $sub) {
	if($sub['type'] == 'sub' && $sub['fup'] == $fid && ($accessmasks || !$hideprivate || !$sub['viewperm'] || strstr($sub['viewperm'], "\t$groupid\t") || strstr($sub['users'], "\t$discuz_uid\t"))) {
		$sublist = array();
		$sql = $accessmasks	? "SELECT f.fid, f.fup, f.type, f.icon, f.name, f.description, f.moderator, f.threads, f.posts, f.lastpost, f.viewperm, a.allowview FROM $table_forums f
						LEFT JOIN $table_access a ON a.uid='$discuz_uid' AND a.fid=f.fid
						WHERE fup='$fid' AND status='1' AND type='sub' ORDER BY f.displayorder"
					: "SELECT fid, fup, type, icon, name, description, moderator, threads, posts, lastpost, viewperm FROM $table_forums WHERE fup='$fid' AND status='1' AND type='sub' ORDER BY displayorder";
		$query = $db->query($sql);
		while($sub = $db->fetch_array($query)) {
			if(forum($sub)) {
				$sublist[] = $sub;
				$subexists = 1;
			}
		}
		break;
	}
}

$page = intval($page) ? intval($page) : 1;
$start_limit = ($page - 1) * $tpp;

$announcement_list = array();
if($page == 1  && $_DCACHE['announcements_forum']) {
	foreach($_DCACHE['announcements_forum'] as $ann) {
		$ann['authorencode'] = rawurlencode($ann['author']);
		$ann['starttime'] = gmdate("$dateformat $timeformat", $ann['starttime'] + ($timeoffset * 3600));
		$announcement_list[] = $ann ;
	}
}	

$forumdisplayadd = $filteradd = '';
if(!empty($filter)) {
	if(is_numeric($filter)) {
		$forumdisplayadd .= "&filter=$filter";
		$filteradd = "AND lastpost>='".($timestamp - $filter)."'";
	} elseif($filter == 'digest') {
		$forumdisplayadd .= "&filter=digest";
		$filteradd = "AND digest>'0'";
	}
} else {
	$filter = '';
}

$ascdesc == 'ASC' ? $forumdisplayadd .= "&ascdesc=$ascdesc" : $ascdesc = 'DESC';


if($whosonlinestatus == 2 || $whosonlinestatus == 3) {
	$whosonlinestatus = 0;
	$onlineinfo = explode("\t", $onlinerecord);
	$detailstatus = ((!isset($_COOKIE['onlinedetail']) && $onlineinfo[0] < 500) || ($_COOKIE['onlinedetail'] || $showoldetails == 'yes')) && $showoldetails != 'no';

	if($detailstatus) {
		updatesession();
		@include language('actions');

		$whosonline = array();
		$forumname = strip_tags($forum['name']);
		$query = $db->query("SELECT uid, groupid, username, invisible, lastactivity, action FROM $table_sessions WHERE uid>'0' AND fid='$fid' AND invisible='0'");
		if($db->num_rows($query)) {
			$whosonlinestatus = 1;
			while($online = $db->fetch_array($query)) {
				$online['icon'] = isset($_DCACHE['onlinelist'][$online['groupid']]) ? $_DCACHE['onlinelist'][$online['groupid']] : $_DCACHE['onlinelist'][0];
				$online['action'] = $actioncode[$online['action']];
				$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + ($timeoffset * 3600));
				$whosonline[] = $online;     
			}
		}
		unset($online);
	}
} else {
	$whosonlinestatus = 0;
}

if($discuz_uid && $newpm) {
	require DISCUZ_ROOT.'./include/pmprompt.php';
}

if(!empty($filter)) {
	$query = $db->query("SELECT COUNT(*) FROM $table_threads WHERE fid='$fid' $filteradd");
	$threadcount = $db->result($query, 0);
} else {
	$threadcount = $forum['threads'];
}

$multipage = multi($threadcount, $tpp, $page, "forumdisplay.php?fid=$fid$forumdisplayadd");

$separatepos = 0;
$threadlist = array();
$colorarray = array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');
$query = $db->query("SELECT  t.* FROM $table_threads t USE INDEX(displayorder)
			WHERE t.fid='$fid' $filteradd 
			ORDER BY t.displayorder DESC, t.lastpost $ascdesc
			LIMIT $start_limit, $tpp ");

while($thread = $db->fetch_array($query)) {
	$thread['icon'] = isset($_DCACHE['icons'][$thread['iconid']]) ? '<img src="'.SMDIR.'/'.$_DCACHE['icons'][$thread['iconid']].'" align="absmiddle">' : '&nbsp;';
	$thread['lastposterenc'] = rawurlencode($thread['lastposter']);

	$postsnum = $thread['replies'] + 1;
	if($postsnum  > $ppp) {
		$pagelinks = '';
		$topicpages = ceil($postsnum / $ppp);
		for ($i = 1; $i <= $topicpages; $i++) {
			$pagelinks .= "<a href=\"viewthread.php?tid={$thread['tid']}&page=$i&fpage=$page\">$i</a> ";
			if($i == 6) {
				$i = $topicpages + 1;
			}
		}
		if($topicpages > 6) {
			$pagelinks .= " .. <a href=\"viewthread.php?tid={$thread['tid']}&page=$topicpages&fpage=$page\">$topicpages</a> ";
		}
		$thread['multipage'] = '&nbsp; &nbsp;( <img src="'.IMGDIR.'/multipage.gif" align="absmiddle" boader="0"> '.$pagelinks.')';
	} else {
		$thread['multipage'] = '';
	}

	if($thread['highlight']) {
		$string = sprintf('%02d', $thread['highlight']);
		$stylestr = sprintf('%03b', $string[0]);

		$thread['highlight'] = 'style="';
		$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
		$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
		$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
		$thread['highlight'] .= $string[1] ? 'color: '.$colorarray[$string[1]] : '';
		$thread['highlight'] .= '"';
	} else {
		$thread['highlight'] = '';
	}

	if($thread['closed']) {
		$thread['new'] = 0;
		if($thread['closed'] > 1) {
			$thread['moved'] = $thread['tid'];
			$thread['tid'] = $thread['closed'];
			$thread['replies'] = '-';
			$thread['views'] = '-';
		} else {
			$thread['moved'] = 0;
		}
		$thread['folder'] = 'lock_folder.gif';
	} else {
		$thread['folder'] = 'folder.gif';
		if($lastvisit < $thread['lastpost'] && !strstr($_COOKIE['oldtopics'], "\t{$thread['tid']}\t")) {
			$thread['new'] = 1;
			$thread['folder'] = 'red_'.$thread['folder'];
		} else {
			$thread['new'] = 0;
		}
		if($thread['replies'] > $thread['views']) {
			$thread['views'] = $thread['replies'];
		}
		if($thread['replies'] >= $hottopic) {
			$thread['folder'] = 'hot_'.$thread['folder'];
		}
	}

	if($thread['attachment']) {
		require_once DISCUZ_ROOT.'./include/attachment.php';
		$thread['attachment'] = attachtype($thread['attachment']).' ';
	} else {
		$thread['attachment'] = '';
	}
	$thread['subject'] = cutstr($thread['subject'],77);
	$thread['dateline'] = gmdate($dateformat, $thread['dateline'] + $timeoffset * 3600);
	$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);

	if($thread['displayorder'] > 0) {
		$separatepos++;
	}
	$threadlist[] = $thread;

}

$check[$filter] = 'selected="selected"';
$ascdesc == 'ASC' ? $check['asc'] = 'selected="selected"' : $check['desc'] = 'selected="selected"';

$forumselect = $forumjump ? forumselect() : NULL;

$usesigcheck = $signature ? 'checked' : NULL;
$allowpost = (!$forum['postperm'] && $allowpost) || ($forum['postperm'] && strstr($forum['postperm'], "\t$groupid\t")) || $forum['allowpost'];

include template('forumdisplay');

?>