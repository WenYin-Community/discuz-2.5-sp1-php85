<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><form method="post" action="admincp.php?action=addmember">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="80%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr><td class="header" colspan="2"><?=$lang['members_add']?></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['usergroup']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>">
<select name="newgroupid">
<?=$groupselect?>
</td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['username']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="newusername"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['password']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="newpassword"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['email']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="newemail"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_add_email_notify']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="checkbox" name="emailnotify" value="yes" checked></td></tr>

</table></td></tr></table>
<br><center><input type="submit" name="addsubmit" value="<?=$lang['submit']?>"></center>
</form>

