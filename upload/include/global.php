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

define('PHP_CLOSE_TAG', '?'.'>');
define('PHP_NEXTLINE', (PHP_OS == 'WINNT')?"\r\n":"\n");
        

function cutstr($string, $length) {
	if(strlen($string) > $length) {
		for($i = 0; $i < $length - 3; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
		return $strcut.' ...';
	} else {
		return $string;
	}
}

function clearcookies() {
	global $timestamp, $cookiepath, $cookiedomain, $discuz_uid, $discuz_user, $discuz_pw, $discuz_secques, $adminid, $groupid, $credit;
	setcookie('_discuz_uid', '', $timestamp - 86400 * 365, $cookiepath, $cookiedomain, false, true);
	setcookie('_discuz_pw', '', $timestamp - 86400 * 365, $cookiepath, $cookiedomain, false, true);
	setcookie('_discuz_secques', '', $timestamp - 86400 * 365, $cookiepath, $cookiedomain, false, true);
	$groupid = 7;
	$discuz_uid = $adminid = $credit = 0;
	$discuz_user = $discuz_pw = $discuz_secques = '';
}

// 语言包文本插值: 取代原来的 eval() 方式。
// 支持 $var、{$var}、$arr[key]、$arr['key']、$arr[key][key2] 五种写法,
// 未定义的变量按 PHP 插值语义展开为空串, 数组展开为 Array。
function dinterpolate($string) {
	if(strpos($string, '$') === false) {
		return $string;
	}
	return preg_replace_callback(
		'/(?<!\\\\)\{\$([a-zA-Z_][a-zA-Z0-9_]*)((?:\[[^\[\]]*\])*)\}|(?<!\\\\)\$([a-zA-Z_][a-zA-Z0-9_]*)((?:\[[^\[\]]*\])*)/',
		'dinterpolate_callback', $string);
}

function dinterpolate_callback($matches) {
	$name = !empty($matches[1]) ? $matches[1] : $matches[3];
	$index = !empty($matches[1]) ? $matches[2] : $matches[4];
	$value = isset($GLOBALS[$name]) ? $GLOBALS[$name] : NULL;
	if($index != '') {
		preg_match_all('/\[([^\[\]]*)\]/', $index, $keys);
		foreach($keys[1] as $key) {
			$key = trim($key);
			if(strlen($key) > 1 && ($key[0] == "'" || $key[0] == '"') && substr($key, -1) == $key[0]) {
				$key = substr($key, 1, -1);
			}
			if(is_array($value) && array_key_exists($key, $value)) {
				$value = $value[$key];
			} else {
				return '';
			}
		}
	}
	if(is_array($value)) {
		return 'Array';
	} elseif(is_object($value)) {
		return method_exists($value, '__toString') ? (string)$value : '';
	} elseif($value === NULL) {
		return '';
	}
	return (string)$value;
}

function dexit($message = '') {
	echo $message;
	output();
	exit();
}

function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;(#\d{3,5};)/', '&\\1', 
			str_replace(array('&', '"', '<', '>'), 
			array('&amp;', '&quot;', '&lt;', '&gt;'),
			$string));
	}
	return $string;
}

function unhtmlspecialchars($string) {
	return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
}

function dreferer() {
	global $referer;

	$HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? ($_SERVER['HTTP_REFERER'] ?? '') : '';
	if(empty($referer) && $HTTP_REFERER) {
		$referer = preg_replace("/(?:([\?&]sid\=[a-z0-9]{8}&?))/i", '', $HTTP_REFERER);
		$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
	}
	if(!strpos($referer, '.php') || strpos($referer, 'logging.php')) {
		$referer = 'index.php';
	}
	// 防 XSS / 头注入: Referer 会进入表单隐藏域与跳转链接, 去掉引号、尖括号与换行
	$referer = str_replace(array("\r", "\n", '"', "'", '<', '>'), '', $referer);
	// 只允许跳回本站, 防止借助 Referer 构造开放重定向
	if(preg_match("#^([a-z]+:)?//#i", $referer)) {
		$refhost = preg_replace("#^([a-z]+:)?//([^/]+).*#i", "\\2", $referer);
		$curhost = isset($_SERVER['HTTP_HOST']) ? ($_SERVER['HTTP_HOST'] ?? '') : '';
		if(strcasecmp($refhost, $curhost) !== 0) {
			$referer = 'index.php';
		}
	}
	return $referer;		
}

