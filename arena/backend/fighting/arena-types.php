<?php

	function getStats($type){
		
		if ($type == "1v1"){
			$wins = $_SESSION['characterProperties']['wins'];
			$losses = $_SESSION['characterProperties']['losses'];
			$total = $wins + $losses;
			
			echo "<strong>";
			echo "Your total 1v1 arena matches: " . $total . "<br><br>";
			echo "Your total wins: " . $wins . "<br>";
			echo "Your total losses: " . $losses . "<br><br>";
			
			if($total > 0 ){
				echo "Your win ratio: " . round(($wins / $total) * 100) . "%";
			}
			echo "</strong>";
		}
		else{
			$wins = $_SESSION['characterProperties']['teamWins'];
			$losses = $_SESSION['characterProperties']['teamLosses'];
			$total = $wins + $losses;
			
			echo "<strong>";
			echo "Your total team (2v2 or more) arena matches: " . $total . "<br><br>";
			echo "Your total wins: " . $wins . "<br>";
			echo "Your total losses: " . $losses . "<br><br>";
			
			if($total > 0 ){
				echo "Your win ratio: " . round(($wins / $total) * 100) . "%";
			}
			echo "</strong>";
		}
	}
	function getTypeTraining(){
		
	}
	function getType3v3(){
	?>
	<br><br>
		<h3>Three versus Three</h3>
		
		<form role="fight" method="post" id="fight-arena" name="fight-arena" action="index.php?fpage=queue-arena&nonUI">
		<input type="hidden" name="battleType" value="3">
		<?php 
		$vitality = $_SESSION['characterProperties']['vitality'];
		$lastSurrender = $_SESSION['characterProperties']['battleSurrender'];
			$i = 0.5;
			echo "<strong>When do you wish to surrender?</strong><br><select name=\"yourSurrender\"><br>";
			do {
				if ($i > $lastSurrender){
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i selected>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
				else{
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
			} while ($i >= 0.1);
			echo "<option value=0>0%</option>";
			echo "</select>";
		?>
		<br><br>
		<?php 
		$yourLevel = $_SESSION['characterProperties']['level'];
		switch ($yourLevel){
			case 1:
			case 2:
			case 3:
				$yourGroup = "1-3";
				break;
			case 4:
			case 5:
			case 6:
				$yourGroup = "4-6";
				break;
			case 7:
			case 8:
			case 9:
				$yourGroup = "7-9";
				break;
			default:
				$yourGroup = "10+";
				break;
		}
		echo "<strong>You will be matched against other gladiators in the " . $yourGroup . " level range</strong>";
		
		?>
		<br><br>
		<button type="submit" form="fight-arena" class="button" style='padding-left:15px;padding-right:15px'>Fight!</button>
		</form>
		
		<?php
	}

	function getType2v2(){
	?>
	<br><br>
		<h3>Two versus Two</h3>
		
		<form role="fight" method="post" id="fight-arena" name="fight-arena" action="index.php?fpage=queue-arena&nonUI">
		<input type="hidden" name="battleType" value="2">
		<?php 
		$vitality = $_SESSION['characterProperties']['vitality'];
		$lastSurrender = $_SESSION['characterProperties']['battleSurrender'];
			$i = 0.5;
			echo "<strong>When do you wish to surrender?</strong><br><select name=\"yourSurrender\"><br>";
			do {
				if ($i > $lastSurrender){
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i selected>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
				else{
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
			} while ($i >= 0.1);
			echo "<option value=0>0%</option>";
			echo "</select>";
		?>
		<br><br>
		<?php 
		$yourLevel = $_SESSION['characterProperties']['level'];
		switch ($yourLevel){
			case 1:
			case 2:
			case 3:
				$yourGroup = "1-3";
				break;
			case 4:
			case 5:
			case 6:
				$yourGroup = "4-6";
				break;
			case 7:
			case 8:
			case 9:
				$yourGroup = "7-9";
				break;
			default:
				$yourGroup = "10+";
				break;
		}
		echo "<strong>You will be matched against other gladiators in the " . $yourGroup . " level range</strong>";
		
		?>
		<br><br>
		<button type="submit" form="fight-arena" class="button" style='padding-left:15px;padding-right:15px'>Fight!</button>
		</form>
		
		<?php
	}
	
	function getType1v1(){
		?>
		<br><br>
		<h3>One versus One</h3>
		<form role="fight" method="post" id="fight-arena" name="fight-arena" action="index.php?fpage=queue-arena&nonUI">
		<input type="hidden" name="battleType" value="1">
		<?php 
		$vitality = $_SESSION['characterProperties']['vitality'];
		$lastSurrender = $_SESSION['characterProperties']['battleSurrender'];
			$i = 0.5;
			echo "<strong>When do you wish to surrender?</strong><br><select name=\"yourSurrender\"><br>";
			do {
				if ($i > $lastSurrender){
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i selected>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
				else{
					$si = $i*100;
					$hp = round($vitality * $i);
					echo "<option value=$i>$si% ($hp hp)</option>";
					$i = $i-0.1;
				}
			} while ($i >= 0.1);
			echo "<option value=0>0%</option>";
			echo "</select>";
		?>
		<br><br>
		<strong>Who do you wish to fight?</strong><br>
		<select name="fightLevel">
			<option value=0>Same level as me</option>
		<option value=1>+ 1 level</option>
		<option value=2>+ 2 levels</option>
		<!--<option value=3>Any level higher than you!</option> -->
		</select>
		<br><br>
		<button type="submit" form="fight-arena" class="button" style='padding-left:15px;padding-right:15px'>Fight!</button>
		</form>
		
		<?php
	}
	if (isset($_GET['type'])){
		if ($_GET['type'] == "1v1"){
			getType1v1();
		}
		elseif($_GET['type'] == "2v2"){
			getType2v2();
		}
		elseif($_GET['type'] == "3v3"){
			getType3v3();
		}
		elseif($_GET['type'] == "training"){
			getTypeTraining();
		}
	}
	if(isset($_GET['statType'])){
		getStats($_GET['statType']);
	}
?>