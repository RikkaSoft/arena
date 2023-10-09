<script>

	function validateForm() {
	    var strengthPoints = parseInt(document.forms["register-char"]["strength"].value);
	    var dexterityPoints = parseInt(document.forms["register-char"]["dexterity"].value);
	    var vitalityPoints = parseInt(document.forms["register-char"]["vitality"].value);
	    var intellectPoints = parseInt(document.forms["register-char"]["intellect"].value);
	    var totalPoints = strengthPoints+dexterityPoints+vitalityPoints+intellectPoints;
	    
	    var maxPoints = 140;
	    if (document.forms["register-char"]["race"].value == "Dryad"){
	    	maxPoints = 120;
	    }
	    
	    if (totalPoints != maxPoints) {
	    	var points = totalPoints + "/"+maxPoints + " stat points\n";
	    }

	    var one_handedSkillPoints = parseInt(document.forms["register-char"]["one_handed"].value); 
	    var two_handedSkillPoints = parseInt(document.forms["register-char"]["two_handed"].value); 
	    var shieldSkillPoints = parseInt(document.forms["register-char"]["shield"].value); 
	    var parrySkillPoints = parseInt(document.forms["register-char"]["parry"].value); 
	    var foul_playSkillPoints = parseInt(document.forms["register-char"]["foul_play"].value); 
	    var light_armourSkillPoints = parseInt(document.forms["register-char"]["light_armour"].value); 
	    var heavy_armourSkillPoints = parseInt(document.forms["register-char"]["heavy_armour"].value); 
	    var finesseSkillPoints = parseInt(document.forms["register-char"]["finesse"].value); 
	    var bowSkillPoints = parseInt(document.forms["register-char"]["bow"].value); 
	    var dodgeSkillPoints = parseInt(document.forms["register-char"]["dodge"].value); 
	    var crossbowSkillPoints = parseInt(document.forms["register-char"]["crossbow"].value); 
	    var initiativeSkillPoints = parseInt(document.forms["register-char"]["initiative"].value); 
	    var totalAvailableSkillPoints = 100+intellectPoints;
	    var totalSkillPoints = one_handedSkillPoints+two_handedSkillPoints+shieldSkillPoints+parrySkillPoints+foul_playSkillPoints+light_armourSkillPoints+heavy_armourSkillPoints+finesseSkillPoints+bowSkillPoints+dodgeSkillPoints+crossbowSkillPoints+initiativeSkillPoints;
	    if (totalSkillPoints != totalAvailableSkillPoints) {
	    	var skills = totalSkillPoints + "/" + totalAvailableSkillPoints + " skillpoints\n";
	    }
	    
	    if (points || skills) {
	    	if (typeof points == 'undefined') {points = ""; }
	    	if (typeof skills == 'undefined') {skills = ""; }
	    	
	    	alert("You need to assign all the points and skills \n\n" + points + skills)
	    	return false;
	    }
	}
	function remainingStats(){

		var strengthPoints = parseInt($('#strength').val());
	    var dexterityPoints = parseInt($('#dexterity').val());
	    var vitalityPoints = parseInt($('#vitality').val());
	    var intellectPoints = parseInt($('#intellect').val());
	    var totalPoints = strengthPoints+dexterityPoints+vitalityPoints+intellectPoints;
	    
	    var maxPoints = 140;
	    if (document.forms["register-char"]["race"].value == "Dryad"){
	    	maxPoints = 120;
	    }
	   	
	   	var result = (maxPoints-totalPoints);
	   	document.getElementById("stats").innerHTML = totalPoints + "/" + maxPoints +" stat points";
	   	
	   	if (result < 0){
	   		document.getElementById("stats").style.color = 'red';
	   	}
	   	else {
	   		document.getElementById("stats").style.color = 'black';
	   	}
	   	
	   	remainingSkills();
		
   	}
   	
   	function remainingSkills(){
	  	var one_handedSkillPoints = parseInt(document.forms["register-char"]["one_handed"].value); 
	    var two_handedSkillPoints = parseInt(document.forms["register-char"]["two_handed"].value); 
	    var shieldSkillPoints = parseInt(document.forms["register-char"]["shield"].value); 
	    var parrySkillPoints = parseInt(document.forms["register-char"]["parry"].value); 
	    var foul_playSkillPoints = parseInt(document.forms["register-char"]["foul_play"].value); 
	    var light_armourSkillPoints = parseInt(document.forms["register-char"]["light_armour"].value); 
	    var heavy_armourSkillPoints = parseInt(document.forms["register-char"]["heavy_armour"].value); 
	    var finesseSkillPoints = parseInt(document.forms["register-char"]["finesse"].value); 
	    var bowSkillPoints = parseInt(document.forms["register-char"]["bow"].value); 
        var dodgeSkillPoints = parseInt(document.forms["register-char"]["dodge"].value); 
        var crossbowSkillPoints = parseInt(document.forms["register-char"]["crossbow"].value); 
        var initiativeSkillPoints = parseInt(document.forms["register-char"]["initiative"].value); 
	    var intellectPoints = parseInt(document.forms["register-char"]["intellect"].value);
	    var totalAvailableSkillPoints = 100+intellectPoints;
	    var totalSkillPoints = one_handedSkillPoints+two_handedSkillPoints+shieldSkillPoints+parrySkillPoints+foul_playSkillPoints+light_armourSkillPoints+heavy_armourSkillPoints+finesseSkillPoints+bowSkillPoints+dodgeSkillPoints+crossbowSkillPoints+initiativeSkillPoints;
       
	   	document.getElementById("skills").innerHTML = totalSkillPoints + "/" + totalAvailableSkillPoints + " skill points";
	   	
	   	if (totalAvailableSkillPoints-totalSkillPoints < 0){
	   		document.getElementById("skills").style.color = 'red';
	   	}
	   	else {
	   		document.getElementById("skills").style.color = 'black';
	   	}
   	}

