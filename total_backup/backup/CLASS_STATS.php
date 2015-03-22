<?php
$LAST_UPDATE = 'Sep-20-2013';
//--- begin timer ---//
$mtime     = explode(" ",microtime());
$starttime = $mtime[1] + $mtime[0];
//------------------//

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   					 // or IE will pull from cache 100% of time (which is really bad) 
header("Cache-Control: no-cache, must-revalidate"); 					 // Must do cache-control headers 
header("Pragma: no-cache");

require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");
include_once("classes/ITS_timer.php");

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
//abort_if_unauthenticated();

$id     =   $_SESSION['user']->id();
$status =   $_SESSION['user']->status();
$info   = & $_SESSION['user']->info();
//------------------------------------------// 
if ($status == 'admin') {

	 global $db_dsn, $db_name, $db_table_users, $db_table_user_state;
	
	 $mdb2 =& MDB2::connect($db_dsn);
	 if (PEAR::isError($mdb2)){throw new Question_Control_Exception($mdb2->getMessage());}	 
	  
		//--- CLASS ------------------------------------------//
		$msg = '';
		$class = array('Fall_2008','Spring_2009','Fall_2009','Spring_2010','Fall_2010','Spring_2011','Fall_2011','Spring_2012','Summer_2012','Fall_2012','Spring_2013','Summer_2013','Fall_2013');
		$Nstudents = array();
		$Nquestions = array();
		
		$tb = '<table class="CPROFILE"><tr><th>Class</th><th>N-students</th><th>N-questions</th></tr>';
		
		foreach ($class as $c) { 
		//--- USERS --- ------------------------------------------//
		$query = 'SELECT id FROM users WHERE status="'.$c.'"';
	    $res   = $mdb2->query($query);
		$users = $res->fetchCol();
		$qN = 0;
        foreach ($users as $uid) { 	  
/*			
switch ($c) {
    case 'Spring_2010':
		 'Fall_2010':
        $sql = '';
        break;
    case 'Spring_2011':
		 'Fall_2011':
		 
        $sql = '';
        break;
    case 2:
        echo "i equals 2";
        break;
}*/
				$query = 'SELECT count(id) FROM stats_'.$uid.' WHERE answered IS NOT NULL'; // AND event NOT IN ("skip","skip-chapter","concept","skip-concept")';  //event IN ("chapter","concept")';
				// echo $query.'<p>';

				$res =& $mdb2->query($query);
				$ans = $res->fetchCol();
				$qN = $qN+$ans[0];
				//------------------------------------------------//				
		}
		
		$Nstudents[$c]  = count($users);
		$Nquestions[$c] = $qN;
		
		$tb .= '<tr><td style="text-align:left">'.str_replace('_',' ', $c).'</td><td>'.$Nstudents[$c].'</td><td>'.$Nquestions[$c].'</td></tr>';
	}
	$tb .= '<tr style="border:2px solid #666"><td><b>TOTAL</b></td><td><b>'.array_sum($Nstudents).'</b></td><td><b>'.array_sum($Nquestions).'</b></td></tr>';
	$tb .= '</table>';
	
		//sort($questions);
		/*echo '<pre>';  print_r($qid);  echo '</pre>'; die('==');*/

		/*
		//--- EACH QUESTION --------------------------------------// 
		//--------------------------------------------------------//
		//$query = 'SELECT id,question FROM webct WHERE qtype="C"';
	  //$users =& $mdb2->queryCol($query);		
		$users = range(1,1200);
		foreach ($users as $uid) { 
		    //echo '<p>'.$uid.'<p>';
		    //$query = 'ALTER TABLE stats_'.$uid.' ADD time_start INTEGER UNSIGNED, ADD time_end INTEGER UNSIGNED, ADD course_id INT(11)';
				//$query = 'SELECT comment FROM stats_'.$uid.' WHERE question_id=335';
				$query = 'SELECT id FROM webct WHERE id='.$uid;
				//echo $query.'<p>';
				$res =& $mdb2->query($query);
				$answers = $res->fetchAll();
				echo $uid.' - '.$answers[0].'<p>';
		}
		*/
  $mdb2->disconnect();		
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>DATABASE</title>
	<link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/login.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/print/ITS_print.css" media="print">
	<link rel="stylesheet" href="tagging/ITS_tagging.css" type="text/css" media="screen">
	<link rel="stylesheet" href="rating/ITS_rating.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_score.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_BOOK.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_test.css" type="text/css" media="screen">
	<link type="text/css" href="jquery-ui-1.8.4.custom/css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />	
  <script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
  <script src="js/ITS_admin.js"></script>
	<script src="js/AJAX.js"></script>
  <script src="js/ITS_AJAX.js"></script>
  <script src="js/ITS_screen.js"></script>
	<script src="js/ITS_QControl.js"></script>
	<script src="js/ITS_book.js"></script>
</head>
<body>
<?php
echo '<br><br><br><center>'.$tb.'</center>';
//--- TIMER -------------------------------------------------//
$mtime     = explode(" ",microtime());
$endtime   = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status,$LAST_UPDATE,$totaltime);
echo $ftr->main();
//-----------------------------------------------------------//
?>
</body>
</html>
