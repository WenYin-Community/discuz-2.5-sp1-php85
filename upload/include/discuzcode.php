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
1 多出来的空格
2 bbcode 解析问题
3 img code 对特殊符号的js未转义
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$searcharray=$replacearray= array();
$post_codecount = $post_phpcodecount = -1;
$codecount = 0 ;

function censor($message) {
	global $_DCACHE;

	require_once(DISCUZ_ROOT.'/forumdata/cache/cache_censor.php');
	return empty($_DCACHE['censor']) ? $message : @preg_replace($_DCACHE['censor']['find'], $_DCACHE['censor']['replace'], $message);
}

function credithide($creditsrequire, $message) {
	if($GLOBALS['credit'] < $creditsrequire && !$GLOBALS['ismoderator']) {
		return "<b>**** Hidden to Credits Lower Than $creditsrequire ****</b>";
	} else {
		return '<b>Below Message for Credits Higher Than '.$creditsrequire.'</b><br>'.
			'==============================<br><br>'.
			str_replace('\\"', '"', $message).'<br><br>'.
			'==============================';
	}
}

function codedisp($code) {
	global $thisbg, $codecount, $post_codecount, $codehtml;
	$post_codecount++;
	$code = htmlspecialchars(str_replace('\\"', '"', preg_replace("/^[\n\r]*(.+?)[\n\r]*$/is", "\\1", $code)));
	$codehtml[$post_codecount] = "<blockquote class=\"code\"><b class=\"smalltxt\">CODE:</b>&nbsp;&nbsp;<a href=\"###\" class=\"smalltxt\" onclick=\"copycode(findobj('code$codecount'));\">[Copy to clipboard]</a><hr size=1 color=\"".BORDERCOLOR."\"><div style=\"padding:5px; font-family:Courier New;\" id=\"code$codecount\">$code</div></blockquote>";

	$codecount++;
	return "[\tDISCUZ_CODE_$post_codecount\t]";
}

function parseurl(&$message) {
	return preg_match("/\[code\].+?\[\/code\]/is", $message) ? $message :
		substr(preg_replace(	array(
					"/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/i",
					"/(?<=[^\]a-z0-9\/\-_.~?=:.])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/i"
				), array(
					"[url]\\1\\3[/url]",
					"[email]\\0[/email]"
				), ' '.$message), 1);
}

