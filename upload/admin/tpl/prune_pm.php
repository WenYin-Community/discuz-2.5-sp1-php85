<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><br><br><form method="post" action="admincp.php?action=pmprune">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr><td class="header" colspan="2"><?=$lang['prune_pm']?></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['prune_pm_ignore_new']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="checkbox" name="ignorenew" value="1"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['prune_pm_day']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="days" size="7"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['prune_pm_user']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="users" size="40"></td></tr>

</table></td></tr></table><br>
<center><input type="submit" name="prunesubmit" value="<?=$lang['submit']?>"></center>
</form>
