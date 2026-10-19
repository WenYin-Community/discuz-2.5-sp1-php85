<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<html>
<head>
<title>Discuz! Administrators' Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; ">
</head>

<frameset cols="160,*" frameborder="no" border="0" framespacing="0" rows="*"> 
<frame name="menu" noresize scrolling="yes" src="admincp.php?action=menu&sid=<?=$sid?>">
<frameset rows="20,*" frameborder="no" border="0" framespacing="0" cols="*"> 
<frame name="top" noresize scrolling="no" src="admincp.php?action=top&sid=<?=$sid?>">
<frame name="main" noresize scrolling="yes" src="admincp.php?action=<?=$action?>&extr=<?=$extr?>&sid=<?=$sid?>">
</frameset></frameset></html>
