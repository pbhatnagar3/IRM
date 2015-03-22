<?php
$LAST_UPDATE = 'Oct-1-2012';
/*=====================================================================//					
     Author(s): Khyati Shrivastava
     Last Revision: Gregory Krudysz, Oct-1-2012
//=====================================================================*/
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");
require_once("classes/ITS_navigation.php");
require_once("classes/ITS_footer.php");
require_once("classes/ITS_tag.php");
require_once("classes/ITS_search.php");
include("classes/ITS_concepts.php");
session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();
$obj = new ITS_concepts();

//--- NAVIGATION ------------------------------// 
	$current = basename(__FILE__,'.php');
	$ITS_nav = new ITS_navigation($status);
	$nav     = $ITS_nav->render($current);    
//---------------------------------------------//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en"><title>ITS-Manage your modules</title>
<script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script src="js/ITS_concepts.js" type="text/javascript"></script>  
<link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS_logs.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/login.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
</head>
    <body>
        <!---===========================================--->
        <div id="framecontent">
            <!---************* NAVIGATION ******************--->
            <?php echo $nav;?>
            <!---******************************************--->
            <div class="innertube">
            <!---******************************************--->
            </div>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent">
            <div id="ITS_question_container">
                <?php echo $adminNav;?>
                
<div>
<a style="float: right" href="concepts.php">Add Questions to an existing Module/Create New Module</a>
<br><input type="button" style="float: right"  name="DelQuestions" id="DelQuestions" style="display:none" value="Delete checked Questions from the Module"/>
</div>
<div style="position:absolute; width:250px; top:85px" id="ModuleList"><?php echo $obj->moduleList(0);?></div>
<div id="ModuleQuestion" style="position:absolute; top:85px; left:250px" ></div>
<input type="hidden" name="currentModule" id="currentModule">
<?php
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
