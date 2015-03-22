<?php
$LAST_UPDATE = 'Jul-20-2013';
//=====================================================================//               
//Author(s): Gregory Krudysz, Khyati Shrivastava
//Last Revision: Gregory Krudysz, Jul-20-2013
//=====================================================================//
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_concepts.php");
require_once("classes/ITS_footer.php");
require_once("classes/ITS_menu.php");
require_once("classes/ITS_message.php");
require_once("classes/ITS_search.php");
require_once("classes/ITS_survey.php");
require_once("classes/ITS_timer.php");
require_once("classes/ITS_resource.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();

if ($status == 'admin' OR $status == 'instructor') {
	
	global $term, $tset;
	
    // connect to database
    $mdb2 =& MDB2::connect($db_dsn);
    if (PEAR::isError($mdb2)) {
        throw new Question_Control_Exception($mdb2->getMessage());
    }
    // DEBUG:    echo '<p>GET: '.$_GET['qNum'].'  POST: '.$_POST['qNum'];
    //--- determine question number ---//

    if (isset($_GET['qNum'])) {
        $qid  = $_GET['qNum'];
        $from = 'if';
    } elseif (isset($_POST['qNum'])) {
        $qid  = $_POST['qNum'];
        $from = 'if';
    } elseif (isset($_SESSION['qNum_current'])) {
        $qid = $_SESSION['qNum_current'];
    } else {
        $qid = 2;
        $from = 'else';
    }
    //------- CHAPTER -------------//
    $ch_max = 13;

    if (isset($_GET['ch'])) {
        $ch = $_GET['ch'];
    } else {
        $ch = 0;
    }
    $chapter = 'Chapter #<select class="ITS_select" name="ch" id="select_chapter" onchange="javascript:this.submit()">';
    for ($c = 0; $c <= ($ch_max + 1); $c++) {
        if ($ch == $c) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        if ($c == 0) {
            $hc           = 'ANY';
            $class_option = 'highlight';
        } elseif ($c == ($ch_max + 1)) {
            $hc           = 'ALL';
            $class_option = 'highlight';
        } else {
            $hc           = $c;
            $class_option = '';
        }
        $chapter .= '<option class="' . $class_option . '" value="' . $c . '" ' . $sel . '>' . $hc . '</option>';
    }
    $chapter .= '</select>';
    
    //------- TYPE ---------------//
    $Qtype_arr = array(
        'ALL',
        'Multiple Choice',
        'Matching',
        'Calculated',
        'Short Answer',
        'Paragraph'
    );
    $Qtype_db  = array(
        '',
        'MC',
        'M',
        'C',
        'S',
        'P'
    );
    if (isset($_GET['type'])) {
        $qt = $_GET['type'];
    } else {
        $qt = 0;
    }
    $type = 'Type <select class="ITS_select" name="type" id="select_type" onchange="javascript:this.submit()">';
    for ($t = 0; $t < count($Qtype_arr); $t++) {
        if ($qt == $t) {
            $tsel = 'selected="selected"';
        } else {
            $tsel = '';
        }
        $type .= '<option value="' . $t . '" ' . $tsel . '>' . $Qtype_arr[$t] . '</option>';
    }
    $type .= '</select>';
    
    $tag_chk = 'Tagging<input id="tag_check" type="checkbox">';
    // update SESSION
    $_SESSION['qNum_current'] = $qid;
    $form                     = $tag_chk.$chapter . '&nbsp;&nbsp;' . $type;
    //--------------------------------------//
    /*
    // QUERY
    $ALL = $ch_max+1;
    switch ($ch) {
    case 0:
    $query_chapter = 'AND category NOT IN (';
    for ($n=1;$n<=$ch_max;$n++) {
    if ($n < 10) { $nn = '0'.$n; }
    else         { $nn = $n;     }
    $query_chapter .= '"PreLab'.$nn.'","Lab'.$nn.'","Chapter'.$nn.'"';
    if ($n<$ch_max) { $sep = ','; }
    else                         { $sep = ')'; }
    $query_chapter .= $sep;
    }
    break;
    case $ALL: $query_chapter = ''; break;
    default:
    if ($ch < 10) { $chs = '0'.$ch; }
    else          { $chs = $ch;  }
    $query_chapter = 'AND category IN ("PreLab'.$chs.'","Lab'.$ch.'","Chapter'.$ch.'")';
    }
    switch ($qt) {
    case 0:  $query_type = 'qtype IN ("MC","M","C","S","P") '; break;
    default: $query_type = 'qtype = "'.$Qtype_db[$qt].'" ';
    }
    $qindex = 0;
    // look for LIST of question
    $query = 'SELECT id,title,image,category,answers FROM questions WHERE '.$query_type.$query_chapter;
    //echo $query; die();
    
    $res =& $mdb2->query($query);
    if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
    $qs = $res->fetchAll();
    
    if (empty($qs[$qindex][1])) { $title = '';              }
    else                                                 { $title = $qs[$qindex][1]; }
    if (empty($qs[$qindex][2])) { $image = '';              }
    else                                                 { $image = $qs[$qindex][2]; }
    if (empty($qs[$qindex][3])) { $category = '';              }
    else                                                 { $category = $qs[$qindex][3]; }
    if (empty($qs[$qindex][4])) { $answers  = '';              }
    else                                                 { $answers  = $qs[$qindex][4]; }
    if (empty($qs[$qindex][5])) { $tags = ''; }
    else {
    $query = 'SELECT name FROM tags WHERE id IN ('.$qs[$qindex][5].')';  //echo $query;
    $res =& $mdb2->query($query);
    if (PEAR::isError($res)) {throw new Question_Control_Exception($res->getMessage());}
    $tagNames  = $res->fetchCol();
    $tags = implode(',',$tagNames);
    }
    
    $Nqs = count($qs);
    /// QUERY SEARCH
    if ($Nqs){
    $qid = $qs[$qindex][0];
    $tagList = '';
    if (!empty($tags)) {
    for ($i=0; $i < count($tagNames); $i++) {
    //$tagList .= '<input type="button" class="logout" value="'.$tagNames[$i].'">';
    $tagList .= '<span class="ITS_tag">'.$tagNames[$i].'</span>';
    }
    }
    
    //$tb = new ITS_table('qs',round(sqrt($Nqs)),round(sqrt($Nqs)),$qs,array(),'ITS_ANSWER');
    //echo $tb->str;
    //echo implode(', ',$qs).'<p>';
    //--------------------------------------//
    }
    else { $qid = ''; $meta = '<p><b>- nothing found -</b>'; }
    */

    $Nqs   = 'z';
    //-- search box --//
    $s     = new ITS_search();
    $sb    = $s->renderBox('questions', $qid);            
    $sbr   = $s->renderResultsBox();
    //--------------------------------------//
    $Q = new ITS_question(1, $db_name, $tb_name);
    $Q->load_DATA_from_DB($qid);
    
    $T = new ITS_statistics(1,$term,$status);

    //echo $Q->render_QUESTION_check()."<p>";
    $Q->get_ANSWERS_data_from_DB();
    //echo $Q->render_ANSWERS('a',0);
    $meta = $Q->render_data();
    //$mdb2->disconnect();
    //--------------------------------------//
    switch ($status) {
        case 'admin':
            $adminNav = $Q->render_Admin_Nav(0,0, 'ITS_button');
            break;
        default:
            $adminNav = '';
            break;
    }
    //--- NAVIGATION ------------------------------// 
    $current = basename(__FILE__, '.php');
    $ITS_nav = new ITS_navigation($status);
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//
    //'<div class="ITS_navigate">'
    $nav2    = '<input id="previousQuestion" class="ITS_navigate_button" type="button" onclick="ITS_QCONTROL(\'PREV\',\'ITS_question_container\')"  name="question_nav" value="<<" qid="' . $qid . '">' . '<input type="text" class="ITS_navigate" onkeypress=ITS_QCONTROL(\'TEXT\',\'ITS_question_container\') name="qNum" value="' . $qid . '" id="ITS_QCONTROL_TEXT" Q_num="' . $qid . '">' . '<input id="nextQuestion" class="ITS_navigate_button" type="button" onclick="ITS_QCONTROL(\'NEXT\',\'ITS_question_container\')" name="question_nav" value="&gt;&gt;">';
} else {
    //* redirect to start page *//
    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <script src="js/ITS_AJAX.js"></script>
        <script src="js/ITS_QControl.js"></script>
        <title>Questions Database</title>
        <link rel="stylesheet" href="css/ITS_questionCreate.css" type="text/css" media="screen">    
        <link rel="stylesheet" href="css/ITS_search.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_Solution_warmup.css" type="text/css">
        <link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">  
        <!-- <script type="text/javascript" src="MathJax/MathJax.js"></script> -->

        <!-- QTI IMPORTER start ----------------------------------->
        <link href="css/ITS_QTI.css"   rel="stylesheet" type="text/css" />
        <link href="css/swfupload.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="swfupload/swfupload.js"></script>
        <script type="text/javascript" src="plugins/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
        <link href="plugins/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="plugins/uploadify/swfobject.js"></script>
        <!-- CodeMirror ----------------------------------->
        <script src="plugins/CodeMirror-2.0/lib/codemirror.js"></script>
        <link rel="stylesheet" href="plugins/CodeMirror-2.0/lib/codemirror.css">
        <script src="plugins/CodeMirror-2.0/mode/javascript/javascript.js"></script>
        <link rel="stylesheet" href="plugins/CodeMirror-2.0/mode/javascript/javascript.css">
        <!------------------------------------------------->  
        <?php
include INCLUDE_DIR.'stylesheet.php';
include 'js/ITS_tag_jquery.php';
include 'js/ITS_search_jquery.php';
include 'js/ITS_Question_jquery.php';
include INCLUDE_DIR.'include_fancybox.php';
include(INCLUDE_DIR . 'include_mathjax.php');
?>
	<link rel="stylesheet" type="text/css" href="js/jquery.tipsy/src/stylesheets/tipsy.css" />	
       <script type="text/javascript">
            $("#exportQuestion").live('click', function(event) {
            var qid = $(this).attr("qid");
            //$.post("QTI.php", 
            $.post('QTI.php',
            { "QTI_type": 1, "qid": qid }, function(url){
				str = 'ITS-GT'+url.trim();
				//alert(str);
				//str = 'ITS-GT/'+url;
				var host = "<?php
echo $host;
?>";
				//alert(host);
				var data = '<a href="http://'+host+'/'+str+'">QTI &mdash; Q.'+qid+'</a>';
                $("#QTI_output").html(data);
                $("#QTI_output").css('display','block');
                /*
                if(url.match(/.zip$/)){
                    var host = "<?php
echo $host;
?>";
                    url='http://'+host+'/'+url;
                    alert(url);
                    //window.open(url,"Download","width=350,height=250");
                }
                else
                    alert('File was not Found on the server, please retry. Thank you.\n Error Message:'+ url);
                */         
            });
         });        
        
         /* Export Many code  */
          $("#exportManyQuestion").live('click', function(event) {
          $("#exportManyQuestionContainer").css("display","inline");
    });      
         /* Export many code ends */    
        </script>
<script type="text/javascript">
$(document).ready(function() {
$(".various").fancybox({
		  type: 'inline',
		  closeClick: true,
		  padding: 5,
          helpers: {
		  overlay : {
		closeClick : true,
		speedOut   : 300,
		showEarly  : false,
		css        : { 'background' : 'rgba(155, 155, 155, 0.5)'}
	},			  
              title : {
                  type : 'inside'
              }
          }
      });    
});
</script>
    </head>
    <body>
		<input type="hidden" name="tex_path" id="tex_path" value="<?php echo $tex_path;?>" />
        <!---===========================================--->
        <div id="framecontent">
            <!---************* NAVIGATION ******************--->
            <?php
echo $nav;
?>
           <!---******************************************--->
            <div class="innertube">
                <!---******************************************--->
                <form id="question" name="question" action="Question.php" method="get">
                <?php
//echo '<table><tr><td width="40%">'.$form . $nav2 . ' &nbsp;Available: <b>' . $Nqs . '</tb><td>' . $sb . '</td></tr></table><br>'.$sbr.'</b>';
echo $form . $nav2 . ' &nbsp;Available: <b>' . $Nqs . '</b>';

?>
               <noscript><input type="submit" value="Submit"></noscript>
                </form>
            </div>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent">
            <div id="ITS_question_container">
<?php
// RENDER RESOURCE
$source = 'concept';
echo 'Source: '.$source.'<br>';
                    
$Robj = new ITS_resource(1,'Fall_2013','convolution');
$edit = TRUE;
$Rstr = $Robj->getEq($edit);

echo $Rstr;
echo '<hr>';

//Tags + metaData pull-down menu
echo $meta . '<br>' . $adminNav . '</div>';

//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE,'');
echo '<p>'.$ftr->main().'</p>';
//-----------------------------------------------------------//
?>
           </div>
            <!----------------------------------------------------------->
    </body>
</html>
