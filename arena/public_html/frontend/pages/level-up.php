<?php

$name = $_SESSION['characterProperties']['name'];
$gender = $_SESSION['characterProperties']['gender'];
$level = $_SESSION['characterProperties']['level'];
$race = $_SESSION['characterProperties']['race'];
$strength = $_SESSION['characterProperties']['strength'];
$dexterity = $_SESSION['characterProperties']['dexterity'];
$vitality = $_SESSION['characterProperties']['vitality'];
$intellect = $_SESSION['characterProperties']['intellect'];
$one_handed = $_SESSION['characterProperties']['one_handed'];
$two_handed = $_SESSION['characterProperties']['two_handed'];
$shield = $_SESSION['characterProperties']['shield'];
$parry = $_SESSION['characterProperties']['parry'];
$finesse = $_SESSION['characterProperties']['finesse'];
$foul_play = $_SESSION['characterProperties']['foul_play'];
$light_armour = $_SESSION['characterProperties']['light_armour'];
$heavy_armour = $_SESSION['characterProperties']['heavy_armour'];
$bow = $_SESSION['characterProperties']['bow'];
$crossbow = $_SESSION['characterProperties']['crossbow'];
$dodge = $_SESSION['characterProperties']['dodgeSkill'];
$initiative = $_SESSION['characterProperties']['initiative'];
$right_hand = $_SESSION['characterProperties']['right_hand'];
$left_hand = $_SESSION['characterProperties']['left_hand'];
$weight = $_SESSION['characterProperties']['weight'];
$levelUp = $_SESSION['characterProperties']['levelUp'];


