<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
	//GENERAL INFO
	$name 				= str_replace(' ', '', $_POST['name']);
	$gender				= $_POST['gender'];
	$race				= $_POST['race'];
	//SERGEANT STATS
	$strength			= $_POST['strength'];
	$dexterity			= $_POST['dexterity'];
	$vitality			= $_POST['vitality'];
	$intellect			= $_POST['intellect'];
	$allStats = $strength+$dexterity+$vitality+$intellect;
	//ADMIRAL SKILLS
	$one_handed 		= $_POST['one_handed'];
	$two_handed 		= $_POST['two_handed'];
	$shield 			= $_POST['shield'];
	$parry 				= $_POST['parry'];
	$foul_play 			= $_POST['foul_play'];
	$light_armour		= $_POST['light_armour'];
	$heavy_armour 		= $_POST['heavy_armour'];
	$bow				= $_POST['bow'];
	$crossbow	 		= $_POST['crossbow'];
	$finesse 	 		= $_POST['finesse'];
	$dodge 		        = $_POST['dodge'];
	$initiative 		= $_POST['initiative'];
	$allSkills = $one_handed+$two_handed+$shield+$parry+$foul_play+$light_armour+$heavy_armour+$bow+$crossbow+$finesse+$dodge+$initiative;
	// MAJOR FEAT
	#$feat				= $_POST['feat'];
	
	

	//CHECK IF CHAR IS UNIQUE
	
	if(preg_match("/^[a-zA-Z0-9]+$/", $name) == 0){
		$_SESSION['registerFail'] = "Your character name contained characters not allowed. <br>Allowed characters are a-z, A-Z, 0-9";
		header('Location: index.php?page=create-char'); 
		exit;
	}
	$sql = "SELECT name FROM characters WHERE name = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $name);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$rowcount = mysqli_num_rows($result);
	
	if ($rowcount >= 1){
		$_SESSION['registerFail'] = "The character name is already taken";
		header('Location: index.php?page=create-char'); 
		exit;
	}
	else {
		$username = $_SESSION['loggedIn'];
		$sql ="SELECT username FROM users WHERE username=? AND username!=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ss", $name,$username);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if (mysqli_num_rows($result)!==0){
			$_SESSION['registerFail'] = "The character name is already taken (or a username has the same name)";
			header('Location: index.php?page=create-char'); 
			exit;
		}
		else{
		$maxStrength = 65;
		$maxDexterity = 65;
		$maxVitality = 65;
		$maxIntellect = 65;
		$maxStats = 140;
		switch ($race){

			case "Human":
				$maxStrength = $maxStrength+5;
				$maxDexterity = $maxDexterity+5;
				$maxVitality = $maxVitality+5;
				$maxIntellect = $maxIntellect+5;
				break;
			case "Elf":
				$maxDexterity = $maxDexterity+20;
				break;
			case "Dwarf":
				$maxVitality = $maxVitality+15;
				$maxDexterity = $maxDexterity-5;
                $maxStrength = $maxStrength+10;
				break;
			case "Troll":
				$maxStrength = $maxStrength+20;
				$maxVitality = $maxVitality+5;
				$maxIntellect = $maxIntellect-5;
				break;
			case "Undead":
				$maxIntellect = $maxIntellect+20;
				break;
			case "Dryad":
				$maxStats = $maxStats-20;
				break;
		}
			if($strength > $maxStrength || $strength < ($maxStrength-50)){
				echo "You have chosen a higher or lower Strength value than allowed";
			}
			elseif($dexterity > $maxDexterity || $dexterity < ($maxDexterity-50)){
				echo "You have chosen a higher or lower Dexterity value than allowed";
			}
			elseif($vitality > $maxVitality || $vitality < ($maxVitality-50)){
				echo "You have chosen a higher or lower Vitality value than allowed";
			}
			elseif($intellect > $maxIntellect || $intellect < ($maxIntellect-50)){
				echo "You have chosen a higher or lower Intellect value than allowed";
			}
			elseif($allStats != $maxStats){
				echo "You have failed to create a character by allocating more statpoints than allowed";
			}
			elseif($allSkills != 100+$intellect){
				echo "You have failed to create a character by allocating more skillpoints than allowed";
			}
			else{
				//INVENTORY
				$sql = "INSERT INTO inventory (iid) VALUES (NULL);";
				if($conn->query($sql) === TRUE){
				$inventory_id = $conn->insert_id;
				}
				else{
					echo "Something went wrong creating your inventory, try again";
					exit;
				}
				$sql = "INSERT INTO craftinginventory (id) VALUES (NULL)";
				if($conn->query($sql) === TRUE){
				$crafting_id = $conn->insert_id;
				}
				else{
					echo "Something went wrong creating your crafting inventory, try again";
					exit;
				}
				
				//EQUIPMENT
				$sql = "INSERT INTO equipment (right_hand) VALUES ('1:1;1')";
				if($conn->query($sql) === TRUE){
				$equipment_id = $conn->insert_id;
				}
				else{
					echo "Something went wrong creating your equipment slots, try again";
				}
				
				//CHARACTER
				$sql = "INSERT INTO characters (inventory_id, equipment_id,crafting_id, name, gender, race, strength,dexterity,vitality,intellect,one_handed,two_handed,shield,parry,finesse,foul_play,light_armour,heavy_armour,bow,crossbow,dodgeSkill,initiative,hp,gold)
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,125)";
				
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "iiisssiiiiiiiiiiiiiiiii", $inventory_id,$equipment_id,$crafting_id,$name,$gender,$race,$strength,$dexterity,$vitality,
				$intellect,$one_handed,$two_handed,$shield,$parry,$finesse,$foul_play,$light_armour,$heavy_armour,$bow,$crossbow,$dodge,$initiative,$vitality);
				
				
				
				if(mysqli_stmt_execute($stmt) === TRUE){
				$char_id = $conn->insert_id;
				}
				else{
					echo "Something went wrong creating your character, try again";
				}
				
				//ADD CONNECTION CHAR -> USER
				$username = $_SESSION['loggedIn'];
				$sql = "UPDATE users SET character_id = ? WHERE username = ?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "is", $char_id,$username);
				
				if(mysqli_stmt_execute($stmt) === TRUE){
					$sql = "UPDATE battlereports SET username='0' WHERE username=?";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "s", $username);
					mysqli_stmt_execute($stmt);
					
					$_SESSION['charId'] = $char_id;
                    if (isset($_SESSION['other']['chatIcon'])){
                        $chatIcon = $_SESSION['other']['chatIcon'];
                        $sql = "UPDATE characters SET chatIcon='$chatIcon' WHERE id='$char_id'";
                        mysqli_query($conn,$sql);
                    }
					require_once(__ROOT__."/backend/character/update-characterSessions.php");
					
                    
					header('Location: index.php?page=news');
					}
				else{
				echo "Something went wrong linking your character, try again";
				}
				$_SESSION['charId'] = $char_id;
				require_once(__ROOT__."/backend/character/update-characterSessions.php");
	
			}
				
		}
	}
		
 ?>
	