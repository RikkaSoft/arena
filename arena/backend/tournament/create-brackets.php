<?php
require_once(__ROOT__."/backend/crafting/craftingFunctions.php");
	function loadTournament($id,$admin,$season,$finals = false){
	    if (!isset($_SESSION['final'])){
	       echo "<a href=\"index.php?page=tournament\">Go back</a>";
        }
        
        if ($finals){
            $table =  "finaltournaments";
        }
        else{
            $table = "tournaments";
        }
		global $conn;
		$sql = "SELECT * FROM $table WHERE id = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		if(mysqli_num_rows($result) > 0){
			if($finals){
				$row['size'] = 32;
			}
            if (!isset($_SESSION['final'])){
                echo "<h2>" . $row['name'] . "</h2>";
            }
            
            if ($row['size'] == 4){
                bracket4($season,$id,$row['name'],$row['running'],$row['finished'],$row['minLevel'],$row['maxLevel'],$row['prizeGold'],$row['prizeXP'],$row['prizeItem'],$row['prizePart'],$row['size'],$row['players'],$row['round1Text'],$row['round2Text'],$row['round2Report'],$row['round3Text'],$row['round3Report']);
            }
            elseif ($row['size'] == 8){
				bracket8($season,$id,$row['name'],$row['running'],$row['finished'],$row['minLevel'],$row['maxLevel'],$row['prizeGold'],$row['prizeXP'],$row['prizeItem'],$row['prizePart'],$row['size'],$row['players'],$row['round1Text'],$row['round2Text'],$row['round2Report'],$row['round3Text'],$row['round3Report'],$row['round4Text'],$row['round4Report']);
			}
			elseif($row['size'] == 16){
				bracket16($season,$id,$row['name'],$row['running'],$row['finished'],$row['minLevel'],$row['maxLevel'],$row['prizeGold'],$row['prizeXP'],$row['prizeItem'],$row['prizePart'],$row['size'],$row['players'],$row['round1Text'],$row['round2Text'],$row['round2Report'],$row['round3Text'],$row['round3Report'],$row['round4Text'],$row['round4Report'],$row['round5Text'],$row['round5Report']);	
			}
            elseif($row['size'] == 32){
                bracket32($season,$id,$row['name'],$row['running'],$row['finished'],$row['minLevel'],$row['maxLevel'],$row['prizeGold'],$row['prizeXP'],$row['prizeItem'],$row['prizePart'],$row['size'],$row['players'],$row['round1Text'],$row['round2Text'],$row['round2Report'],$row['round3Text'],$row['round3Report'],$row['round4Text'],$row['round4Report'],$row['round5Text'],$row['round5Report'],$row['round6Text'],$row['round6Report']); 
            }
            if($season == 0){
				if (!isset($_SESSION['final'])){
					if($_SESSION['characterProperties']['healedDate'] == 0){
						if ($row['finished'] == 0 && $admin != 1){
						    if(isset($_SESSION['characterProperties']['id'])){
						        $playersExploded = explode(",",$row['players']);
		    					if (in_array($_SESSION['characterProperties']['id'],$playersExploded)) {
		    					    echo "<br>You are signed up for this tournament. <button class='button' id=\"chickenOut\">Chicken out <img src=\"frontend/design/images/other/chicken.png\"></button>";
		    						
		    						
		    						$i = 0.5;
		    						echo "<br><br>";
		    						echo "When do you wish to surrender?<br><select id=\"tournamentSurrender\">";
		    						do {
		    							if ($i >= $_SESSION['characterProperties']['tournamentSurrender']){
		    								$si = $i*100;
		    								$hp = round(($_SESSION['characterProperties']['vitality'] * $i));
		    								echo "<option value=$i selected>$si% ($hp hp)</option>";
		    								$i = $i-0.1;
		    								
		    							}
		    							else{
		    								$si = $i*100;
		    								$hp = round(($_SESSION['characterProperties']['vitality'] * $i));
		    								echo "<option value=$i>$si% ($hp hp)</option>";
		    								$i = $i-0.1;
		    							}
		    						} while ($i >= 0.1);
		    						
		    						echo "<option value=0>0% (If you lose, you die)</option>";
		    						echo "</select> ";
		    						
		    						echo "<div id=\"surrDiv\"></div>";
		    								
		    						echo "<script>
		    						$('#chickenOut').click(function(){
		    							$('#tournamentArea').load('index.php?tpage=tournament-actions&nonUI&chickenOut=" . $id . "')
		    						});
		    						$('#tournamentSurrender').change(function(){
		    							var value = $('#tournamentSurrender').val();
		    							$('#surrDiv').load('index.php?tpage=tournament-actions&nonUI&setSurrender=' + value)
		    						});
		    						</script>";
		    					}
		    					else{
		    						if (substr_count($row['players'],",") != ($row['size']-1)){
		    							echo "<br>Do you wish to sign up for this tournament? <button class='button' id=\"signUp\">Sign Up</button>";
		    							echo "<script>
		    							$('#signUp').click(function(){
		    								$('#tournamentArea').load('index.php?tpage=tournament-actions&nonUI&signUp=" . $id . "')
		    							});
		    							</script>";
		    						}
		    					}
		    				}
						}
					}
					else{
						echo "You cannot sign up for tournaments while mortally injured";
					}
				}
			}
		}
	}
	
	function bracket4($season,$id,$tournamentName,$running,$finished,$minLvl,$maxLvl,$prizeGold,$prizeXP,$prizeItem,$prizePart,$size,$players,$round1,$round2,$round2Report,$round3,$round3Report){
            if ($players != ""){
                $players = explode(",",$players);
            }

            //ROUND 1
            $round1String = $round1;
            if(isset($round1String)){
                $round1 = explode(",",$round1String);
                $round1Style = $round1;
            }
            //ROUND 2
            $round2String = $round2;
            //COLORING
            if(isset($round2String)){
                $round2Report = explode(",",$round2Report);
                $round2 = explode(",",$round2String);
                $round2Style = $round2;
                $i = 0;
                $round1Style = array();
                while ($i < 4){
                    if (in_array($round1[$i],$round2)){
                        $round1Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
                    }
                    else{
                        $round1Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
                    }
                    $i++;
                }
            }
            
            //ROUND 3
            $round3String = $round3;
            if(isset($round3String)){
                $round3Report = explode(",",$round3Report);
                $round3 = explode(",",$round3String);
                $round3Style = $round3;
                $i = 0;
                $round2Style = array();
                while ($i < 2){
                    if (in_array($round2[$i],$round3)){
                        $round2Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
                    }
                    else{
                        $round2Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
                    }
                    $i++;
                }
            }
        
        echo "<div class=\"tournamentAreaInner\" >";
        echo "<table id=\"tournament\" summary=\"Tournament Bracket\" width=\"100%\" >";
        echo "<tr><th>Semi Final</th><th>Final</th><th>Winner</th></tr>";
        echo "<tr height=\"20px\">
            <td colspan=\"3\"></td>
        </tr>";
        $i = 0;
        while ($i < 4){
            if(isset($round1[$i])){
                $name = "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\"><p id=\"tournamentPlayer\">" . $round1Style[$i];
                
                $name .= "</p></a>";
                
            }
            else{
                if (isset($players[$i])){
                    $name = "<p class='playerEntry'>Player signed up</p>";
                }
                else{
                    if($running == 0 && $finished == 0){
                        $name = "<p>Waiting for player</p>";
                    }
                    else{
                        $name ="<p>-</p>";
                    }
                }
            }
             echo "<tr>
              <td>" .  $name . "</td>";
            if ($i % 2 == 0){
                if(isset($round2[$i/2])){
                    $name2 = "<p><a href=\"index.php?page=view-battlereport&battleId=" . $round2Report[$i/2] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " . $round2Style[$i/2] . "</p>";
                }
                else{
                    $name2 = "<p>-</p>";
                }
                echo "<td rowspan=\"2\">".  $name2 . "</td>";
            }
            if ($i % 4 == 0){
                if(isset($round3[$i/4])){
                    echo "<td rowspan=\"4\" style=\"text-align:center;\"><p><a style=\"float:left;\" href=\"index.php?page=view-battlereport&battleId=" . $round3Report[$i/4] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " .  "<a class=\"tournamentWinner\"  href=\"index.php?page=view-character&charName=" . $round3[$i/4] . "\">" . $round3Style[$i/4] . "</a>
                    <img style=\"float:right;\" src=\"frontend/design/images/other/crown.png\">
                    </p></td>";
                }
                else{
                    $name3 = "-";
                    echo "<td rowspan=\"4\"><p>".  $name3 . "</p></td>";
                }
                
            } 
             echo "</tr>";
             $i++;
        }
        echo "</table>";
        echo "<br>";
        
        $rewards = "";
        $rewards2 = "";
        if ($prizeGold != 0){
            $rewards .= $prizeGold*0.75 . "g ";
            $rewards2 .= $prizeGold*0.25 . "g ";
        }
        if ($prizeXP != 0){
            $rewards .= $prizeXP*0.75 . "xp ";
            $rewards2 .= $prizeXP*0.25 . "xp ";
        }
        if ($prizeItem != ""){
            $rewards .= $prizeItem;
        }
        if($prizePart != ""){
			$parts = explode(",",$prizePart);
			foreach($parts as $part){
				$info = getPartName($part);
				$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
			}
		}
        
        if ($rewards != ""){
        	echo "<div style='text-align:right'>";
	            echo "<strong>First Prize: " . $rewards . "<br>";
	            if ($rewards2 != ""){
	                echo "Second Prize: " . $rewards2;
	            }
	            echo "</strong>";
	        echo "</div>";
        }
        else{
            echo "<p style=\"text-align:right;\"><strong>First Prize: Bragging rights</p></strong><br>";
        }
        echo "</div>";
    }
	
	function bracket8($season,$id,$tournamentName,$running,$finished,$minLvl,$maxLvl,$prizeGold,$prizeXP,$prizeItem,$prizePart,$size,$players,$round1,$round2,$round2Report,$round3,$round3Report,$round4,$round4Report){
			if ($players != ""){
				$players = explode(",",$players);
			}
			
			#$players = preg_split('/,/', $players, null, PREG_SPLIT_NO_EMPTY);

			//ROUND 1
			$round1String = $round1;
			if(isset($round1String)){
				$round1 = explode(",",$round1String);
				$round1Style = $round1;
			}
			//ROUND 2
			$round2String = $round2;
			//COLORING
			if(isset($round2String)){
				$round2Report = explode(",",$round2Report);
				$round2 = explode(",",$round2String);
				$round2Style = $round2;
				$i = 0;
				$round1Style = array();
				while ($i < 8){
				    if (isset($round1[$i])){
    					if (in_array($round1[$i],$round2)){
    						$round1Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
    					}
    					else{
    						$round1Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
    					}
                    }
					$i++;
				}
			}
			
			//ROUND 3
			$round3String = $round3;
			if(isset($round3String)){
				$round3Report = explode(",",$round3Report);
				$round3 = explode(",",$round3String);
				$round3Style = $round3;
				$i = 0;
				$round2Style = array();
				while ($i < 4){
				    if (isset($round2[$i])){
    					if (in_array($round2[$i],$round3)){
    						$round2Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
    					}
    					else{
    						$round2Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
    					}
                    }
					$i++;
				}
			}
			
			//ROUND 4
			$round4String = $round4;
			if(isset($round4String)){
				$round4Report = explode(",",$round4Report);
				$round4 = explode(",",$round4String);
				$round4Style = $round4;
				$i = 0;
				$round3Style = array();
				while ($i < 2){
				    if (isset($round3[$i])){
    					if (in_array($round3[$i],$round4)){
    						$round3Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
    					}
    					else{
    						$round3Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
    					}
                    }
					$i++;
				}
			}
		
		echo "<div class=\"tournamentAreaInner\">";
		echo "<table id=\"tournament\" summary=\"Tournament Bracket\" width=\"100%\">";
		echo "<tr><th>Round of 8</th><th>Semi Final</th><th>Final</th><th>Winner</th></tr>";
		echo "<tr height=\"20px\">
			<td colspan=\"4\"></td>
		</tr>";
		$i = 0;
		while ($i < 8){
			if(isset($round1[$i])){
				$name = "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\"><p id=\"tournamentPlayer\">" . $round1Style[$i] . "</p></a>";
			}
			else{
				if (isset($players[$i])){
					$name = "<p class='playerEntry'>Player signed up</p>";
				}
				else{
					if($running == 0 && $finished == 0){
						$name = "<p>Waiting for player</p>";
					}
					else{
						$name ="<p>-</p>";
					}
				}
			}
			 echo "<tr>
			  <td>" .  $name . "</td>";
			if ($i % 2 == 0){
				if(isset($round2[$i/2])){
					$name2 = "<p><a href=\"index.php?page=view-battlereport&battleId=" . $round2Report[$i/2] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " . $round2Style[$i/2] . "</p>";
				}
				else{
					$name2 = "<p>-</p>";
				}
				echo "<td rowspan=\"2\">".  $name2 . "</td>";
			}
			if ($i % 4 == 0){
				if(isset($round3[$i/4])){
					$name3 = "<p><a href=\"index.php?page=view-battlereport&battleId=" . $round3Report[$i/4] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " . $round3Style[$i/4] . "</p>";
				}
				else{
					$name3 = "<p>-</p>";
				}
				echo "<td rowspan=\"4\">".  $name3 . "</td>";
			} 
			if ($i % 8 == 0){
				if(isset($round4[$i/8])){
					#$name4 = $round4[$i/8];
					echo "<td rowspan=\"8\" style=\"text-align:center;\"><p><a style=\"float:left;\" href=\"index.php?page=view-battlereport&battleId=" . $round4Report[$i/8] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " .  "<a class=\"tournamentWinner\"  href=\"index.php?page=view-character&charName=" . $round4[$i/8] . "\">" . $round4Style[$i/8] . "</a>
					<img style=\"float:right;\" src=\"frontend/design/images/other/crown.png\">
					</p></td>";
				}
				else{
					$name4 = "-";
					echo "<td rowspan=\"8\"><p>".  $name4 . "</p></td>";
				}
				
			} 
			 echo "</tr>";
			 $i++;
		}
		echo "</table>";
		echo "<br>";
		
		$rewards = "";
		$rewards2 = "";
		if ($prizeGold != 0){
			$rewards .= $prizeGold*0.75 . "g ";
			$rewards2 .= $prizeGold*0.25 . "g ";
		}
		if ($prizeXP != 0){
			$rewards .= $prizeXP*0.75 . "xp ";
			$rewards2 .= $prizeXP*0.25 . "xp ";
		}
		if ($prizeItem != ""){
			$rewards .= $prizeItem;
		}
		if($prizePart != ""){
			$parts = explode(",",$prizePart);
			foreach($parts as $part){
				$info = getPartName($part);
				$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
			}
		}
		
		if ($rewards != ""){
        	echo "<div style='text-align:right'>";
	            echo "<strong>First Prize: " . $rewards . "<br>";
	            if ($rewards2 != ""){
	                echo "Second Prize: " . $rewards2;
	            }
	            echo "</strong>";
	        echo "</div>";
        }
		else{
			echo "<p style=\"text-align:right;\"><strong>First Prize: Bragging rights</p></strong><br>";
		}
		echo "</div>";
	}
		
		

	function bracket16($season,$id,$tournamentName,$running,$finished,$minLvl,$maxLvl,$prizeGold,$prizeXP,$prizeItem,$prizePart,$size,$players,$round1,$round2,$round2Report,$round3,$round3Report,$round4,$round4Report,$round5,$round5Report){
			if ($players != ""){
				$players = explode(",",$players);
			}

			//ROUND 1
			$round1String = $round1;
			if(isset($round1String)){
				$round1 = explode(",",$round1String);
				$round1Style = $round1;
			}
			//ROUND 2
			$round2String = $round2;
			//COLORING
			if(isset($round2String)){
				$round2Report = explode(",",$round2Report);
				$round2 = explode(",",$round2String);
				$round2Style = $round2;
				$i = 0;
				$round1Style = array();
				while ($i < count($round1)){
					if (in_array($round1[$i],$round2)){
						$round1Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
					}
					else{
						$round1Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
					}
					$i++;
				}
			}
			
			//ROUND 3
			$round3String = $round3;
			if(isset($round3String)){
				$round3Report = explode(",",$round3Report);
				$round3 = explode(",",$round3String);
				$round3Style = $round3;
				$i = 0;
				$round2Style = array();
				while ($i <  count($round1)/2){
					if (in_array($round2[$i],$round3)){
						$round2Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
					}
					else{
						$round2Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
					}
					$i++;
				}
			}
			
			//ROUND 4
			$round4String = $round4;
			if(isset($round4String)){
				$round4Report = explode(",",$round4Report);
				$round4 = explode(",",$round4String);
				$round4Style = $round4;
				$i = 0;
				$round3Style = array();
				while ($i < count($round1)/4){
					if (in_array($round3[$i],$round4)){
						$round3Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
					}
					else{
						$round3Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
					}
					$i++;
				}
			}
			
			//ROUND 5
			$round5String = $round5;
			if(isset($round5String)){
				$round5Report = explode(",",$round5Report);
				$round5 = explode(",",$round5String);
				$round5Style = $round5;
				$i = 0;
				$round4Style = array();
				while ($i < count($round1)/8){
					if (in_array($round3[$i],$round4)){
						$round4Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round4[$i] . "&season=" . $season . "\">" . $round4[$i] . "</a>";
					}
					else{
						$round4Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round4[$i] . "&season=" . $season . "\">" . $round4[$i] . "</a>";
					}
					$i++;
				}
			}
		
		echo "<div class=\"tournamentAreaInner\">";
		echo "<table id=\"tournament\" summary=\"Tournament Bracket\" width=\"100%\">";
		echo "<tr><th>Round of 16</th><th>Round of 8</th><th>Semi Final</th><th>Final</th><th>Winner</th></tr>";
		echo "<tr height=\"20px\">
			<td colspan=\"4\"></td>
		</tr>";
		$i = 0;
		while ($i < 16){
			if(isset($round1[$i])){
				$name = "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\"><p id=\"tournamentPlayer\">" . $round1Style[$i] . "</p></a>";
			}
			else{
				if (isset($players[$i])){
					$name = "<p class='playerEntry'>Player signed up</p>";
				}
				else{
					if($running == 0 && $finished == 0){
						$name = "<p>Waiting for player</p>";
					}
					else{
						$name ="<p>-</p>";
					}
				}
			}
			 echo "<tr>
			  <td>" .  $name . "</td>";
			if ($i % 2 == 0){
				if(isset($round2[$i/2])){
					if ($round2Report[$i/2] != 0){
						$battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round2Report[$i/2] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
					}
					else{
						$battleLink = "<a href=\"#\">WO </a>";
					}
					$name2 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round2[$i/2] . "&season=" . $season . "\">" . $round2Style[$i/2] . "</a></p>";
				}
				else{
					$name2 = "<p>-</p>";
				}
				echo "<td rowspan=\"2\">".  $name2 . "</td>";
			}
			if ($i % 4 == 0){
				if(isset($round3[$i/4])){
					if ($round3Report[$i/4] != 0){
						$battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round3Report[$i/4] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
					}
					else{
						$battleLink = "<a href=\"#\">WO </a>";
					}
					$name3 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round3[$i/4] . "&season=" . $season . "\">" . $round3Style[$i/4] . "</p>";
				}
				else{
					$name3 = "<p>-</p>";
				}
				echo "<td rowspan=\"4\">".  $name3 . "</td>";
			} 
			if ($i % 8 == 0){
				if(isset($round4[$i/8])){
					if ($round4Report[$i/8] != 0){
						$battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round4Report[$i/8] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
					}
					else{
						$battleLink = "<a href=\"#\">WO </a>";
					}
					$name4 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round4[$i/8] . "&season=" . $season . "\">" . $round4Style[$i/8] . "</p>";
				}
				else{
					$name4 = "<p>-</p>";
				}
				echo "<td rowspan=\"8\">".  $name4 . "</td>";
			} 
			if ($i % 16 == 0){
				if(isset($round5[$i/16])){
					echo "<td rowspan=\"16\" style=\"text-align:center;\"><p><a style=\"float:left;\" href=\"index.php?page=view-battlereport&battleId=" . $round5Report[$i/16] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " .  "<a class=\"tournamentWinner\"  href=\"index.php?page=view-character&charName=" . $round5[$i/16] . "&season=" . $season . "\">" . $round5Style[$i/16] . "</a>
					<img style=\"float:right;\" src=\"frontend/design/images/other/crown.png\">
					</p></td>";
				}
				else{
					$name5 = "-";
					echo "<td rowspan=\"16\"><p>".  $name5 . "</p></td>";
				}
				
			} 
			 echo "</tr>";
			 $i++;
		}
		echo "</table>";
		echo "<br>";
		
		$rewards = "";
		$rewards2 = "";
		if ($prizeGold != 0){
			$rewards .= $prizeGold*0.75 . "g ";
			$rewards2 .= $prizeGold*0.25 . "g ";
		}
		if ($prizeXP != 0){
			$rewards .= $prizeXP*0.75 . "xp ";
			$rewards2 .= $prizeXP*0.25 . "xp ";
		}
		if ($prizeItem != ""){
			$rewards .= $prizeItem;
		}
		if($prizePart != ""){
			$parts = explode(",",$prizePart);
			foreach($parts as $part){
				$info = getPartName($part);
				$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
			}
		}
		
		if ($rewards != ""){
        	echo "<div style='text-align:right'>";
	            echo "<strong>First Prize: " . $rewards . "<br>";
	            if ($rewards2 != ""){
	                echo "Second Prize: " . $rewards2;
	            }
	            echo "</strong>";
	        echo "</div>";
        }
		else{
			echo "<p style=\"text-align:right;\"><strong>First Prize: Bragging rights</p></strong><br>";
		}
		echo "</div>";
	}
    function bracket32($season,$id,$tournamentName,$running,$finished,$minLvl,$maxLvl,$prizeGold,$prizeXP,$prizeItem,$prizePart,$size,$players,$round1,$round2,$round2Report,$round3,$round3Report,$round4,$round4Report,$round5,$round5Report,$round6,$round6Report){
            
            if ($players != ""){
                $players = explode(",",$players);
            }

            //ROUND 1
            $round1String = $round1;
            if(isset($round1String)){
                $round1 = explode(",",$round1String);
                $round1Style = $round1;
            }
            //ROUND 2
            $round2String = $round2;
            //COLORING
            if(isset($round2String)){
                $round2Report = explode(",",$round2Report);
                $round2 = explode(",",$round2String);
                $round2Style = $round2;
                $i = 0;
                $round1Style = array();
                while ($i < count($round1)){
                    if (in_array($round1[$i],$round2)){
                        $round1Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
                    }
                    else{
                        $round1Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\">" . $round1[$i] . "</a>";
                    }
                    $i++;
                }
            }
            
            //ROUND 3
            $round3String = $round3;
            if(isset($round3String)){
                $round3Report = explode(",",$round3Report);
                $round3 = explode(",",$round3String);
                $round3Style = $round3;
                $i = 0;
                $round2Style = array();
                while ($i <  count($round1)/2){
                    if (in_array($round2[$i],$round3)){
                        $round2Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
                    }
                    else{
                        $round2Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round2[$i] . "&season=" . $season . "\">" . $round2[$i] . "</a>";
                    }
                    $i++;
                }
            }
            
            //ROUND 4
            $round4String = $round4;
            if(isset($round4String)){
                $round4Report = explode(",",$round4Report);
                $round4 = explode(",",$round4String);
                $round4Style = $round4;
                $i = 0;
                $round3Style = array();
                while ($i < count($round1)/4){
                    if (in_array($round3[$i],$round4)){
                        $round3Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
                    }
                    else{
                        $round3Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round3[$i] . "&season=" . $season . "\">" . $round3[$i] . "</a>";
                    }
                    $i++;
                }
            }
            
            //ROUND 5
            $round5String = $round5;
            if(isset($round5String)){
                $round5Report = explode(",",$round5Report);
                $round5 = explode(",",$round5String);
                $round5Style = $round5;
                $i = 0;
                $round4Style = array();
                while ($i < count($round1)/8){
                    if (in_array($round4[$i],$round5)){
                        $round4Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round4[$i] . "&season=" . $season . "\">" . $round4[$i] . "</a>";
                    }
                    else{
                        $round4Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round4[$i] . "&season=" . $season . "\">" . $round4[$i] . "</a>";
                    }
                    $i++;
                }
            }
            //ROUND 6
            $round6String = $round6;
            if(isset($round6String)){
                $round6Report = explode(",",$round6Report);
                $round6 = explode(",",$round6String);
                $round6Style = $round6;
                $i = 0;
                $round5Style = array();
                while ($i < count($round1)/16){
                    if (in_array($round5[$i],$round6)){
                        $round5Style[$i] = "<a class=\"tournamentWinner\" href=\"index.php?page=view-character&charName=" . $round5[$i] . "&season=" . $season . "\">" . $round5[$i] . "</a>";
                    }
                    else{
                        $round5Style[$i] = "<a class=\"tournamentLoser\" href=\"index.php?page=view-character&charName=" . $round5[$i] . "&season=" . $season . "\">" . $round5[$i] . "</a>";
                    }
                    $i++;
                }
            }
        
        echo "<div class=\"tournamentAreaInner\" >";
        echo "<table id=\"tournament\" summary=\"Tournament Bracket\" width=\"100%\" style='font-size:12px'; padding:0px;>";
        echo "<tr><th>Round of 32</th><th>Round of 16</th><th>Round of 8</th><th>Semi Final</th><th>Final</th><th>Winner</th></tr>";
        echo "<tr height=\"20px\">
            <td colspan=\"6\"></td>
        </tr>";
        $i = 0;
        while ($i < 32){
            if(isset($round1[$i])){
                $name = "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round1[$i] . "&season=" . $season . "\"><p id=\"tournamentPlayer\">" . $round1Style[$i] . "</p></a>";
            }
            else{
                if (isset($players[$i])){
                    $name = "<p class='playerEntry'>Player signed up</p>";
                }
                else{
                    if($running == 0 && $finished == 0){
                        $name = "<p>Waiting for player</p>";
                    }
                    else{
                        $name ="<p>-</p>";
                    }
                }
            }
             echo "<tr>
              <td>" .  $name . "</td>";
            if ($i % 2 == 0){
                $round2Done = 1;
                if(isset($round2[$i/2])){
                    if ($round2Report[$i/2] != 0){
                        $battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round2Report[$i/2] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
                    }
                    else{
                        $battleLink = "<a href=\"#\">WO </a>";
                    }
                    $name2 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round2[$i/2] . "&season=" . $season . "\">" . $round2Style[$i/2] . "</a></p>";
                }
                else{
                    if($_SESSION['other']['tournamentAdmin'] == 1){
                            $name2 = " <p><a href=\"index.php?tpage=fightRound&nonUI&id=" . $i . "&round=1&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
                            $round2Done = 0;
                    }
                    else{
                        $name2 = "<p>-</p>";                        
                    }
                }
                echo "<td rowspan=\"2\">".  $name2 . "</td>";
            }
            if ($i % 4 == 0){
                if(isset($round3[$i/4])){
                    if ($round3Report[$i/4] != 0){
                        $battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round3Report[$i/4] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
                    }
                    else{
                        $battleLink = "<a href=\"#\">WO </a>";
                    }
                    $name3 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round3[$i/4] . "&season=" . $season . "\">" . $round3Style[$i/4] . "</p>";
                }
                else{
                    if($_SESSION['other']['tournamentAdmin'] == 1){
                            if ($i > 0){
                                $fighterId = $i / 2;
                            }
                            else{
                                $fighterId = 0;
                            }
                            $name3 = " <p><a href=\"index.php?tpage=fightRound&nonUI&id=" . $fighterId . "&round=2&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
                            $round2Done = 0;
                    }
                    else{
                        $name3 = "<p>-</p>";                        
                    }
                }
                echo "<td rowspan=\"4\">".  $name3 . "</td>";
            } 
            if ($i % 8 == 0){
                if(isset($round4[$i/8])){
                    if ($round4Report[$i/8] != 0){
                        $battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round4Report[$i/8] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
                    }
                    else{
                        $battleLink = "<a href=\"#\">WO </a>";
                    }
                    $name4 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round4[$i/8] . "&season=" . $season . "\">" . $round4Style[$i/8] . "</p>";
                }
                else{
                    if($_SESSION['other']['tournamentAdmin'] == 1){
                            if ($i > 0){
                                $fighterId = $i / 4;
                            }
                            else{
                                $fighterId = 0;
                            }
                            $name4 = " <p><a href=\"index.php?tpage=fightRound&nonUI&id=" . $fighterId . "&round=3&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
                            $round2Done = 0;
                    }
                    else{
                        $name4 = "<p>-</p>";                        
                    }
                }
                echo "<td rowspan=\"8\">".  $name4 . "</td>";
            } 
            if ($i % 16 == 0){
                if(isset($round5[$i/16])){
                    if ($round5Report[$i/16] != 0){
                        $battleLink = "<a href=\"index.php?page=view-battlereport&battleId=" . $round5Report[$i/16] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> ";
                    }
                    else{
                        $battleLink = "<a href=\"#\">WO </a>";
                    }
                    $name5 = "<p>" . $battleLink . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&charName=" . $round5[$i/16] . "&season=" . $season . "\">" . $round5Style[$i/16] . "</p>";
                }
                else{
                    if($_SESSION['other']['tournamentAdmin'] == 1){
                            if ($i > 0){
                                $fighterId = $i / 8;
                            }
                            else{
                                $fighterId = 0;
                            }
                            $name5 = " <p><a href=\"index.php?tpage=fightRound&nonUI&id=" . $fighterId . "&round=4&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
                            $round2Done = 0;
                    }
                    else{
                        $name5 = "<p>-</p>";                        
                    }
                }
                echo "<td rowspan=\"16\">".  $name5 . "</td>";
            } 
            if ($i % 32== 0){
                if(isset($round6[$i/32])){
                    echo "<td rowspan=\"32\" style=\"text-align:center;\"><p><a style=\"float:left;\" href=\"index.php?page=view-battlereport&battleId=" . $round6Report[$i/32] . "&season=" . $season . "\"><img src=\"frontend/design/images/other/battleReport.png\"></a> " .  "<a class=\"tournamentWinner\"  href=\"index.php?page=view-character&charName=" . $round6[$i/32] . "&season=" . $season . "\">" . $round6Style[$i/32] . "</a>
                    <img style=\"float:right;\" src=\"frontend/design/images/other/crown.png\">
                    </p></td>";
                }
                else{
                    if($_SESSION['other']['tournamentAdmin'] == 1){
                            if ($i > 0){
                                $fighterId = $i / 16;
                            }
                            else{
                                $fighterId = 0;
                            }
                            $name6 = " <a href=\"index.php?tpage=fightRound&nonUI&id=" . $fighterId . "&round=5&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a>";
                            $round2Done = 0;
                    }
                    else{
                        $name6 = "-";                       
                    }
                    echo "<td rowspan=\"32\"><p>".  $name6 . "</p></td>";
                }
                
            } 
             echo "</tr>";
             $i++;
        }
        echo "</table>";
        echo "<br>";
        
        $rewards = "";
        $rewards2 = "";
        if ($prizeGold != 0){
            $rewards .= $prizeGold*0.75 . "g ";
            $rewards2 .= $prizeGold*0.25 . "g ";
        }
        if ($prizeXP != 0){
            $rewards .= $prizeXP*0.75 . "xp ";
            $rewards2 .= $prizeXP*0.25 . "xp ";
        }
        if ($prizeItem != ""){
            $rewards .= $prizeItem;
        }
        if($prizePart != ""){
			$parts = explode(",",$prizePart);
			foreach($parts as $part){
				$info = getPartName($part);
				$rewards .= "<span class='inventoryPart prizeRow' id='" . $part . "'>" . $info . "</span><br>";
			}
		}
        
        if ($rewards != ""){
        	echo "<div>";
	            echo "<strong>First Prize: " . $rewards . "<br>";
	            if ($rewards2 != ""){
	                echo "Second Prize: " . $rewards2;
	            }
	            echo "</strong>";
	        echo "</div>";
        }
        else{
            echo "<p style=\"text-align:right;\"><strong>First Prize: Bragging rights</p></strong><br>";
        }
        echo "</div>";
    }
?>