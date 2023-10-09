<?php
global $conn;
$name = $_GET['item_name'];
$type = $_GET['type'];
$enchantEffective = array("80","100","120","140","160");
require_once(__ROOT__."/backend/other/itemFunctions.php");

$split = explode(":",$name);
        $returned = getItemWithoutNameWithStats($name,$type,0);
        $name = $returned['name'];
        $enchants = $returned['enchants'];
        
        if ($enchants == ""){
            $enchants = "This item is not yet enchanted";
        }
        else{
            $enchants = "<strong>Enchants: (These are not calculated into the information above)</strong><br>" . $enchants;
        }
        
        
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
    $row = mysqli_fetch_assoc($result);
    $extras = "";
    if($table == "weapons"){
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
            $hitChance = $row['chance_hit'] . " - " . $row['chance_hit']*1.4;
            
            
            $hitChance = $row['chance_hit'];

            $extras = "<br>Ammunition: " . $row['ammo'] . "<br>";
            $extras .= "Reload time: " . $row['reloadTime'];
        }
        $requirement = "";
        $yourStr = "";
        if ($row['strReq'] > 0){
            if ($_SESSION['characterProperties']['strength'] >= $row['strReq']) {
                $requirement = "<br><a style=color:green>Strength required: " . $row['strReq'] . "</a><br>";
            }
            else{
                $requirement = "<br><a style=color:red>Strength required: " . $row['strReq'] . "</a><br>";
                
            }
            $yourStr = "<strong>Your Strength: " . $_SESSION['characterProperties']['strength'] . "</strong><br>";
        }
        if($row['type'] != "shield"){

                if($row['type'] == "1h"){
                    $skill = $_SESSION['characterProperties']['one_handed'];
                }
                elseif($row['type'] == "2h"){
                    $skill = $_SESSION['characterProperties']['two_handed'];
                }
                elseif($row['type'] == "bow"){
                    $skill = $_SESSION['characterProperties']['bow'];
                }
                elseif($row['type'] == "crossbow"){
                    $skill = $_SESSION['characterProperties']['crossbow'];
                }
                $information = "<div id=storeInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>Recommended Minimum Skill: " . $row['skill'] . "<br>" . 
                "<strong>Your " . $itemType . " skill: " . $skill . "</strong><br>" . 
                $requirement . $yourStr . "<br> Damage: " . $row['min_dmg'] . " - " . $row['max_dmg'] . "<br>" . 
                "Critical hit damage: " . (100 + $row['crit_dmg']) . "%<br>" . "Armour penetration: " . $row['armourPenetration'] . "<br>" . "Weapon hit chance: " . $hitChance . "%<br>" . $extras . "</strong><br>" . 
                "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>" . 
                "Price: " . $row['price'] . "g" . 
                "<br><br>Description:<br>" . $row['description'] . "";

                if($row['userCrafted'] != 0){
                    include_once(__ROOT__.'/backend/crafting/craftingFunctions.php');
                    $information .= "This is a user crafted weapon made of these materials:<br>";
                    $ex = explode(",",$row['parts']);
                    
                    foreach ($ex as $part){
                        $curr = getPart($part);
                        $information .= "<a href='index.php?page=view-part&partId=" .$curr['id'] . "'>" . $curr['name'] . "</a><br>";
                    }
                }
                    $information .= "<br>" . $enchants . "</div>";
        }
        else{
            $itemType = "Shield";
            $blockOrReduction = "Block Chance: " . $row['block_chance'] . "%";
            $skillText = "Recommended Minimum Skill: " . $row['skill'];
            $canEquip = "<br><strong>Your shield skill: " . $_SESSION['characterProperties']['shield'] . "</strong>";
            $information = "<div id=storeInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
                         "<br>Price: " . $row['price'] . "g"  . "<br><br>Description:<br>" . $row['description'] . "<br><br>" . $enchants . "</div>";
                        
        }
        }
        elseif ($table == "armours"){
                $canEquip = "";
                
                $blockOrReduction = "Damage Reduction: " . $row['damage_reduction']*4 . "% / " . $row['damage_reduction'] . " damage";
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



                $itemType = $row['type'];
            
                $information = "<div id=storeInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
                "<br>" . $bonusStats . 
                "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>" . $statText . "<br>Price: " . $row['price'] . "g"  . "<br><br>Description:<br>" . $row['description'] . "<br><br>" . $enchants . "</div>";  
                    

            }
    if (isset($row['picture'])){
        $information .= "<div id=storePicture><img class=storePicture src=frontend/design/images/items/" . $row['picture'] . "> </div>";
    }
    else{
        $information .= "<div id=storePicture></div>";
    } 
    echo $information;