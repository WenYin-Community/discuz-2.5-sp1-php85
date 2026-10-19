<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<form method="post" action="admincp.php?action=icons">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr><td	colspan="4" class="header"><?=$lang['smilies_edit_icon']?></td></tr>
<tr align="center" class="category">
<td width="45"><?=$lang['del']?></td>
<td colspan="2"><?=$lang['smilies_edit_filename']?></td><td><?=$lang['smilies_edit_image']?></td></tr>
<?=$icons?>
<tr><td	bgcolor="<?=ALTBG2?>" colspan="4" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td><?=$lang['add_new']?></td><td colspan="2"><input type="text" name="newurl1" size="35"></td><td>&nbsp;</td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="smiliesubmit" value="<?=$lang['submit']?>"></center></form>

