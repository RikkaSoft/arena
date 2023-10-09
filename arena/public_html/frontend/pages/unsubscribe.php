<?php
global $conn;

if(isset($_GET['i']) && isset($_GET['u'])){
    $id = $_GET['i'];
    $username = $_GET['u'];
    $sql = "SELECT mailInformation FROM users WHERE id=? AND username=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "is", $id,$username);
    mysqli_stmt_execute($stmt);
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        if ($row['mailInformation'] == 0){
            echo "You have already unsubscribed";
        }
        else{
            $id = $_GET['i'];
            $username = $_GET['u'];
            $sql = "UPDATE users SET mailInformation=0 WHERE id=? AND username=?";
            $stmt = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($stmt, "is", $id,$username);
            mysqli_stmt_execute($stmt);
            
            if(mysqli_affected_rows($conn) == 1){
                echo "You will not recieve any more emails from arena@rikka.se";
            }
            else{
                echo "error.. send a request to mail@rikka.se to unsubscribe";
            }
        }
    }
else{
    echo "can't find user";
}
}


?>