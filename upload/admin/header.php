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

@header('Content-Type: text/html; charset='.CHARSET);

?>
<html>
<head>
<? include template('css'); ?>
</head>

<body leftmargin="0" topmargin="0">
<table cellspacing="0" cellpadding="2" border="0" width="100%" height="100%" bgcolor="<?=ALTBG2?>">
<tr valign="middle" class="smalltxt">
<td width="33%"><a href="http://www.discuz.com" target="_blank">Discuz! <?=$version?> <?=$lang['admincp']?></a></td>
<td width="33%" align="center"><a href="http://www.Discuz.net" target="_blank"><?=$lang['header_offical']?></a></td>
<td width="34%" align="right"><a href="index.php" target="_blank"><?=$lang['header_home']?></a></TD>
</tr>
</table>
</body></html>
