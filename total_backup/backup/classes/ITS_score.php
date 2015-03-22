 <?php
/*=====================================================================//
ITS_score - compute user scores.

Constructor: ITS_score( ... )

ex. $scores = new ITS_score( ... );

NOTE: requires ITS_query() class

Author(s): Gregory A. Krudysz, Nabanita Ghosal
Last Update: Sep-20-2013
//=====================================================================*/

class ITS_score
{
    
    public function __construct($userid, $term, $date)
    {
        
        global $db_dsn, $tb_name;
        
        $this->userid       = $userid;
        $this->db_dsn       = $db_dsn;
        $this->tb_name      = $tb_name;
        $this->term         = $term;
        $this->epochtime    = $date;
        
        $this->ptsMax   = 2400;
        $this->ptsGrade = 30;
        
        // connect to database
        $mdb2 =& MDB2::connect($db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($this->mdb2->getMessage());
        }
        $this->mdb2 = $mdb2;
    }
    //=====================================================================//
    public function computeLabScores()
    {
        //=====================================================================//    
        $usertable    = "stats_" . $this->userid;
        $useranswer   = 0;
        $score        = 0;
        $labname      = "lab";
        $quesArray    = array();
        $questiontype = "";
        $tscore       = array();
        $s            = new ITS_statistics($this->userid, $this->term, 'student');
        
        //for every lab
        for ($i = 0; $i < 13; $i++) {
            //for every lab, set score to 0
            $score   = 0;
            $labname = 'lab' . sprintf("%02d", $i + 1);
            
            //echo ' LABNAME = '.$labname;
            $query1 = 'SELECT question_id, qtype FROM activity, ' . $this->tb_name . ' WHERE active=0 AND term="' . $this->term . '" AND activity.question_id=' . $this->tb_name . '.id AND name = "' . $labname . '"';
            $res1 =& $mdb2->query($query1);
            $mdb2->disconnect();
            
            while ($row1 = $res1->fetchAll()) {
                for ($k = 0; $k < count($row1); $k++) {
                    //for every question in the lab
                    $questionid   = $row1[$k][0];
                    $questiontype = $row1[$k][1];
                    $query2       = "SELECT answered FROM " . $usertable . " WHERE question_id = " . $questionid;
                    $res2 =& $mdb2->query($query2);
                    $mdb2->disconnect();
                    while ($row2 = $res2->fetchAll()) {
                        // print_r($row2);
                        for ($l = 0; $l < count($row2); $l++) {
                            $useranswer = $row2[$l][0];
                            $scr        = ($s->get_question_score($questionid, $useranswer, $questiontype));
                            //echo "<p>scr :" . $scr.'<p>';
                            $score      = $score + $s->get_total_score($scr, $useranswer, $questiontype);
                            //echo "<p>score :" .'<p>'.$score;
                        }
                    }
                }
            }
            $tscore[] = $score;
        }
        // print_r($tscore);  
        return $tscore;
    }
    //=====================================================================//
    public function renderLabScores()
    {
        //=====================================================================//    
        $lab_score_arr = $this->computeLabScores();
        $N             = count($lab_score_arr);
        $score_arr     = array_merge($lab_score_arr, array(
            '<b>' . array_sum($lab_score_arr) . '</b>'
        ));
        
        //$lab_arr = array("Lab 1","Lab 2","Lab 3","Lab 4","Lab 5","Lab 6","Lab 7","Lab 8","Lab 9","Lab 10","Lab 11","Lab 12", "Lab 13");  
        $lab_arr = array();
        
        for ($n = 0; $n < $N; $n++) {
            $lab_arr[$n] = '<span class="ITS_score">Lab ' . ($n + 1) . '</span>';
        }
        $lab_arr[$N] = '<span class="ITS_score_Total">TOTAL</span>';
        
        $weight   = array_fill(0, ($N + 1), 100 / ($N + 1));
        $str      = array_merge($lab_arr, $score_arr);
        $tb_score = new ITS_table('a', 2, ($N + 1), $str, $weight, 'ITS_mySCORE');
        
        return $tb_score->str;
    }
    //=====================================================================//
    public function computeConceptScores($tag_id)
    {
        //=====================================================================//    
        // connect to database

        $mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($mdb2->getMessage());
        }
                    
        $usertable = "stats_" . $this->userid;	 				  
				  
		$query = 'SELECT t.id,t.name,count(s.question_id) AS attempted,count(q.id) AS available, ROUND(AVG(s.score),1) AS percent 
					FROM tags AS t 
						LEFT JOIN questions_tags AS qt ON t.id = qt.tags_id      AND t.synonym=0 
						LEFT JOIN questions      AS q  ON q.id = qt.questions_id AND q.qtype IN ("M","MC","C") AND q.status="publish"
						LEFT JOIN '.$usertable.' AS s  ON s.tags = qt.tags_id    AND s.question_id = qt.questions_id AND event = "concept" 
					WHERE qt.tags_id='.$tag_id;
				  	  							 
