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
1 dir_validº¯Êý
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

@set_time_limit(600);

function cpmsg($message, $url_forward = '', $msgtype = 'message') {
	extract($GLOBALS, EXTR_SKIP);
	$message = dinterpolate(isset($msglang[$message]) ? $msglang[$message] : $message);

	if($msgtype == 'form') {
		$message = "<form method=\"post\" action=\"$url_forward\"><input type=\"hidden\" name=\"formhash\" value=\"".FORMHASH."\">".
			"<br><br><br>$message<br><br><br><br>\n".
        		"<input type=\"submit\" name=\"confirmed\" value=\"{$lang['ok']}\"> &nbsp; \n".
       			"<input type=\"button\" value=\"{$lang['cancel']}\" onClick=\"history.go(-1);\"></form><br>";
	} else {
		if($url_forward) {
			$message .= "<br><br><br><a href=\"$url_forward\">{$lang['message_redirect']}</a>";
			$url_forward = transsid($url_forward);
			$message .= "<script>setTimeout(\"redirect('$url_forward');\", 1250);</script>";
		} elseif(strpos($message, $lang['return'])) {
			$message .= "<br><br><br><a href=\"javascript:history.go(-1);\" class=\"mediumtxt\">{$lang['message_return']}</a>";
		}
		$message = "<br><br><br>$message<br><br>";
	}

?>
<br><br><br><br><br><br><table cellspacing="0" cellpadding="0" border="0" width="460" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td>Discuz! Message</td></tr><tr><td bgcolor="<?=ALTBG2?>" align="center">
<table border="0" width="90%" cellspacing="0" cellpadding="0"><tr><td width="100%" align="center">
<?=$message?><br><br>
</td></tr></table></td></tr></table></td></tr></table><br><br><br>
<?

	cpfooter();
	dexit();
}

function dir_valid($dir) {
	return is_dir(DISCUZ_ROOT.'./'.$dir) && !in_array(substr($dir, -1, 1), array('/', '\\')) &&
		 strpos(realpath(DISCUZ_ROOT.'./'.$dir), realpath(DISCUZ_ROOT.'./templates')) === 0 && strstr(realpath(DISCUZ_ROOT.'./'.$dir), realpath(DISCUZ_ROOT.'./templates'));
/*
	$path = trim(DISCUZ_ROOT.'./'.$dir);
	return is_dir($path) && !in_array(substr($path, -1, 1), array('/', '\\'))
		&& substr(realpath($path), 0, strlen(DISCUZ_ROOT)) == DISCUZ_ROOT
		? TRUE : FALSE;
*/
}

function dir_writeable($dir) {
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.test", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.test");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function showforum($forum, $id, $type = '') {
	$dot = array(1 => "<li>", 2 => "<li type=\"circle\">", 3 => "<li type=\"square\">");
	$url = $type == "group" ? "./index.php?gid={$forum['fid']}" : "./forumdisplay.php?fid={$forum['fid']}";
	$editforum = "<a href=\"admincp.php?action=forumdetail&fid={$forum['fid']}\">[".$GLOBALS['lang']['edit'].']</a> ';
	$hide = !$forum['status'] ? ' ('.$GLOBALS['lang']['forums_hidden'].')' : NULL;
	echo $dot[$id]."<a href=\"$url\" target=\"_blank\"><b>{$forum['name']}</b><span class=\"smalltxt\">$hide</a> - ".$GLOBALS['lang']['display_order'].": <input type=\"text\" name=\"order[{$forum['fid']}]\" value=\"{$forum['displayorder']}\" size=\"1\">".
		($forum['type'] != 'group' ? '&nbsp; '.$GLOBALS['lang']['forums_moderator'].": <input type=\"text\" name=\"moderator[{$forum['fid']}]\" value=\"{$forum['moderator']}\" size=\"15\"> - " : ' - ').
		"$editforum<a href=\"admincp.php?action=forumdelete&fid={$forum['fid']}\">".
		'['.$GLOBALS['lang']['delete'].']</a></span><br></li>';
}

function showtype($name, $type = '') {
	if($GLOBALS['lang'][$name]) {
		$name = $GLOBALS['lang'][$name];
	}
	if($type != 'bottom') {
		if(!$type) {
			echo '</table></td></tr></table><br><br>';
		}
		if(!$type || $type == 'top') {

?>
<a name="#<?=$name?>"></a>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header">
<td colspan="3"><?=$name?></td>
</tr>
<?

		}
	} else {
		echo '</table></td></tr></table>';
	}
}

