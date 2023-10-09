<?php
require_once(__ROOT__."/backend/crafting/craftingFunctions.php");
require_once(__ROOT__."/backend/other/itemFunctions.php");
function getOngoing(){

	global $conn;
	$sql = "SELECT * FROM tournaments where running=1";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		echo "<table class=\"tournamentTable\">";
		echo "<th>Name</th><th>Size</th><th>Min Level</th><th>Max Level</th><th>Entrance Fee</th><th>Rewards</th>";
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr>";
			echo "<td width=\"25%\"><a href=\"index.php?page=tournament&id=" . $row['id'] . "\">" . $row['name'] . "</a></td>";
			if ($row['players'] != ""){
				$signedUp = count(explode(",",$row['players']));
			}
			else{
				$signedUp = 0;
			}
			echo "<td width=\"6%\">" . $signedUp . "/" . $row['size'] . "</td>";
			echo "<td width=\"8%\">" . $row['minLevel'] . "</td>";
			echo "<td width=\"8%\">" . $row['maxLevel'] . "</td>";
			if ($row['entranceFee'] == 0){
				$row['entranceFee'] = "Free";
			}
			echo "<td width=\"10%\">" . $row['entranceFee'] . "</td>";
			$rewards = "";
			if ($row['prizeGold'] != 0){
				$rewards .= $row['prizeGold'] . "g ";
			}
			if ($row['prizeXP'] != 0){
				$rewards .= $row['prizeXP'] . "xp ";
			}
			if ($row['prizeItem'] != ""){
				$rewards .= $row['prizeItem'];
			}
			if($row['prizePart'] != ""){
				$parts = explode(",",$row['prizePart']);
				foreach($parts as $part){
					$info = getPartName($part);
					$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
				}
			}
			if ($rewards == ""){
				$rewards = "Nothing but honor";
			}
			echo "<td width=\"20%\">" . $rewards . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		echo "No tournaments are ongoing at the moment...";
	}
}

function getFuture(){
	global $conn;
	$sql = "SELECT * FROM tournaments where running=0 AND finished=0";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		echo "<table id='futureTables' class=\"tournamentTable\">";
		echo "<thead><th>Name</th><th>Size</th><th>Min Level</th><th>Max Level</th><th>Entrance Fee</th><th>Rewards</th><th>Starting</th></thead>";
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr>";
			echo "<td width=\"25%\"><a href=\"index.php?page=tournament&id=" . $row['id'] . "\">" . $row['name'] . "</a></td>";
			if ($row['players'] != ""){
				$signedUp = count(explode(",",$row['players']));
			}
			else{
				$signedUp = 0;
			}
			echo "<td width=\"6%\">" . $signedUp . "/" . $row['size'] . "</td>";
			echo "<td width=\"8%\">" . $row['minLevel'] . "</td>";
			echo "<td width=\"8%\">" . $row['maxLevel'] . "</td>";
			if ($row['entranceFee'] == 0){
				$row['entranceFee'] = "Free";
			}
			echo "<td width=\"10%\">" . $row['entranceFee'] . "</td>";
			$rewards = "";
			if ($row['prizeGold'] != 0){
				$rewards .= $row['prizeGold'] . "g ";
			}
			if ($row['prizeXP'] != 0){
				$rewards .= $row['prizeXP'] . "xp ";
			}
			if ($row['prizeItem'] != ""){
				$rewards .= $row['prizeItem'];
			}
			if($row['prizePart'] != ""){
				$parts = explode(",",$row['prizePart']);
				foreach($parts as $part){
					$info = getPartName($part);
					$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
				}
			}
			if ($rewards == ""){
				$rewards = "Nothing but honor";
			}
			echo "<td width=\"20%\">" . $rewards . "</td>";
            if ($row['start'] == "full"){
                $start = "When Full";
            }
            else{
                $start = $row['start'];
            }
			echo "<td width=\"14%\">" . $start . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		echo "No future tournaments are available at the moment...";
	}
}


