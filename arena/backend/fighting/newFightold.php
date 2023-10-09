<?php
function fight($t1,$t2,$random,$tournamentId,$tournamentRound){
	global $conn,$players, $team1, $team2,$battleReport;
	global $loserTeam,$loserTeamLevel,$winnerTeamLevel;
    global $battleId;
	global $loseColor,$winColor;
if(!function_exists('getPlayerStats')) {
	function getPlayerStats($name){
		global $conn;
		
		$sql = "SELECT * FROM characters WHERE name=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $name);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$charInfo = mysqli_fetch_assoc($result);
		
		$charId = $charInfo['id'];
		
		$sql = "SELECT username FROM users WHERE character_id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $charId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$username = mysqli_fetch_assoc($result);
		
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
	
	function getBattleStats($player){
		global $loseColor,$winColor;
		$return = array();
		$return['name'] = $player['name'];
		if($player['whichTeam'] == "loser"){
			$return['color'] = $loseColor;
		}
		else{
			$return['color'] = $winColor;
		}
		
		if (!empty($player['stats']['rangedAttacks'])){
			#Ranged
			$rangedHits = 0;
			$rangedCriticals = 0;
			$rangedMisses = 0;
			$rangedDamage = 0;
			$rangedTrueDamage = 0;
		
			foreach($player['stats']['rangedAttacks'] as $item){
				if($item['type'] == "normal"){
					$rangedHits++;
					$rangedDamage = $rangedDamage + $item['damage'];
					$rangedTrueDamage = $rangedTrueDamage + $item['trueDamage'];
				}
				elseif($item['type'] == "critical"){
					$rangedCriticals++;
					$rangedDamage = $rangedDamage + $item['damage'];
					$rangedTrueDamage = $rangedTrueDamage + $item['trueDamage'];
				}
				else{
					$rangedMisses++;
				}
			}
			if(($rangedHits + $rangedCriticals + $rangedMisses) > 0){
				if (($rangedHits + $rangedCriticals) > 0){
					$averageRangedDamage = round($rangedDamage / ($rangedHits + $rangedCriticals),1);
					$averageRangedTrueDamage = round($rangedTrueDamage / ($rangedHits + $rangedCriticals),1);
					
					$return['RangedHits'] = "Normal hits: " . $rangedHits . " | " . "Criticals: " . $rangedCriticals . " | " . "Misses: " . $rangedMisses;
					$return['RangedDamage'] = "Total damage: " . $rangedTrueDamage . "(" . $rangedDamage . " before reductions)<br>Average: " . $averageRangedTrueDamage . "(" . $averageRangedDamage . ")";
				}
				else{
					$return['RangedHits'] = "Normal hits: " . $rangedHits . " | " . "Criticals: " . $rangedCriticals . " | " . "Misses: " . $rangedMisses;
				}
			}
		}
		if (!empty($player['stats']['meleeAttacks'])){
			#Melee
			$meleeHits = 0;
			$meleeCriticals = 0;
			$meleeMisses = 0;
			$meleeDamage = 0;
			$meleeTrueDamage = 0;
			foreach($player['stats']['meleeAttacks'] as $item){
				if($item['type'] == "normal" || $item['type'] == "parry"){
					$meleeHits++;
					$meleeDamage = $meleeDamage + $item['damage'];
					$meleeTrueDamage = $meleeTrueDamage + $item['trueDamage'];
				}
				elseif($item['type'] == "critical"){
					$meleeCriticals++;
					$meleeDamage = $meleeDamage + $item['damage'];
					$meleeTrueDamage = $meleeTrueDamage + $item['trueDamage'];
				}
				else{
					$meleeMisses++;
				}
			}
			if(($meleeHits + $meleeCriticals + $meleeMisses) > 0){
				if (($meleeHits + $meleeCriticals) > 0){
					$averageMeleeDamage = round($meleeDamage / ($meleeHits + $meleeCriticals),1);
					$averageMeleeTrueDamage = round($meleeTrueDamage / ($meleeHits + $meleeCriticals),1);
					
					$return['MeleeHits'] = "Normal hits: " . $meleeHits . " | " . "Criticals: " . $meleeCriticals . " | " . "Misses: " . $meleeMisses;
					$return['MeleeDamage'] = "Total damage: " . $meleeTrueDamage . "(" . $meleeDamage . " before reductions)<br>Average: " . $averageMeleeTrueDamage . "(" . $averageMeleeDamage . ")";
				}
				else{
					$return['MeleeHits'] = "Normal hits: " . $meleeHits . " | " . "Criticals: " . $meleeCriticals . " | " . "Misses: " . $meleeMisses;
				}
			}
		}
		if (!empty($player['stats']['blocks'])){
			#blocks
			$blockCount = 0;
			$blockAmount = 0;
			foreach($player['stats']['blocks'] as $item){
				$blockCount++;
				$blockAmount = $blockAmount + $item['damage'];
			}
			if ($blockCount > 0 ){
				$averageBlockAmount = round($blockAmount / $blockCount,1);
				$return['blocks'] = "Times blocked: " . $blockCount . " | Damage blocked: " . $blockAmount . " | Average: " . $averageBlockAmount;
			}
		}
		
		if (!empty($player['stats']['parries'])){
			#parries
			$parryCount = 0;
			$parryAmount = 0;
			foreach($player['stats']['parries'] as $item){
				$parryCount++;
				$parryAmount = $parryAmount + $item['blockedDamage'];
			}
			if ($parryCount > 0 ){
				$averageParryAmount = round($parryAmount / $parryCount,1);
				$return['parries'] = "Times parried: " . $parryCount . " | Damage blocked: " . $parryAmount . " | Average: " . $averageParryAmount;
			}
		}
		
		if (!empty($player['stats']['fouls'])){
			#fouls
			$foulCount = 0;
			foreach($player['stats']['fouls'] as $item){
				$foulCount++;
			}
			if ($foulCount > 0){
				$return['fouls'] = "Times foul played: " . $foulCount;
			}
		}
		
		if ($player['stats']['dodges'] > 0){
			$return['dodges'] = "Times dodged: " . $player['stats']['dodges'];
		}
		
		if (!empty($player['stats']['armourSoak'])){
			#armour
			$armourCount = 0;
			$armourReduction = 0;
			foreach($player['stats']['armourSoak'] as $item){
				$armourCount++;
				$armourReduction = $armourReduction + $item['damage'];
			}
			if($armourCount > 0){
				$averageArmourReduction = round($armourReduction / $armourCount,1);
				$return['armour'] = "Damage absorbed: " . $armourReduction . " | Average: " . $averageArmourReduction; 
			}
		}
		return $return;
	}
	function initiativeRoll($dex,$initiative){
		return mt_rand(0,20) + $dex + $initiative;
	}

	function startingPositions(){
		global $players,$battleReport;
		$iPlayer = 0;
		foreach($players as $player){
			if ($player['hp'] > $player['battleSurrender']){
				$initiativeRolls[$iPlayer] = initiativeRoll($players[$iPlayer]['dexterity'],$players[$iPlayer]['initiative']);
				$iPlayer++;
			}
			else{
				$initiativeRolls[$iPlayer] = "nothing";
				unset($initiativeRolls[$iPlayer]);
				$iPlayer++;
			}
		}

		arsort($initiativeRolls);
		
		
		$startOrder = array();
		foreach ($initiativeRolls as $key => $val){
			array_push($startOrder,$key);
		}
		return $startOrder;
	}
	
	function whereToHit(){
		global $part;
		
		
		$whereToHit = rand(1,18);
		switch ($whereToHit) {
			case 1:
			case 2:
				$part = "leg";
				return "right leg";
				break;
			case 3:
			case 4:
				$part = "leg";
				return "left leg";
				break;
			case 5:
			case 6:
				$part = "arm";
				return "right arm";
				break;		
			case 7:
			case 8:
				$part = "arm";
				return "left arm";
				break;
			case 9:
			case 10:
			case 11:
			case 12:
			case 13:
			case 14:
			case 15:
			case 16:
				$part = "chest";
				return "chest";
				break;
			case 17:
			case 18:
				$part = "head";
				return "head";
				break;
		}
	}
	
	function flavorText($type,$attackType,$partText,$player,$target){
		global $players,$part;
		
		
		
		if ($type == "normal"){
			$wepType = $players[$player][$attackType]['item_type'];
			
			if ($wepType == "swords" || $wepType == "spears" || $wepType == "greatswords" || $wepType == "daggers"){
				$wepText = " thrusts " . $players[$player]['hisher'] . " ";
			}
			elseif($wepType == "axes" || $wepType == "battleaxes" || $wepType == "clubs" || $wepType == "largeclubs" || $wepType == "nothing"){
				$wepText = " swings " . $players[$player]['hisher'] . " ";
			}
						
			$roll = mt_rand(0,3);
			switch ($roll){
				case 0: 
					return $players[$player]['name'] . " hits the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
				case 1:
					return $players[$player]['name'] . " strikes the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
				case 2:
					return $players[$player]['name'] . "'s " . $players[$player][$attackType]['name'] . " gets to taste the " . $partText . " of " . $players[$target]['name'];
				case 3:
					return $players[$player]['name'] . " " . $wepText . $players[$player][$attackType]['name'] . " towards the " . $partText . " of " . $players[$target]['name'];
			}
		
		}
		elseif($type == "critical"){
			$wepType = $players[$player][$attackType]['item_type'];
			$roll = mt_rand(0,1);
			
			switch ($roll){
				case 0: 
					return $players[$player]['name'] . " critically hits the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
				case 1:
					return $players[$player]['name'] . " critically strikes the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
			}
		}
		elseif($type == "dodge"){
			$roll = mt_rand(0,1);
			
			switch ($roll){
				case 0: 
					return $players[$target]['name'] . " dodged the attack of " . $players[$player]['name'] . "!";
				case 1:
					return $players[$target]['name'] . " barely managed to dodge the attack of " . $players[$player]['name'] . "!";
				
			}
		}
        elseif($type == "normalRanged"){
            if ($players[$player]['secondary']['type'] == "bow"){
                $projectile = "an arrow";
            }
            else{
                $projectile = "a bolt";
            }
            
            return $players[$player]['name'] . " shoots " . $projectile . " from " . $players[$player]['hisher'] . " " . $players[$player]['secondary']['name'] . " it hits the " . $partText . " of " . $players[$target]['name'];
        }
        elseif($type == "criticalRanged"){
            if ($players[$player]['secondary']['type'] == "bow"){
                $projectile = "an arrow";
            }
            else{
                $projectile = "a bolt";
            }
            return $players[$player]['name'] . " shoots " . $projectile . " from " . $players[$player]['hisher'] . " " . $players[$player]['secondary']['name'] . " it critically hits the " . $partText . " of " . $players[$target]['name'];
        }
        elseif($type == "dodgeRanged"){
            if ($players[$player]['secondary']['type'] == "bow"){
                $projectile = "an arrow";
            }
            else{
                $projectile = "a bolt";
            }
            return $players[$player]['name'] . " shoots " . $projectile . " from " . $players[$player]['hisher'] . " " . $players[$player]['secondary']['name'] . ". " . $players[$target]['name'] . "'s lightning reflexes caused " . $players[$target]['himher'] . " to dodge it!";
        }
        elseif($type == "blockRanged"){
            if ($players[$player]['secondary']['type'] == "bow"){
                $projectile = "an arrow";
            }
            else{
                $projectile = "a bolt";
            }
            return $players[$player]['name'] . " shoots " . $projectile . " from " . $players[$player]['hisher'] . " " . $players[$player]['secondary']['name'] . ". it hits the shield of " . $players[$target]['name'];
            
        }
		
	}
	
    
    
    
	function attack($player,$attackType,$target){
	    usleep(50000);
		global $players, $battleReport, $part, $whichTeam,$otherTeam;
		
		
		#HIT
		if(mt_rand(1,100) <= round($players[$player][$attackType]['chance_hit'])){
			if($players[$player][$attackType]['chance_hit'] > 100){
				$blockPenalty = round(($players[$player][$attackType]['chance_hit'] - 100) / 2);
			}
			else{
				$blockPenalty = 0;
			}
			
			//SHIELD HIT?
			if($players[$target]['style'] == "shield"){
				$blockSuccess = $players[$target]['left_hand']['block_chance'];
				if (($blockSuccess - $blockPenalty) < round($blockSuccess/2)){
					$blockSuccess = $blockSuccess / 2;
				}
				else{
					$blockSuccess = round($blockSuccess-$blockPenalty);
				}
				if(mt_rand(1,100) >= $blockSuccess){
					$partText = whereToHit();
				}
				else{
					$partText = "shield";
					$part = "shield";
				}
			}
			else{
				$partText = whereToHit();
			}
			#DODGE FAIL
			if ($players[$target]['dodged'] == $players[$player]['name']){
                $dodgeChance = 0;
            }
            else{
    			if ($players[$target]['dodge'] != 0){
    				if (($players[$target]['dodge'] - ($blockPenalty)) < round($players[$target]['dodge']/2)){
    					$dodgeChance = round($players[$target]['dodge']/2);
    				}
    				else{
    					$dodgeChance = round(($players[$target]['dodge'] - ($blockPenalty)));
    				}
    			}
    			else{
    				$dodgeChance = 0;
    			}
			}
            #echo "<br> DODGE: " . $dodgeChance . "<br>";
			if(mt_rand(1,100) >= $dodgeChance){
				#FOUL FAIL
				if ($players[$target]['foul_play'] != 0){
					if (($players[$target]['foul_play'] - ($blockPenalty)) < round($players[$target]['foul_play']/2)){
						$foulChance = round($players[$target]['foul_play']/2);
					}
					else{
						$foulChance = round(($players[$target]['foul_play'] - ($blockPenalty)));
					}
				}
				else{
				$foulChance = 0;
                    
				}
                #echo "<br> FOUL: " . $foulChance . "<br>";
				if(mt_rand(1,100) >= $foulChance){
					
					#PARRY FAIL
					if ($players[$target]['parried'] == $players[$player]['name']){
                        $parryChance = 0;
                    }
                    else{
						if ($players[$target]['parry'] != 0){
							if (($players[$target]['parry'] - ($blockPenalty)) < round($players[$target]['parry']/2)){
								$parryChance = round($players[$target]['parry']/2);
							}
							else{
								$parryChance = round(($players[$target]['parry'] - ($blockPenalty)));
							}
						}
						else{
						$parryChance = 0;
						}
                    }
                    #echo "<br> PARRY: " . $parryChance . "<br>";
					if (mt_rand(1,100) <= $parryChance && $part != "shield"){
						$countered = 0;
						$parryRoll = mt_rand(1,100);
						if($players[$target]['parryType'] == 1){
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
								$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
								if(mt_rand(1,100) >= $players[$target]['critical']){
									$parryCrit = 0;
									$damage = rand($players[$target][$attackHand]['min_dmg'],$players[$target][$attackHand]['max_dmg']);
									$originalDamage = $damage;
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$parryHand]['name'] . " 
									and counter-attacked with " . $players[$target]['hisher'] . " " . $players[$target][$attackHand]['name'] . " ignoring armour and </strong><a class='" . $otherTeam . "'>dealing " . $damage . " damage!</a><br>";
								}
								else{
									#Critical
									$parryCrit = 1;
									$damage = rand($players[$target][$attackHand]['min_dmg'],$players[$target][$attackHand]['max_dmg']) * (1 + ($players[$target][$attackHand]['crit_dmg'] / 100));
									$originalDamage = $damage;
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$parryHand]['name'] . " 
									and counter-attacked with " . $players[$target]['hisher'] . " " . $players[$target][$attackHand]['name'] . " ignoring armour and </strong><a class='" . $otherTeam . "'>critically dealing " . $damage . " damage!</a><br>";
								}
								$opponentDamage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
								$players[$target]['stats']['parries'][] = array("type"=>"counterAttack","damageDealt"=>$damage,"blockedDamage"=>$opponentDamage);
							}
							else{
								#block 30-50%
								$blockPercent = (rand(30,50))/100;
								if(mt_rand(1,100) >= $players[$player]['critical']){
									#Normal hit
									$parryCrit = 0;
									$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
									$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
									$originalDamage = $damage;
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
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$hand]['name'] . " 
										and negated the damage</strong><br>";
										$damage = 0;
									}
									else{
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$hand]['name'] . " 
										the attack broke through but was reduced by " . $absorb . " damage</strong>";
	                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
									}
								}
								else{
									#Critical
									$parryCrit = 1;
									$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
									$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100));
									$originalDamage = $damage;
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
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$hand]['name'] . " 
										and negated the damage</strong><br>";
										$damage = 0;
									}
									else{
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target][$hand]['name'] . ", 
										the attack broke through but was reduced by " . $absorb . " damage</strong>";
	                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit was a critical and dealt " . $damage . " damage" . "</a>";
									}
								}
								$players[$target]['stats']['parries'][] = array("type"=>"normalParry","damageDealt"=>0,"blockedDamage"=>$absorb);
							}
						}
						elseif($players[$target]['parryType'] == 2){
							#2h parry
							if($parryRoll >= 60){
								#block 100%
								$countered = 3;
								$parryCrit = 0;
								$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
								$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . " 
								and negated the damage completely!</strong><br>";
								$damage = 0;
								$opponentDamage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
								$players[$player]['stats']['meleeAttacks'][] = array("type"=>"parry","damage"=>$opponentDamage,"trueDamage"=>0);  
								$players[$target]['stats']['parries'][] = array("type"=>"fullParry","damageDealt"=>0,"blockedDamage"=>$opponentDamage);
							}
							else{
								#block 30-50%
								$blockPercent = (rand(30,50))/100;
								if(mt_rand(1,100) >= $players[$player]['critical']){
									#Normal hit
									$parryCrit = 0;
									$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
									$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
									$originalDamage = $damage;
									$trueDamage = $damage  * (1-$blockPercent);
									$absorb = round($damage-$trueDamage);
									$damage = round($trueDamage);
									if($damage <= 0){
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . " 
										and negated the damage</strong><br>";
										$damage = 0;
									}
									else{
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . ", 
										the attack broke through but was reduced by " . $absorb . " damage</strong>";
	                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
									}
								}
								else{
									#Critical
									$parryCrit = 1;
									$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
									$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100));
									$originalDamage = $damage;
									$trueDamage = $damage  * (1-$blockPercent);
									$absorb = round($damage-$trueDamage);
									$damage = round($trueDamage);
									if($damage <= 0){
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . " 
										and negated the damage</strong><br>";
										$damage = 0;
									}
									else{
										$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . ", 
										the attack broke through but was reduced by " . $absorb . " damage</strong>";
	                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit is a critical and dealt " . $damage . " damage" . "</a>";
									}
								}
								$players[$target]['stats']['parries'][] = array("type"=>"normalParry","damageDealt"=>0,"blockedDamage"=>$absorb);
							}
							
						}
						elseif($players[$target]['parryType'] == 0){
							#1h parry
							#block 30-50%
							$blockPercent = (rand(30,50))/100;
							if(mt_rand(1,100) >= $players[$player]['critical']){
								#Normal hit
								$parryCrit = 0;
								$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
								$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
								$originalDamage = $damage;
								$trueDamage = $damage  * (1-$blockPercent);
								$absorb = round($damage-$trueDamage);
								$damage = round($trueDamage);
								if($damage <= 0){
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . " 
									and negated the damage</strong><br>";
									$damage = 0;
								}
								else{
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . ", 
									the attack broke through but was reduced by " . $absorb . " damage</strong>";
                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
								}
							}
							else{
								#Critical
								$parryCrit = 1;
								$text = $players[$player]['name'] . " tries to hit the " . $partText . " of " . $players[$target]['name'] . " with " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'];
								$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100));
								$originalDamage = $damage;
								$trueDamage = $damage  * (1-$blockPercent);
								$absorb = round($damage-$trueDamage);
								$damage = round($trueDamage);
								if($damage <= 0){
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . " 
									and negated the damage</strong><br>";
									$damage = 0;
								}
								else{
									$damageText = "<strong><br>" . $players[$target]['name'] . " parried the attack with " . $players[$target]['hisher'] . " " . $players[$target]['right_hand']['name'] . ", 
									the attack broke through but was reduced by " . $absorb . " damage</strong>";
                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit is a critical and dealt " . $damage . " damage" . "</a>";
								}
							}
							$players[$target]['stats']['parries'][] = array("type"=>"normalParry","damageDealt"=>0,"blockedDamage"=>$absorb);
						}
						if ($parryCrit == 0){
							$meleeType = "normal";
						}
						else{
							$meleeType = "critical";
						}
						if($countered == 0){
							#THE ACTUAL HIT
							$battleReport .= $text . $damageText;
							$players[$target]['hp'] = $players[$target]['hp'] - $damage;
							$players[$player]['stats']['meleeAttacks'][] = array("type"=>$meleeType,"damage"=>$originalDamage,"trueDamage"=>$damage);  
							$dead = checkSurrender($players[$player]['name']);
							if ($dead == "dead"){
								if(isset($players[$player]['killReward'])){
									$players[$player]['killReward'] = $players[$player]['killReward'] + 1;
								}
								else{
									$players[$player]['killReward'] = 1;
								}
							}
						}
						elseif($countered == 1){
						#If you got in a counterattack
							$battleReport .= $text . $damageText;
							$players[$player]['hp'] = $players[$player]['hp'] - $damage;
							$players[$target]['stats']['meleeAttacks'][] = array("type"=>$meleeType,"damage"=>$originalDamage,"trueDamage"=>$damage);  
							$dead = checkSurrender($players[$target]['name']);
							if ($dead == "dead"){
								if(isset($players[$target]['killReward'])){
									$players[$target]['killReward'] = $players[$target]['killReward'] + 1;
								}
								else{
									$players[$target]['killReward'] = 1;
								}
							}
						}
						else{
							$battleReport .= $text . $damageText;
						}
						
					}
					else{
						#NORMAL DAMAGE
						if(mt_rand(1,100) >= $players[$player]['critical']){
							if($part == "shield"){
								##HITS THE SHIELD
								$text = flavorText("normal",$attackType,$partText,$player,$target);
								$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
								$originalDamage = $damage;
								$trueDamage = $damage  * (1-($players[$target]['block'] / 100));
								$absorb = round($damage-$trueDamage);
								$players[$target]['stats']['blocks'][] = array("type"=>"block","damage"=>$absorb);
								$damage = round($trueDamage);
								if($damage <= 0){
									$damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " completely negated the damage!</strong><br>";
									$damage = 0;
								}
								else{
									$damageText = "<strong>" .$players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " absorbed " . $absorb . " damage</strong>";
                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
								}
							}
							else{
								$text = flavorText("normal",$attackType,$partText,$player,$target);
								$damage = round(rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']));
								$originalDamage = $damage;
								if(isset($players[$target][$part]['damage_reduction'])){
	                                $damageReduction = $players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'];
		                            if ($damageReduction <= 0){
		                            	$damageReduction = 0;
                                        $damageText = "<strong>" .$players[$player]['name'] . "'s " . "attack completely went through " . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . "</strong><br>";
                                        $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
	                                }
                                    else{
                                        if(($damage - ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'])) <= 0){
                                            $damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " completely negated the damage!</strong><br>";
                                            $damage = 0;
                                        }
                                        else{
                                            $damage = $damage-($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']);
                                            if ($damage <= 0){
                                                $damage = 0;
                                            }
                                            $damageText = $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " soaked up " . 
                                            ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']) . " damage," . "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage!" . "</a>";
                                        }
                                    }
									$players[$target]['stats']['armourSoak'][] = array("part"=>$part,"damage"=>$damageReduction);
								}
								else{
									$damageText = "<a class='" . $whichTeam . "'>" . "the hit dealt " . $damage . " damage" . "</a>";
								}			
								
							}
							$players[$player]['stats']['meleeAttacks'][] = array("type"=>"normal","damage"=>$originalDamage,"trueDamage"=>$damage);  
						}
							
							
						#CRITICAL
						else{
							if($part == "shield"){
								##HITS THE SHIELD
								$text = flavorText("critical",$attackType,$partText,$player,$target);
								$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100));
								$originalDamage = $damage;
								$trueDamage = $damage  * (1-($players[$target]['block'] / 100));
								$absorb = round($damage-$trueDamage);
								$players[$target]['stats']['blocks'][] = array("type"=>"block","damage"=>$absorb);
								$damage = round($trueDamage);
								if($damage <= 0){
									$damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " completely negated the damage!</strong><br>";
									$damage = 0;
								}
								else{
									$damageText = "<strong>" .$players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " absorbed " . $absorb . " damage</strong>";
                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
								}
							}
							else{
								$text = flavorText("critical",$attackType,$partText,$player,$target);
								$damage = round(rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100)));
								$originalDamage = $damage;
								if(isset($players[$target][$part]['damage_reduction'])){
	                                $damageReduction = $players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'];
		                            if ($damageReduction <= 0){
		                            	$damageReduction = 0;
                                        $damageText = "<strong>" .$players[$player]['name'] . "'s " . "attack completely went through " . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . "</strong><br>";
                                        $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
	                                }
                                    else{
                                        if(($damage - ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'])) <= 0){
                                            $damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " completely negated the damage!</strong><br>";
                                            $damage = 0;
                                        }
                                        else{
                                            $damage = $damage-($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']);
                                            if ($damage <= 0){
                                                $damage = 0;
                                            }
                                            $damageText = $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " soaked up " . 
                                            ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']) . " damage," . "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage!" . "</a>";
                                        }
                                    }
									$players[$target]['stats']['armourSoak'][] = array("part"=>$part,"damage"=>$damageReduction);
	                            }
								else{
									$damageText = "<a class='" . $whichTeam . "'>" . "the hit dealt " . $damage . " damage" . "</a>";
								}			
							}
							$players[$player]['stats']['meleeAttacks'][] = array("type"=>"critical","damage"=>$originalDamage,"trueDamage"=>$damage);  
						}
						#THE ACTUAL HIT
						$battleReport .= $text . ", " . $damageText;
						$players[$target]['hp'] = $players[$target]['hp'] - $damage;
						
						$dead = checkSurrender($players[$player]['name']);
						if ($dead == "dead"){
							if(isset($players[$player]['killReward'])){
								$players[$player]['killReward'] = $players[$player]['killReward'] + 1;
							}
							else{
								$players[$player]['killReward'] = 1;
							}
						}
					#}
					#PARRY SUCCESS
					#else{
					#	$battleReport .= "<strong>" . $players[$target]['name'] . " parries the attack of " . $players[$player]['name'] . "</strong>";
					#	$players[$target]['parried'] = $players[$player]['name'];
					}
				}
				#FOUL SUCCESS
				else{
					$battleReport .= "<strong>" . $players[$target]['name'] . " dodges the attack of " . $players[$player]['name'] . " and throws some sand in " . $players[$player]['hisher'] . 
					" eyes</strong>";
					$players[$player]['disabled'] = true; 
					$players[$target]['stats']['fouls']++;
				}
			}
			#DODGE SUCCESS
			else{
				$text = flavorText("dodge","none","none",$player,$target);
				$battleReport .= "<strong>" . $text . "</strong>";
                $players[$target]['dodged'] = $players[$player]['name'];
                $players[$target]['stats']['dodges']++;
			}
		}
		#MISS
		else {
			$battleReport .= "<strong>" . $players[$player]['name'] . " missed his attack" . "</strong>";
			$players[$player]['stats']['meleeAttacks'][] = array("type"=>"miss","damage"=>0);
		}
		$battleReport .= "<br>";
	}
	
	function chooseTarget($player,$type){
		global $team1, $team2, $whichTeam,$otherTeam,$rangedDistance,$players;
		
		#Choose a random target
		if ($type == "melee"){
		    $targets = array();
			if (in_array($player,$team1)){
			    foreach ($team2 as $targeted){
    			    $distance = abs($players[$player]['position']-$players[$targeted]['position']);
                    if ($distance == 0){
                        array_push($targets,$targeted);
                    }
                }
                if(!empty($targets)){
                    $target = $targets[array_rand($targets)];
                    $whichTeam = "teamOne";
					$otherTeam = "teamTwo";
                }
			}
			else{
			    foreach ($team1 as $targeted){
                    $distance = abs($players[$player]['position']-$players[$targeted]['position']);
                    if ($distance == 0){
                        array_push($targets,$targeted);
                    }
                }
                if(!empty($targets)){
                    $target = $targets[array_rand($targets)];
                    $whichTeam = "teamTwo";
					$otherTeam = "teamOne";
                }
			}
        }
        elseif($type == "ranged"){
            $oneDist = array();
            $twoDist = array();
            $threeDist = array();
            
            if (in_array($player,$team1)){
                foreach ($team2 as $targeted){
                    $distance = abs($players[$player]['position']-$players[$targeted]['position']);
                        if ($distance == 1){
                            array_push($oneDist,$targeted);
                        }
                        elseif($distance == 2){
                            array_push($twoDist,$targeted);
                        }
                        else{
                            array_push($threeDist,$targeted);
                        }
                    }
                if(!empty($oneDist)){
                    
                    $target = $oneDist[array_rand($oneDist)];
                    $whichTeam = "teamOne";
                    $otherTeam = "teamTwo";
                    $rangedDistance = 1;
                }
                elseif(!empty($twoDist)){
                    $target = $twoDist[array_rand($twoDist)];
                    $whichTeam = "teamOne";
					$otherTeam = "teamTwo";
                    $rangedDistance = 2;
                }
                elseif(!empty($threeDist)){
                    $target = $threeDist[array_rand($threeDist)];
                    $whichTeam = "teamOne";
					$otherTeam = "teamTwo";
                    $rangedDistance = 3;
                }
            }
            else{
                foreach ($team1 as $targeted){
                    $distance = abs($players[$player]['position']-$players[$targeted]['position']);
                        if ($distance == 1){
                            array_push($oneDist,$targeted);
                        }
                        elseif($distance == 2){
                            array_push($twoDist,$targeted);
                        }
                        else{
                            array_push($threeDist,$targeted);
                        }
                    }
                if(!empty($oneDist)){
                    $target = $oneDist[array_rand($oneDist)];
                    $whichTeam = "teamTwo";
					$otherTeam = "teamOne";
                    $rangedDistance = 1;
                }
                elseif(!empty($twoDist)){
                    $target = $twoDist[array_rand($twoDist)];
                    $whichTeam = "teamTwo";
					$otherTeam = "teamOne";
                    $rangedDistance = 2;
                }
				elseif(!empty($threeDist)){
                    $target = $threeDist[array_rand($threeDist)];
                    $whichTeam = "teamTwo";
					$otherTeam = "teamOne";
                    $rangedDistance = 3;
                }
            }
        }
		
        if (isset($target)){
            return $target;
        }
	}
	
    function chooseAction ($player){
        global $players, $team1, $team2;
        if (in_array($player,$team1)){
            $opposingTeam = $team2;   
        }
        else{
            $opposingTeam = $team1;
        }
        if ($players[$player]['canShoot'] == 1){
            foreach ($opposingTeam as $oPlayer){
                if ($players[$oPlayer]['position'] == $players[$player]['position']){
                    return "attack";
                }
            }
            return "shoot";
            exit;                
        }
        else{
            foreach ($opposingTeam as $oPlayer){
                if ($players[$oPlayer]['position'] == $players[$player]['position']){
                    return "attack";
                }
                else{
                    if ($players[$player]['secondary']['ammo'] > 0){
                        $players[$player]['canShoot'] = 1;
                        return "shoot";
                    }
                }
            }
            return "run";
        }
    }
    
    function meleeAttack($player){
        global $players,$battleReport,$whichTeam,$otherTeam;
        
        $target = chooseTarget($player,"melee");
        if (!isset($target)){
            $target = chooseTarget($player,"ranged");
            if(isset($target)){
                rangedAttack($player);
            }
            break;
        }
        if($players[$player]['style'] == "dualwield"){
            if($players[$player]['right_hand']['type'] == "2h"){
                attack($player,"right_hand",$target);
            }
            else{
                attack($player,"right_hand",$target);
                if ($players[$player]['disabled'] == false){
                    if($players[$target]['hp'] > $players[$target]['battleSurrender']){
                        attack($player,"left_hand",$target);
                    }
                    else{
                        $target = chooseTarget($player,"melee");
                        if (!isset($target)){
                            #break;
                        }
                        else{
                            attack($player,"left_hand",$target);
                        }
                    }
                    
                    }
                    else{
                        $battleReport .= $players[$player]['name'] . " is still disabled from the previous counterattack<br>";
                }
            }
        }
        elseif($players[$player]['style'] == "shield" || $players[$player]['style'] == "singleWield"){
            attack($player,"right_hand",$target);
        }
    }

    function rangedAttack($player){
        usleep(50000);
        global $players, $battleReport, $part,$rangedDistance,$whichTeam,$otherTeam;
        $attackType = "secondary";
        if ($players[$player][$attackType]['reload'] > 0){
            $players[$player][$attackType]['reload'] = $players[$player][$attackType]['reload']-1;
            $battleReport .= $players[$player]['name'] . " is reloading " . $players[$player]['hisher'] . " " . $players[$player][$attackType]['name'] . "<br>";
        }
        else{
            $target = chooseTarget($player,"ranged");
            if (isset($target)){
	            if ($rangedDistance == 1){
	                $hitChance = round($players[$player][$attackType]['chance_hit'] * 1.4);
	            }
	            elseif($rangedDistance == 2){
	                $hitChance = round($players[$player][$attackType]['chance_hit'] * 1.2);
	            }
	            else{
	                $hitChance = $players[$player][$attackType]['chance_hit'];
	            }
	            #HIT
	            if(mt_rand(1,100) <= $hitChance){
	                if($hitChance > 100){
	                    $blockPenalty = round(($hitChance - 100) / 2);
	                }
	                else{
	                    $blockPenalty = 0;
	                }
	                if ($players[$target]['canShoot'] == 1){
	                    $canBlock = 0;
	                }
	                else{
	                    $canBlock = 1;
	                }
					if($canBlock == 1){
		                //SHIELD HIT?
						if($players[$target]['style'] == "shield"){
							$blockSuccess = $players[$target]['left_hand']['block_chance'];
							if (($blockSuccess - $blockPenalty) < round($blockSuccess/2)){
								$blockSuccess = $blockSuccess / 2;
							}
							else{
								$blockSuccess = round($blockSuccess-$blockPenalty);
							}
							if(mt_rand(1,100) >= $blockSuccess){
								$partText = whereToHit();
							}
							else{
								$partText = "shield";
								$part = "shield";
							}
						}
						else{
							$partText = whereToHit();
						}
					}
					else{
						$partText = whereToHit();
					}
	                #DODGE FAIL
	                if ($players[$target]['dodge'] != 0){
	                    if (($players[$target]['dodge'] - ($blockPenalty)) < round($players[$target]['dodge']/2)){
	                        $dodgeChance = round($players[$target]['dodge']/2);
	                    }
	                    else{
	                        $dodgeChance = round(($players[$target]['dodge'] - ($blockPenalty)));
	                    }
	                }
	                else{
	                    $dodgeChance = 0;
	                }
	                
	                if(mt_rand(1,100) >= round($dodgeChance/2)){
	                        #NORMAL DAMAGE
	                    if(mt_rand(1,100) >= $players[$player]['critical']){
	                    	if($part == "shield"){
									##HITS THE SHIELD
									$text = flavorText("normalRanged",$attackType,$partText,$player,$target);
									$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']);
									$originalDamage = $damage;
									$trueDamage = $damage  * (1-($players[$target]['block'] / 100));
									$absorb = round($damage-$trueDamage);
									$players[$target]['stats']['blocks'][] = array("type"=>"block","damage"=>$absorb);
									$damage = round($trueDamage);
									if($damage <= 0){
										$damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " completely negated the damage!</strong><br>";
										$damage = 0;
									}
									else{
										$damageText = "<strong>" .$players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " absorbed " . $absorb . " damage</strong>";
	                                    $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
									}
								}
							else{
		                        $text = flavorText("normalRanged",$attackType,$partText,$player,$target);
		                        $damage = round(rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']));
								$originalDamage = $damage;
		                        if(isset($players[$target][$part]['damage_reduction'])){
		                        	$damageReduction = $players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'];
		                            if ($damageReduction <= 0){
		                            	$damageReduction = 0;
		                                $damageText = "<strong>" .$players[$player]['name'] . "'s " . "attack completely went through " . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . "</strong><br>";
		                                $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
		                            }
		                            else{
		                                if(($damage - ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'])) <= 0){
		                                    $damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " completely negated the damage!</strong><br>";
		                                    $damage = 0;
		                                }
		                                else{
		                                    $damage = ($damage-$players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']);
		                                    if ($damage <= 0){
		                                        $damage = 0;
		                                    }
		                                    $damageText = $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " soaked up " . 
		                                    ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']) . " damage," . "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage!" . "</a>";
		                                }
										
		                            }
									$players[$target]['stats']['armourSoak'][] = array("part"=>$part,"damage"=>$damageReduction);
		                        }
		                        else{
		                            $damageText = "<a class='" . $whichTeam . "'>" . "the hit dealt " . $damage . " damage" . "</a>";
		                        } 
		                    }
							$players[$player]['stats']['rangedAttacks'][] = array("type"=>"normal","damage"=>$originalDamage,"trueDamage"=>$damage);    
	                    }
	                    #CRITICAL
	                    else{
	                    	if($part == "shield"){
								##HITS THE SHIELD
								$text = flavorText("critical",$attackType,$partText,$player,$target);
								$damage = rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100));
								$originalDamage = $damage;
								$trueDamage = $damage  * (1-($players[$target]['block'] / 100));
								$absorb = round($damage-$trueDamage);
								$players[$target]['stats']['blocks'][] = array("type"=>"block","damage"=>$absorb);
								$damage = round($trueDamage);
								if($damage <= 0){
									$damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " completely negated the damage!</strong><br>";
									$damage = 0;
								}
								else{
									$damageText = "<strong>" .$players[$target]['name'] . "'s " . $players[$target]['left_hand']['name'] . " absorbed " . $absorb . " damage</strong>";
	                                $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
								}
							}
							else{
		                        $text = flavorText("criticalRanged",$attackType,$partText,$player,$target);
		                        $damage = round(rand($players[$player][$attackType]['min_dmg'],$players[$player][$attackType]['max_dmg']) * (1 + ($players[$player][$attackType]['crit_dmg'] / 100)));
		                        $originalDamage = $damage;
		                        if(isset($players[$target][$part]['damage_reduction'])){
		                            $damageReduction = $players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'];
		                            if ($damageReduction <= 0){
		                            	$damageReduction = 0;
		                                $damageText = "<strong>" .$players[$player]['name'] . "'s " . "attack completely went through " . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . "</strong><br>";
		                                $damageText .= "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage" . "</a>";
		                            }
		                            else{
		                                if(($damage - ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration'])) <= 0){
		                                    $damageText = "<strong>" . $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " completely negated the damage!</strong><br>";
		                                    $damage = 0;
		                                }
		                                else{
		                                    $damage = $damage-($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']);
		                                    if ($damage <= 0){
		                                        $damage = 0;
		                                    }
		                                    $damageText = $players[$target]['name'] . "'s " . $players[$target][$part]['name'] . " soaked up " . 
		                                    ($players[$target][$part]['damage_reduction']-$players[$player][$attackType]['armourPenetration']) . " damage," . "<a class='" . $whichTeam . "'>" . " the hit dealt " . $damage . " damage!" . "</a>";
		                                }
		                            }
									$players[$target]['stats']['armourSoak'][] = array("part"=>$part,"damage"=>$damageReduction);
		                        }
		                        else{
		                            $damageText = "<a class='" . $whichTeam . "'>" . "the hit dealt " . $damage . " damage" . "</a>";
		                        }           
		                    } 
							$players[$player]['stats']['rangedAttacks'][] = array("type"=>"critical","damage"=>$originalDamage,"trueDamage"=>$damage);  
	                    }
						$battleReport .= $text . ", " . $damageText;
	                    $players[$target]['hp'] = $players[$target]['hp'] - $damage;
	
	                    $dead = checkSurrender($players[$player]['name']);
	                    if ($dead == "dead"){
	                        if(isset($players[$player]['killReward'])){
	                            $players[$player]['killReward'] = $players[$player]['killReward'] + 1;
	                        }
	                        else{
	                            $players[$player]['killReward'] = 1;
	                        }
	                    }
	                }
	                #DODGE SUCCESS
	                else{
	                	$players[$target]['stats']['dodges']++;
	                    $text = flavorText("dodgeRanged","none","none",$player,$target);
	                    $battleReport .= "<strong>" . $text . "</strong>";
	                }
	            }
	            #MISS
	            else {
	            	$players[$player]['stats']['rangedAttacks'][] = array("type"=>"miss","damage"=>0,"trueDamage"=>0);
	                $battleReport .= "<strong>" . $players[$player]['name'] . " missed his attack" . "</strong>";
	            }
	            $battleReport .= "<br>";
	            $players[$player][$attackType]['ammo'] = $players[$player][$attackType]['ammo']-1;
	            if ($players[$player][$attackType]['ammo'] == 0){
	                $battleReport .= "<br>" . $players[$player]['name'] . " is out of ammunition<br>"; 
	                $players[$player]['canShoot'] = 0;
	            }
	            
	            if ($players[$player][$attackType]['reloadTime'] > 0){
	                $players[$player][$attackType]['reload'] = $players[$player][$attackType]['reloadTime'];
	            }
	        }
        }
    }

	function checkSurrender($murderer){
		global $players;
		global $team1, $team2;
		global $battleReport;
		
		$i = 0;
		foreach ($players as $player){
			if ($player['hp'] <= $player['battleSurrender']){
				if (array_key_exists($i, $team1)){
					unset($team1[$i]);
					if ($player['hp'] <= 0){
						$battleReport .= "<br><u>" . $players[$i]['name'] . " falls to the ground, he has been mortally wounded by " . $murderer . "</u>";
						return "dead";
					}
					else{
						$battleReport .= "<br><u>" . $players[$i]['name'] . " has taken a real beating and decides to surrender</u>";
					}
				}
				elseif(array_key_exists($i, $team2)){
					unset($team2[$i]);
					if ($player['hp'] <= 0){
						$battleReport .= "<br><u>" . $players[$i]['name'] . " falls to the ground, he has been mortally wounded by " . $murderer . "</u>";
						return "dead";
					}
					else{
						$battleReport .= "<br><u>" . $players[$i]['name'] . " has taken a real beating and decides to surrender</u>";
					}
				}
			}
		$i++;
		}
	}
	
    function advanceForward($player){
        global $players, $battleReport;
        global $team1, $team2;
            if (in_array($player,$team1)){
                $movement = 1;
                $opposingTeam = $team2;
            }
            else{
                $movement = -1;
                $opposingTeam = $team1;
            }
            
            $players[$player]['position'] = $players[$player]['position'] + $movement;
            $battleReport .= $players[$player]['name'] . " advances towards the enemy<br>";
            
            foreach ($opposingTeam as $oPlayer){
                if ($players[$oPlayer]['position'] == $players[$player]['position']){
                    meleeAttack($player);
                    break;
                }
            }
        
    }
	
	function runRound(){
		global $players,$battleReport,$whichTeam;
		#ROLL FOR STARTING POSITIONS
			$startOrder = startingPositions();
			
			foreach ($startOrder as $player){
			    if ($players[$player]['disabled'] == false){
			        if ($players[$player]['hp'] > $players[$player]['battleSurrender']){
				        $action = chooseAction($player);
                        if($action == "run"){
                            advanceForward($player);
                        }
                        elseif($action == "shoot"){
                            rangedAttack($player);
                        }
                        elseif($action == "attack"){
                            meleeAttack($player);
                        }
                        else{
                            $battleReport .= $action;
                        }
					}
				}
				else{
					$recRoll = rand(1,100);
					if($recRoll > 50){
						$players[$player]['disabled'] = false;
						$battleReport .= "<strong>" . $players[$player]['name'] . " manages to rub the sand out of " . $players[$player]['hisher'] . " eyes!</strong><br>";
						if ($players[$player]['hp'] > $players[$player]['battleSurrender']){
					        $action = chooseAction($player);
	                        if($action == "run"){
	                            advanceForward($player);
	                        }
	                        elseif($action == "shoot"){
	                            rangedAttack($player);
	                        }
	                        elseif($action == "attack"){
	                            meleeAttack($player);
	                        }
	                        else{
	                            $battleReport .= $action;
	                        }
						}
					}
					else{
						$players[$player]['disabled'] = false;
						$battleReport .= $players[$player]['name'] . " is still disabled from the previous counterattack<br>";
					}
				}
			}
	}


	function xpReward($divider){
		global $loserTeam,$loserTeamLevel,$winnerTeamLevel;
		$opponentCount = count($loserTeam);
		$levelBonus = (($loserTeamLevel/$opponentCount) * 3);
		$levelDifferenceBonus = (($loserTeamLevel-$winnerTeamLevel) * 0.1)+1;
		if ($opponentCount > 1){
			$xp = round(((rand(3,4) + $levelBonus) * $levelDifferenceBonus) * ($divider + ($opponentCount * 0.10)));
			if ($xp <= 0){
				$xp = 1;
			}
			return $xp;
		}
		else{
			$xp = round(((rand(3,4) + $levelBonus) * $levelDifferenceBonus) * $divider);
			if ($xp <= 0){
				$xp = 1;
			}
			return $xp;
		}
	}
	function goldReward($divider){
		global $loserTeam,$loserTeamLevel,$winnerTeamLevel;
		$opponentCount = count($loserTeam);
		$levelBonus = (($loserTeamLevel/$opponentCount)) * 2;
		$levelDifferenceBonus = (($loserTeamLevel-$winnerTeamLevel) * 0.15)+1;
		if ($opponentCount > 1){
			$gold = round(((rand(4,7) + $levelBonus) * $levelDifferenceBonus) * ($divider + ($opponentCount * 0.10)));
			if ($gold <= 0){
				$gold = 1;
			}
			return $gold;
		}
		else{
			$gold = round(((rand(4,7) + $levelBonus*0.75) * $levelDifferenceBonus*0.75) * $divider);
			if ($gold <= 0){
				$gold = 1;
			}
			return $gold;
		}
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
            if (isset($players[$player][$slot]['damage_reduction'])){
                $players[$player][$slot]['damage_reduction'] = round($players[$player][$slot]['damage_reduction'] + $row['armourBonus'] * $enchantMultiplier);
            }
            $players[$player]['totalWeight'] = $players[$player]['totalWeight'] - $row['weightReduction'] * $enchantMultiplier;
            if ($players[$player]['totalWeight'] < 0){
                $players[$player]['totalWeight'] = 0;
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
        #$items = explode(",", $itemStr);
        #foreach($items as $item){
            #$nameS = "";
            $prefixS = "";
            $suffixS = "";
            
            $seperate = explode(":",$itemStr);
            
            /*
            $id = $seperate[0];
            $sql = "SELECT name FROM $table WHERE id='$id'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            $nameS = $row['name'];
             */
            
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
    
}
			
	#GET EACH PLAYERS STATS & EQUIPMENT
	$players = array();

	if (is_array($t2)){
		$allPlayers = array();
		foreach($t2 as $player){
			array_push($allPlayers,$player);
		}
		array_push($allPlayers,$t1);
		$playerCount = count($allPlayers);
	}
	else{
		$allPlayers = array($t1,$t2);
		$playerCount = count($allPlayers);
	}
	
	
	foreach($allPlayers as $name){
		array_push($players,getPlayerStats($name));
	}
	

	
	
	
	#RANDOMIZE TEAMS
	$team1 = array();
	$i = 0;
	while($i < count($allPlayers)){
		array_push($team1,$i);
		$i++;
	}
	$team2 = array();
	$chosen = array_rand($team1,(count($allPlayers)/2));
	if (count($chosen) == 1){
		unset($team1[$chosen]);
		$team2[$chosen] = $chosen;
	}
	else{
		foreach ($chosen as $player){
			unset($team1[$player]);
			$team2[$player] = $player;
		}
	}
	$origTeam1 = $team1;
	$origTeam2 = $team2;
	
	
	#team coloring & set disabled to false & his/her
	foreach($team1 as $player){
		$players[$player]['nameLink'] = "<a class=\"teamOne .marked\" href=\"index.php?page=view-character&charName=" . $players[$player]['name'] . "\">" . $players[$player]['name'] . "(" . $players[$player]['level'] . ")</a>";
		$players[$player]['disabled'] = false;
        $players[$player]['position'] = 0;
		if ($players[$player]['gender'] == "Male"){
			$players[$player]['hisher'] = "his";
			$players[$player]['himher'] = "him";
		}
		else{
			$players[$player]['hisher'] = "her";
			$players[$player]['himher'] = "her";
		}
	}
	foreach($team2 as $player){
		$players[$player]['nameLink'] = "<a class=\"teamTwo .marked\" href=\"index.php?page=view-character&charName=" . $players[$player]['name'] . "\">" . $players[$player]['name'] . "(" . $players[$player]['level'] . ")</a>";
		$players[$player]['disabled'] = false;
        $players[$player]['position'] = 3;
		if ($players[$player]['gender'] == "Male"){
			$players[$player]['hisher'] = "his";
            $players[$player]['himher'] = "him";
		}
		else{
			$players[$player]['hisher'] = "her";
            $players[$player]['himher'] = "her";
		}
	}
	
	$battleReport = "<div><div id=\"t1Div\">Team 1<br>";
	foreach($team1 as $player){
		$battleReport .= $players[$player]['nameLink'] . "<br>";
	}
	$battleReport .= "</div><div id=\"vsDiv\"> VS </div>";
	$battleReport .= "<div id=\"t2Div\">Team 2<br>";
	foreach($team2 as $player){
		$battleReport .= $players[$player]['nameLink'] . "<br>";
	}
	$battleReport .= "</div></div><div>";
	
	#GET WEAPON AND ARMOUR INFORMATION
	$i = 0;
    $rangedPlayers = 0;
	
	while ($i < $playerCount){
	    $players[$i]['totalWeight'] = 0;
        $players[$i]['style'] = "dualwield";
		$players[$i]['right_hand'] = getItemStats("weapons",$players[$i]['right_hand'],$i);
         
		$players[$i]['left_hand'] = getItemStats("weapons",$players[$i]['left_hand'],$i);
		$players[$i]['secondary'] = getItemStats("weapons",$players[$i]['secondary'],$i);
        
		$players[$i]['head'] = getItemStats("armours",$players[$i]['head'],$i);
		$players[$i]['chest'] = getItemStats("armours",$players[$i]['chest'],$i);
		$players[$i]['arm'] = getItemStats("armours",$players[$i]['arm'],$i);
		$players[$i]['leg'] = getItemStats("armours",$players[$i]['leg'],$i);
        $players[$i]['feet'] = getItemStats("armours",$players[$i]['feet'],$i);
        
        if(isset($players[$i]['feet']['bonusStats'])){
            $explodedStats = explode(",",$players[$i]['feet']['bonusStats']);
            foreach ($explodedStats as $stat){
                $explodedAgain = explode(":",$stat);
                if ($explodedAgain[0] == "Dodge"){
                    $players[$i]['dodgeSkill'] = $players[$i]['dodgeSkill'] + $explodedAgain[1];
                }
                elseif($explodedAgain[0] == "Initiative"){
                    $players[$i]['initiative'] = $players[$i]['initiative'] + $explodedAgain[1];
                }
            }
        }
        
		$i++;
	}	

	
	#modifiers
	
	$sql = "SELECT * FROM modifiers";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
    $maxFoul = $row['maxFoul'];
    $maxDodge = $row['maxDodge'];
    $maxCrit = $row['maxCrit'];
    $maxParry = $row['maxParry'];
    $weightDodgeMod = $row['weightDodgeMod'];
    $weightParryMod = $row['weightParryMod'];
    $weightFoulMod = $row['weightFoulMod'];

	$i = 0;
	while($i < $playerCount){
		#Surrender HP
		if ($tournamentId == 0){
			$players[$i]['battleSurrender'] = round($players[$i]['vitality'] * $players[$i]['battleSurrender']); 
		}
		else{
			$players[$i]['battleSurrender'] = round($players[$i]['vitality'] * $players[$i]['tournamentSurrender']); 
		}
					
		#attackMod
		$attackMod = (($players[$i]['strength'] * $row['attackMod']) + ($players[$i]['dexterity'] * $row['dexAttackMod'])) + 1;
        $rangedAttackMod = (($players[$i]['dexterity'] * $row['dexAttackMod'])*3) + 1;
			
		#Right_hand
			#hit chance
			#if ($players[$i]['right_hand']['type'] == "1h"){
			#	$compareSkill = $players[$i]['one_handed'];
			#}
			#elseif ($players[$i]['right_hand']['type'] == "2h"){
			#	$compareSkill = $players[$i]['two_handed'];
			#}
			#$players[$i]['right_hand']['chance_hit'] = $players[$i]['right_hand']['chance_hit'] + (($compareSkill-$players[$i]['right_hand']['skill']) * $row['weaponSkillDivider'])-$players[$i]['totalWeight'];
			
			#right hand dmg
			#$players[$i]['right_hand']['min_dmg'] = round($players[$i]['right_hand']['min_dmg'] * $attackMod);
			#$players[$i]['right_hand']['max_dmg'] = round($players[$i]['right_hand']['max_dmg'] * $attackMod);
			
			if ($players[$i]['right_hand']['name'] == "Nothing"){
				$players[$i]['right_hand']['name'] = "Bare Hands";
			}
			
		#Left_hand
		
			#shield
		if ($players[$i]['left_hand']['type'] == "shield"){
		    $players[$i]['style'] = "shield";
			#
			
			$players[$i]['right_hand']['min_dmg'] = $players[$i]['right_hand']['min_dmg'] * $attackMod;
			$players[$i]['right_hand']['max_dmg'] = $players[$i]['right_hand']['max_dmg'] * $attackMod;
		}
		else{
			$players[$i]['block'] = 0;
			#weapon
				if($players[$i]['right_hand']['type'] == "1h"){
					#hit
					#$compareSkill = $players[$i]['one_handed'];
					#$players[$i]['left_hand']['chance_hit'] = $players[$i]['left_hand']['chance_hit'] + (($compareSkill-$players[$i]['left_hand']['skill']) * $row['weaponSkillDivider'])-$players[$i]['totalWeight'];
					if($players[$i]['left_hand']['name'] == "Nothing" && $players[$i]['right_hand'] != "Nothing"){
					    $players[$i]['style'] = "singleWield";
                        $players[$i]['right_hand']['min_dmg'] = $players[$i]['right_hand']['min_dmg'] * $attackMod;
                        $players[$i]['right_hand']['max_dmg'] = $players[$i]['right_hand']['max_dmg'] * $attackMod;
					}
                    else{
					#damage
						$players[$i]['left_hand']['min_dmg'] = $players[$i]['left_hand']['min_dmg'] * $attackMod * 0.6;
						$players[$i]['left_hand']['max_dmg'] = $players[$i]['left_hand']['max_dmg'] * $attackMod * 0.6;
							
						#mainHand
						$players[$i]['right_hand']['min_dmg'] = $players[$i]['right_hand']['min_dmg'] * $attackMod * 0.8;
						$players[$i]['right_hand']['max_dmg'] = $players[$i]['right_hand']['max_dmg'] * $attackMod * 0.8;
                    }
					if ($players[$i]['left_hand']['name'] == "Nothing"){
						$players[$i]['left_hand']['name'] = "Bare Hands";
					}
				}
			}
		if ($players[$i]['right_hand']['type'] == "2h"){
			#right hand dmg
			$players[$i]['right_hand']['min_dmg'] = $players[$i]['right_hand']['min_dmg'] * $attackMod;
			$players[$i]['right_hand']['max_dmg'] = $players[$i]['right_hand']['max_dmg'] * $attackMod;
		}
		
        #RANGED
        if ($players[$i]['secondary']['ids'] == "1:1;1"){
            $players[$i]['canShoot'] = 0;
        }
        else{
            $players[$i]['canShoot'] = 1;
            $rangedPlayers += 1;
        
        $rangedCompareSkill = $players[$i][$players[$i]['secondary']['type']];
        $rangedSkillDivider = $row['weaponSkillDivider'];
        $players[$i]['secondary']['reload'] = 0;
        }
        
        if ($players[$i]['secondary']['type'] == "bow"){
            $players[$i]['secondary']['min_dmg'] = $players[$i]['secondary']['min_dmg'] * $rangedAttackMod;
            $players[$i]['secondary']['max_dmg'] = $players[$i]['secondary']['max_dmg'] * $rangedAttackMod;
        }
        

        //PLAYER ENCHANTMENTS
        getPlayerEnchants($players[$i]['right_hand']['ids'],$players[$i]['right_hand']['name'],"right_hand","melee",$i,$players[$i]['right_hand']['enchantTier']);
        getPlayerEnchants($players[$i]['left_hand']['ids'],$players[$i]['left_hand']['name'],"left_hand",$players[$i]['left_hand']['type'],$i,$players[$i]['left_hand']['enchantTier']);
        getPlayerEnchants($players[$i]['secondary']['ids'],$players[$i]['secondary']['name'],"secondary","ranged",$i,$players[$i]['secondary']['enchantTier']);
        getPlayerEnchants($players[$i]['head']['ids'],$players[$i]['head']['name'],"head","armour",$i,$players[$i]['head']['enchantTier']);
        getPlayerEnchants($players[$i]['chest']['ids'],$players[$i]['chest']['name'],"chest","armour",$i,$players[$i]['chest']['enchantTier']);
        getPlayerEnchants($players[$i]['arm']['ids'],$players[$i]['arm']['name'],"arm","armour",$i,$players[$i]['arm']['enchantTier']);
        getPlayerEnchants($players[$i]['leg']['ids'],$players[$i]['leg']['name'],"leg","armour",$i,$players[$i]['leg']['enchantTier']);
        getPlayerEnchants($players[$i]['feet']['ids'],$players[$i]['feet']['name'],"feet","armour",$i,$players[$i]['feet']['enchantTier']);
         
        #Calculate hitchances   
        if($players[$i]['right_hand']['type'] == "1h"){
            $compareSkill = $players[$i]['one_handed'];
            $players[$i]['right_hand']['chance_hit'] = round($players[$i]['right_hand']['chance_hit'] + (($compareSkill-$players[$i]['right_hand']['skill']) * $row['weaponSkillDivider'])-$players[$i]['totalWeight']);
            if ($players[$i]['left_hand']['type'] == "1h"){
                $players[$i]['left_hand']['chance_hit'] = round($players[$i]['left_hand']['chance_hit'] + (($compareSkill-$players[$i]['left_hand']['skill']) * $row['weaponSkillDivider'])-$players[$i]['totalWeight']);
            }
            else{
                $players[$i]['block'] = round($players[$i]['left_hand']['block_amount'] + ($players[$i]['shield'] * $row['blockMod']));
                #$players[$i]['block'] = round($players[$i]['block'] - ((($players[$i]['totalWeight'] * $weightBlockMod)/ 100) * $players[$i]['block']));
            }
        }
        else{
            $compareSkill = $players[$i]['two_handed'];
            $players[$i]['right_hand']['chance_hit'] = round($players[$i]['right_hand']['chance_hit'] + (($compareSkill-$players[$i]['right_hand']['skill']) * $row['weaponSkillDivider'])-$players[$i]['totalWeight']);
        }
        if(isset($rangedSkillDivider)){
            $players[$i]['secondary']['chance_hit'] = round($players[$i]['secondary']['chance_hit'] + (($rangedCompareSkill-$players[$i]['secondary']['skill']) * $rangedSkillDivider)-($players[$i]['totalWeight']/2));
        }
        
                    
		#Foul Play
		$players[$i]['foul_play'] = $players[$i]['foul_play'] * $row['foul_playMod'];
        $players[$i]['foul_play'] = round($players[$i]['foul_play'] - ((($players[$i]['totalWeight'] * $weightFoulMod)/ 100) * $players[$i]['foul_play']),1);
        $players[$i]['foul_play'] = round($players[$i]['foul_play']);
       
		#Dodge
		$players[$i]['dodge'] = round(($players[$i]['dexterity'] * $row['dodgeMod'])*2 + ($players[$i]['dodgeSkill'] * $row['dodgeMod']));
        $players[$i]['dodge'] = round($players[$i]['dodge'] - ((($players[$i]['totalWeight'] * $weightDodgeMod)/ 100) * $players[$i]['dodge']));
        
		#Crit 
		$players[$i]['critical'] = round((($players[$i]['finesse'] * $row['finesseMod']) + ($players[$i]['dexterity'] * $row['critMod'])));
		
		//PARRY
		if($players[$i]['right_hand']['canParry'] == "true" || $players[$i]['left_hand']['canParry'] == "true"){
			$players[$i]['parry'] = round($players[$i]['parry'] * $row['parryMod']);
            $players[$i]['parry'] = round($players[$i]['parry'] - ((($players[$i]['totalWeight'] * $weightParryMod)/ 100) * $players[$i]['parry']));
		}
		else{
			$players[$i]['parry'] = 0;
		}
        
        //ROUND stuff
        $players[$i]['right_hand']['min_dmg'] = round($players[$i]['right_hand']['min_dmg']);
        $players[$i]['right_hand']['max_dmg'] = round($players[$i]['right_hand']['max_dmg']);
        $players[$i]['right_hand']['armourPenetration'] = round($players[$i]['right_hand']['armourPenetration']);
        if ($players[$i]['left_hand']['type'] != "shield"){
            $players[$i]['left_hand']['min_dmg'] = round($players[$i]['left_hand']['min_dmg']);
            $players[$i]['left_hand']['max_dmg'] = round($players[$i]['left_hand']['max_dmg']);
            $players[$i]['left_hand']['armourPenetration'] = round($players[$i]['left_hand']['armourPenetration']);
        }
        $players[$i]['secondary']['min_dmg'] = round($players[$i]['secondary']['min_dmg']);
        $players[$i]['secondary']['max_dmg'] = round($players[$i]['secondary']['max_dmg']);
        $players[$i]['secondary']['armourPenetration'] = round($players[$i]['secondary']['armourPenetration']);
        
        
        //make sure no value is higher than the max after enchants
        if ($players[$i]['foul_play'] >= $maxFoul) {
            $players[$i]['foul_play'] = $maxFoul;
        }
        if ($players[$i]['dodge'] >= $maxDodge){
            $players[$i]['dodge'] = $maxDodge;
        }
        if ($players[$i]['critical'] >= $maxCrit){
            $players[$i]['critical'] = $maxCrit;
        }
        if ($players[$i]['parry'] >= $maxParry){
            $players[$i]['parry'] = $maxParry;
        }
		if($players[$i]['style'] == "dualwield"){
			if($players[$i]['right_hand']['type'] == "2h"){
				#2h
				$players[$i]['parryType'] = 2;
			}
			else{
				#dualwield
				$players[$i]['parryType'] = 1;
			}
		}
		else{
			#block or just a 1h
			$players[$i]['parryType'] = 0;
		}
		
		
		#battlereport stats
		$players[$i]['stats']['rangedAttacks'] = array();
		$players[$i]['stats']['meleeAttacks'] = array();
		$players[$i]['stats']['blocks'] = array();
		$players[$i]['stats']['parries'] = array();
		$players[$i]['stats']['fouls'] = 0;
		$players[$i]['stats']['dodges'] = 0;
		$players[$i]['stats']['armourSoak'] = array();
					
		$i++;
	}

    
	
	#WHILE LOOP UNTIL ONE OF THE TEAM ARRAYS ARE EMPTY	
	$round = 1;
	while(!empty($team1) && !empty($team2) && $round < 101){
			foreach($team1 as $player){
				$players[$player]['blocked'] = "";
                $players[$player]['dodged'] = "";
                $players[$player]['parried'] = "";
			}
			foreach($team2 as $player){
				$players[$player]['blocked'] = "";
                $players[$player]['dodged'] = "";
                $players[$player]['parried'] = "";
			}
			$battleReport .=  "<br><br><h4>Round " . $round . "</h4><br>";
            if ($round == 1){
                if ($rangedPlayers > 0){
                    /*
                    foreach ($players as $player){
                        if ($player['position'] == 1 || $player['position'] == 2){
                            $battleReport .= $player['name'] . ", not burdened by a secondary weapon, rushes out to get as close as possible to the opposing team!<br>";
                        }
                    }
                     * 
                     */
                }
                else{
                    $battleReport .= "None of the combatants are equipped with ranged weapons, they all move forward to melee range.<br><br>";
                    foreach($team1 as $player){
                        $players[$player]['position'] = 0;
                    }
                    foreach($team2 as $player){
                        $players[$player]['position'] = 0;
                    }
                }
            }
			runRound();		
			
			$round++;
	}
	#var_dump($players);
	#var_dump($players[0]['stats']);
	#var_dump($players[1]['stats']);

	$fightWinner = array();
	$surrenderWinner = array();
	$deadCharacters = array();
	
	
	if (empty($team2)){
		$battleReport .= "<br><br>". "<a class=\"teamOne\">" . "Team 1 is victorious!</a><br><br>";
		foreach ($origTeam1 as $player){
			if (in_array($player,$team1)){
				array_push($fightWinner,$player);
			}
			else{
				array_push($surrenderWinner,$player);
			}
		}
		
		$winColor = "teamOne";
		$loseColor = "teamTwo";
		$loserTeam = $origTeam2;
		$winnerTeam = $origTeam1;
	}
	else{
		$battleReport .= "<br><br>" . "<a class=\"teamTwo\">" . "Team 2 is victorious!</a><br><br>";
		foreach ($origTeam2 as $player){
			if (in_array($player,$team2)){
				array_push($fightWinner,$player);
			}
			else{
				if ($players[$player]['hp'] > 0){
				array_push($surrenderWinner,$player);
				}
				else{
					 array_push($deadCharacters,$players[$player]['name']);
				}
			}
		}
		
		$winColor = "teamTwo";
		$loseColor = "teamOne";
		$loserTeam = $origTeam1;
		$winnerTeam = $origTeam2;
	}
	$loserTeamLevel = 0;
	$winnerTeamLevel = 0;
	$loserTeamString = "";
	$winnerTeamString = "";
	foreach ($loserTeam as $player){
		$loserTeamLevel = $loserTeamLevel + $players[$player]['level'];
		$loserTeamString .= $players[$player]['name'] . ",";
	}
	foreach ($winnerTeam as $player){
		$winnerTeamLevel = $winnerTeamLevel + $players[$player]['level'];
		$winnerTeamString .= $players[$player]['name'] . ",";
	}
	$winnerTeamString = rtrim($winnerTeamString, ",");
	$loserTeamString = rtrim($loserTeamString, ",");
	
	if (count($winnerTeam) == 1){
		$winType = "wins";
		$lossType = "losses";
	}
	else{
		$winType = "teamWins";
		$lossType = "teamLosses";
	}
	
	foreach ($fightWinner as $player){
		$players[$player]['whichTeam'] = "winner";
	    $playerSupplies = $players[$player]['adventureTurns'];
        if ($playerSupplies >= 6){
            $playerSupplies = 6;
        }
        else{
            $playerSupplies = $playerSupplies+1;
        }
		$name = $players[$player]['name'];
		$hp = $players[$player]['hp'];
		$xpGain = xpReward(1.5);
		$goldGain = goldReward(2);
		$battleReport .= "<a class=\"" . $winColor . "\">" . $name . " is awarded with " . $xpGain . "xp and " . $goldGain . "gold for winning.";
		if(isset($players[$player]['killReward'])){
			$kills = $players[$player]['killReward']; 
			$xpGainExtra = round($xpGain * ($kills * 1.5));
			$goldGainExtra = round($goldGain * ($kills * 1.5));
			$xpGain = $xpGain + $xpGainExtra;
			$goldGain = $goldGain + $goldGainExtra;
			$battleReport .= " " . $name . " also gets an extra " . $xpGainExtra . "xp and " . $goldGainExtra . "gold for mortally wounding " . $players[$player]['killReward'] . " opponent.";
            if ($tournamentId != 0){
                $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,kills=kills+'$kills',hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
            }
            else{
			    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,kills=kills+'$kills',hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
		    }
        }
		else{
		    if ($tournamentId != 0){
                $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
            }
            else{
			$sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
            }
        }
		$battleReport .= "</a><br>";
		mysqli_query($conn,$sql);
		
	}
	foreach ($surrenderWinner as $player){
		$players[$player]['whichTeam'] = "winner";
		if ($players[$player]['hp'] > 0){
		    $playerSupplies = $players[$player]['adventureTurns'];
            if ($playerSupplies >= 6){
                $playerSupplies = 6;
            }
            else{
                $playerSupplies = $playerSupplies+1;
            }
			$xpGain = xpReward(0.85);
			$goldGain = goldReward(1.05);
			$name = $players[$player]['name'];
			$hp = $players[$player]['hp'];
			$battleReport .= "<a class=\"" . $winColor . "\">" . $name . " is awarded with " . $xpGain . "xp and " . $goldGain . "gold for surrendering but being on the winning team.";
			if(isset($players[$player]['killReward'])){
				$kills = $players[$player]['killReward']; 
				$xpGainExtra = round($xpGain * ($kills * 1.5));
				$goldGainExtra = round($goldGain * ($kills * 1.5));
				$xpGain = $xpGain + $xpGainExtra;
				$goldGain = $goldGain + $goldGainExtra;
				$battleReport .= " " . $name . " also gets an extra " . $xpGainExtra . "xp and " . $goldGainExtra . "gold for mortally wounding " . $players[$player]['killReward'] . " opponent.";
                if ($tournamentId != 0){
                    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,kills=kills+'$kills',hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
                }
                else{
				$sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,kills=kills+'$kills',hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
			    }
			}
			else{
			    if ($tournamentId != 0){
                    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
                }
                else{
				$sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $winType=$winType+1,hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
                }
            }
			$battleReport .= "</a><br>";
			mysqli_query($conn,$sql);
			}
		else{
			array_push($deadCharacters,$players[$player]['name']);
		}
	}
	foreach ($loserTeam as $player){
		$players[$player]['whichTeam'] = "loser";
		
		if ($players[$player]['hp'] > 0){
		    $playerSupplies = $players[$player]['adventureTurns'];
		    if ($playerSupplies >= 6){
		        $playerSupplies = 6;
		    }
            else{
                $playerSupplies = $playerSupplies+1;
            }
			$xpGain = xpReward(0.45);
			$goldGain = goldReward(0.65);
			$name = $players[$player]['name'];
			$hp = $players[$player]['hp'];
			$battleReport .= "<a class=\"" . $loseColor . "\">" . $name . " is awarded with " . $xpGain . "xp and " . $goldGain . "gold for losing.";
			if(isset($players[$player]['killReward'])){
				$kills = $players[$player]['killReward']; 
				$xpGainExtra = round($xpGain * ($kills * 2));
				$goldGainExtra = round($goldGain * ($kills * 2));
				$xpGain = $xpGain + $xpGainExtra;
				$goldGain = $goldGain + $goldGainExtra;
				$battleReport .= " " . $name . " also gets an extra " . $xpGainExtra . "xp and " . $goldGainExtra . "gold for mortally wounding " . $players[$player]['killReward'] . " opponent.";
                if ($tournamentId != 0){
                    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $lossType=$lossType+1,kills=kills+'$kills',hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
                }
                else{
				   $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $lossType=$lossType+1,kills=kills+'$kills',hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
                }
            }
			else{
			    if ($tournamentId != 0){
                    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $lossType=$lossType+1,hp='$hp',adventureTurns='$playerSupplies' WHERE name='$name'";
                }
                else{
				    $sql = "UPDATE characters SET experience=experience+'$xpGain', gold=gold+'$goldGain', $lossType=$lossType+1,hp='$hp',battleReportReady=1,battleReady=0,battleType=0,battleSurrender=1,fightLevelChoice=0,adventureTurns='$playerSupplies' WHERE name='$name'";
			    }
			}
			$battleReport .= "</a><br>";
			mysqli_query($conn,$sql);
			}
		else{
			array_push($deadCharacters,$players[$player]['name']);
		}
	}
	foreach ($deadCharacters as $player){
		$sql = "UPDATE characters SET deadNext=1,hp=0,battleReady=0 WHERE name='$player'";
		mysqli_query($conn,$sql);
		$battleReport .= "<a style=\"color:red;\">" . $player . " died during this battle </a><br>";
	}
	
	if($tournamentId == 0){
		if (count($winnerTeam) == 1){
			$type = "1v1";
		}
		elseif (count($winnerTeam) == 2){
			$type = "2v2";
		}
		elseif (count($winnerTeam) == 3){
			$type = "3v3";
		}
		elseif (count($winnerTeam) == 4){
			$type = "4v4";
		}
		elseif (count($winnerTeam) > 4){
			$type = "mt4";
		}
	}
	else{
		$type ="tournament";
	}
	
	$date = date("Y/m/d H:i");
	
	$battleReport .= "<br>This battle was fought at: " . $date;
	
	#statTable Statistic Table Stats Stat Table
	
	
	
	$allPlayersStats = array();
	foreach($players as $player){
		$allPlayersStats[] = getBattleStats($player);
	}
	
	
	
	$statTable = "<div><table id='statTable' border='2'>";
	
	$count = count($allPlayersStats);
	$i = 0;
	$statTable .= "<th></th>";
	for($i = 0;$i < $count; $i++){
		$statTable .= "<th class='" . $allPlayersStats[$i]['color'] . "'>" . $allPlayersStats[$i]['name'] . "</th>";
	}
	$statTable .= "<tr>";
	#check which values exists
	$possibleKeys = array("RangedHits","RangedDamage","MeleeHits","MeleeDamage","blocks","parries","fouls","dodges","armour");
	$printableKeys = array("RangedHits"=>0,"RangedDamage"=>0,"MeleeHits"=>0,"MeleeDamage"=>0,"blocks"=>0,"parries"=>0,"fouls"=>0,"dodges"=>0,"armour"=>0);
	$nicerKeys = array("Ranged Hits","Ranged Damage","Melee Hits","Melee Damage","Blocks","Parries","Foul plays","Dodges","Armour reduction");
	foreach($allPlayersStats as $player){
		foreach($possibleKeys as $key){
			if(array_key_exists($key, $player)){
				$printableKeys[$key] = 1;
			}
		}
	}
	$si = 0;
	foreach($printableKeys as $key => $value){
		if($value == 1){
			$statTable .= "<td>" . $nicerKeys[$si] . "</td>";
			for($i = 0;$i < $count; $i++){
				if (isset($allPlayersStats[$i][$key])){
					$statTable .= "<td>" . $allPlayersStats[$i][$key] . "</td>";
				}
				else{
					$statTable .= "<td></td>";
				}
				
			}
			$statTable .= "<tr>";
		}
		$si++;
	}
	
	$statTable .= "</table></div>";
	
	$battleReport .= $statTable;
	$battleReport .="</div>";
	
	$realWinColor = "winningTeam";
		
	$winningReport = str_replace($winColor, $realWinColor, $battleReport);
	$losingReport = str_replace($loseColor, $realWinColor, $battleReport);
	
	
	
	
			
	foreach ($loserTeam as $player){
		$username = $players[$player]['username'];
		$name = $loserTeamString;
		$opponent = $winnerTeamString;
        $win = 0;
		$sql = "INSERT INTO battlereports (username, yourName, opponentName, date, report,type,win) VALUES (?,?,?,?,?,?,?)";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ssssssi", $username,$name,$opponent,$date,$losingReport,$type,$win);
		mysqli_stmt_execute($stmt);
	}
	
	foreach ($winnerTeam as $player){
		$username = $players[$player]['username'];
		$name = $loserTeamString;
		$opponent = $winnerTeamString;
        $win = 1;
		$sql = "INSERT INTO battlereports (username, yourName, opponentName, date, report,type,win) VALUES (?,?,?,?,?,?,?)";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ssssssi", $username,$name,$opponent,$date,$winningReport,$type,$win);
		mysqli_stmt_execute($stmt);
	}
	
	if($type == "1v1"){
		foreach ($winnerTeam as $player){
			$winner = $player;
		}
		foreach ($loserTeam as $player){
			$loser = $player;
		}
		$battleId = mysqli_stmt_insert_id($stmt);
		$infoMessage = "<a class=\"headerButtonLink\" href=\"index.php?page=view-battlereport&battleId=" . $battleId . "\">" . $players[$winner]['name'] . "(" . $players[$winner]['level'] . ") beat " . $players[$loser]['name'] . "(" . $players[$loser]['level'] . ") in the arena! - " . $date . "</a>";
		$sql = "UPDATE configuration SET infoBarMessage='$infoMessage'";
		mysqli_query($conn,$sql);
	}
	elseif($type != "1v1" && $type != "tournament"){
		$battleId = mysqli_stmt_insert_id($stmt);
		$infoMessage = "<a class=\"headerButtonLink\" href=\"index.php?page=view-battlereport&battleId=" . $battleId . "\">" . "A large fight has been fought in the arena! - " . $date . "</a>";
		$sql = "UPDATE configuration SET infoBarMessage='$infoMessage'";
		mysqli_query($conn,$sql);
	}
    elseif($type == "tournament"){
        $winner = $player;
        $battleId = mysqli_stmt_insert_id($stmt);
    }
	
	if ($tournamentId != 0){
		$tournamentRoundName = "round";
		$tournamentRoundName .= $tournamentRound+1;
        $tournamentRoundNameText = $tournamentRoundName . "Text";
		$tournamentRoundReport = "round";
		$tournamentRoundReport .= $tournamentRound+1 . "Report";
		#echo $tournamentRoundName;
		#echo "<br>";
		#echo $tournamentRoundReport;
		$sql = "SELECT * FROM tournaments WHERE id='$tournamentId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$reports = $row['' . $tournamentRoundReport . ''];
		if ($reports != ""){
			$reports = explode(",", $reports);
			array_push($reports,$battleId);
			$reports = implode(",", $reports);
		}
		else{
			$reports = $battleId;
		}
		$nextRound = $row['' . $tournamentRoundName . ''];
        $nextRoundText = $row['' . $tournamentRoundName . 'Text'];
		if ($nextRound != ""){
			$nextRound = explode(",", $nextRound);
			array_push($nextRound,$players[$winner]['id']);
			$nextRound = implode(",", $nextRound);
            $nextRoundText = explode(",", $nextRoundText);
            array_push($nextRoundText,$players[$winner]['name']);
            $nextRoundText = implode(",", $nextRoundText);
		}
		else{
			$nextRound = $players[$winner]['id'];
            $nextRoundText = $players[$winner]['name'];
		}
		$sql = "UPDATE tournaments SET $tournamentRoundName='$nextRound',$tournamentRoundNameText='$nextRoundText',$tournamentRoundReport='$reports' WHERE id='$tournamentId'";
		mysqli_query($conn,$sql);
		
	}
	else{
		showLastReport();
		echo"<script>
			window.onload = updateChar();
		</script>";
	}
	
}

function tournamentRound($players,$playersNames,$id,$round){
	global $conn;
	global $battleId;
	$i = 0;
	while ($i < count($players)){
			if ($playersNames[$i+1] == "-"){
			    //WALKOVER, not enough players
				$tournamentRoundName = "round";
				$tournamentRoundName .= $round+1;
                $tournamentRoundNameText = $tournamentRoundName . "Text";
				$tournamentRoundReport = "round";
				$tournamentRoundReport .= $round+1 . "Report";
				$sql = "SELECT * FROM tournaments WHERE id='$id'";
				$result = mysqli_query($conn,$sql);
				$row = mysqli_fetch_assoc($result);
				
				$reports = $row['' . $tournamentRoundReport . ''];
				if ($reports != ""){
					$reports = explode(",", $reports);
					array_push($reports,0);
					$reports = implode(",", $reports);
				}
				else{
					$reports = 0;
				}
				$nextRound = $row['' . $tournamentRoundName . ''];
                $nextRoundText = $row['' . $tournamentRoundName . 'Text'];
				if ($nextRound != ""){
					$nextRound = explode(",", $nextRound);
					array_push($nextRound,$players[$i]);
					$nextRound = implode(",", $nextRound);
                    $nextRoundText = explode(",", $nextRoundText);
                    array_push($nextRoundText,$playersNames[$i]);
                    $nextRoundText = implode(",", $nextRoundText);
				}
				else{
					$nextRound = $players[$i];
                    $nextRoundText = $playersNames[$i];
				}
				$sql = "UPDATE tournaments SET $tournamentRoundName='$nextRound',$tournamentRoundNameText='$nextRoundText',$tournamentRoundReport='$reports' WHERE id='$id'";
				mysqli_query($conn,$sql);
			}
			else{
				#echo "<br>" . $players[$i] . " will fight " . $players[$i+1];
				$p1 = $players[$i];
				$p2 = $players[$i+1];
				if (checkDead($p1) == true){
					if (checkDead($p2) == true){
						//NO ONE WINS
						#echo "Both dead, no one won";
					}
					// PLAYER 2 WINS WO
					#echo $p1 . " dead, WO";
					$tournamentRoundName = "round";
					$tournamentRoundName .= $round+1;
                    $tournamentRoundNameText = $tournamentRoundName . "Text";
					$tournamentRoundReport = "round";
					$tournamentRoundReport .= $round+1 . "Report";
					$sql = "SELECT * FROM tournaments WHERE id='$id'";
					$result = mysqli_query($conn,$sql);
					$row = mysqli_fetch_assoc($result);
					
					$reports = $row['' . $tournamentRoundReport . ''];
					if ($reports != ""){
						$reports = explode(",", $reports);
						array_push($reports,0);
						$reports = implode(",", $reports);
					}
					else{
						$reports = 0;
					}
					$nextRound = $row['' . $tournamentRoundName . ''];
					$nextRoundText = $row['' . $tournamentRoundName . 'Text'];
					if ($nextRound != ""){
						$nextRound = explode(",", $nextRound);
						array_push($nextRound,$p2);
						$nextRound = implode(",", $nextRound);
                        $nextRoundText = explode(",", $nextRoundText);
                        array_push($nextRoundText,$playersNames[$i+1]);
                        $nextRoundText = implode(",", $nextRoundText);
					}
					else{
						$nextRound = $p2;
                        $nextRoundText = $playersNames[$i+1];
					}                    
					$sql = "UPDATE tournaments SET $tournamentRoundName='$nextRound',$tournamentRoundNameText='$nextRoundText',$tournamentRoundReport='$reports' WHERE id='$id'";
					mysqli_query($conn,$sql);
				}
				elseif(checkDead($p2) == true){
					//PLAYER 1 WINS WO
					#echo $p2 . " dead, WO";
					$tournamentRoundName = "round";
					$tournamentRoundName .= $round+1;
                    $tournamentRoundNameText = $tournamentRoundName . "Text";
					$tournamentRoundReport = "round";
					$tournamentRoundReport .= $round+1 . "Report";
					$sql = "SELECT * FROM tournaments WHERE id='$id'";
					$result = mysqli_query($conn,$sql);
					$row = mysqli_fetch_assoc($result);
					
					$reports = $row['' . $tournamentRoundReport . ''];
					if ($reports != ""){
						$reports = explode(",", $reports);
						array_push($reports,0);
						$reports = implode(",", $reports);
					}
					else{
						$reports = 0;
					}
					$nextRound = $row['' . $tournamentRoundName . ''];
                    $nextRoundText = $row['' . $tournamentRoundName . 'Text'];
					if ($nextRound != ""){
						$nextRound = explode(",", $nextRound);
						array_push($nextRound,$p1);
						$nextRound = implode(",", $nextRound);
                        $nextRoundText = explode(",", $nextRoundText);
                        array_push($nextRoundText,$playersNames[$i]);
                        $nextRoundText = implode(",", $nextRoundText);
					}
					else{
						$nextRound = $p1;
                        $nextRoundText = $playersNames[$i];
					}
					$sql = "UPDATE tournaments SET $tournamentRoundName='$nextRound',$tournamentRoundNameText='$nextRoundText',$tournamentRoundReport='$reports' WHERE id='$id'";
					mysqli_query($conn,$sql);
				}
				else{
					$sql = "UPDATE characters SET hp=vitality WHERE id IN('$p1','$p2')";
					mysqli_query($conn,$sql);
					#var_dump($playersNames);
					fight($playersNames[$i],$playersNames[$i+1],1,$id,$round);
				}
			}
			$i = $i + 2;
			#sleep(5);
	}
	#var_dump($players);
	return $battleId;
}

function checkDead($id){
	global $conn;
	$sql = "SELECT deadNext FROM characters WHERE id='$id'";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		if ($row['deadNext'] == 1){
			return true;
		}
	}
	else{
		return true;
	}
	
}

?>