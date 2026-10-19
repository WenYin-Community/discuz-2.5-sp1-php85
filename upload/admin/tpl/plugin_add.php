<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post" action="admincp.php?action=plugin&mod=add">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="plug_stats" value="0">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['plug_add']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plug_title']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="plug_title" value=""></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plug_desc']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="plug_desc" value=""></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plug_stats']?>:</b><br><?=$lang['plug_stats_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="radio" name="plug_stats" value="0"><input type="radio" name="plug_stats" value="0"><input type="radio" name="plug_stats" value="0"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['plug_table']?>:</b><br><?=$lang['plug_table_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="60" name="plug_table" value=""></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['plug_desc']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><textarea name="plug_comment" cols="60" rows="10"></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="addsubmit" value="<?=$lang['submit']?>">
</form>