?>
						<?php 
						if($levelUp < 0){
							echo "You have not leveled up yet, don't try to cheat =(";
						}
						?>
						<script>
							function remainingStats(){
								var strengthPoints = parseInt(document.forms["level-char"]["newStrength"].value);
							    var dexterityPoints = parseInt(document.forms["level-char"]["newDexterity"].value);
							    var vitalityPoints = parseInt(document.forms["level-char"]["newVitality"].value);
							    var intellectPoints = parseInt(document.forms["level-char"]["newIntellect"].value);
							    var totalPoints = strengthPoints+dexterityPoints+vitalityPoints+intellectPoints;
							   	var prePoints = <?php echo $strength+$vitality+5+$intellect+$dexterity; ?>;
							   	var result = (totalPoints-prePoints);
							   	document.getElementById("stats").innerHTML = "You have spent " + result + "/" + "15 stat points";
							   	
							   	if (result > 15){
							   		document.getElementById("stats").style.color = 'red';
							   	}
							   	else {
							   		document.getElementById("stats").style.color = 'black';
							   	}
							   	
							   	
							   	
							   	var one_handedSkillPoints = parseInt(document.forms["level-char"]["one_handed"].value); 
							    var two_handedSkillPoints = parseInt(document.forms["level-char"]["two_handed"].value); 
							    var shieldSkillPoints = parseInt(document.forms["level-char"]["shield"].value); 
							    var parrySkillPoints = parseInt(document.forms["level-char"]["parry"].value); 
							    var foul_playSkillPoints = parseInt(document.forms["level-char"]["foul_play"].value); 
							    var light_armourSkillPoints = parseInt(document.forms["level-char"]["light_armour"].value); 
							    var heavy_armourSkillPoints = parseInt(document.forms["level-char"]["heavy_armour"].value); 
							    var finesseSkillPoints = parseInt(document.forms["level-char"]["finesse"].value); 
							    var bowSkillPoints = parseInt(document.forms["level-char"]["bow"].value); 
                                var dodgeSkillPoints = parseInt(document.forms["level-char"]["dodge"].value); 
                                var crossbowSkillPoints = parseInt(document.forms["level-char"]["crossbow"].value); 
                                var initiativeSkillPoints = parseInt(document.forms["level-char"]["initiative"].value); 
							    var totalAvailableSkillPoints = <?php echo round(25+($intellect/2)); ?>;
							    var totalSkillPoints = one_handedSkillPoints+two_handedSkillPoints+shieldSkillPoints+parrySkillPoints+foul_playSkillPoints+light_armourSkillPoints+heavy_armourSkillPoints+finesseSkillPoints+bowSkillPoints+dodgeSkillPoints+crossbowSkillPoints+initiativeSkillPoints;
                            	var preSkillPoints = <?php echo $one_handed+$two_handed+$shield+$parry+$foul_play+$light_armour+$heavy_armour+$finesse+$bow+$crossbow+$dodge+$initiative; ?>;
							   	var skillResult = (totalSkillPoints-preSkillPoints);
							   	
							   	document.getElementById("skills").innerHTML = "You have spent " + skillResult + "/" + totalAvailableSkillPoints + " skill points";
							   	
							   	if (skillResult > totalAvailableSkillPoints){
							   		document.getElementById("skills").style.color = 'red';
							   	}
							   	else {
							   		document.getElementById("skills").style.color = 'black';
							   	}
							}
							
							function validateForm(){
								var strengthPoints = parseInt(document.forms["level-char"]["newStrength"].value);
							    var dexterityPoints = parseInt(document.forms["level-char"]["newDexterity"].value);
							    var vitalityPoints = parseInt(document.forms["level-char"]["newVitality"].value);
							    var intellectPoints = parseInt(document.forms["level-char"]["newIntellect"].value);
							    var totalPoints = strengthPoints+dexterityPoints+vitalityPoints+intellectPoints;
							   	var prePoints = <?php echo $strength+$vitality+5+$intellect+$dexterity; ?>;
							    if (totalPoints != prePoints+15) {
									var spentPoints = totalPoints - prePoints;
							    	var points = "You have spent " + spentPoints + "/15 stat points\n";
							    }
			
							    var one_handedSkillPoints = parseInt(document.forms["level-char"]["one_handed"].value); 
							    var two_handedSkillPoints = parseInt(document.forms["level-char"]["two_handed"].value); 
							    var shieldSkillPoints = parseInt(document.forms["level-char"]["shield"].value); 
							    var parrySkillPoints = parseInt(document.forms["level-char"]["parry"].value); 
							    var foul_playSkillPoints = parseInt(document.forms["level-char"]["foul_play"].value); 
							    var light_armourSkillPoints = parseInt(document.forms["level-char"]["light_armour"].value); 
							    var heavy_armourSkillPoints = parseInt(document.forms["level-char"]["heavy_armour"].value); 
							    var finesseSkillPoints = parseInt(document.forms["level-char"]["finesse"].value); 
							    var bowSkillPoints = parseInt(document.forms["level-char"]["bow"].value); 
                                var dodgeSkillPoints = parseInt(document.forms["level-char"]["dodge"].value); 
                                var crossbowSkillPoints = parseInt(document.forms["level-char"]["crossbow"].value); 
                                var initiativeSkillPoints = parseInt(document.forms["level-char"]["initiative"].value); 
							    var totalAvailableSkillPoints = <?php echo round(25+($intellect/2)); ?>;
							    var totalSkillPoints = one_handedSkillPoints+two_handedSkillPoints+shieldSkillPoints+parrySkillPoints+foul_playSkillPoints+light_armourSkillPoints+heavy_armourSkillPoints+finesseSkillPoints+bowSkillPoints+dodgeSkillPoints+crossbowSkillPoints+initiativeSkillPoints;
                                var preSkillPoints = <?php echo $one_handed+$two_handed+$shield+$parry+$foul_play+$light_armour+$heavy_armour+$finesse+$bow+$crossbow+$dodge+$initiative; ?>;
							    if (totalSkillPoints != preSkillPoints+totalAvailableSkillPoints) {
							    	var spentSkills = totalSkillPoints-preSkillPoints
							    	var skills = "You have spent " + spentSkills + "/" + totalAvailableSkillPoints + " skillpoints\n";
							    }
							    
							    if (points || skills) {
							    	if (typeof points == 'undefined') {points = ""; }
							    	if (typeof skills == 'undefined') {skills = ""; }							    	
							    	alert("You need to assign all the points/skills\n\n" + points + skills)
							    	return false;
							    }
							}
							
						</script>
						
								
						
						<div class="mainContent">
						    <div class="pageInfoLevelUp">
                            <h2>Level Up!</h2>
                            <br>
                            Congratulations, you have leveled up!
                            <br>
                            <br>
                            Leveling up earns you 5 points in vitality and an additional 15 primary points to be spent on your primary stats
                            <br><br>
                            You also get 25 plus half of your intellect in skill points (rounded up)
                        </div>
                            <form role="levelUp" onsubmit="return validateForm()" method="post" name="level-char" action="index.php?cpage=level-up&nonUI">
							<div class='characterSheet'>
                                <div class='characterSubWrapper'>
                                    <div class='leftChar'>
                                        <div class='characterPicture'>
                                            <br><br>
                                            There might be a picture of your character here some day

                                        </div>
                                    </div>
                                    <div class='rightChar'>
                                        <div class='characterStats'>
                                            <table id="charTable" style="width:100%">
                                                <tbody>
                                                  <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/name.png' class='skillIcon'>
                                                        <a><span title="">
                                                            <input type="text" id="name" name="name" disabled required class='characterFields' value="<?php echo $_SESSION['characterProperties']['name'] ?>">
                                                        </span></a>
                                                        <label>Name</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/gender.png' class='skillIcon'>
                                                        <input type="text" id="yourCharInfo" class="characterFields" name="gender" disabled value="<?php echo $_SESSION['characterProperties']['gender'] ?>">
                                                    <label>Gender</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/race.png' class='skillIcon'>
                                                        
                                                        <input type="text" id="yourCharInfo" class="characterFields" name="race" disabled value="<?php echo $_SESSION['characterProperties']['race'] ?>">
                                                    <label>Race</label>
                                                    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/strength.png' class='skillIcon'>
                                                        <input id="attributesStats" type="number" name="newStrength" <?php echo "value=$strength min=$strength" ?> onchange="remainingStats()"> 
                                                        <a title='Strength increases the damage you do in melee combat. Each weapon has a strength requirement' class='tooltipLeft'><span title=''> 
                                                            <span class='tooltipHover'>Strength</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/agility.png' class='skillIcon'>
                                                        <input id="attributesStats" type="number" name="newDexterity" <?php echo "value=$dexterity min=$dexterity" ?> onchange="remainingStats()">
                                                        <a title='Dexterity greatly increases your ranged damage with bows and slightly increases your melee damage. It also gives you a boost to initiative, crit and dodge' class='tooltipLeft'><span title=''>
                                                            <span class='tooltipHover'>Dexterity</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/stamina.png' class='skillIcon'>
                                                        <input id="attributesStats" type="number" name="newVitality" <?php echo "value=" . ($vitality+5) . " min=" . ($vitality+5) ?> onchange="remainingStats()">
                                                        <a title='Vitality determines your toughness, how much of a beating that you can withstand' class='tooltipLeft'><span title=''>
                                                            <span class='tooltipHover'>Vitality</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/intellect.png' class='skillIcon'>
                                                        <input id="attributesStats" type="number" name="newIntellect" <?php echo "value=$intellect min=$intellect" ?> onchange="remainingStats()">
                                                        <a title='Intellect determines the amount of skillpoints you start with and receieve as you level up' class='tooltipLeft'><span title=''>
                                                            <span class='tooltipHover'>Intellect</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/1h.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="one_handed" <?php echo "value=$one_handed min=$one_handed" ?> onchange="remainingStats()">
                                                        <a title="Makes you better at fighting with One-Handed weapons" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>1H Weapons</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/2h.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="two_handed" <?php echo "value=$two_handed min=$two_handed" ?> onchange="remainingStats()">
                                                        <a title="Makes you better at fighting with Two-Handed weapons" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>2H Weapons</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/bow.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="bow" <?php echo "value=$bow min=$bow" ?> onchange="remainingStats()" >
                                                        <a title="Makes you better at using a Bow" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Bow</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/crossbow.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="crossbow" <?php echo "value=$crossbow min=$crossbow" ?> onchange="remainingStats()" >
                                                        <a title="Makes you better at using a Crossbow" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Crossbow</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/initiative.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="initiative" <?php echo "value=$initiative min=$initiative" ?> max="300" onchange="remainingStats()" >
                                                    <a title="Initiative makes you more likely to be the one to act first" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Initiative</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/finesse.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="finesse" <?php echo "value=$finesse min=$finesse" ?> max="300" onchange="remainingStats()">
                                                        <a title="Finesse slightly increases your chance to critically hit your opponent" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Finesse</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/light.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="light_armour" <?php echo "value=$light_armour min=$light_armour" ?> max="150" onchange="remainingStats()">
                                                        <a title="Allows you to equip yourself with Light Armour" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Light Armour</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/heavy.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="heavy_armour" <?php echo "value=$heavy_armour min=$heavy_armour" ?> max="150" onchange="remainingStats()">
                                                        <a title="Allows you to equip yourself with Heavy Armour" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Heavy Armour</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/shield.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="shield" <?php echo "value=$shield min=$shield" ?> max="300" onchange="remainingStats()">
                                                        <a title="Makes you better at defending with a Shield" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Shield</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/parry.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="parry" <?php echo "value=$parry min=$parry" ?> max="300" onchange="remainingStats()" >
                                                        <a title="Increases your skill at blocking with your weapon" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Parry</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/foul.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="foul_play" <?php echo "value=$foul_play min=$foul_play" ?> max="300" onchange="remainingStats()" >
                                                        <!--"Dignity and an empty sack is worth the sack - Rule of acquisition 109"--> 
                                                        <a title="Allows you to do undignified attacks like throwing sand into your opponents eyes or a kick in the groin" class="tooltipLeft"><span title="">
                                                            <span class='tooltipHover'>Foul Play</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src='frontend/design/images/character/icons/dodge.png' class='skillIcon'>
                                                        <input id="attributes" type="number" name="dodge" <?php echo "value=$dodge min=$dodge"?> max="300" onchange="remainingStats()" >
                                                        <a title="A high dodge skill will help you avoid your opponents attacks" class="tooltipLeft"><span title=""> 
                                                            <span class='tooltipHover'>Dodge</span>
                                                        </span></a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>							
							<div id="remaining" style='float:left;'>
								<div id="stats">
									You have spent 0/15 stat points
								</div>
								
								<div id="skills">
									You have spent 0/<?php echo round(25+($intellect/2)); ?> skill points
								</div>
							</div>
							<br>
								<button type="submit" class='saveCharButton' style='float:right;'>
									Save your attributes
								</button>
							</form>
							</div>
						</div>
						