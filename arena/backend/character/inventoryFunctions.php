<?php
global $conn;
require_once(__ROOT__."/backend/other/itemFunctions.php");
require_once(__ROOT__."/backend/crafting/craftingFunctions.php");

function GetStrengthFromArmour($eqid){
	global $conn;
	$sql = "SELECT * FROM equipment WHERE eid='$eqid'";
	$res = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($res);

	$extraStr = 0;

	$armours = array($row['head'],$row['chest'],$row['arm'],$row['leg']);
	foreach($armours as $armour){
		$id = explode(":",$armour)[0];
		$sql2 = "SELECT * FROM armours WHERE id='$id'";
		$res2 = mysqli_query($conn,$sql2);
		$row2 = mysqli_fetch_assoc($res2);
		$extraStr += $row2['strength'];
	}
	return $extraStr;
}

function getInventory($type,$charId){

	global $conn;
	if($type == "melee"){
		$sql = "SELECT c.inventory_id, i.weapons FROM characters c LEFT JOIN inventory i on i.iid=c.inventory_id WHERE c.id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$ex = explode(",",$row['weapons']);
		foreach($ex as $e){
			if($e != ""){
				$info = getItemWithoutName($e,1);
				echo "<div class='inventoryItem1 itemRow' id='" . $e . "'>" . $info . "</div>";
			}
		}
	}
	elseif($type == "ranged"){
		$sql = "SELECT c.inventory_id, i.secondarys FROM characters c LEFT JOIN inventory i on i.iid=c.inventory_id WHERE c.id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$ex = explode(",",$row['secondarys']);
		foreach($ex as $e){
			if($e != ""){
				$info = getItemWithoutName($e,1);
				echo "<div class='inventoryItem1 itemRow' id='" . $e . "'>" . $info . "</div>";
			}
		}
	}
	elseif($type == "armours"){
		$sql = "SELECT c.inventory_id, i.heads,i.chests,i.arms,i.legs,i.feets FROM characters c LEFT JOIN inventory i on i.iid=c.inventory_id WHERE c.id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$armours = $row['heads'] . "," . $row['chests'] . "," . $row['arms'] . "," . $row['legs'] . "," . $row['feets'];
		$ex = explode(",",$armours);
		foreach($ex as $e){
			if($e != ""){
				$info = getItemWithoutName($e,2);
				echo "<div class='inventoryItem2 itemRow' id='" . $e . "'>" . $info . "</div>";
			}
		}
	}
	elseif($type == "trinkets"){
		$sql = "SELECT c.inventory_id, i.trinkets FROM characters c LEFT JOIN inventory i on i.iid=c.inventory_id WHERE c.id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$ex = explode(",",$row['trinkets']);
		foreach($ex as $e){
			if($e != ""){
				$info = getItemWithoutName($e,2);
				echo "<div class='inventoryItem2 itemRow' id='" . $e . "'>" . $info . "</div>";
			}
		}
	}
	elseif($type == "parts"){
		$sql = "SELECT c.crafting_id, i.* FROM characters c LEFT JOIN craftinginventory i on i.id=c.crafting_id WHERE c.id='$charId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$ex = explode(",",$row['base']);
		if(count($ex) > 1 || $ex[0] != ""){
			echo "<div class='inventoryDivider'>Base Parts</div>";
			foreach($ex as $e){
				if($e != ""){
					$ex2 = explode(":",$e);
					$info = getPartName($ex2[0]);
					echo "<div class='inventoryPart itemRow' id='" . $ex2[0] . "'>" . $info . " x " . $ex2[1] . "</div>";
				}
			}
		}
		$ex = explode(",",$row['main']);
		if(count($ex) > 1 || $ex[0] != ""){
			echo "<div class='inventoryDivider'>Main Parts</div>";
			foreach($ex as $e){
				if($e != ""){
					$ex2 = explode(":",$e);
					$info = getPartName($ex2[0]);
					echo "<div class='inventoryPart itemRow' id='" . $ex2[0] . "'>" . $info . " x " . $ex2[1] . "</div>";
				}
			}
		}
		$ex = explode(",",$row['extra']);
		if(count($ex) > 1 || $ex[0] != ""){
			echo "<div class='inventoryDivider'>Extra Parts</div>";
			foreach($ex as $e){
				if($e != ""){
					$ex2 = explode(":",$e);
					$info = getPartName($ex2[0]);
					echo "<div class='inventoryPart itemRow' id='" . $ex2[0] . "'>" . $info . " x " . $ex2[1] . "</div>";
				}
			}
		}
	}
}


?>