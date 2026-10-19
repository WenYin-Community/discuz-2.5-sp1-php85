<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post"	action="admincp.php?action=discuzcodes">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="6"><?=$lang['discuzcodes_edit']?></td></tr>
<tr align="center" class="category">
<td><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['discuzcodes_tag']?></td><td><?=$lang['available']?></td>
<td><?=$lang['edit']?></td></tr>
<?=$discuzcodes?>
<tr bgcolor="<?=ALTBG2?>"><td colspan="4" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td><?=$lang['add_new']?></td>
<td><input type="text" size="15" name="newtag"></td>
<td colspan="2">&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="bbcodessubmit" value="<?=$lang['submit']?>"></center></form></td></tr>