function postify($message, $smileyoff, $bbcodeoff, $allowsmilies = 1, $allowhtml = 0, $allowbbcode = 1, $allowimgcode = 1) {
	global $credit, $tid, $discuz_uid, $codehtml, $post_codecount, $thisbg, $highlight, $table_posts, $db, $searcharray, $replacearray, $ismoderator,$phpcodehtml,$post_phpcodecount;

	if(!$bbcodeoff && $allowbbcode) {
		$message = preg_replace("/\s*\[code\](.+?)\[\/code\]\s*/ies", "codedisp('\\1')", $message);
	    $message = preg_replace("/\s*\[php\](.+?)\[\/php\]\s*/ies", "phpcodedisp('\\1')", $message);
	}

	if(!$allowhtml) {
		$message = dhtmlspecialchars($message);
	}

	if(!$smileyoff && $allowsmilies) {

		if(is_array($GLOBALS['_DCACHE']['smilies'])) {
			foreach($GLOBALS['_DCACHE']['smilies'] as $smiliey) {
				$message = str_replace($smiliey['code'], "<img src=\"".SMDIR."/$smiliey[url]\" align=\"absmiddle\" border=\"0\">",$message);
			}
		}
	}

	if(!$bbcodeoff && $allowbbcode) {

		if(empty($searcharray['bbcode_regexp']) || empty($replacearray['bbcode_regexp']) || empty($searcharray['bbcode_str']) || empty($replacearray['bbcode_str'])) {
			$searcharray['bbcode_regexp'] = array(
				"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
				"/(\[box=(#[0-9A-F]{6}|[a-z]+)\])[\n\r]*(.+?)[\n\r]*(\[\/box\])/is",
				"/\[url\]\s*(www.|https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/){1}([^\[\"']+?)\s*\[\/url\]/ie",
				"/\[url=www.([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[url=(https?|ftp|gopher|news|telnet|rtsp|mms){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[email\]\s*([A-Za-z0-9\-_.]+)@([A-Za-z0-9\-_]+[.][A-Za-z0-9\-_.]+)\s*\[\/email\]/i",
				"/\[email=([A-Za-z0-9\-_.]+)@([A-Za-z0-9\-_]+[.][A-Za-z0-9\-_.]+)\](.+?)\[\/email\]/is",
				"/\[color=([#0-9a-zA-Z]{3,10})\]/i",
				"/\[size=([\-0-9]{1,2})\]/i",
				"/\[font=([^\[]+?)\]/i",
				"/\[align=(left|right|center)\]/i",
				"/\[center\]/i"
			);
			$replacearray['bbcode_regexp'] = array(
				"<br><br><center><table border=\"0\" width=\"90%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;&nbsp;Quote:</td></tr><tr><td><table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"".BORDERCOLOR."\"><tr><td width=\"100%\" bgcolor=\"".ALTBG2."\" style=\"word-break:break-all\">\\1</td></tr></table></td></tr></table></center><br>",
				"<blockquote style=\"background-color: \\2 ;\"><span class=\"bold\">$title</span>\\3</blockquote>",
				"cuturl('\\1\\2')",
				"<a href=\"http://www.\\1\" target=\"_blank\">\\2</a>",
				"<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>",
				"<a href=\"mailto:\\1@\\2\">\\1@\\2</a>",
				"<a href=\"mailto:\\1@\\2\">\\3</a>",
				"<font color=\"\\1\">",
				"<font size=\"\\1\">",
				"<font face=\"\\1\">",
				"<p align=\"\\1\">",
				"<p align=\"center\">"
			);

			if($GLOBALS['_DCACHE']['bbcodes']) {
				$searcharray['bbcode_regexp'] = array_merge($searcharray['bbcode_regexp'], $searcharray['bbcode_regexp'], $GLOBALS['_DCACHE']['bbcodes']['searcharray']);
				$replacearray['bbcode_regexp'] = array_merge($replacearray['bbcode_regexp'], $replacearray['bbcode_regexp'], $GLOBALS['_DCACHE']['bbcodes']['replacearray']);
				unset($GLOBALS['_DCACHE']['bbcodes']);
			}

			$searcharray['bbcode_str'] = array(
				'[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]',
				'[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
				'[list=A]', '[*]', '[/list]','[/center]'
			);

			$replacearray['bbcode_str'] = array(
				'</font>', '</font>', '</font>', '</p>', '<b>', '</b>', '<i>',
				'</i>', '<u>', '</u>', '<ul>', '<ol type=1>', '<ol type=a>',
				'<ol type=A>', '<li>', '</ul></ol>','</p>'
			);       
		}                

		@$message = str_replace($searcharray['bbcode_str'], $replacearray['bbcode_str'],
				preg_replace($searcharray['bbcode_regexp'], $replacearray['bbcode_regexp'], $message));

		if(preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", $message)) {
			if(stristr($message, '[hide]')) {
				$query = $db->query("SELECT pid FROM $table_posts WHERE tid='$tid' AND authorid='$discuz_uid' LIMIT 1");
				if($ismoderator || $db->result($query, 0)) {
					$message = preg_replace("/\[hide\]\s*(.+?)\s*\[\/hide\]/is", "<span class=\"bold\">Below Message for Repliers</span><br>==============================<br><br>\\1<br><br>==============================", $message);
				} else {
					$message = preg_replace("/\[hide\](.+?)\[\/hide\]/is", "<b>**** Hidden to Non-Reply Visitors *****</b>", $message);
				}
			}
			$message = preg_replace("/\[hide=(\d+)\]\s*(.+?)\s*\[\/hide\]/ies", "credithide(\\1,'\\2')", $message);
		}

	}

	if(!$bbcodeoff && $allowimgcode) {
		if(empty($searcharray['imgcode']) || empty($replacearray['imgcode'])) {
			$searcharray['imgcode'] = array(
				"/\[swf\]\s*([^\[]+?)\s*\[\/swf\]/ies",
				"/\[img\]\s*([^\[]+?)\s*\[\/img\]/ies",
				"/\[img=(\d{1,3})[x|\,](\d{1,3})\]\s*([^\[]+?)\s*\[\/img\]/ies"
			);
			$replacearray['imgcode'] = array(
				"bbcodeurl('\\1', ' <img src=\"images/attachicons/flash.gif\" align=\"absmiddle\"> <a href=\"%s\" target=\"_blank\">Flash: %s</a> ')",
				"bbcodeurl('\\1', '<img src=\"%s\" border=\"0\" onload=\"if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt=\'Click here to open new window\';}\" onmouseover=\"if(this.resized) this.style.cursor=\'hand\';\" onclick=\"if(this.resized) {window.open(this.src);}\">')",
				"bbcodeurl('\\3', '<img width=\"\\1\" height=\"\\2\" src=\"%s\" border=\"0\">')"
			);
		}
		$message = preg_replace($searcharray['imgcode'], $replacearray['imgcode'], $message);
	}

	for($i = 0; $i <= $post_codecount; $i++) {
		$message = str_replace("[\tDISCUZ_CODE_$i\t]", $codehtml[$i], $message);
	}

	if($highlight) {
		foreach(explode('+', $highlight) as $ret) {
			if($ret) {
				$message = preg_replace("/(?<=[\s\"\]>()]|^)(".preg_quote($ret, '/').")(([\.,;-?!()]+)?([\s\"<\[]|$))/siU", "<u><b><font color=\"#FF0000\">\\1</font></b></u>\\2", $message);
			}
		}
	}
	for($i = 0; $i <= $post_phpcodecount; $i++) {
                        $message = str_replace("|\tDISCUZ_PHPCODE_$i\t|", $phpcodehtml[$i], $message);
    	}

	$message = nl2br(str_replace(
							array("\t", '   ', '  '), 
							array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;')
							, $message)
					);

	return $message;
}

