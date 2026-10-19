<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['forums_tips']?>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=forumadd&add=category">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="3"><?=$lang['forums_new_cat']?></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>" width="15%"><?=$lang['name']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="70%"><input type="text" name="newcat" value="Name of New Category" size="40"></td>
<td bgcolor="<?=ALTBG1?>" width="15%"><input type="submit" name="catsubmit" value="<?=$lang['submit']?>"></td></tr>
</table></td></tr></table></form>

<form method="post" action="admincp.php?action=forumadd&add=forum">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="5"><?=$lang['forums_new_forum']?></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>" width="15%"><?=$lang['name']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="28%"><input type="text" name="newforum" value="Name of New Forum" size="20"></td>
<td bgcolor="<?=ALTBG1?>" width="15%"><?=$lang['forums_cat_parent']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="27%"><?=$groupselect?></td>
<td bgcolor="<?=ALTBG1?>" width="15%"><input type="submit" name="forumsubmit" value="<?=$lang['submit']?>"></td></tr>
</table></td></tr></table></form>

<form method="post" action="admincp.php?action=forumadd&add=forum">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="5"><?=$lang['forums_new_sub']?></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>" width="15%"><?=$lang['name']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="28%"><input type="text" name="newforum" value="Name of New Sub Forum" size="20"></td>
<td bgcolor="<?=ALTBG1?>" width="15%"><?=$lang['forums_edit_parent']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="27%"><?=$forumselect?></td>
<td bgcolor="<?=ALTBG1?>" width="15%"><input type="submit" name="forumsubmit" value="<?=$lang['submit']?>"></td></tr>
</table></td></tr></table></form><br>
