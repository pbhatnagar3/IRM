<?php

/*
 * reinforcement_assignment.php
 *-----------------------------------------------------------
 * Author: bnensick3
 * Semester: Fall 2013
 * Team: Reinforcement
 *-----------------------------------------------------------
 * “You only live once, but if you do it right, once is enough.” 
 *-----------------------------------------------------------
 * Class reinforcement_assignment(semester)
 * ----------------------------------------------------------
 * Purpose:
 * Manages the classes to create a reinforcement table	
 */

//CHECK RELATIVE PATHS
require_once 'reinforcement.php';
require_once 'user_stats.php';
require_once 'reinforcement_table.php';
require_once '../config.php';
require_once '../classes/ITS_query.php';
require_once '../FILES/PEAR/MDB2.php';


class reinforcement_assignment{

	//Class based variables
	public $db_dsn;
	public $tb_name;
	public $mdb2;
	public $semester;
	public $ass_num;
	public $diff;

	/*
 	 * Function __construct()
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Basic constructor assignment.
	 * Setup MySQL	
 	 */	
	function __construct($semester,$diff,$ass_num){
        	global $db_dsn,  $tb_name ;
		$this->semester = $semester;
		$this->diff = $diff;
		$this->ass_num = $ass_num;
        	$this->db_dsn = $db_dsn;
        	$this->tb_name = $tb_name;
        	$this->MySQL_set(); //Connect to Database
    	 }	

	/*
 	 * Function __destruct
 	 * ----------------------------------------------------------
 	 * Purpose:
 	 * Basic destructor
	 * End MySQL	
 	 */
    	function __destruct(){
		$this->MySQL_end(); //disconnect to database
	}

	function main(){

		$reinforcement_handle = new reinforcement($this->diff); //Initialize class
		$users = $reinforcement_handle->users_in_semester($this->semester);
		$questions = $reinforcement_handle->generate_questions($this->ass_num);

		
		$table_handle = new SQL_com($this->semester,$this->ass_num);
		$table_handle->remove_table();
		$class_concepts;

		echo "<table border='1'>";
		echo"<tr>";
		echo "<td> user_ID</td>";
		echo "<td> tag_ID</td>";
		echo "<td> q_ID</td>";
		echo "</tr>";
	
		foreach($users as $user){
			$user_stat_handle = new user_stats(intval($user),intval($this->ass_num));
			$concepts = $user_stat_handle->GetDataDiffFilter();
			foreach($concepts as $concept){
				$class_concepts[$concept] = $class_concepts[$concept] +1;		
				echo"<tr>";
				echo "<td> $user </td>";
				echo "<td> $concept </td>";
				echo "<td> $questions[$concept] </td>";
				echo "</tr>";
				$table_handle->update_table(intval($user),intval($concept),intval($questions[$concept]));
			}
		}



		//Work around for not having a semester wide stats function... -.-
		$numConcepts = 0;
		if(sizeof($class_concepts)<=30)
			$numConcepts = 3;
		else
			$numConcepts = ceil(.1*sizeof($concepts));


		$class_tags;
		for($i = 0; $i < $numConcepts; $i++){
			$class_tags[$i] = implode(",",array_keys($class_concepts, max($class_concepts)));
			unset($class_concepts[$class_tags[$i]]);
		}
		foreach($class_tags as $c_tag){	
			$semester_id = $this->semester;
			echo"<tr>";
			echo "<td> $semester_id </td>";
			echo "<td> $c_tag </td>";
			$c_tag = -1*$c_tag;
			echo "<td> $questions[$c_tag] </td>";
			echo "</tr>";
			$table_handle->update_table(-1,-1*$c_tag,$questions[$c_tag]);
		}
		echo"</table>";
	}


	/*
 	 * Function MySQL_set
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Connect to ITS database
 	 */

	function MySQL_set(){
		//Connect to database
		$this->mdb2 =& MDB2::connect($this->db_dsn);
        	if (PEAR::isError($this->mdb2)) {
            		throw new Exception($this->mdb2->getMessage());
        	}
        	return 1;
   	}

	/*
 	 * Function MySQL_end
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Disconnect to ITS database
 	 */

	function MySQL_end(){
		$this->mdb2->disconnect();
		return 1;
	}

	/****FUNCTION INSTRUCTIONS*************************************
	* queries the MySQL database
	* query=the query you would like to send
	* assoc=if you would like the array to use the field names as indices
	* 			TRUE=yes    FALSE=no
	**************************************************************/

	function Query($query,$assoc){	
        	//run query
		$res = $this->mdb2->query($query);
		if (PEAR::isError($res)) {
            		throw new Question_Control_Exception($res->getMessage());
        	}	
        	if($assoc){
			$answer = $res->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$answer = $res->fetchAll();
		}
		return $answer;
	}
}
?>

