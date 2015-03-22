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
<br></big>      <big style="text-decoration: underline; color: rgb(0, 0, 102);"><big><big>
            <a href=tables.php>View Table</a><br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>

<table border="2" cellpadding="5" align="center">
<form action="tables1.php" method="GET">
<center>
<?php
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";

// Select database

if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

if($_GET["table_name2"]!="-")
$table = $_GET["table_name2"];
else 
$table = $_GET["table_name"];
if($_GET["entry_nos"]!="-")
$num = $_GET["entry_nos"];
else
$num="NULL";
$filter = $_GET["filter"];

if($_GET["order"])
$order = $_GET["order"];
else 
$order="";


echo "<center><h2>";
print $table;
echo "</center></h2>";
print '<input type="hidden" name="table_name" value =';
print $table;
print '>';
print '<input type="hidden" name="entry_nos" value =';
print $num;
print '>';
print '<input type="hidden" name="table_name2" value ="-">';
print '<input type="hidden" name="order" value = "';
print $order;
print '">';


print '<select name="filter">';

if(!($result = @ mysql_query("DESCRIBE $table",$connection)))
  echo "query failed<br>";

$i=0;
while($row = mysql_fetch_array($result))
{

print '<center><option value="';
       print $row[0];
       print '"> Order By : ';
print $row[0];
	print '</option>';
$i = $i+1;
}
echo "</select>";
print '<select name="order">';
print'<option value=""> Ascending </option>';
print'<option value="desc"> Descending </option>';
print '</select>';
print '<input value="Submit" type="submit"> </form>';
echo "</center>";
	
	



// Query database for everything
if(!($result = @ mysql_query("DESCRIBE $table",$connection)))
  echo "query failed<br>";
 echo "<tr>";
while($row = mysql_fetch_array($result))
{
echo "<th>$row[0]</th>";
}
echo "</tr>";

if($num!="NULL" && $filter!="")
$query = "SELECT * FROM $table ORDER BY $filter $order LIMIT $num ";
else if($num=="NULL" && $filter!="")
$query = "SELECT * FROM $table ORDER BY $filter $order";
else if($num!="NULL" && $filter=="")
$query = "SELECT * FROM $table LIMIT $num $order";
else
$query = "SELECT * FROM $table $order";



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

?>
</center>

<br>
</html>
