<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><br><form method="post" action="admincp.php?action=runquery">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="550" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan=2><?=$lang['database_run_query']?></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td valign="top"><textarea cols="85" rows="10" name="queries"></textarea><br>
<br><?=$lang['database_run_query_comment']?>
</td></tr></table>
</td></tr></table>
<br><br>
<center><input type="submit" name="sqlsubmit" value="<?=$lang['submit']?>"></center>
</form></td></tr>
