<?php

include(__ROOT__.'/backend/guild/guildFunctions.php');
/*
 * ###Rewards###
 * low = 2-4
 * medium = 4-18
 * high = 16-40
   ALL + level - 3
*/

global $xpGain, $goldGain, $itemGain, $imgLoc;
$imgLoc = "frontend/design/images/adventure/";
$goldGain = 0;
$xpGain = 0;

function getInformation($place){
	global $imgLoc;
	if($place == "forest"){
	    
        echo "<div id='adventureEventInfo'>";
		
		#echo "<img src='" . $imgLoc . "smallTreasure.png'>";
		echo "<p>The dark forest is a dreadful place, rats, wolves and the occasional bear lurks in between the trees. <br>
		There are raider infested caverns and lots of loot to be found. 
		<br><br>
		The thick fog makes it difficult to see properly and compasses aren't functioning making it impossible to map out the woods.
	   <br><br>
		For some reason it's very easy to find your way out though..</p>";
		#echo "<p>The dark forest is recommended for weak adventurers (level 3-8)</p>";
		echo "<button id='forestAdventure' class='adventureButtons'>Go on an adventure</button>";
		
		echo "<script>
		$('#forestAdventure').click(function(){
            $(this).prop('disabled',true);
			$('#adventureOutput').load('index.php?apage=adventureFunctions&adventure=1&nonUI');
		});
		</script>";
		
		
		
	}
	elseif($place == "crypt"){
		echo "<div id='areaTitle'>";
		echo "<h3 style='text-align:center;'>The Forsaken Crypt</h3>";
		echo "</div>";
	}
}

function endAdventure($area,$extraSupply,$row){
    global $conn,$xpGain, $goldGain;
    
    
    $name = $_SESSION['characterProperties']['name']; 
    
    $sql = "UPDATE characters SET adventureTurns=adventureTurns+'$extraSupply',adventureRoll=NULL,adventureArea=NULL,adventureChoice=NULL, specificAdventure=NULL,adventureMonster=NULL,adventureMonsterWin=NULL WHERE name='$name'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    
    if ($row == "none"){
            
        }
    else{
        #var_dump($row);
        if ($row['weaponReward'] != "0"){
            insertRewards("weapon",$row['weaponReward']);
        }
        if ($row['goldReward'] != 0){
            $goldGain += $row['goldReward'];
            #insertRewards("gold",$row['goldReward']);
        }
        if ($row['xpReward'] != 0){
            $xpGain += $row['xpReward'];
            #insertRewards("experience",$row['xpReward']);
        }
    }
    if (isset($row['eventReward'])){
        eventRewards($row['eventReward'],1);
    }
    else{
        eventRewards("none",1);
    }
	
    echo "<br><div id='adventureChoices'>";
    
    	include_once(__ROOT__.'/backend/crafting/craftingFunctions.php');
    	echo "<br><br>Would you look at that.. you found crafting parts!<br>";
        getRandomPart("adventure");
        echo "<br>";

        if(mt_rand(0,100) < 20){
            getRandomPart("adventure");
            echo "<br>";

            getRandomPart("adventure");
            echo "<br>";

            getRandomPart("adventure");
        }
        echo "<br><br>";
    
    echo "Your adventure ends here, you can go on another adventure if you have supplies and/or health for it";
    
    
    
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
    newAdventureButton($area);
    echo "</div>";
    exit;
}

function newAdventure(){
    global $conn;
    if ($_SESSION['characterProperties']['adventureTurns'] > 0){
        $name = $_SESSION['characterProperties']['name'];
        
        $sql = "UPDATE characters SET adventureTurns=adventureTurns-1 WHERE name='$name'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        require_once(__ROOT__."/backend/character/update-characterSessions.php");
        
        ###start
        adventureRoll($_GET['adventure'],29);
    }
    else{
    	echo "<div class='adventureEventInfo'>";
        echo "You don't have enough supplies to go out on an adventure<br><br>You recieve one supply every fifteen minutes and one supply when you fight a match in the arena or in a tournament";
        echo "</div>";
        exit;
    }
}



function clearSpecific(){
    global $conn;
    $character = $_SESSION['characterProperties']['name'];
    $sql = "UPDATE characters SET specificAdventure=NULL WHERE name='$character'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
}


function setAdventureRoll($area,$specificId,$roll){
    global $conn;
    
    
    
    $character = $_SESSION['characterProperties']['name'];
    if ($specificId != 0){
        $sql = "UPDATE characters SET specificAdventure='$specificId',adventureArea='$area' WHERE name='$character'";
    }
    else{
        $sql = "UPDATE characters SET adventureArea='$area', adventureRoll='$roll' WHERE name='$character'";
    }
    mysqli_query($conn,$sql)  or die("Error: ".mysqli_error($conn));
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
}