function getRaceDesc() {
        		var race = $("#race option:selected").text();
                $('#raceDesc').load('index.php?cpage=check-char&nonUI&getRaceDesc=' + race);
                $('#raceTips').load('index.php?cpage=check-char&nonUI&getRaceTips=' + race);
                getRaceStats();
                
}
function getRaceStats(){
	var race = $("#race option:selected").text();
	$.getJSON('index.php?cpage=check-char&nonUI&getRaceStats=' + race,function(data){
		$('#strength').val(data[0]);
		$('#strength').attr({"min":data[0],"max":data[0]+50});
		$('#dexterity').val(data[1]);
		$('#dexterity').attr({"min":data[1],"max":data[1]+50});
		$('#vitality').val(data[2]);
		$('#vitality').attr({"min":data[2],"max":data[2]+50});
		$('#intellect').val(data[3]);
		$('#intellect').attr({"min":data[3],"max":data[3]+50});
		remainingStats();
	    //resizeLeft();
	});
	    
	
}
function checkName(){
	var name = $("#name").val();
	$('#nameOk').load('index.php?cpage=check-char&nonUI&checkName=' + name);
}
/*
function resizeLeft(){
	if (window.matchMedia("(min-width: 768px)").matches) {
		var dynamic = $('#mainPage');
	    var static = $('#createCharInfo');
	    static.height(dynamic.height()-20);	       
    }
    else{

    }
	
}
*/
$(document).ready(function(){
	getRaceDesc();

});

