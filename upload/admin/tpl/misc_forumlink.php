<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['forumlinks_tips']?>
</td></tr></table></td></tr></table>

<br><form method="post"	action="admincp.php?action=forumlinks">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td	bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="6"><?=$lang['forumlinks_edit']?></td></tr>
<tr align="center" class="category">
<td><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['display_order']?></td><td><?=$lang['forumlinks_edit_name']?></td><td><?=$lang['forumlinks_edit_url']?></td><td><?=$lang['forumlinks_edit_note']?></td>
<td><?=$lang['forumlinks_edit_logo']?></td></tr>
<?=$forumlinks?>
<tr bgcolor="<?=ALTBG2?>"><td colspan="6" height="1"></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td><?=$lang['add_new']?></td>
<td><input type="text" size="3"	name="newdisplayorder"></td>
<td><input type="text" size="15" name="newname"></td>
<td><input type="text" size="15" name="newurl"></td>
<td><input type="text" size="15" name="newnote"></td>
<td><input type="text" size="15" name="newlogo"></td>
</tr></table></td></tr></table><br>
<center><input type="submit" name="forumlinksubmit" value="<?=$lang['submit']?>"></center></form></td></tr>
