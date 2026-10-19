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

$query = $db->query("SELECT * FROM $table_settings");
while($setting = $db->fetch_array($query)) {
	$settings[$setting['variable']] = $setting['value'];
}

$options = intval($options);

if(!submitcheck('settingsubmit')) {
?>
<tr bgcolor="<?=ALTBG2?>">
<td align="center">
<form method="post" action="admincp.php?action=settings">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="options" value="<?=$options?>">
<?

	if (!$options || $options=='1'){
		showtype('settings_general', 'top');
		showsetting('settings_bbname', 'settingsnew[bbname]', $settings['bbname'], 'text');
		showsetting('settings_sitename', 'settingsnew[sitename]', $settings['sitename'], 'text');
		showsetting('settings_siteurl', 'settingsnew[siteurl]', $settings['siteurl'], 'text');
		showsetting('settings_bbclosed', 'settingsnew[bbclosed]', $settings['bbclosed'], 'radio');
		showsetting('settings_closedreason', 'settingsnew[closedreason]', $settings['closedreason'], 'textarea');
		showtype('', 'bottom');

	}elseif( $options=='2'){
		showtype('settings_access','top');
		showsetting('settings_regstatus', 'settingsnew[regstatus]', $settings['regstatus'], 'radio');
		showsetting('settings_censoruser', 'settingsnew[censoruser]', $settings['censoruser'], 'text');
		showsetting('settings_doublee', 'settingsnew[doublee]', $settings['doublee'], 'radio');
		showsetting('settings_regverify', 'settingsnew[regverify]', $settings['regverify'], 'radio');
		showsetting('settings_regctrl', 'settingsnew[regctrl]', $settings['regctrl'], 'text');
		showsetting('settings_newbiespan', 'settingsnew[newbiespan]', $settings['newbiespan'], 'text');
		showsetting('settings_welcomemsg', 'settingsnew[welcomemsg]', $settings['welcomemsg'], 'radio');
		showsetting('settings_welcomemsgtxt', 'settingsnew[welcomemsgtxt]', $settings['welcomemsgtxt'], 'textarea');
		showsetting('settings_bbrules', 'settingsnew[bbrules]', $settings['bbrules'], 'radio');
		showsetting('settings_bbrulestxt', 'settingsnew[bbrulestxt]', $settings['bbrulestxt'], 'textarea');
		showtype('', 'bottom');

	}elseif( $options=='3'){
		$stylelist = "<select name=\"settingsnew[styleid]\">\n";
		$query = $db->query("SELECT styleid, name FROM $table_styles");
		while($style = $db->fetch_array($query)) {
	        $selected = $style['styleid'] == $settings['styleid'] ? 'selected="selected"' : NULL;
	        $stylelist .= "<option value=\"{$style['styleid']}\" $selected>{$style['name']}</option>\n";
		}
		$stylelist .= '</select>';
		$checkonline = array($settings['whosonlinestatus'] => 'checked');

		$settings['moddisplay'] == 'selectbox' ? $modselectbox = 'checked' : $modflat = 'checked';
		showtype('settings_styles','top');
		showsetting('settings_styleid', '', '', $stylelist);
		showsetting('settings_tpp', 'settingsnew[topicperpage]', $settings['topicperpage'], 'text');
		showsetting('settings_ppp', 'settingsnew[postperpage]', $settings['postperpage'], 'text');
		showsetting('settings_mpp', 'settingsnew[memberperpage]', $settings['memberperpage'], 'text');
		showsetting('settings_hottopic', 'settingsnew[hottopic]', $settings['hottopic'], 'text');
		showsetting('settings_announcements_num', 'settingsnew[announcements_num]', $settings['announcements_num'], 'text');
		showsetting('settings_hideprivate', 'settingsnew[hideprivate]', $settings['hideprivate'], 'radio');
		showsetting('settings_moddisplay', '', '', '<input type="radio" name="settingsnew[moddisplay]" value="flat" '.$modflat.'> '.$lang['settings_moddisplay_flat'].' &nbsp; <input type="radio" name="settingsnew[moddisplay]" value="selectbox" '.$modselectbox.'> '.$lang['settings_moddisplay_selectbox'].'</td>');
		showsetting('settings_fastpost', 'settingsnew[fastpost]', $settings['fastpost'], 'radio');
		showsetting('settings_modshortcut', 'settingsnew[modshortcut]', $settings['modshortcut'], 'radio');
		showsetting('settings_nocacheheaders', 'settingsnew[nocacheheaders]', $settings['nocacheheaders'], 'radio');
		showsetting('settings_forumjump', 'settingsnew[forumjump]', $settings['forumjump'], 'radio');
		showsetting('settings_debug', 'settingsnew[debug]', $settings['debug'], 'radio');
		showsetting('settings_whosonline', '', '', '<input type="radio" name="settingsnew[whosonlinestatus]" value="0" '.$checkonline[0].'> '.$lang['settings_display_none'].'<br><input type="radio" name="settingsnew[whosonlinestatus]" value="1" '.$checkonline[1].'> '.$lang['settings_whosonline_index'].'<br><input type="radio" name="settingsnew[whosonlinestatus]" value="2" '.$checkonline[2].'> '.$lang['settings_whosonline_forum'].'<br><input type="radio" name="settingsnew[whosonlinestatus]" value="3" '.$checkonline[3].'> '.$lang['settings_whosonline_both'].'</td>');
		showsetting('settings_vtonlinestatus', 'settingsnew[vtonlinestatus]', $settings['vtonlinestatus'], 'radio');
		showtype('', 'bottom');

	}elseif( $options=='4'){
        $checkstatusby = array($settings['userstatusby'] => 'checked');
		showtype('settings_functions','top');
		showsetting('settings_gzipcompress', 'settingsnew[gzipcompress]', $settings['gzipcompress'], 'radio');
		showsetting('settings_memliststatus', 'settingsnew[memliststatus]', $settings['memliststatus'], 'radio');
		showsetting('settings_delayviewcount', 'settingsnew[delayviewcount]', $settings['delayviewcount'], 'radio');
		showsetting('settings_statstatus', 'settingsnew[statstatus]', $settings['statstatus'], 'radio');
		showsetting('settings_statcacherefresh', 'settingsnew[statcacherefresh]', $settings['statcacherefresh'], 'text');
		showsetting('settings_userstatusby', '', '', '<input type="radio" name="settingsnew[userstatusby]" value="0" '.$checkstatusby[0].'> '.$lang['settings_display_none'].'<br><input type="radio" name="settingsnew[userstatusby]" value="1" '.$checkstatusby[1].'> '.$lang['usergroup'].'<br><input type="radio" name="settingsnew[userstatusby]" value="2" '.$checkstatusby[2].'> '.$lang['rank'].'</td>');
		showsetting('settings_logincredits', 'settingsnew[logincredits]', $settings['logincredits'], 'text');
		showsetting('settings_maxonlines', 'settingsnew[maxonlines]', $settings['maxonlines'], 'text');
		showsetting('settings_floodctrl', 'settingsnew[floodctrl]', $settings['floodctrl'], 'text');
		showsetting('settings_searchctrl', 'settingsnew[searchctrl]', $settings['searchctrl'], 'text');
		showsetting('settings_maxsearchresults', 'settingsnew[maxsearchresults]', $settings['maxsearchresults'], 'text');
		showtype('', 'bottom');

	}elseif( $options=='6'){
		showtype('settings_post_permissions','top');
		showsetting('settings_reportpost', 'settingsnew[reportpost]', $settings['reportpost'], 'radio');
		showsetting('settings_delayreply', 'settingsnew[delayreply]', $settings['delayreply'], 'text');
		showsetting('settings_delayeditpost', 'settingsnew[delayeditpost]', $settings['delayeditpost'], 'text');
		showsetting('settings_delaykarma', 'settingsnew[delaykarma]', $settings['delaykarma'], 'text');
		showsetting('settings_editby', 'settingsnew[editedby]', $settings['editedby'], 'radio');
		showsetting('settings_minpostsize', 'settingsnew[minpostsize]', $settings['minpostsize'], 'text');
		showsetting('settings_maxpostsize', 'settingsnew[maxpostsize]', $settings['maxpostsize'], 'text');
		showsetting('settings_maxpolloptions', 'settingsnew[maxpolloptions]', $settings['maxpolloptions'], 'text');
		showsetting('settings_postcredits', 'settingsnew[postcredits]', $settings['postcredits'], 'text');
		showsetting('settings_replycredits', 'settingsnew[replycredits]', $settings['replycredits'], 'text');
		showsetting('settings_digestcredits', 'settingsnew[digestcredits]', $settings['digestcredits'], 'text');
		showsetting('settings_deletedcredits', 'settingsnew[deletedcredits]', $settings['deletedcredits'], 'text');
		showtype('', 'bottom');

	}elseif( $options=='7'){
		$settings['timeformat'] == 'H:i' ? $check24 = 'checked' : $check12 = 'checked';
		$settings['dateformat'] = str_replace('n', 'mm', $settings['dateformat']);
		$settings['dateformat'] = str_replace('j', 'dd', $settings['dateformat']);
		$settings['dateformat'] = str_replace('y', 'yy', $settings['dateformat']);
		$settings['dateformat'] = str_replace('Y', 'yyyy', $settings['dateformat']);

		showtype('settings_misc','top');
		showsetting('settings_timeformat', '', '', '<input type="radio" name="settingsnew[timeformat]" value="24" '.$check24.'> 24 Hour <input type="radio" name="settingsnew[timeformat]" value="12" '.$check12.'> 12 Hour</td>');
		showsetting('settings_dateformat', 'settingsnew[dateformat]', $settings['dateformat'], 'text');
		showsetting('settings_timeoffset', 'settingsnew[timeoffset]', $settings['timeoffset'], 'text');
		showsetting('settings_maxavatarsize', 'settingsnew[maxavatarsize]', $settings['maxavatarsize'], 'text');
		showsetting('settings_maxavatarpixel', 'settingsnew[maxavatarpixel]', $settings['maxavatarpixel'], 'text');
		showsetting('settings_bbinsert', 'settingsnew[bbinsert]', $settings['bbinsert'], 'radio');
		showsetting('settings_smileyinsert', 'settingsnew[smileyinsert]', $settings['smileyinsert'], 'radio');
		showsetting('settings_smcols', 'settingsnew[smcols]', $settings['smcols'], 'text');
		showtype('', 'bottom');
	
	}elseif( $options=='21'){
        $checkattach = array(0 => '', 1 => '', 2 => '');
		$checkattach[$settings['attachsave']] = 'checked';
		showtype('settings_attach_upload','top');
		showsetting('settings_attach_upload_max', 'settingsnew[attach_max]', $settings['attach_max'], 'text');
		showsetting('settings_attach_upload_newpost', 'settingsnew[attach_newpost]', $settings['attach_newpost'], 'text');
		showsetting('settings_attach_upload_editpost', 'settingsnew[attach_editpost]', $settings['attach_editpost'], 'text');
		showsetting('settings_attach_upload_replypost', 'settingsnew[attach_replypost]', $settings['attach_replypost'], 'text');
		showtype('', 'bottom');
		showtype('settings_attachsave','top');
		showsetting('settings_attachsave', '', '', '<input type="radio" name="settingsnew[attachsave]" value="0" '.$checkattach[0].'> '.$lang['settings_attachsave_default'].'<br><input type="radio" name="settingsnew[attachsave]" value="1" '.$checkattach[1].'> '.$lang['settings_attachsave_forum'].'<br><input type="radio" name="settingsnew[attachsave]" value="2" '.$checkattach[2].'> '.$lang['settings_attachsave_type'].'<br><input type="radio" name="settingsnew[attachsave]" value="3" '.$checkattach[3].'> '.$lang['settings_attachsave_month'].'<br><input type="radio" name="settingsnew[attachsave]" value="4" '.$checkattach[4].'> '.$lang['settings_attachsave_day'].'</td>');
		showtype('', 'bottom');
		showtype('settings_attachrefcheck','top');
		showsetting('settings_attachimgpost', 'settingsnew[attachimgpost]', $settings['attachimgpost'], 'radio');
		showsetting('settings_attachrefcheck_normal', 'settingsnew[attachrefcheck]', $settings['attachrefcheck'], 'radio');
		showsetting('settings_attachrefcheck_img', 'settingsnew[attachimgcheck]', $settings['attachimgcheck'], 'radio');
		showsetting('settings_attach_softdownload', 'settingsnew[attachsoftdownload]', $settings['attachsoftdownload'], 'radio');
		showsetting('settings_attach_useimagemessage', 'settingsnew[useimagemessage]', $settings['useimagemessage'], 'radio');
		showtype('', 'bottom');
	}else{
		cpmsg('undefined_action');
	}
?>

</table></td></tr></table><br><br>
<center><input type="submit" name="settingsubmit" value="<?=$lang['submit']?>"></center>
</form>

</td></tr>

<?

} else {

	if($options=='1'){
		$settingsnew['bbname'] = dhtmlspecialchars($settingsnew['bbname']);
		//$settingsnew['welcomemsgtxt'] = dhtmlspecialchars($settingsnew['welcomemsgtxt']);

	}elseif($options=='4' ){
		
		if ($settingsnew['maxonlines'] > 65535 || !is_numeric($settingsnew['maxonlines'])) {
			cpmsg('settings_maxonlines_invalid');
		}else{
			$db->query("ALTER TABLE $table_sessions MAX_ROWS={$settingsnew['maxonlines']}");
			if($settingsnew['maxonlines'] < $settings['maxonlines']) {
				$db->query("DELETE FROM $table_sessions");
			}
		}
		if(!function_exists('ob_gzhandler') && $settingsnew['gzipcompress']) {
			cpmsg('settings_gzip_invalid');
		}
	}elseif($options =='7'){
		$settingsnew['timeformat'] = $settingsnew['timeformat'] == '24' ? 'H:i' : 'h:i A';
		$settingsnew['dateformat'] = str_replace('mm', 'n', $settingsnew['dateformat']);
		$settingsnew['dateformat'] = str_replace('dd', 'j', $settingsnew['dateformat']);
		$settingsnew['dateformat'] = str_replace('yyyy', 'Y', $settingsnew['dateformat']);
		$settingsnew['dateformat'] = str_replace('yy', 'y', $settingsnew['dateformat']);

	}elseif($options =='21'){
		$settingsnew['attach_max'] = intval($settingsnew['attach_max']);
		$settingsnew['attach_newpost'] = $settingsnew['attach_newpost']>1 ? $settingsnew['attach_newpost'] : 1;
		$settingsnew['attach_editpost'] = $settingsnew['attach_editpost']>1 ? $settingsnew['attach_editpost'] : 1;
		$settingsnew['attach_replypost'] = $settingsnew['attach_replypost']>1 ? $settingsnew['attach_replypost'] : 1;

		if ($settingsnew['attach_max'] && ($settingsnew['attach_max'] < $settingsnew['attach_newpost'] || $settingsnew['attach_max'] < $settingsnew['attach_editpost'] || $settingsnew['attach_max'] < $settingsnew['attach_replypost'])){
			$settingsnew['attach_max'] = 0 ;
		}

	}
	foreach($settingsnew as $key => $val) {
		if(isset($settings[$key]) && $settings[$key] != $val) {
			$$key = $val;

			if(in_array($key, array('attachimgpost', 'attachrefcheck', 'attachsave', 'attachimgcheck', 'attachsoftdownload', 'useimagemessage', 'attach_max', 'attach_newpost', 'attach_editpost', 'attach_replypost', 'delayreply', 'delayeditpost', 'delaykarma', 'newbiespan', 'topicperpage', 'postperpage','statcacherefresh', 'memberperpage', 'hottopic', 'logincredits', 'postcredits',
						'announcements_num', 'replycredits', 'digestcredits', 'deletedcredits', 'maxonlines', 'loadctrl', 'floodctrl',
						'searchctrl', 'maxsearchresults', 'maxpostsize', 'maxavatarsize', 'maxavatarpixel', 'maxpolloptions', 'smcols'))) {
				$val = intval($val);
			}
			if($key == 'userstatusby') {
				updatecache('usergroups');
			}

			$db->query("UPDATE $table_settings SET value='$val' WHERE variable='$key'");
		}
	}

	updatecache('settings');
	cpmsg('settings_update_succeed');
}

?>