function getPast(){
	global $conn;
	$sql = "SELECT * FROM tournaments where finished!=0 ORDER BY id DESC";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		echo "<table class=\"tournamentTable\">";
		echo "<thead><th>Name</th><th>Size</th><th>Min Level</th><th>Max Level</th><th>Fee</th><th>Rewards</th><th>Finished</th><th>Winner</th></thead>";
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr>";
			echo "<td width=\"25%\"><a href=\"index.php?page=tournament&id=" . $row['id'] . "\">" . $row['name'] . "</a></td>";
			if ($row['players'] != ""){
				$signedUp = count(explode(",",$row['players']));
			}
			else{
				$signedUp = 0;
			}
			echo "<td width=\"6%\">" . $signedUp . "/" . $row['size'] . "</td>";
			echo "<td width=\"8%\">" . $row['minLevel'] . "</td>";
			echo "<td width=\"8%\">" . $row['maxLevel'] . "</td>";
			if ($row['entranceFee'] == 0){
				$row['entranceFee'] = "Free";
			}
			echo "<td width=\"7%\">" . $row['entranceFee'] . "</td>";
			$rewards = "";
			if ($row['prizeGold'] != 0){
				$rewards .= $row['prizeGold'] . "g ";
			}
			if ($row['prizeXP'] != 0){
				$rewards .= $row['prizeXP'] . "xp ";
			}
			if ($row['prizeItem'] != ""){
				$rewards .= $row['prizeItem'];
			}
			if($row['prizePart'] != ""){
				$parts = explode(",",$row['prizePart']);
				foreach($parts as $part){
					$info = getPartName($part);
					$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
				}
			}
			if ($rewards == ""){
				$rewards = "Nothing but honor";
			}
			echo "<td width=\"14%\">" . $rewards . "</td>";
			echo "<td width=\"14%\">" . $row['finished'] . "</td>";
            echo "<td width=\"8%\">" . $row['winner'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		echo "No tournaments has been played yet...";
	}
}

function getFinales(){
	global $conn;
	$sql = "SELECT * FROM finaltournaments ORDER BY id DESC";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		echo "<table class=\"tournamentTable\">";
		echo "<thead><th>Name</th><th>Size</th><th>Season</th><th>Winner</th></thead>";
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr>";
			echo "<td width=\"25%\"><a href=\"index.php?page=tournament&season=" . $row['season'] . "&finals&id=" . $row['id'] . "\">" . $row['name'] . "</a></td>";
			echo "<td width=\"25%\">" . $row['size'] . "</td>";
			echo "<td width=\"25%\">" . $row['season'] . "</td>";
            echo "<td width=\"25%\">" . $row['winner'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		//echo "No tournaments has been played yet...";
	}
}

function signUp($id){
	#echo "<a href=\"index.php?page=tournament\">Go back</a>";
	global $conn;
	$sql = "SELECT * FROM tournaments where id=?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		if ($row['players'] != ""){
			$players = explode(",",$row['players']);
		}
		else{
			$players = $row['players'];
		}
		if (count($players) >=  $row['size']){
			echo "<p style=\"color:red\">The Tournament is unfortunatly full</p>";
		}
		elseif ($_SESSION['characterProperties']['level'] < $row['minLevel'] || $_SESSION['characterProperties']['level'] > $row['maxLevel']){
			echo "<p style=\"color:red\">You do not meet the level requirements</p>";
		}
		elseif ($row['running'] == 1 || $row['finished'] == 1){
			echo "<p style=\"color:red\">The tournament is already finished or running.</p>";
		}
		else{
			if($players != ""){
			    $player = $_SESSION['characterProperties']['id'];
			    if (!in_array($player, $players)){
    				array_push($players,$player);
    				$players = implode(",", $players);
                }
                else{
                    echo "<p style=\"color:red\">You are already signed up for this tournament</p>";
                }
			}
			else{
				$players = $_SESSION['characterProperties']['id'];
			}
			$sql = "UPDATE tournaments SET players='$players' WHERE id='$id'";
			mysqli_query($conn,$sql);
			require_once(__ROOT__."/backend/tournament/create-brackets.php");
			loadTournament($id,0,0);
		}
	}
	else{
		echo "Tournament not found";
	}
}
function chickenOut($id){
	#echo "<a href=\"index.php?page=tournament\">Go back</a>";
	global $conn;
	$sql = "SELECT * FROM tournaments where id=?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		$players = explode(",",$row['players']);
		if ($row['running'] == 1 || $row['finished'] == 1){
			echo "<p style=\"color:red\">The tournament is already finished or running.</p>";
		}
		else{
			if(($key = array_search($_SESSION['characterProperties']['id'], $players)) !== false) {
			    unset($players[$key]);
			}
			$players = implode(",", $players);
			$sql = "UPDATE tournaments SET players='$players' WHERE id='$id'";
			mysqli_query($conn,$sql);
			require_once(__ROOT__."/backend/tournament/create-brackets.php");
			loadTournament($id,0,0);
		}
	}
	else{
		echo "Tournament not found";
	}
}
function setSurrender($value){
	global $conn;
	$name = $_SESSION['characterProperties']['name'];
	$sql = "UPDATE characters SET tournamentSurrender=? WHERE name='$name'";
	$stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "d", $value);
    mysqli_stmt_execute($stmt);
	echo "You will surrender at " . $value*100 . "% hp";
}

if (isset($_GET['signUp'])){
	signUp($_GET['signUp']);
}
if (isset($_GET['chickenOut'])){
	chickenOut($_GET['chickenOut']);
}
if (isset($_GET['setSurrender'])){
	setSurrender($_GET['setSurrender']);
}

?>