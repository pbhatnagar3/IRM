<?php
$LAST_UPDATE = 'Jul-19-2013';
/*=====================================================================// 					
    Author(s): Gregory Krudysz
//=====================================================================*/
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_search.php");
require_once("classes/ITS_timer.php");
require_once("classes/ITS_resource.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();
// connect to database
$mdb2 = & MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)) {
    throw new Question_Control_Exception($mdb2->getMessage());
}
// DEBUG:	echo '<p>GET: '.$_GET['qNum'].'  POST: '.$_POST['qNum'];
//--- determine question number ---//
if (isset($_GET['qNum'])) {
    $qid = $_GET['qNum'];
    $from = 'if';
} elseif (isset($_POST['qNum'])) {
    $qid = $_POST['qNum'];
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
        $hc = 'ANY';
        $class_option = 'highlight';
    } elseif ($c == ($ch_max + 1)) {
        $hc = 'ALL';
        $class_option = 'highlight';
    } else {
        $hc = $c;
        $class_option = '';
    }
    $chapter .= '<option class="' . $class_option . '" value="' . $c . '" ' . $sel . '>' . $hc . '</option>';
}
$chapter .= '</select>';
//------- TYPE ---------------//
$Qtype_arr = array('ALL', 'Multiple Choice', 'Matching', 'Calculated', 'Short Answer', 'Paragraph');
$Qtype_db  = array('','MC','M','C','S','P');
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

// update SESSION
$_SESSION['qNum_current'] = $qid;
$form = $chapter . '&nbsp;&nbsp;' . $type;
//--------------------------------------//

if (isset($_GET['tid'])) {
  $tid = $_GET['tid'];
  $robj = new ITS_resource($id,$tid);
  //$rList = $robj->renderContainer();
  $rList = $robj->getQuestions($tid);
} else {
	$qid = 5;
	$tobj = new ITS_tag('tags');
    $tList = $tobj->query2('questions');
}
//--------------------------------------//
$nav = '<input id="previousQuestion" class="ITS_navigate_button" type="button" onclick="ITS_QCONTROL(\'PREV\',\'ITS_question_container\')"  name="prev_question" value="<<" qid="' . $qid . '">' .
       '<input type="text" class="ITS_navigate" onkeypress=ITS_QCONTROL(\'TEXT\',\'ITS_question_container\') name="qNum" value="' . $qid . '" id="ITS_QCONTROL_TEXT" Q_num="' . $qid . '">' .
       '<input id="nextQuestion" class="ITS_navigate_button" type="button" onclick="ITS_QCONTROL(\'NEXT\',\'ITS_question_container\')" name="next_question" value="&gt;&gt;">';

//--- NAVIGATION ------------------------------// 
	$current = basename(__FILE__,'.php');
	$ITS_nav = new ITS_navigation($status);
	$nav     = $ITS_nav->render($current);    
//---------------------------------------------//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
	    <link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">   
        <link rel="stylesheet" href="css/ITS_QTI.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_questionCreate.css" type="text/css" media="screen">	
        <link rel="stylesheet" href="css/ITS_resource.css" type="text/css" media="screen">     
        <link rel="stylesheet" href="css/ITS_search.css" type="text/css" media="screen">

		<script type="text/javascript" src="js/jquery.tipsy/src/javascripts/jquery.tipsy.js"></script>
		<link rel="stylesheet" type="text/css" href="js/jquery.tipsy/src/stylesheets/tipsy.css" />
		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <script src="js/ITS_AJAX.js"></script>
        <script src="js/ITS_QControl.js"></script>
        <title>Tags</title>
<?php 
include INCLUDE_DIR.'include_fancybox.php';
include(INCLUDE_DIR.'include_mathjax.php');
include INCLUDE_DIR.'stylesheet.php';
include 'js/ITS_tag_jquery.php';
?>
<script type="text/javascript">
$(document).ready(function() {
	MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
	/* This is basic - uses default settings */
	$("a#fbimage").fancybox();	
	$( "input[name=selectResource]" ).live('click', function(event) {
	        var field = $(this).val();
            $.get("ajax/ITS_resource.php", {
                ajax_args: "test",
                ajax_data: '402~'+field
            }, function(data) {
                $('#resourceContainer').html(data);
            });
});
});
</script>  
    </head>
    <body>
        <!---===========================================--->
        <div id="framecontent">
<!---************* NAVIGATION *****************--->
<?php echo $nav;?>
<!---******************************************--->
            <div class="innertube">
                <!---******************************************--->
                <form id="question" name="question" action="Question.php" method="get">
                <?php echo $form . ' &nbsp;</b>'; ?>
                </form>
            </div>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent">
            <div id="ITS_question_container">
<?php
echo $rList;
echo $tList;
//echo '<div id="metaContainer" class="ITS_meta">'.$tList.'</div><p>';
echo '</div><br>';

//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE,'');
echo '<p>'.$ftr->main().'</p>';
//-----------------------------------------------------------//
?>
           </div>
            <!----------------------------------------------------------->
    </body>
</html>
