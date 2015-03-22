 <?php
/*=====================================================================//
ITS_tag - tag related.

Constructor: ITS_tag()    
ex. $ITS_tag = new ITS_tag();

API: * getByResource($resource_table, $resource_id,$tags_table_extra)

Author(s): Greg Krudysz |  Jul-14-2013                            
//=====================================================================*/

class ITS_tag
{
    public $tb_tags;
    
    //=====================================================================//
    function __construct($tbTags)
    {
        //=====================================================================//
        global $db_dsn, $db_name, $tb_name, $tb_tags, $db_table_user_state, $tex_path, $files_path;
        
        $this->tb_tags = $tbTags;
        $this->db_dsn  = $db_dsn;
        
        // connect to database
        $mdb2 =& MDB2::connect($db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($this->mdb2->getMessage());
        }
        
        $this->mdb2 = $mdb2;
    }
    //=====================================================================//
    function getByResource($rtb, $rid)
    {
        //=====================================================================//
        $query_tag_id = 'SELECT ' . $this->tb_tags . '_id FROM ' . $rtb . '_' . $this->tb_tags . ' WHERE ' . $rtb . '_id=' . $rid;
        // die($query_tag_id);
        $res          = mysql_query($query_tag_id);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $arr[] = $row[$this->tb_tags . '_id'];
        }
        //var_dump($arr); die();
        
