<?php
function EnchantBonuses($enchants,$type,$minDmg,$maxDmg,$accuracy,$armourPenetration,$enchantTier){
    global $conn;
    Global $extraDamage,$extraOne,$extraTwo,$extraBow,$extraXbow,$extraFinesse,$extraInitiative,$extraLight,$extraHeavy,$extraShield,$extraParry,$extraFoul,$extraDodge,$reducedWeight,$extraBlockPercent;
    #echo $enchants;
    $enchantEffective = array("0.8","1","1.2","1.4","1.6");

    if($type == "armour"){
        $sql = "SELECT enchantTier FROM armours WHERE id='$enchantTier'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        $enchantTier = $row['enchantTier'];
    }

    $enchantTier = $enchantTier-1;

    if ($enchants != "1;1"){
        $split = explode(";",$enchants);
        if($split[0] == $split[1]){
            $sql = "SELECT * FROM enchants WHERE id='$split[1]' AND (type='$type' OR type='all')";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            if($row['damageBonusPercent'] > 0){
                $minDmg = $minDmg * (1 + ((($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) * 2) / 100));
                $maxDmg = $maxDmg * (1 + ((($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) * 2) / 100));
            }
            $minDmg = $minDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier])  * 2;
            $maxDmg = $maxDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier])  * 2;
            $accuracy = $accuracy + ($row['accuracyPercent'] * $enchantEffective[$enchantTier])  * 2;
            $armourPenetration = $armourPenetration + round(($row['armourPenetration'] * $enchantEffective[$enchantTier]))  * 2;
    		$extraBlockPercent = $extraBlockPercent + ($row['blockPercent'] * $enchantEffective[$enchantTier]) * 2; 
            
            $extraOne = $extraOne + ($row['oneSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraTwo = $extraTwo + ($row['twoSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraBow = $extraBow + ($row['bowSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraXbow = $extraXbow + ($row['xBowSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraFinesse = $extraFinesse + ($row['finesseSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraInitiative = $extraInitiative + ($row['initiativeSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraShield = $extraShield + ($row['shieldSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraParry = $extraParry + ($row['parrySkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraFoul = $extraFoul + ($row['foulSkill'] * $enchantEffective[$enchantTier])  * 2;
            $extraDodge = $extraDodge + ($row['dodgeSkill'] * $enchantEffective[$enchantTier])  * 2;
            $reducedWeight = $reducedWeight + ($row['weightReduction'] * $enchantEffective[$enchantTier])  * 2;
            
        }
        else{
            $sql = "SELECT * FROM enchants WHERE id = '$split[0]' AND (type='$type' OR type='all')";
            
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            if($row['damageBonusPercent'] > 0){
                $minDmg = $minDmg * (1 + (($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) / 100));
                $maxDmg = $maxDmg * (1 + (($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) / 100));
            }
            $minDmg = $minDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier]);
            $maxDmg = $maxDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier]);
            $accuracy = $accuracy + ($row['accuracyPercent'] * $enchantEffective[$enchantTier]);
            $armourPenetration = round($armourPenetration + ($row['armourPenetration'] * $enchantEffective[$enchantTier]));
            
            $extraOne = $extraOne + ($row['oneSkill'] * $enchantEffective[$enchantTier]);
            $extraTwo = $extraTwo + ($row['twoSkill'] * $enchantEffective[$enchantTier]);
            $extraBow = $extraBow + ($row['bowSkill'] * $enchantEffective[$enchantTier]);
            $extraXbow = $extraXbow + ($row['xBowSkill'] * $enchantEffective[$enchantTier]);
            $extraFinesse = $extraFinesse + ($row['finesseSkill'] * $enchantEffective[$enchantTier]);
            $extraInitiative = $extraInitiative + ($row['initiativeSkill'] * $enchantEffective[$enchantTier]);
            $extraShield = $extraShield + ($row['shieldSkill'] * $enchantEffective[$enchantTier]);
            $extraParry = $extraParry + ($row['parrySkill'] * $enchantEffective[$enchantTier]);
            $extraFoul = $extraFoul + ($row['foulSkill'] * $enchantEffective[$enchantTier]);
            $extraDodge = $extraDodge + ($row['dodgeSkill'] * $enchantEffective[$enchantTier]);
            $reducedWeight = $reducedWeight + ($row['weightReduction'] * $enchantEffective[$enchantTier]);
    		$extraBlockPercent = $extraBlockPercent + ($row['blockPercent'] * $enchantEffective[$enchantTier]); 
            unset($row);
            
            $sql = "SELECT * FROM enchants WHERE id = '$split[1]' AND (type='$type' OR type='all')";
            #echo $sql;
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            if($row['damageBonusPercent'] > 0){
                $minDmg = $minDmg * (1 + (($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) / 100));
                $maxDmg = $maxDmg * (1 + (($row['damageBonusPercent'] * $enchantEffective[$enchantTier]) / 100));
            }
            $minDmg = $minDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier]);
            $maxDmg = $maxDmg + ($row['damageBonusPoint'] * $enchantEffective[$enchantTier]);
            $accuracy = $accuracy + ($row['accuracyPercent'] * $enchantEffective[$enchantTier]);
            $armourPenetration = $armourPenetration + ($row['armourPenetration'] * $enchantEffective[$enchantTier]);
            
            $extraOne = $extraOne + ($row['oneSkill'] * $enchantEffective[$enchantTier]);
            $extraTwo = $extraTwo + ($row['twoSkill'] * $enchantEffective[$enchantTier]);
            $extraBow = $extraBow + ($row['bowSkill'] * $enchantEffective[$enchantTier]);
            $extraXbow = $extraXbow + ($row['xBowSkill'] * $enchantEffective[$enchantTier]);
            $extraFinesse = $extraFinesse + ($row['finesseSkill'] * $enchantEffective[$enchantTier]);
            $extraInitiative = $extraInitiative + ($row['initiativeSkill'] * $enchantEffective[$enchantTier]);
            $extraShield = $extraShield + ($row['shieldSkill'] * $enchantEffective[$enchantTier]);
            $extraParry = $extraParry + ($row['parrySkill'] * $enchantEffective[$enchantTier]);
            $extraFoul = $extraFoul + ($row['foulSkill'] * $enchantEffective[$enchantTier]);
            $extraDodge = $extraDodge + ($row['dodgeSkill'] * $enchantEffective[$enchantTier]);
            $reducedWeight = $reducedWeight + ($row['weightReduction'] * $enchantEffective[$enchantTier]);
    		$extraBlockPercent = $extraBlockPercent + ($row['blockPercent'] * $enchantEffective[$enchantTier]); 
            
        }
    }
    return array("minDmg"=>$minDmg,"maxDmg"=>$maxDmg,"accuracy"=>$accuracy,"penetration"=>$armourPenetration);
}

