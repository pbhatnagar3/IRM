<?php
/*=====================================================================//
ITS_concepts- query DB for concept browser.

Constructor: ITS_concepts()

ex. $query = new ITS_concepts('tableA',2,2,array(1,2,3,4),array(20,30));

Author(s): Khyati Shrivastava | May-10-2012
Last Revision: Greg Krudysz, Jul-20-2013
//=====================================================================*/
/*
if(isset($_REQUEST['tbvalues'])){
$tbvalues = $_REQUEST['tbvalues'];
$obj = new ITS_concepts();
$val = $obj->getRelatedQuestions($tbvalues);
echo $val;
}
if(isset($_REQUEST['moduleName'])){
$moduleName = $_REQUEST['moduleName'];
$tbvalues = $_REQUEST['tbvaluesQ'];
$tbvaluesConcp = $_REQUEST['tbvaluesConcp'];
$obj = new ITS_concepts();
$val = $obj->createModule($moduleName,$tbvalues,$tbvaluesConcp);
echo $val;
}*/
// echo getcwd() . "\n";
// var_dump($_REQUEST['choice']);

class ITS_concepts
{
	public $id;
	public $term;
    public $tb_name;
    public $db_user;
    public $db_pass;
    public $db_host;
    public $db_name;
    public $debug;
    public $mode;
    
    //=====================================================================//
    function __construct($id,$term)
    {
        //=====================================================================//
        //echo 'NOW'; die();
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class();
        }
        global $db_dsn, $db_name, $tb_name, $db_table_user_state, $tex_path;
        
        $dsn = preg_split("/[\/:@()]+/", $db_dsn);
        //foreach ($dsn as $value) {echo $value.'<br>';}
        
