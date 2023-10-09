<?php

function listAllIcons(){
	global $conn;
	
	$name = $_SESSION['loggedIn'];
	$sql = "SELECT chatIcons FROM users WHERE username='$name'";
	$result = mysqli_query($conn,$sql);
	
	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		$myIcons = explode(",",$row['chatIcons']);
		
		$availableIcons = array();
		$lockedIcons = array();
		
		$sql = "SELECT * FROM chaticons";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			if(in_array($row['id'], $myIcons)){
				$availableIcons[] = $row;
			}
			else{
				if($row['unlockable'] == 1){
					$lockedIcons[] = $row;
				}
			}
		}
		echo "<form action='index.php?page=playerIcon' method='post'>";
		echo "<div class='iconTables'>";
		echo "<fieldset class='iconBoxes'>";
		echo "<legend>Unlocked icons</legend>";
		echo "<table>";
		if(!empty($availableIcons)){
			echo "<thead>";
			echo "<th></th>";
			echo "<th>Icon</th>";
			echo "<th>Name</th>";
			echo "<th>Description/Criteria</th>";
			echo "</thead>";
		
			foreach ($availableIcons as $icon){
				$current = "";
				if(isset($_POST['icon'])){
					if($icon['id'] == $_POST['icon']){
						$current = "checked";
					}
				}
				else{
					if($icon['img'] == $_SESSION['other']['chatIcon']){
						$current = "checked";
					}
				}
				
				echo "<tr>";
					echo "<td style='text-align:center'>";
						echo "<input type='radio' name='icon' value='" . $icon['id'] . "' " . $current . ">";
					echo "</td>";
					echo "<td style='text-align:center'>";
						echo "<img src='frontend/design/images/chatIcons/" . $icon['img'] ."'>";
					echo "</td>";
					echo "<td>";
						echo $icon['name'];
					echo "</td>";
					echo "<td>";
						echo $icon['description'];
					echo "</td>";
				echo "</tr>";
			}
		}
		else{
			echo "<tr><td style='text-align:center'>You haven't unlocked any player icons</td></tr>";
		}
		echo "</table>";
		echo "</fieldset>";
		if(!empty($availableIcons)){
			echo "<input type='submit' value='Set Player Icon' style='margin-top:20px'>";
		}
		echo "</form>";
		echo "</div>";
		
		echo "<div class='iconTables'>";
		echo "<fieldset class='iconBoxes'>";
		echo "<legend>Not yet unlocked icons</legend>";
		echo "<table>";
		echo "<thead>";
		echo "<th>Icon</th>";
		echo "<th>Name</th>";
		echo "<th>Description/Criteria</th>";
		echo "</thead>";
		foreach ($lockedIcons as $icon){
			echo "<tr>";
				echo "<td style='text-align:center'>";
					echo "<img src='frontend/design/images/chatIcons/" . $icon['img'] ."'>";
				echo "</td>";
				echo "<td>";
					echo $icon['name'];
				echo "</td>";
				echo "<td>";
					echo $icon['description'];
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</fieldset>";
		echo "</div>";
		
		
	}
}

function setPlayerIcon($iconId){
	global $conn;
	
	$name = $_SESSION['loggedIn'];
	$sql = "SELECT id,character_id,chatIcons FROM users WHERE username='$name'";
	$result = mysqli_query($conn,$sql);
	
	if (mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		$userId = $row['id'];
		$charId = $row['character_id'];
		$list = explode(",", $row['chatIcons']);
		
		if(in_array($iconId, $list)){
			$sql = "SELECT * FROM chaticons WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $iconId);
			if(mysqli_stmt_execute($stmt)){
				$result = $stmt->get_result();
				$row = mysqli_fetch_assoc($result);
				$img = $row['img'];
				$sql = "UPDATE users SET chatIcon='$img' WHERE id='$userId'";
				mysqli_query($conn,$sql);
				$sql = "UPDATE characters SET chatIcon='$img' WHERE id='$charId'";
				mysqli_query($conn,$sql);
				
				fullRefresh($charId);
				$_SESSION['other']['chatIcon'] = $img;
				//TO REFRESH CHARACTERINFO
		        echo"<script>
		            window.onload = updateChar();
		        </script>";
			}
			else{
				echo "<span style='color:red;'>Something failed</span>";
			}
		}
		else{
			echo "<span style='color:red;'>You haven't unlocked this icon</span>";
		}
	}
	else{
		echo "<span style='color:red;'>Error fetching your available icons</span>";
	}
}

function getPlayerIcons($id){
	global $conn;
	
	$sql = "SELECT chatIcons FROM users WHERE id='$id'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if (isset($row['chatIcons'])){
		$availableIcons = $row['chatIcons'];
		$sql = "SELECT * FROM chaticons WHERE id IN($availableIcons)";
		$result = mysqli_query($conn,$sql);
		echo "<div style='width:100%;' class='playerIconTable'>";
		echo "<fieldset class='iconBoxes'>";
		echo "<legend>Unlocked icons</legend>";
		echo "<table>";
		echo "<thead>";
		echo "<th>Icon</th>";
		echo "<th>Name</th>";
		echo "<th>Description/Criteria</th>";
		echo "</thead>";
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr>";
					echo "<td style='text-align:center'>";
						echo "<img src='frontend/design/images/chatIcons/" . $row['img'] ."'>";
					echo "</td>";
					echo "<td>";
						echo $row['name'];
					echo "</td>";
					echo "<td>";
						echo $row['description'];
					echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "</fieldset>";
		echo "</div>";
	}
}




?>