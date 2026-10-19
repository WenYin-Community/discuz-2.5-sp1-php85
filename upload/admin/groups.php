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
1 附件扩展名长度检查
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

if($action == 'admingroups') {

	if (!$mod || $mod == 'list'){
		if (submitcheck('newtitlesubmit')){
			foreach($admintitlenew as $id => $value) {
				$db->query("UPDATE $table_admingroups SET admintitle='$admintitlenew[$id]' WHERE admingid='$id'");
			}
			if ($newadmintitle = trim($newadmintitle)){
				$db->query("INSERT INTO $table_admingroups (admintitle) VALUES ('$newadmintitle')");
			}
			cpmsg('admingroups_edit_succeed', 'admincp.php?action=admingroups');
		}
		$query = $db->query("SELECT * FROM $table_admingroups WHERE 1 ");
		$admingroups = '';
		while($group = $db->fetch_array($query)) {
			$group['adminglobal']=$group['adminglobal']?'Y':'N';
			$admingroups .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\">{$group['admingid']}</td>\n"."<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"20\" name=\"admintitlenew[{$group['admingid']}]\" value=\"{$group['admintitle']}\"></td>\n"."<td bgcolor=\"".ALTBG1."\">{$group['adminglobal']} </td>\n<td bgcolor=\"".ALTBG1."\">";
			if ($group['admingid'] >1){
				$admingroups .= "<a href=admincp.php?action=admingroups&mod=edit&admingid={$group['admingid']}>{$lang['edit']}</a></td>\n";
			}else{
				$admingroups .= "N</td>\n";
			}
		}
		include CP_TPL.'groups_admin.php';

	}else{
		
		if(!submitcheck('groupsubmit')) {

			$query = $db->query("SELECT * FROM $table_admingroups WHERE admingid >1 and admingid='$admingid'");
			while($group = $db->fetch_array($query)) {

?>
			<br><br><form method="post" action="admincp.php?action=admingroups&mod=edit&edit=<?=$group['admingid']?>">
			<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<?
				
				//全局权限
				showtype($lang['admingroups_edit'].' - '.$group['admintitle'], 'top');
				showsetting('admingroups_edit_adminglobal', 'adminglobalnew', $group['adminglobal'], 'radio');
				
				//前台权限
				showtype($lang['admingroups_edit_normal'].' - '.$group['admintitle']);
				showsetting('admingroups_edit_edit_post', 'alloweditpostnew', $group['alloweditpost'], 'radio');
				showsetting('admingroups_edit_edit_poll', 'alloweditpollnew', $group['alloweditpoll'], 'radio');
				showsetting('admingroups_edit_del_post', 'allowdelpostnew', $group['allowdelpost'], 'radio');
				showsetting('admingroups_edit_highlight_thread', 'allowhighlightnew', $group['allowhighlight'], 'radio');
				showsetting('admingroups_edit_digest_thread', 'allowdigestnew', $group['allowdigest'], 'radio');
				showsetting('admingroups_edit_close_thread', 'allowclosenew', $group['allowclose'], 'radio');
				showsetting('admingroups_edit_move_thread', 'allowmovenew', $group['allowmove'], 'radio');
				showsetting('admingroups_edit_top_thread', 'allowtopnew', $group['allowtop'], 'radio');
				showsetting('admingroups_edit_merge_thread', 'allowmergenew', $group['allowmerge'], 'radio');
				showsetting('admingroups_edit_split_thread', 'allowsplitnew', $group['allowsplit'], 'radio');
				showsetting('admingroups_edit_view_ip', 'allowviewipnew', $group['allowviewip'], 'radio');
				showsetting('admingroups_edit_disable_postctrl', 'disablepostctrlnew', $group['disablepostctrl'], 'radio');
				
				//后台权限
				showtype($lang['admingroups_edit_cp'].' - '.$group['admintitle']);
				showsetting('admingroups_edit_mass_prune', 'allowmassprunenew', $group['allowmassprune'], 'radio');
				showsetting('admingroups_edit_censor_word', 'allowcensorwordnew', $group['allowcensorword'], 'radio');
				showsetting('admingroups_edit_ban_ip', 'allowbanipnew', $group['allowbanip'], 'radio');
				showsetting('admingroups_edit_edit_user', 'alloweditusernew', $group['allowedituser'], 'radio');
				showsetting('admingroups_edit_ban_user', 'allowbanusernew', $group['allowbanuser'], 'radio');
				showsetting('admingroups_edit_post_announce', 'allowpostannouncenew', $group['allowpostannounce'], 'radio');
				showsetting('admingroups_edit_view_log', 'allowviewlognew', $group['allowviewlog'], 'radio');
				showtype('', 'bottom');

				echo "<br><center><input type=\"submit\" name=\"groupsubmit\" value=\"{$lang['submit']}\"><center></form>";

				}
		} else {

			$db->query("UPDATE $table_admingroups SET
				adminglobal='$adminglobalnew', 
				alloweditpost='$alloweditpostnew', 
				alloweditpoll='$alloweditpollnew',
				allowdelpost='$allowdelpostnew',
				allowhighlight='$allowhighlightnew',
				allowdigest='$allowdigestnew',
				allowclose='$allowclosenew',
				allowmove='$allowmovenew',
				allowtop='$allowtopnew',
				allowmerge='$allowmergenew',
				allowsplit='$allowsplitnew',
				allowmassprune='$allowmassprunenew', allowcensorword='$allowcensorwordnew', allowviewip='$allowviewipnew',
				allowbanip='$allowbanipnew', 
				allowedituser='$alloweditusernew',
				allowbanuser='$allowbanusernew',
				allowpostannounce='$allowpostannouncenew', allowviewlog='$allowviewlognew', disablepostctrl='$disablepostctrlnew'
				WHERE admingid='$edit' AND admingid >1");

			updatecache('usergroups');
			updatecache('admingroups');
			cpmsg('admingroups_edit_succeed', 'admincp.php?action=admingroups');
		}
	}

} elseif($action == 'usergroups') {

	if(!submitcheck('groupsubmit')) {

		if(!$edit) {
			$membergroup = $specialgroup = $sysgroup = '';
			$upperlimit = $lowerlimit = $misconfig = 0;
			$query = $db->query("SELECT groupid, type, grouptitle, creditshigher, creditslower, stars, color, groupavatar FROM $table_usergroups ORDER BY creditslower");
			while($group = $db->fetch_array($query)) {
				if($group['type'] == 'member') {
					$membergroup .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[{$group['groupid']}]\" value=\"{$group['groupid']}\"></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"group_title[{$group['groupid']}]\" value=\"{$group['grouptitle']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\" name=\"group_creditshigher[{$group['groupid']}]\" value=\"{$group['creditshigher']}\">\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"6\" name=\"group_creditslower[{$group['groupid']}]\" value=\"{$group['creditslower']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"2\"name=\"group_stars[{$group['groupid']}]\" value=\"{$group['stars']}\"></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"6\"name=\"group_color[{$group['groupid']}]\" value=\"{$group['color']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"12\" name=\"group_avatar[{$group['groupid']}]\" value=\"{$group['groupavatar']}\"></td>".
						"<td bgcolor=\"".ALTBG2."\" nowrap><a href=\"admincp.php?action=usergroups&edit={$group['groupid']}\">[{$lang['detail']}]</a></td></tr>\n";
					if($group['creditshigher'] > 0 && $upperlimit != $group['creditshigher']) {
						$misconfig = 1;
					}
					$lowerlimit = $group['creditshigher'] < $lowerlimit ? $group['creditshigher'] : $lowerlimit;
					$upperlimit = $group['creditslower'] > $upperlimit ? $group['creditslower'] : $upperlimit;
				} elseif($group['type'] == 'special') {
					$specifiedmembers = $comma = '';
					$querymember = $db->query("SELECT uid, username FROM $table_members WHERE groupid='{$group['groupid']}'");
					while($member = $db->fetch_array($querymember)) {
						$specifiedmembers .= "$comma<a href=\"viewpro.php?uid={$member['uid']}\" target=\"_blank\">{$member['username']}</a>";
						$comma = ', ';
					}
					$specialgroup .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[{$group['groupid']}]\" value=\"{$group['groupid']}\"></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"group_title[{$group['groupid']}]\" value=\"{$group['grouptitle']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><span class=\"smalltxt\">$specifiedmembers</span></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"2\"name=\"group_stars[{$group['groupid']}]\" value=\"{$group['stars']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\"name=\"group_color[{$group['groupid']}]\" value=\"{$group['color']}\"></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"group_avatar[{$group['groupid']}]\" value=\"{$group['groupavatar']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\" nowrap><a href=\"admincp.php?action=usergroups&edit={$group['groupid']}\">[{$lang['detail']}]</a></td></tr>\n";
				} elseif($group['type'] == 'system') {
					$sysgroup .= "<tr align=\"center\">\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"group_title[{$group['groupid']}]\" value=\"{$group['grouptitle']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\">".$lang['usergroups_system_'.$group['groupid']]."</td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"2\"name=\"group_stars[{$group['groupid']}]\" value=\"{$group['stars']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\"name=\"group_color[{$group['groupid']}]\" value=\"{$group['color']}\"></td>\n".
						"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"group_avatar[{$group['groupid']}]\" value=\"{$group['groupavatar']}\"></td>\n".
						"<td bgcolor=\"".ALTBG1."\" nowrap><a href=\"admincp.php?action=usergroups&edit={$group['groupid']}\">[{$lang['detail']}]</a></td></tr>\n";
				}
			}
			if($misconfig || $upperlimit < 9999 || $lowerlimit > -999) {
				$warning = "<script language=\"JavaScript\">alert('{$lang['usergroups_misconfig']}');</script><span class=\"mediumtxt\">{$lang['usergroups_misconfig_comment']}</span><br><br>";
			}

			include CP_TPL.'groups_user.php';

		} else {

			$query = $db->query("SELECT * FROM $table_usergroups WHERE groupid='$edit'");
			$group = $db->fetch_array($query);

			if(!submitcheck('detailsubmit')) {
				$checksearch = array($group['allowsearch'] => 'checked');
				$checkavatar = array($group['allowavatar'] => 'checked');

				echo "<form method=\"post\" action=\"admincp.php?action=usergroups&edit=$edit&formhash=".FORMHASH."\">\n";

				showtype('usergroups_edit', 'top');
				showsetting('usergroups_edit_title', 'grouptitlenew', $group['grouptitle'], 'text');

				showtype('usergroups_edit_basic');
				if($group['groupid'] == 7) {
					echo '<input type="hidden" name="allowvisitnew" value="1">';
				} else {
					showsetting('usergroups_edit_visit', 'allowvisitnew', $group['allowvisit'], 'radio');
				}
				showsetting('usergroups_edit_view_thread', 'allowviewnew', $group['allowview'], 'radio');
				showsetting('usergroups_edit_view_stats', 'allowviewstatsnew', $group['allowviewstats'], 'radio');
				showsetting('usergroups_edit_invisible', 'allowinvisiblenew', $group['allowinvisible'], 'radio');
				showsetting('usergroups_edit_search', '', '', "<input type=\"radio\" name=\"allowsearchnew\" value=\"0\" $checksearch[0]> {$lang['usergroups_edit_search_disable']}<br><input type=\"radio\" name=\"allowsearchnew\" value=\"1\" $checksearch[1]> {$lang['usergroups_edit_search_thread']}<br><input type=\"radio\" name=\"allowsearchnew\" value=\"2\" $checksearch[2]> {$lang['usergroups_edit_search_post']}");
				showsetting('usergroups_edit_avatar', '', '', "<input type=\"radio\" name=\"allowavatarnew\" value=\"0\" $checkavatar[0]> {$lang['usergroups_edit_avatar_disable']}<br><input type=\"radio\" name=\"allowavatarnew\" value=\"1\" $checkavatar[1]> {$lang['usergroups_edit_avatar_board']}<br><input type=\"radio\" name=\"allowavatarnew\" value=\"2\" $checkavatar[2]> {$lang['usergroups_edit_avatar_custom']}<br><input type=\"radio\" name=\"allowavatarnew\" value=\"3\" $checkavatar[3]> {$lang['usergroups_edit_avatar_upload']}");
				showsetting('usergroups_edit_cstatus', 'allowcstatusnew', $group['allowcstatus'], 'radio');
				showsetting('usergroups_edit_karma', 'allowkarmanew', $group['allowkarma'], 'radio');
				showsetting('usergroups_edit_max_karma_rate', 'maxkarmaratenew', $group['maxkarmarate'], 'text');
				showsetting('usergroups_edit_max_rpd', 'maxrateperdaynew', $group['maxrateperday'], 'text');
				showsetting('usergroups_edit_max_pm_num', 'maxpmnumnew', $group['maxpmnum'], 'text');

				showtype('usergroups_edit_thread');
				showsetting('usergroups_edit_post', 'allowpostnew', $group['allowpost'], 'radio');
				showsetting('usergroups_edit_post_poll', 'allowpostpollnew', $group['allowpostpoll'], 'radio');
				showsetting('usergroups_edit_set_view_perm', 'allowsetviewpermnew', $group['allowsetviewperm'], 'radio');
				showsetting('usergroups_edit_hide_code', 'allowhidecodenew', $group['allowhidecode'], 'radio');
				showsetting('usergroups_edit_vote', 'allowvotenew', $group['allowvote'], 'radio');
				showsetting('usergroups_edit_sig_bbcode', 'allowsigbbcodenew', $group['allowsigbbcode'], 'radio');
				showsetting('usergroups_edit_sig_img_code', 'allowsigimgcodenew', $group['allowsigimgcode'], 'radio');
				showsetting('usergroups_edit_max_sig_size', 'maxsigsizenew', $group['maxsigsize'], 'text');

				showtype('usergroups_edit_attachment');
				showsetting('usergroups_edit_get_attach', 'allowgetattachnew', $group['allowgetattach'], 'radio');
				showsetting('usergroups_edit_post_attach', 'allowpostattachnew', $group['allowpostattach'], 'radio');
				showsetting('usergroups_edit_set_attach_perm', 'allowsetattachpermnew', $group['allowsetattachperm'], 'radio');
				showsetting('usergroups_edit_max_attach_size', 'maxattachsizenew', $group['maxattachsize'], 'text');
				showsetting('usergroups_edit_attach_ext', 'attachextensionsnew', $group['attachextensions'], 'text');

				showtype('', 'bottom');

				echo "<br><center><input type=\"submit\" name=\"detailsubmit\" value=\"{$lang['submit']}\"><center></form>";

			} else {
				if (strlen($attachextensionsnew) > 255) {
					cpmsg('usergroups_edit_attach_ext_comment_toolong');
				}
				$db->query("UPDATE $table_usergroups SET grouptitle='$grouptitlenew', allowvisit='$allowvisitnew',
					allowview='$allowviewnew', allowviewstats='$allowviewstatsnew', allowinvisible='$allowinvisiblenew',
					allowsearch='$allowsearchnew', allowavatar='$allowavatarnew', allowcstatus='$allowcstatusnew',
					allowkarma='$allowkarmanew', maxkarmarate='$maxkarmaratenew', maxrateperday='$maxrateperdaynew',
					maxpmnum='$maxpmnumnew', allowpost='$allowpostnew', allowsetviewperm='$allowsetviewpermnew',
					allowhidecode='$allowhidecodenew', allowpostpoll='$allowpostpollnew', allowvote='$allowvotenew',
					allowsigbbcode='$allowsigbbcodenew', allowsigimgcode='$allowsigimgcodenew', maxsigsize='$maxsigsizenew',
					allowgetattach='$allowgetattachnew', allowpostattach='$allowpostattachnew', allowsetattachperm='$allowsetattachpermnew',
					maxattachsize='$maxattachsizenew', attachextensions='$attachextensionsnew' WHERE groupid='$edit'");

				if($allowinvisiblenew == 0 && $group['allowinvisible'] != $allowinvisiblenew) {
					$db->query("UPDATE $table_members SET invisible='0' WHERE groupid='$edit'");
				}

				updatecache('usergroups');
				cpmsg('usergroups_edit_succeed', 'admincp.php?action=usergroups');

			}

		}

	} else {

		if($type == 'member') {
			$modified = array();

			if($grouptitlenew && ($creditshighernew || $creditslowernew)) {
				$db->query("INSERT INTO $table_usergroups (type, grouptitle, creditshigher, creditslower, stars, color, groupavatar, allowvisit, allowview, allowpost, allowsigbbcode)
					VALUES ('member', '$grouptitlenew', '$creditshighernew', '$creditslowernew', '$starsnew', '$colornew', '$groupavatarnew', '1', '1', '1', '1')");
				$modified[] = array('groupid' => $db->insert_id(), 'creditshigher' => $creditshighernew, 'creditslower' => $creditslowernew);
			}

			$ids = $comma = '';
			if(is_array($group_title)) {
				foreach($group_title as $id => $title) {
					if($delete[$id]) {
						$ids .= "$comma'$id'";
						$comma = ', ';
					} else {
						$db->query("UPDATE $table_usergroups SET grouptitle='$group_title[$id]', creditshigher='$group_creditshigher[$id]', creditslower='$group_creditslower[$id]', stars='$group_stars[$id]', color='$group_color[$id]', groupavatar='$group_avatar[$id]' WHERE groupid='$id'");
						if($db->affected_rows()) {
							$modified[] = array('groupid' => $id, 'creditshigher' => $group_creditshigher[$id], 'creditslower' => $group_creditslower[$id]);
						}
					}
				}
			}
			if($ids) {
				$db->query("DELETE FROM $table_usergroups WHERE groupid IN ($ids) AND type='member'");
			}

			foreach($modified as $group) {
				$db->query("UPDATE $table_members SET groupid='{$group['groupid']}' WHERE !(groupid>'0' AND groupid<'8') AND adminid='0' AND credit>='{$group['creditshigher']}' AND credit<'{$group['creditslower']}'", 'UNBUFFERED');
			}
		} elseif($type == 'special') {
			if($grouptitlenew) {
				$db->query("INSERT INTO $table_usergroups (type, grouptitle, stars, color, groupavatar, allowvisit, allowview, allowpost, allowsigbbcode)
					VALUES ('special', '$grouptitlenew', '$starsnew', '$colornew', '$groupavatarnew', '1', '1', '1', '1')");
			}
			if(is_array($group_title)) {
				$ids = $comma = '';
				foreach($group_title as $id => $title) {
					if($delete[$id]) {
						$ids .= "$comma'$id'";
						$comma = ',';
					} else {
						$db->query("UPDATE $table_usergroups SET grouptitle='$group_title[$id]', stars='$group_stars[$id]', color='$group_color[$id]', groupavatar='$group_avatar[$id]' WHERE groupid='$id'");
					}
				}
			}
			if($ids) {
				$db->query("DELETE FROM $table_usergroups WHERE groupid IN ($ids) AND type='special'");
				$query = $db->query("SELECT groupid FROM $table_usergroups WHERE type='member' AND creditslower>'0' ORDER BY creditslower LIMIT 1");
				$db->query("UPDATE $table_members SET groupid='".$db->result($query, 0)."', adminid='0' WHERE groupid IN ($ids) AND adminid='-1'", 'UNBUFFERED');
				$db->query("UPDATE $table_members SET groupid=adminid WHERE groupid IN ($ids) AND (adminid='1' OR adminid='2' OR adminid='3')", 'UNBUFFERED');
			}
		} elseif($type == 'system') {
			if(is_array($group_title)) {
				foreach($group_title as $id => $title) {
					$db->query("UPDATE $table_usergroups SET grouptitle='$group_title[$id]', stars='$group_stars[$id]', color='$group_color[$id]', groupavatar='$group_avatar[$id]' WHERE groupid='$id'");
				}
			}
		}

		updatecache('usergroups');
		cpmsg('usergroups_edit_succeed', 'admincp.php?action=usergroups');
	}

} elseif($action == 'ranks') {

	if(!submitcheck('ranksubmit')) {

		$ranks = '';
		$query = $db->query("SELECT * FROM $table_ranks ORDER BY postshigher");
		while($rank = $db->fetch_array($query)) {
			$ranks .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[{$rank['rankid']}]\" value=\"{$rank['rankid']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"12\" name=\"ranktitlenew[{$rank['rankid']}]\" value=\"{$rank['ranktitle']}\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\" name=\"postshighernew[{$rank['rankid']}]\" value=\"{$rank['postshigher']}\">\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"2\"name=\"starsnew[{$rank['rankid']}]\" value=\"{$rank['stars']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"6\"name=\"colornew[{$rank['rankid']}]\" value=\"{$rank['color']}\"></td>";
		}
		include CP_TPL.'groups_rank.php';

	} else {

		if($delete) {
			$ids = implode('\',\'', $delete);
			$db->query("DELETE FROM $table_ranks WHERE rankid IN ('$ids')");
		}

		foreach($ranktitlenew as $id => $value) {
			$db->query("UPDATE $table_ranks SET ranktitle='$ranktitlenew[$id]', postshigher='$postshighernew[$id]', stars='$starsnew[$id]', color='$colornew[$id]' WHERE rankid='$id'");
		}

		if($newranktitle) {
			$db->query("INSERT INTO $table_ranks (ranktitle, postshigher, stars, color)
				VALUES ('$newranktitle', '$newpostshigher', '$newstars', '$newcolor')");
		}

		updatecache('ranks');
		cpmsg('ranks_succeed', 'admincp.php?action=ranks');
	}
}

?>