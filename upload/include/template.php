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
	$template = preg_replace("/\{lang\s+(.+?)\}/ies", "languagevar('\\1')", $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?".">", $template);

	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/s", "<?=\\1".PHP_CLOSE_TAG, $template);

	$template = preg_replace("/$var_regexp/es", "addquote('<?=\\1?'.'>')", $template);
	$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "addquote('<?=\\1?>')", $template); 
	

	$template = preg_replace("/\s*\{template\s+(.+?)\}\s*/is", "\n<?include template('\\1'); ".PHP_CLOSE_TAG."\n", $template);
	$template = preg_replace("/\s*\{eval\s+(.+?)\}\s*/ies", "stripvtags('\n<? \\1  '.PHP_CLOSE_TAG.'\n','')", $template);
	$template = preg_replace("/\s*\{echo\s+(.+?)\}\s*/ies", "stripvtags('\n<? echo \\1; '.PHP_CLOSE_TAG.'\n','')", $template);
	$template = preg_replace("/\s*\{elseif\s+(.+?)\}\s*/ies", "stripvtags('\n<? } elseif(\\1) { '.PHP_CLOSE_TAG.'\n','')", $template);
	$template = preg_replace("/\s*\{else\}\s*/is", "\n<? } else { ".PHP_CLOSE_TAG."\n", $template);

	for($i = 0; $i < $nest; $i++) {
		$template = preg_replace("/\s*\{loop\s+(\S+)\s+(\S+)\}\s*(.+?)\s*\{\/loop\}\s*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2) { '.PHP_CLOSE_TAG,'\n\\3\n<? } } '.PHP_CLOSE_TAG.'\n')", $template);
		$template = preg_replace("/\s*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}\s*(.+?)\s*\{\/loop\}\s*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { '.PHP_CLOSE_TAG,'\n\\4\n<? } } '.PHP_CLOSE_TAG.'\n')", $template);
		$template = preg_replace("/\s*\{if\s+(.+?)\}\s*(.+?)\s*\{\/if\}\s*/ies", "stripvtags('\n<? if(\\1) { '.PHP_CLOSE_TAG,'\n\\2\n<? } '.PHP_CLOSE_TAG.'\n')", $template);
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