function specificAdventure($area,$specificId,$roll){
    
    setAdventureRoll($area, $specificId, $roll);
    
    
    if ($specificId > 500){
        monsterButtons($area,$specificId,$roll);
    }
    else{
    
        global $conn;
        if ($specificId == 0){
            $sql = "SELECT * FROM adventure WHERE roll='$roll' AND area='$area'";
            $result = mysqli_query($conn, $sql) or die("Error: ".mysqli_error($conn));
            $row = mysqli_fetch_assoc($result);
            $dontShowRest = 0;
            if(isset($row['gotoEvent'])){
                specificAdventure($area,$row['gotoEvent'],$roll);
                $dontShowRest = 1;
                echo "<div id='adventureEventInfo'>";
                    $event = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['event']);
                    echo $event;
                echo "</div>";
            }
            
        }
        else{
            $sql = "SELECT * FROM adventure WHERE specificId='$specificId'";
            $result = mysqli_query($conn, $sql) or die("Error: ".mysqli_error($conn));
            $row = mysqli_fetch_assoc($result);
            $dontShowRest = 0;
        }
        if ($dontShowRest == 0){
            echo "<div id='adventureEventInfo'>";
                $event = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['event']);
                echo $event;
            echo "</div>";
            
            echo "<div id='adventureChoices'>";
            #echo "<a style='font-size:24px;'>What will you do?</a><br><br>";
            if(isset($row['choice1'])){
                $choice = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['choice1']);
                echo "<button id='choice1' class='adventureButtons'>" . $choice . "</button>";
                echo "<script>
                $('#choice1').click(function(){
                    $(this).prop('disabled',true);
                    $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=1&nonUI');
                });
                </script>";
                
                if(isset($row['choice2'])){
                    $choice = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['choice2']);
                    echo "<button id='choice2' class='adventureButtons'>" . $choice . "</button>";
                    echo "<script>
                        $('#choice2').click(function(){
                            $(this).prop('disabled',true);
                            $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=2&nonUI');
                        });
                    </script>";
                    
                    if(isset($row['choice3'])){
                        $choice = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['choice3']);
                        echo "<button id='choice3' class='adventureButtons'>" . $choice . "</button>";
                        echo "<script>
                            $('#choice3').click(function(){
                                $(this).prop('disabled',true);
                                $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=3&nonUI');
                            });
                        </script>";
                        
                        if(isset($row['choice4'])){
                            $choice = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['choice4']);
                            echo "<button id='choice4' class='adventureButtons'>" . $choice . "</button>";
                            echo "<script>
                                $('#choice4').click(function(){
                                    $(this).prop('disabled',true);
                                    $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=4&nonUI');
                                });
                            </script>";
                        }
                    }
                }
                
                
                
            }
            else{
            
                if ($row['over'] == 1){
                    endAdventure($area[0],0,$row);
                }
            }
            setAdventureRoll($area, $specificId, $roll);
        }
    }
}




function adventureRoll($area,$maxRoll){
	global $conn;
    
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
    
    clearSpecific();
    

	$roll = mt_rand(0,$maxRoll);
	$sql = "SELECT * FROM adventure WHERE area = '$area' AND roll = '$roll'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) == 0){
		if (strlen($roll) == 1){
			$roll = 0;
		}
		else{
		$roll = substr($roll,0,1) * 10;
		}
		
		$sql = "SELECT * FROM adventure WHERE area = '$area' AND roll = '$roll'";
		$result = mysqli_query($conn, $sql);
	}
	
	$row = mysqli_fetch_assoc($result);
	echo "<div id='adventureEventInfo'>";
        $event = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['event']);
        echo $event;
    echo "</div>";
    
	echo "<div id='adventureChoices'>";
    
    if(isset($row['gotoEvent'])){
        specificAdventure($area,$row['gotoEvent'],$roll);
    }
    else{
        if(isset($row['choice1'])){
            echo "<button id='choice1' class='adventureButtons'>" . $row['choice1'] . "</button>";
            echo "<script>
            $('#choice1').click(function(){
                $(this).prop('disabled',true);
                $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=1&nonUI');
            });
            </script>";
            
            if(isset($row['choice2'])){
                echo "<button id='choice2' class='adventureButtons'>" . $row['choice2'] . "</button>";
                echo "<script>
                    $('#choice2').click(function(){
                        $(this).prop('disabled',true);
                        $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=2&nonUI');
                    });
                </script>";
                
                if(isset($row['choice3'])){
                    echo "<button id='choice3' class='adventureButtons'>" . $row['choice3'] . "</button>";
                    echo "<script>
                        $('#choice3').click(function(){
                            $(this).prop('disabled',true);
                            $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=3&nonUI');
                        });
                    </script>";
                    
                    if(isset($row['choice4'])){
                        echo "<button id='choice4' class='adventureButtons'>" . $row['choice4'] . "</button>";
                        echo "<script>
                            $('#choice4').click(function(){
                                $(this).prop('disabled',true);
                                $('#adventureOutput').load('index.php?apage=adventureFunctions&choice=4&nonUI');
                            });
                        </script>";
                    }
                }
            }
    		
    		setAdventureRoll($area, 0, $roll);
    	}
    	else{
        	#eventRewards($row['eventReward'],0);
        	endAdventure($area[0],0,$row);
    	}	
	}
}

/*
function writeChoice($choice){
    global $conn;
    $name = $_SESSION['characterProperties']['name'];
    $sql = "UPDATE characters SET adventureChoice='$choice' WHERE name='$name'";
    mysqli_query($conn,$sql) or die("Error: ".mysqli_error($conn));
}
 */
 
