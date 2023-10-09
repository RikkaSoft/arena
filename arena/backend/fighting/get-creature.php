<?php 
if(isset($_GET['creatureName'])) {
	global $conn;
	$creatureName = $_GET['creatureName'];
	$sql = "SELECT * FROM creatures WHERE name=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "s", $creatureName);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	
	
	$creatureType = 		$row['type'];
	$creatureLevel = 		$row['level'];
	$creatureStrength = 	$row['strength'];
	$creatureDexterity = 	$row['dexterity'];
	$creatureVitality = 	$row['vitality'];
	$creatureIntellect = 	$row['intellect'];
	$creatureMinDamage = 	$row['minDamage'];
	$creatureMaxDamage = 	$row['maxDamage'];
	$creatureExperience = 	$row['experience'];
	$creatureArmour = 		$row['armour'];
	$creatureGold = 		$row['gold'];
	$creatureDescription = 	$row['description'];
	
	

	$yourLevel = $_SESSION['characterProperties']['level'];
	
	if($creatureGold-$yourLevel > 0){
		$trueGold = $creatureGold-$yourLevel;
	}
	else{
		$trueGold = 0;
	}
	if($creatureExperience-$yourLevel > 0){
	$trueXp = $creatureExperience-$yourLevel;
	}
	else{
		$trueXp = 0;
	}
	
	$sql = "SELECT * FROM modifiers";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	$attackMod = $row['attackMod'];
				
	echo "<form role=\"creature\" method=\"post\" action=\"index.php?page=training_results\">
                        	    <div id='beastSheet'>
                        	    <input type='hidden' value='" . $creatureName . "' name='name' />
                        	    <fieldset>
                        	       <legend style='text-align:center;font-size:22px;'>" . $creatureName . "</legend>
                    	           <fieldset style='width:45%;float:left;'>
                                       <legend>Stats</legend>
                                       <div class='hidden-xs'> 
                                           <div style='float:left;width:50%;'>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"strength\" value=\"" . $creatureStrength . "\"readonly> Strength<br>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"dexterity\" value=\"" . $creatureDexterity . "\"readonly> Dexterity<br>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"vitality\" value= \"" . $creatureVitality . "\"readonly> Vitality<br>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"intellect\" value=\"" . $creatureIntellect . "\"readonly> Intellect<br>
                                           </div>
                                           <div style='float:left;width:50%;'>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"damage\" value=\"" . round($creatureMinDamage*(($attackMod*$creatureStrength)+1)) . "-" . round($creatureMaxDamage*(($attackMod*$creatureStrength)+1)) . "\"readonly> Damage<br>
                                               <input id=\"attributesTrain\" type=\"text\" name=\"armour\" value=\"" . $creatureArmour . "\"readonly> Armour
                                           </div>
                                       </div>
                                       <div class='hidden-sm hidden-md hidden-lg'>
                            	           <input id=\"attributesTrain\" type=\"text\" name=\"strength\" value=\"" . $creatureStrength . "\"readonly> Strength<br>
                            	           <input id=\"attributesTrain\" type=\"text\" name=\"dexterity\" value=\"" . $creatureDexterity . "\"readonly> Dexterity<br>
                            	           <input id=\"attributesTrain\" type=\"text\" name=\"vitality\" value= \"" . $creatureVitality . "\"readonly> Vitality<br>
                            	           <input id=\"attributesTrain\" type=\"text\" name=\"intellect\" value=\"" . $creatureIntellect . "\"readonly> Intellect<br>
                            	           <input id=\"attributesTrain\" type=\"text\" name=\"damage\" value=\"" . round($creatureMinDamage*(($attackMod*$creatureStrength)+1)) . "-" . round($creatureMaxDamage*(($attackMod*$creatureStrength)+1)) . "\"readonly> Damage<br>
                        	               <input id=\"attributesTrain\" type=\"text\" name=\"armour\" value=\"" . $creatureArmour . "\"readonly> Armour
                    	               </div>
                    	           </fieldset>
                    	           <fieldset style='width:45%;float:right;'>
                                       <legend style='text-align:right;'>Info and Rewards</legend>
                                       <input style='width:60px;text-align:center;' type=\"text\" name=\"type\" value=\"" . $creatureType . "\"readonly> Type<br>
                                       <input id=\"attributesTrain\" type=\"text\" name=\"level\" value=\"" . $creatureLevel . "\"readonly> Level<br>
                                       <input id=\"attributesTrain\" type=\"text\" name=\"xp\" value=\"" . $trueXp . "\"readonly> XP <br>
                                       <input id=\"attributesTrain\" type=\"text\" name=\"gold\" value=\"" . $trueGold . "\"readonly> Gold
                                   </fieldset>
                                   <fieldset style='width:99.5%;text-align:center;'>
                                       <legend style='text-align:center;margin-top:15px;'>Description</legend>
                                       ".  $creatureDescription . "
                                   </fieldset>
                    	        </fieldset>
								</div>
								";
								
								if(isset($_COOKIE['trainingSurrenderDefault'])){
									$lastSurrValue = (float) $_COOKIE['trainingSurrenderDefault'];
								}
								else{
									$lastSurrValue = 1;
								}
					           echo "<div style='text-align:center'>";
								$i = 0.5;
								echo "When do you wish to surrender?<br><select name=\"yourSurrender\" onchange=\"setSurrender(this)\" >";
								do {
									if ($i >= $lastSurrValue){
										$si = $i*100;
										$hp = round(($_SESSION['characterProperties']['vitality'] * $i));
										echo "<option value=$i selected>$si% ($hp hp)</option>";
										$i = $i-0.1;
									}
									else{
										$si = $i*100;
										$hp = round(($_SESSION['characterProperties']['vitality'] * $i));
										echo "<option value=$i>$si% ($hp hp)</option>";
										$i = $i-0.1;
									}
								} while ($i >= 0.1);
								echo "<option value=0>0% (If you lose, you die)</option>";
								echo "</select><button type=\"submit\" class=\"btn btn-default\" style='margin-left:40px;'>Fight!</button></div></form>";
                                
}

function listCreatures(){
	global $conn;
	$sql = "SELECT name FROM creatures WHERE fightable=1 ORDER BY level";
	$result = mysqli_query($conn,$sql);
	
	
	echo "<select id=\"beastList\" onchange=\"beastInfo()\">
		<option>Choose a Beast</option>";
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$creatureName = $row['name'];
				
				echo "<option>" . $creatureName . "</option>";
			}
	echo "</select>";
}

function listStats(){
	
	$wins = $_SESSION['characterProperties']['trainingWins'];
	$losses = $_SESSION['characterProperties']['trainingLosses'];
	$total = $wins + $losses;
	
	echo "<strong>Your total training matches: " . $total . "</strong><br><br>";
	echo "<strong>Your total wins: " . $wins . "</strong><br>";
	echo "<strong>Your total losses: " . $losses . "</strong><br><br>";
	
	if($total > 0 ){
		echo "<strong>Your win ratio: " . round(($wins / $total) * 100) . "%</strong>";
	}
	
}

?>