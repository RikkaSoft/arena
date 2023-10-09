<?php 
if ($_SESSION['characterProperties']['healedDate'] == 0){
	echo "You are not mortally wounded.";
}
else{
	?>
	<div style='padding:10px'>
	<div style='text-align:center'>
	<h2>You were mortally wounded in your last battle and need to heal until you can get back into battle again</h2>
		<p>You can either wait the time for your character to heal, pay gold for express healing, or delete your character and start over</p>
		
		
		You will be fully healed at <?php echo $_SESSION['characterProperties']['healedDate']; ?>
		
		<?php 
			$start = strtotime(date('Y-m-d H:i'));
			$stop = strtotime($_SESSION['characterProperties']['healedDate']);
			$diff = ($stop - $start);
			if($diff > 60){
				$diff = $diff/60;
				$cost = $diff;
				if($diff > 60){
					$diff = $diff/60;
					if($diff > 24){
						$diff = $diff/24;
						$timeLeft = round($diff) . " days";
						echo "(" . $timeLeft . " left)";			
					}
					else{
						$timeLeft = round($diff) . " hours";
						echo "(" . $timeLeft . " left)";	
					}
				}
				else{
					$timeLeft = round($diff) . " minutes";
						echo "(" . $timeLeft . " left)";	
				}
			}
			else{
				$cost = 1;
				$timeLeft = "1 minute";
				echo "(" . $timeLeft . " left)";	
			}
			
			
		?>
		<br>
		<a href="index.php?page=express-heal" onclick="return confirm('Are you sure you want to pay <?php echo $cost ?> gold to heal yourself immediately? \nIf you wait <?php echo $timeLeft ?> you will be healed for free')">
			<button style='font-size:18px; padding:6px;padding-right:12px;padding-left:12px;' class='button'>Express healing <?php echo $cost ?> <img src="frontend/design/images/other/gold.png"></button>
		</a>
		<br><br><br>
		<br><br>
		<p>Below is the battlereport in which you got mortally hurt</p>
	</div>
	
	<?php 
	
	include(__ROOT__."/backend/other/get-last.php");
	
	?>
	<br><br><br><br>
	<a href="index.php?cpage=delete-character&nonUI" onclick="return confirm('Are you sure you want to delete your character?')">
			<button style='background:red;color:white;font-size:18px;'>Delete character</button>
		</a>
	</div>
	<?php
	}
?>