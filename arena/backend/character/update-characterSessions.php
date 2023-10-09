<?php
global $conn;
function fullRefresh($id){
	global $conn;
	require_once(__ROOT__."/backend/other/itemFunctions.php");
        $sql = "SELECT c.*, e.*, s1.name as 'right_handR', s1.enchantType as 'right_enchant', s2.name as 'left_handR', s2.enchantType as 'left_enchant', s3.name as 'head', e.head as 'headString', 
        s4.name as 'chest', e.chest as 'chestString', s5.name as 'arm', e.arm as 'armString', s6.name as 'leg', e.leg as 'legString', s7.name as 'feet', e.feet as 'feetString', 
        s8.name as 'secondary', e.secondary as 'secondaryString', s9.name as 'trinket', e.trinket as 'trinketString'
            FROM characters c
            LEFT JOIN equipment e on c.equipment_id=e.eid
            LEFT JOIN weapons s1 on s1.id=e.right_hand
            LEFT JOIN weapons s2 on s2.id=e.left_hand
            LEFT JOIN armours s3 on s3.id=e.head
            LEFT JOIN armours s4 on s4.id=e.chest
            LEFT JOIN armours s5 on s5.id=e.arm
            LEFT JOIN armours s6 on s6.id=e.leg
            LEFT JOIN armours s7 on s7.id=e.feet
            LEFT JOIN weapons s8 on s8.id=e.secondary
            LEFT JOIN trinkets s9 on s9.id=e.trinket
            WHERE c.id ='$id'";
        $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));    
        $row = mysqli_fetch_assoc($result);  
        
        if (mysqli_num_rows($result) > 0){
        
            //weapon+enchantString
            $row['right_handString'] = $row['right_hand'];
            $row['left_handString'] = $row['left_hand'];
            
            //get enchants
            $row['right_hand'] = getItemWithName($row['right_hand'],$row['right_handR'],$row['right_enchant']);
            $row['left_hand'] = getItemWithName($row['left_hand'],$row['left_handR'],$row['left_enchant']);
            $row['secondary'] = getItemWithName($row['secondaryString'],$row['secondary'],"ranged");
            $row['head'] = getItemWithName($row['headString'], $row['head'], "armour");
            $row['chest'] = getItemWithName($row['chestString'], $row['chest'], "armour");
            $row['arm'] = getItemWithName($row['armString'], $row['arm'], "armour");
            $row['leg'] = getItemWithName($row['legString'], $row['leg'], "armour");
            $row['feet'] = getItemWithName($row['feetString'], $row['feet'], "armour");
            
            //remove unnessesary elements in characterProperties
            unset($row['right_handR']);
            unset($row['left_handR']);
            unset($row['right_enchant']);
            unset($row['left_enchant']);
            
            $_SESSION['characterProperties'] = $row;
    		unset($_SESSION['charId']);
		}
}
	if (isset($_SESSION['charId'])){
	    fullRefresh($_SESSION['charId']);
	}
	elseif (isset($_SESSION['characterProperties']['id'])){
        $id = $_SESSION['characterProperties']['id'];
        $sql = "SELECT * FROM characters WHERE id ='$id'";
        $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));   
		if (mysqli_num_rows($result) == 0){
			unset($_SESSION['characterProperties']);
		} 
		else{
			$row = mysqli_fetch_assoc($result);  
        	$_SESSION['characterProperties'] = array_merge($_SESSION['characterProperties'],$row);
		}
	}
	else{
		$username = $_SESSION['loggedIn'];
		$sql = "SELECT character_id FROM users WHERE username='$username'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		if ($row['character_id'] != 0){
			fullRefresh($row['character_id']);
		}
	}

?>