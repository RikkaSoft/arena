<?php


	function battleBeast($adventure,$beast,$yourSurrender,$failedEscape){
	global $conn;
    
    if ($adventure == 1){
        $sql = "SELECT name FROM creatures WHERE id=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "s", $beast);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $row = mysqli_fetch_assoc($result);
        $beast = $row['name'];
        #echo "You start to fight the " . $beast . "...<br><br>";
    }
    
		global $yourHp,$yourName,$yourAttackMod,$yourChanceHit,$yourMinDmg,$yourMaxDmg,$yourWepName,$yourCritChance,$yourCritDamage,$yourDodgeChance;
		global $opponentHp,$opponentName,$opponentAttackMod,$opponentChanceHit,$opponentMinDmg,$opponentMaxDmg,$opponentWepName,$opponentCritChance,$opponentCritDamage,$opponentDodgeChance;
		global $firstMove,$yourHes,$yourHem,$opponentHes,$opponentHem;
		global $opponentNextAttackMiss, $yourNextAttackMiss;
		global $opponentTryParry, $yourTryParry;
		global $opponentChestName,$opponentArmName,$opponentLegName,$opponentHeadName;
		global $opponentLegArmour, $opponentArmArmour, $opponentChestArmour, $opponentHeadArmour;
		global $blockMessage,$opponentOffHandType,$opponentRealBlockChance,$opponentOffHandName;
		global $opponentFoulChance,$opponentParryChance;
		global $yourReport;
		global $yourWeight;
		global $opponentArmour;
        global $yourAmmo;
        global $youCanShoot;
        global $yourPosition,$opponentPosition;
        global $yourSecReload, $yourReloadTime;
        global $yourInitiativeSkill, $yourDodgeSkill;
        global $part;
        global $players;
        $rangedPlayers = 0;
        $yourReloadTime = 0;
        $yourSecReload = 0;
		
		global $yourWeight;
		$yourWeight = 0;
		
        $players = array();
        
        function getPlayerStats($name){
            global $conn;
            
            $sql = "SELECT * FROM characters WHERE name=?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = $stmt->get_result();
            $charInfo = mysqli_fetch_assoc($result);
            
            $charId = $charInfo['id'];
            
            $username = array("username"=>$_SESSION['loggedIn']);
            
            $sql = "SELECT * FROM equipment WHERE eid=?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "i", $charInfo['equipment_id']);
            mysqli_stmt_execute($stmt);
            $result = $stmt->get_result();
            $equipment = mysqli_fetch_assoc($result);
            
            return array_merge($username,$charInfo,$equipment);
        }
        
        function getItemStats($table,$item,$player){
            global $conn,$players;
            $ids = $item;
            $item = explode(":", $item);
            $item = $item[0];
            $sql = "SELECT * FROM $table WHERE id='$item'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $players[$player]['totalWeight'] += $row['weight'];
            
            $row['ids'] = $ids;
            return $row;
        }
        
        function applyEnchants($row,$type,$slot,$player,$enchantTier){
            global $players;
            $enchantEffective = array("0.8","1","1.2","1.4");
            $enchantMultiplier = $enchantEffective[$enchantTier-1];
            if ($type == "melee" || $type == "ranged"){
                if($row['damageBonusPercent'] > 0){
                    $players[$player][$slot]['min_dmg'] = $players[$player][$slot]['min_dmg'] * (1 + (($row['damageBonusPercent']) / 100) * $enchantMultiplier);
                    $players[$player][$slot]['max_dmg'] = $players[$player][$slot]['max_dmg'] * (1 + (($row['damageBonusPercent']) / 100) * $enchantMultiplier);
                }
                $players[$player][$slot]['min_dmg'] = $players[$player][$slot]['min_dmg'] + $row['damageBonusPoint'] * $enchantMultiplier;
                $players[$player][$slot]['max_dmg'] = $players[$player][$slot]['max_dmg'] + $row['damageBonusPoint'] * $enchantMultiplier;
                $players[$player][$slot]['chance_hit'] = $players[$player][$slot]['chance_hit'] + $row['accuracyPercent'] * $enchantMultiplier;
                $players[$player][$slot]['armourPenetration'] = $players[$player][$slot]['armourPenetration'] + $row['armourPenetration'] * $enchantMultiplier;
            }
            else{
            	if($row['armourBonus'] > 0){
            		$players[$player][$slot]['damage_reduction'] = round($players[$player][$slot]['damage_reduction'] + $row['armourBonus'] * $enchantMultiplier);
            	}
                $players[$player][$slot]['weight'] = $players[$player][$slot]['weight'] - $row['weightReduction'] * $enchantMultiplier;
                if ($players[$player][$slot]['weight'] < 0){
                    $players[$player][$slot]['weight'] = 0;
                }
            }
				if($row['blockPercent'] > 0){
					$players[$player]['left_hand']['block_chance'] = $players[$player]['left_hand']['block_chance'] + $row['blockPercent'] * $enchantMultiplier;
				}
                $players[$player]['one_handed'] = $players[$player]['one_handed'] + $row['oneSkill'] * $enchantMultiplier;
                $players[$player]['two_handed'] = $players[$player]['two_handed'] + $row['twoSkill'] * $enchantMultiplier;
                $players[$player]['bow'] = $players[$player]['bow'] + $row['bowSkill'] * $enchantMultiplier;
                $players[$player]['crossbow'] = $players[$player]['crossbow'] + $row['xBowSkill'] * $enchantMultiplier;
                $players[$player]['finesse'] = $players[$player]['finesse'] + $row['finesseSkill'] * $enchantMultiplier;
                $players[$player]['initiative'] = $players[$player]['initiative'] + $row['initiativeSkill'] * $enchantMultiplier;
                $players[$player]['shield'] = $players[$player]['shield'] + $row['shieldSkill'] * $enchantMultiplier;
                $players[$player]['parry'] = $players[$player]['parry'] + $row['parrySkill'] * $enchantMultiplier;
                $players[$player]['foul_play'] = $players[$player]['foul_play'] + $row['foulSkill'] * $enchantMultiplier;
                $players[$player]['dodgeSkill'] = $players[$player]['dodgeSkill'] + $row['dodgeSkill'] * $enchantMultiplier;
        }
        
        
        function getPlayerEnchants($itemStr,$nameS,$slot,$type,$player,$enchantTier){
            global $conn;
            global $players;
            $table = "armours";
            if($type == "1h" || $type == "2h"){
                $type = "melee";
                $table = "weapons";
            }
            elseif($type == "bow" || $type == "crossbow"){
                $type = "ranged";
                $table = "weapons";
            }
                $prefixS = "";
                $suffixS = "";
                
                $seperate = explode(":",$itemStr);
                $enchants = explode(";",$seperate[1]);
                $prefix = $enchants[0];
                $suffix = $enchants[1];
                if($prefix != 1 && $suffix == 1){
                    $sql = "SELECT * FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    if($row['prefix'] != ""){
                        $prefixS = $row['prefix'] . " ";
                        applyEnchants($row,$type,$slot,$player,$enchantTier);
                    }
                    
                }
                elseif($prefix == 1 && $suffix != 1){
                    $sql = "SELECT * FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    if($row['suffix'] != ""){
                        $suffixS = " of " . $row['suffix'];
                        applyEnchants($row,$type,$slot,$player,$enchantTier);
                    }
                }
                elseif($prefix != 1 && $suffix != 1){
                    $sql = "SELECT * FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    if($row['prefix'] != ""){
                        $prefixS = $row['prefix'] . " ";
                        applyEnchants($row,$type,$slot,$player,$enchantTier);
                    }
                    unset($row);
                    $sql = "SELECT * FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    if($row['suffix'] != ""){
                        $suffixS = " of " . $row['suffix'];
                        applyEnchants($row,$type,$slot,$player,$enchantTier);
                    }
                    
                }
                $players[$player][$slot]['name'] = $prefixS . $nameS . $suffixS;
            }

        function getFeet($item){
            global $conn,$yourInitiativeSkill, $yourDodgeSkill;
            $sql = "SELECT bonusStats from armours WHERE name='$item'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            if(isset($row['bonusStats'])){
                $explodedStats = explode(",",$row['bonusStats']);
                foreach ($explodedStats as $stat){
                    $explodedAgain = explode(":",$stat);
                    if ($explodedAgain[0] == "Dodge"){
                        $yourDodgeSkill = $yourDodgeSkill + $explodedAgain[1];
                    }
                    elseif($explodedAgain[0] == "Initiative"){
                        $yourInitiativeSkill = $yourInitiativeSkill + $explodedAgain[1];
                    }
                }
            }
        }

		function whereToHit()
				{
				    global $part;
					$whereToHit = rand(1,20);
					switch ($whereToHit) {
						case 1:
						case 2:
						case 3:
                            $part = "leg";
							return "Right leg";
							break;
						case 4:
						case 5:
						case 6:
                            $part = "leg";
							return "Left leg";
							break;
						case 7:
						case 8:
						case 9:
                            $part = "arm";
							return "Right arm";
							break;
						case 10:
						case 11:
						case 12:
                            $part = "arm";
							return "Left arm";
							break;
						case 13:
						case 14:
						case 15:
						case 16:
						case 17:
						case 18:
                            $part = "chest";
							return "chest";
							break;
						case 19:
						case 20:
                            $part = "head";
							return "head";
							break;
					}
				}

				function opponentParry(){
					global $players,$opponentName,$parryMessage;
					if(rand(1,100) > (100-$players[0]['parry'])){
						$parryMessage = "<br><strong>" . $players[0]['name'] . " parried the attack!</strong><br><br>";
						return "true";
					}
				}
				
				function opponentFoulPlay(){
					global $players,$opponentName,$foulMessage,$opponentHes,$opponentHem;
					if(rand(1,100) > (100-$players[0]['foul_play'])){
						$foulMessage = "<br><strong>" . $players[0]['name'] . " dodged the attack and threw some sand into the eyes of " . $opponentName . " causing " . $opponentHem .  " to lose " . $opponentHes . " next round</strong><br><br>";
						return "true";
					}
				}
                
                function EnchantBonuses($enchants,$minDmg,$maxDmg,$accuracy,$armourPenetration,$wepName){
                    global $conn;
                    if ($enchants != "1;1"){
                        $split = explode(";",$enchants);
                        if($split[0] == $split[1]){
                            $sql = "SELECT * FROM enchants WHERE id='$split[0]'";
                            $result = mysqli_query($conn,$sql);
                            $row = mysqli_fetch_assoc($result);
                            if($row['damageBonusPercent'] > 0){
                                $minDmg = $minDmg * (1 + (($row['damageBonusPercent'] * 2) / 100));
                                $maxDmg = $maxDmg * (1 + (($row['damageBonusPercent'] * 2) / 100));
                            }
                            $minDmg = $minDmg + $row['damageBonusPoint'] * 2;
                            $maxDmg = $maxDmg + $row['damageBonusPoint'] * 2;
                            $accuracy = $accuracy + $row['accuracyPercent'] * 2;
                            $armourPenetration = $armourPenetration + $row['armourPenetration'] * 2;
                            $wepName = $row['prefix'] . " " . $wepName . " of " . $row['suffix'];
                        }
                        else{
                            $sql = "SELECT * FROM enchants WHERE id IN ('$split[0]','$split[1]')";
                            $result = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($result)){
                                if($row['damageBonusPercent'] > 0){
                                    $minDmg = $minDmg * (1 + (($row['damageBonusPercent']) / 100));
                                    $maxDmg = $maxDmg * (1 + (($row['damageBonusPercent']) / 100));
                                }
                                $minDmg = $minDmg + $row['damageBonusPoint'];
                                $maxDmg = $maxDmg + $row['damageBonusPoint'];
                                $accuracy = $accuracy + $row['accuracyPercent'];
                                $armourPenetration = $armourPenetration + $row['armourPenetration'];
                                if($row['id'] == $split[0] && $row['id'] != 1){
                                    $wepName = $row['prefix'] . " " . $wepName;
                                }
                                elseif($row['id'] == $split[1] && $row['id'] != 1){
                                    $wepName .= " of " . $row['suffix'];
                                }
                                
                            }
                        }
                    }
                    return array("minDmg"=>$minDmg,"maxDmg"=>$maxDmg,"accuracy"=>$accuracy,"penetration"=>$armourPenetration,"wepName"=>$wepName);
                }
                
                
		
				global $yourChestName,$yourArmName,$yourLegName,$yourHeadName;
				global $yourLegArmour, $yourArmArmour, $yourChestArmour, $yourHeadArmour;
				global $blockMessage,$yourOffHandType,$yourRealBlockChance,$yourOffHandName;
				global $yourFoulChance,$yourParryChance;
				
                
                //STARTS HERE
                array_push($players,getPlayerStats($_SESSION['characterProperties']['name']));
                
                $player = 0;
                
                if ($players[$player]['gender'] == "Male"){
                $players[$player]['hisher'] = "his";
                $players[$player]['himher'] = "him";
                }
                else{
                    $players[$player]['hisher'] = "her";
                    $players[$player]['himher'] = "her";
                }
                
                $players[$player]['totalWeight'] = 0;
                $players[$player]['style'] = "dualwield";
                $players[$player]['right_hand'] = getItemStats("weapons",$players[$player]['right_hand'],$player);
                 
                $players[$player]['left_hand'] = getItemStats("weapons",$players[$player]['left_hand'],$player);
                $players[$player]['secondary'] = getItemStats("weapons",$players[$player]['secondary'],$player);
                
                $players[$player]['head'] = getItemStats("armours",$players[$player]['head'],$player);
                $players[$player]['chest'] = getItemStats("armours",$players[$player]['chest'],$player);
                $players[$player]['arm'] = getItemStats("armours",$players[$player]['arm'],$player);
                $players[$player]['leg'] = getItemStats("armours",$players[$player]['leg'],$player);
                $players[$player]['feet'] = getItemStats("armours",$players[$player]['feet'],$player);
                
                if(isset($players[$player]['feet']['bonusStats'])){
                    $explodedStats = explode(",",$players[$player]['feet']['bonusStats']);
                    foreach ($explodedStats as $stat){
                        $explodedAgain = explode(":",$stat);
                        if ($explodedAgain[0] == "Dodge"){
                            $players[$player]['dodgeSkill'] = $players[$player]['dodgeSkill'] + $explodedAgain[1];
                        }
                        elseif($explodedAgain[0] == "Initiative"){
                            $players[$player]['initiative'] = $players[$player]['initiative'] + $explodedAgain[1];
                        }
                    }
                }
                
                $sql = "SELECT * FROM modifiers";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                $maxBlock = $row['maxBlock'];
                $maxFoul = $row['maxFoul'];
                $maxDodge = $row['maxDodge'];
                $maxCrit = $row['maxCrit'];
                $maxParry = $row['maxParry'];
                
                $players[$player]['battleSurrender'] = round($players[$player]['vitality'] * $yourSurrender); 

                                
                    #attackMod
                    $attackMod = (($players[$player]['strength'] * $row['attackMod']) + ($players[$player]['dexterity'] * $row['dexAttackMod'])) + 1;
                    $rangedAttackMod = (($players[$player]['dexterity'] * $row['dexAttackMod'])*3) + 1;
                    
                    $attackModStr = $row['attackMod'];
                    $attackModDex = $row['dexAttackMod'];
                        
                    #Right_hand
                        if ($players[$player]['right_hand']['name'] == "Nothing"){
                            $players[$player]['right_hand']['name'] = "Bare Hands";
                        }
                        
                    #Left_hand
                    
                        #shield
                    if ($players[$player]['left_hand']['type'] == "shield"){
                        $players[$player]['style'] = "shield";
                        #
                        
                        $players[$player]['right_hand']['min_dmg'] = $players[$player]['right_hand']['min_dmg'] * $attackMod;
                        $players[$player]['right_hand']['max_dmg'] = $players[$player]['right_hand']['max_dmg'] * $attackMod;
                    }
                    else{
                        $players[$player]['block'] = 0;
                        #weapon
                            if($players[$player]['right_hand']['type'] == "1h"){
                                #hit
                                if($players[$player]['left_hand']['name'] == "Nothing" && $players[$player]['right_hand'] != "Nothing"){
                                    $players[$player]['style'] = "singleWield";
                                    $players[$player]['right_hand']['min_dmg'] = $players[$player]['right_hand']['min_dmg'] * $attackMod;
                                    $players[$player]['right_hand']['max_dmg'] = $players[$player]['right_hand']['max_dmg'] * $attackMod;
                                }
                                else{
                                    $yourOffHandType = "weapon";
                                #damage
                                    $players[$player]['left_hand']['min_dmg'] = $players[$player]['left_hand']['min_dmg'] * $attackMod * 0.6;
                                    $players[$player]['left_hand']['max_dmg'] = $players[$player]['left_hand']['max_dmg'] * $attackMod * 0.6;
                                        
                                    #mainHand
                                    $players[$player]['right_hand']['min_dmg'] = $players[$player]['right_hand']['min_dmg'] * $attackMod * 0.8;
                                    $players[$player]['right_hand']['max_dmg'] = $players[$player]['right_hand']['max_dmg'] * $attackMod * 0.8;
                                }
                                if ($players[$player]['left_hand']['name'] == "Nothing"){
                                    $players[$player]['left_hand']['name'] = "Bare Hands";
                                }
                            }
                        }
                    if ($players[$player]['right_hand']['type'] == "2h"){
                        #right hand dmg
                        $players[$player]['right_hand']['min_dmg'] = $players[$player]['right_hand']['min_dmg'] * $attackMod;
                        $players[$player]['right_hand']['max_dmg'] = $players[$player]['right_hand']['max_dmg'] * $attackMod;
                    }
                    
                    #RANGED
                    if ($players[$player]['secondary']['ids'] == "1:1;1"){
                        $players[$player]['canShoot'] = 0;
                    }
                    else{
                        $players[$player]['canShoot'] = 1;
                        $rangedPlayers += 1;
                    
                    $compareSkill = $players[$player][$players[$player]['secondary']['type']];
                    $players[$player]['secondary']['chance_hit'] = round($players[$player]['secondary']['chance_hit'] + (($compareSkill-$players[$player]['secondary']['skill']) * $row['weaponSkillDivider'])-($players[$player]['totalWeight']/2));
                    #echo $players[$player]['secondary']['chance_hit'];
                    $players[$player]['secondary']['reload'] = 0;
                    }
                    
                    if ($players[$player]['secondary']['type'] == "bow"){
                        $players[$player]['secondary']['min_dmg'] = $players[$player]['secondary']['min_dmg'] * $rangedAttackMod;
                        $players[$player]['secondary']['max_dmg'] = $players[$player]['secondary']['max_dmg'] * $rangedAttackMod;
                    }
                    
            
                    //PLAYER ENCHANTMENTS
                    getPlayerEnchants($players[$player]['right_hand']['ids'],$players[$player]['right_hand']['name'],"right_hand","melee",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['left_hand']['ids'],$players[$player]['left_hand']['name'],"left_hand",$players[$player]['left_hand']['type'],$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['secondary']['ids'],$players[$player]['secondary']['name'],"secondary","ranged",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['head']['ids'],$players[$player]['head']['name'],"head","armour",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['chest']['ids'],$players[$player]['chest']['name'],"chest","armour",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['arm']['ids'],$players[$player]['arm']['name'],"arm","armour",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['leg']['ids'],$players[$player]['leg']['name'],"leg","armour",$player,$players[$player]['right_hand']['enchantTier']);
                    getPlayerEnchants($players[$player]['feet']['ids'],$players[$player]['feet']['name'],"feet","armour",$player,$players[$player]['right_hand']['enchantTier']);
                     
                    #Calculate hitchances   
                    if($players[$player]['right_hand']['type'] == "1h"){
                        $compareSkill = $players[$player]['one_handed'];
                        $players[$player]['right_hand']['chance_hit'] = round($players[$player]['right_hand']['chance_hit'] + (($compareSkill-$players[$player]['right_hand']['skill']) * $row['weaponSkillDivider'])-$players[$player]['totalWeight']);
                        if ($players[$player]['left_hand']['type'] == "1h"){
                            $players[$player]['left_hand']['chance_hit'] = round($players[$player]['left_hand']['chance_hit'] + (($compareSkill-$players[$player]['left_hand']['skill']) * $row['weaponSkillDivider'])-$players[$player]['totalWeight']);
                        }
                        else{
                            $players[$player]['block'] = round($players[$player]['left_hand']['block_amount'] + ($players[$player]['shield'] * $row['blockMod']));
                        }
                    }
                    else{
                        $compareSkill = $players[$player]['two_handed'];
                        $players[$player]['right_hand']['chance_hit'] = round($players[$player]['right_hand']['chance_hit'] + (($compareSkill-$players[$player]['right_hand']['skill']) * $row['weaponSkillDivider'])-$players[$player]['totalWeight']);
                    }
                    
                                
                    #Foul Play
                    $players[$player]['foul_play'] = $players[$player]['foul_play'] * $row['foul_playMod'];
                    $players[$player]['foul_play'] = round($players[$player]['foul_play'] - ((($players[$player]['totalWeight'] * $row['weightFoulMod'])/ 100) * $players[$player]['foul_play']),1);
                    $players[$player]['foul_play'] = round($players[$player]['foul_play']);
                    
                    
                    #Dodge
                    
                    $players[$player]['dodge'] = round(($players[$player]['dexterity'] * $row['dodgeMod'])*2 + ($players[$player]['dodgeSkill'] * $row['dodgeMod']));
                    $players[$player]['dodge'] = round($players[$player]['dodge'] - ((($players[$player]['totalWeight'] * $row['weightDodgeMod'])/ 100) * $players[$player]['dodge']),1);
                    $players[$player]['dodge'] = round($players[$player]['dodge']);
                    
                    
                    #Crit 
                    
                    $players[$player]['critical'] = round((($players[$player]['finesse'] * $row['finesseMod']) + ($players[$player]['dexterity'] * $row['critMod'])));
                    
                    
                    
                    //PARRY
                    if($players[$player]['right_hand']['canParry'] == "true" || $players[$player]['left_hand']['canParry'] == "true"){
                        $players[$player]['parry'] = round($players[$player]['parry'] * $row['parryMod']);
                        $players[$player]['parry'] = round($players[$player]['parry'] - ((($players[$player]['totalWeight'] * $row['weightParryMod'])/ 100) * $players[$player]['parry']),1);
                        $players[$player]['parry'] = round($players[$player]['parry']);
                    }
                    else{
                        $players[$player]['parry'] = 0;
                    }
                    
                    //ROUND stuff
                    $players[$player]['right_hand']['min_dmg'] = round($players[$player]['right_hand']['min_dmg']);
                    $players[$player]['right_hand']['max_dmg'] = round($players[$player]['right_hand']['max_dmg']);
                    $players[$player]['right_hand']['armourPenetration'] = round($players[$player]['right_hand']['armourPenetration']);
                    if ($players[$player]['left_hand']['type'] != "shield"){
                        $players[$player]['left_hand']['min_dmg'] = round($players[$player]['left_hand']['min_dmg']);
                        $players[$player]['left_hand']['max_dmg'] = round($players[$player]['left_hand']['max_dmg']);
                        $players[$player]['left_hand']['armourPenetration'] = round($players[$player]['left_hand']['armourPenetration']);
                    }
                    $players[$player]['secondary']['min_dmg'] = round($players[$player]['secondary']['min_dmg']);
                    $players[$player]['secondary']['max_dmg'] = round($players[$player]['secondary']['max_dmg']);
                    $players[$player]['secondary']['armourPenetration'] = round($players[$player]['secondary']['armourPenetration']);
                    
                    
                    //make sure no value is higher than the max after enchants
                    if ($players[$player]['foul_play'] >= $maxFoul) {
                        $players[$player]['foul_play'] = $maxFoul;
                    }
                    if ($players[$player]['dodge'] >= $maxDodge){
                        $players[$player]['dodge'] = $maxDodge;
                    }
                    if ($players[$player]['critical'] >= $maxCrit){
                        $players[$player]['critical'] = $maxCrit;
                    }
                    if ($players[$player]['parry'] >= $maxParry){
                        $players[$player]['parry'] = $maxParry;
                    }
                    if($players[$player]['style'] == "dualwield"){
						if($players[$player]['right_hand']['type'] == "2h"){
							#2h
							$players[$player]['parryType'] = 2;
						}
						else{
							#dualwield
							$players[$player]['parryType'] = 1;
						}
					}
					else{
						#block or just a 1h
						$players[$player]['parryType'] = 0;
					}
                    $yourCritChance = $players[$player]['critical'];
                    
                    
                    
                    #var_dump($players);
