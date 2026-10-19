<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['usergroups_tips']?>
</td></tr></table></td></tr></table>

<form method="post" action="admincp.php?action=usergroups&type=member">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="8"><?=$lang['usergroups_member']?> - <?=$lang['usergroups_detail']?></td></tr>
<tr class="category" align="center"><td width="45"><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['usergroups_title']?></td><td><?=$lang['members_creditshigher']?></td><td><?=$lang['members_creditslower']?></td><td><?=$lang['usergroups_stars']?></td><td><?=$lang['usergroups_color']?></td><td><?=$lang['usergroups_avatar']?></td><td><?=$lang['edit']?></td></tr>
<?=$membergroup?>
<tr height="1" bgcolor="<?=ALTBG2?>"><td colspan="8"></td></tr>
<tr align="center" bgcolor="<?=ALTBG1?>"><td><?=$lang['add_new']?></td>
<td><input type="text" size="12" name="grouptitlenew"></td>
<td><input type="text" size="6" name="creditshighernew"></td>
<td><input type="text" size="6" name="creditslowernew"></td>
<td><input type="text" size="2" name="starsnew"></td>
<td><input type="text" size="6" name="colornew"></td>
<td><input type="text" size="12" name="groupavatarnew"></td>
<td>&nbsp;</td>
</tr></table></td></tr></table><br><center><?=$warning?>
<input type="submit" name="groupsubmit" value="<?=$lang['submit']?>">&nbsp;
</form><br><br>

<form method="post" action="admincp.php?action=usergroups&type=special">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="7"><?=$lang['usergroups_special']?> - <?=$lang['usergroups_detail']?></td></tr>
<tr class="category" align="center"><td width="45"><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['usergroups_title']?></td><td><?=$lang['usergroups_specified_members']?></td><td nowrap><?=$lang['usergroups_stars']?></td><td><?=$lang['usergroups_color']?></td><td><?=$lang['usergroups_avatar']?></td><td><?=$lang['edit']?></td></tr>
<?=$specialgroup?>
<tr height="1" bgcolor="<?=ALTBG2?>"><td colspan="7"></td></tr>
<tr align="center" bgcolor="<?=ALTBG1?>"><td><?=$lang['add_new']?></td>
<td><input type="text" size="12" name="grouptitlenew"></td>
<td>&nbsp;</td>
<td><input type="text" size="2" name="starsnew"></td>
<td><input type="text" size="6" name="colornew"></td>
<td><input type="text" size="12" name="groupavatarnew"></td>
<td>&nbsp;</td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="groupsubmit" value="<?=$lang['submit']?>"></center></form><br><br>

<form method="post" action="admincp.php?action=usergroups&type=system">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="6"><?=$lang['usergroups_system']?> - <?=$lang['usergroups_detail']?></td></tr>
<tr class="category" align="center">
<td><?=$lang['usergroups_title']?></td><td><?=$lang['usergroups_status']?></td><td><?=$lang['usergroups_stars']?></td><td><?=$lang['usergroups_color']?><td><?=$lang['usergroups_avatar']?></td><td><?=$lang['edit']?></td></tr>
<?=$sysgroup?>
</table></td></tr></table><br><center>
<input type="submit" name="groupsubmit" value="<?=$lang['submit']?>"></center></form>
