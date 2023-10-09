<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		if($_SESSION['characterProperties']['healedDate'] == 0){
?>
<?php if($_SESSION['characterProperties']['level'] > 2){ ?>
    <br>
    <?php 
        if (isset($_SESSION['characterProperties']['specificAdventure']) || isset($_SESSION['characterProperties']['adventureRoll'])){
            echo "<p style='text-align:center;margin: 0px;font-weight: bold;'>You're already on an adventure, you need to finish this one before you do anything else<br>Your HP will not be regenerated until you are done with the adventure<br></p>";
        }
    ?>
	
    <div id='adventureTitle'>
        <h2 style='text-align:center;'>The Dark Forest</h2>      
    </div>
	<div id="adventureOutput">
		
	</div>
	<script>
        $(document).ready(function(){
            $('#adventureOutput').load('index.php?apage=checkCurrent&nonUI=true');
        });
    </script>
<?php } else { ?>
	    <br>
	    <div id="notYet">
            <h3>Adventures are not yet available for you<br><br>You need to be level three to go on adventures</h3>
        </div>
<?php } ?>
<?php 
}
else{
	include("frontend/pages/no-character.php");
}
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>
