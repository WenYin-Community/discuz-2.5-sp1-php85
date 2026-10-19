<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center"><tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%"><tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_import_tips']?>
</td></tr></table></td></tr></table>

<br><form name="restore" method="post" action="admincp.php?action=import" enctype="multipart/form-data">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header">
<td colspan="2"><?=$lang['database_import']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" name="from" value="server" checked onclick="this.form.datafile_server.disabled=!this.checked;this.form.datafile.disabled=this.checked"><?=$lang['database_import_from_server']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><input type="text" size="40" name="datafile_server" value="./forumdata/"></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" name="from" value="local" onclick="this.form.datafile_server.disabled=this.checked;this.form.datafile.disabled=!this.checked"><?=$lang['database_import_from_local']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><input type="file" size="29" name="datafile" disabled></td></tr>

</table></td></tr></table><br><center>
<input type="submit" name="importsubmit" value="<?=$lang['submit']?>"></center>
</form>

<br><form method="post" action="admincp.php?action=import">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center"><tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="smalltxt"><tr class="header"><td colspan="9"><?=$lang['database_export_file']?></td></tr>
<tr align="center" class="category"><td width="45"><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['filename']?></td><td><?=$lang['version']?></td>
<td><?=$lang['time']?></td><td><?=$lang['type']?></td>
<td><?=$lang['size']?></td><td><?=$lang['database_method']?></td>
<td><?=$lang['database_volume']?></td><td><?=$lang['operation']?></td></tr>
<?=$exportinfo?>
</table></td></tr></table><br><center>
<input type="submit" name="deletesubmit" value="<?=$lang['submit']?>"></center></form>
