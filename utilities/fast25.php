<?php
/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2002 Crossday Studio (www.discuz.com)                            ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (info@discuz.net) Cnteacher ( Cnteacher@126.com)   ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

// Upgrade Discuz! Board from 2.2 to 2.5
$totalstep = 14;
showheader();
set_time_limit(1000);
define('IN_DISCUZ', TRUE);

require "./config.php";
require "./include/db_mysql.php";

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_magic_quotes_runtime(0);

$action = ($_POST[action]) ? $_POST[action] : $_GET[action];
$step = $_GET[step];
$start = $_GET[start];

if(!$action) {
	echo"本程序用于升级 Discuz! 2.2F 论坛帖子到 Discuz! 2.5 ,请确认之前已经顺利完成了论坛的升级。<hr size=1 color=red><br>";
	echo"<b><font color=\"red\">本升级程序仅仅用于 2.2F （含2.2F SP1）升级到 2.5F。如果您不使用本程序，您照样可以正常的浏览和使用论坛。本程序的主要作用是提升论坛浏览帖子时候的效率。用户阅读帖子的资源消耗会减少10%~30%;<br><br>如果您的论坛安装有其他插件，或者使用了非标准版程序。本程序无法保障您可以升级成功。<br><br></font></b>";
	echo"<b><font color=\"red\">升级前请打开浏览器 JavaScript 支持,整个过程是自动完成的,不需人工点击和干预.<br>升级之前务必备份数据库资料,否则可能产生无法恢复的后果!<br><br>本次升级需要耗时很多,并占用大量CPU资源,每10万贴的升级需要5分钟左右,请在服务器空闲时进行.<br>请确保服务器上 PHP 没有在安全模式，或没有脚本运行时间的硬性限制，否则请在本地机器升级后上传数据库。</font></b><br><br>";
	echo"正确的升级方法为:<br>1. 确保您已经正常升级到 Discuz! 2.5。<br>2. 上传本程序到 Discuz! 目录中;<br>3. 运行本程序,直到出现升级完成的提示;<br><br>";
	echo"<br><font color=red>本程序如果中途退出，您可以随时继续。</font><br><br>";
	echo"<hr size=1 color=red><a href=\"$PHP_SELF?action=upgrade&step=1\">如果您已确认完成上面的步骤,请点这里升级</a>";
} else {
	echo "<br><br>第 $step 步: ";

	$tables = array('attachments', 'announcements', 'banned', 'caches', 'favorites', 'forumlinks', 'forums', 'karmalog', 'members', 'memo',
	'news', 'polls', 'posts', 'searchindex', 'sessions', 'settings', 'styles', 'smilies', 'stats', 'subscriptions', 'templates', 'themes',
	'threads', 'pm', 'pms', 'usergroups', 'words', 'buddys', 'stylevars','upgrade','access');
	foreach($tables as $tablename) {
		${"table_".$tablename} = $tablepre.$tablename;
	}
	unset($tablename);

	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	if($step == 1) {
		$many = 500;
		$start = intval($start);
		$end = $start + $many;

		$smilies = array();
		$query = $db->query("SELECT code FROM $table_smilies WHERE type='smiley' AND code<>''");
		while($smiley = $db->fetch_array($query)) {
			$smilies[]= preg_quote($smiley['code'], '/');
		}

		echo "正在进行论坛帖子优化: 转换帖子 $start 到 $end<br><br>";
		$query = $db->query("SELECT pid, message, smileyoff, bbcodeoff FROM $table_posts LIMIT $start, $many");
		if($db->num_rows($query)) {
			while($post = $db->fetch_array($query)) {
				$bbcodeoff = checkbbcodes($post['message'], $post['bbcodeoff']);
				$smileyoff = checksmilies($post['message'], $post['smileyoff']);
				if($bbcodeoff != $post['bbcodeoff'] || $smileyoff != $post['smileyoff']) {
					$db->query("UPDATE $table_posts SET bbcodeoff=$bbcodeoff, smileyoff=$smileyoff WHERE pid=$post[pid]");
				}
			}
			redirect("$PHP_SELF?action=upgrade&step=1&start=$end");
		} else {
			redirect("$PHP_SELF?action=upgrade&step=2");
		}
	}else{
		echo "恭喜您升级成功,请务必删除本程序. 进行以下两步操作后才能最后完成:<br>1. 在系统设置中 重建论坛帖数 2. 更新缓存";
		echo "<br>谢谢您使用本升级程序<br>Cnteacher 2004.11.1 。<br>";
		echo "<br><hr size=1 color=red ><font color=red><br>为了保障您的论坛安全，请立即使用Ftp 程序删除本程式 。<br>";
		exit;
	}
}

function redirect($url) {
	global $step;
	echo"<script>";
	echo"function redirect() {window.location.replace('$url');}\n";
	echo"setTimeout('redirect();', 1000);\n";
	echo"</script>";
	echo"<br><br><a href=\"$url\">如果您的浏览器没有自动跳转，请点击这里进行下一步</a><br><br>";

}

function checkbbcodes($message, $bbcodeoff) {
	return !$bbcodeoff && !preg_match("/\[.+\].*\[\/.+\]/s", $message) ? -1 : $bbcodeoff;
}

function checksmilies($message, $smileyoff) {
	global $smilies;
	return !$smileyoff && !preg_match('/'.implode('|', $smilies).'/', $message) ? -1 : $smileyoff;
}

function showheader() {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<meta name="keywords" content="Discuz!,Board,Crossday Studio,PHP,MySQL,FORUM">
<meta name="generator" content="Discuz! 2.5 with Templates 2.5">
<meta name="description" content="Discuz! Board - Powered by Discuz! Board 2.5">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<meta http-equiv="MSThemeCompatible" content="Yes">
<style type='text/css'>
<style type="text/css">
a:link,a:visited	{ text-decoration: none; color: #003366 }
a:hover			{ text-decoration: underline }
body			{ scrollbar-base-color: #F8F8F8; scrollbar-arrow-color: #698CC3; font-size: 12px; background-color: #9EB6D8 }
table			{ font-family: Tahoma, Verdana; color: #000000; font-size: 12px }
textarea,input,object	{ font-family: Tahoma, Verdana; font-size: 12px;  color: #000000; font-weight: normal; background-color: #F8F8F8 }
select			{ font-family: Arial, Tahoma; font-size: 11px;  color: #000000; font-weight: normal; background-color: #F8F8F8 }
.nav			{ font-family: Tahoma, Verdana; font-size: 12px; font-weight: bold }
.header			{ font-family: Tahoma, Verdana; font-size: 11px; color: #FFFFFF; font-weight: bold; background-color: #698CC3 }
.category		{ font-family: Arial, Tahoma; font-size: 11px; color: #000000; background-color: #EFEFEF }
.multi			{ font-family: Arial, Tahoma; font-size: 11px; color: #003366; }
.smalltxt		{ font-family: Arial, Tahoma; font-size: 11px }
.mediumtxt		{ font-family: Tahoma, Verdana; font-size: 12px; color: #000000 }
.bold			{ font-weight: bold }</style>
<title>Discuz 2.2F --> 2.5 帖子优化程序</title>
	</head>
<body leftmargin="0" rightmargin="0" topmargin="0">
<br><br>
<table width="98%" cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td width="100%"><H4><font color=white>D2.2F -->D2.5 <B><font size="" color="#FF0000">帖子显示</font></b>优化程序</font></H4>
</td></tr></table>
<table bgcolor="#FFFFFF" width="98%" cellpadding="10" cellspacing="0" border="0" align="center">
<tr>
<td width="100%">
<table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
<tr><td>
<?
}
?>