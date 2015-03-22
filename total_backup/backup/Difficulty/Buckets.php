<?php

require_once("../config.php");
require_once("../FILES/PEAR/MDB2.php");
require_once("../classes/ITS_query.php");
//require_once("../css/ITS.css");


class Buckets{
	
	public $db_dsn;
	public $tb_name;
	public $tb_stats;
    public $tb_conpa;
    public $tb_difficulty;
    public $tb_difficulty_col;
    
    public $fld_conpa;
    
    public $chapter;
    public $mdb2;
	public $debug;
	
	public $count;
	public $percent;
	public $name;
	
	public $DifCol;
	public $NewCol;
	
	public $NumBuckets;
	
	FUNCTION UpdateQID($Dbug){
		
		
		$ITSq = new ITS_query();
		$resource_source = $ITSq->getCategory($this->chapter);
        $query="INSERT INTO {$this->tb_difficulty} (q_id) 
				SELECT id FROM {$this->tb_name} 
				WHERE ({$this->tb_name}.id 
					NOT IN (SELECT q_id FROM {$this->tb_difficulty})) AND ({$resource_source});";
		//var_dump($query);
        $qarr =$this->Query($query,0,0);
	}	

	function sortIntoBuckets($NumBuckets, $dbug){
		$this->UpdateQID(1);
		//Gets an array of q_ids in a chapter
		$qlist = $this->Get_qAssign(0);
		$query = "SELECT {$this->DifCol} FROM {$this->tb_difficulty} 
				  WHERE q_id IN ({$qlist}) AND {$this->DifCol} IS NOT NULL ORDER BY {$this->DifCol} ASC;";
		$result = $this->Query($query, 0, 1);
		$result = $this->GetCol($result, 0);
		
		//Calculates dimensions of bucket for dividing questions between buckets
		$size = count($result);
		$perBucket =round($size/$NumBuckets); // q_ids per bucket
		if($dbug){
			echo("CHAPTER: ");
			var_dump($this->chapter);
			echo("</br>QLIST: ");
			var_dump($qlist);
			echo("</br>SIZE: ");
			var_dump($size);
			echo("</br>PERBUCKET: ");
			var_dump($perBucket);
			echo("</br></br>");
		}
		
		//Calculates which q_ids go into a bucket by figuring out the lower bound
		//and upper bound of difficulty for that bucket by indexing in the array of q_ids.
		//Only iterates over the first ($NumBuckets-1) buckets
		for($i = 1 ; $i < $NumBuckets ; $i++){
			$lower = $result[$perBucket * ($i - 1)]; //Lower bound of difficulty for bucket
			$higher = $result[$perBucket * $i - 1]; //Upper bound of difficulty
			
			$query = "UPDATE {$this->tb_difficulty} SET {$this->NewCol} = {$i} 
					  WHERE q_id IN ({$qlist}) AND {$this->DifCol} <= {$higher} AND {$this->DifCol} >= {$lower};";
			$this->Query($query, 0, 0);
			if($dbug){
				echo("QUERY {$i}: ");
				var_dump($query);
				echo("</br></br>");
				$query = "SELECT q_id FROM {$this->tb_difficulty} 
				  WHERE q_id IN ({$qlist}) AND {$this->DifCol}<={$higher} AND {$this->DifCol}>={$lower} 
				  ORDER BY {$this->DifCol} ASC;";
				$test = $this->Query($query, 0, 0);
				$test = $this->GetCol($test, 0);
				echo("qids: ");
				var_dump($test);
				echo("</br></br>");
			}
			
		}
		
		//Puts the highest difficulty questions into bucket 5.
		//Easier to use 10 as the upper bound of difficulty rather than
		//calculating exactly how many are in the bucket and indexing,
		//since it is likely to be a different number than other buckets.
		$lower = $result[$perBucket * ($NumBuckets - 1)];
		$higher = 10;
		$query = "UPDATE {$this->tb_difficulty} SET {$this->NewCol} = {$NumBuckets} 
					  WHERE q_id IN ({$qlist}) AND {$this->DifCol} <= {$higher} AND {$this->DifCol} >= {$lower} ;";
		$this->Query($query, 0, 0);
		if($dbug){
			echo("QUERY {$NumBuckets}: ");
			var_dump($query);
			echo("</br></br>");
			$query = "SELECT q_id FROM {$this->tb_difficulty} 
				  WHERE q_id IN ({$qlist}) AND {$this->DifCol}<={$higher} AND {$this->DifCol}>={$lower} 
				  ORDER BY {$this->DifCol} ASC;";
			$test = $this->Query($query, 0, 0);
			$test = $this->GetCol($test, 0);
			echo("qids: ");
			var_dump($test);
			echo("</br></br>");
		}
		
		//Puts questions with no assigned difficulty into the middle bucket.
		$middle = ceil($NumBuckets/2);
		$query = "UPDATE {$this->tb_difficulty} SET {$this->NewCol} = {$middle}
					  WHERE q_id IN ({$qlist}) AND {$this->NewCol} IS NULL ;";
		$this->Query($query, 0, 0);
		if($dbug){
			echo("QUERY MID({$middle}): ");
			var_dump($query);
			echo("</br></br>");
			$query = "SELECT q_id FROM {$this->tb_difficulty} 
				  WHERE q_id IN ({$qlist}) AND {$this->NewCol} IS NULL 
				  ORDER BY {$this->DifCol} ASC;";
			$test = $this->Query($query, 0, 0);
			$test = $this->GetCol($test, 0);
			echo("qids: ");
			var_dump($test);
			echo("</br></br>");
		}
	}
    
