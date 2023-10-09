
<div class="layer">
	<div class="footer">
		
		<p style='padding:0px;padding-left:10px;padding-top:3px;margin:0px;float:left; color:white;'>2020 Rikka.se <a href='index.php?page=donate' style='text-decoration: underline;text-decoration-color:yellow'>Donate?</a> <a href='https://store.steampowered.com/app/1183840/Dungeon_of_Rikka/' style='text-decoration: underline;text-decoration-color:red;margin-left:10px;'>Check out my 2D roguelike on Steam!</a></p>
		<div id="time" style='float:right; text-align:center; margin-right:20px;padding-top:3px; color:white' ><?php echo date('H:i:s');?></div>
		
		<script>
			var timer = setInterval(initTime, 1000);
			
			function initTime() {
				var elem = $('#time');
			    var time = elem.text().split(':');
			    if(time[2] == 59){
			    	time[2] = 0;
			    	time[1]++;
			    	time[1] = checkTime(time[1]);
			    	if(time[1] == 60){
			    		time[1] = 0;
			    		time[0]++;
			    		time[0] = checkTime(time[0]);
			    		time[1] = checkTime(time[1]);
			    		if(time[0] == 24){
			    			time[0] = 0;
			    			time[1] = 0;
			    			time[2] = 0;
			    		}
			    	}
			    }
			    else{
			    	time[2]++;
			    }
			    time[2] = checkTime(time[2]);
			    
			    elem.text(time[0] + ":" + time[1] + ":" + time[2]);
			}
			
			function checkTime(i) {
			    if (i < 10) {
			        i = "0" + i
			    }; // add zero in front of numbers < 10
			    return i;
			}
		</script>
	</div>
</div>