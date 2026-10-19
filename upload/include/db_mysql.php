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

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class dbstuff {
	var $querynum = 0;

	function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0) {
		if($pconnect) {
			if(!@mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		} else {
			if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		}

		mysql_select_db($dbname);
	}

	function select_db($dbname) {
		return mysql_select_db($dbname);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
		if ($GLOBALS['debug_on']){
			$mtime = explode(' ', microtime());
			$stime = $mtime[1] + $mtime[0];
		}
		if($type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query')) {
			$query = mysql_unbuffered_query($sql);
		} else {
			if($type == 'CACHE' && intval(mysql_get_server_info()) >= 4) {
				$sql = 'SELECT SQL_CACHE'.substr($sql, 6);
			}
			if(!($query = mysql_query($sql)) && $type != 'SILENT') {
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
		return mysql_affected_rows();
	}

	function error() {
		return mysql_error();
	}

	function errno() {
		return intval(mysql_errno());
	}

	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		$id = mysql_insert_id();
		return $id;
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function close() {
		return mysql_close();
	}

	function halt($message = '', $sql = '') {
		require_once DISCUZ_ROOT.'./include/db_mysql_error.php';
	}
}

?>