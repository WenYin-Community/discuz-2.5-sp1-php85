<?php

/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2001-2005 Comsenz Technology Ltd (www.discuz.com)                ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (tech@discuz.com) Cnteacher (cnteacher@discuz.com) ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

if(!defined('IN_ADMINCP')) {
        exit('Access Denied');
}

if(file_exists(DISCUZ_ROOT.'./install.php')) {
	@unlink(DISCUZ_ROOT.'./install.php');
	if(file_exists(DISCUZ_ROOT.'./install.php')) {
		dexit('Please delete install.php via FTP!');
	}
}

require DISCUZ_ROOT.'./include/attachment.php';

if($adminid == 1) {

	$serverinfo = PHP_OS.' / PHP v'.PHP_VERSION;
	$serverinfo .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
	$dbversion = $db->result($db->query("SELECT VERSION()"), 0);

	if(@ini_get('file_uploads')) {
		$fileupload = $lang['yes'].': file '.ini_get('upload_max_filesize').' - form '.ini_get('post_max_size');
	} else {
		$fileupload = '<font color="red">'.$lang['no'].'</font>';
	}

	$forumselect = $groupselect = '';
	$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups ORDER BY creditslower, groupid");
	while($group = $db->fetch_array($query)) {
		$groupselect .= '<option value="'.$group['groupid'].'">'.$group['grouptitle'].'</option>';
	}
	$query = $db->query("SELECT fid, name FROM $table_forums WHERE type='forum' OR type='sub'");
	while($forum = $db->fetch_array($query)) {
		$forumselect .= '<option value="'.$forum['fid'].'">'.$forum['name'].'</option>';
	}

	$dbsize = 0;
	$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'", 'SILENT');
	while($table = $db->fetch_array($query)) {
		$dbsize += $table['Data_length'] + $table['Index_length'];
	}
	$dbsize = $dbsize ? sizecount($dbsize) : $lang['unknown'];

	$showattach = isset($showattach) ? $showattach : 0;
	if ($showattach) { 
		$attachsize = dirsize(DISCUZ_ROOT.$attachdir);
		$attachsize = is_numeric($attachsize) ? sizecount($attachsize) : $lang['unknown'];
	}else{
		$attachsize='<a href="admincp.php?action=home&showattach=1">'.$lang['showattachsize'].'</a>'; 
	}
}

cpheader();

?>
<font class="mediumtxt">
<b><?=$lang['home_welcome_to']?> <a href="http://www.Discuz.net" target="_blank">Discuz! <?=$version?></a> Administrators' Control Panel</b><br>
Copyright&copy; <a href="http://www.discuz.com" target="_blank">Comsenz Technology Ltd</a>, 2002-2004.

<br><br><br><table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="3"><?=$lang['home_stuff']?></td></tr>

<? if($adminid == 1) { ?>

<form method="post" action="admincp.php?action=members">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_edit_member']?></td>
<td><input type="text" size="30" name="username"></td><td><input type="submit" name="searchsubmit" value="<?=$lang['submit']?>"></td></tr></form>

<form method="post" action="admincp.php?action=export&type=mini&saveto=server">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_data_export']?></td>
<td><input type="text" size="30" name="filename" value="./forumdata/dz_<?=date("md")."_".random(5)?>.sql"></td><td><input type="submit" name="exportsubmit" value="<?=$lang['submit']?>"></td></tr></form>

<form method="post" action="admincp.php?action=forumdetail">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_edit_forum']?></td>
<td><select name="fid"><?=$forumselect?></select></td><td><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>

<form method="post" action="admincp.php?action=usergroups">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_edit_group']?></td>
<td><select name="edit"><?=$groupselect?></td><td><input type="submit" value="<?=$lang['submit']?>"></td></tr></form>

<? } ?>

</table></td></tr></table></td></tr></table><br><br>

<? if($adminid == 1) { ?>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['home_sys_info']?></td></tr>
<tr bgcolor="<?=ALTBG2?>"><td width="50%"><?=$lang['home_environment']?></td><td><?=$serverinfo?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_database']?></td><td><?=$dbversion?></td></tr>
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_upload_perm']?></td><td><?=$fileupload?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_database_size']?></td><td><?=$dbsize?></td></tr>
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_attach_size']?></td><td><?=$attachsize?></td></tr>
</table></td></tr></table></td></tr></table><br><br>

<? } ?>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%">
<tr class="header"><td colspan="2"><?=$lang['home_dev']?></td></tr>
<tr bgcolor="<?=ALTBG2?>"><td width="50%"><?=$lang['home_dev_chief']?></td><td><a href="http://discuz.net/viewpro.php?uid=1" target="_blank">Kevin (Crossday) Dell</a></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_dev_market']?></td><td><a href="http://www.comsenz.com" target="_blank">&#x5317;&#x4EAC;&#x5EB7;&#x76DB;&#x4E16;&#x7EAA;&#x79D1;&#x6280;&#x6709;&#x9650;&#x516C;&#x53F8;</a></td></tr>
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_dev_freegroup']?></td><td><a href="http://discuz.net/viewpro.php?uid=1" target="_blank">Kevin (Crossday) Dell</a>, <a href="http://discuz.net/viewpro.php?uid=859" target="_blank">Cnteacher</a>, <a href="http://discuz.net/viewpro.php?uid=9132" target="_blank">PK0909</a>
</td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_dev_skins']?></td><td><a href="http://discuz.net/viewpro.php?uid=233" target="_blank">Huli Hutu</a>,
<a href="http://discuz.net/viewpro.php?uid=13877" target="_blank">artery</a>, 
<a href="http://discuz.net/viewpro.php?uid=122" target="_blank">Lao Gui</a>, <a href="http://discuz.net/viewpro.php?uid=159" target="_blank">Tyc</a>, <a href="http://discuz.net/viewpro.php?uid=177" target="_blank">stoneage</a>
</td></tr>
<tr bgcolor="<?=ALTBG2?>"><td><?=$lang['home_dev_community_site']?></td><td><a href="http://www.discuz.net" target="_blank">http://www.Discuz.net</a></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td><?=$lang['home_dev_free_site']?></td><td><a href="http://www.freediscuz.net" target="_blank">http://www.FreeDiscuz.net</a></td></tr>
</table></td></tr></table></td></tr></table>