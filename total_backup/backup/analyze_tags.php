<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
    <meta content="text/html; charset=ISO-8859-1"
      http-equiv="Content-Type">
    <title>iNSTRUCTOR iNTERFACE</title>
  </head>

  <body>
    <div style="text-align: center;"><h1><big><span
                style="font-weight: bold; color: rgb(153, 153, 153);"><span
                  style="color: rgb(102, 102, 102);">I</span>ntelligent
                <span style="color: rgb(102, 102, 102);">T</span>utoring
                <span style="color: rgb(102, 102, 102);">S</span>ystem </span>
<br></h1></big>
<big style=" color: rgb(150, 150, 150);">
<h2 style = "color:rgb(150,150,0);"><a style = "color:rgb(150,150,150)" href="instructor-ui.php">iNSTRUCTOR iNTERFACE</a></h2>
</big></div>

<h2 align="center" style=" color: rgb(0, 0, 102);"> <a href="analyze_tags.php"> Tag Analysis</h2><br></a>
<form action="analyze_tags.php">
<div id="pane1" style="width:10%;margin-left:30;float:left;">
<p2>Current tags: </p2>
<?php

$c1 = $_GET["compare1"];
$c2 = $_GET["compare2"];
$c3 = $_GET["compare3"];
$letter = $_GET["letter"];
if($_GET["cutoff"])
$cutoff = $_GET["cutoff"];
else
$cutoff = 50;



if($c1)
print '<input type="checkbox" name="compare1" value=1 checked=yes><br>';
else
print '<input type="checkbox" name="compare1" value=1><br>';

//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br>";

// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

// Query database
if(!($result = @ mysql_query("select * from tags order by name",$connection)))
  echo "query failed<br>";
print '<select size="35" multiple name="tags">';
$i=0;
 while($row = mysql_fetch_array($result))
{
	$a1[$i] = $row[1];
	$i++;
	print '<option value=';
       print $row[1];
       print '>';
	print $row[1];
	print '</option>';


}

print '</select><br>';
print 'Number of enteries: ';
print $i;



print '</div>';


print '<div id="pane2" style="width:20%;margin-left:10;float:left;">';

print '<p2>Book Index: </p2>';
if($c2)
print '<input type="checkbox" name="compare2" value=1 checked=yes><br>';
else
print '<input type="checkbox" name="compare2" value=1><br>';

// Query database
if(!($result = @ mysql_query("select * from index_1 order by name",$connection)))
  echo "query failed<br>";
print '<select size="35" multiple name="tags">';
$i=0;
 while($row = mysql_fetch_array($result))
{
	$a2[$i] = $row[1];
	$i++;
	print '<option value=';
       print $row[1];
       print '>';
	print $row[1];
	print '</option>';


}

print '</select>';
print 'Number of enteries: ';
print $i;

print '</div>';



print '<div id="pane3" style="width:15%;margin-left:10;float:left;">';

print '<p2>Proposed Concepts: </p2>';
if($c3)
print '<input type="checkbox" name="compare3" value=1 checked=yes><br>';
else
print '<input type="checkbox" name="compare3" value=1><br>';

// Query database
if(!($result = @ mysql_query("select * from concepts_step2 order by C2_name",$connection)))
  echo "query failed<br>";
print '<select size="35" multiple name="tags">';
$i=0;
 while($row = mysql_fetch_array($result))
{
	$a3[$i] = $row[1];
	$i++;
	print '<option value=';
       print $row[1];
       print '>';
	print $row[1];
	print '</option>';

}
if(!($result = @ mysql_query("select * from concepts_step1 order by C1_name",$connection)))
  echo "query failed<br>";
//print '<select size="35" multiple name="tags">';
 while($row = mysql_fetch_array($result))
{
	$a3[$i] = $row[1];
	$i++;
	print '<option value=';
       print $row[1];
       print '>';
	print $row[1];
	print '</option>';

}

print '</select><br>';
print 'Number of enteries: ';
print $i;
print '<br>';

print '</div>';

print '<div id="pane4" style="width:10%;margin-left:10;float:left;">';

print '<input type="radio"  name = "letter" value="0" checked=yes>Percentage<br>';
print '<input type="radio"  name = "letter" value="1">By letter<br>';
print '<input type="text"  name = "cutoff"><br>';
print '<input type="submit" value="Find Intersection">';
print '</div>';



