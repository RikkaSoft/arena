<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		if($_SESSION['characterProperties']['healedDate'] == 0){
		require_once(__ROOT__."/backend/fighting/trainingFunctions.php"); 
				
		#var_dump($_SESSION['characterProperties']['trainingCreature']);
		if(!isset($_GET['fight']) && !isset($_GET['finished'])){
			training();
		}
	}
	else{
		include("frontend/pages/no-character.php");
	}
	}
	else{
		include(__ROOT__."/public_html/frontend/pages/notallowed.php");
	}
?>