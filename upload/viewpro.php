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
1 帖子里的注册时间与察看用户资料的注册时间不一致 
*/

require './include/common.php';
require DISCUZ_ROOT.'./include/discuzcode.php';

$discuz_action = 61;

$query = $db->query("SELECT m.*, u.grouptitle, u.color AS groupcolor, u.stars AS groupstars, r.ranktitle, r.color AS rankcolor, r.stars AS rankstars FROM $table_members m
		LEFT JOIN $table_usergroups u USING(groupid)
		LEFT JOIN $table_ranks r ON m.postnum>=r.postshigher
		WHERE ".(isset($uid) ? "uid='$uid'" : "username='$username'")."ORDER BY r.postshigher DESC LIMIT 1");

if(!@$member = $db->fetch_array($query)) {
	showmessage('member_nonexistence');
}

$query = $db->query("SELECT COUNT(*) FROM $table_posts");
@$percent = round($member['postnum'] * 100 / $db->result($query, 0), 2);
$postperday = $timestamp - $member['regdate'] > 86400 ? round(86400 * $member['postnum'] / ($timestamp - $member['regdate']), 2) : $member['postnum'];

$member['grouptitle'] = $member['groupcolor'] ? '<font color="'.$member['groupcolor'].'">'.$member['grouptitle'].'</font>' : $member['grouptitle'];
$member['ranktitle'] = $member['rankcolor'] ? '<font color="'.$member['rankcolor'].'">'.$member['ranktitle'].'</font>' : $member['ranktitle'];

$member['regdate'] = gmdate($dateformat, $member['regdate'] + ($timeoffset * 3600));
$member['site'] = $member['site'] ? 'http://'.str_replace('http://', '', $member['site']) : '';
$member['avatar'] = $member['avatar'] ? "<img src=\"$member[avatar]\" width=\"$member[avatarwidth]\" height=\"$member[avatarheight]\" border=\"0\">" : '<br><br><br>';
$member['lastactivity'] = gmdate("$dateformat $timeformat", $member['lastactivity'] + ($timeoffset * 3600));
$member['lastpost'] = $member['lastpost'] ? gmdate("$dateformat $timeformat", $member['lastpost'] + ($timeoffset * 3600)) : 'x';
$member['bio'] = nl2br($member['bio']);
$member['signature'] = postify($member['signature'], 1, 0, 0, 0, $member['allowsigbbcode'], $member['allowsigimgcode']);

$birthday = explode('-', $member['bday']);
$member['bday'] = $dateformat;
$member['bday'] = str_replace('n', $birthday[1], $member['bday']);
$member['bday'] = str_replace('j', $birthday[2], $member['bday']);
$member['bday'] = str_replace('Y', $birthday[0], $member['bday']);
$member['bday'] = str_replace('y', substr($birthday[0], 2, 4), $member['bday']);

if($allowviewip && !($adminid == 2 && $member['adminid'] == 1) && !($adminid > 2 && ($member['adminid'] == 1 || $member['adminid'] == 2))) {
	require DISCUZ_ROOT.'./include/misc.php';
	$member['regiplocation'] = convertip($member['regip']);
	$member['lastiplocation'] = convertip($member['lastip']);
} else {
	$allowviewip = 0;
}

include template('viewpro');

?>