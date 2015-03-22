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
            <a href=view_question.php>Questions</a><br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>

<center>
<table border="2" cellpadding="5" align="center">
<th>qid</th>
<th>qtype</th>
<th>question</th>
<th>category</th>


<form align="center" action="view_question.php" method="GET">
<?php

print '<h4> Select Question ID to see the question </h4>';

if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";

// Select database

if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

if(!($abc = @ mysql_query("DESCRIBE MinedData",$connection)))
  echo "query failed 1";
$row = mysql_fetch_array($abc);
$row = mysql_fetch_array($abc);
while($row = mysql_fetch_array($abc))
{
echo "<th>$row[0]</th>";
}

print '<th>tags </th>';
echo "</tr>";


$table = "questions";
if($_GET["qid"]!="")
$qid=$_GET["qid"];
else
$qid = 4;
print '<select name="qid">';
//Connect to the MySQL server

if(!($result = @ mysql_query("SELECT id FROM $table",$connection)))
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
print '<input value="Submit" type="submit"> </form><br><br><br>';
echo "</center>";

if(!($result = @ mysql_query("SELECT id,qtype,question,category from questions where id= $qid",$connection)))
  echo "<center><h3> NO RESULT FOUND </h3>";

$row = mysql_fetch_array($result,MYSQL_NUM);
$j=0;
while($j<sizeof($row,0))
{
echo "<td>";
if($row[$j] !=NULL)
 echo "$row[$j]";
else
echo "-";
echo "</td>";
$j = $j +1 ;
}




if(!($statresult = @ mysql_query("SELECT * from MinedData where question_ID= $qid",$connection)))
  echo "<center><h3> Select different tag</h3>";

$stat = mysql_fetch_array($statresult,MYSQL_NUM);

$k=2;
if(sizeof($stat,0)>11)
$size = sizeof($stat,0);
else $size=11;
while($k<$size)
{
echo "<td>";
if($stat!=NULL)
 echo "$stat[$k]";
else
echo "-";
echo "</td>";
$k = $k +1 ;
}
if(!($result = @ mysql_query("SELECT tag_id from questions where id= $qid",$connection)))
  echo "<center><h3> NO RESULT FOUND </h3>";

$row = mysql_fetch_array($result,MYSQL_NUM);
$tags = explode(',',$row[0]);
$k=0;
print '<td>';

while($k<sizeof($tags,0))
{
if(!($result = @ mysql_query("SELECT name from tags where id=$tags[$k]",$connection)))
  echo "<center> - ";
$row1 = mysql_fetch_array($result,MYSQL_NUM);
$names[$k]=$row1[0];
$k=$k+1;
}

$names = implode(',',$names);
print_r($names);
echo "</td>";


echo "</table>";

?>

<br>
</html>
