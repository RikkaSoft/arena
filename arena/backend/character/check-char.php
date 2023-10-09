<?php
if(isset($_GET['getRaceDesc'])){
	$race = $_GET['getRaceDesc'];
	switch ($race){
			
		case "Human":
			$description = "The humans prefer the classic sword and board playstyle.<br>A shield, a sword and some armour<br><br>The Humans have no speciality, they are well rounded fighters<br><br>+5 Strength<br>+5 Dexterity<br>+5 Vitality<br>+5 Intellect";
			break;
		case "Elf":
			$description = "The elves are nimble forest dwelling creatures<br><br>+20 Dexterity";
			break;
		case "Dwarf":
			$description = "The Dwarves are strong and resilient, but less agile than others<br><br>+15 Vitality<br>+10 Strength<br>-5 Dexterity";
			break;
		case "Troll":
			$description = "Trolls are much stronger but dumber than the other races<br><br>+20 Strength<br>+5 Vitality<br>-5 Intellect";
			break;
		case "Undead":
			$description = "The undead are the smartest race since they have a lifetime worth of experience before they turned undead<br><br>+20 Intellect<br><br>";
			break;
		case "Dryad":
			$description = "The dryads have regenerative powers but are less skilled than the other races.<br><br>Regenerates health during battle (1-4 per round depending on level)<br>-20 skill points<br><br>";
			break;
	}
	echo $description;
}

if(isset($_GET['getRaceTips'])){
    $race = $_GET['getRaceTips'];
    switch ($race){
            
        case "Human":
            $tips = "
            <strong>This is recommended:</strong>
            <ul>
            <li>
                Atleast 25 strength
            </li>
            <li>
                High one-handed skill
            </li>
            <li>
                High shield skill
            </li>
            <li>
                30 armour skill
            </li>
            <li>
                (Optional) Bow or crossbow skill
            </li>
            
            </ul>
            ";
            break;
        case "Elf":
            $tips = "Elves usually favors ranged combat combined with dual wielding daggers<br><br>
            <strong>This is recommended:</strong>
            <ul>
            <li>
                Atleast 25 strength
            </li>
            <li>
                Low one-handed skill
            </li>
            <li>
                High Bow skill
            </li>
            <li>
                High dodge skill
            </li>
            <li>
                (optional) Initative to maximize the number of ranged attacks before your opponent reaches you
            </li>
            
            </ul>
            ";
            break;
        case "Dwarf":
            $tips = "The dwarves usually favors builds with an axe, shield and heavy armour<br><br>
            <strong>This is recommended:</strong>
            <ul>
            <li>
                Atleast 35 strength
            </li>
            <li>
                High one-handed skill
            </li>
            <li>
                High shield skill
            </li>
            <li>
                30 heavy armour skill
            </li>
            
            </ul>
            ";
            break;
        case "Troll":
            $tips = "The trolls likes to slay as many foes as possible with hard hitting two handed weapons<br><br>
            <strong>This is recommended:</strong>
            <ul>
            <li>
                Atleast 40 strength
            </li>
            <li>
                High two-handed skill
            </li>
            <li>
                Medium Finesse skill
            </li>
            <li>
                (Optional) Atleast 30 heavy armour skill
            </li>
            
            </ul>
            ";
            break;
        case "Undead":
            $tips = "The undeads are jack of all trades, they don't favor any specific build";
            break;
            
        case "Dryad":
            $tips = "Because of the health regeneration, dryads often fight protected by heavy armour to get the maximum effect out of their regeneration<br><br>
            
			<strong>This is recommended:</strong>
            <ul>
            <li>
                Atleast 25 strength
            </li>
            <li>
                High shield skill
            </li>
            <li>
                30 armour skill
            </li>
            
            </ul>
            ";
            break;
    }
    echo $tips;
}

if(isset($_GET['getRaceStats'])){
	$race = $_GET['getRaceStats'];
		$strength = 15;
		$dexterity = 15;
		$vitality = 15;
		$intellect = 15;
	switch ($race){

		case "Human":
			$strength = $strength+5;
			$dexterity = $dexterity+5;
			$vitality = $vitality+5;
			$intellect = $intellect+5;
			break;
		case "Elf":
			$dexterity = $dexterity+20;
			break;
		case "Dwarf":
			$vitality = $vitality+15;
            $dexterity = $dexterity-5;
            $strength = $strength+10;
			break;
		case "Troll":
			$strength = $strength+20;
            $vitality = $vitality+5;
			$intellect = $intellect-5;
			break;
		case "Undead":
			$intellect = $intellect+20;
			break;
	}
	$array = array($strength,$dexterity,$vitality,$intellect);
	echo json_encode($array);

}

if(isset($_GET['checkName'])){
    global $conn;
	$name = $_GET['checkName'];
	$nameLength = strlen($name);
	
	if ($nameLength < 3){
		echo "<nameText style=\"color:red\">Name too short</nameText>";
	}
	elseif ($nameLength > 2 && $nameLength < 13){
		$sql ="SELECT name FROM characters WHERE name=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $name);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if (mysqli_num_rows($result)!==0){
			echo "<nameText style=\"color:red\">Unavailable!</nameText>";
		}
		else{
			$username = $_SESSION['loggedIn'];
			$sql ="SELECT username FROM users WHERE username=? AND username!=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "ss", $name,$username);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			if (mysqli_num_rows($result)!==0){
				echo "<nameText style=\"color:red\">Unavailable!</nameText>";
			}
			else{
				echo "<nameText style=\"color:green\">Available!</nameText>";
			}
		}
				
	}
	else{
		echo "<nameText style=\"color:red\">Name too long</nameText>";
	}

	

}


?>