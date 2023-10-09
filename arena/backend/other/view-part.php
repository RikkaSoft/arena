<?php


if(isset($_GET['partId'])){

	include_once(__ROOT__.'/backend/crafting/craftingFunctions.php');
	$part = getPart($_GET['partId']);
	if(isset($part['name'])){
		echo "<h4>" . $part['name'] . "</h4>";
		$i = 0;
		foreach ($part as $key => $value){
			if($i > 3){
				if($value != 0){
					echo "<div class='partInfoName listPart'>" . $key . "</div>";
					echo "<div class='partInfoStat listPart'>" . $value ."</div>";
				}
			}
			$i++;
		}
	}
}

?>