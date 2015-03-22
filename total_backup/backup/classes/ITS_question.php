<?php
/*=====================================================================//
ITS_question class - render ITS question according to type.

type: |S|M|P|C|MC|O| - |short answer|matching|paragraph|calculated|multiple choice|other
questionConfig: |1|2| - | text only | text on left, image on the right
answersConfig:  |1|2|3|4| - | text only | -  | - | images

Constructor: ITS_question(student_id,file_name,db_name,table_name)

Methods: load_DATA_from_DB($q_num)
* render_ANSWERS($name,$mode) { // MODE: 1-Question | 2-EDIT
* createEditTable($TargetName,$Target,$style)
* renderQuestionForm($action)

ex. $ITS_question = new ITS_question(90001,"its","user_cpt");

Author(s): Greg Krudysz | Aug-28-2008
		   Khyati 		| Apr-16-2012
Last Revision: Sep-20-2013
//=====================================================================*/

class ITS_question
{
    var $user_id; 		// ITS student ID
    var $file_name; 	// initial CPT file name (e.g. "ch7_iCPT.txt")
    var $db_name; 		// DB name (e.g. "its")
    var $tb_name; // question table name
    var $max_cols; // max number of prob. entries for each node
    var $cpt_array; // CPT array
    var $cpt_attrib; // CPT table attributes
    var $timestamp;
    var $edit_flag = 0;
    
    // question type: |S|M|P|C|MC|O|
    public $Q_type_arr = array('mc', 'm', 'c', 's', 'p');
    public $Q_vals;
    public $style;
    public $Q_question_data = array();
    public $Q_question_parts = array();
    public $Q_question_weights = array();
    public $Q_answers_permutation;
    public $Q_answers_fields = array();
    public $Q_answers_data = array();
    
