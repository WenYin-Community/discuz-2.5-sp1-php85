<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><br><br><br><br>
<form method="post" action="admincp.php?action=forumsmerge">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="3"><?=$lang['forums_merge']?></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>" width="40%"><?=$lang['forums_merge_source']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="60%"><select name="source"><?=$forumselect?></select></td></tr>
<tr align="center"><td bgcolor="<?=ALTBG1?>" width="40%"><?=$lang['forums_merge_target']?>:</td>
<td bgcolor="<?=ALTBG2?>" width="60%"><select name="target"><?=$forumselect?></select></td></tr>
</table></td></tr></table><br><center><input type="submit" name="mergesubmit" value="<?=$lang['submit']?>"></center></form>
