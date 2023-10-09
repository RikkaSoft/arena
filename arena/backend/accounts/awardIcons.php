<?php
	
	function tenKills($userId){
		$iconId = "7";
		$iconName = "10kill.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	
	function thirtyKills($userId){
		$iconId = "8";
		$iconName = "30kill.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function eighteen($userId){
		$iconId = "16";
		$iconName = "18plus.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function gold($userId){
		$iconId = "17";
		$iconName = "rich.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	
	function beefcake($userId){
		$iconId = "18";
		$iconName = "beefcake.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function shieldMaster($userId){
		$iconId = "20";
		$iconName = "shieldMaster.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function parryMaster($userId){
		$iconId = "21";
		$iconName = "parryMaster.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function foulMaster($userId){
		$iconId = "22";
		$iconName = "foulMaster.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function critMaster($userId){
		$iconId = "23";
		$iconName = "critMaster.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function dodgeMaster($userId){
		$iconId = "24";
		$iconName = "dodgeMaster.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function counterer($userId){
		$iconId = "26";
		$iconName = "counterer.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function championOfFife($userId){
		$iconId = "27";
		$iconName = "gloryhammer.png";
		
		checkIfAlreadyAwarded($userId,$iconId,$iconName);
	}
	function addIcon($userId,$allChatIcons){
		global $conn;
		
		$sql = "UPDATE users SET chatIcons='$allChatIcons' WHERE id='$userId'";
		mysqli_query($conn,$sql);
	}
	
	function checkIfAlreadyAwarded($userId,$iconId,$iconName){
		global $conn;
		
		$sql = "SELECT * FROM users WHERE id='$userId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		if($row['chatIcons'] == ""){
			$allChatIcons = array();
		}
		else{
			$allChatIcons = explode(",",$row['chatIcons']);
		}
		
		if(!in_array($iconId, $allChatIcons)){
			$allChatIcons[] = $iconId;
			$allChatIcons = implode(",", $allChatIcons);
			addIcon($userId,$allChatIcons);
			if(!isset($row['chatIcon'])){
				setChatIconIfFirst($userId,$iconName);
			}
		}
	}
	
	function setChatIconIfFirst($userId,$iconName){
		global $conn;
		
		$sql = "SELECT character_id FROM users WHERE id='$userId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$charId = $row['character_id'];
		
		$sql = "UPDATE users SET chatIcon='$iconName' WHERE id='$userId'";
		mysqli_query($conn,$sql);
		$sql = "UPDATE characters SET chatIcon='$iconName' WHERE id='$charId'";
		mysqli_query($conn,$sql);
	}

?>