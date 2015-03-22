<?php

require_once ("config.php");
include("classes/ITS_concepts.php");
//include("ajax/ITS_screen.php");
//die('xxd');
?>
<head>
<center><h2>ITS - Concept Browser</h2></center>
<script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>

 <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
 <script src="js/ITS_concepts.js" type="text/javascript"></script>  
 <link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen"> 
 <link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen"> 
</head>
<body class="ITS">
<?php
$id = 1;
$term = 'Summer_2013';
	$obj = new ITS_concepts($id,$term);
	echo $obj->showLetters();
?>

<div id="content" style="border:3px solid;height: 300px;margin:5%">
	<a href="modules.php">Go Back!</a>
	<div id="moduleNameDialog" style="display:none" ></div>
	<?php 
		echo $obj->conceptContainer();
		echo $obj->SelectedConcContainer();
		echo $obj->ConcQuesContainer(0);
	?>
</div>
</body>
</html>
