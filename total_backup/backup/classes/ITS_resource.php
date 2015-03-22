<?php
/*=====================================================================//
ITS_resource - search box class.
ajax/ITS_resource.php

Constructor: ITS_resource( ... )	
								ex. $r = new ITS_resource( ... );
Author(s): Greg Krudysz
Last Update: Sep-20-2013
//=====================================================================*/
class ITS_resource
{
    public function __construct($id,$term,$tag)
    {
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
        $this->concept = $tag;
    }
    //=====================================================================//
    public function renderBox($rtb, $rid)
    {
        //=====================================================================//
        //if (empty($rating)) { $rating = 0; }
        //$box = '<hr class="ITS_search"><input id="ITS_search_box" type="text" name="keyword" rtb="'.$rtb.'" rid="'.$rid.'">'.
        //       '<div class="ITS_search"></div></p>';	
        $box =  '<div id="resourceContainer"><span>&raquo;&nbsp;Resources</span></div><div id="resourceContainerContent">';    			
        $Rstr = '<input id="ITS_search_box" type="text" name="keyword" rtb="' . $rtb . '" rid="' . $rid . '">';
        $box .= $Rstr . '</div>';
        
        return $box;
    }
    //=====================================================================//
    public function renderResources()
    {
        //=====================================================================//	
        $box = '<hr class="ITS"><div class="ITS_search"></div>';
        
        return $box;
    }
    //=====================================================================//
    public function renderContainer()
    {
        //=====================================================================//	
        $tharr = array(
            'Text',
            'Equation',
            'Image',
            'Example'
        );
        $th    = ''; //'<th></th>';
        $td    = ''; //'<td>' . $this->concept . '</td>';
        for ($n = 0; $n < count($tharr); $n++) {
            $td .= '<th><input type="button" name="selectResource" concept="'.$this->concept.'" value="' . $tharr[$n] . '" class="ITS_res"></th>';
            $th .= '<td id="ITS_resource_'.strtolower($tharr[$n]).'_'.$this->concept.'" rid=""></td>';
        }
        $tb .= '<table class="ITS_resource"><tr>' . $th . '</tr><tr>' . $td . '</tr></table>';
        
        return $tb;
    }
    //=====================================================================//
    public function getResource($field)
    {
        //=====================================================================//	
        //$queryQid = 'SELECT dspfirst_ids FROM dspfirst_map WHERE tag_id='.$this->concept;        
        //$query    = 'SELECT * FROM dspfirst WHERE id IN ('.$queryQid.')';
        //die($this->concept);
        switch ($field) {
            //+++++++++++++++++++++++++++++++++++++++++++//
            case 'text':
                $query = 'SELECT id,content FROM dSPFirst WHERE content REGEXP "'.$this->concept.'" AND meta="paragraph"';
                break;
            case 'equation':
                //SELECT content FROM dSPFirst WHERE  name REGEXP "sampling" AND meta="equation";
                $query = 'SELECT id,code FROM concepts WHERE  name REGEXP "'.$this->concept.'"';
                break;
            case 'image':
                //$query = 'SELECT content FROM dSPFirst WHERE name REGEXP "sampling" AND meta="image"';
                $query = 'SELECT id,dir,name FROM images WHERE name REGEXP "'.$this->concept.'"';
                break;
            case 'example':
                $query = 'SELECT id,statement,solutions,term,year FROM SPF WHERE title REGEXP "'.$this->concept.'" OR keywords REGEXP "'.$this->concept.'" LIMIT 5';
                break;
        }
        
        //echo $query.'<br>';
        $id_arr = array();
        $li_arr = array();
        $res    = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        
        if (!empty($res)) {
            switch ($field) {
                //---//
                case 'equation':
                    //---//
                    $path = '../cgi-bin/mathtex.cgi?\large ';
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
						$id   = mysql_result($res, $i,0);
                        $code = mysql_result($res, $i,1);
                        if (!empty($code)) {
							array_push($id_arr,$id);
                            array_push($li_arr, '<img class="ITS_LaTeX" src="' . $path . $code . ' "/>');
                        }
                    }
                    break;
                //---//
                case 'image':
                    //---//
                    $path = 'ITS_FILES/';
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
						$id   = mysql_result($res, $i);
                        $code = trim(mysql_result($res, $i, 'dir')) . '/' . trim(mysql_result($res, $i, 'name'));
                        if (!empty($code)) {
                            //echo $path.$code.'<br>';
                            $img = '<a id="single_image" href="' . $path . $code . '" class="ITS_question_img" title="' . $path . $code . '"><img src="' . $path . $code . '" class="ITS_question_img ITS_resource_img" alt="' . $path . $code . '"></a>';
                            array_push($id_arr,$id);
                            array_push($li_arr,$img);
                        }
                    }
                    break;
                //---//
                case 'example':
                    //---//
                    $path  = 'ITS_FILES/';
                    $path1 = 'ITS_FILES/SPFIRST/PNGs/';
                    $path2 = 'ITS_FILES/SPFIRST/solutions/';
                    //+++++++++
                    $idx   = 1;
                    while ($row = MySQL_fetch_array($res)) {
                        //echo '<pre>';var_dump($row);echo '</pre>';die();
                        $id   	   = $row['id'];
                        $fname     = $row['statement'];
                        $solutions = $row['solutions'];
                        $term      = $row['term'];
                        switch ($term) {
                            case 'Spring':
                            case 'Summer':
                                $t = $term[0] . $term[1];
                                break;
                            case 'Fall':
                            case 'Winter':
                                $t = $term[0];
                                break;
                        }
                        $year    = $row['year'];
                        $fname   = preg_replace('/.pdf/', '.png', $fname);
                        $f       = $path1 . strtolower($t) . '_' . $year[2] . $year[3] . '/' . $fname;
                        //echo $path;die();
                        $sol_arr = explode(',', $solutions);
                        
                        $sol_list = '';
                        foreach ($sol_arr as $s) {
                            if (empty($s)) {
                                $sol_list .= '';
                            } else {
                                $pathS = $path2 . strtolower($t) . '_' . $year[2] . $year[3] . '/' . $s;
                                $sol_list .= '<a id="single_image" href="' . $pathS . '" class="ITS_question_img" title="' . $pathS . '"><img src="' . $pathS . '" class="ITS_question_img ITS_resource_img" alt="' . $s . '"></a>';
                            }
                        }
                        //echo '<pre>';var_dump($sol_list);echo '</pre>';die();
                        //$sol  = '<div class="file"><a href="'.$solutions.'" target="_blank"><img alt="'.$solutions.'" src="'.$solutions.'" /></a></div>';
                        
                        $sol  = '<div class="file">' . $sol_list . '</div>';
                        $file = '<a id="single_image" href="' . $f . '" class="ITS_question_img" title="' . $f . '"><img src="' . $f . '" class="ITS_question_img ITS_resource_img" alt="' . $fname . '"></a>';
                        //echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['score']}</td></tr>";   
                        array_push($id_arr,$id);
                        array_push($li_arr,$file.'<br>'.$sol_list);
                    } //while
                    break;
                default:
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
						array_push($id_arr,mysql_result($res, $i,0));
                        array_push($li_arr, mysql_result($res, $i,1));
                    }
                    //var_dump($li_arr);die();
                    //+++//
            }
            switch ($field) {
                //---//
                case 'text':
                    //---//
                    $tList = '<table class="CPROFILE">';
                    for ($i = 0; $i < count($li_arr); $i++) {
                        $tList .= '<tr><td><span class="ITS_List">' . $li_arr[$i] . '</span></td><td><input id="aa" type="button" name="resourceSelect" value="select" field="'.$field.'" rid="'.$id_arr[$i].'" concept="'.$this->concept.'"></td></tr>';
                    }
                    $tList .= '</table>';
                    break;
                default:
                    $tList = ''; //'<ul class="ITS_list">';
                    for ($i = 0; $i < count($li_arr); $i++) {
                        //$tList .= '<li><table><tr><td>' . $li_arr[$i] . '</td></tr><tr><td><input type="button" value="SELECT"></td></tr></li>';
                        $tList .= '<div class="fl"><table class="tt"><tr><td><div class="ITS_resource_box">' . $li_arr[$i] . '</div></td></tr><tr><td><input type="button" name="resourceSelect" value="select" field="'.$field.'" rid="'.$id_arr[$i].'" concept="'.$this->concept.'"></td></tr></table></div>';
                    }
                    //$tList .= '</ul>';
            }
        }
        return '<center>' . $tList . '</center>';
    }
        //=====================================================================//
    public function setResource($field,$rid)
    {
        //=====================================================================//
        $callback = 'resourceDelete';
        $action = 'delete';
                switch ($field) {
            //+++++++++++++++++++++++++++++++++++++++++++//
            case 'text':
                $query = 'SELECT content FROM dSPFirst WHERE id='.$rid;
                break;
            case 'equation':
                //SELECT content FROM dSPFirst WHERE  name REGEXP "sampling" AND meta="equation";
                $query = 'SELECT code FROM concepts WHERE id='.$rid;
                break;
            case 'image':
                //$query = 'SELECT content FROM dSPFirst WHERE name REGEXP "sampling" AND meta="image"';
                $query = 'SELECT dir,name FROM images WHERE id='.$rid;
                break;
            case 'example':
                $query = 'SELECT statement,solutions,term,year FROM SPF WHERE id='.$rid;
                break;
        }
        
        //echo $query.'<br>';
        $li_arr = array();
        $res    = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        
        if (!empty($res)) {
            switch ($field) {
                //---//
                case 'equation':
                    //---//
                    $path = '../cgi-bin/mathtex.cgi?\large ';
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
                        $code = mysql_result($res, $i);
                        if (!empty($code)) {
							array_push($id_arr,$id);
                            array_push($li_arr, '<img class="ITS_LaTeX" src="' . $path . $code . ' "/>');
                        }
                    }
                    break;
                //---//
                case 'image':
                    //---//
                    $path = 'ITS_FILES/';
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
                        $code = trim(mysql_result($res, $i, 'dir')) . '/' . trim(mysql_result($res, $i, 'name'));
                        if (!empty($code)) {
                            //echo $path.$code.'<br>';
                            $img = '<a id="single_image" href="' . $path . $code . '" class="ITS_question_img" title="' . $path . $code . '"><img src="' . $path . $code . '" class="ITS_question_img ITS_resource_img" alt="' . $path . $code . '"></a>';
                            array_push($li_arr,$img);
                        }
                    }
                    break;
                //---//
                case 'example':
                    //---//
                    $path  = 'ITS_FILES/';
                    $path1 = 'ITS_FILES/SPFIRST/PNGs/';
                    $path2 = 'ITS_FILES/SPFIRST/solutions/';
                    //+++++++++
                    $idx   = 1;
                    while ($row = MySQL_fetch_array($res)) {
                        //echo '<pre>';var_dump($row);echo '</pre>';die();
                        $fname     = $row['statement'];
                        $solutions = $row['solutions'];
                        $term      = $row['term'];
                        switch ($term) {
                            case 'Spring':
                            case 'Summer':
                                $t = $term[0] . $term[1];
                                break;
                            case 'Fall':
                            case 'Winter':
                                $t = $term[0];
                                break;
                        }
                        $year    = $row['year'];
                        $fname   = preg_replace('/.pdf/', '.png', $fname);
                        $f       = $path1 . strtolower($t) . '_' . $year[2] . $year[3] . '/' . $fname;
                        //echo $path;die();
                        $sol_arr = explode(',', $solutions);
                        
                        $sol_list = '';
                        foreach ($sol_arr as $s) {
                            if (empty($s)) {
                                $sol_list .= '';
                            } else {
                                $pathS = $path2 . strtolower($t) . '_' . $year[2] . $year[3] . '/' . $s;
                                $sol_list .= '<a id="single_image" href="' . $pathS . '" class="ITS_question_img" title="' . $pathS . '"><img src="' . $pathS . '" class="ITS_question_img ITS_resource_img" alt="' . $s . '"></a>';
                            }
                        }
                        //echo '<pre>';var_dump($sol_list);echo '</pre>';die();
                        //$sol  = '<div class="file"><a href="'.$solutions.'" target="_blank"><img alt="'.$solutions.'" src="'.$solutions.'" /></a></div>';
                        
                        $sol  = '<div class="file">' . $sol_list . '</div>';
                        $file = '<a id="single_image" href="' . $f . '" class="ITS_question_img" title="' . $f . '"><img src="' . $f . '" class="ITS_question_img ITS_resource_img" alt="' . $fname . '"></a>';
                        //echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['score']}</td></tr>";   
                        array_push($li_arr,$file.'<br>'.$sol_list);
                    } //while
                    break;
                default:
                    for ($i = 0; $i < mysql_num_rows($res); $i++) {
                        array_push($li_arr, mysql_result($res, $i));
                    }
                    //var_dump($li_arr);die();
                    //+++//
            }
            switch ($field) {
                //---//
                case 'text':
                    //---//
                    $tList = '<table class="CPROFILE">';
                    for ($i = 0; $i < count($li_arr); $i++) {
                        $tList .= '<tr><td><span class="ITS_List">' . $li_arr[$i] . '</span></td><td><input id="aa" type="button" name="'.$callback.'" value="'.$action.'" field="'.$field.'"></td></tr>';
                    }
                    $tList .= '</table>';
                    break;
                default:
                    $tList = ''; //'<ul class="ITS_list">';
                    for ($i = 0; $i < count($li_arr); $i++) {
                        //$tList .= '<li><table><tr><td>' . $li_arr[$i] . '</td></tr><tr><td><input type="button" value="SELECT"></td></tr></li>';
                        $tList .= '<div class="fl"><table class="tt"><tr><td><div class="ITS_resource_box">' . $li_arr[$i] . '</div></td></tr><tr><td><input type="button" name="'.$callback.'" value="'.$action.'" field="'.$field.'" rid="'.$rid.'"></td></tr></table></div>';
                    }
                    //$tList .= '</ul>';
            }
        }
        return '<center>' . $tList . '</center>';    
	}
        //=====================================================================//
    public function saveResource($id,$concept_id,$text,$equation,$image,$example)
    {
        //=====================================================================//      
        $comment = $text.','.$equation.','.$image.','.$example;
        $query_str = 'INSERT IGNORE INTO ' . $this->tb_user .$id . ' (concept_id,comment,epochtime,event) VALUES(' . $concept_id . ',"' .$comment. '",' . time() . ',"resource")';

        $res    = mysql_query($query_str);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        return $query_str; 
	}
        //=====================================================================//
	public function getQuestions($tag_id)
    {
        //=====================================================================//  
        $query = 'SELECT id,question,title,category FROM questions AS q,questions_tags AS qt WHERE q.id =qt.questions_id AND qt.tags_id='.$tag_id;
        //echo $query.'<br>';die();
        $res    = mysql_query($query);
        if (!$res) {
            die('Query execution problem in '.get_class($this).': ' . msql_error());
        }
        $Q   = new ITS_question($this->id, $this->db_name, $this->tb_name);
        $qn = 1;
        $Estr  = '<table class="PROFILE">' . '<tr><th>No.</th><th style="width:77%;">Title</th><th style="width:14%;">Category</th></tr>';
        while ($row = MySQL_fetch_array($res)) {
                        //echo '<pre>';var_dump($row);echo '</pre>';die();
                    //$str .= $row['id'].$row['title'].$row['category'].'<br>';    
           $ques = $Q->renderFieldCheck($row['question']);
           $Estr .= '<tr class="PROFILE" id="tablePROFILE">' . '<td class="PROFILE" >' . ($qn) . '.&nbsp;<a href="Question.php?qNum=' . $row['id'] . '&sol=1" class="ITS_ADMIN">' . $row['id'] . '</a></td>' . '<td class="PROFILE"><div style="text-align:left;color:blue;font-size:105%;border-bottom:2px dashed #999;width:100%">' . $row['title'] . '</div><br><p style="color: grey">'.$ques.'</p></td>' . '<td class="PROFILE" ><p style="color: grey">' . $row['category'] . '</p></td></tr>';    
        $qn++;
        }
        $Estr .= '</table>';                    
            
			/*
            $Q     = new ITS_question($qid, $db_name, $tb_name);
            $Q->load_DATA_from_DB($qid);
            //echo $qid;
            $QUESTION = $Q->render_QUESTION(); //_check($answers[$qn][4]);
            $Q->get_ANSWERS_data_from_DB();
            $ANSWER = $Q->render_ANSWERS('a', 2);
            */
                        
        return $Estr;
    }			
    //=====================================================================//
	public function getEq($edit_flag)
    {
        //=====================================================================//

			$con = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die('Could not Connect!');			
			$Q   = new ITS_question($this->id, $this->db_name, $this->tb_name);
			
			mysql_select_db($this->db_name, $con) or die('Could not select DB');
			$query = 'SELECT code FROM concepts_tags AS ct LEFT JOIN concepts AS c ON c.id=ct.concepts_id WHERE ct.tags_id=(SELECT id FROM tags WHERE name="'.$this->concept.'")';

			// echo $query.'<hr>';
			
			$res = mysql_query($query, $con);
			if (!$res) {
				die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
			}
			//echo mysql_num_rows($res).'<hr>';
			//$concepts_result = mysql_fetch_assoc($res);

			$equation = '';
			for ($x = 0; $x < mysql_num_rows($res); $x++) {
				$row = mysql_fetch_assoc($res);
				foreach ($row AS $r) {
					// echo $r.'<hr>';
					if ($r){
					$tex = $Q->renderFieldCheck('<latex>'.$r.'</latex>');	
					$eq = '<span class="CHOICE">'.$tex.'</span>';
					
					$equation .= '<a id="single_image" class="ITS_question_img" href="IT">'.$eq.'</a>';	
					$style = 'ITS';
					$w = '100px';
					$edit = $Q->createEditTable('equation' . $w, $r, $style);	
					
				// $TABLE_QUESTION = $this->createEditTable('QUESTION',$ques_str, "ITS_QUESTION");
				// $TABLE_QUESTION = $this->renderFieldCheck($TABLE_QUESTION);							
						
					//echo $edit.'<br>';
					}
				}
			}	
          
        return $equation;
    }			
    
} //eo:class
//=====================================================================//
?>
