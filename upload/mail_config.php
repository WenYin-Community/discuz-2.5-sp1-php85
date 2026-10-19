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

$sendmail_silent = 1;		// ignore error reporting, 1=yes, 0=no
				// 屏蔽邮件发送中的全部错误提示, 1=是, 0=否

$mailsend = 1;			// Sending type	0=do not send any mails
				//		1=send via PHP mail() function and UNIX sendmail
				//		2=send via Discuz! ESMTP interface
				//		3=send via PHP mail() and SMTP(only for win32, do not support ESMTP)

				// 邮件发送方式	0=不发送任何邮件
				//		1=通过 PHP 函数及 UNIX sendmail 发送(推荐此方式)
				//		2=通过 SOCKET 连接 SMTP 服务器发送(支持 ESMTP 验证)
				//		3=通过 PHP 函数 SMTP 发送 Email(仅 win32 下有效, 不支持 ESMTP)
				//
				// 可通过 utilities/testmail.php 测试您的系统支持哪种邮件发送方式

if($mailsend == 1) {

	// Send via PHP mail() and UNIX sendmail(no extra configuration)
	// 通过 PHP 函数及 UNIX sendmail 发信(无需配置)

} elseif($mailsend == 2) {	// send via Discuz! ESMTP interface
				// 通过 Discuz! SMTP 模块发信

	$mailcfg['server'] = 'smtp.21cn.com';			// SMTP host address
								// SMTP 服务器

	$mailcfg['port'] = '25';				// SMTP 端口, 默认不需修改
								// SMTP port, leave default for most occations

	$mailcfg['auth'] = 1;					// require authentification? 1=yes, 0=no
								// 是否需要 AUTH LOGIN 验证, 1=是, 0=否

	$mailcfg['from'] = 'Discuz <myaccount@21cn.com>';	// mail from (if authentification required, do use local email address of ESMTP server)
								// 发信人地址 (如果需要验证,必须为本服务器地址)

	$mailcfg['auth_username'] = 'myaccount';		// username for authentification
								// 验证用户名

	$mailcfg['auth_password'] = 'password';			// password for authentification
								// 验证密码

} elseif($mailsend == 3) {	// send via PHP mail() and SMTP(only for win32, do not support ESMTP)
				// 通过 PHP 函数及 SMTP 服务器发信

	$mailcfg['server'] = 'smtp.your.com';		// SMTP host address
							// SMTP 服务器

	$mailcfg['port'] = '25';			// SMTP 端口, 默认不需修改
							// SMTP port

}

?>