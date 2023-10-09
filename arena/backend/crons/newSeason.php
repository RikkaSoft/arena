<?php 
	include("/var/www/html/arena/system/config.php");
	#include("C:\wamp64\www\Griem\Arena\system\config.php");
    require_once(__ROOT__."/backend/crafting/craftingFunctions.php");
    
	global $conn;

	//GET CURRENT SEASON
	$sql = "SELECT season FROM configuration";
	$result= mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$season = $row['season'];

	//TRANSFER CHARACTERS FROM characters TO heroes
	$sql = "SELECT * FROM characters";
    $result = mysqli_query($conn,$sql);
    $chars = array();
    while($row = mysqli_fetch_assoc($result)){
    	$chars[] = $row;
    }
    foreach($chars as $row){
    	echo $row['name'];
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
	    if($last == ""){
	    	$last = 0;
	    }
        
        $sql4 = "INSERT INTO heroes (inventory_id,equipment_id,name,gender,race,level,experience,strength,dexterity,vitality,intellect,one_handed,two_handed,shield,parry,finesse,foul_play,
        light_armour,heavy_armour,bow,crossbow,dodgeSkill,initiative,wins,losses,kills,trainingWins,trainingLosses,teamWins,teamLosses,user,lastReport,season)
        VALUES (
        '$inv_id','$equip_id','$charName','$gender','$race','$level','$experience','$strength','$dexterity','$vitality','$intellect','$one_handed','$two_handed','$shield','$parry','$finesse',
        '$foul_play','$light_armour','$heavy_armour','$bow','$crossbow','$dodgeSkill','$initiative','$wins','$losses','$kills','$trainingWins','$trainingLosses','$teamWins','$teamLosses','$username','$last','$season')";
        echo $sql4;
        mysqli_query($conn,$sql4);
        
        //DELETE CHARACTER
        $sql = "DELETE FROM characters WHERE id = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    $sql = "SELECT * FROM tournaments WHERE id=1";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $prizeItem = $row['prizeItem'];
    $winner = $row['winner'];
    $r1 = $row['round1'];
    $r1T = $row['round1Text'];
    $r2 = $row['round2'];
    $r2T = $row['round2Text'];
    $r2R = $row['round2Report'];
    $r3 = $row['round3'];
    $r3T = $row['round3Text'];
    $r3R = $row['round3Report'];
    $r4 = $row['round4'];
    $r4T = $row['round4Text'];
    $r4R = $row['round4Report'];
    $r5 = $row['round5'];
    $r5T = $row['round5Text'];
    $r5R = $row['round5Report'];
    $r6 = $row['round6'];
    $r6T = $row['round6Text'];
    $r6R = $row['round6Report'];

    $sql = "INSERT INTO finaltournaments (players,name,prizeItem,
    round1,round1Text,
    round2,round2Text,round2Report,
    round3,round3Text,round3Report,
    round4,round4Text,round4Report,
    round5,round5Text,round5Report,
    round6,round6Text,round6Report,
    winner,season) VALUES (32,'$name','$prizeItem',
    '$r1','$r1T',
    '$r2','$r2T','$r2R',
    '$r3','$r3T','$r3R',
    '$r4','$r4T','$r4R',
    '$r5','$r5T','$r5R',
    '$r6','$r6T','$r6R','$winner',$season)";
    mysqli_query($conn,$sql);

	$sql = "DELETE FROM tournaments";
    mysqli_query($conn,$sql);
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

    $sql = "UPDATE configuration SET season=season+1,finals=0,infoBarPrio='off', infoBarMessage='',infoBarMessageAlt='Season " . $season . " is live!'";
    mysqli_query($conn,$sql);

    //SEND EMAILS



    $name = "Season start tournament";
    $partId = (getRandomPartTier(2))['id'];
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (4,'$name',2,4,2,'$partId')";
    mysqli_query($conn,$sql);
    $partId = (getRandomPartTier(2))['id'];
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (4,'$name',5,7,2,'$partId')";
    mysqli_query($conn,$sql);
    $partId = (getRandomPartTier(3))['id'];
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (8,'$name',8,10,3,'$partId')";
    mysqli_query($conn,$sql);
    $partId = (getRandomPartTier(4))['id'];
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (8,'$name',11,13,4,'$partId')";
    mysqli_query($conn,$sql);
    $partId = (getRandomPartTier(5))['id'];
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (8,'$name',14,99,5,'$partId')";
    mysqli_query($conn,$sql);
    $part1 = (getRandomPartTier(5))['id'];
    $part2 = (getRandomPartTier(5))['id'];
    $partId = $part1 . "," . $part2;
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES (16,'$name',2,99,5,'$partId')";
    mysqli_query($conn,$sql);    
?>