function showsetting($setname, $varname, $value, $type = 'radio', $width = '60%') {
	$comment = $GLOBALS['lang'][$setname.'_comment'];


	$aligntop = $type == "textarea" || $width != "60%" ?  "valign=\"top\"" : NULL;
	
	if ($type =='hidden'){
		echo "<input type=\"hidden\" name=\"$varname\" value=\"$value\">\n";
		return;
	}elseif($type =='form'){
		echo "<form method=\"post\" name=\"$varname\" action=\"$value\">\n";
		echo '<input type="hidden" name="formhash" value="'.FORMHASH."\">\n";
		return;
	}elseif($type =='submit'){
		echo "<p align=center><input type=\"submit\" name=\"$varname\" value=\"$value\"></p></form>\n";
		return;
	}

	echo "<tr><td width=\"$width\" bgcolor=\"".ALTBG1."\" $aligntop>".
		'<b>'.(isset($GLOBALS['lang'][$setname]) ? $GLOBALS['lang'][$setname] : $setname).'</b>'.($comment ? '<br><span class="smalltxt">'.$comment.'</span>' : NULL).'</td>'.
		'<td bgcolor="'.ALTBG2.'">';

	if($type == 'radio' || $type == 'radioyes') {
		if ($type == 'radioyes') $value = 1;
		$value ? $checktrue = "checked" : $checkfalse = "checked";
		echo "<input type=\"radio\" name=\"$varname\" value=\"1\" $checktrue> {$GLOBALS['lang']['yes']} &nbsp; &nbsp; \n".
			"<input type=\"radio\" name=\"$varname\" value=\"0\" $checkfalse> {$GLOBALS['lang']['no']}\n";
	} elseif($type == 'color') {
		$preview_varname = str_replace('[', '_', str_replace(']', '', $varname));
		echo "<input type=\"text\" size=\"30\" value=\"$value\" name=\"$varname\" onchange=\"this.form.$preview_varname.style.backgroundColor=this.value;\">\n".
			"<input type=\"button\" id=\"$preview_varname\" value=\"\" style=\"background-color: $value\" disabled>\n";
	} elseif($type == 'text') {
		echo "<input type=\"text\" size=\"30\" value=\"$value\" name=\"$varname\">\n";
	} elseif($type == "textarea") {
		echo "<textarea rows=\"5\" name=\"$varname\" cols=\"30\">".dhtmlspecialchars($value)."</textarea>";
	} else {
		echo $type;
	}
	echo '</td></tr>';
}

function showconf($settings, $width = '50%') {
	global $group,$plugin;
	$setname= $settings['conf_title'];
	$type = $settings['conf_type'];
	$varname ='conf_settings['.$settings['conf_key'].']';
	$value = $settings['conf_value'];
	$cid = $settings['conf_id'];
	$extra = $settings['conf_extra'];
	$comment = nl2br($settings['conf_desc']);
	$aligntop = $type == "textarea" || $width != "50%" ?  "valign=\"top\"" : NULL;
	
	echo "<tr><td width=\"$width\" bgcolor=\"".ALTBG1."\" $aligntop><b>$setname  :</b>&nbsp;<span class=\"smalltxt\"><font color = \"gray\">\${$plugin['plug_key']}[".$settings['conf_key'].']&nbsp;</font>'.($comment ? '<br>'.$comment.'</span>' : NULL).'</td>'.
		'<td bgcolor="'.ALTBG2.'">';

	if($type == 'radio') {
		$value ? $checktrue = "checked" : $checkfalse = "checked";
		echo "<input type=\"radio\" name=\"$varname\" value=\"1\" $checktrue> {$GLOBALS['lang']['yes']} &nbsp; &nbsp; \n".
			"<input type=\"radio\" name=\"$varname\" value=\"0\" $checkfalse> {$GLOBALS['lang']['no']}\n";
	} elseif($type == 'color') {
		$preview_varname = str_replace('[', '_', str_replace(']', '', $varname));
		echo "<input type=\"text\" size=\"30\" value=\"$value\" name=\"$varname\" onchange=\"this.form.$preview_varname.style.backgroundColor=this.value;\">\n".
			"<input type=\"button\" id=\"$preview_varname\" value=\"\" style=\"background-color: $value\" disabled>\n";
	} elseif($type == 'text') {
		echo "<input type=\"text\" size=\"30\" value=\"$value\" name=\"$varname\">\n";
	} elseif($type == "textarea") {
		echo "<textarea rows=\"5\" name=\"$varname\" cols=\"30\">".dhtmlspecialchars($value)."</textarea>";
	} elseif($type == "select") {
		$select_array = explode("\n",$extra);
		echo "<select name=\"$varname\" style=\"width:160\">\n";
		if (is_array($select_array) && count($select_array)){
			foreach($select_array as $key => $option){
				$selected = ($option == $value)?'selected':'';
				echo "<option value=\"$option\" $selected > $option </option>\n";
			}
		}
		echo "</select>\n";
	} else {
		echo $type;
	}
	echo "</td><td bgcolor=\"#E4EAF2\" width=\"50\"><a href=\"admincp.php?action=plugin&mod=setup&do=editconf&group=$group&cid=$cid\"><img src=\"images/admincp/acp_edit.gif\" border=\"0\" alt=\"edit\"></a><a href=\"javascript:dzconfirm('admincp.php?action=plugin&mod=setup&do=delconf&group=$group&cid=$cid','".$GLOBALS['lang']['confirm_delete']."')\"><img src=\"images/admincp/acp_del.gif\" border=\"0\" alt=\"delete\"></a></td></tr>";
}

