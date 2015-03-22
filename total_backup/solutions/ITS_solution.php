<?php
//=====================================================================//
/*
 * http://localhost/ITS-GT/solutions.php?hasaPost=1&viewSolution=1&QNUM=3481
 * http://localhost/ITS-GT/solutions.php?qNum=3481&Num=1&stype=Hint&Add=1&hasaPost=1&editor=1
  ITS_solution class - Solutions.

  Constructor: ITS_solution(*)

  Methods: 
  * showIndex()
  * viewSolution($QNUMVAR)
  * enteredFromIndex($QNUMVAL, $HintVAL, $SolutionVAL, $DetailedVAL)
  * solutionEditor($qNumVAL, $NumVAL, $stypeVAL, $IDVAL, $addVAL)
  * enteredFromEditor($idNumVAL, $qNumVAL, $stypeVAL, $DelVAL, $textVAL)
  * enterRatings($newRatingVAR, $IDVAR, $qNumVAR, $VerVAR)
  * addHint($QNUMVAL, $HintVAL) - old
  * addSolution($QNUMVAL, $SolutionVAL) - old
  * addDetailed($QNUMVAL, $DetailedVAL) - old
  * deleteSol($idNum) - old
  * addGeneralSol($qNum, $stype, $textVAL) - old
  * updateSol($qNum, $idNum, $stype, $textVAL) - old
  * viewHints($QNUMVAR) - old
  * viewSols($QNUMVAR) - old

  HELPER FUNCTIONS:
  * latexCheck($str,$path)
  * solutionStyleCheck($thenumber)

  ex. $ITS_solution = new ITS_solution($id);

  Author(s): Drew Boatwright  | May-11-2012
  Last Revision: May-17-2012, Drew Boatwright
*/
//=====================================================================//

class ITS_solution {
	
	function __construct($id){
	//=============================================================//
		global $db_dsn, $db_name, $tb_name, $host;
		
		$this->sessionId = $id;
		
		$this->db_name = $db_name; //Database name
		$this->tb_name = $tb_name; //Table name
		$this->host = 'localhost'; //Host
		$dsn = preg_split("/[\/:@()]+/",$db_dsn);
		
		$this->db_user = $dsn[1]; //Database username
		$this->db_pass = $dsn[2]; //Database pass
		
		$this->con = mysql_connect($this->host,$this->db_user,$this->db_pass) or die('Could not Connect to DB');
		mysql_select_db($this->db_name, $this->con) or die('Could not select DB');
		
	}
	
	//=============================================================//
	// Show Index
	// Description: Shows the Index for the Solutions page by showing
	// The dropdown list of questions with solutions and showing the
	// form to insert solutions
	//
	// Similar to index.php
	// ********THIS FUNCTION IS NOT USED ANYMORE********
	//=============================================================//
	function showIndex(){
		//Get Unique Questions with Solutions
		$query = 'SELECT DISTINCT question_id FROM solutions ORDER BY question_id ASC;';
		$res = mysql_query($query);
		$numrows = mysql_num_rows($res);
		
		//Drop Down List
		echo '<form action="solutions.php" method="get">';
		echo '<input type="hidden" name="hasaPost" value="1">';
		echo '<input type="hidden" name="viewSolution" value="1">';
		echo '<div align="center">';
		echo '<select name="QNUM">';
		
		for($N = 0; $N < $numrows; $N++) {
			$qNum = mysql_fetch_array($res, MYSQL_NUM);
		
			//CHECK IF WARMUP QUESTION
			$query1 = 'SELECT category FROM questions WHERE id = '.$qNum[0].';';
			$res1 = mysql_query($query1);
			$category = mysql_fetch_array($res1, MYSQL_NUM);
			$string = substr($category[0], 0, -2);
		
		
			////////////////////////////////////////////////////////////////////
			//Check Amount of Unrated and Verified Solutions for question
			$query2 = 'SELECT numratings,verified FROM solutions WHERE question_id = '.$qNum[0].';';
			$res2 = mysql_query($query2);
			$ROWZ = mysql_num_rows($res2);
			
			$NumRatings = 0;
			$Verified = 0;
		
			//Calculate
			for($i = 0; $i < $ROWZ; $i++) {
				$table2 = mysql_fetch_array($res2, MYSQL_NUM);
				if($table2[0] < 3) {
					$NumRatings += 1;
				}
				$Verified = $Verified + $table2[1];
			}
	
			$Verified = $Verified/$ROWZ;
			
			//MAKE LIST
			echo '<option value="'.$qNum[0].'" style="color: ';
			//VERIFIED?
			if($Verified == 1) {
				echo 'green;';
			}
			//RATINGS?
			else {
				if($NumRatings > 0) {
					echo 'red;';
				}
				else {
					//NORMAL
					echo 'black;';
				}
			}
			//Put Question_id
			echo '">'.$qNum[0];
			//FLAGS?
			if($string == 'Warmup') {
				echo ' -W';
			}
			//End FLAGS
			echo '</option>';
			////////////////////////////////////////////////////////////////////
		}
		echo '<input type="submit" value="Submit" />';
		echo '</div>';
		echo '</form>';
		
		echo'
		<div align="center">
		Key: <font color="red">Unrated Solutions</font> <font color="green">All Verified Solutions</font><br>
		Flags: -W = Warmup
		</div>

		<br>
		<hr>
		<center>Enter a Solution</center>
		<hr>

		<form action="solutions.php" method="post">
		<input type="hidden" name="hasaPost" value="1">
		<input type="hidden" name="enteredSolution" value ="1">
		Question Number: <input type="text" name="QNUM" size="6"> <br>
		Hint: <br> <textarea name="Hint" cols="50" rows="5"></textarea> <br>
		Solution: <br> <textarea name="Solution" cols="50" rows="5"></textarea> <br>
		Detailed: <br> <textarea name="Detailed" cols="50" rows="5"></textarea> <br>
		<input type="submit" value="Submit">
		</form>
		';
		
		return;
	}
	
	//=============================================================//
	// View solution of a question
	// Description: Given an input of the id of the question, this function
	// displays the question with which is the followed by the
	// Hints/Solutions/Detailed solutions with their corresponding ratings
	// and verifications
	//
	// *NOTE* This function is to be used in the Question.php page
	//
	// Similar to demo2.php
	//=============================================================//
	function viewSolution($QNUMVAR){
		
		//Set useful variables
		$qNum = $QNUMVAR;         // question number [in SQL: SELECT id from questions]
		$path = '/cgi-bin/mimetex.cgi?';
		$styleCount = 0; //Used for determing CSS style for solutions.
		$ratingFormCount = 0; //used for jQuery Stuff
		$verifyFormCount = 0; //used for jQuery stuff
		$solTextCount = 0; //used for jQuery stuff
		$enableEdit = 0; //used for jQuery stuff
		
		
		//====================================================
		echo '<br>';

		//################## END TITLE ##############
		
		// Grab Hints
		$noHint=0;
		$query = 'SELECT id,solution1,image1 FROM solutions WHERE questions_id = '.$qNum. ' ORDER BY rating1 DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
	 
		//Print Hints
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM); 
			$ID = $text[0];
			
