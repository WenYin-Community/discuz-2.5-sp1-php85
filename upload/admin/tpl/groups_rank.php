<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
	<tr><td bgcolor="<?=BORDERCOLOR?>">
	<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
		<tr class="header"><td><?=$lang['tips']?></td></tr>
		<tr bgcolor="<?=ALTBG1?>"><td>
		<br><?=$lang['ranks_tips']?>
		</td></tr>
	</table>
	</td></tr>
</table>

<form method="post" action="admincp.php?action=ranks">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header" align="center"><td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['ranks_title']?></td><td><?=$lang['ranks_postshigher']?></td><td><?=$lang['ranks_stars']?></td><td><?=$lang['ranks_color']?></td></tr>
<?=$ranks?>
<tr height="1" bgcolor="<?=ALTBG2?>"><td colspan="5"></td></tr>
<tr align="center" bgcolor="<?=ALTBG1?>"><td><?=$lang['add_new']?></td>
<td><input type="text" size="12" name="newranktitle"></td>
<td><input type="text" size="6" name="newpostshigher"></td>
<td><input type="text" size="2" name="newstars"></td>
<td><input type="text" size="6" name="newcolor"></td>
</tr></table></tr></td></table><br>
<center><input type="submit" name="ranksubmit" value="<?=$lang['submit']?>"></center></form>
