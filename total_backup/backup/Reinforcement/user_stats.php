<?php
/*
 * User_stats.php
 *-----------------------------------------------------------
 * Author: chayes30
 * Semester: Spring 2013
 * Team: Concept
 *-----------------------------------------------------------
 * Modified: bnensick3
 * Semester: Fall 2013
 * Team: Reinforcement
 * Added difficulty filter
 *-----------------------------------------------------------
 * Purpose:
 * A class that helps read all the reinforcement data from the database 
 *using its various methods 
 * One just needs to instantiate an object with the appropriate UserID and Assignment_no
 * and use the getData() method to get the data. 
 *-----------------------------------------------------------
 * Notes:
 * The user would have to implode the data to display it properly
 * see UserStatTest.php for reference. 
 */

require_once '../config.php';
require_once '../classes/ITS_query.php';
require_once '../FILES/PEAR/MDB2.php';

class user_stats{
	
	public $db_dsn;
	public $tb_name;
	public $tb_stats;
    	public $tb_conpa;
    	public $fld_conpa;
    	public $chapter;
    	public $mdb2;
	public $debug;
	public $count;
	public $percent;
	public $name;

    	function __construct($id,$chapter){

        	//$this->debug = TRUE;
        	if ($this->debug) {
            		echo '<br>' . get_called_class().'<br>';
        	}

        	global $db_dsn,  $tb_name, $db_table_user_state, $tb_con_per_assign ;        
        	$this->db_dsn = $db_dsn;        
        	$this->tb_name = $tb_name;
        	$this->tb_stats = $db_table_user_state . $id;
        	$this->tb_conpa = 'con_per_assign';
        	$this->fld_conpa = 'orig_tag';
        	$this->chapter = $chapter;
        	$this->MySQL_set();
   	} 
    
    	function __destruct(){
		$this->MySQL_end();
	}
	
	function GetData(){

		if($this->debug)echo "<center>GetData<br></center>";
        
        	$concepts = $this->GetConcepts();

        	foreach($concepts as $in=>$tag){
			$this->count[$in] = $this->GetCount($tag);
			$this->percent[$in] = ($this->GetPercent($tag))*($this->GetCount($tag))/(100);
			$this->name[$in] = $tag;
		}
		$answer["count"] = implode(",",$this->count);
		$answer["percent"] = implode(",",$this->percent);
		$answer["name"] = implode(",",$this->name);
		
		return $answer;
	}
	
	function GetConName($tag,$dbug){
		if($this->debug)echo "<center>GetConName<br></center>";
        
        	$query = "SELECT name FROM tags WHERE id={$tag};";
		$result = $this->Query($query,0,0);
		$answer = $result[0][0];
		
		if($dbug) echo $answer;
		return $answer;
	}
	
	function GetPercent($tag,$dbug){
		if($this->debug)echo "<center>GetPercent<br></center>";
        
		$query = "SELECT avg(score) FROM {$this->tb_stats} WHERE 
					current_chapter={$this->chapter}
					AND question_id IN 
					(SELECT questions_id FROM questions_tags WHERE tags_id={$tag});";
		$result = $this->Query($query,0,0);
		
		if($result[0][0]) $answer = $result[0][0];
		else $answer = 0;
		
		if($dbug) echo $answer;
		return $answer;
	}
	
	function GetCount($tag,$dbug){
		if($this->debug)echo "<center>GetCount<br></center>";
        
        	$query = "SELECT score FROM {$this->tb_stats} WHERE 
					current_chapter={$this->chapter}
					AND score IS NOT NULL 
					AND question_id IN 
					(SELECT questions_id FROM questions_tags WHERE tags_id={$tag});";
		$result = $this->Query($query,0,0);
		$answer = count($result);
		
		if($dbug) echo $answer;
		return $answer;
	}
	
	function GetConcepts($dbug){
		if($this->debug)echo "<center>GetConcepts<br></center>";
        
        $query = "SELECT {$this->fld_conpa} FROM {$this->tb_conpa} WHERE assignment = {$this->chapter};";
        //echo $query.'<br>';
		$result = $this->Query($query,0,0);
		$answer = $result[0][0];
		$answer = explode(",",$answer);
		
		if($dbug) var_dump($answer);
		
		return $answer;
	}
	
	function GetCol($array,$col){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Extracts a column out of a 2d array and returns it in an array
		 * array=your 2d array
		 * col=the column you want to pull out 
		 * ***********************************************************/
		foreach($array as $row) $new[] = $row[$col];
		return $new;
	}
	function MySQL_set(){
		if($this->debug)echo "<center>MySQL setup<br></center>";
        
		// connect to database
		$this->mdb2 =& MDB2::connect($this->db_dsn);
       		if (PEAR::isError($this->mdb2)) {
            		throw new Exception($this->mdb2->getMessage());
        	}
        
        	return 1;
    	}
	
	function MySQL_end(){
		if($this->debug)echo "<center>MySQL disconnect<br></center>";
        
		$this->mdb2->disconnect();
		
		return 1;
	}
	
	function Query($query,$assoc,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * queries the MySQL database
		 * query=the query you would like to send
		 * assoc=if you would like the array to use the field names as indices
		 * 			TRUE=yes    FALSE=no
		 * ***********************************************************/
		if($this->debug)echo  "<center>Query()<br></center>";
		
		$res = $this->mdb2->query($query);
		if (PEAR::isError($res)) {
            		throw new Question_Control_Exception($res->getMessage());
        	}
        
        	if($assoc){
			$answer = $res->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$answer = $res->fetchAll();
		}
		
		if($debug)var_dump($answer);

		return $answer;
	}

	/*
 	 * Function GetDataDiffFilter
 	 *-----------------------------------------------------------
 	 * Purpose:
 	 * Find the 10 percent hardest concepts for a user
	 *-----------------------------------------------------------
	 * Output:
	 * tags = [...] hardest indexes
 	 */

	
	function GetDataDiffFilter(){

		if($this->debug)echo "<center>GetDataWithFilter<br></center>";
        
        	$concepts = $this->GetConcepts();
        
		//Output should be 10% of total tags (rounded up)
        	if(sizeof($concepts)<=30)
			$numConcepts = 3;
		else
			$numConcepts = ceil(.1*sizeof($concepts));

		//Set the array to all 1's
		$tag_percent;
		$tags;
		for ($i = 0; $i < $numConcepts; $i++) {
    			$tag_percent[$i] = 10; //Arbitrarly large number
			$tags[$i] = 0;
		}
		
		//Filter
        	foreach($concepts as $in=>$tag){
			for ($i = 0; $i < $numConcepts; $i++) {
				if ($tag_percent[$i] > $this->GetPercent($tag)/100){
					$tag_percent[$i]= $this->GetPercent($tag)/100;
					$tags[$i] = intval($tag);
					break;
				}
			}
		}
		sort($tags);
		return $tags;
	}
}
?>
