<div class="row" id="top">
    <div class="col-xs-3 col-sm-7 col-md-8 col-lg-8" id="menu">
        <div id="logoDiv">
            <img class="hidden-xs" src="frontend/design/images/logo2.png">
        </div>
        <div class="hidden-xs" id="menuDiv">
            <div class="menuCategory">
                <a href="index.php?page=news">
                    <div id="news" class="headerCategoryButton">
                        News
                    </div>
                </a>
                <a href="index.php?page=tavern">
                    <div id="tavern" class="headerCategoryButton">
                        Tavern
                    </div>
                </a>
                <a href="index.php?page=market">
                    <div id="market" class="headerCategoryButton">
                        Market
                    </div>
                </a>
            </div>
            <div class="menuCategory">
                <a href="index.php?page=adventure">
                    <div id="adventure" class="headerCategoryButton">
                        Adventure
                    </div>
                </a>
                <a href="index.php?page=tournament">
                    <div id="tournament" class="headerCategoryButton">
                        Tournaments
                    </div>
                </a>
                <a href="index.php?page=enchantress">
                    <div id="enchantress" class="headerCategoryButton">
                        Enchantress
                    </div>
                </a>
            </div>
            <div class="menuCategory">
                <a href="index.php?page=arena">
                    <div id="arena" class="headerCategoryButton">
                        <?php if (isset($_SESSION['characterProperties']['battleReportReady'])){ if ($_SESSION['characterProperties']['battleReportReady'] == 1) {echo "<img src='frontend/design/images/other/battleReport.png'> ";}}?>Arena
                    </div>
                </a>
                <a href="index.php?page=training">
                    <div id="training" class="headerCategoryButton">
                        Training
                    </div>
                </a>
               <a href="index.php?page=guilds">
                    <div id="guilds" class="headerCategoryButton">
                        Guilds
                    </div>
                </a>
               
               <!-- <a href="index.php?page=seasonEnd">
                    <div id="seasonEnd" class="headerCategoryButton">
                        <?php
						    $today = date('m/d/Y');
						    $today = strtotime($today);
						    $finish = "03/12/2017";
						    $finish = strtotime($finish);
						    $diff = $finish - $today;
						    $daysleft=floor($diff/(60*60*24));
						    echo $daysleft . " days left";
						?>
                    </div>
                </a>
                <!--<a href="index.php?page=clans">
                    <div id="clans" class="headerCategoryButton">
                        Clans
                    </div>
                </a>-->
            </div>
        </div>
        <div class="hidden-sm hidden-md hidden-lg" id="smallMenuDiv">
            <div class="dropdown">
                <button onclick="myFunction()" class="dropbtn"><img src='frontend/design/images/menu.png' height='80px'></button>
                  <div id="myDropdown" class="dropdown-content">
                    <a href="index.php?page=news" id="newsMob">News</a>
                    <a href="index.php?page=tavern" id="tavernMob">Tavern</a>
                    <a href="index.php?page=market" id="marketMob">Market</a>
                    <a href="index.php?page=adventure" id="adventureMob">Adventure</a>
                    <a href="index.php?page=tournament" id="tournamentMob">Tournaments</a>
                    <a href="index.php?page=enchantress" id="enchantressMob">Enchantress</a>
                    <a href="index.php?page=arena" id="arenaMob"><?php if (isset($_SESSION['characterProperties']['battleReportReady'])){ if ($_SESSION['characterProperties']['battleReportReady'] == 1) {echo "<img src='frontend/design/images/other/battleReport.png'> ";}}?>Arena</a>
                    <a href="index.php?page=training" id="trainingMob">Training</a>
                    <a href="index.php?page=online" id="onlineMob">Online players</a>
                    <a href="index.php?page=leaderboard" id="leaderboardMob">Leaderboard</a>
                    <a href="index.php?page=hallofheroes" id="hallofheroesMob">Heroes</a>
                    <a href="index.php?page=guilds" id="guildsMob">Guilds</a>
                    <a href="index.php?page=logout">Logout</a>
                  </div>
                </div>
                
                <script>
                function myFunction() {
                    var maxWidth = $("#mainDiv").css("width");
                    $(".dropdown-content").css("width",maxWidth);
                    $("#myDropdown").slideToggle("0.4");
                }
                </script>
        </div>
    </div>
    <div class="col-xs-9 col-sm-5 col-md-4 col-lg-4" id="characterDiv">
        <?php 
        
        	#season finals
        	if (isset($_SESSION['final'])){
    			if(isset($_SESSION['loggedIn'])){
	        		echo "<a class='outButtons' href='index.php?page=logout'>Logout</a>";
				}
				else{
					echo "<a class='outButtons' href='index.php?page=login'>Login</a>";
					echo "<a class='outButtons' href='index.php?page=register'>Register</a>";
				}
				echo "<br>";
				echo "<a href='index.php?page=seasonFinals'><h2 style='text-align:center'>Season Eight Finals!</h2></a>";
        	}
			else{
	        	if(isset($_SESSION['loggedIn'])){
	        		if(isset($_GET['page'])){
	        			if($_GET['page'] == "logout"){
	        				echo "<a class='outButtons' href='index.php?page=login'>Login</a>";
							echo "<a class='outButtons' href='index.php?page=register'>Register</a>";
	        			}
						else{
							include(__ROOT__."/backend/character/get-character.php");
						}
	        		}
					else{
						include(__ROOT__."/backend/character/get-character.php");
					}
	        	}
				else{
					echo "<a class='outButtons' href='index.php?page=login'>Login</a>";
					echo "<a class='outButtons' href='index.php?page=register'>Register</a>";
				}
			}
        ?>
    </div>
