<?php
	require_once(__ROOT__."/system/details.php");
	$_SESSION['charId'] = $_SESSION['characterProperties']['id'];
	global $conn;
	
	$newStrength			= $_POST['newStrength'];
	$newDexterity			= $_POST['newDexterity'];
	$newVitality			= $_POST['newVitality'];
	$newIntellect			= $_POST['newIntellect'];
	
	$newStats = array($newStrength,$newDexterity,$newVitality,$newIntellect);

	$newOne_handed 			= $_POST['one_handed'];
	$newTwo_handed 			= $_POST['two_handed'];
	$newShield 			    = $_POST['shield'];
	$newParry 			    = $_POST['parry'];
	$newFoul_play 		   	= $_POST['foul_play'];
	$newLight_armour		= $_POST['light_armour'];
	$newHeavy_armour 		= $_POST['heavy_armour'];
	$newBow				    = $_POST['bow'];
	$newCrossbow	 		= $_POST['crossbow'];
	$newFinesse 	 		= $_POST['finesse'];
	$newDodge       		= $_POST['dodge'];
    $newInitiative 		    = $_POST['initiative'];
	
	$newSkills = array($newOne_handed,$newTwo_handed,$newBow,$newCrossbow,$newShield,$newParry,$newFoul_play,$newLight_armour,$newHeavy_armour,$newFinesse,$newDodge,$newInitiative);
	

	$char_id = $_SESSION['characterProperties']['id'];
	$sql = "SELECT * FROM characters WHERE id =?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $char_id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	if ($levelUp = $row['levelUp'] == 0){
		echo "Nice try bozo";
	}
else{
		$strength				= $row['strength'];
		$dexterity				= $row['dexterity'];
		$vitality				= $row['vitality'];
		$intellect				= $row['intellect'];
		
		$oldStats = array($strength,$dexterity,$vitality,$intellect);
		
		$one_handed 			= $row['one_handed'];
		$two_handed 			= $row['two_handed'];
		$shield 				= $row['shield'];
		$parry 					= $row['parry'];
		$foul_play 				= $row['foul_play'];
		$light_armour			= $row['light_armour'];
		$heavy_armour 			= $row['heavy_armour'];
		$bow					= $row['bow'];
		$crossbow	 			= $row['crossbow'];
		$finesse 	 			= $row['finesse'];
		$dodge 			        = $row['dodgeSkill'];
		$initiative 			= $row['initiative'];
		
		$oldSkills = array($one_handed,$two_handed,$bow,$crossbow,$shield,$parry,$foul_play,$light_armour,$heavy_armour,$finesse,$dodge,$initiative);
		
		for($i = 4;$i < count($newSkills);$i++){
			if($newSkills[$i] > 300){
				echo "LEVEL UP ERROR 65: Skill exceeding 300 - The system thinks you are trying to cheat, are you?<br>";
				exit;
			}
		}
		
		$times = count($newStats);
		$i = 0;
		while($i < $times){
			if($newStats[$i] < $oldStats[$i]){
				echo "LEVEL UP ERROR 74: Reducing stats - The system thinks you are trying to cheat, are you?<br>";
				exit;
			}
			$i++;
		}
		
		$times = count($newSkills);
		$i = 0;
		while($i < $times){
			if($newSkills[$i] < $oldSkills[$i]){
				echo "LEVEL UP ERROR 84: Reducing skills - The system thinks you are trying to cheat, are you?<br>";
				exit;
			}
			$i++;
		}
		
		if ((array_sum($newStats)-20) !== array_sum($oldStats)){
			echo "LEVEL UP ERROR 91: Increasing stats - The system thinks you are trying to cheat, are you?<br>";
			exit;
		}
		if($newVitality-$vitality < 5){
			echo "LEVEL UP ERROR 321: Increase with less than 5 vitality - The system thinks you are trying to cheat, are you?<br>";
			exit;
		}
        $skillPoints = round(25+($intellect/2));
        $newSkillPoints = array_sum($newSkills)-$skillPoints;
        $oldSkillPoints = array_sum($oldSkills);
		if ($newSkillPoints != $oldSkillPoints){
		    echo (array_sum($newSkills)-$skillPoints) . " - " . array_sum($oldSkills) . "<br>";
			echo $newOne_handed . " " . $newTwo_handed . " " . $newShield . " " . $newParry . " " . $newFinesse . " " . $newFoul_play . " " . $newLight_armour . " " . $newHeavy_armour . " " . $newBow . " " . $newCrossbow . " " . $newDodge . " " . $newInitiative;
			echo "newSkills-Int " . (array_sum($newSkills)-$skillPoints) . "<br><br>";
			echo "oldSkills " . array_sum($oldSkills) . "<br><br>";
			echo "LEVEL UP ERROR 102 - The system thinks you are trying to cheat, are you?<br>";
			exit;
		}
		
		
		$sql = "UPDATE characters SET levelUp=levelUp-1, strength=?, dexterity=?, vitality=?, intellect=?, one_handed=?,two_handed=?,shield=?,parry=?,foul_play=?,light_armour=?,
				heavy_armour=?,bow=?,crossbow=?,finesse=?,dodgeSkill=?,initiative=?,hp=? WHERE id = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "iiiiiiiiiiiiiiiiii", $newStrength,$newDexterity,$newVitality,$newIntellect,$newOne_handed,$newTwo_handed,$newShield,$newParry,$newFoul_play,$newLight_armour,
		$newHeavy_armour,$newBow,$newCrossbow,$newFinesse,$newDodge,$newInitiative,$newVitality,$char_id);
		if(mysqli_stmt_execute($stmt) === TRUE){
				header('Location: index.php?page=your-character');
				}
		else{
		echo "Something went wrong, try again";
		}
	}
?>