function mindEnchant($id){
	global $extraDamage,$extraDodge,$extraParry,$extraFoul,$extraShield,$extraBlockPercent;
	global $conn;
	
	$sql = "SELECT * FROM enchants WHERE id = '$id'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	$extraDamage += $row['damageBonusPoint'];
	
	$extraShield = $extraShield + $row['shieldSkill'];
    $extraParry = $extraParry + $row['parrySkill'];
    $extraFoul = $extraFoul + $row['foulSkill'];
    $extraDodge = $extraDodge + $row['dodgeSkill'];
	$extraBlockPercent = $extraBlockPercent + $row['blockPercent']; 
	
}

function getSpecials($part,$table){
    global $conn,$extraDodge,$extraInitiative;
    
    
    
    $part = $_SESSION['characterProperties']['' . $part . 'String'];
    $part = explode(":",$part);
    $part = $part[0];

$sql = "SELECT bonusStats FROM $table WHERE id='$part'";
$result = mysqli_query($conn,$sql);

$row = mysqli_fetch_assoc($result);

if ($row['bonusStats'] != NULL){
    $explodedStats = explode(",", $row['bonusStats']);
    foreach ($explodedStats as $stat){
        $explodedAgain = explode(":",$stat);
        if ($explodedAgain[0] == "Dodge"){
            $extraDodge = $extraDodge + $explodedAgain[1];
        }
        elseif($explodedAgain[0] == "Initiative"){
                $extraInitiative = $extraInitiative + $explodedAgain[1];
            }
        }
    }
    
}
function getTrinket(){
	global $conn;
	global $extraCrit,$extraStr,$extraDex,$extraRangeAcc;
	
	$eid = $_SESSION['characterProperties']['equipment_id'];
	$sql = "SELECT trinket FROM equipment where eid='$eid'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	$trinketId = $row['trinket'];
	
	$sql = "SELECT * FROM trinkets WHERE id='$trinketId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if ($row['extraCrit'] > 0){
			$extraCrit += $row['extraCrit'];
		}
		if ($row['extraStr'] > 0){
			$extraStr += $row['extraStr'];
		}
		if ($row['extraDex'] > 0){
			$extraDex += $row['extraDex'];
		}
		if ($row['extraRangeAcc'] > 0){
			$extraRangeAcc += $row['extraRangeAcc'];
	}
}

