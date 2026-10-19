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
1 smilecheck
2 保存文件名的stripslashes
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function attach_upload() {
	global  $attach, $attach_name, $attach_size, $attach_fname, $attach_type, $attachperm, $allowsetattachperm, $timestamp;
	$success_upload = array();

	if (is_array($attach) && count($attach)){
		foreach( $attach as $key => $t_attach) {
			$t_attach_name = daddslashes($attach_name[$key]);
			$t_attachment = attach_upload_file($t_attach, $t_attach_name, $attach_size[$key], $attach_fname[$key],$attach_type[$key]);
			if ($t_attachment){
				$success_upload[$key] = array (
					'filename' => $t_attach_name,
					'filetype' => $attach_type[$key],
					'filesize' => $attach_size[$key],
					'attachment' => $t_attachment,
					'creditsrequire' => $allowsetattachperm ? $attachperm[$key] : 0,
					'dateline' => $GLOBALS['timestamp'],
					);
			}
		}
	}elseif($attach){
		$attach_name = daddslashes($attach_name);
		$t_attachment = attach_upload_file($attach, $attach_name, $attach_size, $attach_fname,$attach_type);
		if ($t_attachment){
			$success_upload[0] =array( 
				'filename' => $attach_name,
				'filetype' => $attach_type,
				'filesize' => $attach_size,
				'attachment' => $t_attachment,
				'creditsrequire' => $allowsetattachperm ? $attachperm : 0,
				'dateline' => $GLOBALS['timestamp'],
			);
		}
	}
	return $success_upload;
}
function attach_upload_file($attach, $attach_name, $attach_size, $attach_fname, $attach_type) {
	global $db, $table_attachtypes, $extension, $typemaxsize;
	global $attachdir, $maxattachsize, $attachextensions,$attachsave;

	if(!($attach != 'none' && strpos($attach, '..') === FALSE && $attach && trim($attach_name))) {
		return false;
	}
	$filename  = $attach_name;
	$attach_ext = $extension = strtolower(fileext($attach_name));

	// 安全加固: 白名单为空时回退到内置安全白名单, 并始终拒绝可执行/脚本扩展名
	if(!$attachextensions) {
		$attachextensions = 'chm,pdf,zip,rar,tar,tgz,gif,jpg,jpeg,png';
	}
	$deny_exts = array('php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phar', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'exe', 'dll', 'so', 'bat', 'cmd', 'sh');
	if(in_array($attach_ext, $deny_exts) || @!preg_match('/'.preg_quote($attach_ext, '/').'/i', $attachextensions)) {
		showmessage('post_attachment_ext_notallowed');
	}

	if(!$attach_size || ($maxattachsize && $attach_size > $maxattachsize)) {
		showmessage('post_attachment_toobig');
	}

	$query = $db->query("SELECT maxsize FROM $table_attachtypes WHERE extension='".addslashes($attach_ext)."'");
	if($type = $db->fetch_array($query)) {
		if($type['maxsize'] == 0) {
			showmessage('post_attachment_ext_notallowed');
		} elseif($attach_size > $type['maxsize']) {
			$typemaxsize = sizecount($type['maxsize']);
			showmessage('post_attachment_type_toobig');
		}
	}

	if($attachsave) {
		switch($attachsave) {
			case 1: $attach_subdir = 'forumid_'.$GLOBALS['fid']; break;
			case 2: $attach_subdir = 'ext_'.$extension; break;
			case 3: $attach_subdir = 'month_'.date('ym'); break;
			case 4: $attach_subdir = 'day_'.date('ymd'); break;
		}
		$attach_dir = DISCUZ_ROOT.'./'.$attachdir.'/'.$attach_subdir;
		if(!is_dir($attach_dir)) {
			mkdir($attach_dir, 0777);
			fclose(fopen($attach_dir.'/index.htm', 'w'));
		}
		$attach_fname = $attach_subdir.'/';
	} else {
		$attach_fname = '';
	}

	$filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);
	if(preg_match("/[\x7f-\xff]+/s", $filename)) {
		$filename = str_replace('/', '', base64_encode(substr($filename, 0, 20)));
	}
	if(in_array($attach_ext, array('php', 'php3', 'jsp', 'asp', 'aspx', 'cgi', 'pl'))) {
		$extension = '_'.$extension;
	}

	$attach_saved = false;
	$attach_fname .= substr($filename, 0, 64).'_'.random(12).'.'.$extension;
	$target = DISCUZ_ROOT.'./'.$attachdir.'/'.stripslashes($attach_fname);

	if(@copy($attach, $target) || (function_exists('move_uploaded_file') && @move_uploaded_file($attach, $target))) {
		$attach_saved = true;
	}

	if(!$attach_saved && @is_readable($attach)) {
		@$fp = fopen($attach, 'rb');
		@flock($fp, 2);
		@$attachedfile = fread($fp, $attach_size);
		@fclose($fp);

		@$fp = fopen($target, 'wb');
		@flock($fp, 2);
		if(@fwrite($fp, $attachedfile)) {
			$attach_saved = true;
		}
		@fclose($fp);
	}

	if($attach_saved) {
		if(in_array($attach_ext, array('jpg', 'gif', 'png', 'swf', 'bmp')) && function_exists('getimagesize') && !getimagesize($target)) {
			@unlink($target);
			showmessage('post_attachment_ext_notallowed');
		} else {
			return $attach_fname;
		}
	} else {
		showmessage('post_attachment_save_error');
	}
}

