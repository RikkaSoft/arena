<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		require_once(__ROOT__."/backend/fighting/groupFunctions.php");
	
		if($_SESSION['characterProperties']['healedDate'] == 0){

			if(isset($_GET['getGroup'])){
				GetActiveGroup($_GET['getGroup']);	
			}
			else{
				ListAllOpenGroups();
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