$(document).ready(function() {
	$('#selectResource').live('click', function(event) {	
/*-------------------------------------------------------------------------*/		
		//
		alert('aa');
		/*
        var s = $(this).html();
            //	alert('calling');
            $.get("ajax/ITS_resource.php", {
                ajax_args: "changeMode",
                ajax_data: 'question'
            }, function(data) {
                $('#contentContainer').html(data);
            });*/
    });
/*-------------------------------------------------------------------------*/
    $('#changeConcept').live('click', function(event) {
/*-------------------------------------------------------------------------*/		
        $('#coContainer').html('');
        $.post("ajax/ITS_concepts.php", {
            choice: "getConcepts"
        }, function(data) {
            // TODO: to put in condition to check if data returned is null or no questions
            $('#contentContainer').html(data);
        });
    });
/*-------------------------------------------------------------------------
	  Displays the questions in a tabular form for the selected module
/*-------------------------------------------------------------------------*/
    $('.modules').live('click', function(event) {
/*-------------------------------------------------------------------------*/		
        $('input[name=currentModule]').val(this.id);
//alert('xx');
        $.post("ajax/ITS_concepts.php", {
            choice: "getModuleQuestion",
            modulesQuestion: this.id
        }, function(data) {
            //alert(data);
            if (data)
                $("#ModuleQuestion").html(data);
            else
                $("#ModuleQuestion").html("<br> No Questions");
        });
        $("#DelQuestions").show();
    });
    /*-------------------------------------------------------------------------*
	 * Deletes selected questions from the selected module
	 * ------------------------------------------------------------------------*/
    $("#DelQuestions").live('click', function(event) {
/*-------------------------------------------------------------------------*/		
        var tdArrayQ = new Array();
        var ModuleName = $('input[name=currentModule]').val();
        $('#chcktbl:checked').each(function() {
            tdArrayQ.push($(this).val());
        });
        if (tdArrayQ.length == 0) {
            alert('No Questions selected');
            return false;
        }
        var tbvaluesQ = tdArrayQ.join();
        $.post("ajax/ITS_concepts.php", {
            choice: "deleteModuleQuestion",
            deleteQuestion: tbvaluesQ,
            ModuleName: ModuleName
        }, function(data) {
            if (data) {
                $.post("ajax/ITS_concepts.php", {
                    choice: "getModuleQuestion",
                    modulesQuestion: ModuleName
                }, function(data) {
                    if (data)
                        $("#ModuleQuestion").html(data);
                    else
                        $("#ModuleQuestion").html("<br> No Questions");
                });
            } else
                $("#ModuleQuestion").html("<br> No Questions");
        });
    });
/*-------------------------------------------------------------------------*/    
});
