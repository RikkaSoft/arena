<?php
global $conn;
if($_GET['type'] == "1"){
	$type = 1;
}
elseif($_GET['type'] == "2"){
	$type = 2;
}	
else{
	echo "You are trying to cheat";
	exit;
}
$enchantEffective = array("80","100","120","140","160");
require_once(__ROOT__."/backend/other/itemFunctions.php");
$name = $_GET['name'];
$split = explode(":",$name);
        $returned = getItemWithoutNameWithStats($name,$type,0);
        $name = $returned['name'];
        $enchants = $returned['enchants'];
        
        
    if ($type == 1){
        $sql = "SELECT * FROM weapons WHERE id=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $split[0]);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $table = "weapons";
    }
    else{
        $sql = "SELECT * FROM armours WHERE id=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $split[0]);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $table = "armours";
    }
    
    if ($table == "weapons"){
        $row = mysqli_fetch_assoc($result);
            $itemSubType = $table;
            if ($row['type'] == "1h"){
                $itemType = "One-handed weapon";
                
                $skillReq = $row['skill'];
                $hitChance = $row['chance_hit'];
                
            }
            elseif ($row['type'] == "2h"){
                $itemType = "Two-handed weapon";
                
                $skillReq = $row['skill'];
                $hitChance = $row['chance_hit'];
                
            }
            elseif ($row['type'] == "bow" || $row['type'] == "crossbow"){
                $itemType = "Secondary Weapon";
                
                $skillReq = $row['skill'];
                $hitChance = $row['chance_hit'];
                $itemSubType = "secondarys";
                
            }
			
			if(isset($_SESSION['characterProperties']['strength'])){
				$strength = "<br>Your Strength: " . $_SESSION['characterProperties']['strength'];
			}
			else{
				$strength = "";
			}

            if ($enchants != ""){
                $enchants = "<br>Enchants: <br>" . $enchants;
            }
            else{
                $enchants = "";
            }
			if($row['type'] == "shield"){
				$itemType = "Shield";
				$information = "<strong id='strong'>" . $name . "</strong><br>Item type: " . $itemType . "<br>Block Chance: " .$row['block_chance'] . "%<br>" . "Damage Reduction: " . $row['block_amount'] . "%<br>" . $enchants;
			}
			else{
				
	            $information = "<strong id='strong'>" . $name . "</strong><br>Item type: " . $itemType . "
	            <br>Recommended Minimum Skill: " . $row['skill'] . 
	            "<br>Strength Required " . $row['strReq'] .
	            $strength . 
	            "<br> Damage: " . $row['min_dmg'] . " - " . $row['max_dmg'] . "<br>" . 
	            "Critical hit damage: " . (100 + $row['crit_dmg']) . "%<br>" . "Weapon hit chance: " . $hitChance . "%<br>" . 
	            "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br>Price: " . $row['price'] . "g<br>" . 
	            $enchants;
            
            }
            if (isset($row['picture'])){
                $information .= "<img class=storePicture src=frontend/design/images/items/" . $row['picture'] . ">";
            }
            
            echo $information;
    }
    elseif($table == "armours"){
        $row = mysqli_fetch_assoc($result);
        $canEquip = "";
        $itemSubType = $row['item_type'];
    	
		$block = "Damage Reduction: " . $row['damage_reduction'] * 4 . "% / " . $row['damage_reduction'] . " damage";
		
        $itemType = $row['type'];
        if ($enchants != ""){
            $enchants = "<br>Enchants: " . $enchants;
        }
        else{
            $enchants = "";
        }
		$extra = "";
		if(isset($row['bonusStats'])){
			$ex = explode(",",$row['bonusStats']);
			foreach($ex as $e){
				$ex2 = explode(":",$e);
				$extra .= $ex2[0] . " skill: " . $ex2[1]. "<br>";
			}
        }
        
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

        $information = "<strong id='strong'>" . $name . "</strong><br>Item type: " . $itemType . "<br>Required Skill: " . $row['skill'] . "<br>" . $block . "<br>Weight: " . $row['weight'] . "<br>" . $extra .
        "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br>" . $statText . "Price: " . $row['price'] . "g<br>" . $enchants;
        
        if (isset($row['picture'])){
            $information .= "<img class=storePicture src=frontend/design/images/items/" . $row['picture'] . ">";
        }
        echo $information;
}


?>