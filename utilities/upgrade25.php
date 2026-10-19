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
//fix:  BY pk0909
$totalstep = 14;
showheader();
set_time_limit(1000);
define('IN_DISCUZ', TRUE);

require "./config.php";
require "./include/db_mysql.php";

error_reporting(E_ERROR | E_WARNING | E_PARSE);


$action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];
$step = $_GET['step'];
$start = $_GET['start'];

$upgrade0 = <<<EOT
DROP TABLE IF EXISTS cdb_upgrade;
CREATE TABLE cdb_upgrade (
  stepid smallint(6) unsigned NOT NULL default '0',
  action tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (stepid)
);
INSERT INTO cdb_upgrade VALUES (1, 1);
EOT;


$upgrade1 = <<<EOT
ALTER TABLE cdb_threads DROP INDEX digest;
UPDATE cdb_settings SET totalmembers=0, maxavatarsize=maxavatarsize*100;
ALTER TABLE cdb_posts ADD INDEX (author);
ALTER TABLE cdb_pm ADD INDEX (msgfrom);
ALTER TABLE cdb_threads ADD highlight tinyint(1) NOT NULL default '0';
ALTer TABLE cdb_threads ADD INDEX (author), DROP INDEX lastpost;
 
ALTER TABLE cdb_threads CHANGE views views MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL, CHANGE fid fid SMALLINT(6) UNSIGNED DEFAULT '0' NOT NULL;

DROP TABLE IF EXISTS cdb_ranks;
CREATE TABLE cdb_ranks (
  rankid smallint(6) unsigned NOT NULL auto_increment,
  ranktitle varchar(30) NOT NULL,
  postshigher smallint(6) unsigned NOT NULL default '0',
  stars tinyint(3) NOT NULL default '0',
  color varchar(7) NOT NULL,
  PRIMARY KEY  (rankid)
);
INSERT INTO cdb_ranks VALUES (1, 'Beginner', 0, 1, '');
INSERT INTO cdb_ranks VALUES (2, 'Poster', 50, 2, '');
INSERT INTO cdb_ranks VALUES (3, 'Cool Poster', 300, 5, '');
INSERT INTO cdb_ranks VALUES (4, 'Writer', 1000, 4, '');
INSERT INTO cdb_ranks VALUES (5, 'Excellent Writer', 3000, 5, '');

EOT;

$upgrade2 = <<<EOT
ALTER TABLE cdb_posts ADD INDEX (authorid);
ALTER TABLE cdb_posts DROP INDEX author;
ALTER TABLE cdb_posts CHANGE aid aid TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE cdb_posts CHANGE subject subject VARCHAR(80) NOT NULL default '';

ALTER TABLE cdb_pm DROP INDEX msgfrom;
ALTER TABLE cdb_pm ADD msgtoid mediumint(8) UNSIGNED NOT NULL AFTER msgfromid;
UPDATE cdb_pm SET msgtoid=msgtoid1;
ALTER TABLE cdb_pm DROP msgtoid1;
ALTER TABLE cdb_pm ADD INDEX (msgtoid);

ALTER TABLE cdb_favorites ADD uid mediumint(8) UNSIGNED NOT NULL FIRST;
UPDATE cdb_favorites SET uid=uid1;
ALTER TABLE cdb_favorites DROP uid1;

DROP TABLE IF EXISTS cdb_poll;

ALTER TABLE cdb_members ADD lastpost int(10) UNSIGNED NOT NULL AFTER lastvisit;
ALTER TABLE cdb_members ADD lastactivity int(10) UNSIGNED NOT NULL AFTER lastvisit, DROP charset;

UPDATE cdb_members SET lastactivity=lastvisit-3600, lastpost=lastvisit;
UPDATE cdb_posts SET author='' WHERE author='Guest';

DROP TABLE IF EXISTS cdb_sessions;
CREATE TABLE cdb_sessions (
  sid char(6) binary NOT NULL default '',
  ip1 tinyint(3) UNSIGNED NOT NULL default '0',
  ip2 tinyint(3) UNSIGNED NOT NULL default '0',
  ip3 tinyint(3) UNSIGNED NOT NULL default '0',
  ip4 tinyint(3) UNSIGNED NOT NULL default '0',
  uid mediumint(8) UNSIGNED NOT NULL default '0',
  username char(15) NOT NULL default '',
  groupid smallint(6) unsigned NOT NULL default '0',
  styleid smallint(6) unsigned NOT NULL default '0',
  invisible tinyint(1) NOT NULL default '0',
  action tinyint(1) unsigned NOT NULL default '0',
  lastactivity int(10) unsigned NOT NULL default '0',
  fid smallint(6) unsigned NOT NULL default '0',
  tid mediumint(8) unsigned NOT NULL default '0',
  UNIQUE sid (sid)
) TYPE=HEAP MAX_ROWS=2000;