        $this->id      = $id;
        $this->term    = $term;
        $this->db_user = $dsn[1];
        $this->db_pass = $dsn[2];
        $this->db_host = $dsn[4];
        $this->db_name = $dsn[6];
        $this->tb_user = $db_table_user_state;
        // echo "Values: ".$this->db_host.$this->db_user.$this->db_pass;
    }
    //=====================================================================//
    function getConceptNav($concept,$tag_id)
    {
        //=====================================================================//    
        
        $score_str = $this->getConceptScore($tag_id);        
        
        // render concept name
        //$concept = str_replace('-',' ',$concept);
        $concept = (strstr($concept,'()')) ? '<code>'.$concept.'</code>' : ucwords($concept);
                 
        $str = '<div class="navConcept" tid="' . $tag_id . '">' . $concept . '</div><div>'.$equation.'</div><span class="navConceptInfo">' . $score_str . '</span><br><hr>';
        
        return $str;
    }
     //=====================================================================//
    function getConceptScore($tag_id)
    {
        //=====================================================================//    
        
        $s     = new ITS_score($this->id,$this->term,time());
        $info  = $s->computeConceptScores($tag_id);
        $score = $s->renderConceptScores($info);

        return $score;
    }   
    //=====================================================================//
    function getConcepts($letter, $all_flag, $order)
    {
        // $all_flag = 1 // print all concepts available, even with no questions
        //=====================================================================//    
        
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');

        $list_arr = array(
            'name',
            'attempted',
            'available',
            'score'
        ); //'chapter'
        
        $list   = '<div id="navcontainer2"><ul id="navlist2">';
        $active = '';
        foreach ($list_arr as $key => $value) {
            if ($key == $order) {
                $li_id  = ' id="active"';
                $a_id   = ' id="current"';
                $active = $value;
            } else {
                $li_id = '';
                $a_id  = '';
            }
            $list .= '<li ' . $li_id . '><a href="#" ' . $a_id . ' class="concept_orderby" idx="' . $key . '">' . $value . '</a></li>';
        }
        $list .= '</ul></div>';
        
        //$query = "SELECT name FROM SPFindex WHERE name LIKE '" . $letter . "%' ORDER BY name";
        //$query = "SELECT name FROM index_1 WHERE name LIKE '" . $letter . "%' AND chapter_id=3 ORDER BY name";
        //$query = "SELECT name FROM tags WHERE name LIKE '" . $letter . "%' AND synonym=0 ORDER BY name";

        if ($letter == 'ALL') {
            $where = '';
        } else {
            $where = ' WHERE name LIKE "' . $letter . '%"';
        }
        if ($all_flag) {
            $having = '';
        } else {
            $having = ' HAVING available>0 ';
        }
        
        switch ($active) {
            case 'name':
                $orderby = 'ORDER BY name ASC';
                break;
            case 'chapter':
                $orderby = 'ORDER BY ' . $active . ' DESC';
                break;
            case 'attempted':
                $orderby = 'ORDER BY ' . $active . ' DESC';
                break;
            case 'available':
                $orderby = 'ORDER BY ' . $active . ' DESC';
                break;
            case 'score':
                $orderby = 'ORDER BY ' . $active . ' DESC';
                break;
        }
				
	$query = 'SELECT t.id,t.name,count(s.question_id) AS attempted,count(q.id) AS available, ROUND(AVG(s.score),1) AS score 
FROM tags AS t 
LEFT JOIN questions_tags AS qt ON t.id = qt.tags_id AND t.synonym=0
LEFT JOIN questions AS q ON q.id = qt.questions_id AND q.qtype IN ("M","MC","C") AND q.status="publish"
LEFT JOIN '.$this->tb_user.$this->id.' AS s ON s.tags = qt.tags_id AND s.question_id = qt.questions_id AND event = "concept" 
' . $where . '
GROUP BY name' . $having . ' ' . $orderby;				      
       
        // echo $query;  //die();

        // ALTER TABLE its.tags DROP question_id
        // ALTER TABLE its.tags DROP concept_id
        // ALTER TABLE its.tags ADD COLUMN synonym INT, ADD FOREIGN KEY tags_id(synonym) REFERENCES tags(id) ON DELETE CASCADE;
        
        // die($query);
        $res = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        $N = 10; // list items per column
        
        //$str = $list . '<div id="conceptColumnContainer">';
        //B: $str = $list . '<div id="conceptColumnContainer"><div id="conceptColumnX"><ul class="conceptListX">';     
        $str = $list . '<div id="conceptColumnContainer"><div id="conceptColumnB"><ul class="conceptListB">';
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $mod = $x % $N;
            //if ($mod == 0) {
            //    $str .= '<div class="conceptColumn"><ul class="conceptList">';
            //}
            //echo $mod.'<br>';
            $row = mysql_fetch_assoc($res);
            //var_dump($row);
            if (empty($row['score'])) {
                $row['score'] = '&ndash;';
            } else { $row['score'] .= '&nbsp;%'; }
            
            $conceptData = '';
            foreach (array_reverse($list_arr) as $key => $value) {
                    //echo $x.$list_arr[$x].'<br>';
                    if ($key<count($list_arr)-1){
                    $class = ($active==$value) ? ' chighlight' : '';
                    $conceptData .= '<span class="conceptCount'.$class.'">' . $row[$value] . '</span>';
				}
            }
            // render concept name
            $name = $row['name']; 	//$name = str_replace('-',' ',$row['name']);
            $name = (strstr($name,'()')) ? '<code>'.$name.'</code>' : ucwords($name);
            
            $name_str  = $name;
            $class_sel = 'selcon';
            if ($row['available']){
				$class_sel = ($row['attempted']==$row['available']) ? 'selcon' : 'selcon';
				$name_str  = ($row['attempted']==$row['available']) ? '<strike>&nbsp;'.$name.'&nbsp;</strike>' : $name;
			}
			
            //A: $str .= '<li  id="con_' . $row['name'] . '" tid="' . $row['id'] . '" class="'.$class_sel.'">' .$name_str . $conceptData.'</li>';
            //B:
            $str .= '<li  id="con_' . $row['name'] . '" tid="' . $row['id'] . '" class="'.$class_sel.'">' .$name_str.$conceptData.'</li>';   
            //C:$str .= '<li  id="con_' . $row['name'] . '" tid="' . $row['id'] . '" class="'.$class_sel.'"><h3>' .$name_str .'</h3><p>'.$conceptData.'</p></li>';
            //D: $str .= '<li  id="con_' . $row['name'] . '" tid="' . $row['id'] . '" class="'.$class_sel.'"><h3>' .$name_str .'</h3>'.'</li>';
            //$str .= ''.$x.'</div>';
            
            //if ($mod == ($N - 1) || ($x == (mysql_num_rows($res) - 1))) {
            //    $str .= '</ul></div>';
            //}
        }
        $str .= '</ul></div></div>';
        //#2 $str .= '</div>';
        
        mysql_free_result($res);
        mysql_close($con);
        //echo htmlspecialchars($str);
        
        if ($str != '')
        //return "<center><ul class='conceptLIST'>" . $str . "</ul></center>";
            return $str;
        else
            return $str;
    }
    //=====================================================================//
    function getConceptQuestion($concept)
    {
        //=====================================================================//  
        $arr_val  = split(',', $concept);
        $str_vals = "'" . $arr_val[0] . "'";
        for ($i = 1; $i < sizeof($arr_val); $i++) {
            $str_vals .= ",'" . $arr_val[$i] . "'";
        }
        //$query = "SELECT id FROM ".$this->tb_name." w WHERE w.tag_id in (SELECT tag_id FROM SPFindex i WHERE i.name in (".$str_vals."))";
        //$query = "SELECT id FROM ".$this->tb_name." w WHERE w.id IN (SELECT questions_id FROM questions_tags q WHERE q.tags_id IN (SELECT tags_id FROM SPFindex i WHERE i.name IN (".strtolower($str_vals).")))";
        //$query = "SELECT questions_id FROM questions_tags q WHERE q.tags_id IN (SELECT id FROM tags i WHERE i.name IN (" . strtolower($str_vals) . "))"; //AND verified=1";	
					
$query = 'SELECT q.id,q.qtype
FROM tags AS t 
LEFT JOIN questions_tags AS qt ON t.id = qt.tags_id AND t.synonym=0 
LEFT JOIN questions AS q ON (q.id = qt.questions_id AND q.qtype IN ("M","MC","C") AND q.status="publish") 
LEFT JOIN '.$this->tb_user.$this->id.' AS s ON s.tags = qt.tags_id AND s.question_id = qt.questions_id AND event = "concept" 
WHERE t.name='.$str_vals.' AND q.qtype IN ("M","MC","C") AND event IS NULL';			
		
		// echo '<div style="color:red">'.$query.'</div>';
		// die();
        
        return $query;
    }    
    //=====================================================================//
    // returns all questions when given a set of concepts to be matched with tags associated with the questions
    function getRelatedQuestions($tbvalues)
    {
        //=====================================================================//
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');
        //die($tbvalues);
        $arr_val  = split(',', $tbvalues);
        $str_vals = "'" . $arr_val[0] . "'";
        for ($i = 1; $i < sizeof($arr_val); $i++) {
            $str_vals .= ",'" . $arr_val[$i] . "'";
        }
        $query = "SELECT id,question FROM questions w WHERE w.id IN (select questions_id from questions_tags q where q.tags_id IN (SELECT tags_id FROM SPFindex i WHERE i.name IN (" . $str_vals . ")))";
        //SELECT id,question FROM questions w where w.id IN (select questions_id from questions_tags q where q.tags_id in (SELECT tags_id FROM SPFindex i where i.name IN ('Matlab')));
        //die($query);
        
        $res = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        //$concepts_result = mysql_fetch_assoc($res);
        $str = '<table id="ques" class="PROFILE"><tbody><tr><th style="width:5%"><input type="checkbox" id="chckHead"/></th><th style="width:15%;">No.</th><th style="width:80%;">Question</th></tr>';
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row = mysql_fetch_assoc($res);
            $str .= "<tr class='PROFILE'><td class='PROFILE'><input type='checkbox' name='chcktbl' class='chcktbl' id='chcktbl' value=" . $row['id'] . ">" . "</td><td class='PROFILE'>" . "<a href='Question.php?qNum=" . $row['id'] . "' target=”_blank” class='ITS_ADMIN'>" . $row['id'] . "</a>" . "</td><td class='PROFILE'>" . $row['question'] . "</td></tr>";
        }
        $str .= '</tbody></table>';
        
        mysql_free_result($res);
        mysql_close($con);
        
        return $str;
        
    } // End of getRelatedQuestions()  
    /*
     * This function creates entries in the Db for a module
     * If module name already exist, it simply adds question to that module
     * It also adds the tags associated with the module in module_tag table
     */
    //=====================================================================//
    function createModule($moduleName, $tbvalues, $tbvaluesConcp)
    {
        //=====================================================================//
        $returnStr = 'Server returned error initial';
        
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB in ' . get_class($this));
        $ques_ids  = split(',', $tbvalues);
        $tag_names = split(',', $tbvaluesConcp);
        $query     = "SELECT mid FROM module WHERE title = '$moduleName'";
        $res       = mysql_query($query, $con);
        $row       = mysql_fetch_assoc($res);
        // Module name does not exist
        if (empty($row)) {
            //die('ji');
            $query = "INSERT INTO module(title) VALUES ('$moduleName')";
            $res   = mysql_query($query, $con);
            if (!$res)
                return $returnStr . '1' . $query;
            $module_id = mysql_insert_id();
        } else {
            //$row = mysql_fetch_assoc($res);
            $module_id = $row['mid'];
        }
        
        // Adding question to the module created.
        $query = "INSERT IGNORE INTO module_question(mid,qid) VALUES ";
        $query .= "($module_id," . $ques_ids[0] . ")";
        for ($i = 1; $i < count($ques_ids); $i++) {
            $query .= ",($module_id," . $ques_ids[$i] . ")";
        }
        $res = mysql_query($query, $con);
        if (!$res)
            return $returnStr . '2' . $query;
        else
            $returnStr = "ok! $moduleName Saved!!";
        
        // Add the relation between module ids and tags selected.
        $str_vals = "'" . $tag_names[0] . "'";
        for ($i = 1; $i < sizeof($tag_names); $i++) {
            $str_vals .= ",'" . $tag_names[$i] . "'";
        }
        
        $query = "SELECT tag_id FROM index_1 WHERE name in (" . $str_vals . ")";
        $res   = mysql_query($query, $con);
        //die($query);
        
        if (!$res) {
            die('here?' . $query);
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row         = mysql_fetch_assoc($res);
            $tag_ids[$x] = $row['tag_id'];
            //die($row['tag_id']);
        }
        $tags_ids_imploded = implode(',', $tag_ids);
        $tag_ids           = explode(',', $tags_ids_imploded);
        $query             = "INSERT IGNORE INTO module_tag(mid,tag_id) VALUES ";
        $query .= "($module_id, " . $tag_ids[0] . ")";
        for ($i = 1; $i < count($tag_ids); $i++) {
            $query .= ",($module_id, " . $tag_ids[$i] . ")";
        }
        //die($query);
        $res = mysql_query($query, $con);
        if (!$res) {
            return $returnStr . $query;
        } else
            $returnStr = "ok! $moduleName Saved!!";
        
        mysql_free_result($res);
        mysql_close($con);
        
        return $returnStr;
    } // End of createModule function
    //=====================================================================//
    //<a href="Question.php?qNum=990" class="ITS_ADMIN">990</a>
    function ConcQuesContainer()
    {
        //=====================================================================//
        $str = '<div id="ConcQuesContainer"><form id="qform" name="qform"></form></div>';
        return $str;
    }
    //=====================================================================//
    function SelectedConcContainer($mode)
    {
        //=====================================================================//
        $box = '<div id="resourceContainer"><span>&raquo;&nbsp;Resources</span></div><div id="resourceContainerContent">';
        
        $str = '<div id="SelectedConcContainer"><table id="seldcon" class="conceptTable"></table>';
        if ($mode == 0) // 0 is for Instructor mode
            $str .= '<input type="button" id="submitConcepts" name="submit" value="Submit Concepts"></div>';
        else if ($mode == 1) // 1 is for Student mode
            $str .= '<div id="resourceList" class="ITS_meta"></div><input type="button" id="getQuesForConcepts" name="getQuesForConcepts" class="ITS_submit" value="Get Questions"></div><br><br><div id="resourceList" class="ITS_meta"></div></div>';
        
        $box .= $str . '</div>';
        
        return $box;
    }
    //=====================================================================//
    function conceptListContainer($letter, $role)
    {
    //=====================================================================//
        
        $role_flag = ($role == 'admin' OR $role == 'instructor') ? 1 : 0;
        
        $str = '<div id="conceptListContainer">' . $this->getConcepts($letter, $role_flag, 0) . '</div><div id="errorConceptContainer"></div>';
        
        return $str;
    }
    //=====================================================================//
    function showLetters()
    {
    //=====================================================================//
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');
        $query = 'SELECT DISTINCT LEFT(name,1) FROM tags t WHERE t.synonym=0 ORDER BY name';
		// echo $query;
        $res   = mysql_query($query, $con);
        $rand  = rand(1, mysql_num_rows($res));
        
        $str = '<ul class="nav"><li><a href="#" name="ITS_alph_index" value="ALL">ALL</a></li>';
        for ($x = 1; $x <= mysql_num_rows($res); $x++) {
            $row = mysql_fetch_row($res);
            $val = strtoupper($row[0]);
            
            if (!fmod($x, 15)) {
                $str .= '<br><hr class="concept">';
            }
            $idx_id = ($x == $rand) ? 'id="current"' : '';
            $str .= '<li><a href="#" name="ITS_alph_index" ' . $idx_id . ' value="' . $val . '">' . $val . '</a></li>';
        }
        $str .= '</ul>';
        return $str;
    }
    //=====================================================================//
    function moduleList($choice) // switch case ->? 0 for 1st page, 1 for drop down
    {
        //=====================================================================//
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!>');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');
        $query = "SELECT mid,title FROM module";
        $res   = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        $str = '';
        switch ($choice) {
            case 0:
                for ($x = 0; $x < mysql_num_rows($res); $x++) {
                    $row = mysql_fetch_assoc($res);
                    $str .= "<li id='" . $row['title'] . "'><div  align='left' id='" . $row['title'] . "' class='modules'>" . $row['title'] . "</div></li>";
                }
                if ($str != '')
                    $str = "<ul>" . $str . "</ul>";
                break;
            case 1:
                for ($x = 0; $x < mysql_num_rows($res); $x++) {
                    $row = mysql_fetch_assoc($res);
                    $str .= "<option value='" . $row['title'] . "' >" . $row['title'] . "</option>";
                }
                //if($str!='')
                $str = "<div id='moduleListDialogDiv'><select id='moduleListDD' class='moduleListDD'><option value='0'>Create a new module..</option>" . $str . "</select></div>";
                break;
            default:
                $str = 'server error';
        }
        mysql_free_result($res);
        return $str;
    }
    //=====================================================================//
    function getModuleQuestion($modulesQuestion)
    {
        //=====================================================================//
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');
        $query = "SELECT qid FROM module_question where mid in (select mid from module where title='$modulesQuestion')";
        $res   = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        //    return $query ;
        if ($row = mysql_fetch_assoc($res))
            $str = $row['qid'];
        else
            $str = '';
        for ($x = 1; $x < mysql_num_rows($res); $x++) {
            $row = mysql_fetch_assoc($res);
            $str .= ',' . $row['qid'];
        }
        
        // Fetch Tags for the Modules
        $query = "SELECT name FROM SPFindex WHERE tags_id IN (SELECT tag_id FROM module_tag WHERE mid = (SELECT mid FROM module WHERE title='$modulesQuestion'))";
        //return $query;
        $res   = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        $count = 0;
        
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row                     = mysql_fetch_assoc($res);
            $associatedTagsArray[$x] = $row['name'];
        }
        if (count($associatedTagsArray) > 0)
            $associatedTags = implode(',', $associatedTagsArray);
        else
            $associatedTags = "No Tags Associated";
        $query = "select id,question from questions w where w.id in ($str)";
        //return $query.$str;
        $res   = mysql_query($query, $con);
        if (!$res) {
            return "No questions for this module";
            //die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        //$concepts_result = mysql_fetch_assoc($res);
        $str2 = '<table id="ques" class="PROFILE"><tbody><tr><th style="width:5%;"><input type="checkbox" id="chckHead"/>' . '</th><th style="width:80%">Question' //</th><th style="width:15%;">Tags Associated'
            . '</th></tr>';
        
        for ($x = 0; $x < mysql_num_rows($res); $x++) {
            $row = mysql_fetch_assoc($res);
            /* to fetch tags associated with each question in the module
            $query = "SELECT tag_id FROM webct WHERE id=$row['id']";
            $res = mysql_query($query,$con);
            if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
            }
            $row_tags = mysql_fetch_assoc($res);
            $associatedTagsArray = $row_tags['tag_id'];
            if(count($associatedTagsArray)>0)
            $associatedTags = implode(',',$associatedTagsArray);
            else
            $associatedTags = "No Tags Associated";
            */
            
            $str2 .= "<tr class='PROFILE'><td class='PROFILE'>" . "<input type='checkbox' name='chcktbl' class='chcktbl' id='chcktbl' value=" . $row['id'] . "><br><br>" . "<a href='Question.php?qNum=" . $row['id'] . "' target=”_blank” class='ITS_ADMIN'>" . $row['id'] . "</a>" . "</td><td class='PROFILE'>" . $row['question'] . "</td>"
            //."<td class='PROFILE'>$associatedTags</td>"
                . "</tr>";
        }
        $str2 .= '</tbody></table>';
        //return $str;
        return $str2;
    }
    //=====================================================================//
    // delete from module_question where mid=(select mid from module where title=$ModuleName) AND qid IN (string of tbvalues!)
    function deleteModuleQuestion($deleteQuestion, $ModuleName)
    {
        //=====================================================================//
        $con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');
        mysql_select_db($this->db_name, $con) or die('Could not select DB');
        $query = "delete from module_question where mid=(select mid from module where title='$ModuleName') AND qid IN ($deleteQuestion)";
        
        //die($query);
        $res = mysql_query($query, $con);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        return "Successful deletion";
    }
    //=====================================================================//
    public function updateScore()
    {
        //=====================================================================//
        $str = ''; //'<span class="todo">concept scoreboard here</span>';
        return $str;
    }
    //=====================================================================//
} // eof class
?>
