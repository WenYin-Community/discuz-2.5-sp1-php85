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
1 主题作者仍然受积分限制
*/

define('CURRSCRIPT',  'post');

require './include/common.php';

$ismoderator = modcheck($discuz_user);
$postcredits = $forum['postcredits'] != -1 ? $forum['postcredits'] : $postcredits;
$replycredits = $forum['replycredits'] != -1 ? $forum['replycredits'] : $replycredits;


if($tid && $fid) {
	
	//$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
	//$thread = $db->fetch_array($query);
	$thread = $forum;
	$fid = $thread['fid'];
	$navigation = "&raquo; <a href=\"viewthread.php?tid=$tid\">{$thread['subject']}</a>";
	$navtitle = " - {$thread['subject']}";
}

$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">{$forum['name']}</a> $navigation";
$navtitle = ' - '.strip_tags($forum['name']).$navtitle;
if($forum['type'] == 'sub') {
	$query = $db->query("SELECT name, fid FROM $table_forums WHERE fid='{$forum['fup']}'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid={$fup['fid']}\">{$fup['name']}</a> $navigation";
	$navtitle = ' - '.strip_tags($fup['name']).$navtitle;
}

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

if(empty($action)) {
	showmessage('undefined_action', NULL, 'HALTED');
}


if(!$bbcodeoff && !$allowhidecode && preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", $message)) {
	showmessage('post_hide_nopermission');
}

if(!$adminid && !$lastpost && $newbiespan) {
	if(($timestamp - $regdate) < $newbiespan * 3600) {
		showmessage('post_newbie_span');
	}
}

if ( $action == 'reply' && $delayreply && !$ismoderator){
	if (($timestamp - $thread['dateline'] ) > $delayreply * 86400) {
		showmessage('post_delayreply_error');
	}
} 

require DISCUZ_ROOT.'./include/discuzcode.php';
require DISCUZ_ROOT.'./include/post.php';
require DISCUZ_ROOT.'./include/attachment.php';

$subject = $subject ? dhtmlspecialchars(censor(trim($subject))) :'';
$message = $message ? censor(trim($message)) : '';

$smilies = '';
if($previewpost || (!$previewpost && !$topicsubmit && !$replysubmit && !$editsubmit)) {

	$enctype = $allowpostattach ? 'enctype="multipart/form-data"' : NULL;

	if($smileyinsert && is_array($_DCACHE['smilies'])) {
		$smileyinsert = 1;
		$smcols = $smcols ? $smcols : 3;
		$smilies .= '<tr>';
		foreach(array_reverse($_DCACHE['smilies']) as $key => $smiley) {
			$smilies .= '<td align="center" valign="top"><img src="'.SMDIR.'/'.$smiley['url'].'" border="0" onmouseover="this.style.cursor=\'hand\';" onclick="AddText(\''.dhtmlspecialchars(addcslashes($smiley['code'], '\\\'')).'\');"></td>'."\n";
			$smilies .= !(++$key % $smcols) ? '</tr><tr>' : NULL;
		}
	} else {
		$smileyinsert = 0;
	}

	$maxattachsize_kb = $maxattachsize / 1000;
	$allowimgcode = $forum['allowimgcode'] ? 'On' : 'Off';
	$allowhtml = $forum['allowhtml'] ? 'On' : 'Off';
	$allowsmilies = $forum['allowsmilies'] ? 'On' : 'Off';
	$allowbbcode = $forum['allowbbcode'] ? 'On' : 'Off';

	if($discuz_user && $signature && !$usesigcheck) {
		$usesigcheck = 'checked';
	}

	if($previewpost) {
		if (!$_DCACHE['bbcodes']) {
			require_once DISCUZ_ROOT.'./forumdata/cache/cache_viewthread.php';
		}
		$currtime = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);
		$author = $discuz_userss;
		$subject = stripslashes($subject);
		$message = stripslashes($message);
		$subject_preview = $subject;
		$message_preview = postify($message, $smileyoff, $bbcodeoff, $forum['allowsmilies'], $forum['allowhtml'], $forum['allowbbcode'], $forum['allowimgcode']);
		$message = dhtmlspecialchars($message);  

		$urloffcheck = $parseurloff ? 'checked' : NULL;
		$usesigcheck = $usesig ? 'checked' : NULL;
		$smileoffcheck = $smileyoff ? 'checked' : NULL;
		$codeoffcheck = $bbcodeoff ? 'checked' : NULL;

		$topicsubmit = $replysubmit = $editsubmit = '';
	} else {
		$subject = $message = $polloptions = '';
	}

} else {

	if(!$parseurloff) {
		$message = parseurl($message);
	}

}

if($forum['password'] != $_COOKIE["fidpw$fid"] && $forum['password'] != $_DSESSION["fidpw$fid"] && $forum['password']) {
	header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	exit();
}

if($action == 'newthread') {
	require DISCUZ_ROOT.'./include/newthread.php';
} elseif($action == 'reply') {
	require DISCUZ_ROOT.'./include/newreply.php';
} elseif($action == 'edit') {
	require DISCUZ_ROOT.'./include/editpost.php';
}

?>