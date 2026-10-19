<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['styles_tips']?>
</td></tr></table></td></tr></table><br><br>

<form method="post" action="admincp.php?action=styles">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header" align="center">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['styles_name']?></td><td><?=$lang['available']?></td><td>styleID</td><td><?=$lang['styles_template']?></td><td><?=$lang['styles_charset']?></td><td><?=$lang['export']?></td><td><?=$lang['edit']?></td></tr>
<?=$styleselect?>
<tr bgcolor="<?=ALTBG2?>"><td height="1" colspan="8"></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>"><?=$lang['add_new']?></td>
<td bgcolor="<?=ALTBG2?>"><input type='text' name="newname" size="18"></td>
<td colspan="6" bgcolor="<?=ALTBG2?>">&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="stylesubmit" value="<?=$lang['submit']?>"></center></form>

<br><form method="post" action="admincp.php?action=styles">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" bgcolor="<?=BORDERCOLOR?>" align="center">
<tr class="header"><td><?=$lang['styles_import']?></td></tr>
<tr><td bgcolor="<?=ALTBG1?>" align="center"><textarea  name="styledata" cols="80" rows="8"></textarea><br>
<input type="checkbox" name="ignoreversion" value="1"> <?=$lang['styles_import_ignore_version']?></td></tr>
</table><br><center><input type="submit" name="importsubmit" value="<?=$lang['submit']?>"></center></form>
