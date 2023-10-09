<?php 
	if(isset($_SESSION['characterProperties']['id'])){
	require(__ROOT__.'/backend/quests/questFunctions.php');
	
	if(checkQuestPending($_SESSION['characterProperties']['id'],0)){
		listPendingQuests($_SESSION['characterProperties']['id']);
	}
	else{
		listQuests();
	}
	
	#createRandomQuest();
	
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>