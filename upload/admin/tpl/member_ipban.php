<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['ipban_tips']?>
</td></tr></table></td></tr></table>

<form method="post" action="admincp.php?action=ipban">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header" align="center">
<td width="45"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['ip']?></td><td><?=$lang['ipban_location']?></td><td><?=$lang['operator']?></td><td><?=$lang['start_time']?></td><td><?=$lang['end_time']?></td></tr>
<?=$ipbanned?>
<tr bgcolor="<?=ALTBG2?>"><td colspan="6" height="1"></td></tr>
<tr align="center" bgcolor="<?=ALTBG1?>">
<td><?=$lang['add_new']?></td>
<td colspan="3"><b>
<input type="text" name="ip1new" value="<?=$iptoban[0]?>" size="3" maxlength="3"> . 
<input type="text" name="ip2new" value="<?=$iptoban[1]?>" size="3" maxlength="3"> . 
<input type="text" name="ip3new" value="<?=$iptoban[2]?>" size="3" maxlength="3"> . 
<input type="text" name="ip4new" value="<?=$iptoban[3]?>" size="3" maxlength="3"></b></td>
<td colspan="2"><?=$lang['ipban_validity']?>: <input type="text" name="validitynew" value="30" size="3"> <?=$lang['ipban_days']?></td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="ipbansubmit" value="<?=$lang['submit']?>"></center>
</form>
