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
1 缺少括号
2 缓存过期时间
3 控制显示带密码和有权限设置的板块的主题
4 增加最大允许显示数量
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);


$enabled = 0;	// 是否启用 what's new 功能, 1=是, 0=否

$num = 10;	// 默认显示新贴数量

$num_max = 50;	// 最大允许显示新贴数量

$length = 50;	// 标题最大字节数

$pre = '<li>';	// 标题显示前缀

$cache_expire = 1800;	// 缓存过期时间(秒)

$filter_hide = 1;	//是否过滤掉隐藏板块的主题
$filter_password = 1;	//是否过滤掉含有密码的板块的主题
$filter_viewperm = 1;	//是否过滤掉含有权限设置的板块的主题


$enabled ? define('IN_DISCUZ', TRUE) : exit("document.write(\"Access denied. If you'd like to display new threads in html pages,<br>please switch \$enabled to \\\"1\\\" in whatsnew.php and try again.\");");

$timestamp = time();
$PHP_SELF = ($_SERVER['PHP_SELF'] ?? '') ? ($_SERVER['PHP_SELF'] ?? '') : ($_SERVER['SCRIPT_NAME'] ?? '');
$boardurl = 'http://'.($_SERVER['HTTP_HOST'] ?? '').substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));

$fidin = addslashes(trim(($_GET['fidin'] ?? '')));
$fidout = addslashes(trim(($_GET['fidout'] ?? '')));
$num_get = ($_GET['num'] ?? '') ? intval(($_GET['num'] ?? '')) : 0;
$num = $num_get ? ($num_get > $num_max ? $num_max : $num_get) : $num;

if($timestamp % 3 == 1 && @$dir = dir('./forumdata/')) {
	while($entry = $dir->read()) {
		if(preg_match("/whatsnew_[0-9a-f]{32}\.php/", $entry) && ($timestamp-filemtime("./forumdata/".$entry)) > $cache_expire) {
			@unlink('./forumdata/'.$entry);
		}
	}
	$dir->close();
}

$cachefile = './forumdata/whatsnew_'.md5("$fidin\t$fidout\t$num").'.php';

if(@!include($cachefile)) {

	require './config.php';
	require './include/db_'.$database.'.php';

	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

	if(@!$fp = fopen($cachefile, 'wb')) {
		exit("document.write(\"Unable to write to whatsnew's cache file!<br>Please chmod ./forumdata to 777, delete all<br>\\\"whatsnew_xxx.php\\\" files and try again.\");");
	}

	fwrite($fp, "<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>\n");

	$pre = addslashes($pre);
	$fidin = $fidin ? ' AND t.fid IN (\''.str_replace('_', '\',\'', $fidin).'\')' : '';
	$fidout = $fidout ? ' AND t.fid NOT IN (\''.str_replace('_', '\',\'', $fidout).'\')' : '';

	$forumfilter = $fidin || $fidout ? $fidin.$fidout : '';
	$sql_1 = $filter_hide ? "AND f.status='1'" : '';
	$sql_2 = $filter_password ? "AND f.password=''" : '';
	$sql_3 = $filter_viewperm ? "AND f.viewperm=''" : '';

	$query = $db->query("SELECT t.subject, t.tid, t.closed, t.lastpost FROM {$tablepre}threads t, {$tablepre}forums f WHERE t.fid=f.fid $sql_1 $sql_2 $sql_3 $forumfilter ORDER BY t.lastpost DESC LIMIT $num");
	while($threads = $db->fetch_array($query)) {
		if($thread['closed'] > 2) {
			$thread['tid'] = $thread['closed'];
		}
		$threads['subject'] = cutstr($threads['subject'], $length);
		$row = "document.write(\"<a href=$boardurl/viewthread.php?tid={$threads['tid']} target=_blank>$pre{$threads['subject']}</a><br>\");\n";
		fwrite($fp, $row);
		echo $row;
	}

	fclose($fp);

}

function cutstr($string, $length) {

	if(strlen($string) > $length) {
		for($i = 0; $i < $length - 3; $i++) {
			if(ord($string[$i]) > 127) {
				$wordscut .= $string[$i].$string[$i + 1];
				$i++;
			} else {
				$wordscut .= $string[$i];
			}
		}
		return $wordscut.' ...';
	}
	return $string;

}

?>