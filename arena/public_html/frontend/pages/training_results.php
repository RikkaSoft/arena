<?php
if (!isset($_SESSION['unique']) && !isset($_POST['rematch'])){
	#header('Location: index.php?page=training');
}
?>
<script>
$(document).ready(function(){
	window.onload = updateChar();
})
</script>
<div class="mainContent">
	<?php require_once(__ROOT__."/backend/fighting/fight-creature.php"); ?>
</div>