function choice($choice){
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
	global $conn;
	$roll = $_SESSION['characterProperties']['adventureRoll'];
	$area = $_SESSION['characterProperties']['adventureArea'];
	if (isset($_SESSION['characterProperties']['specificAdventure'])){
	   $specificAdventure = $_SESSION['characterProperties']['specificAdventure'];
	   $sql = "SELECT * FROM adventure WHERE specificId='$specificAdventure'";
	}
    else{
        $sql = "SELECT * FROM adventure WHERE roll='$roll' AND area='$area'";
    }
	
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	if($choice == 1){
		$requirement = $row['choice1Req'];
		$returnTrue = $row['choice1ReturnTrue'];
		$returnTrueText = $row['choice1ReturnTrueText'];
		$returnFalse = $row['choice1ReturnFalse'];
		$returnFalseText = $row['choice1ReturnFalseText'];
	}
	elseif($choice == 2){
		$requirement = $row['choice2Req'];
		$returnTrue = $row['choice2ReturnTrue'];
		$returnTrueText = $row['choice2ReturnTrueText'];
		$returnFalse = $row['choice2ReturnFalse'];
		$returnFalseText = $row['choice2ReturnFalseText'];
	}
	elseif($choice == 3){
		$requirement = $row['choice3Req'];
		$returnTrue = $row['choice3ReturnTrue'];
		$returnTrueText = $row['choice3ReturnTrueText'];
		$returnFalse = $row['choice3ReturnFalse'];
		$returnFalseText = $row['choice3ReturnFalseText'];	
	}
	elseif($choice == 4){
		$requirement = $row['choice4Req'];
		$returnTrue = $row['choice4ReturnTrue'];
		$returnTrueText = $row['choice4ReturnTrueText'];
		$returnFalse = $row['choice4ReturnFalse'];
		$returnFalseText = $row['choice4ReturnFalseText'];
      	}		
	if($requirement == "none"){
		if(strlen($returnTrueText) > 0){
			echo "<div id='adventureEventInfo'>";
			  echo $returnTrueText;
		    echo "</div>";
		}
		$exploded = explode(":",$returnTrue);
		if ($exploded[0] == "A"){
		    # A NEW ROLL
		    $explodedAreaRoll = explode("-",$exploded[1]);
            adventureRoll($explodedAreaRoll[0],$explodedAreaRoll[1]);
        }
        else{
            if ($exploded[0] == "E"){
                specificAdventure($area,$exploded[1],0);
                
            }
            elseif($exploded[0] == "D"){
                decreaseHealth($exploded[1]);
                #var_dump($exploded);
                if (isset($exploded[2])){
                    if($exploded[2] == "E"){
                        specificAdventure($area,$exploded[3],0);
                    }
                    else{
                        endAdventure($area[0],0,$row);
                    }
                }
                else{
                    endAdventure($area[0],0,$row);
                }
            }
            elseif($exploded[0] == "M"){
                monsterButtons($area,$exploded[1],0);
            }
            elseif($exploded[0] == "end"){
                endAdventure($area[0],0,$row);
            }
			elseif($exploded[0] == "U"){
				uniqueItemChest($exploded[1]);
				endAdventure($area[0],0,$row);
			}
	   }
    }
	else{
	    $countReq = explode(",", $requirement);
		if(strpos($countReq[0],"GC") !== false){
			$exp = explode(":",$countReq[0]);
			if($_SESSION['characterProperties']['gold'] >= $exp[1]){
				$countReq[0] = $countReq[1];
				$countReq[1] = $countReq[2];
			}
			else{
				$countReq[0] = "alwaysFail";
			}
		}
		if ($countReq[0] == "alwaysFail"){
			$succeedReq = 1;
			$succeeds = 0;
		}
		elseif ($countReq[0] == "RR"){
            $succeedReq = 1;
                $explodedRoll = explode(":",$countReq[1]);
				
				$randRoll = rand(1,$explodedRoll[1]);
				#echo "orig roll: " . $randRoll . "  ";
				$guildId = $_SESSION['characterProperties']['guild'];
				if ($guildId != 0){
					$sql = "SELECT effects FROM guilds WHERE id='$guildId'";
					$result = mysqli_query($conn,$sql);
					$gRow = mysqli_fetch_assoc($result);
					$gEffects = getGuildEffects($gRow['effects'],0);
					
					if ($gEffects['adventureRolls'] > 0){
						$randRoll = $randRoll - $gEffects['adventureRolls'];
						if($randRoll < 0){
							$randRoll = 0;
						}
					}
				}
				#echo $randRoll . " - " . $explodedRoll[0];
                if($randRoll <= $explodedRoll[0]){
                	#echo "hi";
                    $succeeds = 1;
                }
                else{
                    $succeeds = 0;
                }
        }
        else{
            if ($countReq[0] == "all"){
                $succeedReq = count($countReq)-1;
            }
            else{
                $succeedReq = $countReq[0];
            }
            #echo $succeedReq;
            $i = 1;
            $succeeds = 0;
    	    while($i < count($countReq)){
    	        $splitReq = explode(":", $countReq[$i]);
                
                if ($splitReq[0] == "S"){
                $reqStat = "strength";
                }
                elseif ($splitReq[0] == "D"){
                    $reqStat = "dexterity";
                }
                elseif ($splitReq[0] == "V"){
                    $reqStat = "vitality";
                }
                elseif ($splitReq[0] == "I"){
                    $reqStat = "intellect";
                }
                elseif ($splitReq[0] == "one_handed"){
                	$reqStat = "one_handed";
                }
                elseif ($splitReq[0] == "two_handed"){
                	$reqStat = "two_handed";
                }
                elseif ($splitReq[0] == "bow"){
                	$reqStat = "bow";
                }
                elseif ($splitReq[0] == "crossbow"){
                	$reqStat = "crossbow";
                }
                elseif ($splitReq[0] == "finesse"){
                	$reqStat = "finesse";
                }
                elseif ($splitReq[0] == "initiative"){
                	$reqStat = "initiative";
                }
                elseif ($splitReq[0] == "shield"){
                	$reqStat = "shield";
                }
                elseif ($splitReq[0] == "parry"){
                	$reqStat = "parry";
                }
                elseif ($splitReq[0] == "foul_play"){
                	$reqStat = "foul_play";
                }
                elseif ($splitReq[0] == "dodge"){
                	$reqStat = "dodgeSkill";
                }
                
                $chance = ($_SESSION['characterProperties'][$reqStat] / $splitReq[1]) * 100;
				
				$guildId = $_SESSION['characterProperties']['guild'];
				if ($guildId != 0){
					$sql = "SELECT effects FROM guilds WHERE id='$guildId'";
					$result = mysqli_query($conn,$sql);
					$gRow = mysqli_fetch_assoc($result);
					$gEffects = getGuildEffects($gRow['effects'],0);
					
					if ($gEffects['adventureRolls'] > 0){
						$chance = $chance + $gEffects['adventureRolls'];
					}
				}
				#echo $chance;
				
				
				if ($chance >= 95){
					$chance = 95;
				}
				elseif($chance <= 5){
					$chance = 5;
				}
				$roll = mt_rand(1, 100);
				#echo "chance to suceed: " . $chance . " roll: " . $roll;;
                if($roll <= $chance){
               		if($reqStat == "shield"){
               		    if (strpos($_SESSION['characterProperties']['left_hand'], 'Shield') !== false){
               				#echo "succeeded " . $reqStat . "<br>";
               				$succeeds++;
               			}
                        else{
                            #echo "no shield equipped <br>";
                        }
               			
               		}
               		else{
               			#echo "succeeded " . $reqStat . "<br>";
               			$succeeds++;
               		}
                   	
                }
                else{
                    #echo "Failed " . $reqStat . "<br>";
                }
                $i++;
    	    }
	    }
		
		
		
		//SUCCESS
		if($succeeds >= $succeedReq){
			if(strlen($returnTrueText) > 0){
				echo "<div id='adventureEventInfo'>";
				  echo $returnTrueText;
			    echo "</div>";
			}
			$exploded = explode(":",$returnTrue);
            if ($exploded[0] == "A"){
                # A NEW ROLL
                $explodedAreaRoll = explode("-",$exploded[1]);
                adventureRoll($explodedAreaRoll[0],$explodedAreaRoll[1]);
            }
            else{
                if ($exploded[0] == "E"){
                    specificAdventure($area,$exploded[1],0);
                }
                elseif($exploded[0] == "D"){
                    decreaseHealth($exploded[1]);
                    if (isset($exploded[2])){
                        if($exploded[2] == "E"){
                            specificAdventure($area,$exploded[3],0);
                        }
                        else{
                            endAdventure($area[0],0,$row);
                        }
                    }
                    else{
                        endAdventure($area[0],0,$row);
                    }
                }
                elseif($exploded[0] == "M"){
                    monsterButtons($area,$exploded[1],0);
                }
                elseif($exploded[0] == "end"){
                    #eventRewards($row['eventReward'],0);
                    endAdventure($area[0],0,$row);
                }
            }
		}
		
		//FAILURE
		else{
			if(strlen($returnFalseText) > 0){
				echo "<div id='adventureEventInfo'>";
				  echo $returnFalseText;
			    echo "</div>";
			}
			$exploded = explode(":",$returnFalse);
            if ($exploded[0] == "A"){
                # A NEW ROLL
                $explodedAreaRoll = explode("-",$exploded[1]);
                adventureRoll($explodedAreaRoll[0],$explodedAreaRoll[1]);
            }
            else{
                if ($exploded[0] == "E"){
                    specificAdventure($area,$exploded[1],0);
                }
                elseif($exploded[0] == "D"){
                    decreaseHealth($exploded[1]);
                    if (isset($exploded[2])){
                        if($exploded[2] == "E"){
                            specificAdventure($area,$exploded[3],0);
                        }
                        else{
                            endAdventure($area[0],0,$row);
                        }
                    }
                    else{
                        endAdventure($area[0],0,$row);
                    }
                }
                elseif($exploded[0] == "M"){
                    monsterButtons($area,$exploded[1],0);
                }
                elseif($exploded[0] == "end"){
                    endAdventure($area[0],0,$row);
                }
           }
	
        }
    }
}

