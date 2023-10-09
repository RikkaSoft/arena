<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
	//ASSIGN VARIABLES FROM FORM
	$username = $_POST['username'];
	$password = $_POST['password'];
	$hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 12));
	$email = $_POST['email'];

	//CHECK IF USER IS UNIQUE
	if(preg_match("/^[a-zA-Z0-9]+$/", $username) == 0){
		$_SESSION['registerFail'] = "Your character name contained characters not allowed. <br>Allowed characters are a-z, A-Z, 0-9";
		header('Location: index.php?page=register'); 
		exit;
	}
	
	$sql = "SELECT username FROM users WHERE username = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$rowcount = mysqli_num_rows($result);
	
	if ($rowcount >= 1){
		$_SESSION['registerFail'] = "Username " . $username . " already in use";
		header('Location: index.php?page=register'); 
	}
	else {
		$sql = "SELECT email FROM users WHERE email = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $email);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$rowcount = mysqli_num_rows($result);
		
		if ($rowcount >= 1){
		$_SESSION['registerFail'] = "Email " . $email . " already in use";
		header('Location: index.php?page=register'); 
		}
		else{
			//INSERT DATA INTO DATABASE
			$registeredDate = date("Ymd");
			$sql = "INSERT INTO users (username, password, email,registeredDate)
			VALUES (?,?,?,?)";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "ssss", $username,$hash,$email,$registeredDate);
			
	
			if(mysqli_stmt_execute($stmt) === TRUE){
			$_SESSION['registerSuccess'] = $username;
			header('Location: index.php?page=login');
			}
			else{
				echo "Something went wrong" . $sql . "<br>" . $conn->error;
			}
		}
			
	
				
			
	}
 ?>