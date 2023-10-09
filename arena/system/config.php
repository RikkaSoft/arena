<?php
    global $conn;
	define('__ROOT__', dirname(dirname(__FILE__)));
    if (!isset($conn)){
        require_once(__ROOT__."/system/details.php"); 
    }
    
	$query = "SELECT name, maintenance, logo, infoBarMessage, infoBarMessageAlt, infoBarPrio,finals,season,lastHPUpdate FROM configuration";
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	
	$maintenance = 	$row['maintenance'];
	$infoBarMessage	=	$row['infoBarMessage'];
	$infoBarMessageAlt	=	$row['infoBarMessageAlt'];
	$infoBarPrio =			$row['infoBarPrio'];
	$finals = $row['finals'];
	$season = $row['season'];

?>