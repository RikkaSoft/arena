<?php

	
	function loadGuilds(){
		global $conn;
		$sql = "SELECT * FROM guilds";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			echo "<div class='guildBox' id='" . $row['id'] . "'>";
				echo "<div style='width:80%;margin:auto;'>";
				echo "<h4 style='text-align:center;' id='guildName' class='guildName'>" . $row['name'] . "</h4>";
				echo "<img src='frontend/design/images/guilds/" . $row['image'] . "' style='width:60%;margin-left:20%;margin-right:20%;'></img>";
				echo "</div>";
				echo "<div class='guildInfo' id='info" . $row['id'] . "'>";
					
					echo "<div style='float:left;width:50%;'>";
					echo "<p style='font-size:16px;font-weight:600'>Description</p>";
						echo $row['description'];
						echo "<br><br>Entrance fee: " . $row['entranceFee']. "g";
						if($_SESSION['characterProperties']['guild'] == 0){
							echo "<br><button class='joinButton' id='" . $row['id'] . "'>Join Guild</button>";
						}
						else{
							if($_SESSION['characterProperties']['guild'] == $row['id']){
								echo "<br><button class='joinButton' id='" . $row['id'] . "' disabled>Already a member</button>";
							}	
							else{
								echo "<br><button class='joinButton'  id='" . $row['id'] . "' disabled>Already member of a guild</button>";
							}
								
							
						}
					echo "</div>";
					echo "<div style='float:left;width:50%;'>";
						echo "<p style='font-size:16px;font-weight:600'>Effects</p>";
						if(isset($row['effects'])){
							$effects = getGuildEffects($row['effects'],1);
							foreach($effects as $effect => $value){
								if ($value > 0){
									echo $effect . ": " . $value . "<br>";
								}
							}
						}
						else{
							echo "No active effects";
						}
						if(isset($row['players'])){
							$members = getGuildMembers($row['id']);
							echo "<h4>Members</h4>";
							if(!empty($members)){
								foreach($members as $member){
									echo "<a href='index.php?page=view-character&charName=" . $member . "&season=0'>" . $member . "</a><br>";
								}
							}
						}
					echo "</div>";
					
				echo "</div>";
				echo "<div style='position:absolute;bottom:0px;left:0px;'>";
					if ($row['players'] != ""){
						$players = count(array_filter(explode(",",$row['players'])));
					}
					else{
						$players = 0;
					}
					echo "Players: " . $players;
					echo "<br>";
					echo "Guild Level: " . $row['level'];
				echo "</div>";
				
				echo "<div id='closeGuildCard'>";
					echo "X";
				echo "</div>";
			echo "</div>";
			
		}
		#NEW GUILD
			echo "<div class='guildBox' id='newGuild'>";
				echo "<div style='width:80%;margin:auto;'>";
				echo "<h4 style='text-align:center;' id='guildName' class='guildName'>New Guild</h4>";
				
				#echo "<h4 style='text-align:center;'>Guild Images are not yet available</h4>";
				echo "</div>";
				echo "<div class='guildInfo' id='infonewGuild'>";
				echo "<h3 style='margin:3px;'>Name</h3>";
				
					echo "<form action='' method='post' onsubmit='createNewGuild();return false;' >";
					echo "<input type='text' id='newGuildName' class='guildName' maxlength='24' minlength='5' required>";
					echo "<br><br>";
					echo "<div id='leftGuildBox' style='float:left;width:50%;height:70%;'>";
					echo "<p style='font-size:16px;font-weight:600'>Description</p>";
						echo "<textarea id='inputDescription' name='inputDescription' placeholder='(Optional) Guild Description...'></textarea>";
						echo "<br><br>Creation fee: 250g";
						echo "<br><button class='createButton' id=''>Create Guild</button>";
					echo "</div>";
					echo "<div style='float:left;width:50%;text-align:left;'>";
					echo "<p style='font-size:16px;font-weight:600'>Effects (Choose one)</p>";
							$effects = getAllGuildEffects();
							foreach($effects as $effect){
									echo "<input type='radio' name='guildEffect' value='" . $effect['id'] . "' required> " . $effect['effect'] . " " . $effect['value'] . "<br>";
							}
							echo "<br>You will recieve one extra effect per guild level, you can choose the same effect but only three times";
					echo "</div>";		
					echo "</form>";	
				echo "</div>";
				echo "<div id='closeGuildCard'>";
					echo "X";
				echo "</div>";
			echo "</div>";
			
			
		echo "<div>";
		echo "<script>
				
				function createNewGuild(){
					var gName = $('#activeClone > .guildInfo > form > #newGuildName').val();
					var gDescription = $('#activeClone > .guildInfo > form > #leftGuildBox > #inputDescription').val();
					var gEffect = $('input[name=guildEffect]:checked').val();
					var r = confirm('Are you sure you want to create the guild: ' + gName + ' for 250gold?');
					if (r == true) {
					    $('#mainPage').load('index.php?gpage=guildFunctions&nonUI',{
					    	'newGuildName': gName,
					    	'newGuildDescription': gDescription,
					    	'newGuildEffect': gEffect
					    });
					} else {
					   	
					}
				}
				
				$('.guildBox').click(guildBoxes);
				
				function guildBoxes(){
					$('.guildBox').unbind('click');
					var id = $(this).attr('id');
					if(id !== 'closeGuildCard'){
						var pos = $(this).position();
						var height = $(this).height();
						var width = $(this).width();
						$(this).clone(true,true).prop('id', 'activeClone').css({
							'index': '5',
							'position':'absolute',
							'left': pos.left,
							'top': pos.top,
							'height': height,
							'width': width
							}).appendTo('#mainPage');
						
						$('#activeClone').siblings().fadeOut(300,function(){
							
						});
						var topPos = $('#activeClone').parent().position();
						if (window.matchMedia('(max-width: 768px)').matches) {
							var newWidth = '98%';
							var newLeft = '0';
						}
						else{
							var newWidth = '50%';
							var newLeft = $('#activeClone').parent().width() / 2 - $('#activeClone').width() / 1.2;
						}
							
							$('#activeClone').delay(300).animate({
								left: newLeft,
								top: topPos.top,
								width: newWidth,
								height: $('#activeClone').parent().height() -25
							},function(){
								$('#activeClone').children('.guildInfo').fadeIn();
								$('#activeClone').children('#closeGuildCard').fadeIn();
								$('#activeClone').children('#closeGuildCard').bind('click',guildBoxes);
							})
							$('#activeClone').children('#guildName').animate({
								fontSize:'24px'
							});
					}
					else{
						
						$('#activeClone').fadeOut(300,function(){
							$('#activeClone').remove();
							$('.guildBox').bind('click',guildBoxes);
						});
						$('.guildBox').delay(300).fadeIn(300,function(){
							
						});
						
				}}
				
				$('.joinButton').click(function(){
					var id = $(this).attr('id');
					var r = confirm('Are you sure you want to join this guild?');
						if (r == true) {
						$('#mainPage').load('index.php?gpage=guildFunctions&nonUI',{
							'joinGuild':id
						});
					}
				});
			</script>";
		echo "</div>";
	}
	
	function getGuildMembers($id){
		global $conn;
		$playerArray = array();
		$sql = "SELECT name FROM characters WHERE guild IN($id)";
		#echo $sql;
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			array_push($playerArray,$row['name']);
		}
		
		return $playerArray;
	}
	
	
	function getGuild($id){
		global $conn;
		
		echo "<div id='guildArea'>";
			$sql = "SELECT * FROM guilds WHERE id='$id'";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			$xpReq = 1000;
			$xpLevel = 2.4;
			$i = 1;
			while($i < $row['level']){
				$xpReq = $xpReq * $xpLevel;
				$i++;
			}
			$xpReq = round($xpReq);
			echo "<p style='font-size:26px;text-align:center;'>" . $row['name'] . " - Level " . $row['level'] . " - " . $row['experience'] . "/" . $xpReq . "Xp</p>";
			$meterWidth = ($row['experience'] / $xpReq)*100;
			if ($meterWidth > 100){
				$meterWidth = 100;
			}
			echo "<div class=\"meter\">
	                  <span style=\"width:" . $meterWidth . "%\"></span>
	                </div>
	                ";
		if($_SESSION['characterProperties']['id'] == $row['creator'])	{
			if ($row['experience'] >= $xpReq){
				echo "<a href='index.php?gpage=guildFunctions&levelUpGuild'><h2 style='text-align:center'>Level up guild!</h2></a>";
			}
		}		
					
					
		echo "<button id='listAllGuildsButton' style='position:absolute;top:0px;right:0px;z-index:99;'>List all Guilds</button>";			
		echo "<button id='leaveGuildButton' style='position:absolute;bottom:0px;right:0px;z-index:99;color:red;'>Leave Guild</button>";
		echo "<div style='float:left;width:50%;padding:10px;'>";
			echo "<h4>Description</h4>";
			echo $row['description'];
		echo "</div>";
		echo "<div style='float:right;width:50%;padding:10px;'>";
		echo "<h4>Effects</h4>";
			$effects = getGuildEffects($row['effects'],1);
			foreach($effects as $effect => $value){
				if ($value > 0){
					echo $effect . ": " . $value . "<br>";
				}
			}
			$members = getGuildMembers($id);
			echo "<h4>Members " . $row['playerCount'] . "/" . $row['maxPlayers'] . "</h4>";
			if(!empty($members)){
				foreach($members as $member){
					echo "<a href='index.php?page=view-character&charName=" . $member . "&season=0'>" . $member . "</a><br>";
				}
			}
		echo "</div>";
		echo "</div>";
		echo "<script>
			$('#listAllGuildsButton').click(function(){
				$('#guildArea').fadeOut(500,function(){
					$('#mainPage').hide().load('index.php?gpage=guildFunctions&nonUI&showAllGuilds').fadeIn(500);
				});
			});		
			
			$('#leaveGuildButton').click(function(){
				var r = confirm('Are you sure you want to leave your guild? You will have to pay to join again');
				if (r == true) {
					$('#guildArea').fadeOut(500,function(){
						$('#mainPage').hide().load('index.php?gpage=guildFunctions&nonUI&leaveGuild').fadeIn(500);
					});
				};
			});
		</script>";
	}

	function levelUpGui(){
		global $conn;
		
		echo "<div style='margin: auto;width:60%;text-align:center;background:white;border:1px solid black;margin-top:20px;'>";
		echo "<h1 style='text-align:center'>Level up your guild</h1>";
		$charId = $_SESSION['characterProperties']['id'];
		$sql = "SELECT * FROM guilds WHERE creator='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$xpReq = 1000;
		$xpLevel = 2.4;
		$i = 1;
		while($i < $row['level']){
			$xpReq = $xpReq * $xpLevel;
			$i++;
		}
		$xpReq = round($xpReq);
		
		if($row['experience'] >= $xpReq){
			$currEffects = explode(",",$row['effects']);
			
			$effects = getAllGuildEffects();
			echo "<form action='index.php?gpage=guildFunctions' method='post'>";
			foreach($effects as $effect){
				if(count( preg_grep( "/" . $effect['id'] . "/", $currEffects )) < 3){
					echo "<input type='radio' name='guildEffect' value='" . $effect['id'] . "' required> " . $effect['effect'] . " " . $effect['value'] . "<br>";
				}
			}
			echo "<input type='submit'>";
			echo "</form>";
			
		}
		else{
			echo "Not enough XP";
		}
		echo "</div>";
	}
	
	function levelUpGuild($effect){
		global $conn;
		
		$charId = $_SESSION['characterProperties']['id'];
		$guildId = $_SESSION['characterProperties']['guild'];
		$sql = "SELECT * FROM guilds WHERE creator='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$xpReq = 1000;
		$xpLevel = 2.4;
		$i = 1;
		while($i < $row['level']){
			$xpReq = $xpReq * $xpLevel;
			$i++;
		}
		$xpReq = round($xpReq);
		
		if($row['experience'] >= $xpReq){
			if (is_numeric($effect)){
				$currEffects = explode(",",$row['effects']);
				if(count( preg_grep( "/" . $effect . "/", $currEffects )) < 3){
					$effect = $row['effects'] . "," . $effect;
					$sql = "UPDATE guilds SET effects='$effect', level=level+1, entranceFee=entranceFee+400,maxPlayers=maxPlayers+1 WHERE creator='$charId'";
					mysqli_query($conn,$sql);
					getGuild($guildId);
				}
				else{
					echo "You have too many of that effect already";	
				}	
			}
			else{
				echo "effect is not an integer";
			}
		}
		else{
			echo "Not enough XP";
		}
	}
	
	function getGuildEffects($effects,$forShow){
		global $conn;
		
		$extraGoldPoint = 0;
		$extraGoldPercent = 0;
		$extraXpPoint = 0;
		$extraXpPercent = 0;
		$extraAdventurePoint = 0;
		$vendorDiscountPercent = 0;
		
		
		
		$splitEffects = explode(",",$effects);
		$sql = "SELECT * FROM guildeffects WHERE id IN ($effects)";
		
		$result =  mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			$multiplier = 0;
			foreach($splitEffects as $line){
				if ($line == $row['id']){
					$multiplier++;
				}
			}
			if($row['extraGoldPoint'] > 0){
				$extraGoldPoint += $row['extraGoldPoint'] * $multiplier;
			}
			if($row['extraGoldPercent'] > 0){
				$extraGoldPercent += $row['extraGoldPercent'] * $multiplier;
			}
			if($row['extraXpPoint'] > 0){
				$extraXpPoint += $row['extraXpPoint'] * $multiplier;
			}
			if($row['extraXpPercent'] > 0){
				$extraXpPercent += $row['extraXpPercent'] * $multiplier;
			}
			if($row['extraAdventurePoint'] > 0){
				$extraAdventurePoint += $row['extraAdventurePoint'] * $multiplier;
			}
			if($row['vendorDiscountPercent'] > 0){
				$vendorDiscountPercent += $row['vendorDiscountPercent'] * $multiplier;
			}
		}
		
		if($forShow == 1){
			$array = array("Gold per match (+)"=>$extraGoldPoint,"Gold per match (%)"=>$extraGoldPercent,"Xp per match (+)"=>$extraXpPoint,"Xp per match (%)"=>$extraXpPercent,"Adventure rolls (%)"=>$extraAdventurePoint,"Market Discount (%)"=>$vendorDiscountPercent);
			return $array;
		}
		else{
			$array = array("goldPoint"=>$extraGoldPoint,"goldPercent"=>$extraGoldPercent,"xpPoint"=>$extraXpPoint,"xpPercent"=>$extraXpPercent,"adventureRolls"=>$extraAdventurePoint,"discount"=>$vendorDiscountPercent);
			return $array;
		}
	}

	function getGuildPerks($guildId){
		if($guildId != 0){
			global $conn;
			$sql = "SELECT effects FROM guilds WHERE id='$guildId'";
			$result = mysqli_query($conn,$sql);
			$gRow = mysqli_fetch_assoc($result);
			$gEffects = getGuildEffects($gRow['effects'],0);
			
			$goldMultiplier = 1;
			$xpMultiplier = 1;
			$goldPlus = 0;
			$xpPlus = 0;
			$discount = 0;
			
			if ($gEffects['goldPercent'] > 0){
				$goldMultiplier = $goldMultiplier + $gEffects['goldPercent'] / 100;
			}
			if ($gEffects['xpPercent'] > 0){
				$xpMultiplier = $xpMultiplier + $gEffects['xpPercent'] / 100;
			}
			if ($gEffects['goldPoint'] > 0){
				$goldPlus = $goldPlus + $gEffects['goldPoint'];
			}
			if ($gEffects['xpPoint'] > 0){
				$xpPlus = $xpPlus + $gEffects['xpPoint'];
			}
			if ($gEffects['discount'] > 0){
				$discount = $discount + $gEffects['discount'] / 100;
			}
			
			$returnArray = array("goldMultiplier" => $goldMultiplier,"xpMultiplier" => $xpMultiplier,"goldPlus" => $goldPlus,"xpPlus" => $xpPlus, "discount" => $discount);
		}
		else{
			$returnArray = array("goldMultiplier" => 1,"xpMultiplier" => 1,"goldPlus" => 0,"xpPlus" => 0);
		}
		#var_dump($returnArray);
		return $returnArray;
	}
	
	function joinGuild($id){
		global $conn;
		$charId = $_SESSION['characterProperties']['id'];
		$sql = "SELECT guild,gold FROM characters WHERE id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$yourGold = $row['gold'];
		if ($row['guild'] != 0) {
			echo "Already belonging to a guild, leave that before joining another one";
		}
		else{
			$sql = "SELECT * FROM guilds WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $id);
			if(mysqli_stmt_execute($stmt)){
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
				if($row['constant'] == 1 || $row['playerCount'] < $row['maxPlayers']){
					$guildFee = $row['entranceFee'];
					if ($yourGold > $guildFee){
						$sql = "UPDATE guilds SET players = CONCAT(players,',',?),playerCount=playerCount+1 WHERE id=?";
						$stmt = mysqli_prepare($conn,$sql);
						mysqli_stmt_bind_param($stmt, "ii", $charId,$id);
						if(mysqli_stmt_execute($stmt)){
							$sql = "UPDATE characters SET guild=?,guildJoinTime=NOW(),gold=gold-? WHERE id=?";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "iii", $id,$guildFee,$charId);
							if(mysqli_stmt_execute($stmt)){
								getGuild($id);
								require_once(__ROOT__."/backend/character/update-characterSessions.php");
								//TO REFRESH CHARACTERINFO
						        echo"<script>
						            window.onload = updateChar();
						        </script>";
							}
							else{
								echo "DB Error, try again";
							}
						}
						else{
							echo "DB Error, try again";
						}
					}
					else{
						echo "You cannot afford to join this guild, you have " . $yourGold ."g it costs " . $guildFee . "g";
					}
				}
				else{
					echo "The guild is full";
				}
			}
			else{
				echo "DB Error, try again";
			}
		}
	}

	function createGuild($name,$description,$effect){
		global $conn;
		
		if(preg_match("/^[a-zA-Z0-9 ]+$/", $name) == 0){
			echo "guildName contains illegal characters, allowed characters are a-z 0-9 and space";
		}
		else{
			$guildLength =  strlen($name);
			if ($guildLength < 5){
				echo "GuildName too short";
			}
			elseif($name > 24){
				echo "GuildName too long";
			}
			else{
				$charId = $_SESSION['characterProperties']['id'];
				$sql = "SELECT * FROM characters WHERE id='$charId'";
				$result = mysqli_query($conn,$sql);
				
				$row = mysqli_fetch_assoc($result);
				
				if ($row['gold'] < 250){
					echo "Not enough gold, you need 250, you have " . $row['gold'];
				}
				else{
					$maxPlayers = 3;
					$entranceFee = 200;
					$image = "userGuild.png";
					$sql = "INSERT INTO guilds (name,description,effects,maxPlayers,players,creator,image,entranceFee,dateCreated) VALUES(?,?,?,?,?,?,?,?,NOW())";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "ssiiiisi", $name,$description,$effect,$maxPlayers,$charId,$charId,$image,$entranceFee);
					if(mysqli_stmt_execute($stmt)){
						$guildId = mysqli_insert_id($conn);
						
						$sql = "UPDATE characters SET gold=gold-250, guild='$guildId' WHERE id='$charId'";
						if(mysqli_query($conn,$sql)){
							getGuild($guildId);
						}
						else{
							$sql = "DELETE FROM guilds WHERE id ='$guildId'";
							echo "DB error, try again";
						}
					}
					else{
						echo "DB error, try again";
					}
				}
				
			}
		}
	}
	function leaveGuild(){
		global $conn;
		$charId = $_SESSION['characterProperties']['id'];
		$guildId = $_SESSION['characterProperties']['guild'];
		
		$sql = "UPDATE characters SET guild = 0 WHERE id='$charId'";
		mysqli_query($conn,$sql);
		
		$sql = "SELECT * FROM guilds WHERE id='$guildId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$gMembers = explode(",",$row['players']);
		for($i=0;$i < count($gMembers);$i++){
			if ($gMembers[$i] == $charId){
				unset($gMembers[$i]);
			}
		}
		$gMembers = implode(",",$gMembers);
		$sql = "UPDATE guilds SET players='$gMembers',playerCount=playerCount-1 WHERE id='$guildId'";
		mysqli_query($conn,$sql);
		
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
        loadGuilds();
	}
	function getAllGuildEffects(){
		global $conn;
		$returnArray = array();
		$sql = "SELECT * FROM guildeffects";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			
			if($row['extraGoldPoint'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Gold per match (+)", "value" => $row['extraGoldPoint']));
			}
			elseif($row['extraGoldPercent'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Gold per match (%)", "value" => $row['extraGoldPercent']));
			}
			elseif($row['extraXpPoint'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Xp per match (+)", "value" => $row['extraXpPoint']));
			}
			elseif($row['extraXpPercent'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Xp per match (%)", "value" => $row['extraXpPercent']));
			}
			elseif($row['extraAdventurePoint'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Adventure rolls (%)", "value" => $row['extraAdventurePoint']));
			}
			elseif($row['vendorDiscountPercent'] > 0){
				array_push($returnArray, array("id" => $row['id'], "effect" => "Market Discount (%)", "value" => $row['vendorDiscountPercent']));
			}
			
		}
		return $returnArray;
		
	}

	if (isset($_POST['joinGuild'])){
		joinGuild($_POST['joinGuild']);
	}
	elseif(isset($_POST['newGuildName'])){
		createGuild($_POST['newGuildName'],$_POST['newGuildDescription'],$_POST['newGuildEffect']);
	}
	elseif(isset($_GET['showAllGuilds'])){
		loadGuilds();
	}
	elseif(isset($_GET['leaveGuild'])){
		leaveGuild();
	}
	elseif(isset($_GET['levelUpGuild'])){
		levelUpGui();
	}
	elseif(isset($_POST['guildEffect'])){
		levelUpGuild($_POST['guildEffect']);
	}



?>