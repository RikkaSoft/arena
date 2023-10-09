<?php
set_time_limit(1800);
include("/var/www/html/arena/system/config.php");
require_once(__ROOT__."/backend/tournament/tournament-admin.php");
global $conn;

	$sql = "SELECT * FROM tournaments WHERE finished = 0 AND running = 0 AND automatic = 1 AND start='full'";
	$result = mysqli_query($conn,$sql);
	echo mysqli_num_rows($result);
	if (mysqli_num_rows($result) > 0){
        $allTourneys = array();
        while ($row = mysqli_fetch_assoc($result)){
            $allTourneys[] = $row;
        }
        foreach($allTourneys as $row){
            echo "<br>" . $row['name'] . "<br>";

            if($row['players'] != ""){
                $allPlayers = explode(",",$row['players']);
                $newPlayers = array();
                foreach($allPlayers as $player){
                    $sql = "SELECT level FROM characters WHERE id=$player";
                    $result = mysqli_query($conn,$sql);
                    $level = mysqli_fetch_assoc($result);
                    if($level['level'] <= $row['maxLevel'] && $level['level'] >= $row['minLevel']){
                        $newPlayers[] = $player;
                    }
                }
                $count = count($newPlayers);
                if($count != count($allPlayers)){
                    $newPlayers = implode(",",$newPlayers);
                    $tId = $row['id'];
                    $sql = "UPDATE tournaments SET players='$newPlayers' WHERE id=$tId";
                    mysqli_query($conn,$sql);
                }
            }
            else{
                $count = 0;
            }
            echo $count . " - " . $row['size'];
            echo "<br><br>";
            var_dump($newPlayers);
            echo "<br><br>";

            var_dump($allPlayers);
            if ($count == $row['size']){
                    
                //RANDOMIZE START
                startTournamentOld($row['id']);
                
                //TELL THE PEOPLE
                $message = "<a href='index.php?page=tournament&id=" . $row['id'] . "'>" . $row['name'] . " has started</a>";
                #announce($message);
                sleep(30);
                
                //PLAY UNTIL DONE
                $winner = "";
                $i = 1;
                $id = $row['id'];
                while ($winner == "" || $i > 10){
                    $sql = "SELECT * FROM tournaments WHERE id='$id'";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    
                    if (count(explode(",", $row['round' . $i])) > 1){
                        #$message = "<a href='index.php?page=tournament&id=" . $row['id'] . "'>Round " . $i . " will start in thirty seconds</a>";
                        #announce($message);
                        sleep(10);
                        #$message = "<a href='index.php?page=tournament&id=" . $row['id'] . "'>Round " . $i . " starts now</a>";
                        #announce($message);
                        playRound($row['id'],$i);
                    }
                    else{
                        $winner = $row['round' . $i . 'Text'];
                        $message = "<a href='index.php?page=tournament&id=" . $row['id'] . "'>" . $row['name'] . " has finished. Winner: " . $winner . "</a>";
                        announce($message);
                        finishTournament($row['id'],$i,$winner);
                    }
                    $i++;
                }
            }
            else{
                echo "not enough players";
            }
        }
    }
    else{
        echo "no tournament available";
    }
    



?>