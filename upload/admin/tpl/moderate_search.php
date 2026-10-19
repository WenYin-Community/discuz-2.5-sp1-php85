<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><form method="post" action="admincp.php?action=moderate">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr>
<td class="header" colspan="2"><?=$lang['moderate_search']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_detail']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="checkbox" name="detail" value="1"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_forum']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><?=$forumselect?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_viewless']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="viewsless" size="40" value="<?=$viewsless?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_viewmore']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="viewsmore" size="40" value="<?=$viewsmore?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_replyless']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="repliesless" size="40" value="<?=$repliesless?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_replymore']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="repliesmore" size="40" value="<?=$repliesmore?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_beforeday']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="beforedays" size="40" value="<?=$befordays?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_noreplyday']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="noreplydays" size="40" value="<?=$noreplydays?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_user']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="users" size="40" value="<?=$users?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_keyword']?></td>
<td bgcolor="<?=ALTBG2?>" align="right"><input type="text" name="keywords" size="40" value="<?=$keywords?>"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_sticky']?></td>
<td bgcolor="<?=ALTBG2?>" align="right">
<input type="radio" name="sticky" value="0" <?=$checksticky[0]?>> <?=$lang['unlimited']?>&nbsp;
<input type="radio" name="sticky" value="1" <?=$checksticky[1]?>> <?=$lang['moderate_search_include_yes']?>&nbsp;
<input type="radio" name="sticky" value="2" <?=$checksticky[2]?>> <?=$lang['moderate_search_include_no']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_digest']?></td>
<td bgcolor="<?=ALTBG2?>" align="right">
<input type="radio" name="digest" value="0" <?=$checkdigest[0]?>> <?=$lang['unlimited']?>&nbsp;
<input type="radio" name="digest" value="1" <?=$checkdigest[1]?>> <?=$lang['moderate_search_include_yes']?>&nbsp;
<input type="radio" name="digest" value="2" <?=$checkdigest[2]?>> <?=$lang['moderate_search_include_no']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><?=$lang['moderate_search_attach']?></td>
<td bgcolor="<?=ALTBG2?>" align="right">
<input type="radio" name="attach" value="0" <?=$checkattach[0]?>> <?=$lang['unlimited']?>&nbsp;
<input type="radio" name="attach" value="1" <?=$checkattach[1]?>> <?=$lang['moderate_search_include_yes']?>&nbsp;
<input type="radio" name="attach" value="2" <?=$checkattach[2]?>> <?=$lang['moderate_search_include_no']?></td>
</tr>

</table></td></tr></table><br>
<center><input type="submit" name="searchsubmit" value="<?=$lang['submit']?>"></center>
</form>
