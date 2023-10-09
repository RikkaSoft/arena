<?php

function match1v1(){
	global $conn;
	$fightLevelChoice = $_SESSION['characterProperties']['fightLevelChoice'];
	$level = $_SESSION['characterProperties']['level'];
	$name = $_SESSION['characterProperties']['name'];
	if ($_SESSION['characterProperties']['hp'] == $_SESSION['characterProperties']['vitality']+$_SESSION['characterProperties']['vitalityFromGear']){
	    
	if ($fightLevelChoice == 0)
				{
					$sql = "SELECT name FROM characters WHERE battleType='1' AND adventureArea IS NULL AND battleReady='1' AND (level + fightLevelChoice = ? OR level= ? OR (level<? AND fightLevelChoice=3)) AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT 1";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "iiis", $level,$level,$level,$name);
					mysqli_stmt_execute($stmt);
				}
				
				elseif ($fightLevelChoice == 1)
				{
					$sql = "SELECT name FROM characters WHERE battleType='1' AND adventureArea IS NULL AND battleReady='1' AND (level + fightLevelChoice = ? OR level IN(?,?) OR (level<? AND fightLevelChoice=3)) AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT 1";
					$stmt = mysqli_prepare($conn,$sql);
					$levelPlusOne = $level + 1;
					mysqli_stmt_bind_param($stmt, "iiiis", $level,$level,$levelPlusOne,$level,$name);
					mysqli_stmt_execute($stmt);
				}
				elseif ($fightLevelChoice == 2)
				{
					$sql = "SELECT name FROM characters WHERE battleType='1' AND adventureArea IS NULL AND battleReady='1' AND (level + fightLevelChoice = ? OR level IN(?,?,?) OR (level<? AND fightLevelChoice=3)) AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT 1";
					$stmt = mysqli_prepare($conn,$sql);
					$levelPlusOne = $level + 1;
					$levelPlusTwo = $level + 2;
					mysqli_stmt_bind_param($stmt, "iiiiis", $level,$level,$levelPlusOne,$levelPlusTwo,$level,$name);
					mysqli_stmt_execute($stmt);
				}
				elseif ($fightLevelChoice == 3)
				{
					$sql = "SELECT name FROM characters WHERE battleType='1' AND adventureArea IS NULL AND battleReady='1' AND (level + fightLevelChoice = ? OR level > ? OR (level<? AND fightLevelChoice=3)) AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT 1";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "iiis", $level,$level,$level,$name);
					mysqli_stmt_execute($stmt);
				}
                else{
                    echo "<a href=\"index.php?fpage=close-report\"> Something weird has happened, click here to queue up again</a>";
                }
			$result = $stmt->get_result();
			$rowCount = mysqli_num_rows($result);
            
			if ($rowCount == 0) {
					
				echo "<h3>In queue for a 1v1 match</h3>";
				echo "You are still waiting for a match to be found, you will surrender at " . $_SESSION['characterProperties']['battleSurrender']*100 . "% of your maximum HP<br>
				Your current HP is " . $_SESSION['characterProperties']['hp'] . " and you will surrender at " . round(($_SESSION['characterProperties']['vitality']+$_SESSION['characterProperties']['vitalityFromGear'])*$_SESSION['characterProperties']['battleSurrender']) . " HP<br><br>";
				if ($fightLevelChoice == 0){
					echo "You are searching for a fighter who is the same or lower level than you<br><br>";
				}
				elseif ($fightLevelChoice == 1){
					echo "You are searching for a fighter who is one level higher than you.<br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				elseif ($fightLevelChoice == 2){
					echo "You are searching for a fighter who is two levels higher than you.<br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				else {
					echo "You are searching for a fighter who is any level higher than you, <u>Dangerzone!</u><br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				echo "The match will <u>not</u> start unless you are at 100% HP, so don't worry about training while you are waiting to get matched<br><br>";
				echo "<strong><a class=\"headerButtonLink\" href=\"index.php?fpage=stop-arena&nonUI\"> Click here to stop searching for a match </a></strong>";
			}
			else{
				require_once(__ROOT__."/backend/fighting/newFight.php");
				$row = mysqli_fetch_assoc($result);
				fight($name,$row['name'],1,0,0);
			}
		}
		else{
			echo "<h3>In queue for a 1v1 match</h3>";
				echo "You are still waiting for a match to be found, you will surrender at " . $_SESSION['characterProperties']['battleSurrender']*100 . "% of your maximum HP<br>
				Your current HP is " . $_SESSION['characterProperties']['hp'] . " and you will surrender at " . round(($_SESSION['characterProperties']['vitality']+$_SESSION['characterProperties']['vitalityFromGear'])*$_SESSION['characterProperties']['battleSurrender']) . " HP<br><br>";
				if ($fightLevelChoice == 0){
					echo "You are searching for a fighter who is the same or lower level than you<br><br>";
				}
				elseif ($fightLevelChoice == 1){
					echo "You are searching for a fighter who is one level higher than you.<br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				elseif ($fightLevelChoice == 2){
					echo "You are searching for a fighter who is two levels higher than you.<br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				else {
					echo "You are searching for a fighter who is any level higher than you, <u>Dangerzone!</u><br>
					You can still be matched up against fighters lower or equal to your level<br><br>";
				}
				echo "The match will <u>not</u> start unless you are at 100% HP, so don't worry about training while you are waiting to get matched<br><br>";
				echo "<strong><a class=\"headerButtonLink\" href=\"index.php?fpage=stop-arena&nonUI\"> Click here to stop searching for a match </a></strong>";
		}
}

