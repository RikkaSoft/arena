<?php 
global $conn;

$sql = "SELECT * FROM characters WHERE isOnline=1 ORDER BY experience DESC";
$result = mysqli_query($conn, $sql);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$wins = $row['wins']+$row['teamWins'];
				$losses = $row['losses']+$row['teamLosses'];
				$total = $wins+$losses;
				if ($wins != 0){
					$ratio = round(($wins / $total) * 100);
				}
				else{
					$ratio = 0;
				}
                if (isset($row['chatIcon'])){
                    if ($row['chatIcon'] != ""){
                        $chatIcon = "<img src=\"frontend/design/images/chatIcons/" . $row['chatIcon'] . "\"> ";
                    }
                    else{
                        $chatIcon = "";
                    }
                }
                else{
                    $chatIcon = "";
                }
				$red = "";
				if($row['deadNext'] == 1){
					$red = "style='color:red'";
				}
				echo "
				<tr>
				<td>" . $chatIcon . "<a class=\"headerButtonLink\" " . $red . " href=\"index.php?page=view-character&charName=" . $row['name'] . "\">" . $row['name'] . "</a></td>
				<td>" . $row['level'] . "</td>
				<td>" . $wins . "</td>
				<td>" . $losses . "</td>
				<td>" . $ratio . "%</td>
				<td>" . $row['kills'] . "</td>
				<td>" . $row['timesDied'] . "</td>
				</tr>
				";
			}
?>