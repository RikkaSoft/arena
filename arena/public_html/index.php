<?php	
	error_reporting(E_ALL);
	session_start();
	require_once("../system/functions.php");
	require_once("../system/config.php");
	if(!isset($_SESSION['loggedIn'])){
		
		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
	    if ($cookie) {
	        list ($user, $token, $mac) = explode(':', $cookie);
	        if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, 'uhfwiehf7y138g13ybrgy0q78eg8gy8q2g'), $mac)) {

	        }
	        else{
		        $sql = "SELECT * FROM users WHERE username = '$user'";
		        $result = mysqli_query($conn,$sql);
		        $row = mysqli_fetch_assoc($result);
		        $usertoken = $row['loginHash'];
		        if (hash_equals($usertoken, $token)) {
		            $username = strtolower($row['username']);
					$_SESSION['loggedIn'] = $username;
					$_SESSION['loggedInId'] = $row['id'];
					$_SESSION['other']['tournamentAdmin'] = $row['tournamentAdmin'];
					$_SESSION['other']['chatToggle'] = $row['chatToggle'];
		            $chatIcon = $row['chatIcon'];
		            $_SESSION['other']['chatIcon'] = $chatIcon;
					if ($row['character_id'] !== 0){
					    $char_id = $row['character_id'];
					    $sql = "UPDATE characters SET chatIcon='$chatIcon' WHERE id='$char_id'";
		                mysqli_query($conn,$sql);
						$_SESSION['charId'] = $row['character_id'];
					}
		            
		            $sql = "UPDATE users SET lastLoginDate=NOW() WHERE id='$row[id]'";
					mysqli_query($conn,$sql);
		        }
		    }
	    }
	}
	if($finals == 1){
		$_SESSION['final'] = $season;
		#echo "test " . $finals . "test";
		#var_dump($finals);
	}
	else{
		unset($_SESSION['final']);
	}
	if(!isset($_GET['nonUI'])){
		if (isset($_SESSION['characterProperties']['deadNext'])){
	        if ($_SESSION['characterProperties']['deadNext'] == 1){
	        	if($_SESSION['characterProperties']['healedDate'] == 0){
	        		require_once(__ROOT__."/backend/character/mortally-wounded.php");
	        	}
				else{
					$start = strtotime(date('Y-m-d H:i'));
					$stop = strtotime($_SESSION['characterProperties']['healedDate']);
					$diff = ($stop - $start);
					$char_id = $_SESSION['characterProperties']['id'];
					if ($diff <= 0){
						$sql = "UPDATE characters SET deadNext=0,healedDate=0,hp=vitality WHERE id='$char_id'";
						mysqli_query($conn,$sql);
					}
				}
			}
	    }
	}
    if ($maintenance == 1 && $_SESSION['loggedIn'] != "rikka"){
        echo "Season will start soon...";
    }
	else {
	    if (isset($_GET['nonUI'])){
	    	if(isset($_SESSION['final'])){
				if(isset($_GET['page'])){
					$allowedArray = array("match_history","view-item","view-character","tournament-admin","your-character","news","market","hallofheroes","leaderboard","online","chatroom","login","register","reset-password","unsubscribe","view-character","view-item","view-battlereport","view-part","view-battlereport-sequence","tavern");
                	if(in_array($_GET['page'], $allowedArray)){
						getPage();
                	}
	            	if(isset($_SESSION['loggedIn']) && $_GET['page'] == "tavern"){
						getPage();
					}
					else{
						require_once("frontend/pages/seasonFinals.php");
					}
                }
                else{
                	getPage();
                }
            }
            else{
            	getPage();
            }
	    }
        else{
            include("frontend/design/templates/ui.php");
        }
	}
?>