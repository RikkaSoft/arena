<?php
	
	function ChooseWeaponType(){
		$typeRoll = mt_rand(0,100);
		$weaponType = "armour";
		if($typeRoll < 30){
			$weaponType = "melee";
		}
		return $weaponType;
	}

	function getRandomPartVendor($type,$charId){
		global $conn;
		$weaponType = "melee";
		$totalRoll = 0;
		$sql = "SELECT name,id,rarity,slotType FROM craftingparts WHERE slotType='$type' AND weaponType='$weaponType' ORDER BY rarity";

		
		$result = mysqli_query($conn,$sql);
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
			$totalRoll+= $row['rarity'];
		}
		
		$rollToGoUnder = mt_rand(0, $totalRoll-1);
		$compareRoll = 0;
		foreach($rows as $row){
			$compareRoll += $row['rarity'];
			if($compareRoll > $rollToGoUnder){
				$winningPart = $row;
				break;
			}
		}
		$partId = $winningPart['id'];
		$sql = "INSERT INTO craftingpartssale (partId,characterId,sold) VALUES ('$partId','$charId',0)";
		mysqli_query($conn,$sql);
	}

	function getRandomPartTier($tier){
		global $conn;

		$in = false;
		$weaponType = ChooseWeaponType();
		$roll = mt_rand(0,100);
		if ($roll < 75 && $weaponType == "melee"){
			$type = "extra";
		}
		elseif($roll < 90){
			$type = "main";
		}
		else{
			$type = "base";
			if($weaponType == "armour"){
				$type = "'Cmain','Hmain','Amain','Lmain'";
				$in = true;
				if($tier == 5){
					$tier = 4;
				}
			}
		}

		$totalRoll = 0;
		$sql = "SELECT name,id,rarity,slotType FROM craftingparts WHERE slotType='$type' AND tier=$tier AND weaponType='$weaponType' ORDER BY rarity";
		$result = mysqli_query($conn,$sql);
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
			$totalRoll+= $row['rarity'];
		}
		
		$rollToGoUnder = mt_rand(0, $totalRoll-1);
		$compareRoll = 0;
		foreach($rows as $row){
			$compareRoll += $row['rarity'];
			if($compareRoll > $rollToGoUnder){
				$winningPart = $row;
				break;
			}
		}

		return $winningPart;

	}

	function getRandomPart($from, $userCraftId = "none"){
		global $conn;
		$in = false;
		$weaponType = ChooseWeaponType();
		$roll = mt_rand(0,100);
		if ($roll < 75 && $weaponType == "melee"){
			$type = "extra";
		}
		elseif($roll < 90){
			$type = "main";
		}
		else{
			$type = "base";
			if($weaponType == "armour"){
				$type = "'Cmain','Hmain','Amain','Lmain'";
				$in = true;
			}
		}
		
		$totalRoll = 0;
		if(!$in){
			$tierRoll = mt_rand(0,100);
			if($tierRoll < 35){
				$tier = 1;
			}
			else if($tierRoll < 55){
				$tier = 2;
			}
			else if($tierRoll < 75){
				$tier = 3;
			}
			else if($tierRoll < 90){
				$tier = 4;
			}
			else{
				$tier = 5;
			}
			$sql = "SELECT name,id,rarity,slotType FROM craftingparts WHERE slotType='$type' AND weaponType='$weaponType' AND tier='$tier' ORDER BY rarity";
		}
		else{
			$tierRoll = mt_rand(0,100);
			if($tierRoll < 35){
				$tier = 1;
			}
			else if($tierRoll < 55){
				$tier = 2;
			}
			else if($tierRoll < 75){
				$tier = 3;
			}
			else if($tierRoll < 90){
				$tier = 4;
			}
			$sql = "SELECT name,id,rarity,slotType FROM craftingparts WHERE slotType IN ($type) AND weaponType='$weaponType' AND tier='$tier' ORDER BY rarity";
		}
		$result = mysqli_query($conn,$sql);
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
			$totalRoll+= $row['rarity'];
		}
		
		$rollToGoUnder = mt_rand(0, $totalRoll-1);
		$compareRoll = 0;
		foreach($rows as $row){
			$compareRoll += $row['rarity'];
			if($compareRoll > $rollToGoUnder){
				$winningPart = $row;
				break;
			}
		}		
		if ($from == "adventure"){
			echo "You recieved: <a href='index.php?page=view-part&partId=" . $winningPart['id'] . "'>" . $winningPart['name'] . "</a>";
		}
		
		$return = insertPart($winningPart['id'],$winningPart['slotType'],$userCraftId,array("name"=>$winningPart['name'],"type"=>$winningPart['slotType'],"id"=>$winningPart['id']));
		
		return $return;
	}

	function getPartName($id){
	global $conn;
	$sql = "SELECT name FROM craftingparts WHERE id='$id'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	return $row['name'];
	}

	
	
	function insertPart($id,$type,$userCraftId,$partInfo){
		global $conn;

		$armourBases = array('Cmain','Hmain','Amain','Lmain');
		if(in_array($type,$armourBases)){
			$type = "base";
		}
		
		$partsString = getPartInventory($type,$userCraftId);
		if($partsString != ""){
			$partsArr = explode(",",$partsString);
			$found = 0;
			for($i = 0; $i < count($partsArr);$i++){
				$ex = explode(":",$partsArr[$i]);
				if($id == $ex[0]){
					$amount = $ex[1] + 1;
					$partsArr[$i] = $ex[0] . ":" . $amount;
					$found = 1;
					break;
				}
			}
			if($found == 1){
				$insert = implode(",",$partsArr);
			}
			else{
				$insert = $partsString . "," . $id . ":1";
			}
		}
		else{
			$insert = $id . ":1";
		}
		if(isset($insert)){
			if($userCraftId == "none"){
				$craftId = $_SESSION['characterProperties']['crafting_id'];
				$sql = "UPDATE craftinginventory SET $type='$insert' WHERE id='$craftId'";
			}
			else{
				$sql = "UPDATE craftinginventory SET $type='$insert' WHERE id='$userCraftId'";
			}
			if($conn->query($sql) === TRUE){
				
			}
			else{
				return "Something went wrong, couldn't insert part to DB";
			}
		}
		else{
			return "Something went wrong, couldn't find part ID... probably";
		}
		if($userCraftId != "none"){
			return " is also rewarded with: <a href='index.php?page=view-part&partId=" . $partInfo['id'] . "'>" . $partInfo['name'] . "</a>";
		}
		
	}
	
	function verifyCraft(){
		global $conn;
		//Verify amount of mods to slots
		
		$craftPartsArr = $_SESSION['currentCraft'];		
		
		$partInventory = getPartInventoryAll();
		
		if ($partInventory['base'] != ""){
			$baseInventoryArr = explode(",",$partInventory['base']);
		}
		if ($partInventory['main'] != ""){
			$mainInventoryArr = explode(",",$partInventory['main']);
		}
		if ($partInventory['extra'] != ""){
			$extraInventoryArr = explode(",",$partInventory['extra']);
		}

		$baseInventoryIds = array();
		$baseInventoryAmount = array();
		$mainInventoryIds = array();
		$mainInventoryAmount = array();
		$extraInventoryIds = array();
		$extraInventoryAmount = array();
		
		if(is_array($baseInventoryArr)){
			foreach ($baseInventoryArr as $base){
				$ex = explode(":",$base);
				$baseInventoryIds[] = $ex[0];
				$baseInventoryAmount[] = $ex[1];
			}
		}
		if(is_array($mainInventoryArr)){
			foreach ($mainInventoryArr as $main){
				$ex = explode(":",$main);
				$mainInventoryIds[] = $ex[0];
				$mainInventoryAmount[] = $ex[1];
			}
		}
		if(is_array($extraInventoryArr)){
			foreach ($extraInventoryArr as $extra){
				$ex = explode(":",$extra);
				$extraInventoryIds[] = $ex[0];
				$extraInventoryAmount[] = $ex[1];
			}
		}
		
		$baseChange = 0;
		$mainChange = 0;
		$extraChange = 0;
		
		
		$basePart = getPart($craftPartsArr['base']);
	
		$inventoryError = 0;
		$foundPartArray = array();
		
		$stats = array();
		for($i = 0; $i < count($baseInventoryIds);$i++){
			if($basePart['id'] == $baseInventoryIds[$i]){
				$foundPartArray[] = $baseInventoryIds[$i];
				$baseChange = 1;
				$stats = addStats($stats,$basePart);
				$baseInventoryAmount[$i]--;
				if($baseInventoryAmount[$i] >= 0){
					//FOR EACH MAIN
					for($x = 0; $x < $basePart['slots'];$x++){
						if(isset($craftPartsArr['main'])){
							$mainPart = getPart($craftPartsArr['main'][$x]['id']);
							$stats = addStats($stats,$mainPart);
							//CHECK VS INVENTORY ARRAY
							for($y = 0; $y < count($mainInventoryIds);$y++){
								if($mainInventoryIds[$y] == $craftPartsArr['main'][$x]['id']){
									$foundPartArray[] = $mainInventoryIds[$y];
									$mainChange = 1;
									$mainInventoryAmount[$y]--;
									if($mainInventoryAmount[$y] < 0){
										echo "Crafting failed, missing nessesary main resources, try again";
										exit;
									}
									if($mainInventoryAmount >= 0){
										//CHECK EACH EXTRA
										for($z = 0; $z < $mainPart['slots']; $z++){
											for($q = 0; $q < count($extraInventoryIds);$q++){
												#var_dump($extraInventoryIds);
												if($extraInventoryIds[$q] == @$craftPartsArr['main'][$x][$z]){
													$extraPart = getPart($craftPartsArr['main'][$x][$z]);
													$stats = addStats($stats,$extraPart);
													$foundPartArray[] = $extraInventoryIds[$q];
													$extraChange = 1;
													$extraInventoryAmount[$q]--;
													if($extraInventoryAmount[$q] < 0){
														echo "Crafting failed, missing nessesary main resources, try again";
														exit;
													}
												}
											}
										}
									}
									else{
										echo "Crafting failed, missing nessesary main resources, try again";
										exit;
									}
								}
							}
						}
					}
				}
				else{
					echo "Crafting failed, missing nessesary base resources, try again";
					exit;
				}
			}
		}
		
		$sql = "UPDATE craftinginventory SET";
		if($baseChange){
			$notNull = 0;
			$baseInsert = array();
			for($i = 0;$i < count($baseInventoryIds);$i++){
				if($baseInventoryAmount[$i] != 0){
					$baseInsert[] = $baseInventoryIds[$i] . ":" . $baseInventoryAmount[$i];
					$notNull = 1;
				}
			}
			if($notNull == 1){
				$baseInsert = implode(",",$baseInsert);
				$sql .= " base='$baseInsert',";
			}
			else{
				$sql .= " base=NULL,";
			}
		}
		if($mainChange){
			$notNull = 0;
			$mainInsert = array();
			for($i = 0;$i < count($mainInventoryIds);$i++){
				if($mainInventoryAmount[$i] != 0){
					$mainInsert[] = $mainInventoryIds[$i] . ":" . $mainInventoryAmount[$i];
					$notNull = 1;
				}
			}
			if($notNull == 1){
				$mainInsert = implode(",",$mainInsert);
				$sql .= " main='$mainInsert',";
			}
			else{
				$sql .= " main=NULL,";
			}
		}
		if($extraChange){
			$notNull = 0;
			$extraInsert = array();
			for($i = 0;$i < count($extraInventoryIds);$i++){
				if($extraInventoryAmount[$i] != 0){
					$extraInsert[] = $extraInventoryIds[$i] . ":" . $extraInventoryAmount[$i];
					$notNull = 1;
				}
			}
			if($notNull == 1){
				$extraInsert = implode(",",$extraInsert);
				$sql .= " extra='$extraInsert',";
			}
			else{
				$sql .= " extra=NULL,";
			}
		}
		$sql = substr($sql,0,-1);
		updatePartInventory($sql);
		
		$itemString = implode(",",$foundPartArray);
		insertItemToDB($itemString,$basePart['type'],$basePart['weaponType'],$stats,$basePart['slotType']);
	}
	
	function updatePartInventory($sql){
		global $conn;
		
		$craftId = $_SESSION['characterProperties']['crafting_id'];
		$sql .= " WHERE id='$craftId'";
		mysqli_query($conn,$sql);
		
	}
	
	function insertItemToDB($parts,$type,$weaponType,$stats,$armourType){
		global $conn;
		$userID = $_SESSION['characterProperties']['id'];
		if($weaponType == "melee"){
			$onehand = array("axes","clubs","daggers","hammers","swords");
			$twohand = array("battleaxes","greatswords","large clubs","spears");
			
			if($type == "1h"){
				$item_type = $onehand[mt_rand(0,count($onehand)-1)];
			}
			elseif($type == "2h"){
				$item_type = $twohand[mt_rand(0,count($twohand)-1)];
			}
			else{
				echo "Error - unknown weapon type on base";
				exit;
			}
			$name = "";
			if(mt_rand(0,100) > 50){
				$name .= "The ";
			}
			if(mt_rand(0,100) > 50){
				$arr = array("Sharp","Dull","Bloody","Deadly","Dumb","Piercing","Soothing","Bashing","Cutting","Wicked","Crazed","Great","Miniature");
				
				$name .= $arr[mt_rand(0,count($arr)-1)] . " ";
			}
			$arr = array("Oathkeeper","Brightroar","Longclaw","Marvinbane","Tyrfing","Deathspade","Chieftain","Crescent","Blacktounge","Answerer","Equalizer","Flamecutter","Eclipse","Mindreaver");
			$name .= $arr[mt_rand(0,count($arr)-1)];

			$skill = $stats['skill'];$minDmg = $stats['minDmg'];$maxDmg = $stats['maxDmg'];$critDmg = $stats['critDmg'];$armourPen = $stats['armourPen'];$chance_hit = $stats['accuracy'];$strReq = $stats['strReq'];
			$sql = "INSERT INTO weapons (item_type,name,type,enchantType,skill,min_dmg,max_dmg,crit_dmg,armourPenetration,chance_hit,price,canParry,strReq,sellable,enchantTier,userCrafted,parts) VALUES (
			'$item_type','$name','$type','melee','$skill',$minDmg,'$maxDmg','$critDmg','$armourPen','$chance_hit',100,'true','$strReq',0,3,'$userID','$parts')";
			mysqli_query($conn,$sql);
			$itemId = mysqli_insert_id($conn);
			if($itemId != null){
				insertToPlayerDB($itemId,$name,"weapons");
			}
		}
		else{
			if($type == "LA"){
				$sqlArmour = "Light Armour";
				$name = "Light";
			}
			else if($type == "HA"){
				$sqlArmour = "Heavy Armour";
				$name = "Heavy";
			}
			else{
				echo "cheating detected, materials destroyed";
				exit;
			}
			if($armourType == "Hmain"){
				$name .= " Helmet";
				$sqlType = "heads";
			}
			else if($armourType == "Amain"){
				$name .= " Armguards";
				$sqlType = "arms";
			}
			else if($armourType == "Lmain"){
				$name .= " Trousers";
				$sqlType = "legs";
			}
			else if($armourType == "Cmain"){
				$name .= " Chest";
				$sqlType = "chests";
			}

			(int)$skill = $stats['skill'];
			(int)$damageReduction = $stats['Damage Reduction'];
			(float)$minDmg = $stats['minDmg'];
			(float)$maxDmg = $stats['maxDmg'];
			(int)$strReq = $stats['strReq'];
			(int)$critDmg = $stats['critDmg'];
			(int)$oneSkill = $stats['1hSkill'];
			(int)$twoSkill = $stats['2hSkill'];
			(int)$dodge = $stats['dodge'];
			(int)$strength = $stats['Strength'];
			(int)$dexterity = $stats['Dexterity'];
			(int)$vitality = $stats['Vitality'];
			(int)$bow = $stats['Bow'];
			(int)$crossbow = $stats['Crossbow'];
			(int)$initiative = $stats['Initiative'];
			(int)$finesse = $stats['Finesse'];
			(int)$lightArmour = $stats['Light Armour'];
			(int)$heavyArmour = $stats['Heavy Armour'];
			(int)$shield = $stats['Shield'];
			(int)$parry = $stats['Parry'];
			(int)$foulPlay = $stats['Foul Play'];
			(int)$weight = $stats['Weight'];
 
			$description = "User crafted armour";


			$sql = "INSERT INTO armours (item_type,name,type,enchantType,skill,damage_reduction,min_Dmg,max_Dmg,crit_Dmg,price,strReq,sellable,enchantTier,userCrafted,parts,1hSkill,2hSkill,dodge,strength,dexterity,vitality,bow,crossbow,initiative,finesse,lightArmour,heavyArmour,shield,parry,foulPlay,weight) VALUES (
			'$sqlType','$name','$sqlArmour','armour','$skill','$damageReduction',$minDmg,'$maxDmg','$critDmg',100,'$strReq',0,2,true,'$parts','$oneSkill','$twoSkill','$dodge','$strength','$dexterity','$vitality','$bow','$crossbow','$initiative','$finesse','$lightArmour','$heavyArmour','$shield','$parry','$foulPlay','$weight')";

			mysqli_query($conn,$sql);
			$itemId = mysqli_insert_id($conn);
			if($itemId != null){
				insertToPlayerDB($itemId,$name,$sqlType);
			}
			

		}
		
		
	}
	
	function insertToPlayerDB($itemId,$name,$itemType){
		global $conn;
		$invId = $_SESSION['characterProperties']['inventory_id'];
		$sql = "SELECT * FROM inventory WHERE iid='$invId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$insert = $row[$itemType] . $itemId . ":1;1,";
		$sql = "UPDATE inventory SET $itemType='$insert' WHERE iid='$invId'";

		mysqli_query($conn,$sql);
		echo "<div id='weaponOutput' style='text-align:center;font-size:1px;margin-top:20px'>";
		echo "Successfully crafted<br>" . $name . "!";
		echo "</div>";
		
	}

	function getPart($id){
		global $conn;
		
		$sql = "SELECT * FROM craftingparts WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		return $row;
	}

	function removeParts($id,$type,$amount){
		global $conn;
		$partsString = getPartInventory($type);
		if($partsString != ""){
			$partsArr = explode(",",$partsString);
			$found = 0;
			for($i = 0; $i < count($partsArr);$i++){
				
			}
		}
	}
	function getPartInventoryAll($userCraftId = "none"){
		global $conn;
		if($userCraftId == "none"){
			$craftId = $_SESSION['characterProperties']['crafting_id'];
		}
		else{
			$craftId = $userCraftId;
		}
		$sql = "SELECT * FROM craftinginventory WHERE id='$craftId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	
	function getPartInventory($type,$userCraftId = "none"){
		global $conn;
		if($userCraftId == "none"){
			$craftId = $_SESSION['characterProperties']['crafting_id'];
		}
		else{
			$craftId = $userCraftId;
		}
		$sql = "SELECT $type FROM craftinginventory WHERE id='$craftId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		return $row[$type];
	}
	
	function listAllBases(){
		$_SESSION['currentCraft'] = array();
		global $conn;
		echo "<div id='usablePartsContents' class='usablePartsContents'>";
		$craftId = $_SESSION['characterProperties']['crafting_id'];
		$sql = "SELECT base FROM craftinginventory WHERE id='$craftId'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			$allBases = array();
			$row = mysqli_fetch_assoc($result);
			if($row['base'] != ""){
				$rows = explode(",",$row['base']);
				
				foreach($rows as $row){
					$part = explode(":",$row);
					$partId = $part[0];
					$partAmount = $part[1];
					$sql = "SELECT * FROM craftingparts WHERE id='$partId'";
					$result = mysqli_query($conn,$sql);
					$row2 = mysqli_fetch_assoc($result);
					if($row2['weaponType'] == "melee"){
						$row2['amount'] = $partAmount;
						$allBases[] = $row2;
					}
				}
				usort($allBases, 'sortByName');
					foreach($allBases as $base){
						echo "<div class='craftItem craftItemWeapon' id='". $base['id'] . "'>" . $base['name'] . " x " . $base['amount'] . "
						</div><div class='partInfo' id='partInfo" . $base['id'] . "'></div>";
					}
		
				echo "<script>
					$('.craftItemWeapon').click(function(){
						var id = $(this).attr('id');
						var target = $('#partInfo'+id);
						target.load('index.php?crpage=craftingFunctions&nonUI&listBase='+id,function(){
							target.slideToggle(300);
						});
					});
				</script>";
				
			}
			else{
				echo "You don't have any base parts";
			}
		}
		else{
			echo "You don't have any base parts";
		}
		echo "</div>";
	}

	function sortByName($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	function listAllArmourBases(){
		$_SESSION['currentCraft'] = array();
		global $conn;
		echo "<div id='usablePartsContents' class='usablePartsContents'>";
		$craftId = $_SESSION['characterProperties']['crafting_id'];
		$sql = "SELECT base FROM craftinginventory WHERE id='$craftId'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			$allLightBases = array();
			$allHeavyBases = array();
			$row = mysqli_fetch_assoc($result);
			if($row['base'] != ""){
				$rows = explode(",",$row['base']);
				
				foreach($rows as $row){
					$part = explode(":",$row);
					$partId = $part[0];
					$partAmount = $part[1];
					$sql = "SELECT * FROM craftingparts WHERE id='$partId'";
					$result = mysqli_query($conn,$sql);
					$row2 = mysqli_fetch_assoc($result);
					if($row2['weaponType'] == "armour"){
						$row2['amount'] = $partAmount;
						if($row2['type'] == "LA"){
							$allLightBases[] = $row2;
						}
						else{
							$allHeavyBases[] = $row2;
						}
					}
				}
				usort($allLightBases, 'sortByName');
				usort($allHeavyBases, 'sortByName');
				echo "<h3 style='text-align:center;'>Light Armour</h3>";
				foreach($allLightBases as $base){
					echo "<div class='craftItem craftItemArmour' id='". $base['id'] . "'>" . $base['name'] . " x " . $base['amount'] . "
					</div><div class='partInfo' id='partInfo" . $base['id'] . "'></div>";
				}
				echo "<h3 style='text-align:center;'>Heavy Armour</h3>";
					foreach($allHeavyBases as $base){
						echo "<div class='craftItem craftItemArmour' id='". $base['id'] . "'>" . $base['name'] . " x " . $base['amount'] . "
						</div><div class='partInfo' id='partInfo" . $base['id'] . "'></div>";
					}
		
				echo "<script>
					$('.craftItemArmour').click(function(){
						var id = $(this).attr('id');
						var target = $('#partInfo'+id);
						target.load('index.php?crpage=craftingFunctions&nonUI&listBaseArmour='+id,function(){
							target.slideToggle(300);
						});
					});
				</script>";
				
			}
			else{
				echo "<h3 style='text-align:center;'>Armour</h3>";
				echo "You don't have any base parts";
			}
		}
		else{
			echo "<h3 style='text-align:center;'>Armour</h3>";
			echo "You don't have any base parts";
		}
		echo "</div>";
	}
	
	function listAllMains($armour = false){
		global $conn;
		
		if(isset($_SESSION['currentCraft']['base'])){
			echo "<div id='usablePartsContents' class='usablePartsContents'>";
			echo "<h4>Main Parts</h4>";
			$craftId = $_SESSION['characterProperties']['crafting_id'];
			$sql = "SELECT main FROM craftinginventory WHERE id='$craftId'";
			$result = mysqli_query($conn,$sql);
			$allBases = array();
			$row = mysqli_fetch_assoc($result);
			if(isset($row['main'])){
				$rows = explode(",",$row['main']);
				
				$mainSlots = getPart($_SESSION['currentCraft']['base']);
				$mainSlots = $mainSlots['slots'];
				
				foreach($rows as $row){
					$part = explode(":",$row);
					$partId = $part[0];
					$partAmount = $part[1];
					//remove already used parts
					for($i = 0; $i < $mainSlots; $i++){
						if(isset($_SESSION['currentCraft']['main'][$i]['id'])){
							if($partId == $_SESSION['currentCraft']['main'][$i]['id']){
								$partAmount--;
								if($partAmount == 0){
									break;
								}
							}
						}
						else{
							break;
						}
					}
					$sql = "SELECT * FROM craftingparts WHERE id='$partId'";
					$result = mysqli_query($conn,$sql);
					$row2 = mysqli_fetch_assoc($result);
					if($row2['weaponType'] == "armour" && $armour){
						$row2['amount'] = $partAmount;
						$allBases[] = $row2;
					}
					else if ($row2['weaponType'] == "melee" && !$armour){
						$row2['amount'] = $partAmount;
						$allBases[] = $row2;
					}
				}
				usort($allBases, 'sortByName');
					foreach($allBases as $base){
						if($base['amount'] > 0){
							echo "<div class='craftItem' id='". $base['id'] . "'>" . $base['name'] . " x " . $base['amount'] . "
							</div><div class='partInfo' id='partInfo" . $base['id'] . "'></div>";
						}
					}
				if($armour){
					echo "<script>
						$('.craftItem').click(function(){
							var id = $(this).attr('id');
							var target = $('#partInfo'+id);
							target.load('index.php?crpage=craftingFunctions&nonUI&listMain='+id+'&armour',function(){
								target.slideToggle(300);
							});
						});
					</script>";
				}
				else{
					echo "<script>
						$('.craftItem').click(function(){
							var id = $(this).attr('id');
							var target = $('#partInfo'+id);
							target.load('index.php?crpage=craftingFunctions&nonUI&listMain='+id,function(){
								target.slideToggle(300);
							});
						});
					</script>";
				}
				echo "</div>";
			}
			else{
				echo "You don't have any main parts";
			}
		}
		else{
			echo "Error - no build in progress";
		}
	}
	
	function listAllExtras($armour = false){
		global $conn;
		
		if(isset($_SESSION['currentCraft']['base'])){
			echo "<div id='usablePartsContents' class='usablePartsContents'>";
			echo "<h4>Extra Parts</h4>";
			$craftId = $_SESSION['characterProperties']['crafting_id'];
			$sql = "SELECT extra FROM craftinginventory WHERE id='$craftId'";
			$result = mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) > 0){
				$allBases = array();
				$row = mysqli_fetch_assoc($result);
				$rows = explode(",",$row['extra']);
				
				$mainSlots = getPart($_SESSION['currentCraft']['base']);
				$mainSlots = $mainSlots['slots'];
				foreach($rows as $row){
					$part = explode(":",$row);
					$partId = $part[0];
					$partAmount = $part[1];
					//remove already used parts
					for($i = 0; $i < $mainSlots; $i++){
						if(isset($_SESSION['currentCraft']['main'][$i]['id'])){
							$extraSlots = getPart($_SESSION['currentCraft']['main'][$i]['id']);
							$extraSlots = $extraSlots['slots'];
							for($x = 0; $x < $extraSlots; $x++){
								if(isset($_SESSION['currentCraft']['main'][$i][$x])){
									if($partId == $_SESSION['currentCraft']['main'][$i][$x]){
										$partAmount--;
										if($partAmount == 0){
											break;
										}
									}
								}
							}
						}
						else{
							break;
						}
					}
					if($partAmount > 0){
						$sql = "SELECT * FROM craftingparts WHERE id='$partId'";
						$result = mysqli_query($conn,$sql);
						$row2 = mysqli_fetch_assoc($result);
						if($row2['weaponType'] == "armour" && $armour){
							$row2['amount'] = $partAmount;
							$allBases[] = $row2;
						}
						else if ($row2['weaponType'] == "melee" && !$armour){
							$row2['amount'] = $partAmount;
							$allBases[] = $row2;
						}
					}
				}
				usort($allBases, 'sortByName');
					foreach($allBases as $base){
						echo "<div class='craftItemExtra' id='". $base['id'] . "'>" . $base['name'] . " x " . $base['amount'] . "
						</div><div class='partInfo' id='partInfo" . $base['id'] . "'></div>";
					}
	
				echo "<script>
					$('.craftItemExtra').click(function(){
						var id = $(this).attr('id');
						var target = $('#partInfo'+id);
						target.load('index.php?crpage=craftingFunctions&nonUI&listExtra='+id,function(){
							target.slideToggle(300);
						});
					});
				</script>";
				echo "</div>";
			}
			else{
				echo "You don't have any extra parts";
			}
			//add main, maybe
			if(!$armour){			
				$part = getPart($_SESSION['currentCraft']['base']);
				for($i = 0; $i < $part['slots'];$i++){
					if(!isset($_SESSION['currentCraft']['main'][$i]['id'])){
						listAllMains();
					}
				}
			}
			
		}
		else{
			echo "Error - no build in progress";
		}
	}
	
	function addBase($id){
		
		$part = getPart($id);
		$_SESSION['currentCraft']['base'] = $id;
		$_SESSION['currentCraft']['type'] = $part['type'];
	
		listCurrentBuild();
	}
	function addMain($id){
		if(isset($_SESSION['currentCraft']['base'])){
			$space = 0;
			$part = getPart($_SESSION['currentCraft']['base']);
			for($i = 0; $i < $part['slots'];$i++){
				if(!isset($_SESSION['currentCraft']['main'][$i]['id'])){
					$_SESSION['currentCraft']['main'][$i]['id'] = $id;
					$space = 1;
					break;
				}
			}
			if($space == 0){
				echo "Error, no free space in base, try reloading the page";
				exit;
			}
		}
		else{
			echo "Error, no base chosen, try reloading the page";
			exit;
		}
		listCurrentBuild();
	}
	function addExtra($id){
		if(isset($_SESSION['currentCraft']['base'])){
			$space = 0;
			$part = getPart($_SESSION['currentCraft']['base']);
			for($i = 0; $i < $part['slots'];$i++){
				if(isset($_SESSION['currentCraft']['main'][$i]['id'])){
					$mainPart = getPart($_SESSION['currentCraft']['main'][$i]['id']);
					for($x = 0; $x < $mainPart['slots'];$x++){
						if(!isset($_SESSION['currentCraft']['main'][$i][$x])){
							$_SESSION['currentCraft']['main'][$i][$x] = $id;
							#echo "Added";
							#var_dump($_SESSION['currentCraft']);
							$space = 1;
							break;
						}
					}
				}
				if($space == 1){
					break;
				}
			}
			if($space == 0){
				echo "Error, no free space in base, try reloading the page";
				exit;
			}
		}
		else{
			echo "Error, no base chosen, try reloading the page";
			exit;
		}
		listCurrentBuild();
	}
	
	function listBaseInformation($id){
		global $conn;
			$part = getPart($id);
			$i = 0;
			foreach ($part as $key => $value){
				if($i > 3){
					if($value != 0){
						echo "<div class='partInfoName listPart'>" . $key . "</div>";
						echo "<div class='partInfoStat listPart'>" . $value ."</div>";
					}
				}
				$i++;
			}
			echo "<div id='" . $part['id'] . "' class='craftButton craftBase" . $part['id'] . "'>Use this base</div>";
			
			echo "<script>
					$('.craftBase" . $part['id'] . "').click(function(){
						var id = $(this).attr('id');
						
						$('#itemOutput').load('index.php?crpage=craftingFunctions&nonUI&addBase='+id,function(){
							$('#itemOutput').fadeIn(300);
							$('#usablePartsContents').fadeOut(300,function(){
								$('.usablePartsContents').empty();
								$('.craftHeaders').empty();
								$('#usablePartsContents').load('index.php?crpage=craftingFunctions&nonUI&listAllMain',function(){
									$('#usablePartsContents').fadeIn(300);
								});
							});
						});						
					});
				</script>";
		
	}
	function listBaseArmourInformation($id){
		global $conn;
			$part = getPart($id);
			$i = 0;
			foreach ($part as $key => $value){
				if($i > 3){
					if($value != 0){
						echo "<div class='partInfoName listPart'>" . $key . "</div>";
						echo "<div class='partInfoStat listPart'>" . $value ."</div>";
					}
				}
				$i++;
			}
			echo "<div id='" . $part['id'] . "' class='craftButton craftBase" . $part['id'] . "'>Use this base</div>";
			
			echo "<script>
					$('.craftBase" . $part['id'] . "').click(function(){
						var id = $(this).attr('id');
						
						$('#itemOutput').load('index.php?crpage=craftingFunctions&nonUI&addBase='+id,function(){
							$('#itemOutput').fadeIn(300);
							$('#usablePartsContents').fadeOut(300,function(){
								$('.usablePartsContents').empty();
								$('.craftHeaders').empty();
								$('#usablePartsContents').load('index.php?crpage=craftingFunctions&nonUI&listAllArmourExtras',function(){
									$('#usablePartsContents').fadeIn(300);
								});
							});
						});						
					});
				</script>";
		
	}
	function listMainInformation($id,$armour = false){
		global $conn;
			$part = getPart($id);
			$i = 0;
			foreach ($part as $key => $value){
				if($i > 3){
					if($value != 0){
						echo "<div class='partInfoName listPart'>" . $key . "</div>";
						echo "<div class='partInfoStat listPart'>" . $value ."</div>";
					}
				}
				$i++;
			}
			echo "<div id='" . $part['id'] . "' class='craftButton craftMain" . $part['id'] . "'>Use this main</div>";
			if($armour){
			echo "<script>
					$('.craftMain" . $part['id'] . "').click(function(){
						var id = $(this).attr('id');
						$('#usablePartsContents').fadeOut(300,function(){
							$('.usablePartsContents').empty();
							$('.craftHeaders').empty();
							$('#usablePartsContents').load('index.php?crpage=craftingFunctions&nonUI&listAllArmourExtras',function(){
								$('#usablePartsContents').fadeIn(300);
							});
						});
						$('#itemOutput').load('index.php?crpage=craftingFunctions&nonUI&addMain='+id,function(){
							$('#itemOutput').fadeIn(300);
						});
					});
				</script>";
			}
			else{
				echo "<script>
					$('.craftMain" . $part['id'] . "').click(function(){
						var id = $(this).attr('id');
						$('#usablePartsContents').fadeOut(300,function(){
							$('#usablePartsContents').empty();
							$('#usablePartsContents').load('index.php?crpage=craftingFunctions&nonUI&listAllExtras',function(){
								$('#usablePartsContents').fadeIn(300);
							});
						});
						$('#itemOutput').load('index.php?crpage=craftingFunctions&nonUI&addMain='+id,function(){
							$('#itemOutput').fadeIn(300);
						});
					});
				</script>";
			}
	}
	function listExtraInformation($id){
		global $conn;
			$part = getPart($id);
			$i = 0;
			foreach ($part as $key => $value){
				if($i > 3){
					if($value != 0){
						echo "<div class='partInfoName listPart'>" . $key . "</div>";
						echo "<div class='partInfoStat listPart'>" . $value ."</div>";
					}
				}
				$i++;
			}
			echo "<div id='" . $part['id'] . "' class='craftButton craftExtra" . $part['id'] . "'>Use this extra</div>";
			
			echo "<script>
					$('.craftExtra" . $part['id'] . "').click(function(){
						var id = $(this).attr('id');
						$('#usablePartsContents').fadeOut(300,function(){
							$('#usablePartsContents').empty();
							$('#usablePartsContents').load('index.php?crpage=craftingFunctions&nonUI&listAllExtras',function(){
								$('#usablePartsContents').fadeIn(300);
							});
						});
						$('#itemOutput').load('index.php?crpage=craftingFunctions&nonUI&addExtra='+id,function(){
							$('#itemOutput').fadeIn(300);
						});
					});
				</script>";
	}
	
	
	function addStats($stats,$part){
		$dontCopy = array("id","name","type","slotType","weaponType","slots","rarity","tier");
		foreach($part as $key=>$value) {
			if(!in_array($key,$dontCopy)){
				if(isset($stats[$key])){
					$stats[$key] += $value;
				}
				else{
					$stats[$key] = $value;
				}
				
			}
		}

		return $stats;
	}
	
	function listCurrentBuild(){
		$buildArr = $_SESSION['currentCraft'];
		$stats = array();
		$part = getPart($buildArr['base']);
		$stats = addStats($stats,$part);
		echo "<ul>";
		echo "<li><h4>" . $part['name'] . "</h4></li><ul>";
		$finished = 0;
		for($i = 0; $i < $part['slots'];$i++){
			$finished = 1;
			if (isset($buildArr['main'][$i])){
				$mainPart = getPart($buildArr['main'][$i]['id']);
				$stats = addStats($stats,$mainPart);
				echo "<li><h4>" . $mainPart['name'] . "</h4></li><ul>";
				for($x = 0; $x < $mainPart['slots'];$x++){
					if(isset($buildArr['main'][$i][$x])){
						$extraPart = getPart($buildArr['main'][$i][$x]);
						$stats = addStats($stats,$extraPart);
						echo "<li><h4>" . $extraPart['name'] . "</h4></li>";
					}
					else{
						echo "<li><h4>Empty extra slot</h4></li>";
					}
				}
				echo "</ul>";
			}
			else{
				echo "<li><h4>Empty main slot</h4></li>";
			}
		}
		echo "</ul></ul>";
		
		echo "<h3>Stats</h3>";
		
		if($part['weaponType'] == "melee"){
			echo "weapon type: " . $_SESSION['currentCraft']['type'] . "<br>";
			foreach($stats as $key => $value){
				if($value != 0 || $key == "minDmg" || $key == "maxDmg"){
					echo $key . " -> " . $value . "<br>";
				}
			}
			if($finished == 1){
				if($stats['minDmg'] >= 0){
					echo "<div id='craftItem'>Craft weapon! (Materials will be destroyed)</div>";
						echo "<script>
							$('#craftItem').click(function(){
								$('#mainPage').fadeOut(300,function(){
									$('#mainPage').load('index.php?crpage=craftingFunctions&nonUI&craftItem',function(){
										$('#weaponOutput').css('font.size','1px');
										$('#mainPage').show();
										$('#weaponOutput').animate({
											fontSize: '40px'
										},500);
										
									});
								});
							});
						</script>";
				}
				else{
					echo "<div id='craftItem'>Cannot craft a weapon with negative damage</div>";
				}
				
			}
		}
		else{
			$subTypes = array("Hmain"=>"Head","Amain"=>"Arm","Lmain"=>"Legs","Cmain"=>"Chest");
			$type = "Heavy Armour - " . $subTypes[getPart($_SESSION['currentCraft']['base'])["slotType"]];
			if($_SESSION['currentCraft']['type'] == "LA"){
				$type = "Light Armour - " . $subTypes[getPart($_SESSION['currentCraft']['base'])["slotType"]];
			}
			echo "armour type: " . $type . "<br>";
			foreach($stats as $key => $value){
				if($value != 0){
					echo $key . " -> " . $value . "<br>";
				}
			}
			if($finished == 1){
				//if($stats['minDmg'] > 0){
					echo "<div id='craftItem'>Craft Armour! (Materials will be destroyed)</div>";
						echo "<script>
							$('#craftItem').click(function(){
								$('#mainPage').fadeOut(300,function(){
									$('#mainPage').load('index.php?crpage=craftingFunctions&nonUI&craftItem',function(){
										$('#weaponOutput').css('font.size','1px');
										$('#mainPage').show();
										$('#weaponOutput').animate({
											fontSize: '40px'
										},500);
										
									});
								});
							});
						</script>";
				/*}
				else{
					echo "<div id='craftItem'>Cannot craft a weapon with negative damage</div>";
				}
				*/
			}
		}
	}
	
	if(isset($_GET['addBase'])){
		addBase((int)$_GET['addBase']);
	}
	elseif(isset($_GET['listBase'])){
		listBaseInformation($_GET['listBase']);
	}
	elseif(isset($_GET['listBaseArmour'])){
		listBaseArmourInformation($_GET['listBaseArmour']);
	}
	elseif(isset($_GET['listAllArmourExtras'])){
		listAllMains(true);
	}
	elseif(isset($_GET['listMain'])){
		if(isset($_GET['armour'])){
			listMainInformation($_GET['listMain'],true);
		}
		else{
			listMainInformation($_GET['listMain']);
		}	
	}
	elseif(isset($_GET['listExtra'])){
		listExtraInformation($_GET['listExtra']);
	}
	elseif(isset($_GET['listAllMain'])){
		listAllMains();
	}
	elseif(isset($_GET['addMain'])){
		addMain($_GET['addMain']);
	}
	elseif(isset($_GET['listAllExtras'])){
		listAllExtras();
	}
	elseif(isset($_GET['addExtra'])){
		addExtra($_GET['addExtra']);
	}
	elseif(isset($_GET['craftItem'])){
		verifyCraft();
	}


	if(isset($_GET['fetchItems'])){
		switch($_GET['fetchItems']){
			case "1": 
				$type = "melee";
				break;
			case "2": 
				$type = "ranged";
				break;
			case "3": 
				$type = "armours";
				break;
			case "4": 
				$type = "parts";
				break;
		}
		if(isset($type)){
			echo "<div id='itemInfo'></div>";
			getInventory($type, $_SESSION['characterProperties']['id']);
			include_once("frontend/design/js/npcinfo.html");
		}
	}

?>