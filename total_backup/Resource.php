<?php
$LAST_UPDATE = 'Aug-09-2013';
/*=====================================================================//               
//Author(s): Gregory Krudysz

SPFindex_tags
questions_tags
** dspfirst_tags **
//=====================================================================*/
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_search.php");
include_once("classes/ITS_timer.php");
require_once("classes/ITS_survey.php");
require_once("classes/ITS_menu.php");
require_once("classes/ITS_message.php");
require_once("classes/ITS_resource.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();

if ($status == 'admin' OR $status == 'instructor') {
    // connect to database
    $mdb2 =& MDB2::connect($db_dsn);
    if (PEAR::isError($mdb2)) {
        throw new Question_Control_Exception($mdb2->getMessage());
    }
    // DEBUG:    echo '<p>GET: '.$_GET['qNum'].'  POST: '.$_POST['qNum'];
    //--- determine question number ---//

	    if (isset($_GET['rid'])) {
        $qid  = $_GET['rid'];
        echo 'text | image | example';
        //die($rid);
       }
	
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
    
    // update SESSION
    $_SESSION['qNum_current'] = $qid;
    $form                     = $chapter . '&nbsp;&nbsp;' . $type;
    //--------------------------------------//
    $Nqs   = 'z';
    //-- search box --//
    $s     = new ITS_search();
    $sb    = $s->renderBox('questions', $qid);            
    $sbr   = $s->renderResultsBox();
    //--------------------------------------//

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
		<META HTTP-EQUIV="Expires" 	    CONTENT="Tue, 01 Jan 1980 1:00:00 GMT">
        <META HTTP-EQUIV="Pragma"       CONTENT="no-cache">
        <meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=utf-8">
        <script src="js/ITS_AJAX.js"></script>
        <script src="js/ITS_QControl.js"></script>
        <title>Questions Database</title>
        <link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_QTI.css" type="text/css" media="screen">    
        <link rel="stylesheet" href="css/ITS_questionCreate.css" type="text/css" media="screen">    
        <link rel="stylesheet" href="css/ITS_index4.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_tag.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_search.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_Solution_warmup.css" type="text/css">
        <link rel="stylesheet" href="css/ITS_QTI.css" type="text/css">        
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>	  
        <?php
include INCLUDE_DIR.'stylesheet.php';
include 'js/ITS_Question_jquery.php';
include 'js/ITS_search_jquery.php';
?>
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
            });
         });           
        </script>
        <script type="text/javascript" src="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
    /* This is basic - uses default settings */
    $("a#single_image").fancybox();    
    /* Using custom settings */    
    $("a#inline").fancybox({
        'hideOnContentClick': true
    });
    /* Apply fancybox to multiple items */    
    $("a.group").fancybox({
        'transitionIn'    :    'elastic',
        'transitionOut'    :    'elastic',
        'speedIn'        :    600, 
        'speedOut'        :    200, 
        'overlayShow'    :    false
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
//$Q2 = new ITS_question2();
//echo $Q2->render_list();

// RENDER RESOURCE

$source = 'concept';
echo 'Source: '.$source.'<br>';
                    
$Robj = new ITS_resource(1,'Fall_2013','convolution');
$edit = TRUE;
$Rstr = $Robj->getEq($edit);


echo $Rstr;
echo '<hr>';
if (!empty($qid)) {
    echo $sb.'<br>'.$sbr;
}
//Resources pull-down menu
echo '<br><div id="resourceContainer"><span>&raquo;&nbsp;Resources</span></div>
                <div id="results"></div>';
//Tags + metaData pull-down menu
echo $meta . '<br>' . $adminNav . '</div>';
?>

<?php
//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE, '');
echo $ftr->main();
//-----------------------------------------------------------//
?>
           </div>
            <!----------------------------------------------------------->
    </body>
</html>
