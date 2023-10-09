<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
	//ASSIGN VARIABLES FROM FORM
	$email = $_POST['email'];
	$passwordResetId = $_POST['passwordResetId'];
	$password = $_POST['password'];
	$hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 12));
	
	if (strlen($passwordResetId) !== 32){
		echo "Wrong reset ID, try to reset your password again";
	}
	else{
		$sql = "UPDATE users SET password=?, passwordId=0 WHERE email=? AND passwordId=? ";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "sss", $hash,$email,$passwordResetId);
		
	
		if(mysqli_stmt_execute($stmt) === TRUE){
			$_SESSION['passwordChange'] = "Password changed";
			header('Location: index.php?page=login');
		}
		else{
			echo "Something went wrong" . $sql . "<br>" . $conn->error;
		}
	}
?>
