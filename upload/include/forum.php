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
1 forumselect
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function forum(&$forum) {
	global $timeformat, $dateformat, $discuz_uid, $groupid, $lastvisit, $moddisplay, $timeoffset, $hideprivate, $onlinehold;

	if(!$forum['viewperm'] || ($forum['viewperm'] && strstr($forum['viewperm'], "\t$groupid\t")) || $forum['allowview'] || strstr($forum['users'], "\t$discuz_uid\t")) {
		$forum['permission'] = 2;
	} elseif(!$hideprivate) {
		$forum['permission'] = 1;
	} else {
		return FALSE;
	}

	if($forum['icon']) {
		$forum['icon'] = '<a href="forumdisplay.php?fid='.$forum['fid'].'">'.image($forum['icon'], '', 'align="left"').'</a>';
	}

	$forum['lastpost'] = array_pad(explode("\t", $forum['lastpost']), 3, '');
	$forum['folder'] = $lastvisit < $forum['lastpost'][1] ? '<img src="'.IMGDIR.'/red_forum.gif">' : '<img src="'.IMGDIR.'/forum.gif">';

	if($forum['lastpost'][0]) {
		$forum['lastpost'][0] = cutstr($forum['lastpost'][0],31);
		$forum['lastpost'][1] = gmdate("$dateformat $timeformat", $forum['lastpost'][1] + $timeoffset * 3600);
		if($forum['lastpost'][2]) {
			$forum['lastpost'][2] = "<a href=\"viewpro.php?username=".rawurlencode($forum['lastpost'][2])."\">".$forum['lastpost'][2]."</a>";
		}
	} else {
		$forum['lastpost'] = '';
	}

	$forum['moderator'] = moddisplay($forum['moderator'], $moddisplay).'&nbsp;';

	return TRUE;
}


function forumselect() {
	global $_DCACHE, $discuz_uid, $groupid, $accessmasks, $db, $table_access;

	if ($accessmasks) {
		$query = $db->query("SELECT fid FROM $table_access WHERE uid='$discuz_uid' AND allowview='1'");
		while($allow = $db->fetch_array($query)) {
			$forumAllow[] = $allow['fid'];
		}
	}

	$forumlist = '';
	if(!isset($_DCACHE['forums'])) {
		require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
	}

	foreach($_DCACHE['forums'] as $fid1 => $forum1) {
		if($forum1['type'] == 'group') {
			$forumlist .= '<option value="">'.$forum1['name'].'</option>';
			foreach($_DCACHE['forums'] as $fid2 => $forum2) {
				if($forum2['fup'] == $fid1 && $forum2['type'] == 'forum' && (!$forum2['viewperm'] || ($forum2['viewperm'] && strstr($forum2['viewperm'], "\t$groupid\t")) || $forum2['allowview'] || strstr($forum2['users'], "\t$discuz_uid\t") || ($accessmasks && in_array($forum2['fid'], $forumAllow)))) {
					$forumlist .= '<option value="'.$fid2.'">&nbsp; &gt; '.$forum2['name'].'</option>';
					foreach($_DCACHE['forums'] as $fid3 => $forum3) {
						if($forum3['fup'] == $fid2 && $forum3['type'] == 'sub' && (!$forum3['viewperm'] || ($forum3['viewperm'] && strstr($forum3['viewperm'], "\t$groupid\t")) || strstr($forum3['users'], "\t$discuz_uid\t") || ($accessmasks && in_array($forum3['fid'], $forumAllow)))) {
							$forumlist .= '<option value="'.$fid3.'">&nbsp; &nbsp; &nbsp; &gt; '.$forum3['name'].'</option>';
						}
					}
				}
			}
			$forumlist .= '<option value="">&nbsp;</option>';
		} elseif(!$forum1['fup'] && $forum1['type'] == 'forum' && (!$forum1['viewperm'] || ($forum1['viewperm'] && strstr($forum1['viewperm'], "\t$groupid\t")) || strstr($forum1['users'], "\t$discuz_uid\t") || ($accessmasks && in_array($forum1['fid'], $forumAllow)))) {
			$forumlist .= '<option value="'.$fid1.'"> &nbsp; &gt; '.$forum1['name'].'</option>';
			foreach($_DCACHE['forums'] as $fid2 => $forum2) {
				if($forum2['fup'] == $fid1 && $forum2['type'] == 'sub' && (!$forum2['viewperm'] || ($forum2['viewperm'] && strstr($forum2['viewperm'], "\t$groupid\t")) || strstr($forum2['users'], "\t$discuz_uid\t") || ($accessmasks && in_array($forum2['fid'], $forumAllow)))) {
					$forumlist .= '<option value="'.$fid2.'">&nbsp; &nbsp; &nbsp; &gt; '.$forum2['name'].'</option>';
				}
			}
			$forumlist .= '<option value="">&nbsp;</option>';
		}

	}

	return $forumlist;
} 

function moddisplay($mod, $moddisplay) {
	if($moddisplay == 'selectbox') {
		$modlist .= '<img src="images/common/online_moderator.gif" align="absmiddle"><select name="modlist" style="width: 85">';

		if($mod) {
			foreach(explode(',', $mod) as $moderator) {
				$moderator = trim($moderator);
				$modlist .= '<option value="'.rawurlencode($moderator).'">'.$moderator.'</option>';
			}
		}
		$modlist .= '</select>';
		return $modlist;
	} else {
		if($moddisplay == 'forumdisplay') {
			$modicon = '<img src="images/common/online_moderator.gif" align="absmiddle"> ';
		} else {
			$modicon = '';
		}
		if($mod) {
			$modlist = $comma = '';
			foreach(explode(',', $mod) as $moderator) {
				$moderator = trim($moderator);
				$modlist .= $comma.$modicon.'<a href="viewpro.php?username='.rawurlencode($moderator).'">'.$moderator.'</a>';
				$comma = ', ';
			}
		} else {
			$modlist = '';
		}
		return $modlist;
	}
}

?>