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
1 mysql 4.1以上版本无法优化数据表的问题
2 fread预分配内存过大的问题
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

include DISCUZ_ROOT.'./include/attachment.php';

cpheader();

if($action == 'export') {

	if(!submitcheck('exportsubmit', 1)) {
		include CP_TPL.'database_export.php';

	} else {

		if(!$filename) {
			cpmsg('database_export_filename_invalid');
		}

		$time = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);
		if($type == 'full') {
			$tables = array('access', 'admingroups', 'attachments', 'attachtypes', 'announcements', 'banned', 'bbcodes', 'failedlogins', 'favorites', 'forumlinks','forums', 'karmalog', 'members', 'onlinelist', 'pms', 'polls', 'posts','ranks', 'searchindex', 'sessions', 'settings', 'smilies', 'stats', 'styles', 'stylevars', 'subscriptions', 'templates', 'threads', 'usergroups', 'words', 'buddys', 'plugins', 'plugins_settings','caches');
		} elseif($type == 'standard') {
			$tables = array('access', 'admingroups', 'attachments', 'attachtypes', 'announcements', 'banned', 'bbcodes', 'forumlinks', 'forums',
					'members', 'onlinelist', 'polls', 'posts',  'ranks', 'settings', 'smilies', 'stats', 'styles', 'stylevars', 'templates', 'threads', 'usergroups', 'words', 'plugins','plugins_settings');
		} elseif($type == 'mini') {
			$tables = array('access', 'admingroups', 'attachtypes', 'announcements', 'banned', 'bbcodes', 'forumlinks', 'forums', 'members', 'onlinelist', 'ranks', 'settings', 'smilies', 'stats', 'styles', 'stylevars', 'templates', 'usergroups', 'words', 'plugins','plugins_settings');
		}

		if (count($plugins_table)){
			$tables = array_merge($plugins_table,$tables);
		}

		$volume = intval($volume) + 1;
		$idstring = '# Identify: '.base64_encode("$timestamp,$version,$type,$method,$volume")."\n";

		if($method == 'multivol') {

			$db->query("SET SQL_QUOTE_SHOW_CREATE = 0");

			$sqldump = '';
			$tableid = $tableid ? $tableid - 1 : 0;
			$startfrom = intval($startfrom);
			for($i = $tableid; $i < count($tables) && strlen($sqldump) < $sizelimit * 1000; $i++) {
				if ($type <>'forum'){
					$sqldump .= sqldumptable($tablepre.$tables[$i], $startfrom, strlen($sqldump));
				}else{
					$sqldump .= sqldumpforum($tablepre.$tables[$i], $startfrom, strlen($sqldump));
				}

				$startfrom = 0;
			}
			$tableid = $i;

			$dumpfile = substr($filename, 0, strrpos($filename, '.'))."-%s".strrchr($filename, '.');

			if(trim($sqldump)) {
				$sqldump = "$idstring".
					"#\n".
					"# Discuz! Multi-Volume Data Dump Vol.$volume\n".
					"# Version: Discuz! $version\n".
					"# Time: $time\n".
					"# Type: $type\n".
					"# Table Prefix: $tablepre\n".
					"#\n".
					"# Discuz! Home: http://www.discuz.com\n".
					"# Please visit our website for newest infomation about Discuz!\n".
					"# --------------------------------------------------------\n\n\n".
					$sqldump;
		
				@$fp = fopen(($method == 'multivol' ? sprintf($dumpfile, $volume) : $filename), 'w');
				@flock($fp, 2);
				if(@!fwrite($fp, $sqldump)) {
					@fclose($fp);
					cpmsg('database_export_file_invalid');
				} else {
					cpmsg('database_export_multivol_redirect', "admincp.php?action=export&type=$type&saveto=server&filename=$filename&method=multivol&sizelimit=$sizelimit&volume=$volume&tableid=$tableid&startfrom=$startrow&exportsubmit=yes");
				}
			} else {
				$volume--;
				$filelist = '<ul>';
				for($i = 1; $i <= $volume; $i++) {
					$filename = sprintf($dumpfile, $i);
					$filelist .= "<li><a href=\"$filename\">$filename\n";
				}
				cpheader();
				cpmsg('database_export_multivol_succeed');
			}
		}
	}

} elseif($action == 'import') {

	 if(!submitcheck('importsubmit', 1) && !submitcheck('deletesubmit')) {
	 	$exportlog = array();
	 	if(is_dir(DISCUZ_ROOT.'./forumdata')) {
	 		$dir = dir(DISCUZ_ROOT.'./forumdata');
			while($entry = $dir->read()) {
				$entry = "./forumdata/$entry";
				if(is_file($entry) && strtolower(strrchr($entry, '.')) == '.sql') {
					$filesize = filesize($entry);
					$fp = fopen($entry, 'r');
					$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
					fclose ($fp);
 
					$exportlog[$identify[0]] = array(	'version' => $identify[1],
										'type' => $identify[2],
										'method' => $identify[3],
										'volume' => $identify[4],
										'filename' => $entry,
										'size' => $filesize);
				}
			}
			$dir->close();
		} else {
			cpmsg('database_export_dest_invalid');
		}
		krsort($exportlog);
		reset($exportlog);

		$exportinfo = '';
		foreach($exportlog as $dateline => $info) {
			$info[dateline] = is_int($dateline) ? gmdate("$dateformat $timeformat", $dateline + $timeoffset * 3600) : $lang['unknown'];
			switch($info['type']) {
				case full: $info['type'] = $lang['database_export_full']; break;
				case standard: $info['type'] = $lang['database_export_standard']; break;
				case mini: $info['type'] = $lang['database_export_mini']; break;
			}
			$info['size'] = sizecount($info['size']);
			$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
			$info['method'] = $info['method'] == 'multivol' ? $lang['database_multivol'] : $lang['database_shell'];
			$exportinfo .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"$info[filename]\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><a href=\"$info[filename]\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[version]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[dateline]</td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[size]</td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[method]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[volume]</td>\n".
				"<td bgcolor=\"".ALTBG1."\"><a href=\"admincp.php?action=import&from=server&datafile_server=$info[filename]&importsubmit=yes\"".
				($info['version'] != $version ? " onclick=\"return confirm('$lang[database_import_confirm]');\"" : '').">[$lang[import]]</a></td>\n";
		}
		include CP_TPL.'database_import.php';

	 } elseif(submitcheck('importsubmit', 1)) {

		$readerror = 0;
		if($from == 'server') {
			$datafile = addslashes(dirname(dirname(__FILE__))).'/'.$datafile_server;
		}

		if(@$fp = fopen($datafile, 'r')) {
			$sqldump = fgets($fp, 256);
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
			$dumpinfo = array('method' => $identify[3], 'volume' => intval($identify[4]));
			if($dumpinfo['method'] == 'multivol') {
				$sqldump .= fread($fp, 6000000);
			}
			fclose($fp);
		} else {
			if($autoimport) {
				updatecache();
				cpmsg('database_import_multivol_succeed');
			} else {
				cpmsg('database_import_file_illegal');
			}
		}

		if($dumpinfo['method'] == 'multivol') {
			$sqlquery = splitsql($sqldump);
			unset($sqldump);
			foreach($sqlquery as $sql) {
				if(trim($sql) != '') {
					$db->query($sql);
				}
			}

			$datafile_next = str_replace("-$dumpinfo[volume].sql", '-'.($dumpinfo['volume'] + 1).'.sql', $datafile_server);

			if($dumpinfo['volume'] == 1) {
				cpmsg('database_import_multivol_prompt',
					"admincp.php?action=import&from=server&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes",
					'form');
			} elseif($autoimport) {
				cpmsg('database_import_multivol_redirect', "admincp.php?action=import&from=server&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes");
			} else {
				updatecache();
				cpmsg('database_import_succeed');
			}
		} elseif($dumpinfo['method'] == 'shell') {
			require './config.php';
			list($dbhost, $dbport) = explode(':', $dbhost);

			$query = $db->query("SHOW VARIABLES LIKE 'basedir'");
			list(, $mysql_base) = $db->fetch_array($query, MYSQL_NUM);

			$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'bin/';
			shell_exec($mysqlbin.'mysql -h"'.$dbhost.($dbport ? (is_numeric($dbport) ? ' -P'.$dbport : ' -S"'.$dbport.'"') : '').
				'" -u"'.$dbuser.'" -p"'.$dbpw.'" "'.$dbname.'" < '.$datafile);

			updatecache();
			cpmsg('database_import_succeed');
		} else {
			cpmsg('database_import_format_illegal');
		}

	} elseif(submitcheck('deletesubmit')) {
		if(is_array($delete)) {
			foreach($delete as $filename) {
				@unlink($filename);
			}
			cpmsg('database_file_delete_succeed');
		} else {
			cpmsg('database_file_delete_invalid');
		}
	}

} elseif($action == 'runquery') {

	if(!submitcheck('sqlsubmit')) {
		include CP_TPL.'database_runquery.php';
	} else {

		$sqlquery = splitsql(str_replace(' cdb_', ' '.$tablepre, $queries));
		foreach($sqlquery as $sql) {
			if(trim($sql) != '') {
				$db->query(stripslashes($sql), 1);
				$sqlerror = $db->error();
				if($sqlerror) {
					break;
				}
			}
		}

		cpmsg($sqlerror ? 'database_run_query_invalid' : 'database_run_query_succeed');
	}	

} elseif($action == 'optimize') {
	include CP_TPL.'database_optimize.php';
	$optimizetable = '';
	$totalsize = 0;
	$query = $db->query("SELECT VERSION() AS VERSION");
	if ($dbversionArray = $db->fetch_array($query)) {
		$dbVersionTmp = explode('.', $dbversionArray['VERSION']);
		$dbVersion[0] = isset($dbVersionTmp[0]) ? intval($dbVersionTmp[0]) : 3;
		$dbVersion[1] = isset($dbVersionTmp[1]) ? intval($dbVersionTmp[1]) : 23;
		$dbVersion[2] = isset($dbVersionTmp[2]) ? intval($dbVersionTmp[2]) : 32;
	} else {
		$dbVersion = array(3,23,32);
	}
	$tableTypeName = ($dbVersion[0] >= 5 || ($dbVersion[0] == 4 && $dbVersion[1] > 0)) ? 'Engine' : 'Type'; 
	if(!submitcheck('optimizesubmit')) {
		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
		while($table = $db->fetch_array($query)) {
			$checked = $table[$tableTypeName] == 'MyISAM' ? 'checked' : 'disabled';
			echo "<tr><td bgcolor=\"".ALTBG1."\" align=\"center\"><input type=\"checkbox\" name=\"optimizetables[]\" value=\"$table[Name]\" $checked></td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Name]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Rows]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_length]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Index_length]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_free]</td></tr>\n";
			$totalsize += $table['Data_length'] + $table['Index_length'];
		}
		echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">$lang[database_optimize_used] ".sizecount($totalsize)."</td></tr></table><tr><td align=\"center\"><br><input type=\"submit\" name=\"optimizesubmit\" value=\"$lang[submit]\"></td></tr>\n";
	} else {
		$db->query("DELETE FROM $table_subscriptions", 'UNBUFFERED');
		$db->query("UPDATE $table_members SET identifying=''", 'UNBUFFERED');

		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
		while($table = $db->fetch_array($query)) {
			if(is_array($optimizetables) && in_array($table['Name'], $optimizetables)) {
				$optimized = $lang['yes'];
				$db->query("OPTIMIZE TABLE $table[Name]");
			} else {
				$optimized = '<b>'.$lang['no'].'</b>';
			}

			echo "<tr>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$optimized</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Name]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Rows]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_length]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Index_length]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">0</td>\n".
				"</tr>\n";
			$totalsize += $table['Data_length'] + $table['Index_length'];
		}
		echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">Total Used: ".sizecount($totalsize)."</td></tr></table>";
	}

	echo '</table></form>';
}

?>