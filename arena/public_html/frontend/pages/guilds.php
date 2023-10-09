<?php 
	if(isset($_SESSION['characterProperties']['id'])){
	
	require(__ROOT__.'/backend/guild/guildFunctions.php');
	
	if($_SESSION['characterProperties']['guild'] == 0){
		loadGuilds();
	}
	else{
		getGuild($_SESSION['characterProperties']['guild']);
		#showGuildInfo();
		#loadGuilds();
	}
	
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>