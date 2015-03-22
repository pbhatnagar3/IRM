<?php
/*
 SQL_2_default_users.php
 This file replaces USERS with an ALIAS
 Date: Nov-02-10
 Last Update: May-29-2013
 Author(s): Greg Krudysz
*/

require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

/*
error_reporting(E_ALL);
require_once("config.php");
require_once(INCLUDE_DIR . "common.php");
require_once(INCLUDE_DIR . "User.php");
*/
session_start();
//==============================================================================
// connect to database
$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)){throw new Question_Control_Exception($mdb2->getMessage());}

$query = 'SELECT id FROM users WHERE id > 2';  

$res =& $mdb2->query($query);
if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
$users = $res->fetchCol();

echo count($users).'<p>';
for ($u=0; $u < count($users); $u++) {
  //----***--------//
	echo $users[$u].'<br>';
  $query = 'UPDATE users SET first_name="FIRST_NAME'.$users[$u].'", last_name="LAST_NAME'.$users[$u].'", username="USERNAME'.$users[$u].'", password="PASSWORD'.$users[$u].'" WHERE id='.$users[$u];
  //echo $query.'<br>';
	$res =& $mdb2->query($query);
	if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
	//----***--------//
}

$ex = exec("start.sh");

echo 'finished';
// mysqldump --single-transaction --skip-add-locks -h its.vip.gatech.edu its -u root -p > ITS_123.sql
// mysqldump --single-transaction --skip-add-locks its -u root -pcsip > ITS_123.sql
// mysql -u root -D its -p < ITS_VIP_02-12-2011.sql
//sudo apt-get install vsftpd

//==============================================================================
?>
