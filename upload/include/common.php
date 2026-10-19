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

error_reporting(E_ERROR | E_WARNING | E_PARSE);

// PHP 8 起 set_magic_quotes_runtime() / get_magic_quotes_gpc() 均已移除,
// 魔术引号恒为关闭, 因此 daddslashes() 始终转义, register_globals 模拟始终生效

define('IN_DISCUZ', TRUE);
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -7));

$mtime = explode(' ', microtime());
$discuz_starttime = $mtime[1] + $mtime[0];

$magic_quotes_gpc = 0;
$register_globals = 0;

if(!$register_globals || !$magic_quotes_gpc) {

        // 身份与会话变量只能来自会话存储, 不允许由 URL / 表单注入覆盖
        $injectvars = array_flip(array('adminid', 'groupid', 'discuz_uid', 'discuz_user', 'discuz_userss',
                'discuz_pw', 'discuz_secques', 'accessmasks', 'isadmin', 'adminglobal', 'sessionexists',
                'sessionupdated', 'magic_quotes_gpc', 'register_globals'));

        $_POST = daddslashes($_POST);
        $_GET = daddslashes($_GET);
        $_SERVER = daddslashes($_SERVER);
        $_COOKIE = daddslashes($_COOKIE);

        // daddslashes() 对空数组返回 false, 而 PHP 8 的 extract() 只接受数组
        if(is_array($_POST)) {
                @extract(array_diff_key($_POST, $injectvars), EXTR_SKIP);
        }
        if(is_array($_GET)) {
                @extract(array_diff_key($_GET, $injectvars), EXTR_SKIP);
        }
        if(is_array($_FILES) && count($_FILES)) {
                $_FILES = daddslashes($_FILES);
                foreach($_FILES as $key => $val) {
                        $$key = $val['tmp_name'];
                        ${$key.'_name'} = $val['name'];
                        ${$key.'_size'} = $val['size'];
                        ${$key.'_type'} = $val['type'];
                }
        }
        unset($injectvars);

        // 提取完成后再固定一次, 防止被输入覆盖
        $magic_quotes_gpc = 0;
        $register_globals = 0;
}

// PHP 8 把“未定义变量/数组下标”从 E_NOTICE 提升为 E_WARNING, 旧代码会在页面上出现大量提示;
// 这里按 PHP 5 的语义把常用的请求级变量预置为空值, 未提供的参数行为与旧版一致(均为假值)
// 注意: action / searchid / fid / previewpost 等变量在代码中用 isset() 区分“是否提交”,
// 不能预置, 否则会改变 isset() 的语义
$predefined = array('navtitle', 'page', 'goto', 'p', 'order', 'ascdesc', 'desc', 'keyword',
	'srchmem', 'fpage', 'id', 'urlret', 'signature', 'currtime', 'admins', 'regsubmit', 'statstatus',
	'pid', 'digest', 'invisible', 'type', 'tid', 'topicsubmit',
	'replysubmit', 'editsubmit', 'subject', 'navigation', 'viewperm', 'usesigcheck', 'urloffcheck',
	'smileoffcheck', 'smileyoffcheck', 'codeoffcheck', 'poll', 'polloptions', 'ismoderator',
	'searchsubmit');
foreach($predefined as $prevar) {
	if(!isset($$prevar)) {
		$$prevar = '';
	}
}
unset($prevar, $predefined);
if(!isset($forum)) {
	$forum = array();
}
if(!isset($forums)) {
	$forums = array();
}

require DISCUZ_ROOT.'./config.php';
require DISCUZ_ROOT.'./include/global.php';
require DISCUZ_ROOT.'./include/db_'.$database.'.php';

$timestamp = time();

$PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$boardurl = 'http://'.$_SERVER['HTTP_HOST'].substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
$discuz_root = DISCUZ_ROOT; 
$url_redirect = '';

if(getenv('HTTP_CLIENT_IP')) {
	$onlineip = getenv('HTTP_CLIENT_IP');
} elseif(getenv('HTTP_X_FORWARDED_FOR')) {
	list($onlineip) = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
} elseif(getenv('REMOTE_ADDR')) {
	$onlineip = getenv('REMOTE_ADDR');
} else {
	$onlineip = $_SERVER['REMOTE_ADDR'];
}

$_DSESSION = $_DCACHE = array();

$cachelost = (@include DISCUZ_ROOT.'./forumdata/cache/cache_settings.php') ? '' : 'settings';

@extract((array)$_DCACHE['settings'], EXTR_OVERWRITE);
if(!is_array($_DCACHE['settings'])) {
	$_DCACHE['settings'] = array();
}

