<?php

/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2001-2005 Comsenz Technology Ltd (www.discuz.com)                ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (tech@discuz.com) Cnteacher (cnteacher@discuz.com) ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
fix: 对于多附件支持错误
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$postlist = array();
$have_attachment = 0;

$querypost = $db->query("SELECT * FROM $table_posts WHERE tid='$tid' ORDER BY dateline");
while($post = $db->fetch_array($querypost)) {
	$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + ($timeoffset * 3600));
	$post['message'] = postify($post['message'], $post['smileyoff'], $post['bbcodeoff'], $forum['allowsmilies'], $forum['allowhtml'], $forum['allowbbcode'], $forum['allowimgcode']);
	if(!$have_attachment && $post['aid']) $have_attachment = 1;
	$postlist[] = $post;
}

if (empty($postlist)) {
	showmessage('undefined_action', NULL, 'HALTED');
}

if ($have_attachment) {
	require_once DISCUZ_ROOT.'./include/attachment.php';
	$query = $db->query("select * from $table_attachments where tid='$tid'");
	while($attach = $db->fetch_array($query)) {
		$attach['checkid'] = substr(md5($attach['filesize']),0,5);
		$extension = strtolower(fileext($attach['filename']));
		$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
		if($attachimgpost && in_array($extension, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp'))) {
			$attach['attachimg'] = 1;
		} else {
			$attach['attachimg'] = 0;
		}
		$attach['attachsize'] = sizecount($attach['filesize']);
		$attach['dateline'] = $attach['dateline']?gmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600): '';
		$attachelist["{$attach['pid']}"][] = $attach;
	}
}

include template('viewthread_printable');

?>