function getStats(){
global $extraStr,$extraDex,$extraCrit,$extraRangeAcc,$extraVit;
	
?>
    <tr>
        <td>
            <img src='frontend/design/images/character/icons/strength.png' class='skillIcon'>
            <input id='strength' type='number' name='strength' disabled value="<?php echo $_SESSION['characterProperties']['strength'] + $extraStr ?>"> 
            <a title='Strength increases the damage you do in melee combat. Each weapon has a strength requirement' class='tooltipLeft'><span title=''> 
                <span class='tooltipHover'>Strength</span>
            </span></a>
        </td>
    </tr>
    <tr>
        <td>
            <img src='frontend/design/images/character/icons/agility.png' class='skillIcon'>
            <input id='dexterity' type='number' name='dexterity' disabled value="<?php echo $_SESSION['characterProperties']['dexterity'] + $extraDex ?>">
            <a title='Dexterity greatly increases your ranged damage with bows and slightly increases your melee damage. It also gives you a boost to initiative, crit and dodge' class='tooltipLeft'><span title=''>
                <span class='tooltipHover'>Dexterity</span>
            </span></a>
        </td>
    </tr>
    <tr>
        <td>
            <img src='frontend/design/images/character/icons/stamina.png' class='skillIcon'>
            <input id='vitality' type='number' name='vitality' disabled value="<?php echo $_SESSION['characterProperties']['vitality'] + $extraVit ?>">
            <a title='Vitality determines your toughness, how much of a beating that you can withstand' class='tooltipLeft'><span title=''>
                <span class='tooltipHover'>Vitality</span>
            </span></a>
        </td>
    </tr>
    <tr>
        <td>
            <img src='frontend/design/images/character/icons/intellect.png' class='skillIcon'>
            <input id='intellect' type='number' name='intellect' disabled value="<?php echo $_SESSION['characterProperties']['intellect'] ?>">
            <a title='Intellect determines the amount of skillpoints you start with and receieve as you level up' class='tooltipLeft'><span title=''>
                <span class='tooltipHover'>Intellect</span>
            </span></a>
        </td>
    </tr>
  <?php
}

