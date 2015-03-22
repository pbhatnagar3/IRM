<!DOCTYPE html>
<html>
<head>
<script>
var myCount = 0;
function myFunction(){
	$("#contentContainer > *").remove();
	$("#contentContainer").prepend('<p>Click the button to trigger a function.</p><button onclick="myFunction()">Next Question</button><br>');
	$.post(
		'external_display_reinforcement.php',
		{count: myCount},
		function(data){
			$("#contentContainer").append(data);
		}
	);
	myCount++;
};
</script>
</head>
<body>
<p>Click the button to trigger a function.</p>
<button onclick="myFunction()">Start the Reinforcement module</button>
</body>
</html>


