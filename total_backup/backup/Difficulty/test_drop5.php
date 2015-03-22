<?php
$file="test_drop5.php";
echo '<html><body>';
if($_SERVER["REQUEST_METHOD"]!="POST"){
	formIn(0,$file);
}else if(($_POST['pA']+$_POST['pAR']+$_POST['pD']+$_POST['pS'])!=100){
	formIn(1,$file);
}else{
	$DataTable = "MinedData"; //The data table with the mined data
	$QTable = "questions_difficulty";  //the questions difficulty data table
	$DifCol = "difficultyDrop_N";  //The difficulty column to use
	$connection = SQL_setup();
	UpdateQID($connection,$QTable,$DataTable,0);
	CheckField($connection,$QTable,$DifCol,0);
	runDif($connection,$DataTable,$QTable,$DifCol,$_POST['pA'],$_POST['pAR'],$_POST['pD'],$_POST['pS'],0);
	
	
	print "<h2>Form	arguments	in URL</h2><br>\n";	  
foreach($_POST as$x=>$value)	{	
	  print	"{$x}:{$value}<br>\n"; 
};	
};

echo '</body></html>';


FUNCTION SQL_setup(){
	if(!($connection = @ mysql_connect(localhost,root,csip)))
		echo "connect failed<br>";

	// Select database
	if(!(mysql_select_db("its",$connection)))
		echo "select failed<br>\n";  
	
	RETURN $connection;
}

FUNCTION formIn($badIn,$file){
  if($badIn)
		print '<center><bold><font color="red">The inputs must sum to 100%</font></bold></center><br>';
  print	"<form	action={$file}	method='POST'>";
  print '<table align="center">';	
  print	'<tr><td>% given to Average Score:</td>
		 <td><input	type="text"	name="pA"	value=40><br></td></tr>';	
  print	'<tr><td>% given to Average Student Rating:</td>
		 <td><input	type="text"	name="pAR"	value=10><br></td></tr>';	
  print	'<tr><td>% given to Duration:</td>
		 <td><input	type="text"	name="pD"	value=25><br></td></tr>';	
  print	'<tr><td>% given to Number of Skips:</td>
		 <td><input	type="text"	name="pS"	value=25><br></td></tr>';	
  print	'<tr><td>% given to Number of Drop:</td>
		 <td><input	type="text"	name="pDrop"	value=5><br></td></tr>';		 
  print '<tr><td colspan="2" align="center"><center>
		 <input type="submit"value="Run the difficulty algorithm"></center></td></tr>';
  print '</table></form><br>';
 }

FUNCTION runDif($connection,$DataTable,$QTable,$DifCol,$pA,$pAR,$pD,$pS,$Dbug){
	$types = array('M','MC','C','S','P');
	$total=0;
	//print '<table border=2>';
	FOREACH($types as $QType){
		$result=MyQuery("SELECT id,name FROM tags;",$connection,"Failed to get tag IDs and Names",$Dbug);
		WHILE ($row = mysql_fetch_array($result)){
			//print "<tr><td>&nbsp;{$QType}&nbsp;</td>";
			//print "<td>&nbsp;{$row['name']}&nbsp;</td><td>";
			if(updateDif($connection,$DataTable,$QTable,$DifCol,$pA,$pAR,$pD,$pS,$row['id'],$QType,$Dbug))
				$total=$total+printDif($connection,$QTable,$DifCol,$row['id'],$QType,$Dbug);
			//print "</td></tr>";
		}
		
	}
	//print "</table><br> <h1><center>{$total}</center></h1>";
	RETURN 1;
}

