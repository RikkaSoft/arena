<?php
	require_once("../system/details.php");
	global $conn;
	
	
	$subject = 'Arena.rikka.se - Season 15 has started!';
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	
	// Additional headers
	$headers[] = 'From: Arena.rikka.se <arena@rikka.se>';
	$sql = "SELECT * FROM users WHERE mailinformation=1 LIMIT 30";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)){
	
	$to      = $row['email'];
	$message = 'Hello ' . $row['username'] . ',<br><br>
	It\'s been a while since I sent out an email, since the previous email an app of the game has been published on google play and we\'ve gotten quite a lot more players, season 14 had over 70 characters. Hopefully season 15 which starts today will follow suit.<br><br>
	<a href=https://play.google.com/store/apps/details?id=se.rikka.arena> You can get the app here: https://play.google.com/store/apps/details?id=se.rikka.arena </a>
	<br><br>
	These are the new things of the season:<br>
	<ul>
        <li>Dexterity modifier to get a double attack has been decreased from 0.6% to 0.3%
        <li>Dexterity ranged damage modifier has been decreased from 0.6% to 0.3%
        <br>
        <li>Weight penalty for parry has been reduced from 0.75% to 0.65%
        <li>Weight penalty for foul play has been reduced from 0.5% to 0.4%
        <br>
        <li>Health gain per level has been reduced from 10 to 5
        <br>
        <li>Total weight and double attack chance are now shown on your character page
        <br>
        <li>Tournaments now reward parts, higher level tournaments reward better parts (generally)
        <li>Tournaments will be of different sizes, 4 players for low level, 8 for medium and high and the one for everyone is for 16 players
        <li>Tournaments will remove players whom level doesn\'t match the requirement anymore (if you joined when you were the appropriate level and had since leveled up)
        <br>
        <li>The season duration has been halved (approximately). The seasons will start on the second day of the month and end on the 15th. The next one will start on the 16th and end on the first day of the next month
        <br>
        <li>Parts for sale in the marked has been reduced to one base, one handle, and three extras per 12 hours
        <br>
        <li>The legendary crossbow Avelyn has been added to the game
    </ul>
    <br><br>

	<a href=https://arena.rikka.se>https://arena.rikka.se</a><br><br>/Rikka

	';
	mail($to, $subject, $message, implode("\r\n", $headers));

	echo "DELETING " . $row['email'];
	$email = $row['email'];
	$sql2 = "DELETE FROM users WHERE email = '$email'";
	mysqli_query($conn,$sql2);
	}
?>