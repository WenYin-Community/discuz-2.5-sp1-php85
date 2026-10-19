<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header">
	<td ><?=$lang['plugin_links']?></td></tr>
<tr>
	<td bgcolor="<?=ALTBG2?>" align="right">
		<a href="admincp.php?action=plugin&mod=list" ><?=$lang['plugin_backtolist']?></a> | 
		<a href="admincp.php?action=plugin&mod=setup&do=add&group=<?=$group?>" ><?=$lang['plugin_addsetting']?></a> |
		<a href="admincp.php?action=plugin&mod=updatecache" ><?=$lang['plugin_refresh']?></a>
	</td></tr>
</table></td></tr></table>
<br>
