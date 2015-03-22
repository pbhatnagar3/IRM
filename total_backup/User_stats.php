<?php
/*
 * User_stats.php
 *-----------------------------------------------------------
 * Author: pbhatnagar3 (Pujun Bhatnagar)
 * Semester: Fall 2013
 * Team: Reinforcement
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

require_once("config.php"); // #1 include 
require_once("classes/ITS_query.php");
//require_once("classes/ITS_summary.php");
//require_once("FILES/PEAR/MDB2.php");
require_once(INCLUDE_DIR . "include.php");
include_once('FILES/PEAR/MDB2.php');
class User_Stats{
	
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
        //=====================================================================//
        $this->debug = TRUE;
        
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
			$this->name[$in] = $this->GetConName($tag);
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
		
		if($debug)var_dump($answer);

		return $answer;
	}

	
	function GetDataWithFilter(){
		if($this->debug)echo "<center>GetDataWithFilter<br></center>";
        
        $concepts = $this->GetConcepts();
        
        if(sizeof($concepts)<=30)
			$noConcepts = 3;
		elseif(sizeof($concepts)<=40)
				$noConcepts = 4;
		else
				$noConcepts = 5;
			
        foreach($concepts as $in=>$tag){
			$this->count[$in] = $this->GetCount($tag);
			$this->percent[$in] = ($this->GetPercent($tag))*($this->GetCount($tag))/(100);
			$this->name[$in] = $this->GetConName($tag);
			$noConcepts = $noConcepts - 1;
			if($noConcepts == 0)
				break;
		}
		$answer["count"] = implode(",",$this->count);
		$answer["percent"] = implode(",",$this->percent);
		$answer["name"] = implode(",",$this->name);
		
		return $answer;
	}

}
/*
echo '<html><body>';

$test = new User_Stats(1,5);
$data = $test->GetData();
var_dump($data["count"]);
		echo "<br>";
var_dump($data["percent"]);
		echo "<br>";
var_dump($data["name"]);
		echo "<br>";
		
echo '</body></html>';
*/
/*
 SELECT avg(score) FROM stats_1 WHERE current_chapter=9 AND score IS NOT NULL;
 
 SELECT * FROM stats_1 WHERE current_chapter=9 AND score IS NOT NULL ORDER BY epochtime DESC LIMIT 10;
 
 SELECT questions.id, stats_1.score, questions.tag_id FROM stats_1 JOIN questions ON questions.id=stats_1.question_id WHERE current_chapter=9 AND score IS NOT NULL and (tag_id LIKE '%,44,%' OR tag_id LIKE '44,%' OR tag_id LIKE '%,44');

 SELECT score FROM stats_1 WHERE current_chapter=9 AND score IS NOT NULL AND question_id IN (SELECT questions_id FROM questions_tags WHERE tags_id=44);
*/

?>
