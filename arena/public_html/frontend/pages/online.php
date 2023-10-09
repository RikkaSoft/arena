
<script>
	$(document).ready(function() 
		{ 
	    	$("#leaderTable").tablesorter({sortInitialOrder: "desc"}); 
	    } 
	); 
</script>
<div id='mainPagePadding'>
	<h2>Online Players</h2>
	<p>These players are online right now!</p>
	<table id="leaderTable">
		<thead> 
			<tr>
				<th>Name</th>
				<th>Level</th>
				<th>Arena Wins</th>
				<th>Arena Losses</th>
				<th>Win ratio</th>
				<th>Player Kills</th>
				<th>Times Hospitalized</th>
			</tr>
		</thead>
		<tbody>
			<?php include(__ROOT__."/backend/other/update-online.php")?>
		</tbody>							
	</table>
<h2>Leaderboard</h2>
	<p>These are the most powerful gladiators currently alive!</p>
	<table id="leaderTable">
		<thead> 
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Level</th>
				<th>Arena Wins</th>
				<th>Arena Losses</th>
				<th>Win ratio</th>
				<th>Player Kills</th>
				<th>Times Hospitalized</th>
			</tr>
		</thead>
	<tbody>
	<?php include(__ROOT__."/backend/other/update-leaderboard.php")?>
	</tbody>
	</table>
</div>