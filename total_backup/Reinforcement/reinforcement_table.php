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
 * Creates the reinforcement table	
 */

class SQL_com{

	public $db_dsn;
	public $tb_name_ca;
	public $mdb2;
	public $debug;
	public $field;
 
    function __construct($semester,$ass_num){
        //=====================================================================//
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn;
        
        $this->db_dsn = $db_dsn;
        
        $this->tb_name_ca = "reinforcement_team_".$semester."_".$ass_num;
        $this->field = "q_ID";
	$this->field2 = "tag_ID";
        $this->MySQL_set();
        $this->CheckTable(0);
        $this->CheckField(0);

	echo "Table name: " .$this->tb_name_ca;
    } 
    
    function __destruct(){
		$this->MySQL_end();
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
 
	function GetCol($array,$col){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Extracts a column out of a 2d array and returns it in an array
		 * array=your 2d array
		 * col=the column you want to pull out 
		 * ***********************************************************/
		foreach($array as $row) $new[] = $row[$col];
		return $new;
	}

	function CheckTable($debug){
		if($this->debug)echo  "<center>CheckTable()<br></center>";
		
		$query = "SHOW tables LIKE '{$this->tb_name_ca}';";
		$ans = $this->Query($query,0,$debug);
		if(empty($ans)){
			$query = "CREATE TABLE {$this->tb_name_ca}(user_ID INT NOT NULL);";//AUTO_INCREMENT
			$ans = $this->Query($query,0,$debug);
			//echo "<center><h1>Created new table: {$this->tb_name_ca}</h1><br></center>";
			//$query = "INSERT INTO {$this->tb_name_ca} (user_ID) VALUES (0);";
			//echo $query;
			$ans = $this->Query($query,0,$debug);
		}
	}
	
	function CheckField($debug){
		if($this->debug)echo  "<center>CheckField()<br></center>";
		
		$exist=0;
		$result=$this->Query("desc {$this->tb_name_ca};",1,$debug);
		$result = $this->GetCol($result,"field");
		foreach($result AS $x=>$name){
			If(!strcasecmp($name,$this->field)){
				$exist=1;
				break;
			}
		}
		IF(!$exist){
			//echo "<center><h2>Created {$this->field} in {$this->tb_name_ca}</h2></center><br>";
			$this->Query("ALTER TABLE {$this->tb_name_ca} ADD COLUMN {$this->field2} INT;",0,$debug);
			$this->Query("ALTER TABLE {$this->tb_name_ca} ADD COLUMN {$this->field} INT;",0,$debug);
		}
	}

	function update_table($user_ID,$tag_ID,$q_ID){
		
		$query = "Insert into {$this->tb_name_ca} (user_ID,tag_ID,q_ID) values ({$user_ID},{$tag_ID},{$q_ID});";	
		$result = $this->Query($query,0,0);
	}

	function remove_table(){
		$query = "delete  from {$this->tb_name_ca} where 1=1;";
		$result = $this->Query($query,0,0);
	}

	function Done(){
		//echo "<center><h2>Added these organized Concepts to {$this->field} in {$this->tb_name_ca}<br></h2></center>";
	}
}
?>
