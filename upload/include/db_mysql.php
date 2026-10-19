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

// PHP 8 port note: ext/mysql was removed in PHP 7. This class keeps the exact
// same API used by the whole board (query / fetch_array / result / ...) but
// talks to MySQL through mysqli instead.

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

// the constants of the removed mysql extension, mapped onto their mysqli twins
if(!defined('MYSQL_ASSOC')) {
	define('MYSQL_ASSOC', MYSQLI_ASSOC);
}
if(!defined('MYSQL_NUM')) {
	define('MYSQL_NUM', MYSQLI_NUM);
}
if(!defined('MYSQL_BOTH')) {
	define('MYSQL_BOTH', MYSQLI_BOTH);
}

// PHP 8.1+ lets mysqli throw exceptions by default, but the board inspects the
// return value of every query (and relies on the 'SILENT' flag), so the old
// "return false on error" behaviour has to be restored.
mysqli_report(MYSQLI_REPORT_OFF);

class dbstuff {
	var $link = NULL;
	var $querynum = 0;
	var $querytime = 0;
	var $queryinfo = '';

	function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0) {
		if($pconnect) {
			$dbhost = 'p:'.$dbhost;
		}
		$this->link = @mysqli_connect($dbhost, $dbuser, $dbpw);
		if(!$this->link) {
			$this->halt('Can not connect to MySQL server');
		}
		if($dbname) {
			$this->select_db($dbname);
		}
		// the board stores GBK text, the connection must match the tables
		@mysqli_query($this->link, "SET NAMES 'gbk'");
	}

	function select_db($dbname) {
		return mysqli_select_db($this->link, $dbname);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysqli_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
		if ($GLOBALS['debug_on']){
			$mtime = explode(' ', microtime());
			$stime = $mtime[1] + $mtime[0];
		}
		if($type == 'UNBUFFERED') {
			$query = mysqli_query($this->link, $sql, MYSQLI_USE_RESULT);
		} else {
			// the old SQL_CACHE hint was dropped: it is deprecated in MySQL 5.7
			// and removed in MySQL 8, and it never changed the result set
			if(!($query = mysqli_query($this->link, $sql)) && $type != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}
		$this->querynum++;
		if ($GLOBALS['debug_on']){
			$mtime = explode(' ', microtime());
			$endtime =$mtime[1] + $mtime[0] - $stime;
			$this->queryinfo .=sprintf('<li><b>%1.5f</b> %s<hr size=1 noshadow>', $endtime, $sql);
			$this->querytime += $endtime;
		}

		return $query;
	}

	function affected_rows() {
		return $this->link ? mysqli_affected_rows($this->link) : 0;
	}

	function error() {
		// the error page is also used when the connection itself failed
		return $this->link ? mysqli_error($this->link) : mysqli_connect_error();
	}

	function errno() {
		return $this->link ? intval(mysqli_errno($this->link)) : intval(mysqli_connect_errno());
	}

	function result($query, $row) {
		if(!($query instanceof mysqli_result)) {
			return FALSE;
		}
		if(!@mysqli_data_seek($query, $row)) {
			return FALSE;
		}
		$row = mysqli_fetch_row($query);
		return is_array($row) ? $row[0] : FALSE;
	}

	function num_rows($query) {
		return $query instanceof mysqli_result ? mysqli_num_rows($query) : 0;
	}

	function num_fields($query) {
		return $query instanceof mysqli_result ? mysqli_num_fields($query) : 0;
	}

	function free_result($query) {
		return $query instanceof mysqli_result ? mysqli_free_result($query) : FALSE;
	}

	function insert_id() {
		return $this->link ? mysqli_insert_id($this->link) : 0;
	}

	function fetch_row($query) {
		return $query instanceof mysqli_result ? mysqli_fetch_row($query) : FALSE;
	}

	function close() {
		if($this->link) {
			mysqli_close($this->link);
		}
	}

	function halt($message = '', $sql = '') {
		require_once DISCUZ_ROOT.'./include/db_mysql_error.php';
	}
}

?>
