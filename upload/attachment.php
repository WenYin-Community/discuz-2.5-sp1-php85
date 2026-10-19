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

$discuz_action = 14;

$query = $db->query("SELECT a.*, t.fid FROM $table_attachments a LEFT JOIN $table_threads t ON a.tid=t.tid WHERE aid='$aid'");
$attach = $db->fetch_array($query);

$isimage = 0;
$filename = DISCUZ_ROOT.$attachdir.'/'.$attach['attachment'];

if ($useimagemessage && stristr($attach['filetype'],"image")){ 
	$imagesize =@getimagesize ("$filename"); 
	if ($imagesize) $isimage = 1;
}

if($attachrefcheck && preg_replace("/https?:\/\/([^\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) != $_SERVER['HTTP_HOST']) {
	showimgmessage('attachment_referer_invalid');
}

if ($attachsoftdownload && $checkid != substr(md5($attach['filesize']),0,5)){
	showimgmessage('attachment_referer_invalid');
};

if($allowgetattach && $attach['creditsrequire'] && $attach['creditsrequire'] > $credit && $adminid < 1 ) {
	showimgmessage('attachment_nopermission');
}

$query = $db->query("SELECT f.getattachperm, a.allowgetattach FROM $table_forums f
			LEFT JOIN $table_access a ON a.uid='$discuz_uid' AND a.fid=f.fid
			WHERE f.fid='$attach[fid]'");
$forum = $db->fetch_array($query);

if(!$forum['allowgetattach']) {
	if(!$forum['getattachperm'] && !$allowgetattach) {
		showimgmessage('group_nopermission');
	} elseif($forum['getattachperm'] && !strstr($forum['getattachperm'], "\t$groupid\t")) {
		showimgmessage('attachment_forum_nopermission');
	}
}


$db->query("UPDATE $table_attachments SET downloads=downloads+1 WHERE aid='$aid'", 'UNBUFFERED');

if(is_readable($filename) && $attach) {
	$filesize = filesize($filename);

	ob_end_clean();
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: private'); 
	header('Pragma: no-cache');
	header('Content-Encoding: none');
	header('Content-Length: '.$filesize);

    if (!$download && $isimage){ 
		header('Content-Disposition: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ').'filename='.$attach['filename']); 
		header('Content-Type: '.$attach['filetype']); 
		readfile($filename); 
    }else{ 
		header('Content-Disposition: attachment; filename='.$attach['filename']);
		header('Content-Type: '.$attach['filetype']);
		readfile($filename); 
	}
} else {
	showmessage('attachment_nonexistence');
}

function showimgmessage( $message = '' ){
	global $isimage;
	if ($isimage || !$message){
		ob_end_clean();
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: private'); 
		header('Pragma: no-cache');
		header('Content-Encoding: none');
		header('Content-Disposition: inline; filename=notaccess'); 
        header('Content-Type: image/gif'); 
        readfile(DISCUZ_ROOT.'images/common/img_no.gif'); 
		exit();
	}else{
		showmessage($message, NULL, 'HALTED');
	}
}
?>