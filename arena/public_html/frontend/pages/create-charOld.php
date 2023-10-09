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
	    	var points = "You have spent " + totalPoints + "/"+maxPoints + " stat points\n";
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
	    	var skills = "You have spent " + totalSkillPoints + "/" + totalAvailableSkillPoints + " skillpoints\n";
	    }
	    
	    if (points || skills) {
	    	if (typeof points == 'undefined') {points = ""; }
	    	if (typeof skills == 'undefined') {skills = ""; }
	    	
	    	alert("You need to assign all the points and skills \n\n" + points + skills)
	    	return false;
	    }
	}
	function remainingStats(){
   	
		var strengthPoints = parseInt(document.forms["register-char"]["strength"].value);
	    var dexterityPoints = parseInt(document.forms["register-char"]["dexterity"].value);
	    var vitalityPoints = parseInt(document.forms["register-char"]["vitality"].value);
	    var intellectPoints = parseInt(document.forms["register-char"]["intellect"].value);
	    var totalPoints = strengthPoints+dexterityPoints+vitalityPoints+intellectPoints;
	    
	    var maxPoints = 140;
	    if (document.forms["register-char"]["race"].value == "Dryad"){
	    	maxPoints = 120;
	    }
	   	
	   	var result = (maxPoints-totalPoints);
	   	document.getElementById("stats").innerHTML = "You have spent " + totalPoints + "/" + maxPoints +" stat points";
	   	
	   	if (result < 0){
	   		document.getElementById("stats").style.color = 'red';
	   	}
	   	else {
	   		document.getElementById("stats").style.color = 'green';
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
       
	   	document.getElementById("skills").innerHTML = "You have spent " + totalSkillPoints + "/" + totalAvailableSkillPoints + " skill points";
	   	
	   	if (totalAvailableSkillPoints-totalSkillPoints < 0){
	   		document.getElementById("skills").style.color = 'red';
	   	}
	   	else {
	   		document.getElementById("skills").style.color = 'green';
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
	$('#statTable').load('index.php?cpage=check-char&nonUI&getRaceStats=' + race, function(){
	    remainingStats();
	    resizeLeft();
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
				
				<br><br>
	            <a href="index.php?page=specifics" style='color:blue' target="_blank">
	                Click here for more information about skills and stats (popup)
	            </a>
	
						
			</div>
				<div id='characterContainer'>
				<?php if (isset($_SESSION['registerFail'])){ echo "<a style=\"color:red;\">" . $_SESSION['registerFail'] . "</a>"; unset($_SESSION['registerFail']); } ?>
				<form role="register" onsubmit="return validateForm()" method="post" name="register-char" action="index.php?cpage=create-character&nonUI">
				<table id="charTable" border="2" style="width:100%">
				 <tbody>
				 	<tr>
				 		<td colspan="4" align="center">
				 			<h2>Character Sheet</h2>
				 		</td>
				 	</tr>
				  <tr>
					<td colspan="2">
					<label>Name</label>
					<a title="Your name has to be 3-12 characters" class="tooltipRight"><span title="">
						<input type="text" id="name" onchange="checkName()" name="name" pattern=".{3,12}" required class='characterFields' >
					</span></a>
					<label id="nameOk"></label>
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
					<select id="race" onchange="getRaceDesc();" name="race" class='characterFields'>
						<option>Human</option>
						<option>Elf</option>
						<option>Dwarf</option>
						<option>Troll</option>
						<option>Undead</option>
						<option>Dryad</option>
					</select>
				  </tr>
				 	<tr>
				 		<td colspan="4" align="center">
				 			<strong>Primary Stats</strong>
				 		</td>
				 	</tr>
				 	  <tr id="statTable">
	
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
	                    	<div id="raceDesc">
	                    		
	                    	</div>
	                    </td>
	                    <td colspan="2" valign="top" align="center" class="featCells">
                            <div id="raceTips">
                            	TEST
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
	                            <input id="attributes" type="number" name="one_handed" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Light Armour</label>
	                        <a title="Allows you to equip yourself with Light Armour" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="light_armour" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">2H Weapons</label>
	                        <a title="Makes you better at fighting with Two-Handed weapons" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="two_handed" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                </td>
	                <td colspan="2" class="skillCells">
	                	<label class="skillLabels">Heavy Armour</label>
	                        <a title="Allows you to equip yourself with Heavy Armour" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="heavy_armour" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Bow</label>
	                        <a title="Makes you better at using a Bow" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="bow" value="0" min="0" max="80"  onchange="remainingSkills()" >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Shield</label>
	                        <a title="Makes you better at defending with a Shield" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="shield" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Crossbow</label>
	                        <a title="Makes you better at using a Crossbow" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="crossbow" value="0" min="0" max="80"  onchange="remainingSkills()" >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Parry</label>
	                        <a title="Increases your skill at blocking with your weapon" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="parry" value="0" min="0" max="80" onchange="remainingSkills()" >
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Finesse</label>
	                        <a title="Finesse slightly increases your chance to critically hit your opponent" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="finesse" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Foul Play</label>
	                        <!--"Dignity and an empty sack is worth the sack - Rule of aquisition 109"--> 
	                        <a title="Allows you to do undignified attacks like throwing sand into your opponents eyes or a kick in the groin" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="foul_play" value="0" min="0" max="80" onchange="remainingSkills()">
	                        </span></a>
	                        
	                        
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Initiative</label>
	                        <a title="Initiative makes you more likely to be the one to act first" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="initiative" value="0" min="0" max="80"  onchange="remainingSkills()" >
	                        </span></a>
	                        
	                        
	                    </td>
	                    <td colspan="2" class="skillCells">
	                    	<label class="skillLabels">Dodge</label>
	                        <a title="A high dodge skill will help you avoid your opponents attacks" class="tooltipRight"><span title=""> 
	                            <input id="attributes" type="number" name="dodge" value="0" min="0" max="80"  onchange="remainingSkills()" >
	                        </span></a>
	                        
	                        
	                    </td>
	                
				  </tbody>
				</table>
				
				<div id="remaining" style='float:left;'>
					<div id="stats">
						You have spent 80/140 stat points
					</div>
					
					<div id="skills">
						You have spent 0/120 skill points
					</div>
				</div>
				<br>
					<button type="submit" class="btn btn-default" style='float:right;'>
						Create your character!
					</button>
				</form>
				</div>