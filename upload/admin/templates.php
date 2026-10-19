<?php
/**
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2001-2005 Comsenz Technology Ltd (www.discuz.com)                ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (info@discuz.net) Cnteacher (cnteacher@discuz.com) ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
**/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

cpheader();

if($action == 'templates') {

	if(!$edit) {

		if(!submitcheck('tplsubmit')) {

			$templates = '';
			$query = $db->query("SELECT * FROM $table_templates");
			while($tpl = $db->fetch_array($query)) {
				$templates .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"{$tpl['templateid']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"8\" name=\"namenew[{$tpl['templateid']}]\" value=\"{$tpl['name']}\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"6\" name=\"charsetnew[{$tpl['templateid']}]\" value=\"{$tpl['charset']}\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"20\" name=\"directorynew[{$tpl['templateid']}]\" value=\"{$tpl['directory']}\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\">{$tpl['copyright']}</td>\n".
					"<td bgcolor=\"".ALTBG2."\"><a href=\"admincp.php?action=templates&edit={$tpl['templateid']}\">[{$lang['detail']}]</a></td></tr>\n";
			}
			include CP_TPL.'templates_list.php';

		} else {

			if($newname) {
				if(!$newcharset || !$newdirectory) {
					cpmsg('templates_new_charset_directory_invalid');
				} elseif(!dir_valid($newdirectory)) {
					$directory = $newdirectory;
					cpmsg('templates_directory_invalid');
				}
				$db->query("INSERT INTO $table_templates (name, charset, directory, copyright)
					VALUES ('$newname', '$newcharset', '$newdirectory', '$newcopyright')", 'UNBUFFERED');
			}

			foreach($directorynew as $id => $directory) {
				if(!$delete || ($delete && !in_array($id, $delete))) {
					if(!dir_valid($directory)) {
						cpmsg('templates_directory_invalid');
					} elseif($id == 1 && $directory != './templates/default') {
						cpmsg('templates_default_directory_invalid');
					}
					$db->query("UPDATE $table_templates SET name='$namenew[$id]', charset='$charsetnew[$id]', directory='$directorynew[$id]' WHERE templateid='$id'", 'UNBUFFERED');
				}
			}

			if($ids = implode_ids( $delete )) {
				if(in_array('1', $delete)) {
					cpmsg('templates_delete_invalid');
				}
				$db->query("DELETE FROM $table_templates WHERE templateid IN ($ids) AND templateid<>'1'", 'UNBUFFERED');
				$db->query("UPDATE $table_styles SET templateid='1' WHERE templateid IN ($ids)", 'UNBUFFERED');
			}

			updatecache('styles');
			cpmsg('templates_update_succeed', 'admincp.php?action=templates');

		}

	} else {


		$query = $db->query("SELECT * FROM $table_templates WHERE templateid='$edit'");
		if(!$template = $db->fetch_array($query)) {
			cpmsg('undefined_action');
		} elseif(!dir_valid($template['directory'])) {
			$directory = $template['directory'];
			cpmsg('templates_directory_invalid');
		}

		$warning = $template['templateid'] == 1 ?
				$lang['templates_edit_default_comment'] :
				$lang['templates_edit_nondefault_comment'];
		if($keyword) {
			$keywordadd = " - {$lang['templates_keyword']} <i>".dhtmlspecialchars(stripslashes($keyword))."</i> - <a href=\"admincp.php?action=templates&edit=$edit\" style=\"color: ".HEADERTEXT."\">[ {$lang['templates_view_all']} ]</a>";
			$keywordenc = rawurlencode($keyword);
		}

		$tpldir = dir(DISCUZ_ROOT.'./'.$template['directory']);
		$tplarray = $langarray = array();
		while($entry = $tpldir->read()) {
			$extension = strtolower(fileext($entry));
			if($extension == 'htm') {
				$tplname = substr($entry, 0, -4);
				$pos = strpos($tplname, '_');
				if($keyword) {
					if(!stristr(implode("\n", file(DISCUZ_ROOT."./{$template['directory']}/$entry")), $keyword)) {
						continue;
					}
				}
				if(!$pos) {
					$tplarray[$tplname][] = $tplname;
				} else {
					$tplarray[substr($tplname, 0, $pos)][] = $tplname;
				}
			} elseif($extension == 'php') {
				$langarray[] = substr($entry, 0, -9);
			}
		}
		$tpldir->close();

		ksort($tplarray);
		ksort($langarray);
		$templates = $languages = '';

		foreach($tplarray as $tpl => $subtpls) {
			$templates .= "<ul><li><b>$tpl</b><ul>\n";
			foreach($subtpls as $subtpl) {
				$filename = "$subtpl.htm";
				$templates .= "<li>$subtpl &nbsp; <a href=\"admincp.php?action=tpledit&templateid={$template['templateid']}&fn=$filename&keyword=$keywordenc\">[{$lang['edit']}]</a> ".
					"<a href=\"admincp.php?action=tpledit&templateid={$template['templateid']}&fn=$filename&delete=yes\">[{$lang['delete']}]</a>";
			}
			$templates .= "</ul></ul>\n";
		}
		foreach($langarray as $langpack) {
			$languages .= "<ul><li>$langpack &nbsp; <a href=\"admincp.php?action=tpledit&templateid={$template['templateid']}&fn=$langpack.lang.php\">[{$lang['edit']}]</a></ul>\n";
		}
		include CP_TPL.'templates_tpllist.php';
	}
			
} elseif($action == 'tpledit') {

	$query = $db->query("SELECT * FROM $table_templates WHERE templateid='$templateid'");
	if(!$template = $db->fetch_array($query)) {
		cpmsg('templates_edit_nonexistence');
	}
	
	if (strstr($fn, '/') || strstr($fn, '..')) {
		cpmsg('templates_edit_nonexistence');
	}
	$filename = DISCUZ_ROOT."./{$template['directory']}/$fn";
	if(!is_writeable($filename)) {
		cpmsg('templates_edit_invalid');
	}

	if(!submitcheck('editsubmit') && $delete != 'yes') {

		$keywordenc = rawurlencode($keyword);

		$fp = fopen($filename, 'r');
		$content = fread($fp, filesize($filename));
		fclose($fp);

		$content = str_replace("\\'", "'", dhtmlspecialchars($content));
		
		include CP_TPL.'templates_tpledit.php';

	} elseif($delete == 'yes') {

		if(!$confirmed) {
			cpmsg('templates_delete_confirm', "admincp.php?action=tpledit&templateid=$templateid&fn=$fn&delete=yes", 'form');
		} else {
			if(@unlink($filename)) {
				cpmsg('templates_delete_succeed', "admincp.php?action=templates&edit=$templateid");
			} else {
				cpmsg('templates_delete_fail');
			}
		}

	} else {

		$fp = fopen($filename, 'w');
		flock($fp, 2);
		fwrite($fp, stripslashes(str_replace("\x0d\x0a", "\x0a", $templatenew)));
		fclose($fp);

		cpmsg('templates_edit_succeed', "admincp.php?action=templates&edit=$templateid&keyword=$keyword");

	}

} elseif($action == 'tpladd') {

	$query = $db->query("SELECT * FROM $table_templates WHERE templateid='$edit'");
	if(!$template = $db->fetch_array($query)) {
		cpmsg('templates_add_invalid');
	} elseif(!dir_valid($template['directory'])) {
		$directory = $template['directory'];
		cpmsg('templates_directory_invalid');
	} elseif(file_exists(DISCUZ_ROOT."./{$template['directory']}/$name.htm")) {
		cpmsg('templates_add_duplicate');
	} elseif(!@$fp = fopen(DISCUZ_ROOT."./{$template['directory']}/$name.htm", 'w')) {
		cpmsg('templates_add_file_invalid');
	}

	@fclose($fp);
	cpmsg('templates_add_succeed', "admincp.php?action=tpledit&templateid=1&fn=$name.htm");
	
}	

?>