function uniqueItemChest($type){
	global $conn;
	
	if($type === "armour"){
		$sql = "SELECT * FROM armours WHERE uniqueEvent='uniqueItemChest' ORDER BY rarity";
		$type = "specific,armours,";
	}
    else if($type === "ranged"){
        $sql = "SELECT * FROM weapons WHERE uniqueEvent='uniqueItemChest' AND (type='crossbow' OR type='bow') ORDER BY rarity";
        $type = "specific,weapons,";
    }
	else{
		$sql = "SELECT * FROM weapons WHERE uniqueEvent='uniqueItemChest' AND type='$type' ORDER BY rarity";
		$type = "specific,weapons,";
	}
	
	$result = mysqli_query($conn,$sql);
	$itemArray = array();
	while($row = mysqli_fetch_assoc($result)){
		$itemArray[$row['item_type']][] = $row;
	}
	$keys = array_keys($itemArray);
	$itemArray = $itemArray[$keys[mt_rand(0,count($keys)-1)]];
	
	$match = 0;
	$matched = 0;
	$roll = mt_rand(1,100);
	foreach($itemArray as $item){
		$match += $item['rarity'];
		if($match >= $roll){
			insertRewards("weapon", $type . $item['id']);
			$matched = 1;
			break;
		}
	}
	if($matched == 0){
		echo "Unfortunatly the chest only contained gold and broken dreams...";
	}
}



