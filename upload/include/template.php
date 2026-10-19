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

function parse_template($file, $templateid, $tpldir) {
	global $language;

	$nest = 5;
	$tplfile = DISCUZ_ROOT."./$tpldir/$file.htm";
	$objfile = DISCUZ_ROOT."./forumdata/templates/{$templateid}_$file.tpl.php";

	if(!@$fp = fopen($tplfile, 'r')) {
		exit("Current template file './$tpldir/$file.htm' not found or have no access!");
	} elseif(!include language('templates', $templateid, $tpldir)) {
		exit("<br>Current template pack do not have a necessary language file 'templates.lang.php' or have syntax error!");
	}

	$plugins_languagepack = DISCUZ_ROOT.'./plugins/plugin.lang.php';
	if(file_exists($plugins_languagepack)) {
		include $plugins_languagepack;
		if (count($plglang)){
			$language = array_merge($language,$plglang);
		}
	}
	unset($plugins_languagepack);

	$template = fread($fp, filesize($tplfile));
	fclose($fp);
	$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9\-\.\[\]_\"\'\$\x7f-\xff]+\])*)"; 
	$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
	$template = preg_replace_callback("/\{lang\s+(.+?)\}/is", "parse_tpl_lang", $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?".">", $template);

	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/s", "<?=\\1".PHP_CLOSE_TAG, $template);

	$template = preg_replace_callback("/$var_regexp/s", "parse_tpl_var", $template);
	$template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", "parse_tpl_var", $template);
	

	$template = preg_replace("/\s*\{template\s+(.+?)\}\s*/is", "\n<?include template('\\1'); ".PHP_CLOSE_TAG."\n", $template);
	$template = preg_replace_callback("/\s*\{eval\s+(.+?)\}\s*/is", "parse_tpl_eval", $template);
	$template = preg_replace_callback("/\s*\{echo\s+(.+?)\}\s*/is", "parse_tpl_echo", $template);
	$template = preg_replace_callback("/\s*\{elseif\s+(.+?)\}\s*/is", "parse_tpl_elseif", $template);
	$template = preg_replace("/\s*\{else\}\s*/is", "\n<? } else { ".PHP_CLOSE_TAG."\n", $template);

	for($i = 0; $i < $nest; $i++) {
		$template = preg_replace_callback("/\s*\{loop\s+(\S+)\s+(\S+)\}\s*(.+?)\s*\{\/loop\}\s*/is", "parse_tpl_loop2", $template);
		$template = preg_replace_callback("/\s*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}\s*(.+?)\s*\{\/loop\}\s*/is", "parse_tpl_loop3", $template);
		$template = preg_replace_callback("/\s*\{if\s+(.+?)\}\s*(.+?)\s*\{\/if\}\s*/is", "parse_tpl_if", $template);
	}
	$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1".PHP_CLOSE_TAG, $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

	$template = preg_replace("/ \?\>[\n\r]*\<\?=/s", ";\n echo ", $template);
	
	$template = "<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ".PHP_CLOSE_TAG."$template";

	if(!@$fp = fopen($objfile, 'w')) {
		exit("Directory './forumdata/templates/' not found or have no access!");
	}

	flock($fp, 3);
	fwrite($fp, $template);
	fclose($fp);
}

// PHP 8 port: the /e modifier was removed in PHP 7. The replacement code of every
// template tag is now produced by the callbacks below, which yield byte identical
// output to the former eval() based compiler.
function parse_tpl_lang($m) {
	return languagevar($m[1]);
}

function parse_tpl_var($m) {
	return addquote('<?='.$m[1].'?>');
}

function parse_tpl_eval($m) {
	return stripvtags("\n<? ".$m[1]."  ".PHP_CLOSE_TAG."\n", '');
}

function parse_tpl_echo($m) {
	return stripvtags("\n<? echo ".$m[1]."; ".PHP_CLOSE_TAG."\n", '');
}

function parse_tpl_elseif($m) {
	return stripvtags("\n<? } elseif(".$m[1].") { ".PHP_CLOSE_TAG."\n", '');
}

function parse_tpl_loop2($m) {
	return stripvtags("\n<? if(is_array(".$m[1].")) { foreach(".$m[1]." as ".$m[2].") { ".PHP_CLOSE_TAG, "\n".$m[3]."\n<? } } ".PHP_CLOSE_TAG."\n");
}

function parse_tpl_loop3($m) {
	return stripvtags("\n<? if(is_array(".$m[1].")) { foreach(".$m[1]." as ".$m[2]." => ".$m[3].") { ".PHP_CLOSE_TAG, "\n".$m[4]."\n<? } } ".PHP_CLOSE_TAG."\n");
}

function parse_tpl_if($m) {
	return stripvtags("\n<? if(".$m[1].") { ".PHP_CLOSE_TAG, "\n".$m[2]."\n<? } ".PHP_CLOSE_TAG."\n");
}

function addquote($var) {
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\]/s", "['\\1']", $var));
}

function languagevar($var) {
	if(isset($GLOBALS['language'][$var])) {
		return $GLOBALS['language'][$var];
	} else {
		return "!$var!";
	}
}

function stripvtags($expr, $statement) {
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\[\]\"\'\$\x7f-\xff]*)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

?>