			if($text[1] != NULL || $text[2] != NULL){ //|| $text[2] != NULL
			//TITLE AND EDIT BUTTON 
			//           action="solutions.php" method="get"
			echo '<form id="enableEdit'.$ID.'hint">';
			echo '<b>Hint #' .($N+1). '</b> <input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="ID" id="ID" value="'.$ID.'">';
			echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">'; 
			echo '<input type="hidden" name="stype" id="stype" value="Hint">';
			echo '<input type="submit" value="Edit">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value="1">';
			echo '</form>';
			echo '<script>
			$("body").delegate("#enableEdit'.$ID.'hint","submit", function(e){
				e.preventDefault();
				var oldText = $("#soltext'.$ID.'hint").html();
				$("#soltext'.$ID.'hint").hide();
				$("#soltext'.$ID.'hint").empty();
				var hasaPost = $("#enableEdit'.$ID.'hint input#hasaPost").val();
				var Num = $("#enableEdit'.$ID.'hint input#Num").val();
				var ID = $("#enableEdit'.$ID.'hint input#ID").val();
				var stype = $("#enableEdit'.$ID.'hint input#stype").val();
				var qNum = $("#enableEdit'.$ID.'hint input#qNum").val();
				var editor = $("#enableEdit'.$ID.'hint input#editor").val();
				//Inside this text needs to be the form from the editor page
				//$("#soltext'.$ID.'").prepend("Old Text: ");
				$("#soltext'.$ID.'hint").show();
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&ID=" + ID + "&qNum=" + qNum + "&editor=" + editor;
				//alert(dataString);return false;
				
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#soltext'.$ID.'hint").append(html);
						}
				});
				return false;
			});
			</script>
			';
			
		 
			//TEXT
			
			//This deals with if there is an image
			if ($text[2] != NULL){
				//echo 'not null';
				$query5 = 'SELECT name,dir FROM images WHERE id ='.$text[2].';';
				$res5 = mysql_query($query5) or die(mysql_error().' getting image1');
				$imtable = mysql_fetch_array($res2, MYSQL_NUM);
				$imtext = '<img src="'.$imtable[1].'/'.$imtable[0].'"><br>';
			}else{
				$imtext = '';
				//echo 'null';
			}
			
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan=2 class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'hint">'.$imtext.''.self::latexCheck($text[1], $path).'</div></th><br>';
			
			//GRAB RATING
			$query2 = 'SELECT rating1,rating1count,verified FROM solutions WHERE id ='.$ID.';';
			$res2 = mysql_query($query2) or die(mysql_error());
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
		 
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
			//          action="solutions.php" method="post"
			echo '<form id="ratingform'.$ID.'hint">';
			echo '<input type="hidden" name="qID"  id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated"  id="rated" value ="1">';
			echo '<input type="radio" name="rating" id="rating5" value="5"> 5  ';
			echo '<input type="radio" name="rating" id="rating4" value="4"> 4  ';
			echo '<input type="radio" name="rating" id="rating3" value="3"> 3  ';
			echo '<input type="radio" name="rating" id="rating2" value="2"> 2  ';
			echo '<input type="radio" name="rating" id="rating1" value="1"> 1  ';
			echo '<input type="submit" class="button2" value="Rate">';
			echo '</form>';
			echo '<div id="rateresult'.$ID.'hint"></div>';
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'hint">Verified!</div> <form id="verifyform'.$ID.'hint">';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'hint">Not Verified!</div> <form id="verifyform'.$ID.'hint">';
			}
			echo '<input type="hidden" name="qID" id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated" id="rated" value ="1">';
			if($verified == 1) {
				echo '<input type="hidden" name="rating" id="rating" value="6">';
				echo '<input type="submit" id="theButton" value="Un-Verify">';
			}
			else {
				echo '<input type="hidden" name="rating" id="rating" value="7">';
				echo '<input type="submit" id="theButton" value="Verify">';
			}
			echo '</form>';
			echo '<div id="verifyresult'.$ID.'hint"></div>';
			echo '</div></td></tr></table>';
			echo '<br>';
			//===================
			//This code block is dealing with jquery the rating
			echo '<script>
			$("body").delegate("#ratingform'.$ID.'hint","submit", function(e){
				e.preventDefault();
				$("#rateresult'.$ID.'hint").empty();
				$("#rateresult'.$ID.'hint").show();
				var hasaPost = $("#ratingform'.$ID.'hint input#hasaPost").val();
				var rated = $("#ratingform'.$ID.'hint input#rated").val();
				var rating = $("#ratingform'.$ID.'hint input:radio[name=rating]:checked").val();
				var qID = $("#ratingform'.$ID.'hint input#qID").val();
				//var qID = $(this).val();
				var qNum = $("#ratingform'.$ID.'hint input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol1";
				//alert(dataString);return false;
				
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#rateresult'.$ID.'hint").append(html);
								$("#rateresult'.$ID.'hint").fadeOut(1000);
								
						}
				});
				return false;
			});
			</script>
			';
			//===================
			//===================
			//This code block is dealing with jquery the verify
			echo '<script>
			$("body").delegate("#verifyform'.$ID.'hint","submit", function(e){
				e.preventDefault();
				$("#verifyresult'.$ID.'hint").empty();
				$("#verifyresult'.$ID.'hint").show();
				var hasaPost = $("#verifyform'.$ID.'hint input#hasaPost").val();
				var rated = $("#verifyform'.$ID.'hint input#rated").val();
				var qID = $("#verifyform'.$ID.'hint input#qID").val();
				var rating = $("#verifyform'.$ID.'hint input#rating").val();
				//var qID = $(this).val();
				var qNum = $("#verifyform'.$ID.'hint input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol1";
				//alert(dataString);return false;
	
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								var currVerIs = $("#currentVer'.$ID.'hint").html(); // This is dealing with the text
								if(currVerIs == "Verified!"){
									$("#currentVer'.$ID.'hint").html("Not Verified!");
								}else{
									$("#currentVer'.$ID.'hint").html("Verified!");
								}
								var currVerBut = $("#verifyform'.$ID.'hint input#rating").val(); //This is dealing with the button
								if(currVerBut == "6"){
									$("#verifyform'.$ID.'hint input#rating").val("7");
									$("#verifyform'.$ID.'hint input#theButton").val("Verify");
								}else{
									$("#verifyform'.$ID.'hint input#rating").val("6");
									$("#verifyform'.$ID.'hint input#theButton").val("Un-Verify");
								}
								$("#verifyresult'.$ID.'hint").append(html);
								$("#verifyresult'.$ID.'hint").fadeOut(1000);
						}
				});
				return false;
			});
			</script>
			';	
			//===================
			$styleCount++;
			$ratingFormCount++;
			$verifyFormCount++;
			$solTextCount++;
			$enableEdit++;
		}else{$noHint=1;}
		}
		//If no Hints
		if($noHint == 1 || $NumRows ==0) { //$noHint = 1 if there is a row in the solution table but the solutions are empty or "deleted" <strikethrough>--------OR there is an image with no solution text----------</strikethrough>
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=0><tr class="solution"><td class="solution">';
			echo '<div id="editHintBox'.$qNum.'">There are no hints for this question.<br>';
	 	
			// ADD BUTTON
			//          action="solutions.php" method="get"
			echo '<form id="enableaddedForm'.$qNum.'Hint">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">';
			echo '<input type="hidden" name="stype" id="stype" value="Hint">';
			echo '<input type="hidden" name="Add" id="Add" value="1">';
			echo '<input type="submit" value="Add">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value ="1">';
			echo '</form></div>';
			echo '<div id="enableaddedResult'.$qNum.'Hint"></div></td></tr></table>';
			//This script deals with jQuery Adding
			echo '<script>
			$("body").delegate("#enableaddedForm'.$qNum.'Hint","submit", function(e){
				e.preventDefault();
				//var oldText = $("#soltext'.$i.'").html();
				$("#editHintBox'.$qNum.'").hide();
				var hasaPost = $("#enableaddedForm'.$qNum.'Hint input#hasaPost").val();
				var Num = $("#enableaddedForm'.$qNum.'Hint input#Num").val();
				//var ID = $("#enableaddedForm'.$qNum.'Hint input#ID").val();
				var stype = $("#enableaddedForm'.$qNum.'Hint input#stype").val();
				var qNum = $("#enableaddedForm'.$qNum.'Hint input#qNum").val();
				var editor = $("#enableaddedForm'.$qNum.'Hint input#editor").val();
				var addvar = $("#enableaddedForm'.$qNum.'Hint input#Add").val();
				//Inside this text needs to be the form from the editor page
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&qNum=" + qNum + "&editor=" + editor + "&Add=" + addvar;
				//alert(dataString);return false;
	
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
							$("#enableaddedResult'.$qNum.'Hint").append(html);
						}
				});
				return false;
			});
			</script>
			';
			$styleCount++;
		}
		echo '<br>';
		
		mysql_free_result($res);
		
	 
		///////////////////////////////////////////////////////////////////
		//Grab Solutions
		$noSol=0;
		$query = 'SELECT id,solution2,image2 FROM solutions WHERE questions_id = '.$qNum.' ORDER BY rating2 DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
	 
		//Print Solutions
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM);
			$ID = $text[0];
			
			if($text[1] != NULL || $text[2] != NULL){ //|| $text[2] != NULL
			//TITLE AND EDIT BUTTON 
			//           action="solutions.php" method="get"
			echo '<form id="enableEdit'.$ID.'sol">';
			echo '<b>Solution #' .($N+1). '</b> <input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="ID" id="ID" value="'.$ID.'">';
			echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">';
			echo '<input type="hidden" name="stype" id="stype" value="Sol">'; 
			echo '<input type="submit" value="Edit">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value ="1">';
			echo '</form>';
			echo '<script>
			$("body").delegate("#enableEdit'.$ID.'sol","submit", function(e){
				e.preventDefault();
				var oldText = $("#soltext'.$ID.'sol").html();
				$("#soltext'.$ID.'sol").hide();
				$("#soltext'.$ID.'sol").empty();
				var hasaPost = $("#enableEdit'.$ID.'sol input#hasaPost").val();
				var Num = $("#enableEdit'.$ID.'sol input#Num").val();
				var ID = $("#enableEdit'.$ID.'sol input#ID").val();
				var stype = $("#enableEdit'.$ID.'sol input#stype").val();
				var qNum = $("#enableEdit'.$ID.'sol input#qNum").val();
				var editor = $("#enableEdit'.$ID.'sol input#editor").val();
				//Inside this text needs to be the form from the editor page
				//$("#soltext'.$ID.'").prepend("Old Text: ");
				$("#soltext'.$ID.'sol").show();
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&ID=" + ID + "&qNum=" + qNum + "&editor=" + editor;
				//alert(dataString);return false;
				
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#soltext'.$ID.'sol").append(html);
						}
				});
				return false;
			});
			</script>
			';
			
			// TEXT
			//This deals with if there is an image
			if ($text[2] != NULL){
				//echo 'not null';
				$query5 = 'SELECT name,dir FROM images WHERE id ='.$text[2].';';
				$res5 = mysql_query($query5) or die(mysql_error().' getting image1');
				$imtable = mysql_fetch_array($res2, MYSQL_NUM);
				$imtext = '<img src="'.$imtable[1].'/'.$imtable[0].'"><br>';
			}else{
				$imtext = '';
				//echo 'null';
			}
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan="2" class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'sol">'.$imtext.''.self::latexCheck($text[1], $path). '</div></th><br>';
			
			//GRAB RATING
			$query2 = 'SELECT rating2,rating2count,verified FROM solutions WHERE id='.$ID.';';
			$res2 = mysql_query($query2);
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
			
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
			
			echo '<form id="ratingform'.$ID.'sol">';
			echo '<input type="hidden" name="qID"  id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated"  id="rated" value ="1">';
			echo '<input type="radio" name="rating" id="rating5" value="5"> 5  ';
			echo '<input type="radio" name="rating" id="rating4" value="4"> 4  ';
			echo '<input type="radio" name="rating" id="rating3" value="3"> 3  ';
			echo '<input type="radio" name="rating" id="rating2" value="2"> 2  ';
			echo '<input type="radio" name="rating" id="rating1" value="1"> 1  ';
			echo '<input type="submit" class="button2" value="Rate">';
			echo '</form>';
			echo '<div id="rateresult'.$ID.'sol"></div>';
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'sol">Verified!</div> <form id="verifyform'.$ID.'sol">';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'sol">Not Verified!</div> <form id="verifyform'.$ID.'sol">';
			}
			echo '<input type="hidden" name="qID" id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated" id="rated" value ="1">';
			if($verified == 1) {
				echo '<input type="hidden" name="rating" id="rating" value="6">';
				echo '<input type="submit" id="theButton" value="Un-Verify">';
			}
			else {
				echo '<input type="hidden" name="rating" id="rating" value="7">';
				echo '<input type="submit" id="theButton" value="Verify">';
			}
			echo '</form>';
			echo '<div id="verifyresult'.$ID.'sol"></div>';
			echo '</div></td></tr></table><br>';
			//===================
			//This code block is dealing with jquery the rating
			echo '<script>
			$("body").delegate("#ratingform'.$ID.'sol","submit", function(e){
				e.preventDefault();
				$("#rateresult'.$ID.'sol").empty();
				$("#rateresult'.$ID.'sol").show();
				var hasaPost = $("#ratingform'.$ID.'sol input#hasaPost").val();
				var rated = $("#ratingform'.$ID.'sol input#rated").val();
				var rating = $("#ratingform'.$ID.'sol input:radio[name=rating]:checked").val();
				var qID = $("#ratingform'.$ID.'sol input#qID").val();
				//var qID = $(this).val();
				var qNum = $("#ratingform'.$ID.'sol input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol2";
				//alert(dataString);return false;
				
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#rateresult'.$ID.'sol").append(html);
								$("#rateresult'.$ID.'sol").fadeOut(1000);
								
						}
				});
				return false;
			});
			</script>
			';
			//===================
			//===================
			//This code block is dealing with jquery the verify
			echo '<script>
			$("body").delegate("#verifyform'.$ID.'sol","submit", function(e){
				e.preventDefault();
				$("#verifyresult'.$ID.'sol").empty();
				$("#verifyresult'.$ID.'sol").show();
				var hasaPost = $("#verifyform'.$ID.'sol input#hasaPost").val();
				var rated = $("#verifyform'.$ID.'sol input#rated").val();
				var qID = $("#verifyform'.$ID.'sol input#qID").val();
				var rating = $("#verifyform'.$ID.'sol input#rating").val();
				//var qID = $(this).val();
				var qNum = $("#verifyform'.$ID.'sol input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol2";
				//alert(dataString);return false;
	
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								var currVerIs = $("#currentVer'.$ID.'sol").html(); //This is dealing with the text
								if(currVerIs == "Verified!"){
									$("#currentVer'.$ID.'sol").html("Not Verified!");
								}else{
									$("#currentVer'.$ID.'sol").html("Verified!");
								}
								var currVerBut = $("#verifyform'.$ID.'sol input#rating").val(); //This is dealing with the button
								if(currVerBut == "6"){
									$("#verifyform'.$ID.'sol input#rating").val("7");
									$("#verifyform'.$ID.'sol input#theButton").val("Verify");
								}else{
									$("#verifyform'.$ID.'sol input#rating").val("6");
									$("#verifyform'.$ID.'sol input#theButton").val("Un-Verify");
								}
								$("#verifyresult'.$ID.'sol").append(html);
								$("#verifyresult'.$ID.'sol").fadeOut(1000);
						}
				});
				return false;
			});
			</script>
			';	
			//===================
			$styleCount++;
			$ratingFormCount++;
			$verifyFormCount++;
			$solTextCount++;
			$enableEdit++;
		}else{$noSol=1;}
		}
		//If no Solutions
		if($noSol == 1 || $NumRows ==0) { //$noSol = 1 if there is a row in the solution table but the solutions are empty or "deleted" <strikethrough>--------OR there is an image with no solution text----------</strikethrough>
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr class="solution"><td class="solution">';
			echo '<div id="editSolBox'.$qNum.'">There are no solutions for this question.<br>';
			
			// ADD BUTTON
			//          action="solutions.php" method="get"
			echo '<form id="enableaddedForm'.$qNum.'Sol">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			//echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">';
			echo '<input type="hidden" name="Num" id="Num" value="1">';
			echo '<input type="hidden" name="stype" id="stype" value="Sol">';
			echo '<input type="hidden" name="Add" id="Add" value="1">';
			echo '<input type="submit" value="Add">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value ="1">';
			echo '</form></div>';
			echo '<div id="enableaddedResult'.$qNum.'Sol"></div></td></tr></table>';
			echo '<script>
			$("body").delegate("#enableaddedForm'.$qNum.'Sol","submit", function(e){
				e.preventDefault();
				//var oldText = $("#soltext'.$i.'").html();
				$("#editSolBox'.$qNum.'").hide();
				var hasaPost = $("#enableaddedForm'.$qNum.'Sol input#hasaPost").val();
				var Num = $("#enableaddedForm'.$qNum.'Sol input#Num").val();
				//var ID = $("#enableaddedForm'.$qNum.'Sol input#ID").val();
				var stype = $("#enableaddedForm'.$qNum.'Sol input#stype").val();
				var qNum = $("#enableaddedForm'.$qNum.'Sol input#qNum").val();
				var editor = $("#enableaddedForm'.$qNum.'Sol input#editor").val();
				var addvar = $("#enableaddedForm'.$qNum.'Sol input#Add").val();
				//Inside this text needs to be the form from the editor page
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&qNum=" + qNum + "&editor=" + editor + "&Add=" + addvar;
				//alert(dataString);return false;
	
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
							$("#enableaddedResult'.$qNum.'Sol").append(html);
						}
				});
				return false;
			});
			</script>
			';
			$styleCount++;
		}
		echo '<br>';
		mysql_free_result($res);
		
		///////////////////
		//Grab Detailed
		$noDet = 0;
		$query = 'SELECT id,solution3,image3 FROM solutions WHERE questions_id = '.$qNum.' ORDER BY rating3 DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
	 
		//Print Detailed
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM);
			$ID = $text[0];
			
			if($text[1] != NULL || $text[2] != NULL){//|| $text[2] != NULL
			// TITLE AND EDIT BUTTON
			//           action="solutions.php" method="get"
			echo '<form id="enableEdit'.$ID.'det">';
			echo '<b>Detailed #' .($N+1). '</b> <input type="submit" value="Edit">';
			echo '<input type="hidden" name="ID" id="ID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">';
			echo '<input type="hidden" name="stype" id="stype" value="Det">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value ="1">'; 
			echo '</form>';
			echo '<script>
			$("body").delegate("#enableEdit'.$ID.'det","submit", function(e){
				e.preventDefault();
				var oldText = $("#soltext'.$ID.'det").html();
				$("#soltext'.$ID.'det").hide();
				$("#soltext'.$ID.'det").empty();
				var hasaPost = $("#enableEdit'.$ID.'det input#hasaPost").val();
				var Num = $("#enableEdit'.$ID.'det input#Num").val();
				var ID = $("#enableEdit'.$ID.'det input#ID").val();
				var stype = $("#enableEdit'.$ID.'det input#stype").val();
				var qNum = $("#enableEdit'.$ID.'det input#qNum").val();
				var editor = $("#enableEdit'.$ID.'det input#editor").val();
				//Inside this text needs to be the form from the editor page
				//$("#soltext'.$ID.'").prepend("Old Text: ");
				$("#soltext'.$ID.'det").show();
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&ID=" + ID + "&qNum=" + qNum + "&editor=" + editor;
				//alert(dataString);return false;
				
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#soltext'.$ID.'det").append(html);
						}
				});
				return false;
			});
			</script>
			';
		 
			// TEXT
			//This deals with if there is an image
			if ($text[2] != NULL){
				//echo 'not null';
				$query5 = 'SELECT name,dir FROM images WHERE id ='.$text[2].';';
				$res5 = mysql_query($query5) or die(mysql_error().' getting image1');
				$imtable = mysql_fetch_array($res2, MYSQL_NUM);
				$imtext = '<img src="'.$imtable[1].'/'.$imtable[0].'"><br>';
			}else{
				$imtext = '';
				//echo 'null';
			}
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan="2" class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'det">'.$imtext.''.self::latexCheck($text[1], $path). '</div></th><br>';
		 
			//GRAB RATING
			$query2 = 'SELECT rating3,rating3count,verified FROM solutions WHERE id ='.$ID.';';
			$res2 = mysql_query($query2);
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
		 
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
		 
			echo '<form id="ratingform'.$ID.'det">';
			echo '<input type="hidden" name="qID"  id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated"  id="rated" value ="1">';
			echo '<input type="radio" name="rating" id="rating5" value="5"> 5  ';
			echo '<input type="radio" name="rating" id="rating4" value="4"> 4  ';
			echo '<input type="radio" name="rating" id="rating3" value="3"> 3  ';
			echo '<input type="radio" name="rating" id="rating2" value="2"> 2  ';
			echo '<input type="radio" name="rating" id="rating1" value="1"> 1  ';
			echo '<input type="submit" class="button2" value="Rate">';
			echo '</form>';
			echo '<div id="rateresult'.$ID.'det"></div>';
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'det">Verified!</div> <form id="verifyform'.$ID.'det">';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'det">Not Verified!</div> <form id="verifyform'.$ID.'det">';
			}
			echo '<input type="hidden" name="qID" id="qID" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="rated" id="rated" value ="1">';
			if($verified == 1) {
				echo '<input type="hidden" name="rating" id="rating" value="6">';
				echo '<input type="submit" id="theButton" value="Un-Verify">';
			}
			else {
				echo '<input type="hidden" name="rating" id="rating" value="7">';
				echo '<input type="submit" id="theButton" value="Verify">';
			}
			echo '</form>';
			echo '<div id="verifyresult'.$ID.'det"></div>';
			echo '</div></td></tr></table><br>';
			//===================
			//This code block is dealing with jquery the rating
			echo '<script>
			$("body").delegate("#ratingform'.$ID.'det","submit", function(e){
				e.preventDefault();
				$("#rateresult'.$ID.'det").empty();
				$("#rateresult'.$ID.'det").show();
				var hasaPost = $("#ratingform'.$ID.'det input#hasaPost").val();
				var rated = $("#ratingform'.$ID.'det input#rated").val();
				var rating = $("#ratingform'.$ID.'det input:radio[name=rating]:checked").val();
				var qID = $("#ratingform'.$ID.'det input#qID").val();
				//var qID = $(this).val();
				var qNum = $("#ratingform'.$ID.'det input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol3";
				//alert(dataString);return false;
				
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#rateresult'.$ID.'det").append(html);
								$("#rateresult'.$ID.'det").fadeOut(1000);
								
						}
				});
				return false;
			});
			</script>
			';
			//===================
			//===================
			//This code block is dealing with jquery the verify
			echo '<script>
			$("body").delegate("#verifyform'.$ID.'det","submit", function(e){
				e.preventDefault();
				$("#verifyresult'.$ID.'det").empty();
				$("#verifyresult'.$ID.'det").show();
				var hasaPost = $("#verifyform'.$ID.'det input#hasaPost").val();
				var rated = $("#verifyform'.$ID.'det input#rated").val();
				var qID = $("#verifyform'.$ID.'det input#qID").val();
				var rating = $("#verifyform'.$ID.'det input#rating").val();
				//var qID = $(this).val();
				var qNum = $("#verifyform'.$ID.'det input#qNum").val();
				var dataString = "hasaPost="+ hasaPost + "&rated=" + rated + "&rating=" + rating + "&qID=" + qID + "&qNum=" + qNum + "&sol=sol3";
				//alert(dataString);return false;
	
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								var currVerIs = $("#currentVer'.$ID.'det").html(); //This is dealing with the text
								if(currVerIs == "Verified!"){
									$("#currentVer'.$ID.'det").html("Not Verified!");
								}else{
									$("#currentVer'.$ID.'det").html("Verified!");
								}
								var currVerBut = $("#verifyform'.$ID.'det input#rating").val(); //This is dealing with the button
								if(currVerBut == "6"){
									$("#verifyform'.$ID.'det input#rating").val("7");
									$("#verifyform'.$ID.'det input#theButton").val("Verify");
								}else{
									$("#verifyform'.$ID.'det input#rating").val("6");
									$("#verifyform'.$ID.'det input#theButton").val("Un-Verify");
								}
								$("#verifyresult'.$ID.'det").append(html);
								$("#verifyresult'.$ID.'det").fadeOut(1000);
						}
				});
				return false;
			});
			</script>
			';	
			//===================
			$styleCount++;
			$ratingFormCount++;
			$verifyFormCount++;
			$solTextCount++;
			$enableEdit++;
		 }else{$noDet = 1;}
		}
		//If no Detailed
		if($noDet == 1 || $NumRows ==0) { //$noHint = 1 if there is a row in the solution table but the solutions are empty or "deleted" OR there is an image with no solution text
			
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr class="solution"><td class="solution">';
			echo '<div id="editDetBox'.$qNum.'">There are no Detailed Solutions for this question.<br>';
			
			// ADD BUTTON
			// solutions.php method get
			echo '<form id="enableaddedForm'.$qNum.'Det">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			//echo '<input type="hidden" name="Num" id="Num" value="'.($N+1).'">';
			echo '<input type="hidden" name="Num" id="Num" value="1">';
			echo '<input type="hidden" name="stype" id="stype" value="Det">';
			echo '<input type="hidden" name="Add" id="Add" value="1">';
			echo '<input type="submit" value="Add">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="editor" id="editor" value ="1">';
			echo '</form></div>';
			echo '<div id="enableaddedResult'.$qNum.'Det"></div></td></tr></table>';
			echo '<script>
			$("body").delegate("#enableaddedForm'.$qNum.'Det","submit", function(e){
				e.preventDefault();
				//var oldText = $("#soltext'.$i.'").html();
				$("#editDetBox'.$qNum.'").hide();
				var hasaPost = $("#enableaddedForm'.$qNum.'Det input#hasaPost").val();
				var Num = $("#enableaddedForm'.$qNum.'Det input#Num").val();
				//var ID = $("#enableaddedForm'.$qNum.'Det input#ID").val();
				var stype = $("#enableaddedForm'.$qNum.'Det input#stype").val();
				var qNum = $("#enableaddedForm'.$qNum.'Det input#qNum").val();
				var editor = $("#enableaddedForm'.$qNum.'Det input#editor").val();
				var addvar = $("#enableaddedForm'.$qNum.'Det input#Add").val();
				//Inside this text needs to be the form from the editor page
				var dataString = "hasaPost="+ hasaPost + "&Num=" + Num + "&stype=" + stype + "&qNum=" + qNum + "&editor=" + editor + "&Add=" + addvar;
				//alert(dataString);return false;
	
				$.ajax({
						type: "GET",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
							$("#enableaddedResult'.$qNum.'Det").append(html);
						}
				});
				return false;
			});
			</script>
			';
			$styleCount++;
		}
		mysql_free_result($res);
		
		//Back Button
		/*
		echo '<br><form action="solutions.php">
					<div align="center">
					<input type="submit" value="Back" />
					</div>
					</form>';
		*/
		return;
		
		
	}
	
	//=============================================================//
	// Parse solutions entered from showIndex()
	// Description: Given the question number, the hint, the solution,
	// and the detailed solution, this function inserts each of the
	// Hint/Solution/Detailed into the database.
	//
	// Similar to Entered.php
	// ******** THIS FUNCTION IS NOT USED ANYMORE ***********
	//=============================================================//
	function enteredFromIndex($QNUMVAL, $HintVAL, $SolutionVAL, $DetailedVAL){
		
		//Make sure the question ID is actually a number
		if(is_numeric($QNUMVAL)) {
			echo '<b>Number:</b> '.$QNUMVAL;
			echo '<br>';

			//If the Hint is not Empty, then insert it into the database
			if($HintVAL != '') {
				$Hint = str_replace("'", "\'", $HintVAL);	
				$Hint = addslashes($Hint);
				$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Hint\',\''.$Hint.'\', '.$this->sessionId.');';
				$res = mysql_query($query) or die(''.mysql_error().' In Hint Insert');//                                                                     v-'.$_SESSION['user']->id().'
				$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Hint\',\''.$Hint.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
				$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
				echo '<b>Hint:</b> '.$HintVAL;
				echo '<br>';
			}

			//If the Solution is not Empty, then insert it into the database
			if($SolutionVAL != '') {
				$Sol = str_replace("'", "\'", $SolutionVAL);
				$Sol = addslashes($Sol);
				$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Sol\',\''.$Sol.'\', '.$this->sessionId.');';
				$res = mysql_query($query) or die(''.mysql_error().' In Solution Insert');//                                                               v-'.$_SESSION['user']->id().'
				$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Sol\',\''.$Sol.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
				$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
				echo '<b>Solution:</b> '.$SolutionVAL;
				echo '<br>';
			}

			//If the Detailed is not Empty, then insert it into the database
			if($DetailedVAL != '') {
				$Det = str_replace("'", "\'", $DetailedVAL);
				$Det = addslashes($Det);
				$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Det\',\''.$Det.'\', '.$this->sessionId.');';
				$res = mysql_query($query) or die(''.mysql_error().' In Detailed Insert');//                                                               v-'.$_SESSION['user']->id().'
				$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Det\',\''.$Det.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
				$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
				echo '<b>Detailed:</b> '.$DetailedVAL;
				echo '<br>';
			}

			//View solution Button and Back Button
			echo '<form action="solutions.php" method="get">';
			echo '<div align="center">';
			echo '<input type="hidden" name="hasaPost" value="1">';
			echo '<input type="hidden" name="viewSolution" value ="1">';
			echo '<input type="hidden" name="QNUM" value="'.$QNUMVAL.'" />';
			echo '<input type="submit" value="View" />';
			echo '</div>';
			echo '</form>';
			echo '<br><form action="solutions.php">
					<div align="center">
					<input type="submit" value="Back" />
					</div>
					</form>';
		}
		else {
			echo '<H3>INVALID QUESTION NUMBER</H3><br>';
		}
		
		return;
		
	}
	
	//=============================================================//
	// Edit menu for a Hint/Solution/Detailed
	// Description: Given the question number, the hint/sol/det number,
	// the type of solution, the id, and an add variable, this function displays
	// an editor for the given Hint/Solution/Detailed.
	//
	// NOTE* add variable is not needed if JUST editing a question.  It
	//       is used for adding a solution coming from the viewSolution Page.
	//
	// Similar to Editor.php
	//=============================================================//
	function solutionEditor($qNumVAL, $NumVAL, $stypeVAL, $IDVAL, $addVAL){
		$qNum = $qNumVAL;         // question number [in SQL: SELECT id from questions]
		$Num = $NumVAL;
		$stype = $stypeVAL;      //<--- all _GET variables
		$add = $addVAL;
		$ID = $IDVAL;

		

		// DISPLAY QUESTION NUMBER
		//echo '<center>QUESTION: <font color="red">'.$qNum.'</font> ( '.$this->db_name.':'.$this->tb_name.' )</center>';

		//CHECK IF ADD
		IF(isset($add)) {

			// TEXT BOX
			echo '<b>'.$stype.' #'.$Num.'</b>';
			//          action="solutions.php" method="post"
			echo '<form id="addedForm'.$qNum.''.$stype.'">';
			echo '<input type="hidden" name="Del" id="Del" value="2">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="stype" id="stype" value="'.$stype.'">';
			echo '<textarea name="text" id="text" cols="50" rows="5"></textarea>';
			echo 'Image ID:<textarea name="imgVal" id="imgVal" cols="20" rows="1"></textarea>';
			echo '<br>';
			echo '<input type="submit" value="Add">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="edited" id="edited" value ="1">';
			echo '<input type="hidden" name="solID" id="solID" value="'.$ID.'">';
			echo '</form>';
			echo '<div id="addedResult'.$qNum.''.$stype.'"></div>';
			//========================================================================
			//This code block is dealing with the jQuery for adding a solution
			echo '<script>
			$("body").delegate("#addedForm'.$qNum.''.$stype.'","submit", function(e){
				e.preventDefault();
				var hasaPost = $("#addedForm'.$qNum.''.$stype.' input#hasaPost").val();
				var Del = $("#addedForm'.$qNum.''.$stype.' input#Del").val();
				var qNum = $("#addedForm'.$qNum.''.$stype.' input#qNum").val();
				var stype = $("#addedForm'.$qNum.''.$stype.' input#stype").val();
				var edited = $("#addedForm'.$qNum.''.$stype.' input#edited").val();
				var text = $("#addedForm'.$qNum.''.$stype.' textarea#text").val();
				var solID = $("#addedForm'.$qNum.''.$stype.' input#solID").val();
				if(!$("#addedForm'.$qNum.''.$stype.' textarea#imgVal").val()){
				var img = 0;
				}else{
				var img = $("#addedForm'.$qNum.''.$stype.' textarea#imgVal").val();	
				}
				var dataString = "hasaPost="+ hasaPost + "&Del=" + Del + "&stype=" + stype + "&qNum=" + qNum + "&edited=" + edited + "&text=" + text + "&solID=" + solID + "&img=" + img;
				//alert(dataString);return false;
				
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#addedForm'.$qNum.''.$stype.'").empty();
								$("#addedResult'.$qNum.''.$stype.'").html(html);
						}
				});
				return false;
			});
			</script>
			';
			//========================================================================
	
		}
		Else {// GET TEXT OF HINT/SOL/DET
			if($stype == 'Hint'){
				$solField = 'solution1';
				$imField = 'image1';
			}elseif($stype == 'Sol'){
				$solField = 'solution2';
				$imField = 'image2';
			}elseif($stype == 'Det'){
				$solField = 'solution3';
				$imField = 'image3';
			}
			$query = 'SELECT '.$solField.','.$imField.' FROM solutions WHERE id ='.$ID.';';
			$res = mysql_query($query) or die(mysql_error() .'solutionEditor() problem.');
			if (!$res) {
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$text = mysql_fetch_array($res, MYSQL_NUM);
			//$text1 = addslashes($text[0]);
			$text1 = $text[0];

			//UPDATE Box
			//echo '<b>'.$stype.' #'.$Num.'</b>';
			//          action="solutions.php" method="post"
			echo '<form id="updateSol'.$ID.'">';
			echo '<input type="hidden" name="Del" id="Del" value="1">';
			echo '<input type="hidden" name="idNum" id="idNum" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="stype" id="stype" value="'.$stype.'">';
			echo '<textarea name="text" id="text" cols="50" rows="5">'.$text1.'</textarea>';
			echo 'Image ID:<textarea name="imgVal" id="imgVal" cols="20" rows="1">'.$text[1].'</textarea>';
			echo '<br>';
			echo '<input type="submit" value="Update">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="edited" id="edited" value ="1">';
			echo '</form>';
			echo '<div id="updatedResult'.$ID.'"></div>';
			//=====================================================================
			//This Code Block is dealing with the jQuery for updating a solution.
			echo '<script>
			$("body").delegate("#updateSol'.$ID.'","submit", function(e){
				e.preventDefault();
				var hasaPost = $("#updateSol'.$ID.' input#hasaPost").val();
				var Del = $("#updateSol'.$ID.' input#Del").val();
				var idNum = $("#updateSol'.$ID.' input#idNum").val();
				var qNum = $("#updateSol'.$ID.' input#qNum").val();
				var stype = $("#updateSol'.$ID.' input#stype").val();
				var edited = $("#updateSol'.$ID.' input#edited").val();
				var text = $("#updateSol'.$ID.' textarea#text").val();
				if(!$("#updateSol'.$ID.' textarea#imgVal").val()){
				var img = 0;
				}else{
				var img = $("#updateSol'.$ID.' textarea#imgVal").val();	
				}
				var dataString = "hasaPost="+ hasaPost + "&Del=" + Del + "&stype=" + stype + "&idNum=" + idNum + "&qNum=" + qNum + "&edited=" + edited + "&text=" + text + "&img=" + img;
				//alert(dataString);return false;
	
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#updateSol'.$ID.'").empty();
								$("#deleteSol'.$ID.'").empty();
								$("#updatedResult'.$ID.'").html(html);
								//$("#soltext'.$ID.'").empty();
								//$("#soltext'.$ID.'").html(text);
						}
				});
				return false;
			});
			</script>';
			//======================================================================
			
			
			
			
			

			//Delete
			//          action="solutions.php" method="post"
			echo '<form id="deleteSol'.$ID.'">';
			echo '<input type="hidden" name="Del" id="Del" value="0">';
			echo '<input type="hidden" name="idNum" id="idNum" value="'.$ID.'">';
			echo '<input type="hidden" name="qNum" id="qNum" value="'.$qNum.'">';
			echo '<input type="hidden" name="stype" id="stype" value="'.$stype.'">';
			echo '<input type="hidden" name="hasaPost" id="hasaPost" value="1">';
			echo '<input type="hidden" name="edited" id="edited" value ="1">';
			echo '<input type="submit" value="Delete">';
			echo '</form>';
			echo '<div id="deletedResult'.$ID.'"></div>';
			//================================================================
			// This code block is dealing with the jQuery for deleting a solution.
			echo '<script>
			$("body").delegate("#deleteSol'.$ID.'","submit", function(e){
				e.preventDefault();
				var hasaPost = $("#deleteSol'.$ID.' input#hasaPost").val();
				var Del = $("#deleteSol'.$ID.' input#Del").val();
				var idNum = $("#deleteSol'.$ID.' input#idNum").val();
				var qNum = $("#deleteSol'.$ID.' input#qNum").val();
				var stype = $("#deleteSol'.$ID.' input#stype").val();
				var edited = $("#deleteSol'.$ID.' input#edited").val();
				var dataString = "hasaPost="+ hasaPost + "&Del=" + Del + "&stype=" + stype + "&idNum=" + idNum + "&qNum=" + qNum + "&edited=" + edited;
				//alert(dataString);return false;
	
				$.ajax({
						type: "POST",
						url: "solutions.php",
						data: dataString,
						success: function( html) {
								$("#updateSol'.$ID.'").empty();
								$("#deleteSol'.$ID.'").empty();
								$("#deletedResult'.$ID.'").append(html);
						}
				});
				return false;
			});
			</script>
			';
			//=================================================================
			
			
			
			
		} // END ADD ELSE 

		//BACK BUTTON
		//echo '<form action="solutions.php" method ="get">';
		//echo '<input type="hidden" name="QNUM" value="'.$qNum.'">';
		//echo '<input type="hidden" name="hasaPost" value="1">';
		//echo '<input type="hidden" name="viewSolution" value ="1">';
		//echo '<input type="submit" value="Back">';
		//echo '</form>';
		
		return;
		
	}
	
	//=============================================================//
	// Parse solutions edited from solutionEditor()
	// Description: Given the id Number (in solutions table), the question number,
	// the solution type, a delete variable, and the text, this function parses through
	// whatever was edited and then either DELETES, UPDATES, or ADDS the question
	// to the database
	//
	// Similar to Edited.php
	//=============================================================//
	function enteredFromEditor($idNumVAL, $qNumVAL, $stypeVAL, $DelVAL, $textVAL, $imgVAL){
		
		$path = '/cgi-bin/mimetex.cgi?';
		$idNum = $idNumVAL;
		$qNum = $qNumVAL;              //<---- _POST variables
		$stype = $stypeVAL;
		$img = $imgVAL;
		
		//echo $stype;
		//die();
		$Del = $DelVAL;
		if($stype == 'Hint'){
			$solField = 'solution1';
			$imgField = 'image1';
			//echo $solField;
		}elseif($stype == 'Sol'){
			$solField = 'solution2';
			$imgField = 'image2';
			//echo $solField;
		}elseif($stype == 'Det'){
			$solField = 'solution3';
			$imgField = 'image3';
			//echo $solField;
		}
		
		if ($img == 0){
			$img = 'NULL';
		}
		//DELETE
		if($Del == 0) {
			//Grab the text to log before being deleted
			/*
			$query = 'SELECT text FROM solutions WHERE id ='.$idNum.';';
			$res = mysql_query($query) or die(mysql_error());
			if (!$res) {
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$text = mysql_fetch_array($res, MYSQL_NUM) or die(mysql_error());
			$text1 = $text[0];
			//echo $text1;
			$text1 = addslashes($text1);
			$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text1.'\', '.$this->sessionId.', '.time().', \'Deleted\');';
			$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			*/
			//Actually delete
			//$query = 'DELETE FROM solutions WHERE id = '.$idNum.';';
			$query = 'UPDATE solutions SET '.$solField.'=NULL, '.$imgField.'=NULL WHERE id='.$idNum.';';
			$res = mysql_query($query) or die(mysql_error());
			if (!$res) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			echo 'Successfully Deleted';
		}
		//UPDATE
		Elseif($Del == 1) {
			//$textpre = $textVal;//The text before you add slashes
			$text = addslashes($textVAL);
			$query = 'UPDATE solutions SET '.$solField.' =\''.$text.'\', '.$imgField.'='.$img.' WHERE id ='.$idNum.';';
			$res = mysql_query($query);//                                                                                                                   v-'.$_SESSION['user']->id().'
			//$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text.'\', '.$this->sessionId.', '.time().', \'Updated\');';
			//$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			if (!$res) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			//echo self::latexCheck($textpre, $path) or die(mysql_error()).'<br>';
			//echo $text;
			echo ''.self::latexCheck($textVAL, $path).'<br>';
			//echo '<br>Successfully Updated';
		}
		//ADD
		Elseif($Del == 2) {
			//ADD TEXT INTO DATABASE
			$text = addslashes($textVAL);
			$query4 = 'SELECT DISTINCT questions_id FROM solutions WHERE questions_id='.$qNum.';';
			$res4 = mysql_query($query4);
			$numrows4 = mysql_num_rows($res4);
			//echo $numrows4;
			//die('hiderp');
			if($numrows4 == 0){
				$query = 'INSERT INTO solutions (questions_id, '.$solField.', author, verified_by, '.$imgField.') VALUES ('.$qNum.', \''.$text.'\', '.$this->sessionId.', '.$this->sessionId.', '.$img.');';
				$res = mysql_query($query) or die(mysql_error() .'Adding into database');//                                                                                                                   v-'.$_SESSION['user']->id().'
			}
			else{																						// AND author='.$this->sessionId.'
				$query = 'UPDATE solutions SET '.$solField.' =\''.$text.'\', '.$imgField.'='.$img.' WHERE questions_id ='.$qNum.';';
				$res = mysql_query($query) or die(mysql_error() .'Adding into database');
			}
			//$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text.'\', '.$this->sessionId.', '.time().', \'Added\');';
			//$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			if (!$res) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			echo ''.self::latexCheck($textVAL, $path).'<br>';
			//echo 'Successfully Added';
		}
		
		//BACK button
		/*
		echo '<form action="solutions.php" method="get">
					<div align="center">
					<input type="submit" value="Back" />
					<input type="hidden" name="hasaPost" value="1">
					<input type="hidden" name="viewSolution" value="1">
					<input type="hidden" name="QNUM" value="'.$qNum.'">
					</div>
					</form>';
		*/
		return;
		
	}
	
	//=============================================================//
	// Parse ratings coming from viewSolution()
	// Description: Given the new rating, the ID of the solution, 
	// the question number, and a verification variable, this function
	// parses the rating and either verifies the solution or inserts/updates
	// the rating in the database.
	//
	// Similar to Rating.php
	//=============================================================//
	function enterRatings($newRatingVAR, $IDVAR, $qNumVAR, $VerVAR, $solVAL){
		
		//VARIABLES
		$newRating = $newRatingVAR;
		$ID = $IDVAR;
		$qNum = $qNumVAR;
		if($solVAL == 'sol1'){
			$solField = 'solution1';
			$ratField = 'rating1';
			$numratField = 'rating1count';
			
		}elseif($solVAL == 'sol2'){
			$solField = 'solution2';
			$ratField = 'rating2';
			$numratField = 'rating2count';
		}elseif($solVAL == 'sol3'){
			$solField = 'solution3';
			$ratField = 'rating3';
			$numratField = 'rating3count';
		}
		//echo $solField.$ratField.$numratField;
		//die();
		//Grab the text to log before being deleted
		/*
		$query = 'SELECT text, stype FROM solutions WHERE id ='.$ID.';';
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		$text = mysql_fetch_array($res, MYSQL_NUM) or die(mysql_error());
		$text1 = $text[0];
		$stype = $text[1];
		$text1 = addslashes($text1);
		*/

		if($newRating > 5) {
			//This logical block deals with verification
			$Ver = $VerVAR;
			$query = 'UPDATE solutions SET verified='.($newRating - 6).', verified_by='.$this->sessionId.' WHERE id ='.$ID.';';
			$res = mysql_query($query) or die(mysql_error().'error is here');
			//$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text1.'\', '.$this->sessionId.', '.time().', \'Verified or Unverified\');';
			//$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			if (!$res) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			echo 'Successfully Verified or Unverified';
		}
		else {
			//This logical block deals with rating the question
			//Add to the log
			//$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text1.'\', '.$this->sessionId.', '.time().', \'Rated '.$newRating.'\');';
			//$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			//GET CURRENT RATING
			$query = 'SELECT '.$ratField.', '.$numratField.' FROM solutions WHERE id = '.$ID.';';
			$res = mysql_query($query);
			if (!$res) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res, MYSQL_NUM);
			$Current = $table[0];
			$numRates = $table[1];

			//MATH
			$Cur = $Current * $numRates;
			$numRates = $numRates + 1;
			$new = round((($Cur + $newRating)/$numRates), 2);

			//STORE RATING AND NUMRATINGs
			$query2 = 'UPDATE solutions SET '.$ratField.'='.$new.', '.$numratField.'='.$numRates.' WHERE id='.$ID.';';
			$res2 = mysql_query($query2);
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			echo 'Successfully Rated';
		}
		
		//BACK button to showIndex()
		/*
		echo '<form action="solutions.php">
					<div align="center">
					<input type="submit" value="Back" />
					</div>
					</form>';
		*/
		return;
		
	}
	
	//=============================================================//
	// Add a hint to a question ID
	// Description: Given an input of a question_id and the text, this function
	// inserts the hint to the database.
	//
	//=============================================================//
	function addHint($QNUMVAL, $HintVAL){
		if($HintVAL != '') {
			$Hint = str_replace("'", "\'", $HintVAL);	
			$Hint = addslashes($Hint);
			$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Hint\',\''.$Hint.'\', '.$this->sessionId.');';
			$res = mysql_query($query) or die(''.mysql_error().' In Hint Insert');//                                                                     v-'.$_SESSION['user']->id().'
			$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Hint\',\''.$Hint.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
			$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			echo '<b>Hint:</b> '.$HintVAL;
			echo '<br>';
		}
		else{
			echo 'No hint was entered';
		}
		
		return;
	}
	
	//=============================================================//
	// Add a solution to a question ID
	// Description: Given an input of a question_id and the text, this function
	// inserts the solution to the database.
	//
	//=============================================================//
	function addSolution($QNUMVAL, $SolutionVAL){
		if($SolutionVAL != '') {
			$Sol = str_replace("'", "\'", $SolutionVAL);
			$Sol = addslashes($Sol);
			$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Sol\',\''.$Sol.'\', '.$this->sessionId.');';
			$res = mysql_query($query) or die(''.mysql_error().' In Solution Insert');//                                                               v-'.$_SESSION['user']->id().'
			$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Sol\',\''.$Sol.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
			$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			echo '<b>Solution:</b> '.$SolutionVAL;
			echo '<br>';
		}
		else{
			echo 'No solution was entered';
		}
		
		return;
	}
	
	//=============================================================//
	// Add a Detailed Solution to a question ID
	// Description: Given an input of a question_id and the text, this function
	// inserts the detailed solution to the database.
	//
	//=============================================================//
	function addDetailed($QNUMVAL, $DetailedVAL){
		if($DetailedVAL != '') {
			$Det = str_replace("'", "\'", $DetailedVAL);
			$Det = addslashes($Det);
			$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$QNUMVAL.', \'Det\',\''.$Det.'\', '.$this->sessionId.');';
			$res = mysql_query($query) or die(''.mysql_error().' In Detailed Insert');//                                                               v-'.$_SESSION['user']->id().'
			$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$QNUMVAL.', \'Det\',\''.$Det.'\', '.$this->sessionId.', '.time().', \'Added\');'; //Inserts into the log
			$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
			echo '<b>Detailed:</b> '.$DetailedVAL;
			echo '<br>';
		}
		else{
			echo 'No detailed solution was entered';
		}
		
		return;
	}
	
	//=============================================================//
	// Delete a Hint/Solution/Detailed from the database
	// Description: Given the id of the hint/sol/det *NOT question_id*,
	// this function deletes the hint/sol/det from the database
	//
	//=============================================================//
	function deleteSol($idNum){
		//Grab the text to log before being deleted
		$query = 'SELECT text, question_id, stype FROM solutions WHERE id ='.$idNum.';';
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		$text = mysql_fetch_array($res, MYSQL_NUM) or die(mysql_error());
		$text1 = $text[0];
		$qNum = $text[1];
		$stype = $text[2];
		//echo $text1;
		$text1 = addslashes($text1);
		$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text1.'\', '.$this->sessionId.', '.time().', \'Deleted\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		//Actually delete
		$query = 'DELETE FROM solutions WHERE id = '.$idNum.';';
		$res = mysql_query($query);
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		echo 'Successfully Deleted';
		
		return;
	}
	
	//=============================================================//
	// Adds a Hint/Sol/Det to the database
	// Description: Given the question_id, the stype, and the text,
	// this function inserts the hint/sol/det into the database.
	//
	// *NOTE* stype NEEDS to be either Hint, Sol, or Det. <-Case sensitive 
	//
	//=============================================================//
	function addGeneralSol($qNum, $stype, $textVAL){
		$text = addslashes($textVAL);
		$query = 'INSERT INTO solutions (question_id, stype, text, users_id) VALUES ('.$qNum.', \''.$stype.'\',\''.$text.'\', '.$this->sessionId.');';
		$res = mysql_query($query);//                                                                                                                   v-'.$_SESSION['user']->id().'
		$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text.'\', '.$this->sessionId.', '.time().', \'Added\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		echo 'Successfully Added';
		
		return;
	}
	
	//=============================================================//
	// Updates a Hint/Sol/Det in the database
	// Description: Given the question_id, the id, the stype, and the text,
	// this function updates the hint/sol/det in the database at location id.
	//
	// *NOTE* id is the ID in the solutions table
	// *NOTE* stype NEEDS to be either Hint, Sol, or Det. <-Case sensitive 
	//
	//=============================================================//
	function updateSol($qNum, $idNum, $stype, $textVAL){
		$text = addslashes($textVAL);
		$query = 'UPDATE solutions SET text =\''.$text.'\' WHERE id ='.$idNum.';';
		$res = mysql_query($query);//                                                                                                                   v-'.$_SESSION['user']->id().'
		$query3 = 'INSERT INTO solutionsLog (question_id, stype, text, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$stype.'\',\''.$text.'\', '.$this->sessionId.', '.time().', \'Updated\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		echo 'Successfully Updated';
		
		return;
	}
	//=============================================================//
	// Views the hints for a question
	// Description: This function is to be used on the "screen" page 
	//
	//  
	//
	//=============================================================//
	function viewHints($QNUMVAR){
		//Set useful variables
		$qNum = $QNUMVAR;         // question number [in SQL: SELECT id from questions]
		$path = '/cgi-bin/mimetex.cgi?';
		$styleCount = 0; //Used for determing CSS style for solutions.
		//$ratingFormCount = 0; //used for jQuery Stuff
		//$verifyFormCount = 0; //used for jQuery stuff
		//$solTextCount = 0; //used for jQuery stuff
		//$enableEdit = 0; //used for jQuery stuff
		
		// Grab Hints
		$query = 'SELECT id,text FROM solutions WHERE question_id = '.$qNum. ' AND stype = \'Hint\' ORDER BY rating DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) {
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
		
		//Print Hints
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM); 
			$ID = $text[0];
		 
			//TITLE AND EDIT BUTTON 
			echo '<b>Hint #' .($N+1). '</b><br>';
			
		 
			//TEXT
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan=2 class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'">'.self::latexCheck($text[1], $path).'</div></th><br>';
			
			//GRAB RATING
			$query2 = 'SELECT rating,numratings,verified FROM solutions WHERE id ='.$ID.';';
			$res2 = mysql_query($query2) or die(mysql_error());
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
		 
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Verified!</div>';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Not Verified!</div>';
			}
			echo '</div></td></tr></table>';
			echo '<br>';
			$styleCount++;
			$ratingFormCount++;
			$verifyFormCount++;
			$solTextCount++;
			$enableEdit++;
		}
		//If no Hints
		if($NumRows == 0) {
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=0><tr class="solution"><td class="solution">';
			echo '<div id="editHintBox'.$qNum.'">There are no hints for this question.<br>';
	 	
			// ADD BUTTON
			//          action="solutions.php" method="get"
			echo '</div>';
			echo '</td></tr></table>';
			$styleCount++;
		}
		echo '<br>';
		
		mysql_free_result($res);
		
		return;
	}
	
	//=============================================================//
	// views the Solution and Detailed Solution for a question
	// Description: This function is to be used in the "review" mode
	//
	// 
	//
	//=============================================================//
	function viewSols($QNUMVAR){
		//Set useful variables
		$qNum = $QNUMVAR;         // question number [in SQL: SELECT id from questions]
		$path = '/cgi-bin/mimetex.cgi?';
		$styleCount = 0; //Used for determing CSS style for solutions.
		
		///////////////////////////////////////////////////////////////////
		//Grab Solutions
		$query = 'SELECT id,text FROM solutions WHERE question_id = '.$qNum.' AND stype = \'Sol\' ORDER BY rating DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
	 
		//Print Solutions
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM);
			$ID = $text[0];
			
			//TITLE AND EDIT BUTTON 
			//           action="solutions.php" method="get"
			echo '<b>Solution #' .($N+1). '</b><br>';
			
			
			// TEXT
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan="2" class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'">'.self::latexCheck($text[1], $path). '</div></th><br>';
			
			//GRAB RATING
			$query2 = 'SELECT rating,numratings,verified FROM solutions WHERE id='.$ID.';';
			$res2 = mysql_query($query2);
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
			
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Verified!</div>';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Not Verified!</div>';
			}
			echo '</div></td></tr></table><br>';	
			//===================
			$styleCount++;
		}
		//If no Solutions
		if($NumRows == 0) {
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr class="solution"><td class="solution">';
			echo '<div id="editSolBox'.$qNum.'">There are no solutions for this question.<br>';
			
			// ADD BUTTON
			//          action="solutions.php" method="get"
			echo '</div>';
			echo '</td></tr></table>';
			$styleCount++;
		}
		echo '<br>';
		mysql_free_result($res);
		
		///////////////////
		//Grab Detailed
		$query = 'SELECT id,text FROM solutions WHERE question_id = '.$qNum.' AND stype = \'Det\' ORDER BY rating DESC;'; // ORDER BY RATING
		$res = mysql_query($query) or die(mysql_error());
		if (!$res) { 
			die('Query execution problem in SQLforDemo.php: ' . msql_error());
		}
	 
		//Print Detailed
		$NumRows = mysql_num_rows($res);
		for($N = 0; $N < $NumRows; $N++) {
			$text = mysql_fetch_array($res, MYSQL_NUM);
			$ID = $text[0];
			
			// TITLE AND EDIT BUTTON
			echo '<b>Detailed #' .($N+1). '</b><br>';
		 
			// TEXT
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr><th rowspan="2" class="'.self::solutionStyleCheck($styleCount).'" width="65%">';
			echo '<div id="soltext'.$ID.'">'.self::latexCheck($text[1], $path). '</div></th><br>';
		 
			//GRAB RATING
			$query2 = 'SELECT rating,numratings,verified FROM solutions WHERE id ='.$ID.';';
			$res2 = mysql_query($query2);
			if (!$res2) { 
				die('Query execution problem in SQLforDemo.php: ' . msql_error());
			}
			$table = mysql_fetch_array($res2, MYSQL_NUM);
			$rating = $table[0];
			$numratings = $table[1];
			$verified = $table[2];
		 
			// RATING SYSTEM
			echo '<td class="solution"><div align="center">';
			If($numratings == 0) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="BLACK"><b>None</b></font>';}
			Else If($rating < 2) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="RED"><b>'.$rating.'</b></font>';}
			Else If($rating >= 2 AND $rating < 4) {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="SLATEBLUE"><b>'.$rating.'</b></font>';} 
			Else {echo '<font face="Gaze"><u>Current Rating:</u></font> <font color="GREEN"><b>'.$rating.'</b></font>';}
			echo '</div></td></tr>';
		 
			//Verify Button
			echo '<tr class="solution"><td class="solution"><div align="center">';
			if($verified == 1) {
				//                    action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Verified!</div>';
			}
			else {
				//                        action="solutions.php" method="post"
				echo '<div id="currentVer'.$ID.'">Not Verified!</div>';
			}
			echo '</div></td></tr></table><br>';
			//===================
				
			//===================
			$styleCount++;
		 
		}
		//If no Detailed
		if($NumRows == 0) {
			echo '<table class="'.self::solutionStyleCheck($styleCount).'" border=1>';
			echo '<tr class="solution"><td class="solution">';
			echo '<div id="editDetBox'.$qNum.'">There are no Detailed Solutions for this question.<br>';
			
			// ADD BUTTON
			// solutions.php method get
			echo '</div>';
			echo '</td></tr></table>';
			$styleCount++;
		}
		mysql_free_result($res);
		
		return;
	}
	
	//=====================================================================//
	//Latex Check
	//=====================================================================//
	function latexCheck($str,$path) {
	
		$pattern     = "/\\\$\\\$(.*?)\\\$\\\$/im";
		$replacement = '<img class="ITS_LaTeX" latex="${1}" src="' . $path . '${1}"/>';
		$str         = preg_replace($pattern, $replacement, $str);

		return $str;
	}
	//=====================================================================//
	//CSS Solution Style Check
	//=====================================================================//
	function solutionStyleCheck($thenumber){
		$val  = fmod($thenumber,2);
		if($val == 0){
			$theStyle = 'ITS_ANSWER_STRIPESOL';
		}
		else{
			$theStyle = 'ITS_ANSWERSOL';
		}
		
		return $theStyle;
	}
	
}

?>
