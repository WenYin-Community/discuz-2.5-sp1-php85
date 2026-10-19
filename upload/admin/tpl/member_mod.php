<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><br><form method="post" action="admincp.php?action=mod_members">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="membersubmit" value="<?=$lang['submit']?>">
<table cellspacing="0" cellpadding="0" border="0" width="70%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['members_edit']?></td></tr>
<tr bgcolor="<?=ALTBG2?>">
<td><?=$lang['username']?>:</td><td><input type="text" name="username"></td></tr>
</table></td></tr></table><br><center>
<input type="submit" name="membersubmit" value="<?=$lang['submit']?>">
</center></form><br>
