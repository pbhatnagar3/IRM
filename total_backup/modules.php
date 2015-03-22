<?php

include("classes/ITS_concepts.php");
session_start();
// return to login page if not logged in
//abort_if_unauthenticated();
$obj = new ITS_concepts();
?>
<html>
<head><title>ITS-Manage your modules</title>
<script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script src="js/ITS_concepts.js" type="text/javascript"></script>  
<link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS_logs.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/login.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">
<link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
   
</head>
<body style="overflow:auto">
<div class="logout"><a href="logout.php">Logout</a></div>
<div class="logout" style="border:2px"><a href="Question.php">ITS Questions</a></div>
<h1>Manage your modules</h1>
<div>
<a style="float: right" href="concepts.php">Add Questions to an existing Module/Create New Module</a>
<br><input type="button" style="float: right"  name="DelQuestions" id="DelQuestions" style="display:none" value="Delete checked Questions from the Module"/>
</div>
<div style="position:absolute; width:250px; top:85px" id="ModuleList"><?php echo $obj->moduleList(0);?></div>
<div id="ModuleQuestion" style="position:absolute; top:85px; left:250px" ></div>
<input type="hidden" name="currentModule" id="currentModule">
 <!-- FOOTER -->
<?php include '_include/footer.php'; 
?>
<!-- end FOOTER -->
</body>
</html>
