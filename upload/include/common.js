/******************************************************************************
  Crossday Discuz! Board - Common Modules for Discuz!
  Modified by: Crossday Studio (http://crossday.com)
  Based upon:  http://www.cnzzz.com
*******************************************************************************/

var sPop = null;
var postSubmited = false;

function ctlent(obj) {
	if(postSubmited == false && (event.ctrlKey && window.event.keyCode == 13) || (event.altKey && window.event.keyCode == 83)) {
		if(this.document.input.pmsubmit) {
			postSubmited = true;
			this.document.input.pmsubmit.disabled = true;
			this.document.input.submit();
		} else if(validate(this.document.input)) {
			postSubmited = true;
			if(this.document.input.topicsubmit) this.document.input.topicsubmit.disabled = true;
			if(this.document.input.replysubmit) this.document.input.replysubmit.disabled = true;
			if(this.document.input.editsubmit) this.document.input.editsubmit.disabled = true;
			this.document.input.submit();
		}
	}
}
function checkall(form, prefix) {
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name != 'chkall' && (!prefix || (prefix && e.name.match(prefix)))) {
			e.checked = form.chkall.checked;
		}
	}
}

function findobj(n, d) {
	var p,i,x; if(!d) d=document;
	if((p=n.indexOf("?"))>0 && parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document;
		n=n.substring(0,p);
	}
	if(!(x=d[n])&&d.all) x=d.all[n];
	for(i=0;!x && i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x && d.layers&&i>d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	return x;
}

function copycode(obj) {
	var rng = document.body.createTextRange();
	rng.moveToElementText(obj);
	rng.scrollIntoView();
	rng.select();
	rng.execCommand("Copy");
	rng.collapse(false);
}

function dzconfirm(theURL, ConfirmMsg) {
	if (confirm( ConfirmMsg ))	{
		window.location.href=theURL;
	} else {
		return;
	} 
}