    // Constructor //======================================================//
    function __construct($student_id, $db_name, $table_name)
    {
        //=====================================================================//
        global $db_dsn, $db_name, $tb_name, $tb_tags, $db_table_user_state, $files_path, $tex_path, $tex_method;
        
        $this->user_id    = $student_id;
        $this->db_name    = $db_name;
        $this->tb_name    = $table_name;
        $this->tb_tags    = $tb_tags;
        $this->cpt_array  = array();
        $this->cpt_attrib = array();
        $this->style      = 'ITS';
        $this->tex_path   = $tex_path;
        $this->tex_method = $tex_method;
        $this->files_path = $files_path;
    }
    //=====================================================================//
    function load_DATA_from_DB($qid)
    {
        //=====================================================================//
        $query = "DESCRIBE " . $this->tb_name;
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        
        $fields = array();
        for ($f = 0; $f < mysql_num_rows($res); $f++) {
            array_push($fields, mysql_result($res, $f));
            //echo $c.' - '.$fld.' - '.mysql_num_rows($res).'<br>';
        }
        $fieldsSTR = implode(',', $fields);
        //--- DATA
        //$query = "SELECT $fieldsSTR  FROM  $this->tb_name WHERE id=$this->Q_question_data['id']";
        $query     = 'SELECT ' . $fieldsSTR . ' FROM ' . $this->tb_name . ' WHERE id=' . $qid;
        $res       = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        $this->Q_question_data['id'] = $qid;
        $this->Q_question_data       = mysql_fetch_assoc($res);
        //return $this->Q_question_data;       
    }
    //=====================================================================//
    function load_DATA($data)
    {
        //=====================================================================//
        $d = explode('~', $data);
        
        // QUESTION
        $this->Q_question_data['qtype'] = strtolower($d[1]);
        $query                          = "DESCRIBE " . $this->tb_name;
        $res                            = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        $fields = array();
        for ($f = 0; $f < mysql_num_rows($res); $f++) {
            $this->Q_question_data[mysql_result($res, $f)] = '';
        }
        
        // ANSWER
        $query = "DESCRIBE " . $this->tb_name . '_' . strtolower($d[1]);
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        $fields = array();

        for ($f = 0; $f < mysql_num_rows($res); $f++) {
            $this->Q_answers_data[mysql_result($res, $f)] = '';
        }
    }
    //=====================================================================//
    function render_TITLE()
    {
        //=====================================================================//
        // Question info and debug
        echo "<input type=hidden id=ITS_question_info value=" . $this->Q_question_data['id'] . " name=" . strtolower($this->Q_question_data['qtype']) . ">";
        
        $TABLE_TITLE = createEditTable('TITLE', $this->Q_question_data['title'], "ITS");
        
        $title_str = '<p><div class="SubHeading">' . $TABLE_TITLE . '</div>';
        return $title_str;
    }
    //=====================================================================//
    function render_QUESTION_check($conf) // mode: (0-rand) | (1-DB) parameters
    {
        //=====================================================================//
        if (strtolower($this->Q_question_data['qtype']) == 'c') { // replace question variable {v} with rv //
            //echo 'MODE: '.$mode.' at '.date('l jS \of F Y h:i:s A').'<p>';
            //echo '<font color="blue">render_QUESTION_check()</font>:<br>';
            $query  = "SELECT vals FROM " . $this->tb_name . "_" . strtolower($this->Q_question_data['qtype']) . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            $res    = mysql_query($query);
            $vals   = mysql_fetch_array($res);
            // Fetch answer options text
            // Khyatis changes start
            $fields = 'text1';
            //echo $this->Q_question_data['answers'];
            for ($k = 1; $k < $this->Q_question_data['answers']; $k++) {
                $fields .= ', text' . ($k + 1);
            }
            
            $query = "SELECT " . $fields . " FROM " . $this->tb_name . "_" . strtolower($this->Q_question_data['qtype']) . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            //echo $query;die('da');
            
            $res = mysql_query($query);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            $this->Q_question_parts = mysql_fetch_assoc($res);
            
            // get token fields
            $fields = "val1,min_val1,max_val1";
            for ($i = 2; $i <= $vals[0]; $i++) {
                $fields = $fields . ",val" . $i . ",min_val" . $i . ",max_val" . $i;
            }
            
            $query = " SELECT " . $fields . " FROM " . $this->tb_name . "_" . strtolower($this->Q_question_data['qtype']) . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            //echo $query;
            $res   = mysql_query($query);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            
            $vdata    = mysql_fetch_array($res);
            $question = $this->Q_question_data['question'];
            
            $mode = intval(empty($conf));
            
            //ITS_debug('<p>'.$conf.'<br>'.$mode.'</p>');           
            //echo 'MODE: '.$mode.' at '.date('l jS \of F Y h:i:s A').'<p>';die('gg');
            switch ($mode) {
                //-------------------------------------------//
                case 0:
                    //-------------------------------------------//
                    //echo 'case 0';
                    $vals = explode(',', $conf);
                    for ($i = 1; $i <= count($vals); $i++) {
                        //echo $vdata["val".$i].' '.$vals[($i-1)];
                        $question = str_replace($vdata["val" . $i], $vals[($i - 1)], $question);
                        //$this->Q_question_parts['text'.$i] = str_replace($vdata["val" . $i], $vals[($i - 1)], $this->Q_question_parts['text'.$i]);
                        for ($k = 1; $k <= $this->Q_question_data['answers']; $k++) {
                            $this->Q_question_parts['text' . $k] = str_replace($vdata["val" . $i], $vals[($i - 1)], $this->Q_question_parts['text' . $k]);
                        }
                    }
                    break;
                //-------------------------------------------//
                default:
                    //-------------------------------------------//
                    ///*
                    $rnv = array();
                    for ($i = 1; $i <= $vals[0]; $i++) {
                        if ($vdata["min_val" . $i] == 0 & $vdata["max_val" . $i] == 1) {
                            $rnv[($i - 1)] = rand(1, 9) / 10; // fraction 0.x
                        } else {
                            $rnv[($i - 1)] = rand($vdata["min_val" . $i], $vdata["max_val" . $i]);
                        }
                        
                        //echo $vdata["min_val".$i].'___'.$vdata["max_val".$i].'<br>';
                        //echo $vdata["val".$i].' '.$rnv[($i-1)];
                        $question = str_replace($vdata["val" . $i], $rnv[($i - 1)], $question);
                        
                        //  echo("DEBUG: ".$this->Q_question_parts['text1'].$vdata["val" . $i]. $vals[($i - 1)]);
                        for ($k = 1; $k <= $this->Q_question_data['answers']; $k++) {
                            $this->Q_question_parts['text' . $k] = str_replace($vdata["val" . $i], $rnv[($i - 1)], $this->Q_question_parts['text' . $k]);
                        }
                        $this->Q_answers_permutation[$i] = $rnv[($i - 1)];
                    }
                    //var_dump($this->Q_answers_permutation);
                    break;
                    //-------------------------------------------//
            }
            $this->Q_question_data['question'] = $question;
        }
        
        $question_check_str = self::render_QUESTION();
        //die($this->Q_question_parts['text1']);
        return $question_check_str;
    }
    //=====================================================================//
    function render_QUESTION_parts($conf) // mode: (0-rand) | (1-DB) parameters
    {
        //=====================================================================//   
                      $ret_value = '';
        $qtype = strtolower($this->Q_question_data['qtype']);
        if ($qtype == 'c') {
            $query  = "SELECT vals FROM " . $this->tb_name . "_" . $qtype . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            $res    = mysql_query($query);
            $vals   = mysql_fetch_array($res);
            $fields = 'text1';
            for ($k = 1; $k < $this->Q_question_data['answers']; $k++) {
                //die('hi');
                $fields .= ', text' . ($k + 1);
            }
            $query = "SELECT " . $fields . " FROM " . $this->tb_name . "_" . $qtype . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            $res   = mysql_query($query);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            $Q_question_parts = mysql_fetch_assoc($res);
            // get token fields
            $fields           = "val1,min_val1,max_val1";
            for ($i = 2; $i <= $vals[0]; $i++) {
                $fields = $fields . ",val" . $i . ",min_val" . $i . ",max_val" . $i;
            }
            $query = " SELECT " . $fields . " FROM " . $this->tb_name . "_" . $qtype . " WHERE " . $this->tb_name . "_id=" . $this->Q_question_data['id'];
            $res   = mysql_query($query);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            $vdata = mysql_fetch_array($res);
            $mode  = intval(empty($conf));
            switch ($mode) {
                //-------------------------------------------//
                case 0:
                    //-------------------------------------------//
                    $vals = explode(',', $conf);
                    for ($i = 1; $i <= count($vals); $i++) {
                        for ($k = 1; $k <= $this->Q_question_data['answers']; $k++) {
                            $Q_question_parts['text' . $k] = str_replace($vdata["val" . $i], $vals[($i - 1)], $Q_question_parts['text' . $k]);
                        }
                    }
                    break;
                //-------------------------------------------//
                default:
                    //-------------------------------------------//
                    //die("asrij");
                    
                    /*   $rnv = array();
                    for ($i = 1; $i <= $vals[0]; $i++) {
                    if ($vdata["min_val" . $i] == 0 & $vdata["max_val" . $i] == 1) {
                    $rnv[($i - 1)] = rand(1, 9) / 10;  // fraction 0.x
                    } else {
                    $rnv[($i - 1)] = rand($vdata["min_val" . $i], $vdata["max_val" . $i]);
                    }
                    for($k=1;$k<=$this->Q_question_data['answers'];$k++){
                    $this->Q_question_parts['text'.$k] =  str_replace($vdata["val" . $i], $rnv[($i - 1)],  $this->Q_question_parts['text'.$k]);
                    }
                    }
                    */
                    break;
                    //-------------------------------------------//
            }
        for ($k = 1; $k <= $this->Q_question_data['answers']; $k++) {
            $ret_value .= "<div class='ITS_QUESTION'><table class='ITS_QUESTION'><tr><td class='ITS_QUESTION'>" . $Q_question_parts['text' . $k] . "</td></tr></TABLE></div>";
        }      
        }

        //die('returning '.$ret_value );
        return $ret_value;
    }
    //=====================================================================//
    function render_QUESTION()
    {
        //=====================================================================//
        $ques_str = $this->Q_question_data['question'];
        
        //echo $TABLE_QUESTION.'<hr>';
        //--- IMAGE ----------------//
        //echo getcwd() . "\n"; //die();
        //echo '<br>img src="' . $this->files_path . '--'.$this->Q_question_data['image'];
        //die();
        $iid = $this->Q_question_data['images_id'];
        if ($iid) {
            //---NEW---			
            $query_img = 'SELECT dir,name FROM images WHERE id=' . $iid;
            $res       = mysql_query($query_img);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            
            $row = mysql_fetch_assoc($res);
            $src = $this->files_path . '/' . $row['dir'] . '/' . $row['name'];
            //$src = 'ITS_FILES/images/question/5/lighthouse.png';
            
            //$img = '<img src="' . $this->files_path . $this->Q_question_data['image'] . '" class="ITS_question_img" alt="' . $this->files_path . $this->Q_question_data['image'] . '">';
            
            if (preg_match("/phpimg/i", $iid)) {
                //die($this->Q_question_data['image']);
                $img = '<img src="' . $this->files_path . $iid . '" class="ITS_question_img2" alt="' . $this->files_path . $iid . '">';
                //$img = '<a id="inline" href="#data"><img src="' . $this->files_path . $this->Q_question_data['image'] . '" class="ITS_question_img" alt="' . $this->files_path . $this->Q_question_data['image'] . '"></a>';
                //$img.= '<div style="display:none"><div id="data"><img src="' . $this->files_path . $this->Q_question_data['image'] . '" class="ITS_question_img" alt="' . $this->files_path . $this->Q_question_data['image'] . '"></div></div>';
            } else {
                $img = '<a id="single_image" href="' . $src . '" class="ITS_question_img" alt="' . $src . '"><img src="' . $src . '" class="ITS_question_img" alt="' . $src . '"></a>';
            }   
            //$img = '<a class="example2" href="' . $src . '"><img src="' . $src . '" alt="' . $src . '"></a>';
        } else {
            $img = '';
        }
        //echo $this->files_path . $this->Q_question_data['image'].'<br>';
        //$TABLE_IMAGE = $this->createImageTable('IMAGE_ID', $img, "ITS_QUESTION");
        $TABLE_QUESTION = $this->createEditTable('QUESTION', $img . $ques_str, "ITS_QUESTION");
        
        //print_r($TABLE_QUESTION);
        $TABLE_QUESTION = $this->renderFieldCheck($TABLE_QUESTION);
        //--------------------------//
        //echo $this->Q_question_data['questionConfig'];
        //if ($this->Q_question_data['questionConfig'] == 2){ //---- TITLED ----//
        //$question_str = '<table><tr><td>'.$TABLE_QUESTION.'</td></tr></table>'
        
        //$question_str = '<ul class="ITS_list" style="display: inline;"><li>'.$TABLE_QUESTION.'</li><li>'.$img.'</li></ul>';
        //$tb = new ITS_table('question_image',1,2,array($TABLE_QUESTION,$img),array(60,40),'ITS_QUESTION');
        //$question_str = $tb->str;
        //}else{
        //$question_str = "<p><DIV class=Question>" . $TABLE_QUESTION;
        //$question_str = $question_str . "</DIV>";
        //}
        $div_ITS_QUESTION = '<div class="ITS_QUESTION">' . $TABLE_QUESTION . '</div>';
        
        return $div_ITS_QUESTION;
    }
    //=====================================================================//
    function render_data()
    {
        //=====================================================================//
        //var_dump($this->Q_question_data['image']);
        
        if (empty($this->Q_question_data['image'])) {
            //<form method="POST" enctype="multipart/form-data"><input type="hidden" name="protocol" value="http"><input type="file" name="files[]" multiple></form>';
            //$img = '<form name="ITS_file" action="upload2.php" enctype="multipart/form-data" method="POST"><input name="ITS_image" size="10" type="file"><input id="testme" name="upload" value="Upload" type="submit"><input type="hidden" name="qid" value="'.$this->Q_question_data['id'].'" /></form>';
            $img = '<form name="ITS_file" action="ajax/ITS_image.php" enctype="multipart/form-data" method="POST"><input name="ITS_image" size="10" type="file"><input id="testme" name="upload" value="Upload" type="submit"><input type="hidden" name="qid" value="' . $this->Q_question_data['id'] . '" /><noscript><input type="submit" value="Submit"></noscript></form>';
        } else {
            $img = $this->Q_question_data['image'];
        }
        //var_dump($img);
        $qid = $this->Q_question_data['id'];
        if (empty($qid)) {
            $qid = 1;
        }
        
        $t        = new ITS_tag($this->tb_tags);
        $Q_T_arr  = $t->getByResource($this->tb_name, $qid);
        $Q_T_list = $t->renderList($Q_T_arr, $qid, $this->tb_name,'delete');
        
        $ts           = new ITS_tag($this->tb_tags . '_system');
        $Q_T_sys_arr  = $ts->getByResource($this->tb_name, $qid);
        $Q_T_sys_list = $ts->renderList($Q_T_sys_arr, $qid, $this->tb_name,'delete');
        
        //$Ques_tag_list = '';
        //var_dump($this->tb_name);die();
        
        //-- search box --//
        $s   = new ITS_search();
        $sb  = $s->renderBox($this->tb_name, $qid);
        $sbr = $s->renderResultsBox();
        //---
        //$tagBox = new ITS_tagInterface();
        //$tags   = $tagBox->displayTags($this->id,$qid,$tagBox->getTags($qid));
        //$stags  = $b.'<br>'.$tagBox->createSearchAddBox(1,$qid);
        //=======
        //echo $style;

        $style = 'ITS';
        $css   = 'ITS_QUESTION_DB';
        $dbT   = '<tr><td id="ITS_TAGS_LIST" colspan="7">' . $Q_T_list . $Q_T_sys_list . '</td></tr><tr><th colspan="6"><span style="float:left;text-align:right">SEARCH FOR TAGS:<br>or type the name of the tag to add&nbsp;</span>' . $sb . '</th></tr><tr><td colspan="7">' . $sbr . '</td></tr>';
        $db1   = '<tr><th colspan="2">TITLE</th><th>ANS</th><th>CATEGORY</th></tr><tr>' . '<td class="' . $css . '" colspan="2">' . $this->createEditTable('title', $this->Q_question_data['title'], $style) . '</td>' . '<td class="' . $css . '">' . $this->createEditTable('answers', $this->Q_question_data['answers'], $style) . '</td>' . '<td class="' . $css . '">' . $this->createEditTable('category', $this->Q_question_data['category'], $style) . '</td>';
        $db11  = '<tr><th>QUESTION<br>config</b></th><th>ANSWERS<br>config</th></tr><td class="' . $css . '">' . $this->createEditTable('title', $this->Q_question_data['questionConfig'], $style) . '</td>' . '<td class="' . $css . '">' . $this->createEditTable('answers', $this->Q_question_data['answersConfig'], $style) . '</td>' . '</tr>';
        $db2   = '';

        switch (strtolower($this->Q_question_data['qtype'])) {
            case 'c':
                //++++++++++++++//
                //var_dump($this->Q_question_data['answers']_data);die('render_data');             
                
                /*   $texts1  = $this->render_QUESTION_parts();
                if(count($texts)<2)
                $texts['text1'] = $texts1;
                else
                $texts = $texts1;
                //die("Count: ".$texts['text1']);*/
               
                $Nvals = $this->Q_answers_data['vals'];
                $db2 .= '<tr>';
                for ($k = 0, $l = 0; $k < $this->Q_question_data['answers']; $k++, $l++) {
                    $w = $k + 1;
                    $db2 .= '<th>formula' . $w . '</th>' . '<th>text' . $w . '</th>' . '<th>weight' . $w . '</th>';
                    $edit_tb[$k]  = $this->createEditTable('formula' . $w, $this->Q_answers_data['formula' . ($l + 1)], $style);
                    $edit_tbl[$l] = $this->createEditTable('text' . $w, $this->Q_answers_data['text' . ($l + 1)], $style); //$texts['text' . ($l + 1)]
                    $edit_tbw[$l] = $this->createEditTable('weight' . $w, $this->Q_answers_data['weight' . ($l + 1)], $style);
                }
                
                $ChkVal = '<input type="button" name="ShowPreview" id="ShowPreview" value="Value(s)">';
                $Nvals  = 2;
                $db2 .= '<th>' . $ChkVal . '</th><th>Min value</th><th>Max value</th></tr>' . '<tr>';
                for ($k = 0; $k < $this->Q_question_data['answers']; $k++) {
                    $db2 .= '<td rowspan="' . $Nvals . '" class="' . $css . '">' . $edit_tb[$k] . '</td>';
                    $db2 .= '<td rowspan="' . $Nvals . '" class="' . $css . '">' . $edit_tbl[$k] . '</td>';
                    $db2 .= '<td rowspan="' . $Nvals . '" class="' . $css . '">' . $edit_tbw[$k] . '</td>';
                }
                for ($f = 0; $f < $Nvals; $f++) {
                    $val_tb = $this->createEditTable('val' . ($f + 1), $this->Q_answers_data['val' . ($f + 1)], $style);
                    $min_tb = $this->createEditTable('min_val' . ($f + 1), $this->Q_answers_data['min_val' . ($f + 1)], $style); //$vals['min_val' . ($f + 1)]
                    $max_tb = $this->createEditTable('max_val' . ($f + 1), $this->Q_answers_data['max_val' . ($f + 1)], $style);
                    $db2 .= '<td class="ITS_QUESTION_DB">' . $val_tb . '</td><td class="' . $css . '">' . $min_tb . '</td><td class="' . $css . '">' . $max_tb . '</td></tr>';
                }
                //++++++++++++++//
                // DISPLAY 2
                $tb = '<ul id="ITS_ANSWER">';
                $tb = '<table class="CPROFILE">';
                foreach (array_keys($this->Q_answers_data) as $field) {
                    if (!empty($this->Q_answers_data[$field])) {
                        switch ($field) {
                            case "questions_id":
                                break;
                            case "images_id":
                                $tb .= '<tr><th width="5px">' . $field . '</th><td>image here</td></tr>';
                                break;   
                            default:
                                $tb .= '<tr><th width="5px">' . $field . '</th><td>' . $this->createEditTable($field, $this->Q_answers_data[$field], $style) . '</td></tr>';
                                //$tb .= '<li style="float:left;list-style-type: none;padding:4px"><table style="border:1px solid #999"><tr><th>'.$field.'</th></tr><tr><td>'.$this->Q_answers_data[$field].'</td></tr></table></li>';
                        }
                    }
                }
                $tb .= '</table>';
                break;
                default:
                $tb = '';      
               }
             
                $Qtb = '<ul id="ITS_ANSWER">';
                $Qtb = '<table class="CPROFILE">';
                foreach (array_keys($this->Q_question_data) as $field) {
                    if (!empty($this->Q_question_data[$field])) {
                        switch ($field) {
                            case "id":
                            case "question":
                            case "qtype":
                                break;
                            case "images_id":
                                $Qtb .= '<tr><th width="5px">' . $field . '</th><td><a href="">'.$this->Q_question_data[$field].'</a></td></tr>';
                                break;
                 /*          case "status":
				$optionArr    = array(
                    'delete',
                    'edit',
                    'publish'
                );
                $Qstatus = '<select id="Qstatus">';
                foreach ($optionArr as $op) {
                    if ($this->Q_question_data[$field] == $op) {
                        $sel = 'selected="selected"';
                    } else {
                        $sel = '';
                    }
                    $Qstatus .= '<option ' . $sel . '>' . $op . '</option>';
                }
                $Qstatus .= '</select>';
				$Qtb .= '<tr><th width="5px">' . $field . '</th><td><a href="">'.$Qstatus.'</a></td></tr>';
                                break;                          */        
                            default:
                                $Qtb .= '<tr><th width="5px">' . $field . '</th><td>' . $this->createEditTable($field, $this->Q_question_data[$field], $style) . '</td></tr>';
                                //$tb .= '<li style="float:left;list-style-type: none;padding:4px"><table style="border:1px solid #999"><tr><th>'.$field.'</th></tr><tr><td>'.$this->Q_answers_data[$field].'</td></tr></table></li>';
                        }
                    }
                } 
                $Qtb .= '</table>';
                //($this->Q_answers_fields);

        $QAtb    = '<table><tr><th>Question</th><th>Answers</th></tr><tr><td>' . $Qtb . '</td><td>' . $tb . '</td></tr></table>';
        $tagtb   = '<div id="tagContainer" style="display: none;"><table class="' . $css . '">' . $dbT . '</table></div>'; 
        //The Tags container
        $metaTog = '<div id="metaContainerToggle" class="Question_Toggle"><span>&raquo;&nbsp;metaData</span></div>';
        $tagTog  = '<div id="tagContainerToggle" class="Question_Toggle"><span>&raquo;&nbsp;Tags</span></div>';
        $str     = $tagTog . '' . $tagtb . '' . $metaTog . '<div id="metaContainer" style="display: none;"><center>' . $QAtb . '</center></div>'; //metaData Container
        return $str;
    }
    //=====================================================================//
    function get_ANSWERS_data_from_DB()
    {
        //=====================================================================//
        //echo 'get_ANSWERS_data_from_DB'.$this->Q_question_data['qtype']; die();
        //--- FIELDS
        $query = "DESCRIBE " . $this->tb_name . "_" . strtolower($this->Q_question_data['qtype']);
        //die($query);
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }

