<?php
$LAST_UPDATE = 'Feb-14-2013';
/*=====================================================================//
Author(s): Gregory Krudysz         
* Revision: Mi Seon Park: sort capability    Dec-01-2012  
//=====================================================================*/
//--- begin timer -----//
$mtime       = microtime();
$mtime       = explode(" ", $mtime);
$mtime       = $mtime[1] + $mtime[0];
$starttime   = $mtime;
//---------------------//
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

include("classes/ITS_timer.php");
include "file2SQL.php";
include "STATS.php";
require_once("classes/ITS_survey.php");
require_once("classes/ITS_menu.php");
require_once("classes/ITS_message.php");
//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     = $_SESSION['user']->id();
$status = $_SESSION['user']->status();
$info =& $_SESSION['user']->info();

if (isset($_GET['c'])) {
    $course = $_GET['c'];
} else {
    $course = 'ece2025';
}
//------------------------------------------// 
if ($status == 'admin' OR $status == 'instructor') {
    global $db_dsn, $db_name, $tb_name, $db_table_users, $db_table_user_state;
    
    $mdb2 =& MDB2::connect($db_dsn);
    if (PEAR::isError($mdb2)) {
        throw new Question_Control_Exception($mdb2->getMessage());
    }
    
    $subnav = '';
    switch ($course) {
        //=======================================//
        case 'ece2025':
            //=======================================//
            //------- CHAPTER -------------//
            $ch_max = 14;
            if (isset($_GET['ch'])) {
                $ch = $_GET['ch'];
            } else {
                $ch = 1;
            }
            
            $chapter = 'Assignment #<select class="ITS_select" name="ch" id="select_chapter" onchange="javascript:this.form.submit()">';
            for ($c = 1; $c <= $ch_max; $c++) {
                if ($ch == $c) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
                $chapter .= '<option value="' . $c . '" ' . $sel . '>' . $c . '</option>';
            }
            $chapter .= '</select>';
            
            //------- DIFFICULTY -------------//
            $query = 'SHOW COLUMNS FROM ' . $tb_question_diff . ' LIKE "difficulty%"';
            //die($query);
            $res =& $mdb2->query($query);
            if (PEAR::isError($res)) {
                throw new Question_Control_Exception($res->getMessage());
            }
            $diff_arr = $res->fetchCol();
            //var_dump($diff_arr);die('da');      
            
            if (isset($_GET['difficulty'])) {
                $diff = $_GET['difficulty'];
            } else {
                $diff = $diff_arr[count($diff_arr)-1];
            }
            
            $difficulty = '<div style="float:right;margin-right:20px">Difficulty: <select class="ITS_select" name="difficulty" id="select_difficulty" onchange="javascript:this.form.submit()"></div>';
            
            for ($da = 0; $da < count($diff_arr); $da++) {
                if ($diff_arr[$da] == $diff) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
                $difficulty .= '<option value="' . $diff_arr[$da] . '" ' . $sel . '>' . $diff_arr[$da] . '</option>';
            }
            $difficulty .= '</select>';
            
            //--- QUESTIONS ------------------------------------------//
            $msg       = '';
            $questions = array();
            
            //--- USERS --- ------------------------------------------//
            $ITSq            = new ITS_query();
            $resource_source = $ITSq->getCategory($ch);
            $subnav          = '<form id="' . $course . '" name="' . $course . '" action="Course.php" method="GET"><input type="hidden" name="c" value="' . $course . '">' . $chapter . ' ' . $difficulty . '<noscript><input type="submit" value="Submit"></noscript></form>';
            break;
        //=======================================//
        case 'warmup':
            //=======================================//
            //------- CHAPTER -------------//
            $ch_max = 14;
            if (isset($_GET['ch'])) {
                $ch = $_GET['ch'];
            } else {
                $ch = 1;
            }
            
            $chapter = 'Assignment #<select class="ITS_select" name="ch" id="select_chapter" onchange="javascript:this.form.submit()">';
            for ($c = 1; $c <= $ch_max; $c++) {
                if ($ch == $c) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
                $chapter .= '<option value="' . $c . '" ' . $sel . '>' . $c . '</option>';
            }
            $chapter .= '</select>';
            //--- QUESTIONS ------------------------------------------//
            $msg       = '';
            $questions = array();
            
            //--- USERS --- ------------------------------------------//
            $resource_source = 'category IN ("Warm-up0' . $ch . '")';
            $subnav          = '<form id="' . $course . '" name="' . $course . '" action="Course.php" method="GET"><input type="hidden" name="c" value="' . $course . '">' . $chapter . '<noscript><input type="submit" value="Submit"></noscript></form>';
            break;
        //=======================================//
        case 'ece3075':
            //=======================================//
            $resource_source = 'category IN ("ECE3075","ECE 3075")';
            break;
        //=======================================//
        case 'spen':
        case 'vip':
        case 'vip2':
            //=======================================//
            $resource_source = 'category LIKE "%' . $course . '%"';
            break;
        //=======================================//
        default:
            $resource_source = 'id=1';
    }
    
    if ($course == 'ece2025') {
        $orderby = 'id';  
        $tr      = new ITS_statistics($id, $course, $status);
        $Estr    = $tr->render_course($ch, $diff, $orderby);
    } else {
        $query = 'SELECT id,title,category FROM ' . $tb_name . ' WHERE ' . $resource_source;
        $res   = $mdb2->query($query);
        $ques  = $res->fetchAll();
        //-- LIST of questions (count($answers)-1)
        $Estr  = '<table class="PROFILE">' . '<tr><th style="width:4%;">No.</th><th style="width:77%;">Question</th><th style="width:14%;">Author</th></tr>';
        for ($qn = 0; $qn <= (count($ques) - 1); $qn++) {
            $qid   = $ques[$qn][0];
            $title = $ques[$qn][1];
            $cat   = $ques[$qn][2];
            $Q     = new ITS_question($qid, $db_name, $tb_name);
            
            $Q->load_DATA_from_DB($qid);
            //echo $qid;
            $QUESTION = $Q->render_QUESTION(); //_check($answers[$qn][4]);
            $Q->get_ANSWERS_data_from_DB();
            $ANSWER = $Q->render_ANSWERS('a', 2);
            //$ANSWER = $Q->render_ANSWERS('a',0);
            
            $Estr .= '<tr class="PROFILE" id="tablePROFILE">' . '<td class="PROFILE" >' . ($qn + 1) . '<br><br><a href="Question.php?qNum=' . $qid . '&sol=1" class="ITS_ADMIN">' . $qid . '</a></td>' . '<td class="PROFILE" >' . $QUESTION . $ANSWER . '</td>' . '<td class="PROFILE" >' . $title . '<hr><p style="color: grey">' . $cat . '</p></td>';
            $Estr .= '</tr>';
        }
        $Estr .= '</table>';
    }
    //echo $Estr;
}

