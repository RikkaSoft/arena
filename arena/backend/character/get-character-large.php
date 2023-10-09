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
	echo "<div id='characterInformation'>";
	$chatIcon = "";
    if (isset($_SESSION['characterProperties']['chatIcon'])){
        if($_SESSION['characterProperties']['chatIcon'] != ""){
        	$chatIcon = "<a href='index.php?page=playerIcon'><img src='frontend/design/images/chatIcons/" . $_SESSION['characterProperties']['chatIcon'] . "'></a>";
        }
    }
    echo "<div id='nameContainer'>" . $chatIcon . " " . $_SESSION['characterProperties']['name'] . "</div>";
    //HP BAR
    if($_SESSION['characterProperties']['healedDate'] == 0){
    	$rightDiv = "";
        $rightDiv .= "HP: " . $hp . "/" . $vitality . " - <span id='timeUntilRecharge' class='timeUntilRecharge'>" . $timer . "</span>";
        if($_SESSION['characterProperties']['inTraining'] != 0 || isset($_SESSION['characterProperties']['adventureArea'])){
        	$rightDiv .= " <span>Regen paused(training/adventure)</span>";
        }
        $rightDiv .= "<a title=\"You regain 40% of your HP every three minutes\" class=\"tooltipHp\"><span title=\"\">                  
        <div class=\"hpMeter\">
              <span id=\"hpNow\" style=\"width:" . $currentHp . "%\"></span>
              <span id=\"hpThen\" style=\"width:" . $futureHp . "%\"></span>
        </div>
        </span></a>";
	}
	else{
		$rightDiv .= "<a href='index.php?page=no-character'>Character is mortally wounded</a><br>";
	}
	
	if ($_SESSION['characterProperties']['levelUp'] > 0){
		$rightDiv .= "<a class='headerButtonLink' href=\"index.php?page=level-up\" ><div class='levelUpButtonLarge'>Level up!</div></a>";
	}
    else{
        $rightDiv .= "Level: " . $level . " - " . "Xp: " . $fakexp . "/" . $levelup;
        $rightDiv .=
        "<div class=\"meter\" style='margin-bottom:10px;'>
          <span style=\"width:" . ($fakexp / $levelup)*100 . "%\"></span>
        </div>
        ";
    }
    
    $rightDiv .= "<strong>Gold: " . $_SESSION['characterProperties']['gold'] . " x </strong><img src='frontend/design/images/other/gold.png'><br>";
        if ($level > 2){
            $i = 0;
            if ($_SESSION['characterProperties']['adventureTurns'] == 0){
                $rightDiv .= "<strong>Supplies: 0 x </strong><img src='frontend/design/images/other/supply.png'>";
            }
            else{  
                $rightDiv .= "<strong>Supplies: " . $_SESSION['characterProperties']['adventureTurns'] . " x </strong><img src='frontend/design/images/other/supply.png'>";
           }
        }
	
    
    echo $rightDiv;
	echo "</div>";
	echo "<a href='index.php?page=your-character' id='your-character'><div class='characterButton'>Equipment/Stats</div></a>";
	echo "<a href='index.php?page=inventory' id='inventory'><div class='characterButton'>Inventory</div></a>";
	echo "<a href='index.php?page=match_history' id='your-character'><div class='characterButton'>Match History</div></a>";
	echo "<a href='index.php?page=playerIcon' id='your-character'><div class='characterButton'>Player Icon</div></a>";
	
	echo "<a href='index.php?page=specifics' id='specifics'><div class='characterButton'>Help/Information</div></a>";
?>
<script>
       	if (window.matchMedia("(min-width: 768px)").matches) {
	        function updateChar() {
	                $('#largeCharacterInfo').load('index.php?cpage=get-character-large&nonUI')
	        };
	        function updateInventory(){
	            $('#largeCharacterInfo').load('index.php?cpage=update-inventory&nonUI', function(){
	               $('#largeCharacterInfo').load('index.php?cpage=get-character-large&nonUI')
	            });
	        }
            
        }
        
       
</script>
<?php }else{
	echo "</br>You do not have a character yet.</br>";
	echo "<u><a class='headerButtonLink' href='index.php?page=create-char'>Create Character</a></u>";
}?>