<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center"><tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%"><tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_export_tips']?>
</td></tr></table></td></tr></table>

<br><br><form name="backup" method="post" action="admincp.php?action=export">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['database_export_type']?></td></tr>
<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" value="full" name="type"> <?=$lang['database_export_full']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><?=$lang['database_export_full_comment']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" value="standard" checked name="type"> <?=$lang['database_export_standard']?></td>
<td bgcolor="<?=ALTBG2?>"><?=$lang['database_export_standard_comment']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" value="mini" name="type" > <?=$lang['database_export_mini']?></td>
<td bgcolor="<?=ALTBG2?>"><?=$lang['database_export_mini_comment']?></td></tr>

<tr class="header"><td colspan="2"><?=$lang['database_export_method']?></td></tr>

<tr bgcolor="<?=ALTBG2?>">
<td><input type="radio" name="method" value="multivol" checked> <?=$lang['database_export_multivol']?></td>
<td><input type="text" size="40" name="sizelimit" value="2048"></td>
</tr>

<tr bgcolor="<?=ALTBG2?>">
<td colspan="2" height="1"></td>
</tr>

<tr bgcolor="<?=ALTBG2?>">
<td><input type="radio" checked> <?=$lang['database_export_filename']?></td>
<td><input type="text" size="40" name="filename" value="./forumdata/<?=date('md').'_'.random(8)?>.sql" onclick="alert('<?=$lang['database_export_filename_confirm']?>');"></td>
</tr>

</table></td></tr></table><br><center>
<input type="submit" name="exportsubmit" value="<?=$lang['submit']?>"></center></form>