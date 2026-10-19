<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['prune_tips']?>
</td></tr></table></td></tr></table>

<br><br><form method="post" action="admincp.php?action=prune">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">

<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr>
<td class="header" colspan="2"><?=$lang['prune']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_no_update_member']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="checkbox" name="donotupdatemember" value="1" checked></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_forum']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><?=$forumselect?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_time']?></td>
<td align="right" bgcolor="<?=ALTBG2?>">
<input type="text" name="starttime" size="10" value="<?=gmdate('Y-n-j', $timestamp + $timeoffset * 3600 - 86400 * 3)?>"> - 
<input type="text" name="endtime" size="10" value="<?=gmdate('Y-n-j', $timestamp + $timeoffset * 3600)?>" <?=($adminid != 1 ? 'disabled' : '')?>>
</td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_user']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="users" size="40"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_ip']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="useip" size="40"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['prune_keyword']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="keywords" size="40"></td>
</tr>

</table></td></tr></table><br>
<center><input type="submit" name="prunesubmit" value="<?=$lang['submit']?>"></center>
</form>
