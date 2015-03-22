<?php
/*=====================================================================//
ITS_NQ - Next question algorithm.

Constructor: ITS_screen(name,rows,cols,data,width)

ex. $ITS_NQ = NEW ITS_NQ($this->id,$this->sessiont,$qAvailable,$resource_name);

Author(s): Charles Ethan Hayes |  Oct-11-2012
		 Gregory Krudysz |  Sep-3-2013
SCHEMA:
ITS_NQ.php
--NextQuestion();
//=====================================================================*/
 
class ITS_NQ {
    public $debug;
    public $debugInfo;
    
    public $id;
    public $epochtime;
    public $qlist;
    public $qarr;
    public $ch;
   
    public $db_dsn;
    
    //tables in database
    public $tb_difficulty;
    public $tb_user;
    public $tb_question;
    public $tb_difficulty_col;
    
 //=====================================================================//
    function __construct($id, $t, $qarr, $ch){
        //=====================================================================//
        $this->debug = TRUE; //TRUE;
        //if you only want the main debug info functions to run 
        $this->debugInfo = TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn, $db_name, $tb_name, $db_table_user_state, $mimetex_path, $db_table_difficulty, $tb_difficulty_col;
        
        $this->id      = $id;
        $this->db_dsn  = $db_dsn;
        
        $this->tb_name = $tb_name;
        $this->tb_user = $db_table_user_state.$this->id;
        $this->tb_difficulty = $db_table_difficulty;
        $this->tb_difficulty_col = $tb_difficulty_col;
        						 
        $this->epochtime = $t;
        $this->qlist = implode(',',$qarr);
        $this->qarr = $qarr;
        $this->ch = $ch;
    }    
//=====================================================================//
	function NextQuestion(){
		/****FUNCTION INSTRUCTIONS*************************************
		* Filters out the question array to the desired questions
		* ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';

		$event = $this->GetLastEvent($this->debug);
		
		if(!empty($this->qlist)){
			$qAvailable = $this->GetQavailable($event["event"],$event,'',.5,$this->epochtime,$this->debug);
			//if the filter kicked out all of the questions, send it through with instructions on how to pick the question
			if(empty($qAvailable))$qAvailable = $this->GetQavailable("empty",$event,$event["event"],.5,$this->epochtime,$this->debug);
		}else{
			$qAvailable = NULL;
		}
		
		if($this->debugInfo OR $this->debug)$this->Dbug_NextQuestions($event,$qAvailable,TRUE);
		
		return $qAvailable;
	}
	//=====================================================================//
	function pEmptyLimit($limit,$event,$span,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Decides what to do if getQavailable returned an empty array of questions
		 * limit=which event ran and came back empty ($event['event'])
		 * event=all of the last info from the user table ($event) 
		 * span=the width for the difficulty bucket
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>';
		switch($limit){
			//------------------//
			case "skip easier":
				$this->qlist = $this->removeCurrentQ($event,FALSE, $debug);
				$mod = 0.5;
				
				$center = round($event["difficulty"]) -1;
				if($center<1)$center = .5;
				
				$answer = $this->QavailDiffBucket($center,$span+$mod,$span,$debug);
				while(empty($answer)){ 
					if(($center-$span-$mod-0.5)>0 AND $mod>0){
						$mod += 0.5;
						$answer = $this->QavailDiffBucket($center,$span+$mod,$span,$debug);
					} else if($mod>0){
						$mod = -0.5;
						$answer = $this->QavailDiffBucket($center,$span,$span-$mod,$debug);
					} else if(10<($center+$span-$mod)){
						echo'<script type="text/javascript">
						alert( "There are no questions!");
						</script>';
						//$answer = NULL
						break;
					} else {
						$mod -= 0.5;
						$answer = $this->QavailDiffBucket($center,$span,$span-$mod,$debug);
					}
				}
				//$answer = NULL;
				/*echo'<script type="text/javascript">
					alert( "Just passed the easiest question of this chapter");
					</script>';*/
				break;
			//------------------//
			case "skip harder":
				$this->qlist = $this->removeCurrentQ($event,FALSE, $debug);
				$mod = 0.5;
				
				$center = round($event["difficulty"]) +1;
				if($center>10)$center = 9.5;
				
				$answer = $this->QavailDiffBucket($center,$span,$span+$mod,$debug);
				while(empty($answer)){ 
					if(($center+$span+$mod+0.5)<10 AND $mod>0){
						$mod += 0.5;
						$answer = $this->QavailDiffBucket($center,$span,$span+$mod,$debug);
					} else if($mod>0){
						$mod = -0.5;
						$answer = $this->QavailDiffBucket($center,$span-$mod,$span,$debug);
					} else if(0>($center-$span+$mod)){
						echo'<script type="text/javascript">
						alert( "There are no questions!"' . $mod . ');
						</script>';
						//$answer = NULL
						break;
					} else {
						$mod -= 0.5;
						$answer = $this->QavailDiffBucket($center,$span-$mod,$span,$debug);
					}
				}
				break;
			//------------------//	
			case "skip back":
					$answer = "back";
				break;
			//------------------//		
			case "skip forward":
				$center = round($event["difficulty"]);
				$answer = $this->QavailDiffBucket($center,$span,$span,$debug);
				break;
			//------------------//	
			default:
				$answer=NULL;
				break;
				
		}
		return $answer;	
	}
	//=====================================================================//
	function GetQavailable($action,$event,$limit,$span,$time,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Decides how to pick the next Qavailable array based on the 
		 * 		last event recorded in the user table 
		 * action=the last event from the user table ($event['event'])
		 * event=all of the last info from the user table ($event) 
		 * limit=limit for qEmptyLimit
		 * span=width of difficulty bucket
		 * time=session start time
		 * ***********************************************************/
		if(($event['epochtime']-$time)<0)$action = 'new session';
		
		if($this->debug)echo  "<center>GetQavailable(case {$action})<br></center>";
		
		switch($action){
			//picks an easier question 
			case "skip easier":
				$this->qlist = $this->removeCurrentQ($event,FALSE, $debug);
				$center = round($event["difficulty"]) -1;
				if($center<1)$center = .5;
				$answer = $this->QavailDiffBucket($center,$span,$span,$debug);
				break;
			//------------------//
			case "sub_easier":
				$center = round($event["difficulty"]);
				if($center<1)$center = .5;
				if($center>9)$center=9.5;
				$answer = $this->ExpandDiffBucket($center,.1,$span,$debug);
				break;
			//------------------//
			//selects a harder question
			case "skip harder":
				$this->qlist = $this->removeCurrentQ($event,FALSE, $debug);
				$center = round($event["difficulty"]) +1;
				if($center>9)$center = 9.5;
				$answer = $this->QavailDiffBucket($center,$span,$span,$debug);
				break;
			//------------------//
			case "sub_harder":
				$center = round($event["difficulty"]) ;
				if($center>9)$center=9.5;
				if($center<1)$center=.5;
				$answer = $this->ExpandDiffBucket($center,$span,.1,$debug);
				break;
				//------------------//
			//goes back to the last question
			case "skip back":
				$answer=$this->QuesDir($event,0,$debug);
				break;
			//------------------//	
			case "skip forward":
				$answer=$this->QuesDir($event,1,$debug);
				break;
			//------------------//	
			//runs if the question list output was empty
			case "empty":
				$answer=$this->pEmptyLimit($limit,$event,$span,$debug);
				break;
			//------------------//
			case "submit":
				if($event["score"]>50){
					$center = round($event["difficulty"]) +1;
					if($center>9)$center = 9.5;
				}else{
					$center = round($event["difficulty"]) -1;
					if($center<1)$center = .5;
				}
				$answer = $this->QavailDiffBucket($center,$span,$span,$debug);
				break;
			//------------------//		
			case "new session":
				$answer = NULL;
				if(!strcmp($event["event"],"skip back"))$event["event"] = "skip forward";
				$time = $event['epochtime'] -1;
				if($debug)echo "{$event['epochtime']}-{$this->epochtime}<br>";
				$center = round($event["difficulty"]);
				$span = .5;
				//die('sss');
				while(!count($answer) AND (($center+$span-1)<10 OR ($center-$span+1)>0)){
					$answer = $this->GetQavailable($event["event"],$event,'',$span,$time,$debug);
					if(empty($answer))$answer = $this->GetQavailable("empty",$event,$event["event"],$span,$time,$debug);
					$span = $span +.5;
					if($debug)echo "new span: {$span}<br>";
				}
				break;
			//------------------//	
			//gives back the full list of questions that have a difficulty associated with them	
			default:
				$answer = $this->QavailDiffBucket(5,$span,$span,$debug);
				break;
		}
		
		return $answer;	
	}
	//=====================================================================//
	function QavailDiffBucket($center,$spanL,$spanH,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * gets all of the questions in a specific difficulty range
		 * center=the center of the bucket
		 * spanL=the width below center for the bucket (bucket=center-span)
		 * spanH=the width above center for the bucket (bucket=center+span)
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		if(10>$center+$spanH)$max = $center+$spanH;
		else $max=10.1;
		
		if(0<$center-$spanL)$min = $center-$spanL;
		else $min=0;
		
		$query	= $this->CreateDiffQuery($min,$max,FALSE,TRUE,$debug);
		$answer = $this->Query($query,FALSE,$debug);
		
		if($debug)echo "<center>center: {$center}  spans: {$min}-{$max}<br></center>";
		
		return $answer;	
	}
