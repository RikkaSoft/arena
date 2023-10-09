<?php 


function getInventory(){
    global $conn;
            $right_hand =           $_SESSION['characterProperties']['right_hand'];
            $left_hand =            $_SESSION['characterProperties']['left_hand'];
            $secondary =            $_SESSION['characterProperties']['secondary'];
            $head =                 $_SESSION['characterProperties']['head'];
            $chest =                $_SESSION['characterProperties']['chest'];
            $arm =                  $_SESSION['characterProperties']['arm'];
            $leg =                  $_SESSION['characterProperties']['leg'];
            $feet =                 $_SESSION['characterProperties']['feet'];
            echo "<div id=\"enchantressEquipment\">";
            echo "<strong>Your equipped items:</strong> <br>";
            if ($right_hand != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['right_handString'] . " class='equipWeapons'>" . $right_hand . "</a><br>";
            }
            if ($left_hand != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['left_handString'] . " class='equipWeapons'>" . $left_hand . "</a><br>";
            }
            if ($secondary != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['secondaryString'] . " class='equipWeapons'>" . $secondary . "</a><br>";
            }
            if ($head != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['headString'] . " class='equipArmours'>" . $head . "</a><br>";
            }
            if ($chest != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['chestString'] . " class='equipArmours'>" . $chest . "</a><br>";
            }
            if ($arm != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['armString'] . " class='equipArmours'>" . $arm . "</a><br>";
            }
            if ($leg != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['legString'] . " class='equipArmours'>" . $leg . "</a><br>";
            }
            if ($feet != "Nothing"){
                echo "<a href=# id=" . $_SESSION['characterProperties']['feetString'] . " class='equipArmours'>" . $feet . "</a><br>";
            }
			
			if($_SESSION['characterProperties']['level'] >= 10){
				echo "<br>";
				echo "<a href=# id=" . "mindEnchant" . "><strong>Superior Mind Enchantment</strong></a><br>"; #$_SESSION['characterProperties']['mindEnchant']
			}
            echo "</div>";

            $inv_id = $_SESSION['characterProperties']['inventory_id'];
            $sql = "SELECT * FROM inventory WHERE iid = '$inv_id'";
            $result=mysqli_query($conn, $sql);
            $equipment = mysqli_fetch_assoc($result);

            $weapons_inv =          $equipment['weapons'];
            $secondary_inv =        $equipment['secondarys'];
            $head_inv =             $equipment['heads'];
            $chest_inv =            $equipment['chests'];
            $arm_inv =              $equipment['arms'];
            $leg_inv =              $equipment['legs'];
            $feet_inv =             $equipment['feets'];
            
            function getItemOption($itemStr,$nameS,$type){
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
                        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        if($row['prefix'] != ""){
                            $prefixS = $row['prefix'] . " ";
                        }
                    }
                    elseif($prefix == 1 && $suffix != 1){
                        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        if($row['suffix'] != ""){
                            $suffixS = " of " . $row['suffix'];
                        }
                    }
                    elseif($prefix != 1 && $suffix != 1){
                        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        if($row['prefix'] != ""){
                            $prefixS = $row['prefix'] . " ";
                        }
                
                        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        if($row['suffix'] != ""){
                            $suffixS = " of " . $row['suffix'];
                        }
                        
                    }
                    
                    return $prefixS . $nameS . $suffixS;
                 #}
            }
            echo "<div id=\"enchantressInventory\">";
            echo "<strong>Items in your inventory:</strong><br>";
            
            $weapons = explode(",", $weapons_inv);            
            foreach ($weapons as $wep){
                $exploded = explode(":", $wep);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,type,enchantType FROM weapons WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($wep,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$wep class='invWeapons'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            $secondarys = explode(",", $secondary_inv);
            foreach ($secondarys as $sec){
                $exploded = explode(":", $sec);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,enchantType FROM weapons WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($sec,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$sec class='invWeapons'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            $heads = explode(",", $head_inv);
            foreach ($heads as $hea){
                $exploded = explode(":", $hea);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($hea,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$hea class='invArmours'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            $chests = explode(",", $chest_inv);
            foreach ($chests as $che){
                if ($che != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$che'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($che,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$che class='invArmours' >" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            $arms = explode(",", $arm_inv);
            foreach ($arms as $ar){
                if ($ar != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$ar'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($ar,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$ar class='invArmours'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            $legs = explode(",", $leg_inv);
            foreach ($legs as $le){
                if ($le != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$le'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($le,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$le class='invArmours'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }            
            $feets = explode(",", $feet_inv);
            foreach ($feets as $fee){
                if ($fee != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$fee'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($fee,$row['name'],$row['enchantType']);
                        echo "<a href=# id=$fee class='invArmours'>" . $returnedItem . "</a>";
                        echo "<br>";
                }
            }
            
            echo "</div>";
            
            ?>
            <script>
            $('.invWeapons').on('click', function() {
              var name = ( this.id );
              $('#enchantressArea').hide().load('index.php?opage=enchantressFunctions&nonUI&itemName=' + name + '&itemType=1&itemLoc=inv').fadeIn('500');
            });
            $('.invArmours').on('click', function() {
              var name = ( this.id );
              $('#enchantressArea').hide().load('index.php?opage=enchantressFunctions&nonUI&itemName=' + name + '&itemType=2&itemLoc=inv').fadeIn('500');
            });
            $('.equipWeapons').on('click', function() {
              var name = ( this.id );
              $('#enchantressArea').hide().load('index.php?opage=enchantressFunctions&nonUI&itemName=' + name + '&itemType=1&itemLoc=equip').fadeIn('500');
            });
            $('.equipArmours').on('click', function() {
              var name = ( this.id );
              $('#enchantressArea').hide().load('index.php?opage=enchantressFunctions&nonUI&itemName=' + name + '&itemType=2&itemLoc=equip').fadeIn('500');
            });
            $('#mindEnchant').on('click', function() {
              $('#enchantressArea').hide().load('index.php?opage=enchantressFunctions&nonUI&getMindEnchant').fadeIn('500');
            });
            </script>
            <?php
            
}

function viewItem($name,$type,$itemLoc){
global $conn;

$enchantEffective = array("80","100","120","140","160");

if($itemLoc == "equip"){
     $_SESSION['other']['equipOrInventory'] = "equipment";
}
elseif($itemLoc == "inv"){
    $_SESSION['other']['equipOrInventory'] = "inventory";
}
require_once(__ROOT__."/backend/other/itemFunctions.php");
$split = explode(":",$name);
        $returned = getItemWithoutNameWithStats($name,$type,1);
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
                $information = "<div id=enchantInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>Recommended Minimum Skill: " . $row['skill'] . "<br>" . 
                "<strong>Your " . $itemType . " skill: " . $skill . "</strong><br>" . 
                $requirement . $yourStr . "<br> Damage: " . $row['min_dmg'] . " - " . $row['max_dmg'] . "<br>" . 
                "Critical hit damage: " . (100 + $row['crit_dmg']) . "%<br>" . "Armour penetration: " . $row['armourPenetration'] . "<br>" . "Weapon hit chance: " . $hitChance . "%<br>" . $extras . "</strong><br>" . 
                "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>" . 
                "Price: " . $row['price'] . "g" . 
                "<br><br>Description:<br>" . $row['description'] . "<br><br>" . $enchants . "</div>";
        }
        else{
            $itemType = "Shield";
            $blockOrReduction = "Block Chance: " . $row['block_chance'] . "%";
            $skillText = "Recommended Minimum Skill: " . $row['skill'];
            $canEquip = "<br><strong>Your shield skill: " . $_SESSION['characterProperties']['shield'] . "</strong>";
            $information = "<div id=enchantInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
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

            
                $information = "<div id=enchantInformation><h4>" . $name . "</h4>" . "Item type: " . $itemType . "<br>" . $skillText . $canEquip . "<br><br>" . $blockOrReduction . "<br>Weight: " . $row['weight'] .
                "<br>" . $bonusStats . $statText . "<br>" . 
                "Enchantment Effectiveness: " . $enchantEffective[$row['enchantTier']-1] . "%<br><br>" . 
                "<br>Price: " . $row['price'] . "g"  . "<br><br>Description:<br>" . $row['description'] . "<br><br>" . $enchants . "</div>";       
            }
    $information .= "<div id=enchantressConfirmArea>";
    echo "<a href='#' class='itemType' id='" . $type . "'hidden></a>";
    echo $information;
    
   
    ?>
    <script>
        $('#prefix').on('click', function() {
          var itemStr = $('.chosenItem').attr('id');
          var itemType = $('.itemType').attr('id');
          $('#enchantressConfirmArea').hide().load('index.php?opage=enchantressFunctions&nonUI&selItemName=' + itemStr + '&type=prefix&itemType=' + itemType).fadeIn('500');
          var text = $('#prefix');
          $('#prefix').css("color","blue");
          $('#suffix').css("color","green");
          animateText(text);
          
          
        });
        $('#suffix').on('click', function() {
          var itemStr = $('.chosenItem').attr('id');
          var itemType = $('.itemType').attr('id');
          $('#enchantressConfirmArea').hide().load('index.php?opage=enchantressFunctions&nonUI&selItemName=' + itemStr + '&type=suffix&itemType=' + itemType).fadeIn('500');
          var text = $('#suffix');
          $('#suffix').css("color","blue");
          $('#prefix').css("color","green");
          animateText(text);
        });
        
        function animateText(text){
            text.animate({fontSize: '1.2em'}, "fast");
            text.animate({fontSize: '1em'}, "fast");
        }
    </script>
    <?php
    
}

function viewEnchant($itemStr,$enchantType,$itemType){
    #echo $itemStr . $enchantType;
    $split = explode(":", $itemStr);
    $item = $split[0];
    $split = explode(";",$split[1]);
    $prefixId = $split[0];
    $suffixId = $split[1];
    $double = 0;
    
    if ($enchantType == "prefix"){
        if ($prefixId == 1){
            $enchant = "No Enchantment<br><br>";
        }
        else{
            $enchant = getEnchantment($prefixId,"prefix",$item,$itemType);
            $double = 1;
        }
    }
    else{
        if ($suffixId == 1){
            $enchant = "No Enchantment<br><br>";
        }
        else{
            $enchant = getEnchantment($suffixId,"suffix",$item,$itemType);
            $double = 1;
        }
    }
    $cost = getCost($item,$itemType,$double);
    
    echo "<h4 style='text-align:center;'>Current enchantment</h4>";
    
    if (isset($enchant['name'])){
        echo $enchant['name'] . $enchant['effects'];
    }
    else{
        echo $enchant;
    }
	if($double == 1){
		$buttonText = "Replace Enchant";
	}
	else{
		$buttonText = "Enchant item";
	}
    echo "<p>This item can be enchanted for " . $cost . " gold.</p>";
    echo "<p>The enchant will be random and may not be the one you want to have. </p>";
    echo "<p>You can of course always try your luck again if you have the gold for it.</p>";
    echo "<br>";
    echo "<button id='enchantItem'>" . $buttonText . " (" . $cost . " gold)</button>";
    
    ?>
        <script>
        $('#enchantItem').on('click', function(){
            var itemStr = '<?php echo $itemStr; ?>';
            var itemName = document.getElementById(itemStr).innerHTML;
            
                if (<?php echo $double;?> == 1){
                    var conf = confirm("Are you sure you wish to enchant " + itemName + "?\n\n This will replace <?php if (isset($enchant['name'])){echo $enchant['name'];};?> with a random enchantment");
                }
                else{
                    var conf = confirm("Are you sure you wish to enchant " + itemName + "?");
                }
                if (conf == true){
                    var enchantType = '<?php echo $enchantType; ?>';
                    $('#enchantressArea').load('index.php?opage=enchantressFunctions&nonUI&enchantItem=' + itemStr + '&enchantType=' + enchantType);   
                }
            
        });        
        </script>
    <?php
}

function getEffects($row,$itemId,$itemType){
    global $conn;
    
    #echo "itemType " . $itemType . "<br>";
    
    if($itemType == 1){
        $table = "weapons";
    }
    elseif($itemType == 2){
        $table = "armours";
    }
	elseif($itemType == "mind"){
		$enchantTier = 1;
	}
	if($itemType !== "mind"){
	    $sql = "SELECT enchantTier FROM $table WHERE id='$itemId'";
	    $result = mysqli_query($conn,$sql);
	    $row2 = mysqli_fetch_assoc($result);
	    
	    $enchantTier = $row2['enchantTier']-1;
	}
    $enchantEffective = array("0.8","1","1.2","1.4","1.6");
    
    $enchant = "";
    if($row['damageBonusPercent'] > 0){
        $enchant .= "<br>+" . $row['damageBonusPercent'] * $enchantEffective[$enchantTier] . "% Damage ";
    }
    if ($row['damageBonusPoint']  > 0){
        $enchant .= "<br>+" . $row['damageBonusPoint'] * $enchantEffective[$enchantTier] . " Damage ";
    }
    if ($row['accuracyPercent']  > 0){
        $enchant .= "<br>+" . $row['accuracyPercent'] * $enchantEffective[$enchantTier] . "% Accuracy ";
    }
    if ($row['armourPenetration']  > 0){
        $enchant .= "<br>+" . round($row['armourPenetration'] * $enchantEffective[$enchantTier]) . " Armour Penetration ";
    }
    if ($row['armourBonus']  > 0){
        $enchant .= "<br>+" . $row['armourBonus'] * $enchantEffective[$enchantTier] . " Armour ";
    }
    if ($row['weightReduction']  > 0){
        $enchant .= "-" . $row['weightReduction'] * $enchantEffective[$enchantTier] . " Weight ";
    }
    if ($row['oneSkill']  > 0){
        $enchant .= "<br>+" . $row['oneSkill'] * $enchantEffective[$enchantTier] . " One-handed skill ";
    }
    if ($row['twoSkill']  > 0){
        $enchant .= "<br>+" . $row['twoSkill'] * $enchantEffective[$enchantTier] . " Two-handed skill ";
    }
    if ($row['bowSkill']  > 0){
        $enchant .= "<br>+" . $row['bowSkill'] * $enchantEffective[$enchantTier] . " Bow skill ";
    }
    if ($row['xBowSkill']  > 0){
        $enchant .= "<br>+" . $row['xBowSkill'] * $enchantEffective[$enchantTier] . " Crossbow skill ";
    }
    if ($row['finesseSkill']  > 0){
        $enchant .= "<br>+" . $row['finesseSkill'] * $enchantEffective[$enchantTier] . " Finesse skill ";
    }
    if ($row['initiativeSkill']  > 0){
        $enchant .= "<br>+" . $row['initiativeSkill'] * $enchantEffective[$enchantTier] . " Initiative skill ";
    }
    if ($row['shieldSkill']  > 0){
        $enchant .= "<br>+" . $row['shieldSkill'] * $enchantEffective[$enchantTier] . " Shield skill ";
    }
    if ($row['parrySkill']  > 0){
        $enchant .= "<br>+" . $row['parrySkill'] * $enchantEffective[$enchantTier] . " Parry skill ";
    }
    if ($row['foulSkill']  > 0){
        $enchant .= "<br>+" . $row['foulSkill'] * $enchantEffective[$enchantTier] . " Foul Play skill ";
    }
    if ($row['dodgeSkill']  > 0){
        $enchant .= "<br>+" . $row['dodgeSkill'] * $enchantEffective[$enchantTier] . " Dodge skill ";
    }
	if ($row['blockPercent']  > 0){
        $enchant .= "<br>+" . $row['blockPercent'] * $enchantEffective[$enchantTier] . " % block ";
    }
    return $enchant;
}
function getEnchantment($id,$enchantType,$itemId,$itemType){
    global $conn;
    
    $sql = "SELECT * FROM enchants WHERE id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = $stmt->get_result();
    $row = mysqli_fetch_assoc($result);
    $enchant = getEffects($row,$itemId,$itemType);
    
    return array("name"=>$row[$enchantType],"effects"=>$enchant . "<br><br>");
}

function getCost($id,$itemType,$double){
    global $conn;
    
    if($itemType == 1){
        $table = "weapons";
    }
    elseif($itemType == 2){
        $table = "armours";
    }
    
    $sql = "SELECT * FROM $table WHERE id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = $stmt->get_result();
    $row = mysqli_fetch_assoc($result);
    
    if ($table == "weapons"){
        if ($row['price'] > 0){
            $cost = $row['price'] / 10;
        }
        else{
            $cost = 0;
        }
    }
    else{
        if ($row['price'] > 0){
            $cost = $row['price'] / 2;
        }
        else{
            $cost = 0;
        }
    }
    if($cost == 0){
        $cost = 100;
    }
    else{
        if ($cost >= 100){
            $cost = 100;
        }
        elseif($cost <= 20){
            $cost = 20;
        }
    }
    if($double == 1){
        $cost = $cost * 2;
    }
    
    $cost = round($cost);
    $_SESSION['other']['enchantCost'] = $cost;
    return $cost;
    
}

function enchantItem($item,$preOrSuff){
    global $conn;

    if ($_SESSION['other']['enchantCost'] <= $_SESSION['characterProperties']['gold']){
    	$found = 0;
        $invId = $_SESSION['characterProperties']['inventory_id'];
        $equipId = $_SESSION['characterProperties']['equipment_id'];
        $slot = $_SESSION['other']['enchantSlot'];
        $invOrEquip = $_SESSION['other']['equipOrInventory'];
        $enchantType = $_SESSION['other']['enchantType'];
        
        $previousId = explode(":",$item);
        $itemId = $previousId[0];
        $itemType = "2";
        $previousEnchants = explode(";",$previousId[1]);
        if($preOrSuff === "prefix"){
            $previousId = $previousEnchants[0];
        }
        elseif($preOrSuff === "suffix"){
            $previousId = $previousEnchants[1];
        }
        
        
        if ($invOrEquip === "equipment"){
            
            //FIND ITEM IN EQUIPMENT
            $sql = "SELECT * FROM equipment WHERE eid='$equipId'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            if($slot === "weapons"){
                $itemType = "1";
                if ($row['right_hand'] == $item){
                    $slot = "right_hand";
                }
                elseif($row['left_hand'] == $item){
                    $slot = "left_hand";
                }
                elseif($row['secondary'] == $item){
                    $slot = "secondary";
                }
                else{
                    echo "ITEM NOT FOUND, EXITING";
					deletePendingEnchant();
                    exit;
                }
            }
            else{
                $slot = substr($slot, 0, -1);
                
                if($item === $row[$slot]){
                    
                }
                else{
                    echo "ITEM NOT FOUND, EXITING";
					deletePendingEnchant();
                    exit;
                }
            }
            //GET RANDOM ENCHANT
            $sql = "SELECT * FROM enchants WHERE (type='all' OR type='$enchantType') AND id != '$previousId' ORDER BY RAND() LIMIT 3";
            $result = mysqli_query($conn,$sql);
            $enchants = array();
            $newEnchantNames = array();
			$i = 1;
            while($row = mysqli_fetch_assoc($result)){       
				$enchants[] = array("id"=>$row['id'],"name"=>$row[$preOrSuff],"choice"=>$i,"effects"=>getEffects($row,$itemId,$itemType));
				$i++;
			}
        }
        elseif($invOrEquip == "inventory"){
        	$slot = substr($slot, 0, -1);
            $sql = "SELECT * FROM inventory WHERE iid='$invId'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($result);
            
            //FIND ITEM IN INVENTORY SLOT
            $invSlot = explode(",",$row[$slot . "s"]);
            $i = 0;
            foreach ($invSlot as $invItem){
                if ($invItem == $item){
                    $found = 1;
                    #unset($invSlot[$i]);
                    break;
                }
                $i++;
            }
            
            if ($found === 0){
                $invSlot = explode(",",$row["secondarys"]);
                $i = 0;
                foreach ($invSlot as $invItem){
                    if ($invItem == $item){
                        $found = 1;
                        #unset($invSlot[$i]);
                        break;
                    }
                    $i++;
                }
                if($found === 0){
                    echo "ITEM NOT FOUND, EXITING";
                    echo $slot;
                    deletePendingEnchant();
                    exit;
                }
            }
			
			$enchantType = $_SESSION['other']['enchantType'];
			if($enchantType != "armour"){
				$itemType = 1;
			}
            //GET RANDOM ENCHANT
            $sql = "SELECT * FROM enchants WHERE (type='all' OR type='$enchantType') AND id != '$previousId' ORDER BY RAND() LIMIT 3";
            $result = mysqli_query($conn,$sql);
            $enchants = array();
            $newEnchantNames = array();
			$i = 1;
            while($row = mysqli_fetch_assoc($result)){
				$enchants[] = array("id"=>$row['id'],"name"=>$row[$preOrSuff],"choice"=>$i,"effects"=>getEffects($row,$itemId,$itemType));
				$i++;
			}
        }
        else{
            echo "Something has gone wrong";
            exit;
        }
        //UPDATE GOLD
        $price = $_SESSION['other']['enchantCost'];
        $id = $_SESSION['characterProperties']['id'];
        $sql = "UPDATE characters SET gold=gold-'$price' WHERE id='$id'";
        mysqli_query($conn,$sql);
		
		$enchantIdArray = array();
		
		echo "<h2 id='choose' style='text-align:center;display:none;'>Choose your enchant</h2>";
		foreach($enchants as $enchant){
			echo "<div id='" . $enchant['choice'] . "' class='enchantBoxes'>";
				echo "<h4 style='text-align:center'>" . $enchant['name'] . "</h4>";
				echo $enchant['effects'];
			echo "</div>";
			
			$enchantIdArray[] = $enchant['id'];
		}
		
		$charId = $_SESSION['characterProperties']['id'];
		$enchantString = implode(",", $enchantIdArray);
		$sql = "INSERT INTO pendingenchants (character_id,oldItem,slot,preOrSuff,enchants) VALUES('$charId','$item','$slot','$preOrSuff','$enchantString')";
		mysqli_query($conn,$sql);
		?>

		<script>
			$('#choose').fadeIn("1.2",function(){
				$('#1').fadeIn("0,6",function(){
					$('#2').fadeIn("0,6",function(){
						$('#3').fadeIn("0,6",function(){
						
						});
					});
				});
			});
			$('.enchantBoxes').click(function(){
				var choice = $(this).attr('id');
				$('#enchantressArea').load('index.php?opage=enchantressFunctions&nonUI&chooseEnchant=' + choice);
			});
		</script>
		<?php
		
		
	    }
	    else{
	        echo "<br><br><h4 style='text-align:center;'>You don't have enough gold to enchant this item. <br>It costs " . $_SESSION['other']['enchantCost'] . ", and you only have " . $_SESSION['characterProperties']['gold'] . " gold<h4>";
	        exit;
	    }
    }
	
	function writeEnchant($choice){
		global $conn;
		
		
		
		$char_id = $_SESSION['characterProperties']['id'];
		$inv_id = $_SESSION['characterProperties']['inventory_id'];
		$equip_id = $_SESSION['characterProperties']['equipment_id'];
		
		$sql = "SELECT * FROM inventory WHERE iid='$inv_id'";
		$result = mysqli_query($conn,$sql);
		$inventoryRow = mysqli_fetch_assoc($result);
		
		$sql = "SELECT * FROM equipment WHERE eid='$equip_id'";
		$result = mysqli_query($conn,$sql);
		$equipmentRow = mysqli_fetch_assoc($result);
		
		$sql = "SELECT * FROM pendingenchants WHERE character_id='$char_id'";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			
			if($row['slot'] !== "mind"){
				//IF ITS NOT A MIND ENCHANT
				$oldItem = $row['oldItem'];
				$newEnchant = explode(",", $row['enchants']);
				$newEnchant = $newEnchant[$choice-1];
				$preOrSuff = $row['preOrSuff'];
				$itemType2 = 1;
				if($row['slot'] === "right_hand" || $row['slot'] === "left_hand"){
					$equipSlot = $row['slot'];
					$invSlot = "weapons";
				}
				elseif($row['slot'] === "secondary"){
					$equipSlot = $row['slot'];
					$invSlot = $row['slot'] . "s";
				}
				elseif($row['slot'] === "weapon"){
					$equipSlot = "right_hand";
					$equipSlotAlt = "left_hand";
					$invSlot = $row['slot'] . "s";
				}
				else{
					$equipSlot = $row['slot'];
					$invSlot = $row['slot'] . "s";
					$itemType2 = 2;
				}
		
				$found = 0;		
				if(isset($equipSlotAlt)){
					#VALIDATION
					//FIND ITEM IN INVENTORY SLOT
				    $invSlotArray = explode(",",$inventoryRow[$invSlot]);
				    $i = 0;
				    foreach ($invSlotArray as $invItem){
				        if ($invItem == $oldItem){
				            $found = $invSlot;
				            unset($invSlotArray[$i]);
				            $itemType = "inventory";
				            break;
				        }
				        $i++;
				    }
				    if ($found === 0){
				    	if($oldItem == $equipmentRow[$equipSlot]){
				    		$found = $equipSlot;
							$itemType = "equipment";
				    	}
						elseif($oldItem == $equipmentRow[$equipSlotAlt]){
							$found = $equipSlotAlt;
							$itemType = "equipment";
                        }
                        else{
                            $invSlotArray = explode(",",$inventoryRow["secondarys"]);
                            $i = 0;
                            foreach ($invSlotArray as $invItem){
                                if ($invItem == $oldItem){
                                    $found = "secondarys";
                                    unset($invSlotArray[$i]);
                                    $itemType = "inventory";
                                    break;
                                }
                                $i++;
                            }
                        }
				    }
					if($found === 0){
						echo "ITEM NOT FOUND";
						deletePendingEnchant();
						exit;
					}
				}
				else{
					#echo $oldItem . " " . $_SESSION['characterProperties'][$equipSlot];
					if($oldItem == $equipmentRow[$equipSlot]){
			    		$found = $equipSlot;
						$itemType = "equipment";
			    	}
					else{
						//FIND ITEM IN INVENTORY SLOT
					    $invSlotArray = explode(",",$inventoryRow[$invSlot]);
					    $i = 0;
					    foreach ($invSlotArray as $invItem){
					        if ($invItem == $oldItem){
					            $found = $invSlot;
					            unset($invSlotArray[$i]);
					            $itemType = "inventory";
					            break;
					        }
					        $i++;
					    }
					}
					if($found === 0){
						echo "ITEM NOT FOUND";
						deletePendingEnchant();
						exit;
					}
				}
				
				#old item
				$oldItemSplit = explode(":",$oldItem);
				$oldItemId = $oldItemSplit[0];
				$previousEnchants = explode(";",$oldItemSplit[1]);
		
				
				#IF EQUIPMENT
				if($itemType == "equipment"){
					
					if($preOrSuff == "prefix"){
			                $previousEnchants[0] = $newEnchant;
			        }
			        else{
			            $previousEnchants[1] = $newEnchant;
			        }
			        $newItem = $oldItemId . ":" . $previousEnchants[0] . ";" . $previousEnchants[1];
			        
					#echo $newItem;
			        
			        //ADD ITEM TO EQUIPMENT
			        $sql = "UPDATE equipment SET $found='$newItem' WHERE eid='$equip_id'";
			        mysqli_query($conn,$sql);
			        
			        //UPDATE INVENTORY
			        $_SESSION['charId'] = $_SESSION['characterProperties']['id'];
			        require_once(__ROOT__."/backend/character/update-characterSessions.php");
					
				}
				elseif($itemType == "inventory"){
					#IF INVENTORY
					if($preOrSuff == "prefix"){
			                $previousEnchants[0] = $newEnchant;
			        }
			        else{
			            $previousEnchants[1] = $newEnchant;
			        }
			        $newItem = $oldItemId . ":" . $previousEnchants[0] . ";" . $previousEnchants[1];
			        
			        if (count($invSlotArray) == 0){
			            $invSlotArray = $newItem;
			        }
			        else{
			            array_push($invSlotArray,$newItem);
			            $invSlotArray = implode(",", $invSlotArray);
			        }
			        $invSlotArray = $invSlotArray . ",";
			        //ADD ITEM TO INVENTORY
			        $invId = $_SESSION['characterProperties']['inventory_id'];
			        $sql = "UPDATE inventory SET $found='$invSlotArray' WHERE iid='$invId'";
			        mysqli_query($conn,$sql);
		        }
	        }
			else{
				//IT'S A MIND ENCHANT
				$exp = explode(",",$row['enchants']);
				$newEnchant = $exp[$choice-1];
				$preOrSuff = "prefix";
				$oldItemId = 0;
				$itemType2 = "mind";
				$sql = "UPDATE characters SET mindEnchant='$newEnchant' WHERE id='$char_id'";
				mysqli_query($conn,$sql);
			}
		
	    $sql = "SELECT * FROM enchants WHERE id='$newEnchant'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$newEnchantArray = array("id"=>$row['id'],"name"=>$row[$preOrSuff],"effects"=>getEffects($row,$oldItemId,$itemType2));
	    
	    ?>
	    <div id="weirdContainer"> 
	        <div id="myProgress">
	          <div id="myBar"></div>
	        </div>
	        <div id="enchantText" style='width:100%;text-align:center;font-size:20px;'> Enchanting...</div>
	    </div>
	    <div id="enchantsDesc" style="width:100%;text-align:center;"></div>
	    <script>
	    function progress() {
	        var elem = document.getElementById("myBar"); 
	        var width = 1;
	        var id = setInterval(frame, 15);
	        function frame() {
	            if (width >= 100) {
	                clearInterval(id);
	                $('#enchantressArea').fadeOut("slow", function(){
	                    $('#myProgress').remove();
	                    $('#myBar').html("");
	                    $('#enchantText').html("<h2>Enchant Successful!</h2>");
	                    $('#enchantText').css("font-size","6px");
	                    $('#enchantressArea').fadeIn("fast");
	                    $('#enchantText').animate({fontSize: '3em'}, "1000", function(){
	                        $('#enchantsDesc').hide().html("<?php echo "<br><br><h3>" . $newEnchantArray['name'] . "</h3>" . $newEnchantArray['effects']; ?>").fadeIn("fast");
	                        $('#enchantressItems').load('index.php?opage=enchantressFunctions&nonUI&getInventory=1');
	                        updateChar();
	                    });
	                       
	                });
	            }   else {
	                width++; 
	                elem.style.width = width + '%'; 
	            }
	        }
	    }
	    progress();
	    
	    
	    </script>
	    
	    
	    <?php
	    deletePendingEnchant();
    }
	else{
		echo "ERROR: 20617-TOO-232-COMP-23-LICATED-15873-FOR-1874-RIKKA";
	}
}

function enchantNotInProgress(){
	global $conn;
	
	$charId = $_SESSION['characterProperties']['id'];
	
	$sql = "SELECT * FROM pendingenchants WHERE character_id='$charId'";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		return false;
	}
	else{
		return true;
	}
}

function deletePendingEnchant(){
	global $conn;
	
	$charId = $_SESSION['characterProperties']['id'];
	
	$sql = "DELETE FROM pendingenchants WHERE character_id='$charId'";
	$result = mysqli_query($conn,$sql);
	
}

function getPendingEnchant(){
	global $conn;
	
	$charId = $_SESSION['characterProperties']['id'];
	
	$sql = "SELECT * FROM pendingenchants WHERE character_id='$charId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$enchantIds = $row['enchants'];
	$itemId = explode(":",$row['oldItem'][0]);
	if ($row['slot'] == "right_hand" || $row['slot'] == "left_hand" || $row['slot'] == "secondary" || $row['slot'] == "weapons"){
		$itemType = 1;
	}
	elseif($row['slot'] == "mind"){
		$itemType = "mind";	
	}
	else{
		$itemType = 2;
	}
	$preOrSuff = $row['preOrSuff'];
	
	$sql = "SELECT * FROM enchants WHERE id IN($enchantIds) ORDER BY FIELD(id,$enchantIds)";
    $result = mysqli_query($conn,$sql);
    $enchants = array();
    $newEnchantNames = array();
	$i = 1;
    while($row = mysqli_fetch_assoc($result)){
		$enchants[] = array("id"=>$row['id'],"name"=>$row[$preOrSuff],"choice"=>$i,"effects"=>getEffects($row,$itemId,$itemType));
		$i++;
	}
	echo "<div id='enchantressArea'>";
	echo "<h2 id='choose' style='text-align:center;display:none;'>Choose your enchant</h2>";
	foreach($enchants as $enchant){
		echo "<div id='" . $enchant['choice'] . "' class='enchantBoxes'>";
			echo "<h4 style='text-align:center'>" . $enchant['name'] . "</h4>";
			echo $enchant['effects'];
		echo "</div>";
		
		$enchantIdArray[] = $enchant['id'];
	}
	echo "</div>";
	
	?>

	<script>
		$('#choose').fadeIn("1.2",function(){
			$('#1').fadeIn("0,6",function(){
				$('#2').fadeIn("0,6",function(){
					$('#3').fadeIn("0,6",function(){
					
					});
				});
			});
		});
		$('.enchantBoxes').click(function(){
			var choice = $(this).attr('id');
			$('#enchantressArea').load('index.php?opage=enchantressFunctions&nonUI&chooseEnchant=' + choice);
		});
	</script>
	<?php
	
}

function getMindEnchant(){
	global $conn;
	
	$charId = $_SESSION['characterProperties']['id'];
	
	$sql = "SELECT mindEnchant FROM characters WHERE id='$charId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if($row['mindEnchant'] == 0){
		$double = 0;
		echo "<h2 style='text-align:center;'>You do not yet have a Mind Enchant</h2><h4 style='text-align:center;'>You can only have one and it costs 10 000 gold (mind enchant unlocks on level 10)</h4><br>
		<div style='text-align:center;'>The mind enchant is stronger than a normal enchant and unlike armour enchants it can actually boost your weapon damage<br><br>";
		echo "<button id='enchantItem'>Enchant (10 000 gold)</button></div>";
		
	}
	else{
		$sql = "SELECT * FROM enchants WHERE id='$row[mindEnchant]'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$double = 1;
		echo "<div style='text-align:center;'>";
		echo "<h2>" . $row['prefix'] . "</h2>";
		$effects = getEffects($row, 0, "mind");
		echo $effects;
		echo "<br><br><button id='enchantItem'>Replace Enchant (10 000 gold)</button>";
		echo "</div>";
	}
	
	?>
	<script>
	$('#enchantItem').on('click', function(){
                if (<?php echo $double;?> == 1){
                    var conf = confirm("Are you sure you wish to replace your mind enchant for 10 000 gold?");
                }
                else{
                    var conf = confirm("Are you sure you wish to enchant your mind for 10 000 gold?");
                }
                if (conf == true){
                    $('#enchantressArea').load('index.php?opage=enchantressFunctions&nonUI&enchantItem=mind');   
                }
        });  
	</script>
	<?php
	
	
}

function enchantMind(){
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	
	$sql = "SELECT mindEnchant FROM characters WHERE id='$charId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if($row['mindEnchant'] == 0){
		$sql = "SELECT * FROM enchants WHERE type='mind' ORDER BY RAND() LIMIT 3";
	}
	else{
		$sql = "SELECT * FROM enchants WHERE type='mind' AND id != '$row[mindEnchant]' ORDER BY RAND() LIMIT 3";
	}
	
	
	//GET RANDOM ENCHANT
            
    $result = mysqli_query($conn,$sql);
    $enchants = array();
    $newEnchantNames = array();
	$i = 1;
    while($row = mysqli_fetch_assoc($result)){       
		$enchants[] = array("id"=>$row['id'],"name"=>$row['prefix'],"choice"=>$i,"effects"=>getEffects($row,0,"mind"));
		$i++;
	}
	
	$sql = "SELECT gold FROM characters WHERE id='$charId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if ($row['gold'] >= 10000){
	
		//UPDATE GOLD
	    $id = $_SESSION['characterProperties']['id'];
	    $sql = "UPDATE characters SET gold=gold-10000 WHERE id='$id'";
	    mysqli_query($conn,$sql);
		
		$enchantIdArray = array();
		
		echo "<h2 id='choose' style='text-align:center;display:none;'>Choose your enchant</h2>";
		foreach($enchants as $enchant){
			echo "<div id='" . $enchant['choice'] . "' class='enchantBoxes'>";
				echo "<h4 style='text-align:center'>" . $enchant['name'] . "</h4>";
				echo $enchant['effects'];
			echo "</div>";
			
			$enchantIdArray[] = $enchant['id'];
		}
		
		$charId = $_SESSION['characterProperties']['id'];
		$enchantString = implode(",", $enchantIdArray);
		$sql = "INSERT INTO pendingenchants (character_id,oldItem,slot,preOrSuff,enchants) VALUES('$charId','0','mind','prefix','$enchantString')";
		mysqli_query($conn,$sql);
		
		?>
	
			<script>
				$('#choose').fadeIn("1.2",function(){
					$('#1').fadeIn("0,6",function(){
						$('#2').fadeIn("0,6",function(){
							$('#3').fadeIn("0,6",function(){
							
							});
						});
					});
				});
				$('.enchantBoxes').click(function(){
					var choice = $(this).attr('id');
					$('#enchantressArea').load('index.php?opage=enchantressFunctions&nonUI&chooseEnchant=' + choice);
				});
			</script>
		<?php
		}
		else{
			echo "<h2 style='text-align:center'>You cannot afford this enchant</h2>";
		}
}


if(isset($_GET['itemName'])){
    viewItem($_GET['itemName'], $_GET['itemType'],$_GET['itemLoc']);
}
elseif(isset($_GET['selItemName'])){
    viewEnchant($_GET['selItemName'],$_GET['type'],$_GET['itemType']);    
}
elseif (isset($_GET['enchantItem'])){
	if($_GET['enchantItem'] == "mind"){
		enchantMind();
	}
	else{
		enchantItem($_GET['enchantItem'], $_GET['enchantType']);
	}
}
elseif (isset($_GET['getInventory'])){
    getInventory();
}
elseif (isset($_GET['chooseEnchant'])){
	writeEnchant($_GET['chooseEnchant']);
}
elseif(isset($_GET['getMindEnchant'])){
	getMindEnchant();
}