if ($players[$player]['battleSurrender'] >= $players[$player]['hp']){
	echo "You have chosen to surrender on HP higher than your current available HP. Match is cancelled.";		
}
else{
		global $opponentDmg;

		$opponentName =			$beast;
		$sql = "SELECT * FROM creatures WHERE name=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $opponentName);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		
		$opponentStrength = 	$row['strength'];
		$opponentDexterity = 	$row['dexterity'];
		$opponentHp = 			$row['vitality'];
		$opponentIntellect = 	$row['intellect'];
		$opponentType =			$row['type'];
		$opponentXp =			$row['experience'];
		$opponentGold = 		$row['gold'];
		$opponentMinDmg =		$row['minDamage'];
		$opponentMaxDmg =		$row['maxDamage'];
		$opponentArmour = 		$row['armour'];
        $adventureSurrenderDamage =  $row['surrenderDamage'];
        
        $opponentAttackMod = ($opponentStrength * $attackModStr) + 1;
        
        if($players[$player]['canShoot'] == 1){
            $opponentPosition = 3;
            $yourPosition = 0;
            $youCanShoot = 1;
        }
        else{
            $opponentPosition = 0;
            $yourPosition = 0;
            $youCanShoot = 0;
        }
		
		$opponentXp = $opponentXp-$players[$player]['level'];
		if ($opponentXp < 0){
			$opponentXp = 0;
		}
		$opponentGold = $opponentGold-$players[$player]['level'];
		if ($opponentGold < 0){
			$opponentGold = 0;
		}
		#var_dump($players);
		global $yourReport;
		$yourReport = "<h2 align=\"center\"><a style=\"color:blue\">" . $players[$player]['name'] . "</a> VS <a style=\"color:red\">" . $opponentName . "</a></h2>";
        
        $yourAmmo = $players[$player]['secondary']['ammo'];

function yourAttack($yourWepName2,$yourMinDmg2,$yourMaxDmg2,$yourCritDamage2,$yourChanceHit2,$yourArmourPenetration,$type){
                    global $players;
					global $opponentHp,$firstMove,$yourName, $opponentName, $opponentDodgeChance, $yourAttackMod,$opponentHes;
					global $opponentLegArmour, $opponentArmArmour, $opponentChestArmour, $opponentHeadArmour,$activeArmour,$yourCritChance;
					global $dmgReduction;
					global $opponentOffHandType, $blockMessage;
					global $yourNextAttackMiss;
					global $parryMessage, $opponentTryParry;
					global $foulMessage;
					global $yourReport;
					global $opponentArmour;
                    global $yourAmmo;
                    global $yourHes;
                    global $youCanShoot;
                    global $yourReloadTime;
                    global $yourSecReload;
                    global $yourRangedAttackMod;
					$blockMessage = "";
					$firstMove = 1;
					$foul = "";
					$hitPart = "";
					$hitText = "";
					$parry = "";
					$blocked = "";
                    
                    $yourCritDamage2 = ($yourCritDamage2/100 + 1); 
                    
                    if ($yourReloadTime == 0 || $type == "melee"){
                        
                        
                    if ($type == "melee"){
                        $hit1 = "hit";
                        $hit2 = "hits";
                    }
                    else{
                        $hit1 = "shoot";
                        $hit2 = "shoots";
                        $yourAmmo = $yourAmmo-1;
                        $yourReloadTime = $yourReloadTime + $yourSecReload;
                    }                    
					//HITCHANCE
					#echo "hitchance: " . $yourChanceHit2 . "<br>";
					if(rand(1,100) <= $yourChanceHit2)
					{
						$hitPart = whereToHit();
						$hitText = $players[0]['name'] . " tries to " . $hit1 . " the " . $hitPart . " of " . $opponentName . " with " . $opponentHes . " " . $yourWepName2;
						//DODGECHANCE
						if(rand(1,100) < (100-$opponentDodgeChance))
						{
										//WEAPON DMG + MODIFIER
										$dmg = rand($yourMinDmg2,$yourMaxDmg2);
                                        
										if (rand(1,100) > (100-$yourCritChance)){
											$dmg = round($dmg * $yourCritDamage2);
                                            if ($dmg < 0){
                                                $dmg = 0;
                                            }
                                            if (($opponentArmour-$yourArmourPenetration) <= 0){
                                                $dmgAbsorbText = "";
                                            }
                                            else{
                                                $dmg = $dmg - ($opponentArmour-$yourArmourPenetration);
                                                $dmgAbsorbText = $opponentName . "'s hide absorbed " . ($opponentArmour-$yourArmourPenetration) . " damage. ";
                                            }
											
											$damageValueText = "The " . $hit1 . " dealt <strong>" . $dmg . " damage!</strong>" . "<br><br>";
											$yourReport .= $players[0]['name'] . " critically " . $hit2 . " the " . $hitPart . " of " . $opponentName . " with " . $yourHes . " " . $yourWepName2 . ". " . $dmgAbsorbText . "<a style=\"color:blue\">" . $damageValueText . "</a>";
											$opponentHp = $opponentHp-$dmg;
										}
										else{
                                            if ($dmg < 0){
                                                $dmg = 0;
                                            }
                                            if (($opponentArmour-$yourArmourPenetration) <= 0){
                                                $dmgAbsorbText = "";
                                            }
                                            else{
                                                $dmg = $dmg - ($opponentArmour-$yourArmourPenetration);
                                                $dmgAbsorbText = $opponentName . "'s hide absorbed " . ($opponentArmour-$yourArmourPenetration) . " damage. ";
                                            }
                                            
											$damageValueText = "The hit dealt <strong>" . $dmg . " damage!</strong>" . "<br><br>";
											$yourReport .= $hitText . ". " . $dmgAbsorbText . "<a style=\"color:blue\">" . $damageValueText . "</a>";
											$opponentHp = $opponentHp-$dmg;
										}
                                        
									}
									
						//SUCCESSFUL DODGE
						else
						{
							$yourReport .= $hitText . ".<br><strong>" . $opponentName . " dodged " . $players[0]['name'] . "'s attack</strong><br><br>";
						}	
						
					}
					//MISS
					else
					{
						$yourReport .= $players[0]['name'] . " misses " . $yourHes . " " . "attack<br><br>";
					}
					}
                    else{
                        $yourReloadTime = $yourReloadTime - $yourSecReload;
                        $yourReport .= $players[0]['name'] . " is reloading<br><br>";
                    }
                    
                    if ($youCanShoot == 1){
                        if($yourAmmo == 0){
                            $yourReport .= $players[0]['name'] . " is out of ammunition!";
                            $youCanShoot = 0;
                        }
                    }
                    
                    
                    
				
				}
function opponentAttack($run){
                    global $players;
					global $yourHp,$firstMove,$yourName, $opponentName, $yourDodgeChance, $opponentAttackMod,$yourHes,
					$opponentChanceHit,$opponentMinDmg,$opponentMaxDmg,$opponentWepName,$opponentCritChance,$opponentCritDamage;
					global $yourLegArmour, $yourArmArmour, $yourChestArmour, $yourHeadArmour,$activeArmour;
					global $dmgReduction;
					global $yourOffHandType,$blockMessage;
					global $opponentNextAttackMiss;
					global $parryMessage,$yourTryParry;
					global $foulMessage;
					global $yourReport,$opponentDmg;
                    global $yourPosition,$opponentPosition;
                    global $youCanShoot,$opponentHp;
                    global $part;
					$firstMove = 0;
					$blockMessage = "";
					$foul = "";
					$hitPart = "";
					$hitText = "";
					$parry = "";
					$blocked = "";
                    $attack = 1;
                    
                    if ($run == 1){
                        $attack = 0;
                        $opponentPosition = $opponentPosition-1;
                        $yourReport .= "<br>" . $opponentName . " runs closer to " . $players[0]['name'] . "<br>";
                        
                        if ($opponentPosition-$yourPosition == 0){
                            $youCanShoot = 0;
                            $attack = 1;
                        }
                    }
                    
                    if ($attack == 1){
    					//HITCHANCE
    					if(rand(1,100) <= 80){
    						if($players[0]['style'] == "shield"){
								$blockSuccess = $players[0]['left_hand']['block_chance'];
								if(mt_rand(1,100) >= $blockSuccess){
									$hitPart = whereToHit();
								}
								else{
									$hitPart = "shield";
									$part = "shield";
								}
							}
							else{
								$hitPart = whereToHit();
							}
    						$hitText = $opponentName . " hits the " . $hitPart . " of " . $players[0]['name'];
    						//DODGECHANCE
    						#echo "TEST" . $players[0]['dodge'] . "TEST";
    						if(rand(1,100) < (100-$players[0]['dodge']))
    						{
    							//FOUL PLAY
    							$foul = opponentFoulPlay();
    							if ($foul !== "true"){  									
    									#NEWSTART
    									
    									if (mt_rand(1,100) <= $players[0]['parry'] && $part != "shield"){
											$countered = 0;
											$parryRoll = mt_rand(1,100);
											if($players[0]['parryType'] == 1){
												#dualwield parry
												if($parryRoll >= 75){
													$countered = 1;
													#counterattack
													if(rand(0,1) == 0){
														$parryHand = "right_hand";
														$attackHand = "left_hand";
													}
													else{
														$parryHand = "left_hand";
														$attackHand = "right_hand";
													}
													$text = $opponentName . " tries to hit the " . $partText . " of " . $players[0]['name'];
													if(mt_rand(1,100) >= $players[0]['critical']){
														$damage = rand($players[0][$attackHand]['min_dmg'],$players[0][$attackHand]['max_dmg']);
														$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0][$parryHand]['name'] . " 
														and counter-attacked with " . $players[0]['hisher'] . " " . $players[0][$attackHand]['name'] . " ignoring armour and </strong><a style=\"color:blue\">dealing " . $damage . " damage!</a><br>";
													}
													else{
														#Critical
														$damage = rand($players[0][$attackHand]['min_dmg'],$players[0][$attackHand]['max_dmg']) * (1 + ($players[0][$attackHand]['crit_dmg'] / 100));
														$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0][$parryHand]['name'] . " 
														and counter-attacked with " . $players[0]['hisher'] . " " . $players[0][$attackHand]['name'] . " ignoring armour and </strong><a style=\"color:blue\">critically dealing " . $damage . " damage!</a><br>";
													}
												}
												else{
													#block 30-50%
													$blockPercent = (rand(30,50))/100;
														#Normal hit
														$text = $opponentName . " tries to hit the " . $hitPart . " of " . $players[0]['name'];
														$damage = rand($opponentMinDmg,$opponentMaxDmg);
														$trueDamage = $damage  * (1-$blockPercent);
														$absorb = round($damage-$trueDamage);
														$damage = round($trueDamage);
														if(rand(0,1) == 0){
															$hand = "right_hand";
														}
														else{
															$hand = "left_hand";
														}
														if($damage <= 0){
															$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0][$hand]['name'] . " 
															and negated the damage</strong><br>";
															$damage = 0;
														}
														else{
															$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0][$hand]['name'] . " 
															the attack broke through but was reduced by " . $absorb . " damage</strong>";
						                                    $damageText .= "<a style=\"color:red\">" . " the hit dealt " . $damage . " damage" . "</a>";
														}
													}
												}
											elseif($players[$target]['parryType'] == 2){
												#2h parry
												if($parryRoll >= 65){
													#block 100%
													$text = $opponentName . " tries to hit the " . $hitPart . " of " . $players[0]['name'];
													$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0]['right_hand']['name'] . " 
													and negated the damage completely!</strong><br>";
													$damage = 0;
												}
												else{
													#block 30-50%
													$blockPercent = (rand(30,50))/100;
													#Normal hit
													$text = $opponentName . " tries to hit the " . $hitPart . " of " . $players[0]['name'];
													$damage = rand($opponentMinDmg,$opponentMaxDmg);
													$trueDamage = $damage  * (1-$blockPercent);
													$absorb = round($damage-$trueDamage);
													$damage = round($trueDamage);
													if($damage <= 0){
														$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0]['right_hand']['name'] . " 
														and negated the damage</strong><br>";
														$damage = 0;
													}
													else{
														$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0]['right_hand']['name'] . ", 
														the attack broke through but was reduced by " . $absorb . " damage</strong>";
					                                    $damageText .= "<a style=\"color:red\">" . " the hit dealt " . $damage . " damage" . "</a>";
													}
												}
											}
											elseif($players[$target]['parryType'] == 0){
												#block 30-50%
												$blockPercent = (rand(30,50))/100;
												#Normal hit
												$text = $opponentName . " tries to hit the " . $hitPart . " of " . $players[0]['name'];
												$damage = rand($opponentMinDmg,$opponentMaxDmg);
												$trueDamage = $damage  * (1-$blockPercent);
												$absorb = round($damage-$trueDamage);
												$damage = round($trueDamage);
												if($damage <= 0){
													$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0]['right_hand']['name'] . " 
													and negated the damage</strong><br>";
													$damage = 0;
												}
												else{
													$damageText = "<strong><br>" . $players[0]['name'] . " parried the attack with " . $players[0]['hisher'] . " " . $players[0]['right_hand']['name'] . ", 
													the attack broke through but was reduced by " . $absorb . " damage</strong>";
				                                    $damageText .= "<a style=\"color:red\">" . " the hit dealt " . $damage . " damage" . "</a>";
												}
											}
											if($countered == 0){
												#THE ACTUAL HIT
												$yourReport .= $text . $damageText . "<br>";
												$players[0]['hp'] = $players[0]['hp'] - $damage;
												
											}
											else{
											#If you got in a counterattack
												$yourReport .= $text . $damageText . "<br>";
												$opponentHp = $opponentHp - $damage;
											}
										}
										else{
    										//WEAPON DMG + MODIFIER
    										if($part == "shield"){
												##HITS THE SHIELD
												$dmg = rand($opponentMinDmg,$opponentMaxDmg) * $opponentAttackMod;
												$trueDamage = $dmg  * (1-($players[0]['block'] / 100));
												$absorb = round($dmg-$trueDamage);
												$dmg = round($trueDamage);
												if($dmg <= 0){
													$damageText = "<strong> " . $players[0]['name'] . "'s " . $players[0]['left_hand']['name'] . " completely negated the damage!</strong><br>";
													$dmg = 0;
												}
												else{
													$damageText = "<strong> " .$players[0]['name'] . "'s " . $players[0]['left_hand']['name'] . " absorbed " . $absorb . " damage</strong>";
				                                    $damageValueText = " the hit dealt " . $dmg . " damage";
													$yourReport .= $hitText . "." . $damageText . "<a style=\"color:red\">" . $damageValueText . "</a><br><br>";
	    											$players[0]['hp'] = $players[0]['hp']-$dmg;
												}
											}
											else{
	    										$dmg = round((rand($opponentMinDmg,$opponentMaxDmg) * $opponentAttackMod));
	    											#$dmgReduction = reduceDamageOpponent($hitPart);
	    											if($players[0][$part]['damage_reduction'] == 0){
	    												$damageText = "<br>" . $players[0]['name'] . " does not have any protection on " . $players[0]['hisher']  . " " . $hitPart . " and is dealt the full damage of " . $players[0]['hisher']  . " " . "opponents attack! ";
	    												$damageValueText = "The hit dealt <strong>" . $dmg . " damage!</strong>" . "<br><br>";
	    											}
	    											else{
	    												if (($dmg-$players[0][$part]['damage_reduction']) <= 0){
	    													$dmg = 0;
	    													$damageText = "<br>" . $players[0]['name'] . " is protected by " . $players[0]['hisher']  . " " . $players[0][$part]['name'] . " which completely negates the damage from " . $players[0]['hisher']  . " " . "opponents attack!" . "<br><br>";
	    												    $damageValueText = "";
	                                                    }
	    												else {
	    													$dmg = $dmg-$players[0][$part]['damage_reduction'];
	    													$damageText = "<br>" . $players[0]['name'] . " is protected by " . $players[0]['hisher'] . " " . $players[0][$part]['name'] . " which soaks up " . $players[0][$part]['damage_reduction'] . " damage from " . $players[0]['hisher'] . " " . "opponents attack! ";
	    													$damageValueText = "The hit dealt <strong>" . $dmg . " damage!</strong>" . "<br><br>";
	    												}
	    											}
	    											
	    											$yourReport .= $hitText . "." . $damageText . "<a style=\"color:red\">" . $damageValueText . "</a>";
	    											$players[0]['hp'] = $players[0]['hp']-$dmg;
	    										}
    										}
    							}
    							//SUCCESSFUL FOUL PLAY
    							else{
    								$yourReport .= $hitText . "." . $foulMessage;
    								$opponentNextAttackMiss = 1;
    							}
    						}
    						//SUCCESSFUL DODGE
    						else
    						{
    							$yourReport .= $hitText . ".<br><strong>" . $players[0]['name'] . " dodged " . $opponentName . "'s attack</strong><br><br>";
    						}	
    						
    					}
    					//MISS
    					else
    					{
    						$yourReport .= $opponentName . " misses " . $yourHes . " " . "attack<br><br>";
    					}
    				
    				}
				}