DROP TABLE IF EXISTS cdb_adminsessions;
CREATE TABLE cdb_adminsessions (
  uid mediumint(8) NOT NULL default '0',
  ip char(20) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  errorlog tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (uid,ip,dateline)
);

DROP TABLE IF EXISTS cdb_onlinelist;
CREATE TABLE cdb_onlinelist (
  groupid smallint(6) unsigned NOT NULL default '0',
  displayorder tinyint(3) NOT NULL default '0',
  title varchar(30) NOT NULL default '',
  url varchar(30) NOT NULL default ''
);
INSERT INTO cdb_onlinelist VALUES (1, 1, 'Administrator', 'online_admin.gif');
INSERT INTO cdb_onlinelist VALUES (2, 2, 'Super Moderator', 'online_supermod.gif');
INSERT INTO cdb_onlinelist VALUES (3, 3, 'Moderator', 'online_moderator.gif');
INSERT INTO cdb_onlinelist VALUES (0, 4, 'Member', 'online_member.gif');

ALTER TABLE cdb_members ADD groupid smallint(6) UNSIGNED NOT NULL AFTER status;
ALTER TABLE cdb_members ADD adminid smallint(3) NOT NULL AFTER status;
UPDATE cdb_members SET groupid='3', adminid='3' WHERE status='Moderator';
UPDATE cdb_members SET groupid='2', adminid='2' WHERE status='SuperMod';
UPDATE cdb_members SET groupid='1', adminid='1' WHERE status='Admin';
UPDATE cdb_members SET groupid='4', adminid='-1' WHERE status='Banned';
UPDATE cdb_members SET groupid='5', adminid='-1' WHERE status='PostBanned';
UPDATE cdb_members SET groupid='8', adminid='-1' WHERE status='Inactive';

DELETE FROM cdb_searchindex;

CREATE TABLE cdb_admingroups (
  admingid smallint(3) unsigned NOT NULL auto_increment,
  admintitle char(30) NOT NULL default '',
  adminglobal tinyint(1) NOT NULL default '0',
  alloweditpost tinyint(1) NOT NULL default '0',
  alloweditpoll tinyint(1) NOT NULL default '0',
  allowdelpost tinyint(1) NOT NULL default '0',
  allowmassprune tinyint(1) NOT NULL default '0',
  allowcensorword tinyint(1) NOT NULL default '0',
  allowviewip tinyint(1) NOT NULL default '0',
  allowbanip tinyint(1) NOT NULL default '0',
  allowedituser tinyint(1) NOT NULL default '0',
  allowbanuser tinyint(1) NOT NULL default '0',
  allowpostannounce tinyint(1) NOT NULL default '0',
  allowviewlog tinyint(1) NOT NULL default '0',
  allowhighlight tinyint(1) NOT NULL default '0',
  allowdigest tinyint(1) NOT NULL default '0',
  allowclose tinyint(1) NOT NULL default '0',
  allowmove tinyint(1) NOT NULL default '0',
  allowtop tinyint(1) NOT NULL default '0',
  allowmerge tinyint(1) NOT NULL default '0',
  allowsplit tinyint(1) NOT NULL default '0',
  disablepostctrl tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (admingid)
);

