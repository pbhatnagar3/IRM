<?php
$count = $_POST["count"];

if(!($connection1 = @ mysql_connect(localhost, root, csip)))
						echo "connect failed.";
	if(!(mysql_select_db("its", $connection1)))
						echo "database selection failed";
					
	if(!($tempgame = @ mysql_query("Select q_ID from reinforcement_team_Fall_2012_1 where user_ID =1204", $connection1)))
						echo "connection failed";
					
	if(!($tempgame2 = @ mysql_query("Select q_ID from reinforcement_team_Fall_2012_1 where user_ID =-1", $connection1)))
						echo "connection failed";	
	
	echo "<br>";
	//echo $tempgame;
	echo "<br>";
	// while loop : since one has to get all the userIDs for a particular user.
	
		 $element_count = 0;
		while($temp1 = mysql_fetch_array($tempgame)){
			$final[$element_count] = $temp1[0];
			$element_count++;
		}
		
		while($temp2 = mysql_fetch_array($tempgame2)){
			$final[$element_count] = $temp2[0];
			$element_count++;
		}
		$display = count($final);
		//echo "$display";

		$loopcount =0;
		for($loopcount = 0; $loopcount < $display; $loopcount++){
				if(!($tempgame1 = @ mysql_query("Select question from questions where id =$final[$loopcount]", $connection1)))
			echo "connection failed";
		$jerry = mysql_fetch_array($tempgame1);
		if($count > 0){
			$count--;
			continue;
		}		
		echo  $jerry[0];
		echo "<br>";
		echo "<br>";				
		echo "<br>";
		break;
		
		}		
?>
