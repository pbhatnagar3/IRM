<?php
$LAST_UPDATE = 'Aug-31-2013';
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

global $term, $tset;

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     = $_SESSION['user']->id();
$status = $_SESSION['user']->status();
$info   =& $_SESSION['user']->info();
//------------------------------------------// 
//echo '<pre>';var_dump($_POST);echo '</pre>';die();

if ($status == 'admin' OR $status == 'instructor') {


$scripts = '<ul><li><a href="SQL_2_default_users.php">SQL -to- default users</a></li></ul>';

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
echo $scripts;

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
