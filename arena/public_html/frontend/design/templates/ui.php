<!DOCTYPE html>
<html lang="en">
    <?php include("head.php"); ?>
    <body>
        <!--HEADER-->
        <div class="container" id="wrapper">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="mainDiv">
            	<?php include("infoBar.php");?>
            	<?php 
            		if(isset($_SESSION['loggedIn'])){
            			include(__ROOT__."/backend/character/getCharacterData.php");
            			echo "<script>
            			window.setInterval(function(){
				            var counter = $('#timeUntilRecharge').text();
				            var timer = counter.split(':');
				          //by parsing integer, I avoid all extra string processing
				          var minutes = parseInt(timer[0], 10);
				          var seconds = parseInt(timer[1], 10);
				          --seconds;
				          minutes = (seconds < 0) ? --minutes : minutes;
				          if (minutes < 0) clearInterval(interval);
				          seconds = (seconds < 0) ? 59 : seconds;
				          seconds = (seconds < 10) ? '0' + seconds : seconds;
				          $('.timeUntilRecharge').html(minutes + ':' + seconds);
				          if(seconds <= 0 && minutes <= 0){
				            updateChar();
				          }
				        }, 1000);
            			</script>";
					}
            	?>
            	
            	<div class='row hidden-sm-up' style='background:black'>
                	<?php include("header.php"); ?>
                </div>
                <div class="row mainPart" >
                	<div id="largeMenuWrapper">
                		<?php include("menu.php"); ?>
                	</div>
                	<div id="largeCharacterWrapper" class='hidden-xs'>
                		<?php include("character.php"); ?>
                	</div>
                	
                	<div id="mainPageWrapper">
                		
		                <div id="mainPage">
		                        <?php 		                        	
		                        	if(isset($_SESSION['final'])){
										if(isset($_GET['page'])){
											$allowedArray = array("match_history","view-item","view-character","tournament-admin","your-character","news","market","hallofheroes","leaderboard","online","login","register","reset-password","unsubscribe","view-character","view-item","view-battlereport","view-part","view-battlereport-sequence","chatroom");
				                        	if(in_array($_GET['page'], $allowedArray)){
												getPage();
				                        	}
											else{
												if(isset($_SESSION['loggedIn']) && $_GET['page'] == "tavern"){
													getPage();
												}
												else{
													require_once("frontend/pages/seasonFinals.php");
												}
											}
										}
										else{
											getPage();
										}
									}
									else{
									 
									 	$allowedArray = array("news","market","hallofheroes","leaderboard","online","login","register","reset-password","unsubscribe","view-character","view-item","view-battlereport","view-part","view-battlereport-sequence","chatroom");
				                        if(isset($_GET['page'])){
				                        	if(in_array($_GET['page'], $allowedArray)){
												getPage();
				                        	}
											else{
												if(isset($_SESSION['loggedIn'])){
													getPage();
												}
												else{
													require_once(__ROOT__."/backend/accounts/authorized.php");
												}
											}
										}
										else{
											getPage();
										}
									}
		                        ?>
		                </div>
		            </div>
	            </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"  id="footer">
                        <?php include("footer.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

