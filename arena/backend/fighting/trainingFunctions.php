<?php

function listTrainingAreas(){
	global $conn;
	echo "<div style='padding:10px'>";
		echo "<div style='width:60%;float:left'><h3>Training</h3>Click on an area to expand it to see more information about it. You will see the most common enemies in that area, 
		there are enemies which can exceed the level restrictions on the level. Remember to set a surrender HP, some enemies attack your immidiately so you might not have a chance to change it.</div>";
		echo "<div style='width:40%;float:left;'>";
			surrenderDropdown();
		echo "</div>";
	echo "</div>";
	$jsList = array();
	$sql = "SELECT * FROM trainingareas WHERE listable=1 ORDER BY minLevel";
	$result = mysqli_query($conn,$sql);
	echo "<div style='clear:both'>";
		echo "<div id='monsterInfo'></div>";
		echo "<div id='itemInfo'></div>";
		while($row = mysqli_fetch_assoc($result)){
			$jsList[] = $row['id'];
			#GET AVAILABLE NPC IF LISTABLE==1
			$availableMobs = getNPC($row['minLevel'],$row['maxLevel'], $row['monsterType']);
			echo "<div class='trainingAreaBox' id='" . $row['id'] . "'>" . $row['name'] . " (" . $row['minLevel'] . " - " . $row['maxLevel'] . ")</div>
			<div class='infoBox' id='infoBox" . $row['id'] . "'>
				<div id='areaDesc' style='width:75%;padding:10px;float:left;'><fieldset class='infoBoxFieldset'><legend>Description</legend>" . $row['description'] . 
				"<br><br>If you head out into battle, you will be able to fight up to ten times, each victory without returning will give you an experience and gold bonus!</fieldset></div>
				<div id='areaMobs' style='width:25%;padding:10px;float:left;'><fieldset class='infoBoxFieldset'><legend>Enemies</legend>";
				echo "<div style='width:100%;border-bottom:1px solid;'>Common</div>";
				foreach($availableMobs as $mob){
					if($mob['chance'] > 10){
						echo "<span class='enemy commonEnemy' id='" . $mob['name'] . "'>" . $mob['name'] . "</span><br>";
					}
				}
				echo "<br>";
				echo "<div style='width:100%;border-bottom:1px solid;color:#d07200;'>Rare</div>";
				foreach($availableMobs as $mob){
					if($mob['chance'] <= 10){
						echo "<span class='enemy rareEnemy' id='" . $mob['name'] . "'>" . $mob['name'] . "</span><br>";
					}
				}
				echo "</fieldset></div>
				<a href='index.php?page=training&goTrain=" . $row['id'] . "'><button style='width:50%;margin-bottom:10px;margin-left:12.5%;'>Head into battle!</button></a>";
			echo "</div>";
		}
	echo "</div>";
	$jsList = implode(",",$jsList);
	?>
	<script>
		var loopThis = Array(<?php echo $jsList;?>);
		
		for(var i = 0; i < loopThis.length;i++){
			var descId = $('#infoBox'+loopThis[i]).children("#areaDesc");
			var mobsId = $('#infoBox'+loopThis[i]).children("#areaMobs");
			$('#infoBox'+loopThis[i]).show();
			var descHeight = descId.height();
			var mobsHeight = mobsId.height();
			
			if(mobsHeight > descHeight){
				descId.height(mobsHeight);
			}
			else{
				mobsId.height(descHeight);
			}
			$('#infoBox'+loopThis[i]).hide();
		}
		
		$('.trainingAreaBox').click(function(){
			var id = $(this).attr('id');
			$('#infoBox'+id).slideToggle();
		});
	</script>
	<?php
	include("frontend/design/js/npcinfo.html");
}

function getNPC($min,$max, $monsterType, $hideRare = 1){
	global $conn;
	if($hideRare == 1){
		$sql = "SELECT id,name,chance,attackChance FROM npc WHERE level >= $min && level <= $max AND race IN ('$monsterType') AND listable=1";
	}
	else{
		$sql = "SELECT id,name,chance,attackChance FROM npc WHERE level >= $min && level <= $max AND race IN ('$monsterType')";
	}
	$result = mysqli_query($conn,$sql);
	$return = array();
	while($row = mysqli_fetch_assoc($result)){
		$return[] = array("id" => $row['id'], "name" => $row['name'],"chance"=>$row['chance'],"attackChance"=>$row['attackChance']);
	}
	return $return;
}

