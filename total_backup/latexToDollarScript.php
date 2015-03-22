<?php

//require_once("MDB2.php");

include("classes/ITS_table.php");
include("classes/ITS_question.php");

/*========= LOCAL  =========*/
$username = 'root';
$password = 'csip';
$db_name  = 'its';          // database name
$tb_name  = 'questions';    // question table name
/*==========================*/

// connect to database
$con = mysql_connect("localhost",$username,$password);

if (!$con) { die('Could not connect: ' . mysql_error());}
mysql_select_db($db_name, $con);
/*
$res = mysql_query('show tables');
while ($row = mysql_fetch_array($res)){
	echo $row[0].'<br>';
}*/

$arrayOfTables = array($tb_name, $tb_name.'_c', $tb_name.'_mc', $tb_name.'_m', $tb_name.'_p');
echo enableLatexToDollar($arrayOfTables);
	//=====================================================================//
	// This function is creating the arrays of fields in each table and then running
	// that array through the latexToDollar function
function enableLatexToDollar($arrayOfTables){
	//=====================================================================//	
	foreach($arrayOfTables as $num => $value){//Runs through each table in the $arrayOfTables
		$arrayOfFields = array();
		//echo $arrayOfTables[$num].'<br>';
		$res = mysql_query('desc '.$value.'');
		while($row = mysql_fetch_array($res)){//This while loop create and array of field
			//echo $row[0].'<br>';
			$arrayOfFields[] = $row[0];		
		}
		//echo '<br><br>';
		//print_r($arrayOfFields);
		echo latexToDollar($arrayOfFields, $arrayOfTables[$num]);
	}

	return 'Successful';
}
	//=====================================================================//
function latexChange($str) {
	//=====================================================================//
		$pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
		$replacement = '$$${1}$$';
		$str         = preg_replace($pattern, $replacement, $str);

		return $str;
	}
	//=====================================================================//	
 //* FUNCTION WITH ARRAY OF FIELDS
