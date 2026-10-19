<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['censor_tips']?>
</td></tr></table></td></tr></table>

<br><form method="post"	action="admincp.php?action=censor">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr align="center" class="header"><td width="45"><input	type="checkbox"	name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['censor_word']?></td><td><?=$lang['censor_replacement']?></td><td><?=$lang['operator']?></td></tr>
<?=$censorwords?>
<tr bgcolor="<?=ALTBG2?>"><td colspan="4" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>">
<td align="center"><?=$lang['add_new']?></td>
<td align="center"><input type="text" size="30"	name="newfind"></td>
<td align="center"><input type="text" size="30"	name="newreplace"></td>
<td>&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="censorsubmit" value="<?=$lang['submit']?>"></center>
</form>
