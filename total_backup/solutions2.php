<?php
	if(isset($_GET['qNum'])){
		$qid=$_GET['qNum'];
	}else{
		$qid=3481;
	}
?>
<html>
<head>
<title>
</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<link rel="stylesheet" href="css/ITS_Solution_warmup.css" type="text/css">
<script type="text/javascript">

$(document).ready(function() {
	$("#results").hide();
	$("#solutionContainer").click(function(){
		$("#results").slideToggle("slow");
	});
	// .button1
	$("#solutionContainer").one("click", function() {
	//$(".button1").click(function() {
		//var hasaPost = $("input#hasaPost").val(); //=1
		var hasaPost = 1;
		//var viewSolution = $("input#viewSolution").val(); //=1
		var viewSolution = 1;
		//var QNUM = $("input#QNUM").val(); //=qNum
		var QNUM = <?php echo $qid; ?>;
		var dataString = 'hasaPost='+ hasaPost + '&viewSolution=' + viewSolution + '&QNUM=' + QNUM;
		//alert (dataString);return false;
		$('#results').empty();
		$.ajax({
			type: "GET",
			url: "solutions.php",
			data: dataString,
			success: function( html) {
					
					$('#results').html(html);
			}
		});
		return false;
	});
	//=========
	$("#hintContainer").live('click', function() {
		$("#hintResults").slideToggle("slow");
		//$("#results").hide();	
		// .button1
		//$(".button1").click(function() {
			//var hasaPost = $("input#hasaPost").val(); //=1
			var hasaPost = 1;
			//var viewSolution = $("input#viewSolution").val(); //=1
			var viewHints = 1;
			var QNUM = <?php echo $qid; ?>;
			//var QNUM = $('#ITS_QCONTROL_TEXT').attr("value");
			var dataString = 'hasaPost='+ hasaPost + '&viewHints=' + viewHints + '&QNUM=' + QNUM;
			//alert (dataString);return false;
			//$('#results').empty();
			$.ajax({
				type: "GET",
				url: "solutions.php",
				data: dataString,
				success: function( html) {
						
						$('#hintResults').html(html);
				}
			});
			return false;
		});
	//============
	$("#solContainer").live('click', function() {
		$("#solResults").slideToggle("slow");
		//$("#results").hide();	
		// .button1
		//$(".button1").click(function() {
			//var hasaPost = $("input#hasaPost").val(); //=1
			var hasaPost = 1;
			//var viewSolution = $("input#viewSolution").val(); //=1
			var viewSol = 1;
			var QNUM = <?php echo $qid; ?>;
			//var QNUM = $('#ITS_QCONTROL_TEXT').attr("value");
			var dataString = 'hasaPost='+ hasaPost + '&viewSol=' + viewSol + '&QNUM=' + QNUM;
			//alert (dataString);return false;
			//$('#results').empty();
			$.ajax({
				type: "GET",
				url: "solutions.php",
				data: dataString,
				success: function( html) {
						
						$('#solResults').html(html);
				}
			});
			return false;
		});
	
});

</script>

</head>
<body>
<?php

	//$qid = 2241;
	/*
	echo '
	<form>
	<input type="hidden" name="hasaPost" id="hasaPost" value="1" />
	<input type="hidden" name="viewSolution" id="viewSolution" value="1" />
	<input type="hidden" name="QNUM" id="QNUM" value="'.$qid.'" />
	<input type="button" value="Get" class="button1" />
	</form>
	
	';
	*/
	echo '<div id="solutionContainer"><span>&raquo;&nbsp;The Solution</span></div>' .
		'<div id="results"></div>';
		
	echo '<br><br><br><br><div id="hintContainer"><span>&raquo;&nbsp;Hints</span></div>';
	echo '<div id="hintResults"></div>';
	echo '<br><br><br><br><div id="solContainer"><span>&raquo;&nbsp;Solutions & Detailed</span></div>';
	echo '<div id="solResults"></div>';
 //include('js/ITS_solution_jquery.php');
?>


</body>
</html>
