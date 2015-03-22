<?php
$LAST_UPDATE = 'Jul-14-2013';
/*=====================================================================// 					
Author(s): Gregory Krudysz
//=====================================================================*/

require_once("config.php"); // #1 include 
require_once(INCLUDE_DIR . "include.php");

session_start();
// return to login page if not logged in
abort_if_unauthenticated();
//--------------------------------------// 
$mdb2 =& MDB2::connect($db_dsn);

if (isset($_POST)) {
    foreach ($_POST as $key => $t) {
        $sql = 'UPDATE tags SET synonym="' . $t . '" WHERE name="' . $key . '"';
        //echo '<hr>'.$sql.'<hr>';
        $res =& $mdb2->query($sql);
        if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }
    }
    
}
// get TAGS
$query = 'SELECT id,name,synonym FROM tags ORDER BY name'; //echo $query;
$res =& $mdb2->query($query);
if (PEAR::isError($res)) {
    throw new Question_Control_Exception($res->getMessage());
}
$tags = $res->fetchAll();
//$tags = implode(',',$tagNames);

$tb = '<table class="CPROFILE"><th>tag_id</th><th>tag</th><th>synonym ( tag_id )</th>';
foreach ($tags as $t) {
    if (empty($t[2])) {
        $val = '';
    } else {
        $val = 'value="' . $t[2] . '"';
    }
    
    $tb .= '<tr><td style="color:blue">' . $t[0] . '</td><td>' . $t[1] . '</td><td><input size="4" style="color:blue" type="text" name="' . $t[1] . '" ' . $val . '></td></tr>';
}
$tb .= '</table>';
$form = '<center><form action="Synonyms.php" method="post">' . $tb . '<br><input type="submit" value="Update"></form></center>';
$mdb2->disconnect();

//--- NAVIGATION ------------------------------// 
	$current = basename(__FILE__,'.php');
	$ITS_nav = new ITS_navigation($status);
	$nav     = $ITS_nav->render($current);    
//---------------------------------------------//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <title>Synonyms</title>
        <link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_profile.css" type="text/css" media="screen">
        <script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.4.custom/js/jquery-ui-1.8.4.custom.min.js"></script>
<style>
body { margin:50px; padding:0px;text-align:center;max-height:auto;overflow: -moz-scrollbars-vertical; 
overflow-y: scroll;}
</style>
        <title>Synonyms</title>
<?php 
include INCLUDE_DIR.'stylesheet.php';
?>
    </head>
    <body>
<!---===========================================--->
        <div id="framecontent" style="height:60px">
        <!---************* NAVIGATION ******************--->
            <?php echo $nav;?>
            <!---******************************************--->
        </div>
        <!---===========================================--->
        <div id="maincontent" style="top:60px">
<?php
echo '<p><center>'.$form.'</center></p>';
//--- FOOTER ------------------------------------------------//
$ftr = new ITS_footer($status, $LAST_UPDATE, '');
echo $ftr->main();
//-----------------------------------------------------------//
?>
</div>  
</body>
</html>
