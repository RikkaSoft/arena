<?php

    function playMatch($round,$playerId,$tournamentId){
        global $conn;
        
        #echo $round . " - " . $playerId . " - " . $tournamentId;
        
        $sql = "SELECT * FROM tournaments WHERE id='$tournamentId'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        
        $players = explode(",", $row['round' . $round]);
        $playersNames = explode(",", $row['round' . $round . 'Text']);
        $actualPlayers = array($players[$playerId],$players[$playerId+1]);
        $actualPlayersNames = array($playersNames[$playerId],$playersNames[$playerId+1]);
        #echo $actualPlayers[0] . " will fight " . $actualPlayers[1];
        
        
        var_dump($actualPlayersNames);
      require_once(__ROOT__."/backend/fighting/newFight.php");
      $battleId = tournamentRound($actualPlayers,$actualPlayersNames,$tournamentId,$round);  
      header( "Location: index.php?page=view-battlereport-sequence&battleId=" . $battleId . "");
    }
    
    
    
            
            
            
    if (isset($_GET['round'])){
        playMatch($_GET['round'],$_GET['id'],$_GET['tourId']);
    }

?>