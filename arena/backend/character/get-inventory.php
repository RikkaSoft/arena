<?php 
	global $conn;
			$_SESSION['charId'] = $_SESSION['characterProperties']['id'];
			require_once(__ROOT__."/backend/character/update-characterSessions.php");
			
			$equipment_id =		$_SESSION['characterProperties']['equipment_id'];
			$inventory_id =		$_SESSION['characterProperties']['inventory_id'];
			
			$right_hand =			$_SESSION['characterProperties']['right_hand'];
			$left_hand =			$_SESSION['characterProperties']['left_hand'];
            $secondary =            $_SESSION['characterProperties']['secondary'];
			$head =					$_SESSION['characterProperties']['head'];
			$chest =				$_SESSION['characterProperties']['chest'];
			$arm =					$_SESSION['characterProperties']['arm'];
			$leg = 					$_SESSION['characterProperties']['leg'];
			$feet = 				$_SESSION['characterProperties']['feet'];
			$trinket = 				$_SESSION['characterProperties']['trinket'];

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
			$trinket_inv =			$equipment['trinkets'];
			?>
			<strong>Equipment</strong>
			<table id="equipTable" border="0" style="width:100%; margin-top:0px;">
				<tbody>

							 			
							 		
			<?php 
			
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
			
			
			
			echo "<tr><td><img src='frontend/design/images/character/icons/name.png' class='skillIcon'>";
			$weapons = explode(",", $weapons_inv);            
			echo "<select id=\"rightHandChange\" class=\"equipmentList\" name=\"right_hand_equip\">";
			echo "<option>" . $right_hand .  "</option>";
			foreach ($weapons as $wep){
				$exploded = explode(":", $wep);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,type,enchantType FROM weapons WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
				        if ($row['type'] != "shield"){
				            $returnedItem = getItemOption($wep,$row['name'],$row['enchantType']);
					        echo "<option value='$wep'>" . $returnedItem . "</option>";
						}
				}
			}
			if ($right_hand !== "Nothing"){
				echo "<option value='1:1;1'>Nothing</option>";
			}
			echo "</select>Right Hand</td></tr>";
			echo "<tr><td><img src='frontend/design/images/character/icons/name.png' class='skillIcon'>";
			echo "<select id=\"leftHandChange\" class=\"equipmentList\" name=\"left_hand_equip\">";
			echo "<option>" . $left_hand . "</option>";
			foreach ($weapons as $wep){
			    $exploded = explode(":", $wep);
				if ($exploded[0] != 0){
                        $sql = "SELECT name,enchantType FROM weapons WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($wep,$row['name'],$row['enchantType']);
                        echo "<option value='$wep'>" . $returnedItem . "</option>";
                }
			}
			if ($left_hand !== "Nothing"){
				echo "<option value='1:1;1'>Nothing</option>";
			}
			echo "</select>Left Hand</td></tr>";
			
			echo "<tr><td><img src='frontend/design/images/character/icons/bow.png' class='skillIcon'>";
            $secondarys = explode(",", $secondary_inv);
            echo "<select id=\"secondaryChange\"class=\"equipmentList\" bow=\"secondary_equip\">";
            echo "<option>" . $secondary . "</option>";
            foreach ($secondarys as $sec){
                $exploded = explode(":", $sec);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,enchantType FROM weapons WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($sec,$row['name'],$row['enchantType']);
                        echo "<option value='$sec'>" . $returnedItem . "</option>";
                }
            }
            if ($secondary !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
            }
            echo "</select>Secondary</td></tr>";
			
            echo "<tr><td><img src='frontend/design/images/character/icons/head.png' class='skillIcon'>";
			$heads = explode(",", $head_inv);
			echo "<select id=\"headChange\"class=\"equipmentList\" name=\"head_equip\">";
			echo "<option>" . $head . "</option>";
			foreach ($heads as $hea){
			    $exploded = explode(":", $hea);
                if ($exploded[0] != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$exploded[0]'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($hea,$row['name'],$row['enchantType']);
                        echo "<option value='$hea'>" . $returnedItem . "</option>";
                }
			}
            if ($head !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
            }
			$armour = GetArmourOfPart($_SESSION['characterProperties']['headString']);
			echo "</select><span style='min-width:50px;display:inline-block;'>Head</span><span style='font-weight: bold;'><img src='frontend/design/images/character/icons/armour.png'> "  . $armour .  "</span></td></tr>";
			
			echo "<tr><td><img src='frontend/design/images/character/icons/heavy.png' class='skillIcon'>";
			$chests = explode(",", $chest_inv);
			echo "<select id=\"chestChange\" class=\"equipmentList\" name=\"chest_equip\">";
			echo "<option>" . $chest . "</option>";
			foreach ($chests as $che){
				if ($che != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$che'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($che,$row['name'],$row['enchantType']);
                        echo "<option value='$che'>" . $returnedItem . "</option>";
                }
			}
            if ($chest !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
            }
			$armour = GetArmourOfPart($_SESSION['characterProperties']['chestString']);
			echo "</select><span style='min-width:50px;display:inline-block;'>Chest</span><span style='font-weight: bold;'><img src='frontend/design/images/character/icons/armour.png'> "  . $armour .  "</span></td></tr>";
			
			echo "<tr><td><img src='frontend/design/images/character/icons/arms.png' class='skillIcon'>";
			$arms = explode(",", $arm_inv);
			echo "<select id=\"armsChange\"class=\" equipmentList\" name=\"arms_equip\">";
			echo "<option>" . $arm . "</option>";
			foreach ($arms as $ar){
				if ($ar != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$ar'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($ar,$row['name'],$row['enchantType']);
                        echo "<option value='$ar'>" . $returnedItem . "</option>";
                }
			}
            if ($arm !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
            }
			$armour = GetArmourOfPart($_SESSION['characterProperties']['armString']);
			echo "</select><span style='min-width:50px;display:inline-block;'>Arms</span><span style='font-weight: bold;'><img src='frontend/design/images/character/icons/armour.png'> "  . $armour .  "</span></td></tr>";
			
			
			echo "<tr><td><img src='frontend/design/images/character/icons/legs.png' class='skillIcon'>";
			$legs = explode(",", $leg_inv);
			echo "<select id=\"legChange\" class=\"equipmentList\" name=\"legs_equip\">";
			echo "<option>" . $leg . "</option>";
			foreach ($legs as $le){
				if ($le != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$le'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($le,$row['name'],$row['enchantType']);
                        echo "<option value='$le'>" . $returnedItem . "</option>";
                }
			}
            if ($leg !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
			}
			$armour = GetArmourOfPart($_SESSION['characterProperties']['legString']);
			echo "</select><span style='min-width:50px;display:inline-block;'>Legs</span><span style='font-weight: bold;'><img src='frontend/design/images/character/icons/armour.png'> "  . $armour .  "</span></td></tr>";
			
			echo "<tr><td><img src='frontend/design/images/character/icons/feet.png' class='skillIcon'>";
			$feets = explode(",", $feet_inv);
			echo "<select id=\"feetChange\" class=\"equipmentList\" name=\"feet_equip\">";
			echo "<option>" . $feet . "</option>";
			foreach ($feets as $fee){
				if ($fee != 0){
                        $sql = "SELECT name,enchantType FROM armours WHERE id='$fee'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        $returnedItem = getItemOption($fee,$row['name'],$row['enchantType']);
                        echo "<option value='$fee'>" . $returnedItem . "</option>";
                }
			}
            if ($feet !== "Nothing"){
                echo "<option value='1:1;1'>Nothing</option>";
            }
			echo "</select>Feet</td></tr>";
			
			echo "<tr><td><img src='frontend/design/images/character/icons/trinket.png' class='skillIcon'>";
			$trinkets = explode(",", $trinket_inv);
			echo "<select id=\"trinketChange\" class=\"equipmentList\" name=\"trinket_equip\">";
			echo "<option>" . $trinket . "</option>";
			foreach ($trinkets as $trink){
				
				if ($trink != 0){
                        $sql = "SELECT name FROM trinkets WHERE id='$trink'";
                        $result = mysqli_query($conn,$sql);
                        $row = mysqli_fetch_assoc($result);
                        #$returnedItem = getItemOption($trink,$row['name'],$row['enchantType']);
                        echo "<option value='$trink'>" . $row['name'] . "</option>";
                }
			}
            if ($trinket !== "Nothing"){
                echo "<option value='1'>Nothing</option>";
            }
			echo "</select>Trinket</td></tr>";
