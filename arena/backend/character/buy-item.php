<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
	$itemName = 	$_POST['itemName'];
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
    elseif($itemType == 4){
        $itemType = "parts";
    }
	else{
		echo "Hack prevention enabled";
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
        case 11:
            $itemSubType = "parts";
            break;
        default:
            $itemSubType = "error";
    }
	
    if($itemSubType == "error"){
        echo "Hack prevention enabled";
        exit;
    }
    
    if($itemSubType != "parts"){
    	$sql = "SELECT id,price,sellable,name FROM $itemType WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $itemName);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
    	$row = mysqli_fetch_assoc($result);
        if($itemType != "trinkets"){
        	$itemID = $row['id'] . ":1;1";
    	}
    	else{
    		$itemID = $row['id'];
    	}
        $itemName = $row['name'];
        
        if ($row['sellable'] == 0){
            echo "You are trying to buy an item that's not for sale, nice try bozo";
        }
        else{
        	$priceModifier = 1;
    	
    		$guildId = $_SESSION['characterProperties']['guild'];
    		if($guildId != 0){
    			include_once(__ROOT__.'/backend/guild/guildFunctions.php');
    			$returnArray = getGuildPerks($_SESSION['characterProperties']['guild']);
    			$priceModifier = $priceModifier - $returnArray['discount'];
    		}
    		
        	$price = round($row['price'] * $priceModifier);
        	$inv_id = $_SESSION['characterProperties']['inventory_id'];
        	$gold = $_SESSION['characterProperties']['gold'];
        	
        	if ($gold >= $price){
        		$sql = "UPDATE characters SET gold=gold-" . $price . " WHERE name='$char'";
        		if($conn->query($sql) === TRUE){
        		}
        		else{
        		echo $sql . "<br>" . $conn->error;
    				exit;
        		}
        		$sql = "UPDATE inventory SET $itemSubType=CONCAT($itemSubType,'" . $itemID . ",') WHERE iid='$inv_id'";
        		if($conn->query($sql) === TRUE){
        		}
        		else{
        		echo $sql . "<br>" . $conn->error;
    				exit;
        		}
        		echo "You purchased " . $itemName . " for " . $price . " gold!<br><br>" . "Don't forget to equip the item under the <a href=\"index.php?page=your-character\">\"My Character\"</a> page";
        		$_SESSION['charId'] = $_SESSION['characterProperties']['id'];
        		require_once(__ROOT__."/backend/character/update-characterSessions.php");
        	}
        	else{
        		echo "You did not have enough gold to buy " . $itemName . ", you have " . $gold . " and the item costs " . $price;
        	}
    	}
    }
    else{
        include_once(__ROOT__.'/backend/crafting/craftingFunctions.php');
        $charId = $_SESSION['characterProperties']['id'];
        $sql = "SELECT craftingparts.*,craftingpartssale.* FROM craftingpartssale INNER JOIN craftingparts ON craftingpartssale.partID = craftingparts.id WHERE characterId='$charId' AND partId=? AND sold=0";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $itemName);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        if(mysqli_num_rows($result)){
            $row = mysqli_fetch_assoc($result);
            $price = (50-$row['rarity'])*$row['tier'];
            $priceModifier = 1;
        
            $guildId = $_SESSION['characterProperties']['guild'];
            if($guildId != 0){
                include_once(__ROOT__.'/backend/guild/guildFunctions.php');
                $returnArray = getGuildPerks($_SESSION['characterProperties']['guild']);
                $priceModifier = $priceModifier - $returnArray['discount'];
            }
            
            $price = round($price * $priceModifier);

            $gold = $_SESSION['characterProperties']['gold'];
            
            if ($gold >= $price){
                $sql = "UPDATE characters SET gold=gold-" . $price . " WHERE name='$char'";
                if($conn->query($sql) === TRUE){
                }
                else{
                echo $sql . "<br>" . $conn->error;
                    exit;
                }
                insertPart($row['id'],$row['slotType'],"none",$row);

                echo "You bought " . $row['name'] . " for " . $price . " gold";
                $_SESSION['charId'] = $_SESSION['characterProperties']['id'];
                require_once(__ROOT__."/backend/character/update-characterSessions.php");

                $saleId = $row['saleId'];
                $sql = "UPDATE craftingpartssale SET sold=1 WHERE saleId ='$saleId'";
                mysqli_query($conn,$sql);
                echo "<script>
                    $('#partsCat').find('#'+" . $saleId . ").remove();
                </script>";
            }
            else{
                echo "You did not have enough gold to buy " . $itemName . ", you have " . $gold . " and the item costs " . $price;
            }
        }
        else{
            echo "You're not permitted to buy this part";
        }

        
    }
	
?>