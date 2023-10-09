<?php require_once(__ROOT__."/backend/tournament/tournament-actions.php");?>
<div id="tournamentArea">
	<?php 
	if (isset($_GET['id'])){
		require_once(__ROOT__."/backend/tournament/create-brackets.php");
		if(isset($_GET['finals'])){
			loadTournament($_GET['id'],0,$_GET['season'],true);
		}
		else{
			loadTournament($_GET['id'],0,0);
		}
	}
	else{
		echo "<h3>Ongoing Tournaments</h3>";
		getOngoing();
		echo "<h3>Upcoming Tournaments</h3>";
		getFuture();
		echo "<h3>Previous Tournaments</h3>";
		getPast(); 
		echo "<h3>Season Finales</h3>";
		getFinales();
		echo 
			"<script>$(document).ready(function() 
				{ 
			    	$('table').tablesorter(); 
			    } 
			);</script>";
	}
	echo "<div id='itemInfo'></div>";
	include_once("frontend/design/js/npcinfo.html");
	?>
	
</div>