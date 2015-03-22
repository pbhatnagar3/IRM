<?php
/*=====================================================================//
ITS_query - query DB for resources.

		Constructor: ITS_query(ch)
		
								 ex. $query = new ITS_query('tableA',2,2,array(1,2,3,4),array(20,30));
								
	 Author(s): Greg Krudysz       | Nov-07-2011
			  : Khyati Shrivastava | May-10-2012
	 Last Revision:  May 10 2012
//=====================================================================*/

class ITS_query2 {

    public $id;
    public $term;
    public $chapter_number;

    //=====================================================================//
    function __construct() {
        //=====================================================================//
        die('in query2');
        $this->debug = FALSE; //TRUE;

        if ($this->debug) {
            echo '<br>'.get_called_class();
        }
        global $db_dsn,$db_name,$tb_name,$db_table_user_state,$mimetex_path;

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
    function getQuery($qet,$usertable,$ch,$epochtime) {
        //=====================================================================//
			if ($ch == 1)     { $other = '|Complex$';          } 
			elseif ($ch == 13){ $other = '|PEZ$|chapter7DM$';  }
		    else 			  { $other = '';                   }
		    
			$query = 'SELECT '.$qet.' FROM '.$usertable.','.$this->tb_name.' WHERE '.$usertable.'.question_id='.$this->tb_name.'.id AND current_chapter='.$ch.' AND category REGEXP "(SPEN'.$ch.'$|PreLab0'.$ch.'$|Lab'.$ch.'$|Chapter'.$ch.'$|-Mod'.$ch.'$'.$other.')" AND '.$usertable.'.score IS NOT NULL AND epochtime > '.$epochtime;
        
        return $query;
    }
    //=====================================================================//
    function getCategory($ch) {
        //=====================================================================//  
        //die($ch);   
			if ($ch == 1)     { $other = '|Complex$';          } 
			elseif ($ch == 13){ $other = '|PEZ$|chapter7DM$';  }
		    else 			  { $other = '';                   }
		    
			$query = 'category REGEXP "(SPEN'.$ch.'$|PreLab0'.$ch.'$|Lab'.$ch.'$|Chapter'.$ch.'$|-Mod'.$ch.'$'.$other.')" AND qtype IN ("MC","M","C")';
        
        return $query;
    }
    //=====================================================================//
    function getConceptQuestion($tbvalues) {
    //=====================================================================//  
        $arr_val = split(',',$tbvalues);
		$str_vals = "'".$arr_val[0]."'";
		for($i=1;$i<sizeof($arr_val);$i++){
			$str_vals .= ",'".$arr_val[$i]."'";
		}
		//$query = "SELECT id FROM ".$this->tb_name." w WHERE w.tag_id in (SELECT tag_id FROM SPFindex i WHERE i.name in (".$str_vals."))";
		$query = "SELECT id FROM ".$this->tb_name." w where w.id IN (select questions_id from questions_tags q where q.tags_id in (SELECT tags_id FROM SPFindex i where i.name IN (".$str_vals.")))";
		//".$this->tb_name."
        return $query;
    }
    //=====================================================================//
    
} //eo:class
//=====================================================================//
?>
