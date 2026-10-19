<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
	<tr><td bgcolor="<?=BORDERCOLOR?>">
		<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
			<tr class="header"><td><?=$lang['tips']?></td></tr>
			<tr bgcolor="<?=ALTBG1?>"><td><br><?=$lang['attachtypes_tips']?>
				</td></tr>
		</table></td></tr>
</table>

<br><form method="post"	action="admincp.php?action=attachtypes">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr align="center" class="header"><td width="45"><input	type="checkbox"	name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['attachtypes_ext']?></td><td><?=$lang['attachtypes_maxsize']?></td></tr>
<?=$attachtypes?>
<tr bgcolor="<?=ALTBG2?>"><td colspan="3" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>">
<td align="center"><?=$lang['add_new']?></td>
<td align="center"><input type="text" size="10"	name="newextension"></td>
<td align="center"><input type="text" size="15"	name="newmaxsize"></td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="typesubmit" value="<?=$lang['submit']?>"></center>
</form>
