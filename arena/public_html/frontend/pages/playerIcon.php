<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		
	require(__ROOT__ . "/backend/other/playerIconFunctions.php");	
		
	if(isset($_POST['icon'])){
		setPlayerIcon($_POST['icon']);
	}
?>

<div id="playerIconDiv">
	
	
	<?php listAllIcons(); ?>

</div>
<?php
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>

		