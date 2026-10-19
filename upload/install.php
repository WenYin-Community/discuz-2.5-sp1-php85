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
define('DISCUZ_ROOT', dirname(__FILE__).'/');
$languageset['chinese_gb2312'] = array
	(

	'charset' => 'gb2312',

	'username' => '管理员账号:',
	'password' => '管理员密码:',
	'repeat_password' => '重复密码:',
	'admin_email' => '管理员 Email:',

	'succeed' => '成功',
	'fail' => '失败',
	'exit' => '退出安装向导',
	'enabled' => '允许',
	'writeable' => '可写',
	'unwriteable' => '不可写',
	'unlimited' => '不限',

	'env_os' => '操作系统',
	'env_php' => 'PHP 版本',
	'env_mysql' => 'MySQL 版本',
	'env_attach' => '附件上传',
	'env_diskspace' => '磁盘空间',
	'env_dir_writeable' => '目录写入',

	'init_log' => '初始化记录',
	'clear_dir' => '清空目录',
	'select_db' => '选择数据库',
	'create_table' => '建立数据表',
	
	'install_wizard' => 'Discuz! Board Installation Wizard',
	'welcome' => '<p align=left>欢迎来到 Crossday Discuz! Board 2.5 Free 安装向导。</p>',
	'current_process' => '当前状态:',
	'show_license' => 'Discuz! 用户许可协议',
	'agreement' => '请您务必仔细阅读下面的许可协议',
	'agreement_yes' => '我完全同意',
	'agreement_no' => '我不能同意',
	'configure' => '配置 config.php',
	'check_config' => '检查配置文件状态',
	'check_existence' => '存在检查',
	'check_writeable' => '可写检查',
	'edit_config' => '浏览/编辑当前配置',
	'variable' => '设置选项',
	'value' => '当前值',
	'comment' => '注释',
	'dbhost' => '数据库服务器:',
	'dbhost_comment' => '数据库服务器地址, 一般为 localhost',
	'dbuser' => '数据库用户名:',
	'dbuser_comment' => '数据库账号用户名',
	'dbpw' => '数据库密码:',
	'dbpw_comment' => '数据库账号密码',
	'dbname' => '数据库名:',
	'dbname_comment' => '数据库名称',
	'email' => '系统 Email:',
	'email_comment' => '用于发送程序错误报告',
	'tablepre' => '表名前缀:',
	'tablepre_comment' => '同一数据库安装多论坛时可改变默认',
	'tablepre_prompt' => '除非您需要在同一数据库安装多个 Discuz! \n论坛,否则,强烈建议您不要修改表名前缀.',
	'save_config' => '保存配置信息',
	'confirm_config' => '上述配置正确',
	'refresh_config' => '刷新修改结果',
	'recheck_config' => '重新检查设置',
	'check_env' => '检查当前服务器环境',
	'compare_env' => 'Discuz! 所需环境和当前服务器配置对比',
	'env_required' => 'Discuz! 所需配置',
	'env_best' => 'Discuz! 最佳配置',
	'env_current' => '当前服务器',
	'confirm_preparation' => '请确认已完成如下步骤',
	'install_note' => '安装向导提示',
	'add_admin' => '设置管理员账号',
	'start_install' => '开始安装 Discuz!',
	'installing' => '检查管理员账号信息并开始安装 Discuz!。',
	'check_admin' => '检查管理员账号',
	'check_admin_validity' => '检查信息合法性',
	'admin_username_invalid' => '用户名空, 长度超过限制或包含非法字符.',
	'admin_password_invalid' => '两次输入密码不一致.',
	'admin_email_invalid' => 'Email 地址无效',
	'admin_invalid' => '您的信息没有填写完整.',
	'fail_reason' => '失败. 原因:',
	'go_back' => '返回上一页修改',
	'init_file' => '初始化运行目录与文件',

	'config_nonexistence' => '您的 config.php 不存在, 无法继续安装, 请用 FTP 将该文件上传后再试.',
	'config_comment' => '请在下面填写您的数据库账号信息, 通常情况下不需要修改红色选项内容.',
	'config_unwriteable' => '安装向导无法写入配置文件, 请核对现有信息, 如需修改, 请通过 FTP 将改好的 config.php 上传.',

	'php_version_400' => '您的 PHP 版本小于 4.0.0, 无法使用 Discuz!。',
	'php_version_406' => '您的 PHP 版本小于 4.0.6, 无法使用头像尺寸检查和 gzip 压缩功能。',
	'attach_enabled' => '允许/最大尺寸 ',
	'attach_enabled_info' => '您可以上传附件的最大尺寸: ',
	'attach_disabled' => '不允许上传附件',
	'attach_disabled_info' => '附件上传或相关操作被服务器禁止。',
	'mysql_version_323' => '您的 MySQL 版本低于 3.23，安装无法继续进行。',
	'unwriteable_template' => '模板目录(./templates)属性非 777 或无法写入，在线编辑模板功能将无法使用。',
	'unwriteable_attach' => '附件目录(默认是 ./attachments)属性非 777 或无法写入，附件上传功能将无法使用。',
	'unwriteable_avatar' => '自定义头像目录(./customavatars)属性非 777 或无法写入，上传头像功能将无法使用。',
	'unwriteable_forumdata' => '数据目录(./forumdata)属性非 777 或无法写入，论坛运行记录和备份到数据库功能将无法使用。',
	'unwriteable_forumdata_template' => '编译模板目录(./forumdata/templates)属性非 777 或无法写入，安装无法继续进行。',
	'unwriteable_forumdata_cache' => '数据缓存目录(./forumdata/cache)属性非 777 或无法写入，安装无法继续进行。',
	'unwriteable_forumdata_accesslog' => '访问控制目录(./forumdata/accesslog)属性非 777 或无法写入，阻止 DOS 攻击功能将无法使用。',
	'tablepre_invalid' => '您指定的数据表前缀包含点字符(".")，请返回修改。',
	'db_invalid' => '指定的数据库不存在, 系统也无法自动建立, 无法安装 Discuz!.',
	'db_auto_created' => '指定的数据库不存在, 但系统已成功建立, 可以继续安装.',
	'db_not_null' => '数据库中已经安装过 Discuz!, 继续安装会清空原有数据.',
	'db_drop_table_confirm' => '继续安装会清空全部原有数据，您确定要继续吗?',

	'install_abort' => '由于您目录属性或服务器配置原因, 无法继续安装 Discuz!, 请仔细阅读安装说明.',
	'install_process' => '您的服务器可以安装和使用 Discuz!, 请进入下一步安装.',
	'install_succeed' => '恭喜您，Discuz! 安装成功！',
	'goto_forum' => '点击这里进入论坛',

	'license' => '<p>版权所有 (c) 2001-2005, Comsenz Technology Ltd<br>
保留所有权力.

<p>    感谢您选择 Discuz! 论坛产品。希望我们的努力能为您提供一个高效快速和强大的 web
论坛解决方案。

<p>    Discuz! 英文全称为 Crossday Discuz! Board，中文全称为 Discuz! 论坛，以下简称
Discuz!。
<p>    北京康盛世纪科技有限公司为 Discuz! 产品项目的开发商，依法拥有 Discuz! 软件的
著作权（中华人民共和国国家版权局著作权登记号 2003SR6623）。本协议适用于 Discuz! 
2.x 系列版本，北京康盛世纪科技有限公司拥有对本协议的最终解释权和修改权。

<p>    Discuz! 官方技术支持论坛为 http://www.Discuz.net；Discuz! 官方产品网站为
http://www.Discuz.com；北京康盛世纪科技有限公司的官方网站为 http://www.comsenz.com。

<p>    在开始安装 Discuz! 之前，请务必仔细阅读本授权文档，在您确定符合授权协议的全部
条件后，即可继续 Discuz! 论坛的安装。即：您一旦开始安装 Discuz!，即被视为完全同意
本授权协议的全部内容，如果出现纠纷，我们将根据相关法律和协议条款追究责任。

<p>    Discuz! 论坛已在中华人民共和国国家版权局登记，著作权受到法律及国际公约保护。
2.x 版本为免费软件，用户可以在完全遵守本最终用户授权协议的基础上，免费获得、安装
及在非商业用途使用本程序，而不必支付费用。

<p>    您可以查看 Discuz! 的全部源代码，也可以根据自己的需要对其进行修改，但无论如何，
即无论用途如何、是否改动、改动程度如何，只要 Discuz! 程序的任何部分被包含在您修改
后的系统中，都必须保留页脚处的 Discuz! 名称和 Comsenz Technology Ltd 
(http://www.comsenz.com) 的链接。您修改后的代码，在没有获得北京康盛世纪科技有限公司
书面许可的情况下，严禁公开发布或发售。

<p>    用户出于自愿而使用本软件，我们不承诺对提供任何形式的技术支持、使用担保，也不
承担任何因使用本软件而产生问题的相关责任。

<p>    对于且仅对于非营利性个人用户，本版本 Discuz! 是开放源代码的免费软件，欢迎您在原样
完整保留全部版权信息和说明档的前提下，传播和转载本程序。在未购买商业授权前，严禁将本
本软件用于商业用途或盈利性站点，即：企业、营利性社会团体、从事经营性互联网业务的公司
与个人、对最终用户收取费用的收费网站，均需要购买商业版本授权才能使用本软件，有关商业
版本购买事宜请访问官方网站（http://www.discuz.com）。同时欢迎对 Discuz! 感兴趣并有实力
的团体或个人对 Discuz! 的开发提供支持。

<p>    安装 Discuz! 建立在完全同意本授权协议的基础之上，因此而产生的纠纷，违反本协议的
一方将承担全部民事与刑事责任。',

	'preparation' => '<li>将压缩包中 Discuz! 目录下全部文件和目录上传到服务器.</li><li>修改服务器上的 config.php 文件以适合您的配置, 有关数据库账号信息请咨询您的空间服务提供商.</li><li>如果您使用非 WINNT 系统请修改以下属性:<br>&nbsp; &nbsp; <b>./templates</b> 目录 777;&nbsp; &nbsp; <b>./attachments</b> 目录 777;&nbsp; &nbsp; <b>./customavatars</b> 目录 777;&nbsp; &nbsp; <b>./forumdata</b> 目录 777;<br><b>&nbsp; &nbsp; ./forumdata/cache</b> 目录 777;&nbsp; &nbsp; <b>./forumdata/templates</b> 目录 777;&nbsp; &nbsp; <b>./forumdata/accesslogs</b> 目录 777;<br></li><li>确认 URL 中 /attachments 可以访问服务器目录 ./attachments 内容.</li>',

	);

error_reporting(7);
// PHP 8 起 set_magic_quotes_runtime() 已移除, 安装程序无需再做魔术引号设置
define('IN_DISCUZ', TRUE);
$action		= isset($_POST['action']) && $_POST['action'] ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$language	= isset($_POST['language']) && $_POST['language'] ? $_POST['language'] : (isset($_GET['language']) ? $_GET['language'] : 'chinese_gb2312');
$PHP_SELF	= (isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '')).'?language='.urlencode($language);

