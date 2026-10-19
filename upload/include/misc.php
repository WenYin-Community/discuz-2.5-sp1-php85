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

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function convertip($ip) {
	$datadir = DISCUZ_ROOT.'./ipdata/';
	$ip_detail = explode('.', $ip);
	if(file_exists($datadir.$ip_detail[0].'.txt')) {
		$ip_fdata = fopen($datadir.$ip_detail[0].'.txt', 'r');
	} else {
		if(!($ip_fdata = fopen($datadir.'0.txt', 'r'))) {
			echo 'Invalid IP data file';
		}
	}
	for ($i = 0; $i <= 3; $i++) {
		$ip_detail[$i] = sprintf('%03d', $ip_detail[$i]);
	}
	$ip = join('.', $ip_detail);
	do {
		$ip_data = fgets($ip_fdata, 200);
		$ip_data_detail = explode('|', $ip_data);
		if($ip >= $ip_data_detail[0] && $ip <= $ip_data_detail[1]) {
			fclose($ip_fdata);
			return $ip_data_detail[2].$ip_data_detail[3];
		}
	} while(!feof($ip_fdata));
	fclose($ip_fdata);
	return 'UNKNOWN';
}

function procthread($thread) {
	global $dateformat, $timeformat, $timeoffset, $ppp, $colorarray;

	if(empty($colorarray)) {
		$colorarray = array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');
	}

	$thread['forumname'] = $GLOBALS['_DCACHE']['forums'][$thread['fid']]['name'];
	$thread['dateline'] = gmdate($dateformat, $thread['dateline'] + $timeoffset * 3600);
	$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);
	$thread['lastposterenc'] = rawurlencode($thread['lastposter']);

	if($thread['replies'] > $thread['views']) {
		$thread['views'] = $thread['replies'];
	}
	$thread['subject'] = cutstr($thread['subject'],77);
	$postsnum = $thread['replies'] + 1;
	if($postsnum  > $ppp) {
		$posts = $postsnum;
		$topicpages = ceil($posts / $ppp);
		for ($i = 1; $i <= $topicpages; $i++) {
			$pagelinks .= "<a href=\"viewthread.php?tid=$thread[tid]&page=$i&highlight=$index[keywords]\" target=\"_blank\">$i</a> ";
			if($i == 6) {
				$i = $topicpages + 1;
			}
		}
		if($topicpages > 6) {
			$pagelinks .= " .. <a href=\"viewthread.php?tid=$thread[tid]&page=$topicpages&highlight=$index[keywords]\" target=\"_blank\">$topicpages</a> ";
		}
		$thread['multipage'] = " &nbsp; ( <img src=\"".IMGDIR."/multipage.gif\" align=\"absmiddle\" boader=\"0\"> $pagelinks)";
		$pagelinks = '';
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

	if($thread['attachment']) {
		require_once DISCUZ_ROOT.'./include/attachment.php';
		$thread['attachment'] = attachtype($thread['attachment']).' ';
	} else {
		$thread['attachment'] = '';
	}

	return $thread;
}

function updateviews() {
	global $logfile, $db, $table_threads;

	$viewlog = $tidarray = $viewarray = array();
	if(@$viewlog = file($logfile)) {
		foreach($viewlog as $tid) {
			$tid = intval($tid);
			if($tid) {
				$tidarray[$tid]++;
			}
		}

		foreach($tidarray as $tid => $views) {
			$viewarray[$views] .= ','.$tid;
		}

		foreach($viewarray as $views => $tids) {
			$db->query("UPDATE $table_threads SET views=views+$views WHERE tid IN (0$tids)", 'UNBUFFERED');
		}
	}
	@unlink($logfile);
}

?>