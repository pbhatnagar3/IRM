<?php
/*=====================================================================//
ITS_query - query DB for resources.

Constructor: ITS_query(ch)

ex. $query = new ITS_query('tableA',2,2,array(1,2,3,4),array(20,30));

Author(s): Greg Krudysz       | Nov-07-2011
: Khyati Shrivastava | May-10-2012

Last Revision:  Dec-4-2012
//=====================================================================*/

class ITS_query
{
    public $id;
    public $term;
    public $chapter_number;
    
    //=====================================================================//
    function __construct()
    {
        //=====================================================================//
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class();
        }
        global $db_dsn, $db_name, $tb_name, $db_table_user_state, $tex_path;
        
        $this->record  = array();
        $this->db_dsn  = $db_dsn;
        $this->tb_name = $tb_name;
        
        // connect to database
        $mdb2 =& MDB2::connect($db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($this->mdb2->getMessage());
        }
        
        $this->mdb2 = $mdb2;
    }
    //=====================================================================//
    function getQuery($qet, $usertable, $ch, $epochtime)
    {
        //=====================================================================//
        
        if ($ch == 1) {
            $other = '|Complex$';
        } elseif ($ch == 13) {
            $other = '|PEZ$|chapter7DM$';
        } else {
            $other = '';
        }
        if ($ch == 8) {
            $match = array();
            for ($chr = 1; $chr <= 6; $chr++) {
                if ($chr == 1) {
                    $other = '|Complex$';
                } elseif ($chr == 13) {
                    $other = '|PEZ$|chapter7DM$';
                } else {
                    $other = '';
                }
                array_push($match, 'SPEN' . $chr . '$|PreLab0' . $chr . '$|Lab' . $chr . '$|Chapter' . $chr . '$|-Mod' . $chr . '$' . $other);
            }
            $category = implode("|", $match);          
        }else{
			$category = 'SPEN' . $ch . '$|PreLab0' . $ch . '$|Lab' . $ch . '$|Chapter' . $ch . '$|-Mod' . $ch . '$' . $other;
		}
        $query = 'SELECT ' . $qet . ' FROM ' . $usertable . ',' . $this->tb_name . ' WHERE ' . $usertable . '.question_id=' . $this->tb_name . '.id AND current_chapter=' . $ch . ' AND category REGEXP "(' . $category . ')" AND ' . $usertable . '.score IS NOT NULL AND epochtime > ' . $epochtime;
        //echo '<br>'.$query.'<br>';
        //die($query);
        
        return $query;
    }
    //=====================================================================//
    function getCategory($ch)
    {
        //=====================================================================//  
        //die($ch);  
         
        if ($ch == 1) {
            $other = '|Complex$';
        } elseif ($ch == 13) {
            $other = '|PEZ$|chapter7DM$';
        } else {
            $other = '';
        }
        if ($ch == 8) {
            $match = array();
            for ($chr = 1; $chr <= 6; $chr++) {
                if ($chr == 1) {
                    $other = '|Complex$';
                } elseif ($chr == 13) {
                    $other = '|PEZ$|chapter7DM$';
                } else {
                    $other = '';
                }
                array_push($match, 'SPEN' . $chr . '$|PreLab0' . $chr . '$|Lab' . $chr . '$|Chapter' . $chr . '$|-Mod' . $chr . '$' . $other);
            }
            $category = implode("|", $match);
            $query    = 'category REGEXP "(' . $category . ')" AND '.$this->tb_name.'.qtype IN ("MC","M","C")';
            //die($query);
        } else {
            $query = 'category REGEXP "(SPEN' . $ch . '$|PreLab0' . $ch . '$|Lab' . $ch . '$|Chapter' . $ch . '$|-Mod' . $ch . '$' . $other . ')" AND '.$this->tb_name.'.qtype IN ("MC","M","C")';
        }
        //echo 'UPDATE questions SET verified=1 WHERE '.$query.';<br>';
        //echo $query.'<br>';
        //die($query);
        
        return $query;
    }
    //=====================================================================//
} //eo:class
//=====================================================================//
?>
