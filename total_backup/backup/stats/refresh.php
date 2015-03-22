<html>
<body>
<?php
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br>";

// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";
// Query database for everything
if(!($result = @ mysql_query("describe MinedDataV1",$connection)))
  echo "query failed<br>";
?>

<form action="<?php echo $PHP_SELF;?>" method="post">
Select the semesters you would like to use data from:<br />
<input type="checkbox" name="semester[]" value="Fall_2011" />Fall 2011<br />
<input type="checkbox" name="semester[]" value="Spring_2011" />Spring 2011<br />
Throw out questions with a duration longer than:<br />
<input type="text" name="threshold[]" value="1000" />High threshold<br />
Throw out questions with a duration shorter than:<br />
<input type="text" name="threshold[]" value="0" />Low threshold<br />
Throw out questions answered fewer times than:<br />
<input type="text" name="threshold[]" value="0" />Frequency<br />
<input type="submit" name="formSubmit" value="Submit" />
</form>

<?php
//print_r($_POST);
//var_dump($_POST);
//echo $_POST['semester'];
$query="select id from users where status='{$_POST['semester'][0]}';";
//if(isset($_POST['semester']))
//print_r($query);
$result=mysql_query($query);
$i=0;
while($_POST['semester'][$i])  //for each selected semester
{
$query="select id from users where status='{$_POST['semester'][$i]}';";//get the user IDs from that semester
$result=mysql_query($query);
$i++;
	while($row=mysql_fetch_array($result, MYSQL_NUM))
	{
	$users[]=$row[0];//and put them in a new array
	}
}
//print_r($users);

for($k=0;$k<=count($users);$k++){
$query="select score, duration, question_id from stats_{$users[$k]} where duration and score is not null;";
//add ratings later
$result=mysql_query($query);
//$result2=mysql_query($query2);
//$result3=mysql_query($query3);
while($row=mysql_fetch_array($result, MYSQL_NUM)){
//print_r($row);
$tempscores[]=$row[0];
$tempduration[]=$row[1];
$qids[]=$row[2];
}
}
//do we need to worry about NULLs?
//echo "there are currently this many elements in tempscores";
//echo(count($tempscores));
//echo "<br />";
//echo "there are currently this many elements in tempduration";
echo(count($tempduration));
//echo "<br />";
//echo "there are currently this many elements in qids";
//echo(count($qids));
//echo "<br />";

$tempcount=count($tempduration);
for($j=0;$j<=$tempcount;$j++){
	if($tempduration[$j]<$_POST['threshold'][1]){
	unset($tempscores[$j]);
	unset($tempduration[$j]);
	unset($qids[$j]);
	}
	if($tempduration[$j]>$_POST['threshold'][0]){
	unset($tempscores[$j]);
	unset($tempduration[$j]);
	unset($qids[$j]);
	}
}

$tempuniqids=array_unique($qids);
$tempcounts=array_count_values($qids);

//echo "uniqids is <br/>";
//print_r($uniqids);
//echo "counts is <br />";
//print_r($counts);

foreach($tempuniqids as $val){
if($tempcounts[$val]<$_POST['threshold'][2]){//if we don't meet the threshold,unset values from all three arrays
$temp=array_keys($qids,$val);
$tempcount=count($temp);
for($k=0;$k<$tempcount;$k++){
unset($qids[$temp[$k]]);
unset($tempscores[$temp[$k]]);
unset($tempduration[$temp[$k]]);
}

}
}

$uniqids=array_unique($qids);
$counts=array_count_value($qids);

for($i=0;$i<count($uniqids);$i++){
$indx=array_keys($qids,$uniqids[$i]);

for($j=0;$j<$count($indx);$j++){

}
$one=$i;//ID
$two=$uniqids[$i];//QID
$three=;//AVG
$four=;//%0
$five=;//#0
$six=;//%100
$seven=;//#100
$eight=;//AVG DUR.
$nine=$count($indx);//#ANS
$ten=;//AVG RATING
$eleven=;//#RATING

}

//echo "<br />";
//echo("modified thresholds");
//echo "<br />";
//echo("there are currently this many elements in tempscores");
//echo(count($tempscores));
//echo "<br />";
//echo("there are currently this many elements in tempduration");
//echo(count($tempduration));
//echo "<br />";
//echo("there are currently this many elements in qids");
//echo(count($qids));
//echo "<br />";

?>
</body>
</html>

