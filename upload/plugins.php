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

define('CURRSCRIPT',  'plugins');
require './include/common.php';

$baselink = transsid("plugins.php?p=$p");
$msglang = $lang = array();

if ($p && in_array($p,$plugins_script)){
	if(!@include DISCUZ_ROOT.'./plugins/'.$p.'.php'){
		showmessage('plugins_notinstalled');
	}
}else{
	showmessage('plugins_notinstalled');
}
dexit();

function getconfig($plugname){
	global $plugins_script;
	if (!in_array($plugname,$plugins_script)) return '';
	@include DISCUZ_ROOT.'./forumdata/cache/cache_plugins.php';
	return $_DCACHE['plugins_settings'][$plugname];
}

?>