        return $arr;
    }
    //=====================================================================//
    function getByKeyword($keyword, $exclude)
    {
        /* arr[id][name] */
        //=====================================================================//      
        //$query = "SELECT id,name FROM ".$this->tb_tags." WHERE name LIKE '$keyword%' ORDER BY name";
        //echo 'getByKeyword<br>';
        if (!empty($exclude)) {
            $filter = ' AND id NOT IN (' . implode(",", $exclude) . ')';
        } else {
            $filter = '';
        }
        $query = 'SELECT id,name FROM ' . $this->tb_tags . ' WHERE name REGEXP "^' . $keyword . '"' . $filter . ' ORDER BY name';
        //die($query);
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        while ($arr[] = mysql_fetch_array($res, MYSQL_NUM));
        //echo "<pre>";print_r ($list);echo "</pre>";die('out');
        return $arr;
    }
    //=====================================================================//
    function query($keyword, $exclude)
    {
        /* arr[id][name] */
        //=====================================================================//      
        //echo 'query<br>';
        if (!empty($exclude)) {
            $filter = ' AND id IN (' . implode(",", $exclude) . ')';
        } else {
            $filter = '';
        }
        
        $query = 'SELECT id,name FROM ' . $this->tb_tags . ' WHERE name="' . $keyword . '"' . $filter;
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        while ($arr[] = mysql_fetch_array($res, MYSQL_NUM));
        //echo "<pre>";print_r ($arr);echo "</pre>";die('out');
        
        return $arr;
    }
    //=====================================================================//
    function query2($rname)
    {
        //=====================================================================//      
        
        $tList = '<table class="DATA">';
        for ($l = 0; $l < 26; $l++) {
            $query = 'SELECT id,name,synonym FROM ' . $this->tb_tags . ' WHERE name LIKE "' . chr(65 + $l) . '%" ORDER BY name';
            // echo $query; die($query);
            
            $res = mysql_query($query);
            if (!$res) {
                die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
            }
            $tagList = '';
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {               
                $tagList .= $this->render($row["id"], $row["name"], 0, $rname, 'deleteDB',true);
            }
            
            $tList .= '<tr><td class="DATA_list">' . chr(65 + $l) . '</td><td class="DATA_list2">' . $tagList . '</td></tr>';
        }
			$query = 'SELECT id,name,synonym FROM ' . $this->tb_tags . ' WHERE name REGEXP "^[^A-Za-z]" ORDER BY name';
            // echo $query; die($query);
            
            $res = mysql_query($query);
            if (!$res) {
                die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
            }
            $tagList = '';
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                $tagList .= $this->render($row["id"], $row["name"], 0, $rname, 'deleteDB',true);
            }
            
            $tList .= '<tr><td class="DATA_list">OTHER</td><td class="DATA_list2">' . $tagList . '</td></tr>';
        
        $tList .= '</table>';
        
        return $tList;
    }
    //=====================================================================//
    function add($keyword, $rid, $rname)
    {
        //=====================================================================//      
        $tag = $this->render(0, $keyword, $rid, $rname, 'add',true);
        
        return $tag;
    }
    //=====================================================================//
    function addToQues($tid, $tag, $rid, $rname)
    {
        //=====================================================================//  
        //ITS_debug();
        //echo 'ITS_tags:addToQues: <br>'.$tid.' ' . $tag . ' - ' . $rid . '  ' . $rname;die();
        if ($tid == 0) { // new tag
            $tid = $this->addTag($tag);
        }
        
        $query = 'INSERT IGNORE INTO ' . $rname . '_' . $this->tb_tags . ' (' . $rname . '_id,' . $this->tb_tags . '_id) VALUES (' . $rid . ',' . $tid . ')';
        //echo time().'<p>ITS_tags:addToQues: '.$query.'</p>';die();
        
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        $tag = $this->render($tid, $tag, $rid, $rname, 'delete',true);
        
        return $tag;
    }
    //=====================================================================//
    function deleteFromQues($tid, $tname, $rid, $rname)
    {
        //=====================================================================// 
        $query = 'DELETE FROM ' . $rname . '_' . $this->tb_tags . ' WHERE ' . $rname . '_id=' . $rid . ' AND ' . $tname . '_id=' . $tid;
        //echo 'ITS_tags:addToQues: <br>'.$query;die();
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
    }
    //=====================================================================//
    function deleteFromDB($tid, $tname, $rname)
    {
        //=====================================================================//
        // 1. Delete from associated resource_source
        $query = 'DELETE FROM ' . $rname . '_' . $this->tb_tags . ' WHERE ' . $tname . '_id=' . $tid;
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        //2. Delete from tags 
        $query = 'DELETE FROM ' . $this->tb_tags . ' WHERE id=' . $tid;
        $res   = mysql_query($query);
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        
        return $query;
    }
    //  alter table tags change id int auto_increment;
    //=====================================================================//
    function addTag($tname)
    {
        //=====================================================================//      
        //echo 'addTag<br>';
        $query = 'INSERT INTO ' . $this->tb_tags . ' (name) VALUES ("' . $tname . '")';
        $res   = mysql_query($query);
        
        if (!$res) {
            die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
        }
        //if( !mysql_query($query) ){echo '<br>'.$query;} 
        $tid = mysql_insert_id();

        return $tid;
    }
    //=====================================================================//
    function render($tid, $tag, $rid, $rname, $type, $link_flag)
    {
        //=====================================================================// 

        switch ($type) {
            case 'add':
                $icon       = '+';
                $sel_class = '';
                $icon_class = 'tag_add';
                break;
            case 'delete':
                $icon       = 'x';
                $sel_class = '';
                $icon_class = 'tag_del';
                break;
            case 'deleteDB':
                $icon       = 'x';
                $sel_class = 'tagref';
                $icon_class = 'tag_del_DB';
                break;
        }
        if ($link_flag) { $t = '<a href="Tags.php?tid=' . $tid . '">' . $tag . '</a>'; }
        else 			{ $t = $tag; }
  
        $tag = '<div class="ITS_tags"><table><tr><td>' . $t . '</td><td class="' . $icon_class . '" tag="' . $tag . '" tid="' . $tid . '" tname="' . $this->tb_tags . '" rname="' . $rname . '" rid="' . $rid . '">' . $icon . '</td></tr></table></div>';
        
        return $tag;
    }
    //=====================================================================//
    function renderList($list_arr, $rid, $rtable, $type)
    {
        //=====================================================================// 
        $tag_list = '';
        $tag_ids  = implode(',', $list_arr);
        if (!empty($tag_ids)) {
            $query = 'SELECT id,name FROM ' . $this->tb_tags . ' WHERE id IN (' . $tag_ids . ') ORDER BY name';
            //die($query); 
            $res   = mysql_query($query);
            if (!$res) {
                die('Query execution problem in ' . get_class($this) . ': ' . msql_error());
            }
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                $tag_list .= $this->render($row['id'], $row['name'], $rid, $rtable, $type);
            }
        }
        
        return $tag_list;
    }
    //=====================================================================//
    function render2($arr, $rid, $rtable, $tname, $type)
    {
        //=====================================================================// 
        //echo 'render2<br>';
        $tag_list = '';
        if (!empty($arr)) {
            $tag_list = '';
            for ($i = 0; $i < count($arr) - 1; $i++) {
                $tag_list .= $this->render($arr[$i][0], $arr[$i][1], $rid, $tname, $type); //$rtable,
            }
            $tb = '<div class="ITS_TAGS_RES">' . $tag_list . '</div>';
        }
        
        return $tb;
    }
    //=====================================================================//
    function main()
    {
        //=====================================================================// 
        $query = 'SELECT id,chapter,section,paragraph,content,tag_id,name FROM dspfirst WHERE meta="' . $this->meta . '" AND chapter=' . $this->chapter;
        // die($query);
        $res   = $this->mdb2->query($query);
        if (PEAR::isError($res)) {
            throw new Question_Control_Exception($res->getMessage());
        }
        $pars = $res->fetchAll();
        
        $book = '<div class="ITS_BOOK"><p>';
        //for ($i = 2; $i <= 6; $i++) {  //count($pars)-1
        for ($i = 0; $i <= count($pars) - 1; $i++) {
            if (empty($pars[$i][5])) {
                $pars[$i][5] = '""';
            }
            
            $query = 'SELECT name FROM tags WHERE id IN (' . $pars[$i][5] . ')'; // echo '<p>'.$i.' '.$query.'<p>';
            $res   = $this->mdb2->query($query);
            if (PEAR::isError($res)) {
                throw new Question_Control_Exception($res->getMessage());
            }
            $name = $res->fetchAll();
            
            $fpath = '/FILES/SP1Figures/';
            //echo '<p>'.$this->meta.'</p>';
            
            switch ($this->meta) {
                //----------------------//
                case 'paragraph':
                    //----------------------//
                    $tags = '';
                    for ($t = 0; $t <= count($name) - 1; $t++) {
                        $tags .= '<span class="ITS_tag">' . $name[$t][0] . '</span>';
                    }
                    //$book = $book.'<div class="ITS_PARAGRAPH">'.$pars[$i][4].'</div><br>'; 
                    //echo '<font color=red>'.$pars[$i][4].'</font><hr>';
                    
                    $book .= $pars[$i][4] . '<div class="ITS_tags">' . $tags . '</div>';
                    break;
                //----------------------//
                case 'equation':
                    //----------------------//
                    $tags = ''; //array();
                    for ($t = 0; $t <= count($name) - 1; $t++) {
                        $tags .= '<span class="ITS_tag">' . $name[$t][0] . '</span>';
                    }
                    //if ($i==0) { $book .= '<hr class="ITS_hr">';}
                    //$book .= '<div class="sectionContainer"><table class="ITS_BOOK"><tr><td width="5%"><font color="blue">'.$pars[$i][0].'</font></td><td><img class="ITS_EQUATION" src="'.$this->mpath.$pars[$i][4].'"/></td></tr><tr><td colspan="2">'.$tags.'</td></tr></table></div>';
                    
                    $book .= '<img class="ITS_EQUATION" src="' . $this->mpath . $pars[$i][4] . '"/>';
                    break;
                //----------------------//
                case 'math':
                    //----------------------//
                    //$str ="REFERENCE#fig:dtsig#REFERENCE";      
                    //$str = preg_replace("/(a)(.*)(d)/","a($2)d",$str);  
                    // a(s)dfd a()dsfd a(aaa)da(s)d
                    //$book = preg_replace("/I want (\S+) one/", "$1 is the one I want", "I want that one") . "\n";
                    
                    $book .= '<div class="sectionContainer"><table class="ITS_BOOK"><tr><td width="5%"><font color="blue">' . $pars[$i][0] . '</font><td><img class="ITS_EQUATION" src="' . $this->mpath . $pars[$i][4] . '"/></td></tr></table></div>';
                    break;
                //----------------------//
                case 'image': // NO SCORE
                    //----------------------//            
                    $tags = ''; //array();
                    for ($t = 0; $t <= count($name) - 1; $t++) {
                        $tags .= '<span class="ITS_tag">' . $name[$t][0] . '</span>';
                    }
                    
                    //if ($i==0) { $book .= '<hr class="ITS_hr">';}
                    $ch    = $pars[$i][1];
                    $sec   = $pars[$i][2];
                    $fig   = explode('/', $pars[$i][6]);
                    $fN    = count($fig);
                    $fname = trim(str_replace('}', '', $fig[$fN - 1]));
                    $chs   = sprintf('%02d', $ch);
                    $imn   = sprintf('%02d', ($i + 1));
                    
                    //$img_source = $this->filepath.'SP1Figures/Ch'.$chs.'/Fig'.$chs.'-'.$imn.'_'.$fname.'.png';
                    //$img_source = $fpath.'Ch'.$chs.'/art/'.$fname.'.png';
                    $img_source = '../BOOK/BOOK_R/Chapter' . $chs . '/art/' . $fname . '.png';
                    
                    //echo $img_source.'<p>';
                    //die($img_source);
                    $caption = $pars[$i][4];
                    //$caption = preg_replace("/($)(.*)?($)/U",'<img class="ITS_EQUATION" src="'.$tex_path.'$2"/></a>"',$caption);
                    $caption = preg_replace("/(REFERENCE#)(.*)?(#REFERENCE)/U", "<a>$2</a>", $caption);
                    
                    $img_str = '<div class="ITS_Image"><img src="' . $img_source . '" alt="' . $img_source . '"><br><div class="ITS_Caption">' . $caption . '</div></div>';
                    $book .= '<div class="sectionContainer"><table class="ITS_BOOK"><tr><td width="5%"><font color="blue">' . $pars[$i][0] . '</font></td><td>' . $img_str . '</td></tr><tr><td colspan="2">' . $tags . '</td></tr></table></div>';
                    break;
                    //----------------------//
            }
        }
        $book = $book . '</div><p>';
        
        return $book;
    }
    //=====================================================================//
}
?>
