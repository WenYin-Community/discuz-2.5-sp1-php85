<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center"><tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%"><tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_optimize_tips']?>
</td></tr></table></td></tr></table>

<br><br><form name="optimize" method="post" action="admincp.php?action=optimize">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr align="center" class="header">
<td><?=$lang['database_optimize_opt']?></td><td><?=$lang['database_optimize_table_name']?></td><td><?=$lang['type']?></td><td><?=$lang['database_optimize_rows']?></td>
<td><?=$lang['database_optimize_data']?></td><td><?=$lang['database_optimize_index']?></td><td><?=$lang['database_optimize_frag']?></td></tr>
