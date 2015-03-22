<?php
include("../config.php");
global $db_dsn, $db_name, $db_table_users, $db_table_user_state;

$dsn = preg_split("/[\/:@()]+/",$db_dsn);
$db_user = $dsn[1];
$db_pass = $dsn[2];
$host    = $dsn[4];
$db_name = $dsn[6];
//------------------------------------------//
//echo $host.$db_user.$db_pass.'<br>';

$table = 'SPF';
$array = array(
    'chapter',
    'term',
    'year'
);
//var_dump($_POST['term']);
if (isset($_POST['term'])) {
    if ($_POST['term'] == 'ALL') {
        $term = '';
    } else {
        $term = ' AND term="' . $_POST['term'].'"';
    }
} else {
	
}
if (isset($_POST['year'])) {
    if ($_POST['year'] == 'ALL') {
        $year = '';
    } else {
        $year = ' AND year="' . $_POST['year'].'"';
    }
}
if (isset($_POST['chapter'])) {
    if ($_POST['chapter'] == 'ALL') {
        $chapter = '';
    } else {
        $chapter = ' AND chapter="' . $_POST['chapter'].'"';
    }
}	

$s = 0;
$linkID = mysql_connect($host, $db_user, $db_pass) or die("Could 2not connect to host.");
mySQL_select_db($db_name);
echo '<span style="float:right"><a href="../screen.php"> ITS </a></span>';
$tb = '<table><tr><td>Chapter</td><td>Term</td><td>Year</td></tr><tr>';
foreach ($array as $a) {
    $query = 'SELECT DISTINCT ' . $a . ' FROM ' . $table . ' ORDER BY ' . $a.'+0 ASC';
    //die($query);
    $res = mysql_query($query, $linkID) or die("Data 2not found.");
    $row = mysql_fetch_assoc($res);
    
    $str = '<select  name="' . $a . '" class="ITS_select" id="select_user" onchange="javascript:this.submit()">'
		  .'<option class="highlighted" value="ALL" selected="selected">ALL</option>';
    
    while ($row = mysql_fetch_assoc($res)) {
        // echo $_POST[$a]==$row[$a].'<br>';
        if (trim($_POST[$a])==trim($row[$a])) {
			 $sel = 'selected="selected"';
			 //die('xx');
		} else {
			 $sel = '';
		}	 
        $str .= '<option value="' . $row[$a] . '" ' . $sel . '>' . $row[$a] . '</option>';
    }
    $str .= '</select>';
    $tb .= '<td>'.$str.'</td>';
}
$tb .= '</tr></table>';
/*
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <title>Search SPF Database</title>
        <link rel="stylesheet" href="css/ITS_search.css" type="text/css" media="screen">    
    </head>
    <body>
<?php echo $tb;?>		
    </body>
</html>
*/
?>

