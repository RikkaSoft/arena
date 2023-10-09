<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;
	//ASSIGN VARIABLES FROM FORM
	$username = $_POST['username'];
	$password = $_POST['password'];

	//CHECK IF USER IS UNIQUE
	$sql = "SELECT * FROM users WHERE username = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$rowcount = mysqli_num_rows($result);
	if ($rowcount >= 1){
		$row = mysqli_fetch_assoc($result);
		$hash = $row['password'];
		if (password_verify($password, $hash)) {
		    $username = strtolower($username);
			$_SESSION['loggedIn'] = $username;
			$_SESSION['loggedInId'] = $row['id'];
			$_SESSION['other']['tournamentAdmin'] = $row['tournamentAdmin'];
			$_SESSION['other']['chatToggle'] = $row['chatToggle'];
            $chatIcon = $row['chatIcon'];
            $_SESSION['other']['chatIcon'] = $chatIcon;
			if ($row['character_id'] !== 0){
			    $char_id = $row['character_id'];
			    $sql = "UPDATE characters SET chatIcon='$chatIcon' WHERE id='$char_id'";
                mysqli_query($conn,$sql);
				$_SESSION['charId'] = $row['character_id'];
			}
            
            $sql = "UPDATE users SET lastLoginDate=NOW() WHERE id='$row[id]'";
			mysqli_query($conn,$sql);

		    $token = random_bytes(64);
		    $token = bin2hex($token);
		    $sql = "UPDATE users SET loginHash='$token' WHERE id='$row[id]'";
			mysqli_query($conn,$sql);
		    $cookie = $username . ':' . $token;
		    $mac = hash_hmac('sha256', $cookie, 'uhfwiehf7y138g13ybrgy0q78eg8gy8q2g');
		    $cookie .= ':' . $mac;
		    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
			setcookie('rememberme', $cookie, time()+60*60*24*14, '/', $domain, false);
		    #setcookie('rememberme', $cookie);
			
			header('Location: index.php?page=news'); 
		}
		else{
			header('Location: index.php?page=login&loginFail=wrongPass'); 
		}
	}
	
	else {
		header('Location: index.php?page=login&loginFail=noUser'); 
	}
	 
 ?>