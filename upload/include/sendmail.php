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

require DISCUZ_ROOT.'./mail_config.php';
@include language('emails');

if($sendmail_silent) {
	error_reporting(0);
}

if(isset($language[$subject])) {
	$subject = dinterpolate($language[$subject]);
}
if(isset($language[$message])) {
	$message = dinterpolate($language['header'].$language[$message].$language['footer']);
}

$subject = str_replace("\r", '', str_replace("\n", '', $subject));
$message = str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message)))));

if(!$from) {
	$from = "$bbname <$adminemail>";
}

if($mailsend == 1 && function_exists('mail')) {

	if(strpos($to, ',')) {
		@mail('Discuz! User <me@localhost>', $subject, $message, "From: $from\r\nBcc: $to");
	} else {
		@mail($to, $subject, $message, "From: $from");
	}

} elseif($mailsend == 2) {

	$fp = fsockopen($mailcfg['server'], $mailcfg['port'], $errno, $errstr, 30);
	if($mailcfg['auth']) {
		$from = $mailcfg['from'];
		fputs($fp, "EHLO discuz \r\n");
		fputs($fp, "AUTH LOGIN \r\n");
		fputs($fp, base64_encode($mailcfg['auth_username'])." \r\n");
		fputs($fp, base64_encode($mailcfg['auth_password'])." \r\n");
	} else {
		fputs($fp, "HELO discuz\r\n");
	}

	$from = preg_replace("/.*\<(.+?)\>.*/", "\\1", $from);
	fputs($fp, "MAIL FROM: $from\r\n");

	foreach(explode(',', $to) as $touser) {
		$touser = trim($touser);
		if($touser) {
			fputs($fp, "RCPT TO: $touser\r\n");
		}
	}

	fputs($fp, "DATA\r\n");
	$tosend  = "From: $from\r\n";
	$tosend .= "To: Discuz Users <me@localhost>\r\n";
	$tosend .= 'Subject: '.str_replace("\n", ' ', $subject)."\r\n\r\n$message\r\n.\r\n"; 
	fputs($fp, $tosend);

	fputs($fp, "QUIT\r\n");
	$temp = fread($fp, 10000);
	//echo $temp;	//debug
	fclose($fp);

} elseif($mailsend == 3) {

	ini_set('SMTP', $mailcfg['server']);
	ini_set('smtp_port', $mailcfg['port']);
	ini_set('sendmail_from', $from);

	foreach(explode(',', $to) as $touser) {
		$touser = trim($touser);
		if($touser) {
			@mail($touser, $subject, $message, "From: $from");
		}
	}

}

?>