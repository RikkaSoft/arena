<?php if(isset($_SESSION['loggedIn']))
{
	if (!isset($_SESSION['other']['chatToggle'])){
		$_SESSION['other']['chatToggle'] = 1;
	}
	if($_SESSION['other']['chatToggle'] == 0){
	?>
<div id="chatroomWrapper" style="display:none">
<div id="chatroomBody">
	
</div>
<div id="chatRoomPost">
	<textarea type="text" id="chatroomInput" placeholder="Say something (English only)"></textarea>
	<button id="postButton" onclick="postChatroom()" >></button>
	</div>
</div>
<?php
	}
	else{
?>
<div id="chatroomWrapper" style="display:block">
<div id="chatroomBody">
	
</div>
<div id="chatRoomPost">
	<textarea type="text" id="chatroomInput" placeholder="Say something (English only)"></textarea>
	<button id="postButton" onclick="postChatroom()" >Send</button>
	</div>
</div>
<?php
	}
}
?>
<script>
    
	setInterval(function () {
        loadChatroom();
    },10000);

	    $('#chatroomInput').keypress(function(e){
	      if(e.keyCode==13)
	      $('#postButton').click();
	    });
	   loadChatroom(); 
	
	function loadChatroom(){
		$('#chatroomBody').load('index.php?opage=chatroom&getMessage=alrightBro&nonUI',
		function(){
			var textarea = document.getElementById('chatroomBody');
			textarea.scrollTop = textarea.scrollHeight;
		})
	};
	function postChatroom(){
		var message = $('#chatroomInput').val();
		$('#chatroomBody').load('index.php?opage=chatroom&nonUI', {
			postMessage: message
		},function(){
			$('#chatroomInput').val('');
			loadChatroom();
		})
	};
</script>

<div class="layer">
	<div class="footer">
		
		<p style='padding:0px;padding-left:10px;padding-top:3px;margin:0px;float:left; color:white;'>Copyright &#169; 2017 Rikka.se</p>
		
		<?php if (isset($_SESSION['loggedIn'])){ ?>
		<button style="float:right" id="chatButton" onclick="toggleChat()">Show/Hide Chat</button>
		<div id="time" style='float:right; text-align:center; margin-right:20px;padding-top:3px; color:white' ><?php echo date('H:i:s');?></div>
		<?php };?>
		
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

			function toggleChat(){
			    if (document.getElementById('chatroomWrapper').style.display == 'none'){
    			    $("#chatroomWrapper").slideToggle("slow",function(){
                        $.post('index.php?opage=chatroom&nonUI',
                            {
                                chat: 1
                            }
                        );
                    });
                }
                else{
                    $("#chatroomWrapper").slideToggle("slow",function(){
                        $.post('index.php?opage=chatroom&nonUI',
                            {
                                chat: 0
                            }
                        );
                        loadChatroom();
                    });
                }
			}

		</script>
	</div>
</div>