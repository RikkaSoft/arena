<?php
global $conn;
$id = $_SESSION['characterProperties']['id'];
$sql = "SELECT c.*, e.*, s1.name as 'right_hand', s2.name as 'left_hand', s3.name as 'head', s4.name as 'chest', s5.name as 'arm', s6.name as 'leg', s7.name as 'feet', s8.name as 'secondary'
    FROM characters c
    LEFT JOIN equipment e on c.equipment_id=e.eid
    LEFT JOIN weapons s1 on s1.id=e.right_hand
    LEFT JOIN weapons s2 on s2.id=e.left_hand
    LEFT JOIN armours s3 on s3.id=e.head
    LEFT JOIN armours s4 on s4.id=e.chest
    LEFT JOIN armours s5 on s5.id=e.arm
    LEFT JOIN armours s6 on s6.id=e.leg
    LEFT JOIN armours s7 on s7.id=e.feet
    LEFT JOIN weapons s8 on s8.id=e.secondary
    WHERE c.id ='$id'";
$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));    
$row = mysqli_fetch_assoc($result);  
$_SESSION['characterProperties'] = $row;

var_dump($_SESSION['characterProperties']);
?>