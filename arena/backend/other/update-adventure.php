<?php
	global $conn;
	
	if (($_GET['hah']) !== "hp"){
		echo "sorry bro, no cheating";
	}
	else{
    	$sql = "SELECT * FROM characters WHERE adventureTurns < 6";
    	$result = mysqli_query($conn,$sql);
    	
    	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    	{
    		$sql = "UPDATE characters SET adventureTurns=adventureTurns+1 WHERE id='$row[id]'";
            mysqli_query($conn,$sql);
    	}
    }

	session_destroy();
?>