

						<script>
						function getReport($id){
					    	var loadUrl = ("index.php?opage=get-battlereport&nonUI&id=" + $id);
					        $('#battleReports').load(loadUrl,function(){
					        	resizeLeft();
					        });
				   		}	
						$(function() {
						    $( "#accordion" ).accordion({
						    	heightStyle: "content",
						    	collapsible: true,
								active: false
						    });
						  });
						  function resizeLeft(){
								if (window.matchMedia("(min-width: 768px)").matches) {
									var dynamic = $('#battleReports');
								    var static = $('.pageInfo');
								    if(static.height() < dynamic.height()){
								    	static.height(dynamic.height());	
								    }
								    else{
								    	var original = $('#mainPage');
								    	static.height(original.height()-20);
								    }
								           
							    }
							    else{
							
							    }
								
							}
						</script>
						<div class="mainContent">

						<div class="pageInfo">
						    
							    <?php 
								require_once(__ROOT__."/backend/other/get-match-history.php");
                                
								$one =          getMatches("1v1",20); 
								$two =          getMatches("2v2",20);
                                $three =        getMatches("3v3",20);
                                $four =         getMatches("4v4",20);
                                $mt4 =          getMatches("mt4",20);
                                $tournament =   getMatches("tournament",20);
                                $training =     getMatches("training",20);
                                
								echo "<div id=\"accordion\">
								  <a href='#' class='buttona'><div class='button'>1v1</div></a>
								  <div>";
								if(!empty($one)){
										foreach ($one as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No 1v1 matches has been fought yet";
								}
								echo "</div>";
								echo "<a href='#' class='buttona'><div class='button'>2v2</div></a>";
								echo "<div>";
								if(!empty($two)){
										foreach ($two as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No 2v2 matches has been fought yet";
								}
								echo "</div>";
								echo "<a href='#' class='buttona'><div class='button'>3v3</div></a>";
								echo "<div>";
								if(!empty($three)){
										foreach ($three as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No 3v3 matches has been fought yet";
								}
								echo "</div>";
								echo "<a href='#' class='buttona'><div class='button'>4v4</div></a>";
								echo "<div>";
								if(!empty($four)){
										foreach ($four as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No 4v4 matches has been fought yet";
								}
								echo "</div>";
								echo "<a href='#' class='buttona'><div class='button'>5v5+</div></a>";
								echo "<div>";
								if(!empty($mt4)){
										foreach ($mt4 as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No glorious 5v5+ battles has been fought yet";
								}
								echo "</div>";
                                echo "<a href='#' class='buttona'><div class='button'>Tournament</div></a>";
                                echo "<div>";
                                if(!empty($tournament)){
                                        foreach ($tournament as $id){
                                            echo $id;
                                            echo "<br>";
                                        }
                                }
                                else{
                                    echo "No tournament matches has been played yet";
                                }
                                echo "</div>";
								echo "<a href='#' class='buttona'><div class='button'>Training</div></a>";
								echo "<div>";
								if(!empty($training)){
										foreach ($training as $id){
											echo $id;
											echo "<br>";
										}
								}
								else{
									echo "No training battles has been fought yet";
								}
								echo "</div>";
								echo "</div>";
								?>
						
							
						</div>
							<div id="battleReports">

						</div>
					</div>
				