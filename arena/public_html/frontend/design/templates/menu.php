<div id="largeMenu">
	<div id='menuContent' style='height:100%;'>
		<a id='arena' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/arenaIcon.png'/>
				ARENA
			</div>
		</a>
		<a id='groupFight' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/arenaIcon.png'> SKIRMISH
			</div>
		</a>
		<a id='training' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/trainingIcon.png'> TRAINING
			</div>
		</a>
		<a id='adventure' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/adventureIcon.png'> ADVENTURE
			</div>
		</a>
		<a id='market' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/marketIconv2.png'> MARKET
			</div>
		</a>
		<a id='crafting' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/craftingIcon.png'> CRAFTING
			</div>
		</a>
		<a id='enchantress' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/enchantIcon.png'> ENCHANTRESS
			</div>
		</a>
		<a id='tournament' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/tournamentIcon.png'> TOURNAMENTS
			</div>
		</a>
		<a id='guilds' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/guildIcon.png'> GUILDS
			</div>
		</a>
		<a id='quests' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/questsIcon.png'> QUESTS
			</div>
		</a>
		<a id='tavern' class='menuButtonClickable'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/tavernIcon.png'>TAVERN
			</div>
		</a>
		<a id='your-character' class='menuButtonClickable hidden-sm hidden-md hidden-lg hidden-xl'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/characterIcon.png'> MY CHARACTER
			</div>
		</a>
		<a id='online' class='menuButtonClickable hidden-sm hidden-md hidden-lg hidden-xl'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/tavernIcon.png'>Online Players
			</div>
		</a>
		<a id='chatroom' href='index.php?page=chatroom' class='menuButtonClickable hidden-sm hidden-md hidden-lg hidden-xl'>
			<div class='menuItem defaultButton '>
				<span class='helper'></span>
				<img src='frontend/design/images/menu/tavernIcon.png'>Chat
			</div>
		</a>
	</div>
</div>

<script>
	$(document).ready(function(){
		$(".menuButtonClickable").click(function(){
			loadMainPage($(this).attr('id'));
			$('.menuItem').removeClass("activeButton");
			$(this).children().addClass("activeButton");
			window.onload = updateChar();
		});
	});
</script>