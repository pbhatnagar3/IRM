<?php
/*=====================================================================//
ITS_rating - rate questions.

Constructor: ITS_rating( ... )

ex. $rating = new ITS_rating( ... );

Author(s): Nabanita Ghosal
Mods: Greg Krudysz
Last Update: Jun-3-2012
//=====================================================================*/
class ITS_rating
{
    public function __construct()
    {
        global $db_dsn, $tb_name;
        
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
    public function renderRating($rating)
    {
        //=====================================================================//
        if (empty($rating)) {
            $rating = 0;
        }
        //echo $rating;
        $star_rating = '<form id="ITS_rating" action="" method="post">' . '<div id="stars-cap"></div>' . '<div id="ITS_rate" class="center" rating="" value="">';
        
        $title = array(
            'Very easy',
            'Easy',
            'Moderate',
            'Difficult',
            'Very difficult'
        );
        
        for ($n = 1; $n <= count($title); $n++) {
            if ($n == $rating) {
                $chk = 'checked="checked"';
            } else {
                $chk = '';
            }
            $star_rating .= '<input type="radio" ' . $chk . ' name="rate" value="' . $n . '" title="' . $title[$n - 1] . '" id="rate' . $n . '" /> <label for="rate' . $n . '">' . $title[$n - 1] . '</label><br />';
        }
        
        $star_rating .= '<input type="submit" value="Rate it!" />' . '</div>' . '</form><p id="ajax_response"></p>';
        return $star_rating;
    }
    //=====================================================================//
    public function renderDifficulty($qid)
    {
	//select difficulty from database
	$query = 'SELECT difficulty FROM questions_difficulty WHERE q_id = ' . $qid;
	//connect to database
	$mdb2 =& MDB2::connect($this->db_dsn);
	if (PEAR::isError($mdb2)) {
	    throw new Exception($this->mdb2->getMessage());
	}
	$res =& $mdb2->query($query);
	if (PEAR::isError($res)) {
	    throw new Question_Control_Exception($res->getMessage());
	}
	while ($row =& $res->fetchRow()) {
	    // Assuming DB's default fetchmode is
	    // DB_FETCHMODE_ORDERED
	    //echo $row[0] . "\n"
	    $difficulty_rate = $row[0] * 10;
	}
	if($difficulty_rate){
	    $difficulty_title = 'Not Rated';
	    if($difficulty_rate>80){ //78
		$difficulty_title = 'Very difficult';
	    }else if($difficulty_rate>60){ //66
                $difficulty_title = 'Difficult';
            }else if($difficulty_rate>40){ //54
		$difficulty_title = 'Moderate';
	    }else if($difficulty_rate>20){ //42
                $difficulty_title = 'Easy';
            }else{
                $difficulty_title = 'Very easy';
            }
	}else{
	    $difficulty_rate = 0;
	    $difficulty_title = 'Not Rated';
	}
	//$answers = $res->fetchAll();
	//$difficulty_rate = $answers[$qid][0] * 10;
	//$difficulty_rate = 50;
	$difficulty = 'Difficulty: ' . $difficulty_title .'<br />' . '<div class="difficulty-bar orange">' . '<span style="width: ' . $difficulty_rate . '%"></span>' . '</div>';
	return $difficulty;
    }

} //eo:class
//=====================================================================//
?>