@set_time_limit(1000);
@include DISCUZ_ROOT.'./config.php';

header('Content-Type: text/html; charset='.(empty($lang['charset']) ? 'gb2312' : $lang['charset']));
$version = '2.5F_SP1';

function loginit($log) {
	global $lang;

	echo $lang['init_log'].' '.$log;
	$fp = @fopen(DISCUZ_ROOT.'./forumdata/'.$log.'.php','w');
	@fwrite($fp, "<?PHP exit(\"Access Denied\"); ?>\n");
	@fclose($fp);
	result();
}

function runquery($sql) {
	global $lang, $tablepre, $db;

	$sql = str_replace("\r", "\n", str_replace(' cdb_', ' '.$tablepre, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		$ret[$num] = '';
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') ? NULL : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				echo $lang['create_table'].' '.$name.' ... <font color="#0000EE">'.$lang['succeed'].'</font><br>';
			}
			$db->query($query);
		}
	}
}

function result($result = 1, $output = 1) {
	global $lang;

	if($result) {
		$text = '... <font color="#0000EE">'.$lang['succeed'].'</font><br>';
		if(!$output) {
			return $text;
		}
		echo $text;
	} else {
		$text = '... <font color="#FF0000">'.$lang['fail'].'</font><br>';
		if(!$output) {
			return $text;
		}
		echo $text;
	}
}

function dir_writeable($dir) {
	$writeable = 0;
	$dir = DISCUZ_ROOT.$dir;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.test", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.test");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function dir_clear($dir) {
	global $lang;
	echo $lang['clear_dir'].' '.$dir;
	$dir = DISCUZ_ROOT.$dir;
	$directory = dir($dir);
	while($entry = $directory->read()) {
		$filename = $dir.'/'.$entry;
		if(is_file($filename)) {
			@unlink($filename);
		}
	}
	$directory->close();
	result();
}


