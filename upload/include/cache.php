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

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

function array_del( &$array , $keys){
	$f=explode(',',$keys);
	foreach( $f as $key) {
		if(trim($f)) unset($array["$key"]);
	}
}

function updatecache($cachename = '') {
	global $db, $bbname, $table_styles, $table_stylevars, $table_templates, $table_usergroups,$table_admingroups;

	$cachescript = array(	
				'settings'	=> array('settings'),
				'index'		=> array('announcements','onlinelist', 'forumlinks'),
				'forumdisplay'	=> array('announcements_forum','forums','icons','onlinelist'),
				'viewthread'	=> array('forums', 'usergroups', 'ranks','bbcodes','smilies'),
				'post'		=> array('smilies', 'icons'),
				'censor'	=> array('censor'),
				'forums'	=> array('forums'),
				'ipbanned'	=> array('ipbanned'),
				'pm'		=> array('bbcodes','smilies'),
				'plugins'	=> array('plugins', 'plugins_settings')
			);

	foreach($cachescript as $script => $cachenames) {
		if(!$cachename || ($cachename && in_array($cachename, $cachenames))) {
			writetocache($script, $cachenames);
		}
	}
	if(!$cachename || $cachename == 'styles') {
		$stylevars = array();
		$query = $db->query("SELECT * FROM $table_stylevars");
		while($var = $db->fetch_array($query)) {
			$stylevars[$var['styleid']][$var['variable']] = $var['substitute'];
		}
		$query = $db->query("SELECT s.*, t.charset, t.directory AS tpldir FROM $table_styles s LEFT JOIN $table_templates t ON s.templateid=t.templateid");
		while($data = $db->fetch_array($query)) {
			$data = array_merge($data, $stylevars[$data['styleid']]);

			$data['bgcode'] = strpos($data['bgcolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[bgcolor]\")" : "background-color: $data[bgcolor]";
			$data['catbgcode'] = strpos($data['catcolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[catcolor]\")" : "background-color: $data[catcolor]";
			$data['headerbgcode'] = strpos($data['headercolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[headercolor]\")" : "background-color: $data[headercolor]";
			$data['boardlogo'] = image($data['boardimg'], $data['imgdir'], "alt=\"$bbname\"");
			$data['bold'] = $data['nobold'] ? 'normal' : 'bold';

			writetocache($data['styleid'], '', getcachevars($data, 'CONST'), 'style_');
		}
	}
	if(!$cachename || $cachename == 'usergroups') {
		$query = $db->query("SELECT * FROM $table_usergroups");
		while($data = $db->fetch_array($query)) {
			array_del($data,'type,creditshigher,creditslower,groupavatar');
			$tempdate =getcachevars($data);
			$gid=$data['groupid'];
			if ($gid<4 ){
				$query2 = $db->query("SELECT * FROM $table_admingroups where admingid='$gid'");
				$data2 = $db->fetch_array($query2);
				array_del($data2,'admingid');
				$tempdate .=PHP_NEXTLINE.getcachevars($data2);
			}
			writetocache($data['groupid'], '', $tempdate, 'usergroup_');
		}
	}
	if(!$cachename || $cachename == 'admingroups') {
		$table=$GLOBALS['table_admingroups'];
		$query = $db->query("SELECT * FROM $table");
		while($data = $db->fetch_array($query)) {
			writetocache($data['admingid'], '', getcachevars($data), 'admingroup_');
		}
	}

}

function writetocache($script, $cachenames, $cachedata = '', $prefix = 'cache_') {
	global $db;
	if(is_array($cachenames) && !$cachedata) {
		foreach($cachenames as $name) {
			$cachedata .= getcachearray($name);
		}
	}

	$dir = "./forumdata/cache/";
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}		
	if(@$fp = fopen("$dir$prefix$script.php", 'w')) {
		fwrite($fp, "<?php".PHP_NEXTLINE."//Discuz! cache file, DO NOT modify me!".
			PHP_NEXTLINE."//Created on ".date("M j, Y, G:i").PHP_NEXTLINE.PHP_NEXTLINE."$cachedata".PHP_CLOSE_TAG);
		fclose($fp);
	} else {
		dexit('Can not write to cache file, please check directory ./forumdata/ and ./forumdata/cache/ .');
	}
}

function getcachearray($cachename) {
	global $db, $announcements_num;

	$cols = '*';
	$conditions = '';
	switch($cachename) {
		case settings:
			$table = $GLOBALS['table_settings'];
			break;
		case plugins:
			$table = $GLOBALS['table_plugins'];
			$cols = 'plug_title, plug_key, plug_stats, plug_author, plug_version';
			break;
		case plugins_settings:
			$table = $GLOBALS['table_plugins_settings'];
			$cols = 'conf_key, conf_group, conf_value';
			$conditions ='WHERE conf_cached ORDER BY conf_group';
			break;
		case usergroups:
			$table = $GLOBALS['table_usergroups'];
			$cols = "groupid,grouptitle,color,stars,groupavatar,allowavatar,allowsigbbcode,allowsigimgcode";
			$conditions = "ORDER BY creditslower ASC";
			break;
		case announcements:
			$table = $GLOBALS['table_announcements'];
			$cols = " id, subject, posturl, starttime, endtime";
			$conditions = "ORDER BY displayorder ASC, starttime DESC";
			break;
		case announcements_forum:
			$table = $GLOBALS['table_announcements'];
			$cols = " id, author, posturl, subject,starttime";
			$ann_num = $announcements_num ? $announcements_num : 1 ;
			$conditions = "ORDER BY displayorder ASC, starttime DESC LIMIT 0, $ann_num";
			break;
		case forums:
			$table = $GLOBALS['table_forums'];
			$cols = "fid, type, name, fup, viewperm";
			$conditions = "WHERE status='1' ORDER BY displayorder";
			break;
		case forumlinks:
			$table = $GLOBALS['table_forumlinks'];
			$conditions = "ORDER BY displayorder";
			break;
		case smilies:
			$table = $GLOBALS['table_smilies'];
			$cols = "code,url";
			$conditions = "WHERE type='smiley' ORDER BY LENGTH(code) DESC";
			break;
		case icons:
			$table = $GLOBALS['table_smilies'];
			$cols = "id,url";
			$conditions = "WHERE type='icon'";
			break;
		case ipbanned:
			$table = $GLOBALS['table_banned'];
			$cols = "ip1,ip2,ip3,ip4,expiration ";
			$conditions = "WHERE expiration>".time().' ORDER BY `expiration` DESC';
			break;
		case censor:
			$table = $GLOBALS['table_words'];
			break;
		case onlinelist:
			$table = $GLOBALS['table_onlinelist'];
			$cols = "groupid,url,title";
			$conditions = "WHERE 1 ORDER BY displayorder";
			break;
		case bbcodes:
			$table = $GLOBALS['table_bbcodes'];
			$cols = "tag,replacement,params,nest";
			$conditions = "WHERE available=1";
			break;
		case ranks:
			$table = $GLOBALS['table_ranks'];
			$cols='ranktitle,postshigher,stars,color';
			$conditions = "WHERE 1 ORDER BY postshigher desc";
			break;
		break;

	}
	if (!$table) return;
	$data = array();
	$query = $db->query("SELECT $cols FROM $table $conditions");
	switch($cachename) {
		case 'announcements_forum':
			if ($announcements_num){
				while($announcement = $db->fetch_array($query)) {
					$data[] = $announcement;
				}
			}else{
				$data = array();
			}
			break;

		case 'settings':
			$nocache_settings='welcomemsg,bbrulestxt,welcomemsgtxt,bbrules,censoruser,maxonlines';
			while($settings=$db->fetch_array($query)) {
				$data["$settings[variable]"]=$settings['value'];
			}
			array_del($data,$nocache_settings);
			$query = $db->query("SELECT COUNT(*) FROM $GLOBALS[table_members]");
			$data['totalmembers'] = $db->result($query, 0);
			$query = $db->query("SELECT username FROM $GLOBALS[table_members] ORDER BY regdate DESC LIMIT 1");
			$data['lastmember'] = $db->result($query, 0);

			$data["pluglink"] ='';
			$data["plugins_table"] = array();
			$data["plugins_script"] = array();
			$query = $db->query("SELECT plug_title,plug_key,plug_stats,plug_tables  FROM $GLOBALS[table_plugins]");
			while($plugs = $db->fetch_array($query)) {
				if ($plugs[plug_stats] == 2){
					$data["pluglink"] .= '| <a href="plugins.php?p='.$plugs[plug_key].'"><font class="navtd">'.$plugs['plug_title'].'</font></a> ';
					$data["plugins_script"][] = $plugs['plug_key'];
				}
				if ($plugs[plug_tables]){
					$tb = array();
					$tb = explode(",", $plugs['plug_tables']);
					if (is_array($tb) && count($tb) ){
						foreach($tb as $t_tables){
							if (trim($t_tables)){
								$data["plugins_table"][] = $t_tables;
							}
						}

					}
					unset($tb,$t_tables);
				}
			}
			unset($settings,$query,$plugs);
			break;

		case 'censor':
			$data['find'] = $data['replace'] = array();
			while($censor = $db->fetch_array($query)) {
				$censor['find'] = "/".str_replace("/", "\/",preg_quote( $censor['find']))."/i";
				$censor['find'] = preg_replace("/\\\\\{(\d+)\\\\\}/is", ".{0,\\1}", $censor['find']);

				$data['find'][]  = $censor['find'];
				$data['replace'][] = addslashes($censor['replacement']);
			}
			break;

		case 'bbcodes':
			$data['replacearray'] = $data['searcharray']= array();
			while($bbcodes = $db->fetch_array($query)) {
				$data['searcharray'][] = "/\\[".str_replace("/", "\/",preg_quote( $bbcodes['tag']))."](.+?)\\[\\/".str_replace("/", "\/",preg_quote( $bbcodes['tag'])).'\\]/is';
				$bbcodes['replacement'] = preg_replace("/[\n\r]/s", '', $bbcodes['replacement']);

				$data['replacearray'][] = str_replace('{','\\',str_replace('}','',$bbcodes['replacement']));
			}
			break;

		case 'usergroups':
			while($usergroups = $db->fetch_array($query)) {
				if ($usergroups[color]){
					$usergroups['grouptitle'] = '<font color="'.$usergroups['color'].'">'.$usergroups['grouptitle'].'</font>';
				}

				$data["$usergroups[groupid]"]=array(
					'grouptitle' =>$usergroups[grouptitle],
					'groupcolor' =>$usergroups[color],
					'stars' =>$usergroups[stars],
					'groupavatar' =>$usergroups[groupavatar],
					'allowavatar' =>$usergroups[allowavatar],
					'allowsigbbcode' =>$usergroups[allowsigbbcode],
					'allowsigimgcode' =>$usergroups[allowsigimgcode],
				);
			}
			break;

		case 'forums':
			while($forum = $db->fetch_array($query)) {
				$forum['name'] = strip_tags($forum['name']);
				$data[$forum['fid']] = $forum;
				unset($forum['fid']);
			}
			break;

		case 'icons':
			$data = array();
			while($icon = $db->fetch_array($query)) {
				$data["$icon[id]"] = $icon[url];
			}
			break;

		case 'ranks':
			$data = array();
			while($rank = $db->fetch_array($query)) {
				if ($rank['color']){
					$rank['ranktitle'] = '<font color="'.$rank['color'].'">'.$rank['ranktitle'].'</font>';
				}
				$data[] = array('ranktitle' => $rank['ranktitle'],
								'postshigher' => intval($rank['postshigher']),
								'stars' => intval($rank['stars']));
			}
			break;

		case 'plugins':
			$data = array();
			while($plugin = $db->fetch_array($query)) {
				$data["$plugin[plug_key]"] = $plugin;
			}
			break;

		case 'plugins_settings':
			$data = array();
			while($settings = $db->fetch_array($query)) {
					$data["$settings[conf_group]"]["$settings[conf_key]"] = $settings[conf_value];
			}

		case 'ipbanned':
			$mark='';
			while($ipband = $db->fetch_array($query)) {
				$data['expiration'] = $ipband['expiration'];
				$ip = $ipband['ip1']>0?$ipband['ip1']:'\\d+';
				$ip .='\\.'.($ipband['ip2']>0?$ipband['ip2']:'\\d+');
				$ip .='\\.'.($ipband['ip3']>0?$ipband['ip3']:'\\d+');
				$ip .='\\.'.($ipband['ip4']>0?$ipband['ip4']:'\\d+');
				$data['regexp'] .= "$mark$ip";
				$mark='|';
			}
			break;

		case 'forumlinks':
			$tightlink_text = $tightlink_logo = '';
			while($flink = $db->fetch_array($query)) {
				if($flink['note']) {
					$forumlink['content'] = "<a href=\"$flink[url]\" target=\"_blank\"><span class=\"bold\">$flink[name]</span></a><br>$flink[note]";
					if($flink['logo']) {
						$forumlink['type'] = 1;
						$forumlink['logo'] = $flink['logo'];
					} else {
						$forumlink['type'] = 2;
					}
					$data[] = $forumlink;
				} else {
					if($flink['logo']) {
						$tightlink_logo .= " &nbsp; <a href=\"$flink[url]\" target=\"_blank\"><img src=\"$flink[logo]\" border=\"0\" alt=\"$flink[name]\"></a>";
					} else {
						$tightlink_text .= "<a href=\"$flink[url]\" target=\"_blank\">[$flink[name]]</a> ";
					}
				}
			}
			if($tightlink_logo || $tightlink_text) {
				$tightlink_logo .= $tightlink_text ? '<br>' : '';
				$data[] = array('type' => 3, 'content' => $tightlink_logo.$tightlink_text);
			}
			break;

			case onlinelist:
			$data['legend']=$mark='';
			while($onlinelist = $db->fetch_array($query)) {
				$data['legend'] .=$mark.'<img src="images/common/'.$onlinelist[url].'" border=0>&nbsp;'.$onlinelist[title];
				$data["$onlinelist[groupid]"] = $onlinelist[url];
				$mark='&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			break;


		default:
			while($datarow = $db->fetch_array($query)) {
				$data[] = $datarow;
			}
	}
	return "\$_DCACHE['$cachename'] = ".var_export( $data, TRUE).';'.PHP_NEXTLINE.PHP_NEXTLINE;
}

function getcachevars($data, $type = 'VAR') {
	if (is_array($data) && count($data)) {
		foreach($data as $key => $val) {
			$val = str_replace("'", "\\'", $val);
			$evaluate .= $type == 'VAR' ? "\$$key = '$val';".PHP_NEXTLINE : "define('".strtoupper($key)."', '$val');".PHP_NEXTLINE;
		}
	} else {
        $evaluate = PHP_NEXTLINE;
    }
	return $evaluate;
}

if (!function_exists('var_export')){

	function var_export($array, $return = FALSE ) {
		if ($return) {
			return arrayeval($array);
		}else{
			print_r($array);
		}
	}

	function arrayeval($array, $level = 0) {
		for($i = 0; $i <= $level; $i++) {
			$space .= "\t";
		}
		$evaluate = "Array\n$space(\n";
		$comma = "$space";
		foreach($array as $key => $val) {
			$key = is_string($key) ? "'".addcslashes($key, '\'\\')."'" : $key;
			$val = is_string($val) ? "'".addcslashes($val, '\'\\')."'" : $val;
			if(is_array($val)) {
				$evaluate .= "$comma$key => ".arrayeval($val, $level + 1);
			} else {
				$evaluate .= "$comma$key => $val";
			}
			$comma = ",\n$space";
		}
		$evaluate .= "\n$space)";
		return $evaluate;
	}
}


?>