function startTraining($area){
	global $conn;
	if($_SESSION['characterProperties']['inTraining'] == 0){
		$charId = $_SESSION['characterProperties']['id'];
		$charName = $_SESSION['characterProperties']['name'];
		$username = $_SESSION['loggedIn'];
		
		$sql = "SELECT id FROM trainingareas WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
	    mysqli_stmt_bind_param($stmt, "i", $area);
	    mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) > 0){
			$sql = "SELECT id FROM trainingrounds WHERE character_id='$charId' AND finished=0";
			$result = mysqli_query($conn,$sql);
			
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_assoc($result);
				$inactiveId = $row['id'];
				$sql = "UPDATE trainingrounds SET finished=1,finishTime=NOW() WHERE id='$inactiveId'";
				mysqli_query($conn,$sql);
				echo "Something went wrong, try again";
			}
			else{		
				$sql = "UPDATE characters SET inTraining=? WHERE id=?";
				$stmt = mysqli_prepare($conn,$sql);
			    mysqli_stmt_bind_param($stmt, "ii", $area,$charId);
			    mysqli_stmt_execute($stmt);
				
				$sql = "INSERT INTO trainingrounds (character_id,characterName,username,area,startTime) VALUES (?,?,?,?,NOW())";
				$stmt = mysqli_prepare($conn,$sql);
			    mysqli_stmt_bind_param($stmt, "issi", $charId,$charName,$username,$area);
			    mysqli_stmt_execute($stmt);
				
				//find match
				require_once(__ROOT__."/backend/character/update-characterSessions.php");
				$_SESSION['characterProperties']['inTraining'] = $area;
				#training();
			}
		}
		else{
			echo "Error 161 - Non existent area code";
		}
	}
	else{
		#echo "Your character is already training";
	}
}

function updateChoice(){
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	$sql = "UPDATE characters SET trainingCreature='choice' WHERE id='$charId'";
	$_SESSION['characterProperties']['trainingCreature'] = "choice";
	mysqli_query($conn,$sql);
}

function updateStats($outcome, $skip = 0,$attackText = "",$getCreature = ""){
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	if($skip == 0){
		$win = $outcome['outcome'];
		if($win == "win"){
			$sql = "SELECT * FROM trainingrounds WHERE character_id='$charId' AND finished=0";
			$result = mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_assoc($result);
				if(isset($row['rounds'])){
					$roundArray = explode(",",$row['rounds']);
					$roundArray[] = "1:" . $outcome['battleId'];
					$insert = implode(",",$roundArray);
				}
				else{
					$insert = "1:" . $outcome['battleId'];
				}
				$extraXp = $outcome['xpReward'];
				$extraGold = $outcome['goldReward'];
				$sql = "UPDATE trainingrounds SET rounds='$insert',winCount=winCount+1,roundCount=roundCount+1,xpReward=xpReward+'$extraXp',goldReward=goldReward+'$extraGold' WHERE character_id='$charId' AND finished=0";
				mysqli_query($conn,$sql);
				$sql = "UPDATE characters SET trainingCreature=0 WHERE id='$charId'";
				mysqli_query($conn,$sql);
				
				$notShow = statusBar();
				if($notShow == 0){
					echo "<h4 style='text-align:center;margin-top:30px;margin-bottom:30px;'>You won and can continue your training or choose to go back to town</h4>";
					updateChoice();
					continueChoiceButtons();
				}
			}	
		}
		else{
			//You lost, training over
			$sql = "SELECT * FROM trainingrounds WHERE character_id='$charId' AND finished=0";
			$result = mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_assoc($result);
				if(isset($row['rounds'])){
					$roundArray = explode(",",$row['rounds']);
					$roundArray[] = "end:" . $outcome['battleId'];
					$insert = implode(",", $roundArray);
				}
				else{
					$insert = "end:" . $outcome['battleId'];
				}
				
				
				$sql = "UPDATE trainingrounds SET rounds='$insert',roundCount=roundCount+1 WHERE character_id='$charId' AND finished=0";
				mysqli_query($conn,$sql);
				statusBar(1);
				
			}	
		}
		if(isset($outcome)){
			echo "<div style='padding:10px;margin-top:20px;'>";
				echo $attackText;
				if($getCreature != ""){
					getCreature($getCreature,2);
				}
				echo $outcome['battleReport'];
			echo "</div>";
		}
	}
	else{
		//Skipped round
		$sql = "SELECT * FROM trainingrounds WHERE character_id='$charId' AND finished=0";
		$result = mysqli_query($conn,$sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			if(isset($row['rounds'])){
				$roundArray = explode(",",$row['rounds']);
				$roundArray[] = "0:0";
				$insert = implode(",", $roundArray);
			}
			else{
				$insert = "0:0";
			}
			
			$sql = "UPDATE trainingrounds SET rounds='$insert',roundCount=roundCount+1 WHERE character_id='$charId' AND finished=0";
			mysqli_query($conn,$sql);
			$sql = "UPDATE characters SET trainingCreature=0 WHERE id='$charId'";
			mysqli_query($conn,$sql);
			$_SESSION['characterProperties']['trainingCreature'] = 0;
			
		}	
	}
}