		// echo '<p>'.$query.'</p>';				
        $res =& $mdb2->query($query);
        
        while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
					$info['attempt']   = $row['attempted'];
					$info['totalques'] = $row['available'];          
					$info['percent']   = $row['percent'];
		}
		$res->free();
     
        // echo time();
        // echo '<pre>';print_r($info);echo '</pre>';
        
        return $info;
    }
    //=====================================================================//
    public function renderConceptScores($info)
    {
        //=====================================================================//   
        
        $pct = (($info['attempt'])==0) ? '' :  ' <span class="gray"> | </span> ' . $info['percent'] . '<span class="gray"> %</span>';        
      
        $str = $info['attempt'] . '<span class="gray"> / </span> ' . $info['totalques'] . $pct;  
        
        return $str;
    }    
    //=====================================================================//
    public function computeChapterScores($chArr)
    {
        //=====================================================================//    
        // connect to database
        $mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($mdb2->getMessage());
        }
        
        $time_pre = microtime(true);      
               
        $usertable = "stats_" . $this->userid;
        $ITSq      = new ITS_query();
        
        $n = count($chArr);
        //for every chapter
        for ($i = 0; $i < $n; $i++) {
            //for every chapter, set score to 0
            $score = 0;
            //for every chapter, compute score
            $c      = $chArr[$i];
            $query = $ITSq->getQuery('TRUNCATE(SUM(score),2) AS score,count(question_id) AS attempt', $usertable, $c, $this->epochtime);

            //echo 'ITS_score::computeChapterScores():<br><font color="blue">'.$query.'</font><p>';
            
            $res =& $mdb2->query($query);
            $row = $res->fetchAll();
            
            $attemptedQuesNum        = $row[0][1];
            $tscore[$i]['score'] 	 = $row[0][0];
            $tscore[$i]['attempt']   = $attemptedQuesNum;
            $tscore[$i]['totalques'] = $this->getTotalNumQuestions($i + 1);
            
            if ($attemptedQuesNum == 0) {
                $percentage = 0;
            } else {
                $total      = 100 * $attemptedQuesNum;
                $percentage = ($tscore[$i]['score'] / $total) * 100;
            }
            $tscore[$i]['percent'] = $percentage;
        }

        /* timer
        $time_post = microtime(true);
		$exec_time = $time_post - $time_pre;echo $exec_time; */
        
        $mdb2->disconnect();
        
        return $tscore;
    }
    //=====================================================================//
    public function renderChapterScores($chArr)
    {
        //=====================================================================//    
        //echo 'chapter: '.$this->chapter.'<p>';  
        
        $chapter_score_arr = $this->computeChapterScores($chArr);
        $N                 = count($chapter_score_arr);
        $chapter_arr       = array();
        $chapter_arr[]     = '';
        foreach ($chArr as $c) {
            $chapter_arr[] = '<span class="ITS_score">A&ndash;' . $c . '</span>';
        }
        $chapter_arr[]   = '<span class="ITS_score_Total">TOTAL</span>';
        $score_arr[]     = "<b>Score</b>";
        $attemptedQues[] = '<span class="ITS_smallFont">Attempted / Available</span><br><b>Questions</b>';
        $percentageArr[] = "<b>Percentage</b>";
        $note            = 'Grade = ' . $this->ptsGrade . '*( min( Score , ' . $this->ptsMax . ' ) / ' . $this->ptsMax . ' )';
        $gradeArr[]      = '<span class="grade" style="cursor:help" title="' . $note . '">Grade</span>';
        
        $total_score       = 0;
        $total_attemptQues = 0;
        $total_ques        = 0;
        $totalPercent      = 0;
        $totalGrade        = 0;
        $idx               = 1;
        foreach ($chapter_score_arr as $s) {
            $score_arr[]       = round($s['score'], 2). '<span class="gray"> pts</span>';
            $total_score       = $total_score + $s['score'];
            //Attempted Questions
            $attemptedQues[]   = '<span id="qAvail' . $idx . '">' . $s['attempt'] . '</span><span class="gray"> / </span>' . $s['totalques'];
            $total_attemptQues = $total_attemptQues + $s['attempt'];
            $total_ques        = $total_ques + $s['totalques'];
            //Percentages
            $percentageArr[]   = round($s['percent'], 2) . '<span class="gray"> %</span>';
            //Grade
            $grade             = round(10 * $this->ptsGrade * min($s['score'], $this->ptsMax) / $this->ptsMax) / 10;
            $gradeArr[]        = '<span class="gray">' . $grade . '</span> / <span class="gray">' . $this->ptsGrade . '</span><br><span class="grade">' . (round(1000 * ($grade / $this->ptsGrade)) / 10) . '</span><span class="gray"> %</span>';
            $totalGrade        = $totalGrade + $grade;
            $idx++;
        }
        $score_arr[]     = round($total_score, 2) . '<span class="gray"> pts</span>';
        $attemptedQues[] = $total_attemptQues . '<span class="gray"> / </span>' . $total_ques;
        if ($total_attemptQues == 0) {
            $totalPercent = 0;
        } else {
            $totalPercent = $total_score / $total_attemptQues;
        }
        
        $percentageArr[] = round($totalPercent, 2) . '<span class="gray"> %</span>';
        $gradeArr[]      = '<span class="gray">' . $totalGrade . '</span> / <span class="gray">' . (($idx - 1) * $this->ptsGrade) . '</span><br><span class="grade">' . (round(1000 * ($totalGrade / ((($idx - 1) * $this->ptsGrade)))) / 10) . '</span><span class="gray"> %</span>';
        $weight          = array_fill(0, ($N + 3), 100 / ($N + 3));
        $str             = array_merge($chapter_arr, $score_arr, $percentageArr, $attemptedQues, $gradeArr);
        $tb_score        = new ITS_table('a', 5, ($N + 2), $str, $weight, 'ITS_mySCORE');
        $score_table     = '<center>' . $tb_score->str . '</center>';
        
        //var_dump();
        return $score_table;
    }
    //=====================================================================//
    public function getTotalNumQuestions($ch)
    {
        //=====================================================================//
        // connect to database
        $mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($mdb2->getMessage());
        }
        
        $ITSq     = new ITS_query();
        $category = $ITSq->getCategory($ch);
        $query    = 'SELECT count(id) FROM ' . $this->tb_name . ' WHERE ' . $category;
        //echo $query1.'<p>';
        $res =& $mdb2->query($query);
        $row          = $res->fetchAll();
        $totalNumQues = $row[0][0];
        $mdb2->disconnect();
        
        return $totalNumQues;
    }
    //=====================================================================//
    public function getTopScoresByChapter()
    {
        //=====================================================================//
        $top1[] = 'Top Score 1';
        $top2[] = 'Top Score 2';
        $top3[] = 'Top Score 3';
        
        // connect to database
        $mdb2 =& MDB2::connect($this->db_dsn);
        if (PEAR::isError($mdb2)) {
            throw new Exception($mdb2->getMessage());
        }
        
        $query1 = " select distinct ch1,ch2,ch3,ch4,ch5,ch6 from user_scores order by ch1 desc, ch2 desc, ch3 desc, ch4 desc, ch5 desc, ch6 desc limit 3";
        $res1 =& $mdb2->query($query1);
        $mdb2->disconnect();
        
        while ($row1 = $res1->fetchAll()) {
            for ($j = 0; $j < 6; $j++) {
                $top1[] = $row1[0][$j];
                $top2[] = $row1[1][$j];
                $top3[] = $row1[2][$j];
                //$top4[] = $row1[3][$j];
                //$top5[] = $row1[4][$j];
            }
        }
        $chapterArr      = array(
            '',
            'Ch1',
            'Ch2',
            'Ch3',
            'Ch4',
            'Ch5',
            'Ch6'
        );
        $N               = 7;
        $weight          = array_fill(0, ($N), 110 / ($N));
        $str             = array_merge($chapterArr, $top1, $top2, $top3);
        $tb_score        = new ITS_table('a', 4, $N, $str, $weight, 'ITS_mySCORE');
        $top_score_table = '<center>' . $tb_score->str . '</center>';
        
        return $top_score_table;
    }
    //=====================================================================//
    function getGrades($tsquare_file, $A)
    {
        //=====================================================================//     
        $epochtime = mktime(0, 0, 0, 8, 20, 2013);
        $ITSq      = new ITS_query();
        
        $file_path = 'admin/csv/';
        $file_name = 'ITS-A'.$A.'.csv';     
        $fp        = fopen($file_path.$file_name, 'w');
        
        $handle = fopen($users, "r");
        $row    = 1;
        if (($handle = fopen($tsquare_file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($data[0] != 'Student ID') {       
                    $query = 'SELECT id,first_name,last_name FROM users WHERE username="' . $data[0] . '"';
                    // echo $query;die();
                    
                    $res =& $this->mdb2->query($query);
                    if (PEAR::isError($res)) {
                        throw new Question_Control_Exception($res->getMessage());
                    }
                    $user = $res->fetchAll();

                    $q1    = $ITSq->getQuery('ROUND(SUM(score),2) AS sum', 'stats_' . $user[0][0], $A, $epochtime);
                    // echo $q1;die();
                    $r1    = mysql_query($q1);
                    $score = mysql_result($r1, 0, "sum");
                    $grade = round(100*$this->ptsGrade * min($score, $this->ptsMax) / $this->ptsMax)/100;        
                    $grades = array($data[0],$data[1],$grade);
                } else {
					$grades = array($data[0],$data[1],'ITS-'.$A);          
				}
				fputcsv($fp, $grades);
            }
            fclose($handle);
        }   
        fclose($fp);
        
        $link = '<a href="'.$file_path.$file_name.'">'.$file_name.'</a>';
		
        return $link;
    }
    //==============================================================================    
} //eo:class
//=====================================================================//
?>
