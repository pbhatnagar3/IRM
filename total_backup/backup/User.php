<?php
/*=====================================================================//	
Author(s): Greg Krudysz
Last Revision: Nov-9-2012	
//=====================================================================*/
//--- begin timer ---//
$mtime     = microtime();
$mtime     = explode(" ", $mtime);
$mtime     = $mtime[1] + $mtime[0];
$starttime = $mtime;
//------------------//
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_timer.php");
require_once("classes/ITS_user.php");
require_once("classes/ITS_message.php");
require_once("classes/ITS_navigation.php");

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     = $_SESSION['user']->id();
$status = $_SESSION['user']->status();
$info =& $_SESSION['user']->info();
//------------------------------------------// 
if ($status == 'admin') {
    $k    = new ITS_user('j');
    $list = ''; //$k->add_user();
    
    //--- NAVIGATION ------------------------------// 
    $current = basename(__FILE__, '.php');
    $ITS_nav = new ITS_navigation($status);
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//		
}else{
    // redirect to start page //
    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>User</title>
		<link type="text/css" href="js/jquery-ui-1.8.23.custom/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"></script>
	<link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_user.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/login.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/ITS_score.css" type="text/css" media="screen">
	<script type="text/javascript">
	/*
	$(function() {
      $(".ITS_select").change(function() { document.profile.submit(); });
			$("#select_class").buttonset();
  });*/
	/*-------------------------------------------------------------------------*/
  $(document).ready(function() { 
	 /*-------------------------------------------------------------------------*/	
	 function doChange() {			
      var sid     = $("#sortProfile").attr("sid");
      var section = $("#sortProfile").attr("section");
      var status  = $("#sortProfile").attr("status");
      var ch      = $("#sortProfile").attr("ch");
      var orderby = $("#sortProfile option:selected").text();
			//alert(sid+'~'+orderby);
      $.get('ajax/ITS_admin.php', { ajax_args: "orderProfile", ajax_data: sid+'~'+section+'~'+status+'~'+ch+'~'+orderby}, function(data) {
			  //alert(data);
				$("#userProfile").html(data); 
				$("#sortProfile").change(function() { doChange(); });
      });			
    }	
	 /*-------------------------------------------------------------------------*/	 
	 $('#ITS_add_user').live('click', function() {
  	    var fn = $("input[name=first_name]").val();
        var ln = $("input[name=last_name]").val();
        var st = $("input[name=username]").val();
        var de = $("#select_status option:selected").text();
		//
		//alert('ITS_add_user:  '+fn+'~'+ln+'~'+st+'~'+de);
        $.get('ajax/ITS_admin.php',{ ajax_args: "addUser", ajax_data: fn+'~'+ln+'~'+st+'~'+de}, function(data) {
          //alert(data);
		  $('#users').append(data);  	
        });
	 });
	 /*-------------------------------------------------------------------------*/		 
  });
  </script>	
</head>
<body>
<div id="framecontent">
<!---************* NAVIGATION ******************--->
<?php
echo $nav;
?>
<!---*******************************************--->
<div class="innertube">
</div>
</div>
<!---******************************************--->
</div>
<div id="maincontent">
<center>
<form class="ITS_user" method="POST" action="">
<table class="ITS_user">
	 <tr>
	   <th>FIRST NAME</th><th>LAST NAME</th><th>USERNAME</th><th>DESIGNATION</th>
	 </tr>	
	 <tr> 
	 <td><input type="text" class="ITS_fields" name="first_name" width="200px" /></td>
     <td><input type="text" class="ITS_fields" name="last_name" size="15" /></td>
     <td><input type="text" class="ITS_fields" name="username" size="15" /></td>
     <td>
	 			<select class="ITS_status" id="select_status">
			        <option value="">Fall_2013</option>
    			    <option value="">student</option>
					<option value="">instructor</option>
					<option value="">admin</option>
				</select>
	  </td>
	</tr>
  <tr><td style="width:50px" colspan="4"><input type="button" class="ITS_button" id="ITS_add_user" name="add_user" value="Add User"/></td></tr>
	</table>
</form>
</center>
<?php
//-----------------------------------------------------------//
// ACCOUNT INFO
//-----------------------------------------------------------//
//echo $section.'--'.$sid.'--'.$status.'--'.$ch.'<p>';
echo '<div id="users">' . $list . '</div>';
//-----------------------------------------------------------//
//--- begin timer ---//
$mtime     = microtime();
$mtime     = explode(" ", $mtime);
$mtime     = $mtime[1] + $mtime[0];
$endtime   = $mtime;
$totaltime = ($endtime - $starttime);
//------------------//
//--- FOOTER ------------------------------------------------//

//-----------------------------------------------------------//
?>
</div>
</body>
</html>
