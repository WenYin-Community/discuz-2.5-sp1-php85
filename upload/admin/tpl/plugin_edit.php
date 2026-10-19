<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post" action="admincp.php?action=plugin&mod=moreedit&pluginid=<?=$pluginid?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['plugin_edit']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plugin_name']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="new_name" value="<?=$pluginold['name']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plugin_url']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="new_url" value="<?=$pluginold['url']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plugin_cpurl']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="new_cpurl" value="<?=$pluginold['cpurl']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plugin_table']?>:</b><br><?=$lang['plugin_table_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="new_addtable" value="<?=$pluginold['addtable']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['plugin_comment']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><textarea name="new_comment" cols="60" rows="10"><?=$pluginold['comment']?></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="editsubmit" value="<?=$lang['submit']?>">
</form>