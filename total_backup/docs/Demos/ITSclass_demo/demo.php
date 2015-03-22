<?php
require_once("PEAR/MDB2.php");
include("classes/ITS_table.php");
include("classes/ITS_question.php");

$db_name = 'its';
$tb_name = 'questions';
$db_dsn  = 'mysql://root:csip@tcp(localhost:3306)/' . $db_name;
$qid     = 3171;

// connect to database
$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)) {
    throw new Question_Control_Exception($mdb2->getMessage());
}

$Q = new ITS_question(1, $db_name, $tb_name);
$Q->load_DATA_from_DB($qid);
echo '<div style="border:1px solid blue;margin:10%">' . $Q->render_QUESTION() . '</div>';
?>
