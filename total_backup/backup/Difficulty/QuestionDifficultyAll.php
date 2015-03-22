<html>
	<body>
<?php
print "<h>Table lamplist from database lamp</h1>\n<br>";
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br>";

// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

//The data table with the mined data
$DataTable = "MinedData";
//the questions data table
$QTable = "questions";

//percent values
$pA = .4;
$pAR = .1;
$pD = .25;
$pS = .25;


$query = "SELECT MAX(AvgDur) AS Dmax, MIN(AvgDur) AS Dmin, MAX(NumSkips) AS Smax, MIN(NumSkips) AS Smin  FROM {$DataTable};";
print $query ;
echo "<br>";
//Query the database for the min and max bounds
if(!($result=@mysql_query($query,$connection)))
	print "<br>query for mins and maxs failed<br>";

$Bounds = mysql_fetch_array($result);

$query = "UPDATE {$QTable} JOIN {$DataTable} ON {$QTable}.id={$DataTable}.question_id 
		  SET difficultyAll=10*({$pA}*(100-{$DataTable}.Avg)/100 + {$pAR}*{$DataTable}.AvgRating/5 +
						  {$pD}*({$Bounds["Dmax"]}-{$DataTable}.AvgDur)/({$Bounds["Dmax"]}-{$Bounds["Dmin"]}) +
						  {$pS}*({$Bounds["Smax"]}-{$DataTable}.NumSkips)/({$Bounds["Smax"]}-{$Bounds["Smin"]}))
		  Where {$QTable}.id={$DataTable}.question_id;";
echo "<br>";	
print $query ;
echo "<br>";
//Query the database for the min and max bounds
if(!($result=@mysql_query($query,$connection)))
	print $query;
print "<br>php ran";	
?>
</body>
</html>
