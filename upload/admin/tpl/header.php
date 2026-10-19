<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=CHARSET?>">

<? include $css;?>

<script language="JavaScript" src="include/common.js"></script>
<script language="JavaScript">
function checkall(form, prefix) {
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name != 'chkall' && (!prefix || (prefix && e.name.match(prefix)))) {
			e.checked = form.chkall.checked;
		}
	}
}

function redirect(url) {
	window.location.replace(url);
}
</script>
</head>
<body <?=BGCODE?> text="<?=TEXT?>" leftmargin="10" topmargin="10">
<br>