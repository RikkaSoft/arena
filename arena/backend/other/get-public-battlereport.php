<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;

	$id = $_GET['battleId'];
    


    $table = "battlereports";
	$season = "";
	
	if($id != ""){
		
		$sql = "SELECT * FROM $table WHERE id =?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			echo $row['report'];

			echo "<br><br>";
			echo "Link to this Battlereport: <a href=http://arena.rikka.se/index.php?page=view-battlereport&battleId=" . $id . $season . ">http://arena.rikka.se/index.php?page=view-battlereport&battleId=" . $id . $season . "</a>";
		}
		else{
			echo "Invalid battleId";
		}
	}
	else{
		echo "Invalid battleId";
	}

?>
	