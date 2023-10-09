<?php
	include(__ROOT__."/backend/admin/addCreature.php");
	if($_SESSION['loggedIn'] == "rikka"){
?>

<form role="register" method="post" name="register-char" action="index.php?adpage=addCreature&nonUI&addNew">
				<table id="charTable" border="2" style="width:100%">
				 <tbody>
				 	<tr>
				 		<td colspan="4" align="center">
				 			<h2>Character Sheet</h2>
				 		</td>
				 	</tr>
				  <tr>
					<td>
					<label>Name</label>
					<a title="Your name has to be 3-12 characters" class="tooltipRight"><span title="">
						<input type="text" id="name" onchange="checkName()" name="name" pattern=".{3,30}" required class='characterFields' >
					</span></a>
					<label id="nameOk"></label>
					</td>
					<td>
					<label>Level</label>
						<input type="number" name="level" required>
					</td>
					<td>
					<label for="gender">Gender</label>
					<select id="gender" name="gender" class='characterFields'>
						<option>Male</option>
						<option>Female</option>
					</select>
					</td>
					<td>
					<label for="race">Race</label>
					<select id="race" name="race" class='characterFields'>
						<option>Human</option>
						<option>Elf</option>
						<option>Dwarf</option>
						<option>Troll</option>
						<option>Undead</option>
						<option>Dryad</option>
						<option>Beast</option>
						<option>Demon</option>
						<option>Orc</option>
					</select>
				  </tr>
				 	<tr>
				 		<td colspan="4" align="center">
				 			<strong>Primary Stats</strong>
				 		</td>
				 	</tr>
				 	  <tr id="statTable">
					  <td>
						<label>Strength</label>
							<a title="Strength increases the damage you do in melee combat. Each weapon has a strength requirement" class="tooltipRight"><span title=""> 
								<input id="attributesStats" type="number" name="strength" value=20> 
							</span></a>
							
							
						  </td>
							 
						  <td>
						  <label>Dexterity</label>
						  	<a title="Dexterity greatly increases your ranged damage with bows and slightly increases your melee damage. It also gives you a boost to initiative, crit and dodge" class="tooltipRight"><span title="">
								<input id="attributesStats" type="number" name="dexterity" value=20>
							</span></a>
							
							
						  </td>
						  
						  <td>
						  <label>Vitality</label>
						  	<a title="Vitality determines your toughness, how much of a beating that you can withstand" class="tooltipLeft"><span title="">
								<input id="attributesStats" type="number" name="vitality" value=20>
							</span></a>
							
							
						  </td>
						  
						  <td>
						  <label>Intellect</label>
						  	<a title="Intellect determines the amount of skillpoints you start with and receieve as you level up" class="tooltipLeft"><span title="">
								<input id="attributesStats" type="number" name="intellect" value=20>
							</span></a>
							
							
						  </td>
				  </tr>
					<tr height="20px">
						<td colspan="4"></td>
					</tr>
					
					<tr>
	                	<td colspan="2" align="center"class="featCells">
	                            <strong>Race Description</strong>
	                    </td>
	                    <td colspan="2" align="center"class="featCells">
	                            <strong>Starting Tips</strong>
	                    </td>
	                </tr>
	                <tr>
	                	<td colspan="2" valign="top" align="center" class="featCells">
	                    	<input name="raceDesc" style='width:100%;'></input>
	                    </td>
	                    <td colspan="2" valign="top" align="center" class="featCells">
                            <div id="raceTips">
                            	
                            </div>
	                    </td>
	                </tr>
					
					<tr>
	                    <td colspan="2" align="center" style="color:#B20000;" ><strong>Offensive Skills</strong></td>
	                    <td colspan="2" align="center" style="color:green;"><strong>Defensive Skills</strong></td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">1H Weapons</label>
	                        <a title="Makes you better at fighting with One-Handed weapons" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="one_handed" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Light Armour</label>
	                        <a title="Allows you to equip yourself with Light Armour" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="light_armour" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">2H Weapons</label>
	                        <a title="Makes you better at fighting with Two-Handed weapons" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="two_handed" value="0" >
	                        </span></a>
	                        
	                        
	                </td>
	                <td colspan="2" class="skillCells">
	                	<label class="skillLabels">Heavy Armour</label>
	                        <a title="Allows you to equip yourself with Heavy Armour" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="heavy_armour" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Bow</label>
	                        <a title="Makes you better at using a Bow" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="bow" value="0"  >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Shield</label>
	                        <a title="Makes you better at defending with a Shield" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="shield" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Crossbow</label>
	                        <a title="Makes you better at using a Crossbow" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="crossbow" value="0"  >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Parry</label>
	                        <a title="Increases your skill at blocking with your weapon" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="parry" value="0"  >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Finesse</label>
	                        <a title="Finesse slightly increases your chance to critically hit your opponent" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="finesse" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Foul Play</label>
	                        <!--"Dignity and an empty sack is worth the sack - Rule of aquisition 109"--> 
	                        <a title="Allows you to do undignified attacks like throwing sand into your opponents eyes or a kick in the groin" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="foul_play" value="0" >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Initiative</label>
	                        <a title="Initiative makes you more likely to be the one to act first" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="initiative" value="0"  >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Dodge</label>
	                        <a title="A high dodge skill will help you avoid your opponents attacks" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="dodge" value="0"  >
	                        </span></a>
	                        
	                        
	                    </td>
	                
				  </tbody>
				</table>
				
				
				<table>
					<tr>
							<?php
								echo "Right Hand:";
								listItems("right");
								echo "<br>";
								echo "Left Hand:";
								listItems("left");
								echo "<br>";
								echo "Secondary:";
								listItems("secondary");
								echo "<br>";
								echo "Head:";
								listItems("head");
								echo "<br>";
								echo "Chest:";
								listItems("chest");
								echo "<br>";
								echo "Arms:";
								listItems("arms");
								echo "<br>";
								echo "Legs:";
								listItems("legs");
								echo "<br>";
								echo "Feet:";
								listItems("feet");
								echo "<br>";
							?>
					</tr>
					<td>
						
					</td>
				</table>
				
				GOLD REWARD MINUS LEVEL <input name='goldReward'><br>
				XP REWARD MINUS LEVEL <input name='xpReward'>
				
				<br>
					<button type="submit" class="btn btn-default" style='float:right;'>
						Create your character!
					</button>
				</form>
				
<?php
	}
?>