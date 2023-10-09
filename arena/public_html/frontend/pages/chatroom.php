<div id="chatroomWrapper" style='width:100%;height:100%;margin-top:0px;'>
<div id="chatroomBody" style='width:100%;height:100%;'>
	
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