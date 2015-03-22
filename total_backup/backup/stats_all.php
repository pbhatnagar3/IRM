<?php
$LAST_UPDATE = 'Sep-29-2013';
//=====================================================================//               
//Author(s): Gregory Krudysz
//=====================================================================//

//--- begin timer ---//
$mtime     = explode(" ", microtime());
$starttime = $mtime[1] + $mtime[0];
$Debug     = FALSE;
//-------------------//
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

include("classes/ITS_timer.php");

global $db_dsn, $db_name, $tb_name, $db_table_user_state, $tex_path, $term, $tset;

$dsn = preg_split("/[\/:@()]+/", $db_dsn);
//foreach ($dsn as $value) {echo $value.'<br>';}

$user          = $dsn[1];
$pass          = $dsn[2];
$host          = $dsn[4];
$name          = $dsn[6];
$dbNametb_user = $db_table_user_state;

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     = $_SESSION['user']->id();
$status = $_SESSION['user']->status();
$info   = $_SESSION['user']->info();
//----------------------------------------------------//
//mNK = (N*mean(A)+K*mean(B))/(N+K)

$con = mysql_connect($host, $user, $pass) or die('Could not Connect!');

// Check to make sure that we successfully connected
if ($con) {
    mysql_select_db($name, $con) or die('Could not select DB');
    
    //--- CLASS ------------------------------------------//
    $msg   = '';
    $class = array(
        'Fall_2008',
        'Spring_2009',
        'Fall_2009',
        'Spring_2010',
        'Fall_2010',
        'Spring_2011',
        'Fall_2011',
        'Spring_2012',
        'Summer_2012',
        'Fall_2012',
        'Spring_2013',
        'Summer_2013',
        'Fall_2013'
    );

    foreach ($class as $tb_class) {
        
        // CREATE TABLE: first check if table exists
        $stats_tb_class = 'stats_'.$tb_class;
        $res_exists   = mysql_query("SHOW TABLE STATUS LIKE '$stats_tb_class'");
        $table_exists = mysql_num_rows($res_exists) > 0; //""==mysql_error();	// if error: table already exists
        
        if ($table_exists) { 
			$query = 'DROP TABLE '.$stats_tb_class;
			mysql_query($query); 
			echo '<span style="color:brown">'.$query.'</span><br>';
		}

        echo 'CREATE TABLE <b>' . $stats_tb_class.'</b><hr>';
        $sql = "CREATE TABLE IF NOT EXISTS " . $stats_tb_class . " (" . "idx int NOT NULL AUTO_INCREMENT,uid int NOT NULL,status varchar(32),id int NOT NULL,				 " . "question_id		  int,				 " . "concept_id			  int,				 " . "current_chapter	int, 				 " . "answered 				varchar(1024), " . "score 					  float, 			 " . "rating 					int, 				 " . "comment 					varchar(128)," . "tags			  varchar(256)," . "epochtime       int(10) unsigned," . "duration        int(11)," . "event           varchar(63)," . "PRIMARY KEY (idx),		 " . "FOREIGN KEY (concept_id) REFERENCES concept (id) ON DELETE SET NULL " . ")";
        
        //echo '<p><span style="color:green">' . $sql . '</span><hr><p>'; // should print out "CREATE TABLE IF NOT EXIST tb_name ..."
        
        $res = mysql_query($sql);
        if (!$res) {
            die('<p>Invalid query:<p> ' . mysql_error());
        }
        // else{ echo "<p>" . var_dump($res); } 
        
        // SELECT $users by semester
        $query = 'SELECT id,status FROM users WHERE status IN ("' . $tb_class . '")';
        
        $res = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in : ' . msql_error());
        }
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row     = mysql_fetch_assoc($res);
            $uid[$x] = $row['id'];
            $ust[$x] = $row['status'];
        }
        
        $Nusers = mysql_num_rows($res);
    
        // GET STATS FIELDS
        $query  = 'DESCRIBE stats_1';
        $fields = '';
        $res    = mysql_query($query, $con);
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row        = mysql_fetch_assoc($res);
            $fields[$x] = $row['Field'];
        }
        $fields_str = implode(',', $fields);
        //var_dump($fields_str);die();
                
        // FOR each USER: fetch stats_{uid} and write to stats_all
        for ($u = 0; $u < $Nusers; $u++) {
            // echo $uid[$u] . ' ' . $ust[$u] . '<hr>';
            $query = 'INSERT INTO ' . $stats_tb_class . ' (uid,status,' . $fields_str . ') SELECT ' . $uid[$x] . ',"' . $ust[$x] . '",' . $fields_str . ' FROM stats_' . $uid[$u];
            //echo $query.'<br>';
            
            $res = mysql_query($query, $con);
            if (!$res) {
                die('Query execution problem in : ' . msql_error());
            }
        }
    }
    mysql_free_result($res);
    mysql_close($con);
} else {
    echo 'Connection failed.';
}

//--- TIMER -------------------------------------------------//
$mtime     = explode(" ", microtime());
$endtime   = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
echo $totaltime . '<hr>';
//--- FOOTER ------------------------------------------------//
//$ftr       = new ITS_footer($status, $LAST_UPDATE, $totaltime);
//echo $ftr->main();
//-----------------------------------------------------------//
?>
