<?php 
	require_once("../system/details.php");
	global $conn;

	//TRANSFER CHARACTERS FROM characters TO heroes
	$sql = "SELECT * FROM characters";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)){
        $inv_id = $row['inventory_id'];
        $equip_id = $row['equipment_id'];
        $charName = $row['name'];
        $gender = $row['gender'];
        $race = $row['race'];
        $level = $row['level'];
        $experience = $row['experience'];
        $strength = $row['strength'];
        $dexterity = $row['dexterity'];
        $vitality = $row['vitality'];
        $intellect = $row['intellect'];
        $one_handed = $row['one_handed'];
        $two_handed = $row['two_handed'];
        $shield = $row['shield'];
        $parry = $row['parry'];
        $finesse = $row['finesse'];
        $foul_play = $row['foul_play'];
        $light_armour = $row['light_armour'];
        $heavy_armour = $row['heavy_armour'];
        $bow = $row['bow'];
        $crossbow = $row['crossbow'];
        $dodgeSkill = $row['dodgeSkill'];
        $initiative = $row['initiative'];
        $wins = $row['wins'];
        $losses = $row['losses'];
        $kills = $row['kills'];
        $trainingWins = $row['trainingWins'];
        $trainingLosses = $row['trainingLosses'];
        $teamWins = $row['teamWins'];
        $teamLosses = $row['teamLosses'];
	    $id = $row['id'];
	    $sql2 = "SELECT username FROM users WHERE character_id='$id'";
	    $result = mysqli_query($conn,$sql2);
	    $row2 = mysqli_fetch_assoc($result);
	    
	    $username = $row2['username'];

	    $sql3 = "SELECT * FROM battlereports WHERE username =? ORDER BY id DESC LIMIT 1";
	    $stmt = mysqli_prepare($conn,$sql3);
	    mysqli_stmt_bind_param($stmt, "s", $username);
	    mysqli_stmt_execute($stmt);
	    $result = $stmt->get_result();
	    $row3 = mysqli_fetch_assoc($result);
	    
	    $last = $row3['id'];
        
        $sql4 = "INSERT INTO heroes (inventory_id,equipment_id,name,gender,race,level,experience,strength,dexterity,vitality,intellect,one_handed,two_handed,shield,parry,finesse,foul_play,
        light_armour,heavy_armour,bow,crossbow,dodgeSkill,initiative,wins,losses,kills,trainingWins,trainingLosses,teamWins,teamLosses,user,lastReport,season)
        VALUES (
        '$inv_id','$equip_id','$charName','$gender','$race','$level','$experience','$strength','$dexterity','$vitality','$intellect','$one_handed','$two_handed','$shield','$parry','$finesse',
        '$foul_play','$light_armour','$heavy_armour','$bow','$crossbow','$dodgeSkill','$initiative','$wins','$losses','$kills','$trainingWins','$trainingLosses','$teamWins','$teamLosses','$username','$last','$season')";
        mysqli_query($conn,$sql4);
        
        //DELETE CHARACTER
        $sql = "DELETE FROM characters WHERE id = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    $sql = "DELETE FROM inventory";
    mysqli_query($conn,$sql);
    $sql = "DELETE FROM guilds";
    mysqli_query($conn,$sql);
    $sql = "DELETE FROM questactive";
    mysqli_query($conn,$sql);
    $sql = "DELETE FROM questpending";
    mysqli_query($conn,$sql);
    $sql = "DELETE FROM questcomplete";
    mysqli_query($conn,$sql);
    $sql = "DELETE FROM questavailable WHERE userCreated=1";
    mysqli_query($conn,$sql);
    $sql = "UPDATE users SET character_id=0";
    mysqli_query($conn,$sql);


    
?>