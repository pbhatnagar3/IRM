<?php
class User_DiffStats{
	
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
	
    function __construct($id,$chapter){
    //=====================================================================//
        $this->debug = FALSE;//TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn,  $tb_name, $db_table_user_state, $tb_con_per_assign, $db_table_difficulty, $tb_difficulty_col ;
        
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
    //=====================================================================//
    function __destruct(){
		$this->MySQL_end();
	}
	//=====================================================================//
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
	//=====================================================================//
	function GetData(){
		if($this->debug)echo "<center>GetData<br></center>";
        
        $ques = $this->Get_qAssign();
        for($i=0;$i<=10;$i++){
			$result = $this->GetTotal($ques,$i);
			if($result[1])$count = $this->GetCount($result[0]);
			else $count = 0;
			
			$answer[$i]['total'] = $result[1];
			$answer[$i]['count'] = $count;//$result[1] - $count;
		}	
 		return $answer;
	}
	//=====================================================================//	
	function GetTotal($ques,$num,$dbug){
		if($this->debug)echo "<center>GetTotal<br></center>";
        
        $numh = $num +0.5;
        $numl = $num -0.5;
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
	//=====================================================================//
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
	//=====================================================================//
	function GetDifficulty($qid){      
		$query =   "SELECT {$this->tb_difficulty_col} FROM {$this->tb_difficulty} WHERE q_id={$qid};";
		$result = $this->Query($query,0,0);
		$answer = round($result[0][0]);
		$values = $this->GetData();
		$title = array(
            'Very easy',
            'Easy',
            'Moderate',
            'Difficult',
            'Very difficult'
        );
		$res = '';
		$width = 100;
		$nwidth = round($width*0.75);
		for($i=5;$i>0;$i--){ //1&lt;br&gt;
			$percent = round($nwidth *$values[$i]['count'] / $values[$i]['total']);
			if(!$values[$i]['total']) $percent =$nwidth;
			$str = '<div class="DifficultyBar" style="width:'.$width.'px;" 
					title="'.$title[$i-1].': '.$values[$i]['count'].' / '.$values[$i]['total'].'">';				

			if($i==$answer) $str = '<div class="DifficultyBar DiffBarSel" style="width:'.$width.'px" 
									title="'.$title[$i-1].': '.$values[$i]['count'].' / '.$values[$i]['total'].'">';
									
			$str = $str. '<div style="position:relative;top:10px;">';
			for($n=0;$n<$i;$n++)$str = $str.'<img src="Difficulty/star.gif" width="15px" height="15px">';//"<center><b>{$i}</b> <br> </center>";//{$values[$i]['count']} / {$values[$i]['total']}<br>
			$str = $str.'<br><div class="DifficultyBar-in" style="width:'.$nwidth.'px;position:relative;top:5px;">
					<div  style="width:'.$percent.'px;"></div>
					</div></div>';
			//$str = $str . '<input type="radio" name="newrate" value="3" class = "star-on2" title="Not that bad" />';
        	//if($i==$answer) $str = $str . '</div>';
			$str = $str . '</div>';
			$res = $res.$str;
		
		}
		return $res;	
	}
	//=====================================================================//	
	function GetCol($array,$col){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Extracts a column out of a 2d array and returns it in an array
		 * array=your 2d array
		 * col=the column you want to pull out 
		 * ***********************************************************/
		foreach($array as $row) $new[] = $row[$col];
		return $new;
	}
	//=====================================================================//	
	function MySQL_set(){
		if($this->debug)echo "<center>MySQL setup<br></center>";
        
		// connect to database
		$this->mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($this->mdb2)) {
            throw new Exception($this->mdb2->getMessage());
        }
        
        return 1;
    }
	//=====================================================================//
	function MySQL_end(){
		if($this->debug)echo "<center>MySQL disconnect<br></center>";
        
		$this->mdb2->disconnect();
		
		return 1;
	}
	//=====================================================================//
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
	//=====================================================================//
}
?>
