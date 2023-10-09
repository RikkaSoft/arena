<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;
	if($_SESSION['characterProperties']['healedDate'] == 0){
		$char_id =  $_SESSION['characterProperties']['id'];
		$charName = $_SESSION['characterProperties']['name'];
		$inv_id = $_SESSION['characterProperties']['inventory_id'];
		$equip_id = $_SESSION['characterProperties']['equipment_id'];
		$username = $_SESSION['loggedIn'];
	    
		$timesDied = $_SESSION['characterProperties']['timesDied'];
		
		$totalTime = round(($_SESSION['characterProperties']['level'] * 10) * ($_SESSION['characterProperties']['timesDied']+1)/2);
		$date = date('Y-m-d H:i');
		$healedDate = date('Y-m-d H:i', strtotime($date. ' + ' . $totalTime . ' minutes'));
		
		$sql = "UPDATE characters SET timesDied=timesDied+1, healedDate='$healedDate', adventureRoll=NULL,adventureArea=NULL,adventureChoice=NULL, specificAdventure=NULL,adventureMonster=NULL,adventureMonsterWin=NULL WHERE id='$char_id'";
		mysqli_query($conn,$sql);
		
		$_SESSION['charId'] = $char_id;
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
		
		header ('Location: index.php?nonUI&page=no-character');
	}
?>