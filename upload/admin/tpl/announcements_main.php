<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['announce_tips']?>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=announcements">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="7"><?=$lang['announce_edit']?></td></tr>
<tr align="center" class="category">
<td width="45"><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['author']?></td><td><?=$lang['subject']?></td><td><?=$lang['message']?></td><td><?=$lang['start_time']?></td><td><?=$lang['end_time']?></td><td><?=$lang['display_order']?></td></tr>
<?=$announcements?>
</table></td></tr></table><br><center>
<input type="submit" name="announcesubmit" value="<?=$lang['submit']?>"></center></form>

<br><form method="post" action="admincp.php?action=announcements">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['announce_add']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['subject']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="newsubject"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['start_time']?>:</b><br><?=$lang['announce_time_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="newstarttime" value="<?=$newstarttime?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['end_time']?>:</b><br><?=$lang['announce_time_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="newendtime"> <?=$lang['announce_end_time_comment']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['announce_posturl']?>:</b><br><?=$lang['announce_posturl_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="newposturl" value =""> http://something ... </td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['message']?>:</b><br><?=$lang['announce_message_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><textarea name="newmessage" cols="60" rows="10"></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="addsubmit" value="<?=$lang['submit']?>">
</form>



