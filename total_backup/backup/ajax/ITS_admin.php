<?php
/*---------------------------------------------------------------------
ajax/ITS_admin - script for AJAX ITS_admin.php
Author(s): Greg Krudysz
Date: Apr-10-2013
---------------------------------------------------------------------*/
$Debug = FALSE;
require_once("../FILES/PEAR/MDB2.php");
require_once("../config.php");
require_once("../" . INCLUDE_DIR . "include.php");
require_once("../classes/ITS_user.php");

$style = '<head><link type="text/css" href="jquery-ui-1.8.23.custom/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />' . '<link type="text/css" href="css/ITS_question.css" rel="stylesheet" />' . '</head>';

session_start();
//===================================================================//
global $db_dsn, $db_name, $tb_name, $db_table_user_state;

//-- Get AJAX arguments
$args = preg_split('[,]', $_GET['ajax_args']);

//-- Get AJAX user data
//$Data = rawurldecode($_GET['ajax_data']);
$Data = $_GET['ajax_data'];

$action = $args[0];
// preprocess before SQL
$Data   = str_replace("'", "&#39;", $Data);
//$Data = nl2br($Data);

/*
echo 'action = '.$action.'<p>';
echo 'data   = '.$Data.'<p>'; die();
*/

$mdb2 =& MDB2::connect($db_dsn);
if (PEAR::isError($mdb2)) {
    throw new Question_Control_Exception($mdb2->getMessage());
}
//var_dump($action);die('in ajax/ITS_admin');
//-----------------------------------------------//
switch ($action) {
    //-------------------------------------------//
    case 'fixResult':
        //-------------------------------------------//    
        $data  = preg_split('[~]', $Data);
        $query = 'SELECT answers,vals FROM ' . $tb_name . ' w,' . $tb_name . '_c l WHERE (w.id=' . $data[0] . ' AND l.id=' . $data[0] . ')';
        $res =& $mdb2->query($query);
        $meta = $res->fetchRow();
        
        $n      = $meta[0];
        $m      = $meta[1];
        $fields = 'formula1';
        for ($k = 1; $k < $n; $k++) {
            $fields .= ', formula' . ($k + 1);
        }
        for ($i = 1; $i <= $m; $i++) {
            $fields .= ',val' . $i;
        }
        
        $query = 'SELECT ' . $fields . ' FROM ' . $tb_name . '_c WHERE id=' . $data[0];
        $res =& $mdb2->query($query);
        $meta = $res->fetchRow();
        
        for ($k = 1; $k <= $n; $k++) {
            $formulaes[$k - 1] = $meta[$k - 1];
        }
        
        for ($k = 0; $k < $m; $k++) {
            $index   = $n + $k;
            $var[$k] = $meta[$index];
        }
        
        $vars_form      = $data[1];
        //echo $vars_form;
        $var_form_array = explode(',', $vars_form);
        $obj            = new ITS_question($data[0], $db_name, $tb_name);
        $res            = '';
        for ($j = 0; $j < $n; $j++) {
            //$res .= implode(',',$var).$formulaes[$j];        
            $res[$j] = $obj->returnResult($var, $var_form_array, $formulaes[$j]);
        }
        $retString = implode(',', $res);
        
        echo $retString;
        break;
    //-------------------------------------------//
    case 'PreviewDialog':
        //-------------------------------------------// 
        $data = preg_split('[~]', $Data);
        $Q    = new ITS_question($student_id, $db_name, $tb_name);
        $Q->load_DATA_from_DB($data[0]);
        $Q->get_ANSWERS_data_from_DB();
        $qstr = $Q->render_QUESTION();
        $qstr .= $Q->render_ANSWERS('a', 2);
        //width:400px;
        $str = '<a class="various" href="#inline">Show Preview</a><br></b><div id="inline" style="display: none;">' . $qstr . '</div>';
        break;
    //-------------------------------------------//
    case 'PreviewOptions':
        //-------------------------------------------//    
        $data  = preg_split('[~]', $Data);
        $query = 'SELECT answers,vals FROM ' . $tb_name . ' w,' . $tb_name . '_c l WHERE (w.id=' . $data[0] . ' AND l.' . $tb_name . '_id=' . $data[0] . ')';
        
        $res =& $mdb2->query($query);
        $meta = $res->fetchRow();
        
        $n      = $meta[0];
        $m      = $meta[1];
        $fields = 'formula1';
        for ($k = 1; $k < $n; $k++) {
            $fields .= ', formula' . ($k + 1);
        }
        
        for ($i = 1; $i <= $m; $i++) {
            $fields .= ',val' . $i . ',min_val' . $i . ',max_val' . $i;
        }
        $query = 'SELECT ' . $fields . ' FROM ' . $tb_name . '_c WHERE ' . $tb_name . '_id=' . $data[0];
        //echo $query;die();
        $res =& $mdb2->query($query);
        $meta = $res->fetchRow();
        
        $str        = '<div><table class="ITS" width="100%"><tr>';
        $thstring   = '';
        $th2string  = "<tr>";
        $td_input_v = '';
        $td_input_f = '';
        $vars       = array();
        for ($k = 1; $k <= $m; $k++) {
            $index = $n + ($k - 1) * 3;
            $thstring .= "<th width='20%'>Variable " . $k . "</th>";
            $th2string .= '<td width="20%">' . $meta[$index] . '</td>';
            $td_input_v .= '<td width="20%"><input type="text" class="cp" name="Variable' . $k . '" id="Variable' . $k . '"/></td>';
            $var[$k - 1] = $meta[$index];
        }
        
        $temp_str = '';
        
        for ($i = 0; $i < 5; $i++) {
            $index = $n;
            for ($k = 0; $k < $m; $k++) {
                $rand_vals[$i][$k] = rand($meta[$index + 1], $meta[$index + 2]);
                $ss[$i] .= '<td width="20%">' . $rand_vals[$i][$k] . '</td>';
                $index += 3;
            }
        }
        
        for ($k = 1; $k <= $n; $k++) {
            $index = $k - 1;
            $thstring .= "<th width='20%'>Formula " . $k . "</th>";
            $th2string .= '<td width="20%">' . $meta[$index] . '</td>';
            $td_input_f .= '<td width="20%"><input type="text" readonly="readonly" name="Formula' . $k . '" id="Formula' . $k . '"/></td>';
            $formulaes[$k - 1] = $meta[$index];
        }
        //$str .= $query."</tr></table></div>";
        
        // to display 10 rows of random variables    
        $obj = new ITS_question($data[0], $db_name, $tb_name);
        $res = 0;
        $trs = '';
        for ($i = 0; $i < 5; $i++) {
            $temp_str = '';
            for ($j = 0; $j < $n; $j++) {
                $res = $obj->returnResult($var, $rand_vals[$i], $formulaes[$j]);
                $temp_str .= '<td width="20%">' . $res . '</td>';
            }
            $trs .= '<tr>' . $ss[$i] . $temp_str . '</tr>';
        }
        
        $form_table = '<form class="ITS" action="#" name="OptionForm"><input type="hidden" value="' . $m . '" name="var_count" id="var_count">' . '<input type="hidden" value="' . $n . '" name="formula_count">' . '<tr>' . $td_input_v . $td_input_f . '</tr><tr><td colspan="' . ($m + $n) . '"><br><div id="errorContainer"></div><br><input type="button" value="Calculate" name="calcResult" id="calcResult" class="ITS_submit"></td></tr></form>';
        $str .= $thstring . $th2string . $trs . $form_table . "</table>" . "</div>";
        break;
    //-------------------------------------------//
    case 'uploadImage':
        //-------------------------------------------//
        //die('here'); $str = var_dump($_FILES);  
        /*
        $data  = preg_split('[~]',$Data);
        $uploaddir = '/var/www/ITS/FILES/images/';
        $uploadfile = $uploaddir . basename($_FILES['ITS_image']['name']);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
        $str = '<font color="green">File is valid, and was successfully uploaded.</br></font>';
        } else {
        $str = '<font color="red">Possible file upload attack!<br></font>';
        }*/
        break;
    //-------------------------------------------//
    case 'getConcept':
        //-------------------------------------------//
        $data  = preg_split('[~]', $Data);
        $query = 'SELECT * FROM stats_' . $id; //die($query);
        $res =& $mdb2->query($query);
        if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }
        $list = $usr->add_user($data[0], $data[1], $data[2], $data[3]);
        $str  = $list;
        break;
    //-------------------------------------------//
    case 'addUser':
        //-------------------------------------------//
        $data = preg_split('[~]', $Data);
        //var_dump($data);die();
        
        $usr = new ITS_user($data[2]);
        $str = $usr->add_user($data[0], $data[1], $data[2], $data[3]);
        break;
    //-------------------------------------------//
    case 'orderProfile':
        //-------------------------------------------//
        $data = preg_split('[~]', $Data);
        $tr   = new ITS_statistics($data[0], $data[1], $data[2]);
        $str  = $tr->render_profile2($data[3], $data[4]);
        break;
    //-------------------------------------------//
    case 'orderCourse':
        //-------------------------------------------//
        $data = preg_split('[~]', $Data);
        $tr   = new ITS_statistics($data[0], $data[1], $data[2]);
        $str  = $tr->render_course($data[3], $data[4], $data[5]);
        break;
    //-------------------------------------------//
    case 'deleteDialog':
        //-------------------------------------------//
        $id = $Data;
        
        //$query = 'SELECT last_name FROM users WHERE id='.$id;
        $query = 'DELETE FROM stats_' . $id; //die($query);
        $res =& $mdb2->query($query);
        if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }
        $str = $query;
        //$user = $res->fetchRow();
        //$str = $user[0];
        break;
    //-------------------------------------------//            
    case 'getQuestionMeta':
        //-------------------------------------------//
        $id    = $Data;
        $query = 'SELECT id,title,image_id,category,tag_id FROM ' . $tb_name . ' WHERE id=' . $id;
        //echo $query;        //
        
        $res =& $mdb2->query($query);
        $meta     = $res->fetchRow();
        $title    = $meta[1];
        $image    = $meta[2];
        //        die($image);
        $category = $meta[3];
        $tags     = $meta[4];
        if (empty($tags)) {
            $str = '<p><b>-- no data --</b></p>';
        } else {
            $query = 'SELECT name FROM tags WHERE id IN (' . $tags . ')';
            
            $res =& $mdb2->query($query);
            $tagNames = $res->fetchCol();
            
            $tagList = '';
            for ($i = 0; $i < count($tagNames); $i++) {
                //$tagList .= '<input type="button" class="logout" value="'.$tagNames[$i].'">';
                $tagList .= '<span class="ITS_tag">' . $tagNames[$i] . '</span>';
            }
            
            $sts = '<center><table class="ITS_ANSWER_C">' . '<tr><th id="title">Title</th><th>Image</th><th>Category</th><th id="tags">Tags</th></tr>' . '<tr><td>' . $title . '</td><td>' . $image . '</td><td>' . $category . '</td><td>' . $tagList . '</td></tr>' . '</table></center>';
            $str = $sts;
            //die($str);
        }
        break;
    //-------------------------------------------//            
    case 'createQuestion':
        //-------------------------------------------//
        $data       = preg_split('[~]', $Data);
        $action     = $data[0]; //var_dump($data);die();
        $type       = $data[1];
        $student_id = 1;
        
        $obj = new ITS_question($student_id, $db_name, $tb_name);
        //die($type);
        switch ($action) {
            //---------------//
            case 'new':
                //---------------//
                $N                             = 4; // default number of answers
                $obj->Q_question_data['qtype'] = $type;
                $obj->load_DATA($Data);
                break;
            //---------------//
            case 'clone':
                //---------------//
                $q_num = $data[1]; //die($data[1]);
                $obj->load_DATA_from_DB($q_num);
                $obj->get_ANSWERS_data_from_DB();
                break;
                //---------------//
        }
        $str = $obj->renderQuestionForm($Data);
        break;
    //-------------------------------------------//            
    case 'addQuestion':
        //-------------------------------------------//
        $str        = '';
        $Qfield_key = array();
        $Qfield_str = array();
        $Afield_key = array();
        $Afield_str = array();
        $fields     = explode("&", $Data);
        $Qidx       = 0;
        $Aidx       = 0;
        //var_dump($fields);die();
        foreach ($fields as $field) {
            $field_key_value = explode("=", $field);
            $key             = urldecode($field_key_value[0]);
            $value           = urldecode($field_key_value[1]);
            
            //DEBUG: echo $key.' --- '.$value.'<p>';
            //if (!empty($value)) {
            switch ($key) {
                case 'qtype';
                case 'title';
                case 'image';
                case 'question';
                case 'answers';
                case 'answersConfig';
                case 'questionConfig';
                case 'category';
                case 'author';
                case 'verified';
                case 'verified_by';
                    $Qfield_key[$Qidx] = $key;
                    $Qfield_str[$Qidx] = addslashes($value); //htmlspecialchars($value,ENT_QUOTES);    
                    //echo $Qfield_key[$Qidx].' - '.$Qfield_str[$Qidx].'<p>';
                    $Qidx++;
                    break;
                default:
                    $Afield_key[$Aidx] = $key;
                    $Afield_str[$Aidx] = addslashes($value); //htmlspecialchars($value,ENT_QUOTES);    
                    $Aidx++;
            }
        }
        //echo '<pre>';var_dump($Qfield_str);echo '</pre><br>';
        
        // QUESTION SQL INSERT
        // title,question,image,answers,answersConfig,questionConfig,category
        $Qquery_fields = implode(',', $Qfield_key);
        $Qquery_values = implode('","', $Qfield_str);
        
        $Qquery = 'INSERT INTO ' . $tb_name . ' (' . $Qquery_fields . ') VALUES("' . $Qquery_values . '");';
        // DEBUG: 
        //echo $Qquery; //die();mysql_real_escape_string
        //echo '<pre>';var_dump($Qquery);echo '</pre><br>';
        
        $qtype = strtolower($Qfield_str[0]);
        
        mysql_query($Qquery);
        $qid = mysql_insert_id();
        //var_dump($qid);die($qid);
        
        // ANSWER SQL INSERT
        $Aquery_fields = implode(',', $Afield_key);
        $Aquery_values = implode('","', $Afield_str);
        $Aquery        = 'INSERT INTO ' . $tb_name . '_' . $qtype . ' ( ' . $tb_name . '_id,' . $Aquery_fields . ') VALUES(' . $qid . ',"' . $Aquery_values . '");';
        mysql_query($Aquery);
        //mysql_real_escape_string(). 
        $msg = '<div class="ITS_MESSAGE" name="addQ">Added Question <a href="Question.php?qNum=' . $qid . '">' . $qid . '</a>';
        $str = $msg . '<div class="ITS_SQL">' . $Qquery . '</div><div class="ITS_SQL">' . $Aquery . '</div></div>';
        break;
    //-------------------------------------------//            
    case 'deleteQuestion':
        //-------------------------------------------//
        $data  = preg_split('[~]', $Data);
        $id    = $data[0];
        $type  = $data[1];
        $query = 'DELETE w,wt FROM `' . $tb_name . '` w,`' . $tb_name . '_' . $type . '` wt WHERE w.id=' . $id . ' AND wt.id=w.id';
        $res =& $mdb2->query($query);
        $str = '<div class="ITS_SQL">' . $query . '</div>'; //'done';
        break;
    //-------------------------------------------//            
    case 'editAnswers':
        //-------------------------------------------//
        $data       = preg_split('[~]', $Data);
        $q_num      = $data[0]; //var_dump($data);die();
        $type       = $data[1];
        $q_type     = $data[2];
        $N          = $data[3]; // default number of answers
        $student_id = 1;
        
        $obj = new ITS_question($student_id, $db_name, $tb_name);
        //die($type);
        switch ($type) {
            //---------------//
            case 'new':
                //---------------//
                // data: id,qtype,title,question,image,answers,answersConfig,questionConfig,category
                $data = array(
                    NULL,
                    $q_type,
                    '',
                    '',
                    NULL,
                    $N,
                    1,
                    1,
                    NULL
                );
                //$obj->load_DATA($data);
                $str  = 'aaa';
                break;
            //---------------//
            case 'clone':
                //---------------//
                $obj->load_DATA_from_DB($q_num);
                $obj->get_ANSWERS_data_from_DB();
                break;
        }
        /*
        //echo $obj->Q_answers;
        $class = 'text ui-widget-content ui-corner-all ITS_Q';
        $ans   = '<table id="ITS_Qans" class="ITS_Qans">';
        for ($a = 1; $a <= $N; $a++) {
        if ($a > $obj->Q_answers) {
        $answerVal = '';
        $weightVal = '';
        } else {
        $answerVal = htmlspecialchars($obj->Q_answers_values[$a - 1]);
        $weightVal = htmlspecialchars($obj->Q_weights_values[$a - 1]);
        }
        $answer_label = '<label for="answer' . $a . '">' . 'answer&nbsp;' . $a . '</label>';
        $answer_field = '<input type="text" name="answer' . $a . '" id="answer' . $a . '" value="' . $answerVal . '" class="' . $class . '" />';
        $weight_label = '<label for="weight' . $a . '">' . 'weight&nbsp;' . $a . '</label>';
        $weight_field = '<input type="text" name="weight' . $a . '" id="weight' . $a . '" value="' . $weightVal . '" class="' . $class . '" />';
        $ans .= '<tr><td width="10%">' . $answer_label . '</td><td width="60%">' . $answer_field . '</td><td width="10%">' . $weight_label . '</td><td width="5%">' . $weight_field . '</td></tr>';
        }
        $ans .= '</table>';
        $str = $ans;
        */
        break;
}
//-----------------------------------------------//
$mdb2->disconnect();
echo $str;
?>
