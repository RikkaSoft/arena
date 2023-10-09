<?php
	global $conn;
	require_once(__ROOT__."/backend/character/inventoryFunctions.php");

	$equipment_id =		$_SESSION['characterProperties']['equipment_id'];
	$inventory_id =		$_SESSION['characterProperties']['inventory_id'];
	$light_armour = 	$_SESSION['characterProperties']['light_armour'];
	$heavy_armour = 	$_SESSION['characterProperties']['heavy_armour'];
	$strength = 		$_SESSION['characterProperties']['strength'];
	$id =				$_SESSION['characterProperties']['id'];

	$extraStr = GetStrengthFromArmour($equipment_id);
	$strength += $extraStr;
	
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
	
	
    $item = explode(":", $_POST['name']);
    $item = $item[0];
    $itemString = $_POST['name'];
	$equipType = $_POST['equipType'];
	$inventoryType = 	$_POST['inventoryType'];
	#$item = "1";
    #$equipType = "left_hand";
    #$inventoryType = "weapons";
    #$itemString = "1:1;1";
	
	
	$weightType = "armours";
    $switch_hands = "false";
	
	//CHECK IF CURRENT WEAPON IS A 2H - not sure if this works...
	if($equipType != "trinket"){
		if ($equipType === "left_hand"){
			$right_item_pre = explode(":", $_SESSION['characterProperties']['right_handString']);
	        $right_item_preString = $right_item_pre;
	        $right_item_pre = $right_item_pre[0];
			
				$sql = "SELECT type FROM weapons WHERE id=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $right_item_pre);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
				
				if ($row['type'] === "2h"){
					        #$right_item_pre_id = $row['id'];
							#$sql = "UPDATE inventory SET weapons=CONCAT(weapons,'$right_item_preString',',') WHERE id = ?";
							#$stmt = mysqli_prepare($conn,$sql);
							#mysqli_stmt_bind_param($stmt, "i", $inventory_id);
							#mysqli_stmt_execute($stmt);
							
							#$sql = "UPDATE equipment SET right_hand='1:1;1' WHERE id = ?";
							#$stmt = mysqli_prepare($conn,$sql);
							#mysqli_stmt_bind_param($stmt, "i", $equipment_id);
							#mysqli_stmt_execute($stmt);
							exit;
				}
			
			
			
		}
	
		//CHECK IF USER WANTS TO EQUIP A 2H
		if (($equipType === "right_hand" || $equipType === "left_hand" || $equipType === "secondary")){
			$sql = "SELECT type,strReq FROM weapons WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $item);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			$row = mysqli_fetch_assoc($result);
			if (isset($row['strReq'])){
				if ($strength < $row['strReq']){
					exit;
				}
			}
			
			if ($row['type'] === "2h"){
	
				
				if ($equipType === "right_hand"){
					if ($_SESSION['characterProperties']['left_hand'] != "Nothing"){
						exit;
					}
				}
				else {
					if ($_SESSION['characterProperties']['right_hand'] != "Nothing"){
						exit;
					}
				}
				$switch_hands = "true";
			}
	        $weightType = "weapons";
			
		}
	    else{
			
		$head = explode(":",$_SESSION['characterProperties']['headString'])[0];
		$chest = explode(":",$_SESSION['characterProperties']['chestString'])[0];
		$arm = explode(":",$_SESSION['characterProperties']['armString'])[0];
		$leg = explode(":",$_SESSION['characterProperties']['legString'])[0];
		$armourIds = "'" . $head . "'" . ",'" . $chest . "'" . ",'" . $arm . "'" . ",'" . $leg . "'" ;

		//Armour check
		#if ($equipType !== "left_hand" || $equipType !== "right_hand" || $equipType !== "secondary"){
			$sql = "SELECT type,skill FROM armours where id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $item);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			$row = mysqli_fetch_assoc($result);
			if ($row['type'] === "Light Armour"){
				$sql = "SELECT SUM(lightArmour) FROM armours WHERE id IN ($armourIds)";
				$res = mysqli_query($conn,$sql);
				$row2 = mysqli_fetch_assoc($res);
				$light_armour += $row2["SUM(lightArmour)"];
				if ($light_armour < $row['skill']){
					exit;
				}
				
			}
			elseif ($row['type'] === "Heavy Armour"){
				$sql = "SELECT SUM(heavyArmour) FROM armours WHERE id IN ($armourIds)";
				$res = mysqli_query($conn,$sql);
				$row2 = mysqli_fetch_assoc($res);
				$heavy_armour += $row2["SUM(heavyArmour)"];
				if ($heavy_armour < $row['skill']){
					exit;
				}
			}
		}
	}
	if ($inventoryType == "secondarys" || $inventoryType == "weapons"){
	    $table = "weapons";
    }
	elseif($inventoryType == "trinkets"){
		$table = "trinkets";
	}
    else{
            switch($inventoryType){
                case "heads":
                    break;
                case "chests":
                    break;
                case "arms":
                    break;
                case "legs":
                    break;
                case "feets":
                    break;
                default:
                    exit;
            }
    }
	$item_pre =	$_SESSION['characterProperties'][$equipType . "String"];
    #$item_pre = $item_pre[0];
    
    
    #$sql = "SELECT id FROM $table WHERE name='$item_pre'";
    #$result = mysqli_query($conn,$sql);
    #$row = mysqli_fetch_assoc($result);
    #$item_pre = $row['id'];

	$sql = "SELECT $inventoryType FROM inventory WHERE iid = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $inventory_id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	$invTypeToExplode = $row[$inventoryType];
	
	$explodedInv = explode(',',$invTypeToExplode);
	unset($explodedInv[(count($explodedInv)-1)]);
	$itemCount = 0;
	$found = "false";
    #echo $item;
    if ($itemString != "1:1;1" && $itemString != "1"){
    	foreach ($explodedInv as $items){
    	    echo $items . " - " . $itemString . "<br>";
    		if($items == $itemString){
      			$found = "true";
    			unset($explodedInv[$itemCount]);
    			break;
    		}
    		$itemCount++;
    	}
    }
    else{
        $found = "true";
    }
	#var_dump($explodedInv);
	if ($found == "true"){
        
		$implodedInv = implode(',', $explodedInv);
        if($item_pre != "1:1;1" && $item_pre != "1"){
    		if($implodedInv !== ""){
    			$updateInv = $implodedInv . ",";
    			$updateInv .= $item_pre . ",";
    		}
    		else{
    			$updateInv = $item_pre . ",";
    		}
        }
        else{
            if ($item_pre != "1:1;1" && $item_pre != "1"){
                if($implodedInv !== ""){
                $updateInv = $implodedInv . ",";
                $updateInv .= $item_pre . ",";
                }
                else{
                    $updateInv = $item_pre . ",";
                }
            }
            else{
                $updateInv = $implodedInv . ",";
            }
        }
		$sql = "UPDATE inventory SET $inventoryType=? WHERE iid = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "si", $updateInv,$inventory_id);
		mysqli_stmt_execute($stmt);
		
		if ($switch_hands === "true"){
			$sql = "UPDATE equipment SET right_hand=?, left_hand='1:1;1' WHERE eid = ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "si", $itemString,$equipment_id);
			mysqli_stmt_execute($stmt);

		}
		else{
			$sql = "UPDATE equipment SET $equipType=? WHERE eid = ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "si", $itemString,$equipment_id);
			mysqli_stmt_execute($stmt);
		}
		if($equipType != "trinket"){
			//COUNT WEIGHT	
	        $item_pre = explode(":",$item_pre);
	        $item_pre = $item_pre[0];
			$weight = $_SESSION['characterProperties']['weight'];
				$sql = "SELECT weight FROM $weightType where id=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $item_pre);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
				$weight = $weight - $row['weight'];	
	            var_dump($row);
	        
				$sql = "SELECT weight FROM $weightType where id=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $item);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
				$weight = $weight + $row['weight'];		
				
				
			$sql = "UPDATE characters SET weight=? WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "ii", $weight,$id);
			mysqli_stmt_execute($stmt);
		}
	}
    else{
        echo "NOT FOUND";
    }		
?>
	