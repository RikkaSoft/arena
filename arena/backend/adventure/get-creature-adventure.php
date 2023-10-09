<?php 
if(!function_exists('getCreature')) {
	function getCreature($creatureName, $adventure = 1,$quantity = 1){
		if($quantity > 1){
			$numberOfEnemies = " x " . $quantity;
		}
		else{
			$numberOfEnemies = "";
		}
	    global $conn;
		if($adventure == 0 || $adventure == 2){
			$sql = "SELECT * FROM npc WHERE id=?";
		    $stmt = mysqli_prepare($conn,$sql);
		    mysqli_stmt_bind_param($stmt, "i", $creatureName);
		}
		else{
		    $sql = "SELECT * FROM npc WHERE name=?";
		    $stmt = mysqli_prepare($conn,$sql);
		    mysqli_stmt_bind_param($stmt, "s", $creatureName);
		}
	    mysqli_stmt_execute($stmt);
	    $result = $stmt->get_result();
	    $row = mysqli_fetch_assoc($result);
	    
	    $creatureName = 		$row['name'];
		
		if($adventure == 0){
			echo "<h4 style='text-align:center;'>You see a " . $creatureName . ", what do you do?</h4>";
		}
		
	    $creatureType =         $row['race'];
	    $creatureLevel =        $row['level'];
	    $creatureStrength =     $row['strength'];
	    $creatureDexterity =    $row['dexterity'];
	    $creatureVitality =     $row['vitality'];
	    $creatureIntellect =    $row['intellect'];
	    #$creatureMinDamage =    $row['minDamage'];
	    #$creatureMaxDamage =    $row['maxDamage'];
	    $creatureExperience =   $row['xpReward'];
	    #$creatureArmour =       $row['armour'];
	    $creatureGold =         $row['goldReward'];
	    $creatureDescription =  $row['description'];
		$creatureEquipId = 		$row['equipment_id'];
		
		$sql = "SELECT * FROM npcequipment WHERE eid = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $creatureEquipId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		
		$right_hand =		$row['right_hand'];
		$left_hand =		$row['left_hand'];
		$head =				$row['head'];
		$chest =			$row['chest'];
		$arm =				$row['arm'];
		$leg = 				$row['leg'];
		$feet = 			$row['feet'];
		$secondary =        $row['secondary'];
		
		$equipment = array($right_hand,$left_hand,$secondary,$head,$chest,$arm,$leg,$feet);            	
	    $i = 0;
	    require_once(__ROOT__."/backend/other/itemFunctions.php");
	    foreach ($equipment as $equip){
	        if ($equip != "1:1;1"){
	            if ($i < 3){
	                $type = "1";
	            }
	            else{
	                $type = "2";
	            }
	            $equipment[$i] = "<span id='" . $equip . "' class='item" . $type . " npcItem'>" . getItemWithoutName($equip,$type) . "</span>";
	        }
	        else{
	            $equipment[$i] = "Nothing";
	        }
	        $i++;
	    }
	    
	    
	
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
	    echo "<div id='itemInfo'></div>";            
	    echo "
	         <div id='beastSheet'>
			    <input type='hidden' value='" . $creatureName . "' name='name' />
			    <fieldset>
			       <legend style='text-align:center;font-size:22px;'>" . $creatureName . " - level " . $creatureLevel . " " . $creatureType . $numberOfEnemies . "</legend>
		           <fieldset style='width:45%;float:left;'>
		               <legend>Stats and Rewards</legend>
		               <div class='hidden-xs'> 
		                   <div style='float:left;width:50%;' style='text-align:left'>
		                   <table>
	                   			<tr>
		                   			<td>
		                   				Strength
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"strength\" value=\"" . $creatureStrength . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Dexterity
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"dexterity\" value=\"" . $creatureDexterity . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Vitality
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"vitality\" value= \"" . $creatureVitality . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Intellect
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"intellect\" value=\"" . $creatureIntellect . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Gold Reward
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"gold\" value=\"" . $trueGold . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				XP Reward
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"xp\" value=\"" . $trueXp . "\"readonly>
		                       		</td>
	                       		</tr>
	                   		</table>
		                   </div>
		               </div>
		               <div class='hidden-sm hidden-md hidden-lg' style='text-align:left'>
		    	           <table>
	                   			<tr>
		                   			<td>
		                   				Strength
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"strength\" value=\"" . $creatureStrength . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Dexterity
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"dexterity\" value=\"" . $creatureDexterity . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Vitality
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"vitality\" value= \"" . $creatureVitality . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Intellect
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"intellect\" value=\"" . $creatureIntellect . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				Gold Reward
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"gold\" value=\"" . $trueGold . "\"readonly>
		                       		</td>
	                       		</tr>
	                       		<tr>
		                   			<td>
		                   				XP Reward
		                   			</td>
		                   			<td>
		                       			<input id=\"attributesTrain\" type=\"text\" name=\"xp\" value=\"" . $trueXp . "\"readonly>
		                       		</td>
	                       		</tr>
	                   		</table>
		               </div>
		           </fieldset>
		           <fieldset style='width:45%;float:right;text-align:left;'>
		               <legend style='text-align:right;'>Equipment</legend>
		                Right Hand: $equipment[0]" 
						. "</br>Left Hand: $equipment[1]" . "</br>Secondary: $equipment[2]" . "</br>Head: $equipment[3]" . "</br>Chest: $equipment[4]" . "</br>Arm: $equipment[5]"
						. "</br>Leg: $equipment[6]
		           </fieldset>
		           <fieldset style='width:99.5%;text-align:center;'>
		               <legend style='text-align:center;margin-top:15px;'>Description</legend>
		               ".  $creatureDescription . "
		           </fieldset>
		        </fieldset>
				</div>";
	
	?>
	<script>
		$('.item1').click(function(){
			showItemInfo($(this).position(),$(this).attr('id'),"1");
		});
		$('.item2').click(function(){
			showItemInfo($(this).position(),$(this).attr('id'),"2");
		});
		
		function showItemInfo(position,name,type){
			$('#itemInfo').load("index.php?page=view-npc-item&nonUI&name="+name+"&type="+type,function(){
				$('#itemInfo').css("top",position.top-parseInt($('#itemInfo').height())/2);
					$('#itemInfo').css("left",position.left-parseInt($('#itemInfo').width())-30);
					if($('#itemInfo').css('display') == 'none'){
						$('#itemInfo').slideToggle();
					}
					else{
						$('#itemInfo').show();
					}
					$('body').click(function(evt){
						clickAnywhere(evt);
					})
			});
		}
		
		function clickAnywhere(evt){
				if(evt.target.id != 'itemInfo' && $(evt.target).attr('class') != 'item1' && $(evt.target).attr('class') != 'item2'){
					if($('#monsterInfo').css('display') != 'none'){
						$('#monsterInfo').css('display','none');
						$('#itemInfo').css('display','none');
					}
				}
			}
	</script>
	
	<?php
	}
}
	?>
