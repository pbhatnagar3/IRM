<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
    <meta content="text/html; charset=ISO-8859-1"
      http-equiv="Content-Type">
    <title>ITS STATS</title>
  </head>

  <body>
    <div style="text-align: center;"><h1><big><span
                style="font-weight: bold; color: rgb(153, 153, 153);"><span
                  style="color: rgb(102, 102, 102);">I</span>ntelligent
                <span style="color: rgb(102, 102, 102);">T</span>utoring
                <span style="color: rgb(102, 102, 102);">S</span>ystem </span>
<br></h1></big>
<big style=" color: rgb(0, 150, 50);">
<h2><a href="itsstats.php">ITS STATS</a></h2>
<br></big>

      <big style="text-decoration: underline; color: rgb(0, 0, 102);"><big><big>
            <a href=averages.php>Averages</a><br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>






<table border="2" cellpadding="5" align="center">
<form action="averages.php" method="GET">
<center>
<?php

print '<h4> Select Question ID to see the stats </h4>';





if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";

// Select database

if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";
$table = "MinedData";
if($_GET["qid"]!="")
$qid=$_GET["qid"];
else
$qid = 4;
print '<select name="qid">';
//Connect to the MySQL server

if(!($result = @ mysql_query("SELECT question_id FROM $table",$connection)))
  echo "query failed<br>";


while($row = mysql_fetch_array($result))
{

print '<center><option value="';
       print $row[0];
       print '"> ';
print $row[0];
	print '</option>';

}

echo "</select><center><h2>";

echo "</center></h2>";
echo "</select>";
print '<input value="Submit" type="submit"> </form>';
echo "</center>";

	
	




echo "<tr>";

echo "<th>qID</th>";
echo "<th>Avg_Score</th>";
echo "<th>Avg_Dur</th>";
echo "<th>Avg_Rating</th>";

echo "</tr>";




$query = "SELECT question_id,Avg,AvgDur, AvgRating FROM $table WHERE question_id = $qid";




if(!($result = @ mysql_query($query,$connection)))
  echo "query failed<br>";
while($row = mysql_fetch_array($result))
{
$i = 0;
 while($i<mysql_num_fields($result))
{
echo "<td>";
if($row[$i] !=NULL)
 echo "$row[$i] ";
else
echo "-";
echo "</td>";
$i = $i +1 ;
} 
echo "</tr>";

}




//echo "</table>";





print '<br><br><center><img style="-webkit-user-select: none" src=score_time_notavg.php?qid=';
print $qid;
print '>';

print '<img style="-webkit-user-select: none" src=score_rating_notavg.php?qid=';
print $qid;
print '>';

?>
</center>

<br>
</html>
