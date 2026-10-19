<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><br><br><br><br><br>
<form method="post" action="admincp.php?extr=<?=urlencode($extr)?>&action=<?=urlencode($action)?>">
<input type="hidden" name="cploginsubmit" value="1">
<table cellspacing="0" cellpadding="0" border="0" width="60%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
	<tr class="header">
		<td colspan="2"><?=$lang['password_required']?> # <?=intval($session['errorlog'])?></td></tr>
	<tr>
		<td bgcolor="<?=ALTBG1?>" width="25%"><?=$lang['username']?>:</td><td bgcolor="<?=ALTBG2?>"><?=$discuz_user?> <a href="logging.php?action=logout&referer=index.php" target="_blank">[<?=$lang['menu_logout']?>]</a></td></tr>
	<tr>
		<td bgcolor="<?=ALTBG1?>" width="25%"><?=$lang['password']?>:</td><td bgcolor="<?=ALTBG2?>"><input type="password" name="admin_password" size="25"></td></tr>
</table>
</td></tr></table>
<br><center><input type="submit" name="cploginsubmit" value="<?=$lang['submit']?>"></center></form>
<br><br>