function fileext($filename) {
	return substr(strrchr($filename, '.'), 1);
}

function formhash() {
	global $discuz_user, $discuz_uid, $discuz_pw, $timestamp;
	return substr(md5(substr($timestamp, 0, -6).$discuz_user.$discuz_uid.$discuz_pw), 8, 8);
}

function image($imageinfo, $basedir = '', $remark = '') {
	if($basedir) {
		$basedir .= '/';
	}
	if(strstr($imageinfo, ',')) {
		$flash = explode(",", $imageinfo);
		return "<embed src=\"$basedir".trim($flash[0])."\" width=\"".trim($flash[1])."\" height=\"".trim($flash[2])."\" type=\"application/x-shockwave-flash\" $remark></embed>";
	} else {
		return "<img src=\"$basedir$imageinfo\" $remark border=\"0\">";
	}
}

function ipbanned($onlineip) {
	global $timestamp, $cachelost;

	$_DCACHE = array();
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/cache_ipbanned.php') ? '' : ' ipbanned';
	if(!empty($_DCACHE['ipbanned'])) {
		if($_DCACHE['ipbanned']['expiration'] < $timestamp) {
			@unlink(DISCUZ_ROOT.'./forumdata/cache/cache_ipbanned.php');
		}
		return preg_match("/(".$_DCACHE['ipbanned']['regexp'].")/", $onlineip) ? TRUE : FALSE;
	}
}

function isemail($email) {
	return strlen($email) > 8 && preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email);
}

function language($file, $templateid = 0, $tpldir = '') {
	$tpldir = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : './templates/default');
	$templateid = $templateid ? $templateid : (defined('TEMPLATEID') ? TEMPLATEID : 1);

	$languagepack = DISCUZ_ROOT.'./'.$tpldir.'/'.$file.'.lang.php';
	if(file_exists($languagepack)) {
		return $languagepack;
	} elseif($templateid != 1 && $tpldir != './templates/default') {
		return language($file, 1, './templates/default');
	} else {
		return FALSE;
	}
}

function modcheck($username) {
	global $adminid, $adminglobal, $forum, $fup;

	$succeedtomod = 0; // whether can moderators can be succeeded to

	$username = preg_quote(stripslashes($username), '/');
	if($adminid == 1 || $adminid == 2 || ($adminid>3 && $adminglobal) || ($adminid > 2 && (preg_match("/(,|^)\s*$username\s*(,|$)/i", $forum['moderator']) || ($succeedtomod && $forum['type'] == 'sub' && !empty($fup) && preg_match("/(,|^)\s*$username\s*(,|$)/i", $fup['moderator']))))) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function multi($num, $perpage, $curr_page, $mpurl) {
	$multipage = '';
	if($num > $perpage) {
		$page = 10;
		$offset = 2;

		$pages = ceil($num / $perpage);
		$from = $curr_page - $offset;
		$to = $curr_page + $page - $offset - 1;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			if($from < 1) {
				$to = $curr_page + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curr_page - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}
		$multipage .= '<a href="'.$mpurl.'&page=1">&lt;&lt;</a> &nbsp;';
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
				$multipage .= '<a href="'.$mpurl.'&page='.$i.'">['.$i.']</a>&nbsp;';
			} else {
				$multipage .= '<u><b>['.$i.']</b></u>&nbsp;';
			}
		}
		$multipage .= $pages > $page ? " ... <a href=\"$mpurl&page=$pages\"> [$pages] &gt;&gt;</a>" : " <a href=\"$mpurl&page=$pages\">&gt;&gt;</a>";
	}
	return $multipage;
}