//Take in an array of fields, and the name of a table,and then loops through each field in the database in that table
//replacing <latex>*</latex> with $$*$$
function latexToDollar($field, $tables){
	//=====================================================================//
	foreach($field as $num => $value){ //value is the name of the field
		$res = mysql_query('SELECT id,'.$value.' FROM '.$tables.'') or die(mysql_error());
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		while($row = mysql_fetch_array($res)){
			$QID = $row['id'];
			$oldText = $row[''.$value.''];
			if(empty($oldText)){
				//Do nothing
			}
			else{
				//Run through the script
				//echo '<b>Original ID: '.$QID.' FIELD: '.$value.' TABLE: '.$tables.'</b><br>';
				//echo $oldText;
				
				//echo '<br>-----------------------<br><b>New ID: '.$QID.' FIELD: '.$value.' TABLE: '.$tables.'</b><br>';
				$updatedText = latexChange($oldText);
				//echo $updatedText;
				$updatedText = addslashes($updatedText);
				//BEWARE UNCOMMENTING NEXT LINE WILL UPDATE THE DATABASE
				mysql_query("UPDATE ".$tables." SET ".$value."='".$updatedText."' WHERE id=".$QID."") or die(mysql_error());
				//echo '<br>====================================================================<br>';
				//$res1 = mysql_query('SELECT id,'.$value.' FROM '.$tables.' WHERE id='.$QID.'') or die('query error 2');
				//while($row1 = mysql_fetch_array($res1)){
					//$QID1 = $row1['id'];
					//$theText1 = $row1[''.$value.''];
					//echo 'This is now what the text looks like in the database: <br>'.$theText1.'<br>';
				//}
			}
		}
	}
	return;
}
/*=======================================================================================
 * ======================================================================================
 * ======================================================================================
 * ======================================================================================
 //*FUNCTION WITH ARRAY OF FIELDS, PATTERN, and REPLACEMENT
 * does not work yet
//Takes in an array of fields, a pattern, and a replacement and goes through
//the database in the fields and replaces the pattern it finds
function patternReplacement($field, $pattern, $replacement){
	
	//Function to use the pattern and replacement
	function latexChange($str, $pattern, $replacement) {
	//=====================================================================//
		//$thepattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
		$thepattern		= $pattern;
		//$thereplacement = '$$${1}$$';
		$thereplacement = $replacement;
		$str         = preg_replace($thepattern, $thereplacement, $str);

		return $str;
	}
	
	foreach($field as $num => $value){
		$res = mysql_query('SELECT id,'.$value.' FROM webct') or die('query error');
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		while($row = mysql_fetch_array($res)){
			$QID = $row['id'];
			$oldText = $row[''.$value.''];
			echo '<b>Original ID: '.$QID.' FIELD: '.$value.'</b><br>';
			echo $oldText;
		 
			echo '<br>-----------------------<br><b>New ID: '.$QID.' FIELD: '.$value.'</b><br>';
			$updatedText = latexChange($oldText);
			echo $updatedText;
			$updatedText = addslashes($updatedText);
			//BEWARE UNCOMMENTING NEXT LINE WILL UPDATE THE DATABASE
			//$val = mysql_query('UPDATE webct SET '.$value.'='.$updatedText.' WHERE id='.$QID.'');
			echo '<br>====================================================================<br>';
		}
	}
	return;
	
}

$arrayOfFields = array('question');
$aPattern = "/<latex[^>]*>(.*?)<\/latex>/im";
$aReplacement = '$$${1}$$';
echo patternReplacement($arrayOfFields, $aPattern, $aReplacement);
*/
/*====================================================================================
 * ===================================================================================
 * This block of code only updates 1 question in the database hardcoded
function latexChange($str) {
	//=====================================================================//
		$pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
		$replacement = '$$${1}$$';
		$str         = preg_replace($pattern, $replacement, $str);

		return $str;
	}
		$res = mysql_query('SELECT id,question FROM webct WHERE id=3473') or die('query error');
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		while($row = mysql_fetch_array($res)){
			$QID = $row['id'];
			$oldText = $row['question'];
			echo '<b>Original ID: '.$QID.'</b><br>';
			echo $oldText;
		 
			echo '<br>-----------------------<br><b>New ID: '.$QID.'</b><br>';
			$updatedText = latexChange($oldText);
			echo $updatedText;
			$updatedText = addslashes($updatedText);
			//BEWARE UNCOMMENTING NEXT LINE WILL UPDATE THE DATABASE
			mysql_query("UPDATE webct SET question='".$updatedText."' WHERE id=".$QID."") or die(mysql_error());
			echo '<br>====================================================================<br>';
		} 
		$res1 = mysql_query('SELECT id,question FROM webct WHERE id=3473') or die('query error 2');
		while($row1 = mysql_fetch_array($res1)){
			$QID1 = $row1['id'];
			$theText1 = $row1['question'];
			echo 'This is now what the text looks like in the database: <br>'.$theText1;
		}
*/	 
/*
function latexChange($str) {
	//=====================================================================//
		$pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
		$replacement = '$$${1}$$';
		$str         = preg_replace($pattern, $replacement, $str);

		return $str;
	}
 * OLD BASE SCRIPT
$res = mysql_query('SELECT id,question from webct') or die('query error');
	if (!$res) {
	 	die('Query execution problem in SQLforDemo.php: ' . msql_error());
	 }
	 while($row = mysql_fetch_array($res)){
		 $QID = $row['id'];
		 $question = $row['question'];
		 echo '<b>Original ID: '.$QID.'</b><br>';
		 echo $question;
		 
		 echo '<br>-----------------------<br><b>New ID: '.$QID.'</b><br>';
		 $updatedQuestion = latexChange($question);
		 echo $updatedQuestion;
		 $updatedQuestion = addslashes($updatedQuestion);
		 //BEWARE UNCOMMENTING NEXT LINE WILL UPDATE THE DATABASE
		 //$val = mysql_query('UPDATE webct SET question='.$updatedQuestion.' WHERE id='.$QID.'');
		 echo '<br>====================================================================<br>';
	 }
*/ 
?>
