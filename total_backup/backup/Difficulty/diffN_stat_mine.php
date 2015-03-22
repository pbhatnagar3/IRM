<?php
$file="diffN_stat_mine.php";
echo '<html><body>';

	$DataTable = "MinedData"; //The data table with the mined data
	$QTable = "questions_difficulty";  //the questions difficulty data table
	$DifCol = "difficultyDrop_N_mine";  //The difficulty column to use
	$connection = SQL_setup();
	//UpdateQID($connection,$QTable,$DataTable,0);
	CheckField($connection,$QTable,$DifCol,0);
	runStat($connection,$QTable,$DifCol,$Dbug);
echo '</body></html>';


FUNCTION SQL_setup(){
	if(!($connection = @ mysql_connect(localhost,root,csip)))
		echo "connect failed<br>";

	// Select database
	if(!(mysql_select_db("its",$connection)))
		echo "select failed<br>\n";  
	
	RETURN $connection;
}

FUNCTION runStat($connection,$QTable,$DifCol,$Dbug){
	$total=0;
	print "<table border=1><tr>";
	for($i=0;$i<=9;$i++){
		$j=$i+1;
		print "<td><center>{$i}-{$j}</center></td>";
	}
	print "<td><center>Total</center></td></tr><tr>";
	for($i=0;$i<=9;$i++){
		$query="SELECT Count(*) as NUM FROM {$QTable} JOIN questions ON questions.id={$QTable}.q_id 
				WHERE {$QTable}.q_id=questions.id 
					AND {$QTable}.{$DifCol}>{$i}
					AND {$QTable}.{$DifCol}<=({$i}+1)";
		$result=MyQuery($query,$connection,"Failed to Count elements",$Dbug);
		
		$row = mysql_fetch_array($result);
		print "<td><center>{$row['NUM']}</center></td>";
		
		$total=$total+$row['NUM'];
	}
	
	$queryMax="SELECT Max({$DifCol}) as NUM FROM {$QTable};";  
	$max=MyQuery($queryMax,$connection,"Failed",$Dbug);
	$row = mysql_fetch_array($max);
	print "Max difficulty level = {$row['NUM']}<br>";
	
	$queryMin="SELECT Min({$DifCol}) as NUM FROM {$QTable};";  
	$min=MyQuery($queryMin,$connection,"Failed",$Dbug);
	$row = mysql_fetch_array($min);
	print "Min difficulty level = {$row['NUM']}<br>";
	
	$querySTD="SELECT STD({$DifCol}) as NUM FROM {$QTable};";
	$std=MyQuery($querySTD,$connection,"Failed",$Dbug);
	$row = mysql_fetch_array($std);
	print "STD = {$row['NUM']}<br>";
	
	$queryMean="SELECT AVG({$DifCol}) as NUM FROM {$QTable};";
	$Mean=MyQuery($queryMean,$connection,"Failed",$Dbug);
	$row = mysql_fetch_array($Mean);
	print "Mean = {$row['NUM']}<br>";
	//print "<td><center>{$min}</center></td></tr></table>";
	//print "<td><center>{$std}</center></td></tr></table>";
		
	print "<td><center>{$total}</center></td></tr></table>";
	RETURN $total;
	
}

FUNCTION updateDif($connection,$DataTable,$QTable,$DifCol,$pA,$pAR,$pD,$pS,$tag_id,$QType,$Dbug){
	$ran=1;
	
	//Query the database for the min and max bounds
	$query = "SELECT MAX(AvgDur) AS Dmax, MIN(AvgDur) AS Dmin, MAX(NumSkips) AS Smax, MIN(NumSkips) AS Smin  
				FROM {$DataTable} JOIN questions ON questions.id={$DataTable}.question_id 
				WHERE {$DataTable}.question_id=questions.id 
				AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$|^{$tag_id}$'
				AND questions.qtype LIKE '{$QType}';";
	if(!($result=MyQuery($query,$connection,"query for mins and maxs failed",$Dbug)))
		$ran=0;
		
	$Bounds = mysql_fetch_array($result);
	
	//Query the database for the min and max bounds
	$query = "UPDATE {$QTable} JOIN {$DataTable} ON {$QTable}.q_id={$DataTable}.question_id JOIN questions ON questions.id={$QTable}.q_id  
			SET {$QTable}.{$DifCol}=.1*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
							{$pD}*({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}) +
							{$pS}*({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))
			Where {$QTable}.q_id={$DataTable}.question_id 
				AND questions.tag_id REGEXP '^{$tag_id},|,{$tag_id},|,{$tag_id}$|^{$tag_id}$'
				AND questions.qtype LIKE '{$QType}';";	
	if(!($result=MyQuery($query,$connection,"Difficulty setting failed",$Dbug)))
		$ran=0;
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