?>

				</tbody>
			</table>
<script>
	
	$('#rightHandChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'rightHandChange';
	  var realName = $("#rightHandChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "right_hand",
	  	inventoryType: "weapons"
	  }, function(){
	  	reloadInventoryWep(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#leftHandChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'leftHandChange';
	  var realName = $("#leftHandChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "left_hand",
	  	inventoryType: "weapons"
	  }, function(){
	  	reloadInventoryWep(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#secondaryChange').on('change', function() {
      var name = ( this.value );
      var itemType = 'secondaryChange';
      var realName = $("#secondaryChange option:selected").text();
      $.post('index.php?cpage=set-inventory',
      {
        name: name,
        equipType: "secondary",
        inventoryType: "secondarys"
      }, function(){
        reloadInventoryWep(itemType, realName);
        updateChar();
      });    
    });
		
	$('#headChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'headChange';
	  var realName = $("#headChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "head",
	  	inventoryType: "heads"
	  }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#chestChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'chestChange';
	  var realName = $("#chestChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "chest",
	  	inventoryType: "chests"
	   }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#armsChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'armsChange';
	  var realName = $("#armsChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "arm",
	  	inventoryType: "arms"
	   }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#legChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'legChange';
	  var realName = $("#legChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "leg",
	  	inventoryType: "legs"
	   }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	
	$('#feetChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'feetChange';
	  var realName = $("#feetChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "feet",
	  	inventoryType: "feets"
	   }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	$('#trinketChange').on('change', function() {
	  var name = ( this.value );
	  var itemType = 'trinketChange';
	  var realName = $("#trinketChange option:selected").text();
	  $.post('index.php?cpage=set-inventory',
	  {
	  	name: name,
	  	equipType: "trinket",
	  	inventoryType: "trinkets"
	   }, function(){
	  	reloadInventory(itemType, realName);
	  	updateChar();
	  });	 
	});
	

</script>

<?php
	function GetArmourOfPart($part){
		global $conn;
		$totalArmour = 0;
		$explode = explode(":", $part);
		$partId = $explode[0];
		$ex = explode(";",$explode[1]);
		$firstEnchant = $ex[0];
		$secondEnchant = $ex[1];

		$sql = "SELECT damage_reduction FROM armours WHERE id='$partId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$totalArmour += $row['damage_reduction'];

		if($firstEnchant != 1){
			$sql = "SELECT armourBonus FROM enchants WHERE id='$firstEnchant'";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			$totalArmour += $row['armourBonus'];
		}
		if($secondEnchant != 1){
			$sql = "SELECT armourBonus FROM enchants WHERE id='$secondEnchant'";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
			$totalArmour += $row['armourBonus'];
		}
		return $totalArmour;
	}

?>