//=====================================================================//
	function ExpandDiffBucket($center,$spanH,$spanL,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * recursively calls QavailDiffBucket() until it gets a none empty question array. 
		 * 		each call increases the spans by the original amount that they  
		 * center=the center of the bucket
		 * spanH=the width above center for the bucket (bucket=center+span)
		 * spanL=the width below center for the bucket (bucket=center-span)
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		$answer = NULL;
		$rateL=$spanL;
		$rateH=$spanH;
		while(!count($answer) AND (($center+$spanH)<=10 OR ($center-$spanL)>=0)){
			$answer = $this->QavailDiffBucket($center,$spanL,$spanH,$debug);
			$spanL = $spanL + $rateL;
			$spanH = $spanH + $rateH;		
		}
				
		return $answer;
	}
	//=====================================================================//
	function QavailDiffBucket2($center,$spanH,$spanL,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Prints out a comment to the screen stating which function 
		 * 		didn't send through any available questions
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		if(10>$center+$spanH)$max = $center+$spanH;
		else $max=10.1;
		
		if(0<$center-$spanL)$min = $center-$spanL;
		else $min=0;
		
		//$query	= $this->CreateDiffQuery($min,$max,FALSE,TRUE,$debug);
		$query	= $this->CreateDiffQuery($min,$max,FALSE,FALSE,$debug);
		$answer = $this->Query($query,FALSE,$debug);
		if($debug)echo "<center>center: {$center}  spans: {$min}-{$max}<br></center>";
		
		//if answer is empty, re-run CreateDiffQuery to pull random Q
		//if(empty($answer)){
		//	$query=$this->CreateDiffQuery(0,10,FALSE,FALSE,$debug);
		//	$answer=$this->Query($query,FALSE,$debug);
		//}
		//IF ANSWER IS EMPTY, EXPAND BUCKET
		$count=0;
		while(empty($answer)){
		$count=$count+1;
		if(10>$center+$spanH)$max = $max+$spanH;
		else $max=10.1;
		
		if(0<$center-$spanL)$min = $min-$spanL;
		else $min=0;
		$query=$this->CreateDiffQuery($min,$max,FALSE,FALSE,$debug);
		$answer=$this->Query($query,FALSE,$debug);
		if(empty($answer)&&$max==10.1&&min==0)break;
		}
		echo "<pre> count is $count </pre>";
		//if it's truly empty, same result is returned.  if not, user gets random Q
		return $answer;	
	}
	//=====================================================================//
	function GetLastEvent($debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * get an array with the all of the information from the previous action of the user
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		$query =  $this->CreateUserQuery("{$this->tb_user}.current_chapter={$this->ch}",1,$debug);
		$answer = $this->Query($query,TRUE,$debug);
		return $answer[0];	
	}
	//=====================================================================//
	function QuesDir($event,$forward,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * gets either a question forward or back of the questions seen by the user
		 * event=all of the last info from the user table ($event) 
		 * forward= True if you are trying to skip forward (FALSE for back)
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		$where = "{$this->tb_user}.epochtime>={$this->epochtime} AND {$this->tb_user}.id <= {$event["stats_id"]} AND {$this->tb_user}.current_chapter={$this->ch} AND {$this->tb_user}.event LIKE 'skip%'";
		
		$query =  $this->CreateUserQuery($where,2,$debug);
		$answer = $this->Query($query,TRUE,$debug);
		$i=0;
		
		while(count($answer) AND !empty($answer[1]) AND (($answer[1]['event']=='skip back' AND !$forward) OR (strcmp($answer[1]['event'],'skip back') AND $forward))){
			$query = "SELECT id FROM {$this->tb_user} WHERE question_id={$event['q_id']} AND id < {$answer[0]['stats_id'] } ORDER BY {$this->tb_user}.epochtime DESC LIMIT 1;";
			$start = $this->Query($query,TRUE,$debug);
			if(!empty($start)){
				$where = "{$this->tb_user}.epochtime>={$this->epochtime} AND {$this->tb_user}.id <= {$start[0]['id']} AND {$this->tb_user}.current_chapter={$this->ch} AND {$this->tb_user}.event LIKE 'skip%'";		
				$query =  $this->CreateUserQuery($where,2,$debug);
				$answer = $this->Query($query,TRUE,$debug);
			}else{
				$answer=NULL;
			}
		}
		
		if(!empty($answer[1]) AND !($answer[1]['q_id']==8)){//replace q_id 8 with the dummy q_id **EDIT**
			$final[0][0]=$answer[1]['q_id'];
			$final[0][1]=$answer[1]['qtype'];
		}else{
			$final=NULL;
		}
		
		return $final;	
	}
