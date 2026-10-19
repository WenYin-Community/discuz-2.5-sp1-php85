<?PHP

/*
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: [DISCUZ!]  Crossday Discuz! Board                                    ::
 :: (c) 2001-2005 Comsenz Technology Ltd (www.discuz.com)                ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 :: Author:  Crossday (tech@discuz.com) Cnteacher (cnteacher@discuz.com) ::
 :: Version: 2.5F   2004/10/01 05:15                                     ::
 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$timestamp = time();
$errmsg = '';

$dberror = $this->error();
$dberrno = $this->errno();

if($dberrno == 1114) {

?>
<html>
<head>
<title>Max Onlines Reached</title>
</head>
<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="600" align="center" height="85%">
  <tr align="center" valign="middle">
    <td>
    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 9px">
    <tr>
      <td valign="middle" align="center" bgcolor="#EBEBEB">
        <br><b style="font-size: 10px">Forum onlines reached the upper limit</b>
        <br><br><br>Sorry, the number of online visitors has reached the upper limit.
        <br>Please wait for someone else going offline or visit us in idle hours.
        <br><br>
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
<?

	exit();

} else {

	$discuz_user = $GLOBALS['_DSESSION']['discuz_user'] ? $GLOBALS['_DSESSION']['discuz_user'] : $GLOBALS['HTTP_COOKIE_VARS']['_discuz_user'];
	if($message) {
		$errmsg = "<b>Discuz! info</b>: $message\n\n";
	}
	if($discuz_user) {
		$errmsg .= "<b>User</b>: $discuz_user\n";
	}
	$errmsg .= "<b>Time</b>: ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS['timeoffset'] * 3600))."\n";
	$errmsg .= "<b>Script</b>: ".$GLOBALS['PHP_SELF']."\n\n";
	if($sql) {
		$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n";
	}
	$errmsg .= "<b>Error</b>:  $dberror\n";
	$errmsg .= "<b>Errno.</b>:  $dberrno";

	echo "</table></table></table></table></table>\n";
	echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
	echo nl2br($errmsg);
	if($GLOBALS['adminemail']) {
		$errnos = array();
		$errorlogs = '';
		if($errlog = @file('./forumdata/dberror.log')) {
			for($i = 0; $i < count($errlog); $i++) {
				$log = explode("\t", $errlog[$i]);
				if(($timestamp - $log[0]) > 86400) {
					$errlog[$i] = "";
				} else {
					$errnos[] = $log[1];
				}
			}
		}
		if(!in_array($dberrno, $errnos)) {
			$errorlogs .= "$timestamp\t$dberrno\n";
			echo "<br><br>An error report has been dispatched to our administrator.";
			@sendmail($GLOBALS['adminemail'], '[Discuz!] MySQL Error Report',
					"There seems to have been a problem with the database of your Discuz! Board\n\n".
					strip_tags($errmsg)."\n\n".
					"Please check-up your MySQL server and forum scripts, similar errors will not be reported again in recent 24 hours\n".
					"If you have troubles in solving this problem, please visit Discuz! Community http://www.Discuz.net.");
		} else {
			echo '<br><br>Similar error report has beed dispatched to administrator before.';
		}
		for($i = 0; $i < count($errlog); $i++) {
			$errorlogs .= $errlog[$i] ? trim($errlog[$i])."\n" : NULL;
		}
		@$fp = fopen(DISCUZ_ROOT.'./forumdata/dberror.log', 'w');
		@flock($fp, 2);
		@fwrite($fp, $errorlogs);
		@fclose($fp);
	}
	echo '</p>';
	exit();

}

?>