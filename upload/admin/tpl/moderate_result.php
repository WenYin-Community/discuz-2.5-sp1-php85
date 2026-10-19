<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><form method="post" action="admincp.php?action=moderate">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr>
<td class="header" colspan="2"><?=$lang['moderate_result']?> <?=$threadcount?></td>
</tr>
<?	if(!$threadcount) { ?>
	<tr><td bgcolor="<?=ALTBG2?>" colspan="2"><?=$lang['moderate_thread_nonexistence']?></td></tr>

<?  } else {
		if(!$detail) { ?>
			<input type="hidden" name="tids" value="<?=$tids?>">
		<?}?>
		<input type="hidden" name="fids" value="<?=$fids?>">
<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="operation" value="move" onclick="this.form.modsubmit.disabled=false;"> <?=$lang['moderate_move']?></td>
<td bgcolor="<?=ALTBG2?>"><select name="forum"><?=forumselect()?></select></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="operation" value="delete" onclick="this.form.modsubmit.disabled=false;"> <?=$lang['moderate_delete']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="checkbox" name="donotupdatemember" value="1" checked> <?=$lang['moderate_delete_no_update_member']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="operation" value="stick" onclick="this.form.modsubmit.disabled=false;"> <?=$lang['moderate_stick']?></td>
<td bgcolor="<?=ALTBG2?>">
<input type="radio" name="stick_level" value="0" checked> <?=$lang['remove']?> &nbsp; &nbsp; 
<input type="radio" name="stick_level" value="1"> <img src="<?=IMGDIR?>/star.gif"> &nbsp; &nbsp; 
<input type="radio" name="stick_level" value="2"> <img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"> &nbsp; &nbsp; 
<input type="radio" name="stick_level" value="3"> <img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="operation" value="adddigest" onclick="this.form.modsubmit.disabled=false;"> <?=$lang['moderate_add_digest']?></td>
<td bgcolor="<?=ALTBG2?>">
<input type="radio" name="digest_level" value="0" checked> <?=$lang['remove']?> &nbsp; &nbsp; 
<input type="radio" name="digest_level" value="1"> <img src="<?=IMGDIR?>/star.gif"> &nbsp; &nbsp; 
<input type="radio" name="digest_level" value="2"> <img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"> &nbsp; &nbsp; 
<input type="radio" name="digest_level" value="3"> <img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"><img src="<?=IMGDIR?>/star.gif"></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="operation" value="deleteattach" onclick="this.form.modsubmit.disabled=false;"> <?=$lang['moderate_delete_attach']?></td>
<td bgcolor="<?=ALTBG2?>">&nbsp;</td>
</tr>
<?

		if($detail) {

?>
</table></td></tr></table><br><br><table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>"><table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header" align="center"><td>&nbsp;</td><td><?=$lang['subject']?></td><td><?=$lang['forum']?></td><td><?=$lang['author']?></td><td nowrap><?=$lang['moderate_replies']?></td><td nowrap><?=$lang['moderate_views']?></td><td><?=$lang['moderate_lastpost']?></td></tr>
<?=$threads?>
<?}}?>

</table></td></tr></table><br><center><input type="submit" name="modsubmit" value="<?=$lang['submit']?>" disabled></center></form>
