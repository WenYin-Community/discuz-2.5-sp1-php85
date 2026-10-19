<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post" action="admincp.php?action=attachments">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" style="table-layout: fixed;word-break: break-all">
<tr><td class="header" width="6%" align="center"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td class="header" width="15%" align="center"><?=$lang['attachments_name']?></td>
<td class="header" width="25%" align="center"><?=$lang['attachments_filename']?></td>
<td class="header" width="14%" align="center"><?=$lang['author']?></td>
<td class="header" width="23%" align="center"><?=$lang['attachments_thread']?></td>
<td class="header" width="8%" align="center"><?=$lang['size']?></td>
<td class="header" width="8%" align="center"><?=$lang['download']?></td></tr>
<?=$attachments?>
</table></td></tr>
</table><br>
<center><input type="submit" name="deletesubmit" value="<?=$lang['submit']?>"></center></form>
