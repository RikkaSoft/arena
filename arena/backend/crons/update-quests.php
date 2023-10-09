<?php
include("/var/www/html/arena/system/details.php");
	
	$sql = "UPDATE characters SET questDaily1Available=1,questDaily2Available=1,questDaily3Available=1";
	mysqli_query($conn, $sql);

?>