function output() {
	global $sid;

	if(empty($GLOBALS['_COOKIE']['sid'])) {
		$content = ob_get_contents();
		$content = preg_replace_callback("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/is", "parse_output_link", $content);
		$content = preg_replace("/(\<form.+?\>)/is", "\\1\n<input type=\"hidden\" name=\"sid\" value=\"$sid\">", $content);
		ob_end_clean();
		!empty($GLOBALS['gzipcompress']) ? ob_start('ob_gzhandler') : ob_start();
		echo $content;
	}
}

function parse_output_link($m) {
	return transsid($m[3], '<a'.$m[1].'href='.$m[2]);
}

// 密码哈希: 新密码使用 password_hash, 历史 md5 在登录成功后自动升级
function dpassword_hash($password) {
	return password_hash($password, PASSWORD_DEFAULT);
}

function dpassword_verify($password, $stored) {
	if(!is_string($stored) || $stored === '') {
		return FALSE;
	}
	if(preg_match('/^[a-f0-9]{32}$/i', $stored)) {
		return hash_equals(strtolower($stored), md5($password));
	}
	return password_verify($password, $stored);
}

function dpassword_needs_rehash($stored) {
	if(preg_match('/^[a-f0-9]{32}$/i', $stored)) {
		return TRUE;
	}
	return password_needs_rehash($stored, PASSWORD_DEFAULT);
}

function quescrypt($questionid, $answer) {
	return $questionid > 0 && $answer != '' ? substr(md5($answer.md5($questionid)), 16, 8) : '';
}

function random($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[random_int(0, $max)];
	}
	return $hash;
}

function sendmail($to, $subject, $message, $from = '') {
	extract($GLOBALS, EXTR_SKIP);
	require DISCUZ_ROOT.'./include/sendmail.php';
}

function showmessage($show_message, $url_forward = '', $extra = '') {
	extract($GLOBALS, EXTR_SKIP);
	$GLOBALS['discuz_action'] = $extra == 'HALTED' ? 254 : 255;

	@include_once language('messages');

	$plugins_languagepack = DISCUZ_ROOT.'./plugins/plugin.lang.php';
	if(file_exists($plugins_languagepack)) {
		include_once $plugins_languagepack;
		if (!empty($msglang) && is_array($msglang)){
			$language = array_merge($language,$msglang);
		}
	}
	unset($plugins_languagepack);

	if(isset($language[$show_message])) {
		$show_message = dinterpolate($language[$show_message]);
	}
	$url_redirect = $url_forward ? '<meta http-equiv="refresh" content="2;url='.transsid($url_forward).'">' : NULL;

	// 如果您想要使用提示信息快速跳转页面，请将下面语句中的
	// 'showmessage' 修改为 'quickmessage'

	include template('showmessage');
	dexit();
}

function showstars($num) {
	for($i = 0; $i < $num; $i++) {
		echo '<img src="'.IMGDIR.'/star.gif">';
	}
}

function submitcheck($var, $allowget = 0) {
	global $_SERVER;
	if(!empty($GLOBALS[$var])) {
		$ispost = isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] ?? '') == 'POST';
		if(!$ispost && !$allowget) {
			showmessage('submit_invalid');
		}
		// 安全加固: GET 与 POST 一律校验 formhash 与来源主机(旧版 GET 会跳过令牌)
		if(!empty($GLOBALS['formhash']) && $GLOBALS['formhash'] == formhash() &&
			(empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", ($_SERVER['HTTP_REFERER'] ?? '')) == preg_replace("/([^\:]+).*/", "\\1", ($_SERVER['HTTP_HOST'] ?? '')))) {
			return TRUE;
		}
		showmessage('submit_invalid');
	} else {
		return FALSE;
	}
}

