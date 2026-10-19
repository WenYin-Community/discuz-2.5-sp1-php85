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

require "./include/common.php";

$discuz_action = 131;

if(!$allowviewstats) {
	showmessage('group_nopermission', NULL, 'HALTED');
}

$stats_update_time = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);

$stats_cachekey = '';

if(!$type) {
	$vartype = $statstatus ? "'total', 'month', 'hour'":"'total'";
	$stats_cachekey = 'stats_cache_main';
} elseif($type == "agent") {
	$vartype = "'os', 'browser'";
	$stats_cachekey = 'stats_cache_'.$type;
} else {
	$vartype = "'$type'";
	if (!in_array($type,array('week','hour','threads','member'))){
		showmessage('undefined_action', NULL, 'HALTED');
	}else{
		$stats_cachekey = 'stats_cache_'.$type;
	}
}

$$stats_cachekey = array();
$query = $db->query("SELECT cval,dateline FROM $table_caches WHERE ckey='$stats_cachekey'");
if ($stats_temp = $db->fetch_array($query)){
	if ($timestamp -$stats_temp['dateline'] < $statcacherefresh){
		$$stats_cachekey = unserialize($stats_temp['cval']);
		$stats_update_time = gmdate("$dateformat $timeformat", $stats_temp['dateline'] + $timeoffset * 3600);
	}
	unset($stats_temp);
}

