<?php
$LAST_UPDATE = 'Sep-06-2012';
/*=====================================================================/
Author(s): Gregory Krudysz, Khyati Shrivastava
Last Revision: Gregory Krudysz, Sep-06-2012
/=====================================================================*/
require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_navigation.php");
require_once("classes/ITS_footer.php");
require_once("FILES/PEAR/XML/Unserializer.php");
require_once("classes/ITS_QTI.php");

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
    $current = 'QTI';
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//
    global $db_dsn, $db_name, $tb_name, $tb_images, $db_table_users, $db_table_user_state;

    session_start();
    
    if (!isset($_POST['QTI_type'])){
       $val = 'no QTI';
   } else {
	   $obj = new ITS_QTI();
	   if ($_POST['QTI_type']==1){
		  	$quesid = $_REQUEST['qid'];
			$val = $obj->exportManyQues($quesid,0,0,1);  // 1 for single question	
			echo $val;		
			exit;
	   } else {
		    $category  = $_REQUEST['category_export'];
			$ques_type = $_REQUEST['ques_type'];
			$val = $obj->exportManyQues(0,$category,$ques_type,2); // $qti_type = [ 1-single question | 2-multiple questions ]
	   }
   }
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
		<link rel="stylesheet" href="css/ITS_image.css" type="text/css">        
        <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
		<link rel="stylesheet" href="css/ITS_QTI.css" type="text/css">           
        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
		<script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
		<script type="text/javascript" src="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
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
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});
	/*-------------------------------------------------------------------------*/	
})
</script>
    </head>
    <body>
        <!---===========================================--->
        <div id="framecontent" style="height:60px">
        <!---************* NAVIGATION ******************--->
            <?php echo $nav;?>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent" style="top:60px">
            <div id="ITS_question_container">
<center>
<?php echo $val;?>	
	</center>  				
	</div>
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
