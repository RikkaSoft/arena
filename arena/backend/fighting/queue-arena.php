<?php
	global $conn;
	$battleType = $_POST['battleType'];
	$surrenderValue = $_POST['yourSurrender'];
	if (isset($_POST['fightLevel'])){
		$fightLevelChoice = $_POST['fightLevel'];
	}
	else{
		$fightLevelChoice = 0;
	}
	$name = $_SESSION['characterProperties']['name'];
	
    $searchTime = date("Hi");
    
	$sql = "UPDATE characters SET battleReady=1,battleType=?,battleSurrender=?,fightLevelChoice=?,searchTime='$searchTime' WHERE name=?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "idis", $battleType,$surrenderValue,$fightLevelChoice,$name);
	mysqli_stmt_execute($stmt);
	
	#Update session
	require_once(__ROOT__."/backend/character/update-characterSessions.php");
	
	#Redirect
	header("Location: index.php?page=arena");

?>