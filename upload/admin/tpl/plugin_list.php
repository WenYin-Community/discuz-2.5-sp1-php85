<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr class="header"><td ><?=$lang['tips']?></td></tr>
<tr><td bgcolor="<?=ALTBG2?>"><?=$lang['plug_edit_info']?></td></tr>
</table></td></tr></table>
<br>
<form method="post" action="admincp.php?action=plugin&mod=edit">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">

<tr class="header" align="center"><td width="24" ></td><td ><?=$lang['plug_title']?></td><td width="50"><?=$lang['version']?></td><td width="100"><?=$lang['plug_author']?></td><td align="center" width="40"><?=$lang['plug_stats']?></td><td width='60'><?=$lang['edit']?></td></tr>

<?
		$plugcount = 0 ;
		while($plugin = $db->fetch_array($query)) {
			$plugcount ++;
			$plugin[plug_stats] = $pluginstat["$plugin[plug_stats]"];
			echo "<tr><td width='24' bgcolor=\"".ALTBG2."\"><img src=\"images/admincp/settings_folder.gif\" border=\"0\"></td><td bgcolor=\"".ALTBG1."\"><a href='".$basecplink."&mod=setup&group=".$plugin[plug_key]."'><b>$plugin[plug_title]</b></a><br>$plugin[plug_desc]</td><td bgcolor=\"".ALTBG2."\" align='center'>$plugin[plug_version]</td><td bgcolor=\"".ALTBG1."\" align='center'>$plugin[plug_author]</td><td bgcolor=\"".ALTBG2."\" align='center'>$plugin[plug_stats]</td><td  bgcolor=\"".ALTBG2."\" align ='center'><a href=\"".$basecplink."&mod=edit&id=".$plugin[plug_id]."\" ><img src=\"images/admincp/acp_edit.gif\" border=\"0\" alt='edit'></a>&nbsp;<a href=\"".$basecplink."&mod=delete&id=".$plugin[plug_id]."\" ><img src=\"images/admincp/acp_del.gif\" border=\"0\" alt='delete'></a>&nbsp;<a href=\"".$basecplink."&mod=setup&do=explord&group=".$plugin[plug_key]."\" ><img src=\"images/admincp/acp_down.gif\" border=\"0\" alt='download'></a></td>";
		}
		if (!$plugcount){
			echo "<tr><td width='24' bgcolor=\"".ALTBG2."\"><img src=\"images/admincp/settings_folder.gif\" border=\"0\"></td><td bgcolor=\"".ALTBG1."\" align='center' colspan=\"5\">".$lang['no_plug']."</td>";
		}
?>
</table></td></tr></table>
</form>