<?php
global $conn;

$name = $_SESSION['characterProperties']['name'];
$sql = "UPDATE characters SET battleReportReady=0 WHERE name='$name'";
mysqli_query($conn,$sql);
header("Location: index.php?page=arena");

?>