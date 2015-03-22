<?php

/*
 * reinforcement.php
 *-----------------------------------------------------------
 * Author: bnensick3
 * Semester: Fall 2013
 * Team: Reinforcement
 *-----------------------------------------------------------
 * “You only live once, but if you do it right, once is enough.” 
 *-----------------------------------------------------------
 * Class reinforcement(diff)
 * ----------------------------------------------------------
 * Variables:
 * diff - user input: difficulty alogrithm
 *-----------------------------------------------------------
 * Purpose:
 * Calculate the hardest question in an assignment for all
 * critical concepts.	
 */

//CHECK RELATIVE PATHS
require_once '../config.php';
require_once '../classes/ITS_query.php';
require_once'../FILES/PEAR/MDB2.php';

class reinforcement{

	//Class based variables
	public $db_dsn;
	public $tb_name;
	public $mdb2;
	public $ass_num; //User Input
	public $diff; //User Input

    	/*
 	 * Function __construct()
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Basic constructor assignment.
	 * Setup MySQL	
 	 */

    	function __construct($diff){
        	global $db_dsn,  $tb_name ;
		$this->diff = $diff; //User Input
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

	/*
 	 * Function generate_questions
 	 * ----------------------------------------------------------
 	 * Variables:
 	 * ass_num - user input: assignment number
 	 * tag_id - user input: tag_id
	 * ----------------------------------------------------------
	 * Output:
	 * Array where array[concept_id] = class_level_reinforcement_question_id
	 * Array where array[-concept_id] = student_level_reinforcement_question_id
	 * ----------------------------------------------------------
 	 * Purpose:
 	 * Handles all the routines
 	 */
	function generate_questions($ass_num){
		
		$this->ass_num = $ass_num; //user input assignment
		$reinforcement_questions['assignment'] = $ass_num;
		
		$concept_in_assign = $this->concepts_in_assign(); //From con_per_assign table
		$questions_in_assign = $this->questions_in_assign();
		

		foreach ($concept_in_assign as $concept){
			$questions_per_concept = $this->questions_per_concept($concept);
			$questions_overlap = array_values(array_intersect($questions_in_assign,$questions_per_concept));
			$question_difficulty = $this-> question_difficulty($questions_overlap);

			$reinforcement_question_index= implode(",",array_keys($question_difficulty, max($question_difficulty))); //Work around for max function not having an index
		
			//echo "Assignment: ", $this->ass_num, " Tag ID: ", $concept, " Question ID: ",$questions_overlap[$reinforcement_question_index];
			$reinforcement_questions[$concept] = $questions_overlap[$reinforcement_question_index];

			//Remove the max Index
			unset($question_difficulty[$reinforcement_question_index]);
			
			//Manipulation to get the max index (2nd max)
			$reinforcement_question_index= implode(",",array_keys($question_difficulty, max($question_difficulty)));
			//For Debug Purposes
			//echo "Student Level Question: Assignment: ", $this->ass_num, " Tag ID: ", $concept, " Question ID: ",$questions_overlap[$reinforcement_question_index];
			//echo "<br>";
			$reinforcement_questions[(-1*$concept)] = $questions_overlap[$reinforcement_question_index];
		} 
		return ($reinforcement_questions);
	}
	
	/****FUNCTION INSTRUCTIONS*************************************
	* Extracts a column out of a 2d array and returns it in an array
	* array=your 2d array
	* col=the column you want to pull out 
	**************************************************************/

	function GetCol($array,$col){
		foreach($array as $row) $new[] = $row[$col];
		return $new;
	}
	
	/*
 	 * Function concepts_in_assign
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Finds all critical concepts in an assignment.
	 *-----------------------------------------------------------
	 * Output:
	 * concept_list(array)- concept in the assignment
 	 */

	function concepts_in_assign(){
        	$ITSq = new ITS_query();
        	$query = 'SELECT orig_tag FROM con_per_assign WHERE assignment = ' . $this->ass_num;
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		$concept_list = explode(',',$qarr[0]);
        	return $concept_list;
	}

	/*
 	 * Function questions_in_assign
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Finds all questions in an assignment. No such table exists
	 * therefore, a complex querry is used.
	 *-----------------------------------------------------------
	 * Output:
	 * question_list(array)- questions in the assignment
	 *-----------------------------------------------------------
	 * Note:
	 * In the future, if a table is made for questions/assign use
	 * said table
 	 */

	function questions_in_assign(){
        	$ITSq = new ITS_query();
		$resource_source = $ITSq->getCategory($this->ass_num);
        	$query = 'SELECT id FROM ' . $this->tb_name . ' WHERE ' . $resource_source;
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		$question_list = $qarr;   
        	return $question_list;
	}
	
	/*
 	 * Function questions_per_concept
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Finds all questions for a concept
	 *-----------------------------------------------------------
	 * Output:
	 * question_list(array)- questions for a concept
 	 */
	
	function questions_per_concept($concept){
        	$ITSq = new ITS_query();
        	$query = 'SELECT questions_id FROM questions_tags WHERE tags_id = ' .$concept;
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		$question_list = $qarr;
		//The next query is a sanity check
		$query = 'SELECT q_id FROM questions_difficulty WHERE q_id IN (' .implode(',',$question_list). ')';
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		$question_list = $qarr;    
        	return $question_list;
	}
	
	/*
 	 * Function question_difficulty
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Find the difficulty of an array of questions
	 *-----------------------------------------------------------
	 * Input:
	 * question_ids
	 * Output:
	 * $question_difficulty
 	 */

	function question_difficulty($question_ids){
		$ITSq = new ITS_query();
        	$query = 'SELECT ' .$this->diff.' FROM questions_difficulty WHERE q_id IN (' .implode(',',$question_ids). ')';
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		$question_difficulty = $qarr;   
        	return $question_difficulty;
	}

	/*
 	 * Function users_in_semester
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * return all users in a semester
 	 */

	function users_in_semester($semester){
        	$ITSq = new ITS_query();
        	$query = 'SELECT id FROM users WHERE status = "' .$semester.'"';
		//echo $query; //Uncomment this if you are interested in query or database structure changes
        	$qarr =$this->Query($query,0,0);
        	$qarr = $this->GetCol($qarr,0);
		//var_dump($qarr);
		$users = $qarr;
        	return $users;
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
