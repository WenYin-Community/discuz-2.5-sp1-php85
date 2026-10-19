<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<br>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr class="header"><td ><?=$lang['tips']?></td></tr>
<tr><td bgcolor="<?=ALTBG2?>"><?=$tip?></td></tr>
</table></td></tr></table>
<br>
<form method="post" action="<?=$submitaction?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<?=$addvalue?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr ><td height="100" align="center" bgcolor="<?=ALTBG1?>"><?=$message?></td></tr>
</table></td></tr></table>
<p align='center'>
<input type="submit" name='yes' value=' Yes '>&nbsp;&nbsp;&nbsp;
<input type="submit" name='no' value=' No! '></p>
</form>