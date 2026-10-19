<?

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
1 编辑附件中，右边的附件链接如果设了防盗链，就不能点击了
2 图标排列问题
3 主题图标更新问题
4 附件数量限制问题
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 13;

$query = $db->query("SELECT pid FROM $table_posts WHERE tid='$tid' ORDER BY dateline LIMIT 1");
$isfirstpost = $db->result($query, 0) == $pid ? 1 : 0;

$query = $db->query("SELECT m.adminid, p.authorid, p.dateline, p.aid, p.dateline FROM $table_posts p LEFT JOIN $table_members m ON m.uid=p.authorid WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");
$orig = $db->fetch_array($query);
$isorigauthor = $discuz_uid && $discuz_uid == $orig['authorid'];
$alloweditpost = $alloweditpost && !(in_array($orig['adminid'], array(1, 2, 3)) && $adminid > $orig['adminid']) ? 1 : 0;

if((!$ismoderator || !$alloweditpost) && !$isorigauthor) {
	showmessage('post_edit_nopermission', NULL, 'HALTED');
}

if(!submitcheck('editsubmit')) {

	$icons = '';
	if(is_array($_DCACHE['icons']) && $isfirstpost) {
		$key = 1;
		foreach($_DCACHE['icons'] as $id => $icon) {
			$icons .= ' <input type="radio" name="iconid" value="'.$id.'" '.($thread['iconid'] == $id ? 'checked' : '').'><img src="'.SMDIR.'/'.$icon.'">';
			$icons .= !(++$key % 10) ? '<br>' : '';
		}
	}

	$query = $db->query("SELECT * FROM $table_posts WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");
	$postinfo = $db->fetch_array($query);

	if($delayeditpost && ($timestamp -$postinfo['dateline'] >$delayeditpost) && !$ismoderator ) {
		showmessage('post_edit_timeout', NULL, 'HALTED');
	}

	$usesigcheck = $postinfo['usesig'] ? 'checked="checked"' : NULL;
	$urloffcheck = $postinfo['parseurloff'] ? 'checked="checked"' : NULL;
	$smileyoffcheck = $postinfo['smileyoff'] == 1 ? 'checked="checked"' : NULL;
	$codeoffcheck = $postinfo['bbcodeoff'] == 1 ? 'checked="checked"' : NULL;

	if($alloweditpoll && $thread['poll']) {
		$query = $db->query("SELECT pollopts FROM $table_polls WHERE tid='$tid'");
		$polloptions = unserialize($db->result($query, 0));
		for($i = 0; $i < count($polloptions['options']); $i++) {
			$polloptions['options'][$i][0] = htmlspecialchars(stripslashes($polloptions['options'][$i][0]))."\n";
		}
	} else {
		$polloptions = '';
	}

	if($allowpostattach) {
		if($postinfo['aid']) {
			require_once DISCUZ_ROOT.'./include/attachment.php';
			$attachquery = $db->query("select * from $table_attachments where pid = '$postinfo[pid]' ORDER BY  aid");
			while($attaches = $db->fetch_array($attachquery)) {
				$extension = strtolower(fileext($attaches['filename']));
				$attaches['attachicon'] = attachtype($extension."\t".$attaches['filetype']);
				$attaches['attachsize'] = sizecount($attaches['filesize']);
				$attaches['dateline'] =	$attaches['dateline']?gmdate("$dateformat $timeformat", $attaches['dateline'] + $timeoffset * 3600):gmdate("$dateformat $timeformat", $orig['dateline'] + $timeoffset * 3600);
				$attaches[checkid] = substr(md5($attaches['filesize']),0,5);
				$postinfo[attach_list][] = $attaches;
			}
		}
	}

	$postinfo['subject'] = str_replace('"', "&quot;", $postinfo['subject']);
	$postinfo['message'] = dhtmlspecialchars($postinfo['message']);
	$postinfo['message'] = preg_replace("/\n{2}\[\[i\] Last edited by .+? on .+? at .+? \[\/i\]\]$/s", '', $postinfo['message']);
	if($previewpost) {
		$postinfo['message'] = $message;
	}
	
	$multiattach = $allowpostattach && $attach_editpost ? array_fill(1,$attach_editpost,'1'):0;

	include template('post_editpost');

} else {

	if( $editmethord == 'post' && !$delete ) {

		if($post_invalid = checkpost()) {
			showmessage($post_invalid);
		}

		$viewpermadd = ($allowsetviewperm && $isfirstpost) ? "creditsrequire='$viewperm'" : NULL;

		if($isfirstpost) {
			if($subject == '' || $message == '') {
				showmessage('post_sm_isnull');
			}

			$pollopts = '';
			if($alloweditpoll && $thread['poll'] && trim($polloptions)) {
				$query = $db->query("SELECT pollopts FROM $table_polls WHERE tid='$tid'");
				$pollarray = unserialize($db->result($query, 0));

				$optsdeleted = 0;
				$pollarray['max'] = 0;
				foreach($polloptions as $key => $option) {
					if(trim($option)) {
						$pollarray['options'][$key][0] = $option;
						if($pollarray['options'][$key][1] > $pollarray['max']) {
							$pollarray['max'] = $pollarray['options'][$key][1];
						
						}
					} else {
						$optsdeleted = 1;
						$pollarray['total'] -= $pollarray['options'][$key][1];
						unset($pollarray['options'][$key]);
					}
				}

				if($optsdeleted) {
					$newoptions = array();
					foreach($pollarray['options'] as $option) {
						$newoptions[] = $option;
					}
					$pollarray['options'] = $newoptions;
					unset($newoptions);
				}

				$pollarray['multiple'] = $multiplepoll;
				$pollopts = addslashes(serialize($pollarray));
			}

			$db->query("UPDATE $table_threads SET iconid='$iconid', subject='$subject' WHERE tid='$tid'", 'UNBUFFERED');
			if($pollopts) {
				$db->query("UPDATE $table_polls SET pollopts='$pollopts' WHERE tid='$tid'", 'UNBUFFERED');
			}
		} else {
			if($subject == '' && $message == '') {
				showmessage('post_sm_isnull');
			}
		}

		if($editedby && ($timestamp - $orig['dateline']) > 60 && $adminid != 1){
			$editdate = gmdate($_DCACHE['settings']['dateformat'], $timestamp + $timeoffset * 3600);
			$edittime = gmdate($_DCACHE['settings']['timeformat'], $timestamp + $timeoffset * 3600);
			$message .= "\n\n[[i] Last edited by $discuz_user on $editdate at $edittime [/i]]";
		}

		$bbcodeoff = checkbbcodes($message, $bbcodeoff);
		$smileyoff = checksmilies($message, $smileyoff);
		
		$db->query("UPDATE $table_posts SET message='$message', usesig='$usesig', bbcodeoff='$bbcodeoff', parseurloff='$parseurloff',	smileyoff='$smileyoff', subject='$subject' WHERE pid='$pid'");
		if($viewpermadd) {
			$db->query("UPDATE $table_threads SET $viewpermadd WHERE tid='$tid'", 'UNBUFFERED');
		}
		$modaction = 'editpost';

	}elseif ($editmethord == 'attach') {
		$post_attaches =array();
		$post_attaches_count = $delAtt = $del_aids = $uploadCount = 0;
		if ($orig['aid']){
			$attachquery = $db->query("select * from $table_attachments where pid ='$pid'");
			while($att = $db->fetch_array($attachquery)) {
				$saveaid = 1;
				if(is_array($deleteaids) && count($deleteaids)){
					if (in_array($att[aid],$deleteaids)){
						$del_aids .=','.$att[aid];	@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$att['attachment']);
						$saveaid = 0;
						$delAtt++;
					}
				}
				if ($saveaid) {
					if ($allowsetattachperm && $att[creditsrequire] != $origattachperm["$att[aid]"]){
						$att[creditsrequire] = $origattachperm["$att[aid]"];
						$db->query("UPDATE $table_attachments set creditsrequire='$att[creditsrequire]' WHERE aid='$att[aid]'", 'UNBUFFERED');
					}
					$post_attaches_count ++;
				}
			}
			if ($del_aids) {
				$db->query("DELETE FROM $table_attachments WHERE pid='$pid' and aid in($del_aids)", 'UNBUFFERED');
			}
		}

		foreach ($attach_name as $tmp) {
			if (trim($tmp)) $uploadCount++;
		}

		if ($attach_max && ($uploadCount + $post_attaches_count) >$attach_max){
			showmessage('attachment_edit_more', "post.php?action=edit&fid=$fid&tid=$tid&pid=$pid&page=$page");
		}

		$post_attaches = attach_upload();
		if($post_attaches && $allowpostattach) {
			foreach( $post_attaches as $v) {
				$db->query("INSERT INTO $table_attachments (tid, pid ,uid, creditsrequire, filename, filetype, filesize, attachment, dateline, downloads)
					VALUES ('$tid','$pid','$discuz_uid', '$v[creditsrequire]', '$v[filename]', '$v[filetype]', '$v[filesize]', '$v[attachment]','$timestamp', '0')");
				$post_attaches_count++;
			}
			unset($post_attaches, $v);
		}

		if ( $delAtt || $uploadCount ){
			updatethread_type($tid , $thread['attachment']);
			if($orig['aid'] <> $post_attaches_count){ 
				$db->query("UPDATE $table_posts SET aid='$post_attaches_count' WHERE pid='$pid'", 'UNBUFFERED');
			}
		}

		$modaction = 'editattach';

	} elseif( $editmethord == 'post' && $delete ) {

		if(!$allowdelpost && !$isorigauthor) {
			showmessage('post_edit_nopermission', NULL, 'HALTED');
		}

		if(!$isfirstpost) {
			updatemember('-', $orig['authorid'], $deletedcredits);
			if ($orig['aid']){
				$query = $db->query("SELECT attachment FROM $table_attachments WHERE pid='$pid'");
				while($post_attachment = $db->fetch_array($query)) {
					@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$post_attachment['attachment']);
				}
				$db->query("DELETE FROM $table_attachments WHERE pid='$pid'");
				updatethread_type($tid , $thread['attachment']);
			}
			$db->query("DELETE FROM $table_posts WHERE pid='$pid'", 'UNBUFFERED');
			updateforumcount($fid);
			updatethreadcount($tid);

			$modaction = 'delposts';

		} else {

			if(!$allowdelpost && $isorigauthor && $thread['replies'] >= 1) {
				showmessage('post_edit_nopermission', NULL, 'HALTED');
			}

			$uids = $comma = '';
			$query = $db->query("SELECT authorid FROM $table_posts WHERE tid='$tid'");
			while($post = $db->fetch_array($query)) {
				$uids .= "$comma$post[authorid]";
				$comma = ',';
			}
			updatemember('-', $uids, $deletedcredits);

			$db->query("DELETE FROM $table_threads WHERE tid='$tid' OR closed='$tid'", 'UNBUFFERED');
			$db->query("DELETE FROM $table_polls WHERE tid='$tid'", 'UNBUFFERED');

			$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid='$tid'");
			while($thread_attachment = $db->fetch_array($query)) {
				@unlink(DISCUZ_ROOT.'./'.$attachdir.'/'.$thread_attachment['attachment']);
			}

			$db->query("DELETE FROM $table_attachments WHERE tid='$tid'", 'UNBUFFERED');
			$db->query("DELETE FROM $table_posts WHERE tid='$tid'");
			updateforumcount($fid);

			$modaction = 'delete';

		}

	}

	if(!$isorigauthor) {
		@$fp = fopen(DISCUZ_ROOT.'./forumdata/modslog.php', 'a');
		@flock($fp, 2);
		@fwrite($fp, "$timestamp\t$discuz_user\t$groupid\t$onlineip\t$forum[fid]\t$forum[name]\t$thread[tid]\t$thread[subject]\t$modaction\n");
		@fclose($fp);
	}

	if($delete && $isfirstpost) {
		showmessage('post_edit_delete_succeed', "forumdisplay.php?fid=$fid");
	}elseif($editmethord == 'attach') {
		showmessage('attachment_edit_succeed', "post.php?action=edit&fid=$fid&tid=$tid&pid=$pid&page=$page");
	} else {
		showmessage('post_edit_succeed', "viewthread.php?tid=$tid&page=$page#pid$pid");
	}

}

?>