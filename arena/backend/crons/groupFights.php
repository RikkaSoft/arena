<?php
set_time_limit(1800);
include("/var/www/html/arena/system/config.php");
#include("C:\wamp64\www\Griem\Arena\system\config.php");
global $conn;

$sql = "SELECT * FROM groupfights WHERE finished=0";
$result = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($result)){
	$id = $row['id'];
	$sql = "SELECT groupfightmembers.*,characters.name,characters.hp,characters.vitality,characters.battleGroupSurrender FROM groupfightmembers INNER JOIN characters ON characters.id = groupfightmembers.characterId WHERE groupfightmembers.groupId='$id' AND groupfightmembers.ready=1";
	$res = mysqli_query($conn,$sql);
	$count = mysqli_num_rows($res);
	if($count == $row['size']){
		$s = "UPDATE groupfights SET finished=1,updateIndex=updateIndex+1 WHERE id='$id'";
		mysqli_query($conn,$s);
		$t1 = array();
		$t2 = array();
		$fail = false;
		while($ro = mysqli_fetch_assoc($res)){
			if($ro['team'] == 1){
				$t1[] = $ro['name'];
			}
			else if($ro['team'] == 2){
				$t2[] = $ro['name'];
			}
			$surrHp = round($ro['vitality'] * $ro['battleGroupSurrender']);
			$currHp = $ro['hp'];
			if($currHp <= $surrHp){
				$fail = true;
				$mess = $ro['name'] . " has lower HP than surrender value";
				$sq = "INSERT INTO groupfightchat (groupId,message,characterName,time) VALUES('$id','$mess','Fight System',NOW())";
				echo $sq;
				mysqli_query($conn,$sq);
			}
		}
		if($fail){
			$mess = "The fight will try to start in one minute if everyone is ready";
			$sq = "INSERT INTO groupfightchat (groupId,message,characterName,time) VALUES('$id','$mess','Fight System',NOW())";
			mysqli_query($conn,$sq);
			$s = "UPDATE groupfights SET finished=0,updateIndex=updateIndex+1 WHERE id='$id'";
			mysqli_query($conn,$s);
		}
		else{
			require_once(__ROOT__."/backend/fighting/newFight.php");

			GroupFight($t1,$t2,$id);
		}
	}
	else{
		include_once("/var/www/html/arena/backend/fighting/groupFunctions.php");
		$sql = "SELECT groupfightmembers.*,characters.id,characters.name,characters.hp,characters.vitality,characters.battleGroupSurrender,characters.isOnline FROM groupfightmembers INNER JOIN characters ON characters.id = groupfightmembers.characterId WHERE groupfightmembers.groupId='$id' AND groupfightmembers.ready=0 AND characters.isOnline=0";
		$res = mysqli_query($conn,$sql);
		$count = mysqli_num_rows($res);
		if($count > 0){
			while($row = mysqli_fetch_assoc($res)){
				$charId = $row['id'];
				$sql2 = "DELETE FROM groupfightmembers WHERE characterId='$charId' AND groupId='$id'";
				mysqli_query($conn,$sql2);
				IncrementIndex($id);
			}
		}
	}
}


?>