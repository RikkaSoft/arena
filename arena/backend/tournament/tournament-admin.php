<?php
function showOptions(){
	if($_SESSION['other']['tournamentAdmin'] == 0){
		echo "You don't have permission to view this page";
	}
	else{
		echo "<button id=\"newTournament\">New tournament</button>";
		echo "<br><br>";
		echo "<h3>Upcoming Tournaments:</h3>";
		getUpcomingTournaments();
		
		
		
		echo "<script>
		function getTournament(id){
			$('#tournamentInfo').load('index.php?tpage=tournament-admin&nonUI&getTournament=' + id);
		}
		$('#newTournament').click(function(){
			$('#tournamentInfo').load('index.php?tpage=tournament-admin&nonUI&newTournament');
		});
		</script>";
	}
}


function getUpcomingTournaments(){
	global $conn;
	$sql = "SELECT name,id FROM tournaments WHERE finished != 1";
	$result = mysqli_query($conn,$sql);
	if (mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)){
			echo "<a href='javascript:void(0)' onclick='getTournament(" . $row['id'] .  ")'>" . $row['name'] . "</a>";
			echo "<br>";
		}
	}
	else{
		echo "None";
	}
}
function getTournament($id){
	global $conn;
    echo $id;
	$sql = "SELECT * FROM tournaments WHERE id = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	if(mysqli_num_rows($result) == 0){
		echo "No such tournament exists";
	}
	else{
		echo "<h3>" . $row['name'] . "</h3>";
		require_once(__ROOT__."/backend/tournament/create-brackets.php");
		loadTournament($id,1,0);
		echo "<br><br>";
		if ($row['running'] == 0) {
			echo "<button id=\"startTournament\">Start tournament/Randomize start</button>";
		}
		else{
			echo "<button id=\"playRound\">Play round</button>";
		}
		
		echo "<br><br>";
		
		echo "<h3>Tournament Options</h3>";
		echo "Tournament Name: <input class='maxInputs' id='name' value='" . $row['name'] . "'>";
		echo "<br>";
		echo "Size: <input class='maxInputs' id='size' value='" . $row['size'] . "'>";
		echo "<br>";
		echo "Minimum Level: <input class='maxInputs' id='minLevel' value='" . $row['minLevel'] . "'>";
		echo "<br>";
		echo "Maximum Level: <input class='maxInputs' id='maxLevel' value='" . $row['maxLevel'] . "'>";
		echo "<br>";
		echo "Prize(g): <input class='maxInputs' id='prizeGold' value='" . $row['prizeGold'] . "'>";
		echo "<br>";
		echo "Prize(xp): <input class='maxInputs' id='prizeXp' value='" . $row['prizeXP'] . "'>";
		echo "<br>";
		echo "Prize(item): <input class='maxInputs' id='prizeItem' value='" . $row['prizeItem'] . "'>";
		echo "<br>";
		echo "<button id=\"saveTournamentEdit\">save</button><button id=\"deleteTournament\" style='float:right;'>delete tournament</button>";
		echo "<br><br>";
		$i = 10;
		if($row['running'] == 1){
			$rounds = array($row['round10'],$row['round9'],$row['round8'],$row['round7'],$row['round6'],$row['round5'],$row['round4'],$row['round3'],$row['round2'],$row['round1']);
			foreach ($rounds as $round){
				if ($round != ""){
					break;
				}
				$i = $i-1;
			}
		}
		
		echo "<script>
		$('#startTournament').click(function(){
				$('#tournamentInfo').load('index.php?tpage=tournament-admin&nonUI&startTournament=' + " . $id . ");
		});
		$('#playRound').click(function(){
			var r = confirm('This will start round" . $i . " is this correct?');
			if (r == true){
				$('#tournamentInfo').load('index.php?tpage=tournament-admin&nonUI&playRound=' + " . $id . "+ '&round=' + " . $i . ");
			}
		});
		
		$('#saveTournamentEdit').click(function(){
			$.post('index.php?tpage=tournament-admin&changeTournament=' + " . $id . ",
			  {
			  	name: $('#name').val(),
			  	size: $('#size').val(),
			  	minLevel: $('#minLevel').val(),
			  	maxLevel: $('#maxLevel').val(),
			  	prizeGold: $('#prizeGold').val(),
			  	prizeXp: $('#prizeXp').val(),
			  	prizeItem: $('#prizeItem').val()
			  },function(){
			  	getTournament(" . $id . ");
			  });	 
		});
		$('#deleteTournament').click(function(){
			$('#tournamentInfo').load('index.php?tpage=tournament-admin&nonUI&deleteTournament=' + " . $id . ");
		});
		</script>";
		
		
	}
}