function checkflood() {
	global $disablepostctrl, $floodctrl, $discuz_uid, $timestamp, $lastpost, $forum;
	if(!$disablepostctrl && $floodctrl) {
		if($discuz_uid) {
			if($timestamp - $floodctrl <= $lastpost) {
				return TRUE;
			}
		} else {
			$lastpost = explode("\t", $forum['lastpost']);
			if(($timestamp - $floodctrl) <= $lastpost[1] && $discuz_user == $lastpost[2]) {
				return TRUE;
			}
		}
	}
	return FALSE;
}

function checkpost() {
	global $subject, $message, $disablepostctrl, $minpostsize, $maxpostsize;
	if(strlen($subject) > 80) {
		return 'post_subject_toolang';
	}
	if(!$disablepostctrl) {
		if($maxpostsize && strlen($message) > $maxpostsize) {
			return 'post_message_toolang';
		} elseif($minpostsize && strlen(preg_replace("/\[quote\].+?\[\/quote\]/is", '', $message)) < $minpostsize) {
			return 'post_message_tooshort';
		}
	}
	return FALSE;
}

function checkbbcodes($message, $bbcodeoff) {
	return !$bbcodeoff && !preg_match("/\[.+\].*\[\/.+\]/s", $message) ? -1 : $bbcodeoff;
}

function checksmilies($message, $smileyoff) {
	$message = stripcslashes($message);
	$smilies = array();
	foreach($GLOBALS['_DCACHE']['smilies'] as $smiley) {
		$smilies[]= preg_quote($smiley['code'], '/');
	}

	return !$smileyoff && !preg_match('/'.implode('|', $smilies).'/', $message) ? -1 : $smileyoff;
}

function updatemember($operator, $uid, $credits) {
	global $db, $table_members, $table_usergroups, $discuz_uid, $adminid, $groupid, $credit, $timestamp;
	
	$addcredit = $addpost = $newcredit = $newpost =0;
	
	if(!$uid ) return;

	if($uid == $discuz_uid) {
		$groupidadd = NULL;
		$newcredit = $credit + intval("$operator$credits");
		if($adminid == 0 && $credits <> 0 && !($newcredit % 10)) { 
			$query = $db->query("SELECT groupid FROM $table_usergroups WHERE type='member' AND '$newcredit'>=creditshigher AND '$newcredit'<creditslower");
			$groupidadd = ", groupid='".$db->result($query, 0)."'";
		}
		$db->query("UPDATE $table_members SET postnum=postnum$operator(1), credit=$newcredit, lastpost='$timestamp' $groupidadd WHERE uid='$uid'");
	} else {
		$member = array();
		foreach(explode(',', $uid) as $id) {
			$member[trim($id)]++;
		}
		foreach($member as $uid => $posts) {
			if($credits) {
				$query = $db->query("SELECT m.adminid, u.groupid FROM $table_members m
								LEFT JOIN $table_usergroups u ON (u.creditshigher<>'0' || u.creditslower<>'0') AND m.credit$operator$credits*$posts>=u.creditshigher AND m.credit$operator$credits*$posts<u.creditslower
								WHERE uid='$uid'");
				if($member2 = $db->fetch_array($query)) {
					$groupidadd = $member2['adminid'] == 0 ? ", groupid='{$member2['groupid']}'" : NULL;
					$db->query("UPDATE $table_members SET postnum=postnum$operator$posts, credit=credit$operator($credits*$posts) $groupidadd WHERE uid='$uid'", 'UNBUFFERED');
				}
			} else {
				$db->query("UPDATE $table_members SET postnum=postnum$operator$posts WHERE uid='$uid'", 'UNBUFFERED');
			}
		}
	}
}

function updateforumcount($fid) {
	global $db, $table_threads, $table_forums;
	$query = $db->query("SELECT COUNT(*) AS threadcount, SUM(t.replies)+COUNT(*) AS replycount FROM $table_threads t, $table_forums f WHERE f.fid='$fid' AND t.fid=f.fid");
	$countrow = $db->fetch_array($query);
	if(is_array($countrow)) {
		extract($countrow, EXTR_SKIP);
	}

	$query = $db->query("SELECT subject, lastpost, lastposter FROM $table_threads USE INDEX(displayorder) WHERE fid='$fid' ORDER BY lastpost DESC LIMIT 1");
	$thread = $db->fetch_array($query);
	$thread['subject'] = addslashes($thread['subject']);
	$thread['lastposter'] = addslashes($thread['lastposter']);
	$db->query("UPDATE $table_forums SET posts='$replycount', threads='$threadcount', lastpost='{$thread['subject']}\t{$thread['lastpost']}\t{$thread['lastposter']}' WHERE fid='$fid'", 'UNBUFFERED');
}

function updatethreadcount($tid) {
	global $db, $table_threads, $table_posts;
	$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid'");
	$replycount = $db->result($query, 0) - 1;
	if($replycount < 0) {
		$db->query("DELETE FROM $table_threads WHERE tid='$tid'");
		$db->query("DELETE FROM $table_polls WHERE tid='$tid'");
		return;
	}
	$query = $db->query("SELECT author, dateline FROM $table_posts WHERE tid='$tid' ORDER BY dateline DESC LIMIT 1");
	$lastpost = $db->fetch_array($query);
	$lastpost['author'] = addslashes($lastpost['author']);
	$db->query("UPDATE $table_threads SET replies='$replycount', lastposter='{$lastpost['author']}', lastpost='{$lastpost['dateline']}' WHERE tid='$tid'", 'UNBUFFERED');
}

?>