if (!$$stats_cachekey){
$query = $db->query("SELECT * FROM $table_stats WHERE type IN ($vartype) ORDER BY type");
	while($stats = $db->fetch_array($query)) {
		switch($stats['type']) {
			case 'total':
				$stats_total[$stats['var']] = $stats['count'];
				break;
			case 'os':
				$stats_os[$stats['var']] = $stats['count'];
				if($stats['count'] > $maxos) {
					$maxos = $stats['count'];
				}
				break;
			case 'browser':
				$stats_browser[$stats['var']] = $stats['count'];
				if($stats['count'] > $maxbrowser) {
				$maxbrowser = $stats['count'];
				}
				break;
			case 'month':
				$stats_month[$stats['var']] = $stats['count'];
				if($stats['count'] > $maxmonth) {
				$maxmonth = $stats['count'];
				$maxmonth_year = intval($stats['var'] / 100);
				$maxmonth_month = $stats['var'] - $maxmonth_year * 100;
				}
				break;
			case 'week':
				$stats_week[$stats['var']] = $stats['count'];
				if($stats['count'] > $maxweek) {
					$maxweek = $stats['count'];
					$maxweek_day = $stats['var'];
				}
				break;
			case 'hour':
				$stats_hour[$stats['var']] = $stats['count'];
				if($stats['count'] > $maxhour) {
					$maxhour = $stats['count'];
					$maxhourfrom = $stats['var'];
					$maxhourto = $maxhourfrom + 1;
				}
				break;
		}
	}
}
if(!$type) {
	if (!$stats_cache_main){
		$query = $db->query("SELECT COUNT(*) FROM $table_forums WHERE type='forum' OR type='sub'");
		$stats_cache_main['forums'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_threads");
		$stats_cache_main['threads'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*), (MAX(dateline)-MIN(dateline))/86400 FROM $table_posts");
		list($stats_cache_main['posts'], $stats_cache_main['runtime']) = $db->fetch_row($query);

		$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE dateline>='".($timestamp - 86400)."'");
		$stats_cache_main['postsaddtoday'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE regdate>='".($timestamp - 86400)."'");
		$stats_cache_main['membersaddtoday'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members");
		$stats_cache_main['members'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE adminid>'0'");
		$stats_cache_main['admins'] = $db->result($query, 0);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE postnum='0'");
		$stats_cache_main['memnonpost'] = $db->result($query, 0);
		$stats_cache_main['mempost'] = $stats_cache_main['members'] - $stats_cache_main['memnonpost'];

		@$stats_cache_main['mempostavg'] = sprintf ("%01.2f", $stats_cache_main['posts'] / $stats_cache_main['members']);
		@$stats_cache_main['threadreplyavg'] = sprintf ("%01.2f", ($stats_cache_main['posts'] - $stats_cache_main['threads']) / $stats_cache_main['threads']);
		@$stats_cache_main['mempostpercent'] = sprintf ("%01.2f", 100 * $stats_cache_main['mempost'] / $stats_cache_main['members']);
		@$stats_cache_main['postsaddavg'] = round($stats_cache_main['posts'] / $stats_cache_main['runtime']);
		@$stats_cache_main['membersaddavg'] = round($stats_cache_main['members'] / $stats_cache_main['runtime']);

		$query = $db->query("SELECT author, COUNT(*) AS postnum FROM $table_posts WHERE dateline >= '".($timestamp - 86400)."' GROUP BY author ORDER BY postnum DESC LIMIT 1");
		list($stats_cache_main['bestmem'], $stats_cache_main['bestmempost']) = $db->fetch_row($query);
		if($stats_cache_main['bestmem']) {
			$stats_cache_main['bestmem'] = "<a href=\"viewpro.php?username=".rawurlencode($stats_cache_main['bestmem'])."\"><span class=\"bold\">".$stats_cache_main['bestmem']."</span></a>";
		} else {
			$stats_cache_main['bestmem'] = 'None';
			$stats_cache_main['bestmempost'] = 0;
		}
		$query = $db->query("SELECT posts, threads, fid, name FROM $table_forums ORDER BY posts DESC LIMIT 1");
		$stats_cache_main['hotforum'] = $db->fetch_array($query);
	
		if($statstatus){
			$stats_total['visitors'] = $stats_total['members'] + $stats_total['guests'];
			@$stats_cache_main['pageviewavg'] = sprintf ("%01.2f", $stats_total['hits'] / $stats_total['visitors']);
			$stats_cache_main['stats_total']=$stats_total;
			$stats_cache_main['statsbar_month'] = statsdata('month', $maxmonth);
		}
		$stats_cache_main['maxmonth'] = $maxmonth;
		$stats_cache_main['maxmonth_year'] = $maxmonth_year;
		$stats_cache_main['maxmonth_month'] = $maxmonth_month;
		$stats_cache_main['maxhourfrom'] = $maxhourfrom;
		$stats_cache_main['maxhourto'] = $maxhourto;
		$stats_cache_main['maxhour'] = $maxhour;
		@$stats_cache_main['activeindex'] = round(($stats_cache_main['membersaddavg'] / $stats_cache_main['members'] + $stats_cache_main['postsaddavg'] / $stats_cache_main['posts']) * 1500 + $stats_cache_main['threadreplyavg'] * 10 + $stats_cache_main['mempostavg'] * 1 + $stats_cache_main['mempostpercent'] / 10 + $stats_cache_main['pageviewavg']);

		update_stats_caches($stats_cachekey,$$stats_cachekey);
	}
	include template('stats_main');

} elseif($type == 'week' || $type == 'hour' || $type == 'agent') {
	if (!$$stats_cachekey && $statstatus){
		switch($type){
			case 'week':
				$$stats_cachekey = array('statsbar_week' => statsdata('week', $maxweek)); 
				break;
			
			case 'hour':
				$$stats_cachekey = array('statsbar_hour' => statsdata('hour', $maxhour)); 
				break;
			case 'agent':
				$$stats_cachekey = array(
					'statsbar_browser' => statsdata('browser', $maxbrowser, 0),
					'statsbar_os' => statsdata('os', $maxos, 0));
				break;
		}
		update_stats_caches($stats_cachekey,$$stats_cachekey);
	}
	include template('stats_misc');

} elseif($type == 'threads') {
	$threadview = $threadreply = array();
	if (!$$stats_cachekey){
		$query = $db->query("SELECT views, tid, subject FROM $table_threads ORDER BY views DESC LIMIT 0, 20");
		while($thread = $db->fetch_array($query)) {
			$thread['subject'] = cutstr($thread['subject'], 45);
			$threadview[] = $thread;
		}
		$query = $db->query("SELECT replies, tid, subject FROM $table_threads ORDER BY replies DESC LIMIT 0, 20");
		while($thread = $db->fetch_array($query)) {
			$thread['subject'] = cutstr($thread['subject'], 50);
			$threadreply[] = $thread;
		}

		$$stats_cachekey = array('threadview' => $threadview,'threadreply' => $threadreply);
		update_stats_caches($stats_cachekey,$$stats_cachekey);
	}else{
		$threadview = $stats_cache_threads['threadview'];
		$threadreply = $stats_cache_threads['threadreply'];
	}
	unset($$stats_cachekey);
	include template('stats_threads');

} elseif($type == 'member') {

	$members = '';
	$credits = $total = $thismonth = $today = array();
	if (!$$stats_cachekey){

		$query = $db->query("SELECT username, uid, credit FROM $table_members ORDER BY credit DESC LIMIT 0, 20");
		while($member = $db->fetch_array($query)) {
			$credits[] = $member;
		}

		$query = $db->query("SELECT username, uid, postnum FROM $table_members ORDER BY postnum DESC LIMIT 0, 20");
		while($member = $db->fetch_array($query)) {
			$total[] = $member;
		}

		$query = $db->query("SELECT DISTINCT(author) AS username, COUNT(pid) AS postnum FROM $table_posts WHERE dateline >= ".($timestamp - 86400 * 30)." GROUP BY author ORDER BY postnum DESC LIMIT 0, 20");
		while($member = $db->fetch_array($query)) {
			$thismonth[] = $member;
		}

		$query = $db->query("SELECT DISTINCT(author) AS username, COUNT(pid) AS postnum FROM $table_posts WHERE dateline >= ".($timestamp - 86400)." GROUP BY author ORDER BY postnum DESC LIMIT 0, 20");
		while($member = $db->fetch_array($query)) {
			$today[] = $member;
		}
		$$stats_cachekey = array(
					'credits' => $credits,
					'total' => $total,
					'thismonth' => $thismonth,
					'today' => $today,
					);
		update_stats_caches($stats_cachekey,$$stats_cachekey);

	}else{
		$credits = $stats_cache_member['credits'];
		$total = $stats_cache_member['total'];
		$thismonth = $stats_cache_member['thismonth'];
		$today = $stats_cache_member['today'];
	}
	unset($$stats_cachekey);
	for($i = 0; $i < 20; $i++) {
$credits[$i]['usernameurl'] = $credits[$i] ? "<a href=\"viewpro.php?username=".rawurlencode($credits[$i]['username'])."\">".$credits[$i]['username']."</a>" : '';
$total[$i]['usernameurl'] = $total[$i] ? "<a href=\"viewpro.php?username=".rawurlencode($total[$i]['username'])."\">".$total[$i]['username']."</a>" : '';
$thismonth[$i]['usernameurl'] = $thismonth[$i] ? ($thismonth[$i]['username'] ? "<a href=\"viewpro.php?username=".rawurlencode($thismonth[$i]['username'])."\">".$thismonth[$i]['username']."</a>" : "Guest") : '';
$today[$i]['usernameurl'] = $today[$i] ? ($today[$i]['username'] ? "<a href=\"viewpro.php?username=".rawurlencode($today[$i]['username'])."\">".$today[$i]['username']."</a>" : "Guest") : '';
$members .= "<tr $bgcolor><td><li> ".$credits[$i]['usernameurl']."</td><td align=\"right\">".$credits[$i]['credit']."</td><td bgcolor=\"".ALTBG1."\"></td>\n".
        "<td><li type=\"square\"> ".$total[$i]['usernameurl']."</td><td align=\"right\">".$total[$i]['postnum']."</td><td bgcolor=\"".ALTBG1."\"></td>\n".
                        "<td><li> ".$thismonth[$i]['usernameurl']."</td><td align=\"right\">".$thismonth[$i]['postnum']."</td><td bgcolor=\"".ALTBG1."\"></td>\n".
        "<td><li type=\"square\"> ".$today[$i]['usernameurl']."</td><td align=\"right\">".$today[$i]['postnum']."</td></tr>\n";
		$bgcolor = $bgcolor ? '' : 'bgcolor="'.ALTBG2.'"';
	}

	include template('stats_member');

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

function statsdata($type, $max, $sort = 1) {
	global $barno;

	$statsbar = '';
	$sum = 0;

	$datarray = $GLOBALS["stats_$type"];
	if(is_array($datarray)) {
		if($sort) {
			ksort($datarray);
		}
		foreach($datarray as $count) {
			$sum += $count;
		}
	} else {
		$datarray = array();
	}

	foreach($datarray as $var => $count) {
		$barno ++;
		switch($type) {
			case 'month':
				$var = substr($var, 0, 4).'-'.substr($var, -2);
				break;
			case 'week':
				switch($var) {
					case 00: $var = 'Sunday'; break;
					case 01: $var = 'Monday'; break;
					case 02: $var = 'Tuesday'; break;
					case 03: $var = 'Wednesday'; break;
					case 04: $var = 'Thursday'; break;
					case 05: $var = 'Friday'; break;
					case 06: $var = 'Saturday'; break;
				}
				break;
			case 'hour':
				$var = intval($var);
				if($var <= 12) {
					$var = "$var AM";
				} else {
					$var -= 12;
					$var = "$var PM";
				}
				break;
			default:
				$var = '<img src="images/stats/'.strtolower(str_replace('/', '', $var)).'.gif" border="0"> '.$var;
				break;
		}
		@$width = intval(370 * $count / $max);
		@$percent = sprintf ("%01.1f", 100 * $count / $sum);
		$width = $width ? $width : '2';
		$var = $count == $max ? '<span class="bold"><i>'.$var.'</i></span>' : $var;
		$count = '<img src="images/common/bar'.($barno % 10).'.gif" width="'.$width.'" height="10" border="0"> &nbsp; <span class="bold">'.$count.'</span> ('.$percent.'%)';
		$statsbar .= "<tr><td width=\"100\">$var</td><td width=\"500\">$count</td></tr>\n";
	}

	return $statsbar;
}

function update_stats_caches($ckey, $cachearray){
	
	global $db, $table_caches,$timestamp;
	$query = $db->query("SELECT COUNT(*) FROM $table_caches WHERE ckey='$ckey'");
	if ($db->result($query, 0)){
		$db->query("UPDATE $table_caches SET cval = '".addslashes(serialize($cachearray))."' ,dateline='$timestamp' WHERE ckey='$ckey'");
	}else{
		$db->query("INSERT INTO $table_caches (ckey, cval, dateline)			VALUES ('$ckey', '".addslashes(serialize($cachearray))."', '$timestamp')", 'SILENT');
	}
}
?>