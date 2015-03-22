<?php
/*
ALTER TABLE tags MODIFY COLUMN id INT AUTO_INCREMENT
ALTER TABLE tags MODIFY COLUMN name VARCHAR(64) UNIQUE KEY
ALTER TABLE index_1 CHANGE oldname newname varchar (10) ;
*/
$LAST_UPDATE = 'May-6-2012';
//--- begin timer -----//
$mtime     = explode(" ",microtime());
$starttime = $mtime[1] + $mtime[0];
//---------------------//

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   		   // or IE will pull from cache 100% of time (which is really bad) 
header("Cache-Control: no-cache, must-revalidate"); 		   // Must do cache-control headers 
header("Pragma: no-cache");
	
require_once ("../../config.php");
global $db_dsn, $db_name, $db_table_users, $db_table_user_state;
	 
$dsn = preg_split("/[\/:@()]+/",$db_dsn);
$db_user = $dsn[1];
$db_pass = $dsn[2];
$host    = $dsn[4];
$db_name = $dsn[6];
//------------------------------------------//
	
MySQL_connect($host,$db_user,$db_pass);
MySQL_select_db($db_name);

//$res_tb = 'questions';				//SPF
$res_tb = 'SPF';
$tag_tb = 'images';					//tags
$res_fd = 'id,statement,term,year,solutions';		//id, keywords
$tb 	= '<table>';

/*----------------------------------------------*/
// Select the 'id' and 'name' from tags table
$query = 'SELECT id,name,dir FROM '.$tag_tb;
$res   = MySQL_query($query);
$tag_arr = array();		
while ($row = MySQL_fetch_array($res)) {
	$tag_arr[] = $row;
}
//echo '<pre>'.print_r($tag_arr).'</pre>';
// CONCEPTS
// Select the 'id' and 'keywords' from SPF table
$cquery = 'SELECT '.$res_fd.' FROM '.$res_tb;
$cres   = MySQL_query($cquery);
while ($qrow = MySQL_fetch_array($cres)) {
	$fname = $qrow['statement'];
	$term = $qrow['term'];
    switch ($term){
		case 'Spring':
		case 'Summer':
			$t = $term[0].$term[1];
			break;
		case 'Fall':
		case 'Winter':
			$t = $term[0];
			break;
	}
	$year = $qrow['year'];
	$id = $qrow['id'];
	$solutions = $qrow['solutions'];
	$fname = preg_replace('/.pdf/','.png', $fname);
	$f = 'SPFIRST/ALLpngs/'.strtolower($t).'_'.$year[2].$year[3].'/'.$fname;
	$fsname = $solutions;
	//$fs = 'SPFIRST/solutions/'.strtolower($t).'_'.$year[2].$year[3].'/'.$fsname;
	//echo $f.'<br>';
	
	foreach($tag_arr as $t){
		/*
		if(isset($sol_arr)){
			//echo $id.'not empty<br>';die();
			foreach($sol_arr as $s){
				//echo $s.'<br>';
				$fs = 'SPFIRST/solutions/'.strtolower($theTerm).'_'.$year[2].$year[3].'/'.$s;
				//echo $fs.'==?=='.$t[2].'/'.$t[1].'<br>';
				if(!strcasecmp($fs,$t[2].'/'.$t[1])){
					$mysql = 'INSERT IGNORE INTO spf_'.$tag_tb.' (spf_id,'.$tag_tb.'_id) VALUES ('.$id.','.$t[0].')';
					echo $mysql.'       '.$fs.'==?=='.$t[2].'/'.$t[1].'<br>';
					//mysql_query($mysql) or die(mysql_error().'Keyword id: '.$question[0].' tag id: '.$t[0].'');
				}
			}
		}
		*/
		//
		echo $t[2].'/'.$t[1].' ==?=='.$f.'<br>';
		if(!strcasecmp($f,$t[2].'/'.$t[1])){
			$mysql = 'INSERT IGNORE INTO spf_'.$tag_tb.' (spf_id,'.$tag_tb.'_id) VALUES ('.$id.','.$t[0].')';
			//
			echo $mysql.'<br>';
			//mysql_query($mysql) or die(mysql_error().'Keyword id: '.$question[0].' tag id: '.$t[0].'');
		}
	}
	/*
	$question  = $qrow;                                      //Question array question[0] = 'id'   question[1] = 'question'     question[2] = 'tag_id'
	//echo '<pre>'.print_r($question).'</pre>';
	// Pre-process
	$pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
    $str         = preg_replace($pattern,'', $qrow[1]);
	//$str         = preg_replace('/<PRE>/','', $str);
	$keywords = explode(' ', $str);							//An array of all the keywords
	//echo '<pre>'.print_r($keywords).'</pre>';
	foreach ($keywords as $k){
		$k = preg_replace('/[(),.?<>="*]/','',$k);
		$k = trim($k);
		$k = substr($k, 0, -3); //individual keyword (in this case image in spf table)
		//echo $k.'<br>';
		//var_dump($tags);
		if ($k){
		foreach ($tag_arr as $t){
		//echo $t[0].' '.$t[1].'<br>';
		//echo substr($t[1], 0, -3).'<br>';
			 if (!strcasecmp($k,substr($t[1], 0, -4))) { 
				 //$tb .= '<tr><td>'.$k.'</td><td>'.$concept[0].'</td><td>'.$t[1].'</td><td>'.$t[0].'</td></tr>';
				 //echo 'INSERT IGNORE INTO '.$res_tb.'_'.$tag_tb.' ('.$res_tb.'_id,'.$tag_tb.'_id) VALUES ('.$concept[0].','.$t[0].')<br>';
				 $mysql = 'INSERT IGNORE INTO spf_'.$tag_tb.' (spf_id,'.$tag_tb.'_id) VALUES ('.$question[0].','.$t[0].')';
				 echo $mysql.'<br>';
				 mysql_query($mysql) or die(mysql_error().'Keyword id: '.$question[0].' tag id: '.$t[0].'');
		     }
		}
	}
	}
	*/
	//var_dump($keywords);
	//die('done');
}

