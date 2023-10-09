<?php if ($_GET['battleId'] != 0){ ?>
<script>
	$(document).ready(function(){
		var report = '<?php require_once(__ROOT__."/backend/other/get-battlereport-sequence.php");?>';
		if((Math.floor(Math.random() * 2) + 1) == 1){
		    var regex = new RegExp('winningTeam', 'g');
			var report = report.replace(regex,"teamRed");
			var regex = new RegExp('teamTwo', 'g');
			var report = report.replace(regex,"teamBlue");
			var regex = new RegExp('teamOne', 'g');
            var report = report.replace(regex,"teamBlue");
		}
		else{
		    var regex = new RegExp('winningTeam', 'g');
            var report = report.replace(regex,"teamBlue");
            var regex = new RegExp('teamTwo', 'g');
            var report = report.replace(regex,"teamRed");
            var regex = new RegExp('teamOne', 'g');
            var report = report.replace(regex,"teamRed");
		}
		var roundSplit = report.split("<h4>");
		var length = roundSplit.length;
		var roundSplitEnd = roundSplit[length-1].split("<br><br><br>");
		roundSplit[length-1] = roundSplitEnd[0] + "<br><br><br>";
		roundSplit[length] = "</h4>" + roundSplitEnd[1];
		var length = roundSplit.length;
		
		var targetDiv = $("#report");
		var currentInput = targetDiv.html();
		targetDiv.html(currentInput + roundSplit[0]);
		
		
		var i = 1;
		var interval = setInterval(function() { 
          	var currentInput = targetDiv.html();
			targetDiv.html(currentInput + "<h4>" + roundSplit[i]);
          i++; 
          $("#mainPage").scrollTop(function() { return this.scrollHeight; });
          if(i >= length) clearInterval(interval);
  		 }, 3000);
		

	});

</script>
<div class="mainContent">
	<div id="report">
		
	</div>
</div>
<?php 
	}
else{
	echo "<div class='mainContent'>
	<div id='report'>
		The match was a walkover...
	</div>
</div>";
}
?>