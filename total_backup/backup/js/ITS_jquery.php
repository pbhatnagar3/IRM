<?php
/* =============================================================  /
  LAST_UPDATE: Sep-6-2013
  Author(s): Gregory Krudysz
/* ============================================================= */
?>
<script type="text/javascript">
    /*-------------------------------------------------------------------------*/
    $(document).ready(function() {
        /*var d = new Date();
          var chhide = 0;
          if (d.getHours()>=4) { chhide = 3; }
          else 				   { chhide = 2; }
        */
        var v = $('#Question').attr('view');
        if (v==0){ getMSG(); }
        //$("#chapter_index4").css({background: '#ffff88',border: '2px solid #666666'}); 
        //--------------rating-------------------//
        var rate = 4;
        $("#ITS_rate").children().not(":radio").hide();
        $("#ITS_rate").stars({
            showTitles:false,
            cancelShow:false,
            captionEl: $("#stars-cap"),
            callback: function(ui, type, value) {
                //alert('begin value :'+value+'rate '+rate);
                //$("#ITS_rate").attr('rating') = value;
                jQuery.data(document.body, 'foo', 52);
                //var rate  = $("#ITS_rate").value();
                //alert('saved rate: '+rate);
                //alert('val: '+rate);
                //$.get('ajax/ITS_screen.php', { ajax_args: "rating", ajax_data: value}, function(data){
                //	rate = data;  //$("#ajax_response").html(rate);
                //});
            }		
        });
        //-------------fancy box-----------------//
		imageBox();
		mathJax();
        //--------------rating-------------------//
        $(".icon#tagg_icon").click(function(){
            $(".ITS_instruction").slideToggle("normal");
        });
        $(".icon#Tag_icon").mouseover(function(){
            $(".ITS_tags").slideToggle("normal");
        });
        $("div.tagsMore#header").click(function(){
            $("div.tagsMore#list").slideToggle("fast");
        });
        $("#messageContainer").click(function(){
            $("#messageContainerContent").slideToggle("slow");
        });
        $("#adminContainer").click(function(){
            $("#adminContainerContent").slideToggle("slow");
        }); 
        $("#scoreContainer").click(function(){
            $("#scoreContainerContent").slideToggle("slow");
        });
        $("#levelContent").hide();
		$("#levelContainerToggle").live('click', function() {$("#levelContent").slideToggle("slow");});
        $("#feedbackContainer").live('click', function() {$("#feedbackContainerContent").slideToggle("slow");});        
        //$("#accor").accordion({autoHeight: false});
        $("#dialog").dialog({
            autoOpen: true, 
            resizable: false, 
            width:425
        });
        /*-------------------------------------------------------------------------*/		
        var ch = $('#Question').attr('ch');
        //alert(v+' ~ '+ch);
        indexUPDATE(ch,v,'Question');
        /*-------------------------------------------------------------------------*/
        $.ajaxSetup({
    beforeSend:function(){
        // show gif here, eg:
        $("#loading").show();
    },
    complete:function(){
        // hide gif here, eg:
        $("#loading").hide();
    }
		});
        /*-------------------------------------------------------------------------*/
        $('#Practice').live('click', function() {
		if($(this).attr('choice_mode')=='concept'){return;} // Do nothing in case of concept mode
            var v = $(this).attr('view');
            var header = $(this).attr('id');
            var ch = $('.chapter_index#current').text().replace(/^\s+|\s+$/g,""); // need to trim white spaces
            // alert('#Practice: '+ch);

            $('[name=header]').each(function(index) {
                if ($(this).attr('id') == header){
                    $(this).children("a").attr('id','current');
                } 
                else 											 			 {
                    $(this).children("a").attr('id','');
                }
            });
            $('.chapter_index').each(function(index) {
                $(this).css({
                    display: 'inline'
                });
            });         
            indexUPDATE(ch,v,'Practice'); //indexUPDATE(ch,chhide,v);
			
            // re-load updated ch
            var ch = $('.chapter_index#current').text().replace(/^\s+|\s+$/g,""); // need to trim white spaces
            //alert('#Practice: updated ch '+ch);
            
            $.get('ajax/ITS_screen.php', {
                ajax_args: "practiceMode", 
                ajax_data: ch
            }, function(data) {
                $('#contentContainer').html(data);
                imageBox();
                mathJax();
                var qid = $('#ratingContainer').attr('qid');
                ratingUPDATE(qid);	// update rating module (style, rendering & state)								
            });
        });
        /*-------------------------------------------------------------------------*/
        $('#Question').live('click', function() {
            var v = $(this).attr('view');
            var header = $(this).attr('id');
			
			if($(this).attr('choice_mode')=='concept'){
				$.get('ajax/ITS_screen.php',{
				ajax_args: "nextQuestion", 
				ajax_data: 'concept'
				}, function(data) {
					$('#contentContainer').html(data); 
					imageBox();
					mathJax();
				});
				$('[name=header]').each(function(index) {
                if ($(this).attr('id') == header){
                    $(this).children("a").attr('id','current');
                } 
                else{
                    $(this).children("a").attr('id','');
                }
            });	
					
			  if (v==0) {  // 0 => "Q" tab is deactivated 
               getMSG();
			}
				return;
			}
			
		    var ch = $('.chapter_index#current').text(); //$(this).text().replace(/^\s+|\s+$/g,""); // need to trim white spaces
            var chhide = $('#chList').attr('ih');   
 
            $('[name=header]').each(function(index) {
                if ($(this).attr('id') == header){
                    $(this).children("a").attr('id','current');
                } 
                else{
                    $(this).children("a").attr('id','');
                }
            });
			//alert(c);		
           if (v==0) {  // 0 => "Q" tab is deactivated 
     
               getMSG();
			}
			else {	
				ch = chUPDATE(ch,chhide,v);
                indexUPDATE(ch,v,'Question');
                $.get('ajax/ITS_screen.php', {
                    ajax_args: "newChapter", 
                    ajax_data: ch+',question'
                }, function(data) {
                    $('#contentContainer').html(data);
                    mathJax();
                    var qid = $('#ratingContainer').attr('qid');
                    ratingUPDATE(qid);	// update rating module (style, rendering & state)	
                    imageBox();			
                });
            }
        });
        /*-------------------------------------------------------------------------*/
        $('#Review').live('click', function() {
			if($(this).attr('choice_mode')=='concept'){return;} 
			// DO nothing - no review mode in concept based assignment. Should hide this tab all together. 
    
            var header = $(this).attr('id');
            var ch     = $('.chapter_index#current').text();
            
            //alert('header: '+header+' chapter: '+ch);
            //if (ch == 'Survey') { ch = 14; }
            $('[name=header]').each(function(index) {
                if ($(this).attr('id') == header){ $(this).children("a").attr('id','current'); } 
                else  							 { $(this).children("a").attr('id',''); }
            });		

            var qIdx = $("#qAvail"+ch).html();  // Look-up from the Score-board
            //alert('chapter: '+ch+' index: '+qIdx);
			indexUPDATE(ch,v,'Review');
			//alert(ch+qIdx);
            $.get('ajax/ITS_screen.php', {
                ajax_args: "reviewMode", 
                ajax_data: ch+','+qIdx
            }, function(data) {      		   
                $('#contentContainer').html(data);
                var qid = 1000; //$('#ratingContainer').attr('qid');
                //alert(qid);
                ratingUPDATE(qid);	// update rating module (style, rendering & state)	
                imageBox();
                mathJax();

                //------------- slider -------------//
                var qN   = $("#N1").attr('qN');
                var qMax = $("#qAvail"+ch).html();    // Look-up from the Score-board
                //alert('chapter: '+ch+' index: '+qIdx+' qN: '+qN);
                slider(ch,qN);
                navUPDATE(ch);
                //------------//
            });
			indexUPDATE(ch,v,'Review');
        });
        /*-------------------------------------------------------------------------*/
        $('.chapter_index').live('click', function() {
            var ch = $(this).text().replace(/^\s+|\s+$/g,""); // need to trim white spaces
            var v  = $('#Question').attr('view');
        //    alert(ch+' '+v);
            var mode = $('[name=header] a#current').parent().attr('id');
            // if(mode=='concept')
			// alert(mode);
            indexUPDATE(ch,v,mode);
            
            //alert(mode);
            switch (mode) {
                //----------//
                case 'Practice':
                    //----------//
                    //alert(current);
                    var callback = "newChapter"; 
                    var header   = 'Chapter '+ch; 
                    $.get('ajax/ITS_screen.php', {
                        ajax_args: callback, 
                        ajax_data: ch+','+mode.toLowerCase()
                    }, function(data) {
                        $('#contentContainer').html(data);	
                        imageBox();		
                        mathJax();
                    });
                    break;				
                //----------//
            case 'Question':
                //----------//
                var v = $('#'+mode).attr('view');
                //alert(v);
                if (v==0) {
					getMSG();
                }
                else {
                    if (ch == 'Survey') {
                        callback = "getSurvey";
                        header = ch;
                    }
                    else {
                        callback = "newChapter";
                        header = 'Chapter '+ch;
                    }		
                    $.get('ajax/ITS_screen.php', {
                        ajax_args: callback, 
                        ajax_data: ch.toLowerCase()+','+mode.toLowerCase()
                    }, function(data) {
                        $('#contentContainer').html(data);		
						imageBox();
						mathJax();
                    });
                }
                break;
            //----------//
        case 'Review':
            //----------//
            if (ch == 'Survey') {
                header = ch; 
                $.get('ajax/ITS_screen.php', {
                    ajax_args: "surveyMode", 
                    ajax_data: ch+',0'
                }, function(data) {
                    $('#contentContainer').html(data);
                });
            }
            else {				 	
                var qMax = $("#qAvail"+ch).html();  // Look-up from the Score-board
                callback = "reviewUpdate"; 
                header   = 'Chapter '+ch;
                // alert(ch+' '+qMax);
                $.get('ajax/ITS_screen.php', {
                    ajax_args: callback, 
                    ajax_data: ch+','+qMax
                }, function(data) {
                    $('#N2').html(data);
                    slider(ch,qMax);
                    imageBox();
                    mathJax();
                    navUPDATE(ch);
                    if (qMax>0) {
                        $('#N1').css({display: 'block'});
                        $("#slider").slider( "option", "max", qMax );
                        $("#slider").slider( "option", "value", qMax );
                        $("#reviewNavTxt").html( qMax + " / " + qMax);

                        navUPDATE(ch);
                        var qid = $('#ratingContainer').attr('qid');
                        ratingUPDATE(qid);
                    } else {
                        $('#N1').css({display: 'none'});
                    }
                });
            }
            break;
    }
});
/*-------------------------------------------------------------------------*/
$('.ITS_navigation[name=updateReview_index]').live('click', function(event) {
    //alert('.ITS_navigation[name=updateReview_index]');
    var ch  = $(this).attr("ch");
    var del = $(this).attr("del"); //alert(ch+' | '+del);		
    if (ch == 'Survey') {
        $.get('ajax/ITS_screen.php', {
            ajax_args: "surveyMode", 
            ajax_data: ch+','+del
        }, function(data) {
            $('#contentContainer').html(data);
            imageBox();			
            mathJax();
        });			
    }
    else {
        var idx = parseInt(del) + parseInt($( "#slider" ).slider("option","value")) - 1;
        //alert(idx);
        $.get('ajax/ITS_screen.php', {
            ajax_args: "reviewUpdate", 
            ajax_data: ch+','+idx
        }, function(data) {
            //$('#ITS_meta').html(data); 
            $("#reviewNavTxt").html( (idx+1) + " / " + $("#slider").slider("option","max"));
            $("#slider").slider({ value: (idx+1) });
            navUPDATE(ch); // Need to update slider first
            $('#N2').html(data);
            var qid = $('#ratingContainer').attr('qid');
            ratingUPDATE(qid);	
            imageBox();			
            mathJax();
        });
    }
});
/*-------------------------------------------------------------------------*/
$('.ITS_navigation[name=update_index]').live('click', function(event) {
    var mode = $(this).attr("mode");  //alert(mode);
    var ch   = $(this).attr("ch");
    //alert('.ITS_navigation[name=update_index]: mode('+mode+')');
    $.get('ajax/ITS_screen.php', {
        ajax_args: "getContent",
        ajax_data: mode+','+ch
    }, function(data) {
        $('#contentContainer').html(data);
        imageBox();
        mathJax();
    });
});
/*-------------------------------------------------------------------------*//*
        $('#matchingInstruction').live('click', function() {
            //var show = $( "#dialog" ).dialog( "isOpen" );
            $("#dialog").dialog('open');
            $(".ui-dialog-titlebar").css({
                background: '#999', 
                border: 'none'
            });
        });*/
/*-------------------------------------------------------------------------*/
$('.ansCheck').live('click', function() {
    //alert($(this).attr("id"));
    var chkid = '#'+$(this).attr("for");
    $('#errorContainer').css({
        display: 'none'
    });
    // selection restriction
    var check_info = chkid.split("_");    
    for(var c = 1; c <= check_info[4]; c++) {
        if ( c==check_info[2] ) { 
            //alert('#check_'+check_info[1]+'_'+c);
            //$('#check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).attr("checked",true); //$(obj).is(':checked')
            //alert($('#check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).val());
            //alert($('#check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).attr("checked"));
            $(this).addClass('ansCheck_sel');
            $(this).attr("checked",true);
            //alert($(this).attr("id"));
            //$('#check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).addClass('ansCheck_sel');
        }
        else {
            $('#check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).attr("checked",false); //$(obj).is(':checked')
            $('#label_check_'+check_info[1]+'_'+c+'_'+check_info[3]+'_'+check_info[4]).removeClass('ansCheck_sel');
        }
    }
});
/*-------------------------------------------------------------------------*/
$('span[name=ITS_MC]').live('click', function() {	
    var current = $(this).attr("id");
    $('#ratingContainer').css({
        display: 'block'
    });
    $('#errorContainer').css({
        display: 'none'
    });
    $('span[name=ITS_MC]').each(function(index) {
        //alert(current +'=='+ $(this).attr("id"));
        if ( current == $(this).attr("id") ) {
            $(this).removeClass('CHOICE_ACTIVE').addClass('CHOICE_SELECTED');
        }
        else                                 {
            $(this).removeClass('CHOICE_SELECTED').addClass('CHOICE_ACTIVE');
        }
    });
});
/*-------------------------------------------------------------------------*/
$('#ITS_submit').live('click', function() {
    //*** Prevent Multiple submissions: - 1. Client: disable button/form ***//
    //$(this).attr('disabled', true);
    //***//
    var ch     = $(this).attr("ch");
    var qid    = $(this).attr("qid");
    var qtype  = $(this).attr("qtype");
    var t      = $(this).attr("t");
    var mode   = $(this).attr("mode");
    
    var values = new Array();
	var isempty_c=true;
	
	if (mode=="concept"){ var tid = $('.navConcept').attr("tid"); }
	else { tid = ''; }
     
    //alert(ch+' '+qid+' '+qtype+' '+values+' '+t+' '+mode+' '+tid);
    switch (qtype.toUpperCase()) {
        //----------//
        case 'M':
            //----------//
            $('.ansCheck_sel').each(function(index) {	  //:checked
                //alert($(this).attr("id"));
                var check_info = $(this).attr("id").split("_");
                if (!values[check_info[4]-1]) {
                    values[check_info[4]-1] = '';
                }
                //alert('values['+check_info[2]+'] = '+check_info[3]);
                values[check_info[2]-1] = check_info[3];
            });
            break;
        //----------//
    case 'MC':
        //----------//    
        var alphaChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $('span[class="CHOICE_SELECTED"]').each(function(index) {	    //name=ITS_MC
            //if ($(this).hasClass("CHOICE_ACTIVE")) { };
            var check_info = $(this).attr("id").split("_");
            //values[check_info[1]-1] = check_info[1];
            //if (!values[check_info[2]-1]) { values[check_info[2]-1] = '';}
            //values[0] = check_info[1]; 
            values[0] = alphaChars.charAt(check_info[1]-1);
            //alert('va '+values);
        });
        break;
    //----------//
case 'C':
    var count = $('#answersCount').val();
    var val = 0;
    var id ='';
    for(var i=0;i<count; i++){
		id =  '#ITS_TA'+(i);
		values[i]=$(id).val();  
		if($(id).val()==''){
			isempty_c=false;
		}
	}
    break;
//----------//
case 'P':
//----------//
var val = $('#ITS_TA').val();
values[0] = val;
break;			
default:
}
var chosen = values.join();     
//alert(chosen);
if (chosen&&isempty_c) {
var c = $(this).attr("c");
//alert(ch+'~'+qid+'~'+qtype+'~'+chosen+'~'+c+'~'+t+'~ rate: '+rating+'~'+mode+'~'+tid);
switch (mode) {
case 'survey':
    $.get('ajax/ITS_screen.php', {
        ajax_args: "recordSurveyAnswer", 
        ajax_data: qid+'~'+qtype+'~'+chosen+'~'+c+'~'+ch+'~'+t+'~'+mode+'~'+tid
    }, function(data) {
        $('#contentContainer').html(data);
        imageBox();
        mathJax();
    });
    break;
default:
    //alert(qid+'~'+qtype+'~'+chosen+'~'+c+'~'+ch+'~'+t+'~'+mode+'~'+tid);
    /*
    $.get('ajax/ITS_screen.php', {
        ajax_args: "recordChapterAnswer", 
        ajax_data: qid+'~'+qtype+'~'+chosen+'~'+c+'~'+ch+'~'+t+'~'+mode+'~'+tid
    }, function(data) {
        //alert($(this).data('chosen'));
        $('#contentContainer').html(data);
        $("#ITS_rate").css({display: 'block'});
        $('#ITS_nav_next').css({display: 'block'});
        ratingUPDATE(qid);
        imageBox();
        mathJax();
        //$("#dialog").dialog({ autoOpen: false, resizable: false, width:425 });
    });			*/
    //----------
    $.ajax({
     async: false,
     type: 'GET',
     url: 'ajax/ITS_screen.php',
     data: {ajax_args: "recordChapterAnswer", ajax_data: qid+'~'+qtype+'~'+chosen+'~'+c+'~'+ch+'~'+t+'~'+mode+'~'+tid},
     success: function(data) {
        $('#contentContainer').html(data);
        $("#ITS_rate").css({display: 'block'});
        $('#ITS_nav_next').css({display: 'block'});
        ratingUPDATE(qid);
        imageBox();
        mathJax();
     }
});
    				
}	

if (mode=="concept"){ 	
	var uid = $('#logout').attr("uid");
	var term = $('#logout').attr("course");
	//alert(uid+'~'+term+'~'+tid);
    $.get('ajax/ITS_concepts.php', {
        choice: "updateConceptInfo", 
        tid: tid
    }, function(data) {
        $('.navConceptInfo').html(data);
    });	
}
//--- update Scores ---//
$.get('ajax/ITS_screen.php', {
ajax_args: "updateScores", 
ajax_data: ''
}, function(data) {
$('#scoreContainerContent').html(data);
		  var tbid = '#a_4_'+Number(Number(ch)+1);
          $(tbid).html($(tbid).html()).parent().css({background: '#ff9'}).delay(3000);
});
//---
/*
$.ajax({
     async: false,
     type: 'GET',
     url: 'ajax/ITS_screen.php',
     data: {ajax_args: "updateScores", ajax_data: ''},
     success: function(data) {
		  var tbid = '#a_4_'+Number(Number(ch)+1);
          $('#scoreContainerContent').html(data); 
          $(tbid).html($(tbid).html()+'<br>'+$.now());
          //css({background: '#fff999'}).delay(3000);
     }
});*/
//---
} 
else {
$('#errorContainer').html('Please select an answer.').css({display: 'inline'});
//*** Prevent Multiple submissions: 1. Client - No ans selected => re-enable
$(this).attr('disabled', false);
}
});
//$("#dialog").dialog('close');
/*
                  var myans = Array();
                                    myans = $('input.ansCheck:checkbox').attr("checked",true).attr("id");
                 alert(myans.length);
            var qid   = $(this).attr("qid");
            var qtype = $(this).attr("qtype");
            var L = $("li[name=matchingLeft]").length;                 // no. left choices
            var M = $("INPUT[@name='check'][type='checkbox']").length; // no. matrix elements
            var values = new Array();

            for (var r = 0; r < M/L; r++) {
                for (var c = 1; c <= L; c++) {
                  if ($('#check_'+(r+1)+'_'+c).attr("checked")) { values[r] = c; }
                }
                if (values[r] == null) { values[r] = ''; }
            }
            var chosen = values.join();
                                    //alert(qid+'~'+qtype+'~'+chosen);

            $.get('ajax/ITS_screen.php', { ajax_args: "recordChapterAnswer", ajax_data: qid+'~'+qtype+'~'+chosen}, function(data) {
                                        //alert($(this).data('chosen'));
                $('#contentContainer').html(data);
                                                    $('.check').button();  			
                                                    $('.ui-widget-content .ui-state-default').css({background: '#ffffff', border: '2px solid #cccccc',color: '#080000',cursor: "default"});     
                                                    $('.ui-button').css({ width: '20px', height: '20px' });
                      $('.ui-button-text-only .ui-button-text').css({ padding: '0.1em' });   
            })*/
/*-------------------------------------------------------------------------*/	 
$('#ITS_skip').live('click', function() {
    var ch     = $(this).attr("ch");
    var qid    = $(this).attr("qid");
    var qtype  = $(this).attr("qtype");
    var t      = $(this).attr("t");
    var mode   = $(this).attr("mode");
    
    if (mode=="concept"){ var tid = $('.navConcept').attr("tid"); }
	else { tid = ''; }

//alert(ch+' '+qid+' '+qtype+' '+t+' '+mode+'~'+tid);
$.get('ajax/ITS_screen.php',{
	ajax_args: "skip", 
	ajax_data: qid+'~'+qtype+'~'+ch+'~'+t+'~'+mode+'~'+tid
	}, function(data) {
		$('#contentContainer').html(data); 
		$.get('ajax/ITS_screen.php',{
		ajax_args: "concept", 
		ajax_data: qid
		},
		function(data){
			$('#QuesConcept').html(data);
		});
		imageBox();
		mathJax();
	});
});
/*-------------------------------------------------------------------------*/
/*
$('#nav1').live('click', function() {
	alert($('#nav1 > li').html());
var mySelect = $('#nav1');
$.each(myOptions, function(val, text) {
    mySelect.append(
        $('<option></option>').val(val).html(text)
    );
});
});*/
/*-------------------------------------------------------------------------*/
 /*       $('#resBoxContainer').live('click', function() {
            $.get('ajax/ITS_screen.php', {
                ajax_args: "getResource", 
                ajax_data: ''
            }, function(data) {
                //alert($(this).data('chosen'));
                $('#resBoxContainer').html(data);  
            });
        });*/
/*-------------------------------------------------------------------------*/
});

//*****************************************//
function chUPDATE(ch,chhide,v) {
//*****************************************//
//alert(ch+'-'+chhide);
                $('.chapter_index').each(function(index) {
                    // update style: closed chapters invisible
                    if (v) {
                        if ( (index) < chhide ){
                            if ( ch == (parseInt(index+1)) ){ ch=parseInt(chhide)+1; } 
                        }
                    }
                    //alert('text: '+$(this).text() +'='+ ch);
                });
  return ch;              
}
//*****************************************//
function indexUPDATE(ch,v,mode) {
//*****************************************//
var idx_hide = $('#chList').attr('ih');
var r = $('#Question').attr('r');
//alert('DEBUG: "ch" '+ch+', "view Q?" '+v+' , "hide" '+idx_hide+', "role" '+r+', "mode" '+mode);

$('.chapter_index').each(function(index) {
// INDEX HIDE
if (r==0){
switch(mode) {
    case 'Question':
        //alert(mode);
        //alert(index +'=='+ idx_hide);
        if ( (index) < idx_hide ){
            $(this).css({display: 'none'});
            //alert(ch +'=='+ (parseInt(index+1)));
            //if ( ch == (parseInt(index+1)) ){ ch=parseInt(chhide)+1; }
        }
        else {$(this).css({display: 'inline'});}
        break;
    case 'Practice':
        if ( (index) >= idx_hide ){$(this).css({display: 'none'});}
        else 						{$(this).css({display: 'inline'});}
        if (ch > idx_hide) { ch = idx_hide; }
        break;
    case 'Review':
        //alert(index+ ' - ' + idx_hide);
        $(this).css({display: 'inline'});
        //if (ch > idx_hide) { ch = idx_hide; }
        break;
    default:
        alert('ITS_jquery.php: error');
}
} 
else { $(this).css({display: 'inline'}); }

if ($(this).text()==ch){$(this).attr('id','current');}
else 				   {$(this).attr('id','');}

});
}
//*****************************************//
function navUPDATE(ch) {
//*****************************************//
// alert('navUPDATE: '+ch);

$('.ITS_navigation[name=updateReview_index]').attr("ch",ch);
var qIdx = $('#slider').slider("option","value");
var qMax = $('#slider').slider("option","max");

//alert('navUPDATE: '+ch+' '+qIdx+' '+qMax);		        
if ( qIdx >= qMax ) { $('#ITS_nav_next').css({display: 'none'});  }
else                { $('#ITS_nav_next').css({display: 'block'}); }

if ( qIdx < 2 )    { $('#ITS_nav_prev').css({display: 'none'});  }
else               { $('#ITS_nav_prev').css({display: 'block'}); }

// hide counter if no questions
if (qMax < 1){ $( "#reviewNavTxt" ).css({display: 'none'});}
else { $( "#reviewNavTxt" ).css({display: 'block'});}
// slider visible for 5 questions or more
if (qMax < 5){ 
$( "#slider" ).css({display: 'none'});
}
else { 
$( "#slider" ).css({display: 'block'});
}
}
//*****************************************//
function ratingUPDATE(qid) { 
//*****************************************//
//alert('ratingUPDATE('+qid+')');
$("#ITS_rate").children().not(":radio").hide();
$("#ITS_rate").stars({disabled: true,captionEl: $("#stars-cap")});
$("#ITS_rate").css({display:'block'});

var ui = $("#ITS_rate").data("stars");

// IF NO RATING: enable for rating 
if (ui.options.value == 0) { 
$("#ITS_rate").stars({
captionEl: $("#stars-cap"),
disabled: false,
callback: function(ui, type, value){ 
if (value) {
    //alert('qid: '+qid+' rate: '+value);
    //--- update rating ---//
    $.get('ajax/ITS_screen.php', {
        ajax_args: "recordRating", 
        ajax_data: qid+'~'+value
    }, function(data) {
    });
}
}			
});
}  
}
//*****************************************//
function slider(ch,qN) { 
//*****************************************//
                $( "#slider" ).slider({
                    value: qN,
                    min: 1,
                    max: qN,
                    step: 1,
                    slide: function( event, ui ) { $( "#reviewNavTxt" ).html( ui.value + " / " + $(this).slider("option","max")) },
                    stop: function( event, ui ) { $.get('ajax/ITS_screen.php', {
                            ajax_args: "reviewUpdate",
                            ajax_data: ch+','+(ui.value-1)
                        }, function(data) {
                            //$('#ITS_meta').html(data);
                            //alert(ui.value);
                            ch = $('.chapter_index#current').text();
                            navUPDATE(ch); // Need to update slider first

                            $('#N2').html(data);
                            imageBox();
                            mathJax();
                            //$("#ITS_rate").css({display: 'block'});
                            var qid = $('#ratingContainer').attr('qid');
                            //alert(ch+' | '+qN+' | '+qMax+' | qid='+qid);
                            ratingUPDATE(qid);
                        }); }

                });
}
//*****************************************//
function imageBox() { 
//*****************************************//
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
}
//*****************************************//
function mathJax() { 
//*****************************************//
MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
}
//*****************************************//
function getMSG() { 
//*****************************************//
  //var msg = '<h1 style="text-align:left"><ul><li>ITS has closed for graded questions.</li><li><b>Practice</b> section allows for unlimited viewing and does not contribute towards your score.</li><li>Your answers for Chapter 1-7, 9 & 11 are available for review.</li></h1>';				
  var msg = '<div class="ITS_MESSAGE"><ul><li>ITS Assignments 1 - 4 <br>have closed.</li><li>Your answers <br> are available for review.</li></div>';
  $('#contentContainer').html(msg);
}
//*****************************************//
</script>