function training(){
	global $conn;	
	include(__ROOT__."/backend/adventure/get-creature-adventure.php");
	
	if($_SESSION['characterProperties']['inTraining'] == 0){
		listTrainingAreas();
	}
	else{
		
		
		#var_dump($_SESSION['characterProperties']['trainingCreature']);
		if ($_SESSION['characterProperties']['trainingCreature'] == '0'){
			#statusBar();
			$area = $_SESSION['characterProperties']['inTraining'];
			$sql = "SELECT * FROM trainingareas WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
		    mysqli_stmt_bind_param($stmt, "s", $area);
		    mysqli_stmt_execute($stmt);
		    $result = $stmt->get_result();
		    $row = mysqli_fetch_assoc($result);
			
			#randomize enemy
			$allAvailableMobs = getNPC($row['minLevel'], $row['maxLevel'], $row['monsterType'], 0);
			
			$maxRoll = 0;
			foreach($allAvailableMobs as $mob){
				$maxRoll += $mob['chance'];
			}
			
			$roll = mt_rand(1,$maxRoll);
			
			$currChance = 1;
			foreach($allAvailableMobs as $mob){
				$currChance += $mob['chance'];
				if($roll <= $currChance){
					$enemy = $mob;
					break;
				}
			}
			#echo "ENEMY: " . var_dump($enemy) . "<br><br>"; 
			if($mob['attackChance'] > 0){
				if(mt_rand(0,100) <= $mob['attackChance']){
					$attackText = "<h4 style='color:red;text-align:center;'>" . $mob['name'] . " sees you and immediately attacks!</h4>";
					$mobId = $mob['id'];
					
					$charId = $_SESSION['characterProperties']['id'];
					$_SESSION['characterProperties']['trainingCreature'] = $mobId;
					$sql = "UPDATE characters SET trainingCreature='$mobId' WHERE id='$charId'";
					mysqli_query($conn,$sql);
					
					$outcome = fightMonster();
					afterFight($outcome,$attackText,$mob['id']);
					
					
				}
				else{
					statusBar();
					$mobId = $mob['id'];
					$charId = $_SESSION['characterProperties']['id'];
					$_SESSION['characterProperties']['trainingCreature'] = $mobId;
					$sql = "UPDATE characters SET trainingCreature='$mobId' WHERE id='$charId'";
					mysqli_query($conn,$sql);
					
					
					getCreature($mob['id'],0);
					
					fightButtons();
				}
			}
			else{
				statusBar();
				$mobId = $mob['id'];
				$charId = $_SESSION['characterProperties']['id'];
				$_SESSION['characterProperties']['trainingCreature'] = $mobId;
				$sql = "UPDATE characters SET trainingCreature='$mobId' WHERE id='$charId'";
				mysqli_query($conn,$sql);
				
				
				getCreature($mob['id'],0);
				
				fightButtons();
			}
		}
		elseif($_SESSION['characterProperties']['trainingCreature'] == "choice"){
			//Haven't made the decision to continue or not
			statusBar();
			continueChoiceButtons();
		}
		else{
			//There is a creature you haven't chosen if you want to attack or not
			statusBar();
			getCreature($_SESSION['characterProperties']['trainingCreature'],0);
			fightButtons();
		}
	}
}


function continueChoiceButtons(){
	echo "<a href='index.php?page=training&nextRound'><button class='trainingButtons'>Continue training (Next round)</button></a>";
	echo "<a href='index.php?page=training&finished'><button style='margin-bottom:50px;' class='trainingButtons'>Finish training</button></a>";
}

function endButtons($area){
	echo "<a href='index.php?page=training&goTrain=" . $area . "'><button class='trainingButtons'>Train in the same area again!</button>";
	echo "<a href='index.php?page=training'><button style='margin-bottom:50px;' class='trainingButtons'>Choose a different area</button></a>";
}

