<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<br><br><form method="post" action="admincp.php?action=counter">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['counter_thread']?></td></tr>
<tr bgcolor="<?=ALTBG2?>">
<td align="center"><?=$lang['counter_amount']?> &nbsp; &nbsp; <input type="text" name="pertask" value="500"></td></tr>
</table></td></tr></table><br><center>
<input type="submit" name="threadsubmit" value="<?=$lang['submit']?>"> &nbsp;
<input type="reset" value="<?=$lang['reset']?>"></center></form><br>

<form method="post" action="admincp.php?action=counter">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['counter_member']?></td></tr>
<tr bgcolor="<?=ALTBG2?>">
<td align="center"><?=$lang['counter_amount']?> &nbsp; &nbsp; <input type="text" name="pertask" value="1000"></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="membersubmit" value="<?=$lang['submit']?>"> &nbsp;
<input type="reset" value="<?=$lang['reset']?>"></center></form><br>

<form method="post" action="admincp.php?action=counter">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['counter_forum']?></td></tr>
<tr bgcolor="<?=ALTBG2?>">
<td align="center"><?=$lang['counter_amount']?> &nbsp; &nbsp; <input type="text" name="pertask" value="15"></td>
</tr></table></td></tr></table><br><center>
<input type="submit" name="forumsubmit" value="<?=$lang['submit']?>"> &nbsp;
<input type="reset" value="<?=$lang['reset']?>"></center></form><br>
