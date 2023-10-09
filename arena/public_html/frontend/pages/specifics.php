<div class="mainContent" style='padding:10px'>
    <h2>Specifics for skills & other stuff</h2>
    <p>This is just a temporary page detailing the specifics of stats and skills as well as explaining some of the quirks of the game</p>
    <h4>Skills</h4>
    <?php
        global $conn;
        $sql = "SELECT * FROM modifiers";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result); 
    ?>
    <strong>Skills work as followed:<br><br></strong>
    <strong>Shield</strong> Shields are treated as an armour piece which your opponent can hit. The chance of that is based on the shield and eventual enchants. 
    If you block an attack the damage removed will be a percentage of the total damage, this rate increases with skillpoints.<br>There is also a 30% chance to critically block after a successful block negating 100% of the damage.
    <strong>Dodge</strong> allows you to completely dodge attacks taking no damage what so ever.<br><br>
    <strong>Parry</strong> enables you to deflect an enemys attack, depending on your weapons it will have different effects.
    <br>A dual wielding parry has a 25% chance to take no damage and also counterattack, the counterattack ignores all armour. 75% Of the times you will parry but the enemy hit will go through dealing 50-70% of the original damage.
    <br>A two handed parry has a 40% chance of negating 100% of the damage, and 60% chance to negate 30-50% damage.
    <br>A parry with a 1h weapon and no offhand or a shield will reduce the damage by 30-50%.
    <br><br>
    <strong>Foul play</strong> will make you take no damage from an opponents attack and also throw sand in his/her eyes. Your opponent then has a 50% chance to miss the next turn. 
    <br><br>
    <p>These are the modifiers for all skills</p>
    <ul>
        <li>Shield: <?php echo $row['blockMod']; ?> points block absorption per skillpoint</li>
        <li>Dodge: <?php echo $row['dodgeMod']; ?> points dodge chance per skillpoint</li>
        <li>Parry: <?php echo $row['parryMod']; ?> points parry chance per skillpoint</li>
        <li>Foul Play: <?php echo $row['foul_playMod']; ?> points foul play chance per skillpoint</li>
        <li>Finesse: <?php echo $row['finesseMod']; ?> points critical hit chance per skillpoint</li>
        <li>Weapon skills (1h,2h,ranged) <?php echo $row['weaponSkillDivider']; ?> points hit chance per skillpoint</li>
    </ul>
    <h4>Stats</h4>
    <ul>
        <li>Strength: <?php echo $row['attackMod']*100 ?>% melee damage per point</li>
        <li>Dexterity: <?php echo $row['dexAttackMod']*100 ?>% melee damage per point - <?php echo $row['dexAttackMod']*100 ?>% ranged damage per point</li>
    </ul>
    
    <h4>Maximum modifiers</h4>
    <ul>
        <li>Block cannot go higher than <?php echo $row['maxBlock']; ?>%</li>
        <li>Dodge cannot go higher than <?php echo $row['maxDodge']; ?>%</li>
        <li>Parry cannot go higher than <?php echo $row['maxParry']; ?>%</li>
        <li>Foul Play cannot go higher than <?php echo $row['maxFoul']; ?>%</li>
        <li>Critical hit cannot go higher than <?php echo $row['maxCrit']; ?>%</li>
    </ul>
    <h4>Weight</h4>
    <p></p>All shields and armours (except for boots) has a weight. This is what your total weight affects:</p>
    <ul>
        <li>1 points lower accuracy per weight</li>
        <li><?php echo $row['weightDodgeMod']; ?>% lower dodge per weight</li>
        <li><?php echo $row['weightParryMod']; ?>% lower parry per weight</li>
        <li><?php echo $row['weightFoulMod']; ?>% lower foul per weight</li>
        <!--<li><?php echo $row['weightBlockMod']; ?>% lower block per weight</li>-->
    </ul>
    <h4>Other stuff</h4>
    
    <ul>
        <li>Hit chance over 100% will decrease your opponents evade skills, it can however not lower them more than 50% of their value. For example a 150% hit chance
            will make an opponent with a block chance of 50% actually have a 25% block chance when fighting you. If you would have 200% hit chance it would make no difference
            since your opponent cannot go under 50% of his initial value
        </li>
        <li>
            You can only dodge or block ranged attacks, and dodge have a 50% penalty, so if you have a 20% dodge chance it will be 10% against an arrow/bolt.
        </li>
        <li>
            Crossbow damage do not scale with dexterity or strength
        </li>
        
    </ul>
    
    
    <script>
		$(document).ready(function() 
			{ 
		    	$("#leaderTable").tablesorter({sortInitialOrder: "desc"}); 
		    } 
		); 
	</script>
    <br><br>
    <h4>Enchants</h4>
    <table id='leaderTable'>
    	<thead>
	    	<th>Name</th>
	    	<th>Type</th>
	    	<th>Damage%</th>
	    	<th>Damage+</th>
	    	<th>Acc+</th>
	    	<th>Armour pen</th>
	    	<th>Armour bonus</th>
	    	<th>Weight-</th>
	    	<th>1h</th>
	    	<th>2h</th>
	    	<th>Xbow</th>
	    	<th>Bow</th>
	    	<th>Finesse</th>
	    	<th>Initiative</th>
	    	<th>Parry</th>
	    	<th>Foul</th>
	    	<th>Dodge</th>
	    	<th>Shield</th>
	    	<th>Block%</th>
    	</thead>
    <?php 
    $sql = "SELECT * FROM enchants";
    $result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)){
		echo "<tr>
			<td>" . $row['prefix'] . "/" . $row['suffix'] . "</td>
			<td>" . $row['type'] . "</td>
			<td>" . $row['damageBonusPercent'] . "</td>
			<td>" . $row['damageBonusPoint'] . "</td>
			<td>" . $row['accuracyPercent'] . "</td>
			<td>" . $row['armourPenetration'] . "</td>
			<td>" . $row['armourBonus'] . "</td>
			<td>" . $row['weightReduction'] . "</td>
			<td>" . $row['oneSkill'] . "</td>
			<td>" . $row['twoSkill'] . "</td>
			<td>" . $row['xBowSkill'] . "</td>
			<td>" . $row['bowSkill'] . "</td>
			<td>" . $row['finesseSkill'] . "</td>
			<td>" . $row['initiativeSkill'] . "</td>
			<td>" . $row['parrySkill'] . "</td>
			<td>" . $row['foulSkill'] . "</td>
			<td>" . $row['dodgeSkill'] . "</td>
			<td>" . $row['shieldSkill'] . "</td>
			<td>" . $row['blockPercent'] . "</td>
		</tr>";
	}    
    ?>
    
    </table>
    
</div>
 