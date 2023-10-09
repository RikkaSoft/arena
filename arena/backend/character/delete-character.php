<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;
	if($_POST['deleteme'] == ($_SESSION['characterProperties']['id'] . $_SESSION['loggedInId'])){

        $char_id =  $_SESSION['characterProperties']['id'];
        $charName = $_SESSION['characterProperties']['name'];
        $inv_id = $_SESSION['characterProperties']['inventory_id'];
        $equip_id = $_SESSION['characterProperties']['equipment_id'];
        $username = $_SESSION['loggedIn'];
        
        $sql = "SELECT * FROM battlereports WHERE username =? ORDER BY id DESC LIMIT 1";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $row = mysqli_fetch_assoc($result);
        
        $last = $row['id'];
        
        if($_SESSION['characterProperties']['level'] >= 10){
            
            
            
            $gender = $_SESSION['characterProperties']['gender'];
            $race = $_SESSION['characterProperties']['race'];
            $level = $_SESSION['characterProperties']['level'];
            $experience = $_SESSION['characterProperties']['experience'];
            $strength = $_SESSION['characterProperties']['strength'];
            $dexterity = $_SESSION['characterProperties']['dexterity'];
            $vitality = $_SESSION['characterProperties']['vitality'];
            $intellect = $_SESSION['characterProperties']['intellect'];
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
            $dodgeSkill = $_SESSION['characterProperties']['dodgeSkill'];
            $initiative = $_SESSION['characterProperties']['initiative'];
            $wins = $_SESSION['characterProperties']['wins'];
            $losses = $_SESSION['characterProperties']['losses'];
            $kills = $_SESSION['characterProperties']['kills'];
            $trainingWins = $_SESSION['characterProperties']['trainingWins'];
            $trainingLosses = $_SESSION['characterProperties']['trainingLosses'];
            $teamWins = $_SESSION['characterProperties']['teamWins'];
            $teamLosses = $_SESSION['characterProperties']['teamLosses'];
            
            $sql = "INSERT INTO heroes (inventory_id,equipment_id,name,gender,race,level,experience,strength,dexterity,vitality,intellect,one_handed,two_handed,shield,parry,finesse,foul_play,
            light_armour,heavy_armour,bow,crossbow,dodgeSkill,initiative,wins,losses,kills,trainingWins,trainingLosses,teamWins,teamLosses,user,lastReport)
            VALUES (
            '$inv_id','$equip_id','$charName','$gender','$race','$level','$experience','$strength','$dexterity','$vitality','$intellect','$one_handed','$two_handed','$shield','$parry','$finesse',
            '$foul_play','$light_armour','$heavy_armour','$bow','$crossbow','$dodgeSkill','$initiative','$wins','$losses','$kills','$trainingWins','$trainingLosses','$teamWins','$teamLosses','$username','$last')";
            mysqli_query($conn,$sql) or die(mysqli_error($conn));
        }
        
        
        //DELETE CHARACTER
        $sql = "DELETE FROM characters WHERE id = ?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "i", $char_id);
            mysqli_stmt_execute($stmt);
        
        if($_SESSION['characterProperties']['level'] < 10){
        //DELETE INVENTORY
        $sql = "DELETE FROM inventory WHERE iid = ?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "i", $inv_id);
            mysqli_stmt_execute($stmt);
        
        //DELETE EQUIPMENT
        $sql = "DELETE FROM equipment WHERE eid = ?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "i", $equip_id);
            mysqli_stmt_execute($stmt);
            
        }
        
        $sql = "DELETE FROM groupfightmembers WHERE characterId = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $char_id);
        mysqli_stmt_execute($stmt);
        
        $sql = "UPDATE users SET character_id='NULL' WHERE username=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
            
        
        
        $sql = "UPDATE battlereports SET username=0 WHERE username=? AND id <> ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "si", $username,$last);
        mysqli_stmt_execute($stmt);
        
        unset($_SESSION['characterProperties']);
        unset($_SESSION['charId']);
        header ('Location: index.php?page=create-char');

    }
    else{
        echo "something went wrong";
    }
	
?>