function monsterButtons($area, $specificId,$roll){
    global $conn;
    setAdventureRoll($area, $specificId, $roll);
    $quantity = 1;
    $sql = "SELECT * FROM adventure WHERE specificId='$specificId'";
    $result = mysqli_query($conn,$sql) or die("Error: ".mysqli_error($conn));
    $row = mysqli_fetch_assoc($result);
    #var_dump($row);
    $optional = $row['enemyOptional'];
    if(isset($row['enemyQuantity'])){
    	$enemyQuantity = $row['enemyQuantity'];
	}
    
    echo "<div id='adventureEventInfo'>";
        $event = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['event']);
        echo $event;
    echo "</div>";
    
    if (isset($_SESSION['characterProperties']['adventureMonster'])){
    	if (strpos($_SESSION['characterProperties']['adventureMonster'], ':') !== false) {
		    $exp = explode(":",$_SESSION['characterProperties']['adventureMonster']);
			$quantity = $exp[1];
		}
        $monsterId = $_SESSION['characterProperties']['adventureMonster'];
        $sql = "SELECT * FROM npc WHERE id='$monsterId'";
        $result = mysqli_query($conn,$sql)  or die("Error: ".mysqli_error($conn));
        $row = mysqli_fetch_assoc($result);
        $enemy = $row['name'];
    }
    else{
        if (isset($row['enemy'])){
            $monsterId = $row['enemy'];
            if (strpos($monsterId, ':') !== false) {
			    $exp = explode(":",$enemy);
				$quantity = $exp[1];
				$monsterId = $exp[0];
			}
            $sql = "SELECT * FROM npc WHERE id='$monsterId'";
            $result = mysqli_query($conn,$sql)  or die("Error: ".mysqli_error($conn));
            $row = mysqli_fetch_assoc($result);
            $enemy = $row['name'];
        }
        else{
            $monsterLevel = explode("-", $row['enemyLevel']);
            $sql = "SELECT * FROM npc WHERE level BETWEEN '$monsterLevel[0]' AND '$monsterLevel[1]'";
            
            $result = mysqli_query($conn,$sql)  or die("Error: ".mysqli_error($conn));
            $rows = mysqli_fetch_all($result);
            
            $targetMonster = mt_rand(0, count($rows)-1);
            $target = $rows[$targetMonster];
            $monsterId = $target[0];
                        
            $sql = "SELECT * FROM npc WHERE id = '$monsterId'";
            $result = mysqli_query($conn,$sql)  or die("Error: ".mysqli_error($conn));
            $row = mysqli_fetch_assoc($result);
            $enemy = $row['name'];
        }
		if(isset($enemyQuantity)){
			$explode = explode("-", $enemyQuantity);
			$quantity = mt_rand($explode[0],$explode[1]);
			$monsterId = $monsterId . ":" . $quantity;
		}
		
		$name = $_SESSION['characterProperties']['name'];
		$sql = "UPDATE characters SET adventureMonster='$monsterId' WHERE name='$name'";
        mysqli_query($conn,$sql);
    }    
    
    if ($optional == 0){
        $yourSpeed = ($_SESSION['characterProperties']['level'] * 10) + $_SESSION['characterProperties']['dexterity'] + ($_SESSION['characterProperties']['initiative']/2);
        $opponentSpeed = ($row['level'] * 10) + $row['dexterity']*2;
        
        if ($yourSpeed < $opponentSpeed){
            $success = " style='color:#CC0000'\">Attempt to escape (success rate: Low)";
        }
        elseif($yourSpeed > $opponentSpeed*0.7){
            $success = " style='color:blue'\">Attempt to escape (success rate: Medium)";
        }
        elseif($yourSpeed > ($opponentSpeed * 2)){
            $success = " style='color:green'\">Attempt to escape (success rate: High)";
        }
    }
    else{
        #echo "<br>You need to defeat this enemy to continue your adventure, what will you do?";
    }
    
    require_once(__ROOT__."/backend/adventure/get-creature-adventure.php");
    
    echo "<div id='adventureEventInfo'>";
        getCreature($enemy,1,$quantity);
    echo "</div>";
    
    echo "<div id='adventureChoices'>";
    
    
    $i = $_SESSION['characterProperties']['hp'] / $_SESSION['characterProperties']['vitality'];
    $i = substr($i,0,strpos($i,".") + 2);
    if ($i > 0.5){
        $i = 0.5;
    }
    echo "When do you want to <strong>try</strong> to surrender? (If you cannot escape you will be forced to fight)<br><select id=\"adventureSurrender\" name=\"yourSurrender\">";
    
    
    
    if ($i != 0.0){
        $i = $i * 10;
        while($i > 0){
            if ($i >= ($_SESSION['characterProperties']['adventureSurrender']*10)){
                $si = $i*10;
                $hp = round(($_SESSION['characterProperties']['vitality'] * ($i/10)));
                echo "<option value=" . ($i/10) . " selected>$si% ($hp hp)</option>";
                $i = $i-1;
                
            }
            else{
                $si = $i*10;
                $hp = round(($_SESSION['characterProperties']['vitality'] * ($i/10)));
                echo "<option value=" . ($i/10) . ">$si% ($hp hp)</option>";
                $i = $i-1;
            }
        }
    }

    echo "<option value=0>0% (If you lose, you die)</option>";
    echo "</select><br><br><br>";
    
    #var_dump($i);
    
    echo "<button id='fightMonster' class='adventureButtons'>Fight!</button>";
        echo "<script>
            $('#fightMonster').click(function(){
                $(this).prop('disabled',true);
                var surrValue = $('#adventureSurrender').val();
                var enemy = '$enemy';
                $('#adventureOutput').load('index.php?apage=adventureFunctions&nonUI&insertSurrender=' + surrValue + '&enemy=' + enemy, function(){
                    $('#adventureOutput').load('index.php?apage=adventureFunctions&fightMonster&nonUI');
                });
            });
        </script>";
        
        if ($optional == 0){
        echo "<button id='escapeMonster' class='adventureButtons'" . $success . "</button>";
        echo "<script>
            $('#escapeMonster').click(function(){
                $(this).prop('disabled',true);
                $('#adventureOutput').load('index.php?apage=adventureFunctions&escapeMonster&nonUI');
            });
        </script>";
        
        }
        else{
        echo "<button id='walkAway' class='adventureButtons'>Walk Away</button>";
        echo "<script>
            $('#walkAway').click(function(){
                $(this).prop('disabled',true);
                $('#adventureOutput').load('index.php?apage=adventureFunctions&walkAway&nonUI');
            });
        </script>";
        }
        
        echo "</div>";     
        
}