</script>
<div id="createCharInfo">
				<h2>Create Character</h2>
				<p>It is time for a new fighter to enter the Arena, to become a legend, or to have his blood stain the walls</p>
				<p>You have a total of <strong>140 points</strong> (except if you are a Dryad) to place in the four main attributes. <strong>Strength, Dexterity, Stamina, Intellect</strong><br> The value cannot be lower than the initial racial value, it also can't be more than 50 points higher than the initial racial value.</p>
				<p>You have a total of <strong>100 + Intellect points</strong> to spend on your skills. A value between 1 and 80 is required for each skill you wish to aquire
				<p>Each time you level up you will get an additional 15 stat points and 25 + intellect/2 skill points!</p>
				
	
						
			</div>
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
							<form role="register" onsubmit="return validateForm()" autocomplete="off" method="post" name="register-char" action="index.php?cpage=create-character&nonUI">
							<table id="charTable" style="width:100%">
								<tbody>
								  <tr>
									<td>
										<img src='frontend/design/images/character/icons/name.png' class='skillIcon'>
										<a><span title="">
											<input type="text" id="name" onchange="checkName()" name="name" pattern=".{3,12}" required class='characterFields' >
										</span></a>
										<label>Name</label>

										<label id="nameOk"></label>
									</td>
								</tr>
								<tr>
									<td>
										<img src='frontend/design/images/character/icons/gender.png' class='skillIcon'>
										<select id="gender" name="gender" class='characterFields'>
											<option>Male</option>
											<option>Female</option>
										</select>
										<label for="gender">Gender</label>

									</td>
								</tr>
								<tr>
									<td>
										<img src='frontend/design/images/character/icons/race.png' class='skillIcon'>
										
										<select id="race" onchange="getRaceDesc();" name="race" class='characterFields'>
											<option>Human</option>
											<option>Elf</option>
											<option>Dwarf</option>
											<option>Troll</option>
											<option>Undead</option>
											<option>Dryad</option>
										</select>
										<label for="race">Race</label>
									</td>
							  	</tr>
							  	<tr>
							  		<td>
						  				<img src='frontend/design/images/character/icons/strength.png' class='skillIcon'>
										<input id='strength' type='number' name='strength' onchange=remainingStats()> 
								  		<a title='Strength increases the damage you do in melee combat. Each weapon has a strength requirement' class='tooltipLeft'><span title=''> 
											<span class='tooltipHover'>Strength</span>
										</span></a>
								  	</td>
							 	</tr>
							  	<tr>
								  	<td>
								  		<img src='frontend/design/images/character/icons/agility.png' class='skillIcon'>
										<input id='dexterity' type='number' name='dexterity' onchange=remainingStats()>
									  	<a title='Dexterity greatly increases your ranged damage with bows and slightly increases your melee damage. It also gives you a boost to initiative, crit and dodge' class='tooltipLeft'><span title=''>
											<span class='tooltipHover'>Dexterity</span>
										</span></a>
							 		</td>
							  	</tr>
							  	<tr>
								  	<td>
								  		<img src='frontend/design/images/character/icons/stamina.png' class='skillIcon'>
										<input id='vitality' type='number' name='vitality' onchange=remainingStats()>
									  	<a title='Vitality determines your toughness, how much of a beating that you can withstand' class='tooltipLeft'><span title=''>
											<span class='tooltipHover'>Vitality</span>
										</span></a>
					 			 	</td>
							  	</tr>
							  	<tr>
								  	<td>
								  		<img src='frontend/design/images/character/icons/intellect.png' class='skillIcon'>
										<input id='intellect' type='number' name='intellect' onchange=remainingStats()>
									  	<a title='Intellect determines the amount of skillpoints you start with and receieve as you level up' class='tooltipLeft'><span title=''>
											<span class='tooltipHover'>Intellect</span>
										</span></a>
								  	</td>
							  	</tr>
							  	<tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/1h.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="one_handed" value="0" min="0" max="80" onchange="remainingSkills()">
				                        <a title="Makes you better at fighting with One-Handed weapons" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>1H Weapons</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/2h.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="two_handed" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<a title="Makes you better at fighting with Two-Handed weapons" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>2H Weapons</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/bow.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="bow" value="0" min="0" max="80"  onchange="remainingSkills()" >
			                       		<a title="Makes you better at using a Bow" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Bow</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/crossbow.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="crossbow" value="0" min="0" max="80"  onchange="remainingSkills()" >
			                       		<a title="Makes you better at using a Crossbow" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Crossbow</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/initiative.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="initiative" value="0" min="0" max="80"  onchange="remainingSkills()" >
			                        <a title="Initiative makes you more likely to be the one to act first" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Initiative</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/finesse.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="finesse" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<a title="Finesse slightly increases your chance to critically hit your opponent" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Finesse</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/light.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="light_armour" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<a title="Allows you to equip yourself with Light Armour" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Light Armour</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/heavy.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="heavy_armour" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<a title="Allows you to equip yourself with Heavy Armour" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Heavy Armour</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/shield.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="shield" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<a title="Makes you better at defending with a Shield" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Shield</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/parry.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="parry" value="0" min="0" max="80" onchange="remainingSkills()" >
			                        	<a title="Increases your skill at blocking with your weapon" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Parry</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/foul.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="foul_play" value="0" min="0" max="80" onchange="remainingSkills()">
			                        	<!--"Dignity and an empty sack is worth the sack - Rule of acquisition 109"--> 
			                        	<a title="Allows you to do undignified attacks like throwing sand into your opponents eyes or a kick in the groin" class="tooltipLeft"><span title=""> 
											<span class='tooltipHover'>Foul Play</span>
										</span></a>
									</td>
			                    </tr>
			                    <tr>
			                    	<td>
			                    		<img src='frontend/design/images/character/icons/dodge.png' class='skillIcon'>
			                            <input id="attributes" type="number" name="dodge" value="0" min="0" max="80"  onchange="remainingSkills()" >
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
					<div class='characterSubWrapper characterExtras'>
						
						<div id="raceDesc">
			                    		
		            	</div>
		                <div id="raceTips">
		                	TEST
		                </div>
		            </div>
		            <div class='characterSubWrapper characterBottom'>
						<div id="remaining" style='float:left;font-weight: bold;'>
							<div id="stats">
								80/140 stat points
							</div>
							
							<div id="skills">
								0/120 skill points
							</div>
						</div>
						<div class='buttonWrapper'>
							<button type="submit" class="charButton">
								Create your character!
							</button>
						</div>
					</div>
					</form>
			</div>
			<a href="index.php?page=specifics" style='color:lightblue' target="_blank">
	                Click here for more information about skills and stats (popup)
	            </a>
