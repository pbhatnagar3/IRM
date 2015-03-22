<?php
$LAST_UPDATE = 'Sep-26-2012';
/*=====================================================================//               
Last Revision: Gregory Krudysz, Sep-26-2012
//=====================================================================*/

require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

require_once("classes/ITS_navigation.php");
require_once("classes/ITS_footer.php");
require_once("classes/ITS_tag.php");
require_once("classes/ITS_search.php");
require_once("classes/ITS_book.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$status = $_SESSION['user']->status();

if ($status == 'admin' OR $status == 'instructor') {
    // connect to database
    $mdb2 =& MDB2::connect($db_dsn);
    if (PEAR::isError($mdb2)) {
        throw new Question_Control_Exception($mdb2->getMessage());
    }
    //--- NAVIGATION ------------------------------// 
    $current = basename(__FILE__, '.php');
    $ITS_nav = new ITS_navigation($status);
    $nav     = $ITS_nav->render($current);
    //---------------------------------------------//
    
    /*
    for (qid=1 to qid=K_ques){
    
    $img_arr = 
    SQL: SELECT images_id FROM spf_images WHERE spf_id IN (    
    SELECT spf_id FROM spf_tags WHERE tags_id IN (
    SELECT tags_id FROM questions_tags WHERE questions_id=k
    );
    for (iid=1 to iid=I_images){
    SQL: INSERT IGNORE INTO solutions SET questions_id,images_id VALUES (qid,iid);
    }
    }    
    */
    /*
    | chapter       |
    | paragraph     |
    | index         |
    | section       |
    | image         |
    | math          |
    | protect       |
    | footnote      |
    | equation      |
    | table         |
    | subsection    |
    | subsubsection |
    | texttt
    */
    $ch   = 3;
    $meta = 'paragraph';
    $x    = new ITS_book('dspfirst', $ch, $meta, $tex_path);
    $o    = $x->main();
    //-----------------
} else {
    //* redirect to start page *//
    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <script src="js/ITS_AJAX.js"></script>
        <script src="js/ITS_QControl.js"></script>
        <title>Images</title>
        <link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_Solution_warmup.css" type="text/css">
        <link type="text/css" href="js/jquery-ui-1.8.23.custom/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />    
        <!-- <script type="text/javascript" src="MathJax/MathJax.js"></script> -->
        <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"></script>
     
	<link rel="stylesheet" href="css/ITS_BOOK.css" type="text/css" media="screen">
    <script src="js/ITS_admin.js" type="text/javascript"></script>
	<script src="js/ITS_book.js"  type="text/javascript"></script>
    <script src="js/ITS_AJAX.js"  type="text/javascript"></script>        
    </head>
    <body>
        <!---===========================================--->
        <div id="framecontent">
            <!---************* NAVIGATION ******************--->
            <?php
echo $nav;
?>
           <!---******************************************--->
            <div class="innertube">
                <!---******************************************--->
                <form id="question" name="question" action="Question.php" method="get">
                <?php
//echo '';
?>
               <noscript><input type="submit" value="Submit"></noscript>
                </form>
            </div>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent">
            <div id="ITS_question_container">
<!-- NAVIGATION ----------------------------------------------->
<div id="bookNavContainer">
<div id="chContainer">
<span id="chText">CHAPTER</span>
<ul id="chList">
<li id="active"><a href="#" onclick="ITS_book_select(this)" name="chapter" value="1" id="current"> 1</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="2"> 2</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="3"> 3</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="4"> 4</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="5"> 5</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="6"> 6</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="7"> 7</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="8"> 8</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="9"> 9</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="10"> 10</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="11"> 11</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="12"> 12</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="chapter" value="13"> 13</a></li>
</ul>
</div>
<ul id="metaList"> 
<li id="active"><a href="#" onclick="ITS_book_select(this)" name="meta" value="paragraph" id="current">book</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="meta" value="equation">equations</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="meta" value="math">math symbols</a></li>
<li><a href="#" onclick="ITS_book_select(this)" name="meta" value="image">images</a></li>
</ul>
</div>				
<div id="bookContainer" >				
<?php
echo $o . '</div>';
echo '</div>';

//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE, '');
echo $ftr->main();
//-----------------------------------------------------------//
?>
           </div>
            <!----------------------------------------------------------->
    </body>
</html>
