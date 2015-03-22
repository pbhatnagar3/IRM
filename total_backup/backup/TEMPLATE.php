<?php
$LAST_UPDATE = 'Oct-8-2012';
/*=====================================================================//               
Last Revision: Gregory Krudysz, Oct-8-2012
//=====================================================================*/
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");
require_once("classes/ITS_navigation.php");
require_once("classes/ITS_footer.php");
require_once("classes/ITS_tag.php");
require_once("classes/ITS_search.php");

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
    //--- NAVIGATION ------------------------------// 
    $current = basename(__FILE__, '.php');
    $ITS_nav = new ITS_navigation($status);
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//
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
        <title>Images</title>
        <link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_Solution_warmup.css" type="text/css">   
        <!-- <script type="text/javascript" src="MathJax/MathJax.js"></script> -->
        <?php
include 'js/ITS_Question_jquery.php';
include 'js/ITS_search_jquery.php';
?>
        <script type="text/javascript" src="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />    
    </head>
    <body>
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
echo 'TEXT HERE';
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
echo 'PAGE CONTENT';
echo '</div>';

//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE, '');
echo $ftr->main();
//-----------------------------------------------------------//
?>
           </div>
            <!----------------------------------------------------------->
    </body>
</html>
