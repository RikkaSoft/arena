<?php
	global $conn;
	$id = $_SESSION['loggedInId'];
	$sql = "UPDATE users SET loginHash ='' WHERE id='$id'";
	mysqli_query($conn,$sql);
	unset($_SESSION);
	unset($_COOKIE['rememberme']);
	setcookie('rememberme', '', time() - 3600);
	session_destroy(); 
	include("login.php");
?>