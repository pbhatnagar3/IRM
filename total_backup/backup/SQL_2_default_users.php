<?php
/*
 SQL_2_default_users.php
 This file replaces USERS with an ALIAS
 Last Update: Sep-25-2013
 Author(s): Greg Krudysz
*/

require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();

if ($status == 'admin') {
	$host = $_SERVER['SERVER_NAME'];
	if ($host=='localhost'){
//==============================================================================
// connect to database
$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)){throw new Question_Control_Exception($mdb2->getMessage());}

$query = 'SELECT id FROM users WHERE id > 2';  

$res =& $mdb2->query($query);
if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
$users = $res->fetchCol();

echo 'N-students: '.count($users).'<br>';

for ($u=0; $u < count($users); $u++) {
  //----***--------//
	//echo $users[$u].'<br>';
	$query = 'UPDATE users SET first_name="FIRST_NAME'.$users[$u].'", last_name="LAST_NAME'.$users[$u].'", username="USERNAME'.$users[$u].'", password="PASSWORD'.$users[$u].'" WHERE id='.$users[$u];
	//echo $query.'<br>';
	$res =& $mdb2->query($query);
	if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
	//----***--------//
}
$path = '/var/www/';
<<<<<<< HEAD
$file = 'ITS_'.date("m-d-y").'_ALIAS.sql'; 
$cmd = 'mysqldump --single-transaction --skip-add-locks its -u root -pcsip > '.$path.$file;
echo 'Aliased DB and saved with command:<pre class="cmd">'.$cmd.'</pre>';
exec($cmd . " > /dev/null &");
=======
$file = 'ITS_'.date("m-d-Y").'_ALIAS.sql'; 
$cmd = 'mysqldump --single-transaction --skip-add-locks its -u root -pcsip > '.$path.$file;
echo 'Aliased DB and saved with command:<pre class="cmd">'.$cmd.'</pre>';
//exec($cmd . " > /dev/null &");
exec($cmd);
>>>>>>> 5d477cfe7a07b5615f2d2cbda771d739cf5a41c9
echo '<br>finished<br>';

// mysqldump --single-transaction --skip-add-locks -h its.vip.gatech.edu its -u root -p > ITS_123.sql
// mysqldump --single-transaction --skip-add-locks its -u root -pcsip > ITS_123.sql
// mysql -u root -D its -p < ITS_VIP_02-12-2011.sql
//sudo apt-get install vsftpd

//==============================================================================
}else{
	echo '<center><p>This file can not run on <b>'.$host.'</b> server - it can only run on a <b>localhost</b> server!</p></center>';
}
}
?>