?>
<html>
<head>
<title>Discuz! Board Installation Wizard</title>
<style>
A:visited	{COLOR: #3A4273; TEXT-DECORATION: none}
A:link		{COLOR: #3A4273; TEXT-DECORATION: none}
A:hover		{COLOR: #3A4273; TEXT-DECORATION: underline}
body,table,td	{COLOR: #000000; FONT-FAMILY: Tahoma, Verdana, Arial; FONT-SIZE: 12px; LINE-HEIGHT: 20px; scrollbar-base-color: #F8F8F8; scrollbar-arrow-color: #5C5C8D}
input		{COLOR: #085878; FONT-FAMILY: Tahoma, Verdana, Arial; FONT-SIZE: 12px;  color: #00000; scrollbar-base-color: #F8F8F8; scrollbar-arrow-color: #5C5C8D}
.install	{FONT-FAMILY: Arial, Verdana; FONT-SIZE: 20px; FONT-WEIGHT: bold; COLOR: #000000}
</style>
</head>
<?

if(!in_array($language, array('chinese_gb2312', 'chinese_big5', 'english'))) {

?>
<body bgcolor="#9EB6D8">
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" align="center">
<tr><td valign="middle" align="center">

<table cellpadding="0" cellspacing="0" border="0" align="center">
  <tr align="center" valign="middle">
    <td bgcolor="#000000">
    <table cellpadding="10" cellspacing="1" border="0" width="600" height="100%" align="center">
    <tr>
      <td valign="middle" align="center" bgcolor="#FFFFFF">
        <br><br><b>Discuz! Board Installation Wizard</b><br><br>Please choose your prefered language<br><br><center><a href="?language=chinese_gb2312">[Simplfied Chinese]</a> &nbsp; <a href="#">[Traditional Chinese]</a> &nbsp; <a href="#">[English]</a><br><br>
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>

</td></td></table>
</body>
</html>
<?

	exit();
} else {
	$lang = $languageset[$language];

?>
<body bgcolor="#9EB6D8" text="#000000">
<table width="95%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
  <tr>
    <td>
      <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <td class="install" height="30" valign="bottom"><font color="#FF0000">&gt;&gt;</font> 
            <?=$lang['install_wizard']?></td>
        </tr>
        <tr>
          <td> 
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td align="center"> 
            <b><?=$lang['welcome']?></b>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
<?

}

if(!$action) {

	//$discuz_license = str_replace('  ', '&nbsp; ', nl2br(trim($lang['license'])));

?>
        <tr> 
          <td><b><?=$lang['current_process']?> </b><font color="#0000EE"><?=$lang['show_license']?></font></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['agreement']?></font></b></td>
        </tr>
        <tr>
          <td><br>
            <table width="90%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr>
                <td bgcolor="#F8F8F8">
                  <table width="99%" cellspacing="1" border="0" align="center">
                    <tr>
                      <td>
                        <?=$lang['license']?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="config">
              <input type="submit" name="submit" value="<?=$lang['agreement_yes']?>" style="height: 25">&nbsp;
              <input type="button" name="exit" value="<?=$lang['agreement_no']?>" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

} elseif($action == 'config') {

	$exist_error = FALSE;
	$write_error = FALSE;
	if(file_exists('./config.php')) {
		$fileexists = result(1, 0);
	} else {
		$fileexists = result(0, 0);
		$exist_error = TRUE;
	}
	if(is_writeable('./config.php')) {
		$filewriteable = result(1, 0);
	} else {
		$filewriteable = result(0, 0);
		$write_error = TRUE;
	}
	if($exist_error) {
		$config_info = $lang['config_nonexistence'];
	} elseif(!$write_error) {
		$config_info = $lang['config_comment'];
	} elseif($write_error) {
		$config_info = $lang['config_unwriteable'];
	}

?>
        <tr> 
          <td><b><?=$lang['current_process']?> </b><font color="#0000EE"><?=$lang['configure']?></font></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['check_config']?></font></b></td>
        </tr>
        <tr>
          <td>config.php <?=$lang['check_existence']?> <?=$fileexists?></td>
        </tr>
        <tr>
          <td>config.php <?=$lang['check_writeable']?> <?=$filewriteable?></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['edit_config']?></font></b></td>
        </tr>
        <tr>
          <td align="center"><br><?=$config_info?></td>
        </tr>
<?

	if(!$exist_error) {

		if(!$write_error) {

			$dbhost = 'localhost';
			$dbuser = 'dbuser';
			$dbpw = 'dbpw';
			$dbname = 'dbname';
			$adminemail = 'admin@domain.com';
			$tablepre = 'cdb_';

			@include './config.php';

?>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <table width="650" cellspacing="1" bgcolor="#698CC3" border="0" align="center">
                <tr bgcolor="#698CC3">
                  <td align="center" width="20%" style="color: #FFFFFF"><?=$lang['variable']?></td>
                  <td align="center" width="30%" style="color: #FFFFFF"><?=$lang['value']?></td>
                  <td align="center" width="50%" style="color: #FFFFFF"><?=$lang['comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8" style="color: #FF0000">&nbsp;<?=$lang['dbhost']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbhost" value="<?=$dbhost?>" size="30"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbhost_comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbuser']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbuser" value="<?=$dbuser?>" size="30"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbuser_comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbpw']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="password" name="dbpw" value="<?=$dbpw?>" size="30"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbpw_comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbname']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbname" value="<?=$dbname?>" size="30"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['dbname_comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['email']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="adminemail" value="<?=$adminemail?>" size="30"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['email_comment']?></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8" style="color: #FF0000">&nbsp;<?=$lang['tablepre']?></td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="tablepre" value="<?=$tablepre?>" size="30" onClick="javascript: alert('<?=$lang['install_note']?>:\n\n<?=$lang['tablepre_prompt']?>');"></td>
                  <td bgcolor="#F8F8F8">&nbsp;<?=$lang['tablepre_comment']?></td>
                </tr>
              </table>
              <br>
              <input type="hidden" name="action" value="environment">
              <input type="hidden" name="saveconfig" value="1">
              <input type="submit" name="submit" value="<?=$lang['save_config']?>" style="height: 25">
              <input type="button" name="exit" value="<?=$lang['exit']?>" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

		} else {

			@include './config.php';

?>
        <tr>
          <td>
            <br>
            <table width="60%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr bgcolor="#698CC3">
                <td align="center" style="color: #FFFFFF"><?=$lang['variable']?></td>
                <td align="center" style="color: #FFFFFF"><?=$lang['value']?></td>
                <td align="center" style="color: #FFFFFF"><?=$lang['comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$dbhost</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbhost?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['dbhost_comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$dbuser</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbuser?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['dbuser_comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$dbpw</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbpw?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['dbpw_comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$dbname</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbname?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['dbname_comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$adminemail</td>
                <td bgcolor="#EEEEF6" align="center"><?=$adminemail?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['email_comment']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">$tablepre</td>
                <td bgcolor="#EEEEF6" align="center"><?=$tablepre?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['tablepre_comment']?></td>
              </tr>
            </table>
            <br>
          </td>
        </tr>
        <tr>
          <td align="center">
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="environment">
              <input type="submit" name="submit" value="<?=$lang['confirm_config']?>" style="height: 25">
              <input type="button" name="exit" value="<?=$lang['refresh_config']?>" style="height: 25" onclick="javascript: window.location=('<?=$PHP_SELF?>&action=config');">
            </form>
          </td>
        </tr>
<?

		}

	} else {

?>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="config">
              <input type="submit" name="submit" value="<?=$lang['recheck_config']?>" style="height: 25">
              <input type="button" name="exit" value="<?=$lang['exit']?>" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

	}

} elseif($action == 'environment') {

	if(!empty($_POST['saveconfig']) && is_writeable('./config.php')) {

		$dbhost = $_POST['dbhost'];
		$dbuser = $_POST['dbuser'];
		$dbpw = $_POST['dbpw'];
		$dbname = $_POST['dbname'];
		$adminemail = $_POST['adminemail'];
		$tablepre = $_POST['tablepre'];

		$fp = fopen('./config.php', 'r');
		$configfile = fread($fp, filesize('./config.php'));
		fclose($fp);

		$configfile = preg_replace("/[$]dbhost\s*\=\s*[\"'].*?[\"']/is", "\$dbhost = '$dbhost'", $configfile);
		$configfile = preg_replace("/[$]dbuser\s*\=\s*[\"'].*?[\"']/is", "\$dbuser = '$dbuser'", $configfile);
		$configfile = preg_replace("/[$]dbpw\s*\=\s*[\"'].*?[\"']/is", "\$dbpw = '$dbpw'", $configfile);
		$configfile = preg_replace("/[$]dbname\s*\=\s*[\"'].*?[\"']/is", "\$dbname = '$dbname'", $configfile);
		$configfile = preg_replace("/[$]adminemail\s*\=\s*[\"'].*?[\"']/is", "\$adminemail = '$adminemail'", $configfile);
		$configfile = preg_replace("/[$]tablepre\s*\=\s*[\"'].*?[\"']/is", "\$tablepre = '$tablepre'", $configfile);

		$fp = fopen('./config.php', 'w');
		fwrite($fp, trim($configfile));
		fclose($fp);

	}

	include './config.php';
	include './include/db_'.$database.'.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

	$msg = '';
	$quit = FALSE;

	$curr_os = PHP_OS;

	$curr_php_version = PHP_VERSION;
	if($curr_php_version < '4.0.0') {
		$msg .= "<font color=\"#FF0000\">{$lang['php_version_400']}</font>\t";
		$quit = TRUE;
	} elseif($curr_php_version < '4.0.6') {
		$msg .= "<font color=\"#FF0000\">{$lang['php_version_406']}</font>\t";
	}

	if(@ini_get('file_uploads')) {
		$max_size = @ini_get('upload_max_filesize');
		$curr_upload_status = $lang['attach_enabled'].$max_size;
		$msg .= $lang['attach_enabled_info'].$max_size."\t";
	} else {
		$curr_upload_status = $lang['attach_disabled'];
		$msg .= "<font color=\"#FF0000\">{$lang['attach_disabled_info']}</font>\t";
	}

	$query = $db->query("SELECT VERSION()");
	$curr_mysql_version = $db->result($query, 0);
	if($curr_mysql_version < '3.23') {
		$msg .= "<font color=\"#FF0000\">{$lang['mysql_version_323']}</font>\t";
		$quit = TRUE;
	}

	$curr_disk_space = intval(diskfreespace('.') / (1024 * 1024)).'M';

	if(dir_writeable('./templates')) {
		$curr_tpl_writeable = $lang['writeable'];
	} else {
		$curr_tpl_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_template']}</font>\t";
	}

	if(dir_writeable('./customavatars')) {
		$curr_avatar_writeable = $lang['writeable'];
	} else {
		$curr_avatar_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_avatar']}</font>\t";
	}

	if(dir_writeable($attachdir)) {
		$curr_attach_writeable = $lang['writeable'];
	} else {
		$curr_attach_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_attach']}</font>\t";
	}

	if(dir_writeable('./forumdata')) {
		$curr_data_writeable = $lang['writeable'];
	} else {
		$curr_data_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_forumdata']}</font>\t";
	}

	if(dir_writeable('./forumdata/templates')) {
		$curr_template_writeable = $lang['writeable'];
	} else {
		$curr_template_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_forumdata_template']}</font>\t";
		$quit = TRUE;
	}

	if(dir_writeable('./forumdata/cache')) {
		$curr_cache_writeable = $lang['writeable'];
	} else {
		$curr_cache_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_forumdata_cache']}</font>\t";
		$quit = TRUE;
	}

	if(dir_writeable('./forumdata/accesslogs')) {
		$curr_accesslog_writeable = $lang['writeable'];
	} else {
		$curr_accesslog_writeable = $lang['unwriteable'];
		$msg .= "<font color=\"#FF0000\">{$lang['unwriteable_forumdata_accesslog']}</font>\t";
	}

	if(strstr($tablepre, '.')) {
		$msg .= "<font color=\"#FF0000\">{$lang['tablepre_invalid']}</font>\t";
		$quit = TRUE;
	}

	$db->select_db($dbname);
	if($db->error()) {
		$db->query("CREATE DATABASE $dbname DEFAULT CHARACTER SET gbk");
		if($db->error()) {
			$msg .= "<font color=\"#FF0000\">{$lang['db_invalid']}</font>\t";
			$quit = TRUE;
		} else {
			$db->select_db($dbname);
			$msg .= "{$lang['db_auto_created']}\t";
		}
	}
	// 表继承数据库默认字符集, 保证中文以 GBK 存取
	$db->query("ALTER DATABASE $dbname DEFAULT CHARACTER SET gbk", 'SILENT');

	$query = $db->query("SELECT COUNT(*) FROM $tablepre"."settings", 'SILENT');
	if(!$db->error()) {
		$msg .= "<font color=\"#FF0000\">{$lang['db_not_null']}</font>\t";
		$alert = " onSubmit=\"return confirm('{$lang['db_drop_table_confirm']}');\"";
	} else {
		$alert = '';
	}

	if($quit) {
		$msg .= "<font color=\"#FF0000\">{$lang['install_abort']}</font>";
	} else {
		$msg .= $lang['install_process'];
	}
?>
        <tr>
          <td><b><?=$lang['current_process']?> </b><font color="#0000EE"><?=$lang['check_env']?></font></td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['compare_env']?></font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <table width="80%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr bgcolor="#698CC3">
                <td align="center"></td>
                <td align="center" style="color: #FFFFFF"><?=$lang['env_required']?></td>
                <td align="center" style="color: #FFFFFF"><?=$lang['env_best']?></td>
                <td align="center" style="color: #FFFFFF"><?=$lang['env_current']?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['env_os']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center">UNIX/Linux/FreeBSD</td>
                <td bgcolor="#F8F8F8" align="center"><?=$curr_os?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['env_php']?></td>
                <td bgcolor="#EEEEF6" align="center">4.0.0+</td>
                <td bgcolor="#F8F8F8" align="center">4.0.6+</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_php_version?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['env_attach']?></td>
                <td bgcolor="#EEEEF6" align="center"3><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['enabled']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_upload_status?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['env_mysql']?></td>
                <td bgcolor="#EEEEF6" align="center">3.23+</td>
                <td bgcolor="#F8F8F8" align="center">4.0.13</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_mysql_version?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['env_diskspace']?></td>
                <td bgcolor="#EEEEF6" align="center">2M+</td>
                <td bgcolor="#F8F8F8" align="center">50M+</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_disk_space?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./templates <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_tpl_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center"><?=$attachdir?> <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_attach_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./customavatars <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_avatar_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./forumdata <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['unlimited']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_data_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./forumdata/templates <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_template_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./forumdata/cache <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_cache_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#F8F8F8" align="center">./forumdata/accesslogs <?=$lang['env_dir_writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#F8F8F8" align="center"><?=$lang['writeable']?></td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_accesslog_writeable?></td>
              </tr>
            </table>
            <br>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['confirm_preparation']?></font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <ol><?=$lang['preparation']?></ol>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['install_note']?></font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <ol>
<?

	foreach(explode("\t", $msg) as $message) {
		echo "              <li>$message</li>\n";
	}
	echo"            </ol>\n";

	if($quit) {

?>
            <center>
            <input type="button" name="refresh" value="<?=$lang['recheck_config']?>" style="height: 25" onclick="javascript: window.location=('<?=$PHP_SELF?>&action=environment');">&nbsp;
            <input type="button" name="exit" value="<?=$lang['exit']?>" style="height: 25" onclick="javascript: window.close();">
            </center>
<?

	} else {

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['add_admin']?></font></b></td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>"<?=$alert?>>
              <table width="350" cellspacing="1" bgcolor="#000000" border="0" align="center">
                <tr>
                  <td bgcolor="#F8F8F8" width="40%">&nbsp;<?=$lang['username']?></td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="text" name="username" value="Crossday" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8" width="40%">&nbsp;<?=$lang['admin_email']?></td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="text" name="email" value="name@domain.com" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8" width="40%">&nbsp;<?=$lang['password']?></td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="password" name="password1" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#F8F8F8" width="40%">&nbsp;<?=$lang['repeat_password']?></td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="password" name="password2" size="30"></td>
                </tr>
              </table>
              <br>
              <input type="hidden" name="action" value="install">
              <input type="submit" name="submit" value="<?=$lang['start_install']?>" style="height: 25" >&nbsp;
              <input type="button" name="exit" value="<?=$lang['exit']?>" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>

<?

	}	

} elseif($action == 'install') {

	$username = $_POST['username'];
	$email = $_POST['email'];
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];

?>
        <tr>
          <td><b><?=$lang['current_process']?> </b><font color="#0000EE"> <?=$lang['installing']?></font></td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['check_admin']?></font></b></td>
        </tr>
        <tr>
          <td><?=$lang['check_admin_validity']?>
<?

	$msg = '';
	if($username && $email && $password1 && $password2) {
		if($password1 != $password2) {
			$msg = $lang['admin_password_invalid'];
		} elseif(strlen($username) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $username)) {
			$msg = $lang['admin_username_invalid'];
		} elseif(!strstr($email, '@') || $email != stripslashes($email) || $email != htmlspecialchars($email, ENT_COMPAT, 'ISO-8859-1')) {
			$msg = $lang['admin_email_invalid'];
		}
	} else {
		$msg = $lang['admin_invalid'];
	}

	if($msg) { 

?>
            ... <font color="#FF0000"><?=$lang['fail_reason']?> <?=$msg?></font></td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <input type="button" name="back" value="<?=$lang['go_back']?>" onclick="javascript: history.go(-1);">
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td align="center">
            <b style="font-size: 11px">Powered by <a href="http://discuz.net" target="_blank">Discuz! <?=$version?></a> , &nbsp; Copyright &copy; <a href="http://www.discuz.com" target=\"_blank\">Comsenz Technology Ltd </a>, 2001-2005</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
</body>
</html>

<?

		exit();
	} else {
		echo result(1, 0)."</td>\n";
		echo"        </tr>\n";
	}

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> <?=$lang['select_db']?></font></b></td>
        </tr>
<?
	include './config.php';
	include './include/db_'.$database.'.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);

echo"        <tr>\n";
echo"          <td>{$lang['select_db']} $dbname ".result(1, 0)."</td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";
echo"            <hr noshade align=\"center\" width=\"100%\" size=\"1\">\n";
echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td><b><font color=\"#FF0000\">&gt;</font><font color=\"#000000\"> {$lang['create_table']}</font></b></td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";

	$sql = <<<EOT
DROP TABLE IF EXISTS cdb_access;
CREATE TABLE cdb_access (
  uid mediumint(8) unsigned NOT NULL default '0',
  fid smallint(6) unsigned NOT NULL default '0',
  allowview tinyint(1) NOT NULL default '0',
  allowpost tinyint(1) NOT NULL default '0',
  allowreply tinyint(1) NOT NULL default '0',
  allowgetattach tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (uid,fid)
);

DROP TABLE IF EXISTS cdb_admingroups;
CREATE TABLE cdb_admingroups (
  admingid smallint(3) unsigned NOT NULL auto_increment,
  admintitle char(30) NOT NULL default '',
  adminglobal tinyint(1) NOT NULL default '0',
  alloweditpost tinyint(1) NOT NULL default '0',
  alloweditpoll tinyint(1) NOT NULL default '0',
  allowdelpost tinyint(1) NOT NULL default '0',
  allowmassprune tinyint(1) NOT NULL default '0',
  allowcensorword tinyint(1) NOT NULL default '0',
  allowviewip tinyint(1) NOT NULL default '0',
  allowbanip tinyint(1) NOT NULL default '0',
  allowedituser tinyint(1) NOT NULL default '0',
  allowbanuser tinyint(1) NOT NULL default '0',
  allowpostannounce tinyint(1) NOT NULL default '0',
  allowviewlog tinyint(1) NOT NULL default '0',
  allowhighlight tinyint(1) NOT NULL default '0',
  allowdigest tinyint(1) NOT NULL default '0',
  allowclose tinyint(1) NOT NULL default '0',
  allowmove tinyint(1) NOT NULL default '0',
  allowtop tinyint(1) NOT NULL default '0',
  allowmerge tinyint(1) NOT NULL default '0',
  allowsplit tinyint(1) NOT NULL default '0',
  disablepostctrl tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (admingid)
) ;


INSERT INTO cdb_admingroups VALUES (1, 'Administrator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO cdb_admingroups VALUES (2, 'SuperModerator', 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO cdb_admingroups VALUES (3, 'Moderator', 0, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1);
    

DROP TABLE IF EXISTS cdb_adminsessions;
CREATE TABLE cdb_adminsessions (
  uid mediumint(8) NOT NULL default '0',
  ip char(20) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  errorlog tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (uid,ip,dateline)
);

DROP TABLE IF EXISTS cdb_announcements;
CREATE TABLE cdb_announcements (
  id smallint(6) unsigned NOT NULL auto_increment,
  author varchar(15) NOT NULL default '',
  subject varchar(250) NOT NULL default '',
  posturl varchar(250) NOT NULL default '',
  displayorder tinyint(3) NOT NULL default '0',
  starttime int(10) unsigned NOT NULL default '0',
  endtime int(10) unsigned NOT NULL default '0',
  message text NOT NULL,
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_attachments;
CREATE TABLE cdb_attachments (
  aid mediumint(8) unsigned NOT NULL auto_increment,
  tid mediumint(8) unsigned NOT NULL default '0',
  pid int(10) unsigned NOT NULL default '0',
  uid mediumint(8) unsigned NOT NULL default '0',
  creditsrequire smallint(6) unsigned NOT NULL default '0',
  filename char(100) NOT NULL default '',
  filetype char(50) NOT NULL default '',
  filesize int(10) unsigned NOT NULL default '0',
  attachment char(100) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  downloads smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (aid),
  KEY tid (tid),
  KEY pid (pid),
  KEY uid (uid)

);

DROP TABLE IF EXISTS cdb_attachtypes;
CREATE TABLE cdb_attachtypes (
  id smallint(6) unsigned NOT NULL auto_increment,
  extension char(10) NOT NULL default '',
  maxsize int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_banned;
CREATE TABLE cdb_banned (
  id smallint(6) unsigned NOT NULL auto_increment,
  ip1 smallint(3) NOT NULL default '0',
  ip2 smallint(3) NOT NULL default '0',
  ip3 smallint(3) NOT NULL default '0',
  ip4 smallint(3) NOT NULL default '0',
  admin varchar(15) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  expiration int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_bbcodes;
CREATE TABLE cdb_bbcodes (
  id mediumint(8) unsigned NOT NULL auto_increment,
  available tinyint(1) NOT NULL default '0',
  tag varchar(100) NOT NULL default '',
  replacement text NOT NULL,
  example varchar(255) NOT NULL default '',
  explanation text NOT NULL,
  params tinyint(1) unsigned NOT NULL default '1',
  nest tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_buddys;
CREATE TABLE cdb_buddys (
  uid mediumint(8) unsigned NOT NULL default '0',
  buddyid mediumint(8) unsigned NOT NULL default '0',
  KEY uid (uid)
);

DROP TABLE IF EXISTS cdb_caches;
CREATE TABLE cdb_caches (
  cid int(10) unsigned NOT NULL auto_increment,
  ckey varchar(80) NOT NULL default '',
  cval text NOT NULL,
  dateline int(10) NOT NULL default '0',
  extr varchar(80) NOT NULL default '',
  PRIMARY KEY  (cid),
  KEY ckey (ckey)
);

DROP TABLE IF EXISTS cdb_failedlogins;
CREATE TABLE cdb_failedlogins (
  ip char(15) NOT NULL default '',
  count tinyint(1) unsigned NOT NULL default '0',
  lastupdate int(10) unsigned NOT NULL default '0'
);

DROP TABLE IF EXISTS cdb_favorites;
CREATE TABLE cdb_favorites (
  uid mediumint(8) unsigned NOT NULL default '0',
  tid mediumint(8) unsigned NOT NULL default '0',
  KEY tid (tid)
);

DROP TABLE IF EXISTS cdb_forumlinks;
CREATE TABLE cdb_forumlinks (
  id smallint(6) unsigned NOT NULL auto_increment,
  displayorder tinyint(3) NOT NULL default '0',
  name varchar(100) NOT NULL default '',
  url varchar(200) NOT NULL default '',
  note varchar(200) NOT NULL default '',
  logo varchar(100) NOT NULL default '',
  PRIMARY KEY  (id)
);

INSERT INTO cdb_forumlinks VALUES (1, 0, 'Discuz! Board', 'http://www.discuz.com', 'Discuz! 官方网站，提供最新 Discuz! 软件下载、使用讨论与技术交流', 'images/logo.gif');
INSERT INTO cdb_forumlinks VALUES (2, 0, 'Free Discuz!', 'http://www.freediscuz.net', 'Free Discuz!，免费 Discuz! 插件与风格', 'http://www.freediscuz.net/bbs/images/logo4.gif');

DROP TABLE IF EXISTS cdb_forums;
CREATE TABLE cdb_forums (
  fid smallint(6) unsigned NOT NULL auto_increment,
  fup smallint(6) unsigned NOT NULL default '0',
  type enum('group','forum','sub') NOT NULL default 'forum',
  icon char(50) NOT NULL default '',
  name char(255) NOT NULL default '',
  description char(255) NOT NULL default '',
  status tinyint(1) NOT NULL default '0',
  displayorder tinyint(3) NOT NULL default '0',
  moderator char(255) NOT NULL default '',
  styleid smallint(6) unsigned NOT NULL default '0',
  threads mediumint(8) unsigned NOT NULL default '0',
  posts mediumint(8) unsigned NOT NULL default '0',
  lastpost char(110) NOT NULL default '',
  allowsmilies tinyint(1) NOT NULL default '0',
  allowhtml tinyint(1) NOT NULL default '0',
  allowbbcode tinyint(1) NOT NULL default '0',
  allowimgcode tinyint(1) NOT NULL default '0',
  postcredits tinyint(3) NOT NULL default '-1',
  replycredits tinyint(3) NOT NULL default '-1',
  password char(12) NOT NULL default '',
  viewperm char(100) NOT NULL default '',
  postperm char(100) NOT NULL default '',
  replyperm char(100) NOT NULL default '',
  getattachperm char(100) NOT NULL default '',
  PRIMARY KEY  (fid),
  KEY forum (status,type,displayorder)
);

INSERT INTO cdb_forums VALUES (1, 0, 'forum', '', '默认论坛', '', 1, 0, '', 0, 0, 0, '', 1, 0, 1, 1, -1, -1, '', '', '', '', '');

DROP TABLE IF EXISTS cdb_karmalog;
CREATE TABLE cdb_karmalog (
  kid int(10) NOT NULL auto_increment,
  uid mediumint(8) unsigned NOT NULL default '0',
  pid int(10) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  score tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (kid),
  KEY pid (pid),
  KEY dateline (dateline)
);

DROP TABLE IF EXISTS cdb_members;
CREATE TABLE cdb_members (
  uid mediumint(8) unsigned NOT NULL auto_increment,
  username varchar(15) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  secques varchar(8) NOT NULL default '',
  gender tinyint(1) NOT NULL default '0',
  adminid smallint(3) NOT NULL default '0',
  groupid smallint(6) unsigned NOT NULL default '0',
  regip varchar(15) NOT NULL default '',
  regdate int(10) unsigned NOT NULL default '0',
  lastip varchar(15) NOT NULL default '',
  lastvisit int(10) unsigned NOT NULL default '0',
  lastactivity int(10) unsigned NOT NULL default '0',
  lastpost int(10) unsigned NOT NULL default '0',
  postnum smallint(6) unsigned NOT NULL default '0',
  credit int(10) NOT NULL default '0',
  extracredit int(10) NOT NULL default '0',
  email varchar(60) NOT NULL default '',
  site varchar(75) NOT NULL default '',
  icq varchar(12) NOT NULL default '',
  oicq varchar(12) NOT NULL default '',
  yahoo varchar(40) NOT NULL default '',
  msn varchar(40) NOT NULL default '',
  location varchar(30) NOT NULL default '',
  bday date NOT NULL default '0000-00-00',
  bio text NOT NULL,
  avatar varchar(100) NOT NULL default '',
  avatarwidth tinyint(3) unsigned NOT NULL default '0',
  avatarheight tinyint(3) unsigned NOT NULL default '0',
  signature text NOT NULL,
  customstatus varchar(30) NOT NULL default '',
  tpp tinyint(3) unsigned NOT NULL default '0',
  ppp tinyint(3) unsigned NOT NULL default '0',
  styleid smallint(6) unsigned NOT NULL default '0',
  dateformat varchar(10) NOT NULL default '',
  timeformat varchar(5) NOT NULL default '',
  showemail tinyint(1) NOT NULL default '0',
  newsletter tinyint(1) NOT NULL default '0',
  invisible tinyint(1) NOT NULL default '0',
  timeoffset char(4) NOT NULL default '',
  ignorepm text NOT NULL,
  newpm tinyint(1) NOT NULL default '0',
  accessmasks tinyint(1) NOT NULL default '0',
  identifying varchar(20) NOT NULL default '',
  PRIMARY KEY  (uid),
  KEY username (username)
);

DROP TABLE IF EXISTS cdb_onlinelist;
CREATE TABLE cdb_onlinelist (
  groupid smallint(6) unsigned NOT NULL default '0',
  displayorder tinyint(3) NOT NULL default '0',
  title varchar(30) NOT NULL default '',
  url varchar(30) NOT NULL default ''
);

INSERT INTO cdb_onlinelist VALUES (1, 1, '管理员', 'online_admin.gif');
INSERT INTO cdb_onlinelist VALUES (2, 2, '超级版主', 'online_supermod.gif');
INSERT INTO cdb_onlinelist VALUES (3, 3, '版主', 'online_moderator.gif');
INSERT INTO cdb_onlinelist VALUES (0, 4, '注册用户', 'online_member.gif');

DROP TABLE IF EXISTS cdb_pms;
CREATE TABLE cdb_pms (
  pmid int(10) unsigned NOT NULL auto_increment,
  msgfrom varchar(15) NOT NULL default '',
  msgfromid mediumint(8) unsigned NOT NULL default '0',
  msgtoid mediumint(8) unsigned NOT NULL default '0',
  folder enum('inbox','outbox') NOT NULL default 'inbox',
  new tinyint(1) NOT NULL default '0',
  subject varchar(75) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  message text NOT NULL,
  PRIMARY KEY  (pmid),
  KEY msgtoid (msgtoid),
  KEY msgfromid (msgfromid)
);

DROP TABLE IF EXISTS cdb_polls;
CREATE TABLE cdb_polls (
  tid mediumint(8) unsigned NOT NULL default '0',
  pollopts mediumtext NOT NULL,
  PRIMARY KEY  (tid)
);

DROP TABLE IF EXISTS cdb_posts;
CREATE TABLE cdb_posts (
  pid int(10) unsigned NOT NULL auto_increment,
  fid smallint(6) unsigned NOT NULL default '0',
  tid mediumint(8) unsigned NOT NULL default '0',
  aid tinyint(1) NOT NULL default '0',
  author varchar(15) NOT NULL default '',
  authorid mediumint(8) unsigned NOT NULL default '0',
  subject varchar(80) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  message mediumtext NOT NULL,
  useip varchar(15) NOT NULL default '',
  usesig tinyint(1) NOT NULL default '0',
  bbcodeoff tinyint(1) NOT NULL default '0',
  smileyoff tinyint(1) NOT NULL default '0',
  parseurloff tinyint(1) NOT NULL default '0',
  rate smallint(6) NOT NULL default '0',
  ratetimes tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (pid),
  KEY fid (fid),
  KEY dateline (dateline),
  KEY authorid (authorid),
  KEY tid (tid,dateline)
);

DROP TABLE IF EXISTS cdb_ranks;
CREATE TABLE cdb_ranks (
  rankid smallint(6) unsigned NOT NULL auto_increment,
  ranktitle varchar(30) NOT NULL default '',
  postshigher smallint(6) unsigned NOT NULL default '0',
  stars tinyint(3) NOT NULL default '0',
  color varchar(7) NOT NULL default '',
  PRIMARY KEY  (rankid)
);

INSERT INTO cdb_ranks VALUES (1, 'Beginner', 0, 1, '');
INSERT INTO cdb_ranks VALUES (2, 'Poster', 50, 2, '');
INSERT INTO cdb_ranks VALUES (3, 'Cool Poster', 300, 5, '');
INSERT INTO cdb_ranks VALUES (4, 'Writer', 1000, 4, '');
INSERT INTO cdb_ranks VALUES (5, 'Excellent Writer', 3000, 5, '');

DROP TABLE IF EXISTS cdb_searchindex;
CREATE TABLE cdb_searchindex (
  searchid int(10) unsigned NOT NULL auto_increment,
  keywords varchar(255) NOT NULL default '',
  searchstring varchar(255) NOT NULL default '',
  useip varchar(15) NOT NULL default '',
  uid mediumint(10) unsigned NOT NULL default '0',
  dateline int(10) unsigned NOT NULL default '0',
  expiration int(10) unsigned NOT NULL default '0',
  threads smallint(6) unsigned NOT NULL default '0',
  tids text NOT NULL,
  PRIMARY KEY  (searchid)
);

DROP TABLE IF EXISTS cdb_sessions;
CREATE TABLE cdb_sessions (
  sid char(6) binary NOT NULL default '',
  ip1 tinyint(3) unsigned NOT NULL default '0',
  ip2 tinyint(3) unsigned NOT NULL default '0',
  ip3 tinyint(3) unsigned NOT NULL default '0',
  ip4 tinyint(3) unsigned NOT NULL default '0',
  uid mediumint(8) unsigned NOT NULL default '0',
  username char(15) NOT NULL default '',
  groupid smallint(6) unsigned NOT NULL default '0',
  styleid smallint(6) unsigned NOT NULL default '0',
  invisible tinyint(1) NOT NULL default '0',
  action tinyint(1) unsigned NOT NULL default '0',
  lastactivity int(10) unsigned NOT NULL default '0',
  fid smallint(6) unsigned NOT NULL default '0',
  tid mediumint(8) unsigned NOT NULL default '0',
  UNIQUE KEY sid (sid)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS cdb_settings;
CREATE TABLE cdb_settings (
  variable varchar(32) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (variable)
);

INSERT INTO cdb_settings VALUES ('announcements_num', 0x31);
INSERT INTO cdb_settings VALUES ('attachimgpost', 0x31);
INSERT INTO cdb_settings VALUES ('attachrefcheck', 0x30);
INSERT INTO cdb_settings VALUES ('attachsave', 0x31);
INSERT INTO cdb_settings VALUES ('attachimgcheck', 0x30);
INSERT INTO cdb_settings VALUES ('attachsoftdownload', 0x30);
INSERT INTO cdb_settings VALUES ('attach_max', 0x30);
INSERT INTO cdb_settings VALUES ('attach_newpost', 0x32);
INSERT INTO cdb_settings VALUES ('attach_editpost', 0x31);
INSERT INTO cdb_settings VALUES ('attach_replypost', 0x31);
INSERT INTO cdb_settings VALUES ('bbclosed', 0x30);
INSERT INTO cdb_settings VALUES ('bbinsert', 0x31);
INSERT INTO cdb_settings VALUES ('bbname', 0x44697363757a2120426f617264);
INSERT INTO cdb_settings VALUES ('bbrules', 0x30);
INSERT INTO cdb_settings VALUES ('bbrulestxt', '');
INSERT INTO cdb_settings VALUES ('censoruser', '');
INSERT INTO cdb_settings VALUES ('closedreason', '');
INSERT INTO cdb_settings VALUES ('statcacherefresh', 0x33363030);
INSERT INTO cdb_settings VALUES ('dateformat', 0x592d6e2d6a);
INSERT INTO cdb_settings VALUES ('debug', 0x31);
INSERT INTO cdb_settings VALUES ('delayviewcount', 0x30);
INSERT INTO cdb_settings VALUES ('delayeditpost', 0x30);
INSERT INTO cdb_settings VALUES ('delayreply', 0x30);
INSERT INTO cdb_settings VALUES ('delaykarma', 0x30);
INSERT INTO cdb_settings VALUES ('deletedcredits', 0x31);
INSERT INTO cdb_settings VALUES ('digestcredits', 0x3130);
INSERT INTO cdb_settings VALUES ('dosevasive', 0x30);
INSERT INTO cdb_settings VALUES ('doublee', 0x31);
INSERT INTO cdb_settings VALUES ('editedby', 0x31);
INSERT INTO cdb_settings VALUES ('fastpost', 0x31);
INSERT INTO cdb_settings VALUES ('floodctrl', 0x3135);
INSERT INTO cdb_settings VALUES ('forumjump', 0x31);
INSERT INTO cdb_settings VALUES ('gzipcompress', 0x30);
INSERT INTO cdb_settings VALUES ('hideprivate', 0x31);
INSERT INTO cdb_settings VALUES ('hottopic', 0x3130);
INSERT INTO cdb_settings VALUES ('loadctrl', 0x30);
INSERT INTO cdb_settings VALUES ('logincredits', 0x30);
INSERT INTO cdb_settings VALUES ('maxavatarpixel', 0x313230);
INSERT INTO cdb_settings VALUES ('maxavatarsize', 0x30);
INSERT INTO cdb_settings VALUES ('maxonlines', 0x31303030);
INSERT INTO cdb_settings VALUES ('maxpolloptions', 0x3130);
INSERT INTO cdb_settings VALUES ('maxpostsize', 0x3130303030);
INSERT INTO cdb_settings VALUES ('maxsearchresults', 0x353132);
INSERT INTO cdb_settings VALUES ('memberperpage', 0x3235);
INSERT INTO cdb_settings VALUES ('memliststatus', 0x31);
INSERT INTO cdb_settings VALUES ('minpostsize', 0x30);
INSERT INTO cdb_settings VALUES ('moddisplay', 0x666c6174);
INSERT INTO cdb_settings VALUES ('modshortcut', 0x30);
INSERT INTO cdb_settings VALUES ('newbiespan', 0x30);
INSERT INTO cdb_settings VALUES ('nocacheheaders', 0x30);
INSERT INTO cdb_settings VALUES ('onlinerecord', 0x310931303430303334363439);
INSERT INTO cdb_settings VALUES ('postcredits', 0x31);
INSERT INTO cdb_settings VALUES ('postperpage', 0x3130);
INSERT INTO cdb_settings VALUES ('regctrl', 0x30);
INSERT INTO cdb_settings VALUES ('regstatus', 0x31);
INSERT INTO cdb_settings VALUES ('regverify', 0x30);
INSERT INTO cdb_settings VALUES ('replycredits', 0x31);
INSERT INTO cdb_settings VALUES ('reportpost', 0x31);
INSERT INTO cdb_settings VALUES ('searchctrl', 0x3330);
INSERT INTO cdb_settings VALUES ('sitename', '北京康盛世纪科技有限公司');
INSERT INTO cdb_settings VALUES ('siteurl', 0x687474703a2f2f7777772e63726f73736461792e636f6d2f);
INSERT INTO cdb_settings VALUES ('smcols', 0x33);
INSERT INTO cdb_settings VALUES ('smileyinsert', 0x31);
INSERT INTO cdb_settings VALUES ('statstatus', 0x30);
INSERT INTO cdb_settings VALUES ('styleid', 0x31);
INSERT INTO cdb_settings VALUES ('timeformat', 0x683a692041);
INSERT INTO cdb_settings VALUES ('timeoffset', 0x38);
INSERT INTO cdb_settings VALUES ('topicperpage', 0x3230);
INSERT INTO cdb_settings VALUES ('userstatusby', 0x31);
INSERT INTO cdb_settings VALUES ('version', 0x322e35);
INSERT INTO cdb_settings VALUES ('vtonlinestatus', 0x30);
INSERT INTO cdb_settings VALUES ('welcomemsg', 0x30);
INSERT INTO cdb_settings VALUES ('welcomemsgtxt', '');
INSERT INTO cdb_settings VALUES ('whosonlinestatus', 0x31);
INSERT INTO cdb_settings VALUES ('useimagemessage', 0x31);

DROP TABLE IF EXISTS cdb_smilies;
CREATE TABLE cdb_smilies (
  id smallint(6) unsigned NOT NULL auto_increment,
  type enum('smiley','icon') NOT NULL default 'smiley',
  code varchar(10) NOT NULL default '',
  url varchar(30) NOT NULL default '',
  PRIMARY KEY  (id)
);

INSERT INTO cdb_smilies VALUES (1, 'smiley', ':)', 'smile.gif');
INSERT INTO cdb_smilies VALUES (2, 'smiley', ':(', 'sad.gif');
INSERT INTO cdb_smilies VALUES (3, 'smiley', ':D', 'biggrin.gif');
INSERT INTO cdb_smilies VALUES (4, 'smiley', ';)', 'wink.gif');
INSERT INTO cdb_smilies VALUES (5, 'smiley', ':cool:', 'cool.gif');
INSERT INTO cdb_smilies VALUES (6, 'smiley', ':mad:', 'mad.gif');
INSERT INTO cdb_smilies VALUES (7, 'smiley', ':o', 'shocked.gif');
INSERT INTO cdb_smilies VALUES (8, 'smiley', ':P', 'tongue.gif');
INSERT INTO cdb_smilies VALUES (9, 'smiley', ':lol:', 'lol.gif');
INSERT INTO cdb_smilies VALUES (10, 'icon', '', 'icon1.gif');
INSERT INTO cdb_smilies VALUES (11, 'icon', '', 'icon2.gif');
INSERT INTO cdb_smilies VALUES (12, 'icon', '', 'icon3.gif');
INSERT INTO cdb_smilies VALUES (13, 'icon', '', 'icon4.gif');
INSERT INTO cdb_smilies VALUES (14, 'icon', '', 'icon5.gif');
INSERT INTO cdb_smilies VALUES (15, 'icon', '', 'icon6.gif');
INSERT INTO cdb_smilies VALUES (16, 'icon', '', 'icon7.gif');
INSERT INTO cdb_smilies VALUES (17, 'icon', '', 'icon8.gif');
INSERT INTO cdb_smilies VALUES (18, 'icon', '', 'icon9.gif');

DROP TABLE IF EXISTS cdb_stats;
CREATE TABLE cdb_stats (
  type varchar(20) NOT NULL default '',
  var varchar(20) NOT NULL default '',
  count int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (type,var)
);

INSERT INTO cdb_stats VALUES ('total', 'hits', 0);
INSERT INTO cdb_stats VALUES ('total', 'members', 0);
INSERT INTO cdb_stats VALUES ('total', 'guests', 0);
INSERT INTO cdb_stats VALUES ('os', 'Windows', 0);
INSERT INTO cdb_stats VALUES ('os', 'Mac', 0);
INSERT INTO cdb_stats VALUES ('os', 'Linux', 0);
INSERT INTO cdb_stats VALUES ('os', 'FreeBSD', 0);
INSERT INTO cdb_stats VALUES ('os', 'SunOS', 0);
INSERT INTO cdb_stats VALUES ('os', 'BeOS', 0);
INSERT INTO cdb_stats VALUES ('os', 'OS/2', 0);
INSERT INTO cdb_stats VALUES ('os', 'AIX', 0);
INSERT INTO cdb_stats VALUES ('os', 'Other', 0);
INSERT INTO cdb_stats VALUES ('browser', 'MSIE', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Netscape', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Mozilla', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Lynx', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Opera', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Konqueror', 0);
INSERT INTO cdb_stats VALUES ('browser', 'Other', 0);
INSERT INTO cdb_stats VALUES ('week', '0', 0);
INSERT INTO cdb_stats VALUES ('week', '1', 0);
INSERT INTO cdb_stats VALUES ('week', '2', 0);
INSERT INTO cdb_stats VALUES ('week', '3', 0);
INSERT INTO cdb_stats VALUES ('week', '4', 0);
INSERT INTO cdb_stats VALUES ('week', '5', 0);
INSERT INTO cdb_stats VALUES ('week', '6', 0);
INSERT INTO cdb_stats VALUES ('hour', '00', 0);
INSERT INTO cdb_stats VALUES ('hour', '01', 0);
INSERT INTO cdb_stats VALUES ('hour', '02', 0);
INSERT INTO cdb_stats VALUES ('hour', '03', 0);
INSERT INTO cdb_stats VALUES ('hour', '04', 0);
INSERT INTO cdb_stats VALUES ('hour', '05', 0);
INSERT INTO cdb_stats VALUES ('hour', '06', 0);
INSERT INTO cdb_stats VALUES ('hour', '07', 0);
INSERT INTO cdb_stats VALUES ('hour', '08', 0);
INSERT INTO cdb_stats VALUES ('hour', '09', 0);
INSERT INTO cdb_stats VALUES ('hour', '10', 0);
INSERT INTO cdb_stats VALUES ('hour', '11', 0);
INSERT INTO cdb_stats VALUES ('hour', '12', 0);
INSERT INTO cdb_stats VALUES ('hour', '13', 0);
INSERT INTO cdb_stats VALUES ('hour', '14', 0);
INSERT INTO cdb_stats VALUES ('hour', '15', 0);
INSERT INTO cdb_stats VALUES ('hour', '16', 0);
INSERT INTO cdb_stats VALUES ('hour', '17', 0);
INSERT INTO cdb_stats VALUES ('hour', '18', 0);
INSERT INTO cdb_stats VALUES ('hour', '19', 0);
INSERT INTO cdb_stats VALUES ('hour', '20', 0);
INSERT INTO cdb_stats VALUES ('hour', '21', 0);
INSERT INTO cdb_stats VALUES ('hour', '22', 0);
INSERT INTO cdb_stats VALUES ('hour', '23', 0);

DROP TABLE IF EXISTS cdb_styles;
CREATE TABLE cdb_styles (
  styleid smallint(6) unsigned NOT NULL auto_increment,
  name varchar(20) NOT NULL default '',
  available tinyint(1) NOT NULL default '1',
  templateid smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (styleid),
  KEY themename (name)
);

INSERT INTO cdb_styles VALUES (1, 'Default Style', 1, 1);

DROP TABLE IF EXISTS cdb_stylevars;
CREATE TABLE cdb_stylevars (
  stylevarid smallint(6) unsigned NOT NULL auto_increment,
  styleid smallint(6) unsigned NOT NULL default '0',
  variable text NOT NULL,
  substitute text NOT NULL,
  PRIMARY KEY  (stylevarid),
  KEY styleid (styleid)
);

INSERT INTO cdb_stylevars VALUES (1, 1, 'bgcolor', '#E9EDF7');
INSERT INTO cdb_stylevars VALUES (2, 1, 'altbg1', '#F8F9FC');
INSERT INTO cdb_stylevars VALUES (3, 1, 'altbg2', '#FFFFFF');
INSERT INTO cdb_stylevars VALUES (4, 1, 'link', '#003366');
INSERT INTO cdb_stylevars VALUES (5, 1, 'bordercolor', '#DDE3EC');
INSERT INTO cdb_stylevars VALUES (6, 1, 'headercolor', 'headerbg.gif');
INSERT INTO cdb_stylevars VALUES (7, 1, 'headertext', '#FFFFFF');
INSERT INTO cdb_stylevars VALUES (8, 1, 'catcolor', 'catbg.gif');
INSERT INTO cdb_stylevars VALUES (9, 1, 'tabletext', '#000000');
INSERT INTO cdb_stylevars VALUES (10, 1, 'text', '#000000');
INSERT INTO cdb_stylevars VALUES (11, 1, 'borderwidth', '1');
INSERT INTO cdb_stylevars VALUES (12, 1, 'tablewidth', '99%');
INSERT INTO cdb_stylevars VALUES (13, 1, 'tablespace', '4');
INSERT INTO cdb_stylevars VALUES (14, 1, 'font', 'Tahoma, Verdana');
INSERT INTO cdb_stylevars VALUES (15, 1, 'fontsize', '12px');
INSERT INTO cdb_stylevars VALUES (16, 1, 'nobold', '0');
INSERT INTO cdb_stylevars VALUES (17, 1, 'boardimg', 'logo.gif');
INSERT INTO cdb_stylevars VALUES (18, 1, 'imgdir', 'images/default');
INSERT INTO cdb_stylevars VALUES (19, 1, 'smdir', 'images/smilies');
INSERT INTO cdb_stylevars VALUES (20, 1, 'cattext', '#000000');
INSERT INTO cdb_stylevars VALUES (21, 1, 'smfontsize', '11px');
INSERT INTO cdb_stylevars VALUES (22, 1, 'smfont', 'Tahoma');
INSERT INTO cdb_stylevars VALUES (23, 1, 'maintablespace', '8');
INSERT INTO cdb_stylevars VALUES (24, 1, 'maintablewidth', '98%');
INSERT INTO cdb_stylevars VALUES (25, 1, 'maintablecolor', '#FFFFFF');


DROP TABLE IF EXISTS cdb_subscriptions;
CREATE TABLE cdb_subscriptions (
  uid mediumint(8) unsigned NOT NULL default '0',
  tid mediumint(8) unsigned NOT NULL default '0',
  email varchar(60) NOT NULL default '',
  lastnotify int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (uid,tid)
);

DROP TABLE IF EXISTS cdb_templates;
CREATE TABLE cdb_templates (
  templateid smallint(6) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  charset varchar(30) NOT NULL default '',
  directory varchar(100) NOT NULL default '',
  copyright varchar(100) NOT NULL default '',
  PRIMARY KEY  (templateid)
);

INSERT INTO cdb_templates VALUES (1, 'Default', '{$lang['charset']}', './templates/default', 'Designed by ARTERY');

DROP TABLE IF EXISTS cdb_threads;
CREATE TABLE cdb_threads (
  tid mediumint(8) unsigned NOT NULL auto_increment,
  fid smallint(6) unsigned NOT NULL default '0',
  creditsrequire smallint(6) unsigned NOT NULL default '0',
  iconid smallint(6) unsigned NOT NULL default '0',
  author char(15) NOT NULL default '',
  authorid mediumint(8) unsigned NOT NULL default '0',
  subject char(80) NOT NULL default '',
  dateline int(10) unsigned NOT NULL default '0',
  lastpost int(10) unsigned NOT NULL default '0',
  lastposter char(15) NOT NULL default '',
  views mediumint(8) unsigned NOT NULL default '0',
  replies smallint(6) unsigned NOT NULL default '0',
  displayorder tinyint(1) NOT NULL default '0',
  highlight tinyint(1) NOT NULL default '0',
  digest tinyint(1) NOT NULL default '0',
  poll tinyint(1) NOT NULL default '0',
  attachment tinyint(1) NOT NULL default '0',
  closed mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (tid),
  KEY displayorder (fid,displayorder,lastpost),
  KEY digest (digest)
);

DROP TABLE IF EXISTS cdb_usergroups;
CREATE TABLE cdb_usergroups (
  groupid smallint(6) unsigned NOT NULL auto_increment,
  type enum('system','special','member') NOT NULL default 'member',
  grouptitle char(30) NOT NULL default '',
  creditshigher int(10) NOT NULL default '0',
  creditslower int(10) NOT NULL default '0',
  stars tinyint(3) NOT NULL default '0',
  color char(7) NOT NULL default '',
  groupavatar char(60) NOT NULL default '',
  allowcstatus tinyint(1) NOT NULL default '0',
  allowavatar tinyint(1) NOT NULL default '0',
  allowvisit tinyint(1) NOT NULL default '0',
  allowview tinyint(1) NOT NULL default '0',
  allowpost tinyint(1) NOT NULL default '0',
  allowpostpoll tinyint(1) NOT NULL default '0',
  allowgetattach tinyint(1) NOT NULL default '0',
  allowpostattach tinyint(1) NOT NULL default '0',
  allowvote tinyint(1) NOT NULL default '0',
  allowsearch tinyint(1) NOT NULL default '0',
  allowkarma tinyint(1) NOT NULL default '0',
  allowinvisible tinyint(1) NOT NULL default '0',
  allowsetviewperm tinyint(1) NOT NULL default '0',
  allowsetattachperm tinyint(1) NOT NULL default '0',
  allowhidecode tinyint(1) NOT NULL default '0',
  allowsigbbcode tinyint(1) NOT NULL default '0',
  allowsigimgcode tinyint(1) NOT NULL default '0',
  allowviewstats tinyint(1) NOT NULL default '0',
  maxpmnum smallint(6) unsigned NOT NULL default '0',
  maxsigsize smallint(6) unsigned NOT NULL default '0',
  maxkarmarate tinyint(3) unsigned NOT NULL default '0',
  maxrateperday smallint(6) unsigned NOT NULL default '0',
  maxattachsize int(10) unsigned NOT NULL default '0',
  attachextensions char(255) NOT NULL default '',
  PRIMARY KEY  (groupid),
  KEY creditsrange (creditshigher,creditslower)
);

INSERT INTO cdb_usergroups VALUES (1, 'system', '管理员', 0, 0, 9, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 200, 500, 30, 500, 2048000, '');
INSERT INTO cdb_usergroups VALUES (2, 'system', '超级版主', 0, 0, 8, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 120, 300, 15, 50, 2048000, '');
INSERT INTO cdb_usergroups VALUES (3, 'system', '版主', 0, 0, 7, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 0, 1, 1, 1, 1, 1, 1, 80, 200, 10, 30, 2048000, 'chm,pdf,zip,rar,tar,tgz,gif,jpg,jpeg,png');
INSERT INTO cdb_usergroups VALUES (4, 'system', '禁止发言', 0, 0, 0, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (5, 'system', '禁止访问', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (6, 'system', 'IP 禁止', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (7, 'system', '游客', 0, 0, 0, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (8, 'system', '等待验证用户', 0, 0, 0, '', '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 50, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (9, 'member', '社区乞丐', -9999999, 0, 0, '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (10, 'member', '新手上路', 0, 50, 1, '', '', 0, 0, 1, 1, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 20, 80, 0, 0, 0, '');
INSERT INTO cdb_usergroups VALUES (11, 'member', '注册用户', 50, 200, 2, '', '', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 1, 0, 1, 30, 100, 4, 10, 0, '');
INSERT INTO cdb_usergroups VALUES (12, 'member', '中级用户', 200, 500, 3, '', '', 0, 2, 1, 1, 1, 1, 1, 0, 1, 2, 0, 0, 0, 0, 0, 1, 0, 1, 50, 150, 6, 15, 256000, 'gif,jpg,jpeg,png');
INSERT INTO cdb_usergroups VALUES (13, 'member', '高级用户', 500, 1000, 4, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 0, 0, 0, 0, 1, 0, 1, 60, 200, 10, 30, 512000, 'zip,rar,tar,tgz,gif,jpg,jpeg,png');
INSERT INTO cdb_usergroups VALUES (14, 'member', '银牌会员', 1000, 3000, 6, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 0, 1, 1, 0, 1, 1, 1, 80, 300, 15, 40, 1024000, 'chm,pdf,zip,rar,tar,tgz,gif,jpg,jpeg,png');
INSERT INTO cdb_usergroups VALUES (15, 'member', '金牌会员', 3000, 9999999, 8, '', '', 1, 3, 1, 1, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 0, 1, 1, 1, 100, 500, 20, 50, 2048000, 'chm,pdf,zip,rar,tar,tgz,gif,jpg,jpeg,png');

DROP TABLE IF EXISTS cdb_words;
CREATE TABLE cdb_words (
  id smallint(6) unsigned NOT NULL auto_increment,
  admin varchar(15) NOT NULL default '',
  find varchar(60) NOT NULL default '',
  replacement varchar(60) NOT NULL default '',
  PRIMARY KEY  (id)
);

DROP TABLE IF EXISTS cdb_plugins;
CREATE TABLE cdb_plugins (
  plug_id smallint(6) NOT NULL auto_increment,
  plug_title varchar(255) NOT NULL default '',
  plug_version varchar(255) NOT NULL default '',
  plug_author varchar(255) NOT NULL default '0',
  plug_key varchar(255) NOT NULL default '',
  plug_stats tinyint(1) NOT NULL default '0',
  plug_cp varchar(255) NOT NULL default '',
  plug_tables varchar(255) NOT NULL default '',
  plug_license text NOT NULL,
  plug_desc text NOT NULL,
  PRIMARY KEY  (plug_id),
  KEY plug_keyword (plug_key)
);

DROP TABLE IF EXISTS cdb_plugins_settings;
CREATE TABLE cdb_plugins_settings (
  conf_id int(10) unsigned NOT NULL auto_increment,
  conf_title varchar(255) NOT NULL default '',
  conf_desc text NOT NULL,
  conf_group varchar(255) NOT NULL default '',
  conf_type varchar(255) NOT NULL default '',
  conf_key varchar(255) NOT NULL default '',
  conf_value text NOT NULL,
  conf_extra text NOT NULL,
  conf_position smallint(3) unsigned NOT NULL default '0',
  conf_cached tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (conf_id),
  KEY conf_group (conf_group)
);

EOT;

	runquery($sql);
	$db->query("DELETE FROM {$tablepre}members");
	$db->query("INSERT INTO {$tablepre}members (username, password, adminid, groupid, regip, regdate, lastvisit, lastpost, email, dateformat, timeformat, showemail, newsletter, timeoffset)
		VALUES ('$username', '".password_hash($password1, PASSWORD_DEFAULT)."', '1', '1', 'hidden', '".time()."', '".time()."', '".time()."', '$email', 'Y-n-j', 'h:i A', '1', '1', '8');");

echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";
echo"            <hr noshade align=\"center\" width=\"100%\" size=\"1\">\n";
echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td><b><font color=\"#FF0000\">&gt;</font><font color=\"#000000\"> {$lang['init_file']}</font></b></td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";

loginit('karmalog');
loginit('illegallog');
loginit('modslog');
loginit('cplog');
dir_clear('./forumdata/templates');
dir_clear('./forumdata/cache');
dir_clear('./forumdata/accesslogs');

?>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td align="center">
            <font color="#FF0000"><b><?=$lang['install_succeed']?></font><br>
            <?=$lang['username']?></b> <?=$username?><b> &nbsp; <?=$lang['password']?></b> <?=$password1?><br><br>
            <a href="index.php" target="_blank"><?=$lang['goto_forum']?></a>
          </td>
        </tr>
<?

	
}

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td align="center">
            <b style="font-size: 11px">Powered by <a href="http://discuz.net" target="_blank">Discuz! <?=$version?></a> , &nbsp; Copyright &copy; <a href="http://www.discuz.com" target=\"_blank\">Comsenz Technology Ltd </a>, 2001-2005</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
</body>
</html>

