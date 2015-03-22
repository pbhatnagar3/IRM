<?php
/*  ITS_book - script for AJAX ITS_book class

Author(s): Greg Krudysz
Date: Jun-4-2012        
---------------------------------------------------------------------*/
require_once("../config.php");
require_once("../FILES/PEAR/MDB2.php");
require_once("../classes/ITS_table.php");
require_once("../classes/ITS_configure.php");
require_once("../classes/ITS_question.php");
require_once("../classes/ITS_statistics.php");
require_once("../classes/ITS_screen.php");

require_once("../classes/ITS_tag.php");
//require_once("../classes/ITS_book.php");
//require_once("../classes/ITS_logs.php");

session_start();
//$id     = $_SESSION['user']->id();
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
	  case 'addTAG':
	  //-------------------------------------------//	  
		  $data = preg_split('[~]',$Data);
		  $t    = new ITS_tag('tags');
		  //$logs	= new ITS_log($id);
		  $tag  = $t->addToQues($data[0],$data[1],$data[2],$data[3]);
		  //echo $logs->addtolog($data[2], 'tags', 'name', $data[1], 'Added');
		  $str  = $tag;
		  break;
      //-------------------------------------------//
	  case 'deleteTAG':
	  //-------------------------------------------//			  
		  $data = preg_split('[~]',$Data);
		  $t    = new ITS_tag('tags');
		  //$logs2	= new ITS_log($id)
		  $tag  = $t->deleteFromQues($data[0],$data[1],$data[2],$data[3]);
		  // echo $logs2->addtolog($data[2], 'tags', 'name', $data[1], 'Removed');
		  $str  = $tag;
		  break;		  
    //-------------------------------------------//
	  case 'submit':
	  //-------------------------------------------//
		  $data = preg_split('[~]',$Data);
		  $t    = new ITS_tag('tags');
		  
		  $Ques_tag_arr  = $t->getByResource($data[1],$data[2]);
		  //var_dump($Ques_tag_arr);		  die('stop');
		  $Keyw_tag_list = '';
          $Keyw_tag_arr  = $t->query($data[0],$Ques_tag_arr);     
          if (empty($Keyw_tag_arr[0])) { 
			  $Keyw_tag_list = $t->add($data[0]);
		  }

		  //die($list);
		  $str = $Keyw_tag_list;
		  break;		  
	  //-------------------------------------------//
}
//-----------------------------------------------//
$mdb2->disconnect();
echo $style.$str;

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
		echo $mimetex_path.$row["content"].'<img class="ITS_EQUATION" src="'.$mimetex_path.$row["content"].'"/>';
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

$x = new ITS_book('dspfirst',$ch,$meta,$mimetex_path);
$o = $x->main();
echo $o.'<p>';
*/
//-----------------------------------------------//
?>