function afterBattle($outcome){
    global $conn;
    
    if ($outcome['dead'] == 1){
        
    }
    else{
        unset($_SESSION['characterProperties']['adventureMonster']);
            
        $roll = $_SESSION['characterProperties']['adventureRoll'];
        $area = $_SESSION['characterProperties']['adventureArea'];
        $choice = $_SESSION['characterProperties']['adventureChoice'];
        if (isset($_SESSION['characterProperties']['specificAdventure'])){
           $specificAdventure = $_SESSION['characterProperties']['specificAdventure'];
           $sql = "SELECT * FROM adventure WHERE specificId='$specificAdventure'";
        }
        else{
            $sql = "SELECT * FROM adventure WHERE roll='$roll' AND area='$area'";
        }
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
    
        if ($outcome['win'] == 1){
            $winText = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['winText']);
            echo $winText;
            if ($row['win'] != "end"){
                if (isset($row['enemyBonus'])){
                    eventRewards($row['enemyBonus'],0);
                }
                $exploded = explode(":",$row['win']);
                
                if ($exploded[0] == "E"){
                    specificAdventure($area,$exploded[1],0);
                    
                }
                elseif($exploded[0] == "A"){
                    $explodedArea = explode("-",$exploded[1]);
                    adventureRoll($explodedArea[0], $explodedArea[1]);
                }
                elseif($exploded[0] == "D"){
                    decreaseHealth($exploded[1]);
                    if (isset($exploded[2])){
                        if($exploded[2] == "E"){
                            specificAdventure($area,$exploded[3],0);
                        }
                        else{
                            endAdventure($area[0],0,$row);
                        }
                    }
                    else{
                        endAdventure($area[0],0,$row);
                    }
                }
                elseif($exploded[0] == "M"){
                    monsterButtons($area,$exploded[1],0);
                }
                elseif($exploded[0] == "end"){
                    endAdventure($area[0],0,$row);
                }
            }
            else{
                if (isset($row['enemyBonus'])){
                    eventRewards($row['enemyBonus'],0);
                }
                endAdventure($area[0],0,$row);
            }
            
        }
        else{
            $lossText = str_replace("[yourName]", $_SESSION['characterProperties']['name'], $row['lossText']);
			if(strlen($lossText) > 0){
				echo "<div id='adventureEventInfo'>";
	            	echo $lossText;
				echo "</div>";
			}
            if ($row['loss'] != "end"){
                $exploded = explode(":",$row['loss']);
                if ($exploded[0] == "E"){
                    specificAdventure($area,$exploded[1],0);
                    
                }
                elseif($exploded[0] == "A"){
                    $explodedArea = explode("-",$exploded[1]);
                    adventureRoll($explodedArea[0], $explodedArea[1]);
                }
                elseif($exploded[0] == "D"){
                    decreaseHealth($exploded[1]);
                    if (isset($exploded[2])){
                        if($exploded[2] == "E"){
                            specificAdventure($area,$exploded[3],0);
                        }
                        else{
                            endAdventure($area[0],0,$row);
                        }
                    }
                    else{
                        endAdventure($area[0],0,$row);
                    }
                }
                elseif($exploded[0] == "M"){
                    monsterButtons($area,$exploded[1],0);
                }
                elseif($exploded[0] == "end"){
                    endAdventure($area[0],0,$row);
                }
            }
            else{
                endAdventure($area[0],0,$row);
            }
        }
    }
}

function newAdventureButton($area){
	echo "<br><br>";
	echo "<button id='forestAdventure' class='adventureButtons'>Go on another adventure</button>";
	
	echo "<script>
	$('#forestAdventure').click(function(){
        $(this).prop('disabled',true);
		$('#adventureOutput').load('index.php?apage=adventureFunctions&adventure=" . $area . "&nonUI');
	});
	</script>";
}


function eventRewards($rewardRow,$end){
	global $xpGain, $goldGain, $itemGain;
    if ($rewardRow != "none"){
    	$rewards = explode(",", $rewardRow);
    	foreach ($rewards as $reward){
    		$rewardSplit = explode(":",$reward);
    		getReward($rewardSplit[0],$rewardSplit[1],$end);
    	}
    }
	echo "<div id='adventureEventInfo'>";
    if ($end == 1){
    	if ($xpGain != 0){
    		if ($goldGain != 0){
    			echo "<br>You gained a total of <a style='color:greenyellow;font-weight: bold;'>" . $xpGain . " experience</a> and <a style='color:orange;font-weight: bold;'>" . $goldGain . " gold</a> on this adventure.";
                insertRewards("experience",$xpGain);
                insertRewards("gold",$goldGain);
    		}
    		else{
    			echo "<br>You gained a total of <a style='color:greenyellow;font-weight: bold;'>" . $xpGain . " experience</a> on this adventure.";
    			insertRewards("experience",$xpGain);
    		}
    	}
    	elseif($goldGain != 0){
    		echo "<br>You gained a total of <a style='color:#998100;font-weight: bold;'>" . $goldGain . " gold</a> on this adventure.";
    		insertRewards("gold",$goldGain);
    	}
		if($xpGain > 0){
			updateGuildXpAdventure($_SESSION['characterProperties']['guild'],$xpGain);
		}
    	//TO REFRESH CHARACTERINFO
        echo"<script>
            window.onload = updateChar();
        </script>";
    }
	echo "</div>";
}

