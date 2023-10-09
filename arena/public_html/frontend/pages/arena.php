<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		if($_SESSION['characterProperties']['healedDate'] == 0){
?>
<?php require_once(__ROOT__."/backend/character/update-characterSessions.php"); ?>
<?php require_once(__ROOT__."/backend/fighting/arena-status.php");?>

<?php 
	if ($_SESSION['characterProperties']['battleReady'] == 1 && isset($_SESSION['characterProperties']['battleType'])) {
		echo "<div style='width:100%;text-align:center;'>";
		echo "<img src='frontend/design/images/dice.svg' id='dice' style='margin-top:40px;display:none;'>";
		echo "</div>";
		echo "<script>
			setTimeout(
			    function() {
			      $('#dice').css('display','initial');
			    }, 300);
		</script>";
		if ($_SESSION['characterProperties']['battleType'] == 1){
			echo "<script>
				$('#mainPage').load('index.php?fpage=arena-status&match1v1&nonUI');
			</script>";
			#match1v1();
		}
		else {
			echo "<script>
				var type = '" . $_SESSION['characterProperties']['battleType'] . "';
				$('#mainPage').load('index.php?fpage=arena-status&matchGroup='+type+'&nonUI');
			</script>";
			matchGroup();
		}
	}
	elseif($_SESSION['characterProperties']['battleReportReady'] == 1){
		showLastReport();
	}
	else{
         
     if($_SESSION['characterProperties']['level'] > 1){ ?>
	<div id="arenaInfo">
		
		<h2>Arena</h2>
		
		<p>The arena is where you fight other gladiators</p>
		<p>To fight another character you need to choose at which HP you wish to surrender. 
			<br>Once you press fight you try to find another gladiator, if you are unable to fight 
			directly you will be placed in a queue and the fight will start whenever other gladiators are ready</p>
		<p>
			The match will not start if your HP is lower than 100%, so you can queue up whenever you want without having to worry about starting the match and dying in one hit.
		</p>
		<br><br>
		<div id="arenaStats">
			
		</div>
	</div>
	<div id="arenaChoices"> 
		<br>
		<a href="#" id="1v1">
			<div id="1v1b" class="arenaButton button">
				1v1
			</div>
		</a>
		<!--
		<a href="#" id="2v2">
			<div id="2v2b" class="button arenaButton">
				2v2
			</div>
		</a>
		<a href="#" id="3v3">
			<div id="3v3b" class="button arenaButton">
				3v3
			</div>
		</a>
		-->
		<div id="arenaForms">
        
        </div>
	</div>
	
	<script>
		$('#1v1').click(function(){
			$('#arenaForms').load('index.php?fpage=arena-types&type=1v1&nonUI=true');
			$('#arenaStats').load('index.php?fpage=arena-types&statType=1v1&nonUI=true');
		});
		$('#2v2').click(function(){
			$('#arenaForms').load('index.php?fpage=arena-types&type=2v2&nonUI=true');
			$('#arenaStats').load('index.php?fpage=arena-types&statType=2v2&nonUI=true');
		});
		$('#3v3').click(function(){
			$('#arenaForms').load('index.php?fpage=arena-types&type=3v3&nonUI=true');
			$('#arenaStats').load('index.php?fpage=arena-types&statType=2v2&nonUI=true');
		});
		$(document).ready(function(){
			$('#arenaForms').load('index.php?fpage=arena-types&type=1v1&nonUI=true');
			$('#arenaStats').load('index.php?fpage=arena-types&statType=1v1&nonUI=true');
		});
	</script>
<?php
		}else{ ?>
		    <br>
		    <div id="notYet">
		       <h3 style="text-align:center;">The arena is not available for you yet<br><br>You need to be level two to fight in the arena<br><br>Level up by fighting beasts in training</h3>
		    </div>
		<?php }}
?>
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