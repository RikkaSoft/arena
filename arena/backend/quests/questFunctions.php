<?php

	function createRandomQuest(){
		global $conn;
		
		$levelMin = $_SESSION['characterProperties']['level']-1;
		if($levelMin < 1){
			$levelMin = 1;
		}
		$levelMax = $_SESSION['characterProperties']['level']+1;
		
		
		$questTypes = array("MK","MK","MK","MK","PK","PC","IC");
		$questType = $questTypes[mt_rand(0,6)];
		
		#$questType = "PK";
		
		if($questType == "MK"){
			$roll = mt_rand(0,100);
			
			if($roll < 50){
				$monsterAmount = 2;
			}
			elseif($roll < 70){
				$monsterAmount = 3;
			}
			elseif($roll < 80){
				$monsterAmount = 4;
			}
			elseif($roll < 90){
				$monsterAmount = 5;
			}
			else{
				$monsterAmount = 6;
			}
			$mobArray = getSuitableMonster($monsterAmount,$_SESSION['characterProperties']['level']);
			#var_dump($mobArray);
			$mobIds = array();
			$mobKills = array();
			$xpReward = 0;
			$goldReward = 0;
			foreach($mobArray as $mob){
				$mobIds[] = $mob['id'];
				$mobKills[] = $mob['quantity'];
				$xpReward += $mob['quantity'] * ($mob['xp']-1*1.3);
				$goldReward += $mob['quantity'] * ($mob['gold']-1*1.7);
			}
			$xpReward = round($xpReward * (mt_rand(8,12)/10));
			$goldReward = round($goldReward * (mt_rand(8,12)/10));
			$mobIds = implode(",",$mobIds);
			$mobKills = implode(",",$mobKills);
			
			$itemReward = chanceGetItem($goldReward,"random",2,1);
			
			
			if(isset($itemReward)){
				$itemId = $itemReward['id'];
				$itemType = $itemReward['type'];
				$sql = "INSERT INTO questavailable (mobIds,mobKills,xpReward,goldReward,itemReward,itemType,userCreated,levelMin,levelMax,questSubType) VALUES ('$mobIds','$mobKills','$xpReward','$goldReward','$itemId','$itemType',1,$levelMin,$levelMax,'MK')";
			}
			else{
				$sql = "INSERT INTO questavailable (mobIds,mobKills,xpReward,goldReward,userCreated,levelMin,levelMax,questSubType) VALUES ('$mobIds','$mobKills','$xpReward','$goldReward',1,$levelMin,$levelMax,'MK')";
			}
			mysqli_query($conn,$sql);
			
		}
		elseif($questType == "PK"){
			$playerKills = 0;
			$playerVictories = mt_rand(3,10);
			$goldReward = $playerVictories * $_SESSION['characterProperties']['level'] * 3 + mt_rand(0,$_SESSION['characterProperties']['level'] * 15);
			$xpReward = $playerVictories * $_SESSION['characterProperties']['level'] * 3 + mt_rand(0,$_SESSION['characterProperties']['level'] * 15);
			if(mt_rand(0,4) == 2){
				$playerKills = mt_rand(1,3);
				$goldReward += $playerKills * $_SESSION['characterProperties']['level'] * 3 + mt_rand(0,$_SESSION['characterProperties']['level'] * 25);
				$xpReward += $playerKills * $_SESSION['characterProperties']['level'] * 3 + mt_rand(0,$_SESSION['characterProperties']['level'] * 25);
			}
			$itemReward = chanceGetItem($goldReward,"random",1,1);
			
			if(isset($itemReward)){
				$itemId = $itemReward['id'];
				$itemType = $itemReward['type'];
				$sql = "INSERT INTO questavailable (playerVictories,playerKills,xpReward,goldReward,itemReward,itemType,userCreated,levelMin,levelMax,questSubType) VALUES ('$playerVictories','$playerKills','$xpReward','$goldReward','$itemId','$itemType',1,$levelMin,$levelMax,'PK')";
			}
			else{
				$sql = "INSERT INTO questavailable (playerVictories,playerKills,xpReward,goldReward,userCreated,levelMin,levelMax,questSubType) VALUES ('$playerVictories','$playerKills','$xpReward','$goldReward',1,$levelMin,$levelMax,'PK')";
			}
			#echo $sql;
			mysqli_query($conn,$sql);
		}
		elseif($questType == "PC"){
			$roll = mt_rand(0,100);
			
			if($roll < 65){
				$partAmount = 1;
			}
			elseif($roll < 80){
				$partAmount = 2;
			}
			else{
				$partAmount = 3;
			}
			
			$sql = "SELECT * FROM craftingparts ORDER BY RAND() LIMIT $partAmount";
			$result = mysqli_query($conn,$sql);
			
			$partIds = array();
			$partAmount = array();
			$goldReward = 0;
			$xpReward = 0;
			
			while($row = mysqli_fetch_assoc($result)){
				$partIds[] = $row['id'];
				$amount = mt_rand(1,3);
				$partAmount[] = $amount;
				$goldReward += $row['tier']*$amount*26;
				$xpReward += $row['tier']*$amount*22;
			}
			$partIds = implode(",",$partIds);
			$partAmount = implode(",",$partAmount);
			$xpReward = round($xpReward * (mt_rand(8,12)/10));
			$goldReward = round($goldReward * (mt_rand(8,12)/10));
			
			$itemReward = chanceGetItem($goldReward,"random",2,1);
			
			if(isset($itemReward)){
				$itemId = $itemReward['id'];
				$itemType = $itemReward['type'];
				$sql = "INSERT INTO questavailable (partHandin,partAmount,xpReward,goldReward,itemReward,itemType,userCreated,levelMin,levelMax,questSubType) VALUES ('$partIds','$partAmount','$xpReward','$goldReward','$itemId','$itemType',1,$levelMin,$levelMax,'PC')";
			}
			else{
				$sql = "INSERT INTO questavailable (partHandin,partAmount,xpReward,goldReward,userCreated,levelMin,levelMax,questSubType) VALUES ('$partIds','$partAmount','$xpReward','$goldReward',1,$levelMin,$levelMax,'PC')";
			}
			mysqli_query($conn,$sql);
		}
		elseif($questType == "IC"){
			$itemRequired = getSuitableItem(999999999,"random",1,1);
			$itemHandinType = $itemRequired['type'];
			$itemHandin = $itemRequired['id'];
			if($itemHandinType == "weapons"){
				$goldReward = round($itemRequired['value']*0.4);
				$xpReward = round($itemRequired['value']*0.3);
			}
			else{
				$goldReward = round($itemRequired['value']*0.65);
				$xpReward = round($itemRequired['value']*0.25);
			}
			
			if($goldReward > 4000){
				$goldReward = 4000;
			}
			if($xpReward > 2400){
				$xpReward = 2400;
			}
			
			$xpReward = round($xpReward * (mt_rand(8,12)/10));
			$goldReward = round($goldReward * (mt_rand(8,12)/10));
			
			$itemReward = chanceGetItem($goldReward,"random",2,1);
			
			if(isset($itemReward)){
				$itemId = $itemReward['id'];
				$itemType = $itemReward['type'];
				$sql = "INSERT INTO questavailable (itemHandin,itemHandinType,xpReward,goldReward,itemReward,itemType,userCreated,levelMin,levelMax,questSubType) VALUES ('$itemHandin','$itemHandinType','$xpReward','$goldReward','$itemId','$itemType',1,'$levelMin','$levelMax','IC')";
			}
			else{
				$sql = "INSERT INTO questavailable (itemHandin,itemHandinType,xpReward,goldReward,userCreated,levelMin,levelMax,questSubType) VALUES ('$itemHandin','$itemHandinType','$xpReward','$goldReward',1,'$levelMin','$levelMax','IC')";
			}
			mysqli_query($conn,$sql);
		}
		return mysqli_insert_id($conn);
	}
	
	function chanceGetItem($goldReward,$random,$unique,$getItemType){
		if(mt_rand(0,4) == 3){
			#GET ITEM
			$item = getSuitableItem($goldReward,$random,$unique,$getItemType);
			if($item){
				return $item;
			}
		}
	}
	function getSuitableItem($value,$type,$unique,$getItemType = 0){
		global $conn;
		if($type == "random"){
			if(mt_rand(0,5) <= 1){
				$type = "armours";
			}
			else{
				$type = "weapons";
			}
		}
		
		if($unique == 1){
			//ONLY UNIQUES
			$sql = "SELECT * FROM $type WHERE price <= '$value' AND rarity IS NOT NULL AND available=1 AND userCrafted=0 ORDER BY RAND() LIMIT 1";
		}
		elseif($unique == 0){
			//ONLY vendorable
			$sql = "SELECT * FROM $type WHERE price <= '$value' AND sellable=1 AND available=1 AND userCrafted=0 ORDER BY RAND() LIMIT 1";
		}
		else{
			//ANY
			$sql = "SELECT * FROM $type WHERE price <= '$value' AND available=1 AND userCrafted=0 ORDER BY RAND() LIMIT 1";
		}
		#echo $sql;
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			if($getItemType == 1){
				return array("id"=>$row['id'],"type"=>$type,"value"=>$row['price']);
			}
			else{
				return $row['id'];
			}
		}
		else{
			return false;
		}
		
	}
	
	function getSuitableMonster($monsterAmount,$level){
		global $conn;
		$levelPlus = $level+3;
		$levelMinus = $level-3;
		#echo "TEST";
		$sql = "SELECT * FROM npc WHERE level <= '$levelPlus' AND level >= '$levelMinus' ORDER BY RAND() LIMIT $monsterAmount";
		$result = mysqli_query($conn,$sql);
		#echo $sql;
		if(mysqli_num_rows($result) != $monsterAmount){
			$sql = "SELECT * FROM npc ORDER BY RAND() LIMIT $monsterAmount";
			$result = mysqli_query($conn,$sql);
		}
		$mobArray = array();
		while($row = mysqli_fetch_assoc($result)){
			if($row['chance'] <= 10){
				$quantity = mt_rand(1,5);
			}
			else{
				$quantity = mt_rand(5,15);
			}
			$mobArray[] = array("id"=>$row['id'],"quantity"=>$quantity,"gold"=>$row['goldReward'],"xp"=>$row['xpReward']);
		}	
		return $mobArray;
	}

	function insertQuestPending($charId,$questIds,$questSlot){
		global $conn;
		$quest1 = $questIds[0];
		$quest2 = $questIds[1];
		$quest3 = $questIds[2];
		$sql = "INSERT INTO questpending (charId,quest1,quest2,quest3,questSlot) VALUES ('$charId','$quest1','$quest2','$quest3','$questSlot')";	
		mysqli_query($conn,$sql);	
	}
	
	function getFinishedQuests($charId){
		global $conn;
		$sql = "SELECT questId FROM questcomplete WHERE charId='$charId'";
		$result = mysqli_query($conn,$sql);
		$ids = array();
		if(mysqli_num_rows($result) > 0){
			while ($row = mysqli_fetch_assoc($result)){
				$ids[] = $row['questId'];
			}
			return implode(",",$ids);
		}
	}
	
	function getRandomQuest($type,$questNumber){
		global $conn;
		
		$finishedIds = getFinishedQuests($_SESSION['characterProperties']['id']);
		$activeQuests = getActiveQuests();
		$activeQuestIds = array();
		if(isset($activeQuests['1'])){
			$activeQuestIds[] = $activeQuests['1']['questId'];
		}
		if(isset($activeQuests['2'])){
			$activeQuestIds[] = $activeQuests['2']['questId'];
		}
		if(isset($activeQuests['3'])){
			$activeQuestIds[] = $activeQuests['3']['questId'];
		}
		
		$level = $_SESSION['characterProperties']['level'];
		
		if(empty($activeQuestIds)){
			$sql = "SELECT * FROM questavailable WHERE '$level' >= levelMin  AND '$level' <= levelMax AND questType='$type' AND id NOT IN ($finishedIds) ORDER BY RAND() LIMIT 3";
		}
		else{
			$activeIds = implode(",",$activeQuestIds);
			if(isset($finishedIds)){
				$activeIds .= "," . $finishedIds;
			}
			$sql = "SELECT * FROM questavailable WHERE '$level' >= levelMin  AND '$level' <= levelMax AND questType='$type' AND id NOT IN ($activeIds) ORDER BY RAND() LIMIT 3";
			#echo $sql;
		}
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) < 3){
			for ($i=0; $i < 10; $i++) { 
				createRandomQuest();
			}
			if(isset($activeIds)){
				$sql = "SELECT * FROM questavailable WHERE '$level' >= levelMin  AND '$level' <= levelMax AND questType='$type' AND id NOT IN ($activeIds) ORDER BY RAND() LIMIT 3";
			}	
			else{
				$sql = "SELECT * FROM questavailable WHERE '$level' >= levelMin  AND '$level' <= levelMax AND questType='$type' ORDER BY RAND() LIMIT 3";
			}
			$result = mysqli_query($conn,$sql);
		}
		
		$questIdArr = array();
		$rowArr = array();
		while ($row = mysqli_fetch_assoc($result)){
			$questIdArr[] = $row['id'];
			$rowArr[] = $row;
		}
		insertQuestPending($_SESSION['characterProperties']['id'], $questIdArr,$questNumber);
		questBoxes($rowArr);
	}
	
	function questBoxes($rowArr){
		
		foreach ($rowArr as $row){
			echo "<div class='questHolder'>";
				echoQuestInfo($row,0,1,0);
				echo "<div class='newButton chooseQuest' id='" . $row['id'] . "'>Choose this quest</div>";
			echo "</div>";
		}
		echo "<div id='generateNewQuest'>None of these quests are any good. <br><div class='newButton' id='newQuestButton'>Create a new random quest for me!</div></div>";
		echo "<div id='monsterInfo'></div>";
		echo "<div id='itemInfo'></div>";
		include("frontend/design/js/npcinfo.html");
		
		?>
			<script>
				$('.chooseQuest').click(function(){
					var id = $(this).attr('id');
					$('#mainPage').fadeOut(400,function(){
						$('#mainPage').load('index.php?nonUI&qpage=questFunctions&chooseQuest='+id,function(){
							$('#mainPage').fadeIn(400);
						});
					});
				});
				
				$('#newQuestButton').click(function(){
					$('#mainPage').fadeOut(400,function(){
						$('#mainPage').load('index.php?nonUI&qpage=questFunctions&generateNewQuest',function(){
							$('#mainPage').fadeIn(400);
						});
					});
				});
			</script>
		<?php
	}
	
	function echoQuestInfo($row,$showProgress,$choiceListing,$questSlot){
		#var_dump($row);
		global $conn;
		
		$charId = $_SESSION['characterProperties']['id'];
		$questId = $row['id'];
		
		if($row['questSubType'] == "MK"){
			$questType = "Enemy Quest";
		}
		elseif($row['questSubType'] == "PK"){
			$questType = "Arena Quest";
		}
		elseif($row['questSubType'] == "IC"){
			$questType = "Item Quest";
		}
		elseif($row['questSubType'] == "PC"){
			$questType = "Part Quest";
		}
		echo "<div id='questTitle'>";
			echo $questType;
		echo "</div>";
		#var_dump($row);
			echo "<fieldset id='questInfo'>";
			echo "<legend>Objectives</legend>";
			$complete = 1;
				if(isset($row['mobIds'])){
					if($showProgress == 1){
						$sql = "SELECT * FROM questactive WHERE charId='$charId' AND questId='$questId'";
						$result = mysqli_query($conn,$sql);
						$mobProgress = mysqli_fetch_assoc($result);
						$mobProgress = explode(",",$mobProgress['mobProgress']);
						
						$expIds = explode(",",$row['mobIds']);
						$expKills = explode(",",$row['mobKills']);
						for ($i=0; $i < count($expIds); $i++) {
							if($expKills[$i] > 1){
								$s = "s";
							} 
							else{
								$s = "";
							}
							$name = getNpcName($expIds[$i]);
							echo "Killed <strong>" .$mobProgress[$i] . " / " . $expKills[$i] . "</strong> <span class='enemy' id='" . $name . "'>" . $name . $s . "</span><br>";
							if($mobProgress[$i] != $expKills[$i]){
								$complete = 0;
							}
						}
					}
					else{
						$expIds = explode(",",$row['mobIds']);
						$expKills = explode(",",$row['mobKills']);
						for ($i=0; $i < count($expIds); $i++) {
							if($expKills[$i] > 1){
								$s = "s";
							} 
							else{
								$s = "";
							}
							$name = getNpcName($expIds[$i]);
							echo "Kill <strong>" . $expKills[$i] . "</strong> <span class='enemy' id='" . $name . "'>" . $name . $s . "</span><br>";
						}
					}
					
					
				}
				if(isset($row['playerVictories'])){
					if($showProgress == 1){
						$sql = "SELECT * FROM questactive WHERE charId='$charId' AND questId='$questId'";
						$result = mysqli_query($conn,$sql);
						$pk = mysqli_fetch_assoc($result);
						echo "<strong>" .$pk['arenaProgress'] . " / " . $row['playerVictories'] . "</strong> arena or tournament victories";
						if($pk['arenaProgress'] < $row['playerVictories']){
							$complete = 0;
						}
					}
					else{
						echo "<strong>" . $row['playerVictories'] . "</strong> arena or tournament victories";
					}
					if($row['playerKills'] > 0){
						if($showProgress == 1){
							echo "<br><strong>" .$pk['arenaKillProgress'] . " / " . $row['playerKills'] . "</strong> kills (mortal wounds)";
						}
						else{
							echo "<br><strong>" . $row['playerKills'] . "</strong> kills (mortal wounds)";
						}
						if($pk['arenaKillProgress'] < $row['playerKills']){
							$complete = 0;
						}
					}
				}
				if(isset($row['partHandin'])){
					$expIds = explode(",",$row['partHandin']);
					$expKills = explode(",",$row['partAmount']);
					$playerParts = getPlayerParts($_SESSION['characterProperties']['crafting_id'],0);
					for ($i=0; $i < count($expIds); $i++) {
						$found = 0;
						if($expKills[$i] > 1){
							$s = "s";
						} 
						else{
							$s = "";
						}
						$pid = $expIds[$i];
						$sql = "SELECT name FROM craftingparts WHERE id='$pid'";
						$result = mysqli_query($conn,$sql);
						$row5 = mysqli_fetch_assoc($result);
						$name = $row5['name'];
						#if($choiceListing == 0){
							$partCount = 0;
							#var_dump($playerParts);
							foreach($playerParts as $part){
								$ex = explode(":",$part);
								if($expIds[$i] == $ex[0]){
									$found = 1;
									if($expKills[$i] <= $ex[1]){
										$partCount = $expKills[$i];
									}
									else{
										$partCount = $ex[1];
										$complete = 0;
									}
									break;
								}
								
							}
							if($found == 0){
								$complete = 0;
							}
							echo "Hand in <strong>" . $partCount . "/" . $expKills[$i] . "</strong> <span class='itemPart' id='" . $expIds[$i] . "'>" . $name . $s . "</span><br>";
						/*}
						else{
							echo "Hand in <strong>" . $expKills[$i] . "</strong> <span class='itemPart' id='" . $expIds[$i] . "'>" . $name . $s . "</span><br>";
						}*/
					}
				}
				if(isset($row['itemHandin'])){
					$name = getItemName($row['itemHandinType'],$row['itemHandin']);
					#echo "Hand in <strong>1</strong>";
					$itemCount = 0;
					if($row['itemHandinType'] == "weapons"){
						$playerInventory = getPlayerInventory($_SESSION['characterProperties']['inventory_id'],"weapons");
						foreach($playerInventory as $item){
							$ex = explode(":",$item);
							if($row['itemHandin'] == $ex[0]){
								$itemCount = 1;
								break;
							}							
						}
						#echo "Hand in <strong>" . $itemCount . "/1</strong> <span class='item1Override' id='" . $row['itemHandin'] . ":1;1'> " . $name . "</span>";
						echo "Hand in <strong>" . $itemCount . "/1</strong> <span class='item1Override' id='" . $row['itemHandin'] . ":1;1'> " . $name . "</span>";

					} 
					else{
						$playerInventory = getPlayerInventory($_SESSION['characterProperties']['inventory_id'],"armour");
						#var_dump($playerInventory);
						foreach($playerInventory as $item){
							$ex = explode(":",$item);
							if($row['itemHandin'] == $ex[0]){
								$itemCount = 1;
								break;
							}							
						}
						echo "Hand in <strong>" . $itemCount . "/1</strong> <span class='item2Override' id='" . $row['itemHandin'] . ":1;1'> " . $name . "</span>";

					}
					if($itemCount == 0){
						$complete = 0;
					}

				}
			echo "</fieldset>";
		
			echo "<fieldset id='questRewards'>";
				echo "<legend>Rewards</legend>";
				echo "XP: " . $row['xpReward'];
				echo "<br>";
				echo "Gold: " . $row['goldReward'];
				if(isset($row['itemReward'])){
					$name = getItemName($row['itemType'],$row['itemReward']);
					echo "<br>";
					if($row['itemType'] == "weapons"){
						echo "<span class='item1Override' id='" . $row['itemReward'] . ":1;1'> " . $name . "</span>";
					} 
					else{
						echo "<span class='item2Override' id='" . $row['itemReward'] . ":1;1'> " . $name . "</span>";
					}
				}
			echo "</fieldset>";
		
		if($complete == 1 && $choiceListing == 0){
			echo "<div class='newButton completeButton' id='" .$questSlot . "'>Complete!</div>";
		}
		if($choiceListing == 0){
			echo "<div class='abandonQuest' id='" . $questSlot . "'>";
				echo "X";
			echo "</div>";
		}
	}
	
	function getPlayerParts($charID,$returnRow){
		global $conn;
		$sql = "SELECT * FROM craftinginventory WHERE id='$charID'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		if($returnRow == 1){
			return $row;
		}
		else{
			$allParts = $row['base'] . "," . $row['main'] . "," . $row['extra'];
			return explode(",",$allParts);
		}
		
	}
	function getPlayerInventory($iid,$type){
		global $conn;
		$sql = "SELECT * FROM inventory WHERE iid='$iid'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		#var_dump($row);
		if($type == "weapons"){
			$allParts = $row['weapons'] . "," . $row['secondarys'];
		}
		else{
			$allParts = $row['heads'] . "," . $row['chests'] . "," . $row['arms'] . "," . $row['legs'] . "," . $row['feets'];
		}
		return explode(",",$allParts);
	}
	
	
	function getItemName($table,$id){
		global $conn;
		$sql = "SELECT name FROM $table WHERE id='$id'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		return $row['name'];
	}

	function getNpcName($id){
		global $conn;
		$sql = "SELECT name FROM npc WHERE id='$id'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		return $row['name'];
	}
	
	function listQuests(){
		
		$quests = getActiveQuests();
		if(isset($quests['1'])){
			echo "<div class='questHolder' id=1>";
				echoQuestInfo($quests['1'],1,0,1);
			echo "</div>";
		}
		else{
			echo "<div class='questHolder availableQuest' id=1>";
				if($_SESSION['characterProperties']['questDaily1Available'] == 1){
					echo "Click to pick a quest!";
				}
				else{
					echo "New quest available at 23:59";
				}
			echo "</div>";
		}
		if(isset($quests['2'])){
			echo "<div class='questHolder' id=2>";
				echoQuestInfo($quests['2'],1,0,2);
			echo "</div>";
		}
		else{
			echo "<div class='questHolder availableQuest' id=2>";
				if($_SESSION['characterProperties']['questDaily2Available'] == 1){
					echo "Click to pick a quest!";
				}
				else{
					echo "New quest available at 23:59";
				}
			echo "</div>";
		}
		if(isset($quests['3'])){
			echo "<div class='questHolder' id=3>";
				echoQuestInfo($quests['3'],1,0,3);
			echo "</div>";
		}
		else{
			echo "<div class='questHolder availableQuest' id=3>";
				if($_SESSION['characterProperties']['questDaily3Available'] == 1){
					echo "Click to pick a quest!";
				}
				else{
					echo "New quest available at 23:59";
				}
			echo "</div>";
		}
		echo "<div id='monsterInfo'></div>";
		echo "<div id='itemInfo'></div>";
		include("frontend/design/js/npcinfo.html");
		echo "<script>
			$('.availableQuest').click(function(){
				var type = $(this).attr('id');
				$('#mainPage').fadeOut(400,function(){
					$('#mainPage').load('index.php?nonUI&qpage=questFunctions&pickQuest&type='+type,function(){
						$('#mainPage').fadeIn(400);
					});
				});
			});
		</script>";
		echo "<script>
			$('.abandonQuest').click(function(){
				var r = confirm('Do you really want to abandon this quest?');
				if (r == true) {
					var questSlot = $(this).attr('id');
					$('#mainPage').fadeOut(400,function(){
						$('#mainPage').load('index.php?nonUI&qpage=questFunctions&abandonQuest='+questSlot,function(){
							$('#mainPage').fadeIn(400);
						});
					});
				}
			});
			$('.completeButton').click(function(){
				var questSlot = $(this).attr('id');
				$('#mainPage').fadeOut(400,function(){
					$('#mainPage').load('index.php?nonUI&qpage=questFunctions&completeQuest='+questSlot,function(){
						$('#mainPage').fadeIn(400);
					});
				});
			});
		</script>";
		
	}
	
	function getActiveQuests(){
		global $conn;
		$charId = $_SESSION['characterProperties']['id'];
		
		$sql = "SELECT * FROM questactive INNER JOIN questavailable ON questactive.questId = questavailable.id WHERE questactive.charId='$charId'";
		#echo $sql;
		$result = mysqli_query($conn,$sql);
		$quests = array();
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				#var_dump($row);
				if($row['questSlot'] == 1){
					$quests['1'] = $row;
				}
				elseif($row['questSlot'] == 2){
					$quests['2'] = $row;
				}		
				elseif($row['questSlot'] == 3){
					$quests['3'] = $row;
				}
			}
		}
		return $quests;
	}
	
	function updateQuestProgressionKills($questId,$newAmount,$charId){
		global $conn;
		$sql = "UPDATE questactive SET mobProgress='$newAmount' WHERE charId='$charId' and questId='$questId'";
		mysqli_query($conn,$sql);
	}
	
	function updateQuestProgressionKillsArena($questId,$kill,$charId){
		global $conn;
		
		if($kill == 1){
			$value = "arenaKillProgress";
		}
		elseif($kill == 0){
			$value = "arenaProgress";
		}
		$sql = "UPDATE questactive SET $value=$value+1 WHERE charId='$charId' and questId='$questId'";
		mysqli_query($conn,$sql);
	}
	
	function checkQuestsAfterKill($charId,$mob,$mobId,$kill){
		global $conn;
		$sql = "SELECT qac.*,qal.* FROM questactive AS qac INNER JOIN questavailable AS qal ON qac.questId = qal.id WHERE qac.charId='$charId'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				if($mob == 1){				
					$mobIds = explode(",",$row['mobIds']);
					$killRequired = explode(",",$row['mobKills']);
					$killProgress = explode(",",$row['mobProgress']);
					$found = 0;
					for($i = 0; $i < count($mobIds);$i++){
						if($mobIds[$i] == $mobId){
							$found = 1;
							if($killProgress[$i] < $killRequired[$i]){
								if(($killProgress[$i]+1) >= $killRequired[$i]){
									$killProgress[$i] = $killRequired[$i];
								}
								else{
									$killProgress[$i] = $killProgress[$i]+1;
								}
							}
						}
					}
					if($found == 1){
						$newAmount = implode(",",$killProgress);
						#var_dump($newAmount);
						#var_dump($killProgress);
						updateQuestProgressionKills($row['questId'], $newAmount, $charId);
					}
				}
				else{
					if($kill == 1){
						if(isset($row['playerKills'])){
							if($row['arenaKillProgress'] < $row['playerKills']){
								updateQuestProgressionKillsArena($row['questId'], 1, $charId);
							}
						}
					}
					else{
						if(isset($row['playerVictories'])){
							if($row['arenaProgress'] < $row['playerVictories']){
								updateQuestProgressionKillsArena($row['questId'], 0, $charId);
							}
						}
					}
				}
			}
		}
	}
	
	function removeQuestPending($charId){
		global $conn;
		$sql = "DELETE FROM questpending WHERE charId='$charId'";
		mysqli_query($conn,$sql);
	}
	
	function bindQuestToPlayer($charId,$questId,$questSlot){
		global $conn;
		
		$sql = "SELECT * FROM questavailable WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $questId);
		if(mysqli_stmt_execute($stmt)){
			$result = $stmt->get_result();
			$row = mysqli_fetch_assoc($result);
			if($row['questSubType'] == "MK"){
				$mobKills = explode(",",$row['mobKills']);
				for ($i=0; $i < count($mobKills); $i++) { 
					$mobKills[$i] = 0;
				}
				$mobKills = implode(",",$mobKills);
				$sql = "INSERT INTO questactive (questId,charId,mobProgress,questSlot) VALUES('$questId','$charId','$mobKills','$questSlot')";		
			}
			elseif($row['questSubType'] == "PK"){
				$sql = "INSERT INTO questactive (questId,charId,arenaProgress,arenaKillProgress,questSlot) VALUES('$questId','$charId',0,0,'$questSlot')";		
			}
			else{
				$sql = "INSERT INTO questactive (questId,charId,questSlot) VALUES('$questId','$charId','$questSlot')";		
			}
			#echo $sql;
			mysqli_query($conn,$sql);
			removeQuestPending($charId);
		}
		else{
			echo "invalid quest ID";
			exit;
		}		
	}
	
	function checkCompleteQuest($charId,$questSlot){
		global $conn;
		$sql = "SELECT qac.*,qal.* FROM questactive AS qac INNER JOIN questavailable AS qal ON qac.questId = qal.id WHERE qac.charId=? AND qac.questSlot=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ii", $charId,$questSlot);
		if(mysqli_stmt_execute($stmt)){
			$result = $stmt->get_result();
			$row = mysqli_fetch_assoc($result);
			if($row['questSubType'] == "MK"){
				if($row['mobKills'] == $row['mobProgress']){
					completeQuest($row);
				}
				else{
					echo "You have not killed the correct amount of mobs";
					exit;
				}
			}
			elseif($row['questSubType'] == "PK"){
				if($row['arenaProgress'] == $row['playerVictories'] && $row['arenaKillProgress'] == $row['playerKills']){
					completeQuest($row);
				}
				else{
					echo "You have not killed/beaten the correct amount of players";
					exit;
				}
			}
			elseif($row['questSubType'] == "PC"){
				$complete = 1;
				$expIds = explode(",",$row['partHandin']);
				$expKills = explode(",",$row['partAmount']);
				$playerParts = getPlayerParts($_SESSION['characterProperties']['crafting_id'],0);
				for ($i=0; $i < count($expIds); $i++) {
					foreach($playerParts as $part){
						$found = 0;
						$ex = explode(":",$part);
						if($expIds[$i] == $ex[0]){
							$found = 1;
							if($expKills[$i] <= $ex[1]){
								
							}
							else{
								$complete = 0;
							}
							break;
						}
						if($found = 0){
							$complete = 0;
						}
					}
				}
				if($complete == 1){
					subtractParts($expIds,$expKills,$_SESSION['characterProperties']['crafting_id']);
					completeQuest($row);
					
				}
			}
			elseif($row['questSubType'] == "IC"){
				$table = $row['itemHandinType'];
				$itemId = $row['itemHandin'];
				$sql = "SELECT * FROM $table WHERE id=$itemId";
				$result=mysqli_query($conn, $sql);
				$item = mysqli_fetch_assoc($result);
				$itemSubType = $item['item_type'];

				if($table == "weapons"){
					if($itemSubType == "crossbows" || $itemSubType == "bows"){
						$itemSubType = "secondarys";
					}
					else{
						$itemSubType = "weapons";
					}
				}


				$inv_id = $_SESSION['characterProperties']['inventory_id'];
				$sql = "SELECT * FROM inventory WHERE iid = '$inv_id'";
				$result=mysqli_query($conn, $sql);
				$equipment = mysqli_fetch_assoc($result);
				
				$sellItems = $equipment[$itemSubType];
				
				$explodedInv = explode(",", $sellItems);
				
				$found = "false";
				$itemCount = 0;
				foreach ($explodedInv as $items){
					$baseId = (explode(":",$items))[0];
					if($baseId == $itemId){
						$found = "true";
						unset($explodedInv[$itemCount]);
						break;
					}
					$itemCount++;
				}
				
				if ($found == "true"){
					$implodedInv = implode(',', $explodedInv);
					
					$sql = "UPDATE inventory SET $itemSubType=? WHERE iid = ?";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "si", $implodedInv,$inv_id);
					mysqli_stmt_execute($stmt);
					completeQuest($row);
				}
				else{
					echo "You don't seem to have this item";
				}
			}
		}
	}
	function subtractParts($partIds,$amount,$craftId){
		global $conn;
		$playerParts = getPlayerParts($craftId,1);
		$baseParts = explode(",",$playerParts['base']);
		$mainParts = explode(",",$playerParts['main']);
		$extraParts = explode(",",$playerParts['extra']);

		for ($i=0; $i < count($partIds) ; $i++) { 
			for ($ii=0; $ii < count($baseParts); $ii++) {
				$ex = explode(":",$baseParts[$ii]); 
				if($ex[0] == $partIds[$i]){
					$ex[1] -= $amount[$i];
					$baseParts[$ii] = implode(":",$ex);
					break;
				}
			}
			for ($ii=0; $ii < count($mainParts); $ii++) { 
				$ex = explode(":",$mainParts[$ii]); 
				if($ex[0] == $partIds[$i]){
					$ex[1] -= $amount[$i];
					$mainParts[$ii] = implode(":",$ex);
					break;
				}
			}
			for ($ii=0; $ii < count($extraParts); $ii++) { 
				$ex = explode(":",$extraParts[$ii]); 
				if($ex[0] == $partIds[$i]){
					$ex[1] -= $amount[$i];
					$extraParts[$ii] = implode(":",$ex);
					break;
				}
			}
		}
		$baseParts = implode(",",$baseParts);
		$mainParts = implode(",",$mainParts);
		$extraParts = implode(",",$extraParts);
		
		$sql = "UPDATE craftinginventory SET base='$baseParts',main='$mainParts',extra='$extraParts' WHERE id='$craftId'";
		mysqli_query($conn,$sql);
		
	}
	
	function completeQuest($row){
		global $conn;
		
		insertQuestRewards($row['xpReward'], $row['goldReward'],$row['charId']);
		if (isset($row['itemReward'])){
			insertItem($row['itemReward'], $row['itemType']);
		}
		
		$startDate = $row['questStart'];
		$questId = $row['questId'];
		$charId = $row['charId'];
		$sql = "INSERT INTO questcomplete (questId,charId,startDate) VALUES ('$questId','$charId','$startDate')";
		mysqli_query($conn,$sql);	
		finishQuest($charId,$row['questSlot']);
	}
	
	function insertQuestRewards($xp,$gold,$charId){
		global $conn;
		$sql = "UPDATE characters SET experience=experience+'$xp',gold=gold+'$gold' WHERE id='$charId'";
		mysqli_query($conn,$sql);
	}
	
	function insertItem($itemId,$itemType){
		global $conn;
			$sql = "SELECT * FROM $itemType WHERE id='$itemId'";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			if($itemType == "weapons"){
				if($row['item_type'] == "bows" || $row['item_type'] == "crossbows"){
					$insertType = "secondarys";
				}
				else{
					$insertType = "weapons";
				}
			}
			else{
				$insertType = $row['item_type'];
			}
			$item = $itemId . ":1;1,";
			$invId = $_SESSION['characterProperties']['inventory_id'];
			$sql = "UPDATE inventory SET $insertType=concat($insertType,'$item') WHERE iid='$invId'";
			#var_dump($sql);
			mysqli_query($conn,$sql);
	}
	
	function checkQuestPending($charId,$returnSlot){
	global $conn;
		
		$sql = "SELECT * FROM questpending WHERE charId='$charId'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			if($returnSlot == 1){
				$row = mysqli_fetch_assoc($result);
				return $row['questSlot'];
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
		
	}
	
	
	function whichQuestSlot($charId,$questId){
		global $conn;
		
		$sql = "SELECT * FROM questpending WHERE charId='$charId'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 1){
			echo "Something has gone wrong, try again";
			removeQuestPending($charId);
		}
		elseif(mysqli_num_rows($result) == 1){
			$row = mysqli_fetch_assoc($result);
			
			if($row['quest1'] == $questId || $row['quest2'] == $questId || $row['quest3'] == $questId){
				#echo "BINDING QUEST<br>";
				bindQuestToPlayer($charId,$questId,$row['questSlot']);
				listQuests();
				
			}
		}
		else{
			echo "The system thinks you are trying to cheat";
		}
	}
	
	function listPendingQuests($charId){
		global $conn;
		$sql = "SELECT * FROM questpending WHERE charId='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$ids = $row['quest1'] . "," . $row['quest2'] . "," . $row['quest3'];
		$questSlot = $row['questSlot'];
		
		$sql = "SELECT * FROM questavailable WHERE id IN ($ids)";
		$result = mysqli_query($conn,$sql);
		$rowArr = array();
		while($row = mysqli_fetch_assoc($result)){
			$rowArr[] = $row;
		}
		questBoxes($rowArr);
		
	}
	
	function finishQuest($charId, $questSlot){
		global $conn;
		
		switch ($questSlot){
			case 1:
				$charQuestSlot = "questDaily1Available";
				break;
			case 2:
				$charQuestSlot = "questDaily2Available";
				break;
			case 3:
				$charQuestSlot = "questDaily3Available";
				break;
		}
		if (isset($charQuestSlot)){
			$sql = "DELETE FROM questactive WHERE charId='$charId' AND questSlot='$questSlot'";
			mysqli_query($conn,$sql);
			$sql = "UPDATE characters SET $charQuestSlot=0 WHERE id='$charId'";
			mysqli_query($conn,$sql);
			require_once(__ROOT__."/backend/character/update-characterSessions.php");
			//TO REFRESH CHARACTERINFO
            echo
            "<script>
                window.onload = updateChar();
            </script>";
		}
		#var_dump($sql);
		
	}
	
	if(isset($_GET['pickQuest'])){
		if(!checkQuestPending($_SESSION['characterProperties']['id'],0)){
			$type = $_GET['type'];
			if($type == 1){
				if($_SESSION['characterProperties']['questDaily1Available'] == 1){
					getRandomQuest("daily",1);
				}
			}
			if($type == 2){
				if($_SESSION['characterProperties']['questDaily2Available'] == 1){
					getRandomQuest("daily",2);
				}
			}
			if($type == 3){
				if($_SESSION['characterProperties']['questDaily3Available'] == 1){
					getRandomQuest("daily",3);
				}
			}
		}
		else{
			echo "Error, you already have a quest waiting to be chosen, please refresh the quest page";
		}
	}
	if(isset($_GET['chooseQuest'])){
		whichQuestSlot($_SESSION['characterProperties']['id'],$_GET['chooseQuest']);
	}
	if(isset($_GET['generateNewQuest'])){
		$charId = $_SESSION['characterProperties']['id'];
		$questSlot = checkQuestPending($charId,1);
		if(isset($questSlot)){
			$questId = createRandomQuest();
			if(isset($questId)){
				bindQuestToPlayer($charId, $questId, $questSlot);
				listQuests();
			}
			else{
				echo "Quest creation failed, try again";
			}
		}
		else{
			echo "The system thinks you are trying to cheat";
		}
	}
	if(isset($_GET['abandonQuest'])){
		finishQuest($_SESSION['characterProperties']['id'],intval($_GET['abandonQuest']));
		listQuests();
	}
	if(isset($_GET['completeQuest'])){
		checkCompleteQuest($_SESSION['characterProperties']['id'],intval($_GET['completeQuest']));
		listQuests();
	}
?>