function changeTournament($name,$size,$minLevel,$maxLevel,$prizeGold,$prizeXp,$prizeItem,$id){
	global $conn;
	$sql = "UPDATE tournaments SET name=?,size=?,minLevel=?,maxLevel=?,prizeGold=?,prizeXp=?,prizeItem=? WHERE id = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "siiiiisi", $name,$size,$minLevel,$maxLevel,$prizeGold,$prizeXp,$prizeItem,$id);
	mysqli_stmt_execute($stmt);
}

function deleteTournament($id){
	global $conn;
	$sql = "DELETE FROM tournaments WHERE id = ?";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
}

function newTournament(){
	echo "Tournament Name: <input class='maxInputs' id='name' value='Name'>";
		echo "<br>";
		echo "Size: <input class='maxInputs' id='size' value='4'>";
		echo "<br>";
		echo "Minimum Level: <input class='maxInputs' id='minLevel' value='1'>";
		echo "<br>";
		echo "Maximum Level: <input class='maxInputs' id='maxLevel' value='99'>";
		echo "<br>";
		echo "Prize(g): <input class='maxInputs' id='prizeGold' value='0'>";
		echo "<br>";
		echo "Prize(xp): <input class='maxInputs' id='prizeXp' value='0'>";
		echo "<br>";
		echo "Prize(item): <input class='maxInputs' id='prizeItem' value=''>";
		echo "<br>";
		echo "<button id=\"createTournament\">Create Tournament</button>";
		
		echo "<script>
		$('#createTournament').click(function(){
			$.post('index.php?tpage=tournament-admin&createTournament',
			  {
			  	name: $('#name').val(),
			  	size: $('#size').val(),
			  	minLevel: $('#minLevel').val(),
			  	maxLevel: $('#maxLevel').val(),
			  	prizeGold: $('#prizeGold').val(),
			  	prizeXp: $('#prizeXp').val(),
			  	prizeItem: $('#prizeItem').val()
			  });
		});
		</script>";
}

function createTournament($name,$size,$minLevel,$maxLevel,$prizeGold,$prizeXp,$prizeItem){
	global $conn;
	$sql = "INSERT INTO tournaments (name,size,minLevel,maxLevel,prizeGold,prizeXp,prizeItem) VALUES (?,?,?,?,?,?,?)";
	$stmt = mysqli_prepare($conn,$sql);
	mysqli_stmt_bind_param($stmt, "siiiiis", $name,$size,$minLevel,$maxLevel,$prizeGold,$prizeXp,$prizeItem);
	mysqli_stmt_execute($stmt);
}

