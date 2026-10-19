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

require './include/common.php';

require DISCUZ_ROOT.'./include/misc.php';
require DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';

$forums = isset($forums) ? $forums : '';
$page = intval($page) ? intval($page) : 1;
$start_limit = ($page - 1) * $tpp;

$forumsarray = array();
foreach((is_array($forums) ? $forums : explode('_', $forums)) as $forum) {
	$forum = intval(trim($forum));
	if(!empty($forum)) {
		$forumsarray[] = $forum;
	}
}


$fids = '0';
$forumlist = $forumcheck = array();
foreach($_DCACHE['forums'] as $fid => $forum) {
	if($forum['type'] != 'group' && (!$forum['viewperm'] && $allowview) || ($forum['viewperm'] && strstr($forum['viewperm'], "\t$groupid\t"))) {
		$forumlist[] = array('fid' => $fid, 'name' => $forum['name']);
		if(!$forumsarray || in_array($fid, $forumsarray)) {
			$fids .= ','.$fid;
			$forumcheck[$fid] = 'checked';
		}
	}
}

$keywordadd = $keyword ? "AND subject LIKE '%$keyword%'" : '';

$query = $db->query("SELECT COUNT(*) FROM {$tablepre}threads WHERE digest>'0' AND fid IN ($fids) AND displayorder>='0' $keywordadd");
$threadcount = $db->result($query, 0);

if(!$threadcount) {
	showmessage('digest_nonexistence');
}

if(!$order || !in_array($order, array('dateline', 'lastpost', 'replies', 'views'))) {
	$order = 'digest';
}
$ordercheck = array('dateline' => '', 'digest' => '', 'lastpost' => '', 'replies' => '', 'views' => '');
$ordercheck[$order] = 'selected="selected"';

$forumsarray = $threadlist = array();
$query = $db->query("SELECT * FROM {$tablepre}threads WHERE digest>'0' AND fid IN ($fids) AND displayorder>='0' $keywordadd ORDER BY $order DESC LIMIT $start_limit, $tpp");
while($thread = $db->fetch_array($query)) {
	$threadlist[] = procthread($thread);
}

$multipage = multi($threadcount, $tpp, $page, "digest.php?order=$order&keyword=".rawurlencode($keyword)."&forums=".str_replace(',', '_', $fids), $threadmaxpages);
$keyword = dhtmlspecialchars($keyword);

include template('digest');

?>