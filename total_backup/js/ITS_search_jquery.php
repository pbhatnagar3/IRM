<?php
/* =============================================================
  Author(s): Gregory Krudysz
  Last Update: Nov-5-2012
/* ============================================================= */
?>
<script type="text/javascript">
    $(document).ready(function() {    
    /*-------------------------------------------------------------------------*/
    $("#ITS_search_box").live('keyup', function(event) {
		var key = $(this).val();
		var rtb = $(this).attr("rtb");
		var rid = $(this).attr("rid");
		var action;
        //$('div.ITS_search').html(key);	
        if(event.keyCode == 13){ action = 'submit'; } 
		else 				   { action = 'search'; }
        $.get('ajax/ITS_search.php', {
                ajax_args: action, 
                ajax_data: key+'~'+rtb+'~'+rid
            }, function(data) {
                $('div.ITS_search').html(data);							
        });
    });	    
    /*-------------------------------------------------------------------------*/
	//$("#ITS_search_box").submit(function() {
		//var key = $(this).val();
		//alert('sub');	
        /*
        $.get('ajax/ITS_search.php', {
                ajax_args: "search", 
                ajax_data: key
            }, function(data) {
                $('div.ITS_search').html(data);							
        });
        */
    //});   
    /*-------------------------------------------------------------------------*/	
    //function doChange() {alert('ch');}
    /*
                $(function() {
                // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
                $( "#dialog:ui-dialog" ).dialog( "destroy" );
                $( "#dialog-form:ui-dialog" ).dialog( "destroy" );
                var Qtitle = $( "#title" ),
                        Qimage = $( "#image" ),
                        Qquestion = $( "#question" ),
                        allFields = $( [] ).add( Qtitle ).add( Qimage ).add( Qquestion ),
                        tips = $( ".validateTips" );

                $( "#dialog-form" ).dialog( {		  
                        autoOpen: false,
                        height: 950,
                        width: 850,
                        modal: true,
                        buttons: {
                                "Create New Question": function() {
                                        var bValid = true;
                                        allFields.removeClass( "ui-state-error" );
                                        if ( true ) {
                                                $( "#users tbody" ).append( "<tr>" +
                                                        "<td>" + Qtitle.val() + "</td>" + 
                                                        "<td>" + Qimage.val() + "</td>" + 
                                                        "<td>" + Qquestion.val() + "</td>" +
                                                "</tr>" ); 
                                                $( this ).dialog( "close" );
                                        }
                                },
                                Cancel: function() {$( this ).dialog( "close" );}
                        },
                        close: function() {allFields.val( "" ).removeClass( "ui-state-error" );}
                });
        });*/
    /*----------------------*/
});
//===========================================//
</script>
