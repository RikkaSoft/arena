<?php
	global $dbserver,$dbusername,$dbpassword,$db;
	//DATABASE CONNECTION
	$dbserver =	"localhost";
	$dbusername = 	"root";
	$dbpassword =	"pass";
	$db = 		"arena";
	//ESTABLISH CONNECTION
	$conn = new mysqli($dbserver,$dbusername,$dbpassword,$db);
	
	//CHECK CONNECTION 
	if ($conn->connect_error){
		die("Connection failed: ".$conn->connect_error);
	}
?>