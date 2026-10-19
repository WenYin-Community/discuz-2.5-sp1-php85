<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<form method="post" action="admincp.php?action=templates">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header" align="center">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['templates_name']?></td><td><?=$lang['templates_charset']?></td><td><?=$lang['templates_directory']?></td><td><?=$lang['templates_copyright']?></td><td><?=$lang['edit']?></td></tr>
<?=$templates?>
<tr bgcolor="<?=ALTBG2?>"><td height="1" colspan="76"></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>"><?=$lang['add_new']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="text" size="8" name="newname"></td>
<td bgcolor="<?=ALTBG1?>"><input type="text" size="6" name="newcharset"></td>
<td bgcolor="<?=ALTBG2?>"><input type="text" size="20" name="newdirectory"></td>
<td bgcolor="<?=ALTBG1?>"><input type="text" size="25" name="newcopyright"></td>
<td bgcolor="<?=ALTBG2?>">&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="tplsubmit" value="<?=$lang['submit']?>"></center></form>
