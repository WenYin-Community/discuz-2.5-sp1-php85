<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="98%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="0" width="100%">
<tr><td>
	<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%">
	<tr class="header"><td colspan="3"><?=$lang[$lognames[$action]]?></td></tr>
	<form method="post" action="admincp.php?action=<?=$action?>">
	<input type="hidden" name="formhash" value="<?=FORMHASH?>">
	<tr bgcolor="<?=ALTBG2?>"><td width="25%"><?=$lang['logs_lpp']?></td>
	<td width="55%"><input type="text" name="lpp" size="40" maxlength="40" value="<?=$lpp?>"></td>
	<td width="20%"><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>

	<form method="post" action="admincp.php?action=<?=$action?>">
	<input type="hidden" name="formhash" value="<?=FORMHASH?>">
	<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['logs_search']?></td><td><input type="text" name="keyword" size="40" value="<?=dhtmlspecialchars($keyword)?>"></td>
	<td><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>
</td></tr></table></td></tr></table></table>

<br><br>

<table cellspacing="0" cellpadding="0" border="0" width="98%" align="center">
<tr class="multi"><td><?=$multipage?></td></tr>
<tr><td bgcolor="<?=BORDERCOLOR?>">
	<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
	<?=$logheader?>
	<?=$logbody?>
</table></td></tr><tr class="multi"><td><?=$multipage?></td></tr>
</table>

