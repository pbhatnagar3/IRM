<?php
//require_once("../include/include.php");
include 'config.php';
include 'classes/ITS_query.php';
include 'FILES/PEAR/MDB2.php';	

$file="test2.php";
echo '<html><body>';
if($_SERVER["REQUEST_METHOD"]!="POST"){
	formIn(0,$file);
}elseif($_POST["cutoff"]<=0){
	formIn(1,$file);
}else{
	$input = new SQL_com($_POST["Field"]);
	$CiA = new Con_in_Assign($_POST["cutoff"]);

	for ($i=1;$i<12;$i++){
		try{
		echo "Chapter {$i} concepts: {$carr}<br>";
		
		$carr = $CiA->List_Concepts($i);
		
		echo "Call 1 Worked";
		
		$input->Update_Concepts($i,$carr);
		
		echo "Call 2 Worked";
		
		}catch(Exception $e){
		echo $e->getMessage();
		}
	}
	$input->Done();
};
echo '</body></html>';

function formIn($badIn,$file){
  if($badIn)
		print '<center><bold><font color="red">cutoff must be greater than 0</font></bold></center><br>';
  print	"<form	action={$file}	method='POST'>";
  print '<table align="center">';	
  print	'<tr><td>Name of the Column to store Concepts:</td>
		 <td><input	type="text"	name="Field"	value="orig_tag"><br></td></tr>';	
  print	'<tr><td>Cutoff number of Concepts seen to include in assignment:</td>
		 <td><input	type="text"	name="cutoff"	value=3><br></td></tr>';	
  print '<tr><td colspan="2" align="center"><center>
		 <input type="submit"value="Find the concepts in each assignment"></center></td></tr>';
  print '</table></form><br>';
 }

class Con_in_Assign{
	
	public $db_dsn;
	public $tb_name;
	public $mdb2;
	public $debug;
	public $cutoff;
    
    function __construct($cutoff){
        //=====================================================================//
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn,  $tb_name ;
        
        $this->db_dsn = $db_dsn;
        
        $this->tb_name = $tb_name;
        
        $this->cutoff = $cutoff;
        
        $this->MySQL_set();
        //$this->qlist = implode(',',$qarr);
        //$this->qarr = $qarr;
    } 
    
    function __destruct(){
		$this->MySQL_end();
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
	
	function Get_qAssign($assign){
		if($this->debug)echo "<center>Get_qAssign()<br></center>";
        
        $ITSq         = new ITS_query();
		$resource_source = $ITSq->getCategory($assign);
        $query = 'SELECT id FROM ' . $this->tb_name . ' WHERE ' . $resource_source;
        $qarr =$this->Query($query,0,0);     
        $qarr = $this->GetCol($qarr,0);
        
        $ques_list = implode(",", $qarr);
             
        return $ques_list; 
	}

	function Get_Concepts($qAssign){
		if($this->debug)echo "<center>Get_Concepts()<br></center>";
		$query = "SELECT tag_id FROM questions_tags WHERE questions_id IN ({$qAssign})";
		echo "Debug: Get_Concepts 1" ;
		echo "Query: ", $query;
				die($query);
		$qarr =$this->Query($query,0,0);
		echo "Broken beyond line 114<hr>";
        $qarr = $this->GetCol($qarr,0);
        $ques_list = implode(",", $qarr);
        $ques_arr  = explode(",", $ques_list);
		echo $ques_arr;
        sort($ques_arr);
        //var_dump($ques_arr);
        return $ques_arr;
		
	} 
	
	function Count_Concepts($carr){
		if($this->debug)echo "<center>Count_Concepts()<br></center>";
        
        foreach($carr AS $i=>$con){
			if($con){
				if($con!=$carr[$i-1]){
					$concepts[$carr[$i-1]]=$count;
					$count=1;
				}else{ $count = $count + 1;}
			}
		}
		
		//var_dump($concepts);
		return $concepts;
	}
	
	function Use_Concepts($count){
		if($this->debug)echo "<center>Use_Concepts()<br></center>";
        
        $i=0;
		foreach($count as $con=>$num){
			if($num>$this->cutoff){
				$ans[$i]=$con;
				$i++;
			}
		}
		$ans = implode(",", $ans);
        
		return $ans;	
	}
	
	function List_Concepts($chapter){
		if($this->debug)echo "<center>List_concepts()<br></center>";
        
        $carr = $this->Get_qAssign($chapter);
		if(empty($carr)){
			$carr = "no questions";
		}else{
			echo "Debug: List_Concepts 1 ";
			$carr = $this->Get_Concepts($carr);
			echo "Debug: List_Concepts 2 ";
			$carr = $this->Count_Concepts($carr);
			$carr = $this->Use_Concepts($carr);
			echo $carr;
		}
		echo "----------------------------------";
		return $carr;
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
		//var_dump($answer);die('ssd');
		if($debug)var_dump($answer);

		return $answer;
	}

}

class SQL_com{

	public $db_dsn;
	public $tb_name_ca;
	public $mdb2;
	public $debug;
	public $field;
 
    function __construct($field){
        //=====================================================================//
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class().'<br>';
        }
        global $db_dsn;
        
        $this->db_dsn = $db_dsn;
        
        $this->tb_name_ca = "con_per_assign";
        $this->field = $field;
        $this->MySQL_set();
        $this->CheckTable(0);
        $this->CheckField(0);
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
			$query = "CREATE TABLE {$this->tb_name_ca}(assignment INT NOT NULL  PRIMARY KEY);";//AUTO_INCREMENT
			$ans = $this->Query($query,0,$debug);
			echo "<center><h1>Created new table: {$this->tb_name_ca}</h1><br></center>";
			$query = "INSERT INTO {$this->tb_name_ca} (assignment) VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11);";
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
			echo "<center><h2>Created {$this->field} in {$this->tb_name_ca}</h2></center><br>";
			$this->Query("ALTER TABLE {$this->tb_name_ca} ADD COLUMN {$this->field} VARCHAR(512);",0,$debug);
		}
	}

	function Update_Concepts($assign,$carr){
		if($this->debug)echo  "<center>Update_Concepts()<br></center>";
		
		$query = "UPDATE {$this->tb_name_ca} SET {$this->field} = '{$carr}' WHERE assignment = {$assign};";
		$result = $this->Query($query,0,0);
		//echo "{$query} <br>";
	}

	function Done(){
		echo "<center><h2>Added these organized Concepts to {$this->field} in {$this->tb_name_ca}<br></h2></center>";
	}
}
?>
