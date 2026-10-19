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

require './include/common.php';

$discuz_action = 51;

if(!$page) {

	@include language('customfaq');
	include template('faq');

} elseif($page == 'usermaint') {

	include template('faq_usermaint');

} elseif($page == 'using') {

	include template('faq_using');

} elseif($page == 'messages') {

	$smilies = array();
	$query = $db->query("SELECT code, url FROM $table_smilies WHERE type='smiley'");
	while($smiley = $db->fetch_array($query)) {
		$smilies[] = $smiley;
	}

	include template('faq_messages');

} elseif($page == 'misc') {

	$discuzcodes = array();
	$query = $db->query("SELECT * FROM $table_bbcodes WHERE available='1'");
	while($discuzcode = $db->fetch_array($query)) {
		$discuzcode['explanation'] = htmlspecialchars($discuzcode['explanation']);
		$discuzcodes[] = $discuzcode;
	}

	include template('faq_misc');

} elseif($page == 'custom') {

	@include language('customfaq');
	if(is_array($customfaq)) {
		for($i = 0; $i < count($customfaq['item']); $i++) {
			$customfaq['item'][$i]['message'] = str_replace('  ', '&nbsp; ', nl2br($customfaq['item'][$i]['message']));
		}
	}
	include template('faq_custom');

}

?>