<?php
/* ============================================================= */
  $LAST_UPDATE = 'Feb-14-2013';
  /* Author(s): Gregory Krudysz
/* ============================================================= */
?>
<script type="text/javascript">
	$(function() {
      $(".ITS_select").change(function() { document.ece2025.submit(); });
			$("#select_class").buttonset();
    });
	/*-------------------------------------------------------------------------*/
  $(document).ready(function() { 
     $("#scoreContainer").click(function(){$("#scoreContainerContent").slideToggle("slow");});
	 /*-------------------------------------------------------------------------*/		
	 $("#sortProfile").change(function() { doChange(); }).attr("onchange", function() { doChange(); });
	 /*-------------------------------------------------------------------------*/	 
	 $("a.ITS_question_img").fancybox({
	      type: 'image',
		  closeClick: true,
		  aspectRatio: true,
		  padding: 5,
          helpers: {
	overlay : {
		closeClick : true,
		speedOut   : 300,
		showEarly  : false,
		css        : { 'background' : 'rgba(255, 255, 255, 0)'}
	},			  
              title : {
                  type : 'inside'
              }
          }
      });
	/*--------------------Added by Mi Seon Park----------------------------------*/
	 /*$("#select_option").change(function() { doChange(); }).attr("onchange", function() { doChange(); });
	function doChange(){
	    var ch      = $("#select_option").attr("ch");
	    var orderby = $("#select_option option:selected").text();
	    $.get('ITS_admin_AJAX.php', { ajax_args: "orderQuestions", ajax_data: ch+'~'+orderby}, function(data) {
                          //alert(data);
                                $("#select_option").html(data); 
                                $("#select_option").change(function() { doChange(); });
      });   
	}*/
	 /*-------------------------------------------------------------------------*/
	$("#sortProfile").change(function() { doChange(); }).attr("onchange", function() { doChange(); });
	/*-------------------------------------------------------------------------*/
	function doChange() {			
      var sid     = $("#sortProfile").attr("sid");
      var section = $("#sortProfile").attr("section");
      var status  = $("#sortProfile").attr("status");
      var ch      = $("#sortProfile").attr("ch");
      var diff    = $("#select_difficulty option:selected").text();
      var orderby = $("#sortProfile option:selected").text();
			//alert(sid+'~'+orderby+'~'+diff);
      $.get('ajax/ITS_admin.php', { 
		ajax_args: "orderCourse", ajax_data: sid+'~'+section+'~'+status+'~'+ch+'~'+diff+'~'+orderby}, function(data) {
		//ajax_args: "", ajax_data: ch+'~'+orderby}, function(data) {
			  //alert(data);
				$("#userProfile").html(data); 
				$("#sortProfile").change(function() { doChange(); });
      });			
    }	
	 /*-------------------------------------------------------------------------*/
  });
</script>
