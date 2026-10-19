<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><form method="post" action="admincp.php?action=members">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr><td class="header" colspan="2"><?=$lang['members_search']?></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['admingroup']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>">
<select name="admingroupid">
<option value=""><?=$lang['members_search_all_groups']?></option>
<?=$adminselect?>
</select></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['usergroup']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>">
<select name="usergroupid">
<option value=""><?=$lang['members_search_all_groups']?></option>
<?=$groupselect?>
</select></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_username']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="username" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_email']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="srchemail" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_regdatebefore']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="regdatebefore" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_regdateafter']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="regdateafter" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_creditslower']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="creditslower" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_creditshigher']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="creditshigher" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_postslower']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="postslower" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_postshigher']?>:</td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="postshigher" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_location']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="srchlocation" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_signature']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="srchsig" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_awaydays']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="awaydays" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_regip']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="regip" size="40"></td></tr>

<tr><td bgcolor="<?=ALTBG1?>"><?=$lang['members_search_lastip']?></td>
<td align="right" bgcolor="<?=ALTBG2?>"><input type="text" name="lastip" size="40"></td></tr>

</table></td></tr></table><br><center>
<input type="submit" name="searchsubmit" value="<?=$lang['members_search']?>"> &nbsp; 
<input type="submit" name="deletesubmit" value="<?=$lang['members_delete']?>"></center></form>
