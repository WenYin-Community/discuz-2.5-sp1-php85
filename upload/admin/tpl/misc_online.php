<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
	<tr><td bgcolor="<?=BORDERCOLOR?>">
		<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
		<tr class="header"><td><?=$lang['tips']?></td></tr>
		<tr bgcolor="<?=ALTBG1?>"><td>
		<br><?=$lang['onlinelist_tips']?>
		</td></tr></table></td></tr></table>

<br><form method="post"	action="admincp.php?action=onlinelist">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
	<tr><td	bgcolor="<?=BORDERCOLOR?>">
		<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
			<tr align="center" class="header">
				<td><?=$lang['display_order']?></td>
				<td><?=$lang['usergroups_title']?></td>
				<td><?=$lang['onlinelist_image']?></td>
			</tr>
	<?=$onlinelist?>
	</table></td></tr></table><br>
<center><input type="submit" name="onlinesubmit" value="<?=$lang['submit']?>"></center></form></td></tr>