function cuturl($url) {
	$length = 65;
	$urllink = "<a href=\"".(substr(strtolower($url), 0, 4) == 'www.' ? "http://$url" : $url).'" target="_blank">';
	if(strlen($url) > $length) {
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
	}
	$urllink .= $url.'</a>';
	return $urllink;
}

function bbcodeurl($url, $tags) {
	if(!preg_match("/<.+?>/s",$url)) {
		if(!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://'))) { 
			$url = 'http://'.$url;
		}
		return str_replace('submit', '', sprintf($tags, $url, $url));
	} else {
		return '&nbsp;'.$url;
	}
}

function phpcodedisp($code) {
        global $thisbg, $codecount, $post_phpcodecount, $phpcodehtml,$post;
        $post_phpcodecount++;
        $phpcode = phphighlite(str_replace("\\\"", "\"", $code));
		$phpcodeid ="pcode_$post[pid]_$post_phpcodecount";
		$phpcodehtml[$post_phpcodecount] = "<blockquote class=\"code\"><b class=\"smalltxt\">PHP:</b>&nbsp;&nbsp;<a href=\"###\" class=\"smalltxt\" onclick=\"copycode(findobj('$phpcodeid'));\">[Copy to clipboard]</a><hr size=1 color=\"".BORDERCOLOR."\"><div style=\"padding:5px; font-family:Courier New;\" id=\"$phpcodeid\">$phpcode</div></blockquote>";
        $phpcodecount++;
        return "|\tDISCUZ_PHPCODE_$post_phpcodecount\t|";
}
function phphighlite($code) {
	if(!strpos($code,"&lt;?\n") && !strpos($code,'&lt;?') &&  substr($code,0,2)!='&lt;?'){
		$code='<'.'?'.trim($code).' ?'.'>';
		$addedtags=1;
	}
	ob_start();
	$oldlevel=error_reporting(0);
	highlight_string($code);
	error_reporting($oldlevel);
	$buffer = ob_get_contents();
	ob_end_clean();
	if ($addedtags) {
		$openingpos = strpos($buffer,'&lt;?');
		$closingpos = strrpos($buffer, '?');
		$buffer=substr($buffer, 0, $openingpos).substr($buffer, $openingpos+5, $closingpos-($openingpos+5)).substr($buffer, $closingpos+5);
	}
	$buffer = str_replace("&quot;", "\"", $buffer);
	$buffer = str_replace("<br />", '', $buffer);
	return $buffer;
}

function clear_bbcode($code){
	$rep = array('[', ']', ':', '(', ')');
	for ($x = 0; $x < count($rep); $x++) {
		$code = str_replace($rep[$x], '&#'.ord($rep[$x]).';', $code);
	}
	return $code;
}
?>