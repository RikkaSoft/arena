<?php

function getMatches($type, $limit){
    global $conn;
    $name = $_SESSION['loggedIn'];

    $sql = "SELECT id,yourName,opponentName,date,win FROM battlereports WHERE username = ? AND type = ? ORDER BY id DESC LIMIT ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ssi",$name,$type,$limit);
    mysqli_stmt_execute($stmt);
    $result = $stmt->get_result();
    $resultArray = array();
    while ($row = mysqli_fetch_assoc($result)){
        
        if (isset($row['win'])){
            if($row['win'] == 1){
                $color = "style='color:#8484FF;'";
            }
            else{
                $color = "style='color:#cc0000;'";
            }
        }
        else{
            $color = "style='color:black;'";
        }
        
        array_push($resultArray,"<a href=\"#\" " . $color . " onclick=\"getReport('" . $row['id'] . "')\">" . $row['yourName'] . " vs " . $row['opponentName'] . " - " . $row['date'] . "</a>");
    }
    return $resultArray;
}



?>