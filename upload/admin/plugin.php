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

$pluginstat = array( $lang['plug_close'] ,  $lang['plug_hide'] ,  $lang['plug_open']);

if( $do <> 'explord'){
	cpheader();
}


//===============================
// Add plugin (setting's group)
//===============================
if($mod == 'add') {
	if(!submitcheck('addsubmit')) {
		showsetting('form', 'pluginform', 'admincp.php?action=plugin&mod=add', 'form');
		showtype('plug_add', 'top');
		showsetting('plug_title', 'plug_title', '', 'text');
		showsetting('plug_author', 'plug_author', '', 'text');
		showsetting('plug_version', 'plug_version', '', 'text');
		showsetting('plug_key', 'plug_key', '', 'text');
		showsetting('plug_cp', 'plug_cp', '', 'text');
		showsetting('plug_tables', 'plug_tables', '', 'text');
		showsetting('plug_desc', 'plug_desc', '', 'textarea');
		showsetting('plug_license', 'plug_license', '', 'textarea');
		showtype('', 'bottom');
		showsetting('submit', 'addsubmit', $lang['submit'], 'submit');

		showsetting('form', 'pluginform', 'admincp.php?action=plugin&mod=import', 'form');
		showtype('plugin_import', 'top');
		showsetting('plugin_import_allways', 'importallways', '0', 'radio');
		echo '<tr ><td colspan="3" bgcolor="'.ALTBG2.'"><textarea name="confdata" cols="100" rows="10"></textarea></td></tr>';
		showtype('', 'bottom');
		showsetting('plug_desc', 'importsubmit', $lang['submit'], 'submit');

	} else{
		$plug_title		= dhtmlspecialchars(trim($plug_title));
		$plug_author	= dhtmlspecialchars($plug_author);
		$plug_version	= dhtmlspecialchars(trim($plug_version));
		$plug_key		= trim($plug_key);
		$plug_cp		= dhtmlspecialchars(trim($plug_cp));
		$plug_tables	= dhtmlspecialchars(trim($plug_tables));
		$plug_desc		= dhtmlspecialchars($plug_desc);
		$plug_license	= dhtmlspecialchars($plug_license);
		
		if (!$plug_title) cpmsg('plugin_title_error');
		if (!$plug_key || !is_key($plug_key)) cpmsg('plugin_keyword_error');
		
		$query = $db->query("SELECT plug_id FROM $table_plugins where plug_key='$plug_key' limit 1");
		if($db->result($query, 0)) 	cpmsg('plugin_keyword_used');

		$db->query("INSERT INTO $table_plugins (plug_title, plug_version, plug_author, plug_key, plug_cp, plug_tables, plug_license, plug_desc)	VALUES ('$plug_title', '$plug_version', '$plug_author', '$plug_key', '$plug_cp', '$plug_tables', '$plug_license', '$plug_desc')");
		plugin_updatecache();
		cpmsg('plugin_add_ok', 'admincp.php?action=plugin&mod=list');
	}


//===============================
// Edit plugin (setting's group)
//===============================

}elseif($mod == 'edit' ){
	$id = intval($id);
	$query = $db->query("SELECT * FROM $table_plugins where plug_id='$id' limit 1");
	if (!$plugin = $db->fetch_array($query)){
		cpmsg('plugin_notfound', 'admincp.php?action=plugin&mod=list');
	}
	if(!submitcheck('editsubmit')) {

		$plugin[plug_open] = $plugin['plug_stats'] ? 1 : 0;
		$plugin[plug_showinmenu] = $plugin['plug_stats'] > 1 ? 1 : 0;

		showsetting('form', 'pluginform', 'admincp.php?action=plugin&mod=edit', 'form');
		showsetting('plugid', 'id', $id, 'hidden');
		showtype('plug_edit', 'top');
		showsetting('plug_title', 'plug_title', $plugin[plug_title], 'text');
		showsetting('plug_version', 'plug_version', $plugin[plug_version], 'text');
		showsetting('plug_opennow', 'plug_open', $plugin[plug_open], 'radio');
		showsetting('plug_showinmenu', 'plug_show', $plugin[plug_showinmenu], 'radio');
		showsetting('plug_key', 'plug_key', $plugin[plug_key], 'text');
		showsetting('plug_cp', 'plug_cp', $plugin[plug_cp], 'text');
		showsetting('plug_tables', 'plug_tables', $plugin[plug_tables], 'text');
		showsetting('plug_desc', 'plug_desc', $plugin[plug_desc], 'textarea');
		showsetting('plug_license', 'plug_license', $plugin[plug_license], 'textarea');
		showtype('', 'bottom');
		showsetting('', 'editsubmit', $lang['submit'], 'submit');
		

	}else{
		$plug_title		= dhtmlspecialchars(trim($plug_title));
		$plug_version	= dhtmlspecialchars(trim($plug_version));
		$plug_key		= trim($plug_key);
		$plug_cp		= dhtmlspecialchars(trim($plug_cp));
		$plug_tables	= dhtmlspecialchars(trim($plug_tables));
		$plug_desc		= dhtmlspecialchars($plug_desc);
		$plug_license	= dhtmlspecialchars($plug_license);

		if (!$plug_title) cpmsg('plugin_title_error');
		if (!$plug_key || !is_key($plug_key)) cpmsg('plugin_keyword_error');
		if ($plug_key <> $plugin['plug_key'] ){
			$query = $db->query("SELECT plug_id FROM $table_plugins where plug_key='$plug_key' limit 1");
			if($db->result($query, 0)) {
				cpmsg('plugin_keyword_used');
			}
			$db->query("UPDATE $table_plugins_settings set conf_group='$plug_key'  where conf_group='".$plugin['plug_key']."'");

		}

		$plug_stats = $plug_open ? ($plug_show ? 2 : 1) :0 ;

		$db->query("UPDATE $table_plugins set plug_title='$plug_title', plug_version='$plug_version', plug_stats='$plug_stats' ,plug_key='$plug_key', plug_cp='$plug_cp', plug_tables='$plug_tables', plug_license='$plug_license', plug_desc='$plug_desc' where plug_id='$id' limit 1");
		plugin_updatecache();
		cpmsg('plugin_edit_ok', 'admincp.php?action=plugin&mod=list');
	}


//===============================
// Delete plugin (setting's group)
//===============================
}elseif($mod == 'delete' ){
	$id = intval($id);
	$query = $db->query("SELECT plug_title,plug_key FROM $table_plugins where plug_id='$id' limit 1");
	if (!$plugin = $db->fetch_array($query)){
		cpmsg('plugin_notfound', 'admincp.php?action=plugin&mod=list');
	}
	if(!submitcheck('deletesubmit')) {
		$tip = $lang['plugin_delete_tip'];
		$message = $lang['plugin_delete_message']. " <b>$plugin[plug_title] </b>";
		$submitaction = 'admincp.php?action=plugin&mod=delete';
		$addvalue = "<input type='hidden' name='id' value='$id'>\n";
		$addvalue .= "<input type='hidden' name='deletesubmit' value='1'>";
		include CP_TPL.'plugin_submit.php';
		
	}else{
		if($yes){
			$db->query("DELETE FROM $table_plugins where plug_id='$id' ");
			$db->query("DELETE FROM $table_plugins_settings where conf_group='$plugin[plug_key]' ");
			plugin_updatecache();
			cpmsg('plugin_deleteok', 'admincp.php?action=plugin&mod=list');
		}else{
			cpmsg('plugin_cancel', 'admincp.php?action=plugin&mod=list');
		}
	}


//================================
// List plugins (setting's groups)
//================================
}elseif($mod=='list' ){
	$basecplink ='admincp.php?action=plugin';
	$plugins = '';
	$query = $db->query("SELECT * FROM $table_plugins ORDER BY plug_id ASC");
	include CP_TPL.'plugin_list.php';


//=================================
//import plugins (setting's groups)
//=================================
}elseif($mod == 'import' && submitcheck('importsubmit')){
	$basecplink ='admincp.php?action=plugin&mod=list';
	$confarray = array();
	$confdata  = preg_replace("/(#.*\s+)*/", '', $confdata);
	$confarray = daddslashes(unserialize(base64_decode($confdata)), 1);
	if(!is_array($confarray) || !count($confarray)) {
		cpmsg('plug_import_data_invalid');
	}
	$plugin   = $settings = array();
	$plugin   = $confarray['plugin'];
	$settings = $confarray['settings'];

	showtype($lang['plugin_import'], 'top');
	showsetting('plug_title', 'plug_title','',$plugin[plug_title]);
	showsetting('plug_board_version', 'plug_version','',$confarray['boardversion']);
	showsetting('plug_version', 'plug_version','',$confarray['groupversion']);
	showsetting('plug_author', 'plug_version','',$confarray['groupauthor']);
	showsetting('plug_key', 'plug_key','', $plugin[plug_key]);
	showsetting('plug_cp', 'plug_cp','' , $plugin[plug_cp]);
	showsetting('plug_tables', 'plug_tables','' , $plugin[plug_tables]);
	showsetting('plug_desc', 'plug_desc', '', nl2br($plugin[plug_desc]));
	showsetting('plug_license', 'plug_desc', '', nl2br($plugin[plug_license]));
	showtype('', 'bottom');

	if(strip_tags($confarray['boardversion']) != strip_tags($version)) {
		$forumversion = strip_tags($confarray['version']);
		cpmsg('plug_import_version_invalid');
	}
	if (!$plugin['plug_title']) cpmsg('plugin_title_error');
	if (!$plugin['plug_key'] || !is_key($plugin['plug_key'])) cpmsg('plugin_keyword_error');
	$query = $db->query("SELECT plug_id FROM $table_plugins where plug_key='$plugin[plug_key]' limit 1");
	if($db->result($query, 0)) {
		if (!$importallways) cpmsg('plugin_already_installed');
		$db->query("DELETE FROM $table_plugins where plug_key='$plugin[plug_key]'");
		$db->query("DELETE FROM $table_plugins_settings where conf_group='$plugin[plug_key]'");
	}
	$db->query("INSERT INTO $table_plugins (plug_title, plug_version, plug_author, plug_key, plug_cp, plug_tables, plug_license, plug_desc)	VALUES ('$plugin[plug_title]', '$plugin[plug_version]', '$plugin[plug_author]', '$plugin[plug_key]', '$plugin[plug_cp]', '$plugin[plug_tables]', '$plugin[plug_license]', '$plugin[plug_desc]')");
	plugin_updatecache();
	if (is_array($settings) && count($settings)){
		foreach($settings as $setting){
				$db->query("INSERT INTO $table_plugins_settings (conf_title , conf_desc , conf_group , conf_type , conf_key , conf_value , conf_extra , conf_position , conf_cached ) 
				VALUES ('$setting[conf_title]', '$setting[conf_desc]', '$plugin[plug_key]', '$setting[conf_type]','$setting[conf_key]', '$setting[conf_value]', '$setting[conf_extra]','$setting[conf_position]' , '$setting[conf_cached]')");
		}
		plugin_updatecache(0);
		cpmsg('plug_import_ok', $basecplink);
	}


//=================================
//updatecache plugins (setting's groups)
//=================================
}elseif($mod=='updatecache'){
	$basecplink ='admincp.php?action=plugin&mod=list';
	plugin_updatecache();
	cpmsg('plug_updatecache_ok', $basecplink);


//================================================
// The Plugins's CP For normal plugins !        
//                                              
// $do:    Add, delete , modify, update settings
//		   explord ,imp the group settings
// $group: the plugin's keyname                 
//================================================
}elseif($mod=='setup'){

	if (!trim($group)) 	cpmsg('plugin_notfound', 'admincp.php?action=plugin&mod=list');

	$basecplink ="admincp.php?action=plugin&mod=setup&group=$group";
	$query = $db->query("SELECT * FROM $table_plugins where plug_key='$group' limit 1");
	
	if (!$plugin = $db->fetch_array($query)){
		cpmsg('plugin_notfound', 'admincp.php?action=plugin&mod=list');
	}elseif($plugin['plug_cp'] && $do <> 'explord'){
		if(file_exists(DISCUZ_ROOT.'./admin/'.$plugin['plug_cp'])){
			include DISCUZ_ROOT.'./admin/'.$plugin['plug_cp'];
			dexit();
		}else{
			cpmsg('plugin_cpnotfound', 'admincp.php?action=plugin&mod=list');
		}
	}

	//========================
	// Add setting's options
	//========================
	if ($do == 'add' && !submitcheck('addsubmit')) {
		
		$p_array = array( 
			'conf_title'		=> 'text',
			'conf_key'			=> 'text',
			'conf_type'			=> '<select name="conf_type" ><option value="text">text</option><option value="radio">radio</option><option value="textarea">textarea</option><option value="select">select</option></seletc>',
			'conf_value'		=> 'text',
			'conf_position'		=> 'text',
			'conf_cached'		=> 'radioyes',
			'conf_extra'		=> 'textarea',
			'conf_desc'			=> 'textarea',
		);

		include CP_TPL.'plugin_settings.php';
		showsetting('form', 'pluginform', $basecplink.'&do=add', 'form');
		showtype($plugin['plug_title'].' - '.$lang['plugin_addsetting'], 'top');
		foreach ($p_array as $key =>$val ){
			showsetting( $key, $key, '' ,$val);
		}
		showtype('', 'bottom');
		showsetting('', 'addsubmit', $lang['submit'], 'submit');

	}elseif ($do == 'add' && submitcheck('addsubmit')) {
		$conf_title = dhtmlspecialchars(trim($conf_title));
		$conf_key = trim($conf_key);
		$conf_value = dhtmlspecialchars(trim($conf_value));
		$conf_extra = dhtmlspecialchars(trim($conf_extra));
		$conf_position = intval(trim($conf_position));
		$conf_cached = $conf_cached? 1 : 0;
		$conf_desc = trim($conf_desc);

		if (!$conf_title) cpmsg('conf_title_error');

		if (!$conf_key || !is_key($conf_key)){
			cpmsg('conf_key_error');
		}else{
			$query = $db->query("SELECT conf_id FROM $table_plugins_settings where conf_key ='$conf_key'  AND conf_group='$group' limit 1");
			if($db->result($query, 0)) {
				cpmsg('conf_keyword_used');
			}else{
				$db->query("INSERT INTO $table_plugins_settings (conf_title , conf_desc , conf_group , conf_type , conf_key , conf_value , conf_extra ,  conf_position , conf_cached ) 
				VALUES ('$conf_title', '$conf_desc', '$group', '$conf_type','$conf_key', '$conf_value', '$conf_extra','$conf_position' , '$conf_cached')");
				plugin_updatecache(0);
				cpmsg('conf_add_ok', $basecplink);
			}
		}


	//========================
	// Delete setting's options
	//========================

	}elseif ($do == 'delconf' && $cid=intval($cid)) {
		$db->query("DELETE FROM $table_plugins_settings where conf_id='$cid' ");
		plugin_updatecache(0);
		cpmsg('conf_del_ok', $basecplink);
	
	//========================
	// Edit setting's options
	//========================

	}elseif ($do == 'editconf' && $cid=intval($cid)) {
		$query = $db->query("SELECT * FROM $table_plugins_settings where conf_id ='$cid'");
		if (!$settings = $db->fetch_array($query)){
			cpmsg('conf_notfound', $basecplink);
		}

		$conf_type_array =array('text','radio','textarea','select','color');
		$conf_type_select = '<select name="conf_type" >';
		foreach($conf_type_array as $key => $val) {
			if ($val == $settings['conf_type']) $selected = 'selected';
			else $selected = '';
			$conf_type_select .= "<option value=\"$val\" $selected >$val</option>";
		}
		$conf_type_select .='</select>';

		if(!submitcheck('editsubmit')){
			$p_array = array( 
				'conf_title'		=> 'text',
				'conf_key'			=> 'text',
				'conf_value'		=> 'text',
				'conf_type'			=> $conf_type_select,
				'conf_position'		=> 'text',
				'conf_cached'		=> 'radio',
				'conf_extra'		=> 'textarea',
				'conf_desc'			=> 'textarea',
			);

			include CP_TPL.'plugin_settings.php';
			showsetting('form', 'pluginform', $basecplink.'&do=editconf', 'form');
			showsetting('confid', 'cid', $cid, 'hidden');
			showtype($plugin['plug_title'].' - '.$lang['plugin_editsetting'], 'top');
			foreach ($p_array as $key => $val){
				showsetting( $key,$key,$settings[$key],$val);
			}
			showtype('', 'bottom');
			showsetting('', 'editsubmit', $lang['submit'], 'submit');

		}else{
			$conf_title = dhtmlspecialchars(trim($conf_title));
			$conf_key = trim($conf_key);
			$conf_value = dhtmlspecialchars(trim($conf_value));
			$conf_extra = dhtmlspecialchars(trim($conf_extra));
			$conf_position = intval(trim($conf_position));
			$conf_cached = $conf_cached? 1 : 0;
			$conf_desc = trim($conf_desc);

			if (!$conf_title) cpmsg('conf_title_error');

			if (!$conf_key || !is_key($conf_key)){
				cpmsg('conf_key_error');
			}elseif($conf_key <> $settings['conf_key']){
				$query = $db->query("SELECT conf_id FROM $table_plugins_settings where conf_key ='$conf_key' AND conf_group='$group' limit 1");
				if($db->result($query, 0)) {
					cpmsg('conf_keyword_used');
				}
			}
			$db->query("UPDATE $table_plugins_settings set conf_title='$conf_title' , conf_desc='$conf_desc' , conf_type='$conf_type' , conf_key='$conf_key', conf_value='$conf_value' , conf_extra='$conf_extra' ,  conf_position='$conf_position' , conf_cached='$conf_cached' WHERE conf_group='$group' AND conf_id='$cid'");
			plugin_updatecache(0);
			cpmsg('conf_edit_ok', $basecplink);
		}
	
	//========================
	// Update settings
	//========================

	}elseif($do == 'update' && submitcheck('editsubmit')){
		foreach($conf_settings as $key => $val) {
			$db->query("UPDATE $table_plugins_settings SET conf_value='$val' WHERE conf_key='$key' AND conf_group='$group'");
		}
		plugin_updatecache(0);
		cpmsg('conf_update_ok', $basecplink);

	
	//========================
	//explord the group settings 
	//========================
	}elseif($do == 'explord'){
		$plugarray = array();
		$plugarray['plugin'] = $plugin;

		$plugarray['boardversion'] = strip_tags($version);
		$plugarray['groupkey'] = $plugin[plug_key];
		$plugarray['groupauthor'] = $plugin[plug_author];
		$plugarray['grouptitle'] = $plugin[plug_title];
		$plugarray['groupversion'] = strip_tags($plugin[plug_version]);

		$time = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);

		$query = $db->query("SELECT * from $table_plugins_settings where conf_group='$group'");
		while($settings = $db->fetch_array($query)) {
			$plugarray[settings][] = $settings ;
		}
		
		$plug_export = "# Discuz! Plugin's settings Dump".PHP_NEXTLINE.
			"# Board Version   : Discuz! $version".PHP_NEXTLINE.
			"# Plugin's Key    : ".$plugarray['groupkey'].''.PHP_NEXTLINE.
			"# Plugin's Title  : ".$plugarray['grouptitle'].''.PHP_NEXTLINE.
			"# Plugin's Version: ".$plugarray['groupversion'].''.PHP_NEXTLINE.
			"# Plugin's Author : ".$plugarray['groupauthor'].''.PHP_NEXTLINE.
			"# Time            : $time".PHP_NEXTLINE.
			"# From            : $bbname ($boardurl)".PHP_NEXTLINE.
			"# --------------------------------------".PHP_NEXTLINE.
			"# FreeDiscuz! Suport: http://www.freediscuz.net".PHP_NEXTLINE.
			"# --------------------------------------".PHP_NEXTLINE.PHP_NEXTLINE.
				wordwrap(base64_encode(serialize($plugarray)), 60, PHP_NEXTLINE, 1);

		ob_end_clean();
		header('Content-Encoding: none');
		header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
		header('Content-Disposition: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ').'filename="plug_'.$plugarray[groupkey].'.txt"');
		header('Content-Length: '.strlen($plug_export));
		header('Pragma: no-cache');
		header('Expires: 0');
		echo $plug_export;
		exit();
	
	}else{ 
		include CP_TPL.'plugin_settings.php';
		showsetting('form', 'pluginform', $basecplink.'&do=update', 'form');
		showsetting('editsubmit', 'editsubmit', '1', 'hidden');
		showtype($lang['plugin_setup'].' - '.$plugin['plug_title'], 'top');
		$query = $db->query("SELECT * from $table_plugins_settings where conf_group ='$group' ORDER BY conf_position");
		$scount = 0;
		while($setting = $db->fetch_array($query)) {
			$scount ++;
			showconf($setting);
		}
		if ($scount){
			showtype('', 'bottom');
			showsetting('', 'editsubmit', $lang['submit'], 'submit');
		}else{
			showsetting($lang['plugin_nosettings'], '', '', '');
			showtype('', 'bottom');
		}
	}
}else{
	cpmsg('noaccess');
}

function is_key( $str ){
		return ereg("^[a-zA-Z]+[a-zA-Z0-9_]+$", $str);
}

function plugin_updatecache($all = 1){
	updatecache('plugins');
	if ($all){
		updatecache('settings');
	}
}


//==================================================
// Build a custmus cache file that included settings
// cachefile   : forumdata/cache/plug_{cachename}.php
// settings    : $_settings[{cachename}]
// $cachearray : the file's data.
//==================================================
function buildcache($cachename, $cachearray=array()) {
	if (!$cachename) return false;
	$cachedata = "\$_settings['$cachename'] = ".var_export($cachearray , TRUE).';'.PHP_NEXTLINE.PHP_NEXTLINE;
	writetocache($cachename, $cachename, $cachedata, 'plug_');
}

?>