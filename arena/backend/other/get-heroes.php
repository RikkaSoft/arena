<?php
global $conn;

function getHeroes($season){
    global $conn;
    $sql = "SELECT * FROM heroes WHERE season=? ORDER BY experience DESC";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $season);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
    if (mysqli_num_rows($result) > 0){
    echo "
    <table id=\"leaderTable\">
        <thead> 
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Arena Wins</th>
                <th>Arena Losses</th>
                <th>Win ratio</th>
                <th>Player Kills</th>
                <th>Last match</th>
            </tr>
        </thead>
    <tbody>
    ";
    while ($row = mysqli_fetch_assoc($result)){
        $wins = $row['wins']+$row['teamWins'];
        $losses = $row['losses']+$row['teamLosses'];
        $total = $wins+$losses;
        if ($wins != 0){
            $ratio = round(($wins / $total) * 100);
        }
        else{
            $ratio = 0;
        }
        echo "
        <tr>
        <td><a class=\"headerButtonLink\" href=\"index.php?page=view-character&hero=" . $row['user'] . "&charName=" . $row['name'] . "\">" . $row['name'] . "</a></td>
        <td>" . $row['level'] . "</td>
        <td>" . $wins . "</td>
        <td>" . $losses . "</td>
        <td>" . $ratio . "%</td>
        <td>" . $row['kills'] . "</td>";
		if($row['lastReport'] == 0){
			echo "<td>Season Winner!</td>";
		}
		else{
			echo "<td><a href='index.php?page=view-battlereport&battleId=" . $row['lastReport'] . "'>Last match</a></td>";	
		}
        echo "</tr>
        ";
    }
    echo "
    </tbody>
    </table>
    ";
    }
else{
    Echo "<strong>No heroes have died, they are all alive, for now...</strong>";
}
}

function getSeason($season){
    global $conn;
    $table = "s" . $season . "characters";
    $level = 9;
    if ($season == 1){
        $level = 7;
    }
    $sql = "SELECT * FROM $table WHERE level > $level ORDER BY experience DESC";
    $result = mysqli_query($conn,$sql);
    echo "
    <table id=\"leaderTable\">
        <thead> 
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Arena Wins</th>
                <th>Arena Losses</th>
                <th>Win ratio</th>
                <th>Player Kills</th>
            </tr>
        </thead>
    <tbody>
    ";
    while ($row = mysqli_fetch_assoc($result)){
        $wins = $row['wins']+$row['teamWins'];
        $losses = $row['losses']+$row['teamLosses'];
        $total = $wins+$losses;
        if ($wins != 0){
            $ratio = round(($wins / $total) * 100);
        }
        else{
            $ratio = 0;
        }
        echo "
        <tr>
        <td><a class=\"headerButtonLink\" href=\"index.php?page=view-character&season=" . $season . "&charName=" . $row['name'] . "\">" . $row['name'] . "</a></td>
        <td>" . $row['level'] . "</td>
        <td>" . $wins . "</td>
        <td>" . $losses . "</td>
        <td>" . $ratio . "%</td>
        <td>" . $row['kills'] . "</td>
        </tr>
        ";
    }
    echo "
    </tbody>
    </table>
    ";
}

function getSeasonFinale($season){
    global $conn;
    $table = "s" . $season . "tournaments";
    $sql = "SELECT * FROM $table";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    bracket32($season,$row['id'],$row['name'],$row['running'],$row['finished'],$row['minLevel'],$row['maxLevel'],$row['prizeGold'],$row['prizeXP'],$row['prizeItem'],$row['size'],$row['players'],$row['round1'],$row['round2'],$row['round2Report'],$row['round3'],$row['round3Report'],$row['round4'],$row['round4Report'],$row['round5'],$row['round5Report'],$row['round6'],$row['round6Report']); 
}

function bracket32($season,$id,$tournamentName,$running,$finished,$minLvl,$maxLvl,$prizeGold,$prizeXP,$prizeItem,$size,$players,$round1,$round2,$round2Report,$round3,$round3Report,$round4,$round4Report,$round5,$round5Report,$round6,$round6Report){
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
                    $name = "<p style=\"color:blue;\">Player signed up</p>";
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
                            $name2 = " <p><a href=\"index.php?tpage=fightRound&id=" . $i . "&round=1&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
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
                            $name3 = " <p><a href=\"index.php?tpage=fightRound&id=" . $fighterId . "&round=2&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
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
                            $name4 = " <p><a href=\"index.php?tpage=fightRound&id=" . $fighterId . "&round=3&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
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
                            $name5 = " <p><a href=\"index.php?tpage=fightRound&id=" . $fighterId . "&round=4&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a></p>";
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
                            $name6 = " <a href=\"index.php?tpage=fightRound&id=" . $fighterId . "&round=5&tourId=" . $id . "\"><img src=\"frontend/design/images/other/fightIcon.png\"></a>";
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
        
        if ($rewards != ""){
            echo "<p style=\"text-align:right;\"><strong>First Prize: " . $rewards . "<br>";
            if ($rewards2 != ""){
                echo "Second Prize: " . $rewards2;
            }
            echo "</strong></p>";
        }
        else{
            echo "<p style=\"text-align:right;\"><strong>First Prize: Bragging rights</p></strong><br>";
        }
        echo "</div>";
    }

?>
