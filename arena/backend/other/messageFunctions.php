<?php

function writeEventMessage($charId,$eventType,$title,$message){
	global $conn;
	
	$sql = "INSERT INTO eventmessages (charId,eventType,title,message) VALUES ($charId,$eventType,$title,$message)";
	if(mysqli_query($conn,$sql)){
		return true;
	}
	else{
		return false;
	}
}

function getMessages($charId,$eventType,$start,$amount = 20){
	global $conn;
	if($start == 0){
		$sql = "SELECT id,title,timestamp FROM eventmessages WHERE charId='$charId' AND eventType='$eventType' ORDER BY ID DESC LIMIT $amount";
	}
	else{
		$sql = "SELECT id,title,timestamp FROM eventmessages WHERE charId='$charId' AND eventType='$eventType' AND id < '$start' ORDER BY ID DESC LIMIT $amount";
	}
	$result = mysqli_query($conn,$sql);
	if($mysqli_num_rows($result) > 0){
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
		}
		return json_encode($rows);
	}else{
		return false;
	}
}

function getMessage($id){
	global $conn;
	
	#get row with stmt
	
	return $row;
}


if(isset($_GET['loadMessages'])){
	$charId = $_SESSION['characterProperties']['id'];
	if(isset($_GET['messageAmount'])){
		getMessages($charId,$_POST['eventType'],$_POST['start'],$_GET['messageAmount']);
	}
	else{
		getMessages($charId,$_POST['eventType'],$_POST['start']);
	}
}

?>