function AddArmourStatsToExtra(){
    global $conn,$extraDodge,$extraInitiative,$extraMinDamage,$extraMaxDamage,$extraOne,$extraTwo,$extraBow,$extraXbow,$extraFinesse,$extraInitiative,$extraLight,$extraHeavy,$extraShield,$extraParry,$extraFoul,$extraDodge,$reducedWeight,$extraBlockPercent,$extraCrit,$extraStr,$extraDex,$extraRangeAcc,$extraVit;

    $allArmours = array("legString","headString","chestString","armString");
    foreach($allArmours as $armourPart){
        $armourPart = explode(":",$_SESSION['characterProperties'][$armourPart]);
        $armourPart = $armourPart[0];

        $sql = "SELECT * FROM armours WHERE id='$armourPart'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        $extraOne+= $row['1hSkill'];
        $extraTwo+= $row['2hSkill'];
        $extraBow+= $row['bow'];
        $extraXbow+= $row['crossbow'];
        $extraFinesse+= $row['finesse'];
        $extraInitiative+= $row['initiative'];
        $extraShield+= $row['shield'];
        $extraParry+= $row['parry'];
        $extraFoul+= $row['foulPlay'];
        $extraDodge+= $row['dodge'];
        $extraLight+= $row['lightArmour'];
        $extraHeavy+= $row['heavyArmour'];
        $extraCrit+= $row['crit_Dmg'];
        $extraDex+= $row['dexterity'];
        $extraStr+= $row['strength'];
        $extraVit+= $row['vitality'];
        $extraMinDamage = $row['min_Dmg'];
        $extraMaxDamage = $row['max_Dmg'];
    }

}

function SaveVitalityFromGear(){
    global $conn,$extraVit;
    $charId = $_SESSION['characterProperties']['id'];
    $sql = "UPDATE characters SET vitalityFromGear='$extraVit' WHERE id='$charId'";
    mysqli_query($conn,$sql);
}

