<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;

	$username = $_SESSION['loggedIn'];
	
	$sql = "SELECT * FROM battlereports WHERE username =? ORDER BY id DESC LIMIT 1";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	echo $row['report'];



?>