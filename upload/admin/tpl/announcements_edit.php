<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post" action="admincp.php?action=announcements&edit=<?=$edit?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['announce_edit']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['subject']?>:</b></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="subjectnew" value="<?=$announce['subject']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['start_time']?>:</b><br><?=$lang['announce_time_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="starttimenew" value="<?=$announce['starttime']?>"></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>"><b><?=$lang['end_time']?>:</b><br><?=$lang['announce_time_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="endtimenew" value="<?=$announce['endtime']?>"> <?=$lang['announce_end_time_comment']?></td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['announce_posturl']?>:</b><br><?=$lang['announce_posturl_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><input type="text" size="45" name="posturlnew" value ="<?=$announce['posturl']?>"> </td></tr>

<tr><td width="21%" bgcolor="<?=ALTBG1?>" valign="top"><b><?=$lang['message']?>:</b><br><?=$lang['announce_message_comment']?></td>
<td width="79%" bgcolor="<?=ALTBG2?>"><textarea name="messagenew" cols="60" rows="10"><?=dhtmlspecialchars($announce['message'])?></textarea></td></tr>

</table></td></tr></table><br><center><input type="submit" name="editsubmit" value="<?=$lang['submit']?>">
</form>