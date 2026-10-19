<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['access_tips']?>
</td></tr></table></td></tr></table>

<form method="post" action="admincp.php?action=access&uid=<?=$uid?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="6"><?=$lang['access_edit']?> - <?=$member['username']?></td></tr>
<tr class="header" align="center">
<td><?=$lang['forum']?></td><td><?=$lang['access_default']?></td><td><?=$lang['access_view']?></td><td><?=$lang['access_post']?></td><td><?=$lang['access_reply']?></td><td><?=$lang['access_getattach']?></td></tr>
<?=$members?>
</table></td></tr></table><br>
<center><input type="submit" name="accesssubmit" value="<?=$lang['submit']?>"></center></form>
