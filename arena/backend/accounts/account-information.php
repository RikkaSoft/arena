<?php 
	require_once(__ROOT__."/system/details.php");
	global $conn;

	$username = $_SESSION['loggedIn'];
	$sql = "SELECT id, email, name, age FROM users WHERE username='$username'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);

	$id = 			$row['id'];
	$email = 		$row['email'];
	$name = 		$row['name'];
	$age = 			$row['age'];
	
	echo "Member-ID: " . $id . "</br>" . 
	#"Name: " . $name . "</br>" . 
	#"Age: " . $age . "</br>" .
	"Username: " . $username . "</br>" . 
	"Email-Address: " . $email . "</br>";
	
	


?>