//--- NAVIGATION ------------------------------// 
$current = basename(__FILE__, '.php');
$ITS_nav = new ITS_navigation($status);
$nav     = $ITS_nav->render($current, $course);
//---------------------------------------------//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Course</title>
        <!---->
        <link rel="stylesheet" href="css/ITS.css" 		 type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_course.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/login.css" 	 type="text/css" media="screen">
        <link rel="stylesheet" href="css/admin.css" 	 type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_score.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_BOOK.css" 	 type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_test.css" 	 type="text/css" media="screen">
        <script src="js/ITS_admin.js"></script>
        <script src="js/AJAX.js"></script>
        <script src="js/ITS_AJAX.js"></script>
        <script src="js/ITS_screen.js"></script>
        <script src="js/ITS_QControl.js"></script>
        <script src="js/ITS_book.js"></script>
        <script src="tagging/ITS_tagging.js"></script>
        <script src="rating/forms/star_rating.js"></script>
<?php
include INCLUDE_DIR . 'stylesheet.php';
include(INCLUDE_DIR . 'include_mathjax.php');
include 'js/ITS_course_jquery.php';
?>
    </head>
    <body>
        <div id="framecontent">
            <!---************* NAVIGATION *****************--->
<?php
echo $nav;
?>
            <!---******************************************--->
            <div class="innertube">
<?php
echo $subnav;
?>
            </div>
            <!---******************************************--->
        </div>
        <div id="maincontent">
            <?php
//-----------------------------------------------------------//
echo '<div id="userProfile">' . $Estr . '</div>';
//echo $section.'--'.$sid.'--'.$status.'--'.$ch.'<p>';

/*
$c       = new ITS_course('Fall_2011');
$courses = $c->render_courses();
echo '<center>'.$courses.'</center>';

// DELETE COURSE
if ( isset($_POST['delete_class']) ){
$del = $c->delete();
echo $del;
}
*/
//-----------------------------------------------------------//
//--- begin timer ---//
$mtime     = explode(" ", microtime());
$endtime   = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
//--- FOOTER ------------------------------------------------//
$ftr       = new ITS_footer($status, $LAST_UPDATE, $totaltime);
echo $ftr->main();
//-----------------------------------------------------------//
?>
        </div>
    </body>
</html>
