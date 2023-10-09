<?php 
    global $conn;
    if(!isset($conn)){
	   require_once(__ROOT__."/system/details.php");
    }
	require_once(__ROOT__."/backend/character/update-characterSessions.php");
	
	#print_r($_SESSION['characterProperties']);
	if (!isset($_SESSION['characterProperties']['id'])){
		echo "<a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:right;' href=\"index.php?page=logout\">Logout</a>";
		echo "</br>You do not have a character yet.</br>";
		?>
		<u><a class="headerButtonLink" href="index.php?page=create-char">Create Character</a></u>
		<?php
	}
	else{
			#var_dump($_SESSION);
			//ICON AWARD
			if($_SESSION['characterProperties']['gold'] >= 100000){
				require(__ROOT__."/backend/accounts/awardIcons.php");
				gold($_SESSION['loggedInId']);
			}
			
			
            $leftDiv = "";
            $rightDiv = "";			
            $rightDiv .= "<div style='height:30px;'><a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:left;' href=\"index.php?page=your-character\" id='your-character'>Your Character</a>";
            $rightDiv .= "<a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:right;' href=\"index.php?page=logout\">Logout</a></div>";
            $rightDiv .=             "<strong>" . $_SESSION['characterProperties']['gold'] . " </strong><img src='frontend/design/images/other/gold.png'><br>";
			$rightDivSmall = "";			
            $rightDivSmall .= "<div style='height:30px;'><a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:left;' href=\"javascript:void(0)\" onclick=\"updateChar()\">Update</a>";
            $rightDivSmall .= "<a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:right;' href=\"index.php?page=your-character\">My character</a></div>";
            $rightDivSmall .=             "<strong>" . $_SESSION['characterProperties']['gold'] . " </strong><img src='frontend/design/images/other/gold.png'><br>";
            $chatIcon = "";
            if ($_SESSION['characterProperties']['chatIcon'] !== ""){
            	$chatIcon = "<a href='index.php?page=playerIcon'><img src='frontend/design/images/chatIcons/" . $_SESSION['characterProperties']['chatIcon'] . "'></a>";
            }
            $name = $chatIcon . " <strong>" . $_SESSION['characterProperties']['name'] . "</strong><br><br>";
			if($_SESSION['characterProperties']['isOnline'] == 1 && $_SESSION['characterProperties']['isOnlineTen']  == 1){
			}
			else{
				$sql = "UPDATE characters SET isOnline=1, isOnlineTen=1 WHERE id=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $_SESSION['characterProperties']['id']);
				mysqli_stmt_execute($stmt);
				require_once(__ROOT__."/backend/character/update-characterSessions.php");
			}
			
			
			
			
			#echo "<a class=\"headerButtonLink\" href=\"index.php?page=your-character\"><strong>My Character</strong> <br></a>";
			#echo "Name: " . $_SESSION['characterProperties']['name']  . "</br>" . "Gender: " . $_SESSION['characterProperties']['gender']  . "</br>" . "Race: " . $_SESSION['characterProperties']['race']  . 
			
			
			
			$level = $_SESSION['characterProperties']['level'];
			$xp = $_SESSION['characterProperties']['experience'];
			
            if ($level > 2){
                $i = 0;
                if ($_SESSION['characterProperties']['adventureTurns'] == 0){
                    $rightDiv .= "<strong>0 x </strong><img src='frontend/design/images/other/supply.png'>";
					$rightDivSmall .= "<strong>0 x </strong><img src='frontend/design/images/other/supply.png'>";
                }
                else{  
                    $rightDiv .= "<strong>" . $_SESSION['characterProperties']['adventureTurns'] . " x </strong><img src='frontend/design/images/other/supply.png'>";
					$rightDivSmall .= "<strong>" . $_SESSION['characterProperties']['adventureTurns'] . " x </strong><img src='frontend/design/images/other/supply.png'>";
                }
                
                #echo $_SESSION['characterProperties']['adventureTurns'] . "/6<br>";
            }
			
            
            //HP BAR
            if($_SESSION['characterProperties']['healedDate'] == 0){
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
	            $leftDiv .= "HP: " . $hp . "/" . $vitality;
	            if($_SESSION['characterProperties']['inTraining'] != 0 || isset($_SESSION['characterProperties']['adventureArea'])){
	            	$leftDiv .= " <span style='color:#7f0000'>Regen paused(training/adventure)</span>";
	            }
	            $leftDiv .= "<a title=\"You regain 40% of your HP every three minutes\" class=\"tooltipHp\"><span title=\"\">                  
	            <div class=\"hpMeter\">
	                  <span id=\"hpNow\" style=\"width:" . $currentHp . "%\"></span>
	                  <span id=\"hpThen\" style=\"width:" . $futureHp . "%\"></span>
	            </div>
	            </span></a>";
			}
			else{
				$leftDiv .= "<a style='color:red;' href='index.php?page=no-character'>Character is mortally wounded</a><br>";
			}
            
            //XP BAR
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
			
			if ($_SESSION['characterProperties']['levelUp'] > 0){
			    $leftDiv .= "Level: " . $level . " - " . "Xp: " . $fakexp . "/" . $levelup . " ";
				$leftDiv .= "<a class=\"headerButtonLink\" href=\"index.php?page=level-up\" >Level up!</a><br>";
			}
            else{
                $leftDiv .= "Level: " . $level . " - " . "Xp: " . $fakexp . "/" . $levelup;
                $leftDiv .=
                "<div class=\"meter\">
                  <span style=\"width:" . ($fakexp / $levelup)*100 . "%\"></span>
                </div>
                ";
            }
            
            
			
            
            echo "<div class='hidden-xs' style='width:40%;float:left;line-height:14px;'>
                    " . $name . "
                    <a class=\"headerButtonLink\" style='font-size:12px;background-color:#dadada;padding:2px;padding-left:10px;padding-right:10px;color:black;border: 1px solid black;float:left;' href=\"javascript:void(0)\" onclick=\"updateChar()\">Update</a>
                    </div>
                    <div class='hidden-xs' style='bottom:5px;left:5px;position:absolute;width:97%;'>
                        " . $leftDiv . "
                    
                </div>
                <div class='hidden-xs' style='width:60%;float:left;text-align:right;font-size:16px;'>
                    " . $rightDiv . " 
                </div>";
                
            //MOBILE
            echo "<div class='hidden-sm hidden-md hidden-lg' style='width:40%;float:left;text-size:14px;'>
                    " . $name . "</div>
                    <div class='hidden-sm hidden-md hidden-lg' style='bottom:5px;left:5px;position:absolute;width:60%;line-height:100%;'>
                        " . $leftDiv . "
                    
                </div>
                <div class='hidden-sm hidden-md hidden-lg' style='width:60%;float:left;text-align:right;font-size:16px;'>
                    " . $rightDivSmall . " 
                </div>";
                
			
		}
	
?>
<script>
            
        function updateChar() {
                $('#characterDiv').load('index.php?cpage=get-character&nonUI')
        };
        function updateInventory(){
            $('#characterDiv').load('index.php?cpage=update-inventory&nonUI', function(){
               $('#characterDiv').load('index.php?cpage=get-character&nonUI')
            });
        }
       
</script>