function startTournamentOld($id){
	global $conn;
	$sql = "SELECT players FROM tournaments where ID='$id'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	
	$players = explode(",",$row['players']);
	shuffle($players);
	$playerArray = array();
    $i = 0;
    foreach($players as $player){
        $sql = "SELECT name FROM characters WHERE id=$player";
        $result = mysqli_query($conn,$sql);
        if (mysqli_num_rows($result) == 0){
            $playerArray[$i] = "-";
        }
        else{
            $row = mysqli_fetch_assoc($result);
            $playerArray[$i] = $row['name'];
        }
        $i++;
    }
    $playerString = implode(",",$playerArray);
	$round1 = implode(",",$players);
	
	
	$sql = "UPDATE tournaments SET round1='$round1',round1Text='$playerString',running=1 WHERE id='$id'";
	mysqli_query($conn,$sql);
	getTournament($id);
	
}
function startTournament($id){
	global $conn;
	$sql = "SELECT players,size FROM tournaments where id='$id'";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$players = $row['players'];
	$numPlayers = $row['size'];
	
	$playersId = array();
	$playersName = array();
	$sql = "SELECT name,id FROM characters WHERE id IN ($players) ORDER BY experience DESC";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)){
		array_push($playersId,$row['id']);
		array_push($playersName,$row['name']);
	};
	
	$playerString = array();
	$round1 = array();
	if($numPlayers == 8){
		if (count($playersId < 8)){
			$i = count($playersId);
			while ($i < 8){
				array_push($playersId,"0");
				array_push($playersName,"-");
				$i++;
			}
		}
		$playerString[0] = $playersName[0];
		$round1[0] = $playersId[0];
		$playerString[1] = $playersName[7];
		$round1[1] = $playersId[7];	
		$playerString[2] = $playersName[3];
		$round1[2] = $playersId[3];	
		$playerString[3] = $playersName[4];
		$round1[3] = $playersId[4];	
		$playerString[4] = $playersName[2];
		$round1[4] = $playersId[2];	
		$playerString[5] = $playersName[5];
		$round1[5] = $playersId[5];
		$playerString[6] = $playersName[1];
		$round1[6] = $playersId[1];	
		$playerString[7] = $playersName[6];
		$round1[7] = $playersId[6];	
		
	}
	elseif($numPlayers == 16){
		if (count($playersId < 16)){
			$i = count($playersId);
			while ($i < 16){
				array_push($playersId,"0");
				array_push($playersName,"-");
				$i++;
			}
		}
		$playerString[0] = $playersName[0];
		$round1[0] = $playersId[0];
		$playerString[1] = $playersName[15];
		$round1[1] = $playersId[15];	
		$playerString[2] = $playersName[7];
		$round1[2] = $playersId[7];	
		$playerString[3] = $playersName[8];
		$round1[3] = $playersId[8];	
		$playerString[4] = $playersName[3];
		$round1[4] = $playersId[3];	
		$playerString[5] = $playersName[12];
		$round1[5] = $playersId[12];
		$playerString[6] = $playersName[4];
		$round1[6] = $playersId[4];	
		$playerString[7] = $playersName[11];
		$round1[7] = $playersId[11];	
		$playerString[8] = $playersName[1];
		$round1[8] = $playersId[1];
		$playerString[9] = $playersName[14];
		$round1[9] = $playersId[14];	
		$playerString[10] = $playersName[6];
		$round1[10] = $playersId[6];	
		$playerString[11] = $playersName[9];
		$round1[11] = $playersId[9];	
		$playerString[12] = $playersName[2];
		$round1[12] = $playersId[2];	
		$playerString[13] = $playersName[13];
		$round1[13] = $playersId[13];
		$playerString[14] = $playersName[5];
		$round1[14] = $playersId[5];	
		$playerString[15] = $playersName[10];
		$round1[15] = $playersId[10];	
	}
	elseif($numPlayers == 32){
		if (count($playersId < 32)){
			$i = count($playersId);
			while ($i < 32){
				array_push($playersId,"0");
				array_push($playersName,"-");
				$i++;
			}
		}
		$playerString[0] = $playersName[0];
		$round1[0] = $playersId[0];
		$playerString[1] = $playersName[31];
		$round1[1] = $playersId[31];	
		$playerString[2] = $playersName[15];
		$round1[2] = $playersId[15];	
		$playerString[3] = $playersName[16];
		$round1[3] = $playersId[16];	
		$playerString[4] = $playersName[8];
		$round1[4] = $playersId[8];	
		$playerString[5] = $playersName[23];
		$round1[5] = $playersId[23];
		$playerString[6] = $playersName[7];
		$round1[6] = $playersId[7];	
		$playerString[7] = $playersName[24];
		$round1[7] = $playersId[24];	
		$playerString[8] = $playersName[3];
		$round1[8] = $playersId[3];
		$playerString[9] = $playersName[28];
		$round1[9] = $playersId[28];	
		$playerString[10] = $playersName[12];
		$round1[10] = $playersId[12];	
		$playerString[11] = $playersName[19];
		$round1[11] = $playersId[19];	
		$playerString[12] = $playersName[11];
		$round1[12] = $playersId[11];	
		$playerString[13] = $playersName[20];
		$round1[13] = $playersId[20];
		$playerString[14] = $playersName[4];
		$round1[14] = $playersId[4];	
		$playerString[15] = $playersName[27];
		$round1[15] = $playersId[27];	
		$playerString[16] = $playersName[1];
		$round1[16] = $playersId[1];
		$playerString[17] = $playersName[30];
		$round1[17] = $playersId[30];	
		$playerString[18] = $playersName[14];
		$round1[18] = $playersId[14];	
		$playerString[19] = $playersName[17];
		$round1[19] = $playersId[17];	
		$playerString[20] = $playersName[9];
		$round1[20] = $playersId[9];	
		$playerString[21] = $playersName[22];
		$round1[21] = $playersId[22];
		$playerString[22] = $playersName[6];
		$round1[22] = $playersId[6];	
		$playerString[23] = $playersName[25];
		$round1[23] = $playersId[25];	
		$playerString[24] = $playersName[2];
		$round1[24] = $playersId[2];
		$playerString[25] = $playersName[29];
		$round1[25] = $playersId[29];	
		$playerString[26] = $playersName[13];
		$round1[26] = $playersId[13];	
		$playerString[27] = $playersName[18];
		$round1[27] = $playersId[18];	
		$playerString[28] = $playersName[10];
		$round1[28] = $playersId[10];	
		$playerString[29] = $playersName[21];
		$round1[29] = $playersId[21];
		$playerString[30] = $playersName[5];
		$round1[30] = $playersId[5];	
		$playerString[31] = $playersName[26];
		$round1[31] = $playersId[26];	
	}
	elseif($numPlayers == 4){
		if (count($playersId < 4)){
			$i = count($playersId);
			while ($i < 4){
				array_push($playersId,"0");
				array_push($playersName,"-");
				$i++;
			}
		}
		$playerString[0] = $playersName[0];
		$round1[0] = $playersId[0];
		$playerString[1] = $playersName[3];
		$round1[1] = $playersId[3];	
		$playerString[2] = $playersName[1];
		$round1[2] = $playersId[1];	
		$playerString[3] = $playersName[2];
		$round1[3] = $playersId[2];
	}
	
	
    $playerString = implode(",",$playerString);
	$round1 = implode(",",$round1);
	
	
	$sql = "UPDATE tournaments SET round1='$round1',round1Text='$playerString',running=1 WHERE id='$id'";
	mysqli_query($conn,$sql);
	#getTournament($id);
	
}

