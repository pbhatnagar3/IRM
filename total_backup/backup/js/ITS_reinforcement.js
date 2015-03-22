$(document).ready(function(){
	$("#Reinforcement").click(function(event){
		//alert("clicked");
		$("#navListQC > li > a").attr("id","");
		$("#Reinforcement > a").attr("id","current");
		$("#contentContainer").children("*").remove();
		$("#contentContainer").append($('<div />').load("/html/test.php"));
	});
});
