<?php
/*
  User_stats.php
 *-----------------------------------------------------------
 * Author: pbhatnagar3 (Pujun Bhatnagar)
 * Semester: Fall 2013
 * Team: Reinforcement
 *-----------------------------------------------------------
 * Purpose:
 * GUI to test the working of User_stat by reading and displaying all the data
 * for any UserID for any particular assignment.  
 *-----------------------------------------------------------
 * Notes:
 * The user would have to implode the data to display it properly
 * see User_stats.php for reference. 
 */






require_once("User_stats.php");
require_once("config.php"); // #1 include 
require_once("classes/ITS_query.php");
//require_once("classes/ITS_summary.php");
//require_once("FILES/PEAR/MDB2.php");
require_once(INCLUDE_DIR . "include.php");
include_once('FILES/PEAR/MDB2.php');


/*
$stats = new User_Stats($id,$assignment_num);
		$data= $stats->GetData();
		$concept_name = explode(",",$data['name']);
		$num_attempted = explode(",",$data['count']);
		$num_correct = explode(",",$data['percent']);
		
		
		echo $data;
		echo $concept_name;
		echo $num_attempted;
		
	*/
	echo "<form action=\"UserStatTest.php\" method=\"get\">";
echo "<center>UserID: <input type=\"text\" name=\"UserID\" value=\"1758\"><br>";
echo "Assignment_no: <input type=\"text\" name=\"Assignment_no\" value=\"1\"><br>";
echo "<input type=\"submit\">";
echo "</form>";
	
	
	$UserID=(integer)$_GET["UserID"];
	$Assignment_no=(integer)$_GET["Assignment_no"];
	echo "$game";
		$stats = new User_Stats($UserID,$Assignment_no);
		$data= $stats->GetDataWithFilter();
		$concept_name = explode(",",$data['name']);
		$series1_data = explode(",",$data['count']);
		$series2_data = explode(",",$data['percent']);
	
	/**
		foreach($concept_name as $value){
			echo "$value <br>";
		}
		
		foreach($series1_data as $value){
			echo "$value <br>";
		}
		echo "<br>";
		foreach($series2_data as $value){
			echo "$value <br>";
		}
		
		**/
		
		echo "<table border='1'>";
		$td=count($series1_data);
		echo"<tr>";
			echo "<td> CONCEPT NAME </td>";
			echo "<td> CONCEPT COUNT </td>";
			echo "<td> CONCEPT PERCENTAGE </td>";
			echo "</tr>";
		
		//echo "$td";
		
		
		for($i=0; $i<$td; $i=$i+1){
			echo"<tr>";
			echo "<td> $concept_name[$i] </td>";
			echo "<td> $series1_data[$i] </td>";
			echo "<td> $series2_data[$i] </td>";
			echo "</tr>";			
		}
		
		echo"</table>";
			
		
		?>
