<?php

/*
	[DISCUZ!] utilities/repair.php - generally repair of database
	This is NOT a freeware, use is subject to license terms

	Version: 2.0.0
	Author: Crossday (info@discuz.net)
	Copyright: Crossday Studio (www.crossday.com)
	Last Modified: 2002/12/6 17:00
*/

error_reporting(7);

define('IN_DISCUZ', true);

require "./config.php";
require "./include/db_$database.php";

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
$db->select_db($dbname);

if(!get_cfg_var("register_globals")) {
	foreach($HTTP_GET_VARS as $key => $val) {
		$$key = $val;
	}
}

function checktable($table, $loops = 0) {
  global $db, $nohtml;

   $result = $db->query("CHECK TABLE $table");
   if (!$nohtml) {
     echo "<tr bgcolor='#CCCCCC'><td colspan=4 align='center'>检查数据表 $table</td></tr>";
     echo "<tr><td>表名</td><td>操作</td><td>类型</td><td>返回信息</td></tr>";
   } else {
     echo "\n>>>>>>>>>>>>>检查数据表 $table<br>\n";
     echo "---------------------------------<br>\n";
   }
   $error = 0;
   while ($r = $db->fetch_row($result)) {
     if ($r[2] == 'error') {
       if ($r[3] == "数据表不支持检查和修复") {
         $r[2] = '状态';
         $r[3] = '数据表不支持检查,修复和优化';
         unset($bgcolor);
         $nooptimize = 1;
       } else {
         $error = 1;
         $bgcolor = 'red';
         unset($nooptimize);
       }
     } else {
       unset($bgcolor);
       unset($nooptimize);
     }
     if (!$nohtml) {
       echo "<tr><td>$r[0]</td><td>$r[1]</td><td bgcolor='$bgcolor'>$r[2]</td><td>$r[3]</td></tr>";
     } else {
       echo "$r[0] | $r[1] | $r[2] | $r[3]<br>\n";
     }
   }
   if ($error) {
     if (!$nohtml) {
       echo "<tr><td colspan=4 align='center'>修复数据表 $table</td></tr>";
     } else {
       echo ">>>>>>>>>>>>>修复数据表 $table<br>\n";
     }
     $result2=$db->query("REPAIR TABLE $table");
     if ($result2[3]!='OK')
       $bgcolor='red';
     else
       unset($bgcolor);
     if (!$nohtml) {
       echo "<tr><td>$result2[0]</td><td>$result2[1]</td><td>$result2[2]</td><td bgcolor='$bgcolor'>$result2[3]</td></tr>";
     } else {
       echo "$result2[0] | $result[1] | $result2[2] | $result2[3]<br>\n";
     }
   }
   if (($result2[3]=='OK'||!$error)&&!$nooptimize) {
     if (!$nohtml) {
       echo "<tr><td colspan=4 align='center'>优化数据表 $table</td></tr>";
     } else {
       echo ">>>>>>>>>>>>>优化数据表 $table<br>\n";
     }
     $result3=$db->query("OPTIMIZE TABLE $table");
     $error=0;
     while ($r3=$db->fetch_row($result3)) {
       if ($r3[2]=='error') {
         $error=1;
         $bgcolor='red';
       } else {
         unset($bgcolor);
       }
       if (!$nohtml) {
         echo "<tr><td>$r3[0]</td><td>$r3[1]</td><td bgcolor='$bgcolor'>$r3[2]</td><td>$r3[3]</td></tr>";
       } else {
         echo "$r3[0] | $r3[1] | $r3[2] | $r3[3]<br><br>\n";
       }
     }
   }
   if ($error&&$loops) {
     checktable($table,($loops-1));
   }
}

if ($check) {

  $tables=$db->query("SHOW TABLES");

  if (!$nohtml) {
    echo "<HTML><HEAD></HEAD><BODY><table border=1 cellspacing=0 cellpadding=4 STYLE=\"font-family: Tahoma, Verdana, 宋体; font-size: 11px\">";
  }

  if ($iterations) {
    $iterations--;
  }
  while ($table=$db->fetch_row($tables, MYSQL_BOTH)) {

     $answer=checktable($table[0],$iterations);
     if (!$nohtml) {
       echo "<tr><td colspan=4>&nbsp;</td></tr>";
     }
     flush();
  }

  if (!$nohtml) {
    echo "</table></BODY></HTML>";
  }
} else {
  echo "<HTML><HEAD></HEAD><BODY STYLE=\"font-family: Tahoma, Verdana, 宋体; font-size: 11px\"><b>数据库修复实用工具 REPAIR for Discuz! (MySQL 3.23+ required)</b><br><br>".
       "本程序可以尽量修复出现错误的数据库,同时对数据表进行优化,当然,它不是任何错误都能修复.<br>".
       "本程序由 jelsoft 为其产品 VBulletin 编写,但 jelsoft 不对此提供技术支持.<br>".
       "Crossday Studio 对其进行了一些修改和优化,使它可以用于 Discuz! 及新版 PHP.<br><br>".
       "注意:<br><br>".
       "<b>本程序需放于 Discuz! 目录中才能使用</b><br><br>".
       "用法:<br><br>".
       "<b>iterations=x</b> (x 是尝试修复数据表的次数)<br>".
       "<b>nohtml=1</b> (不输出 html 页面)<br>".
       "<b>check=1</b> (没有此选项,本程序将不进行任何操作而显示本帮助页面)<br><br>".
       "用法举例:<br><br>".
       "<b><a href=\"repair.php?html=1&check=1\">repair.php?html=1&check=1</a></b> (最常用的用法,将检查或修复数据库,返回 html 格式的操作结果)<br>".
       "<b><a href=\"repair.php?check=1&iterations=5\">repair.php?check=1&iterations=5</a></b> (尝试五次修复数据库)".
       "</BODY></HTML>";
}
?>
