<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<form method="post" action="admincp.php?action=smilies">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="4" align="left"><?=$lang['smilies_edit']?></td></tr>
<tr align="center" class="category">
<td width="45"><?=$lang['del']?></td>
<td><?=$lang['smilies_edit_code']?></td><td><?=$lang['smilies_edit_filename']?></td><td><?=$lang['smilies_edit_image']?></td></tr>
<?=$smilies?>
<tr><td	bgcolor="<?=ALTBG2?>" colspan="4" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center"><td><?=$lang['add_new']?></td>
<td><input type="text" size="25" name="newcode"></td>
<td><input type="text" size="25" name="newurl1"></td>
<td></td></tr>
</table></td></tr></table><br>
<center><input type="submit" name="smiliesubmit" value="<?=$lang['submit']?>"></center></form>

