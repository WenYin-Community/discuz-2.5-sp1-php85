<?php

/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2002 Crossday Studio (www.discuz.com)                            ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (info@discuz.net) Cnteacher ( Cnteacher@126.com)   ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/
//fix:  BY pk0909
/*
1 帖子中的 “上一主题 | 下一主题” 失效
*/

require './include/common.php';

if(isset($fid) && empty($forum)) {
	showmessage('forum_nonexistence', NULL, 'HALTED');
}

@include DISCUZ_ROOT.'./forumdata/cache/cache_viewthread.php';

if($goto == 'lastpost' && ($tid || $fid)) {
	define('ViewLastPost','1');
	require DISCUZ_ROOT.'./viewthread.php';
	exit();

} elseif($goto == 'newpost' && $tid ) {
	$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid' AND dateline<='$lastvisit'");
	$page = ceil($db->result($query, 0) / $ppp);
	require DISCUZ_ROOT.'./viewthread.php';
	exit();

} elseif($goto == 'nextnewset' && $fid && $tid) {

	$query = $db->query("SELECT lastpost FROM $table_threads WHERE tid='$tid'");
	$this_lastpost = $db->result($query, 0);
	$query = $db->query("SELECT tid FROM $table_threads WHERE fid='$fid' AND lastpost>'$this_lastpost' ORDER BY lastpost ASC LIMIT 1");
	if($next = $db->fetch_array($query)) {
		$tid = $next['tid'];
		header("Location: {$boardurl}viewthread.php?tid=$tid&sid=$sid");
		exit();
	} else {
		showmessage('redirect_nextnewset_nonexistence');
	}

} elseif($goto == 'nextoldset' && $fid && $tid) {

	$query = $db->query("SELECT lastpost FROM $table_threads WHERE tid='$tid'");
	$this_lastpost = $db->result($query, 0);

	$query = $db->query("SELECT tid FROM $table_threads WHERE fid='$fid' AND lastpost<'$this_lastpost' ORDER BY lastpost DESC LIMIT 1");
	if($last = $db->fetch_array($query)) {
		$tid = $last['tid'];
		header("Location: {$boardurl}viewthread.php?tid=$tid&sid=$sid");
		exit();
	} else {
		showmessage('redirect_nextoldset_nonexistence');
	}
}

showmessage('undefined_action', NULL, 'HALTED');

?>