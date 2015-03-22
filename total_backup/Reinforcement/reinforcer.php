<?php

/*
 * reinforcer.php
 *-----------------------------------------------------------
 * Author: bnensick3
 * Semester: Fall 2013
 * Team: Reinforcement
 *-----------------------------------------------------------
 * “You only live once, but if you do it right, once is enough.” 
 *-----------------------------------------------------------
 * Purpose:
 * Create a GUI to make the reinforcement table
 *-----------------------------------------------------------
 * Notes:
 * con_per_assign table must exist!
 * See con_per_assign_generator.php to create the table.
 */

//CHECK RELATIVE PATHS
require_once 'reinforcement_assignment.php';
require_once 'reinforcement.php';
require_once 'user_stats.php';


$file='reinforcer.php'; //Matches file name

echo '<html><body>';
if($_SERVER["REQUEST_METHOD"]!="POST"){
	input_form(0,$file);
}elseif($_POST["ass_num"]<=0){
	input_form(1,$file); //User input error
}else{
	input_form(0,$file);
	$handle = new reinforcement_assignment($_POST["semester"],$_POST["diff"],$_POST["ass_num"]);
	$handle->main();

};
echo '</body></html>';


/*
 * Function input_form(form_status, file)
 * ----------------------------------------------------------
 * Variables:
 * form_status - state based variable (0 or 1) 1 = user input
 * error.
 * file - see definition above.
 *-----------------------------------------------------------
 * Purpose:
 * Create an input form that accepts a concept id (numeric)
 * and assignment number (numeric) via HTML.
 * Calculate button calculate the hardest question of the
 * given concept in the given assignment.	
 */

function input_form($form_status,$file){
	//Check for previous input error
  	if($form_status){
  		print '<center><bold><font color="red">Assignment Number MUST be > 0! </font></bold
	      	</center><br>';
  	} 
  	print	"<form	action={$file}	method='POST'>";
  	print '<table align="center">';	
	print	'<tr><td>Semester ID:</td>
		<td><input	type="text"	name="semester"	value="Fall_2012"><br></td></tr>'; //Defaults difficultySTD
	print	'<tr><td>Difficulty Alogrithm:</td>
		<td><input	type="text"	name="diff"	value="difficultySTD"><br></td></tr>'; //Defaults difficultySTD	
  	print	'<tr><td>Assignment Number:</td>
		<td><input	type="text"	name="ass_num"	value=1><br></td></tr>'; //Defaults to 1
  	print '<tr><td colspan="2" align="center"><center>
		<input type="submit"value="Calculate"></center></td></tr>'; //Standard submit button
  	print '</table></form><br>';
 }

?>
