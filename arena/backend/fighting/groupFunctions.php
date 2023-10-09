<?php
	
	function ListAllOpenGroups(){
		global $conn;

		$sql = "SELECT groupfights.*, count(groupfightmembers.id) FROM groupfights LEFT JOIN groupfightmembers ON groupfightmembers.groupId = groupfights.id WHERE finished=0 GROUP BY groupfights.id ORDER BY minLevel";
		$result = mysqli_query($conn,$sql);
		echo "<div class='activeGroupWrapper'>";
		echo "<h4>Active Skirmishes</h4>";

			if(mysqli_num_rows($result) > 0){
				while($row = mysqli_fetch_assoc($result)){
					$row['groupName'] = strip_tags($row['groupName']);
					echo "<a href='index.php?page=groupFight&getGroup=" . $row['id'] . "'><div class='activeGroup'>";

					echo "<div class='activeGroupName'>" . $row['groupName'] . "</div>";
					echo "<div class='activeGroupLevel'>Level " . $row['minLevel'] . "-" . $row['maxLevel'] . "</div>";
					echo "<div class='activeGroupPlayers'>" . $row['count(groupfightmembers.id)'] . "/" . $row['size'] . " players (" . $row['size']/2 . "v" . $row['size']/2 . ")</div></div></a>";
				}
			}
			else{
				echo "<h5>There are no active skirmishes, you can create one by clicking the Create a Skirmish button at the bottom of the screen</h5>";
			}
		echo "</div>";
		echo "<button id='createGroup' class='button'>Create a Skirmish</button>";
		echo "<script>
			$('#createGroup').click(function(){
				$('#mainPage').load('index.php?fpage=groupFunctions&nonUI&createGroupGui');		
			});
		</script>";
	}

	function CheckGroupIfUpdated($groupId, $index){
		global $conn;
		$sql = "SELECT updateIndex FROM groupfights WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $groupId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		if($row['updateIndex'] > $index){
			GetActiveGroup($groupId);
		}
	}

	function GetActiveGroup($id){
		global $conn;
		$sql = "SELECT groupfights.*, groupfightmembers.groupId,groupfightmembers.characterId,groupfightmembers.ready,groupfightmembers.team, characters.name, characters.level FROM groupfights INNER JOIN groupfightmembers ON groupfightmembers.groupId = groupfights.id INNER JOIN characters ON characters.id = groupfightmembers.characterId WHERE groupfights.id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$t1 = array();
		$t2 = array();
		$details = "";
		$joined = false;
		if(mysqli_num_rows($result) > 0 ){
			while($row = mysqli_fetch_assoc($result)){
				if($row['team'] == 1){
					$t1[] = $row;
				}
				else if($row['team'] == 2){
					$t2[] = $row;
				}
				$details = $row;
			};
			if($details['finished'] == 0){
				$details['groupName'] = strip_tags($details['groupName']);
				echo "<div class='groupWrapper'>";
				echo "<a href='index.php?page=groupFight'>Go back</a>";

					echo "<div class='groupName'>";
						echo "<h3>" . $details['groupName'] . "</h3>";
					echo "</div>";
					echo "<div style='text-align:center'>";
					echo "The skirmish starts within one minute when all gladiators are ready. Ready yourself by clicking the red diamond next to your name. You will not be healed before the fight starts";
					echo "</div>";
					echo "<div class='team1 team'>";
					echo "<h4>Team 1</h4>";
					echo "<table style='margin:0 auto;'>";
					$t1slot = false;
					$t2slot = false;
					$notInTeam1 = true;
					$notInTeam2 = true;
					for ($i=0; $i < $details['size']/2 ; $i++) {
						echo "<tr>";
						$ready = "";
						if(isset($t1[$i])){
							if($t1[$i]['name'] == $_SESSION['characterProperties']['name']){
								$joined = true;
								if($t1[$i]['ready'] == 1){
									echo "<td><img id='ready' class='ready' src='frontend/design/images/other/ready.png'></span></td>";
								}
								else{
									echo "<td><img id='ready' src='frontend/design/images/other/notReady.png'></span></td>";
								}
								$notInTeam1 = false;
								echo "<td><a href='index.php?page=view-character&charName=" . $t1[$i]['name'] . "'><span class='name'>" . $t1[$i]['name'] . " (" . $t1[$i]['level'] . ")" . "</span></a></td>";
								echo "<td><span class='leaveGroup'>Leave</span></td>";
							}
							else{
								if($t1[$i]['ready'] == 1){
									echo "<td><img src='frontend/design/images/other/ready.png'></td>";
									echo "<td><a href='index.php?page=view-character&charName=" . $t1[$i]['name'] . "'><span class='name'>" . $t1[$i]['name'] . " (" . $t1[$i]['level'] . ")" . "</span></a></td>";
								}
								else{
									echo "<td><img src='frontend/design/images/other/notReady.png'></td>";
									echo "<td><a href='index.php?page=view-character&charName=" . $t1[$i]['name'] . "'><span class='name'>" . $t1[$i]['name'] . " (" . $t1[$i]['level'] . ")" . "</span></a></td>";
								}
							}
						}
						else{
							$t1slot = true;
							echo "<td></td>";
							echo "<td><span class='groupMember'>Empty slot</span></td>";
						}
						echo "</tr>";
					}
					echo "</table>";
					if($t1slot && $notInTeam1){
						echo "<br><button class='button emptySpot' id=1>Join team</button>";
					}
					echo "</div>";
					echo "<div class='team2 team'>";
					echo "<h4>Team 2</h4>";
					echo "<table style='margin:0 auto;'>";
					for ($i=0; $i < $details['size']/2 ; $i++) { 
						echo "<tr>";
						$ready = "";
						if(isset($t2[$i])){
							if($t2[$i]['name'] == $_SESSION['characterProperties']['name']){
								$joined = true;
								if($t2[$i]['ready'] == 1){
									echo "<td><img id='ready' class='ready' src='frontend/design/images/other/ready.png'></span></td>";
								}
								else{
									echo "<td><img id='ready' src='frontend/design/images/other/notReady.png'></span></td>";
								}
								$notInTeam2 = false;
								echo "<td><a href='index.php?page=view-character&charName=" . $t2[$i]['name'] . "'><span class='name'>" . $t2[$i]['name'] . " (" . $t2[$i]['level'] . ")" . "</span></a></td>";
								echo "<td><span class='leaveGroup'>Leave</span></td>";
							}
							else{
								if($t2[$i]['ready'] == 1){
									echo "<td><img src='frontend/design/images/other/ready.png'></td>";
									echo "<td><a href='index.php?page=view-character&charName=" . $t2[$i]['name'] . "'><span class='name'>" . $t2[$i]['name'] . " (" . $t2[$i]['level'] . ")" . "</span></a></td>";
								}
								else{
									echo "<td><img src='frontend/design/images/other/notReady.png'></td>";
									echo "<td><a href='index.php?page=view-character&charName=" . $t2[$i]['name'] . "'><span class='name'>" . $t2[$i]['name'] . " (" . $t2[$i]['level'] . ")" . "</span></a></td>";
								}
							}
						}
						else{
							$t2slot = true;
							echo "<td></td>";
							echo "<td><span class='groupMember'>Empty slot</span></td>";
						}
						echo "</tr>";

					}
					echo "</table>";
					if($t2slot && $notInTeam2){
						echo "<br><button class='button emptySpot' id=2>Join team</button>";
					}
					
					echo "</div>";
					echo "<div class='groupChatWrapper'>";
						echo "<div id='groupChat' class='groupChat'></div>";
						echo '<textarea type="text" class="groupChatInput" placeholder="Say something"></textarea>';
						echo '<button class="groupChatSubmit">Send</button>';
					echo "</div>";


				echo "</div>";

				if($joined){
					echo "<div class='surrender' style='text-align:center;margin-top:40px;'>";
					$vitality = $_SESSION['characterProperties']['vitality'];
					$lastSurrender = $_SESSION['characterProperties']['battleGroupSurrender'];
						$i = 0.5;
						echo "<strong>When do you wish to surrender?</strong><br><select style='background:#580202' id='surrenderValue' name=\"yourSurrender\"><br>";
						do {
							if ($i >= $lastSurrender){
								$si = $i*100;
								$hp = round($vitality * $i);
								echo "<option value=$i selected>$si% ($hp hp)</option>";
								$i = $i-0.1;
							}
							else{
								$si = $i*100;
								$hp = round($vitality * $i);
								echo "<option value=$i>$si% ($hp hp)</option>";
								$i = $i-0.1;
							}
						} while ($i >= 0.1);
						if($lastSurrender == 0){
						echo "<option value=0 selected>0%</option>";

						}
						else{
						echo "<option value=0>0%</option>";

						}
						echo "</select>";
						echo "</div>";
						echo "<script>
							$('#surrenderValue').change(function(){
								var surrVal = $('#surrenderValue').val();
								$.post( 'index.php?fpage=groupFunctions&nonUI&setSurrender', { surrVal: surrVal });
							});

						</script>";
				}

				echo "<script>
					var group = " . $details['id'] . ";
					$('.groupChat').load('index.php?fpage=groupFunctions&nonUI&getFullChat='+group,function(){
						ScrollDownChat();
					});
					$('.emptySpot').click(function(){
						var team = $(this).attr('id');
						$('#mainPage').load('index.php?fpage=groupFunctions&nonUI&joinGroup='+group+'&team='+team);
					});
					$('.leaveGroup').click(function(){
						$('#mainPage').load('index.php?fpage=groupFunctions&nonUI&leaveGroup='+group);
					});
					var chatInterval = window.setInterval(function(){
						UpdateChat();
					}, 5000);
					var updateInterval = window.setInterval(function(){
						UpdateGroup();
					}, 5000);

					function UpdateGroup(){
						var index = '" . $details['updateIndex'] . "';
						$.get('index.php?fpage=groupFunctions&nonUI&updateGroup='+group+'&index='+index, function( data ) {
						  data = data.trim();
						  if(data != ''){
						  	clearInterval(chatInterval);
						  	clearInterval(updateInterval);
						  	$('#mainPage').html(data);
						  }
						});

					}

					function UpdateChat(){
						var index = $('.groupMessage').last().attr('id');
						if(index != null){
							$.get('index.php?fpage=groupFunctions&nonUI&getNewChat='+group+'&index='+index, function( data ) {
							  data = data.trim();
							  if(data != ''){
							  	$('.groupChat').append(data);
							  	ScrollDownChat();
							  }
							});
						}
						else{
							$.get('index.php?fpage=groupFunctions&nonUI&getFullChat='+group, function( data ) {
							  data = data.trim();
							  if(data != ''){
							  	$('.groupChat').append(data);
							  	ScrollDownChat();
							  }
							});
						}
					}

					function ScrollDownChat(){
						var textarea = document.getElementById('groupChat');
						textarea.scrollTop = textarea.scrollHeight;
					}

					$('.groupChatSubmit').click(function(){
						if($('.groupChatInput').val().trim() != ''){
							var message = $('.groupChatInput').val();
							$.post( 'index.php?fpage=groupFunctions&nonUI&postToChat='+group, { message: message })
							  .done(function(data) {
							    UpdateChat();
							  });
							$('.groupChatInput').val('');
						}
					});
					$('.groupChatInput').keypress(function(e){
				      if(e.keyCode==13)
				      $('.groupChatSubmit').click();
				    });
				    $('#ready').click(function(){
				    	var ready = 1;
				    	if($(this).hasClass('ready')){
				    		ready = 0;
				    		$(this).removeClass('ready');
				    		$(this).attr('src','frontend/design/images/other/notReady.png');
				    	}
				    	else{
				    		$(this).addClass('ready');	
				    		$(this).attr('src','frontend/design/images/other/ready.png');	    		
				    	}
				    	$.get('index.php?fpage=groupFunctions&nonUI&setReady='+group+'&ready='+ready);
			    	});

				</script>";
			}
			else{
				if($details['report'] != null){
					echo "<a href='index.php?page=groupFight'>Go back</a>";
					$reportId = $details['report'];
					require_once(__ROOT__."/backend/other/get-battlereport-sequence.php");
					/*echo "<div style='padding:10px';>";
					$sql = "SELECT report FROM battlereports WHERE id='$reportId'";
					$result = mysqli_query($conn, $sql);
					$row = mysqli_fetch_assoc($result);
					echo $row['report'];
					echo "</div>";
					*/
					?>
					<script>
						$(document).ready(function(){
							var report = '<?php getBattleReport($reportId);?>';
							if((Math.floor(Math.random() * 2) + 1) == 1){
							    var regex = new RegExp('winningTeam', 'g');
								var report = report.replace(regex,"teamRed");
								var regex = new RegExp('teamTwo', 'g');
								var report = report.replace(regex,"teamBlue");
								var regex = new RegExp('teamOne', 'g');
					            var report = report.replace(regex,"teamBlue");
							}
							else{
							    var regex = new RegExp('winningTeam', 'g');
					            var report = report.replace(regex,"teamBlue");
					            var regex = new RegExp('teamTwo', 'g');
					            var report = report.replace(regex,"teamRed");
					            var regex = new RegExp('teamOne', 'g');
					            var report = report.replace(regex,"teamRed");
							}
							var roundSplit = report.split("<h4>");
							var length = roundSplit.length;
							var roundSplitEnd = roundSplit[length-1].split("<br><br><br>");
							roundSplit[length-1] = roundSplitEnd[0] + "<br><br><br>";
							roundSplit[length] = "</h4>" + roundSplitEnd[1];
							var length = roundSplit.length;
							
							var targetDiv = $("#report");
							var currentInput = targetDiv.html();
							targetDiv.html(currentInput + roundSplit[0]);
							
							
							var i = 1;
							var interval = setInterval(function() { 
					          	var currentInput = targetDiv.html();
								targetDiv.html(currentInput + "<h4>" + roundSplit[i]);
					          i++; 
					          $("#mainPage").scrollTop(function() { return this.scrollHeight; });
					          if(i >= length) clearInterval(interval);
					  		 }, 3000);
							

						});

					</script>
					<div class="mainContent">
						<div id="report">
							
						</div>
					</div>
					<?php
					echo "<script>
						clearInterval(chatInterval);
				  		clearInterval(updateInterval);
				  	</script>";

				}
				else{
					echo "<h4>The fight is currently being generated</h4>";
					echo "<div style='text-align:center;'>";
					echo "<img src='frontend/design/images/dice.svg' id='dice' style='margin-top:40px;'>";
					echo "</div>";
					echo "<script>
					
					clearInterval(chatInterval);
				  	clearInterval(updateInterval);
				  	var updateInterval = window.setInterval(function(){
						UpdateGroup();
					}, 5000);
					function UpdateGroup(){
						var index = '" . $details['updateIndex'] . "';
						$.get('index.php?fpage=groupFunctions&nonUI&updateGroup='+group+'&index='+index, function( data ) {
						  data = data.trim();
						  if(data != ''){
						  	$('#mainPage').html(data);
						  }
						});
					}
					</script>";
				}
			}
		}
		else{
			$sql = "DELETE FROM groupfights WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $id);
			mysqli_stmt_execute($stmt);
			ListAllOpenGroups();
		}
	}

	function GetFullChat($groupId){
		global $conn;
		$sql = "SELECT * FROM groupfightchat WHERE groupId=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $groupId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				echo "<span class='groupMessage' id='" . $row['id'] . "'>" . $row['time'] . " | " . $row['characterName'] . " - " . strip_tags($row['message']) . "</span>";
			}
		}
	}

	function GetNewChat($groupId,$index){
		global $conn;
		$sql = "SELECT * FROM groupfightchat WHERE groupId=? AND id > ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ii", $groupId,$index);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)){
				echo "<span class='groupMessage' id='" . $row['id'] . "'>" . $row['time'] . " | " . $row['characterName'] . " - " . strip_tags($row['message']) . "</span>";
			}
		}
	}
	function PostToChat($groupId,$message){
		global $conn;
		strip_tags($message);
		$sql = "INSERT INTO groupfightchat (groupId,message,characterName,time) VALUES (?,?,?,NOW())";
		echo $sql;
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "iss", $groupId,$message,$_SESSION['characterProperties']['name']);
		mysqli_stmt_execute($stmt);
	}

	function SetGroupSurrender($value){
		global $conn;
		if($value <= 0.5 && $value >= 0){
			$sql = "UPDATE characters SET battleGroupSurrender=? WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "di", $value,$_SESSION['characterProperties']['id']);
			mysqli_stmt_execute($stmt);
			$_SESSION['characterProperties']['battleGroupSurrender'] = $value;
		}
	}

	function JoinGroup($groupId,$team,$getGroup = true){
		global $conn;
		$charId = $_SESSION['characterProperties']['id'];
		$already = CheckIfAlreadySignedUp($charId);
		if($already == 'true' || $already == $groupId){
		//Check that your level matches the groups
			$sql = "SELECT * FROM groupfights WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $groupId);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			$groupInfo = mysqli_fetch_assoc($result);
			if($_SESSION['characterProperties']['level'] >= $groupInfo['minLevel'] && $_SESSION['characterProperties']['level'] <= $groupInfo['maxLevel']){
				
			}
			else{
				echo "<span class='errorMessage'>You do not meet the level requirements</span>";
				GetActiveGroup($groupId);
				return;
			}

		//Check that there are spots available
			$sql = "SELECT * FROM groupfightmembers WHERE groupId=? AND team=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "ii", $groupId,$team);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			$groupmembercount = mysqli_num_rows($result);

			if($groupmembercount < $groupInfo['size']/2){
				$sql = "SELECT * FROM groupfightmembers WHERE groupId=?";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "i", $groupId);
				mysqli_stmt_execute($stmt);
				$result = $stmt->get_result();
				while($row = mysqli_fetch_assoc($result)){
					if($_SESSION['characterProperties']['id'] == $row['characterId']){
						if($row['team'] != $team){
							LeaveGroup($groupId,false,true);
						}
						else{
							echo "<span class='errorMessage'>You are already signed up for this</span>";
							GetActiveGroup($groupId);
							return;
						}
					}
				}				
				//Allowed to join
				$charId = $_SESSION['characterProperties']['id'];
				$sql = "INSERT INTO groupfightmembers (groupId,characterId,ready,team) VALUES (?,?,0,?)";
				$stmt = mysqli_prepare($conn,$sql);
				mysqli_stmt_bind_param($stmt, "iii", $groupId,$charId,$team);
				mysqli_stmt_execute($stmt);

				IncrementIndex($groupId);
			}
			else{
				echo "<span class='errorMessage'>There are no available spots</span>";
				GetActiveGroup($groupId);
				return;
			}
			if($getGroup){
				GetActiveGroup($groupId);
			}
		}
		else{
			echo "<script>alert('You are already a member of a group');</script>";
			GetActiveGroup($already);
		}
	}

	function CreateGroupGUI(){
		echo "Group Name<br>";
		echo "<input id='gname' class='groupFields' type=text><br><br>";
		echo "<select id='gplayers' class='groupFields'>";
		for ($i=4; $i <= 32 ; $i = $i+2) { 
			echo "<option value='" . $i . "'>" . $i/2 . "v" . $i/2 . "</option>";
		}
		echo "</select><br><br>";
		echo "Who is allowed to join?<br>";
		echo "<select id='gwho' class='groupFields'>";
		echo "<option value='1'>+-1 of my level</option>";
		echo "<option value='2'>+-2 of my level</option>";
		echo "<option value='3'>+-3 of my level</option>";
		echo "<option value='4'>+-4 of my level</option>";
		echo "<option value='5'>Anyone!";
		echo "</select><br><br>";
		echo "<button class='button' id='create'>Crate Skirmish</button>";

		echo "<script>
			$('#create').click(function(){
				var gname = $('#gname').val();
				var gplayers = $('#gplayers').val();
				var gwho = $('#gwho').val();
				$.post( 'index.php?fpage=groupFunctions&nonUI&createGroup', { gname: gname,gplayers:gplayers,gwho:gwho })
				  .done(function(data) {
				  	data = data.trim();
				  	var result = data.split(',');
				  	if(result[0] == 'OK'){
				  		$('#mainPage').load('index.php?page=groupFight&nonUI&getGroup='+result[1]);	
				  	}
				  	else{
				  		alert('You are already a member of another group');
				  		$('#mainPage').load('index.php?page=groupFight&nonUI&getGroup='+result[1]);	
				  	}
				});
			});
		</script>";
	}

	function IncrementIndex($groupId){
		global $conn;
		$sql = "UPDATE groupfights SET updateIndex=updateIndex+1 WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $groupId);
		mysqli_stmt_execute($stmt);
	}

	function CheckIfAlreadySignedUp($charId){
		global $conn;
		$sql = "SELECT groupfightmembers.characterId, groupfights.* FROM groupfights INNER JOIN groupfightmembers ON groupfightmembers.groupId=groupfights.id WHERE groupfights.finished = 0 AND groupfightmembers.characterId = ?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $charId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			return $row['id'];
		}
		else{
			return 'true';
		}
	}

	function LeaveGroup($groupId,$show = true,$switchingTeams = false){
		global $conn;
		//Check that your level matches the groups
			$deleted = false;
			$sql = "SELECT * FROM groupfightmembers WHERE groupId=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "i", $groupId);
			mysqli_stmt_execute($stmt);
			$result = $stmt->get_result();
			while($row = mysqli_fetch_assoc($result)){
				if($_SESSION['characterProperties']['id'] == $row['characterId']){
					$sql = "DELETE FROM groupfightmembers WHERE characterId=? AND groupId=?";
					$stmt = mysqli_prepare($conn,$sql);
					mysqli_stmt_bind_param($stmt, "ii", $_SESSION['characterProperties']['id'],$groupId);
					mysqli_stmt_execute($stmt);
					IncrementIndex($groupId);
					if(!$switchingTeams){
						$sql = "SELECT groupfights.*, groupfightmembers.characterId FROM groupfights INNER JOIN groupfightmembers ON groupfightmembers.groupId = groupfights.id WHERE groupfights.id=?";
						$stmt = mysqli_prepare($conn,$sql);
						mysqli_stmt_bind_param($stmt, "i", $groupId);
						mysqli_stmt_execute($stmt);
						$result = $stmt->get_result();
						if(mysqli_num_rows($result) == 0){
							$sql = "DELETE FROM groupfights WHERE id=?";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "i", $groupId);
							mysqli_stmt_execute($stmt);
							$deleted = true;
						}
						else{
							$creator = false;
							while($row = mysqli_fetch_assoc($result)){
								if(!isset($first)){
									$first = $row;
								}
								if($row['creator'] == $row['characterId']){
									$creator = true;
									break;
								}
							}
							if(!$creator){
								$sql = "UPDATE groupfights SET creator=? WHERE id=?";
								$stmt = mysqli_prepare($conn,$sql);
								mysqli_stmt_bind_param($stmt, "ii", $first['characterId'],$groupId);
								mysqli_stmt_execute($stmt);
							}
						}
					}

					if($show){
						if($deleted){
							ListAllOpenGroups();
						}
						else{
							GetActiveGroup($groupId);
						}
					}
					return;
				}
			}
			if($show){
				echo "<span class='errorMessage'>You are not a part of this group</span>";
				GetActiveGroup($groupId);
			}
	}

	function SetReady($groupId,$ready){
		global $conn;
		$charId = $_SESSION['characterProperties']['id'];
		$sql = "UPDATE groupfightmembers SET ready=? WHERE groupId=? AND characterId=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "iii", $ready,$groupId,$charId);
		mysqli_stmt_execute($stmt);
		IncrementIndex($groupId);
	}

	function CreateGroup($name,$players,$range){
		global $conn;
		$name = strip_tags($name);
		$level = $_SESSION['characterProperties']['level'];
		$creator = $_SESSION['characterProperties']['id'];
		$already = CheckIfAlreadySignedUp($creator);
		if($already == 'true'){
			if($range == 5){
				$minLevel = 1;
				$maxLevel = 99;
			}
			else if($range > 0 && $range < 5){
				$minLevel = $level-$range;
				$maxLevel = $level+$range;
			}
			else{
				echo "invalid input, trying to cheat? error code: oajsSdinSq7DD141u3ufnpup3r6180ghaHsaduhUSByqwbnJSNDAHBty3";
				exit;
			}
			$sql = "INSERT INTO groupfights (groupName,size,minLevel,maxLevel,creator) VALUES (?,?,?,?,?)";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "siiii", $name,$players,$minLevel,$maxLevel,$creator);
			mysqli_stmt_execute($stmt);
			$id = mysqli_stmt_insert_id($stmt);
			JoinGroup($id,1,false);
			echo "OK," . $id;
		}
		else{
			echo "alreadyJoined," . $already;
		}
	}

	if(isset($_GET['joinGroup'])){
		JoinGroup($_GET['joinGroup'],$_GET['team']);
	}
	else if(isset($_GET['leaveGroup'])){
		LeaveGroup($_GET['leaveGroup']);
	}
	else if(isset($_GET['getFullChat'])){
		GetFullChat($_GET['getFullChat']);
	}
	else if(isset($_GET['getNewChat'])){
		GetNewChat($_GET['getNewChat'],$_GET['index']);
	}
	else if(isset($_GET['postToChat'])){
		if(isset($_POST['message'])){
			PostToChat($_GET['postToChat'],$_POST['message']);
		}
	}
	else if(isset($_GET['setReady'])){
		SetReady($_GET['setReady'],$_GET['ready']);
	}
	else if(isset($_GET['updateGroup'])){
		CheckGroupIfUpdated($_GET['updateGroup'],$_GET['index']);
	}
	else if(isset($_GET['createGroupGui'])){
		CreateGroupGUI();
	}
	else if(isset($_GET['createGroup'])){
		CreateGroup($_POST['gname'],$_POST['gplayers'],$_POST['gwho']);
	}
	else if(isset($_GET['setSurrender'])){
		SetGroupSurrender($_POST['surrVal']);
	}
?>