$tables = array('access', 'admingroups', 'adminsessions', 'attachments', 'attachtypes', 'announcements', 'banned', 'bbcodes', 'caches','failedlogins', 'favorites','forumlinks', 'forums', 'karmalog', 'members', 'onlinelist', 'polls', 'posts', 'ranks', 'searchindex', 'sessions', 'settings','smilies', 'stats', 'styles', 'stylevars', 'subscriptions', 'templates', 'threads', 'pms', 'usergroups', 'words', 'buddys','plugins','plugins_settings'); 

if (!empty($plugins_table) && is_array($plugins_table)) $tables = array_merge($plugins_table,$tables);

foreach($tables as $tablename) {
	${'table_'.$tablename} = $tablepre.$tablename;
}
unset($tablename, $plugins_table);


if(defined('CURRSCRIPT') && in_array(CURRSCRIPT, array('index', 'forumdisplay', 'viewthread', 'post', 'pm'))) {
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/cache_'.CURRSCRIPT.'.php') ? '' : ' '.CURRSCRIPT;
}

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
$db->select_db($dbname);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

$sid = isset($_GET['sid']) ? $_GET['sid'] :(isset($_POST['sid']) ? $_POST['sid'] :(isset($_COOKIE['sid']) ? $_COOKIE['sid'] : ''));

$discuz_uid = isset($_COOKIE['_discuz_uid']) ? $_COOKIE['_discuz_uid'] : '';
$discuz_pw = isset($_COOKIE['_discuz_pw']) ? $_COOKIE['_discuz_pw'] : '';
$discuz_secques = isset($_COOKIE['_discuz_secques']) ? $_COOKIE['_discuz_secques'] : '';

$newpm = $newpmexists = $sessionexists = $adminid = $adminglobal = $user_lastactivity = $is_sessionuser = 0;
$ipbanned = $accessmasks = $credit = 0;

$userinfo="m.uid AS discuz_uid, m.username AS discuz_user, m.password AS discuz_pw, m.adminid, m.groupid, m.email, m.timeoffset,m.tpp, m.ppp, m.credit, m.timeformat, m.dateformat, m.signature, m.invisible, m.lastvisit, m.lastactivity as user_lastactivity, m.lastpost, m.newpm, m.accessmasks, m.regdate";

if($sid) {
	if($discuz_uid) {
		$query = $db->query("SELECT s.sid, s.styleid, s.groupid='6' AS ipbanned, $userinfo FROM $table_sessions s, $table_members m WHERE m.uid=s.uid AND s.sid='$sid' AND CONCAT_WS('.',s.ip1,s.ip2,s.ip3,s.ip4)='$onlineip' AND m.uid='$discuz_uid' AND m.password='$discuz_pw' AND m.secques='$discuz_secques'");
	} else {
		$query = $db->query("SELECT sid, uid AS sessionuid, groupid, groupid='6' AS ipbanned, styleid FROM $table_sessions WHERE sid='$sid' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='$onlineip'");
	}
	if($_DSESSION = $db->fetch_array($query)) {
		$sessionexists = 1;
		if(!empty($_DSESSION['sessionuid'])) {
			$query = $db->query("SELECT $userinfo FROM $table_members m WHERE uid='{$_DSESSION['sessionuid']}'");
			$_DSESSION = array_merge($_DSESSION, $db->fetch_array($query));
			$is_sessionuser = 1;
		}
	} else {
		$query = $db->query("SELECT sid, groupid, groupid='6' AS ipbanned, styleid FROM $table_sessions WHERE sid='$sid' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='$onlineip'");
		if($_DSESSION = $db->fetch_array($query)) {
			clearcookies();
			$sessionexists = 1;
		}
	}
}
if(!$sessionexists) {
	if($discuz_uid) {
		$query = $db->query("SELECT $userinfo ,m.styleid FROM $table_members m WHERE uid='$discuz_uid' AND password='$discuz_pw' AND secques='$discuz_secques'");
		if(!($_DSESSION = $db->fetch_array($query))) {
			clearcookies();
		}
	}

	if(ipbanned($onlineip)) {
		$_DSESSION['ipbanned'] = 1;
	}

	$sid = random(6);
}

@extract((array)$_DSESSION, EXTR_OVERWRITE);

$lastvisit = empty($lastvisit) ? $timestamp - 86400 : $lastvisit;

if(empty($discuz_uid) || empty($discuz_user)) {
	$discuz_user = '';
	$discuz_uid = $adminid = $credit =0;
	$groupid = (isset($groupid) && $groupid == 6) ? 6 : 7;
} else {
	$discuz_userss = $discuz_user;
	$discuz_user = addslashes($discuz_user);
	$credit = intval($credit);
}

define('FORMHASH', formhash());

if($statstatus) {
	require DISCUZ_ROOT.'./include/counter.php';
}

if(!isset($_COOKIE['sid']) || $sid != $_COOKIE['sid']) {
	setcookie('sid', $sid, $timestamp + 2592000, $cookiepath, $cookiedomain);
}

