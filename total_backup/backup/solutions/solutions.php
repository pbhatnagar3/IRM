<?php	
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); // always modified
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");          // or IE will pull from cache 100% of time (which is really bad) 
header("Cache-Control: no-cache, must-revalidate");        // Must do cache-control headers 
header("Pragma: no-cache");

require_once ("classes/ITS_timer.php");

require_once ("config.php");


require_once ("classes/ITS_solution.php");
//die('hi');
//require_once ("solutionsBackupLatest/ITS_solution.php");
require_once ("classes/ITS_logs.php");
require_once(INCLUDE_DIR. "common.php");
require_once(INCLUDE_DIR ."User.php");



session_start();

// return to login page if not logged in
abort_if_unauthenticated();
$id     = $_SESSION['user']->id();
//echo 'users.id = '. $id;

?>
<html>
<head>
<title>Solutions</title>
<?php

if(isset($_GET['hasaPost']) || isset($_POST['hasaPost'])){
	if(isset($_GET['viewSolution'])){
		echo'
		<link rel="stylesheet" href="css/ITS_Solution_warmup.css">
		<!--<link rel="stylesheet" href="css/ITS_Solution_solution.css">-->
		';
	}
}
?>
</head>

<?php
$ITS_solution 	= new ITS_solution($id);
$ITS_logs		= new ITS_logs($id);
if(isset($_GET['hasaPost']) || isset($_POST['hasaPost'])){
		//echo 'we are here?';
		if(isset($_GET['viewSolution'])){
			$path = '/cgi-bin/mathtex.cgi?';
			//echo 'We are in viewSolution';
			echo '
			<body class="ITS">
			<div class="main">
			';
			$qNum = $_GET['QNUM']; //$_POST['something'];
			// DISPLAY QUESTION NUMBER
			echo '<center>SOLUTIONS - QUESTION: <font color="red">'.$qNum.'</font> ( '.$db_name.':'.$tb_name.' )</center><br><br>';
			
			//$Q = new ITS_question(1,$db_name,$tb_name) or die('error1');
			//$Q->load_DATA_from_DB($qNum);
			
			// title
			//echo '<HR>'.$Q->render_TITLE().'<HR>';
			
			// question
			//echo $ITS_solution->latexCheck($Q->render_QUESTION(),$path);
			
			// answer
			//$Q->get_ANSWERS_data_from_DB();
			//$Q->get_ANSWERS_solution_from_DB();
			//echo '<br>';	
			//echo $ITS_solution->latexCheck($Q->render_ANSWERS('a'),$path);
			
			//Display the solutions
			echo $ITS_solution->viewSolution($qNum);
		}
		elseif (isset($_POST['enteredSolution'])){
			//echo 'We are here in enteredSolution';
			$qNum = $_POST['QNUM'];
			$Hint = $_POST['Hint'];
			$Solution = $_POST['Solution'];
			$Detailed = $_POST['Detailed'];
			
			echo $ITS_solution->enteredFromIndex($qNum, $Hint, $Solution, $Detailed);
			
		}
		elseif (isset($_GET['editor'])){
			//echo 'We are in the editor ';
			$qNum = $_GET['qNum'];
			$ID = $_GET['ID'];
			$stype = $_GET['stype']; // Hint/Sol/Det
			$Num = $_GET['Num'];
			$add = $_GET['Add']; //$add = $_GET['Add'];
			
			echo $ITS_solution->solutionEditor($qNum, $Num, $stype, $ID, $add);
			
		}
		elseif (isset($_POST['edited'])){
			//echo 'We are in the edited';
			$idNum = $_POST['idNum'];
			$qNum = $_POST['qNum'];
			$stype = $_POST['stype'];
			$Del = $_POST['Del'];
			$text = $_POST['text'];
			$img = $_POST['img'];
			echo $img;
			if($Del == 0){
				$DelLogVal = 'Deleted';
			}elseif($Del == 1){
				$DelLogVal = 'Edited';
			}elseif($Del == 2){
				$DelLogVal = 'Added';
			}
			if($stype == 'Hint'){
				$theField  = 'solution1';
			}elseif($stype == 'Sol'){
				$theField  = 'solution2';
			}elseif($stype == 'Det'){
				$theField  = 'solution3';
			}
			
			echo $ITS_solution->enteredFromEditor($idNum, $qNum, $stype, $Del, $text, $img);
			echo $ITS_logs->addtolog($qNum, 'solutions', $theField, $text, $DelLogVal);
			
		}
		elseif (isset($_POST['rated'])){
			//echo 'We are in the rated';
			$newRating = $_POST['rating'];
			$ID = $_POST['qID'];
			$qNum = $_POST['qNum'];
			$Ver = $_POST['Ver'];
			$sol = $_POST['sol'];
			if($newRating == 7){
				//Verified
				$theField = 'verified';
				$theAction = 'Verified';
			}elseif($newRating == 6){
				//Unverified
				$theField = 'verified';
				$theAction = 'Un-Verified';
			}else{
				//Regular Rated
				if($sol == 'sol1'){
					$theField = 'rating1';
				}elseif($sol == 'sol2'){
					$theField = 'rating2';
				}elseif($sol == 'sol3'){
					$theField = 'rating3';
				}
				$theAction = 'Rated '.$newRating;
			}
			//die('hi');
			echo $ITS_solution->enterRatings($newRating, $ID, $qNum, $Ver, $sol);
			echo $ITS_logs->addtolog($qNum, 'solutions', $theField, '', $theAction);
			
		}
		elseif(isset($_GET['viewHints'])){
			$qNum = $_GET['QNUM'];
			echo '<br>';
			echo $ITS_solution->viewHints($qNum);
			
		}
		elseif(isset($_GET['viewSol'])){
			$qNum = $_GET['QNUM'];
			echo '<br>';
			echo $ITS_solution->viewSols($qNum);
		}else{
			echo 'we are in here for some reason';
		}
		
		
}
else{
	//echo 'We are in the show index';
	//echo $ITS_solution->showIndex();
	//$qid = 3000;
	/*
	echo '
	<form>
	<input type="hidden" name="hasaPost" id="hasaPost" value="1" />
	<input type="hidden" name="viewSolution" id="viewSolution" value="1" />
	<input type="hidden" name="QNUM" id="QNUM" value="'.$qid.'" />
	<input type="button" value="Get" class="button1" onclick="getStuff();" />
	</form>
	
	';
	*/
}
/*
echo '<div id="results"></div>';
include('js/ITS_solution_jquery.php');
*/
?>


</body>

</html>