function showmenu($title, $menus = array()) {
	global $menucount, $expand;
	if(is_array($menus)) {
		$menucount++;
	}
?>
<tr><td bgcolor="<?=ALTBG1?>"><a name="#<?=$menucount?>"></a>
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center"> 
<tr><td bgcolor="<?=BORDERCOLOR?>"> 
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" > 
<?

	if(is_array($menus)) {
		$expanded = preg_match("/(^|_)$menucount($|_)/is", $expand);
		echo "<tr><td width=\"100%\" class=\"header\"><img src=\"images/common/".($expanded ? 'minus' : 'plus').".gif\"><a href=\"admincp.php?action=menu&expand=$expand&change=$menucount#$menucount\" style=\"color: ".HEADERTEXT."\">$title</td></tr>\n";
		
		if($expanded) {
			foreach($menus as $menu) {
				echo "<tr><td bgcolor=\"".ALTBG2."\" onMouseOver=\"this.style.backgroundColor='".ALTBG1."'\" onMouseOut=\"this.style.backgroundColor='".ALTBG2."'\"><img src=\"images/common/spacer.gif\"><a href=\"{$menu['url']}\" target=\"main\">{$menu['name']}</a></td></tr>";
			}
		}
	} else {
		echo "<tr><td width=\"100%\" class=\"header\"><img src=\"images/common/plus.gif\"><a href=\"$menus\" target=\"main\" style=\"color: ".HEADERTEXT."\">$title</a></td></tr>\n";
	
	}
	echo "</table></td></tr></table></td></tr>";
}

function sqldumptable($table, $startfrom = 0, $currsize = 0) {
	global $db, $sizelimit, $startrow;
	$offset = 300;
	if(!$startfrom) {
		$tabledump = "DROP TABLE IF EXISTS $table;\n";

		$createtable = $db->query("SHOW CREATE TABLE $table");
		$create = $db->fetch_row($createtable);

		$tabledump .= $create[1].";\n\n";
	}

	$tabledumped = 0;
	$numrows = $offset;
	while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
		$tabledumped = 1;
		$limitadd = "LIMIT $startfrom, $offset";
		$startfrom += $offset;
		$rows = $db->query("SELECT * FROM $table $limitadd");
		$numfields = $db->num_fields($rows);
		$numrows = $db->num_rows($rows);
		while ($row = $db->fetch_row($rows)) {
			$comma = "";
			$tabledump .= "INSERT INTO $table VALUES(";
			for($i = 0; $i < $numfields; $i++) {
				$tabledump .= $comma."'".mysql_escape_string($row[$i])."'";
				$comma = ",";
			}
			$tabledump .= ");\n";
		}
	}

	$startrow = $startfrom;
	$tabledump .= "\n";
	return $tabledump;
}

function splitsql($sql){
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return($ret);
}

function cpheader() {
	extract($GLOBALS, EXTR_SKIP);
	$css=template('css');
	include DISCUZ_ROOT.'./admin/tpl/header.php';
}

function cpfooter() {
	global $version;
	include DISCUZ_ROOT.'./admin/tpl/footer.php';
	updatesession();
}

function dirsize($dir) { 
	@$dh = opendir($dir);
	$size = 0;
	while ($file = @readdir($dh)) {
		if ($file != '.' and $file != '..') {
			$path = $dir."/".$file;
			if (@is_dir($path)) {
				$size += dirsize($path);
			} else {
				$size += filesize($path);
			}
		}
	}
	@closedir($dh);
	return $size;
}
?>