        $fields = array();
        for ($f = 0; $f < mysql_num_rows($res); $f++) {
            array_push($fields, mysql_result($res, $f));
            //echo $c.' - '.$fld.' - '.mysql_num_rows($res).'<br>';
        }
        
        $this->Q_answers_fields = implode(',', $fields);
        //--- DATA
        $query                  = "SELECT $this->Q_answers_fields FROM $this->tb_name" . "_" . strtolower($this->Q_question_data['qtype']) . " WHERE $this->tb_name" . "_" . "id=" . $this->Q_question_data['id'];
        //die($query);
        $res                    = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        $this->Q_answers_data = mysql_fetch_assoc($res);
    }
    //=====================================================================//
    function render_ANSWERS($name, $mode) // MODE: 1-Question | 2-EDIT
    {
        //=====================================================================//  
        //var_dump($this->Q_question_data);die();
        
        $answer_str = '';
        //--DEBUG--// ITS_debug($mode);
        $qtype      = strtolower($this->Q_question_data['qtype']);
        switch ($qtype) {
            //-------------------------------------------//
            case 's':
                //-------------------------------------------//
                $answer_str = $answer_str . '<form action=score.php method=post name=form1>' . '<textarea class=TXA_ANSWER name=TXA_ANSWER width=100% cols=80% rows=3></textarea>' . '<p><noscript><input type="submit" value="Submit"></noscript></form>';
                break;
            //-------------------------------------------//
            case 'mc':
                //-------------------------------------------// 
                $rows   = $this->Q_question_data['answers'];
                $width  = array(
                    2,
                    2,
                    96
                );
                $answer = array();
                
                //--DEBUG--// ITS_debug($rows);
                $str = '<p><div class="ITS_ANSWER_IMG">';
                for ($i = 1; $i <= $rows; $i++) {
                    if (($i % 2) == 0) {
                        $style = "ITS_ANSWER_STRIPE";
                    } else {
                        $style = "ITS_ANSWER";
                    }
                    
                    if ($this->Q_question_data['answersConfig'] == 3) {
                        $style = "ITS_ANSWER";
                    }
                    
                    $ans        = $this->Q_answers_data["answer" . $i];
                    $ans        = $this->renderFieldCheck($ans);
                    $answer[$i] = trim($ans);
                    $weight[$i] = $this->Q_answers_data["weight" . $i];
                    $image[$i]  = $this->renderQuestionImage($this->Q_answers_data["image" . $i], 0);
                    //var_dump($image[$i]);
                    //die($mode);
                    
                    switch ($mode) {
                        case 2: // 2-Edit
                            $ans        = $this->Q_answers_data["answer" . $i];
                            $answer[$i] = $this->createEditTable('ANSWER' . $i, trim($ans), 'ITS_ANSWER');
                            $answer[$i] = $this->renderFieldCheck($answer[$i]);
                            $weight[$i] = $this->createEditTable('WEIGHT' . $i, $this->Q_answers_data["weight" . $i], 'ITS_WEIGHT');
                            $image[$i]  = $this->createImageTable('IMAGE' . $i, $image[$i], 'ITS_IMAGE');
                            break;
                    }
                    
                    // solution check and selection
                    $checked = 'false';
                    $chk     = '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . chr($i + 64) . '" "' . $checked . '">';
                    //$chk  = "<input type=checkbox name=".$name."[".($i)."] value=".chr($i+64)." ".$checked.">";
                    //$data = array('<span id="TextAlphabet'.chr($i+64).'" class="TextAlphabet">'.chr($i+64).'.</span>',$chk,$edit_tb);
                    //$tb   = new ITS_table('ANSWER_'.$this->Q_question_data['qtype'],1,count($data),$data,$width,$style);
                    //$answer[$i] = $tb->str;
                    //$style = "ITS_ANSWER";
                    //$edit_tb2 = $this->createEditTable('ANSWER'.$i,'<span id="TextAlphabet'.chr($i+64).'" class="TextAlphabet">'.chr($i+64).'.</span>'.trim($this->Q_answers_data["answer".$i]),$style);
                    //$str = $str.'<li name="answerLab_active" id="answerLab_active" onclick=ITS_content_select(this)><span id="TextAlphabet'.chr($i+64).'" class="TextAlphabet">'.chr($i+64).'.</span>'.trim($this->Q_answers_data["answer".$i]).'</li>';
                    
                    $caption[$i] = '<span id="TextAlphabet' . chr($i + 64) . '" class="TextAlphabet">' . chr($i + 64) . '.</span>'; //chr($i+64)
                }
                //var_dump($this->Q_question_data['answersConfig']);//die();
                //echo $mode;
                //var_dump($this->Q_question_data['answersConfig']);die();
                $answer_str = new ITS_configure($this->Q_question_data['id'], $caption, $answer, $weight, $image, $this->Q_question_data['answersConfig'], $mode);
                //$str = $str.'</ul>';
                $answer_str = $answer_str->str;
                break;
            //-------------------------------------------//
            case 'p':
                //-------------------------------------------//
                // TEMPLATE
                $template = $this->Q_answers_data;
                if (!empty($template)) {
                    $TABLE_TEMPLATE = $this->createEditTable('TEMPLATE', $template[0], "ITS_TEMPLATE");
                    $answer_str     = $answer_str . '<br>' . $TABLE_TEMPLATE;
                }
                
                // ANSWERS
                switch ($mode) {
                    case 0:
                        $answer_str = '';
                        break;
                    case 2:
                        $answer_str = '';
                        break;
                    default:
                        for ($n = 1; $n <= $this->Q_question_data['answers']; $n++) {
                            $answer_str = $answer_str . '<textarea class="TXA_ANSWER" id="ITS_TA" name="' . $name . '"></textarea>';
                        }
                }
                break;
            //-------------------------------------------//
            case 'm':
                //-------------------------------------------//
                //$this->mode = 1;
                $n      = $this->Q_question_data['answers'];
                $ii     = 1;
                $L_list = '';
                for ($i = 1; $i <= $n; $i++) {
                    //echo $this->Q_answers_data["L".$i].' ~ '.$this->Q_answers_data["R".$i].'<p>';
                    $check_NULL  = !strcmp($this->Q_answers_data["L" . $i], 'NULL');
                    $check_EMPTY = empty($this->Q_answers_data["L" . $i]);
                    if (!(($check_NULL) OR ($check_EMPTY))) {
                        $L[$i - 1] = $i;
                    } else {
                        $L[$i - 1] = -$i;
                    } //$L_list .= $i.','; }//$nn++; }
                }
                $R = $L; //range(1,count($R_answers)/2);
                //print_r($R); die();
                
                switch ($mode) {
                    //-------------------------------------------//
                    case 2:
                        //-------------------------------------------//
                        $inactive = '_inactive';
                        break;
                    //-------------------------------------------//
                    case 1:
                        //-------------------------------------------//
                        $inactive = '';
                        shuffle($R);
                        //echo '<p>AFTER SHUFFLE: '.implode(',',$R).'<p>';
                        //print_r($R); echo '<p>count(R) mode=1: '.count($R).'<p>';
                        break;
                    //-------------------------------------------//
                    default:
                        //-------------------------------------------//
                        $inactive = '_inactive'; //print_r($this->Q_answers_permutation);
                        $R = $this->Q_answers_permutation;
                        /* DELETE THIS BLOCK: 
                        // Config from DB
                        $query    = 'SELECT comment FROM stats_' . $this->user_id . ' WHERE question_id=' . $this->Q_question_data['id'] . ' AND event<>"skip" ORDER BY id';
                        //echo 'IN MODE=default<p>'.$query; //die();
                        $res      = mysql_query($query);
                        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
                        }
                        
                        $C = mysql_fetch_array($res);
                        //echo 'C<p>'; //print_r($C);//echo count($C); die();
                        $R = explode(',', $C[0]); //$this->Q_answers_permutation;
                        */ 
                        //echo '<hr>';print_r($R); echo '<br>';print_r($RR);echo '<hr>';
                        //echo '<p>AFTER SHUFFLE (INACTIVE): '.implode(',',$R).'<p>';die();
                }
                
                // construct ANSWERS table
                $rows     = $this->Q_question_data['answers'];
                $width    = array(
                    10,
                    40,
                    50
                );
                //--------------------------------//
                // LEFT TABLE
                //--------------------------------//
                $nn       = 0;
                $tb_L_str = '';
                
                //echo $mode.'<pre>';var_dump($R);echo '</pre>';
                
                $class = 'ansCheck';
                $nn    = count($R);
                
                $ii = 1;
                $ik = 1;
                // echo $nn.' - ';
                for ($i = 1; $i <= $nn; $i++) {
                    $check_NULL  = !strcmp($this->Q_answers_data["L" . $i], 'NULL');
                    $check_EMPTY = empty($this->Q_answers_data["L" . $i]);
                    if (!(($check_NULL) OR ($check_EMPTY))) {
                        if (($i % 2) == 0) {
                            $style = "ITS_ANSWER_STRIPE";
                        } else {
                            $style = "ITS_ANSWER";
                        }
                        
                        $bank = '';
                        for ($b = 1; $b <= $n; $b++) {
                            //echo '<p>id='.$L_idx[$i-1].'_'.$b.'_'.$nn.'_'.$n.'<p>';
                            $bank .= '<label class="' . $class . $inactive . '" id="label_check_' . $i . '_' . $b . '_' . $nn . '_' . $n . '" for="check_' . $i . '_' . $b . '_' . $nn . '_' . $n . '"><input type="checkbox" class="' . $class . '" id="check_' . $i . '_' . $b . '_' . $nn . '_' . $n . '" name="checkL"/>' . chr($b + 64) . '</label>';
                        }
                        $style = '';
                        //echo '<p>'.$L_idx[$i-1].' - '.$this->Q_answers_data["L".$L_idx[$i-1]].'<p>';
                        //DEBUG: $edit_tb = $this->createEditTable('L'.$i,"L".$i.' * '.$this->Q_answers_data["L".$i],$style);
                        //echo 'MODE: '.$mode.'<p>';
                        switch ($mode) {
                            case 0:
                                $ans = $this->renderFieldCheck($this->Q_answers_data["L" . $i]);
                                break;
                            case 1:
                                $ans = $this->renderFieldCheck($this->Q_answers_data["L" . $i]);
                                break;
                            case 2:
                                //echo 'L' . $i.' -- '. $L_answers["L" . $i].'<br>';
                                $ans = $this->createEditTable('L' . $i, $this->Q_answers_data["L" . $i], $style);
                                $ans = $this->renderFieldCheck($ans);
                                $ig  = $this->renderQuestionImage($this->Q_answers_data["Limage" . $i], 1);
                                $img = $this->createImageTable('Limage' . $i, $ig, $style);
                                break;
                                //case 2: $ans = $this->createEditTable('L'.$i,$this->Q_answers_data["L".$i],$style); break;
                        }
                        $data[$ii - 1] = '<b>' . $ik . '. </b>';
                        $data[$ii]     = '<div class="' . $class . '">' . $bank . '</div>';
                        $data[$ii + 1] = $ans;
                        $data[$ii + 2] = $img;
                        
                        //$tb_L    = new ITS_table('ANSWER_'.$this->Q_question_data['qtype'].'_'.$i,1,count($data),$data,$width,$class);
                        //$tb_L_str = $tb_L_str.$tb_L->str;
                        //$tb_L_str .= '<li name="matchingLeft">'.$tb_L->str.'</li>';
                        $ii = $ii + 4;
                        $ik++;
                    }
                }
                //echo $n.'<pre>';var_dump($data);echo '</pre>';die();
                $tb_L     = new ITS_table('ANSWER_M_Left', count($data) / 4, 4, $data, array(
                    2,
                    4,
                    90,
                    4
                ), $class);
                $tb_L_str = $tb_L->str;
                
                //$tb_L_str = '<ul id="sortable1" class="ITS_ANSWER_M">'.$tb_L->str.'</ul>';
                //--------------------------------//
                // RIGHT TABLE
                //--------------------------------//
                //print_r($R_answers);die();
                $widthR   = array(
                    10,
                    90
                );
                $tb_R_str = '<table class="ITS_ANSWER_BOXED">';
                for ($i = 1; $i <= $n; $i++) {
                    //##echo '<p>'.$i.' - '.$R[$i-1].' - '.$R_answers["R".abs($R[$i-1])].'<p>';
                    //if ( $R[$i-1] > 0 ) { //
                    //if (!(is_null($R_answers["R".abs($R[$i-1])]))){
                    //if (($i % 2) == 0){ $style = "ITS_ANSWER_STRIPE"; }
                    //else 			        { $style = "ITS_ANSWER";        }
                    $style = "ITS_ANSWER";
                    $label = '<span class="TextAlphabet">' . chr($i + 64) . '</span>';
                    
                    //DEBUG: $edit_tb = $this->createEditTable('R'.$i,'('.$R[$i-1].') * '.$R_answers["R".abs($R[$i-1])],$style);
                    //var_dump($R); //echo ($R[$i-1]).' # <p>';//.(abs($R[$i-1])).' # '.$R_answers["R".(abs($R[$i-1]))].'<p><hr>';
                    //if (isset($R[$i-1])) { $ans = $R_answers["R".(abs($R[$i-1]))]; }
                    //else 								 { $ans = '--';                            }
                    //echo 'R' . (abs($R[$i - 1])).' -- '. $R_answers["R" . (abs($R[$i - 1]))].'<br>';
                    //$ans = $R_answers["R" . (abs($R[$i - 1]))];
                    
                    $ans = $this->createEditTable('R' . (abs($R[$i - 1])), $this->Q_answers_data["R" . (abs($R[$i - 1]))], $style);
                    $ans = $this->renderFieldCheck($ans);
                    /*
                    preg_match_all('/<img[^>]+>/i',$ans, $result);
                    if (!empty($result)) {
                    //print_r($result);
                    $img = array();
                    foreach( $result as $img_tag) {
                    print_r($img_tag);
                    //preg_match_all('/(src)=("[^"]*")/i',$img_tag, $img[$img_tag]);
                    }
                    //print_r($img);
                    } */
                    
                    $ig   = $this->renderQuestionImage($this->Q_answers_data["Rimage" . (abs($R[$i - 1]))], 1);
                    $Rimg = $this->createImageTable('Rimage' . $i, $ig, $style);
                    
                    //==$tb_R = new ITS_table('RT'.$this->Q_question_data['qtype'],1,2,array($label,$edit_tb),array(30,70),'ITSxx');
                    $data = array(
                        $label,
                        $ans,
                        $Rimg
                    );
                    
                    $style = 'CHOICE_ACTIVE';
                    $tb_R_str .= '<tr><td class="ITS_ANSWER_M">' . $label . '</td><td class="ITS_ANSWER_M"><div class="' . $style . '">' . $ans . '</div></td><td class="ITS_ANSWER_M"><div class="' . $style . '">' . $Rimg . '</div></td></tr>';
                    /*$form_image = '<form name="browser" action="server_browser.php"><input type="hidden" name="id" value="'.$this->Q_question_data['id'].'"><input type="hidden" name="col_name" value="Rimage' . $i.'"><input type="submit" value="Upload bb Image"></form>';
                    $tb_R_str .= '<td>'.$form_image.'</td></tr>';*/
                    //}
                }
                
                $tb_R_str .= '</table>';
                $this->Q_answers_permutation = $R;
                
                $tb         = new ITS_table('ANSWER_' . $qtype, 1, 2, array(
                    $tb_L_str,
                    $tb_R_str
                ), array(
                    50,
                    50
                ), 'ITS');
                $answer_str = $tb->str;
                break;
            //-------------------------------------------//
            case 'c':
                //-------------------------------------------//
                //$trial = $this->render_QUESTION_parts();
                $style = '';
                $class = 'ITS';
                /* for ($i=1;$i<=$N;$i++){
                $answer[$i] = $this->createEditTable('ANSWER'.$i,$this->data[$i],'ITS_ANSWER');
                $weight[$i] = $this->createEditTable('WEIGHT'.$i,$this->weight[$i],'ITS_WEIGHT');
                $tb = '<table class="ITS_ANSWER_BOXED"><tr><td class="ITS_ANSWER_BOXED_ALPH">'.$this->caption[$i].'</td><td class="ITS_ANSWER_BOXED"><span class="'.$style.'">'.$answer[$i].'</span></td><td><div class="CHOICE_WEIGHT">'.$weight[$i].'</div></td></tr></table>';
                $answer_str .= $tb;
                } */
                
                // ANSWERS
                switch ($mode) {
                    case 0:
                        $answer_str = '';
                        break;
                    case 2:
                        $answer_str = '';
                        break;
                    default: {
						$answer_str .= '<table>'; 
                        //Changes to make multiple answer boxes: SHOULD the IDS be different for diff boxes?? how is the scoring done
                        for ($k = 0; $k < $this->Q_question_data['answers']; $k++)
                            $answer_str .= '<tr><td style="text-align:right">'.$this->renderFieldCheck($this->Q_question_parts['text' . ($k + 1)]) . '</td><td><textarea class="TXA_ANSWER" id="ITS_TA' . $k . '" name="' . $name . '"></textarea></td></tr>';
                    }
                        $answer_str .= '</table><input type="hidden" value="' . $this->Q_question_data['answers'] . '" id="answersCount">';
                        // $answer_str .= '<textarea class="TXA_ANSWER" id="ITS_TA" name="' . $name . '"></textarea>';
                }
                break;
                //-------------------------------------------//
        }
        //$mysqldate = date( 'Y-m-d H:i:s', $phpdate );
        $this->timestamp = date('Y-m-d H:i:s');
        $div_ITS_ANSWER  = '<div class="ITS_ANSWER"><center>' . $answer_str . '</center></div>';
        
        return $div_ITS_ANSWER;
    }
    //=====================================================================//
    function set_ANSWERS_solution($solution)
    {
        //=====================================================================//
        switch (strtolower($this->Q_question_data['qtype'])) {
            //-------------------------------------------//
            case 's':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'mc':
                //-------------------------------------------//
                $n = $this->Q_question_data['answers'];
                for ($i = 0; $i <= $n - 1; $i++) {
                    if (($solution - 1) == $i) {
                        $weights[$i] = 100;
                    } else {
                        $weights[$i] = 0;
                    }
                }
                $this->Q_weights_values = $weights;
                break;
            //-------------------------------------------//
            case 'p':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'm':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'c':
                //-------------------------------------------//
                break;
        }
    }
    //=====================================================================//
    function get_ANSWERS_solution()
    {
        //=====================================================================//
        switch (strtolower($this->Q_question_data['qtype'])) {
            //-------------------------------------------//
            case 's':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'mc':
                //-------------------------------------------//
                $n         = $this->Q_question_data['answers'];
                $mx_weight = max($this->Q_weights_values);
                
                for ($i = 0; $i <= $n - 1; $i++) {
                    if ($this->Q_weights_values[$i] == $mx_weight) {
                        return chr(65 + $i);
                    }
                }
                break;
            //-------------------------------------------//
            case 'p':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'm':
                //-------------------------------------------//
                break;
            //-------------------------------------------//
            case 'c':
                //-------------------------------------------//
                break;
        }
    }
    //=====================================================================//
    function renderQuestionImage($img_val, $popup)
    {
        //=====================================================================//
        if ($img_val) {
            $query_img = 'SELECT dir,name FROM images WHERE id=' . $img_val;
            //echo $query_img.'<br>';
            $res       = mysql_query($query_img);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            
            $row = mysql_fetch_assoc($res);
            $src = $this->files_path . '/' . $row['dir'] . '/' . $row['name'];
            if ($popup) {
                $class = 'ITS_question_img';
                $img   = '<a id="single_image" href="' . $src . '" class="' . $class . '" alt="' . $src . '"><img src="' . $src . '" class="' . $class . '" alt="' . $src . '"></a>';
            } else {
                $class = 'none';
                $img   = '<img src="' . $src . '" class="' . $class . '" alt="' . $src . '">';
            }
        } else {
            $img = '';
        }
        //var_dump($img);echo '<br>';//die();
        return $img;
    }
    //=====================================================================//
    function renderQuestionForm($action)
    {
        //=====================================================================//    
        // var_dump(array_keys($this->Q_question_data));//die();
        // var_dump(array_keys($this->Q_answers_data));
        
        /* KEEP
        $tb = '<table style="1px solid blue">';
        foreach (array_keys($this->Q_answers_data) as $field ){
        $tb .= '<tr><td>'.$field . '</td><td>' . $this->Q_answers_data[$field] . '</td></tr>';
        }
        $tb .= '</table>'; //die();*/
        
        $act   = explode('~', $action);
                $class = 'text ui-widget-content ui-corner-all ITS_Q';
        $label_class = 'field_label';
        //echo $act[0]; die();
        
        //-- NEW: Set Defualt Values
        if ($act[0]=='new') {
			$this->Q_question_data['qtype'] = $act[1];
			$qtype = '<div id="navContainer">' . '<ul id="navListQC">' . '<li qtype="mc" value="mc" name="qtype"><a href="#" id="current">Multiple Choice</a></li>' . '<li qtype="m"  value="m"  name="qtype"><a href="#">Matching</a></li>' . '<li qtype="c"  value="c"  name="qtype"><a href="#">Calculated</a></li>' . '<li qtype="s"  value="s"  name="qtype"><a href="#">Short Answer</a></li>' . '<li qtype="p"  value="p"  name="qtype"><a href="#">Paragraph</a></li>' . '</ul>' . '</div>';
			            switch ($this->Q_question_data['qtype']) {
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'm':
                case 'mc':
                $this->Q_question_data['answers'] = 4;
               break;
               default:
               $this->Q_question_data['answers'] = 1;
		   }		
		} else {
			switch (strtolower($this->Q_question_data['qtype'])) {
                case 'm':  $field = 'MATCHING';break;
                case 'mc': $field = 'MULTIPLE CHOICE';break;
				case 'c':  $field = 'CALCULATED';break;
		   }
			 $qtype = '<p><b>'.$field.' QUESTION<b></p>';
		}
		/*
        $qtypes = array('Multiple Choice','Matching','Calculated','Short Answer','Paragraph');
        $qtype = '<select id="ITS_qtype" name="qtype" qid="'.$this->Q_question_data['id'].'">';
        for ($t=1; $t<=count($qtypes); $t++) {
        if ($this->Q_question_data['qtype']_arr[$t-1]==$act[1]) { $issel = 'selected="selected"'; }
        else                                  { $issel = '';                    }
        $qtype .= '<option '.$issel.' value="'.$this->Q_question_data['qtype']_arr[$t-1].'">'.$qtypes[$t-1].'</option>';
        }
        $qtype .= '</select>';*/
        
        $form = $tb . '<form id="Qform"><fieldset><table class="ITS_newQuestion">';
        $form .= '<tr>'.$qtype.'</tr>';
        
        foreach (array_keys($this->Q_question_data) as $field) {
            $label = '<label for="' . $field . '"><b>' . strtoupper(preg_replace('/_/', ' ', $field)) . ': </b></label>';
            //--------------------------//
            //echo $field.'<br>';
            switch ($field) {
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'id':
                case 'qtype':
                case 'verified':
                case 'verified_by':
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'answers':
                    //+++++++++++++++++++++++++++++++++++++++++++//
                    $ansMax = 10;
                    $Nans   = $this->Q_question_data[$field];
                    
                    /* $sel = '<select id="'.$fields[$i].'" name="'.$fields[$i].'" qid="'.$this->Q_question_data['id'].'"  style="float:right">';
                    for ($a=1; $a<=$ansMax; $a++) {
                    if ($a==$Nans) { $issel = 'selected="selected"'; }
                    else           { $issel = ''; }
                    $sel .= '<option '.$issel.' value="'.($a).'">'.$a.'</option>';
                    }
                    $sel .= '</select>'; */
                    
                    $sel   = '<input type="button" name="changeAnswer" id="addAnswer" v="+" value="+" class="ITS_buttonQ round">' . '<br><input type="button" name="changeAnswer" id="remAnswer" v="-" value="&mdash;" class="ITS_buttonQ round">';
                    $n     = $this->Q_question_data[$field];
                    $qtype = strtolower($this->Q_question_data['qtype']);
                    switch ($qtype) {
                        //-------------------------------------------//
                        case 's':
                            //-------------------------------------------//
                            break;
                        //-------------------------------------------//
                        case 'mc':
                            //-------------------------------------------//
                            //var_dump($this->Q_answers_data);
                            $sel .= '<input type="hidden" name="' . $field . '" value="' . $n . '">';
                            $ans = '<table id="ITS_Qans" class="ITS_Qans" n="' . $n . '" qtype="' . $qtype . '">' . '<tr><th width="5%">No.</th><th width="80%">Answer</th><th width="10%">Weight</th></tr>';
                            for ($a = 1; $a <= $n; $a++) {
                                $answer_field = '<textarea name="answer' . $a . '" id="answer' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['answer' . $a]) . '</textarea>';
                                $weight_field = '<textarea name="weight' . $a . '" id="weight' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['weight' . $a]) . '</textarea>';
                                $ans .= '<tr><td>' . $a . '</td><td>' . $answer_field . '</td><td>' . $weight_field . '</td></tr>';
                            }
                            $ans .= '</table>';
                            //$form .= '<tr id="ansQ"><td>'.$label.'<br>'.$sel.'<span id="ansUpdate" class="ansUpdate" action="'.$action.'">update</span></td><td>'.$ans.'</td></tr>';
                            $form .= '<tr id="ansQ"><td>' . $label . '<br>' . $sel . '</td><td>' . $ans . '</td></tr>';
                            break;
                        //-------------------------------------------//
                        case 'p':
                            //-------------------------------------------//
                            break;
                        //-------------------------------------------//
                        case 'm':
                            //-------------------------------------------//         
                            $sel .= '<input type="hidden" name="' . $field . '" value="' . $n . '">';
                            $ans = '<table id="ITS_Qans" class="ITS_Qans" n="' . $n . '" qtype="' . $qtype . '">' . '<tr><th width="5%">No.</th><th width="45%">Left</th><th width="45%">Rright</th></tr>';
                            for ($a = 1; $a <= $n; $a++) {
                                $L_field = '<textarea name="L' . $a . '" id="answer' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data["L" . $a]) . '</textarea>';
                                $R_field = '<textarea name="R' . $a . '" id="R' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data["R" . $a]) . '</textarea>';
                                $ans .= '<tr><td>' . $a . '</td><td>' . $L_field . '</td><td>' . $R_field . '</td></tr>';
                            }
                            $ans .= '</table>';
                            $form .= '<tr id="ansQ"><td>' . $label . '<br>' . $sel . '</td><td>' . $ans . '</td></tr>';
                            break;
                        //-------------------------------------------//
                        case 'c':
                            //-------------------------------------------//  
                            $sel  = '<input type="hidden" name="vals" value="' . $this->Q_answers_data['vals'] . '">';
                            $sel1 = '<input type="hidden" name="' . $field . '" id="answers" value="' . $n . '"><input type="button" name="changeAnswer" id="add_fcount" v="+" value="+" class="ITS_buttonQ round">' . '<br><input type="button" name="changeAnswer" id="dec_fcount" v="-" value="&mdash;" class="ITS_buttonQ round">';
                            
                            // FORMULAS:
                            $FORMULAS = '<table id="ITS_QansF" class="ITS_Qans" n="' . $n . '" qtype="' . $qtype . '">' . '<tr><th width="5%">No.</th><th width="30%">Text</th><th width="55%">Formula</th><th width="10%">Weight</th></tr>';
                            
                            for ($a = 1; $a <= $n; $a++) {
                                $FORMULAS .= '<tr id="tr_formula' . $a . '">' . '<td>' . $a . '</td>' . '<td><textarea name="text' . $a . '" id="text' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['text' . $a]) . '</textarea></td>' . '<td><textarea name="formula' . $a . '" id="formula' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['formula' . $a]) . '</textarea></td>' . '<td><textarea name="weight' . $a . '" id="weight' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['weight' . $a]) . '</textarea></td></tr>';
                            }
                            $FORMULAS .= '</table><p> * Weights must sum up to 100</p>';
                            
                            // VARIABLES:
                            $Nvars     = $this->Q_answers_data['vals'];
                            $VARIABLES = '<table id="ITS_Qans" class="ITS_Qans" n="' . $Nvars . '" qtype="' . $qtype . '">' . '<tr><th width="5%">No.</th><th width="75%">Variable</th><th width="10%">Min</th><th width="10%">Max</th></tr>';
                            for ($a = 1; $a <= $Nvars; $a++) {
                                $VARIABLES .= '<tr><td>' . $a . '</td><td width="40%"><textarea type="text" name="val' . $a . '" id="val' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['val' . $a]) . '</textarea></td>' . '<td width="10%"><textarea type="text" name="min_val' . $a . '" id="minvalue' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['min_val' . $a]) . '</textarea></td>' . '<td width="10%"><textarea type="text" name="max_val' . $a . '" id="maxvalue' . $a . '" class="' . $class . '">' . htmlspecialchars($this->Q_answers_data['max_val' . $a]) . '</textarea></td>' . '</tr>';
                            }
                            $VARIABLES .= '</table>';
                            
                            $form .= '<tr id="ansQ"><td><b>FORMULAS:</b><br>' . $sel1 . '</td><td>' . $FORMULAS . '</td></tr>';
                            $form .= '<tr id="ansQ"><td><b>VARIABLES:</b><br>' . $sel . '</td><td>' . $VARIABLES . '</td></tr>';
                            //die($form);
                            break;
                    }
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'question':
                    //+++++++++++++++++++++++++++++++++++++++++++//
                    $field = '<textarea name="' . $field . '" id="' . $field . '" class="' . $class . '" style="height:100px">' . htmlspecialchars($this->Q_question_data[$field]) . '</textarea>';
                    $form .= '<tr><td style="width:10%;padding:0.25em;text-align:right">' . $label . '</td><td colspan="5">' . $field . '</td></tr>';
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'images_id':
                    //+++++++++++++++++++++++++++++++++++++++++++//
                    $field = '<input type="file" size="50%" name="' . $field . '" id="' . $field . '" value="' . htmlspecialchars($this->Q_question_data[$field]) . '" class="' . $class . '"/>';
                    $form .= '<tr><td style="width:10%;padding:0.25em;text-align:right">' . $label . '</td><td colspan="5" style="text-align:center">' . $field . '</td></tr>';
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'questionConfig':
                    //+++++++++++++++++++++++++++++++++++++++++++//                
                    ///*
                    $conf = $this->Q_question_data[$field];
                    if ($conf == 2) {
                        $cmt = '&nbsp; Question with image on the side';
                    } else {
                        $cmt = '';
                    }
                    $sel = '<select id="' . $field . '" name="' . $field . '" style="margin:0">';
                    for ($a = 1; $a <= 2; $a++) {
                        if ($a == $conf) {
                            $issel = 'selected="selected"';
                        } else {
                            $issel = '';
                        }
                        $sel .= '<option ' . $issel . ' value="' . ($a) . '">' . $a . '</option>';
                    }
                    $sel .= '</select>';
                    $field = $sel . $cmt;
                    $form .= '<tr><td style="width:10%;padding:0.25em;text-align:right">' . $label . '</td><td colspan="5">' . $field . '</td></tr>';
                    //*/
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'answersConfig':
                    //+++++++++++++++++++++++++++++++++++++++++++//        
                    $Nans   = $Q_question_data[$field];
                    $cmtArr = array(
                        "LIST",
                        "TILED",
                        "MATRIX",
                        "IMAGES"
                    );
                    $sel    = '<select id="' . $field . '" name="' . $field . '" style="margin:0">';
                    $issel  = '';
                    $cmt    = '';
                    for ($a = 1; $a <= 4; $a++) {
                        if ($a == $Nans) {
                            $issel = 'selected="selected"';
                            $cmt   = '&nbsp;' . $cmtArr[$a - 1];
                        } else {
                            $issel = '';
                            $cmt   = '';
                        }
                        $sel .= '<option ' . $issel . ' value="' . ($a) . '">' . $a . '</option>';
                    }
                    $sel .= '</select>';
                    $field = $sel . $cmt;
                    $form .= '<tr><td style="width:10%;padding:0.25em;text-align:right">' . $label . '</td><td colspan="5">' . $field . '</td></tr>';
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'category':
                    //+++++++++++++++++++++++++++++++++++++++++++//
                    $issel = '';
                    $sel   = '<select id="' . $field . '" name="' . $field . '" class="'.get_class($this).'">';
                    $query = 'SELECT DISTINCT ' . $field . ' FROM ' . $this->tb_name . ' GROUP BY category';
                    //echo $query.'<p>';
                    $res   = mysql_query($query);
                    if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
                    }
                    //$categories = mysql_fetch_row($res);
                    //var_dump($categories);
                    for ($c = 0; $c < mysql_num_rows($res); $c++) {
                        $val = mysql_result($res, $c);
                        //echo $c.' - '.$val.' - '.$field.' - '.mysql_num_rows($res).'<br>';
                        if ($this->Q_question_data[$field] == $val) {
                            $issel = 'selected="selected"';
                        } else {
                            $issel = '';
                        }
                        $sel .= '<option ' . $issel . ' value="' . $val . '">' . $val . '</option>';
                    }
                    $sel .= '</select>';
                    $form .= '<tr><td class="'.$label_class.'">' . $label . '</td><td colspan="5">' . $sel . '</td></tr>';
                    mysql_free_result($res);
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                case 'author':
                    //+++++++++++++++++++++++++++++++++++++++++++// 
                    $form .= '<input type="hidden" name="' . $field . '" value="'.htmlspecialchars($this->user_id).'">';
                    break;
                //+++++++++++++++++++++++++++++++++++++++++++//
                default:
                    //+++++++++++++++++++++++++++++++++++++++++++//
                    $field = '<textarea name="' . $field . '" id="' . $field . '" class="' . $class . '">' . htmlspecialchars($this->Q_question_data[$field]) . '</textarea>';
                    $form .= '<tr><td class="'.$label_class.'">' . strtoupper($label) . '</td><td colspan="5">' . $field . '</td></tr>';
            }
        }
        $buttons = '<div id="cancelDialog" class="ITS_button" style="float:right">Cancel</div>' . '<div id="PreviewDialog" class="ITS_button" style="float:right">Show Preview</div>' . '<div id="submitDialog" class="ITS_button" style="float:right">Create New Question</div>';
        $form .= '</table>' . $buttons . '</fieldset><noscript><input type="submit" value="Submit"></noscript></form>';
        $dialog = '<div title="Create New Question" id="xxy">' . $form . '</div>';
        
        return $dialog;
    }
    //=====================================================================//
    function render_Admin_Nav($qid, $qtype, $style)
    {
        //=====================================================================// 
        
        switch ($qid) {
    case 0:
        $nav .= '<input type="button" class="' . $style . '" onclick="ITS_QCONTROL_EDITMODE(this)" name="editMode" value="Edit" status="true">';
        break;
    default:
        $nav .= '<input type="button" class="' . $style . '" id="createQuestion" name="new"   value="New"   qid="' . $qid . '" qtype="' . $qtype . '">' . '<input type="button" class="' . $style . '" id="cloneQuestion"  name="clone" value="Clone" qid="' . $qid . '" qtype="' . $qtype . '">' . '<input type="button" class="' . $style . '" id="importQuestion" name="new"   value="import QTI" qid="' . $qid . '">' . '<input type="button" class="' . $style . '" id="exportQuestion" name="export"   value="export to QTI" qid="' . $qid . '">' . '<input type="button" class="' . $style . '" id="exportManyQuestion" name="export_many"   value="export multiple question" qid="' . $qid . '">' . '<input type="button" class="' . $style . '" onclick="ITS_QCONTROL_EDITMODE(this)" name="editMode" value="Edit" status="true">';
        break;
}
        return $nav;
    }
    //=========================================================================//
    function returnResult($varlist, $rand_values_list, $formula)
    {
        //=====================================================================//
        $pattern     = '/([\d.]+)\*\*[\(]([\d\+\-\*\/]+)[\)]/';
        $replacement = 'pow($1,$2)';
        $equation    = preg_replace($pattern, $replacement, $formula);
        $newFormula  = $equation;
        for ($i = 0; $i < count($varlist); $i++) {
            $newFormula = str_replace($varlist[$i], $rand_values_list[$i], $newFormula);
        }
        $solution = 0;
        eval("\$solution=" . $newFormula . ";");
        //$result = eval($newf);
        return $solution;
    }
    //=====================================================================//
    function createEditTable($TargetName, $Target, $style)
    {
        //=====================================================================//
        // eg. createEditTable('TITLE','This is my title',$style);
        // die($this->tex_path);
        // echo '|div id="ITS_'.$TargetName.'_TARGET" class="ITS_TARGET" code="'.htmlspecialchars($Target_str).'" path="'.$this->tex_path.'"|<hr>';
        
        /*if(stristr($TargetName, 'image')!=FALSE && $Target!='') {
        $tb  = '<img src="'.$this->files_path.$Target.'">';
        }
        else {
        $tb  = $Target;
        }*/
        
        $Table = '<table class="' . $style . '">' . '<tr>' . '<td class="' . $style . '">' . '<div id="ITS_' . $TargetName . '_TARGET" class="ITS_TARGET" code="' . htmlspecialchars($Target) . '">' . $Target . '</div>' . '</td>' . '<td class="ITS_EDIT">' . '<span class="ITS_QCONTROL" id="ITS_' . $TargetName . '" ref="' . strtolower($TargetName) . '"></span></td></tr></table>';
        
        //echo '<pre>';print_r($Table);echo '</pre>';
        //code="' . htmlspecialchars($Target) . '"
        //path="' . $this->tex_path . '"
        
        return $Table;
    }
    //=====================================================================//
    function createImageTable($TargetName, $Target, $style)
    {
        //=====================================================================//
        // eg. createEditTable('TITLE','This is my title',$style);
        //die($this->tex_path);
        //echo '|div id="ITS_'.$TargetName.'_TARGET" class="ITS_TARGET" code="'.htmlspecialchars($Target_str).'" path="'.$this->tex_path.'"|<hr>';
        
        //if(stristr($TargetName, 'image')!=FALSE && $Target!=''){ $tb  = '<img src="'.$this->files_path.$Target.'">'; }
        //else 												     { $tb  = $Target; }
        
        //var_dump($Table);       
        //code="' . $Target . '"
        $Table = '<table class="' . $style . '">' . '<tr>' . '<td class="' . $style . '">' . '<div id="ITS_' . $TargetName . '_TARGET" class="ITS_TARGET">' . $Target . '</div>' . '</td>' . '<td class="' . $style . '">' . '<span class="ITS_ICONTROL" id="ITS_' . $TargetName . '" ref="' . strtolower($TargetName) . '"></span></td></tr></table>';
        //$debug = htmlspecialchars($Table);echo $debug.'<hr>';
        return $Table;
    }
    //=====================================================================//
    function renderFieldCheck($field)
    {
        //=====================================================================//
        // LATEX: <latex>latex_code</latex> ==> TEX img
        
        $field = latexCheck($field, $this->tex_method, $this->tex_path);
        
        return $field;
    }
    //=====================================================================//
}
//eo:class
//=====================================================================//
function latexCheck($str, $method, $path)
{
    //=====================================================================//
    //ITS_debug(); /die($path);
    //echo $str.' '.$method.' '.$path.'<hr>';
    switch ($method) {
    case 'mathtex': // server-side
		$replacement = '<img class="ITS_LaTeX" latex="${1}" src="' . $path . ' ${1}"/>';
        break;
    case 'mathJax': // client-side
        $replacement = '\(${1}\)';
        break;
	}
    
    $pattern = "/<latex>(.*?)<\/latex>/im";
    $str = preg_replace($pattern, $replacement, $str);

    //echo '<center><div style="background:yellow">'.$str.'</div></center><hr>'; // die();
    return $str;
}
?>
