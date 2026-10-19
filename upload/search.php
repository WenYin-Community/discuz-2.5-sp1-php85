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
1 ´¿ÖÐÎÄËÑË÷
*/

require_once './include/common.php';
require DISCUZ_ROOT.'./include/forum.php';
require DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';

if(!$allowsearch) {
	showmessage('group_nopermission', NULL, 'NOPERM');
}

$discuz_action = 111;


$cachelife_time = 300;		// Life span for cache of searching in specified range of time
$cachelife_text = 3600;		// Life span for cache of searching in specified range of full text

if(empty($searchid) && !$page && !submitcheck('searchsubmit', 1)) {

	$forumselect = forumselect();
	include template('search');

} else {

	$orderby = $orderby == 'views' || $orderby == 'replies' ? $orderby : 'lastpost';
	$ascdesc = $ascdesc == 'asc' ? 'asc' : 'desc';

	if(isset($searchid)) {

		$searchid = intval($searchid);

		require DISCUZ_ROOT.'./include/misc.php';

		$page = intval($page) ? intval($page) : 1;
		$start_limit = ($page - 1) * $tpp;

		$query = $db->query("SELECT keywords, threads, tids FROM $table_searchindex WHERE searchid='$searchid'");
		if(!$index = $db->fetch_array($query)) {
			showmessage('search_id_invalid');
		}
		$index['keywords'] = rawurlencode($index['keywords']);

		$threadlist = array();
		$query = $db->query("SELECT * FROM $table_threads WHERE tid IN ({$index['tids']}) ORDER BY $orderby $ascdesc LIMIT $start_limit, $tpp");
		while($thread = $db->fetch_array($query)) {
			$threadlist[] = procthread($thread);
		}

		$multipage = multi($index['threads'], $tpp, $page, "search.php?searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		include template('search');

	} else {

		$srchtxt = trim($srchtxt);
		$srchuname = trim($srchuname);
		$searchstring = md5($srchtxt.'|'.intval($srchuid).'|'.trim($srchuname).'|'.intval($srchfid).'|'.intval($srchfrom).'|'.intval($before).'|'.intval($titleonly));

		$searchindex = array('id' => 0, 'dateline' => '0');
		$query = $db->query("SELECT searchid, dateline, ('$searchctrl'<>'0' AND ".($uid ? "uid='$uid'" : "useip='$onlineip'")." AND dateline>$timestamp-$searchctrl) AS flood, (searchstring='$searchstring' AND expiration>'$timestamp') AS indexvalid FROM $table_searchindex WHERE ('$searchctrl'<>'0' AND ".($uid ? "uid='$uid'" : "useip='$onlineip'")." AND dateline>$timestamp-$searchctrl) OR (searchstring='$searchstring' AND expiration>'$timestamp') ORDER BY flood");
		while($index = $db->fetch_array($query)) {
			if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
				if(substr($timestamp, -1) == '0') {
					$db->query("DELETE FROM $table_searchindex WHERE expiration<'$timestamp'", 'UNBUFFERED');
				}
				$searchindex = array('id' => $index['searchid'], 'dateline' => $index['dateline']);
				break;
			} elseif($index['flood']) {
				showmessage('search_ctrl');
			}
		}

		if($searchindex['id']) {
			$searchid = $searchindex['id'];
		} else {

			if(!isset($srchfid)) {
				$srchfid = 'all';
			}

			$fids = $comma = '';
			foreach($_DCACHE['forums'] as $fid => $forum) {
				if($forum['type'] != 'group' && (!$forum['viewperm'] && $allowview) || ($forum['viewperm'] && strstr($forum['viewperm'], "\t$groupid\t"))) {
					$fids .= "$comma'$fid'";
					$comma = ',';
				}
			}

			if(!$srchtxt && !$srchuid && !$srchuname && !$srchfrom) {
				showmessage('search_invalid');
			} elseif(empty($srchfid)) {
				showmessage('search_forum_invalid');
			} elseif(!$fids) {
				showmessage('group_nopermission', NULL, 'NOPERM');
			}

			if($srchfrom && !$srchtxt && !$srchuid && !$srchuname) {
	
				$searchfrom = $before ? '<=' : '>=';
				$searchfrom .= $timestamp - $srchfrom;
				$sqlsrch = "FROM $table_threads t WHERE fid IN ($fids) AND lastpost$searchfrom";
				if($srchfid != "all" && $srchfid) {
					$sqlsrch .= " AND t.fid='$srchfid'";
				}
				$expiration = $timestamp + $cachelife_time;
				$keywords = '';

			} else {

				$sqlsrch = $allowsearch == 2 && !$titleonly ?
					"FROM $table_posts p, $table_threads t WHERE t.fid IN ($fids) AND p.tid=t.tid" :
					"FROM $table_threads t WHERE t.fid IN ($fids)";

	
				if($srchuname) {
					$srchuid = $comma = '';
					$srchuname = str_replace('*', '%', $srchuname);
					$query = $db->query("SELECT uid FROM $table_members WHERE username LIKE '$srchuname'");
					while($member = $db->fetch_array($query)) {
						$srchuid .= "$comma'{$member['uid']}'";
						$comma = ', ';
					}
					if(!$srchuid) {
						showmessage('member_nonexistence','search.php');
						$sqlsrch .= ' AND 0';
					}
				} elseif($srchuid) {
					$srchuid = "'$srchuid'";
				}

				if($srchtxt) {
					if(preg_match("(AND|\+|&|\s)", $srchtxt) && !preg_match("(OR|\|)", $srchtxt)) {
						$andor = ' AND ';
						$sqltxtsrch = '1';
						$srchtxt = preg_replace("/( AND |&| )/is", "+", $srchtxt);
					} else {
						$andor = ' OR ';
						$sqltxtsrch = '0';
						$srchtxt = preg_replace("/( OR |\|)/is", "+", $srchtxt);
					}
					$srchtxt = str_replace('*', '%', $srchtxt);
					foreach(explode('+', $srchtxt) as $text) {
						$text = trim($text);
						if($text) {
							$useBin = (preg_match("/[a-zA-Z]/", $text) && $database == 'mysql') ? '' : 'BINARY';
							$sqltxtsrch .= $andor;
							$sqltxtsrch .= $allowsearch == 2 && !$titleonly ? " (p.message LIKE $useBin '%$text%' OR p.subject LIKE $useBin '%$text%')" : "t.subject LIKE $useBin '%$text%'";
						}
					}
					$sqlsrch .= " AND ($sqltxtsrch)";
				}

				if($srchuid) {
					$sqlsrch .= ' AND '.($allowsearch == 2 && !$titleonly ? 'p' : 't').".authorid IN ($srchuid)";
				}

				if($srchfid != 'all' && $srchfid) {
					$sqlsrch .= " AND t.fid='$srchfid'";
				}

				if($srchfrom) {
					$searchfrom = $before ? '<=' : '>=';
					$searchfrom .= $timestamp - $srchfrom;
					$sqlsrch .= " AND t.lastpost$searchfrom";
				}

				$keywords = str_replace('%', '+', $srchtxt).(trim($srchuname) ? '+'.str_replace('%', '+', $srchuname) : NULL);
				$expiration = $timestamp + $cachelife_text;

			}

			$threads = $tids = 0;
			$query = $db->query("SELECT DISTINCT t.tid, t.closed $sqlsrch LIMIT $maxsearchresults");
			while($thread = $db->fetch_array($query)) {
				if($thread['closed'] <= 1) {
					$tids .= ','.$thread['tid'];
					$threads++;
				}
			}

			$db->query("INSERT INTO $table_searchindex (keywords, searchstring, useip, uid, dateline, expiration, threads, tids)
					VALUES ('$keywords', '$searchstring', '$onlineip', '$discuz_uid', '$timestamp', '$expiration', '$threads', '$tids')");
			$searchid = $db->insert_id();

		}

		showmessage('search_redirect', "search.php?searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

	}

}

?>