FUNCTION printDif($connection,$QTable,$DifCol,$tag_id,$QType,$Dbug){
	$total=0;
	//print "<table border=1><tr>";
	for($i=0;$i<=9;$i++){
		$j=$i+1;
	//	print "<td><center>{$i}-{$j}</center></td>";
	}
	//print "<td><center>Total</center></td></tr><tr>";
	for($i=0;$i<=9;$i++){
		$query="SELECT Count(*) as NUM FROM {$QTable} JOIN questions ON questions.id={$QTable}.q_id 
				WHERE {$QTable}.q_id=questions.id 
					AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$'
					AND questions.qtype LIKE '{$QType}'
					AND {$QTable}.{$DifCol}>{$i}
					AND {$QTable}.{$DifCol}<=({$i}+1)";
		$result=MyQuery($query,$connection,"Failed to Count elements",$Dbug);
		$row = mysql_fetch_array($result);
		//print "<td><center>{$row['NUM']}</center></td>";
		$total=$total+$row['NUM'];
	}
	//print "<td><center>{$total}</center></td></tr></table>";
	RETURN $total;
	
}

FUNCTION updateDif($connection,$DataTable,$QTable,$DifCol,$pA,$pAR,$pD,$pS,$tag_id,$QType,$Dbug){
	$ran  = 1;
	$max  = mysql_fetch_array($result);
	//Query the database for the min and max bounds
	
	$query = "SELECT MAX(AvgDur) AS Dmax, MIN(AvgDur) AS Dmin, MAX(NumSkips) AS Smax, MIN(NumSkips) AS Smin,
			  AVG(AvgDur) AS Davg, STD(AvgDur) AS Dstd, AVG(NumSkips) AS Savg, STD(NumSkips) AS Sstd    
				FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.qtype LIKE '{$QType}';";
				
	/*			 AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$'*/
				 
	if(!($result=MyQuery($query,$connection,"query for mean and std failed",$Dbug)))
		$ran=0;
		
	$Bounds = mysql_fetch_array($result);
	print_r("oldDMax = ".$Bounds[Dmax]."\n oldDMIN = ".$Bounds[Dmin]."\n oldSmax = ".$Bounds[Smax]."\n oldSmin = ".$Bounds[Smin]);
	//print_r("oldDMax = ".$Bounds[Dmax]."\n\n\n");
	//print_r("oldDmin = ".$Bounds[Dmin]."\n\n\n");
	$queryDMax = "SELECT AvgDur as Dmax FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.qtype LIKE '{$QType}' Order by AvgDur DESC LIMIT 4,1;";
	if(!($result=MyQuery($queryDMax,$connection,"query for DMax failed",$Dbug)))
		$ran=0;
	$temp=mysql_fetch_array($result);
	if($temp["Dmax"]!=null)
	$Bounds["Dmax"]=$temp["Dmax"];
	//print_r("newDMax = ".$Bounds["Dmax"]."\n\n\n");
	
	$queryDMin = "SELECT AvgDur as Dmin FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.qtype LIKE '{$QType}' Order by AvgDur LIMIT 4,1;";
	//print($queryDMin);
	
	if(!($result=MyQuery($queryDMin,$connection,"query for DMin failed",$Dbug)))
		$ran=0;
	$temp=mysql_fetch_array($result);
	//print_r($temp);
	if($temp["Dmin"]!=null)
	$Bounds["Dmin"]=$temp["Dmin"];
	//print_r("newDmin = ".$Bounds["Dmin"]."\n\n\n");
	
	$querySMax = "SELECT NumSkips as Smax FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.qtype LIKE '{$QType}' Order by NumSkips DESC LIMIT 4,1;";
	if(!($result=MyQuery($querySMax,$connection,"query for SMin failed",$Dbug)))
		$ran=0;
	$temp=mysql_fetch_array($result);
	if($temp["Smax"]!=null)
	$Bounds["Smax"]=$temp["Smax"];
	
	$querySMax = "SELECT NumSkips as Smin FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.qtype LIKE '{$QType}' Order by NumSkips LIMIT 4,1;";
	if(!($result=MyQuery($querySMax,$connection,"query for SMax failed",$Dbug)))
		$ran=0;
	$temp=mysql_fetch_array($result);
	if($temp["Smin"]!=null)
		$Bounds["Smin"]=$temp["Smin"];
	
	//$Bounds["Dmax"]=//$Bounds["Davg"]+$Bounds["Dstd"];
	//$Bounds["Dmin"]=//$Bounds["Davg"]-$Bounds["Dstd"];
	//$Bounds["Smax"]=//$Bounds["Savg"]+$Bounds["Sstd"];
	//$Bounds["Smin"]=//$Bounds["Savg"]-$Bounds["Sstd"];
	//Query the database for the min and max bounds
	//print_r("newDMax = ".$Bounds[Dmax]."\n newDMIN = ".$Bounds[Dmin]."\n newSmax = ".$Bounds[Smax]."\n newSmin = ".$Bounds[Smin]);
	
	
	$query = "UPDATE {$QTable} JOIN {$DataTable} ON {$QTable}.q_id={$DataTable}.question_id JOIN questions ON questions.id={$QTable}.q_id  
			SET {$QTable}.{$DifCol}=
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))<1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})>0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
									{$pD}*({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}) +
									{$pS}*({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))<1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})>0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
									{$pD}*0 +
									{$pS}*({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))<1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})>0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
									{$pD}*1+
									{$pS}*({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))<1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})<0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
									{$pD}*({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}) +
									{$pS}*0),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))>1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})>0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
								{$pD}*({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}) +
								{$pS}*1),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))<0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))<1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})<0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
								{$pD}*0 +
								{$pS}*0),
								IF((({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>1   AND 
								   (({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}))>0   AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))>1 AND
								   (({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]})>0),
								   .1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
							{$pD}*1 +{$pS}*1),NULL)))))))
								
								
			Where {$QTable}.q_id={$DataTable}.question_id 
			AND questions.qtype LIKE '{$QType}';";
