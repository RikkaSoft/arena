<?php 

function getBattleReport($id){
	global $conn;
	$sql = "SELECT * FROM battlereports WHERE id =?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	$report = str_replace("'", "\'", $row['report']);
	echo $report;
	
}
	
	if(isset($_GET['battleId'])){
		getBattleReport($_GET['battleId']);
	}
?>
	