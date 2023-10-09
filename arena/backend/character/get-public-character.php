<?php 
	global $conn;
    $table = "characters";
    $season = 0;
    if(isset($_GET['season'])){
        $season = (int) $_GET['season'];
        if ($season != 0){
                $table = "heroes";
        }
        else{
            $table = "characters";
        }
    }

    
	if (isset($_GET['charName'])){
		$name = $_GET['charName'];
		if($table == "heroes"){
			$sql = "SELECT * FROM $table WHERE name = ? AND season = ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "si", $name,$season);
		}
		else{
			$sql = "SELECT * FROM $table WHERE name = ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "s", $name);
		}
		
		
	}
	elseif(isset($_GET['username'])){
		$username = $_GET['username'];
		$sql = "SELECT character_id FROM users WHERE username = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		if ($row == 0){
			echo "User doesn't exist";	
			exit;
		}
		else{
            
			$charId = $row['character_id'];
			$sql = "SELECT * FROM $table WHERE id = ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "s", $charId);
		}
	}
	
	
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	if ($row == 0){
	 echo "Character doesn't exist";	
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
			$arenaWins =		$row['wins'];
			$arenaLosses =		$row['losses'];
			$trainingWins =		$row['trainingWins'];
			$trainingLosses =	$row['trainingLosses'];
			$kills =			$row['kills'];
            $charId =           $row['id'];
			
            if ($season != 0){
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = mysqli_prepare($conn,$sql);
                mysqli_stmt_bind_param($stmt, "s", $row['user']);
            }
            else{
                $sql = "SELECT * FROM users WHERE character_id = ?";
                $stmt = mysqli_prepare($conn,$sql);
                mysqli_stmt_bind_param($stmt, "i", $charId);
            }
            
            mysqli_stmt_execute($stmt);
            $result = $stmt->get_result();
            $row = mysqli_fetch_assoc($result);
			
            $userId = $row['id'];
            $username = $row['username'];
            $realName = $row['name'];
			$regDate = $row['registeredDate'];
			$loginDate = $row['lastLoginDate'];
            if ($realName == ""){
                $realName = "Not set";
            }
			
			$sql = "SELECT * FROM equipment WHERE eid = ?";
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
            
            echo "<div id='getPlayerInfo'>";
            echo "<h2>User information</h2>";
            
            #echo "Member ID: " . $userId . "<br>";
            echo "Username: " . $username . "<br>";
			echo "<br>";
            #echo "Real name: " . $realName . "<br>";
            $regDate = substr($regDate, 0,4) . "-" . substr($regDate, 4,2) . "-" . substr($regDate, 6,2);
            echo "Registered on: " . $regDate . "<br>";
            echo "Most recent login: ";
			if(isset($loginDate)){
				echo $loginDate . "<br>";
			}
			else{
				echo "User hasn't logged in since the login timestamps were introduced (2017-05-25)";
			}
			
			echo "<h2>Character information</h2>";
			echo "Name: " . $name . "<br>";
			echo "Gender: " . $gender . "<br>";
			echo "Race: " . $race . "<br>";
			echo "level: " . $level . "<br>";
			echo "<br>";
			
			$arenaTotal = $arenaWins + $arenaLosses;
			$trainingTotal = $trainingWins + $trainingLosses;
			
			if ($arenaTotal > 0 ){
				echo "Arena win ratio: " . round(($arenaWins / $arenaTotal) * 100) . "%<br>";
			}
			else{
				echo $name . " has not played any arena matches yet<br>";
			}
			if ($trainingTotal > 0 ){
				echo "Training win ratio: " . round(($trainingWins / $trainingTotal) * 100) . "%<br>";
			}
			else{
				echo $name . " has not played any training matches yet<br>";
			}
			echo "<br>";
			
			if ($kills > 0){
				echo $name . " has killed " . $kills . " other players";
			}
			else{
				echo $name . " has not yet killed any other players";
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
                    $equipment[$i] = "<a href=\"index.php?page=view-item&type=" . $type . "&item_name=" . $equip . "\">" . getItemWithoutName($equip,$type) . "</a>";
                }
                else{
                    $equipment[$i] = "Nothing";
                }
                $i++;
            }
			
			echo "</br></br>" . "<strong>Equipment</strong></a>" . "</br>Right Hand: $equipment[0]" 
			. "</br>Left Hand: $equipment[1]" . "</br>Secondary: $equipment[2]" . "</br>Head: $equipment[3]" . "</br>Chest: $equipment[4]" . "</br>Arm: $equipment[5]"
			. "</br>Leg: $equipment[6]" . "</br>Feet: $equipment[7]";
			
			echo "</div>";
			echo "<div id='getPlayerIcons'>";
				//PLAYER ICONS
				require(__ROOT__ ."/backend/other/playerIconFunctions.php");
				getPlayerIcons($userId);
			echo "</div>";
	}
	
?>