INSERT INTO cdb_admingroups VALUES (1, 'Administrator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO cdb_admingroups VALUES (2, 'SuperModerator', 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO cdb_admingroups VALUES (3, 'Moderator', 0, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1);
    

DROP TABLE IF EXISTS cdb_bbcodes;
CREATE TABLE cdb_bbcodes (
  id mediumint(8) UNSIGNED NOT NULL auto_increment,
  available tinyint(1) NOT NULL,
  tag varchar(100) NOT NULL default '',
  replacement text NOT NULL,
  example varchar(255) NOT NULL default '',
  explanation text NOT NULL,
  params tinyint(1) UNSIGNED NOT NULL default '1',
  nest tinyint(3) UNSIGNED NOT NULL default '1',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_access;
CREATE TABLE cdb_access (
  uid mediumint(8) unsigned NOT NULL default '0',
  fid smallint(6) unsigned NOT NULL default '0',
  allowview tinyint(1) NOT NULL default '0',
  allowpost tinyint(1) NOT NULL default '0',
  allowreply tinyint(1) NOT NULL default '0',
  allowgetattach tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (uid, fid)
);

DROP TABLE IF EXISTS cdb_attachtypes;
CREATE TABLE cdb_attachtypes (
  id smallint(6) UNSIGNED NOT NULL auto_increment,
  extension char(10) NOT NULL default '',
  maxsize int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_caches;
CREATE TABLE cdb_caches (
  cid int(10) unsigned NOT NULL auto_increment,
  ckey varchar(80) NOT NULL default '',
  cval text NOT NULL,
  dateline int(10) NOT NULL default '0',
  extr varchar(80) NOT NULL default '',
  PRIMARY KEY  (cid),
  KEY ckey (ckey)
);

DROP TABLE IF EXISTS cdb_plugins;
CREATE TABLE cdb_plugins (
  plug_id smallint(6) NOT NULL auto_increment,
  plug_title varchar(255) NOT NULL default '',
  plug_version varchar(255) NOT NULL default '',
  plug_author varchar(255) NOT NULL default '0',
  plug_key varchar(255) NOT NULL default '',
  plug_stats tinyint(1) NOT NULL default '0',
  plug_cp varchar(255) NOT NULL default '',
  plug_tables varchar(255) NOT NULL default '',
  plug_license text NOT NULL,
  plug_desc text NOT NULL,
  PRIMARY KEY (plug_id),
  KEY plug_keyword (plug_key)
);

DROP TABLE IF EXISTS cdb_plugins_settings;
CREATE TABLE cdb_plugins_settings (
  conf_id int(10) unsigned NOT NULL auto_increment,
  conf_title varchar(255) NOT NULL default '',
  conf_desc text NOT NULL,
  conf_group varchar(255) NOT NULL default '',
  conf_type varchar(255) NOT NULL default '',
  conf_key varchar(255) NOT NULL default '',
  conf_value text NOT NULL,
  conf_extra text NOT NULL,
  conf_position smallint(3) unsigned NOT NULL default '0',
  conf_cached tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (conf_id),
  KEY conf_group (conf_group)
);

DROP TABLE IF EXISTS cdb_failedlogins;
CREATE TABLE cdb_failedlogins (
  ip char(15) NOT NULL,
  count tinyint(1) UNSIGNED NOT NULL,
  lastupdate int(10) UNSIGNED NOT NULL
);

DROP TABLE IF EXISTS cdb_pms;
ALTER TABLE cdb_pm RENAME cdb_pms;
ALTER TABLE cdb_pms ADD INDEX (msgfromid);

UPDATE cdb_threads SET lastposter='' WHERE lastposter='Guest';
UPDATE cdb_settings SET version='2.5';
UPDATE cdb_forums SET viewperm='', postperm='', getattachperm='', postattachperm='';

ALTER TABLE cdb_threads CHANGE topped displayorder tinyint(1) NOT NULL, ADD iconid smallint(6) UNSIGNED NOT NULL AFTER icon, ADD INDEX displayorder (fid, displayorder, lastpost);

ALTER TABLE cdb_banned ADD expiration int(10) UNSIGNED NOT NULL;
EOT;

$upgrade3 = <<<EOT
ALTER TABLE cdb_threads CHANGE closed closed1 VARCHAR(15) NOT NULL;
ALTER TABLE cdb_threads ADD closed mediumint(8) UNSIGNED NOT NULL;

UPDATE cdb_threads SET closed='1' WHERE closed1='1' OR closed1='yes';
UPDATE cdb_threads SET closed=REPLACE(closed1, 'moved|', '') WHERE closed1 LIKE 'moved|%';

ALTER TABLE cdb_threads DROP closed1;

ALTER TABLE cdb_posts CHANGE pid pid1 int(10) UNSIGNED NOT NULL, DROP PRIMARY KEY, ADD pid int(10) UNSIGNED NOT NULL auto_increment FIRST, ADD PRIMARY KEY (pid);
UPDATE cdb_posts SET pid=pid1;
ALTER TABLE cdb_posts DROP pid1, DROP icon;

ALTER TABLE cdb_settings CHANGE totalmembers nocacheheaders tinyint(1) NOT NULL, DROP lastmember, CHANGE welcommsg welcomemsg TINYINT(1) DEFAULT '0' NOT NULL, ADD minpostsize MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL AFTER memberperpage;
ALTER TABLE cdb_settings CHANGE welcommsgtxt welcomemsgtxt TEXT NOT NULL;

ALTER TABLE cdb_subscriptions DROP INDEX tid, ADD PRIMARY KEY (uid, tid), CHANGE email email1 varchar(60) NOT NULL, ADD email varchar(60) NOT NULL AFTER tid;;
UPDATE cdb_subscriptions SET email=email1;
ALTER TABLE cdb_subscriptions DROP email1;
ALTER TABLE cdb_smilies CHANGE type type ENUM('smiley', 'icon') DEFAULT 'smiley' NOT NULL;
UPDATE cdb_smilies SET type='icon' WHERE type<>'smiley';
ALTER TABLE cdb_words ADD admin VARCHAR(15) NOT NULL AFTER id;

DROP TABLE IF EXISTS cdb_searchindex;
CREATE TABLE cdb_searchindex (
  searchid int(10) UNSIGNED NOT NULL auto_increment,
  keywords varchar(255) NOT NULL,
  searchstring varchar(255) NOT NULL,
  useip varchar(15) NOT NULL,
  uid mediumint(10) UNSIGNED NOT NULL,
  dateline int(10) UNSIGNED NOT NULL,
  expiration int(10) UNSIGNED NOT NULL,
  threads smallint(6) UNSIGNED NOT NULL,
  tids text NOT NULL,
  PRIMARY KEY  (searchid)
);

DROP TABLE IF EXISTS cdb_polls;
CREATE TABLE cdb_polls (
  tid mediumint(8) UNSIGNED NOT NULL,
  pollopts MEDIUMTEXT NOT NULL,
  PRIMARY KEY  (tid)
);

DROP TABLE IF EXISTS settmp;
CREATE TABLE settmp (
  variable varchar(32) NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (variable)
);


ALTER TABLE cdb_threads CHANGE author author CHAR(15) NOT NULL, CHANGE subject subject CHAR(80) NOT NULL, CHANGE lastposter lastposter CHAR(15) NOT NULL, CHANGE attachment attachment TINYINT(1) DEFAULT '0' NOT NULL;

ALTER TABLE cdb_members ADD avatarwidth TINYINT( 3 ) UNSIGNED NOT NULL AFTER avatar;
ALTER TABLE cdb_members ADD avatarheight TINYINT( 3 ) UNSIGNED NOT NULL AFTER avatarwidth;
ALTER TABLE cdb_members ADD invisible TINYINT( 1 ) NOT NULL AFTER newsletter;
ALTER TABLE cdb_members ADD lastip varchar(15) NOT NULL AFTER regdate;
ALTER TABLE cdb_members ADD accessmasks tinyint(1) NOT NULL AFTER newpm;
ALTER TABLE cdb_members ADD secques varchar(8) NOT NULL AFTER password;
ALTER TABLE cdb_members CHANGE password password VARCHAR( 32 ) NOT NULL , CHANGE `timeoffset` `timeoffset` CHAR( 4 ) NOT NULL;
ALTER TABLE cdb_members DROP pwdrecover, DROP pwdrcvtime;
ALTER TABLE cdb_members ADD identifying varchar(20) NOT NULL AFTER accessmasks;
ALTER TABLE cdb_members CHANGE location location VARCHAR(30) NOT NULL, CHANGE customstatus customstatus VARCHAR(30) NOT NULL;
ALTER TABLE cdb_members ADD extracredit INT(10) NOT NULL AFTER credit;
ALTER TABLE cdb_members CHANGE credit credit INT(10) NOT NULL;

UPDATE cdb_members SET avatarwidth=80, avatarheight=80;

ALTER TABLE cdb_forums DROP postcredits;
ALTER TABLE cdb_forums ADD postcredits TINYINT( 3 ) DEFAULT '-1' NOT NULL AFTER allowimgcode;
ALTER TABLE cdb_forums ADD replycredits TINYINT( 3 ) DEFAULT '-1' NOT NULL AFTER postcredits;
ALTER TABLE cdb_forums CHANGE icon icon CHAR(50) NOT NULL, CHANGE name name CHAR(255) NOT NULL, CHANGE description description CHAR(255) NOT NULL, CHANGE moderator moderator CHAR(255) NOT NULL, CHANGE lastpost lastpost CHAR(100) NOT NULL, CHANGE password password CHAR(12) NOT NULL, CHANGE viewperm viewperm CHAR(100) NOT NULL, CHANGE postperm postperm CHAR(100) NOT NULL, CHANGE getattachperm replyperm CHAR(100) NOT NULL, CHANGE postattachperm getattachperm CHAR(100) NOT NULL, CHANGE threads threads MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE cdb_forums DROP INDEX status;
ALTER TABLE cdb_forums ADD INDEX forum (status, type, displayorder);

ALTER TABLE cdb_announcements ADD displayorder TINYINT(3) NOT NULL AFTER subject;
ALTER TABLE cdb_announcements ADD posturl varchar(250) NOT NULL default '' AFTER subject;

ALTER TABLE cdb_usergroups ADD type ENUM('system', 'special', 'member') NOT NULL default 'member' AFTER groupid;
ALTER TABLE cdb_usergroups ADD allowinvisible TINYINT(1) NOT NULL AFTER allowkarma;
ALTER TABLE cdb_usergroups ADD color CHAR(7) NOT NULL AFTER stars;

UPDATE cdb_usergroups SET type='system' WHERE status IN ('Admin','SuperMod','Moderator','Banned','PostBanned','IPBanned','Guest','Inactive');
UPDATE cdb_usergroups SET type='member' WHERE status='Member';
UPDATE cdb_usergroups SET type='special' WHERE specifiedusers<>'';

ALTER TABLE cdb_threads DROP INDEX author, ADD INDEX (digest), CHANGE author author CHAR(15) NOT NULL, CHANGE subject subject CHAR(80) NOT NULL, CHANGE lastposter lastposter CHAR(15) NOT NULL;

ALTER TABLE cdb_buddys ADD INDEX (uid);

ALTER TABLE cdb_attachments CHANGE filename filename CHAR(100) NOT NULL, CHANGE filetype filetype CHAR(50) NOT NULL, CHANGE filesize filesize INT(10) UNSIGNED DEFAULT '0' NOT NULL, CHANGE attachment attachment CHAR(100) NOT NULL;
ALTER TABLE cdb_attachments ADD dateline int(10) UNSIGNED NOT NULL default '0' AFTER attachment;
ALTER TABLE cdb_attachments CHANGE downloads downloads smallint(6) unsigned NOT NULL default '0';
ALTER TABLE cdb_attachments ADD uid MEDIUMINT( 8 ) UNSIGNED NOT NULL AFTER pid ;
ALTER TABLE cdb_attachments ADD INDEX (tid);
ALTER TABLE cdb_attachments ADD INDEX (pid);
ALTER TABLE cdb_attachments ADD INDEX (uid);

ALTER TABLE cdb_forums CHANGE lastpost lastpost CHAR(110) NOT NULL;
EOT;

		$upgrade4 = <<<EOT
DELETE FROM cdb_settings WHERE variable IN ('regctrl', 'userstatusby', 'newbiespan', 'attachrefcheck', 'delayeditpost', 'delaykarma', 'attachimgcheck', 'attachsoftdownload', 'attach_max', 'attach_newpost', 'attach_editpost', 'attach_replypost', 'useimagemessage', 'statcacherefresh');
INSERT INTO cdb_settings VALUES ('regctrl', '0');
INSERT INTO cdb_settings VALUES ('userstatusby', 1);
INSERT INTO cdb_settings VALUES ('newbiespan', 0);
INSERT INTO cdb_settings VALUES ('attachrefcheck', '0');
INSERT INTO cdb_settings VALUES ('delayeditpost', '0');
INSERT INTO cdb_settings VALUES ('delaykarma', '0');
INSERT INTO cdb_settings VALUES ('attachimgcheck', '0');
INSERT INTO cdb_settings VALUES ('attachsoftdownload', '1');
INSERT INTO cdb_settings VALUES ('attach_max', '0');
INSERT INTO cdb_settings VALUES ('attach_newpost', '4');
INSERT INTO cdb_settings VALUES ('attach_editpost', '3');
INSERT INTO cdb_settings VALUES ('attach_replypost', '3');
INSERT INTO cdb_settings VALUES ('useimagemessage', '1');
INSERT INTO cdb_settings VALUES ('statcacherefresh', '3600');
INSERT INTO cdb_settings VALUES ('delayreply', 0x30);

UPDATE cdb_members set styleid = '0' where 1 ;

DROP TABLE IF EXISTS cdb_karmalog;
CREATE TABLE cdb_karmalog (
  kid int(10) NOT NULL auto_increment,
  uid mediumint(8) unsigned NOT NULL default '0',
  pid int(10) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  score tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (kid),
  KEY pid (pid),
  KEY dateline (dateline)
);

EOT;

if(!$action) {
	echo"本程序用于升级 Discuz! 2.2F 到 Discuz! 2.5,请确认之前已经顺利安装 Discuz! 2.2<hr size=1 color=red><br>";
	echo"<b><font color=\"red\">本升级程序只能从 2.2F （含2.2F SP1）升级到 2.5F。<br><br>如果您的论坛安装有其他插件，或者使用了非标准版程序。本程序无法保障您可以升级成功。<br><br></font></b>";
	echo"<b><font color=\"red\">升级前请打开浏览器 JavaScript 支持,整个过程是自动完成的,不需人工点击和干预.<br>升级之前务必备份数据库资料,否则可能产生无法恢复的后果!<br><br>本次升级需要耗时很多,并占用大量CPU资源,每10万贴的升级需要5分钟左右,请在服务器空闲时进行.<br>请确保服务器上 PHP 没有在安全模式，或没有脚本运行时间的硬性限制，否则请在本地机器升级后上传数据库。</font></b><br><br>";
	echo"正确的升级方法为:<br>1. 原有论坛的默认论坛风格改成“Default Style”<br>2. 关闭原有论坛,上传 Discuz! 2.5 版的全部文件和目录,覆盖服务器上的 2.2F文件，设定好config.php 当中关于数据库的设置。<br>3. 根据安装说明,新建customavatars目录,forumdata/accesslogs目录,两个目录属性777<br>4. 上传本程序到 Discuz! 目录中;<br>4. 运行本程序,直到出现升级完成的提示;<br><br>";
	echo"<br><font color=red>尽管本程序使用了升级保护机制，但是如果您中途退出，还是有可能会导致升级无法继续。</font><br><br>";
	echo"<hr size=1 color=red><a href=\"$PHP_SELF?action=upgrade&step=1\">如果您已确认完成上面的步骤,请点这里升级</a>";
} else {
	echo "<br><br>第 $step 步: ";

	$tables = array('attachments', 'announcements', 'banned', 'caches', 'favorites', 'forumlinks', 'forums', 'karmalog', 'members', 'memo',
	'news', 'polls', 'posts', 'searchindex', 'sessions', 'settings', 'styles', 'smilies', 'stats', 'subscriptions', 'templates', 'themes',
	'threads', 'pm', 'pms', 'usergroups', 'words', 'buddys', 'stylevars','upgrade','access','plugins','plugins_settings');
	foreach($tables as $tablename) {
		${"table_".$tablename} = $tablepre.$tablename;
	}
	unset($tablename);

	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	
	$query = $db->query("SELECT action FROM $table_upgrade WHERE stepid='$step'", 'SILENT');
	if($db->error()) {
		runquery($upgrade0,'建立临时数据库，用来记录升级步骤！<br><br>');
	}else{
		$step_action = $db->result($query, 0);
		if (!$step_action){
			$query = $db->query("INSERT INTO $table_upgrade VALUES ('$step', '1')", 'SILENT');
		}elseif($step_action >1 ){
			echo "----<font color=red>升级成功</font><br>";
			redirect("$PHP_SELF?action=upgrade&step=".($step + 1));
			exit;
		}
	}

	if($step == 1) {
		runquery($upgrade1,'升级 post,pm,thread 数据表结构');
	} elseif($step == 2) {
		user2id($table_karmalog);
	} elseif($step == 3) {
		user2id($table_buddys);
	} elseif($step == 4) {
		user2id($table_buddys, 'buddyname', 'buddyid');
	} elseif($step == 5) {
		user2id($table_favorites, 'username', 'uid1');
	} elseif($step == 6) {
		user2id($table_pm, 'msgto', 'msgtoid1');
	} elseif($step == 7) {
		user2id($table_pm, 'msgfrom', 'msgfromid', TRUE);
	} elseif($step == 8) {
		user2id($table_subscriptions);
	} elseif($step == 9) {
		user2id($table_threads, 'author', 'authorid', TRUE);
	} elseif($step == 10) {
		user2id($table_posts, 'author', 'authorid', TRUE);
	} elseif($step == 11) {
		runquery($upgrade2);
		$query = $db->query("SELECT DISTINCT attachment FROM $table_threads WHERE attachment<>''");
		while($t = $db->fetch_array($query)) {
			$db->query("UPDATE $table_threads SET attachment='".attachtype($t['attachment'], 'id')."' WHERE attachment='{$t['attachment']}'");
		}
	} elseif($step == 12) {
		runquery($upgrade3);
		$group = array();
		$query = $db->query("SELECT * FROM $table_usergroups");
		while($g = $db->fetch_array($query)) {
			switch($g['status']) {
				case 'Admin': $group[1] = $g; break;
				case 'SuperMod': $group[2] = $g; break;
				case 'Moderator': $group[3] = $g; break;
				case 'PostBanned': $group[4] = $g; break;
				case 'Banned': $group[5] = $g; break;
				case 'IPBanned': $group[6] = $g; break;
				case 'Guest': $group[7] = $g; break;
				case 'Inactive': $group[8] = $g; break;
				case 'Member': $group[] = $g; break;
			}
		}
		$db->query("DELETE FROM $table_usergroups");
		$db->query("ALTER TABLE $table_members DROP status");
		ksort($group);
		foreach($group as $groupid => $array) {
			$sql = "INSERT INTO $table_usergroups VALUES ('$groupid'";
			foreach($array as $key => $value) {
				if($key != 'groupid') {
					$sql .= ",'".addslashes($value)."'";
				}
			}
			$sql .= ')';
			$db->query($sql);
		}
		$query = $db->query("SELECT groupid, specifiedusers FROM $table_usergroups WHERE specifiedusers<>''");
		while($g = $db->fetch_array($query)) {
			$g['specifiedusers'] = "'".str_replace("\t", "','", trim($g['specifiedusers']))."'";
			$db->query("UPDATE $table_members SET groupid='{$g['groupid']}', adminid='-1' WHERE username IN ({$g['specifiedusers']})");
		}
		$db->query("ALTER TABLE $table_usergroups DROP INDEX creditshigher, DROP INDEX creditslower, ADD INDEX creditsrange (creditshigher, creditslower), DROP specifiedusers, DROP status, DROP maxmemonum, DROP ismoderator, DROP issupermod, DROP isadmin, ADD allowhidecode tinyint(1) NOT NULL AFTER allowsetattachperm, CHANGE grouptitle grouptitle CHAR(30) NOT NULL, CHANGE groupavatar groupavatar CHAR(60) NOT NULL, CHANGE attachextensions attachextensions CHAR(255) NOT NULL;");
	} elseif($step == 13) {
		$query = $db->query("ALTER TABLE $table_stats DROP INDEX type, DROP INDEX var, ADD PRIMARY KEY (type, var)", 'SILENT');
		$query = $db->query("SELECT groupid, creditshigher, creditslower FROM $table_usergroups WHERE (creditshigher<>'0' OR creditslower<>'0') AND groupid>'8'");
		while($g = $db->fetch_array($query)) {
			$db->query("UPDATE $table_members SET groupid='{$g['groupid']}' WHERE groupid='0' AND credit>='{$g['creditshigher']}' AND credit<'{$g['creditslower']}'");
		}
		$query = $db->query("SELECT * FROM $table_settings");
		foreach($db->fetch_array($query) as $key => $val) {
			$val = addslashes($val);
			$db->query("INSERT INTO settmp VALUES('$key', '$val')");
		}
		$db->query("DROP TABLE $table_settings");
		$db->query("DELETE FROM settmp WHERE variable IN ('replycredits', 'deletedcredits', 'forumjump', 'maxavatarpixel', 'maxsearchresults', 'delayviewcount', 'maxpolloptions')");
		$db->query("INSERT INTO settmp VALUES ('replycredits', '1')");
		$db->query("INSERT INTO settmp VALUES ('deletedcredits', '1')");
		$db->query("INSERT INTO settmp VALUES ('forumjump', '1')");
		$db->query("INSERT INTO settmp VALUES ('maxavatarpixel', '120')");
		$db->query("INSERT INTO settmp VALUES ('maxsearchresults', '512')");
		$db->query("INSERT INTO settmp VALUES ('delayviewcount', '0')");
		$db->query("INSERT INTO settmp VALUES ('maxpolloptions', '10')");
		$db->query("UPDATE settmp SET value='30' WHERE variable='searchctrl'");
		$db->query("ALTER TABLE settmp RENAME $table_settings, ORDER BY variable");

		$query = $db->query("SELECT tid, pollopts FROM $table_threads WHERE pollopts<>''");
		while($thread = $db->fetch_array($query)) {
			$thread['pollopts'] = addslashes($thread['pollopts']);
			$db->query("INSERT INTO $table_polls (tid, pollopts) VALUES ('{$thread['tid']}', '{$thread['pollopts']}')");
		}
		$db->query("ALTER TABLE $table_threads ADD poll tinyint(1) DEFAULT '0' NOT NULL AFTER pollopts");
		$db->query("UPDATE $table_threads SET poll='1' WHERE pollopts<>''");
		$db->query("ALTER TABLE $table_threads DROP pollopts");
		$db->query("ALTER TABLE $table_banned DROP INDEX ip1, DROP INDEX ip2, DROP INDEX ip3, DROP INDEX ip4", 'SILENT');

		$query = $db->query("SELECT id, url FROM $table_smilies WHERE type='icon'");
		while($icon = $db->fetch_array($query)) {
			$db->query("UPDATE $table_threads SET iconid='{$icon['id']}' WHERE icon='{$icon['url']}'");
		}
		$db->query("ALTER TABLE $table_threads DROP icon");
		$db->query("ALTER TABLE $table_threads CHANGE author author CHAR(15) NOT NULL, CHANGE subject subject CHAR(80) NOT NULL, CHANGE lastposter lastposter CHAR(15) NOT NULL");

		@unlink('./forumdata/cache/cache_settings.php');
		$db->query("ALTER TABLE $table_forums DROP theme", 'SILENT');

	} elseif($step == 14) {


		runquery($upgrade4,'更新论坛系统设置');

		$query = $db->query("SELECT styleid FROM $table_styles");
		while($style = $db->fetch_array($query)) {
			$db->query("INSERT INTO $table_stylevars (styleid, variable, substitute) VALUES ('{$style['styleid']}', 'maintablespace', '8')");
			$db->query("INSERT INTO $table_stylevars (styleid, variable, substitute) VALUES ('{$style['styleid']}', 'maintablewidth', '98%')");
			$db->query("INSERT INTO $table_stylevars (styleid, variable, substitute) VALUES ('{$style['styleid']}', 'maintablecolor', '#FFFFFF')");
		}
		$uids = '0';
		$query = $db->query("SELECT DISTINCT uid FROM $table_access");
		while($a = $db->fetch_array($query)) {
			$uids .= ",{$a['uid']}";
		}
		$db->query("UPDATE $table_members SET accessmasks='1' WHERE uid IN ($uids)");
		loginit('karmalog');
		loginit('illegallog');
		loginit('modslog');
		loginit('cplog');
		@unlink('./forumdata/cache/cache_settings.php');

	}
	echo "第 $step 步升级成功<br>";
	$query = $db->query("UPDATE $table_upgrade SET action='9' WHERE stepid='$step'");
	if($step < $totalstep) {
		redirect("$PHP_SELF?action=upgrade&step=".($step + 1));
	}else{
		$query = $db->query("DROP TABLE IF EXISTS $table_upgrade");
		echo "<hr size=1 color=red ><br>全部升级成功！请测试您的论坛是否运行正确。<br>";
		echo "<hr size=1 color=red ><br>登陆论坛后请到管理后台更新cache，并且重新进行论坛统计。<br>";
		echo "<br>谢谢您使用本升级程序<br>Cnteacher 2004.11.1 。<br>";
		echo "<br><hr size=1 color=red ><font color=red><br>为了保障您的论坛安全，请立即使用Ftp 程序删除本程式 。<br>";
		exit;
	}
}

function runquery($query,$info='') {
	global $db, $tablepre;
	if ($info) {
		echo $info.'&gt;';
	}
	$expquery = explode(";", $query);
	foreach($expquery as $sql) {
		$sql = trim($sql);
		if($sql != "" && $sql[0] != "#") {
			$db->query(str_replace("cdb_", $tablepre, $sql),'SILENT');
		}
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

function user2id($table, $columnorig = 'username', $columnnew = 'uid', $keepold = FALSE) {
	extract($GLOBALS, EXTR_SKIP);
	$startrow = intval($_GET['startrow']);
	$converted = 0;
	if(!$startrow) {
		$db->query("ALTER TABLE $table ADD $columnnew mediumint(8) UNSIGNED NOT NULL AFTER $columnorig");
	}
	$query = $db->query("SELECT $table.$columnorig, $table_members.uid FROM $table LEFT JOIN $table_members ON $table.$columnorig=$table_members.username GROUP BY $table.$columnorig LIMIT $startrow, 100");
	while($member = $db->fetch_array($query)) {
		$converted = 1;
		$db->query("UPDATE $table SET $columnnew='{$member['uid']}' WHERE $columnorig='".addslashes($member[$columnorig])."'");
	}
	if(empty($converted)) {
		if(empty($keepold)) {
			$db->query("ALTER TABLE $table DROP $columnorig");
		}
		echo "数据表 $table 转换完毕。<br><br>";
	} else {
		echo "转换数据表 $table, 起始于第 $startrow 列<br>";
		redirect("$PHP_SELF?action=upgrade&step=$step&startrow=".($startrow + 100));
		exit();
	}
}

function attachtype($type, $returnval = 'html') {
	if(!isset($GLOBALS['_DCACHE']['attachicon'])) {
		$GLOBALS['_DCACHE']['attachicon'] = array
			(	1 => 'common.gif',
				2 => 'binary.gif',
				3 => 'zip.gif',
				4 => 'rar.gif',
				5 => 'msoffice.gif',
				6 => 'text.gif',
				7 => 'html.gif',
				8 => 'real.gif',
				9 => 'av.gif',
				10 => 'flash.gif',
				11 => 'image.gif'
			);
	}

	if(is_numeric($type)) {
		$typeid = $type;
	} else {
		if(preg_match("/image|^(jpg|gif|png|bmp)\t/", $type)) {
			$typeid = 11;
		} elseif(preg_match("/flash|^(swf|fla|swi)\t/", $type)) {
			$typeid = 10;
		} elseif(preg_match("/audio|video|^(wav|mid|mp3|m3u|wma|asf|asx|vqf|mpg|mpeg|avi|wmv)\t/", $type)) {
			$typeid = 9;
		} elseif(preg_match("/real|^(ra|rm|rv)\t/", $type)) {
			$typeid = 8;
		} elseif(preg_match("/htm|^(php|js|pl|cgi|asp)\t/", $type)) {
			$typeid = 7;
		} elseif(preg_match("/text|^(txt|rtf|wri|chm)\t/", $type)) {
			$typeid = 6;
		} elseif(preg_match("/word|powerpoint|^(doc|ppt)\t/", $type)) {
			$typeid = 5;
		} elseif(preg_match("/^rar\t/", $type)) {
			$typeid = 4;
		} elseif(preg_match("/compressed|^(zip|arj|arc|cab|lzh|lha|tar|gz)\t/", $type)) {
			$typeid = 3;
		} elseif(preg_match("/octet-stream|^(exe|com|bat|dll)\t/", $type)) {
			$typeid = 2;
		} elseif($type) {
			$typeid = 1;
		} else {
			$typeid = 0;
		}
	}
	if($returnval == 'html') {
		return '<img src="images/attachicons/'.$GLOBALS['_DCACHE']['attachicon'][$typeid].'" align="absmiddle" border="0">';
	} elseif($returnval == 'id') {
		return $typeid;
	}
}

function loginit($log) {
	global $lang;
	$fp = @fopen('./forumdata/'.$log.'.php','w');
	@fwrite($fp, "<?PHP exit(\"Access Denied\"); ?".">\n");
	@fclose($fp);
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
<title>Discuz 2.2F --> 2.5 升级程序</title>
</head>
<body leftmargin="0" rightmargin="0" topmargin="0">
<br><br>
<table width="98%" cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td width="100%"><H4><font color=white>D2.2F -->D2.5 升级程序</font></H4>
</td></tr></table>
<table bgcolor="#FFFFFF" width="98%" cellpadding="10" cellspacing="0" border="0" align="center">
<tr>
<td width="100%">
<table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
<tr><td>
<?
}
?>