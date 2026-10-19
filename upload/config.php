<?php

//	[DISCUZ!] config.php - basically configuration of Discuz! Board

// [EN]	Set below parameters according to your account information provided by your hosting
// [CH] 以下变量请根据空间商提供的账号参数修改,如有疑问,请联系服务器提供商

	$dbhost = 'localhost';   			// database server 
	$dbuser = 'username';   				// database username 
	$dbpw = 'password';   					// database password 
	$dbname = 'dbname';					// database name
	$adminemail = 'admin@your.com';		// admin's email
	$dbreport = 0;						// send db error report? 1=yes
					

// [EN] Special parameters

	$cookiedomain = '';					// cookie 作用域	
	$cookiepath = '/';					// cookie 作用路径		
					
	$headercharset = 0;					// 强制设置字符集,只乱码时使用
	$errorreport = 1;					// 是否报告PHP错误 0= admin(safe), 1 = every one
	$onlinehold = 900;					// 在线保持时间(单位:秒)
	$pconnect = 0;						// 数据库持久连接 0=关闭, 1=打开

// [EN] Special parameters, DO NOT modify these unless you are an expert in Discuz!
// [CH] 论坛投入使用后不能修改的变量 

	$tablepre = 'cdb_';					// 表名前缀
	$attachdir = './attachments';		// 附件保存位置 (服务器路径, 属性 777, 
										//必须为 web 可访问到的目录, 不加 "/")
	$attachurl = 'attachments';			// 附件路径 URL 地址 , 不加 "/"


// [EN] !ATTENTION! Preservation or debugging for developing
// [CH] 切勿修改以下变量,仅供程序开发调试用!
	$database = 'mysql';				// 数据库类型
	$tplrefresh = 1;					// 模板自动刷新开关 0=关闭, 1=打开
	$debug_on = 0;						//系统调试模式