$i = 1;
//echo "You will surrender at " . $yourSurrenderHp . "<br>";

#var_dump($players);

if($failedEscape != 0){
    $yourReport .= "<br>";
    $yourReport .= $opponentName . " starts by attacking " . $players[0]['name'] . " when " . $yourHes . " during his escape attempt<br>";
    $yourReport .= "The hit dealt " . $failedEscape . " damage!<br><br>";
}

while($players[0]['hp'] > $players[$player]['battleSurrender'] && $opponentHp > 0 && $i < 51){
usleep(60000);
$firstMove = 0;
	$yourDexRoll = $players[0]['dexterity'] + rand(1,20) + $players[0]['initiative'] - $players[0]['weight'];
	$opponentsDexRoll = $opponentDexterity + rand(1,20);
	
	$yourReport .= "<h4>Round " . $i . "</h4>";

	if ($yourDexRoll > $opponentsDexRoll){
	    if($youCanShoot == 1){
                if ($yourPosition < $opponentPosition){
                    $distance = abs($yourPosition-$opponentPosition);
                    $currentHit = $players[0]['secondary']['chance_hit']*(((3-$distance) * 0.2)+1);
                }
                yourAttack($players[$player]['secondary']['name'],$players[$player]['secondary']['min_dmg'],$players[$player]['secondary']['max_dmg'],$players[$player]['secondary']['crit_dmg'],$currentHit,$players[$player]['secondary']['armourPenetration'],"ranged");
            }
        else{
		if ($yourNextAttackMiss == 0){
    			if($yourOffHandType == "weapon"){
    				yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    				if ($yourNextAttackMiss == 0){
    					yourAttack($players[$player]['left_hand']['name'],$players[$player]['left_hand']['min_dmg'],$players[$player]['left_hand']['max_dmg'],$players[$player]['left_hand']['crit_dmg'],$players[$player]['left_hand']['chance_hit'],$players[$player]['left_hand']['armourPenetration'],"melee");
    				}
    			}
    			else{
    				yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    			}
    		}
    		else{
    			$yourNextAttackMiss = 0;
    			$yourReport .= "<br>" . $players[0]['name'] . " is unable to attack because of the previous counterattack of " . $opponentName . "<br><br>";
    
    		} 
    	}
	}
	elseif ($yourDexRoll === $opponentsDexRoll){
		if(rand(0,1) === 0){
		    if($youCanShoot == 1){
                if ($yourPosition < $opponentPosition){
                    $distance = abs($yourPosition-$opponentPosition);
                    $currentHit = $players[0]['secondary']['chance_hit']*(((3-$distance) * 0.2)+1);
                }
                yourAttack($players[$player]['secondary']['name'],$players[$player]['secondary']['min_dmg'],$players[$player]['secondary']['max_dmg'],$players[$player]['secondary']['crit_dmg'],$currentHit,$players[$player]['secondary']['armourPenetration'],"ranged");
            }
            else{
    			if ($yourNextAttackMiss == 0){
    				if($yourOffHandType == "weapon"){
    					yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    					if ($yourNextAttackMiss == 0){
    						yourAttack($players[$player]['left_hand']['name'],$players[$player]['left_hand']['min_dmg'],$players[$player]['left_hand']['max_dmg'],$players[$player]['left_hand']['crit_dmg'],$players[$player]['left_hand']['chance_hit'],$players[$player]['left_hand']['armourPenetration'],"melee");
    					}
    				}
    				else{
    					yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    				}
    			}
    			else{
    				$yourNextAttackMiss = 0;
    				$yourReport .= "<br>" . $players[0]['name'] . " is unable to attack because of the previous counterattack of " . $opponentName . "<br><br>";
			    }
            }
		}
		else{
			if ($opponentNextAttackMiss == 0){
			    if ($yourPosition == $opponentPosition){
			        opponentAttack(0);
			    }
                else{
                    opponentAttack(1);
                }
				
			}
			else{
				$opponentNextAttackMiss = 0;
				$firstMove = 0;
				$yourReport .= "<br>" . $opponentName . " is unable to attack because of the previous counterattack of " . $players[0]['name'] . "<br><br>";
			}
		}
	}
	else {
			if ($opponentNextAttackMiss == 0){
				if ($yourPosition == $opponentPosition){
                    opponentAttack(0);
                }
                else{
                    opponentAttack(1);
                }
			}
			else{
				$opponentNextAttackMiss = 0;
				$firstMove = 0;
				$yourReport .= "<br>" . $opponentName . " is unable to attack because of the previous counterattack of " . $players[0]['name'] . "<br><br>";
			}
	}
		
	if ($firstMove === 1 && $opponentHp > 0)
	{
		if ($opponentNextAttackMiss == 0){
			     if ($yourPosition == $opponentPosition){
                    opponentAttack(0);
                }
                else{
                    opponentAttack(1);
                }
			}
			else{
				$opponentNextAttackMiss = 0;
				$firstMove = 0;
				$yourReport .= "<br>" . $opponentName . " is unable to attack because of the previous counterattack of " . $players[0]['name'] . "<br><br>";
			}
		}
	elseif ($firstMove === 0 && $players[0]['hp'] > $players[$player]['battleSurrender'])
	{
	    if($youCanShoot == 1){
            if ($yourPosition < $opponentPosition){
                $distance = abs($yourPosition-$opponentPosition);
                $currentHit = $players[0]['secondary']['chance_hit']*(((3-$distance) * 0.2)+1);
            }
            yourAttack($players[$player]['secondary']['name'],$players[$player]['secondary']['min_dmg'],$players[$player]['secondary']['max_dmg'],$players[$player]['secondary']['crit_dmg'],$currentHit,$players[$player]['secondary']['armourPenetration'],"ranged");
        }
        else{
    		if ($yourNextAttackMiss == 0){
    			if($yourOffHandType == "weapon"){
    				yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    				if ($yourNextAttackMiss == 0){
    					yourAttack($players[$player]['left_hand']['name'],$players[$player]['left_hand']['min_dmg'],$players[$player]['left_hand']['max_dmg'],$players[$player]['left_hand']['crit_dmg'],$players[$player]['left_hand']['chance_hit'],$players[$player]['left_hand']['armourPenetration'],"melee");
    				}
    			}
    			else{
    				yourAttack($players[$player]['right_hand']['name'],$players[$player]['right_hand']['min_dmg'],$players[$player]['right_hand']['max_dmg'],$players[$player]['right_hand']['crit_dmg'],$players[$player]['right_hand']['chance_hit'],$players[$player]['right_hand']['armourPenetration'],"melee");
    			}
    		}
    		else{
    			$yourNextAttackMiss = 0;
    			$yourReport .= "<br>" . $players[0]['name'] . " is unable to attack because of the previous counterattack of " . $opponentName . "<br><br>";
    		}
        }
	}
	
		if ($opponentHp > 0 && $players[0]['hp'] > $players[$player]['battleSurrender']){
		$yourReport .= "<br>";
	}
	
	$i++;
}

    $yourNameSql = strtolower($players[0]['name']);
    $opponentNameSql = strtolower($opponentName);
    $yourUsername = strtolower($players[0]['username']);
    $yourName = $players[0]['name'];
    $yourHp = $players[0]['hp'];
    
    $dead = 0;
    if($i == 51){
        $win = 0;
    	$yourReport .= "This has gone on long enough, the match is a draw, you leave in disgrace!";
    	$sql = "UPDATE characters SET hp='$players[0]['hp']' WHERE name='$yourNameSql'";
    	mysqli_query($conn,$sql);
    }
    elseif($players[0]['hp'] > $players[$player]['battleSurrender']){
    	$win = 1;
    	$sql = "UPDATE characters SET hp=?, experience = experience + ?, gold = gold + ?, trainingWins=trainingWins+1  WHERE name=?";
    	$stmt = mysqli_prepare($conn,$sql);
    	mysqli_stmt_bind_param($stmt, "iiis", $players[0]['hp'],$opponentXp,$opponentGold,$yourNameSql);
    	mysqli_stmt_execute($stmt);
    	
    	$yourReport .= "<br><a style=\"color:blue\">" . $yourName . " has slain the " . $opponentName . "!</a><br>";
    	$yourReport .= $yourName . " is awarded with " . $opponentGold . " gold, and " . $opponentXp . " experience points!";
    }
    
    elseif($players[0]['hp'] > 0) {
        $win = 0;
        if ($adventure == 1){
            $extraDmg = explode("-", $adventureSurrenderDamage);
            $extraDmg = mt_rand($extraDmg[0],$extraDmg[1]);
            
            $chance = mt_rand(1,100);
            if ($chance > 50){
                $yourReport .= "<br><strong>" . $yourName . " tries to escape but gets hit by " . $opponentName . "!</strong>";
                $yourReport .= "<br><a style=\"color:red\">The hit dealt " . $extraDmg . " damage!</a><br>";
                $players[0]['hp'] = $players[0]['hp'] - $extraDmg;
                if ($players[0]['hp'] < 0){
                    $yourReport .= "That hit dealt the final blow and " . $yourName . " falls lifeless to the ground...";
                    $sql = "UPDATE characters SET hp=0,deadNext=1 WHERE name='$yourNameSql'";
                    mysqli_query($conn,$sql);
                    $dead = 1;  
                }
                $sql = "UPDATE characters SET hp='$yourHp' WHERE name='$yourNameSql'";
                mysqli_query($conn,$sql);
                $yourReport .= "<br><strong>" . $yourName . " escaped!</strong>";
            }
            else{
                $yourReport .= $yourName . " escapes the " . $opponentName;
                $sql = "UPDATE characters SET hp='$yourHp' WHERE name='$yourNameSql'";
                mysqli_query($conn,$sql);
            }
        }
        else{
        	$yourReport .= "<br><a style=\"color:red\">" . $yourName . " gives up after taking too much damage from " . $opponentName . "<br>" .  $yourName . " still had " . 
        	$players[0]['hp'] . " HP left, what a coward!<br>" . $opponentName . " is victorious</a>";
        	$sql = "UPDATE characters SET hp='$yourHp', trainingLosses=trainingLosses+1 WHERE name='$yourNameSql'";
        	mysqli_query($conn,$sql);
        }
    }
    
    elseif($players[0]['hp'] <= 0){
        $win = 0;
    	$yourReport .= "<br><a style=\"color:red\">The " . $opponentName . " has mortally wounded " . $yourName . "</a>";
    	$sql = "UPDATE characters SET hp=0,deadNext=1 WHERE name='$yourNameSql'";
    	mysqli_query($conn,$sql);
        $dead = 1;	
    }
    				
    $reportType = "training";
    $date = date("Y/m/d H:i");
    $sql = "INSERT INTO battlereports (username,yourName, opponentName, date, report,type,win) VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $yourUsername,$yourNameSql,$opponentNameSql,$date,$yourReport,$reportType,$win);
    mysqli_stmt_execute($stmt);
    
    if ($adventure == 0){
    
        $_SESSION['charId'] = $_SESSION['characterProperties']['id'];
        unset($_SESSION['unique']);
        
        echo $yourReport;
        echo "<br>";
        echo "<form role=\"creature\" method=\"post\" action=\"index.php?page=training_results\">
        			<input hidden type=\"text\" name=\"name\" value=\"" . $opponentName . "\"readonly>
        			<input hidden type=\"text\" name=\"yourSurrender\" value=\"" . $_POST['yourSurrender'] . "\"readonly>
        			<input hidden type=\"text\" name=\"rematch\" value=\"true\"readonly>";
        echo "<br><br><button type=\"submit\" class=\"btn btn-default\">Fight again!</button></form>";
        
    }
    else{
        ##If its an adventure fight...
        $battleId = mysqli_insert_id($conn);
        
        
        
            if ($dead == 1){
                echo "You were mortally wounded by " . $opponentName;
                
                echo "<br><br><a href='index.php?page=view-battlereport&battleId=" . $battleId . "' target='_blank'>Click here to view the match (popup)</a>";
                
                echo "<br><br>The character information to the left will be updated once you go to another page";
                $outcome = array("win"=>0,"dead"=>1);
            }
            else{
                if($win == 1){
                    echo "You beat " . $opponentName . " in combat!";
                    $outcome = array("win"=>1,"dead"=>0);
                }
                else{
                    echo "You got beaten by " . $opponentName;
                    $outcome = array("win"=>0,"dead"=>0);
                }
                echo "<br><br><a href='index.php?page=view-battlereport&battleId=" . $battleId . "' target='_blank'>Click here to view the match (popup)</a><br><br>";
                require_once(__ROOT__."/backend/character/update-characterSessions.php");
        
                //TO REFRESH CHARACTERINFO
                echo
                "<script>
                    window.onload = updateChar();
                </script>";
            }
        return $outcome;
        }
}

}

if (isset($_POST['name'])){
    battleBeast(0,$_POST['name'],$_POST['yourSurrender'],0);
}

?>