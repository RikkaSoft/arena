<?php
	include("/var/www/html/arena/system/details.php");
	
	$sql = "UPDATE characters SET isOnline=0 WHERE isOnlineTen=0 AND isOnline=1";
	mysqli_query($conn, $sql);
	
	$sql = "UPDATE characters SET isOnlineTen=0 WHERE isOnlineTen=1";
	mysqli_query($conn, $sql);

	$sql = "SELECT id FROM battlereports ORDER BY id DESC LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);
	
	$message = "There has been over " . $row['id'] . " battles in the training and arena!";
	$sql = "UPDATE configuration SET infoBarMessageAlt='$message'";
	mysqli_query($conn, $sql);
	session_destroy();
?>