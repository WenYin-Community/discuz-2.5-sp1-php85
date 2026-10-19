<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br><form method="post" action="admincp.php?action=attachments">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">

<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr><td class="header" colspan="2"><?=$lang['attachments_search']?></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_nomatched']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="checkbox" name="nomatched" value="1"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_forum']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><select name="forum"><option value="all">&nbsp;&nbsp;> <?=$lang['all']?></option>
<option value="">&nbsp;</option><?=forumselect()?></select></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_sizeless']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="sizeless" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_sizemore']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="sizemore" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_dlcountless']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="dlcountless" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_dlcountmore']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="dlcountmore" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_daysold']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="daysold" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_filename']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="filename" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['attachments_author']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="author" size="40"></td></tr>

</table></td></tr></table><br><center>
<input type="submit" name="searchsubmit" value="<?=$lang['submit']?>"></center>
</form>
