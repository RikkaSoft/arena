<?php 
	global $conn;
	if (isset($_GET['name'])){
		$name = $_GET['name'];
		$sql = "SELECT * FROM npc WHERE name = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $name);
	}	
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	if ($row == 0){
	 echo "enemy doesn't exist";	
	}
	else{
			
			$name =				$row['name'];
			$gender = 			$row['gender'];
			$race = 			$row['race'];
			$vitality = 		$row['vitality'];
			$strength = 		$row['strength'];
			$intellect = 		$row['intellect'];
			$dexterity =		$row['dexterity'];
			$level =			$row['level'];
			$equipment_id =		$row['equipment_id'];
            $charId =           $row['id'];
			$desc = 			$row['description'];
			$xpReward = 		$row['xpReward'];
			$goldReward = 		$row['goldReward'];
			
			$sql = "SELECT * FROM npcequipment WHERE eid = ?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $equipment_id);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
			$row = mysqli_fetch_assoc($result);
			
			$right_hand =		$row['right_hand'];
			$left_hand =		$row['left_hand'];
			$head =				$row['head'];
			$chest =			$row['chest'];
			$arm =				$row['arm'];
			$leg = 				$row['leg'];
			$feet = 			$row['feet'];
			$secondary =        $row['secondary'];
			
			$equipment = array($right_hand,$left_hand,$secondary,$head,$chest,$arm,$leg,$feet);            
			echo "Name: " . $name . " (" . $level . ")<br>";
			#echo "Gender: " . $gender . "<br>";
			echo "Race: " . $race . "<br>";
			
			$playerLevel = $_SESSION['characterProperties']['level'];
			$xpReward = ($xpReward - $playerLevel);
			$goldReward = ($goldReward - $playerLevel);
			
			if($xpReward < 0){
				$xpReward = 0;
			}
			if($goldReward < 0){
				$goldReward = 0;
			}
			echo "Xp/Gold reward: " . $xpReward . "/" . $goldReward . "<br>";
			#echo "level: " . $level . "<br>";
			if($desc != ""){
				echo "<br>";			
				echo $desc;
				echo "<br>";
			}
            $i = 0;
            require_once(__ROOT__."/backend/other/itemFunctions.php");
            foreach ($equipment as $equip){
                if ($equip != "1:1;1"){
                    if ($i < 3){
                        $type = "1";
                    }
                    else{
                        $type = "2";
                    }
                    $equipment[$i] = "<span id='" . $equip . "' class='item" . $type . " npcItem' >" . getItemWithoutName($equip,$type) . "</span>";
                }
                else{
                    $equipment[$i] = "Nothing";
                }
                $i++;
            }
			
			echo "</br>" . "<strong id='strong'>Equipment</strong></a>" . "</br>Right Hand: $equipment[0]" 
			. "</br>Left Hand: $equipment[1]" . "</br>Secondary: $equipment[2]" . "</br>Head: $equipment[3]" . "</br>Chest: $equipment[4]" . "</br>Arm: $equipment[5]"
			. "</br>Leg: $equipment[6]" . "</br>";#Feet: $equipment[7]";
	}
	
?>