function surrenderDropdown(){
	$vitality = $_SESSION['characterProperties']['vitality'];
	$lastSurrender = $_SESSION['characterProperties']['trainingSurrender'];
	if($lastSurrender == '0'){
		$lastSurrender = 0.05;
	}
	$i = 0.5;
	echo "<div style='text-align:center;margin-top:20px;margin-bottom:20px;'>";
	echo "<strong>When do you wish to surrender?</strong> <select id='surrenderValue'><br>";
	$found = 0;
	do {
		$hp = round($vitality * $i);
		if($hp < $_SESSION['characterProperties']['hp']){
			#echo "<option>" . $i . " - " . $lastSurrender . "</option>";
			if (abs(($i-$lastSurrender)/$lastSurrender) < 0.06){
				if(!isset($surrValue)){
					$surrValue = $i;
				}
				$si = $i*100;
				echo "<option value=$i selected>$si% ($hp hp)</option>";
				$found = 1;
			}
			else{
				if(!isset($surrValue)){
					$surrValue = $i;
				}
				$si = $i*100;
				echo "<option value=$i>$si% ($hp hp)</option>";
			}
		}
		$i = $i-0.1;
	} while ($i >= 0.1);
	echo "<option value=0>0%</option>";
	echo "</select>";
	if($found == 0){
		if(isset($surrValue)){
			setSurrender((string)$surrValue);
		}
		else{
			setSurrender(0);
		}
	}
	echo "</div>";
	?><script>
		$('#surrenderValue').change(function(){
			changeSurrender(this);
		});
		
		function changeSurrender(element){
			var value = $('option:selected',element).val();
			$('#fightButton').attr("disabled", true);
			$.post('index.php?fpage=trainingFunctions&setSurr&nonUI',{
				surrValue:value
			}, function(){
				$('#fightButton').attr("disabled", false);
			});
		}
	</script>
	<?php
}
function fightButtons(){
	surrenderDropdown();
	echo "<a href='index.php?page=training&fight'><button class='trainingButtons' id='fightButton'>Fight!</button></a>";
	echo "<a href='index.php?page=training&skipRound'><button class='trainingButtons'>Skip and continue training (pass round)</button></a>";
	echo "<a href='index.php?page=training&finished'><button style='margin-bottom:50px;' class='trainingButtons'>Finish training</button></a>";
	
	
}
function fightMonster(){
	
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	$sql = "SELECT name,trainingCreature FROM characters WHERE id='$charId'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	if($row['trainingCreature'] != 0){	
		$player = $row['name'];
		$monsterId = $row['trainingCreature'];
		$sql = "SELECT * FROM npc WHERE id='$monsterId'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		$monster = $row['name'];
		require_once(__ROOT__."/backend/fighting/newFight.php");
		$resultsAndReport = fight($player,$monster,0,0,0,1,0);
		#var_dump($resultsAndReport);
		return $resultsAndReport;
	   	#$outcome = fight($_SESSION['loggedIn'],$_SESSION['characterProperties']['adventureMonster'],0,0,0,1,1,$_SESSION['characterProperties']['adventureSurrender'],1);
	   	
   	}
	else{
		training();
	}
}
function statusBar($end = 0){
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	$sql = "SELECT * FROM trainingrounds WHERE character_id='$charId' AND finished=0";
	$result = mysqli_query($conn,$sql);
	$i = 0;
	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		$statusBar = "<div id='statusBar'>";
		$i = 1;
		$wins = 0;
		if($row['rounds'] != null || $end == 1){
			$roundsArray = explode(",",$row['rounds']);
			foreach($roundsArray as $round){
				$split = explode(":",$round);
				if($split[0] == '1'){
					$statusBar .= "<a href='index.php?page=view-battlereport&battleId=" . $split[1] . "'<span id='statusTile' class='winTile'><img src='frontend/design/images/other/fightIcon.png'></span></a>";
					$wins++;
					$i++;
				}
				elseif($split[0] == '0'){
					$statusBar .= "<span id='statusTile' class='winTile'><img src='frontend/design/images/other/runner.png'></span>";
					$i++;
				}
			}
			$notPerfect = 1;
			if(count($roundsArray) >= 10){
				$end = 1;
				$notPerfect = 0;
			}
			$extraRewards = "";
			if($end == 1){
				if($notPerfect == 1){
					$statusBar .= "<span id='statusTile' style='color:red;'>X</span>";
				}
				$i++;
				$extraXp = 0;
				$extraGold = 0;
				if($row['xpReward'] != 0 && $row['goldReward'] != 0){
					$extraXp = round($row['xpReward'] * ($wins/10));
					$extraGold = round($row['goldReward'] * ($wins/10));
					if($extraXp == 0){
						$extraXp = 1;
					}
					if($extraGold == 0){
						$extraGold = 1;
					}
					
				}
				else{
					$extraRewards = "<h4 style='text-align:center'>You got no training chain bonus =(</h4>";
				}		
				$extraRewards = "<h4 style='text-align:center'>You earned a total of <span style='color:#998100;font-weight: bold;'>" . ($row['goldReward'] + $extraGold) . "(" . $extraGold . " bonus) gold</span> and <span style='color:darkgreen;font-weight: bold;'>" . ($row['xpReward'] + $extraXp) . "(" . $extraXp . " bonus) experience</span></h4>";
					
				$sql = "UPDATE trainingrounds SET finished=1,finishTime=NOW() WHERE character_id='$charId' AND finished=0";
				mysqli_query($conn,$sql);
				$sql = "UPDATE characters SET inTraining=0,trainingCreature=0,experience=experience+'$extraXp',gold=gold+'$extraGold' WHERE id='$charId'";
				mysqli_query($conn,$sql);
				

				
			}
			while ($i < 11){
				$statusBar .= "<span id='statusTile'>" . $i . "</span>";
				$i++;
			}
			
			
			
			$statusBar .= "</div>";
			echo "<h3 style='color:wheat;text-align:center'>Training Chain Bonus: " . ($wins*10) . "%</h3>";	
			echo $statusBar;	
			echo $extraRewards;	
			
			if($end == 1){
				echo "<h4 style='text-align:center'>Your training ends here, you can choose to go train again in the same area or go back and choose another one</h4>";
				endButtons($_SESSION['characterProperties']['inTraining']);
				$_SESSION['characterProperties']['inTraining'] = 0;
			}
			if($notPerfect == 0){
				return 1;
			}
		}
	}
	
}

