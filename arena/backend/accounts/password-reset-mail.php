<?php
	require_once(__ROOT__."/system/details.php");
	global $conn;
	
function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email=?";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = $stmt->get_result();

if( mysqli_num_rows($result) > 0) {

	$randomId = generateRandomString();
	$sql = "UPDATE users SET passwordId=? WHERE email=?  LIMIT 1";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "ss", $randomId,$email);
	mysqli_stmt_execute($stmt);
	
	$to      = $email;
	$subject = 'Arena.rikka.se | Password Reset';
	$message = "
	<html>
		<head>
		  <title>Arena.rikka.se | Password Reset</title>
		</head>
		<body>
				You've gotten this mail because you've requested a password reset.<br>
				If you didn't request this just ignore this email, otherwise follow this link and choose a new password.<br><br>
				
				<a href=\"http://arena.rikka.se/index.php?page=reset-password&email=" . $email . "&passwordResetId=" . $randomId . "\">
				http://arena.rikka.se/index.php?page=reset-password&email=" . $email . "&passwordResetId=" . $randomId . "</a>
			
			
		</body>
	</html>
	";
	
	
	$headers =  'MIME-Version: 1.0' . "\r\n" .
				'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
				'From: arena@rikka.se' . "\r\n" .
			    'Reply-To: arena@rikka.se' . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			   
	$message = wordwrap($message, 70, "\r\n");
	
	mail($to, $subject, $message, $headers);
	
	echo "A mail has been sent to " . $email . " Check you mail for the link to continue.";
	}
else{
	echo "Email not found";
}

?>
