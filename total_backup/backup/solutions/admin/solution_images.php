<?php
//echo 'hi'; die();
/*
ALTER TABLE tags MODIFY COLUMN id INT AUTO_INCREMENT
ALTER TABLE tags MODIFY COLUMN name VARCHAR(64) UNIQUE KEY
ALTER TABLE index_1 CHANGE oldname newname varchar (10) ;
*/
$LAST_UPDATE = 'May-6-2012';
//--- begin timer -----//
$mtime     = explode(" ",microtime());
$starttime = $mtime[1] + $mtime[0];
//---------------------//

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   		   // or IE will pull from cache 100% of time (which is really bad) 
header("Cache-Control: no-cache, must-revalidate"); 		   // Must do cache-control headers 
header("Pragma: no-cache");

require_once ("../config.php");
global $db_dsn, $db_name, $db_table_users, $db_table_user_state;
	 
$dsn = preg_split("/[\/:@()]+/",$db_dsn);
$db_user = $dsn[1];
$db_pass = $dsn[2];
$host    = $dsn[4];
$db_name = $dsn[6];
//------------------------------------------//
	
MySQL_connect($host,$db_user,$db_pass);
MySQL_select_db($db_name);


$qid = 32;
$img_arr = array();
$query = 'SELECT images_id FROM spf_images WHERE spf_id IN(SELECT spf_id FROM spf_tags WHERE tags_id IN (SELECT tags_id FROM questions_tags WHERE questions_id='.$qid.'));';
			
$res = mysql_query($query) or die(mysql_error().' Houston we have an error');
//$tag_arr = array();	
$c=0;	
while ($row = MySQL_fetch_array($res)) {
	$img_arr[] = $row;
	//echo '<pre>'.print_r($img_arr[$c]).'</pre>';
}

foreach($img_arr as $iid){
	//echo $iid[0].'<br>';
	$mysql =  	'INSERT IGNORE INTO solutions (questions_id,image1,author,verified_by) VALUES ('.$qid.','.$iid[0].',1,1)';
	//$mysql = 	'INSERT IGNORE INTO '.$res_tb.'_'.$tag_tb.' ('.$res_tb.'_id,'.$tag_tb.'_id) VALUES ('.$question[0].','.$t[0].')';
	echo $mysql.'<br>';
	mysql_query($mysql) or die(mysql_error().' Error adding into database');
}


?>