//=====================================================================//
	function removeCurrentQ($event,$allowC,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * removes the current question if it is not calculated so the same question can't pop up
		 * $event=the event array from GetLastEvent
		 * allowC=True if you want to allow calculated questions to be reused
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		if(strcmp($event["qtype"],"C") OR !$allowC){
			$qlist = explode(',',$this->qlist);
			$run=TRUE;
			$k=count($qlist);
			$i=-1;
			while($i++<$k AND $run){
				if($qlist[$i]==$event["q_id"]){
					unset($qlist[$i]);
					$run = FALSE;
				}
			}
			$qlistO = implode(',',$qlist);//str_replace(",".$event["q_id"].",",",",$this->qlist);
		}else{
			$qlistO=$this->qlist;
		}
		
		if($debug)echo "<center>With current question [{$this->qlist}]<br>Without current question of type {$event["qtype"]} [{$qlistO}]<br></center>";

		return $qlistO;		
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
	function Dbug_NextQuestions($event,$qAvailable,$details){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Shows debug info for all of NextQuestion
		 * event=the event array
		 * qAvailable=the output array
		 * details=TRUE if you want detailed debug info from function
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		echo "<center>last question[ ID:{$event['q_id']}  DIFFICULTY:{$event['difficulty']} EVENT:{$event['event']}]<br></center>";
		$this->DbugQarr($qAvailable,$details);
	}
	 //=====================================================================//
	function DbugQarr($answer,$showArr){
		/****FUNCTION INSTRUCTIONS*************************************
		 * Shows debug info for the Question ID array in and out
		 * answer=Question array out
		 * showArr=TRUE if you want to see the actual array in and out
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		$qarrI = explode(',',$this->qlist);
		$cI = count($qarrI);
		$cO = count($answer);
		echo "<center># of questions in: ".$cI."<br># of questions out: ".$cO."<br></center>";
		if($showArr){
			$qlistO = implode(',',$this->GetCol($answer,0));
			echo "<center>Questions In [{$this->qlist}]<br>Questions Out [{$qlistO}]<br></center>";
		}
		
		return 0;			
	}
	//=====================================================================//
	function CreateDiffQuery($DiffMin,$DiffMax,$include,$remove,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * creates a query to only use questions of desired difficulty
		 * DiffMin=the minimum difficulty question you want to allow to be used
		 * DiffMax=the maximum difficulty question you want to allow through
		 * include=allow it to include the max value in the output
		 * remove=remove the questions previously shown during session
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		$dbq_table = "{$this->tb_difficulty} JOIN {$this->tb_name} 
							ON {$this->tb_difficulty}.q_id={$this->tb_name}.id";
		
		if(empty($this->qlist)) $dbq_in = "{$this->tb_name}.id IN (-1)";
		else $dbq_in    = "{$this->tb_name}.id IN ({$this->qlist})";
		
		if($include) $dbq_diff  = "{$this->tb_difficulty}.{$this->tb_difficulty_col} >= {$DiffMin} AND {$this->tb_difficulty}.{$this->tb_difficulty_col} <= {$DiffMax}";
		else $dbq_diff  = "{$this->tb_difficulty}.{$this->tb_difficulty_col} >= {$DiffMin} AND {$this->tb_difficulty}.{$this->tb_difficulty_col} < {$DiffMax}";
		
		if($remove) $dbq_rem = "AND {$this->tb_name}.id NOT IN (SELECT question_id FROM {$this->tb_user} WHERE epochtime>={$this->epochtime})";
		else $dbq_rem = "";
		
		$dbq_where = "WHERE {$dbq_in} AND {$dbq_diff} {$dbq_rem}";
		
		$query = "SELECT {$this->tb_name}.id,{$this->tb_name}.qtype FROM {$dbq_table} {$dbq_where};";
		
		if($debug)echo '<font color="green">'.$query."</font><br>";
		return $query;
	}
	//=====================================================================//
	function CreateUserQuery($where,$limit,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * creates a query to pull all of the information needed 
		 * where=the where part of the query statement
		 * limit=number of rows to pull {enter 0 for no limit}
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		//FROM (stats_1 JOIN questions_difficulty ON stats_1.question_id=questions_difficulty.q_id) JOIN questions ON questions_difficulty.q_id=questions.id
		$dbq_table = "({$this->tb_user} JOIN {$this->tb_difficulty} ON {$this->tb_user}.question_id={$this->tb_difficulty}.q_id)
							JOIN {$this->tb_name} ON {$this->tb_difficulty}.q_id={$this->tb_name}.id";
		$dbq_fields = "{$this->tb_difficulty}.q_id AS q_id, {$this->tb_difficulty}.{$this->tb_difficulty_col} AS difficulty,
							{$this->tb_user}.event AS event, {$this->tb_user}.epochtime AS epochtime, 
							{$this->tb_user}.score AS score, {$this->tb_user}.id AS stats_id,
							{$this->tb_user}.current_chapter AS chapter, {$this->tb_user}.duration AS duration,
							{$this->tb_name}.qtype AS qtype";  //{$this->tb_name}.tag_id AS tag_id"
		$dbq_order = "ORDER BY {$this->tb_user}.epochtime DESC";
		if($limit){$dbq_limit="LIMIT {$limit}";}else{$dbq_limit='';}
		if(!is_empty($where))$where = "WHERE ".$where;
		
		$query = "SELECT {$dbq_fields} FROM {$dbq_table} {$where} {$dbq_order} {$dbq_limit};";
		if($debug)echo '<font color="red">'.$query."</font><br>";
		return $query;
	}
//=====================================================================//
	function Query($query,$assoc,$debug){
		/****FUNCTION INSTRUCTIONS*************************************
		 * queries the MySQL database
		 * query=the query you would like to send
		 * assoc=if you would like the array to use the field names as indices
		 * 			TRUE=yes    FALSE=no
		 * ***********************************************************/
		if($this->debug)echo  '<font color="blue">'.__METHOD__.'</font>'.'<br>';
		
		//die($query);
		
		// connect to database
        $mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($mdb2->getMessage());
        }
        
        //run query
		$res = $mdb2->query($query);
		if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }

        if($assoc){
			$answer = $res->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$answer = $res->fetchAll();
		}
		
		//var_dump($answer);die($answer);
		
		$mdb2->disconnect();
		
		if($debug)var_dump($answer);

		return $answer;
	}
//=====================================================================//
}
?>
