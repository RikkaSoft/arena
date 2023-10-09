<?php 
if (isset($_SESSION['characterProperties']['id'])){
    if(!isset($lastHPUpdate)){
    	$query = "SELECT lastHPUpdate FROM configuration";
        $result = mysqli_query($conn,$query);
        $row = mysqli_fetch_assoc($result);
        $lastHPUpdate = $row['lastHPUpdate'];
        $Date = strtotime(date('Y-m-d G:i:s'));
        $hpRegenDate = strtotime($lastHPUpdate. ' + 3 minutes');
        $secs = abs($Date - $hpRegenDate);
        $mins = floor($secs / 60);
        $secs = $secs - $mins * 60;
        if(strlen($secs) == 1){
            $secs = "0" . $secs;
        }
        $timer = $mins . ":" . $secs;
    }


	$rightDivSmall = "";			
    $rightDivSmall .= "<strong>" . $_SESSION['characterProperties']['gold'] . " </strong><img src='frontend/design/images/other/gold.png'><br>";
    $chatIcon = "";
    if (isset($_SESSION['characterProperties']['chatIcon'])){
        if($_SESSION['characterProperties']['chatIcon'] != ""){
    	   $chatIcon = "<a href='index.php?page=playerIcon'><img src='frontend/design/images/chatIcons/" . $_SESSION['characterProperties']['chatIcon'] . "'></a>";
        }
    }
    $name = $chatIcon . " <strong>" . $_SESSION['characterProperties']['name'] . "</strong>";

    if ($level > 2){
        $i = 0;
        if ($_SESSION['characterProperties']['adventureTurns'] == 0){
			$rightDivSmall .= "<strong>0 x </strong><img src='frontend/design/images/other/supply.png'>";
        }
        else{  
			$rightDivSmall .= "<strong>" . $_SESSION['characterProperties']['adventureTurns'] . " x </strong><img src='frontend/design/images/other/supply.png'>";
        }
    }
	
    
    //HP BAR
    if($_SESSION['characterProperties']['healedDate'] == 0){
        $hp = $_SESSION['characterProperties']['hp'];
        $vitality = $_SESSION['characterProperties']['vitality'] + $_SESSION['characterProperties']['vitalityFromGear'];
        
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

        $leftDiv = "HP: " . $hp . "/" . $vitality . " - <span id='timeUntilRecharge' class='timeUntilRecharge'>" . $timer . "</span>";
        
        if($_SESSION['characterProperties']['inTraining'] != 0 || isset($_SESSION['characterProperties']['adventureArea'])){
        	$leftDiv .= " <span>Regen paused</span>";
        }
        $leftDiv .= "<a title=\"You regain 40% of your HP every three minutes\" class=\"tooltipHp\"><span title=\"\">                  
        <div class=\"hpMeter\">
              <span id=\"hpNow\" style=\"width:" . $currentHp . "%\"></span>
              <span id=\"hpThen\" style=\"width:" . $futureHp . "%\"></span>
        </div>
        </span></a>";
	}
	else{
		$leftDiv .= "<a href='index.php?page=no-character'>Character is mortally wounded</a><br>";
	}
    
	if ($_SESSION['characterProperties']['levelUp'] > 0){
		$leftDiv .= "<a class='headerButtonLink ' href=\"index.php?page=level-up\" ><div class='levelUpButton'>Level up!</div></a>";
	}
    else{
        $leftDiv .= "Level: " . $level . " - " . "Xp: " . $fakexp . "/" . $levelup;
        $leftDiv .=
        "<div class=\"meter\">
          <span style=\"width:" . ($fakexp / $levelup)*100 . "%\"></span>
        </div>
        ";
    }
    
    //MOBILE
    echo "<div class='hidden-sm hidden-md hidden-lg' style='width:100%;'><div style='width:40%;float:left;'>
        	" . $name . "
        </div>
	        <div style='width:60%;float:left;'>
	        	<a class=\"headerButtonLink smallButton\" href=\"index.php?page=logout\">Logout</a>
				<a class=\"headerButtonLink smallButton\" style='margin-right:10%;' href=\"javascript:void(0)\" onclick=\"updateChar()\">Update</a>
			</div>
		</div>
        <div class='hidden-sm hidden-md hidden-lg' style='width:60%;float:left;'>
            " . $leftDiv . "
        
    </div>
    <div class='hidden-sm hidden-md hidden-lg' style='width:40%;float:right;text-align:right;font-size:16px;'>
        " . $rightDivSmall . " 
    </div>";
	
?>
<script>
	if (window.matchMedia("(max-width: 768px)").matches) {
	    function updateChar() {
	            $('#characterDivSmall').load('index.php?cpage=get-character&nonUI')
	    };
	    function updateInventory(){
	        $('#characterDivSmall').load('index.php?cpage=update-inventory&nonUI', function(){
	           $('#characterDivSmall').load('index.php?cpage=get-character&nonUI')
	        });
	    }
	}
    
</script>
<?php }else{
	echo "</br>You do not have a character yet.</br>";
	echo "<u><a class='headerButtonLink' href='index.php?page=create-char'>Create Character</a></u>";
}?>