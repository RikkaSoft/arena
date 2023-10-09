<?php
	
	
	
function listItems($type){
	global $conn;
	switch($type){
		case "right":
			$sql = "SELECT * FROM weapons WHERE type IN('1h','2h') ORDER BY price";
			break;
		case "left":
			$sql = "SELECT * FROM weapons WHERE type IN('1h','shield') ORDER BY price";
			break;
		case "secondary":
			$sql = "SELECT * FROM weapons WHERE type IN('bow','crossbow') ORDER BY price";
			break;
		case "head":
			$sql = "SELECT * FROM armours WHERE item_type='heads' ORDER BY price";
			break;
		case "chest":
			$sql = "SELECT * FROM armours WHERE item_type='chests' ORDER BY price";
			break;
		case "arms":
			$sql = "SELECT * FROM armours WHERE item_type='arms' ORDER BY price";
			break;
		case "legs":
			$sql = "SELECT * FROM armours WHERE item_type='legs' ORDER BY price";
			break;
		case "feet":
			$sql = "SELECT * FROM armours WHERE item_type='feets' ORDER BY price";
			break;
		default:
			$sql = "nope";
	}
	
	if($sql !== "nope"){
		$result = mysqli_query($conn,$sql);
		echo "<select name='" . $type . "'>";
			echo "<option value='1:1;1'>Nothing</option>";
			while($row = mysqli_fetch_assoc($result)){
				echo "<option value='" . $row['id'] . ":1;1'>" . $row['name'] . "</option>";
			}
		echo "</select>";
	}
	
}

function addNewCreature(){
	global $conn;
	var_dump($_POST);
	
	//GENERAL INFO
	$name 				= $_POST['name'];
	$gender				= $_POST['gender'];
	$race				= $_POST['race'];
	$level 				= $_POST['level'];
	//SERGEANT STATS
	$strength			= $_POST['strength'];
	$dexterity			= $_POST['dexterity'];
	$vitality			= $_POST['vitality'];
	$intellect			= $_POST['intellect'];
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
	
	$right_hand 		= $_POST['right'];
	$left_hand 			= $_POST['left'];
	$secondary			= $_POST['secondary'];
	$head		 		= $_POST['head'];
	$chest		 		= $_POST['chest'];
	$arms		 		= $_POST['arms'];
	$legs 				= $_POST['legs'];
	$feet		 		= $_POST['feet'];
	
	$goldReward 		= $_POST['goldReward'];
	$xpReward			= $_POST['xpReward'];
	$description 		= $_POST['raceDesc'];
	
	
	
	//EQUIPMENT
	$sql = "INSERT INTO npcequipment (right_hand,left_hand,secondary,head,chest,arm,leg,feet) VALUES (?,?,?,?,?,?,?,?)";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "ssssssss",$right_hand,$left_hand,$secondary,$head,$chest,$arms,$legs,$feet);
	if(mysqli_stmt_execute($stmt) === TRUE){
		$equipment_id = $conn->insert_id;
	}
	else{
		echo "Something went wrong" . $sql . "<br>" . $conn->error;
	}
	
	//CHARACTER
	$sql = "INSERT INTO npc (description,equipment_id, name, gender, race, level,strength,dexterity,vitality,intellect,one_handed,two_handed,shield,parry,finesse,foul_play,light_armour,heavy_armour,bow,crossbow,dodgeSkill,initiative,hp,goldReward,xpReward)
	VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "sisssiiiiiiiiiiiiiiiiiiii", $description,$equipment_id,$name,$gender,$race,$level,$strength,$dexterity,$vitality,
	$intellect,$one_handed,$two_handed,$shield,$parry,$finesse,$foul_play,$light_armour,$heavy_armour,$bow,$crossbow,$dodge,$initiative,$vitality,$goldReward,$xpReward);
	if(mysqli_stmt_execute($stmt) === TRUE){
		$char_id = $conn->insert_id;
	}
	else{
		echo "Something went wrong" . $sql . "<br>" . $conn->error;
	}
}

if(isset($_GET['addNew'])){
	addNewCreature();
}

?>