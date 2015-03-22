<?php
function traverse($count){
if(!($connection1 = @ mysql_connect(localhost, root, csip)))
						echo "connect failed.";
	if(!(mysql_select_db("its", $connection1)))
						echo "database selection failed";
					
	if(!($tempgame = @ mysql_query("Select questionID from testTable where UserID =1758", $connection1)))
						echo "connection failed";
	echo "<br>";
	echo $tempgame;
	echo "<br>";
	// while loop : since one has to get all the userIDs for a particular user.
		
		while($game = mysql_fetch_array($tempgame)){
		echo "userID : $game[0]"  ;
		echo ' <br>';
		if(!($tempgame1 = @ mysql_query("Select question from questions where id =$game[0]", $connection1)))
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
		//echo "printing the output of the array";
		//print_r ($game);
		echo "<br>" ; 
							
		$content_str = $game[1];
		return ($count++);	

}


?>