function updateGuildXpAdventure($guildId,$amount){
		global $conn;
		
		if($guildId != 0){
			$sql = "UPDATE guilds SET experience=experience+'$amount' WHERE id='$guildId'";
			mysqli_query($conn,$sql);
		}
	}

function getReward($type,$quantity,$end){
	global $goldGain, $xpGain, $itemGain;
	if ($type == "gold" || $type == "xp"){
		if ($quantity == "low"){
			$value = mt_rand(2,4 + ($_SESSION['characterProperties']['level']-3));
		}
		elseif ($quantity == "medium"){
			$value = mt_rand(4,18) + ($_SESSION['characterProperties']['level']-3);
		}
		elseif ($quantity == "high"){
			$value = mt_rand(16,40) + ($_SESSION['characterProperties']['level']-3);
		}
		
		if($type == "gold"){
			$goldGain += $value+6;
		}
		elseif($type == "xp"){
			$xpGain += $value;
		}
	}
    elseif($type == "D"){
        decreaseHealth($quantity);
        //TO REFRESH CHARACTERINFO
        echo
        "<script>
            window.onload = updateChar();
        </script>";
    }
    elseif($type == "GD"){
        decreaseGold($quantity);
        //TO REFRESH CHARACTERINFO
        echo
        "<script>
            window.onload = updateChar();
        </script>";
    }
}
function decreaseGold($quantity){
    global $conn;
    $name = $_SESSION['characterProperties']['name'];
    
    if($quantity > $_SESSION['characterProperties']['gold']){
        $quantity = $_SESSION['characterProperties']['gold'];
    }
    else{
        
    }
    if($quantity > 0){
        $sql = "UPDATE characters SET gold=gold-'$quantity' WHERE name='$name'";
        mysqli_query($conn,$sql);
    }
    echo "<strong>You lost " . $quantity . " gold</strong><br>";
}
function decreaseHealth($percent){
    global $conn;
	echo "<div class='adventureEventInfo'>";
    $name = $_SESSION['characterProperties']['name'];
    if($percent == 100){
        $sql = "UPDATE characters SET hp=1 WHERE name='$name'";
        $result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
        $area = $_SESSION['characterProperties']['adventureArea'];
        endAdventure($area[0],0,"none");
    }
    else{
        $currentHp = $_SESSION['characterProperties']['hp'];
        $maxHp = $_SESSION['characterProperties']['vitality'];
        $lostHp = round($maxHp*($percent/100));
        $futureHp = $currentHp-$lostHp;
        if ($futureHp <= 0){
            $sql = "UPDATE characters SET hp=1 WHERE name='$name'";
            echo "<a><br>You lost " . $lostHp . " hp and nearly died...</a><br><br>";
            $result = mysqli_query($conn,$sql) or die(mysqli_error($conn));
            
            $area = $_SESSION['characterProperties']['adventureArea'];
            endAdventure($area[0],0,"none");
            
        }
        else{
            $sql = "UPDATE characters SET hp='$futureHp' WHERE name='$name'";
            echo "<a><br>You lost " . $lostHp . " hp...</a><br><br>";
            mysqli_query($conn,$sql) or die(mysqli_error($conn));
        }
    }
    echo "</div>";	
    
    
}

function insertRewards($type,$quantity){
	global $conn;
    #echo $quantity;
	if($type != "weapon"){
		$name = $_SESSION['characterProperties']['name'];
		$sql = "UPDATE characters SET $type=$type+'$quantity' WHERE name='$name'";
		#echo $sql;
		mysqli_query($conn,$sql);
		require_once(__ROOT__."/backend/character/update-characterSessions.php");
	}
	else{
        $randomItem = 0;
        $foundItem = 1;
        #echo $quantity;
        $split = explode(",",$quantity);
        if($split[0] == "type"){
            //1h,2h,bow,xbow,armour types
            
            $type = $split[1];
            $minPrice = $split[2];
            $maxPrice = $split[3];
            
            if($type == "Light Armour" || $type == "Heavy Armour"){
                $tableType = "armours";
            }
            else{
                $tableType = "weapons";
            }
            $randomItem = 1;
            $sql = "SELECT id FROM $tableType WHERE price >= '$minPrice' AND price <= '$maxPrice' AND type='$type' AND userCrafted=0 ORDER BY RAND() LIMIT 1";
        }
        elseif($split[0] == "itemType"){
            //swords,axes,battleaxes etc.
            
            if($split[1] == "arms" || $split[1] == "heads" || $split[1] == "chests" || $split[1] == "feets" || $split[1] == "legs"){
                $tableType = "armours";
            }
            else{
                $tableType = "weapons";
            }
            $type = $split[1];
            $minPrice = $split[2];
            $maxPrice = $split[3];
            
            $sql = "SELECT id FROM $tableType WHERE price >= '$minPrice' AND price <= '$maxPrice' AND item_type='$type' AND userCrafted=0 ORDER BY RAND() LIMIT 1";
            $randomItem = 1;
        }
        elseif($split[0] == "random"){
            if($split[1] == "all"){
                if (rand(0,1) == 1){
                    $tableType = "weapons";
                }
                else{
                    $tableType = "armours";
                }
            }
            else{
                $tableType = $split[1];
            }
            $minPrice = $split[2];
            $maxPrice = $split[3];
            $randomItem = 1;
            $sql = "SELECT id FROM $tableType WHERE price >= '$minPrice' AND price <= '$maxPrice' AND userCrafted=0 ORDER BY RAND() LIMIT 1";
        }
        elseif($split[0] == "specific"){
            $tableType = $split[1];
            $quantity = $split[2];
        }
        
        if (isset($maxPrice)){
            $levelMaxPrice = $_SESSION['characterProperties']['level'] * 150;
            if ($_SESSION['characterProperties']['level'] > 10){
                $levelMaxPrice = $levelMaxPrice + 2000;
            }
            if($levelMaxPrice < $maxPrice){
                $maxPrice = $levelMaxPrice;
            }
        }
        if($randomItem == 1){
            #echo "<br>" . $sql;
            $result = mysqli_query($conn,$sql);
            $ids = array();
            while($row = mysqli_fetch_assoc($result)){
                array_push($ids,$row['id']);
            }
            if (count($ids) == 0){
                echo "Unfortunatly, the item was just an illusion";
                $foundItem = 0;
            }
            else{
                $quantity = $ids[(mt_rand(0,(count($ids)-1)))];
            }
        }
        else{
            
        }
        
        
		if($foundItem == 1){
    		if ( $tableType == "weapons"){
    		   $sql = "SELECT id,name,type FROM weapons WHERE id='$quantity'";
    		   $result = mysqli_query($conn,$sql);
    		   $row = mysqli_fetch_assoc($result);
    		   $itemName = $row['name'];
               if ($row['type'] == "bow" || $row['type'] == "crossbow"){
                   $itemType = "secondarys";
               }
               else{
                   $itemType = "weapons";
               }
			   $linkType = "1";
    		}
            elseif($tableType == "armours"){
               $sql = "SELECT id,name,item_type FROM armours WHERE id='$quantity'";
               $result = mysqli_query($conn,$sql);
               $row = mysqli_fetch_assoc($result);
               $itemName = $row['name'];
               $itemType = $row['item_type'];
			   $linkType = "2";
            }
            $itemId = $quantity . ":1;1";
            $inv_id = $_SESSION['characterProperties']['inventory_id'];
            $sql = "UPDATE inventory SET $itemType=CONCAT($itemType,'" . $itemId . ",') WHERE iid='$inv_id'";
            if($conn->query($sql) === TRUE){
            }
            else{
                echo $conn->error;
            }
    		  echo "You recieve item:<a href='index.php?page=view-item&type=" . $linkType . "&item_name=" . $itemId . ":1;1'>" . $itemName . "</a><br>";
    	    }
    	}
}

