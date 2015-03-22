<?php
require_once("../FILES/PEAR/MDB2.php");
require_once("../config.php");
require_once("../" . INCLUDE_DIR . "include.php");
require_once("../classes/ITS_resource.php");

$style = '';

session_start();

if (empty($_SESSION['concepts'])) {
    abort_if_unauthenticated();
} else {
    $obj = $_SESSION['concepts'];
}

if (isset($_REQUEST['letter'])) {
    $letter = $_REQUEST['letter'];
    $role   = $_REQUEST['role'];
    $order  = $_REQUEST['index'];
    $role_flag = ($role=='admin' OR $role=='instructor') ? 1 : 0;
    $retStr = $obj->getConcepts($letter,$role_flag,$order);
    echo $retStr;
}
if (isset($_REQUEST['choice'])) {
    $choice = $_REQUEST['choice'];
    
    switch ($choice) {
        case 'submitConcepts':
            $tbvalues = $_REQUEST['tbvalues'];
            $retStr   = $obj->getRelatedQuestions($tbvalues);
            break;
        case 'createModule':
            $moduleName    = $_REQUEST['moduleName'];
            $tbvalues      = $_REQUEST['tbvaluesQ'];
            $tbvaluesConcp = $_REQUEST['tbvaluesConcp'];
            $retStr        = $obj->createModule($moduleName, $tbvalues, $tbvaluesConcp);
            break;
        case 'showLetters':
            $retStr = $obj->showLetters();
            break;
        case 'getConceptNav': 
			$concept = $_REQUEST['concept'];
			$tag_id  = $_REQUEST['tag_id'];		
			$retStr  = $obj->getConceptNav($concept,$tag_id);      
			break;     
        case 'getConcepts':
			$role   = $_REQUEST['role'];
			$letter = $_REQUEST['index'];
            $retStr = '<div id="navConcept"></div><div id="conceptContainer">'.$obj->conceptListContainer($letter,$role) . '</div>';
            break;
        case 'getQuestions':
            $retStr = $obj->getQuestionsStudent();
            break;
        case 'getModuleQuestion':
            $modulesQuestion = $_REQUEST['modulesQuestion'];
            $retStr          = $obj->getModuleQuestion($modulesQuestion);
            break;
        case 'deleteModuleQuestion':
            $deleteQuestion = $_REQUEST['deleteQuestion'];
            $ModuleName     = $_REQUEST['ModuleName'];
            $retStr         = $obj->deleteModuleQuestion($deleteQuestion, $ModuleName);
            break;
        case 'getModuleDDList':
            $retStr = $obj->moduleList(1);
            break;
        case 'updateScore':
            $retStr = $obj->updateScore();
            break;                       
        case 'updateConceptInfo':
			$tid = $_REQUEST['tid'];
            $retStr = $obj->getConceptScore($tid);
            break;                            
        default:
    }
    echo $retStr;
}
if (isset($_REQUEST['resource'])) {
    $letter = $_REQUEST['resource'];
    $data   = preg_split('[~]',$letter);
    $obj    = new ITS_resource($data[0]);
    $retStr = $obj->renderContainer();
    echo $retStr;
}
?>
