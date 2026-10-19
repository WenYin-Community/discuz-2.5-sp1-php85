<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['newsletter_tips']?>
</td></tr></table></td></tr></table>

<br><br><form method="post" action="admincp.php?action=newsletter">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['newsletter']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>" valign="top"¡¡>
<?=$lang['usergroups_edit_title']?>
</td><td bgcolor="<?=ALTBG2?>">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><?=$usergroups?></tr></table></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>" valign="top">
<?=$lang['admingroup_title']?>
</td><td bgcolor="<?=ALTBG2?>">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><?=$admingroups?></tr></table></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['newsletter_send_via']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="radio" name="sendtype" value="user"   checked>&nbsp;<?=$lang['newsletter_touser']?>&nbsp;&nbsp;<input type="radio" name="sendtype" value='admin'>&nbsp;<?=$lang['newsletter_toadmin']?></td>
</tr>

<tr class="header"><td colspan="2"><?=$lang['newsletter_message']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['subject']?>:</td>
<td bgcolor="<?=ALTBG2?>"><input type="text" name="subject" size="80"></td>
</tr>


<tr>
<td bgcolor="<?=ALTBG1?>" valign="top"><?=$lang['message']?>:</td><td bgcolor="<?=ALTBG2?>">
<textarea cols="80" rows="10" name="message"></textarea></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['newsletter_send_via']?></td>
<td bgcolor="<?=ALTBG2?>">
<input type="radio" value="email" name="sendvia"> <?=$lang['email']?>
<input type="radio" value="pm" checked name="sendvia"> <?=$lang['pm']?>
</td></tr>

</table></td></tr></table><br>
<center><input type="submit" name="newslettersubmit" value="<?=$lang['submit']?>"></center>
</form>

