$(document).ready(function () {
    /*-------------------------------------------------------------------------/    
    * Submits the concepts selected and will return a set of questions matching the condition    
   /*-------------------------------------------------------------------------*/
    $('#submitConcepts').live('click', function (event) {
        /*-------------------------------------------------------------------------*/
        var tdArray = new Array();
        var addButton = "<input type='button' name='createModule' id='createModule' value='Submit questions'>";
        $('#errorConceptContainer').html("");

        $('#seldcon tr').each(function () {
            $(this).find('td').each(function () {
                if ($(this).text() != 'x') tdArray.push($(this).text());
            });
        });
        var tbvalues = tdArray.join();
        //    alert(tbvalues);
        // Ajax call to display questions
        $.post("ajax/ITS_concepts.php", {
            //data to be sent in request
            choice: 'submitConcepts',
            tbvalues: tbvalues
        }, function (data) {
            //alert('aya data:'+ data +' !');
            if (data) {
                data += '<input type="button" id="createModule" name="createModule" value="Create or add to a Module"><br><br><br><br>';
                $("#ConcQuesContainer").html(data);
            } else $("#ConcQuesContainer").html("<br> No questions Available");
        });
    });
    /*-------------------------------------------------------------------------*
     * for Students!
    /*-------------------------------------------------------------------------*/
    //$('#getQuesForConcepts').live('click', function(event) {
    $('.selcon').live('click', function (event) {
        /*-------------------------------------------------------------------------*/
        $('#contentContainer').hide();
        $('#navContainer').fadeOut();
        
        var id = (this.id).split('_');
        var field = id[1];
        var tid = $(this).attr('tid');
        
        /*
        var tdArray = new Array();
        $('#errorConceptContainer').html("");
        $('.resource_concept').each(function() {
                    tdArray.push($(this).text());
        });
        
        var tbvalues = tdArray.join();
        // Ajax call to send questions to replace the question container
        //alert(tbvalues);
        
        // --- RESOURCES --- //
        // Save RESOURCE data
        var id = $('#logout').attr("uid");
        var concept = $( "input[name=selectResource]" ).attr("concept");
        var cid = $('#resource_'+concept).attr("cid");
        var text = $('#ITS_resource_text_'+concept).attr("rid");
        var equation = $('#ITS_resource_equation_'+concept).attr("rid");
        var image = $('#ITS_resource_image_'+concept).attr("rid");
        var example = $('#ITS_resource_example_'+concept).attr("rid");
        
        //alert(id+'~'+cid+'~'+text+'~'+equation+'~'+image+'~'+example);
        $.get('ajax/ITS_resource.php', {
            ajax_args: "resourceDB",
            ajax_data: id+'~'+cid+'~'+text+'~'+equation+'~'+image+'~'+example
        }, function(data) {
            //alert(data);
                //$("#contentContainer").html(data);
        });
        // ----------------- //
        alert(tbvalues);
        */
        $('#errorConceptContainer').html("");
		// alert(field+' '+tid);
        $.post("ajax/ITS_concepts.php", {
            choice: 'getConceptNav',
            concept: field,
            tag_id: tid
        }, function (data) {
            if (data) {
                $("#navContainer").html(data);
            } else {
                $("#navContainer").html("<br> No Concepts Available");
            }
        });
        
        $.get('ajax/ITS_screen.php', {
            ajax_args: "getQuestionsForConcepts",
            ajax_data: field
        }, function (data) {
            if (data) {
				$("#contentContainer").html(data);
				mathJax();
			}
            else $("#contentContainer").html("There was some error in the request");
        });
        $('#navContainer').fadeIn();
        $('#contentContainer').fadeIn();
    });
    /*-------------------------------------------------------------------------*
     * Deletes a row in the selected concepts table
     *-------------------------------------------------------------------------*/
    $(".choice_del").live('click', function () {
        $('#errorConceptContainer').html("");
        $(this).parents('tr').remove();
    });
    /*-------------------------------------------------------------------------*
     * Prompts a user to select or input module name of the module to be 
     * created with selected questions
     *-------------------------------------------------------------------------*/
    $('#createModule').live("click", function () {
        /*-------------------------------------------------------------------------*/
        // collecting selected concepts:
        var tdArray = new Array();
        $('#seldcon tr').each(function () {
            $(this).find('td').each(function () {
                if ($(this).text() != 'x') tdArray.push($(this).text());
            });
        });
        var tbvaluesConcp = tdArray.join();
        var atLeastOneIsChecked = $('#chcktbl:checked').length > 0;
        if (!atLeastOneIsChecked) {
            alert("Please select atleast one question");
            return false;
        }
        $.post("ajax/ITS_concepts.php", {
            //data to be sent in request
            choice: "getModuleDDList",
        }, function (data) {
            if (data) {
                var str = '<input type="button" value="Submit" name="subModule" id="subModule"></input>';
                $("#moduleNameDialog").val(data);
                var dialog = $(data).appendTo('#moduleNameDialog');
                dialog.attr("id", "ModuleNameDivDD");
                dialog.dialog({
                    title: "Select Module Name",
                    show: 'blind',
                    hide: 'slide',
                    resizable: true,
                    width: '50%',
                    height: 'auto',
                    modal: true,
                    close: function () {
                        $('#subModule').css('border', '2px solid brown');
                        dialog.dialog("option", "close", "slide");
                    }
                });
                //alert($('#ModuleNameDivDD'));                                                            
                $('#ModuleNameDivDD').append(str);
                alert($('#subModule'));
            } else $("#ConcQuesContainer").html("There was some error in the request");
        });
    });
    /*-------------------------------------------------------------------------        
          Create a module with selected questions and entered module name
    -------------------------------------------------------------------------*/
    $('#subModule').live('click', function () {
        /*-------------------------------------------------------------------------*/
        var moduleName = $('#moduleListDD').val();
        if (moduleName == 0) moduleName = $('input[name=moduleName]').val();
        if (moduleName == '') {
            $('#ModuleNameDivDD').append('<br>Please enter the module name');
            return false;
        }
        var tdArrayQ = new Array();
        $('#chcktbl:checked').each(function () {
            //alert("adding"+ $(this).val());
            tdArrayQ.push($(this).val());
        });
        var tbvaluesQ = tdArrayQ.join();
        // alert("TB values: "+tbvaluesQ);
        var tdArray = new Array();
        $('#seldcon tr').each(function () {
            $(this).find('td').each(function () {
                if ($(this).text() != 'X') tdArray.push($(this).text());
            });
        });
        var tbvaluesConcp = tdArray.join();

        $.post("ajax/ITS_concepts.php", {
            //data to be sent in request
            choice: 'createModule',
            moduleName: moduleName,
            tbvaluesQ: tbvaluesQ,
            tbvaluesConcp: tbvaluesConcp
        }, function (data) {
            if (data) $("#ConcQuesContainer").html(data);
            else $("#ConcQuesContainer").html("There was some error in the request");
        });
        $('#ModuleNameDivDD').remove();
    });
    /*-------------------------------------------------------------------------*        
     * Triggers when a module name is selected from the dropdown at module 
     * creation
     *-------------------------------------------------------------------------*/
    $('.moduleListDD').live('change', function () {
        /*-------------------------------------------------------------------------*/
        if ($(this).val() == 0) {
            var str = '';
            str = '<br> Module Name: <input type="text" MAXLENGTH="20" name="moduleName"></input>';
            $('#ModuleNameDivDD').append(str);
        }
    });
    /*-------------------------------------------------------------------------        
     Checks or unchecks all table rows when the head is checked or unchecked
    -------------------------------------------------------------------------*/
    $('#chckHead').live("click", function () {
        /*-------------------------------------------------------------------------*/
        if (this.checked == false) {
            $('.chcktbl:checked').attr('checked', false);
        } else {
            $('.chcktbl:not(:checked)').attr('checked', true);
        }
    });
    /*-------------------------------------------------------------------------*
     * "Order By" concept list
     *-------------------------------------------------------------------------*/
    $(".concept_orderby").live('click', function () {

		var letter = $('#current[name=ITS_alph_index]').text();
        var role   = $('#myselectid option:selected').text();
        var index  = $(this).attr('idx');
        
        // alert(index+'~'+letter+'~'+role);
        // orderbyUPDATE(index);
        
        $.get("ajax/ITS_concepts.php", {
             letter: letter,
             role:   role,
             index:  index
        }, function (data) {
             $("#contentContainer").html(data);
        });
    });      
    /*-------------------------------------------------------------------------*
     * Called when letter clicked on
     * ------------------------------------------------------------------------*/
    $('[name="ITS_alph_index"]').live("click", function () {
        /*-------------------------------------------------------------------------*/
        var header = $(this).html();
        var role = $('#myselectid option:selected').text();
        
        //alert(header);
        $('[name="ITS_alph_index"]').each(function (index) {
            if ($(this).html() == header) {
                $(this).attr('id', 'current');
            } else {
                $(this).attr('id', '');
            }
        });
        $('#navContainer').hide();
        $.get("ajax/ITS_concepts.php", {
            letter: $(this).html(),
             role: role,
             index: 2
        }, function (data) {
            if (data) {
                $("#contentContainer").html(data);
            } else {
                $("#conceptContainer").html("<br>No Concepts Available");
            }
        });
    });
    /* -------------------------------------------------------------------------*/
    $("label[for='ASSIGNMENTS']").live('click', function (event) {
    /*--------------------------------------------------------------------------*/
			$('#contentContainer').fadeOut();
            var role = $(this).attr('r');
            $('#scoreContainer').show();

            /* scoreContainer */
            $.get('ajax/ITS_screen.php', {
				ajax_args: "updateScores", 
				ajax_data: ''
            }, function (data) {
                $('#scoreContainerContent').html(data);
            });      
            $.get("ajax/ITS_screen.php", {
                ajax_args: "showAssignments",
                ajax_data: role
            }, function (data) {        
                $('#modeContentContainer').html(data);
    
                var ch = $('.chapter_index#current').text();
				var view = 1;
                //var ch = chUPDATE(ch,chhide,v);			
            $.get("ajax/ITS_screen.php", {
                ajax_args: "showTab",
                ajax_data: ch+','+role+','+view
            }, function (data) {      
                $('#navContainer').html(data);          
                indexUPDATE(ch,view,'Question');
                $('#navContainer').show();
            });     
            });
            $.get("ajax/ITS_screen.php", {
                ajax_args: "changeMode",
                ajax_data: 'question'
            }, function (data) {
                $('#contentContainer').html(data);
                mathJax();
            });    
            $('#contentContainer').fadeIn();  
	});       
    /* -------------------------------------------------------------------------*/
    $("label[for='CONCEPTS']").live('click', function (event) {
    /*--------------------------------------------------------------------------*/
			//$('#contentContainer').html('<img src="admin/icons/ajax-loader.gif" class="fancybox" id="loading">');
			$('#contentContainer').fadeOut();
			var role = $(this).attr('r');
			$('#scoreContainer').hide();
			$('#scoreContainerContent').hide();
            $('#navContainer').hide();

            /* scoreContainer */        
            /*$.post("ajax/ITS_concepts.php", {
                choice: "updateScore"
            }, function (data) {
                $('#scoreContainerContent').html(data);
            });*/
            $.post("ajax/ITS_concepts.php", {
                choice: "showLetters",
            }, function (data) {
                $('#modeContentContainer').html(data);
                var letter=$('#current[name=ITS_alph_index]').text();
				//alert(letter+' '+role);
            $.post("ajax/ITS_concepts.php", {
                choice: "getConcepts",
                index: letter,
                role: role
            }, function (data) {
                $('#contentContainer').html(data);
            });
            });
            $('#contentContainer').fadeIn();       
	});    
    /*-------------------------------------------------------------------------*
     * In student mode, this function call returns with all matched questions for practice
     * -------------------------------------------------------------------------*/
    /*-------------------------------------------------------------------------*/
    $('#changeConcept').live('click', function (event) {
        /*-------------------------------------------------------------------------*/
        $('#coContainer').html('');
        $.post("ajax/ITS_concepts.php", {
            choice: "getConcepts"
        }, function (data) {
            // TODO: to put in condition to check if data returned is null or no questions
            $('#contentContainer').html(data);
        });
    });
    /*-------------------------------------------------------------------------
      Displays the questions in a tabular form for the selected module
/*-------------------------------------------------------------------------*/
    $('.modules').live('click', function (event) {
        /*-------------------------------------------------------------------------*/
        $('input[name=currentModule]').val(this.id);
        //alert('xx');
        $.post("ajax/ITS_concepts.php", {
            choice: "getModuleQuestion",
            modulesQuestion: this.id
        }, function (data) {
            //alert(data);
            if (data) $("#ModuleQuestion").html(data);
            else $("#ModuleQuestion").html("<br> No Questions");
        });
        $("#DelQuestions").show();
    });
    /*-------------------------------------------------------------------------*
     * Deletes selected questions from the selected module
     * ------------------------------------------------------------------------*/
    $("#DelQuestions").live('click', function (event) {
        /*-------------------------------------------------------------------------*/
        var tdArrayQ = new Array();
        var ModuleName = $('input[name=currentModule]').val();
        $('#chcktbl:checked').each(function () {
            tdArrayQ.push($(this).val());
        });
        if (tdArrayQ.length == 0) {
            //alert('No Questions selected');
            return false;
        }
        var tbvaluesQ = tdArrayQ.join();
        $.post("ajax/ITS_concepts.php", {
            choice: "deleteModuleQuestion",
            deleteQuestion: tbvaluesQ,
            ModuleName: ModuleName
        }, function (data) {
            if (data) {
                $.post("ajax/ITS_concepts.php", {
                    choice: "getModuleQuestion",
                    modulesQuestion: ModuleName
                }, function (data) {
                    if (data) $("#ModuleQuestion").html(data);
                    else $("#ModuleQuestion").html("<br> No Questions");
                });
            } else $("#ModuleQuestion").html("<br> No Questions");
        });
    });
    /*-------------------------------------------------------------------------*/
});
//*****************************************//
function orderbyUPDATE(idx) {
//*****************************************//
$('.concept_orderby').each(function(index) {
if (index==idx){$(this).attr('id','current');$(this).parent().attr('id','active');}
else 				    {$(this).attr('id','');$(this).parent().attr('id','');}
});
}
//*****************************************//
