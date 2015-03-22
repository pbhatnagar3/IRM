<html>
<head>
<title>ITS_solution Class Documentation</title>
</head>
<body vlink="#0000FF">
<h1>ITS_Solution Class</h1><br>
<h2>Files used:</h2>
<ul>
	<li><a href="#sol">solutions.php</a></li>
	<li><a href="#solclass">classes/ITS_solution.php</a></li>
	<li><a href="#solquest">classes/ITS_Solution_question.php</a></li>
	<li><a href="#soltable">classes/ITS_Solution_table.php</a></li>
	<!--<li><a href="#csssol">css/ITS_Solution_solution.css</a></li>-->
	<li><a href="#csswarm">css/ITS_Solution_warmup.css</a></li>
	<!--<li><a href="#csspage">css/ITS_Solution_pagestyles.css</a></li>-->
</ul>
<h2>MySQL Tables:</h2>
<ul>
	<li><a href="#solutions">solutions</a></li>
	<li><a href="#solutionsLog">solutionsLog</a></li>
</ul>
<br>==================================================================<br>
<p><h2><a name="sol">solutions.php</a></h2></p>
<p>Main page where all solution actions take place.</p><br><br>
<p><h2><a name="solclass">classes/ITS_solution.php</a></h2></p>
<p>Main class file<br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__construct($id)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Constructor function which takes in the user.id() of the person logged on.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;showIndex()</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Show Index<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Shows the Index for the Solutions page by showing<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// The dropdown list of questions with solutions and showing the<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// form to insert solutions<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to the old index.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;viewSolution($QNUMVAR)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// View solution of a question<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given an input of the id of the question, this function<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// displays the question with which is the followed by the<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Hints/Solutions/Detailed solutions with their corresponding ratings<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// and verifications<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to the old demo2.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enteredFromIndex($QNUMVAL, $HintVAL, $SolutionVAL, $DetailedVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Parse solutions entered from showIndex()<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the question number, the hint, the solution,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// and the detailed solution, this function inserts each of the<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Hint/Solution/Detailed into the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to the old Entered.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;solutionEditor($qNumVAL, $NumVAL, $stypeVAL, $IDVAL, $addVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Edit menu for a Hint/Solution/Detailed<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the question number, the hint/sol/det number,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// the type of solution, the id, and an add variable, this function displays<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// an editor for the given Hint/Solution/Detailed.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// NOTE* add variable is not needed if JUST editing a question.  It<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//       is used for adding a solution coming from the viewSolution Page.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to the old Editor.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enteredFromEditor($idNumVAL, $qNumVAL, $stypeVAL, $DelVAL, $textVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Parse solutions edited from solutionEditor()<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the id Number (in solutions table), the question number,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// the solution type, a delete variable, and the text, this function parses through<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// whatever was edited and then either DELETES, UPDATES, or ADDS the question<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// to the database<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to to the old Edited.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enterRatings($newRatingVAR, $IDVAR, $qNumVAR, $VerVAR)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Parse ratings coming from viewSolution()<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the new rating, the ID of the solution, <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// the question number, and a verification variable, this function<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// parses the rating and either verifies the solution or inserts/updates<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// the rating in the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Similar to the old Rating.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;addHint($QNUMVAL, $HintVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Add a hint to a question ID<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given an input of a question_id and the text, this function<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// inserts the hint to the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;addSolution($QNUMVAL, $SolutionVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Add a solution to a question ID<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given an input of a question_id and the text, this function<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// inserts the solution to the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;addDetailed($QNUMVAL, $DetailedVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Add a Detailed Solution to a question ID<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given an input of a question_id and the text, this function<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// inserts the detailed solution to the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;deleteSol($idNum)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Delete a Hint/Solution/Detailed from the database<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the id of the hint/sol/det *NOT question_id*,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// this function deletes the hint/sol/det from the database<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;addGeneralSol($qNum, $stype, $textVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Adds a Hint/Sol/Det to the database<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the question_id, the stype, and the text,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// this function inserts the hint/sol/det into the database.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// *NOTE* stype NEEDS to be either Hint, Sol, or Det. &lt;--Case sensitive <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;updateSol($qNum, $idNum, $stype, $textVAL)</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Updates a Hint/Sol/Det in the database<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Description: Given the question_id, the id, the stype, and the text,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// this function updates the hint/sol/det in the database at location id.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// *NOTE* id is the ID in the solutions table<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// *NOTE* stype NEEDS to be either Hint, Sol, or Det. &lt;--Case sensitive <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
</p><br><br>
<p><h2><a name="solquest">classes/ITS_Solution_question.php</a></h2></p>
<p>Helper class for viewing solutions</p><br><br>
<p><h2><a name="soltable">classes/ITS_Solution_table.php</a></h2></p>
<p>Helper class for viewing solutions</p><br><br>
<p><h2><a name="csssol">css/ITS_Solution_solution.css</a></h2></p>
<p>Main css file used.</p><br><br>
<p><h2><a name="csswarm">css/ITS_Solution_warmup.css</a></h2></p>
<p>Additional css file used.</p><br><br>
<p><h2><a name="csspage">css/ITS_Solution_pagestyles.css</a></h2></p>
<p>Additional css file used. Might not be used.</p><br><br>
<p><h2><a name="solutions">solutions</a></h2></p>
<p>MySQL Table which holds the current solutions to a question.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>id</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Auto-incrementing id<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int() Primary Key<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>question_id</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// ID of the question<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>stype</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Hint/Sol/Det<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>text</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Text of the Hint/Sol/Det<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(4096)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>rating</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Current rating<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// float<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>numratings</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Number of ratings<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>verified</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// 0 or 1<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// 0 for unverified, 1 for verified<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(1)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>users_id</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// user.id() obtained from the session<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
</p><br><br>
<p><h2><a name="solutionsLog">solutionsLog</a></h2></p>
<p>MySQL Table which holds the log of the solutions.  It can log Adding, Updating, Deleting, Rating.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>id</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Auto-incrementing id<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(11) Primary Key<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>question_id</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// ID of the question<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// int(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>stype</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Hint/Sol/Det<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>text</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Text of the Hint/Sol/Det<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(4096)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>author</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// user.id() obtained from the session.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(11)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>timestamp</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// time()<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(30)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>actionTaken</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Added, Updated, Deleted, Rated #<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// varchar(30)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//=============================================================//<br><br><br>
</p><br><br>


</body>
</html>
