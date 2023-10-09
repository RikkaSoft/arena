<?php
	set_time_limit(600);
	global $conn;
	include("/var/www/html/arena/system/config.php");
	#include("C:\wamp64\www\Griem\Arena\system\config.php");

	//SET FINALS (restrict access to fights, training etc.)
	$sql = "UPDATE configuration SET finals=1,infoBarPrio='The season finals are live'";
	mysqli_query($conn,$sql);

	//GET TOP 32
	$sql = "SELECT id FROM characters ORDER BY experience DESC limit 32";
	$result= mysqli_query($conn,$sql);

	$ids = array();
	while($row = mysqli_fetch_assoc($result)){
		$ids[] = $row['id'];
	}
	$top32 = implode(",",$ids);

	//GET CURRENT SEASON
	$sql = "SELECT season FROM configuration";
	$result= mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$season = $row['season'];

	//DELETE ALL PREVIOUS TOURNAMENTS
	$sql = "DELETE FROM tournaments";
	mysqli_query($conn,$sql);

	//INSERT FINAL TOURNAMENT
	$sql = "INSERT INTO tournaments (id,automatic,size,players,name,prizeItem) VALUES(1,0,32,'" . $top32 . "','Season " . $season . " finals','The crown of the season (chat icon)')";
	mysqli_query($conn,$sql);
	$finalId = mysqli_insert_id($conn);

	//SET CHARACTERS SURRENDER

	$sql = "UPDATE characters SET battleReady=0,battleSurrender=0,deadNext=0,tournamentSurrender=0";
	mysqli_query($conn,$sql);

	//GET TOURNAMENT FUNCTIONS
	include("/var/www/html/arena/backend/tournament/tournament-admin.php");
	#include("C:/wamp64/www/Griem/Arena/backend/tournament/tournament-admin.php");
	startTournament($finalId);
	announce("The season " . $season . " final tournament has started!");

	
	for($i = 1; $i <= 5;$i++){
		playRound($finalId,$i);
		announce("Round " . $i . " has been played!");
		sleep(30);
	}

	$sql = "SELECT * FROM tournaments WHERE id='$finalId'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $winner = $row['round' . $i . 'Text'];

    announce("Congratulations to " . $winner . " for winning season " . $season);
    
    $sql = "SELECT id FROM characters WHERE name='$winner'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $cId = $row['id'];

    $sql = "SELECT id FROM users WHERE character_id=$cId";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $uId = $row['id'];


    $date = new DateTime();
	$date->modify('+1 day');

    $sql = "UPDATE configuration SET infoBarPrio='The next season will start ". $date->format('Y-m-d') . " 08:00 '";
	mysqli_query($conn,$sql);


	$sql = "INSERT INTO chaticons (unlockable,name,img,description) VALUES (0,'Season " . $season . " Winner','tbd.png','The crown awarded for winning the season')";
	mysqli_query($conn,$sql);
	$iconId = mysqli_insert_id($conn);

	include_once("/var/www/html/arena/backend/accounts/awardIcons.php");
	#include_once("C:/wamp64/www/Griem/Arena/backend/accounts/awardIcons.php");
	checkIfAlreadyAwarded($uId,$iconId,'tbd.png');

?>