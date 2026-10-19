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

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

if($action == 'onlinelist') {

	if(!submitcheck('onlinesubmit')) {

		$listarray = array();
		$query = $db->query("SELECT * FROM $table_onlinelist");
		while($list = $db->fetch_array($query)) {
			$listarray[$list['groupid']] = $list;
		}

		$onlinelist = '';
		$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups WHERE groupid<>'7' AND type<>'member'");
		$group = array('groupid' => 0, 'grouptitle' => 'Member');
		do {
			$onlinelist .= "<tr align=\"center\">\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"3\" name=\"displayordernew[{$group['groupid']}]\" value=\"{$listarray[$group['groupid']]['displayorder']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"15\" name=\"titlenew[{$group['groupid']}]\" value=\"".($listarray[$group['groupid']]['title'] ? $listarray[$group['groupid']]['title'] : $group['grouptitle'])."\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"20\" name=\"urlnew[{$group['groupid']}]\" value=\"{$listarray[$group['groupid']]['url']}\"></td></tr>\n";
		} while($group = $db->fetch_array($query));
		include CP_TPL.'misc_online.php';
	} else {

		if(is_array($urlnew)) {
			$db->query("DELETE FROM $table_onlinelist");
			foreach($urlnew as $id => $url) {
				$url = trim($url);
				if($id == 0 || $url) {
					$db->query("INSERT INTO $table_onlinelist (groupid, displayorder, title, url)
						VALUES ('$id', '$displayordernew[$id]', '$titlenew[$id]', '$url')");
				}
			}
		}

		updatecache('onlinelist');
		cpmsg('onlinelist_succeed', 'admincp.php?action=onlinelist');

	}

} elseif($action == 'forumlinks') {

	if(!submitcheck('forumlinksubmit')) {

		$forumlinks = '';
		$query = $db->query("SELECT * FROM $table_forumlinks ORDER BY displayorder");
		while($forumlink = $db->fetch_array($query)) {
			$forumlinks .= "<tr bgcolor=\"".ALTBG2."\" align=\"center\">\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$forumlink['id']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"3\" name=\"displayorder[{$forumlink['id']}]\" value=\"{$forumlink['displayorder']}\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"15\" name=\"name[{$forumlink['id']}]\" value=\"{$forumlink['name']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"15\" name=\"url[{$forumlink['id']}]\" value=\"{$forumlink['url']}\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"15\" name=\"note[{$forumlink['id']}]\" value=\"{$forumlink['note']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"15\" name=\"logo[{$forumlink['id']}]\" value=\"{$forumlink['logo']}\"></td></tr>\n";
		}
		include CP_TPL.'misc_forumlink.php';

	} else {

		if($ids = implode_ids( $delete )) {
			$db->query("DELETE FROM	$table_forumlinks WHERE	id IN ($ids)");
		}

		if(is_array($name)) {
			foreach($name as $id =>	$val) {
				$db->query("UPDATE $table_forumlinks SET displayorder='$displayorder[$id]', name='$name[$id]', url='$url[$id]', note='$note[$id]', logo='$logo[$id]' WHERE id='$id'");
			}
		}

		if($newname != '') {
			$db->query("INSERT INTO	$table_forumlinks (displayorder, name, url, note, logo)	VALUES ('$newdisplayorder', '$newname',	'$newurl', '$newnote', '$newlogo')");
		}

		updatecache('forumlinks');
		cpmsg('forumlinks_succeed', 'admincp.php?action=forumlinks');

	}

} elseif($action == 'discuzcodes') {

	if(!submitcheck('bbcodessubmit') && !$edit) {

		$discuzcodes = '';
		$query = $db->query("SELECT * FROM $table_bbcodes");
		while($bbcode = $db->fetch_array($query)) {
			$discuzcodes .= "<tr bgcolor=\"".ALTBG2."\" align=\"center\">\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$bbcode['id']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"15\" name=\"tagnew[{$bbcode['id']}]\" value=\"{$bbcode['tag']}\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"availablenew[{$bbcode['id']}]\" value=\"1\" ".($bbcode['available'] ? 'checked' : NULL)."></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><a href=\"admincp.php?action=discuzcodes&edit={$bbcode['id']}\">[{$lang['detail']}]</a></td></tr>\n";
		}
		include CP_TPL.'misc_bbcode.php';

	} elseif(submitcheck('bbcodessubmit')) {

		if(is_array($delete)) {
			$ids = '\''.implode('\',\'', $delete).'\'';
			$db->query("DELETE FROM	$table_bbcodes WHERE id IN ($ids)");
		}

		if(is_array($tagnew)) {
			foreach($tagnew as $id => $val) {
				if(!preg_match("/^[0-9a-z]+$/i", $tagnew[$id])) {
					cpmsg('discuzcodes_edit_tag_invalid');
				}
				$db->query("UPDATE $table_bbcodes SET tag='$tagnew[$id]', available='$availablenew[$id]' WHERE id='$id'");
			}
		}

		if($newtag != '') {
			$db->query("INSERT INTO	$table_bbcodes (tag, available, params, nest)
				VALUES ('$newtag', '0', '1', '1')");
		}

		updatecache('bbcodes');
		cpmsg('discuzcodes_edit_succeed', 'admincp.php?action=discuzcodes');

	} elseif($edit) {

		$query = $db->query("SELECT * FROM $table_bbcodes WHERE id='$edit'");
		if(!$bbcode = $db->fetch_array($query)) {
			cpmsg('undefined_action');
		}

		if(!submitcheck('editsubmit')) {

			echo "<form method=\"post\" action=\"admincp.php?action=discuzcodes&edit=$edit&formhash=".FORMHASH."\">\n";

			showtype($lang['discuzcodes_edit'].' - '.$bbcode['tag'], 'top');
			showsetting('discuzcodes_edit_tag', 'tagnew', $bbcode['tag'], 'text');
			showsetting('discuzcodes_edit_replacement', 'replacementnew', $bbcode['replacement'], 'textarea');
			showsetting('discuzcodes_edit_example', 'examplenew', $bbcode['example'], 'text');
			showsetting('discuzcodes_edit_explanation', 'explanationnew', $bbcode['explanation'], 'text');
			showsetting('discuzcodes_edit_params', 'paramsnew', $bbcode['params'], 'text');
			showsetting('discuzcodes_edit_nest', 'nestnew', $bbcode['nest'], 'text');
			showtype('', 'bottom');

			echo "<br><center><input type=\"submit\" name=\"editsubmit\" value=\"{$lang['submit']}\"></center></form>";

		} else {

			$tagnew = trim($tagnew);
			if(!preg_match("/^[0-9a-z]+$/i", $tagnew)) {
				cpmsg('discuzcodes_edit_tag_invalid');
			} elseif($paramsnew < 1 || $paramsnew > 3 || $nestnew < 1 || $nestnew > 3) {
				cpmsg('discuzcodes_edit_range_invalid');
			}

			$db->query("UPDATE $table_bbcodes SET tag='$tagnew', replacement='$replacementnew', example='$examplenew', explanation='$explanationnew', params='$paramsnew', nest='$nestnew' WHERE id='$edit'");

			updatecache('bbcodes');
			cpmsg('discuzcodes_edit_succeed', 'admincp.php?action=discuzcodes');

		}
	}

} elseif($action == 'censor') {

	if(!submitcheck('censorsubmit')) {

		$censorwords = '';
		$query = $db->query("SELECT * FROM $table_words");
		while($censor =	$db->fetch_array($query)) {
			$disabled = $adminid != 1 && $censor['admin'] != $discuz_userss ? 'disabled' : NULL;
			$censorwords .=	'<tr align="center"><td bgcolor="'.ALTBG1.'"><input type="checkbox" name="delete[]" value="'.$censor['id'].'" '.$disabled."></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"30\" name=\"find[{$censor['id']}]\" value=\"".dhtmlspecialchars($censor['find'])."\" $disabled></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"30\" name=\"replace[{$censor['id']}]\" value=\"{$censor['replacement']}\" $disabled></td>\n".
				"<td bgcolor=\"".ALTBG2."\">{$censor['admin']}</td></tr>\n";
		}
		include CP_TPL.'misc_censor.php';

	} else {

		if($ids = implode_ids( $delete )) {
			$db->query("DELETE FROM	$table_words WHERE id IN ($ids) AND ('$adminid'='1' OR admin='$discuz_user')");
		}

		if(is_array($find)) {
			foreach($find as $id =>	$val) {
				$db->query("UPDATE $table_words	SET find='$find[$id]', replacement='$replace[$id]' WHERE id='$id' AND ('$adminid'='1' OR admin='$discuz_user')");
			}
		}

		if($newfind != '') {
			$db->query("INSERT INTO	$table_words (admin, find, replacement) VALUES
					('$discuz_user', '$newfind', '$newreplace')");
		}

		updatecache('censor');
		cpmsg('censor_succeed', 'admincp.php?action=censor');

	}

} elseif($action == 'smilies' || $action=='icons') {

	if(!submitcheck('smiliesubmit')) {
		$sqlaction = $action == 'smilies' ? "type='smiley'" : "type='icon'";
		$smilies = $icons = '';
		$query = $db->query("SELECT * FROM $table_smilies where $sqlaction");
		while($smiley =	$db->fetch_array($query)) {
			if($smiley['type'] == 'smiley') {
				$smilies .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"25\" name=\"code[{$smiley['id']}]\" value=\"{$smiley['code']}\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"25\" name=\"url[{$smiley['id']}]\" value=\"{$smiley['url']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><img src=\"./".SMDIR."/{$smiley['url']}\"></td></tr>\n";
			} elseif($smiley['type'] == 'icon') {
				$icons	.= "<tr	align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\"></td>\n".
					"<td colspan=\"2\" bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"35\" name=\"url[{$smiley['id']}]\" value=\"{$smiley['url']}\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><img src=\"".SMDIR."/{$smiley['url']}\"></td></tr>\n";
			}
		}
		if ($action == 'smilies'){
			include CP_TPL.'misc_smilies.php';
		} else {
			include CP_TPL.'misc_icons.php';
		}

	} else {

		if(is_array($delete)) {
			$ids = $comma =	'';
			foreach($delete	as $id)	{
				$ids .=	"$comma'$id'";
				$comma = ',';
			}
			$db->query("DELETE FROM	$table_smilies WHERE id	IN ($ids)");
		}

		if(is_array($url)) {
			foreach($url as	$id => $val) {
				if ($action == 'smilies'){
					$db->query("UPDATE $table_smilies SET code='$code[$id]', url='$url[$id]' WHERE id='$id'");
				} else {
					$db->query("UPDATE $table_smilies SET url='$url[$id]' WHERE id='$id'");
				}
			}
		}
		

		$newcode = trim($newcode);
		$newurl1 = trim($newurl1);
		if($action =='smilies' && $newcode && $newurl1) {
			$query = $db->query("SELECT code FROM $table_smilies WHERE type='smiley' and (code='$newcode' or url='$newurl1')");
			if($db->result($query, 0)) {
				cpmsg('smilies_duplicate');
			}
			$query = $db->query("INSERT INTO $table_smilies	(type, code, url)
				VALUES ('smiley', '$newcode', '$newurl1')");
		} elseif ( $action == 'icons' && $newurl1 ) {
			$query = $db->query("SELECT url FROM $table_smilies WHERE type='icon' and  url='$newurl1'");
			if($db->result($query, 0)) {
				cpmsg('icons_duplicate');
			}
			$query = $db->query("INSERT INTO $table_smilies	(type, code, url)
				VALUES ('icon', '', '$newurl1')");
		}
		
		if ($action == 'smilies'){
			updatecache('smilies');	
			cpmsg('smilies_succeed', 'admincp.php?action=smilies');
		} else {
			updatecache('icons');
			cpmsg('icons_succeed', 'admincp.php?action=icons');
		}
	}

} elseif($action == 'attachtypes') {

	if(!submitcheck('typesubmit')) {

		$attachtypes = '';
		$query = $db->query("SELECT * FROM $table_attachtypes");
		while($type = $db->fetch_array($query)) {
			$attachtypes .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$type['id']}\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"10\" name=\"extension[{$type['id']}]\" value=\"{$type['extension']}\"></td>\n".
				"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"15\" name=\"maxsize[{$type['id']}]\" value=\"{$type['maxsize']}\"></td></tr>\n";
		}

		include CP_TPL.'misc_attachtypes.php';

	} else {

		if(is_array($delete)) {
			$ids = $comma =	'';
			foreach($delete	as $id)	{
				$ids .=	"$comma'$id'";
				$comma = ',';
			}
			$db->query("DELETE FROM	$table_attachtypes WHERE id IN ($ids)");
		}

		if(is_array($extension)) {
			foreach($extension as $id => $val) {
				$db->query("UPDATE $table_attachtypes SET extension='$val', maxsize='$maxsize[$id]' WHERE id='$id'");
			}
		}

		$newextension = trim($newextension);
		if($newextension != '') {
			$query = $db->query("SELECT id FROM $table_attachtypes WHERE extension='$newextension'");
			if($db->result($query, 0)) {
				cpmsg('attachtypes_duplicate');
			}
			$db->query("INSERT INTO	$table_attachtypes (extension, maxsize) VALUES
					('$newextension', '$newmaxsize')");
		}

		cpmsg('attachtypes_succeed', 'admincp.php?action=attachtypes');

	}

} elseif($action == 'updatecache') {

	updatecache();

	$tpl = dir(DISCUZ_ROOT.'./forumdata/templates');
	while($entry = $tpl->read()) {
		if (strpos($entry, '.tpl.php')) {
			@unlink(DISCUZ_ROOT.'./forumdata/templates/'.$entry);
		}
	}
	$tpl->close();

	$db->query("DELETE FROM $table_searchindex");
	$db->query("DELETE FROM $table_caches");

	cpmsg('update_cache_succeed');

}elseif($action == 'logout') {
	$query = $db->query("DELETE FROM $table_adminsessions WHERE uid='$discuz_uid' OR dateline<$timestamp-3600", 'SILENT');
	cpmsg('logout_succeed');
}

?>