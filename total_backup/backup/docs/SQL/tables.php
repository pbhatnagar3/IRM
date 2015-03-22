<?php
include "../../config.php";
$dsn = preg_split("/[\/:@()]+/",$db_dsn);
//foreach ($dsn as $value) {echo $value.'<br>';}//die();
		
$host = $dsn[4];
$user = $dsn[1];
$pass = $dsn[2];
$db   = $dsn[6];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta content="text/html; charset=ISO-8859-1"
      http-equiv="Content-Type">
    <title>View Tables</title>
  </head>
  <body>
	  <p><center><h3> SQL TABLES</h3></center></p>
  <form action="tables1.php" method="GET"><br>
<center>
<h3> Select Table</h3>
<?php
//Connect to the MySQL server
if (!($connection = @mysql_connect($host,$user,$pass)))
    echo "connect failed<br>";

// Select database
if (!(mysql_select_db("its", $connection)))
    echo "select failed<br>\n";
// Query database for everything
if (!($result = @mysql_query("show tables", $connection)))
    echo "query failed<br>";
print '<select name=table_name>';

while ($row = mysql_fetch_array($result)) {
    echo '<option value="'.$row[0].'">'.$row[0].'</option>';
}
print '</select><br><br>';
?>
--- OR ---
<br><br>

Some Important Tables:
<select name=table_name2>
<option value ="-"> - <br>
<option value ="MinedDataV1"> MinedDataV1 <br>
<option value ="tags"> tags <br>
<option value ="index_1"> index_1 <br>
<option value ="dspfirst"> dspfirst <br>
<option value ="questions"> questions <br>
<option value ="users"> users <br>
</select>
  
</select>
<br><br>

 Number of entries : 
<input type="text" name="entry_nos" value="-" style="width:35px;" >

<p><input value="Submit" type="submit"> </form></p>
</center>
</html>
