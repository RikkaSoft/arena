<?php
global $conn;
if(!isset($conn)){
	   require_once(__ROOT__."/system/details.php");
    }
	require_once(__ROOT__."/backend/character/update-characterSessions.php");
		
if (isset($_SESSION['characterProperties']['id'])){
	if($_SESSION['characterProperties']['gold'] >= 100000){
		require_once(__ROOT__."/backend/accounts/awardIcons.php");
		gold($_SESSION['loggedInId']);
	}
	//XP BAR
	
	$level = $_SESSION['characterProperties']['level'];
	$xp = $_SESSION['characterProperties']['experience'];
	
	$levelup = (5 * pow(2,$level));
    
    if($level > 7){
        $fakexpReduction = 1270;
        $lvlAbove7 = $level-8;
        $levelup = 640*1.5;
        while ($lvlAbove7 > 0){
            $fakexpReduction = $fakexpReduction + ($levelup);
            $levelup = $levelup*1.5;
            $lvlAbove7--;
        }
		if ($level > 8){
			$fakexp = round($xp-$fakexpReduction);
		}
		else{
			$fakexp = round($xp-$fakexpReduction);
		}
		$levelup = round($levelup);
        
    }
    elseif($level > 1){
		$fakexp = $xp - ((5 * pow(2,$level))-10);
        $levelup = (5 * pow(2,$level));
	}
	else {
		$fakexp = $xp;
        $levelup = 10;
	}
	
	if($fakexp >= $levelup){
		$sql = "UPDATE characters SET level = level+1, levelUp = levelUp+1 WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $_SESSION['characterProperties']['id']);
		mysqli_stmt_execute($stmt);
		$levelUp = 1;
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
	}
	
	
	if ($fakexp > $levelup){
		$fakexp = $levelup;
	}
	
	if($_SESSION['characterProperties']['isOnline'] == 1 && $_SESSION['characterProperties']['isOnlineTen']  == 1){
	}
	else{
		$sql = "UPDATE characters SET isOnline=1, isOnlineTen=1 WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $_SESSION['characterProperties']['id']);
		mysqli_stmt_execute($stmt);
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
	}
	
	$hp = $_SESSION['characterProperties']['hp'];
    $vitality = $_SESSION['characterProperties']['vitality'];
    
    $currentHp = ($hp/$vitality)*100;
    if ($currentHp >= 100){
        $futureHp = 0;
    }
    else{
        $futureHp = 40;
        if ($futureHp + $currentHp >= 100){
            $futureHp = 100-$currentHp;
        }

    }
}

?>