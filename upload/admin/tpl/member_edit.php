<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<form method="post" action="admincp.php?action=members">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="multi"><?=$multipage?></td></tr>
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr align="center" class="header">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['username']?></td><td><?=$lang['credits']?></td><td><?=$lang['postnum']?></td><td><?=$lang['admingroup']?><td><?=$lang['usergroup']?></td><td><?=$lang['custom_status']?></td><td><?=$lang['access']?></td><td><?=$lang['edit']?></td></tr>
<?=$members?>
</table></td></tr>
<tr><td class="multi"><?=$multipage?></td></tr>
</table><br><center>
<input type="submit" name="editsubmit" value="<?=$lang['submit']?>"></center>
</form>

