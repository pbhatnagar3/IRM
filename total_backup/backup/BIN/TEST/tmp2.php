<?php
/*=====================================================================*/

// ALTER TABLE MinedData MODIFY COLUMN id INT primary key NOT NULL AUTO_INCREMENT;
//die('now');

        for($i=3470;$i<3581;$i++) {
        echo 'INSERT IGNORE INTO MinedData (question_id) VALUES('. $i .');<br>';
        echo 'INSERT IGNORE INTO questions_difficulty (q_id) VALUES('. $i .');<br>';
        }

//=====================================================================//
?>