/*				 AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$'*/
				 
	if(!($result=MyQuery($query,$connection,"Difficulty setting failed",$Dbug)))
		$ran=0;
/*				 AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$'
				 
	if(!($result=MyQuery($query,$connection,"Difficulty setting failed",$Dbug)))
		$ran=0;*/
/*SELECT questions.id,MinedData.question_id,questions_difficulty.q_id,questions.qtype,questions.tag_id,questions_difficulty.difficulty,
MinedData.Avg,MinedData.AvgDur,MinedData.AvgRating,MinedData.NumSkips 
From questions JOIN questions_difficulty ON questions.id=questions_difficulty.q_id JOIN MinedData ON questions.id=MinedData.question_id;
* */
	return $ran;	
}

FUNCTION CheckField($connection,$Table,$Field,$Dbug){
	$exist=0;
	$result=MyQuery("desc {$Table};",$connection,"Table does not exist: {$Table} ",$Dbug);
	WHILE(($row = mysql_fetch_array($result)) AND !$exist){
		if($Dbug)print $row["Field"]."<br>";
		If(!strcasecmp($row["Field"],$Field))
			$exist=1;
	}
	IF(!$exist){
		Print "Created {$Field} in {$Table}<br>";
		MyQuery("ALTER TABLE {$Table} ADD COLUMN {$Field} DECIMAL(5,4);",$connection,"Column creation failed",$Dbug);
	}
}

FUNCTION UpdateQID($connection,$QTable,$DataTable,$Dbug){
	$query="INSERT INTO {$QTable} (q_id) 
			SELECT question_id FROM {$DataTable} 
			WHERE {$DataTable}.question_id 
				NOT IN (SELECT q_id FROM {$QTable});";
	MyQuery($query,$connection,"Failed to update {$QTable}.q_id field",$Dbug);
}	

FUNCTION MyQuery($query,$connection,$fail,$Dbug){
	if($Dbug)print $query."<br>";
	if(!($result=@mysql_query($query,$connection)))
		print "<br><center><h2>{$fail}</h2></center><br>";
	RETURN $result;
}
		
	
?>