</div>

<script>
	$(document).ready(function(){
		var page = '<?php if(isset($_GET['page'])){echo $_GET['page'];};?>';
		if (page != null){
			$('#' + page).css("background-color","#aaaaaa");
			$('#' + page + 'Mob').css("background-color","#aaaaaa");
		}
		
		if (window.matchMedia('(max-width: 768px)').matches) {
			var didScroll;
			var lastScrollTop = 0;
			var delta = 120;
			var navbarHeight = $('#top').outerHeight();
	
			$('#mainPage').scroll(function(event){
			    didScroll = true;
			});
			
			setInterval(function() {
			    if (didScroll) {
			        hasScrolled();
			        didScroll = false;
			        
			    }
			}, 250);
		}
		
		
		function hasScrolled() {
		    var st = $('#mainPage').scrollTop();
		    // Make sure they scroll more than delta
		    if(Math.abs(lastScrollTop - st) <= delta){
		    	return;
		    }
		    if (st > lastScrollTop && st > navbarHeight){
		        // Scroll Down
		        if($('#top').hasClass('topHide')){
		        	
		        }
		        else{
		        	$('#top').addClass('topHide');
		        	$('#top').slideToggle(250);
		        }
		    } else {
		        // Scroll Up
		        if($('#top').hasClass('topHide')){
		        	$('#top').removeClass('topHide');
		            $('#top').slideToggle(250);
		        }
		    }
		    
		    lastScrollTop = st;
		}
		
	});
	
	
	
	
	
	
	function hasScrolled() {
	    var st = $(this).scrollTop();
	    
	    // Make sure they scroll more than delta
	    if(Math.abs(lastScrollTop - st) <= delta)
	        return;
	    
	    // If they scrolled down and are past the navbar, add class .nav-up.
	    if (st > lastScrollTop && st > navbarHeight){
	        // Scroll Down
	        $('header').removeClass('nav-down').addClass('nav-up');
	    } else {
	        // Scroll Up
	        if(st + $(window).height() < $(document).height()) {
	            $('header').removeClass('nav-up').addClass('nav-down');
	        }
	    }
	    
	    lastScrollTop = st;
	}
	
</script>