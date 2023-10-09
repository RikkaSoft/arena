<?php
	global $conn;
	include("/var/www/html/arena/system/details.php");
	
	$sql = "SELECT * FROM characters WHERE adventureArea IS NULL AND inTraining=0";
	$result = mysqli_query($conn,$sql);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$newHp = 0;
		$id = $row['id']; $hp = $row['hp']; 
		$vitality = $row['vitality'] + $row['vitalityFromGear'];
		if ($hp < $vitality){
		$newHp = ($hp + ($vitality * 0.4));
			if ($hp + ($vitality * 0.4) >= $vitality)
			{
				$newHp = $vitality;
			}
		
		$sql2 = "UPDATE characters SET hp=$newHp WHERE id='$id'";
		$result2 = mysqli_query($conn,$sql2);
		echo "$id " . "updated<br>" . $newHp . "/" . $vitality . "<br>";
		}
	}
	$sql = "UPDATE configuration SET lastHPUpdate = NOW()";
	mysqli_query($conn,$sql);
	echo "HI";
	session_destroy();
?>