    function __construct($DifCol, $chapter){
        //=====================================================================//
        $this->debug = FALSE;//TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn,  $tb_name, $db_table_user_state, $tb_con_per_assign, $db_table_difficulty, $tb_difficulty_col ;
        
        $this->NewCol = $DifCol."_NEW";
        $this->DifCol = $DifCol;
        $this->tb_difficulty = $db_table_difficulty;
        
        $this->tb_difficulty_col = $tb_difficulty_col;
        
        $this->db_dsn = $db_dsn;
        
        $this->tb_name = $tb_name;
        
        $this->tb_stats = $db_table_user_state . $id;
        
        $this->tb_conpa = $tb_con_per_assign;
        
        $this->fld_conpa = 'orig_tag';
        
        $this->chapter = $chapter;
        
        $this->MySQL_set();
   } 
    
    function __destruct(){
		$this->MySQL_end();
	}
	
	function Get_qAssign($dbug){
		if($this->debug)echo "<center>Get_qAssign()<br></center>";
        
        $ITSq         = new ITS_query();
		$resource_source = $ITSq->getCategory($this->chapter);
        $query = 'SELECT id FROM ' . $this->tb_name . ' WHERE ' . $resource_source;
        $qarr =$this->Query($query,0,0);
        $qarr = $this->GetCol($qarr,0);
        $ques_list = implode(",", $qarr);
        if($dbug) var_dump($ques_list);
                
        return $ques_list;
	}
	
	function GetData(){
		if($this->debug)echo "<center>GetData<br></center>";
        
        $ques = $this->Get_qAssign();
        for($i=0;$i<=10;$i++){
			$result = $this->GetTotal($ques,$i);
			if($result[1])$count = $this->GetCount($result[0]);
			else $count = 0;
			
			$answer[$i]['total'] = $result[1];
			$answer[$i]['count'] = $result[1] - $count;
		}
		
 		return $answer;
	}
		
	function GetTotal($ques,$num,$dbug){
		if($this->debug)echo "<center>GetTotal<br></center>";
        
        $numh = $num +.5;
        $numl = $num -.5;
        $query = "SELECT q_id FROM {$this->tb_difficulty} 
					WHERE {$this->tb_difficulty_col} < {$numh} 
					AND {$this->tb_difficulty_col} > {$numl} 
					AND q_id IN ({$ques});";
		
		$result = $this->Query($query,0,0);
		$resultA = $this->GetCol($result, 0);
		$answer[0] = implode(',',$resultA);
		
		$answer[1] = count($result);
		
		if($dbug) echo $answer;
		return $answer;
	}
	
	function GetCount($ques,$dbug){
		if($this->debug)echo "<center>GetCount<br></center>";
        
        $query = "SELECT score FROM {$this->tb_stats} WHERE 
					current_chapter={$this->chapter}
					AND score IS NOT NULL 
					AND question_id IN ({$ques});";
		//var_dump($query);
		$result = $this->Query($query,0,0);
		$answer = count($result);
		
		if($dbug) echo $answer;
		return $answer;
	}
	
	function GetDifficulty($qid){      
		$query =   "SELECT {$this->tb_difficulty_col} FROM {$this->tb_difficulty} WHERE q_id={$qid};";
		$result = $this->Query($query,0,0);
		$answer = round($result[0][0]);
		$values = $this->GetData();
		$res = '';
		for($i=0;$i<=10;$i++){ //1&lt;br&gt;
			$width = 100;
			$str = '<div class="DifficultyBar" style="width:'.$width.'px;" 
					title="Difficulty rank '.$i.': '.$values[$i]['count'].' question(s) left out of '.$values[$i]['total'].'">';
			if($i==$answer) $str = $str . '<div  style="width:'.$width.'px;">';
			$str = $str. "<center><b>{$i}</b> <br> {$values[$i]['count']} / {$values[$i]['total']}</center>	";
			if($i==$answer) $str = $str . '</div>';
			$str = $str . '</div>';
			$res = $res.$str;
		
		}
		return $res;
		
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

}
/*
?>

<html>
<head>
<link rel="stylesheet" href="../css/ITS.css" type="text/css" media="screen"> 
</head>	
<body>
<?php

$test = new User_DiffStats(1,5);
//die(hi);
$data = $test->GetData();

echo "Total:\t";
for($i = 0; $i<=10; $i++){echo $data[$i]['total']; echo "\t";}
echo "<br>";

echo "Count:\t\t";
for($i = 0; $i<=10; $i++){echo $data[$i]['count']; echo "\t";}
echo "<br>";
	
$bar = $test->getDifficulty(1091);	
echo $bar;
echo '</body></html>';

?>*/