function playRound($id,$round){
		global $conn;
		$sql = "SELECT * FROM tournaments WHERE id='$id'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($result);
		
		$players = explode(",", $row['round' . $round]);
        $playersNames = explode(",", $row['round' . $round. 'Text']);
		
		#FINALS
		#sleep(15);
        #$message = "Starting round " . $round . " now!";
        #announce($message);
		
		require_once(__ROOT__."/backend/fighting/newFight.php");
		
		tournamentRound($players,$playersNames,$id,$round);
}

function announce($message){
    $date = date("Ymd");
        
    $fileName = __ROOT__."/public_html/frontend/chat/$date.txt";
    $myfile = fopen($fileName, "a+") or die("Error reading file");
    $time = date("H:i");
    fwrite($myfile, $time . " - " . "<strong>Mr Tournament</strong>: " . $message . "<br>");
    fclose($myfile);
}

function finishTournament($id,$round,$winner){
	require_once(__ROOT__."/backend/crafting/craftingFunctions.php");
    global $conn;
    #echo "works";
    $date = date("Y/m/d H:i");
    
    $sql = "UPDATE tournaments SET running=0,finished='$date',winner='$winner' WHERE id='$id'";
    mysqli_query($conn,$sql);

    
    $name = "The ";
    
    $adverbLoc = __ROOT__."/backend/tournament/names/adverb.txt";
    $myfile = fopen($adverbLoc, "r") or die("Error reading file");
    $adverb = fread($myfile,filesize($adverbLoc));
    $adverb = explode(",",$adverb);
    $adverb = $adverb[array_rand($adverb)];
    
    $adjectiveLoc = __ROOT__."/backend/tournament/names/adjective.txt";
    $myfile = fopen($adjectiveLoc, "r") or die("Error reading file");
    $adjective = fread($myfile,filesize($adjectiveLoc));
    $adjective = explode(",",$adjective);
    $adjective = $adjective[array_rand($adjective)];
    
    $nounLoc = __ROOT__."/backend/tournament/names/noun.txt";
    $myfile = fopen($nounLoc, "r") or die("Error reading file");
    $noun = fread($myfile,filesize($nounLoc));
    $noun = explode(",",$noun);
    $noun = $noun[array_rand($noun)];
    
    $name .= " " . $adverb . " " . $adjective . " " . $noun;

    $sql = "SELECT * FROM tournaments WHERE id='$id'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $size = $row['size'];
    $min = $row['minLevel'];
    $max = $row['maxLevel'];
    $partTier = $row['partTier'];
    $parts = explode(",",$row['prizePart']);
    $amount = count($parts);
    $partReward = array();
    for ($i=0; $i < $amount; $i++) { 
    	$partReward[] = (getRandomPartTier($partTier))['id'];
    }
    $prizePart = implode(",",$partReward);
    $sql = "INSERT INTO tournaments (size,name,minLevel,maxLevel,partTier,prizePart) VALUES ($size,'$name',$min,$max,$partTier,'$prizePart')";
    mysqli_query($conn,$sql);

    $sql = "SELECT crafting_id FROM characters WHERE name='$winner'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    foreach($parts as $partId){
    	$sql = "SELECT slotType FROM craftingparts WHERE id=$partId";
    	$result = mysqli_query($conn,$sql);
    	$part = mysqli_fetch_assoc($result);
    	insertPart($partId,$part['slotType'],$row['crafting_id'],1);
	}
    
}

if(isset($_SESSION['other']['tournamentAdmin'])){
	if($_SESSION['other']['tournamentAdmin'] == 1){
		if(isset($_GET['getTournament'])){
			getTournament($_GET['getTournament']);
		}
		elseif(isset($_GET['changeTournament'])){
			changeTournament($_POST['name'],$_POST['size'],$_POST['minLevel'],$_POST['maxLevel'],$_POST['prizeGold'],$_POST['prizeXp'],$_POST['prizeItem'],$_GET['changeTournament']);
		}
		elseif(isset($_GET['deleteTournament'])){
			deleteTournament($_GET['deleteTournament']);
		}
		elseif(isset($_GET['newTournament'])){
			newTournament();
		}
		elseif(isset($_GET['createTournament'])){
			createTournament($_POST['name'],$_POST['size'],$_POST['minLevel'],$_POST['maxLevel'],$_POST['prizeGold'],$_POST['prizeXp'],$_POST['prizeItem']);	
		}
		elseif(isset($_GET['startTournament'])){
			startTournament($_GET['startTournament']);
		}
		elseif(isset($_GET['playRound'])){
			playRound($_GET['playRound'],$_GET['round']);
		}
	}
}
	

?>

	  
	  


