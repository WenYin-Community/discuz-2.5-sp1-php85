<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="3"><?=$lang['templates_maint']?></td></tr>

<form method="post" action="admincp.php?action=tpladd&edit=<?=$edit?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr bgcolor="<?=ALTBG2?>"><td width="25%"><?=$lang['templates_maint_new']?></td>
<td width="55%"><input type="text" name="name" size="40" maxlength="40"></td>
<td width="20%"><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>

<form method="get" action="admincp.php">
<input type="hidden" name="action" value="templates">
<input type="hidden" name="edit" value="<?=$edit?>">
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['templates_maint_search']?></td><td><input type="text" name="keyword" size="40"></td>
<td><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>

</table></td></tr></table></td></tr></table><br><br>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['templates_select']?><?=$keywordadd?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><br><center><b><?=$warning?></b></center><br>
<ul><li><b>Discuz! <?=$lang['templates_language_pack']?></b><?=$languages?></ul>
<ul><li><b>Discuz! <?=$lang['templates_html']?></b><?=$templates?></ul>
</td></tr></table></td></tr></table>
