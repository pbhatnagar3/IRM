<?php
$LAST_UPDATE = 'Aug-31-2013';
//=====================================================================//               
//Author(s): Gregory Krudysz
//=====================================================================//
//--- begin timer ---//
$mtime       = explode(" ", microtime());
$starttime   = $mtime[1] + $mtime[0];
$Debug       = FALSE;
//-------------------//
require_once("config.php"); // #1 include
require_once(INCLUDE_DIR . "include.php");

include("classes/ITS_timer.php");

global $db_dsn, $db_name, $tb_name, $db_table_user_state, $tex_path, $term, $tset;

$dsn = preg_split("/[\/:@()]+/", $db_dsn);
//foreach ($dsn as $value) {echo $value.'<br>';}

$user          = $dsn[1];
$pass          = $dsn[2];
$host          = $dsn[4];
$name          = $dsn[6];
$dbNametb_user = $db_table_user_state;

//$timer = new ITS_timer();
session_start();

// return to login page if not logged in
abort_if_unauthenticated();

$id     = $_SESSION['user']->id();
$status = $_SESSION['user']->status();
$info   = $_SESSION['user']->info();
//----------------------------------------------------//
//mNK = (N*mean(A)+K*mean(B))/(N+K)

$con = mysql_connect($host, $user, $pass) or die('Could not Connect!');

// Check to make sure that we successfully connected
if ($con) {
    $returnStr = __FILE__;
    mysql_select_db($name, $con) or die('Could not select DB');
    
    // SELECT questions
    $query = 'SELECT id,lower(qtype) AS type FROM questions WHERE lower(qtype) IN ("mc","m","c")';
    $res   = mysql_query($query, $con);
    if (!$res) {
        die('Query execution problem in : ' . msql_error());
    }
    for ($q = 0; $q < mysql_num_rows($res); $q++) {
        $row      = mysql_fetch_assoc($res);
        $ques[$q] = $row['id'];
        $type[$q] = $row['type'];
    }
    $NQ = mysql_num_rows($res);
       
    // SELECT $users by semester
    $query = 'SELECT id FROM users WHERE status IN ("Fall_2013","Spring_2013","Fall_2012","Fall_2011","Spring_2012","Summer_2012")';
    
    $res = mysql_query($query, $con);
    if (!$res) {
        die('Query execution problem in : ' . msql_error());
    }
    for ($x = 0; $x < mysql_num_rows($res); $x++) {
        $row       = mysql_fetch_assoc($res);
        $users[$x] = $row['id'];
    }
    
    $Nusers = mysql_num_rows($res);
    
    $qidx = 1;
    for ($qi = 1; $qi <= $NQ; $qi++) {
        $qid   = $ques[$qi];
        $qtype = $type[$qi];
        
        $score    = array();
        $rating   = array();
        $duration = array();
        //event = [];
        for ($u = 1; $u <= $Nusers; $u++) {
            //SELECT scores | ratings | durations | event FROM answers that have both a score & duration
            
            $query = 'SELECT score,rating,duration FROM stats_' . $users[$u] . ' WHERE duration IS NOT NULL AND question_id=' . $qid;
            $res   = mysql_query($query, $con);
            if (!$res) {
                die('Query execution problem in : ' . msql_error());
            }
            //echo mysql_num_rows($res);
            echo $users[$u] . '  ' . mysql_num_rows($res) . '<br>';
            // SCORE
            if (mysql_num_rows($res) > 0) {
                for ($x = 0; $x < mysql_num_rows($res); $x++) {
                    $row = mysql_fetch_assoc($res);
                    array_push($score, $row['score']);
                    array_push($rating, $row['rating']);
                    array_push($duration, $row['duration']);
                }
            }
        }
        die('-ee-');
    }
    //disp([qi) ' ' length(score))]);
    //      if (!empty($score)){
    //-- SCORE
    /*
    $score_idx = ~isnan(cell2mat(score));
    $score_post = cell2mat(score(score_idx));
    $score_ave = mean(score_post);
    $score_N = length(score_post);
    $score_num_zero = sum(score_post==0);
    $score_num_perf = sum(score_post==100);
    $score_prc_zero = 100*score_num_zero/score_N;
    $score_prc_perf = 100*score_num_perf/score_N;
    
    //-- DURATION
    $duration_post = cell2mat(duration(score_idx));
    $duration_ave = mean(duration_post);
    
    //-- RATING
    $rating_post = cell2mat(rating(score_idx));
    $rating_arr = rating_post(~isnan(rating_post));
    $rating_N = count($rating_arr);
    if ($rating_N==0){
    $rating_ave = 'NULL';
    }else{
    $rating_ave = mean($rating_arr);
    }           
    */
    //-- SKIPS
    /*            $skips_post = !$score_idx;
    $skips_N = sum($skips_post);
    
    // index | qid | qtype | av score | pct zero | pct perf | N zero | N
    // perf | av dur | N ans | av rat | N rat | N skips
    $query = 'INSERT INTO MinedData VALUES ('.$qidx.','.$qid.',"'.$qtype.'",'.$score_ave.','.$score_prc_zero.','.$score_prc_perf.','.$score_num_zero.','.$score_num_perf.','.$duration_ave.','.$score_N.','.$rating_ave.','.$rating_N.','.$skips_N;
    echo $query.'<br>';
    $res = mysql_query($query, $con);
    if (!$res) {
    return $returnStr . $query;
    } else{
    $returnStr = "ok! $moduleName Saved!!";
    $qidx = $qidx+1;
    }
    mysql_free_result($res);
    mysql_close($con);
    */
} else {
    echo 'Connection failed.';
}

//--- TIMER -------------------------------------------------//
$mtime     = explode(" ", microtime());
$endtime   = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
//--- FOOTER ------------------------------------------------//
$ftr       = new ITS_footer($status, $LAST_UPDATE, $totaltime);
echo $ftr->main();
//-----------------------------------------------------------//
?>
