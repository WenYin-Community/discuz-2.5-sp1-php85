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

$newpmexists = 0;

if($ignorepm == 'yes') {

	$db->query("UPDATE $table_pms SET new='2' WHERE msgtoid='$discuz_uid' AND folder='inbox' AND new='1'");
	$db->query("UPDATE $table_members SET newpm='0' WHERE uid='$discuz_uid'");

} else {

	if($maxpmnum == 0) {

		$query = $db->query("DELETE FROM $table_pms WHERE msgtoid='$discuz_uid' AND folder='inbox'", 'UNBUFFERED');
		$db->query("UPDATE $table_members SET newpm='0' WHERE uid='$discuz_uid'");

	} else {

		$query = $db->query("SELECT pmid, msgfrom, msgfromid, subject, message FROM $table_pms WHERE msgtoid='$discuz_uid' AND folder='inbox' AND new='1'");
		$newpmnum = $db->num_rows($query);
		if($newpmnum) {
			$newpmexists = 1;
			$pmlist = array();
			$pmdetail = '';
			while($pm = $db->fetch_array($query)) {
				$pm['subject'] = cutstr($pm['subject'], 20);
				$pm['message'] = dhtmlspecialchars(cutstr($pm['message'], 50));
				$pmlist[] = $pm;
			}
	
			$ignorelink = $PHP_SELF.'?ignorepm=yes';
			foreach($_GET as $key => $val) {
				$ignorelink .= '&'.$key.'='.@rawurlencode($val);
			}
		} else {
			$db->query("UPDATE $table_members SET newpm='0' WHERE uid='$discuz_uid'");
		}

	}

}

?>