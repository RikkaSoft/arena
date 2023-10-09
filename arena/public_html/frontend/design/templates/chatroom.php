<?php if(@$_GET['page'] != 'chatroom'){ ?>
	<div id="chatroomWrapper">
		<div id=chatroomFullscreen style="cursor:pointer;">Maximize chat</div>
	<div id="chatroomBody">
		
	</div>
	<div id="chatRoomPost">
		<?php
		if(isset($_SESSION['loggedIn'])){
			echo '<textarea type="text" id="chatroomInput" placeholder="Say something (English only)"></textarea>';
		}
		else{
			echo '<textarea type="text" id="chatroomInput" placeholder="You need to login to chat" disabled></textarea>';
		}
		?>
		<button id="postButton">Send</button>
		</div>
	</div>
<?php }?>
<script>
	$( document ).ready(function(){
		if($('#chatroomBody').is(':visible')){
			setInterval(function () {
		        loadChatroom();
		    },10000);
			$('#chatroomFullscreen').click(function(){
				$('#mainPage').html('<div id="chatroomWrapper" style="height:100%;margin-top:0px;"><div id="chatroomBodyDup" style="overflow:auto;"></div></div>');
				loadChatroom();
			});

			$('#chatroomInput').keypress(function(e){
			      if(e.keyCode==13)
			      $('#postButton').click();
			    });
			   loadChatroom(); 
			
			function loadChatroom(){
				var textarea = document.getElementById('chatroomBody');
				var oldHeight = textarea.scrollHeight;
				if(document.getElementById("chatroomBodyDup") !== null){
					var textarea2 = document.getElementById('chatroomBodyDup');
					var oldHeight2 = textarea2.scrollHeight;
				}
				
				$('#chatroomBody').load('index.php?opage=chatroom&getMessage=alrightBro&nonUI',
				function(){
					var textarea = document.getElementById('chatroomBody');
					if(oldHeight != textarea.scrollHeight){
						textarea.scrollTop = textarea.scrollHeight;
					}
					if(textarea2 !== null){
						$('#chatroomBodyDup').html($('#chatroomBody').html());
						var textarea2 = document.getElementById('chatroomBodyDup');
						if(oldHeight2 != textarea2.scrollHeight){
							textarea2.scrollTop = textarea2.scrollHeight;
						}
					}
				});

			};
			$('#postButton').click(function(){
				var message = $('#chatroomInput').val();
				$('#chatroomBody').load('index.php?opage=chatroom&nonUI', {
					postMessage: message
				},function(){
					$('#chatroomInput').val('');
					loadChatroom();
				})
			});
		}
	});
</script>
