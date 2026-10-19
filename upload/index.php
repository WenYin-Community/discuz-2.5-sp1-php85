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
1 ¿Õ°×ÂÛÌ³ÎÊÌâ
*/

define('CURRSCRIPT',  'index');

require './include/common.php';
require DISCUZ_ROOT.'./include/forum.php';

$discuz_action = 1;

if(isset($showoldetails)) {
	switch ($showoldetails) {
		case 'no': setcookie('onlinedetail', 0, $timestamp + 86400 * 365, $cookiepath, $cookiedomain); break;
		case 'yes': setcookie('onlinedetail', 1, $timestamp + 86400 * 365, $cookiepath, $cookiedomain); break;
	}
} else {
	$showoldetails = false;
}

$currenttime = gmdate($timeformat, $timestamp + $timeoffset * 3600);
$lastvisittime = gmdate("$dateformat $timeformat", $lastvisit + $timeoffset * 3600);

$memberenc = rawurlencode($lastmember);
$newthreads = round(($timestamp - $lastvisit + 600) / 1000) * 1000;

if(empty($gid)) {

	$navigation = $navtitle = '';

	$announcements = '';
	if($_DCACHE['announcements']) {
		$space = '';
		foreach($_DCACHE['announcements'] as $announcement) {
			if($timestamp >= $announcement['starttime'] && ($timestamp <= $announcement['endtime'] || !$announcement['endtime'])) {
				if ($announcement['posturl']){
					$announcements .= $space.'<a href="'.$announcement['posturl'].'" target="_blank"><span class="bold">'.$announcement['subject'].'</span> '.
					'('.gmdate($dateformat, $announcement['starttime'] + $timeoffset * 3600).')</a>';
				}else{
					$announcements .= $space.'<a href="announcement.php?id='.$announcement['id'].'#'.$announcement['id'].'" target="_blank"><span class="bold">'.$announcement['subject'].'</span> '.
					'('.gmdate($dateformat, $announcement['starttime'] + $timeoffset * 3600).')</a>';
				}
				$space = '&nbsp; &nbsp; &nbsp; &nbsp;';
			}
		}
	}
	unset($_DCACHE['announcements']);

	$threads = $posts = 0;
	$forumlist = $catforumlist = $forums = $catforums = $categories = array();

	$sql = $accessmasks	? "SELECT f.fid, f.fup, f.type, f.icon, f.name, f.description, f.moderator, f.threads, f.posts, f.lastpost, f.viewperm, a.allowview FROM $table_forums f
					LEFT JOIN $table_access a ON a.uid='$discuz_uid' AND a.fid=f.fid
					WHERE f.status='1' ORDER BY f.type, f.displayorder"
				: "SELECT fid, fup, type, icon, name, description, moderator, threads, posts, lastpost, viewperm FROM $table_forums WHERE status='1' ORDER BY type, displayorder";
	$query = $db->query($sql);

	while($forum = $db->fetch_array($query)) {
		$forumname[$forum['fid']] = strip_tags($forum['name']);
		if($forum['type'] != 'group') {
			$threads += $forum['threads'];
			$posts += $forum['posts'];

			if($forum['type'] != 'sub') {
				$forums[$forum['fid']] = $forum;
			} elseif($forums[$forum['fup']]['type'] == 'forum') {
				$forums[$forum['fup']]['threads'] += $forum['threads'];
				$forums[$forum['fup']]['posts'] += $forum['posts'];
			}
		} else {
			$categories[] = $forum;
		}
	}
	
	if($categories) {
		foreach($categories as $group) {
			$group_forum = array();
			foreach($forums as $fid => $forum) {
				if($forum['fup'] == $group['fid']) {
					if(forum($forum)) {
						$group_forum[] = $forum;
						unset($forums[$fid]);
					}
				} elseif(!$forum['fup'] && $forum['type'] == 'forum') {
					$catforums[] = $forum;
					unset($forums[$fid]);
				}
			}
			if($group_forum) {
				$forumlist = array_merge($forumlist, array($group), $group_forum);
			}
		}
	} else {
		$catforums = $forums;
	}

 	foreach($catforums as $forum) {
		if($forum['fid'] && forum($forum) ) {
			$catforumlist[] = $forum;
		}
	}
	if($catforumlist) {
		$forumlist[] = array('fid' => 0, 'type' => 'group', 'name' => $bbname);
		$forumlist = array_merge($forumlist, $catforumlist);
	}

	unset($fid, $forums, $catforums, $catforumlist, $categories, $group, $forum, $group_forum);

	if($whosonlinestatus == 1 || $whosonlinestatus == 3) {
		$whosonlinestatus = 1;

		$onlineinfo = explode("\t", $onlinerecord);
		$detailstatus = ((!isset($_COOKIE['onlinedetail']) && $onlineinfo[0] < 500) || ($_COOKIE['onlinedetail'] || $showoldetails == 'yes')) && $showoldetails != 'no';

		if($detailstatus) {
			@include language('actions');

			updatesession();
			$onlinenum = $membercount = $invisiblecount = $guestcount = 0;
			$whosonline = array();
			$query = $db->query("SELECT uid, username, groupid, invisible, action, lastactivity, fid FROM $table_sessions");
			while($online = $db->fetch_array($query)) {
				if($online['uid']) {
					$membercount++;
					if(!$online['invisible']) {
						$online['icon'] = isset($_DCACHE['onlinelist'][$online['groupid']]) ? $_DCACHE['onlinelist'][$online['groupid']] : $_DCACHE['onlinelist'][0];
					} else {
						$invisiblecount++;
						if ($adminid == 1 ){
							$online['icon'] = 'online_invisible.gif';
						}else{
							continue;
						}
					}

					$online['fid'] = $online['fid'] ? $forumname[$online['fid']] : 0;
					$online['action'] = $actioncode[$online['action']];
					$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + ($timeoffset * 3600));
					$whosonline[] = $online;
				} else {
					$guestcount++;
				}
			}
			$onlinenum = $membercount + $guestcount;
			unset($online);
		} else {
			$query = $db->query("SELECT COUNT(*) FROM $table_sessions");
			$onlinenum = $db->result($query, 0);
		}

		if($onlinenum > $onlineinfo[0]) {
			require_once DISCUZ_ROOT.'./include/cache.php';
			$db->query("UPDATE LOW_PRIORITY $table_settings SET value='$onlinenum\t$timestamp' WHERE variable='onlinerecord'");
			updatecache('settings');
			$onlineinfo = array($onlinenum, $timestamp);
		}

		$onlineinfo[1] = gmdate($dateformat, $onlineinfo[1] + ($timeoffset * 3600));
	} else {
		$whosonlinestatus = 0;
	}

	if($discuz_user && $newpm) {
		require DISCUZ_ROOT.'./include/pmprompt.php';
	}

	include template('index');

} else {

	$forumlist = $cat = array();
	$threads = $posts = 0;
	$sql = $accessmasks 
			?
			"SELECT f.fid, f.fup, f.type, f.icon, f.name, f.description, f.moderator, f.threads, f.posts, f.lastpost, f.viewperm, a.allowview FROM $table_forums f
			LEFT JOIN $table_access a ON a.uid='$discuz_uid' AND a.fid=f.fid
			WHERE (f.fid='$gid' OR (f.fup='$gid' AND f.type='forum')) AND f.status='1' ORDER BY f.type, f.displayorder"
			:
			"SELECT fid, fup, type, icon, name, description, moderator, threads, posts, lastpost, viewperm FROM $table_forums
			WHERE (fid='$gid' OR (fup='$gid' AND type='forum')) AND status='1' ORDER BY type, displayorder";
	$query = $db->query($sql);
	while($forum = $db->fetch_array($query)) {
		if($forum['type'] == 'group') {
			$cat = $forum;
		} else {
			$threads += $forum['threads'];
			$posts += $forum['posts'];
		}
		if(forum($forum)) {
			$forumlist[] = $forum;
		}
	}

	if(empty($cat)) {
		showmessage('forum_nonexistence', NULL, 'HALTED');
	}

	$navigation = '&raquo; '.$cat['name'];
	$navtitle = ' - '.strip_tags($cat['name']);

	include template('index');

}
?>