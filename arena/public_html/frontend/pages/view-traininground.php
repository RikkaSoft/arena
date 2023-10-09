<?php
	global $conn;
	$id = $_GET['id'];
	$sql = "SELECT * FROM trainingrounds WHERE id=?";
	$stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = $stmt->get_result();
    $row = mysqli_fetch_assoc($result);

    $rounds = explode(",",$row['rounds']);
    foreach($rounds as $round){
    	$r = explode(":",$round);
    	echo "<a href='index.php?page=view-battlereport&battleId=" . $r[1] . "'>" . $r[1] . "</a><br>";
    }
?>