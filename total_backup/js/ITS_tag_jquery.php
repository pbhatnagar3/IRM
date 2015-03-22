<?php
/* =============================================================
  Author(s): Gregory Krudysz
  Last Update: Jun-21-2013
/* ============================================================= */
?>
<script type="text/javascript">
    $(document).ready(function() { 
		/*-------------------------------------------------------------------------*/ 
        $('.tag_del_DB').live('click', function(event) {
			var tid   = $(this).attr('tid');
			var tname = $(this).attr('tname');
			var rid   = $(this).attr('rid');
			var rname = $(this).attr('rname');
			//alert(tid+'~'+tname+'~'+rid+'~'+rname);			

var agree=confirm("Are you sure you want to delete this tag \n and all its linked resources?");
if (agree)
{
				$.get('ajax/ITS_tag.php', {
                ajax_args: "tag_del_DB", 
                ajax_data: tid+'~'+tname+'~'+rname
            }, function(data) {
				//$('.innertube').append(data);	
                //$('div.taginfo').html(data);							
            });
          
			$(this).parent().parent().parent().parent().hide(800, function () {	
				$(this).remove();
			});
}
		});   
		/*-------------------------------------------------------------------------*/ 		
        $('.tag_del').live('click', function(event) { 
			var tid   = $(this).attr('tid');
			var tname = $(this).attr('tname');
			var rid   = $(this).attr('rid');
			var rname = $(this).attr('rname');
			//alert(tid+'~'+tname+'~'+rid+'~'+rname);			

			$.get('ajax/ITS_tag.php', {
                ajax_args: "tag_del", 
                ajax_data: tid+'~'+tname+'~'+rid+'~'+rname
            }, function(data) {
				$('#ITS_question_container').append(data);	
                //$('div.taginfo').html(data);							
            });
          
            //var parentTag = $(this).parent().get(0).tagName;alert(parentTag);
			$(this).parent().parent().parent().parent().hide(800, function () {	
				$(this).remove();
			});
		});  		
		/*-------------------------------------------------------------------------*/ 
        $('.tag_add').live('click', function(event) { 
			// alert('tag_add');
			var tid   = $(this).attr('tid');
			var tag   = $(this).attr('tag');
			var rid   = $(this).attr('rid');
			var rname = $(this).attr('rname');
			//alert(tid+'~'+tag+'~'+rid+'~'+rname);

			$.get('ajax/ITS_tag.php', {
                ajax_args: "tag_add",
                ajax_data: tid+'~'+tag+'~'+rid+'~'+rname
            }, function(data) {
                $('#ITS_TAGS_LIST').append(data);							
            });

            /* remove from list */
            $(this).parent().parent().parent().parent().hide(800, function () {
				$(this).remove();
			});
		});		           
        /*-------------------------------------------------------------------------*/ 
        $('.tagrefXX').live('click', function(event) {  
			alert('ss');
			var tid   = $(this).attr('tid');
			var tname = $(this).html();
			/*
			$.get('ajax/ITS_tag.php', {
                ajax_args: "practiceMode", 
                ajax_data: tid+','+tname
            }, function(data) {
                $('div.taginfo').html(data);							
            });*/
		if ($("div.taginfo").is(":hidden")) {
			$("div.taginfo").slideDown("slow");
		} else {
			$("div.taginfo").hide();
		}
		}); 	
    /*-------------------------------------------------------------------------*/	
    $(".ITS_addTAG").live('click', function(event) {
        var obj_id = $(this).attr("ref");	
        var tag    = $(this).attr("tag");
        $("#TXA_"+obj_id+"_TARGET").insertAtCaret(tag);
    });      
    /*-------------------------------------------------------------------------*/
});
//===========================================//
</script>
