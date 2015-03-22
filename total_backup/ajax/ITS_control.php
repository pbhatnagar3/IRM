 <?php
/* ITS_control - script for AJAX question control objects: CANCEL | SAVE
when in 'Edit' mode, called from js/ITS_QControl.js

Author(s): Greg Krudysz
Last Update: Jul-15-2013
----------------------------------------------------------------------*/

require_once("../FILES/PEAR/MDB2.php");
require_once("../config.php");
require_once("../" . INCLUDE_DIR . "include.php");
require_once("../classes/ITS_search.php");

session_start();
//===================================================================//
global $db_dsn, $db_name, $tb_name, $db_table_user_state, $term;

//-- Get AJAX arguments
$args    = split('[,]', $_GET['ajax_args']);
$qid     = $args[0];
$Control = $args[1];
$Target  = $args[2]; // target = {TITLE|QUESTION|IMAGE|...}

//-- Get AJAX user data
$Data = rawurldecode($_GET['ajax_data']);
// preprocess before SQL
$Data = str_replace("'", "&#39;", $Data);
//$Data = nl2br($Data);

//echo '<span style="border:2px solid yellow">'.strftime('%H:%M:%S').'</span>';

//-- Connect to DB
$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)) {
    throw new Question_Control_Exception($mdb2->getMessage());
}
$Q = new ITS_question(1, $db_name, $tb_name);
$T = new ITS_statistics(1, $term, 'admin');
$Q->load_DATA_from_DB($qid);
$qtype = strtolower($Q->Q_question_data['qtype']);
$publish = (strtolower($Q->Q_question_data['status'])=='publish') ? '<img src="admin/icons/check.jpeg" class="ITS_publish">' : '';

// die($Control);
// JS: encodeURIComponent() -> PHP: rawurldecode()
// PHP: rawurlencode() -> JS: decodeURIComponent()

switch ($Control) {
    //-------------------------------------------//
    case 'PREV':
    case 'NEXT':
    case 'TEXT':
        //-------------------------------------------//        
        $adminNav = $Q->render_Admin_Nav($qid, $qtype, 'ITS_button');
        $nav      = '<div id="importQuestionContainer">' . '<form id="QTI2form" action="upload_QTIfile.php" enctype="multipart/form-data" method="post">' . '<table><tr>' . '<td><label for="files">QTI file</label></td>' . '<td><input type="file" name="file" id="file"></td>' . '<td><input id="file_upload" name="file_upload" type="file"></td>' . '<td><input type="submit" name="submit" value="Submit" id="QTIsubmit"></td>' . '</tr></table></form></div>' . $Q->render_QUESTION() . '<p>';
        $Q->get_ANSWERS_data_from_DB();
        
        // Users pull-down menu
        $usersContainer     = '<div id="usersContainerToggle" class="Question_Toggle"><span>&raquo;&nbsp;Users</span></div><div id="usersContent" style="display:none;"><center>' . $T->render_question_users($qid) . '</center></div>';
        $solutionContainers = '<div id="solutionContainer" class="Question_Toggle"><span>&raquo;&nbsp;Solutions</span></div><div id="results"></div>';
        echo $publish . $nav . $Q->render_ANSWERS('a', 2) . $usersContainer . $solutionContainers . '' . $Q->render_data() . $adminNav;
        break;
    //-------------------------------------------//
    case 'CANCEL':
        //-------------------------------------------//
        //-- evaluate corresponding method based on target={TITLE|QUESTION|IMAGE|...}
        die('sss');
        $field = strtolower(str_replace("ITS_", "", $Target));
        die($field);
        if (array_key_exists($field, $Q->Q_question_data)) {
            $str = $Q->Q_question_data[$field];
        } else {
            $query = "SELECT " . $field . " FROM " . $tb_name . "_" . $qtype . " WHERE id=" . $qid . ";";
            die($query);
            $res =& $mdb2->query($query);
            if (PEAR::isError($res)) {
                throw new Question_Control_Exception($res->getMessage());
            }
            
            $row = $res->fetchRow();
            $str = $row[0];
        }
        echo $str;
        break;
    //-------------------------------------------//
    case 'SAVE':
        //-------------------------------------------//
        // DEBUG: var_dump($Data);//die();
        $field = strtolower(str_replace("ITS_", "", $Target));
        
        if (array_key_exists($field, $Q->Q_question_data)) {
            $query = 'UPDATE ' . $tb_name . ' SET ' . $field . '="' . trim(addslashes($Data)) . '" WHERE id=' . $qid;
        } else {
            $query = 'UPDATE ' . $tb_name . '_' . $qtype . ' SET ' . $field . '="' . trim(addslashes($Data)) . '" WHERE ' . $tb_name . '_id=' . $qid;
        }
        // die($query);    
        $res =& $mdb2->query($query);
        if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }
        
        // Pre-process string for output:
        $str = $Q->renderFieldCheck($Data);
        echo $str;
        break;
        //-------------------------------------------//
}
//=====================================================================//
?> 
