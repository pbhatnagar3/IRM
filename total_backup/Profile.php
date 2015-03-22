<?php
$LAST_UPDATE = 'Sep-6-2013';
//=====================================================================//               
//Author(s): Gregory Krudysz
//=====================================================================//
//--- begin timer ---//
$mtime       = explode(" ", microtime());
$starttime   = $mtime[1] + $mtime[0];
$Debug       = FALSE;
//-------------------//
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

include("classes/ITS_timer.php");
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
//------------------------------------------// 
//echo '<pre>';var_dump($_POST);echo '</pre>';die();

if ($status == 'admin' OR $status == 'instructor') {
	
	global $term, $tset;
	
    if ($_POST['getGradesSubmit'] == 'Submit') {
        //--- FILE UPLOAD ---------------------*//  
        if ($Debug) {
            if ($_FILES["file"]["error"] > 0) {
                $Debug = "Error: " . $_FILES["file"]["error"] . "<br>";
            } else {
                echo "Upload: " . $_FILES["file"]["name"] . "<br>";
                echo "Type: " . $_FILES["file"]["type"] . "<br>";
                echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                echo "Stored in: " . $_FILES["file"]["tmp_name"];
            }
        }
        $tsquare_file = $_FILES["file"]["tmp_name"];
        $A            = $_POST['assignment'];
        $s            = new ITS_statistics(1,$term, 'admin');
        $grade_link   = $s->getGrades($tsquare_file, $A);
        //-------------------------------------*//
    } else {
        $grade_link = '';
    }
    //------- CLASS -------------//
    switch ($status) {
        case 'instructor':
            $class_arr = array($term,
                'instructor'
            );
            $delButton = '';
            break;
        case 'admin':
            $class_arr = array($term,
                'admin',
                'instructor'
            );
            $delButton = '<div id="deleteButton" uid="' . $id . '" class="dialogButton">Clear my<br>Profile</div>' . '<div id="deleteDialog" title="Delete Account Info?" style="display:none">' . '<B>ALL</B> of your ITS records will be permanently deleted and cannot be recovered.<br>' . '<div class="mysql"><code>mysql>&nbsp;<font class="mysql">DELETE FROM stats_' . $id . '</font></code></div>' . '</div>';
            break;
    }
    
    if (isset($_GET['class'])) {
        $section = $_GET['class'];
    } else {
        $section = $class_arr[0];
    }
    
    //$class = '<div name="class" id="select_class">Class: ';
    $class = 'Class: <select class="ITS_select" name="class" id="select_class" onchange="javascript:this.submit()">';
    for ($cs = 0; $cs < count($class_arr); $cs++) {
        if ($section == $class_arr[$cs]) {
            $sel             = 'selected="selected"';
            $current_section = $class_arr[$cs];
        } else {
            $sel = '';
        }
        
        //$class .= '<input type="checkbox" name="class" id="check'.$cs.'" '.$sel.'/><label for="check'.$cs.'">'.$class_arr[$cs].'</label>';
        $class .= '<option value="' . $class_arr[$cs] . '" ' . $sel . '>' . preg_replace('/_/', ' ', $class_arr[$cs]) . '</option>';
    }
    $class .= '</select>';
    
    //------- USER ---------------//
    if (isset($_GET['sid'])) {
        $uid = $_GET['sid'];
    } else {
        $uid = $id;
    }
    $usertable = 'stats_' . $uid;
    
    $mdb2 =& MDB2::connect($db_dsn);
    $query = 'SELECT id,last_name,first_name,status FROM users WHERE status IN ("' . $section . '") ORDER BY last_name'; // "admin",
    $res =& $mdb2->query($query);
    
    $mdb2->disconnect();
    $user_data = $res->fetchAll();
    $users     = '<select  name="sid" class="ITS_select" id="select_user" onchange="javascript:this.submit()">';
    
    //echo $uid.' == '.$user[0].'<p>';
    if ($uid == 0) {
        $sel          = 'selected="selected"';
        $current_user = 'ALL';
    } else {
        $sel          = '';
        $current_user = 'ALL';
    }
    
    $users .= '<option class="highlighted" value="ALL" ' . $sel . '>ALL</option>';
    
    foreach ($user_data as &$user) {
        if ($uid == $user[0]) {
            $sel          = 'selected="selected"';
            $current_user = $user[3];
        } else {
            $sel = '';
        }
        
        if ($user[3] == 'admin') {
            $cl = 'class="highlighted"';
        } else {
            $cl = '';
        }
        
        $users .= '<option ' . $cl . ' value="' . $user[0] . '" ' . $sel . '>' . $user[1] . ', ' . $user[2] . '</option>';
    }
    $users .= '</select>';
    
    //echo $uid.' -- '.$user[0].' -- '.$current_user.' -- '.strcmp($current_user,'ALL');
    if (strcmp($current_user, 'ALL')) { // indiv user
        //--- CHAPTER ---------------------------------//
        $ch_max = 14;
        if (isset($_GET['ch'])) {
            $ch = $_GET['ch'];
        } else {
            $ch = 1;
        }
        
        $chapter = 'Assignment #<select class="ITS_select" name="ch" id="select_chapter" onchange="javascript:this.submit()">';
        for ($c = 1; $c <= $ch_max; $c++) {
            if ($ch == $c) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $chapter .= '<option value="' . $c . '" ' . $sel . '>' . $c . '</option>';
        }
        $chapter .= '</select>';
        //---------------------------------------------// 
        switch ($status) {
            case 'admin':
                $id_str = ' &nbsp; <tt>id: </tt>' . $uid;
                break;
            default:
                $id_str = '';
        }
        $classInfo = '<a href="Profile.php?class=' . $current_user . '&sid=0">' . preg_replace('/_/', ' ', $current_user) . '</a>';
        $form      = $class . ' &nbsp; ' . $users . ' &nbsp; ' . $chapter . ' &nbsp; ' . $classInfo . $id_str;
        
        $chArr = range(1, $ch_max);
        
        // SCORE
        $score   = new ITS_score($uid, $term, $tset);
        $str     = $score->renderChapterScores($chArr);
        $myScore = '<div id="scoreContainer"><span>&raquo;&nbsp;User Scores</span></div>' . '<div id="scoreContainerContent">' . $str . '</div>';
        $sort    = 'id';
        $tr      = new ITS_statistics($uid, $section, $status);
        $list    = $tr->render_profile2($ch, $sort);
        //die('---dd---');
        //----------------------------//
    } else {
        /*
        switch ($section) {
        case 'mc':
        case 'm':*/
        $chs     = array(
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            11
        );
        $form    = $class . '&nbsp;  Profile:&nbsp;' . $users;
        $myScore = '';
        
        $tr   = new ITS_statistics($uid, $section, $status);
        $list = $tr->render_class_profile($section, $chs, $tset);
        
        /*
        echo '<pre>';
        print_r($list);
        echo '</pre>';*/
    }
    //--- NAVIGATION ------------------------------// 
    $current = basename(__FILE__, '.php');
    $ITS_nav = new ITS_navigation($status);
    $nav     = $ITS_nav->render($current, '');
    //---------------------------------------------//	      
} else {
    // redirect to start page //
    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Profile</title>
        <link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
        <?php
include INCLUDE_DIR . 'stylesheet.php';
include 'js/ITS_Profile_jquery.php';
?>
    </head>
    <body>
        <div id="framecontent">
            <!---************* NAVIGATION ******************--->
            <?php
echo $nav;
?>
            <!---*******************************************--->
            <div class="innertube">
                <table class="DATA"> <tr><td width="85%">
                            <form id="profile" name="profile" action="Profile.php" method="get">
<?php
echo $form;
?>
                            </form></td>
                        <td><?php
echo $delButton;
?></td></tr>
                </table>
            </div>
        </div>
        <!---******************************************--->
    </div>
    <div id="maincontent">
<?php
//--- PROFILE -----------------------------------------------//
echo $myScore . $grade_link . '<div id="userProfile">' . $list . '</div>';

//--- TIMER -------------------------------------------------//
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
