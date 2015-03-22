<?php
// return to login page if not logged in
//==============================================================================
function abort_if_unauthenticated() {
//==============================================================================
// echo $_SESSION['auth']->authenticated();
// var_dump($_SESSION['auth']);  

	if (!isset($_SESSION['auth']) || !$_SESSION['auth']->authenticated()){
		/* close session */
		//echo "rejected";

		$_SESSION = array();
		
		//var_dump($_COOKIE[session_name()]);  die("hello");
		
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(), '', time() - 4200, '/');// 		setcookie('name', 'content', 1);
		}

		session_destroy();

		//* redirect to start page *
		header("Location: http://" . $_SERVER['HTTP_HOST']
				. rtrim(dirname($_SERVER['PHP_SELF']), '/\\')
				. "/login.php");//*/
		exit;
	}
}
//==============================================================================
function ITS_debug($info){
//==============================================================================
/*
  echo '<div class="debug"><pre>';
  var_dump($info);
  echo '</pre><table class="ITS_backtrace">';	
  array_walk( debug_backtrace(), create_function( '$a,$b', 'print "<tr><td><font color=\"blue\">". basename( $a[\'file\'] ). "</b></font></td><td><font color=\"red\">{$a[\'line\']}</font></td><td><font color=\"green\">{$a[\'function\']}()</font></td><td>". dirname( $a[\'file\'] ). "/</td></tr>";' ) ); 	
  echo '</table></div>';
*/
}
//==============================================================================
function answering_history($user_id) {
//==============================================================================
	global $db_dsn;
	
	// Instantiate the DataGrid
  //$datagrid =& new Structures_DataGrid();

	$mdb2 =& MDB2::connect($db_dsn);
	if (PEAR::isError($mdb2)){ die($mdb2->getMessage()); }
	
	$query = "SELECT name,question_id,answered,score FROM stats_$user_id LEFT JOIN questions ON stats_$user_id.question_id=question.id LEFT JOIN concept ON questions.concept_id=concept.id";
	$res =& $mdb2->query($query);
	if (PEAR::isError($res)) {die($res->getMessage());}
/*
	// Display table
	$width = array(2,49,49);
	$data = array('','<b>Answered</b>','<b>Score</b>');
	//$tb = new ITS_table('add_header',1,3,$data,$width,'ITS_DATA');
	//echo $tb->str;
	
	$rows = 0;
	//$data = array();
	while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$data = array_merge($data,array($rows+1 . ". ",$row['answered'],$row['score']));
				//$data = array_merge($data,array($row['name'],$row['question_id'],$row['answered'],$row['score']));
				$rows = $rows+1;
	}
//var_dump($data);
//die();
	if (!empty($data)) {
		 $tb = new ITS_table('add',$rows+1,3,$data,$width,'ITS_DATA');
		 //$tb->set_style('ITS_ANSWER_STRIPE');
		 echo $tb->str;
	}
	*/
	//--- STATS ---
	echo "<hr>";
	
	$user = array();
  $query = "SELECT * FROM users WHERE status='admin'";
	$res =& $mdb2->queryCol($query);
	if (PEAR::isError($res)) {die($res->getMessage());}
	
	// user = [user_id][stats_row][stats_col] where [stats_col] = [id,question_id,answered,score]
	
	foreach ($res as $student_id){
	   // obtain all data
	   $query = "SELECT id,question_id,answered,score FROM stats_".$student_id;   
		 $res_stats =& $mdb2->queryAll($query);
		 $user[$student_id] = $res_stats;
		 
		 // obtain all sums
	   $query = "SELECT sum(score),avg(score) FROM stats_".$student_id;   
		 $res_s_a =& $mdb2->queryAll($query);
		 $stats[$student_id] = $res_s_a;
		  
		 //echo "<p>".$student_id." | sum: ".$stats[$student_id][0][0]." | avg:  ".$stats[$student_id][0][1];  
     $avg = round(100*$stats[$student_id][0][1]);
		 //echo "<p>".$avg;
		 $tb_score = new ITS_table('score',1,2,array('&nbsp;','&nbsp;'),array($avg,100-$avg),'ITS_GRAPH');
		 $tb = new ITS_table('user_score',1,2,array($student_id.".",$tb_score->str),array(10,90),'ITS');
		 echo $tb->str;
	}
	
	//var_dump($stats[1]); //die();

	/* 
	foreach ($res as $student_id){
	echo "<p>".$student_id." | ".$user[$student_id][1];
	}
	*/
	echo "<p><hr><p><a href=practice.php target =main_frame>back to Quiz</a><br/>";
	
	//--------
	/*
	$options = array('dsn' => $db_dsn);
	$test = $datagrid->bind($query,$options);
	if (PEAR::isError($test)) { echo $test->getMessage(); }
	 		
	// Print the DataGrid with the default renderer (HTML Table)
	$datagrid->setRenderer('HTMLTable');
	$test = $datagrid->render();
	if (PEAR::isError($test)) { echo $test->getMessage(); }
	*/
	//$res->free();
	$mdb2->disconnect();

	//return $table;
}
//==============================================================================
?>