function insertSurrender($value,$opponentName){
    global $conn;
    $name = $_SESSION['characterProperties']['name'];
    $sql = "UPDATE characters SET adventureSurrender=? WHERE name=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ds", $value,$name);
    mysqli_stmt_execute($stmt);
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
}

function escapeAttempt(){
    require_once(__ROOT__."/backend/character/update-characterSessions.php");
    global $conn;
    $enemy = $_SESSION['characterProperties']['adventureMonster'];
    $sql = "SELECT * FROM npc WHERE id='$enemy'";
    
    
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $result = mysqli_query($conn,$sql);
    
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    
    $yourPoints = $_SESSION['characterProperties']['dexterity'] + ($_SESSION['characterProperties']['level']*10) + ($_SESSION['characterProperties']['initiative']/2);
    $opponentPoints = ($row['level'] * 10) + ($row['dexterity']*2);
    
    $chance = $yourPoints / $opponentPoints;
    
    if ($chance > 0.9){
        $chance = 90;
    }
    else{
        $chance = round($chance*100);
    }
    
    if(mt_rand(1,100) <= $chance){
        echo "You escaped from " . $row['name'] . "!<br>";
        
        $roll = $_SESSION['characterProperties']['adventureRoll'];
        $area = $_SESSION['characterProperties']['adventureArea'];
        $choice = $_SESSION['characterProperties']['adventureChoice'];
        if (isset($_SESSION['characterProperties']['specificAdventure'])){
           $specificAdventure = $_SESSION['characterProperties']['specificAdventure'];
           $sql = "SELECT * FROM adventure WHERE specificId='$specificAdventure'";
        }
        else{
            $sql = "SELECT * FROM adventure WHERE roll='$roll' AND area='$area'";
        }
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        
        endAdventure($area[0], 0, $row);
    }
    else{
        #$extraDmg = mt_rand($row['minDamage'],$row['maxDamage']);
        
        echo "You fail to flee, you can no longer escape and are forced to fight your opponent";
		
        require_once(__ROOT__."/backend/fighting/newFight.php");
        $outcome = fight($_SESSION['characterProperties']['name'],$_SESSION['characterProperties']['adventureMonster'],0,0,0,1,1,$_SESSION['characterProperties']['adventureSurrender'],1);
    	afterBattle($outcome);
    }
    
}

if (isset($_GET['place'])){
	getInformation($_GET['place']);
}
elseif(isset($_GET['adventure'])){
    newAdventure();	
}
elseif(isset($_GET['choice'])){
	choice($_GET['choice']);
}
elseif(isset($_GET['walkAway'])){
    echo "You walk away from the enemy, heading back to town...";
    endAdventure($_SESSION['characterProperties']['adventureArea'], 0,'none');
}
elseif(isset($_GET['escapeMonster'])){
    escapeAttempt();
}
elseif(isset($_GET['fightMonster'])){
    require_once(__ROOT__."/backend/fighting/newFight.php");
	$enemy = $_SESSION['characterProperties']['adventureMonster'];
	if (strpos($enemy, ':') !== false) {
	    $exp = explode(":",$enemy);
	    $enemy = array();
	    for($i=0;$i<$exp[1];$i++){
	    	array_push($enemy,$exp[0]);
	    }
	}
	echo "<div id='adventureEventInfo'>";
    	$outcome = fight($_SESSION['characterProperties']['name'],$enemy,0,0,0,1,1,$_SESSION['characterProperties']['adventureSurrender'],1);
	echo "</div>";
    afterBattle($outcome);
}
elseif(isset($_GET['insertSurrender'])){
    insertSurrender($_GET['insertSurrender'],$_GET['enemy']);
}
?>