function getDetails(){
    global $conn;
	Global $extraDamage,$extraOne,$extraTwo,$extraBow,$extraXbow,$extraFinesse,$extraInitiative,$extraShield,$extraParry,$extraFoul,$extraDodge,$reducedWeight,$extraBlockPercent;
    global $extraCrit,$extraStr,$extraDex,$extraRangeAcc;
    global $attackMod;
    global $extraMinDamage,$extraMaxDamage;
    global $extraLight,$extraHeavy;
	

    $extraOne = 0;
    $extraTwo = 0;
    $extraBow = 0;
    $extraXbow = 0;
    $extraFinesse = 0;
    $extraInitiative = 0;
    $extraShield = 0;
    $extraParry = 0;
    $extraFoul = 0;
    $extraDodge = 0;
    $reducedWeight = 0;
    $extraBlockPercent = 0;
    $extraDamage = 0;
    
    AddArmourStatsToExtra();
    
	getTrinket();

    SaveVitalityFromGear();
	
    $offHandText = "";
	$strength = $_SESSION['characterProperties']['strength'];
	$dexterity = $_SESSION['characterProperties']['dexterity'];
	$one_handed = $_SESSION['characterProperties']['one_handed'];
	$two_handed = $_SESSION['characterProperties']['two_handed'];
	$shield = $_SESSION['characterProperties']['shield'];
	$parry = $_SESSION['characterProperties']['parry'];
	$finesse = $_SESSION['characterProperties']['finesse'];
	$foul_play = $_SESSION['characterProperties']['foul_play'];
	$light_armour = $_SESSION['characterProperties']['light_armour'];
	$heavy_armour = $_SESSION['characterProperties']['heavy_armour'];
	$bow = $_SESSION['characterProperties']['bow'];
	$crossbow = $_SESSION['characterProperties']['crossbow'];
    $weight = $_SESSION['characterProperties']['weight'];

    $totalStr = $extraStr + $strength;
	$totalDex = $extraDex + $dexterity;
	
    //MODIFIERS
    $sql = "SELECT * FROM modifiers";
    $result = mysqli_query($conn,$sql);
    $modRow = mysqli_fetch_assoc($result);
    $attackMod = (($totalStr * $modRow['attackMod']) + ($totalDex * $modRow['dexAttackMod'])) + 1;
    $rangedAttackMod = ($totalDex * ($modRow['dexAttackMod']*2) + 1);
    $weaponSkillDivider = $modRow['weaponSkillDivider'];
    $blockMod = $modRow['blockMod'];
    
	$weightDodgeMod = $modRow['weightDodgeMod'];
    $weightParryMod = $modRow['weightParryMod'];
    $weightFoulMod = $modRow['weightFoulMod'];
	$armourPercent = $modRow['armourPercent'];
    
    

	#$extraCrit = 0;
	#$extraRangeAcc = 0;
	#$extraStr = 0;
	#$extraDex = 0;
	
	
	if($_SESSION['characterProperties']['mindEnchant'] != 0){
		mindEnchant($_SESSION['characterProperties']['mindEnchant']);
	}
    
    
    
    
    
	
    if ($_SESSION['characterProperties']['feet'] != "Nothing"){
        getSpecials("feet", "armours");
    }
	
	
	$usableSkills = array();
	echo "<table id=\"statusTable\" border=\"0\" style=\"width:100%; margin-top:0px;\">
			<tbody>";
	    
        
        
		
		
		$right_hand = $_SESSION['characterProperties']['right_handString'];
        $explode = explode(":", $right_hand);
        $right_handId = $explode[0];
        $right_handEnchants = $explode[1];
        $left_hand = $_SESSION['characterProperties']['left_handString'];
        $explode = explode(":", $left_hand);
        $left_handId = $explode[0];
        $left_handEnchants = $explode[1];
        $secondary = $_SESSION['characterProperties']['secondaryString'];
        $explode = explode(":", $secondary);
        $secondary_Id = $explode[0];
        $secondary_Enchants = $explode[1];
        
		//WEAPONS
		$sql = "SELECT * FROM weapons where id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $right_handId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		
		$wepChanceHit = $row['chance_hit'];
		$wepSkillReq =	$row['skill'];
		$wepType =		$row['type'];
		$wepMinDmg =	($row['min_dmg'] + $extraDamage + $extraMinDamage) * $attackMod;
		$wepMaxDmg =	($row['max_dmg'] + $extraDamage + $extraMaxDamage)* $attackMod;
        $wepPen =       $row['armourPenetration'];
		$wepTier =      $row['enchantTier'];
        
		if($wepType == "2h"){
		    #echo $wepSkillReq;
            
			$wepSkill = $two_handed;
            #echo " " . $wepSkill;
			$wepHit = $wepChanceHit + (($wepSkill-$wepSkillReq) * $weaponSkillDivider) - $weight;
			$twoEnchanted = EnchantBonuses($right_handEnchants,"melee",$wepMinDmg,$wepMaxDmg,$wepHit,$wepPen,$wepTier);
            
            
			#echo "<tr><td colspan=\"2\" class=\"equipmentNameList\">Hit chance: " . round($enchanted['accuracy']) . "%</td>";
			#echo "<td colspan=\"3\" class=\"equipmentNameList\">Dmg: " . round($enchanted['minDmg']) . " - " . round($enchanted['maxDmg']) . "| Pen: " . $enchanted['penetration'] . "</td>";
			#echo "</tr>";
		}
		else{
			$wepSkill = $one_handed;
			$wepHit = $wepChanceHit + (($wepSkill-$wepSkillReq) * $weaponSkillDivider) - $weight;
			
            
            
            
			if($right_handId != 1 && $left_handId == 1){
			    
			}
            else{
				$sql = "SELECT * FROM weapons where id=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $left_handId);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
                
                if($row['type'] != "shield"){
    				
    				$offWepChanceHit = 	                $row['chance_hit'];
    				$offWepSkillReq =	                $row['skill'];
    				$offWepType =		                $row['type'];
    				$offWepMinDmg =		                (($row['min_dmg'] + $extraDamage + $extraMinDamage)*0.6) * $attackMod;
				    $offWepMaxDmg =		                (($row['max_dmg'] + $extraDamage + $extraMaxDamage)*0.6) * $attackMod;
                    $offWepPen =                        $row['armourPenetration'];
					$offWepTier =                       $row['enchantTier'];
    				$wepMinDmg	=		                $wepMinDmg*0.8;
    				$wepMaxDmg	=		                $wepMaxDmg*0.8;
    				
    				$offWepHit = $offWepChanceHit + (($wepSkill-$offWepSkillReq) * $weaponSkillDivider) - $weight;
    			    $offEnchanted = EnchantBonuses($left_handEnchants,"melee",$offWepMinDmg,$offWepMaxDmg,$offWepHit,$offWepPen,$offWepTier);

    			}
    			else{
    			    $offEnchanted = EnchantBonuses($left_handEnchants,"shield",0,0,0,0,$row['enchantTier']);
    				$blockChance = 		$row['block_chance'];
					$blockAmount = 		$row['block_amount'];
    			}
			}
            $mainEnchanted = EnchantBonuses($right_handEnchants,"melee",$wepMinDmg,$wepMaxDmg,$wepHit,$wepPen,$wepTier);
            
            #var_dump($mainEnchanted);
            #echo "<tr><td colspan=\"2\" class=\"equipmentNameList\">Right Hit: " . round($mainEnchanted['accuracy'])  . "%</td>";
            #echo "<td colspan=\"3\" class=\"equipmentNameList\">Dmg: " . round($mainEnchanted['minDmg']) . " - " . round($mainEnchanted['maxDmg']) . " | Pen: " . $mainEnchanted['penetration'] . "</td>";
            #echo "</tr>";
            #echo $offHandText;
            
		}



        //SECONDARY
        $secondaryText = "";
        if ($_SESSION['characterProperties']['secondary'] != "Nothing"){
            $sql = "SELECT * FROM weapons where id=?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "i", $secondary_Id);
            mysqli_stmt_execute($stmt);
            $result = $stmt->get_result();
            $row = mysqli_fetch_assoc($result);
            
            $secChanceHit = $row['chance_hit'];
            $secSkillReq =  $row['skill'];
            $secType =      $row['type'];
            $secMinDmg =    $row['min_dmg'] + $extraDamage + $extraMinDamage;
            $secMaxDmg =    $row['max_dmg'] + $extraDamage + $extraMaxDamage;
            $ammo =         $row['ammo'];
            $secPenetration =  $row['armourPenetration'];
			$secTier =          $row['enchantTier'];
            
            $secSkill =     $_SESSION['characterProperties'][$secType];
            
            $secChanceHit = $secChanceHit + (($secSkill-$secSkillReq) * $weaponSkillDivider) - $weight/2;
            
            $secEnchanted = EnchantBonuses($secondary_Enchants,"ranged",$secMinDmg,$secMaxDmg,$secChanceHit,$secPenetration,$secTier);

        }

        $armourSlots = array("head","chest","arm","leg","feet");
        foreach($armourSlots as $slot){
            $item = $_SESSION['characterProperties'][$slot . 'String'];
            $enchants = explode(":",$item);
            $itemId = $enchants[0];
            $enchants = $enchants[1];
            EnchantBonuses($enchants, "armour", 0, 0, 0, 0,$itemId);
        }








        
        //MODIFIERS
        $initiative = $_SESSION['characterProperties']['initiative'] + $extraInitiative;
        $foul_play = $_SESSION['characterProperties']['foul_play'] + $extraFoul;
        $dodge = $_SESSION['characterProperties']['dodgeSkill'] + $extraDodge;
        $parry = $_SESSION['characterProperties']['parry'] + $extraParry;
        $finesse = $_SESSION['characterProperties']['finesse'] + $extraFinesse;
        $shield = $_SESSION['characterProperties']['shield'] + $extraShield;
        
        if ($reducedWeight < $weight){
            
        }
        else{
            $reducedWeight = $weight;
        }
        
        #Recalculate accuracy based on skillenchants
        if ($wepType == "2h"){
            if($extraTwo > 0 || $reducedWeight != 0){
                
                $wepChanceHit = round($twoEnchanted['accuracy'] + ($extraTwo * $weaponSkillDivider) + $reducedWeight);
            }
            else{
                $wepChanceHit = round($twoEnchanted['accuracy']);
            }
            echo "<tr><td colspan=\"1\" class=\"equipmentNameList\">Right hit chance: " . $wepChanceHit . "% - ";
            echo "Dmg: " . round($twoEnchanted['minDmg']) . " - " . round($twoEnchanted['maxDmg']) . " | Pen: " . round($twoEnchanted['penetration']) . "</td>";
            echo "</tr>";
        }
        else{
            if($extraOne > 0 || $reducedWeight != 0){
                $wepChanceHit = round($mainEnchanted['accuracy'] + ($extraOne * $weaponSkillDivider) + $reducedWeight);
            }
            else{
                $wepChanceHit = round($mainEnchanted['accuracy']);
            }
            
            if (isset($offWepType)){
                if($offWepType != "shield"){
                    if($extraOne > 0 || $reducedWeight != 0){
                        $offWepChanceHit = round($offEnchanted['accuracy'] + ($extraOne * $weaponSkillDivider) + $reducedWeight);
                    }
                    else{
                        $offWepChanceHit = round($offEnchanted['accuracy']);
                    }
                    $offHandText .= "<tr><td colspan=\"2\" class=\"equipmentNameList\">Left hit chance: " . $offWepChanceHit . "%</td>";
                    $offHandText .= "<td colspan=\"2\" class=\"equipmentNameList\">Dmg: " . round($offEnchanted['minDmg']) . " - " . round($offEnchanted['maxDmg']) . " | Pen: " . round($offEnchanted['penetration']) . "</td>";
                    $offHandText .= "</tr>";
                }
            }
            echo "<tr><td colspan=\"2\" class=\"equipmentNameList\">Right Hit: " . $wepChanceHit . "%</td>";
            echo "<td colspan=\"2\" class=\"equipmentNameList\">Dmg: " . round($mainEnchanted['minDmg']) . " - " . round($mainEnchanted['maxDmg']) . " | Pen: " . round($mainEnchanted['penetration']) . "</td>";
            echo "</tr>";
            echo $offHandText;
        }
        
        if (isset($secChanceHit)){
            if($secChanceHit > 0 || $reducedWeight != 0){
                $secChanceHit = round($secChanceHit + ($extraBow * $weaponSkillDivider) + $reducedWeight);
            }
            else{
                $wepChanceHit = round($secChanceHit);
            }
            if ($secType == "bow"){
                $secondaryText .= "<tr><td colspan=\"2\" class=\"equipmentNameList\"><a title=\"The hitchance is based on how close you are to your target\" class=\"tooltipLeft\"><span title=\"\">
                Sec Hit: " . round($secChanceHit + $extraRangeAcc)  . "-" . round(($secChanceHit + $extraRangeAcc)*1.4) . "%";
                $secondaryText .= "</span></a></td>";
                $secondaryText .= "<td colspan=\"2\" class=\"equipmentNameList\">Dmg: " . round($secEnchanted['minDmg'] * $rangedAttackMod) . " - " . round($secEnchanted['maxDmg'] * $rangedAttackMod) . " | Pen: " . round($secEnchanted['penetration']) . " | Ammo: " . $ammo . "</td>";
                $secondaryText .= "</tr>";
            }
            elseif($secType == "crossbow"){
                $secondaryText .= "<tr><td colspan=\"2\" class=\"equipmentNameList\"><a title=\"The hitchance is based on how close you are to your target\" class=\"tooltipLeft\"><span title=\"\">
                Sec Hit: " . round($secChanceHit + $extraRangeAcc)  . "-" . round(($secChanceHit + $extraRangeAcc)*1.4) . "%";
                $secondaryText .= "</span></a></td>";
                $secondaryText .= "<td colspan=\"2\" class=\"equipmentNameList\">Dmg: " . round($secEnchanted['minDmg']) . " - " . round($secEnchanted['maxDmg']) . " | Pen: " . round($secEnchanted['penetration']) . " | Ammo: " . $ammo . "</td>";
                $secondaryText .= "</tr>";
            }
        }
        
        echo $secondaryText;
        
        
        if (isset($blockChance)){
			$blockPercent = $blockAmount + (($shield) * $blockMod);
            $blockText = "Block chance: " . ($blockChance+$extraBlockPercent) . "% Absorb: " . $blockPercent . "%";
            array_push($usableSkills,$blockText);
        }

        $foulPlayChance = $foul_play * $modRow['foul_playMod'];
		$foulPlayChance = round($foulPlayChance - (((($weight-$reducedWeight) * $weightFoulMod)/ 100) * $foulPlayChance),1);
        if ($foulPlayChance >= $modRow['maxFoul']){
            $foulPlayChance = $modRow['maxFoul'];
        }
        
        $dodgeChance = ($totalDex * ($modRow['dodgeMod']*2)) + ($dodge * $modRow['dodgeMod']);
        $dodgeChance = round($dodgeChance - (((($weight-$reducedWeight) * $weightDodgeMod)/ 100) * $dodgeChance),1);
        if ($dodgeChance >= $modRow['maxDodge']){
            $dodgeChance = $modRow['maxDodge'];
        }
        
        $parryChance = $parry * $modRow['parryMod'];
		$parryChance = round($parryChance - (((($weight-$reducedWeight) * $weightParryMod)/ 100) * $parryChance),1);
        if ($parryChance >= $modRow['maxParry']){
            $parryChance = $modRow['maxParry'];
        }
        
        $critChance = (($totalDex * $modRow['critMod']) + ($finesse * $modRow['finesseMod']));
        if ($critChance >= $modRow['maxCrit']){
            $critChance = $modRow['maxCrit'];
        }
		
		#WEIGHT PENALTIES
		
		
		
		
        
		if($parryChance > 0){
			$parryText = "Parry chance: " . round($parryChance,1) . "%";
			array_push($usableSkills,$parryText);
		}
		if(round($foulPlayChance) > 0){
			$foulText = "Foul play chance: " . round($foulPlayChance,1) . "%";
			array_push($usableSkills,$foulText);
		}
		
		$dodgeText = "Dodge chance: " . round($dodgeChance,1) . "%";
		array_push($usableSkills,$dodgeText);

		$critText = "Crit chance: " . round($critChance,1) . "%";
        array_push($usableSkills,$critText);

        $double = round(($initiative * $modRow['doubleIniMod']) + ($totalDex * $modRow['doubleDexMod']),1);
        $doubleAttackText = "Double attack chance: " . $double . "%";
        array_push($usableSkills,$doubleAttackText);


		
		$i = 0;
        echo "</tbody></table>";
        #echo "<br>";
        echo "<table id=\"statusTable2\" border=\"0\" style=\"width:100%; margin-top:0px;\">
            <tbody><tr style='height:20px;'>";
            
		foreach ($usableSkills as $skill){
			if ($i % 2 == 0){
				echo "</tr><tr>";
			}
			echo "<td colspan=\"1\" class=\"equipmentNameList\">" . $skill . "</td>";
			$i++;
		}
		if($extraCrit > 0){
			if ($i % 2 == 0){
				echo "</tr><tr>";
			}
			echo "<td colspan=\"1\" class=\"equipmentNameList\">Extra crit dmg: " . $extraCrit . "%</td>";
            $i++;
		}

        $weightText = round($weight-$reducedWeight);
        if($weightText > 0){
            if ($i % 2 == 0){
                echo "</tr><tr>";
            }
            echo "<td colspan=\"1\" class=\"equipmentNameList\">Total Weight: "  . $weightText . "</td>";
        }
        echo "</tr></tbody></table>";
				
		
        
        
        $_SESSION['extraStats']['one'] = $extraOne;
        $_SESSION['extraStats']['two'] = $extraTwo;
        $_SESSION['extraStats']['bow'] = $extraBow;
        $_SESSION['extraStats']['xBow'] = $extraXbow;
        $_SESSION['extraStats']['finesse'] = $extraFinesse;
        $_SESSION['extraStats']['initiative'] = $extraInitiative;
        $_SESSION['extraStats']['shield'] = $extraShield;
        $_SESSION['extraStats']['parry'] = $extraParry;
        $_SESSION['extraStats']['foul'] = $extraFoul;
        $_SESSION['extraStats']['dodge'] = $extraDodge;
        $_SESSION['extraStats']['lightArmour'] = $extraLight;
        $_SESSION['extraStats']['heavyArmour'] = $extraHeavy;
        
        
}

if(isset($_GET['reloadStats'])){
    getDetails();
}
elseif(isset($_GET['reload'])){
	getTrinket();
	getStats();
}

?>