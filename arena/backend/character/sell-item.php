<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
	$itemId = 	intval($_POST['itemName']);
	$itemType =		$_POST['itemType'];
	$itemSubType = 	$_POST['itemSubType'];
	$char = 		$_SESSION['characterProperties']['name'];

    if($itemType == 1){
        $itemType = "weapons";
    }
	elseif($itemType == 2){
        $itemType = "armours";
    }
	elseif($itemType == 3){
        $itemType = "trinkets";
    }
	else{
		echo "First anti cheat system engaged";
		exit;
	}
    switch($itemSubType){
        case 1:
            $itemSubType = "weapons";
            break;
        case 2:
            $itemSubType = "heads";
            break;
        case 3:
            $itemSubType = "chests";
            break;
        case 4:
            $itemSubType = "arms";
            break;
        case 5:
            $itemSubType = "legs";
            break;
        case 6:
            $itemSubType = "feets";
            break;
        case 7:
            $itemSubType = "secondarys";
            break;
		case 9:
			$itemSubType = "trinkets";
			break;
        default:
            $itemSubType = "error";
    }
    
	if($itemSubType == "error"){
		echo "Second anti cheat system engaged";
		exit;
	}
    
	$sql = "SELECT name,price FROM $itemType WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $itemId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	$itemName = $row['name'];
	$sellPrice = round($row['price']/4);
	
	$gold = $_SESSION['characterProperties']['gold'];
	
	$inv_id = $_SESSION['characterProperties']['inventory_id'];
	$sql = "SELECT * FROM inventory WHERE iid = '$inv_id'";
	$result=mysqli_query($conn, $sql);
	$equipment = mysqli_fetch_assoc($result);
	
	$sellItems = $equipment[$itemSubType];
	
	$explodedInv = explode(",", $sellItems);
	
	$found = "false";
	$itemCount = 0;
	foreach ($explodedInv as $items){
		if($items == $itemId){
			$found = "true";
			unset($explodedInv[$itemCount]);
			break;
		}
		$itemCount++;
	}
	
	if ($found == "true"){
		$implodedInv = implode(',', $explodedInv);
		
		$sql = "UPDATE inventory SET $itemSubType=? WHERE iid = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "si", $implodedInv,$inv_id);
		mysqli_stmt_execute($stmt);
		
		$sql = "UPDATE characters SET gold=gold+? WHERE name = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "is", $sellPrice,$char);
		mysqli_stmt_execute($stmt);
		
		echo "You sold " . $itemName . " for " . $sellPrice . " gold!";
		$_SESSION['charId'] = $_SESSION['characterProperties']['id'];
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
	}
	else{
		echo "Item not found";
	}
	
?>