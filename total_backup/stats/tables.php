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
            Tables<br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>









  <form action="tables1.php" method="GET"> <br>
 

<center>
<h3> Select Table</h3>

<?php
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br>";

// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";
// Query database for everything
if(!($result = @ mysql_query("show tables",$connection)))
  echo "query failed<br>";


print '<select name=table_name>';

 while($row = mysql_fetch_array($result))
{
	print '<option value=';
       print $row[0];
       print '>';
	print $row[0];
	print '</option>';
	
}


print '</select><br><br>';

 
?>
--- OR ---
<br><br>

Some Important Tables:
<select name=table_name2>
<option value ="-"> - <br>
<option value ="MinedData"> MinedData <br>
<option value ="tags"> tags <br>
<option value ="questions"> questions <br>

</select>
  
</select>
<br><br>

 Number of entries : 
<input type="text" name="entry_nos" value="-" style="width:35px;" >

<br><br><br><br><br><br>

<input value="Submit" type="submit"> </form>
</center>
</html>
