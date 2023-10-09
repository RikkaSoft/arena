<?php
	global $conn;
	
	$char = $_SESSION['characterProperties']['name'];
	
	$sql = "UPDATE characters SET battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0 WHERE name='$char'";
	$result = mysqli_query($conn,$sql) or die("Error: ".mysqli_error($conn));
	
	$_SESSION['charId'] = $_SESSION['characterProperties']['id'];
	require_once(__ROOT__."/backend/character/update-characterSessions.php");
	
	header('Location: index.php?page=arena');
?>