$tpp = empty($_DSESSION['tpp']) ? $topicperpage : $_DSESSION['tpp'];
$ppp = empty($_DSESSION['ppp']) ? $postperpage : $_DSESSION['ppp'];

if($discuz_uid && $accessmasks) {
	$accessadd1 = ', a.allowview, a.allowpost, a.allowreply, a.allowgetattach';
	$accessadd2 = "LEFT JOIN $table_access a ON a.uid='$discuz_uid' AND a.fid=f.fid";
} else {
	$accessadd1 = $accessadd2 = '';
}

if(!empty($tid)){
	$query = $db->query("SELECT f.* $accessadd1 , t.* FROM $table_forums f, $table_threads t $accessadd2 WHERE t.tid='".intval($tid)."' AND f.fid=t.fid LIMIT 1");
	$forum = $db->fetch_array($query);
	$fid = is_array($forum) && isset($forum['fid']) ? $forum['fid'] : 0;
}elseif(!empty($fid)) {
	$query = $db->query("SELECT f.* $accessadd1 FROM $table_forums f $accessadd2 WHERE f.fid='".intval($fid)."'", 'CACHE');
	$forum = $db->fetch_array($query);
}
// PHP 8 起未定义数组下标会提示, 预置论坛常用字段(缺失时为假值, 与原逻辑一致)
if(is_array($forum)) {
	foreach(array('fid', 'fup', 'type', 'name', 'status', 'postperm', 'viewperm', 'getattachperm', 'allowpost', 'allowview', 'styleid', 'moderator', 'password') as $forumkey) {
		if(!isset($forum[$forumkey])) {
			$forum[$forumkey] = '';
		}
	}
	unset($forumkey);
}

$styleid = !empty($_GET['styleid']) ? $_GET['styleid'] :
		(!empty($_POST['styleid']) ? $_POST['styleid'] :
		(!empty($_DSESSION['styleid']) ? $_DSESSION['styleid'] :
		(isset($_DCACHE['settings']['styleid']) ? $_DCACHE['settings']['styleid'] : 0)));

if(@!include DISCUZ_ROOT.'./forumdata/cache/style_'.intval(!empty($forum['styleid']) ? $forum['styleid'] : $styleid).'.php') {
	$styleid = (isset($_DCACHE['settings']['styleid']) ? $_DCACHE['settings']['styleid'] : 0);
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/style_'.$styleid.'.php') ? '' : ' style_'.$styleid;
}

$groupid = $ipbanned ? 6 : (empty($groupid) ? 7 : $groupid);
$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/usergroup_'.$groupid.'.php') ? '' : ' usergroup_'.$groupid;

//adminidcheck
if($adminid >0 && $adminid != $groupid) {
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/admingroup_'.$adminid.'.php') ? '' : ' admingroup_'.$groupid;
} elseif($adminid<1) {
	$alloweditpost  = $alloweditpoll = $allowdelpost = $allowmassprune = $allowcensorword = $allowviewip = $allowbanip = $allowedituser = $allowbanuser  = $allowpostannounce = $allowviewlog = $disablepostctrl = 0;
	if(!$errorreport) error_reporting(0);
}

$isadmin = ($adminid == 1)? 1 : 0;

if($cachelost) {
	require DISCUZ_ROOT.'./include/cache.php';
	updatecache();
	dexit('Cache List: '.$cachelost.'<br>Caches successfully created, please refresh.');
}

if($nocacheheaders) {
	@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	@header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	@header('Cache-Control: no-store, no-cache, must-revalidate');
	@header('Cache-Control: post-check=0, pre-check=0', false);
	@header('Pragma: no-cache');
}
if($headercharset) {
	@header('Content-Type: text/html; charset='.CHARSET);
}

$gzipcompress ? ob_start('ob_gzhandler') : ob_start();

if(isset($allowvisit) && $allowvisit == 0) {
	setcookie('_discuz_uid', $discuz_uid, $timestamp + 86400 * 365, $cookiepath, $cookiedomain);
	setcookie('_discuz_pw', $discuz_pw, $timestamp + 86400 * 365, $cookiepath, $cookiedomain);
	setcookie('_discuz_secques', $discuz_secques, $timestamp + 86400 * 365, $cookiepath, $cookiedomain);
	showmessage('user_banned', NULL, 'HALTED');
} elseif($bbclosed && !((defined('CURRSCRIPT') && CURRSCRIPT == 'logging' && $action == 'login') || $adminid == 1)) {
	clearcookies();
	showmessage($closedreason ? $closedreason : 'board_closed');
}

function daddslashes(&$string, $force = 0) {
	if(!$GLOBALS['magic_quotes_gpc'] || $force) {
		if(is_array($string) && count($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		}elseif($string) {
			$string = addslashes($string);
		}elseif(!is_array($string)){
			$string = False;
		}
	}
	return $string;
}

?>