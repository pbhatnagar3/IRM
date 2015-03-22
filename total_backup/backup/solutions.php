<?php	
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

//require_once ("classes/ITS_timer.php");
require_once ("classes/ITS_solution.php");
require_once ("classes/ITS_logs.php");

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
			//echo 'We are in the show question';
			echo '
			<body class="ITS">
			<div class="main">
			';
			$qNum = $_GET['QNUM']; //$_POST['something'];
			// DISPLAY QUESTION NUMBER
			//echo '<center>SOLUTIONS - QUESTION: <font color="red">'.$qNum.'</font> ( '.$db_name.':'.$tb_name.' )</center><br><br>';
			
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
			//echo 'We are here in entered solution';
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
			$stype = $_GET['stype'];
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
			if($Del == 0){
				$DelLogVal = 'Deleted';
			}elseif($Del == 1){
				$DelLogVal = 'Edited';
			}elseif($Del == 2){
				$DelLogVal = 'Added';
			}
			
			echo $ITS_solution->enteredFromEditor($idNum, $qNum, $stype, $Del, $text);
			echo $ITS_logs->addtolog($qNum, 'solutions', $stype, $text, $DelLogVal);			
		}
		elseif (isset($_POST['rated'])){
			//echo 'We are in the rated';
			$newRating = $_POST['rating'];
			$ID = $_POST['qID'];
			$qNum = $_POST['qNum'];
			$Ver = $_POST['Ver'];
			
			echo $ITS_solution->enterRatings($newRating, $ID, $qNum, $Ver);			
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
		}		
}
else{
	//echo 'We are in the show index';
	echo $ITS_solution->showIndex();
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
echo  $str;
?>
</body>
</html>
