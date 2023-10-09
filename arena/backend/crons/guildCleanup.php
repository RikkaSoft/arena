<?php
	global $conn;
	include("/var/www/html/arena/system/details.php");
	
	$sql = "SELECT id,guild FROM characters";
	$result = mysqli_query($conn,$sql);
	$playerArray = array();
	while($row = mysqli_fetch_assoc($result)){
		array_push($playerArray,$row['id'] . "-" . $row['guild']);
	}
		
	$sql = "SELECT * FROM guilds";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)){
		$guildId = $row['id'];
		$newMembers = array();
		$touched = 0;
		if($row['players'] != ""){
			$gMembers = explode(",",$row['players']);
			for($i=0;$i < count($gMembers);$i++){
				if (!in_array($gMembers[$i] . "-" . $guildId,$playerArray)){
					$touched = 1;
				}
				else{
					array_push($newMembers,$gMembers[$i]);
				}
			}
			var_dump($newMembers);
			if($touched == 1){
				$newCount = count($newMembers);
				$newMembersArray = implode(",",$newMembers);
				
				$sql = "UPDATE guilds SET playerCount='$newCount',players='$newMembersArray' WHERE id='$guildId'";
				mysqli_query($conn,$sql);
			}
		}
		else{
			
			if($row['constant'] == 0){
				$sql = "DELETE FROM guilds WHERE id='$guildId'";
				echo $sql;
				mysqli_query($conn,$sql);
			}
		}
	}
	
	



?>