function matchGroup($num){
	global $conn;
	
	$playerCount = ($num*2)-1;
	if ($_SESSION['characterProperties']['hp'] >= $_SESSION['characterProperties']['vitality']){
		
	$yourLevel = $_SESSION['characterProperties']['level'];
	$name = $_SESSION['characterProperties']['name'];
		switch ($yourLevel){
			case 1:
			case 2:
			case 3:
				$level = "1";
				break;
			case 4:
			case 5:
			case 6:
				$level = "4";
				break;
			case 7:
			case 8:
			case 9:
				$level = "7";
				break;
			default:
				$level = "10+";
				break;
		}
		
		if ($level != "10+"){
			$sql = "SELECT name FROM characters WHERE battleType=? AND adventureArea IS NULL AND battleReady='1' AND level IN($level,$level+1,$level+2) AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "isi", $num,$name,$playerCount);
			mysqli_stmt_execute($stmt);	
		}
		else{
			$sql = "SELECT name FROM characters WHERE battleType=? AND adventureArea IS NULL AND battleReady='1' AND level>9 AND name!=? AND hp=vitality+vitalityFromGear ORDER BY searchTime LIMIT ?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "isi", $num,$name,$playerCount);
			mysqli_stmt_execute($stmt);	
		}
		
		$result = $stmt->get_result();
		$rowCount = mysqli_num_rows($result);
		if ($rowCount != $playerCount) {
	
			echo "<h3>In queue for a " . $num . "v" . $num . " match</h3>";
			echo "You are still waiting for a match to be found, you will surrender at " . $_SESSION['characterProperties']['battleSurrender']*100 . "% of your maximum HP<br>
			Your current HP is " . $_SESSION['characterProperties']['hp'] . " and you will surrender at " . round(($_SESSION['characterProperties']['vitality']+$_SESSION['characterProperties']['vitalityFromGear'])*$_SESSION['characterProperties']['battleSurrender']) . " HP<br><br>";

			echo "The match will <u>not</u> start unless you are at 100% HP, so don't worry about training while you are waiting to get matched<br><br>";
			echo "<strong><a class=\"headerButtonLink\" href=\"index.php?fpage=stop-arena&nonUI\"> Click here to stop searching for a match </a></strong>";
		}
		else{
			require_once(__ROOT__."/backend/fighting/newFight.php");
			$players = array();
			while ($row = mysqli_fetch_assoc($result)){
				array_push($players,$row['name']);
			}
				
			fight($name,$players,1,0,0);
		}
	}
	else{
		echo "<h3>In queue for a " . $num . "v" . $num . " match</h3>";
		echo "You are still waiting for a match to be found, you will surrender at " . $_SESSION['characterProperties']['battleSurrender']*100 . "% of your maximum HP<br>
		Your current HP is " . $_SESSION['characterProperties']['hp'] . " and you will surrender at " . round(($_SESSION['characterProperties']['vitality']+$_SESSION['characterProperties']['vitalityFromGear'])*$_SESSION['characterProperties']['battleSurrender']) . " HP<br><br>";

		echo "The match will <u>not</u> start unless you are at 100% HP, so don't worry about training while you are waiting to get matched<br><br>";
		echo "<strong><a class=\"headerButtonLink\" href=\"index.php?fpage=stop-arena&nonUI\"> Click here to stop searching for a match </a></strong>";
	}			
}


function showLastReport(){
	global $conn;
	echo "<div style='padding:10px';>";
	echo "<a href=\"index.php?fpage=close-report&nonUI\">Close Report</a>";
	$name = $_SESSION['loggedIn'];
	$sql = "SELECT report FROM battlereports WHERE type NOT IN ('tournament') AND username='$name' ORDER BY id DESC LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);
	echo $row['report'];
	echo "<br><br><a href=\"index.php?fpage=close-report&nonUI\">Close Report</a>";
	echo "</div>";
}

#match1v1();

if(isset($_GET['match1v1'])){
	match1v1();
}
else if(isset($_GET['matchGroup'])){
	matchGroup($_GET['matchGroup']);
}
?>