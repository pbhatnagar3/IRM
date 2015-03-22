<?php 
/*
Author(s): Greg Krudysz
Last Update: Aug-18-2013
=============================================*/
error_reporting(E_ALL);
require_once("config.php");
require_once(INCLUDE_DIR . "Authentication.php");
require_once("classes/ITS_footer.php");

session_start();

// already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth']->authenticated()){
	/* redirect to index page */
	header("Location: http://" . $_SERVER['HTTP_HOST']
			. rtrim(dirname($_SERVER['PHP_SELF']), '/\\')
			. "/index.php");
	exit;
}
session_destroy();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<title>Quid</title>
<link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/ITS_index4.css" />
	<link rel="stylesheet" type="text/css" href="css/ITS_intro.css" />	
<script>
function sf(){document.f.username.focus();}
function submitf(obj){
  obj.innerHTML = 'loading ...';
  document.f.submit();
}
</script>
</head>

<body onload="sf()">
<center>
<?php
if (isset($_REQUEST['ask_info'])){
  $msg = 'Please enter username and password<br />';
}
elseif (isset($_REQUEST['failed'])){
  $msg = 'Login failed. Please try again<br />';
}
else{
  $msg = 'Please Login<br />';
}
?>
<div id="container">
	<div id="header"></div>
	<!-- div #navigation -->
	<div id="navigation">
		<ul>
			<li><a href="login.php" id="gtlogo"><img src="css/media/gtbuzz.jpg">GT Login</a></li>
			<li style="float:left"><div class="loginContainer">
			<form action="auth.php" method="post" name="f">
			<table>
			<tr><td>Username:</td><td>Password:</td></tr>
			<tr><td><input class="login" name="username" type="text" value="gte269x"></td><td><input class="login" name="password" type="password" value="gte269x"></td><td><div class="login" onClick="submitf(this)">Login</div></td></tr>
			</table></form></div></li>
		</ul>

	</div>
	<!-- end div #navigation -->
	<div id="content">
		<p><div style="font-size:400%;"><font color="gray">QUID</font></div></p>
<p>
<ul class="list_intro">
<li>
<font color="gray">QUID</font> is a web-based Q+A system designed to enhance a
student's conceptual understanding by providing many
problem-centered exercises.</li>
<li>The system can be keyed to a course textbook
for knowledge representation and can track each student's problem
solving proficiency in terms of concepts.</li>
</ul>
</p>
	</div>
	<!-- div#FOOTER -->
	<div id="footer">
	<center>
		<p>
  krudysz<b>&Dagger;</b>ece.gatech.edu <b>+</b> jim.mcclellan<b>&Dagger;</b>ece.gatech.edu
  	</p>
	<p>
School of Electrical and Computer Engineering <br>
Georgia Institute of Technology, Atlanta, GA 30332-0250
</p>
  		<b>QUID@GT</b> is supported in part by the National Science Foundation Award No. 1041343
	<br>"Collaborative Research: CI-Team Implementation Project: The Signal Processing Education Network"<br>
	</center><br>
Copyright &copy; QUID@GT, 2009-2013
	</div>
<!-- end div#FOOTER -->
</div>
<!-- end div#container -->
</body>
</html>
