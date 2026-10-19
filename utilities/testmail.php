<?php

/*
	[DISCUZ!] utilities/testmail.php - test email sending module of Discuz!
	This is NOT a freeware, use is subject to license terms

	Version: 2.0.0
	Author: Crossday (info@discuz.net)
	Copyright: Crossday Studio (www.crossday.com)
	Last Modified: 2002/12/6 17:00
*/

error_reporting(7);
define('IN_DISCUZ', true);

define('DISCUZ_ROOT', './');
define('TPLDIR', './templates/default');

$from = 'my@mydomain.com';			// 发件人邮件地址
$to1 = 'test@test.com';				// 测试单一邮件发送地址
$to2 = 'test1@test1.com, test2@test2.net';	// 测试邮件群体发送地址

require './include/global.php';

$mailsend = 1;
sendmail($to1, '标准方式发送 Email(单发)', "通过 PHP 函数及 UNIX sendmail 发送\n\n单一邮件发送至 $to1\n\n来自 $from", $from);
sendmail($to2, '标准方式发送 Email(群发)', "通过 PHP 函数及 UNIX sendmail 发送\n\n群体发送邮件发送至 $to2\n\n来自 $from", $from);

$mailsend = 2;
sendmail($to1, '通过 SMTP 服务器(SOCKET)发送 Email(单发)', "通过 SOCKET 连接 SMTP 服务器发送\n\n单一邮件发送至 $to1\n\n来自 $from", $from);
sendmail($to2, '通过 SMTP 服务器(SOCKET)发送 Email(群发)', "通过 SOCKET 连接 SMTP 服务器发送\n\n群体发送邮件发送至 $to2\n\n来自 $from", $from);

$mailsend = 3;
sendmail($to1, '通过 PHP 函数 SMTP 发送 Email(单发)', "通过 PHP 函数 SMTP 发送 Email\n\n单一邮件发送至 $to1\n\n来自 $from", $from);
sendmail($to2, '通过 PHP 函数 SMTP 发送 Email(群发)', "通过 PHP 函数 SMTP 发送\n\n群体发送邮件发送至 $to2\n\n来自 $from", $from);

?>