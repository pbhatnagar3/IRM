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
      <big style=" color: rgb(0, 0, 102);"><big><big>

            <a href="viewquestions.php">View Questions</a>


<br>
          </big></big></big><big style="color: rgb(0, 0, 102);"><big><big><br>
          </big></big></big>
      <div style="text-align: left;"><big style="color: rgb(0, 0, 102);"><big><big>
            </big></big></big>









  <form action="viewquestions1.php" method="GET"> <br>
 

<center>
<h3> Select Question Tag</h3>

<?php
//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br>";

// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";
// Query database for everything
if(!($result = @ mysql_query("select * from tags",$connection)))
  echo "query failed<br>";


print '<select name=tag>';

 while($row = mysql_fetch_array($result))
{
	print '<option value=';
       print $row[1];
       print '>';
	print $row[1];
	print '</option>';
	
}


print '</select><br><br>';


 
?>

 
<input value="Submit" type="submit"> </form>
</center>
</html>
