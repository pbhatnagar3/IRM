<script type="text/javascript">
$(document).ready(function () {
    /*-------------------------------------------------------------------------*/
function doChange(){alert("change")}$(".tagref").live("click",function(){var e=$(this).attr("tid");var t=$(this).html();$.get("ajax/ITS_tag.php",{ajax_args:"practiceMode",ajax_data:e+","+t},function(e){$("div.taginfo").html(e)});if($("div.taginfo").is(":hidden")){$("div.taginfo").slideDown("slow")}else{$("div.taginfo").hide()}});$("#selectQtype").change(function(){doChange()}).attr("onchange",function(){doChange()});$(".ITS_select").change(function(){document.profile.submit()});$("#scoreContainer").click(function(){$("#scoreContainerContent").slideToggle("slow")});$("#usersContent").hide();$("#usersContainerToggle").live("click",function(){$("#usersContent").slideToggle("slow")});$("#metaContainer").hide();$("#metaContainerToggle").live("click",function(){$("#metaContainer").slideToggle("slow")});$("#tagContainerToggle").live("click",function(){$("#tagContainer").slideToggle("slow")});$("#toolsContainer").hide();$("#toolsContainerToggle").live("click",function(){$("#toolsContainer").slideToggle("slow")});$("#feedbackContainer").click(function(){$("#feedbackContainerContent").slideToggle("slow")})  
    <?php
// This is php is for the live script used on Question.php to either show the solution when navigated from the Course page or not.
if (isset($_GET['sol'])) {
    echo '
		$("#solutionContainer").live("click", function() {
			$("#results").slideToggle("slow");
			var hasaPost = 1;
			var viewSolution = 1;
			var QNUM = $("#ITS_QCONTROL_TEXT").attr("value");
			var dataString = "hasaPost="+ hasaPost + "&viewSolution=" + viewSolution + "&QNUM=" + QNUM;
			$.ajax({
				type: "GET",
				url: "solutions.php",
				data: dataString,
				success: function( html) {
						
						$("#results").html(html);
						
				}
			});
			return false;
		});
		$(document).ready(function() {
			var hasaPost = 1;
			var viewSolution = 1;
			var QNUM = $("#ITS_QCONTROL_TEXT").attr("value");
			var dataString = "hasaPost="+ hasaPost + "&viewSolution=" + viewSolution + "&QNUM=" + QNUM;
			$.ajax({
				type: "GET",
				url: "solutions.php",
				data: dataString,
				success: function( html) {
						
						$("#results").html(html);
						$("#results").show();
				}
			});
		});
		';
} else {
    echo '
		$("#solutionContainer").live("click", function() {
			$("#results").slideToggle("slow");
			var hasaPost = 1;
			var viewSolution = 1;
			var QNUM = $("#ITS_QCONTROL_TEXT").attr("value");
			var dataString = "hasaPost="+ hasaPost + "&viewSolution=" + viewSolution + "&QNUM=" + QNUM;
			$.ajax({
				type: "GET",
				url: "solutions.php",
				data: dataString,
				success: function( html) {
						
						$("#results").html(html);
				}
			});
			return false;
		});
		';
}
?>
$("#hintContainer").live("click",function(){$("#hintResults").slideToggle("slow");var e=1;var t=1;var n=$("#ITS_QCONTROL_TEXT").attr("value");var r="hasaPost="+e+"&viewHints="+t+"&QNUM="+n;$.ajax({type:"GET",url:"solutions.php",data:r,success:function(e){$("#hintResults").html(e)}});return false})
$("#solContainer").live("click",function(){$("#solResults").slideToggle("slow");var e=1;var t=1;var n=$("#ITS_QCONTROL_TEXT").attr("value");var r="hasaPost="+e+"&viewSol="+t+"&QNUM="+n;$.ajax({type:"GET",url:"solutions.php",data:r,success:function(e){$("#solResults").html(e)}});return false})
function doChange1(){}$("#tag_check").live("click",function(e){if($(this).is(":checked")){$("#tagContainer").slideDown("slow")}else{$("#tagContainer").slideUp("slow")}});$("input[name='question_nav']").live("click",function(e){if($("#tag_check").is(":checked")){$("#tagContainer").css("display","inline")}else{$("#tagContainer").hide()}});$("#testme2").live("click",function(e){$("#ImgDlg").dialog("close");var t=17});$("#deleteButton").live("click",function(e){var t=$(this).attr("uid");$("#addQuestionDialog:ui-dialog").dialog("destroy");$("#addQuestionDialog").dialog({resizable:false,height:300,modal:true,buttons:{"Add Question":function(){$(this).dialog("close");$.get("ajax/ITS_admin.php",{ajax_args:"AddQuestionDialog",ajax_data:t},function(e){})},Cancel:function(){$(this).dialog("close")}}})});$("#title").live("mouseover",function(e){});$("#createQuestion").live("click",function(e){var t=$(this).attr("qtype");$.get("ajax/ITS_admin.php",{ajax_args:"createQuestion",ajax_data:"new~"+t},function(e){$("#ITS_question_container").append(e);$("#dialog-form").css("display","block");$("li[name='qtype']").each(function(e){if($(this).attr("qtype")==t){$(this).children("a").attr("id","current")}else{$(this).children("a").attr("id","")}})})});$("li[name='qtype']").live("click",function(e){var t=$(this).attr("qtype");$("li[name='qtype']").each(function(e){if($(this).attr("qtype")==t){$(this).children("a").attr("id","current")}else{$(this).children("a").attr("id","")}});var n,r,i;var s="text ui-widget-content ui-corner-all ITS_Q";var o="<b>ANSWERS:</b><br>";var u='<input type="button" name="changeAnswer" id="remAnswer" v="-" value="—" class="ITS_buttonQ" /><br>'+'<input type="button" name="changeAnswer" id="addAnswer" v="+" value="+" class="ITS_buttonQ">';switch(t){case"mc":i=4;r='<table id="ITS_Qans" class="ITS_Qans" n="'+i+'" qtype="'+t+'">';for(n=1;n<=i;n++){r+="<tr>"+"<td>"+n+"</td>"+'<td><textarea name="answer'+n+'" id="answer'+n+'" class="'+s+'"></td>'+'<td><textarea type="text" name="weight'+n+'" id="weight'+n+'" class="'+s+'"></textarea></td>'+"</tr>"}r+="</table>"+'<textarea type="hidden" name="answers" id="answers" value="'+i+'">';r="<td>"+o+u+"</td>"+"<td>"+r+"</td>";break;case"m":i=4;r='<table id="ITS_Qans" class="ITS_Qans" n="'+i+'" qtype="'+t+'">';for(n=1;n<=i;n++){r+="<tr>"+"<td>"+n+'</td><td><textarea name="L'+n+'" id="answer'+n+'" class="'+s+'"></td>'+"<td></td>"+'<td><textarea name="R'+n+'" id="R'+n+'" class="'+s+'"></textarea></td>'+"</tr>"}r+="</table>"+'<input type="hidden" name="answers" id="answers" value="'+i+'" />';r="<td>"+o+u+"</td>"+"<td>"+r+"</td>";break;case"c":o="<b>Number of Variables:</b><br>";i=1;r='<table id="ITS_Qans" class="" n="'+i+'" qtype="'+t+'">'+"<tr><td><LABEL>Number of Formulas</LABEL></td>"+'<td><input type="button" value="+" id="add_fcount" class="ITS_buttonQ"></td><td><input type="button" id="dec_fcount" value="-" class="ITS_buttonQ"></td>'+'<td  width="90%">Weights must sum up to 100</td></tr>';r+='<tr><input type="hidden" name="answers" id="answers" value="1" /></tr>';r+='<tr id="tr_formula1"><td width="10%"><label for="text1">text 1</label></td>'+'<td width="30%" ><textarea name="text1" id="text1" value="" class="'+s+'" /></td>'+'<td width="10%"><label for="formula1">formula 1</label></td>'+'<td width="30%" colspan="4"><textarea name="formula1" id="formula1" value="" class="'+s+'" /></td>'+'<td width="10%"><label for="weight1">Weight 1</label></td><td width="20%"><input type="text" class="'+s+'" MAXLENGTH=3 name="weight1" id="weight1"></td>'+"</tr>";r+='<tr><td><input type="hidden" name="vals" id="vals" value="'+i+'" /></td></tr>';for(n=1;n<=i;n++){r+="<tr>"+'<td width="10%"><label for="value'+n+'">value '+n+"</label></td>"+'<td width="40%"><textarea type="text" name="val'+n+'" id="answer'+n+'" value="" class="'+s+'" /></textarea></td>'+'<td width="10%"><label for="minvalue'+n+'">min</label></td>'+'<td width="10%"><textarea type="text" name="min_val'+n+'" id="minvalue'+n+'" value="" class="'+s+'"/></textarea></td>'+'<td width="10%"><label for="maxvalue'+n+'">max</label></td>'+'<td width="10%"><textarea type="text" name="max_val'+n+'" id="maxvalue'+n+'" value="" class="'+s+'"/></textarea></td>'+"</tr>"}r+="</table>";r="<td>"+o+u+"</td>"+"<td>"+r+"</td>";break;case"s":i=1;r='<table id="ITS_Qans" class="ITS_Qans" n="'+i+'" qtype="'+t+'">'+"<tr>"+'<td width="10%"><label for="answer'+i+'">answer '+i+"</label></td>"+'<td width="85%"><textarea name="ans" id="answer" value="" class="'+s+'" /></td>'+"</tr>"+"</table>";r="<td>"+o+u+"</td>"+"<td>"+r+"</td>";break;case"p":i=1;r='<table id="ITS_Qans" class="ITS_Qans" n="'+i+'" qtype="'+t+'">'+"<tr>"+'<td width="10%"><label for="template">template</label></td>'+'<td width="85%"><textarea name="template" id="template" value="" class="'+s+'" /></td>'+"</tr>"+"<tr>"+'<td width="10%"><label for="answer">answer</label></td>'+'<td width="85%"><textarea name="answer" id="answer" value="" class="'+s+'" /></td>'+"</tr>"+"</table>";r="<td>"+o+"</td><td>"+r+"</td>";break}$("#ansQ").html(r)});$("#add_fcount").live("click",function(){var e="text ui-widget-content ui-corner-all ITS_Q";var t=parseInt($("#answers").val());var n=t+1;if(n<=4){$("#answers").val(n);var r='<tr id="tr_formula'+n+'">'+"<td>"+n+'</td><td><textarea name="text'+n+'" id="text'+n+'" class="'+e+'"></textarea></td>'+'<td><textarea name="formula'+n+'" id="formula'+n+'" class="'+e+'"></textarea></td>'+'<td><textarea type="text" name="weight'+n+'" class="'+e+'" id="weight'+n+'"></textarea></td>'+"</tr>";$("#tr_formula"+t).after(r)}});$("#dec_fcount").live("click",function(){var e="text ui-widget-content ui-corner-all ITS_Q";var t=parseInt($("#answers").val());var n=t-1;if(n>0){$("#answers").val(n);$("#formula"+t).remove()}});$("input[name=changeAnswer]").live("click",function(event){var css="text ui-widget-content ui-corner-all ITS_Q";var qtype=$("#ITS_Qans").attr("qtype");var n=Number($("#ITS_Qans").attr("n"));var v=$(this).attr("v");var td="";var n1=n+1;switch(v){case"+":if(n<23){switch(qtype){case"mc":td="<td>"+n1+"</td>"+'<td><textarea name="answer'+n1+'" id="answer'+n1+'" class="'+css+'" /></textarea></td>'+'<td><textarea type="text" name="weight'+n1+'" id="weight'+n1+'" class="'+css+'" /></textarea></td>';break;case"m":td="<td>"+n1+"</td>"+'<td><textarea name="L'+n1+'" id="answer'+n1+'" class="'+css+'"></textarea></td>'+'<td><textarea name="R'+n1+'" id="R'+n1+'" class="'+css+'"></textarea></td>';break;case"c":td="<td>"+n1+"</td>"+'<td><textarea type="text" name="val'+n1+'" id="answer'+n1+'" value="" class="'+css+'"></textarea></td>'+'<td><textarea type="text" name="min_val'+n1+'" id="minvalue'+n1+'" class="'+css+'"></textarea></td>'+'<td><textarea type="text" name="max_val'+n1+'" id="maxvalue'+n1+'" class="'+css+'"></textarea></td>';$("#vals").attr("value",n1);break;case"s":td="<td>"+n1+"</td>"+'<td><textarea name="ans'+n1+'" id="answer'+n1+'" class="'+css+'"></textarea></td>';break}$("#ITS_Qans").append("<tr>"+td+"</tr>");$("#ITS_Qans").attr("n",eval(n+v+1))}break;default:if(n>1){$("#answer"+n).parent().parent().remove();$("#ITS_Qans").attr("n",eval(n+v+1));switch(qtype){case"mc":$("#vals").attr("value",n+v+1);break}}break}});$("#cloneQuestion").live("click",function(e){var t=$(this).attr("qid");var n=$(this).attr("qtype");$.get("ajax/ITS_admin.php",{ajax_args:"createQuestion",ajax_data:"clone~"+t},function(e){$("#ITS_question_container").append(e);$("#dialog-form").css("display","block");$("li[name='qtype']").each(function(e){if($(this).attr("qtype")==n){$(this).children("a").attr("id","current")}else{$(this).children("a").attr("id","")}})})});$("#deleteQuestion").live("click",function(e){var t=$(this).attr("qid");var n="mc";var r=$('<h2 style="color:#009">Delete Question <b><font color="red">'+t+"</font></b> ?</h2>").appendTo("body");r.dialog({resizable:false,height:160,modal:true,buttons:{"Delete Question":function(){$(this).dialog("close");$.get("ajax/ITS_admin.php",{ajax_args:"deleteQuestion",ajax_data:t+"~"+n},function(e){$("#ITS_question_container").html(e)})},Cancel:function(){$(this).dialog("close")}}})});$("#PreviewDialog").live("click",function(e){var t=$("#ITS_QCONTROL_TEXT").val();$.get("ajax/ITS_admin.php",{ajax_args:"PreviewDialog",ajax_data:t},function(e){$("#ITS_question_container").append(e);$(".inline").fancybox({type:"inline",closeClick:true,aspectRatio:true,autoSize:true,padding:3,helpers:{overlay:{closeClick:true,speedOut:300,showEarly:false,css:{background:"rgba(155, 155, 155, 0.5)"}},title:{type:"inside"}}})})});$("#ShowPreview").live("click",function(e){var t=$("#ITS_QCONTROL_TEXT").val();$.get("ajax/ITS_admin.php",{ajax_args:"PreviewOptions",ajax_data:t},function(e){var t=$(e).appendTo("#ITS_question_container");t.attr("id","TestDialog");t.dialog({show:"blind",hide:"slide",resizable:true,width:"80%",height:"auto",modal:false,close:function(){}})})});$("#calcResult").live("click",function(e){var t=$("#ITS_QCONTROL_TEXT").val();var n;var r=new Array;var i;var s=/^-?\d{0,5}(\.\d{1,3})?$/;for(var o=1;o<=$("#var_count").val();o++){i="#Variable"+o;r[o-1]=$(i).val();if($(i).val().length!=0&&$(i).val().match(s)){}else{$("#errorContainer").html("One or more inputs are invalid").css({display:"inline"});return false}}var u=r.join(",");$.get("ajax/ITS_admin.php",{ajax_args:"fixResult",ajax_data:t+"~"+u},function(e){n=e.split(",");var t="";for(var r=1;r<=n.length;r++){t="#Formula"+r;$(t).val(n[r-1])}})});$(".cp").live("click",function(){$("#errorContainer").css({display:"none"})});$("#submitDialog").live("click",function(e){var t=$("#ITS_Qans").attr("qtype");var n=$("#ITS_Qans").attr("n");if(t=="mc"||t=="m"){$("#answers").val(n)}var r=$("#Qform").serialize();r="qtype="+t+"&"+r;$.get("ajax/ITS_admin.php",{ajax_args:"addQuestion",ajax_data:r},function(e){$("#ITS_question_container").append(e)});$("#xxy").remove()});$("#ansUpdate").live("click",function(e){var t=$(this).attr("action");var n=$("#answers").attr("qid");var r=$("#answers").val();$.get("ajax/ITS_admin.php",{ajax_args:"editAnswers",ajax_data:n+"~"+t+"~"+r},function(e){$("#ITS_Qans").html(e)})});$("#cancelDialog").live("click",function(e){$("#Qform").remove()});$("#ITS_image_TARGET").live("click",function(e){});$("#tags").live("click",function(e){var t=$(this).attr("uid");$("#tagDialog:ui-dialog").dialog("destroy");$("#tagDialog").dialog({resizable:false,height:200,modal:true,buttons:{"Delete Now":function(){$(this).dialog("close");$.get("ajax/ITS_admin.php",{ajax_args:"deleteDialog",ajax_data:t},function(e){})},Cancel:function(){$(this).dialog("close")}}})});$("#QTIsubmit").live("click",function(e){});$("#importQuestion").live("click",function(e){$("#importQuestionContainer").css("display","inline")});$("a[name='ITS_EDIT_QCONTROL']").live("click",function(e){var t=$("textarea.ITS_EDIT").attr("id");var n=$("textarea#"+t).val();var r=renderQuestionAnswer(n);$("#"+t).attr("value",r)});$(".ITS_button[name='editMode']").live("click",function(e){$("span.ITS_QCONTROL").each(function(e){$(this).html('<div id="navEdit"><ul id="navlistEdit"><li><a href="#" name="ITS_EDIT" ref="'+$(this).attr("id")+'"> Edit </a></li></ul></div>');var t=$(this).attr("id");$("#"+t+"_TARGET").css({border:"2px dotted silver"})});$("span.ITS_ICONTROL").each(function(e){$(this).html('<div id="navEdit"><ul id="navlistEdit"><li><a href="#" name="ITS_IMG" ref="'+$(this).attr("ref")+'"> Image </a></li></ul></div>');var t=$(this).attr("id");$("#"+t+"_TARGET").css({border:"2px dashed #666",padding:"0.25em",overflow:"auto"})})})
$("a[name='ITS_EDIT']").live("click",function(e){var t=$(this).attr("ref");var n=t+"_TARGET";$("#"+n).css({border:"2px solid blue","background-color":"#FFFFFF"});var r=$("#"+n).attr("code");var i=$("#"+n).width();if(i<200){i=200}if(t=="ITS_QUESTION"){var s=120;if(i<800){i=800}}else{var s=40}if(t=="ITS_answers"){var o='<select class="ITS_EDIT" id="TXA_'+n+'"><option value="1">1</option><option value="2">2</option>'+'<option value="3">3</option><option value="4">4</option></select>'}else{var o='<textarea class="ITS_EDIT" id="TXA_'+n+'" style="height:'+s+"px;width:"+i+'px">'+r+"</textarea>"}$("#"+n).html(o);var u='<div id="navEdit"><ul id="navlistEdit"><li id="ITS_tagLATEX" class="ITS_addTAG" ref="'+t+'" tag=" <latex></latex>"><b><</b>latex<b>></b></li><li id="ITS_tagIMG" class="ITS_addTAG" ref="'+t+'" tag=" <image></image>" style="margin-bottom:20px"><b><</b>image<b>></b></li><li id="active"><a href="#" name="ITS_SAVE" ref="'+t+'"> Save </a></li><li><a href="#" name="ITS_CANCEL" ref="'+t+'"> Cancel </a></li></ul></div>';$("#"+t).html(u);$("textarea.ITS_EDIT").resizable({handles:"se"})});$("a[name='ITS_SAVE']").live("click",function(e){var t=$(this).attr("ref");var n=t+"_TARGET";var r=$("#TXA_"+n).val();$("#"+n).attr("code",r);var i=htmlEncode(r);var s=$("#ITS_QCONTROL_TEXT").val();var o=t.replace(/ITS_/,"");var u=s+",SAVE,"+o;$.get("ajax/ITS_control.php",{ajax_args:u,ajax_data:r},function(e){$("#"+n).html(e);mathJax()});var a='<div id="navEdit"><ul id="navlistEdit"><li><a href="#" name="ITS_EDIT" ref="'+t+'"> Edit </a></li></ul></div>';$("#"+t).html(a)});$("a[name='ITS_CANCEL']").live("click",function(e){var t=$(this).attr("ref");var n=t+"_TARGET";var r=$("#"+n).attr("code");var i=$("#tex_path").val();var s=r.replace(/<latex>\s*([^>]*)?<\/latex>/gi,"\\($1\\)");$("#"+n).html(s).css({border:"2px dotted silver"});var o='<div id="navEdit"><ul id="navlistEdit"><li><a href="#" name="ITS_EDIT" ref="'+t+'"> Edit </a></li></ul></div>';$("#"+t).html(o);mathJax()});$("a[name='ITS_IMG']").live("click",function(e){var t=$("#ITS_QCONTROL_TEXT").attr("value");var n=$(this).attr("ref");var r=$('<div id="ImgDlg" style="ITS" fld="'+n+'"></div>').appendTo("body");r.dialog({title:"UPLOAD IMAGE FROM:",resizable:true,width:"auto",height:"auto",modal:false,buttons:{"My Computer":function(){$("#ImgDlg").html('<center><form name="ITS_file" action="ajax/ITS_image.php" enctype="multipart/form-data" method="POST"><input id="ITS_image_file" name="ITS_image" size="10" type="file"><input id="ITS_image_upload" name="upload" value="Upload" type="submit"><input type="hidden" name="qid" value="'+t+'"><input type="hidden" name="fld" value="'+n+'"></form></center>');$("#ImgDlg").dialog("option","title","From My Computer:")},SERVER:function(){window.location.replace("Image.php?id="+t+"&f="+n)},"delete":function(){$("#ImgDlg").html('<center><input name="ITS_image_delete" value="Yes, delete it" type="button" qid="'+t+'" fld="'+n+'"><input name="ITS_image_delete" value="No" type="button">');$("#ImgDlg").dialog("option","title","Delete this image from question?")},Cancel:function(){$(this).dialog("close")}}})});$("#ITS_image_upload").live("click",function(e){$("#ImgDlg").dialog("close")});$('input[name="ITS_image_delete"]').live("click",function(e){if($(this).val()=="Yes, delete it"){var t=$(this).attr("qid");var n=$(this).attr("fld");$.get("ajax/ITS_image.php",{ajax_args:"delete",ajax_data:t+"~"+0+"~"+n},function(e){$("#ImgDlg").dialog("close");window.location.replace("Question.php?qNum="+t)})}else{$("#ImgDlg").dialog("option","title","Image:").html("")}});$("span.ITS_concept").live("click",function(e){var t=$(this).attr("id");$(this).append("<div> SOME MATH </div>")})
});
function mathJax(){MathJax.Hub.Queue(["Typeset",MathJax.Hub])}function htmlEncode(e){return $("<div/>").text(e).html()}function htmlDecode(e){return $("<div/>").html(e).text()}function renderQuestionAnswer(e){var t="";var n='<img src="/cgi-bin/mimetex.exe?(.*?)">';var r=e.match(n);if(r!=null){var i="<latex>"+r[0].slice(31,-2)+"</latex>";var s=new RegExp(n);var e=e.replace(s,i)}else{}return e}$.fn.insertAtCaret=function(e){return this.each(function(){if(document.selection){this.focus();sel=document.selection.createRange();sel.text=e;this.focus()}else if(this.selectionStart||this.selectionStart=="0"){startPos=this.selectionStart;endPos=this.selectionEnd;scrollTop=this.scrollTop;this.value=this.value.substring(0,startPos)+e+this.value.substring(endPos,this.value.length);this.focus();this.selectionStart=startPos+e.length/2;this.selectionEnd=startPos+e.length/2;this.scrollTop=scrollTop}else{this.value+=e;this.focus()}})}
/*------------------------------------------ */
</script>																																																											