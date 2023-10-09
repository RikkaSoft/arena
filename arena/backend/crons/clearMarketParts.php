<?php
	global $conn;
	include("/var/www/html/arena/system/details.php");
	$sql = "DELETE FROM craftingpartssale";
	mysqli_query($conn,$sql);
?>