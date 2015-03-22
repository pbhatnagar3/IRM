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
            <a href=viewquestions.php>View Questions</a><br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>



<table border="2" cellpadding="5" align="center">
<th>qid</th>
<th>qtype</th>
<th>question</th>
<th>category</th>

<center>
<?php




$tag= $_GET["tag"];




echo "<h3> Questions with ";
echo " tag = '$tag' </h3>";

 
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";

// Select database

if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

if(!($abc = @ mysql_query("DESCRIBE MinedData",$connection)))
  echo "query failed 1<br>";
$row = mysql_fetch_array($abc);
$row = mysql_fetch_array($abc);
while($row = mysql_fetch_array($abc))
{
echo "<th>$row[0]</th>";
}
echo "</tr>";


if(!($result = @ mysql_query("SELECT question_id from tags where name= '{$tag}'",$connection)))
  echo "query failed <br>";

$str = mysql_fetch_array($result,MYSQL_NUM);
$str = str_replace(array(" "),array(","),$str[0]);
$str = str_replace(array(",,"),array(","),$str);
$str = str_replace(array(",,"),array(","),$str);
$ids=(string)$str;

print '<br><br><center><img style="-webkit-user-select: none" src=score-time.php?id=';
print $ids;
print '>';

print '<img style="-webkit-user-select: none" src=score-rating.php?id=';
print $ids;
print '><br><br>';








$str1=explode(",",$ids);


$i = 0;
 while($i<sizeof($str1))
{
if(!($result = @ mysql_query("SELECT id,qtype,question,category from questions where id= $str1[$i]",$connection)))
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


if(!($statresult = @ mysql_query("SELECT * from MinedData where question_id= $str1[$i]",$connection)))
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


$i = $i +1 ;
echo "</tr>";
} 
echo "</tr>";







//echo "</table>";

?>
</center>

<br>
</html>
