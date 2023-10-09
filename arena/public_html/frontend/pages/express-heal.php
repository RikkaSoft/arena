<?php
if($_SESSION['characterProperties']['healedDate'] != 0){
	global $conn;
	echo "<h2>Express Healing</h2>";
	$start = strtotime(date('Y-m-d H:i'));
	$stop = strtotime($_SESSION['characterProperties']['healedDate']);
	$diff = ($stop - $start);
	if($diff > 60){
		$diff = $diff/60;
		$cost = $diff;
	}
	else{
		$cost = 1;
	}

	if($_SESSION['characterProperties']['gold'] >= $cost){
		$char_id = $_SESSION['characterProperties']['id'];
		$sql = "UPDATE characters SET gold=gold-$cost,deadNext=0,healedDate=0,hp=vitality WHERE id='$char_id'";
		mysqli_query($conn,$sql);
		
		echo "You have been healed for " . $cost . " gold";
		//TO REFRESH CHARACTERINFO
		echo
		"<script>
			window.onload = updateChar();
		</script>";
	}
	else{
		echo "You do not have enough gold for express healing";
	}
}
else{
	echo "You are not in need of express healing";
}
?>