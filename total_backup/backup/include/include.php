<?php
/*----------------------------------------/
Author(s): Gregory Krudysz
Last Update: Jul-21-2013
/----------------------------------------*/

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // or IE will pull from cache 100% of time (which is really bad) 
header("Cache-Control: no-cache, must-revalidate"); // Must do cache-control headers 
header("Pragma: no-cache");

$dir  = dirname($_SERVER['PHP_SELF']);
$path = ''; //realpath($_SERVER["DOCUMENT_ROOT"]) . $dir . '/';

preg_match('/scripts|admin|ajax|search|doc|ITS_FILES|classes/', $dir, $ajax_match); // dirs

if (!empty($ajax_match)) {
	if ($ajax_match[0]=='admin'){ $path .= '../../'; }
	else {
    $path .= '../';
}
} else  {
    require_once($MDB2_path . 'MDB2.php');
}

//die(INCLUDE_DIR);
require_once($path.INCLUDE_DIR."Authentication.php");
require_once($path.INCLUDE_DIR."common.php");
require_once($path.INCLUDE_DIR."User.php");
require_once($path."classes/ITS_concepts.php");
require_once($path."classes/ITS_configure.php");
require_once($path."classes/ITS_feedback.php");
require_once($path."classes/ITS_footer.php");
require_once($path."classes/ITS_question.php");
require_once($path."classes/ITS_query.php");
require_once($path."classes/ITS_navigation.php");
require_once($path."classes/ITS_rating.php");

require_once($path."classes/ITS_NQ.php");
require_once($path."Difficulty/User_DiffInfo.php");
require_once($path."classes/ITS_screen.php");
require_once($path."classes/ITS_score.php");
require_once($path."classes/ITS_statistics.php");
require_once($path."classes/ITS_table.php");
require_once($path."classes/ITS_tag.php");
?>