if($c1 or $c2 or $c3)
{
print '<div id="pane5" style="width:20%;margin-left:10;float:left;">';

print '<p2>Intersection:  </p2>';


$i=0;
while($a1[i])
{
	$a1[i] = strtolower($a1[i]);
	$i++;
}

$i=0;
while($a2[i])
{
	$a2[i] = strtolower($a2[i]);
	$i++;
}

$i=0;
while($a3[i])
{
	$a3[i] = strtolower($a3[i]);
	$i++;
}



function string_inter($a1,$a2,$cutoff,$letter)
{
//$big = count($a1)>count($a2)?$a1:$a2;
//$small = count($a1)<count($a2)?$a1:$a2;
//$big = $a1;
//$small = $a2;

$i=0;
$k=0;
if($letter == 1)
{
while($i<count($a1))
{
	$j=0;
	while($j<count($a2))
	{
		similar_text($a1[$i],$a2[$j],$per);
		//$norm = strlen($a1)<strlen($a2)? strlen($a2)/strlen($a1):strlen($a1)/strlen($a2);
		if(similar_text($a2[$j],$a1[$i]) > $cutoff )
		{
		$res[$k++] = $a2[$j];
		}

		$j++;
	}
	$i++;
}
}
else
{
while($i<count($a1))
{
	$j=0;
	while($j<count($a2))
	{
		similar_text($a1[$i],$a2[$j],$per);
		//$norm = strlen($a1)<strlen($a2)? strlen($a2)/strlen($a1):strlen($a1)/strlen($a2);
		 if($per>=$cutoff)
		{
		$res[$k++] = $a2[$j];
		//$res[$k++] = strcmp($a1[$i],$a2[$j])>0 ? $a1[$i] : $a2[$j] ;
		}

		$j++;
	}
	$i++;
}


}
$res = array_unique($res);
return $res;
}

function string_diff($a1,$a2,$cutoff,$letter)
{
//$big = count($a1)>count($a2)?$a1:$a2;
//$small = count($a1)<count($a2)?$a1:$a2;
//$big = $a1;
//$small = $a2;

$i=0;
$k=0;

if($letter == 1)
{
while($i<count($a1))
{
	$j=0;
	while($j<count($a2))
	{
		similar_text($a1[$i],$a2[$j],$per);
		//$norm = strlen($a1)<strlen($a2)? strlen($a2)/strlen($a1):strlen($a1)/strlen($a2);
		if(similar_text( $a2[$j],$a1[$i]) > $cutoff)
		{
		$res2[$k++] = $a2[$j];
		}
	
		$j++;
	}
	$i++;
}
}
else
{

while($i<count($a1))
{
	$j=0;
	while($j<count($a2))
	{
		similar_text($a1[$i],$a2[$j],$per);
		//$norm = strlen($a1)<strlen($a2)? strlen($a2)/strlen($a1):strlen($a1)/strlen($a2);
	if($per>=$cutoff)
		{
		$res2[$k++] = $a2[$j];
		//$res[$k++] = strcmp($a1[$i],$a2[$j])>0 ? $a1[$i] : $a2[$j] ;
		}
		$j++;
	}
	$i++;
}

}

$res2 = array_unique($res2);

$res2 = array_diff($a2,$res2);
return $res2;
}



$sel = $c1 + $c2 + $c3;  
if($sel == 3)
{
 $res = string_inter($a1,$a3,$cutoff,$letter);
 $res = string_inter($res,$a2,$cutoff,$letter);
 $res2 = string_diff($a1,$a3,$cutoff,$letter);
 $res2 = string_diff($res2,$a2,$cutoff,$letter);
	
}
else if($sel == 2)
{
	if($c1 and $c2)
	{
		$res = string_inter($a1,$a2,$cutoff,$letter);
		$res2 = string_diff($a1,$a2,$cutoff,$letter);

	}
	else if($c2 and $c3)
	{ 
		$res = string_inter($a3,$a2,$cutoff,$letter);
		$res2 = string_diff($a3,$a2,$cutoff,$letter);
	}
	else if($c1 and $c3)
	{
		$res = string_inter($a3,$a1,$cutoff,$letter);
		$res2 = string_diff($a3,$a1,$cutoff,$letter);
	}
}
else if($sel == 1)
{
	if($c1)
	$res = $a1;
	else if($c2)
	$res = $a2;
	else if($c3)
	$res = $a3;
}


print '<br><select size="35" multiple name="tags">';
$i=0;
$r = 0;
 while($i<10000)
{
	if(strlen($res[$i])>0){
	print '<option value=';
       print $res[$i];
       print '>';
	print $res[$i];
	print '</option>';
	$r++;
	}
		$i++;
}

print '</select><br>';
print 'Number of enteries: ';
print $r;


print '</div>';

print '<div id="pane6" style="width:15%;margin-left:10;float:left;">';
print '<p2>Difference:  </p2>';

print '<br><select size="35" multiple name="tags">';
$i=0;
$r = 0;
 while($i<10000)
{
	if(strlen($res2[$i])>0){
	print '<option value=';
       print $res2[$i];
       print '>';
	print $res2[$i];
	print '</option>';
	$r++;
	}
		$i++;
}

print '</select><br>';
print 'Number of enteries: '.$r.'</div>';
}

?>

</body>
</html>
