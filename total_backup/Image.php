<?php
$LAST_UPDATE = 'Oct-26-2012';
/*=====================================================================//               
  Author(s): Gregory Krudysz
//=====================================================================*/
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_search.php");
require_once("classes/ITS_image.php");

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
    $current = 'Image';
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//
    global $db_dsn, $db_name, $tb_name, $tb_images, $db_table_users, $db_table_user_state;
    
    if (isset($_REQUEST['id'])) {
        $id              = $_REQUEST['id'];
        $fld             = $_REQUEST['f'];
        $img             = new ITS_image($id, $fld);
        $images_for_ques = 0; // for div - ques_pic_table
        $images_in_db    = 1; // for div - main_table
        $page_num        = 0;
    } else {
        $id              = 1;
        $fld             = $_REQUEST['f'];
        $img             = new ITS_image($id, $fld);
        $images_for_ques = 0; // for div - ques_pic_table
        $images_in_db    = 1; // for div - main_table
        $page_num        = 0;
    }
    
    session_start();
    $_SESSION['image'] = $img;
    // QUESTION IMAGES
    $qidstr            = '<div id="ITS_Q" qid="' . $id . '"><a href="Question.php?qNum=' . $id . '" class="ITS_ADMIN">' . $id . '</a></div>';
    $Qimgs             = $img->image_viewer($page_num, $images_for_ques);
    //var_dump(trim($Qimgs));
    //echo '<br>'.count(trim($Qimgs));die();
    
    if (($Qimgs[0] == "") || (empty($Qimgs))) {
        $Qimgs_str = 'None';
    } else {
        $Qimgs_str = $Qimgs;
    }
    
    // SERVER IMAGES		
    $Simgs_str = $img->image_viewer(0, $images_in_db);
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
        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
        <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
        <?php
include 'js/ITS_Question_jquery.php';
include 'js/ITS_search_jquery.php';
?> 
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
	var page = 0;
	$("#picture	").delegate("td", "click", function() {  
			$("#image").val($(this).attr("id")); 
			$("td").removeClass("active"); 
			$("td").addClass("bo");
			$(this).removeClass("bo");
			$(this).addClass("active");
		//	alert('clicked');
	});
	/*-------------------------------------------------------------------------*/	
	$(".img_sm").live("hover", function(){ 
		var iid = $(this).attr('iid');
		var src = $(this).attr("src");
		$("a#single_image").fancybox();
		$("#ITS_image_container").html('<a id="single_image" href="'+src+'"><img src="'+src+'" iid="'+iid+'"></a>'); 		
        $('.img_sm').each(function(index) {
			if (index==val){$(this).attr('id','current');} 
			else 		   {$(this).attr('id','');       }
        });
        $("a#single_image").fancybox();	
	}); 
	/*-------------------------------------------------------------------------*/
	$("input[name='img_nav']").live('click', function(event) {
		var nav = $(this).val();
		var pg  = $("#pgno").html()-1;
		
		if ( nav=='>' ) { pg=pg+1; } 
		else 			{ if (pg){ pg=pg-1; }}

		$('#pgno').html(pg+1);		
        $.get('ajax/ITS_image.php', {
            ajax_args: "navigation", 
            ajax_data: (pg)
        }, function(data) {
            $('#list').html(data);			
        });				
	});
	/*-------------------------------------------------------------------------*/
	$("input[name='control']").live("click", function(event){ 
		var qid = $('#ITS_Q').attr('qid');
		var iid = $("#ITS_image_container img").attr('iid');
		//alert(qid+' - '+iid);

		switch($(this).val()){
		case "Select":
		  var fld = $(this).attr('fld');
		  $.get('ajax/ITS_image.php', {
            ajax_args: "insert", 
            ajax_data: qid+'~'+iid+'~'+fld
          }, function(data) {
            //$('#list').html(data);		
            window.location.replace('Question.php?qNum='+qid);	
          });
		  break;
		case "Cancel":
		  window.location.replace('Question.php?qNum='+qid);
		  break;
		default:
		  alert('action error');
		}
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
<!-- div #Image_container -->
		<!-- <?php
echo $qidstr;
?> -->
		<div id="Image_container">
		<table class="ITS_Image">
		<tr><td id="ITS_image_container"><div class="logo">IMAGES</div></td></tr>
		<tr><td id="list"><center><?php
echo $Simgs_str;
?></center></td></tr>		
		</table>
		</div>
<!-- end: #Image_container -->		
<!-- div #Image_navigation_container -->
		<div id="Image_navigation_container">
			<input type="button" name="img_nav" value="<" id="prev">
			<span id="pgno">1</span>
			<input type="button" name="img_nav" value=">" id="next">
			<input type="hidden" name="id" 		value="<?php
echo $id;
?>"><br>
			<input type="hidden" name="image"   		  id="image">
			<input type="hidden" name="fld" 	value="<?php
echo $fld;
?>">	
			<p>
			<input id="img_submit" type="submit" name="control" value="Select" qid="<?php
echo $id;
?>" fld="<?php
echo $fld;
?>">
			<input id="img_cancel" type="button" name="control" value="Cancel" qid="<?php
echo $id;
?>">
			</p>
		</div>
	<!-- end #Image_navigation_container -->			
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
