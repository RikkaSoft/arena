<?php 
	global $conn,$priceModifier;
	$sql = "SELECT * FROM modifiers";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	global $weaponMod,$armourPercent;
	$weaponMod = $row['weaponSkillDivider'];
	$armourPercent = $row['armourPercent'];
    
    if(!isset($_SESSION['characterProperties']['name'])){
        $_SESSION['characterProperties']['one_handed'] = 0;
        $_SESSION['characterProperties']['two_handed'] = 0;
        $_SESSION['characterProperties']['bow'] = 0;
        $_SESSION['characterProperties']['crossbow'] = 0;
        $_SESSION['characterProperties']['shield'] = 0;
        $_SESSION['characterProperties']['strength'] = 0;
        $_SESSION['characterProperties']['light_armour'] = 0;
        $_SESSION['characterProperties']['heavy_armour'] = 0;
		$guildId = 0;
    }
		
	
	$priceModifier = 1;
	if(!isset($guildId)){
		$guildId = $_SESSION['characterProperties']['guild'];
	}
	if($guildId != 0){
		include_once(__ROOT__.'/backend/guild/guildFunctions.php');
			$returnArray = getGuildPerks($_SESSION['characterProperties']['guild']);
			$priceModifier = $priceModifier - $returnArray['discount'];
	}

	function selectVendorParts(){
		$partTypes = array("base","main","extra");
		getRandomPartVendor($partTypes[0],$_SESSION['characterProperties']['id']);
		getRandomPartVendor($partTypes[1],$_SESSION['characterProperties']['id']);
		for ($ii=0; $ii < 3; $ii++) { 
			getRandomPartVendor($partTypes[2],$_SESSION['characterProperties']['id']);
		}
	}

	function getParts(){
		global $conn,$priceModifier;
		include_once(__ROOT__.'/backend/crafting/craftingFunctions.php');
		$charId = $_SESSION['characterProperties']['id'];
		$sql = "SELECT craftingparts.*,craftingpartssale.* FROM craftingpartssale INNER JOIN craftingparts ON craftingpartssale.partID = craftingparts.id WHERE characterId='$charId'";
		$result = mysqli_query($conn,$sql);
		$allSold = true;
		if(mysqli_num_rows($result) == 0){
			#Create new parts
			selectVendorParts();
			$sql = "SELECT craftingparts.*,craftingpartssale.* FROM craftingpartssale INNER JOIN craftingparts ON craftingpartssale.partID = craftingparts.id WHERE characterId='$charId'";
			$result = mysqli_query($conn,$sql);
		}
		$all = array();
		while($row = mysqli_fetch_assoc($result)){
			if($row['sold'] == 0){
				$all[$row['slotType']][] = $row;
				$allSold = false;
			}
		}
		if($allSold){
			echo "You've bought all available parts, check back tomorrow";
		}
		else{
			if(isset($all['base'])){
				echo '<li>
					<label for="base-toggle"><img src="frontend/design/images/bullets/base.png"> Base</label>
					<input type="checkbox" id="base-toggle"/>
					<ul id="base">';
					foreach($all['base'] as $row){
						pastePart($row);
					}
				echo '</ul>
				</li>';
			}
			if(isset($all['main'])){
				echo '<li>
					<label for="main-toggle"><img src="frontend/design/images/bullets/main.png"> Main</label>
					<input type="checkbox" id="main-toggle"/>
					<ul id="main">';
					foreach($all['main'] as $row){
						pastePart($row);
					}
				echo '</ul>
				</li>';
			}
			if(isset($all['extra'])){
				echo '<li>
					<label for="extra-toggle"><img src="frontend/design/images/bullets/extra.png"> Extra</label>
					<input type="checkbox" id="extra-toggle"/>
					<ul id="extra">';
					foreach($all['extra'] as $row){
						pastePart($row);
					}
				echo '</ul>
				</li>';
			}
		}
	}

	function pastePart($row){
		global $priceModifier;
		$price = round(((50-$row['rarity'])*$row['tier'])*$priceModifier);
		$part = getPart($row['partId']);
		$effects = "";
		$i = 0;
		foreach ($part as $key => $value){
			if($i > 3){
				if($value > 0){
					$effects .= "<div class=partInfoName listPart>" . $key . "</div>";
					$effects .= "<div class=partInfoStat listPart>" . $value ."</div>";
				}
			}
			$i++;
		}
		$information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: " . $row['type'] . " part<br><br><strong>Effects: </strong>" . $effects . "<br><br>Price: " . $price . "g"  . "</div>";
		if (isset($row['picture'])){
			$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
		}
		else{
			$information .= "<div id=storePicture></div>";
		}
		
		echo "<li id=" . $row['saleId'] . " onclick=\"itemOutput(" . "'4'" . "," . "'11'" . ",'" . $row['partId'] . "'," . "'$information'" . ")\" id=\"" . "parts" . $row['partId'] . "\"" . "	
		class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $row['name'] . " (" . $price . ")</a></li>";
	}

	function getTrinkets(){
		global $conn,$priceModifier;
		
		$sql = "SELECT * FROM trinkets WHERE sellable=1 ORDER BY price";
		$result = mysqli_query($conn,$sql);
		
		while($row = mysqli_fetch_assoc($result)){
			$effects = "";
			
			if($row['extraCrit'] > 0){
				$effects .= "<br>" . $row['extraCrit'] . "% Extra critical hit damage (not chance)";	
			}
			if($row['extraStr'] > 0){
				$effects .= "<br>" . $row['extraStr'] . " Extra strength";	
			}
			if($row['extraDex'] > 0){
				$effects .= "<br>" . $row['extraDex'] . " Extra dexterity";	
			}
			if($row['extraVit'] > 0){
				$effects .= "<br>" . $row['extraVit'] . " Extra vitality";	
			}
			if($row['extraRangeAcc'] > 0){
				$effects .= "<br>" . $row['extraRangeAcc'] . "% Extra hit chance with ranged weapons";	
			}
			
			$information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: Trinket<br><br><strong>Effects: </strong>" . $effects . "<br><br>Price: " . round($row['price'] * $priceModifier) . "g"  . "<br><br>Description:<br>" . $row['description'] . "</div>";
			
			if (isset($row['picture'])){
				$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
			}
			else{
				$information .= "<div id=storePicture></div>";
			}
			
			echo "<li onclick=\"itemOutput(" . "'3'" . "," . "'9'" . ",'" . $row['id'] . "'," . "'$information'" . ")\" id=\"" . "trinkets" . $row['id'] . "\"" . "	
			class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $row['name'] . " (" . round($row['price'] * $priceModifier) . ")</a></li>";
			
		}
	}
	
	function getItems($table,$type,$type2){
		global $conn,$weaponMod,$armourPercent,$priceModifier;
        $extras = "";
        if(isset($_SESSION['characterProperties']['equipment_id'])){
			$strength = $_SESSION['characterProperties']['strength'];
			$eid = $_SESSION['characterProperties']['equipment_id'];
			$sql = "SELECT trinket FROM equipment where eid='$eid'";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			$trinketId = $row['trinket'];
			if($trinketId != 1){
				$sql = "SELECT * FROM trinkets WHERE id='$trinketId'";
				$result = mysqli_query($conn,$sql);
				$row = mysqli_fetch_assoc($result);
				if ($row['extraStr'] > 0){
					$strength += $row['extraStr'];
				}
			}
		}
		else{
			$strength = $_SESSION['characterProperties']['strength'];
		}
		
        $enchantEffective = array("80","100","120","140","160");
        
		if ($type2 == "na"){
			$sql = "SELECT * FROM $table WHERE item_type=? AND sellable=1 ORDER BY price";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "s", $type);
		}
		else{
			$sql = "SELECT * FROM $table WHERE item_type=? AND type=? AND sellable=1 ORDER BY price";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "ss", $type,$type2);
		}
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if ($table == "weapons"){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$name = $row['id'];
				$itemSubType = '1';
				if ($row['type'] == "1h"){
					$itemType = "One-handed weapon";
					
					$skillReq = $row['skill'];
					$hitChance = $row['chance_hit'];
					$extraHitChance = round(($_SESSION['characterProperties']['one_handed']-$skillReq)*$weaponMod);
					$totalHit = $row['chance_hit'];
                    
                    if ($extraHitChance >= 0){
                        $pm = "+";
                    }
                    else {
                        $pm = "";
                    }
                    $totalHit += $extraHitChance;
					$yourSkill = "<strong>Your one-handed skill: " . $_SESSION['characterProperties']['one_handed'] . "</strong>";
					$skill = "<a title=\'Modifier based on weapon skill\' class=tooltipRight><span>(" . $pm . $extraHitChance .")</span></a>";
				}
				elseif ($row['type'] == "2h"){
					$itemType = "Two-handed weapon";
					
					$skillReq = $row['skill'];
					$hitChance = $row['chance_hit'];
					$extraHitChance = round(($_SESSION['characterProperties']['two_handed']-$skillReq)*$weaponMod);
                    
					$totalHit = $row['chance_hit'];
                    
                    if ($extraHitChance >= 0){
                        $pm = "+";
                    }
                    else {
                        $pm = "";
                    }
                    $totalHit += $extraHitChance;
                    
					$yourSkill = "<strong>Your two-handed skill: " . $_SESSION['characterProperties']['two_handed'] . "</strong>";
                    $skill = "<a title=\'Modifier based on weapon skill\' class=tooltipRight><span>(" . $pm . $extraHitChance .")</span></a>";
				}
                elseif ($row['type'] == "bow" || $row['type'] == "crossbow"){
                    $itemType = "Secondary Weapon";
                    
                    $skillReq = $row['skill'];
                    $hitChance = $row['chance_hit'] . " - " . $row['chance_hit']*1.4;
                    
                    if ($row['item_type'] == "bows"){
                        $extraHitChance = round(($_SESSION['characterProperties']['bow']-$skillReq)*$weaponMod);
                        $yourSkill = "<strong>Your bow skill: " . $_SESSION['characterProperties']['bow'] . "</strong>";
                    }
                    elseif($row['item_type'] == "crossbows"){
                        $extraHitChance = round(($_SESSION['characterProperties']['crossbow']-$skillReq)*$weaponMod);
                        $yourSkill = "<strong>Your crossbow skill: " . $_SESSION['characterProperties']['crossbow'] . "</strong>";
                    }
                    
                    $totalHit = $row['chance_hit'];
                    
                    if ($extraHitChance >= 0){
                        $pm = "+";
                    }
                    else {
                        $pm = "";
                    }
                    $totalHit += $extraHitChance;
                    
                    $itemSubType = "7";
                    $extras = "<br>Ammunition: " . $row['ammo'] . "<br>";
                    $extras .= "Reload time: " . $row['reloadTime'];
                    $skill = "<a title=\'Modifier based on weapon skill\' class=tooltipRight><span>(" . $pm . $extraHitChance .")</span></a>";
                }
                
                

                
                
				$requirement = "";
				$yourStr = "";
				if ($row['strReq'] > 0){
					if ($strength >= $row['strReq']) {
						$requirement = "<br><a style=color:green>Strength required: " . $row['strReq'] . "</a><br>";
					}
					else{
						$requirement = "<br><a style=color:red>Strength required: " . $row['strReq'] . "</a><br>";
						
					}
					$yourStr = "<strong>Your Strength: " . $strength . "</strong><br>";
				}
				##FIX
				if($row['type'] != "shield"){
    				if (!isset($_SESSION['characterProperties']['weight'])){
    				    $_SESSION['characterProperties']['weight'] = 0;
    				}
    				if ($_SESSION['characterProperties']['weight'] != 0){
    				    if ($itemType == "Secondary Weapon"){
    				        $weight =  "<a title=\'Modifier based on weight\' class=tooltipRight><span>(-" . round($_SESSION['characterProperties']['weight']/2) . ")</span></a>";
                            $totalHit -= round($_SESSION['characterProperties']['weight']/2);
                            $totalHit = $totalHit . " - " . round($totalHit*1.4);
                        }
                        else{
                            $weight =  "<a title=\'Modifier based on weight\' class=tooltipRight><span>(-" . $_SESSION['characterProperties']['weight'] . ")</span></a>";
                            $totalHit -= $_SESSION['characterProperties']['weight'];
                        }
    			    }
                    else{
                        $weight = "";
                    }
                    
    				
        				$information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: " . $itemType . "<br>Recommended Minimum Skill: " . $row['skill'] . "<br>" . $yourSkill . "<br>" . $requirement . $yourStr . "<br> Damage: " . $row['min_dmg'] . " - " . $row['max_dmg'] . "<br>" . 
        				"Critical hit damage: " . (100 + $row['crit_dmg']) . "%<br>" . "Armour penetration: " . $row['armourPenetration'] . "<br>" . "Weapon hit chance: " . $hitChance . "%<br>" . "<strong>Your hit chance: " . $totalHit . "% ". $skill . $weight . $extras . 
        				"</strong><br><br>" . "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>Price: " . round($row['price'] * $priceModifier) . "g" . "<br><br>Description:<br>" . $row['description'] . "</div>";
				}
                else{
                    $itemType = "Shield";
                    $blockOrReduction = "Block Chance: " . $row['block_chance'] . "%";
					$blockAmount = "Block Amount: " . $row['block_amount'] . "%";
                    $skillText = "Recommended Minimum Skill: " . $row['skill'];
                    $canEquip = "<br><strong>Your shield skill: " . $_SESSION['characterProperties']['shield'] . "</strong>";
                    $information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>" . $blockAmount . "<br>Weight: " . $row['weight'] .
                                 "<br><br>" . "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>Price: " . round($row['price'] * $priceModifier) . "g"  . "<br><br>Description:<br>" . $row['description'] . "</div>";
                                
                }
				
				if (isset($row['picture'])){
					$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
				}
				else{
					$information .= "<div id=storePicture></div>";
				}
	
				echo "<li onclick=\"itemOutput(" . "'1'" . "," . "'$itemSubType'" . "," . "'$name'" . "," . "'$information'" . ")\" id=\"" . $table . $row['id'] . "\"" . "	class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $row['name'] . " (" . round($row['price'] * $priceModifier) . ")</a></li>";
			}
		}
		elseif ($table == "armours"){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$canEquip = "";
				$name = $row['id'];
				$itemSubType = $row['item_type'];
                
                switch($itemSubType){
                    case "heads":
                        $itemSubType = "2";
                        break;
                    case "chests":
                        $itemSubType = "3";
                        break;
                    case "arms":
                        $itemSubType = "4";
                        break;
                    case "legs":
                        $itemSubType = "5";
                        break;
                    case "feets":
                        $itemSubType = "6";
                        break;
                    default:
                        $itemSubType = "error";
                }
                
				$blockOrReduction = "Damage Reduction: " . $row['damage_reduction']*$armourPercent . "% / " . $row['damage_reduction'] . " damage";
				$skillText = "Required Skill: " . $row['skill'];
					
				if($row['type'] == "Light Armour"){
					if($_SESSION['characterProperties']['light_armour'] < $row['skill']){
						$canEquip = "<a style=color:red> - Skill too low to equip</a>";
						$canEquip .= "<br><strong>Your light armour skill: " . $_SESSION['characterProperties']['light_armour'] . "</strong>";
					}
					else{
						$canEquip .= "<br><strong>Your light armour skill: " . $_SESSION['characterProperties']['light_armour'] . "</strong>";
					}
				}
				elseif($row['type'] == "Heavy Armour"){
					if($_SESSION['characterProperties']['heavy_armour'] < $row['skill']){
						$canEquip = "<a style=color:red> - Skill too low to equip</a>";
						$canEquip .= "<br><strong>Your heavy armour skill: " . $_SESSION['characterProperties']['heavy_armour'] . "</strong>";
					}
					else{
						$canEquip .= "<br><strong>Your heavy armour skill: " . $_SESSION['characterProperties']['heavy_armour'] . "</strong>";
					}
				}
				$bonusStats = "";
				if (isset($row['bonusStats'])){
				    $explodedStats = explode(",",$row['bonusStats']);
                    foreach ($explodedStats as $stat){
                        $explodedAgain = explode(":",$stat);
                        $bonusStats .= $explodedAgain[0] . " skill: " . $explodedAgain[1] . "<br>";
                    }
				}
				
				$itemType = $row['type'];
			
				$information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
				"<br>" . $bonusStats . "<br>" . "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>Price: " . round($row['price'] * $priceModifier) . "g"  . "<br><br>Description:<br>" . $row['description'] . "</div>";
				
				if (isset($row['picture'])){
					$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
				}
				else{
					$information .= "<div id=storePicture></div>";
				}
				
				echo "<li onclick=\"itemOutput(" . "'2'" . "," . "'$itemSubType'" . "," . "'$name'" . "," . "'$information'" . ")\" id=\"" . $table . $row['id'] . "\"" . "	class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $row['name'] . " (" . round($row['price'] * $priceModifier) . ")</a></li>";
				
			}
			
		}
	}
	
	function getItemName($itemStr,$nameS,$type){
        global $conn;
        #$items = explode(",", $itemStr);
        #foreach($items as $item){
            #$nameS = "";
            $prefixS = "";
            $suffixS = "";
            $seperate = explode(":",$itemStr);
            $id = $seperate[0];
            $enchants = explode(";",$seperate[1]);
            $prefix = $enchants[0];
            $suffix = $enchants[1];
            if($prefix != 1 && $suffix == 1){
                $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND type='$type'";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                if($row['prefix'] != ""){
                    $prefixS = $row['prefix'] . " ";
                }
            }
            elseif($prefix == 1 && $suffix != 1){
                $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND type='$type'";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                if($row['suffix'] != ""){
                    $suffixS = " of " . $row['suffix'];
                }
            }
            elseif($prefix != 1 && $suffix != 1){
                $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND type='$type'";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                if($row['prefix'] != ""){
                    $prefixS = $row['prefix'] . " ";
                }
        
                $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND type='$type'";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                if($row['suffix'] != ""){
                    $suffixS = " of " . $row['suffix'];
                }
                
            }
                return $prefixS . $nameS . $suffixS;
        }

	function getYourItems(){
		global $conn;
        
        $enchantEffective = array("80","100","120","140","160");
        
		if(isset($_SESSION['characterProperties']['name'])){
		    echo "<h3>Sell Equipment</h3>";
    		$inv_id = $_SESSION['characterProperties']['inventory_id'];
    		$sql = "SELECT * FROM inventory WHERE iid = '$inv_id'";
    		$result=mysqli_query($conn, $sql);
    		$equipment = mysqli_fetch_assoc($result);
    
    		$weapons_inv =			$equipment['weapons'];
            $secondary_inv =        $equipment['secondarys'];
    		$head_inv =				$equipment['heads'];
    		$chest_inv =			$equipment['chests'];
    		$arm_inv =				$equipment['arms'];
    		$leg_inv = 				$equipment['legs'];
    		$feet_inv = 			$equipment['feets'];
    		$trinket_inv = 			$equipment['trinkets'];
			
    		$slots = array($equipment['weapons'],$equipment['secondarys'],$equipment['heads'],$equipment['chests'],$equipment['arms'],$equipment['legs'],$equipment['feets']);
    		$i = 0;
    		foreach ($slots as $slot){
    			$exploded = explode(",",$slot);
    			
    				if ($i == 0 || $i == 1){
    					foreach ($exploded as $item){
    						if ($item == "1" || $item == ''){
    
    						}
    						else{
    								//WEAPON
    								$sql = "SELECT * FROM weapons WHERE id=? LIMIT 1";
    								$stmt = mysqli_prepare($conn,$sql);
    								mysqli_stmt_bind_param($stmt, "i", $item);
    								mysqli_stmt_execute($stmt);
    								$result = $stmt->get_result();
    								$row = mysqli_fetch_assoc($result);
                                    $itemId = $row['id'];
    								
                                    $name = getItemName($item,$row['name'],$row['enchantType']);
    								if ($row['type'] == "1h"){
    									$itemType = "One-handed weapon";
    									
    									$skillReq = $row['skill'];
    									$hitChance = $row['chance_hit'];
                                        $w_type = "1";
    								
    								}
    								elseif ($row['type'] == "2h"){
    									$itemType = "Two-handed weapon";
    									
    									$skillReq = $row['skill'];
    									$hitChance = $row['chance_hit'];
                                        $w_type = "1";
    
    								}
                                    elseif($row['type'] == "bow" || $row['type'] == "crossbow"){
                                        $itemType = "Secondary Weapon";
                                        $hitChance = $row['chance_hit'];
                                        $w_type = "7";
                                    }
                                    elseif($row['type'] == "shield"){
                                        $itemType = "Shield";
                                        $w_type = "1";
                                        $blockOrReduction = "Block Chance: " . $row['block_chance'] . "%";
                                        $skillText = "Recommended Minimum Skill: " . $row['skill'];
                                    }
                   
    								
    								$requirement = "<br>Strength required: " . $row['strReq'] . "<br>";
    									
    								
    								if($itemType != "Shield"){
    								    $information = "<div id=storeInformation><h3>" . $name . "</h3>" . "Item type: " . $itemType . "<br>Recommended Minimum Skill: " . $row['skill'] . "<br>" . $requirement . "<br> Damage: " . $row['min_dmg'] . " - " . $row['max_dmg'] . "<br>" . 
                                        "Bonus crit damage: " . $row['crit_dmg'] . "%<br>" . "Chance to Hit: " . $hitChance . "%<br><br>" . "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>Sell Price: " . round($row['price']/4) . "g" . 
                                        "<br><br>Description:<br>" . $row['description'] . "</div>";
    								}
                                    else{
                                        $information = "<div id=storeInformation><h3>" . $name . "</h3>" . "Item type: " . $itemType . "<br>" . $skillText . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
                                        "<br>Sell Price: " . round($row['price']/4) . "g"  . "<br><br>Description:<br>" . $row['description'] . "</div>";
                                    }
    								
    								if (isset($row['picture'])){
    									$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
    								}
    								else{
    									$information .= "<div id=storePicture></div>";
    								}
    					
    								echo "<li onclick=\"itemOutputSell(" . "'1'" . "," . "'$w_type'" . "," . "'$itemId'" . "," . "'$information'" . ")\" class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $name . " (" . round($row['price']/4) . ")</a></li>";

    						}
    					}
    				}
    				else{
    				foreach ($exploded as $item){
    					if ($item == "Nothing" || $item == ''){
    					}
    					else{
    						$sql = "SELECT * FROM armours WHERE id=? LIMIT 1";
    						$stmt = mysqli_prepare($conn,$sql);
    						mysqli_stmt_bind_param($stmt, "i", $item);
    						mysqli_stmt_execute($stmt);
    						$result = $stmt->get_result();
    						$row = mysqli_fetch_assoc($result);
                                $itemId = $row['id'];
    						
    								$canEquip = "";
                                    $name = getItemName($item,$row['name'],$row['enchantType']);
    								$itemSubType = $row['item_type'];
                                    
                                    switch($itemSubType){
                                        case "heads":
                                            $itemSubType = "2";
                                            break;
                                        case "chests":
                                            $itemSubType = "3";
                                            break;
                                        case "arms":
                                            $itemSubType = "4";
                                            break;
                                        case "legs":
                                            $itemSubType = "5";
                                            break;
                                        case "feets":
                                            $itemSubType = "6";
                                            break;
                                        default:
                                            $itemSubType = "error";
                                    }
    								if($row['type'] == "Light Armour"){
										if($_SESSION['characterProperties']['light_armour'] < $row['skill']){
											$canEquip = "<a style=color:red> - Skill too low to equip</a>";
											$canEquip .= "<br><strong>Your light armour skill: " . $_SESSION['characterProperties']['light_armour'] . "</strong>";
										}
										else{
											$canEquip .= "<br><strong>Your light armour skill: " . $_SESSION['characterProperties']['light_armour'] . "</strong>";
										}
									}
									elseif($row['type'] == "Heavy Armour"){
										if($_SESSION['characterProperties']['heavy_armour'] < $row['skill']){
											$canEquip = "<a style=color:red> - Skill too low to equip</a>";
											$canEquip .= "<br><strong>Your heavy armour skill: " . $_SESSION['characterProperties']['heavy_armour'] . "</strong>";
										}
										else{
											$canEquip .= "<br><strong>Your heavy armour skill: " . $_SESSION['characterProperties']['heavy_armour'] . "</strong>";
										}
									}
									$bonusStats = "";
									if (isset($row['bonusStats'])){
									    $explodedStats = explode(",",$row['bonusStats']);
					                    foreach ($explodedStats as $stat){
					                        $explodedAgain = explode(":",$stat);
					                        $bonusStats .= $explodedAgain[0] . " skill: " . $explodedAgain[1] . "<br>";
					                    }
									}
    								$blockOrReduction = "Damage Reduction: " . $row['damage_reduction']*4 . "% / " . $row['damage_reduction'] . " damage";
    								$skillText = "Required Skill: " . $row['skill'];
    								
									$itemType = $row['type'];
									
									$allStats = array("min_Dmg"=>"Bonus minimum weapon damage","max_Dmg"=>"Bonus maximum weapon damage","crit_Dmg"=>"Bonus critical weapon damage",
									"strReq"=>"Strength Requirement","1hSkill"=>"Bonus one handed skill","2hSkill"=>"Bonus two handed skill","dodge"=>"Bonus dodge skill","strength"=>"Bonus strength",
									"dexterity"=>"Bonus dexterity","vitality"=>"Bonus vitality","bow"=>"Bonus bow skill","crossbow"=>"Bonus crossbow skill",
									"initiative"=>"Bonus initiative skill","finesse"=>"Bonus finesse skill","lightArmour"=>"Bonus light armour skill","heavyArmour"=>"Bonus heavy armour skill",
									"shield"=>"Bonus shield skill","parry"=>"Bonus parry skill","foulPlay"=>"Bonus foul play skill");
									$statText = "";
									foreach($allStats as $key => $stat){
										if($row[$key] > 0){
											$statText .= $stat . ": " . $row[$key] . "<br>";
										}
									}

    							
    								$information = "<div id=storeInformation><h3>" . $name . "</h3>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
    								"<br>" . $bonusStats . "<br>" . $statText . "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>Sell Price: " . round($row['price']/4) . "g"  . "<br><br>Description:<br>" . $row['description'] . "</div>";
    								
    								if (isset($row['picture'])){
    									$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
    								}
    								else{
    									$information .= "<div id=storePicture></div>";
    								}
    								
    								echo "<li onclick=\"itemOutputSell(" . "'2'" . "," . "'$itemSubType'" . "," . "'$itemId'" . "," . "'$information'" . ")\" class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $name . " (" . round($row['price']/4) . ")</a></li>";
    			
    					}
    				}
    			}
    		$i++;
    		}
			$trinket_inv = explode(",",$trinket_inv);
			foreach($trinket_inv as $trinket){
				$itemId = $trinket;
				$sql = "SELECT * FROM trinkets WHERE id='$itemId'";
				$result = mysqli_query($conn,$sql);
				if(mysqli_num_rows($result) > 0){
					$row = mysqli_fetch_assoc($result);
					$effects = "";
				
					if($row['extraCrit'] > 0){
						$effects .= "<br>" . $row['extraCrit'] . "% Extra critical hit damage (not chance)";	
					}
					if($row['extraStr'] > 0){
						$effects .= "<br>" . $row['extraStr'] . " Extra strength";	
					}
					if($row['extraDex'] > 0){
						$effects .= "<br>" . $row['extraDex'] . " Extra dexterity";	
					}
					if($row['extraVit'] > 0){
						$effects .= "<br>" . $row['extraVit'] . " Extra vitality";	
					}
					if($row['extraRangeAcc'] > 0){
						$effects .= "<br>" . $row['extraRangeAcc'] . "% Extra hit chance with ranged weapons";	
					}
					
					$information = "<div id=storeInformation><h3>" . $row['name'] . "</h3>" . "Item type: Trinket<br><br><strong>Effects: </strong>" . $effects . "<br><br>Description:<br>" . $row['description'] . "</div>";
					
					if (isset($row['picture'])){
						$information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
					}
					else{
						$information .= "<div id=storePicture></div>";
					}
					
					echo "<li onclick=\"itemOutputSell(" . "'3'" . "," . "9" . "," . "'$itemId'" . "," . "'$information'" . ")\" class=\"listItems\"><a class=\"itemLinks\"href=\"javascript:void(0)\">" . $row['name'] . " (" . round($row['price']/4) . ")</a></li>";
				}
			}
		}
	}

if(isset($_GET['yourItems'])){
	getYourItems();
}
?>
