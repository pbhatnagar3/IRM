<?php
$LAST_UPDATE = 'Sep-6-2013';
//--- begin timer ---//
$mtime     = explode(" ",microtime());
$starttime = $mtime[1] + $mtime[0];
//------------------//

require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

include("classes/ITS_timer.php");

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     =   $_SESSION['user']->id();
$status =   $_SESSION['user']->status();
$info   = & $_SESSION['user']->info();
//------------------------------------------// 

if ($status == 'admin') {
	 global $db_dsn, $db_name, $db_table_users, $db_table_user_state, $tset;

	 $mdb2 =& MDB2::connect($db_dsn);
	 if (PEAR::isError($mdb2)){throw new Question_Control_Exception($mdb2->getMessage());}	 
 
		//--- QUESTIONS ------------------------------------------//
		$msg = 'finished';
		$questions = array();
		
		//--- USERS --- ------------------------------------------//
		$query = 'SELECT id FROM users WHERE status="Fall_2013" ORDER BY id'; // 
	    $res   = $mdb2->query($query);
		$users = $res->fetchCol();
		$qid   = 888;
		//var_dump($users);
			foreach ($users as $uid) {
				//echo '<p>'.$uid.'<p>';
				//$query = 'ALTER TABLE stats_'.$uid.' ADD event VARCHAR(63) AFTER duration';
				$query1 = 'SELECT id FROM stats_'.$uid.' WHERE question_id='.$qid.' AND current_chapter=2 AND epochtime>='.$tset;
				//echo $query1.'<br>';			
				$res1 =& $mdb2->query($query1);
				$out = $res1->fetchCol();	
				//var_dump($out);
				
				if (!(empty($out))){
					echo '<b>user: '.$uid.'</b><br>';
				foreach ($out as $s) {
					echo $s.'<br>';
					//$query2 = 'UPDATE stats_'.$uid.' SET comment=NULL,event="skip" WHERE id='.$s;
					$query2 = 'UPDATE stats_'.$uid.' SET current_chapter=3 WHERE question_id='.$qid.' AND current_chapter=2 AND epochtime>='.$tset;
					//$query2 = 'DELETE FROM stats_'.$uid.' WHERE id='.$s;
					$res2   =& $mdb2->query($query2);
					echo $query2.'<br>';	
				}
				echo '<hr>';
			}
			}	
		//echo '<pre>';  print_r($qid);  echo '</pre>'; die('==');	

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
  die('stop');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>DATABASE</title>
	<link type="text/css" href="jquery-ui-1.8.4.custom/css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
	<!--[if IE 6]>
	<link rel="stylesheet" href="css/IE6/ITS.css" type="text/css" media="screen">
	<![endif]-->
  <script src="js/ITS_admin.js"></script>
	<script src="js/AJAX.js"></script>
  <script src="js/ITS_AJAX.js"></script>
  <script src="js/ITS_screen.js"></script>
	<script src="js/ITS_QControl.js"></script>
	<script src="js/ITS_book.js"></script>
	<script src="tagging/ITS_tagging.js"></script>
	<script src="rating/forms/star_rating.js"></script>
	<script type="text/javascript">
	$(function() {
      $(".ITS_select").change(function() { document.profile.submit(); });
			$("#select_class").buttonset();
  });
	/*-------------------------------------------------------------------------*/
  $(document).ready(function() { 
     //$("#scoreContainer").click(function(){$("#scoreContainerContent").slideToggle("slow");});
  });
  </script>	
<script type="text/javascript">
</script>
</head>
<body>
<?php
echo $msg;
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
