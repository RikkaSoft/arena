<?php 
	if(isset($_SESSION['characterProperties']['id'])){
	include(__ROOT__.'/backend/crafting/craftingFunctions.php');
?>
<div id='craftInfo'>Here you can craft your own weapons with crafting parts. Crafting parts can be recieved from adventures as well as arena matches. <br>There are three types of parts (Base, Main & Extra).<br>
	Each piece has a different amount of slots available. For example, the Basic hilt has 1 slot which you can place a Dull blade on which in turn has 2 slots for extra parts.
</div>

<div id='usableParts'><h3 class='craftHeaders' style='text-align:center;'>Weapons</h3>
	<?php listAllBases();?>
	
	<?php listAllArmourBases();?>
</div>
<div id='itemOutput'></div>

<?php
	}
	else{
		include(__ROOT__."/public_html/frontend/pages/notallowed.php");
	}
?>
