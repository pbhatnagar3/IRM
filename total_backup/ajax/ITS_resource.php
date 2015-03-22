<?php
/*  ITS_resource - script for AJAX ITS_resource class

Author(s): Greg Krudysz
Date: Nov-21-2012        
---------------------------------------------------------------------*/
require_once("../FILES/PEAR/MDB2.php");
require_once("../config.php");
require_once("../" . INCLUDE_DIR . "include.php");
require_once("../classes/ITS_resource.php");

session_start();

//$id = $_SESSION['user']->id();
//===================================================================//
global $db_dsn, $db_name, $tb_name, $db_table_user_state, $files_path;

//-- Get AJAX arguments
$args = preg_split('[,]',$_GET['ajax_args']);

//-- Get AJAX user data
$Data = rawurldecode($_GET['ajax_data']);
// preprocess before SQL
$Data = str_replace ("'","&#39;",$Data);
//$Data = nl2br($Data);
$action = $args[0];

/*
echo 'action = '.$action.'<p>';
echo 'data   = '.$Data.'<p>';    die();
*/

$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)){throw new Question_Control_Exception($mdb2->getMessage());}
//echo $action;
//-----------------------------------------------//
switch ($action){ 
      //-------------------------------------------//
	  case 'test':
	  //-------------------------------------------//	  
		  $data = preg_split('[~]',$Data);
		  $obj    = new ITS_resource(trim($data[0]));
		  $list  = $obj->getResource($data[1]);
		  $str  = $list;
		  break;  
      //-------------------------------------------//
	  case 'select':
	  //-------------------------------------------//	  
		  $data = preg_split('[~]',$Data);
		  $obj    = new ITS_resource(trim($data[0]));
		  $list  = $obj->setResource($data[0],$data[1]);
		  $str  = $list;
		  break;  
	  //-------------------------------------------//	 
	  case 'resourceDB':
	  //-------------------------------------------//	  
		  $data = preg_split('[~]',$Data);
		  $obj    = new ITS_resource(trim($data[0]));
		  $list  = $obj->saveResource($data[0],$data[1],$data[2],$data[3],$data[4],$data[5]);
		  $str  = $list;
		  break;  
	  //-------------------------------------------//	    
}
//-----------------------------------------------//
$mdb2->disconnect();
//echo 'ECHO'.$style.$str;
echo $str;
//-----------------------------------------------//
/*
$data = preg_split('[,]',$Data);

$tid   = $data[0];
$tname = $data[1];

                $ids   = 'SELECT dspfirst_ids FROM dspfirst_map WHERE tag_id='.$tid;
                $query = 'SELECT meta,content FROM dspfirst WHERE id IN ('.$ids.')';
                //echo $query;
                $res = mysql_query($query);
                if (!$res) {die('Query execution problem in ITS_tag_AJAX: ' . msql_error());}
                
                
    while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    switch ($row['meta']) {
    case 'paragraph':
        echo '<br>'.$row["content"].'</br>';
        break;
        case 'equation':
		echo $tex_path.$row["content"].'<img class="ITS_EQUATION" src="'.$tex_path.$row["content"].'"/>';
        break;
    default:
        echo "default: ".$row['meta'];
        break;
}
}
*/
//-----------------------------------------------//
/*
$ch = 1;
$meta = 'math';

$x = new ITS_book('dspfirst',$ch,$meta,$tex_path);
$o = $x->main();
echo $o.'<p>';
*/
//-----------------------------------------------//
?>
