<?php  if(!defined('IN_ADMINCP')) exit('Access Denied'); ?>

<script language="JavaScript">
var n = 0;
function displayHTML(obj) {
	win = window.open(" ", 'popup', 'toolbar = no, status = no, scrollbars=yes');
	win.document.write("" + obj.value + "");
}
function HighlightAll(obj) {
	obj.focus();
	obj.select();
	if (document.all) {
		obj.createTextRange().execCommand("Copy");
		window.status = "<?=$lang['templates_edit_clickboard']?>";
		setTimeout("window.status=''", 1800);
	}
}
function findInPage(obj, str) {
	var txt, i, found;
	if (str == "") {
		return false;
	}
	if (document.layers) {
		if (!obj.find(str)) {
			while(obj.find(str, false, true)) {
				n++;
			}
		} else {
			n++;
		}
		if (n == 0) {
			alert("<?=$lang['templates_edit_keyword_not_found']?>");
		}
	}
	if (document.all) {
		txt = obj.createTextRange();
		for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
			txt.moveStart('character', 1);
			txt.moveEnd('textedit');
		}
		if (found) {
			txt.moveStart('character', -1);
			txt.findText(str);
			txt.select();
			txt.scrollIntoView();
			n++;
		} else {
			if (n > 0) {
				n = 0;
				findInPage(str);
			} else {
				alert("<?=$lang['templates_edit_keyword_not_found']?>");
			}
		}
	}
	return false;
}
</script>
<form method="post" action="admincp.php?action=tpledit&templateid=<?=$templateid?>&fn=<?=$fn?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="keyword" value="<?=$keywordenc?>">
<table cellspacing="0" cellpadding="0" border="0" width="60%" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%">
<tr><td class="header"><?=$lang['templates_edit']?> - <?=$fn?></td></tr>
<tr><td bgcolor="<?=ALTBG1?>" align="center">
<textarea cols="100" rows="25" name="templatenew"><?=$content?></textarea><br><br>
<input name="search" type="text" accesskey="t" size="20" onChange="n=0;">
<input type="button" value="<?=$lang['search']?>" accesskey="f" onClick="findInPage(this.form.templatenew, this.form.search.value)">&nbsp;&nbsp;&nbsp;
<input type="button" value="<?=$lang['return']?>" accesskey="e" onClick="history.go(-1)">
<input type="button" value="<?=$lang['preview']?>" accesskey="p" onClick="displayHTML(this.form.templatenew)">
<input type="button" value="<?=$lang['copy']?>" accesskey="c" onClick="HighlightAll(this.form.templatenew)">&nbsp;&nbsp;&nbsp;
<input type="submit" name="editsubmit" value="<?=$lang['submit']?>">
</td></tr></table></td></tr></table>
</form>