/*-----------------------------------------*/
/*
echo '<pre>';print_r($tags);echo '</pre>';die();
*/
$str.= $tb.'</table>';
$str = 'done';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>DATABASE</title>
	<!---->
	<link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/login.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/print/ITS_print.css" media="print">
	<link rel="stylesheet" href="tagging/ITS_tagging.css" type="text/css" media="screen">
	<link rel="stylesheet" href="rating/ITS_rating.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_score.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_BOOK.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_test.css" type="text/css" media="screen">
	
	<link type="text/css" href="jquery-ui-1.8.4.custom/css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
	<!--[if IE 6]>
	<link rel="stylesheet" href="css/IE6/ITS.css" type="text/css" media="screen">
	<![endif]-->
	<script src="js/ITS_admin.js"></script>
	<script src="js/AJAX.js"></script>
	<script src="js/ITS_AJAX.js"></script>
	<script src="js/ITS_screen.js"></script>
	<script src="js/ITS_QControl.js"></script>
	<script src="js/ITS_book.js"></script>
	<script src="tagging/ITS_tagging.js"></script>
	<script src="rating/forms/star_rating.js"></script>
	<script type="text/javascript">
	$(function() {
      $(".ITS_select").change(function() { document.profile.submit(); });
			$("#select_class").buttonset();
  });
	/*-------------------------------------------------------------------------*/
  $(document).ready(function() { 
     //$("#scoreContainer").click(function(){$("#scoreContainerContent").slideToggle("slow");});
  });
  </script>
  <style>
	  #select_class { margin-top: 2em; }
		.ui-widget-header   { background: #aaa; border: 2px solid #666; }
		.ui-dialog-titlebar { background: #aaa; border: 2px solid #666; }
		.ui-dialog-content  { text-align: left; color: #666; padding: 0.5em; }
		.ui-button-text { color: #00a; }
	</style>	
<script type="text/javascript">
</script>
</head>
<body>
<?php
echo $str;
//--- TIMER -------------------------------------------------//
$mtime     = explode(" ",microtime());
$endtime   = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status,$LAST_UPDATE,$totaltime);
echo $ftr->main();
//-----------------------------------------------------------//

/*
 * 
 while ($row = MySQL_fetch_array($res)) {
								//echo '<pre>';var_dump($row);echo '</pre>';die();
                                $fname = $row['statement'];
                                $solutions = $row['solutions'];
                                $term = $row['term'];
                                switch ($term){
									case 'Spring':
									case 'Summer':
									$t = $term[0].$term[1];
									break;
									case 'Fall':
									case 'Winter':
									$t = $term[0];
									break;
								}
                                $year = $row['year'];
                                $fname = preg_replace('/.pdf/','.png', $fname);
								$f = $path1.strtolower($t).'_'.$year[2].$year[3].'/'.$fname;
//echo $path;die();
                                $sol_arr = explode(',', $solutions);

                                $sol_list = '';
                                foreach ($sol_arr as $s) {
                                    if (empty($s)) {
                                        $sol_list .= '';
                                    } else {
										$pathS = $path2.strtolower($t).'_'.$year[2].$year[3].'/'.$s;
                                        //$sol_list .= '<div class="file"><a href="'.$path.'" target="_blank"><img alt="'.$path.'" src="admin/icons/png_icon.png" /></a></div>';
                                        $sol_list .= '<a id="fbimage" href="'.$pathS.'" title="'.$pathS.'"><img class="thumb" src="'.$pathS.'" alt="'.$s.'"></a><br>';
                                    }
                                }	
                                //echo '<pre>';var_dump($sol_list);echo '</pre>';die();
                                //$sol  = '<div class="file"><a href="'.$solutions.'" target="_blank"><img alt="'.$solutions.'" src="'.$solutions.'" /></a></div>';

                                $sol = '<div class="file">' . $sol_list . '</div>';
                                //$file = '<div class="file"><a href="' . $f . '" target="_blank"><img alt="' . $f . '" src="admin/icons/png_icon.png" /></a></div>';
                                $file = '<a id="fbimage" href="'.$f.'" title="'.$f.'"><img class="thumb" src="'.$f.'" alt="'.$fname.'"></a><br>';
                                //echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['score']}</td></tr>";   
                                
                                $keywords = explode(' ',$row["keywords"]);
                                $key_list = '';
                                foreach ($keywords as $kw){
									$key_list .= '<span class="keywords">'.$kw.'</span>';
								}
                                $tb .= "<tr>" .
                                        '<td class="search_title">' . $row["title"] .'<br><span class="keywords">'.$key_list. '</span></td>' .
                                        "<td>" . $file . "</td>" .
                                        '<td class="search_solution">' . $sol_list . '</td>' .
                                        "<td>{$row['chapter']}</td>" .
                                        "<td>{$row['term']}</td>" .
                                        "<td>{$row['year']}</td>" .
                                        "</tr>";
                            } //while
                            $tb .= "</table>";
                            echo $tb;*/
?>
</body>
</html>
