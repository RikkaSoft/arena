<?php
if(isset($_GET['page'])){
	$pageActive = $_GET['page'];
}
else{
	$pageActive = "";
}
?>
<div class="row" style='background:black'>

    <div  id="infoBar">

    	<div id="infoBarText" style="line-height:20px" class='col-sm-12 col-sm-7 col-md-8 col-lg-8'>

				<?php global $infoBarMessageAlt, $infoBarMessage, $infoBarPrio; 

				if ($infoBarPrio == "off"){

					if (rand(0,1) == 0){

							echo $infoBarMessageAlt;

					}

					else

					{

						echo $infoBarMessage;

					}

				}

				else{

					echo $infoBarPrio;

				}

				?>

    	</div>

			
        <div id="infoBarButtons" class='hidden-xs col-sm-5 col-md-4 col-lg-4'>

			<?php
			if(isset($_SESSION['loggedIn'])){
	        		if(isset($_GET['page'])){
	        			if($_GET['page'] == "logout"){
							echo "<a class='headerButtonLink' href='index.php?page=login'>
					        	<div class='headerSubCategoryButton'>
					        		Login
					        	</div>
				        	</a>";
							echo "<a class='headerButtonLink' href='index.php?page=register'>
					        	<div class='headerSubCategoryButton'>
					        		Register
					        	</div>
				        	</a>";

	        			}
					else{
						echo "<a class='headerButtonLink' id='logoutButton' href='#'>
				        	<div class='headerSubCategoryButton'>
				        		Logout
				        	</div>
			        	</a>";
					}
				}
			}
			else{

					echo "<a class='headerSubCategoryButton' href='index.php?page=login'>Login</a>";

					echo "<a class='headerSubCategoryButton' href='index.php?page=register'>Register</a>";

			}
			?>
        	<a class="headerButtonLink" href="index.php?page=online">

	        	<div class='headerSubCategoryButton <?php if($pageActive == "online"){echo "headerActive";} ?>' id='online'>

	        		Online Players

	        	</div>

        	</a>

        	<a class="headerButtonLink" href="index.php?page=leaderboard"'>
	        	<div class='headerSubCategoryButton <?php if($pageActive == "leaderboard"){echo "headerActive";} ?>' id='leaderboard'>
	        		Leaderboard
	        	</div>
        	</a>
        	<!--
        	<a class="headerButtonLink" href="index.php?page=hallofheroes">
	        	<div class='headerSubCategoryButton <?php if($pageActive == "hallofheroes"){echo "headerActive";} ?>' id='hallofheroes'>
	        		Heroes
	        	</div>
        	</a>
        	-->
        	
        	

        </div>

    </div>

</div>