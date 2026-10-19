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
1 未对后台添加的用户名作检查
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

if($action == 'addmember') {

	if(!submitcheck('addsubmit')) {
		updatecache('settings');
		$groupselect = '';
		$query = $db->query("SELECT groupid, type, grouptitle FROM $table_usergroups WHERE groupid IN (2,3,8) OR (type='member' AND creditshigher='0') OR type='special' ORDER BY creditslower DESC, groupid DESC");
		while($group = $db->fetch_array($query)) {
			if($group['type'] == 'system') {
				$group['adminid'] = $group['groupid'] <= 3 ? $group['groupid'] : -1;
			} elseif($group['type'] == 'special') {
				$group['adminid'] = -1;
			} else {
				$group['adminid'] = 0;
			}
			$groupselect .= "<option value=\"{$group['adminid']}|{$group['groupid']}\">{$group['grouptitle']}</option>\n";
		}
		include CP_TPL.'member_add.php';

	} else {

		$newusername = trim($newusername);
		if(preg_match("/^\s*$|^c:\\con\\con$||[%,\*\"\s\t\<\>\&]|^游客|^Guest/is", $newusername)) {
			cpmsg('members_add_username_illegal');
		} elseif (strlen($newusername) > 15) {
			cpmsg('members_add_username_toolong');
		}
		$newpassword = trim($newpassword);
		$newemail = trim($newemail);

		if(!$newusername || !$newpassword || !$newemail) {
			cpmsg('members_add_invalid');
		}

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE username='$newusername'");
		if($db->result($query, 0)) {
			cpmsg('members_add_username_duplicate');
		}

		$newgroupid = explode('|', $newgroupid);
		$db->query("INSERT INTO $table_members (username, password, gender, adminid, groupid, regip, regdate, lastvisit, lastactivity, postnum, credit, email, tpp, ppp, styleid, dateformat, timeformat, showemail, newsletter, timeoffset)
			VALUES ('$newusername', '".dpassword_hash($newpassword)."', '0', '$newgroupid[0]', '$newgroupid[1]', 'Manual Acting', '$timestamp', '$timestamp', '$timestamp', '0', '0', '$newemail', '0', '0', '0', '{$_DCACHE['settings']['dateformat']}', '{$_DCACHE['settings']['timeformat']}', '1', '1', '{$_DCACHE['settings']['timeoffset']}')");

		if($emailnotify == 'yes') {
			sendmail($newemail, 'add_member_subject', 'add_member_message');
		}

		updatecache('settings');
		cpmsg('members_add_succeed');
	}

} elseif($action == 'members') {

	if(!submitcheck('searchsubmit', 1) && !submitcheck('deletesubmit') && !submitcheck('editsubmit')) {

		$adminselect = $groupselect = '';
		
		$query = $db->query("SELECT admingid, admintitle FROM $table_admingroups ORDER BY admingid");
		while($agroup = $db->fetch_array($query)) {
				$adminselect .= "<option value=\"{$agroup['admingid']}\" \$aselect[{$agroup['admingid']}]>{$agroup['admintitle']}</option>\n";
		}
		$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups WHERE groupid >'3' and groupid<>'7' ORDER BY (creditshigher<>'0' || creditslower<>'0'), creditslower");
		while($group = $db->fetch_array($query)) {
			$groupselect .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
		}
		include CP_TPL.'member_search.php';

	} elseif(submitcheck('searchsubmit', 1) || submitcheck('deletesubmit')) {

		$memberperpage = 100;

		if(!$page) {
			$page = 1;
		}
		$offset = ($page - 1) * $memberperpage;

		$conditions = '1';
		$conditions .= $username != '' ? " AND BINARY username LIKE '".str_replace('*', '%', $username)."'" : NULL;
		$conditions .= $srchemail != '' ? " AND email LIKE '".str_replace('*', '%', $srchemail)."'" : NULL;
		$conditions .= $admingroupid != '' ? " AND adminid='$admingroupid'" : NULL;
		$conditions .= $usergroupid != '' ? " AND groupid='$usergroupid'" : NULL;

		$conditions .= $regdatebefore != '' ? " AND regdate<'".strtotime($regdatebefore)."'" : NULL;
		$conditions .= $regdateafter != '' ? " AND regdate>'".strtotime($regdateafter)."'" : NULL;
		$conditions .= $srchlocation != '' ? " AND location LIKE '%$srchlocation%'" : NULL;
		$conditions .= $srchsig != '' ? " AND signature LIKE '%$srchsig%'" : NULL;
   
		$conditions .= $creditshigher != '' ? " AND credit>'$creditshigher'" : NULL;
		$conditions .= $creditslower != '' ? " AND credit<'$creditslower'" : NULL;
		$conditions .= $postshigher != '' ? " AND postnum>'$postshigher'" : NULL;
		$conditions .= $postslower != '' ? " AND postnum<'$postslower'" : NULL;

		$conditions .= $awaydays != '' ? " AND lastvisit<'".($timestamp - $awaydays * 86400)."'" : NULL;
		$conditions .= $regip != '' ? " AND regip LIKE '$regip%'" : NULL;
		$conditions .= $lastip != '' ? " AND lastip LIKE '$lastip%'" : NULL;

		if(!$conditions) {
			cpmsg('members_search_invalid');
		}
		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE $conditions");
		$membernum = $db->result($query, 0);

		if(submitcheck('searchsubmit', 1)) {
		
			$multipage = multi($membernum, $memberperpage, $page, "admincp.php?action=members&searchsubmit=yes&username=$username&srchemail=$srchemail&admingroupid=$admingroupid&usergroupid=$usergroupid&regdatebefore=$regdatebefore&regdateafter=$regdateafter&creditshigher=$creditshigher&creditslower=$creditslower&postshigher=$postshigher&postslower=$postslower&srchlocation=$srchlocation&srchsig=$srchsig&awaydays=$awaydays&regip=$regip");

			$agselect = $ugselect = '<option value="">&nbsp;</option>';

			$query = $db->query("SELECT admingid, admintitle FROM $table_admingroups ORDER BY admingid");
			while($agroup = $db->fetch_array($query)) {
				$agselect .= "<option value=\"{$agroup['admingid']}\" \$aselect[{$agroup['admingid']}]>{$agroup['admintitle']}</option>\n";
			}

			$query = $db->query("SELECT groupid, type, grouptitle, creditshigher, creditslower FROM $table_usergroups WHERE groupid<>'7' ORDER BY (creditshigher<>'0' || creditslower<>'0'), creditslower");
			while($group = $db->fetch_array($query)) {
				if($group['type'] == 'system') {
					$group['adminid'] = $group['groupid'] <= 3 ? $group['groupid'] : -1;
				} elseif($group['type'] == 'special') {
					$group['adminid'] = -1;
				} else {
					$group['adminid'] = 0;
				}
				$ugselect .= "<option value=\"{$group['adminid']}|{$group['groupid']}\" \$uselect[{$group['groupid']}]>{$group['grouptitle']}</option>\n";
			}

			$query = $db->query("SELECT * FROM $table_members WHERE $conditions LIMIT $offset, $memberperpage");
			while($member = $db->fetch_array($query)) {
				$aselect = array($member['adminid'] => 'selected="selected"');
				$uselect = array($member['groupid'] => 'selected="selected"');

				$admingroups = dinterpolate($agselect);
				$usergroups = dinterpolate($ugselect);

				$members .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$member['uid']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><a href=\"viewpro.php?uid={$member['uid']}\" target=\"_blank\">{$member['username']}</a></td>\n".
					"<td bgcolor=\"".ALTBG1."\">{$member['credit']}</td>\n".
					"<td bgcolor=\"".ALTBG2."\">{$member['postnum']}</td>\n".
					"<td bgcolor=\"".ALTBG1."\"><select name=\"adminidnew[{$member['uid']}]\">$admingroups</td>\n".
					"<td bgcolor=\"".ALTBG2."\"><select name=\"groupidnew[{$member['uid']}]\">$usergroups</td>\n".
					"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\" name=\"usercstatus[{$member['uid']}]\" value=\"{$member['customstatus']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><a href=\"admincp.php?action=access&uid={$member['uid']}\">[{$lang['detail']}]</a></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><a href=\"admincp.php?action=memberprofile&uid={$member['uid']}\">[{$lang['detail']}]</a></td></tr>\n";
			}
			include CP_TPL.'member_edit.php';

		} elseif(submitcheck('deletesubmit')) {
			if(!$confirmed) {
				cpmsg('members_delete_confirm', "admincp.php?action=members&deletesubmit=yes&username=$username&srchemail=$srchemail&admingroupid=$admingroupid&usergroupid=$usergroupid&regdatebefore=$regdatebefore&regdateafter=$regdateafter&creditshigher=$creditshigher&creditslower=$creditslower&postshigher=$postshigher&postslower=$postslower&srchlocation=$srchlocation&srchsig=$srchsig&awaydays=$awaydays&regip=$regip", 'form');
			} else {
				$query = $db->query("DELETE FROM $table_members WHERE $conditions");
				$numdeleted = $db->affected_rows();
        
				updatecache('settings');
				cpmsg('members_delete_succeed');
			}
		}

	} elseif(submitcheck('editsubmit')) {
		if(is_array($delete)) {
			$ids = $comma = '';
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ', ';
			}
			$db->query("DELETE FROM $table_members WHERE uid IN ($ids)");
			updatecache('settings');
		}
		if(is_array($groupidnew)) {
			foreach($groupidnew as $id => $usergroupidnew) {
				list($useradminid, $usergroupid) = explode('|', $usergroupidnew);
				if(empty($usergroupid)) {
					$query = $db->query("SELECT username FROM $table_members WHERE uid='$id'");
					$username = $db->result($query, 0);
					cpmsg('members_edit_group_invalid');
				}
				$useradminid = $adminidnew[$id] ? $adminidnew[$id] : $useradminid;
				$db->query("UPDATE $table_members SET adminid='$useradminid', groupid='$usergroupid', customstatus='$usercstatus[$id]' WHERE uid='$id'");
			}
		}
		cpmsg('members_edit_succeed');
	}

} elseif($action == 'mod_members') {

	if(empty($extr) && !submitcheck('membersubmit', 1) && !submitcheck('editsubmit', 1)) {
		include CP_TPL.'member_mod.php';

	} else {

		$query = $db->query("SELECT * FROM $table_members WHERE username='$username' OR uid='$extr'");
		if(!$member = $db->fetch_array($query)) {
			cpmsg('members_edit_nonexistence');
		} elseif($member['adminid'] && $member['groupid'] != 4 && $member['groupid'] != 5) {
			cpmsg('members_edit_illegal');
		}

		if(!empty($extr) || submitcheck('membersubmit')) {

			if($member['groupid'] == 4) {
				$check['post'] = 'checked';
			} elseif($member['groupid'] == 5) {
				$check['visit'] = 'checked';
			} else {
				$check['none'] = 'checked';
			}

			echo '<br><form method="post" action="admincp.php?action=mod_members&username='.rawurlencode($member['username']).'&formhash='.FORMHASH.'>';

			showtype("{$lang['members_edit']} - {$member['username']}", 'top');

			if($allowbanuser) {
				showsetting('members_edit_ban', '', '', '<input type="radio" name="bannew" value="" '.$check['none'].'> '.$lang['members_edit_ban_none'].'<br><input type="radio" name="bannew" value="post" '.$check['post'].'> '.$lang['members_edit_ban_post'].'<br><input type="radio" name="bannew" value="visit" '.$check['visit'].'> '.$lang['members_edit_ban_visit']);
			}
			if($allowedituser) {
				showsetting('members_edit_location', 'locationnew', $member['location'], 'text');
				showsetting('members_edit_bio', 'bionew', $member['bio'], 'textarea');
				showsetting('members_edit_signature', 'signaturenew', $member['signature'], 'textarea');
			}
			showtype('', 'bottom');

			echo '<br><br><center><input type="submit" name="editsubmit" value="'.$lang['submit'].'"></center></form>';

		} elseif(submitcheck('editsubmit', 1)) {

			$sql = 'uid=uid';

			if($allowbanuser) {
				if($bannew == 'post') {
					$sql .= ', adminid=\'-1\', groupid=\'4\'';
				} elseif($bannew == 'visit') {
					$sql .= ', adminid=\'-1\', groupid=\'5\'';
				} elseif($member['groupid'] == 4 || $member['groupid'] == 5) {
					$query = $db->query("SELECT groupid FROM $table_usergroups WHERE creditshigher<'{$member['credit']}' AND creditslower>'{$member['credit']}'");
					$sql  .= ', groupid=\''.$db->result($query, 0).'\'';
				}
			}

			if($allowedituser) {
				$locationnew = cutstr(dhtmlspecialchars($locationnew), 28);
				$bionew = cutstr(dhtmlspecialchars($bionew),800);
				$sql .= ", location='$locationnew', bio='$bionew', signature='$signaturenew'";
			}

			$db->query("UPDATE $table_members SET $sql WHERE username='$username'");
			cpmsg('members_edit_succeed', 'admincp.php?action=mod_members');

		}

	}

} elseif($action == 'access') {

	$query = $db->query("SELECT username FROM $table_members WHERE uid='$uid'");
	if(!$member = $db->fetch_array($query)) {
		cpmsg('undefined_action');
	}

	require DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';

	if(!submitcheck('accesssubmit')) {

		$accessmasks = array();
		$query = $db->query("SELECT * FROM $table_access WHERE uid='$uid'");
		while($access = $db->fetch_array($query)) {
			$accessmasks[$access['fid']] = $access;
		}

		$members = '';
		foreach($_DCACHE['forums'] as $fid => $forum) {
			if($forum['type'] != 'group') {
				if(isset($accessmasks[$fid])) {
					$check = array(	'default'	=> '',
							'view'		=> ($accessmasks[$fid]['allowview'] ? 'checked' : ''),
							'post'		=> ($accessmasks[$fid]['allowpost'] ? 'checked' : ''),
							'reply'		=> ($accessmasks[$fid]['allowreply'] ? 'checked' : ''),
							'getattach'	=> ($accessmasks[$fid]['allowgetattach'] ? 'checked' : ''));
				} else {
					$check = array(	'default'	=> 'checked',
							'view'		=> '',
							'post'		=> '',
							'reply'		=> '',
							'getattach'	=> '');
				}

				$members .= "<tr><td bgcolor=\"".ALTBG1."\" width=\"25%\"><a href=\"admincp.php?action=forumdetail&fid={$forum['fid']}\">{$forum['name']}</a></td>".
						"<td bgcolor=\"".ALTBG2."\" width=\"15%\" align=\"center\"><input type=\"checkbox\" name=\"defaultnew[$fid]\" value=\"1\" {$check['default']}></td>\n".
						"<td bgcolor=\"".ALTBG1."\" width=\"15%\" align=\"center\"><input type=\"checkbox\" name=\"allowviewnew[$fid]\" value=\"1\" {$check['view']}></td>\n".
						"<td bgcolor=\"".ALTBG2."\" width=\"15%\" align=\"center\"><input type=\"checkbox\" name=\"allowpostnew[$fid]\" value=\"1\" {$check['post']}></td>\n".
						"<td bgcolor=\"".ALTBG1."\" width=\"15%\" align=\"center\"><input type=\"checkbox\" name=\"allowreplynew[$fid]\" value=\"1\" {$check['reply']}></td>\n".
						"<td bgcolor=\"".ALTBG2."\" width=\"15%\" align=\"center\"><input type=\"checkbox\" name=\"allowgetattachnew[$fid]\" value=\"1\" {$check['getattach']}></td></tr>";
			}
		}
		include CP_TPL.'member_access.php';

	} else {

		$accessarray = array();
		if(is_array($_DCACHE['forums'])) {
			foreach($_DCACHE['forums'] as $fid => $forum) {
				if($forum['type'] != 'group') {
					if(!$defaultnew[$fid] && ($allowviewnew[$fid] || $allowpostnew[$fid] || $allowreplynew[$fid] || $allowgetattachnew[$fid])) {
						$accessarray[$fid] = "'$allowviewnew[$fid]', '$allowpostnew[$fid]', '$allowreplynew[$fid]', '$allowgetattachnew[$fid]'";
					}
				}
			}
		}

		$db->query("DELETE FROM $table_access WHERE uid='$uid'");
		$db->query("UPDATE $table_members SET accessmasks='".($accessarray ? 1 : 0)."' WHERE uid='$uid'");

		foreach($accessarray as $fid => $access) {
			$db->query("INSERT INTO $table_access (uid, fid, allowview, allowpost, allowreply, allowgetattach)
					VALUES ('$uid', '$fid', $access)");
		}

		updatecache('forums');
		cpmsg('access_succeed');

	}

} elseif($action == 'memberprofile') {

	$uid = empty($uid) ? $extr : $uid;

	$query = $db->query("SELECT m.*, u.type FROM $table_members m LEFT JOIN $table_usergroups u USING(groupid) WHERE m.uid='$uid'");
	if(!$member = $db->fetch_array($query)) {
		cpmsg('undefined_action');
	}

	if(!submitcheck('editsubmit')) {

		$styleselect = "<select name=\"styleidnew\">\n<option value=\"\">{$lang['default']}</option>";
		$query = $db->query("SELECT styleid, name FROM $table_styles");
		while($style = $db->fetch_array($query)) {
			$styleselect .= "<option value=\"{$style['styleid']}\" ".($style['styleid'] == $member['styleid'] ? 'selected="selected"' : NULL).">{$style['name']}</option>\n";
		}
		$styleselect .= '</select>';

		$member['dateformat'] = str_replace('n', 'mm', $member['dateformat']);
		$member['dateformat'] = str_replace('j', 'dd', $member['dateformat']);
		$member['dateformat'] = str_replace('y', 'yy', $member['dateformat']);
		$member['dateformat'] = str_replace('Y', 'yyyy', $member['dateformat']);
		$member['timeformat'] == 'H:i' ? $check24 = 'checked="checked"' : $check12 = 'checked="checked"';

		$member['regdate'] = gmdate('Y-n-j', $member['regdate']);
		$member['lastvisit'] = gmdate('Y-n-j', $member['lastvisit']);

		echo "<br><form method=\"post\" action=\"admincp.php?action=memberprofile&uid=$uid&formhash=".FORMHASH."\">";

		showtype("{$lang['members_edit']} - {$member['username']}", 'top');
		showsetting('members_edit_username', 'usernamenew', $member['username'], 'text');
		showsetting('members_edit_password', 'passwordnew', '', 'text');
		showsetting('members_edit_email', 'emailnew', $member['email'], 'text');
		showsetting('members_edit_clearquestion', 'clearquestion', !$member['secques'], 'radio');
		showsetting('members_edit_credit', 'creditnew', $member['credit'], 'text');
		showsetting('members_edit_postnum', 'postnumnew', $member['postnum'], 'text');
		showsetting('members_edit_regip', 'regipnew', $member['regip'], 'text');
		showsetting('members_edit_regdate', 'regdatenew', $member['regdate'], 'text');
		showsetting('members_edit_lastvisit', 'lastvisitnew', $member['lastvisit'], 'text');
		
		showtype('members_edit_info');
		showsetting('members_edit_site', 'sitenew', $member['site'], 'text');
		showsetting('members_edit_oicq', 'oicqnew', $member['oicq'], 'text');
		showsetting('members_edit_icq', 'icqnew', $member['icq'], 'text');
		showsetting('members_edit_yahoo', 'yahoonew', $member['yahoo'], 'text');
		showsetting('members_edit_msn', 'msnnew', $member['msn'], 'text');
		showsetting('members_edit_location', 'locationnew', $member['location'], 'text');
		showsetting('members_edit_bday', 'bdaynew', $member['bday'], 'text');
		showsetting('members_edit_avatar', 'avatarnew', $member['avatar'], 'text');
		showsetting('members_edit_bio', 'bionew', $member['bio'], 'textarea');
		showsetting('members_edit_signature', 'signaturenew', $member['signature'], 'textarea');

		showtype('members_edit_option');
		showsetting('members_edit_style', '', '', $styleselect);
		showsetting('members_edit_tpp', 'tppnew', $member['tpp'], 'text');
		showsetting('members_edit_ppp', 'pppnew', $member['ppp'], 'text');
		showsetting('members_edit_cstatus', 'cstatusnew', $member['customstatus'], 'text');
		showsetting('members_edit_timeformat', '', '', '<input type="radio" name="timeformatnew" value="24" '.$check24.'> 24 Hour <input type="radio" name="timeformatnew" value="12" '.$check12.'> 12 Hour');
		showsetting('members_edit_dateformat', 'dateformatnew', $member['dateformat'], 'text');
		showsetting('members_edit_timeoffset', 'timeoffsetnew', $member['timeoffset'], 'text');
		showsetting('members_edit_invisible', 'invisiblenew', $member['invisible'], 'radio');
		showsetting('members_edit_showemail', 'showemailnew', $member['showemail'], 'radio');
		showsetting('members_edit_newsletter', 'newsletternew', $member['newsletter'], 'radio');
		showsetting('members_edit_ignorepm', 'ignorepmnew', $member['ignorepm'], 'textarea');

		showtype('', 'bottom');

		echo '<br><br><center><input type="submit" name="editsubmit" value="'.$lang['submit'].'"></center></form>';

	} else {

		$usernameold = addslashes($member['username']);
		if(preg_match("/^\s*$|^c:\\con\\con$||[%,\*\"\s\t\<\>\&]|^游客|^Guest/is", $usernamenew)) {
			cpmsg('members_add_username_illegal');
		} elseif (strlen($usernamenew) > 15) {
			cpmsg('members_add_username_toolong');
		}
		if($usernamenew && $usernameold != $usernamenew) {
			$query = $db->query("SELECT uid FROM $table_members WHERE username='$usernamenew'");
			if(($db->result($query, 0)) && ($db->result($query, 0)) != $member['uid']) {
				cpmsg('members_edit_duplicate');
			}
			$db->query("UPDATE {$tablepre}announcements SET author='$usernamenew' WHERE author='$usernameold'");
			$db->query("UPDATE {$tablepre}banned SET admin='$usernamenew' WHERE admin='$usernameold'");
			$db->query("UPDATE {$tablepre}forums SET lastpost=REPLACE(lastpost, '\t$usernameold', '\t$usernamenew')");
			$db->query("UPDATE {$tablepre}members SET username='$usernamenew' WHERE uid='{$member['uid']}'");
			$db->query("UPDATE {$tablepre}pms SET msgfrom='$usernamenew' WHERE msgfromid='{$member['uid']}'");
			$db->query("UPDATE {$tablepre}posts SET author='$usernamenew' WHERE authorid='{$member['uid']}'");
			$db->query("UPDATE {$tablepre}threads SET author='$usernamenew' WHERE authorid='{$member['uid']}'");
			$db->query("UPDATE {$tablepre}threads SET lastposter='$usernamenew' WHERE lastposter='$usernameold'");

			$username = $usernamenew;
		}

		$creditnew = intval($creditnew);
		if($member['type'] == 'member' && $member['credit'] != $creditnew) {
			$query = $db->query("SELECT groupid FROM $table_usergroups WHERE type='member' AND $creditnew>=creditshigher AND $creditnew<creditslower");
			$groupidadd = ', groupid=\''.$db->result($query, 0).'\'';
		} else {
			$groupidadd = '';
		}

		$regdatenew = strtotime($regdatenew);
		$lastvisitnew = strtotime($lastvisitnew);

		$passwordadd = $passwordnew ? ", password='".dpassword_hash($passwordnew)."'" : NULL;
		$secquesadd = $clearquestion ? ", secques=''" : NULL;

		$dateformatnew = str_replace('mm', 'n', $dateformatnew);
		$dateformatnew = str_replace('dd', 'j', $dateformatnew);
		$dateformatnew = str_replace('yyyy', 'Y', $dateformatnew);
		$dateformatnew = str_replace('yy', 'y', $dateformatnew);
		$timeformatnew = $timeformatnew == '24' ? 'H:i' : 'h:i A';

		$db->query("UPDATE $table_members SET email='$emailnew', credit='$creditnew', postnum='$postnumnew', regip='$regipnew',
				regdate='$regdatenew', lastvisit='$lastvisitnew', site='$sitenew', oicq='$oicqnew', icq='$icqnew',
				yahoo='$yahoonew', msn='$msnnew', location='$locationnew', bday='$bdaynew', bio='$bionew',
				styleid='$styleidnew', tpp='$tppnew', ppp='$pppnew', timeformat='$timeformatnew',
				customstatus='$cstatusnew', ignorepm='$ignorepmnew', showemail='$showemailnew', newsletter='$newsletternew',
				invisible='$invisiblenew', timeoffset='$timeoffsetnew', avatar='$avatarnew', signature='$signaturenew'
				$passwordadd $secquesadd $groupidadd  WHERE uid='$uid'");

		cpmsg('members_edit_succeed');

	}

} elseif($action == 'ipban') {

	if(!submitcheck('ipbansubmit')) {

		require DISCUZ_ROOT.'./include/misc.php';

		$iptoban = explode('.', $extr);

		$ipbanned = '';
		$query = $db->query("SELECT * FROM $table_banned ORDER BY dateline");
		while($banned = $db->fetch_array($query)) {
			for($i = 1; $i <= 4; $i++) {
				if($banned["ip$i"] == -1) {
					$banned["ip$i"] = '*';
				}
			}
			$disabled = $adminid != 1 && $banned['admin'] != $discuz_userss ? 'disabled' : NULL;
			$banned['dateline'] = gmdate($dateformat, $banned['dateline'] + $timeoffset * 3600);
			$banned['expiration'] = gmdate($dateformat, $banned['expiration'] + $timeoffset * 3600);
			$theip = "{$banned['ip1']}.{$banned['ip2']}.{$banned['ip3']}.{$banned['ip4']}";
			$ipbanned .= "<tr align=\"center\">\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[{$banned['id']}]\" value=\"{$banned['id']}\" $disabled></td>\n".
				"<td bgcolor=\"".ALTBG2."\">$theip</td>\n".
				"<td bgcolor=\"".ALTBG1."\">".convertip($theip, "./")."</td>\n".
				"<td bgcolor=\"".ALTBG2."\">{$banned['admin']}</td>\n".
				"<td bgcolor=\"".ALTBG1."\">{$banned['dateline']}</td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"10\" name=\"expirationnew[{$banned['id']}]\" value=\"{$banned['expiration']}\" $disabled></td></tr>\n";
		}
		include CP_TPL.'member_ipban.php';


	} else {

		if($ids = implode_ids( $delete )) {
			$db->query("DELETE FROM $table_banned WHERE id IN ($ids) AND ('$adminid'='1' OR admin='$discuz_user')");
		}

		if($ip1new != '' && $ip2new != '' && $ip3new != '' && $ip4new != '') {
			$own = 0;
			$ip = explode('.', $onlineip);
			for($i = 1; $i <= 4; $i++) {
				if(!is_numeric(${'ip'.$i.'new'}) || ${'ip'.$i.'new'} < 0) {
					if($adminid != 1) {
						cpmsg('ipban_nopermission');
					}
					${'ip'.$i.'new'} = -1;
					$own++;
				} elseif(${'ip'.$i.'new'} == $ip[$i - 1]) {
					$own++;
				}
				${'ip'.$i.'new'} = intval(${'ip'.$i.'new'});
			}

			if($own == 4) {
				cpmsg('ipban_illegal');
			}

			$query = $db->query("SELECT * FROM $table_banned");
			while($banned = $db->fetch_array($query)) {
				$exists = 0;
				for($i = 1; $i <= 4; $i++) {
					if($banned["ip$i"] == -1) {
						$exists++;
					} elseif($banned["ip$i"] == ${"ip".$i."new"}) {
						$exists++;
					}
				}
				if($exists == 4) {
					cpmsg('ipban_invalid');
				}
			}

			$expiration = $timestamp + $validitynew * 86400;

			$db->query("UPDATE $table_sessions SET groupid='6' WHERE ('$ip1new'='-1' OR ip1='$ip1new') AND ('$ip2new'='-1' OR ip2='$ip2new') AND ('$ip3new'='-1' OR ip3='$ip3new') AND ('$ip4new'='-1' OR ip4='$ip4new')");
			$db->query("INSERT INTO $table_banned (ip1, ip2, ip3, ip4, admin, dateline, expiration)
				VALUES ('$ip1new', '$ip2new', '$ip3new', '$ip4new', '$discuz_user', '$timestamp', '$expiration')");

		}

		if(is_array($expirationnew)) {
			foreach($expirationnew as $id => $expiration) {
				$db->query("UPDATE $table_banned SET expiration='".strtotime($expiration)."' WHERE id='$id' AND ('$adminid'='1' OR admin='$discuz_user')");
			}
		}

		updatecache('ipbanned');
		cpmsg('ipban_succeed', 'admincp.php?action=ipban');
	}
	
}

?>