function setSurrender($surrValue){
	global $conn;
	
	switch ($surrValue){
		case "0":
			break;
		case "0.1":
			break;
		case "0.2":
			break;
		case "0.3":
			break;
		case "0.4":
			break;
		case "0.5":
			break;
		default:
			echo "Error 356 - set surrender warning";
			exit;
	}
	$_SESSION['characterProperties']['trainingSurrender'] = $surrValue;
	$charId = $_SESSION['characterProperties']['id'];
	$sql = "UPDATE characters SET trainingSurrender='$surrValue' WHERE id='$charId'";
	#echo "<option>$sql</option>";
	mysqli_query($conn,$sql);
}

function afterFight($outcome,$attackText = "",$getCreature = ""){
	if($attackText == ""){
		updateStats($outcome);
	}
	else{
		updateStats($outcome,0,$attackText,$getCreature);
	}
}

function setNextRound(){
	global $conn;
	$charId = $_SESSION['characterProperties']['id'];
	$sql = "SELECT trainingCreature FROM characters WHERE id='$charId'";
	$result = mysqli_query($conn,$sql);
	
	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_assoc($result);
		
		if($row['trainingCreature'] == "choice"){
			$_SESSION['characterProperties']['trainingCreature'] = 0;
			$sql = "UPDATE characters SET trainingCreature=0 WHERE id='$charId'";
			mysqli_query($conn,$sql);
		}
		else{
			#echo "Error 508 - not authorized to change nextRound";
		}
		
	}
	
}

function showBattleReport($id){
	$sql = "SELECT * FROM battleReports WHERE id =?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	echo $row['report'];

	echo "<br><br>";
	echo "Link to this Battlereport: <a href=http://arena.rikka.se/index.php?page=view-battlereport&battleId=" . $id . ">http://arena.rikka.se/index.php?page=view-battlereport&battleId=" . $id . "</a>";
}

function finishRound(){
	statusBar(1);
}

if(isset($_GET['goTrain'])){
	startTraining($_GET['goTrain']);
}
elseif(isset($_GET['fight'])){
	if($_SESSION['characterProperties']['trainingCreature'] != "choice"){
		$outcome = fightMonster();
		afterFight($outcome);
	}
	else{
		training();
	}
}
elseif(isset($_GET['nextRound'])){
	setNextRound();
}
elseif(isset($_GET['skipRound'])){
	if($_SESSION['characterProperties']['trainingCreature'] != "choice"){
		updateStats("",1);
	}
}
elseif(isset($_POST['surrValue'])){
	setSurrender($_POST['surrValue']);
}
elseif(isset($_GET['finished'])){
	if($_SESSION['characterProperties']['inTraining'] != 0){
		finishRound();
	}
	else{
		training();
	}
}



?>