function template($file, $templateid = 0, $tpldir = '') {
	global $tplrefresh;

	$tpldir = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : './templates/default');
	$templateid = $templateid ? $templateid : (defined('TEMPLATEID') ? TEMPLATEID : 1);

	$tplfile = DISCUZ_ROOT.'./'.$tpldir.'/'.$file.'.htm';
	$objfile = DISCUZ_ROOT.'./forumdata/templates/'.$templateid.'_'.$file.'.tpl.php';
	if((defined('TEMPLATEID') ? TEMPLATEID : 1) != 1 && $templateid != 1 && !file_exists($tplfile)) {
		return template($file, 1, './templates/default/');
	}
	if($tplrefresh == 1 || ($tplrefresh > 1 && substr($GLOBALS['timestamp'], -1) > $tplrefresh)) {
		if(@filemtime($tplfile) > @filemtime($objfile)) {
			require_once DISCUZ_ROOT.'./include/template.php';
			parse_template($file, $templateid, $tpldir);
		}
	}
	return $objfile;
}

function transsid($url, $tag = '') {
	global $sid;
	$tag = stripslashes($tag);
	if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, 'sid='))) {
		$pos = strpos($url, '#');
		$urlret = '';
		if($pos) {
			$urlret = substr($url, $pos);
			$url = substr($url, 0, $pos);
		}
		$url .= (strpos($url, '?') ? '&' : '?').'sid='.$sid.$urlret;
	}
	return $tag.$url;
}

function updatesession() {
	if(empty($GLOBALS['sessionupdated'])) {
		global $db, $sessionexists, $sessionupdated, $sid, $onlineip, $discuz_uid, $discuz_user, $timestamp, $groupid, $styleid, $invisible, $discuz_action, $fid, $tid, $onlinehold, $logincredits, $table_sessions, $table_members, $user_lastactivity, $onlinehold;

		if($sessionexists == 1) {
			$db->query("UPDATE $table_sessions SET uid='$discuz_uid', username='$discuz_user', groupid='$groupid', styleid='$styleid', invisible='$invisible', action='$discuz_action', lastactivity='$timestamp', fid='$fid', tid='$tid' WHERE sid='$sid'");
			if ($onlinehold && $user_lastactivity && $timestamp - $user_lastactivity > $onlinehold) {
				$db->query("UPDATE $table_members SET lastvisit=lastactivity, lastactivity=$timestamp WHERE uid='$discuz_uid'", 'UNBUFFERED');
			}
		} else {
			$ips = explode('.', $onlineip);

			$db->query("DELETE FROM $table_sessions WHERE sid='$sid' OR lastactivity<($timestamp-$onlinehold) OR ('$discuz_uid'<>'0' AND uid='$discuz_uid') OR (uid='0' AND ip1='$ips[0]' AND ip2='$ips[1]' AND ip3='$ips[2]' AND ip4='$ips[3]' AND lastactivity>$timestamp-60)");
			$db->query("INSERT INTO $table_sessions (sid, ip1, ip2, ip3, ip4, uid, username, groupid, styleid, invisible, action, lastactivity, fid, tid)
				VALUES ('$sid', '$ips[0]', '$ips[1]', '$ips[2]', '$ips[3]', '$discuz_uid', '$discuz_user', '$groupid', '$styleid', '$invisible', '$discuz_action', '$timestamp', '$fid', '$tid')");
			if($discuz_uid) {
				$db->query("UPDATE $table_members SET credit=credit+".intval($logincredits).", lastip='$onlineip', lastvisit=lastactivity, lastactivity=$timestamp WHERE uid='$discuz_uid'", 'UNBUFFERED');
			}
		}
		$sessionupdated = 1;
	}
}

function implode_ids( $array ){
	$ids = $comma = '';
	if ( is_array($array) && count($array) ){
		foreach($array as $id) {
			$ids .= "$comma'$id'";
			$comma = ", ";
		}
	}
	return $ids;
}


function debuginfo() {
	if($GLOBALS['debug']) {
		global $db, $discuz_starttime, $gzipcompress;
		$mtime = explode(' ', microtime());
		$totaltime = number_format(($mtime[1] + $mtime[0] - $discuz_starttime), 6);
		echo '<br>Processed in '.$totaltime.' second(s), '.$db->querynum.' queries'.
			($gzipcompress ? ', Gzip enabled' : NULL);
	}
}

?>